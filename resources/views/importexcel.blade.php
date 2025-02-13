@extends('common_template')

@section('content')
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Upload Excel Form</title>


<style>
    body {
        background-color: #f4f6f9;
        font-family: Arial, sans-serif;
    }

    .container {
        max-width: 600px;
        margin-top: 50px;
    }

    .form-card {
        background-color: #fff;
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    .form-card h3 {
        text-align: center;
        margin-bottom: 20px;
        color: #333;
    }

    .form-group {
        margin-bottom: 10px;
    }

    .form-control {
        border-radius: 5px;
    }

    .btn-submit {
        background-color: #007bff;
        color: #fff;
        border-radius: 5px;
        padding: 10px 20px;
        width: 100%;
    }

    .btn-submit:hover {
        background-color: #0056b3;
    }

    .footer {
        text-align: center;
        margin-top: 30px;
        color: #888;
    }
</style>


<div class="form-card">
    <h3><i class="fas fa-upload"></i> Upload Excel File</h3>
    <form id="excelForm" action="#" method="POST" enctype="multipart/form-data">
        @csrf <!-- Laravel CSRF token -->
        <div class="form-group">
            <label for="excelFile">Choose Excel File</label>
            <input type="file" class="form-control" id="excelFile" name="excelFile" accept=".xlsx, .xls" required>
        </div>
        <div class="form-group">
            <button type="submit" class="btn-submit" id="submitBtn">
                <i class="fas fa-paper-plane"></i> Submit
            </button>
        </div>
    </form>
</div>
</div>


</script>

@endsection