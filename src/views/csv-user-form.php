<!DOCTYPE html>
<html lang="en">
  <?php include __DIR__ . '/../partials/head.php'?>
  <link rel="stylesheet" href="<?= BASE_URL ?>css/components/card.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>css/components/forms.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>css/pages/csv-form.css">
<body>
  <?php include __DIR__ . '/../partials/header.php'?>

  <main>
    <div class="card">
      <h3>Import Users via CSV</h3>

      <form id="import-form" enctype="multipart/form-data">

        <div class="input-label">
          <label>CSV File</label>
          <div class="drop-zone" id="drop-zone">
            <input type="file" id="csv-file" name="csv_file" accept=".csv">
            <span class="drop-icon">📂</span>
            <span class="drop-label">Import or drop CSV file here</span>
            <p>Accepts <strong>.csv</strong> files only</p>
            <div class="file-chosen" id="file-chosen">✔ <span id="file-name-display"></span></div>
          </div>
        </div>

        <div id="mapping-section">
          <div class="section-title">Map CSV Headers to Fields</div>

          <div class="preview-strip" id="preview-strip">
            Detected <span id="row-count">0</span> data rows · <span id="col-count">0</span> columns
          </div>

          <table class="mapping-table" id="mapping-table">
            <thead>
              <tr>
                <th style="width:38%">Required Field</th>
                <th style="width:15%">Type</th>
                <th>Map to CSV Column</th>
              </tr>
            </thead>
            <tbody id="mapping-tbody">
            </tbody>
          </table>

          <div class="map-notice" id="map-notice"></div>
        </div>

        <button id="preview-button" type="button">
          Preview Data
        </button>

        <div id="preview-section" hidden="true">
          <div class="section-title">Data Preview</div>
          <div class="preview-table-container">
            <table class="preview-table" id="preview-table">
              <thead id="preview-thead"></thead>
              <tbody id="preview-tbody"></tbody>
            </table>
          </div>
        </div>

        <div class="input-label">
          <label for="remarks-field">Remarks <span class="field-optional">(optional)</span></label>
          <textarea id="remarks-field" name="remarks" placeholder="Add any notes about this import batch…"></textarea>
        </div>

        <button id="reset-button" type="button">
          Reset Changes
        </button>

        <button id="submit-button" type="submit" name="action" value="submit">
          Submit
        </button> 
      </form>
    </div>
  </main>

  <?php include __DIR__ . '/../partials/footer.php'?>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/PapaParse/5.4.1/papaparse.min.js"></script>
  <script src="<?= BASE_URL ?>script/csv-user-parser.js" type="module" defer></script>
</body>
</html>
