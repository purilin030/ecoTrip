let parsedRows = [];
let hasErrors = false;

// Event Listener for File Upload
document.getElementById('csvFileInput').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (!file) return;

    const reader = new FileReader();
    reader.onload = function(event) {
        parseCSV(event.target.result);
    };
    reader.readAsText(file);
});

function parseCSV(text) {
    const lines = text.split('\n').filter(line => line.trim() !== '');
    
    parsedRows = [];
    hasErrors = false;
    let validCount = 0;
    let errorCount = 0;
    
    const tbody = document.getElementById('previewTableBody');
    tbody.innerHTML = ''; 

    // Prepare Validation Arrays (LowerCase for case-insensitive check)
    const validCatsLower = validCategories.map(c => c.toLowerCase());
    const validCitiesLower = validCities.map(c => c.toLowerCase());
    const validDifficulties = ['easy', 'medium', 'hard'];
    const validStatuses = ['active', 'draft', 'inactive', 'archived'];

    // Start loop at 1 to skip Header Row
    for (let i = 1; i < lines.length; i++) {
        // Simple Split (Assuming no commas in text fields for this demo)
        const cols = lines[i].split(',').map(c => c.trim().replace(/^"|"$/g, '')); 

        // Map CSV Columns to Object
        const rowData = {
            Title: cols[0] || '',
            Category: cols[1] || '',
            Points: cols[2] || '0',
            Preview: cols[3] || '',
            City: cols[4] || '',
            Difficulty: cols[5] || 'Medium',
            Start: cols[6] || '',
            End: cols[7] || '',
            Status: cols[8] || 'Draft',
            Detailed: cols[9] || '',
            Photo: cols[10] || ''
        };

        // --- VALIDATION LOGIC ---
        let statusHtml = '<span class="status-ok">OK</span>';
        let rowError = false;

        // 1. Check Required Fields
        if (!rowData.Title || !rowData.Start || !rowData.End) {
            statusHtml = '<span class="status-error">Missing Req Fields</span>';
            rowError = true;
        }
        // 2. Check Category (Case Insensitive)
        else if (!validCatsLower.includes(rowData.Category.toLowerCase())) {
            statusHtml = `<span class="status-error">Invalid Cat: ${rowData.Category}</span>`;
            rowError = true;
        }
        // 3. Check City (Case Insensitive)
        else if (!validCitiesLower.includes(rowData.City.toLowerCase())) {
            statusHtml = `<span class="status-error">Invalid City: ${rowData.City}</span>`;
            rowError = true;
        }
        // 4. Check Difficulty
        else if (!validDifficulties.includes(rowData.Difficulty.toLowerCase())) {
            statusHtml = `<span class="status-error">Invalid Diff</span>`;
            rowError = true;
        }

        if (rowError) {
            hasErrors = true;
            errorCount++;
        } else {
            validCount++;
            parsedRows.push(rowData);
        }

        // --- RENDER TABLE ROW ---
        const tr = document.createElement('tr');
        if (rowError) tr.classList.add('bg-red-50');

        tr.innerHTML = `
            <td class="row-num text-gray-500">${i}</td>
            <td class="font-medium text-gray-900">${rowData.Title}</td>
            <td>${rowData.Category}</td>
            <td>${rowData.Points}</td>
            <td class="truncate max-w-[120px]" title="${rowData.Preview}">${rowData.Preview}</td>
            <td>${rowData.City}</td>
            <td><span class="capitalize">${rowData.Difficulty}</span></td>
            <td>${rowData.Start}</td>
            <td>${rowData.End}</td>
            <td><span class="px-2 py-0.5 rounded text-xs bg-gray-200">${rowData.Status}</span></td>
            <td class="truncate max-w-[120px]" title="${rowData.Detailed}">${rowData.Detailed}</td>
            <td class="text-xs text-gray-500 truncate max-w-[80px]">${rowData.Photo}</td>
            <td class="sticky right-0 bg-white border-l text-center">${statusHtml}</td>
        `;
        tbody.appendChild(tr);
    }

    // Update Counters
    document.getElementById('validCount').textContent = validCount;
    document.getElementById('errorCount').textContent = errorCount;

    // Enable/Disable Import Button
    const importBtn = document.getElementById('importBtn');
    if (validCount > 0) {
        importBtn.disabled = false;
        importBtn.classList.remove('opacity-50', 'cursor-not-allowed');
        importBtn.classList.add('hover:bg-green-700');
    } else {
        importBtn.disabled = true;
        importBtn.classList.add('opacity-50', 'cursor-not-allowed');
        importBtn.classList.remove('hover:bg-green-700');
    }
}

function processImport() {
    if (parsedRows.length === 0) return;

    const btn = document.getElementById('importBtn');
    const originalText = btn.innerText;
    btn.innerText = "Importing...";
    btn.disabled = true;

    fetch('csv_handler.php?action=import', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ rows: parsedRows })
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {
            alert(data.message);
            window.location.href = 'manage_challenge.php';
        } else {
            alert('Import Error: ' + JSON.stringify(data.errors));
            btn.innerText = originalText;
            btn.disabled = false;
        }
    })
    .catch(err => {
        console.error(err);
        alert('System error during import.');
        btn.innerText = originalText;
        btn.disabled = false;
    });
}