import { searchUsers } from "./api.js";
import {
  parsedRows,
  importForm,
  previewBtn,
  initDropZone,
  initReset,
  getMapping,
  checkDuplicateMappings,
  renderPreview,
} from "./csv-parser.js";

const FIELDS = [
  { key: 'first_name',    label: 'First Name',    required: true  },
  { key: 'last_name',     label: 'Last Name',      required: true  },
  { key: 'email',         label: 'Email',          required: true  },
  { key: 'role',          label: 'Role',           required: true  },
  { key: 'active_status', label: 'Active Status',  required: false, note: 'defaults to Active if blank' },
];

const VALID_ROLES    = ['Faculty', 'Staff', 'Admin', 'SuperAdmin'];
const VALID_STATUSES = ['Active', 'Inactive'];

// init
initDropZone(FIELDS);
initReset();

// preview
previewBtn.addEventListener('click', () => {
  const mapping = getMapping();
  renderPreview(FIELDS, mapping, getUserRowErrors);
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

  parseUserCsv(parsedRows, mapping, async function(valid, errors) {
    if (errors.length > 0) {
      const errSummary = errors.map(e => `Row ${e.row}: ${e.errors.join(', ')}`).join('\n');
      alert(`${errors.length} row(s) had errors and were skipped:\n\n${errSummary}`);
    }

    if (valid.length === 0) {
      alert('No valid rows to import.');
      return;
    }

    // Pre-check duplicate emails against the DB
    try {
      const emails = valid.map(r => r.email);

      if (new Set(emails).size !== emails.length) {
        throw new Error('Duplicate emails found in your CSV. Each email must be unique.');
      }

      const emailChecks = emails.map(async email => {
        const data = await searchUsers({ search: email });
        if (data[0]) throw new Error(`Email ${email} already exists.`);
      });

      await Promise.all(emailChecks);

    } catch (err) {
      alert(err.message);
      return;
    }

    const payload = valid.map(row => ({
      'first-name':    row.first_name,
      'last-name':     row.last_name,
      'email':         row.email,
      'privilege':     row.role,
      'active-status': row.active_status || 'Active',
    }));

    fetch(`${window.location.origin}/api/index.php?resource=users&action=add`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      credentials: 'same-origin',
      body: JSON.stringify({ users: payload })
    })
      .then(res => {
        if (!res.ok) return res.json().then(err => { throw new Error(err.message ?? 'Import failed.'); });
        return res.json();
      })
      .then(() => {
        alert(`Successfully imported ${valid.length} user(s).`);
        window.location.href = `${window.location.origin}/index.php?page=user-manager`;
      })
      .catch(err => alert(err.message ?? 'Import failed. Please try again.'));
  });
});

// parser user
function parseUserCsv(rows, mapping, onComplete) {
  const valid  = [];
  const errors = [];

  rows.forEach((row, index) => {
    const rowNum = index + 2;

    const mapped = {
      first_name:    row[mapping.first_name]    ?? '',
      last_name:     row[mapping.last_name]      ?? '',
      email:         row[mapping.email]          ?? '',
      role:          row[mapping.role]           ?? '',
      active_status: row[mapping.active_status]  ?? '',
    };

    const rowErrors = getUserRowErrors(mapped);

    if (rowErrors.length > 0) {
      errors.push({ row: rowNum, errors: rowErrors });
    } else {
      valid.push(mapped);
    }
  });

  onComplete(valid, errors);
}

// row validation
function getUserRowErrors(mapped) {
  const errors = [];

  if (!mapped.first_name) errors.push('Missing First Name');
  if (!mapped.last_name)  errors.push('Missing Last Name');
  if (!mapped.email)      errors.push('Missing Email');
  if (!mapped.role)       errors.push('Missing Role');

  if (mapped.role && !VALID_ROLES.includes(mapped.role)) {
    errors.push(`Invalid role "${mapped.role}". Must be one of: ${VALID_ROLES.join(', ')}`);
  }

  if (mapped.active_status && !VALID_STATUSES.includes(mapped.active_status)) {
    errors.push(`Invalid status "${mapped.active_status}". Must be Active or Inactive`);
  }

  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  if (mapped.email && !emailRegex.test(mapped.email)) {
    errors.push(`Invalid email format: "${mapped.email}"`);
  }

  return errors;
}