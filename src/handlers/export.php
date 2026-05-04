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

  private function generatePdf(
    string $template, 
    array $data, 
    string $filename, 
    bool $forDownload, 
    ?string $filepath = null
  ): void {
    $cssPath = __DIR__ . '/../../public/css/asset-pdf.css';
    $css = file_get_contents($cssPath);

    ob_start();
    extract($data);
    include __DIR__ . "/../../src/template/{$template}.php";
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

  public function exportAssetsByStatus(?string $statusName, bool $add_remarks): void {
    $status = null;

    if (!empty($statusName)) {
      $status = array_map(
        fn($s) => AssetStatus::from($s),
        explode(',', $statusName)
      );
    }

    $assets = $this->assetRepo->search(new AssetSearchCriteria(status: $status));

    usort($assets, function ($a, $b) {
      return $b->purchaseDate <=> $a->purchaseDate
        ?: $a->procNum <=> $b->procNum;
    });

    $this->generatePdf(
      template: "condemn-unassigned-assets",
      data: [
        "assets"      => $assets,
        "statusName"  => $statusName,
        "add_remarks" => $add_remarks,
      ],
      filename: $statusName . "_assets.pdf",
      forDownload: true,
    );
  }

  public function exportUserAssignedAssets(?string $userParam, bool $add_remarks): void {
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
      template: "assigned-assets",
      data: [
        "user"        => $user,
        "data"        => $data,
        "assets"      => $assets,
        "add_remarks" => $add_remarks,
      ],
      filename: $user->name->last . "_assigned_assets.pdf",
      forDownload: true,
    );
  }

  public function exportFacultyAssignedAssets(?string $usersParam, bool $add_remarks): void {
    $usersID = [];

    if (!empty($usersParam)) {
      $usersID = array_filter(explode(",", $usersParam));
    }

    $data = [];

    foreach ($usersID as $id) {
      $user = $this->userRepo->identify($id);

      $assets = $this->assignRepo->getAssignedAssets($user);

      usort($assets, function ($a, $b) {
        $dateA = strtotime($this->assignRepo->getAssignmentDate($a));
        $dateB = strtotime($this->assignRepo->getAssignmentDate($b));

        return $dateB <=> $dateA;
      });

      $assetDates = [];

      foreach ($assets as $asset) {
        $assetDates[$asset->propNum] = explode(' ', $this->assignRepo->getAssignmentDate($asset))[0];
      }

      $data[] = [
        "user"        => $user,
        "assets"      => $assets,
        "assignDates" => $assetDates,
      ];
    }

    $this->generatePdf(
      template: "faculty-assigned-assets",
      data: [
        "data"        => $data,
        "add_remarks" => $add_remarks,
      ],
      filename: "Faculty_assigned_assets.pdf",
      forDownload: true,
    );
  }
}
