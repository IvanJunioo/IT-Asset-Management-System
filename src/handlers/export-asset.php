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
    $assets = $assignRepo->getAssignedAssets($user);

    $html = '<html><body><h1>Assigned Assets</h1>';
    foreach ($assets as $asset) {
        $data = $asset->jsonSerialize();
        $html .= '<table border="1" cellpadding="5" style="margin-bottom:15px; width:100%;">';
        foreach ($data as $key => $value) {
            $html .= "<tr>
            <th style='text-align:left'>".htmlspecialchars((string)$key)."</th><td>".htmlspecialchars((string)$value)."</td></tr>";
        }
        $html .= '</table>';
    }
    $html .= '</body></html>';

    $dompdf = new Dompdf();
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();
    if (ob_get_length()) ob_end_clean();

    $dompdf->stream("assigned_assets.pdf", ["Attachment" => true]);
    exit;

} catch (Exception $e) {
    if (ob_get_length()) ob_end_clean();
    echo json_encode(["error" => $e->getMessage()]);
    exit;
}