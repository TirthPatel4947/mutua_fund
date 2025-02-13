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
                                    <img id="user-avatar" src="../../../app-assets/images/portrait/small/avatar-s-26.png" alt="users avatar" class="users-avatar-shadow rounded-circle" height="64" width="64">
                                </a>
                                <div class="media-body">
                                    <h4 class="media-heading">Tirth Patel</h4>
                                    <div class="col-12 px-0 d-flex">
                                        <!-- Button to trigger the file input -->
                                        <a href="javascript:void(0);" class="btn btn-sm btn-primary mr-25" onclick="document.getElementById('photo-upload').click();">Upload New Photo</a>
                                        <!-- Reset button to remove avatar -->
                                        <a href="javascript:void(0);" class="btn btn-sm btn-warning" onclick="resetAvatar();">Remove Avatar</a>
                                    </div>
                                    <!-- Hidden file input -->
                                    <input type="file" id="photo-upload" style="display:none" accept="image/*" onchange="previewAvatar(event)">
                                </div>
                            </div>
                            <!-- users edit media object ends -->

                            <!-- users edit account form start -->
                            <form novalidate>
                                <div class="row">
                                    <!-- Basic Information -->
                                    <div class="col-12 col-sm-6">
                                        <div class="form-group">
                                            <div class="controls">
                                                <label>First Name</label>
                                                <input type="text" class="form-control" placeholder="First Name" value="Tirth" required>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="controls">
                                                <label>Last Name</label>
                                                <input type="text" class="form-control" placeholder="Last Name" value="Patel" required>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label>Gender</label>
                                            <select class="form-control" required>
                                                <option value="male" selected>Male</option>
                                                <option value="female">Female</option>
                                                <option value="other">Other</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <div class="controls">
                                                <label>Phone</label>
                                                <input type="text" class="form-control" placeholder="Phone Number" value="1234567890" required>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="controls">
                                                <label>Email</label>
                                                <input type="email" class="form-control" placeholder="Email" value="tirthpatel@gmail.com" required>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="controls">
                                                <label>PAN No</label>
                                                <input type="text" class="form-control" placeholder="PAN Number" value="ABCDE1234F" required>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="controls">
                                                <label>Birthdate</label>
                                                <input type="text" class="form-control" placeholder="Birthdate" value="5-1-2005" required>
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
    // Store the default avatar URL in a constant
    const DEFAULT_AVATAR = "../../../app-assets/images/portrait/small/avatar-s-26.png";

    // Load avatar from localStorage on page load
    document.addEventListener("DOMContentLoaded", () => {
        const savedAvatar = localStorage.getItem("userAvatar");
        const avatar = document.getElementById("user-avatar");
        avatar.src = savedAvatar || DEFAULT_AVATAR; // Set to saved avatar or default
    });

    // Preview avatar image when the user selects a new image
    function previewAvatar(event) {
        const avatar = document.getElementById('user-avatar');
        const file = event.target.files[0];
        const reader = new FileReader();

        reader.onload = function(e) {
            avatar.src = e.target.result; // Update avatar with the selected image
            localStorage.setItem("userAvatar", e.target.result); // Save avatar in localStorage
        };
        reader.readAsDataURL(file);
    }

    // Reset avatar to default image
    function resetAvatar() {
        const avatar = document.getElementById('user-avatar');
        avatar.src = DEFAULT_AVATAR; // Set to default avatar
        localStorage.removeItem("userAvatar"); // Remove saved avatar from localStorage
    }
</script>

@endsection