@extends('common_template')
@section('content')
<title>Profile</title>
<div class="content-header row"></div>
<div class="content-body">
    <!-- users edit start -->
    <section class="users-edit">
        <div class="card">
            <div class="card-content">
                <div class="card-body">
                    <ul class="nav nav-tabs mb-2" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link d-flex align-items-center active" id="account-tab" data-toggle="tab" href="#account" aria-controls="account" role="tab" aria-selected="true">
                                <i class="feather icon-user mr-25"></i><span class="d-none d-sm-block">Account</span>
                            </a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="account" aria-labelledby="account-tab" role="tabpanel">
                            <!-- users edit media object start -->
                            <div class="media mb-2">
                                <a class="mr-2" href="#">
                                    <img id="user-avatar" 
                                    src="{{ auth()->user()->avatar ? asset('assets/images/' . auth()->user()->avatar) : asset('app-assets/images/portrait/small/avatar-s-26.png') }}" 
                                    onerror="this.onerror=null; this.src='{{ asset('app-assets/images/portrait/small/avatar-s-26.png') }}';"
                                    alt="User Avatar" class="users-avatar-shadow rounded-circle" height="64" width="64">
                                </a>
                                <div class="media-body">
                                    <h4 class="media-heading">{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}</h4>
                                    <div class="col-12 px-0 d-flex">
                                        <!-- Button to trigger the file input -->
                                        <a href="javascript:void(0);" class="btn btn-sm btn-primary mr-25" onclick="document.getElementById('photo-upload').click();">Upload New Photo</a>
                                        <!-- Reset button to remove avatar -->
                                        <a href="javascript:void(0);" class="btn btn-sm btn-warning" onclick="resetAvatar();">Remove Avatar</a>
                                    </div>
                                    <!-- Hidden file input -->
                                    <form action="{{ route('profile.avatar.update') }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <input type="file" id="photo-upload" name="avatar" style="display:none" accept="image/*" onchange="previewAvatar(event)">
                                        <button type="submit" id="avatar-submit" style="display:none;"></button>
                                    </form>
                                </div>
                            </div>
                            <!-- users edit media object ends -->

                            <!-- users edit account form start -->
                            <form action="{{ route('profile.update') }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="row">
                                    <!-- Basic Information -->
                                    <div class="col-12 col-sm-6">
                                        <div class="form-group">
                                            <div class="controls">
                                                <label>First Name</label>
                                                <input type="text" class="form-control" name="first_name" placeholder="First Name" value="{{ auth()->user()->first_name }}" required>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="controls">
                                                <label>Last Name</label>
                                                <input type="text" class="form-control" name="last_name" placeholder="Last Name" value="{{ auth()->user()->last_name }}" required>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label>Gender</label>
                                            <select class="form-control" name="gender" required>
                                                <option value="male" {{ auth()->user()->gender == 'male' ? 'selected' : '' }}>Male</option>
                                                <option value="female" {{ auth()->user()->gender == 'female' ? 'selected' : '' }}>Female</option>
                                                <option value="other" {{ auth()->user()->gender == 'other' ? 'selected' : '' }}>Other</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <div class="controls">
                                                <label>Phone</label>
                                                <input type="text" class="form-control" name="phone" placeholder="Phone Number" value="{{ auth()->user()->phone }}" required>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="controls">
                                                <label>Email</label>
                                                <input type="email" class="form-control" name="email" placeholder="Email" value="{{ auth()->user()->email }}" required>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="controls">
                                                <label>PAN No</label>
                                                <input type="text" class="form-control" name="pan_no" placeholder="PAN Number" value="{{ auth()->user()->pan_no }}" required>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="controls">
                                                <label>Birthdate</label>
                                                <input type="date" class="form-control" name="birthdate" value="{{ auth()->user()->birthdate }}" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Add Save Changes and Cancel buttons -->
                                <div class="form-group d-flex justify-content-end mt-2">
                                    <button type="submit" class="btn btn-primary mr-1">Save Changes</button>
                                    <button type="reset" class="btn btn-secondary">Cancel</button>
                                </div>
                            </form>
                            <!-- users edit account form ends -->

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- users edit ends -->
</div>
</div>
</div>
<!-- END: Content-->
<script>
    function previewAvatar(event) {
        const avatar = document.getElementById('user-avatar');
        const file = event.target.files[0];
        const reader = new FileReader();

        reader.onload = function(e) {
            avatar.src = e.target.result; // Update avatar preview
            document.getElementById('avatar-submit').click(); // Auto-submit form
        };
        reader.readAsDataURL(file);
    }

    function resetAvatar() {
    if (confirm("Are you sure you want to remove your avatar?")) {
        fetch("{{ route('profile.avatar.remove') }}", {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": "{{ csrf_token() }}",
                "Content-Type": "application/json"
            }
        }).then(response => {
            if (response.ok) {
                document.getElementById('user-avatar').src = "{{ asset('app-assets/images/portrait/small/avatar-s-26.png') }}";
            }
        });
    }
}


</script>

@endsection
