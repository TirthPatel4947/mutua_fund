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
                    <th>Price Per Unit</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<script>
document.getElementById('importForm').addEventListener('submit', function(e) {
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
            document.getElementById('importSection').classList.add('d-none');  // Hide Import Form
            document.getElementById('dataSection').classList.remove('d-none'); // Show Data Section
            document.getElementById('backButton').classList.remove('d-none');  // Show Back Button
            loadInsertedData();
        }

        document.getElementById('importForm').reset(); // Clear file input
    })
    .catch(error => console.error('Error:', error));
});

// Fetch and display inserted data
function loadInsertedData() {
    fetch("{{ route('import.list') }}")
    .then(response => response.json())
    .then(data => {
        let tableBody = document.querySelector("#insertedDataTable tbody");
        tableBody.innerHTML = "";

        if (data.length === 0) {
            tableBody.innerHTML = "<tr><td colspan='5' class='text-center'>No data available</td></tr>";
            return;
        }

        data.forEach(row => {
            tableBody.innerHTML += `<tr>
                <td>${row.portfolio}</td>
                <td>${row.fund_name}</td>
                <td>${row.date}</td>
                <td>${row.price_per_unit}</td>
                <td>${row.total}</td>
            </tr>`;
        });
    })
    .catch(error => console.error('Error:', error));
}

// Back button event to show file input again
document.getElementById('backButton').addEventListener('click', function() {
    document.getElementById('importSection').classList.remove('d-none'); // Show Import Form
    document.getElementById('dataSection').classList.add('d-none');     // Hide Data Section
    document.getElementById('backButton').classList.add('d-none');      // Hide Back Button
});

// Ensure inserted records are loaded when page loads
loadInsertedData();
</script>
@endsection
