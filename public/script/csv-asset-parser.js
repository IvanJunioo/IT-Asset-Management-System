import { searchAssets } from "./api.js";
import {
  parsedRows,
  importForm,
  previewBtn,
  initDropZone,
  initReset,
  getMapping,
  checkDuplicateMappings,
  normalizeDate,
  renderPreview,
} from "./csv-parser.js";

const FIELDS = [
  { key: 'procurement_number', label: 'Procurement No.',         required: true  },
  { key: 'property_number',    label: 'Property No.',            required: true  },
  { key: 'serial_number',      label: 'Serial No.',              required: false },
  { key: 'purchase_date',      label: 'Purchase Date',           required: true  },
  { key: 'detailed_specs',     label: 'Specifications',          required: true  },
  { key: 'price',              label: 'Price',                   required: true  },
  { key: 'status',             label: 'Status',                  required: true  },
  { key: 'assigned_to',        label: 'Assigned To',             required: false, note: 'required when status = Assigned' },
  { key: 'description',        label: 'Description',             required: false },
  { key: 'url',                label: 'Support Docs',            required: true  },
];

// init
initDropZone(FIELDS);
initReset();

// preview
previewBtn.addEventListener('click', () => {
  const mapping = getMapping();
  renderPreview(FIELDS, mapping, getAssetRowErrors);
});

// submit
importForm.addEventListener('submit', function(e) {
  e.preventDefault();

  const mapping = getMapping();

  const missingRequired = FIELDS
    .filter(f => f.required && !mapping[f.key])
    .map(f => f.label);

  if (missingRequired.length) {
    alert(`Please map all required fields:\n• ${missingRequired.join('\n• ')}`);
    return;
  }

  const duplicate = checkDuplicateMappings(FIELDS, mapping);
  if (duplicate) {
    alert(`Column "${duplicate}" is mapped to multiple fields. Please ensure each CSV column is only mapped once.`);
    return;
  }

  const remarks = document.getElementById('remarks-field').value;

  parseAssetCsv(parsedRows, mapping, async function(valid, errors) {
    if (errors.length > 0) {
      const errSummary = errors.map(e => `Row ${e.row}: ${e.errors.join(', ')}`).join('\n');
      alert(`${errors.length} row(s) had errors and were skipped:\n\n${errSummary}`);
    }

    if (valid.length === 0) {
      alert('No valid rows to import.');
      return;
    }

    // Pre-check duplicates against the DB
    try {
      const pnums = valid.map(r => r.property_number);
      const snums = valid.map(r => r.serial_number).filter(s => s);

      if (new Set(pnums).size !== pnums.length) throw new Error('Property numbers in your CSV must be unique.');
      if (new Set(snums).size !== snums.length) throw new Error('Serial numbers in your CSV must be unique.');

      const pnumChecks = pnums.map(async pnum => {
        const data = await searchAssets({ search: pnum });
        if (data[0]) throw new Error(`Property number ${pnum} already exists.`);
      });

      const snumChecks = snums.map(async snum => {
        const data = await searchAssets({ search: snum, check_snum: true });
        if (data[0]) throw new Error(`Serial number ${snum} already exists.`);
      });

      await Promise.all([...pnumChecks, ...snumChecks]);

    } catch (err) {
      alert(err.message);
      return;
    }

    const payload = valid.map(row => ({
      PropNum:      row.property_number,
      ProcNum:      row.procurement_number,
      SerialNum:    row.serial_number    || null,
      PurchaseDate: normalizeDate(row.purchase_date),
      Specs:        row.detailed_specs,
      Price:        row.price,
      Status:       row.status,
      ShortDesc:    row.description      || null,
      Remarks:      remarks,
      URL:          row.url              || null,
    }));

    fetch(`${window.location.origin}/api/index.php?resource=assets&action=add`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      credentials: 'same-origin',
      body: JSON.stringify({ assets: payload })
    })
      .then(res => {
        if (!res.ok) return res.json().then(err => { throw new Error(err.message ?? 'Import failed.'); });
        return res.json();
      })
      .then(() => {
        alert(`Successfully imported ${valid.length} asset(s).`);
        window.location.href = `${window.location.origin}/index.php?page=asset-manager`;
      })
      .catch(err => alert(err.message ?? 'Import failed. Please try again.'));
  });
});

// parser
function parseAssetCsv(rows, mapping, onComplete) {
  const valid  = [];
  const errors = [];

  rows.forEach((row, index) => {
    const rowNum = index + 2;

    const mapped = {
      procurement_number: row[mapping.procurement_number] ?? '',
      property_number:    row[mapping.property_number]    ?? '',
      serial_number:      row[mapping.serial_number]      ?? '',
      purchase_date:      row[mapping.purchase_date]      ?? '',
      detailed_specs:     row[mapping.detailed_specs]     ?? '',
      price:              row[mapping.price]              ?? '',
      status:             row[mapping.status]             ?? '',
      assigned_to:        row[mapping.assigned_to]        ?? '',
      description:        row[mapping.description]        ?? '',
      url:                row[mapping.url]                ?? '',
    };

    const rowErrors = getAssetRowErrors(mapped);

    if (rowErrors.length > 0) {
      errors.push({ row: rowNum, errors: rowErrors });
    } else {
      valid.push(mapped);
    }
  });

  onComplete(valid, errors);
}

// row validation
function getAssetRowErrors(mapped) {
  const errors = [];

  if (!mapped.procurement_number) errors.push('Missing Procurement Number');
  if (!mapped.property_number)    errors.push('Missing Property Number');
  if (!mapped.purchase_date)      errors.push('Missing Purchase Date');
  if (!mapped.detailed_specs)     errors.push('Missing Detailed Specification');
  if (!mapped.price)              errors.push('Missing Price');
  if (!mapped.status)             errors.push('Missing Status');
  if (!mapped.url)                errors.push('Missing Support Docs URL');

  if (mapped.status === 'Assigned' && !mapped.assigned_to) {
    errors.push('Missing Assigned User for Assigned asset');
  }

  return errors;
}