const FIELDS = [
  { key: 'procurement_number', label: 'Procurement Number',     required: true  },
  { key: 'property_number',    label: 'Property Number',        required: true  },
  { key: 'serial_number',      label: 'Serial Number',          required: false },
  { key: 'purchase_date',      label: 'Purchase Date',          required: true  },
  { key: 'detailed_specs',     label: 'Detailed Specifications', required: true  },
  { key: 'price',              label: 'Price',                  required: true  },
  { key: 'status',             label: 'Status',                 required: true  },
  { key: 'assigned_to',        label: 'Assigned To',            required: false, note: 'required when status = Assigned' },
  { key: 'description',        label: 'Description',            required: false },
];

let mappedColumns = new Set();
let csvHeaders = [];
let parsedRows = [];

// DOM elemenst
const dropZone = document.getElementById('drop-zone');
const fileInput = document.getElementById('csv-file');
const fileChosen = document.getElementById('file-chosen');
const fileNameDisp = document.getElementById('file-name-display');
const mappingSection = document.getElementById('mapping-section');
const mappingTbody = document.getElementById('mapping-tbody');
const previewStrip = document.getElementById('preview-strip');
const rowCountEl = document.getElementById('row-count');
const colCountEl = document.getElementById('col-count');
const mapNotice = document.getElementById('map-notice');
const resetBtn = document.getElementById('reset-button');
const importForm = document.getElementById('import-form');
const previewBtn = document.getElementById('preview-button');
const previewSection = document.getElementById('preview-section');
const previewThead = document.getElementById('preview-thead');
const previewTbody = document.getElementById('preview-tbody');

dropZone.addEventListener('dragover',  e => { e.preventDefault(); dropZone.classList.add('dragover'); });
dropZone.addEventListener('dragleave', () => dropZone.classList.remove('dragover'));
dropZone.addEventListener('drop', e => {
  e.preventDefault();
  dropZone.classList.remove('dragover');
  const file = e.dataTransfer.files[0];
  if (file) handleFile(file);
});

fileInput.addEventListener('change', () => {
  if (fileInput.files[0]) handleFile(fileInput.files[0]);
});

resetBtn.addEventListener('click', () => {
  fileInput.value              = '';
  fileChosen.style.display     = 'none';
  mappingSection.style.display = 'none';
  previewStrip.style.display   = 'none';
  mappingTbody.innerHTML       = '';
  mapNotice.textContent        = '';
  csvHeaders                   = [];
  parsedRows                   = [];
  document.getElementById('remarks-field').value = '';
  mappedColumns.clear();
  previewSection.hidden = true;
});

importForm.addEventListener('submit', function(e) {
  e.preventDefault();

  const mapping = {};
  mappingTbody.querySelectorAll('select').forEach(sel => {
    mapping[sel.dataset.field] = sel.value;
  });

  const missingRequired = FIELDS
    .filter(f => f.required && !mapping[f.key])
    .map(f => f.label);

  if (missingRequired.length) {
    alert(`Please map all required fields:\n• ${missingRequired.join('\n• ')}`);
    return;
  }

  for (const f of FIELDS) {
    if (mapping[f.key]) {
      if (mappedColumns.has(mapping[f.key])) {
        alert(`Column "${mapping[f.key]}" is mapped to multiple fields. Please ensure each CSV column is only mapped once.`);
        return;
      }
      mappedColumns.add(mapping[f.key]);
    }
  }

  const remarks = document.getElementById('remarks-field').value;

  parseAssetCsv(parsedRows, mapping, function(valid, errors) {
    if (errors.length > 0) {
      // TODO: show these in the UI instead of console
      console.warn('Row errors:', errors);
    }

    if (valid.length === 0) {
      alert('No valid rows to import.');
      return;
    }

    // TODO: parse to JSON and send to server
  });
});

