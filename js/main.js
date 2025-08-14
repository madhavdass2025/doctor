document.addEventListener('DOMContentLoaded', function() {
    // --- Tabbed Interface Logic ---
    const tabs = document.querySelectorAll('.tab-link');
    const tabContents = document.querySelectorAll('.tab-content');

    tabs.forEach(tab => {
        tab.addEventListener('click', function(event) {
            event.preventDefault();
            const target = document.querySelector(tab.dataset.tab);

            // Remove active class from all tabs and content
            tabs.forEach(t => t.classList.remove('active'));
            tabContents.forEach(c => c.classList.remove('active'));

            // Add active class to the clicked tab and corresponding content
            tab.classList.add('active');
            target.classList.add('active');
        });
    });

    // --- Dynamic Row Addition for Forms ---
    // Example for adding a new medicine row
    const addMedicineBtn = document.getElementById('add-medicine-row');
    const medicineTableBody = document.querySelector('#medicine-table tbody');

    if (addMedicineBtn && medicineTableBody) {
        addMedicineBtn.addEventListener('click', function() {
            const newRow = document.createElement('tr');
            // This HTML should be updated to match the final form fields
            newRow.innerHTML = `
                <td><input type="text" name="medicine_name[]" class="form-control"></td>
                <td><input type="text" name="dosage[]" class="form-control"></td>
                <td><input type="text" name="frequency[]" class="form-control"></td>
                <td><input type="text" name="total_units[]" class="form-control"></td>
                <td><select name="time[]" class="form-control"><option value="After Food">After Food</option><option value="Before Food">Before Food</option></select></td>
                <td><input type="text" name="type[]" class="form-control"></td>
                <td><button type="button" class="btn btn-danger remove-row">Remove</button></td>
            `;
            medicineTableBody.appendChild(newRow);
        });
    }

    // --- Event Delegation for Removing Rows ---
    document.body.addEventListener('click', function(event) {
        if (event.target.classList.contains('remove-row')) {
            event.target.closest('tr').remove();
        }
    });

});
