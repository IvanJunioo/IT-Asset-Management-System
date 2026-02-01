<?php
require '../../vendor/autoload.php'; 
require_once '../../config/config.php';
require_once '../repos/user.php';
require_once '../repos/asset.php';
require_once '../repos/assignment.php';

ob_start();
use Dompdf\Dompdf;

try {
    session_start();
    $userRepo = new UserRepo($pdo);
    $user = $userRepo->identify($_SESSION['user_id']);

    $assignRepo = new AssignmentRepo($pdo);

    $data = [];
    $assets = $assignRepo->getAssignedAssets($user);
    foreach ($assets as $asset){
        $data[] = [
            'asset' => [$asset, $assignRepo->getAssignmentDate($asset)]
        ];
    }

    $cssPath = __DIR__ . '/../../public/css/asset-pdf.css';
    $css = file_get_contents($cssPath);
    include '../template/assigned-assets.php';
    $html = ob_get_clean();
    $html = "<style>{$css}</style>" . $html;

    $dompdf = new Dompdf();
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();
    if (ob_get_length()) ob_end_clean();

    $dompdf->stream($user->name->last . "_assigned_assets.pdf", ["Attachment" => true]);
    exit;

} catch (Exception $e) {
    if (ob_get_length()) ob_end_clean();
    echo json_encode(["error" => $e->getMessage()]);
    exit;
}