previewBtn.addEventListener('click', () => {
  if (parsedRows.length === 0) {
    alert('Please upload a CSV file first.');
    return;
  }

  const mapping = {};
  mappingTbody.querySelectorAll('select').forEach(sel => {
    mapping[sel.dataset.field] = sel.value;
  });

  const mappedFields = FIELDS.filter(f => mapping[f.key]);

  if (mappedFields.length === 0) {
    alert('Please map at least one field before previewing.');
    return;
  }

  previewThead.innerHTML = '';
  const headerRow = document.createElement('tr');
  headerRow.innerHTML = mappedFields.map(f => `<th>${f.label}</th>`).join('');
  previewThead.appendChild(headerRow);

  previewTbody.innerHTML = '';
  const sampleRows = parsedRows.slice(0, 10);

  sampleRows.forEach((row, index) => {
    const tr = document.createElement('tr');

    const mapped = {};
    FIELDS.forEach(f => {
      mapped[f.key] = mapping[f.key] ? (row[mapping[f.key]] ?? '') : '';
    });

    const rowErrors = getAssetRowErrors(mapped);
    if (rowErrors.length > 0) tr.classList.add('preview-row-error');

    tr.innerHTML =
      mappedFields.map(f => {
        const val = mapped[f.key];
        const empty = val === '';
        const isRequired = f.required || (f.key === 'assigned_to' && mapped.status === 'Assigned');
        const cellClass = (empty && isRequired) ? 'cell-missing' : '';
        return `<td class="${cellClass}">${val || '<span class="cell-empty">—</span>'}</td>`;
      }).join('');

    previewTbody.appendChild(tr);
  });

  if (parsedRows.length > 10) {
    const tr = document.createElement('tr');
    tr.innerHTML = `<td colspan="${mappedFields.length + 1}" class="preview-more">
      Showing 10 of ${parsedRows.length} rows
    </td>`;
    previewTbody.appendChild(tr);
  }

  previewSection.hidden = false;
  previewSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
});

function handleFile(file) {
  if (!file.name.endsWith('.csv')) {
    alert('Please upload a .csv file.');
    return;
  }

  fileNameDisp.textContent = file.name;
  fileChosen.style.display = 'block';

  Papa.parse(file, {
    header: true,
    skipEmptyLines: true,
    complete(results) {
      csvHeaders = results.meta.fields || [];
      parsedRows = results.data;

      rowCountEl.textContent = parsedRows.length;
      colCountEl.textContent = csvHeaders.length;
      previewStrip.style.display = 'block';

      renderMappingTable();
      mappingSection.style.display = 'block';
    }
  });
}

function renderMappingTable() {
  mappingTbody.innerHTML = '';

  FIELDS.forEach(field => {
    const tr = document.createElement('tr');

    const tdName = document.createElement('td');
    tdName.innerHTML = `
      <span class="field-name">${field.label}</span>
      ${field.note ? `<br><span class="field-optional">${field.note}</span>` : ''}
    `;

    const tdType = document.createElement('td');
    tdType.innerHTML = field.required
      ? `<span class="required-badge">Required</span>`
      : `<span class="optional-badge">Optional</span>`;

    const tdSelect = document.createElement('td');
    const select = document.createElement('select');
    select.name = `map_${field.key}`;
    select.dataset.field = field.key;

    const blank = document.createElement('option');
    blank.value = '';
    blank.textContent = '— select column —';
    select.appendChild(blank);

    csvHeaders.forEach(h => {
      const opt = document.createElement('option');
      opt.value = h;
      opt.textContent = h;
      if (autoMatch(field.key, h)) opt.selected = true;
      select.appendChild(opt);
    });

    updateSelectStyle(select);
    select.addEventListener('change', () => syncSelectOptions());

    tdSelect.appendChild(select);
    tr.append(tdName, tdType, tdSelect);
    mappingTbody.appendChild(tr);
  });

  validateMapping();
  syncSelectOptions();
}

function autoMatch(fieldKey, csvHeader) {
  const norm = s => s.toLowerCase().replace(/[\s_\-]/g, '');
  const fk  = norm(fieldKey);
  const hdr = norm(csvHeader);
  return fk === hdr || hdr.includes(fk) || fk.includes(hdr);
}

