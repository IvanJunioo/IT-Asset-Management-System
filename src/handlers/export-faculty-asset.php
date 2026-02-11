<?php
require __DIR__ . '/../../vendor/autoload.php'; 
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../repos/user.php';
require_once __DIR__ . '/../repos/asset.php';
require_once __DIR__ . '/../repos/assignment.php';

use Dompdf\Dompdf;

try {
    $usersParam = $_GET['users'] ?? null;
    $usersID = [];
    if (!empty($usersParam)) {
        $usersID = array_filter(explode(",", $usersParam));
    }
    $userRepo = new UserRepo($pdo);
    $assignRepo = new AssignmentRepo($pdo);

    $data = [];
    foreach ($usersID as $id) {
        $user = $userRepo->identify($id);
        if (!$user) continue;

        $assets = $assignRepo->getAssignedAssets($user);

        $assetDates = [];
        foreach ($assets as $asset) {
            $assetDates[$asset->propNum] =
                $assignRepo->getAssignmentDate($asset);
        }

        $data[] = [
            'user'        => $user,
            'assets'      => $assets,
            'assignDates' => $assetDates,
        ];
    }

    $cssPath = __DIR__ . '/../../public/css/asset-pdf.css';
    $css = file_get_contents($cssPath);

    ob_start();
    include '../template/faculty-assigned-assets.php';
    $html = ob_get_clean();

    $html = "<style>{$css}</style>" . $html;

    $dompdf = new Dompdf();
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    while (ob_get_level()) {
        ob_end_clean();
    }

    $dompdf->stream("Faculty_assigned_assets.pdf", [
        "Attachment" => true
    ]);
    exit;

} catch (Exception $e) {
    while (ob_get_level()) {
        ob_end_clean();
    }
    echo json_encode(["error" => $e->getMessage()]);
    exit;
}
