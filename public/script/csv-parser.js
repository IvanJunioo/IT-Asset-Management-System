// All functions are pure / accept state as parameters.
// No globals, no FIELDS references

export let csvHeaders = [];
export let parsedRows = [];

// DOM refs (shared across all CSV import pages)
export const dropZone = document.getElementById('drop-zone');
export const fileInput = document.getElementById('csv-file');
export const fileChosen = document.getElementById('file-chosen');
export const fileNameDisp = document.getElementById('file-name-display');
export const mappingSection = document.getElementById('mapping-section');
export const mappingTbody = document.getElementById('mapping-tbody');
export const previewStrip = document.getElementById('preview-strip');
export const rowCountEl = document.getElementById('row-count');
export const colCountEl = document.getElementById('col-count');
export const mapNotice = document.getElementById('map-notice');
export const resetBtn = document.getElementById('reset-button');
export const importForm = document.getElementById('import-form');
export const previewBtn = document.getElementById('preview-button');
export const previewSection = document.getElementById('preview-section');
export const previewThead = document.getElementById('preview-thead');
export const previewTbody = document.getElementById('preview-tbody');

// Drop zone + file input setup
export function initDropZone(FIELDS) {
  dropZone.addEventListener('dragover', e => { e.preventDefault(); dropZone.classList.add('dragover'); });
  dropZone.addEventListener('dragleave', () => dropZone.classList.remove('dragover'));
  dropZone.addEventListener('drop', e => {
    e.preventDefault();
    dropZone.classList.remove('dragover');
    const file = e.dataTransfer.files[0];
    if (file) handleFile(file, FIELDS);
  });

  fileInput.addEventListener('change', () => {
    if (fileInput.files[0]) handleFile(fileInput.files[0], FIELDS);
  });
}

export function handleFile(file, FIELDS) {
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
      // Mutate in place so importers that destructured these refs stay in sync
      csvHeaders.length = 0;
      csvHeaders.push(...(results.meta.fields || []));
      parsedRows.length = 0;
      parsedRows.push(...results.data);

      rowCountEl.textContent     = parsedRows.length;
      colCountEl.textContent     = csvHeaders.length;
      previewStrip.style.display = 'block';

      renderMappingTable(FIELDS);
      mappingSection.style.display = 'block';
    }
  });
}

// reset
export function initReset() {
  resetBtn.addEventListener('click', () => {
    fileInput.value              = '';
    fileChosen.style.display     = 'none';
    mappingSection.style.display = 'none';
    previewStrip.style.display   = 'none';
    previewSection.hidden        = true;
    mappingTbody.innerHTML       = '';
    mapNotice.textContent        = '';
    csvHeaders.length            = 0;
    parsedRows.length            = 0;
    const remarks = document.getElementById('remarks-field');
    if (remarks) remarks.value = '';
  });
}

// mapping table
export function renderMappingTable(FIELDS) {
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
    const select   = document.createElement('select');
    select.name          = `map_${field.key}`;
    select.dataset.field = field.key;

    const blank = document.createElement('option');
    blank.value       = '';
    blank.textContent = '— select column —';
    select.appendChild(blank);

    csvHeaders.forEach(h => {
      const opt = document.createElement('option');
      opt.value       = h;
      opt.textContent = h;
      if (autoMatch(field.key, h)) opt.selected = true;
      select.appendChild(opt);
    });

    select.addEventListener('change', () => syncSelectOptions(FIELDS));

    tdSelect.appendChild(select);
    tr.append(tdName, tdType, tdSelect);
    mappingTbody.appendChild(tr);
  });

  syncSelectOptions(FIELDS);
}

// hide already-chosen options in other selects
export function syncSelectOptions(FIELDS) {
  const selects = Array.from(mappingTbody.querySelectorAll('select'));

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

  validateMapping(FIELDS);
}

export function validateMapping(FIELDS) {
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

export function updateSelectStyle(select) {
  if (select.value) {
    select.classList.add('mapped');
    select.classList.remove('unmapped');
  } else {
    select.classList.remove('mapped');
    select.classList.add('unmapped');
  }
}

// helpers
export function autoMatch(fieldKey, csvHeader) {
  const norm = s => s.toLowerCase().replace(/[\s_\-]/g, '');
  const fk   = norm(fieldKey);
  const hdr  = norm(csvHeader);
  return fk === hdr || hdr.includes(fk) || fk.includes(hdr);
}

export function getMapping() {
  const mapping = {};
  mappingTbody.querySelectorAll('select').forEach(sel => {
    mapping[sel.dataset.field] = sel.value;
  });
  return mapping;
}

export function checkDuplicateMappings(FIELDS, mapping) {
  const mappedColumns = new Set();
  for (const f of FIELDS) {
    if (mapping[f.key]) {
      if (mappedColumns.has(mapping[f.key])) {
        return mapping[f.key]; // return the duplicate column name
      }
      mappedColumns.add(mapping[f.key]);
    }
  }
  return null; // no duplicates
}

export function normalizeDate(val) {
  if (!val) return '';

  // dd-mm-yyyy or dd/mm/yyyy
  const dmyMatch = val.match(/^(\d{2})[-\/](\d{2})[-\/](\d{4})$/);
  if (dmyMatch) {
    const [, dd, mm, yyyy] = dmyMatch;
    return `${yyyy}-${mm}-${dd}`;
  }

  // Already yyyy-mm-dd
  if (/^\d{4}-\d{2}-\d{2}$/.test(val)) return val;

  // Fall back to Date parsing
  const d = new Date(val);
  if (isNaN(d.getTime())) return val;
  return d.toISOString().split('T')[0];
}

// preview
export function renderPreview(FIELDS, mapping, getRowErrors) {
  if (parsedRows.length === 0) {
    alert('Please upload a CSV file first.');
    return;
  }

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
  parsedRows.slice(0, 10).forEach(row => {
    const tr = document.createElement('tr');

    const mapped = {};
    FIELDS.forEach(f => {
      mapped[f.key] = mapping[f.key] ? (row[mapping[f.key]] ?? '') : '';
    });

    if (getRowErrors(mapped).length > 0) tr.classList.add('preview-row-error');

    tr.innerHTML = mappedFields.map(f => {
      const val        = mapped[f.key];
      const isRequired = f.required || (f.key === 'assigned_to' && mapped.status === 'Assigned');
      const cellClass  = (val === '' && isRequired) ? 'cell-missing' : '';
      return `<td class="${cellClass}">${val || '<span class="cell-empty">—</span>'}</td>`;
    }).join('');

    previewTbody.appendChild(tr);
  });

  if (parsedRows.length > 10) {
    const tr = document.createElement('tr');
    tr.innerHTML = `<td colspan="${mappedFields.length}" class="preview-more">
      Showing 10 of ${parsedRows.length} rows
    </td>`;
    previewTbody.appendChild(tr);
  }

  previewSection.hidden = false;
  previewSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
}