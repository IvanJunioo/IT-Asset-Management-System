<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../repos/actlog.php';
require_once __DIR__ . '/../repos/user.php';


final class LogHandler {
  public function __construct(
    private readonly ActLogRepoInterface $logRepo,
    private readonly UserRepoInterface $userRepo,
  ) {}

  public function getLogs(
    LogSearchCriteria $criteria,
    int $page,
    int $limit,
  ): array {
    $logs = [];
    foreach ($this->logRepo->getLogs($criteria,$page,$limit) as $log) {
      $metadata = json_decode($log["Metadata"], true);
      
      switch ($metadata["object"]) {
        case "asset":
          break;
        case "user":
          $user = $this->userRepo->identify($metadata["empID"]);
          $log["objName"] = $user? $user->name->FLast() : "Unknown user";
          break;
      }
      
      $priv = UserPrivilege::from($_SESSION['privilege']);

      $logs[] = [
        ...$log,
        "linkActor" => in_array($priv, [UserPrivilege::SuperAdmin, UserPrivilege::Admin]),
        "linkObject" => $metadata["object"] == "asset" || in_array($priv, [UserPrivilege::SuperAdmin, UserPrivilege::Admin]),
      ];
    }
    return [
      "logs" => $logs,
      "count" => $this->logRepo->countLogs(criteria: $criteria),
    ];
  }

  public function systemLog(
    string $log,
    array $metadata,
  ): void {
    $empID = $_SESSION["user_id"];
    $this->logRepo->add(
      new User(
        empID: $empID, 
        name: new Fullname(), 
        email: "", 
        privilege: UserPrivilege::Staff
      ), // temporary User data object
      "User $empID $log", 
      $metadata,
    );
  }

  private function isValidEmail(string $email): bool {
    foreach (["@up.edu.ph", "@dcs.upd.edu.ph"] as $emailSuffix) {
      if (str_ends_with($email, $emailSuffix)) return true;
    }
    return false;
  }

  public function login(string $code): void {
    global $client;

    $token = $client->fetchAccessTokenWithAuthCode($code);
    $client->setAccessToken($token['access_token']);

    $oauth = new Google\Service\Oauth2($client);

    $userinfo = $oauth->userinfo->get();

    $email = $userinfo->email;

    if (!$this->isValidEmail($email)) {
      throw new RuntimeException("Email is not allowed. Please use your UP or DCS mail to login.", 401);
    }

    $users = $this->userRepo->search(new UserSearchCriteria(email: $email));
    if (count($users) == 0) throw new RuntimeException("User email $email not found in database.", 404);
    $user = $users[0];

    if (!$user || !$user->isActive) {
      throw new RuntimeException("Your account is deactivated. Please contact the admin to reactivate your account.", 403);
    }

    $_SESSION['user_id'] = $user->empID;
    $_SESSION['email'] = $email;
    $_SESSION['user_fname'] = $user->name->first;
    $_SESSION['user_lname'] = $user->name->last;
    $_SESSION['privilege'] = $user->privilege->value;
    $_SESSION['logged_in'] = true;

    header("Location: " . BASE_URL . "index.php?page=dashboard");
  }

  public function logout(): void {
    $_SESSION = []; // unset all session variables

    if (ini_get("session.use_cookies")) {
      $params = session_get_cookie_params();
      setcookie(
        session_name(),
        "",
        time() - 999999,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
      );
    }

    session_destroy();
  }
}
