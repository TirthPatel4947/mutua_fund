@extends('common_template')

@section('content')
<title>Import Excel</title>
<div class="container">
    <h2>Import Excel File</h2>

    <!-- Import Form Section -->
    <div id="importSection">
        <a href="{{ asset('sample/sample_fund_data.xlsx') }}" class="btn btn-secondary mb-3" download>
            <i class="feather icon-download"></i> Download Sample File
        </a>

        <form id="importForm" enctype="multipart/form-data">
            @csrf
            <input type="file" name="excel_file" id="excelFile" class="form-control mb-3" accept=".xlsx, .xls">
            <button type="submit" class="btn btn-success">Upload & Insert</button>
        </form>

        <div id="message"></div>
    </div>

    <!-- Back Button (Initially Hidden) -->
    <button id="backButton" class="btn btn-warning d-none mt-3">Back</button>

    <!-- Inserted Data List (Initially Hidden) -->
    <div id="dataSection" class="d-none">
        <h3 class="mt-4">Inserted Records</h3>
        <table class="table table-bordered" id="insertedDataTable">
            <thead>
                <tr>
                    <th>Portfolio</th>
                    <th>Fund Name</th>
                    <th>Date</th>
                    <th>Unit</th> <!--  Added Unit Column -->
                    <th>Price Per Unit</th>
                    <th>Total</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody></tbody>

        </table>
        <div class="d-flex justify-content-end mt-3">
            <button id="submitButton" class="btn btn-primary btn-lg">Submit</button>
        </div>
    </div>
</div>

