@extends('common_template')
<title>Portfolio</title>
@section('content')
<div class="container mt-4">
    <div class="card">
        <div class="card-body">
            <h2>Create Portfolio</h2>
            <form action="{{ route('portfolio.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="portfolioName" class="form-label">Portfolio Name</label>
                    <input type="text" class="form-control" id="portfolioName" name="portfolio_name" placeholder="Enter portfolio name" required>
                </div>
                <button type="submit" class="btn btn-primary">Submit</button>
            </form>
        </div>
    </div>
</div>
@endsection