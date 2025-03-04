@extends('common_template')

@section('content')
<title>Change Password</title>
<div class="content-header row"></div>
<div class="content-body">
    <section class="users-edit">
        <div class="card">
            <div class="card-content">
                <div class="card-body">
                    <h4>Change Password</h4>

                    @if (session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('password.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label>Current Password</label>
                            <input type="password" class="form-control" name="current_password" required>
                        </div>

                        <div class="form-group">
                            <label>New Password</label>
                            <input type="password" class="form-control" name="new_password" required>
                        </div>

                        <div class="form-group">
                            <label>Confirm New Password</label>
                            <input type="password" class="form-control" name="new_password_confirmation" required>
                        </div>

                        <button type="submit" class="btn btn-primary">Update Password</button>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