<script>
    document.getElementById('importForm').addEventListener('submit', function (e) {
        e.preventDefault();
        let formData = new FormData(this);
        let messageDiv = document.getElementById('message');

        fetch("{{ route('import.submit') }}", {
            method: "POST",
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            messageDiv.innerHTML = "";

            if (data.error) {
                messageDiv.innerHTML = `<p style="color:red;">${data.error}</p>`;
            } else {
                messageDiv.innerHTML = `<p style="color:green;">${data.message}</p>`;
                document.getElementById('importSection').classList.add('d-none');
                document.getElementById('dataSection').classList.remove('d-none');
                document.getElementById('backButton').classList.remove('d-none');
                loadInsertedData();
            }
            document.getElementById('importForm').reset();
        })
        .catch(error => console.error('Error:', error));
    });

    function loadInsertedData() {
        fetch("{{ route('import.list') }}")
        .then(response => response.json())
        .then(data => {
            let tableBody = document.querySelector("#insertedDataTable tbody");
            tableBody.innerHTML = "";

            if (data.length === 0) {
                tableBody.innerHTML = "<tr><td colspan='7' class='text-center'>No data available</td></tr>";
                return;
            }

            data.forEach(row => {
                tableBody.innerHTML += `<tr>
                    <td class="clickable ${!row.portfolio ? 'blank' : ''}" data-id="${row.id}" data-type="portfolio">
                        ${row.portfolio || 'Click to select'}
                    </td>
                    <td class="clickable ${!row.fund_name ? 'blank' : ''}" data-id="${row.id}" data-type="fund_name">
                        ${row.fund_name || 'Click to select'}
                    </td>
                    <td>${row.date}</td>
                    <td>${row.unit}</td>
                    <td>${row.price}</td>
                    <td>${row.total}</td>
                    <td>${row.status}</td>
                </tr>`;
            });

            attachClickEvents(); 
        })
        .catch(error => console.error('Error:', error));
    }

    function attachClickEvents() {
        document.querySelectorAll('.clickable').forEach(cell => {
            cell.addEventListener('click', function () {
                let cellType = this.dataset.type;
                let recordId = this.dataset.id;

                if ($(this).find('select.select2').length > 0) {
                    $(this).find('select.select2').select2('close');
                    return;
                }

                let dropdown = document.createElement('select');
                dropdown.classList.add("form-control", "select2");
                dropdown.innerHTML = "<option disabled selected>Search and select fund</option>";
                this.innerHTML = "";
                this.appendChild(dropdown);

                $(dropdown).select2({
                    width: '100%',
                    placeholder: "Search...",
                    allowClear: true,
                    minimumInputLength: 1,
                    ajax: {
                        url: `{{ url('/fetch-options') }}/${cellType}`,
                        dataType: 'json',
                        delay: 250,
                        data: params => ({ search: params.term || '' }),
                        processResults: data => ({
                            results: data.map(option => ({
                                id: option.id,
                                text: option.name
                            }))
                        })
                    }
                });

                setTimeout(() => $(dropdown).select2('open'), 100);

                $(dropdown).on('select2:close', function () {
                    let parentCell = $(dropdown).parent();
                    if (!parentCell.text().trim()) {
                        parentCell.html('Click to select');
                    }
                });

                $(dropdown).on('select2:select', function (e) {
                    let selectedValue = e.params.data.text;
                    let selectedId = e.params.data.id;
                    updateRecord(recordId, cellType, selectedValue, dropdown, selectedId);
                });
            });
        });
    }

    function updateRecord(id, field, value, dropdown, selectedId = null) {
        let updateData = { id, field, value };

        if (field === "portfolio") {
            updateData.field = "portfolio_id";
            updateData.value = selectedId;
        } else if (field === "fund_name") {
            updateData.field = "fundname_id";
            updateData.value = selectedId;
        }

        console.log("🔥 Sending data to API:", updateData);

        fetch("/update-record", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(updateData)
        })
        .then(response => response.json())
        .then(data => {
            console.log("✅ API Response:", data);
            if (data.success) {
                console.log("🎉 Record updated successfully!");
            } else {
                console.error("Error updating record:", data.error || data.message);
            }
        })
        .catch(error => console.error(" Fetch error:", error));
    }

    document.getElementById('submitButton').addEventListener('click', function () {
        let rows = document.querySelectorAll("#insertedDataTable tbody tr");
        let reportData = [];
        let missingFields = [];

        rows.forEach((row, index) => {
            let columns = row.querySelectorAll("td");

            if (columns.length >= 7) {
                let portfolio = columns[0].textContent.trim();
                let fundName = columns[1].textContent.trim();
                let date = columns[2].textContent.trim();
                let unit = columns[3].textContent.trim();
                let price = columns[4].textContent.trim();
                let status = columns[6].textContent.trim();

                if (!portfolio || !fundName || !date || !unit || !price || !status || portfolio === "Click to select" || fundName === "Click to select") {
                    missingFields.push(index + 1);
                    return;
                }

                let unitValue = parseFloat(unit);
                let priceValue = parseFloat(price);

                if (isNaN(unitValue) || isNaN(priceValue) || unitValue <= 0 || priceValue <= 0) {
                    missingFields.push(index + 1);
                    return;
                }

                reportData.push({
                    portfolio,
                    fund_name: fundName,
                    date,
                    unit: unitValue,
                    price: priceValue,
                    status
                });
            }
        });

        if (missingFields.length > 0) {
            alert(`Error: The following rows have missing or invalid values: ${missingFields.join(", ")}. Please complete all fields.`);
            return;
        }

        if (reportData.length === 0) {
            alert("No valid data to submit.");
            return;
        }

        console.log("Sending Data:", reportData);

        fetch("/report/store", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ report_data: reportData })
        })
        .then(response => response.json())
        .then(data => {
            console.log("Response Received:", data);
            if (data.success) {
                alert("Data successfully stored!");

                if (data.redirect_url) {
                    window.location.href = data.redirect_url;
                } else {
                    console.error("Redirect URL missing!");
                }

                document.getElementById('dataSection').classList.add('d-none');
                document.getElementById('importSection').classList.remove('d-none');
            } else {
                alert("Error storing data: " + (data.error || "Unknown error"));
            }
        })
        .catch(error => console.error("Error:", error));
    });

    loadInsertedData();
</script>


<style>
    .blank {
        background-color: #ffdddd !important;
        border: 1px solid #ff0000 !important;
        color: #ff0000 !important;
        font-weight: bold;
        text-align: center;
    }
</style>
@endsection