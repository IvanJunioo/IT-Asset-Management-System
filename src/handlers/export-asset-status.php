
<?php
require '../../vendor/autoload.php'; 
require_once '../../config/config.php';
require_once '../repos/user.php';
require_once '../repos/asset.php';
require_once '../repos/assignment.php';

ob_start();
use Dompdf\Dompdf;

try {
    $statusName = $_GET['status'] ?? null;
    $repo = new AssetRepo($pdo);
    $status = $statusName !== ""? array_map("AssetStatus::from", explode(',', $statusName)) : null;
    $assets = $repo->search(new AssetSearchCriteria(status: $status));
    usort($assets, function ($a, $b) {
        return $b->purchaseDate <=> $a->purchaseDate ?:
        $a->procNum <=> $b->procNum;
    });

    $cssPath = __DIR__ . '/../../public/css/asset-pdf.css';
    $css = file_get_contents($cssPath);
    include '../template/condemn-unassigned-assets.php';
    $html = ob_get_clean();
    $html = "<style>{$css}</style>" . $html;

    $dompdf = new Dompdf();
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();
    if (ob_get_length()) ob_end_clean();

    $dompdf->stream($statusName."_assets.pdf", ["Attachment" => true]);
    exit;

} catch (Exception $e) {
    if (ob_get_length()) ob_end_clean();
    echo json_encode(["error" => $e->getMessage()]);
    exit;
}


