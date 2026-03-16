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
    string $search,
    int $page,
    int $limit,
  ): array {
    $logs = [];
    foreach ($this->logRepo->getLogs($search,$page,$limit) as $log) {
      $metadata = json_decode($log["Metadata"], true);
      
      switch ($metadata["object"]) {
        case "asset":
          break;
        case "user":
          $user = $this->userRepo->identify($metadata["empID"]);
          $log["objName"] = $user->name->FLast();
          break;
      }
      
      $logs[] = $log;
    }
    return [
      "logs" => $logs,
      "count" => $this->logRepo->countLogs(search: $search),
    ];
  }

  public function getLoginUrl(): string {
    global $url;
    return $url;
  }

  public function login(string $code): void {
    global $client;

    session_start();

    $token = $client->fetchAccessTokenWithAuthCode($code);
    $client->setAccessToken($token['access_token']);

    $oauth = new Google\Service\Oauth2($client);

    $userinfo = $oauth->userinfo->get();

    $email = $userinfo->email;

    if (!in_array(substr($email, -10), ["@up.edu.ph", "@dcs.upd.edu.ph"])) {
      exit("Email not allowed.");
    }

    $users = $this->userRepo->search(new UserSearchCriteria(email: $email));
    if (count($users) == 0) throw new Exception("User email $email not found in database!");
    $user = $users[0];

    $_SESSION['user_id'] = $user->empID;
    $_SESSION['email'] = $email;
    $_SESSION['user_fname'] = $user->name->first;
    $_SESSION['user_lname'] = $user->name->last;
    $_SESSION['privilege'] = $user->privilege->value;
    $_SESSION['logged_in'] = true;
  }

  public function logout(): void {
    session_start();

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