function updateSelectStyle(select) {
  if (select.value) {
    select.classList.add('mapped');
    select.classList.remove('unmapped');
  } else {
    select.classList.remove('mapped');
    select.classList.add('unmapped');
  }
}

function validateMapping() {
  const selects = mappingTbody.querySelectorAll('select');
  const missing = [];

  selects.forEach(sel => {
    const field = FIELDS.find(f => f.key === sel.dataset.field);
    if (field && field.required && !sel.value) missing.push(field.label);
  });

  if (missing.length) {
    mapNotice.className   = 'map-notice error';
    mapNotice.textContent = `Required fields not mapped: ${missing.join(', ')}`;
  } else {
    mapNotice.className   = 'map-notice';
    mapNotice.textContent = 'All required fields are mapped.';
  }
}

function parseAssetCsv(rows, mapping, onComplete) {
  /*
    Validates already-parsed rows using the user's column mapping.
    mapping keys: 
    procurement_number, property_number, serial_number,
    purchase_date, detailed_specs, price, status,
    assigned_to, description
  */
  const valid  = [];
  const errors = [];

  rows.forEach((row, index) => {
    const rowNum   = index + 2; // skip header, 1-based index

    // Remap raw CSV columns -> named fields using the user's mapping
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
    };

    const rowErrors = getAssetRowErrors(mapped);

    if (rowErrors.length > 0) {
      errors.push({ row: rowNum, errors: rowErrors });
    } else {
      valid.push(mapped); // push the clean mapped object
    }
  });

  onComplete(valid, errors);
}

function parseUserCsv(csvFile, onComplete) {
  /*
    Parses and validates a users CSV.
    Expected columns (case-insensitive, spaces/underscores flexible):
    first_name, last_name, email, role
  */
  Papa.parse(csvFile, {
    header: true,
    skipEmptyLines: true,
    transformHeader: header => header.trim().toLowerCase().replace(/\s+/g, '_'),
    complete: function(results) {
      const valid  = [];
      const errors = [];

      results.data.forEach((row, index) => {
        const rowNum    = index + 2;
        const rowErrors = [];

        if (!row.first_name) rowErrors.push("Missing First Name");
        if (!row.last_name) rowErrors.push("Missing Last Name");
        if (!row.email) rowErrors.push("Missing Email");
        if (!row.role) rowErrors.push("Missing Role");

        if (rowErrors.length > 0) {
          errors.push({ row: rowNum, errors: rowErrors });
        } else {
          valid.push(row);
        }
      });

      onComplete(valid, errors);
    }
  });
}

function getAssetRowErrors(mapped) {
  /*
    Validates a single mapped row object and returns an array of error strings.
    mapped object keys: 
    procurement_number, property_number, serial_number,
    purchase_date, detailed_specs, price, status,
    assigned_to, description
  */
  const rowErrors = [];

  if (!mapped.procurement_number) rowErrors.push("Missing Procurement Number");
  if (!mapped.property_number) rowErrors.push("Missing Property Number");
  if (!mapped.purchase_date) rowErrors.push("Missing Purchase Date");
  if (!mapped.detailed_specs) rowErrors.push("Missing Detailed Specification");
  if (!mapped.price) rowErrors.push("Missing Price");
  if (!mapped.status) rowErrors.push("Missing Status");

  if (mapped.status === "Assigned" && !mapped.assigned_to) {
    rowErrors.push("Missing Assigned User for Assigned asset");
  }

  return rowErrors;
}

function syncSelectOptions() {
  const selects = Array.from(mappingTbody.querySelectorAll('select'));

  // Collect all currently chosen values (excluding blank)
  const chosen = new Set(
    selects.map(s => s.value).filter(v => v !== '')
  );

  selects.forEach(sel => {
    const currentVal = sel.value;

    Array.from(sel.options).forEach(opt => {
      if (opt.value === '') return;
      opt.hidden = chosen.has(opt.value) && opt.value !== currentVal;
    });

    updateSelectStyle(sel);
  });

  validateMapping();
}