@extends('common_template')

@section('content')
<div class="container mt-4">
    
    @if(session('success'))
        <script>
            window.onload = function() {
                alert("{{ session('success') }}");  // Show success message in alert
                window.location.href = "{{ route('dashboard') }}";  // Redirect to dashboard after clicking OK
            };
        </script>
    @endif

    <div class="card">
        <div class="card-body">
            <h2>Create Portfolio</h2>
            <form action="{{ route('portfolio.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="portfolioName" class="form-label">Portfolio Name</label>
                    <input type="text" class="form-control" id="portfolioName" name="name" placeholder="Enter portfolio name" required>
                </div>
                <button type="submit" class="btn btn-primary">Submit</button>
            </form>
        </div>
    </div>

</div>
@endsection
