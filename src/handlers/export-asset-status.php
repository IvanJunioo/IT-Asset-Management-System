<?php
require __DIR__ . '/../../vendor/autoload.php'; 
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../repos/user.php';
require_once __DIR__ . '/../repos/asset.php';
require_once __DIR__ . '/../repos/assignment.php';

use Dompdf\Dompdf;

try {
    $statusName = $_GET['status'] ?? null;
    $repo = new AssetRepo($pdo);

    $status = null;
    if (!empty($statusName)) {
        $status = array_map(
            fn($s) => AssetStatus::from($s),
            explode(',', $statusName)
        );
    }

    $assets = $repo->search(new AssetSearchCriteria(status: $status));
    usort($assets, function ($a, $b) {
        return $b->purchaseDate <=> $a->purchaseDate
            ?: $a->procNum <=> $b->procNum;
    });

    $cssPath = __DIR__ . '/../../public/css/asset-pdf.css';
    $css = file_get_contents($cssPath);

    ob_start();
    include '../../public/template/condemn-unassigned-assets.php';
    $html = ob_get_clean();

    $html = "<style>{$css}</style>" . $html;

    $dompdf = new Dompdf();
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    if (ob_get_level()) {
        ob_end_clean();
    }

    $dompdf->stream(($statusName ?? "assets") . "_assets.pdf", [
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
