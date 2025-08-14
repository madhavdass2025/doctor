document.addEventListener('DOMContentLoaded', function() {

    // --- 1. Tabbed Interface Logic ---
    const tabLinks = document.querySelectorAll('.tab-link');
    const tabContents = document.querySelectorAll('.tab-content');
    const previewTabLink = document.querySelector('[data-tab="#preview"]');

    tabLinks.forEach(tab => {
        tab.addEventListener('click', function(event) {
            event.preventDefault();

            // If the preview tab is clicked, generate the preview content first
            if (tab === previewTabLink) {
                generatePreview();
            }

            // Deactivate all tabs
            tabLinks.forEach(t => t.classList.remove('active'));
            tabContents.forEach(c => c.classList.remove('active'));

            // Activate the clicked tab and its content
            tab.classList.add('active');
            const targetContent = document.querySelector(tab.dataset.tab);
            if (targetContent) {
                targetContent.classList.add('active');
            }
        });
    });

    // --- 2. Generic Dynamic Row Addition ---
    // Use event delegation on a parent element
    document.querySelector('.container').addEventListener('click', function(event) {
        if (event.target.classList.contains('add-row-btn')) {
            const templateId = event.target.dataset.templateId;
            const targetTbodySelector = event.target.dataset.targetTbody;

            const template = document.getElementById(templateId);
            const tbody = document.querySelector(targetTbodySelector);

            if (template && tbody) {
                const newRow = template.content.cloneNode(true);
                tbody.appendChild(newRow);
            }
        }
    });

    // --- 3. Generic Dynamic Row Removal ---
    document.querySelector('.container').addEventListener('click', function(event) {
        if (event.target.classList.contains('remove-row')) {
            event.target.closest('tr').remove();
        }
    });

    // --- 4. Preview Generation Function ---
    function generatePreview() {
        const previewContent = document.getElementById('preview-content');
        let html = '';

        // Helper to get text from a select element, returns empty string if not found
        const getSelectText = (selectElement) => {
            if (selectElement && selectElement.selectedIndex > 0) {
                return selectElement.options[selectElement.selectedIndex].text;
            }
            return '';
        };

        // Vitals and Diagnosis
        const temp = document.querySelector('[name="temperature"]').value.trim();
        const weightKg = document.querySelector('[name="weight_kg"]').value.trim();
        const weightG = document.querySelector('[name="weight_g"]').value.trim();
        const diagnosis = document.querySelector('[name="diagnosis_notes"]').value.trim();
        html += `<h4>Diagnosis & Vitals <button type="button" class="btn edit-btn" data-edit-tab="#diagnosis">Edit</button></h4>`;
        html += `<p><strong>Temperature:</strong> ${temp || '<em>Not provided</em>'}</p>`;
        html += `<p><strong>Weight:</strong> ${weightKg || '0'} kg ${weightG || '0'} g</p>`;
        html += `<div><strong>Diagnosis Notes:</strong><br>${diagnosis ? diagnosis.replace(/\n/g, '<br>') : '<em>Not provided</em>'}</div>`;
        html += '<hr>';

        // Medicines
        html += `<h4>Medications <button type="button" class="btn edit-btn" data-edit-tab="#medicine">Edit</button></h4>`;
        const medicineRows = document.querySelectorAll('#medicine-table tbody tr');
        if (medicineRows.length > 0) {
            html += '<ul>';
            medicineRows.forEach(row => {
                const medName = getSelectText(row.querySelector('[name="medicine_id[]"]'));
                const otherMed = row.querySelector('[name="medicine_other_name[]"]').value.trim();
                const dosage = row.querySelector('[name="dosage[]"]').value.trim();
                const freq = getSelectText(row.querySelector('[name="frequency[]"]'));
                const finalMedName = otherMed || medName;
                if (finalMedName) { // Only show if a medicine was actually entered
                    html += `<li><strong>${finalMedName}</strong>: ${dosage || 'N/A'}, ${freq || 'N/A'}</li>`;
                }
            });
            html += '</ul>';
        } else {
            html += '<p>No medication prescribed.</p>';
        }
        html += '<hr>';

        // Injections, Surgery, Scans
        html += `<h4>Procedures <button type="button" class="btn edit-btn" data-edit-tab="#procedures">Edit</button></h4>`;
        // Injections
        const injectionRows = document.querySelectorAll('#injections-table tbody tr');
        if (injectionRows.length > 0) {
            html += '<p><strong>Injections:</strong></p><ul>';
            injectionRows.forEach(row => {
                const injName = getSelectText(row.querySelector('[name="injection_id[]"]'));
                const otherInj = row.querySelector('[name="injection_other_name[]"]').value.trim();
                const dosage = row.querySelector('[name="injection_dosage[]"]').value.trim();
                const finalInjName = otherInj || injName;
                if(finalInjName){
                    html += `<li><strong>${finalInjName}</strong>: ${dosage || 'N/A'}</li>`;
                }
            });
            html += '</ul>';
        }

        // Surgery
        const surgeryName = getSelectText(document.querySelector('[name="surgery_id"]'));
        const otherSurgery = document.querySelector('[name="surgery_other_name"]').value.trim();
        const surgeryNotes = document.querySelector('[name="surgery_notes"]').value.trim();
        const finalSurgeryName = otherSurgery || surgeryName;
        if(finalSurgeryName) {
             html += `<p><strong>Surgery:</strong><br><strong>${finalSurgeryName}</strong>: ${surgeryNotes ? surgeryNotes.replace(/\n/g, '<br>') : '<em>No notes.</em>'}</p>`;
        }

        // Scans
        const scanName = getSelectText(document.querySelector('[name="scan_id"]'));
        const otherScan = document.querySelector('[name="scan_other_name"]').value.trim();
        const scanNotes = document.querySelector('[name="scan_notes"]').value.trim();
        const finalScanName = otherScan || scanName;
        if(finalScanName) {
             html += `<p><strong>Scans:</strong><br><strong>${finalScanName}</strong>: ${scanNotes ? scanNotes.replace(/\n/g, '<br>') : '<em>No notes.</em>'}</p>`;
        }
        html += '<hr>';


        // Lab Tests
        html += `<h4>Lab Tests <button type="button" class="btn edit-btn" data-edit-tab="#lab-tests">Edit</button></h4>`;
        const testRows = document.querySelectorAll('#lab-tests-table tbody tr');
        if (testRows.length > 0) {
            html += '<ul>';
            testRows.forEach(row => {
                const testName = getSelectText(row.querySelector('[name="lab_test_id[]"]'));
                const otherTest = row.querySelector('[name="lab_test_other_name[]"]').value.trim();
                const instructions = row.querySelector('[name="lab_test_instructions[]"]').value.trim();
                const finalTestName = otherTest || testName;
                if(finalTestName){
                    html += `<li><strong>${finalTestName}</strong>: ${instructions || '<em>No instructions.</em>'}</li>`;
                }
            });
            html += '</ul>';
        } else {
            html += '<p>No lab tests prescribed.</p>';
        }

        previewContent.innerHTML = html;
    }

    // --- 5. "Edit from Preview" Logic ---
    document.getElementById('preview-content').addEventListener('click', function(event) {
        if (event.target.classList.contains('edit-btn')) {
            const targetTabId = event.target.dataset.editTab;
            const targetTabLink = document.querySelector(`.tab-link[data-tab="${targetTabId}"]`);
            if (targetTabLink) {
                // We don't need to generate preview again when clicking edit
                // Deactivate all tabs
                tabLinks.forEach(t => t.classList.remove('active'));
                tabContents.forEach(c => c.classList.remove('active'));
                // Activate the target tab
                targetTabLink.classList.add('active');
                document.querySelector(targetTabId).classList.add('active');
            }
        }
    });

});
