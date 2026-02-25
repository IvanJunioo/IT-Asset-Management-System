<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../repos/user.php';
require_once __DIR__ . '/../repos/asset.php';
require_once __DIR__ . '/../repos/assignment.php';

use Dompdf\Dompdf;

final class ExportHandler
{
    public function __construct(
        private readonly PDO $pdo
    ) {}

    private function generatePdf(string $template, array $data, string $filename, bool $forDownload, ?string $filepath = null): void
    {
        $cssPath = __DIR__ . '/../../public/css/asset-pdf.css';
        $css = file_get_contents($cssPath);

        ob_start();
        extract($data);
        include "../../public/template/{$template}.php";
        $html = ob_get_clean();

        $html = "<style>{$css}</style>" . $html;

        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        if ($forDownload) {
            if (ob_get_length()) ob_end_clean();
    
            $dompdf->stream($filename, [
                "Attachment" => true
            ]);
    
            exit;
        }

        if ($filepath) {
            file_put_contents($filepath, $dompdf->output());
        }
    }

    public function exportAssetsByStatus(?string $statusName): void
    {
        $repo = new AssetRepo($this->pdo);
        $status = null;

        if (!empty($statusName)) {
            $status = array_map(
                fn($s) => AssetStatus::from($s),
                explode(',', $statusName)
            );
        }

        $assets = $repo->search(
            new AssetSearchCriteria(status: $status)
        );

        usort($assets, function ($a, $b) {
            return $b->purchaseDate <=> $a->purchaseDate
                ?: $a->procNum <=> $b->procNum;
        });

        $this->generatePdf(
            "condemn-unassigned-assets",
            ["assets" => $assets,
            "statusName" => $statusName],
            $statusName . "_assets.pdf",
            true
        );
    }

    public function exportUserAssignedAssets(?string $userParam): void
    {
        session_start();

        $userRepo = new UserRepo($this->pdo);
        $assignRepo = new AssignmentRepo($this->pdo);

        $user = $userRepo->identify($_SESSION['user_id']);

        if ($userParam) {
            $user = $userRepo->identify($userParam);
        }

        $data = [];

        $assets = $assignRepo->getAssignedAssets($user);

        foreach ($assets as $asset) {
            $data[] = [
                'asset' => [
                    $asset,
                    explode(' ', $assignRepo->getAssignmentDate($asset))[0]
                ]
            ];
        }

        $this->generatePdf(
            "assigned-assets",
            ["data" => $data,
            "assets" => $assets],
            $user->name->last . "_assigned_assets.pdf",
            true
        );
    }

    public function exportFacultyAssignedAssets(?string $usersParam): void
    {
        $userRepo = new UserRepo($this->pdo);
        $assignRepo = new AssignmentRepo($this->pdo);

        $usersID = [];

        if (!empty($usersParam)) {
            $usersID = array_filter(explode(",", $usersParam));
        }

        $data = [];

        foreach ($usersID as $id) {

            $user = $userRepo->identify($id);
            if (!$user) continue;

            $assets = $assignRepo->getAssignedAssets($user);

            usort($assets, function ($a, $b) use ($assignRepo) {
                $dateA = strtotime($assignRepo->getAssignmentDate($a));
                $dateB = strtotime($assignRepo->getAssignmentDate($b));

                return $dateB <=> $dateA;
            });

            $assetDates = [];

            foreach ($assets as $asset) {
                $assetDates[$asset->propNum] =
                    explode(' ', $assignRepo->getAssignmentDate($asset))[0];
            }

            $data[] = [
                "user" => $user,
                "assets" => $assets,
                "assignDates" => $assetDates
            ];
        }

        $this->generatePdf(
            "faculty-assigned-assets",
            ["data" => $data],
            "Faculty_assigned_assets.pdf",
            true
        );
    }

    public function exportMultipleFiles(string $usersParam): void
    {
        
    }
}