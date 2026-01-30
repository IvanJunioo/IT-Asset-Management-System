
<?php
require '../../vendor/autoload.php'; 
require_once '../../config/config.php';
require_once '../repos/user.php';
require_once '../repos/asset.php';
require_once '../repos/assignment.php';

ob_start();
use Dompdf\Dompdf;

try {
    $usersID = explode(",",$_GET['users'] ?? "");
    $userRepo = new UserRepo($pdo);
    $assignRepo = new AssignmentRepo($pdo);

    $users = [];
    $assetsMapping = [];
    foreach ($usersID as $ID){
      $user = $userRepo->identify($ID);
      $assets = $assignRepo->getAssignedAssets($user);
      $users[]=$user;
      $assetsMapping[$user->empID] = $assets;
    }

    $cssPath = __DIR__ . '/../../public/css/asset-pdf.css';
    $css = file_get_contents($cssPath);
    include '../template/faculty-assigned-assets.php';
    $html = ob_get_clean();
    $html = "<style>{$css}</style>" . $html;

    $dompdf = new Dompdf();
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();
    if (ob_get_length()) ob_end_clean();

    $dompdf->stream("Faculty_assigned_assets.pdf", ["Attachment" => true]);
    exit;

} catch (Exception $e) {
    if (ob_get_length()) ob_end_clean();
    echo json_encode(["error" => $e->getMessage()]);
    exit;
}


