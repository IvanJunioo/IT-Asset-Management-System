<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../repos/user.php';
require_once __DIR__ . '/../repos/asset.php';
require_once __DIR__ . '/../repos/assignment.php';

use Dompdf\Dompdf;

final class ExportHandler {
    public function __construct(
        private readonly UserRepoInterface $userRepo,
        private readonly AssetRepoInterface $assetRepo,
        private readonly AssignmentRepoInterface $assignRepo,
    ) {}

    private function generatePdf(string $template, array $data, string $filename, bool $forDownload, ?string $filepath = null): void {
        $cssPath = __DIR__ . '/../../public/css/asset-pdf.css';
        $css = file_get_contents($cssPath);

        ob_start();
        extract($data);
        include __DIR__ . "/../../public/template/{$template}.php";
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

    public function exportAssetsByStatus(?string $statusName): void {
        $status = null;

        if (!empty($statusName)) {
            $status = array_map(
                fn($s) => AssetStatus::from($s),
                explode(',', $statusName)
            );
        }

        $assets = $this->assetRepo->search(
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

    public function exportUserAssignedAssets(?string $userParam): void {
      $user = $this->userRepo->identify($userParam ? (int)$userParam : $_SESSION['user_id']);

      $data = [];

      $assets = $this->assignRepo->getAssignedAssets($user);

      foreach ($assets as $asset) {
        $data[] = [
          'asset' => [
            $asset,
            explode(' ', $this->assignRepo->getAssignmentDate($asset))[0]
          ]
        ];
      }

      $this->generatePdf(
        "assigned-assets",
        ["user" => $user,
        "data" => $data,
        "assets" => $assets],
        $user->name->last . "_assigned_assets.pdf",
        true
      );
    }

    public function exportFacultyAssignedAssets(?string $usersParam): void
    {
        $usersID = [];

        if (!empty($usersParam)) {
            $usersID = array_filter(explode(",", $usersParam));
        }

        $data = [];

        foreach ($usersID as $id) {

            $user = $this->userRepo->identify($id);
            if (!$user) continue;

            $assets = $this->assignRepo->getAssignedAssets($user);

            usort($assets, function ($a, $b) {
                $dateA = strtotime($this->assignRepo->getAssignmentDate($a));
                $dateB = strtotime($this->assignRepo->getAssignmentDate($b));

                return $dateB <=> $dateA;
            });

            $assetDates = [];

            foreach ($assets as $asset) {
                $assetDates[$asset->propNum] =
                    explode(' ', $this->assignRepo->getAssignmentDate($asset))[0];
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
        $userRepo = $this->userRepo;
        $assignRepo = $this->assignRepo;
        $usersID = array_filter(explode(",", $usersParam));
        $tempDir = __DIR__ . "/../../storage/exports/";

        if (!is_dir($tempDir)) {
            mkdir($tempDir, 0777, true);
        }

        $pdfFiles = [];
        foreach ($usersID as $id) {
            $user = $userRepo->identify($id);
            if (!$user) continue;
            $assets = $assignRepo->getAssignedAssets($user);
            $data = [];
            foreach ($assets as $asset) {
                $data[] = [
                    'asset' => [
                        $asset,
                        explode(' ', $assignRepo->getAssignmentDate($asset))[0]
                    ]
                ];
            }

            $filename = $tempDir . $user->name->last. "_assigned_assets.pdf";
            $this->generatePdf(
                "assigned-assets",
                [
                    "data" => $data,
                    "assets" => $assets
                ],
                $filename,
                false,
                $filename
            );
            $pdfFiles[] = $filename;
        }

        $zipFile = $tempDir . "assignments.zip";
        $zip = new ZipArchive();

        if ($zip->open($zipFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new Exception("Cannot create zip file");
        }

        foreach ($pdfFiles as $filePath) {
            $zip->addFile($filePath, basename($filePath));
        }

        $zip->close();

        if (ob_get_length()) ob_end_clean();

        header("Content-Type: application/zip");
        header("Content-Disposition: attachment; filename=Faculty_assignments.zip");
        header("Content-Length: " . filesize($zipFile));

        readfile($zipFile);

        foreach (glob($tempDir . "*") as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
        exit;
    }
}
