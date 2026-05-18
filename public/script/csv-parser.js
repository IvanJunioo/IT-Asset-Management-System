function parseAssetCsv(csvFile, onComplete) {
  /*
    Parses the given CSV file (for assets) and validates each row for required fields.
    Expected fields present in the csvFile:
    - Procurement Number (procnum) 
    - Property Number (propnum)
    - Purchase Date (purchasedate)
    - Detailed Specification (specs)
    - Price (price)
    - Status (status)
    - Assigned User (assignee) [required if status is "Assigned"]
  */

  Papa.parse(csvFile, {
    header: true,
    skipEmptyLines: true,
    transformHeader : header => header.trim().toLowerCase().replace(/\s+/g, '_'),
    complete: function(results) {
      const valid = [];
      const errors = [];
      
      results.data.forEach((row, index) => {
        const rowNum = index + 2; // account for header and 0-indexing
        const rowErrors = [];

        if (!row.procnum) rowErrors.push("Missing Procurement Number");
        if (!row.propnum) rowErrors.push("Missing Property Number");
        if (!row.purchasedate) rowErrors.push("Missing Purchase Date");
        if (!row.specs) rowErrors.push("Missing Detailed Specification");
        if (!row.price) rowErrors.push("Missing Price");
        if (!row.status) rowErrors.push("Missing Status");

        if (row.status === "Assigned" && !row.assignee) {
          rowErrors.push("Missing Assigned User for Assigned asset");
        }

        if (rowErrors.length > 0) {
          errors.push({row: rowNum, errors: rowErrors});
        } else {
          valid.push(row);
        }
      });

      onComplete(valid, errors);
    }
  })
}

function parseUserCsv(csvFile, onComplete) {
  /*
    Parses the given CSV file (for users) and validates each row for required fields.
    Expected fields present in the csvFile:
    - First Name (fname) 
    - Last Name (lname)
    - Email (email)
    - Role (role)
  */

  Papa.parse(csvFile, {
    header: true,
    skipEmptyLines: true,
    transformHeader : header => header.trim().toLowerCase().replace(/\s+/g, '_'),
    complete: function(results) {
      const valid = [];
      const errors = [];
      
      results.data.forEach((row, index) => {
        const rowNum = index + 2; // account for header and 0-indexing
        const rowErrors = [];

        if (!row.fname) rowErrors.push("Missing First Name");
        if (!row.lname) rowErrors.push("Missing Last Name");
        if (!row.email) rowErrors.push("Missing Email");
        if (!row.role) rowErrors.push("Missing Role");

        if (rowErrors.length > 0) {
          errors.push({row: rowNum, errors: rowErrors});
        } else {
          valid.push(row);
        }
      });

      onComplete(valid, errors);
    }
  })
}

