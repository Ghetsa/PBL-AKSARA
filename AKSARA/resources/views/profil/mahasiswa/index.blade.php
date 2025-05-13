@extends('layouts.template')

@section('content')
<!-- [ Main Content ] start -->
    <div class="row">
    <!-- [ sample-page ] start -->
    <div class="col-sm-12">
        <div class="card">
        <div class="card-header pb-0">
            <ul class="nav nav-tabs profile-tabs" id="myTab" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="profile-tab-1" data-bs-toggle="tab" href="#profile-1" role="tab"
                aria-selected="true">
                <i class="ti ti-user me-2"></i>Profile
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="profile-tab-2" data-bs-toggle="tab" href="#profile-2" role="tab"
                aria-selected="true">
                <i class="ti ti-file-text me-2"></i>Personal
                </a>
            </li>
                {{-- <i class="ti ti-id me-2"></i>My Account --}}
            <li class="nav-item">
                <a class="nav-link" id="profile-tab-4" data-bs-toggle="tab" href="#profile-4" role="tab"
                aria-selected="true">
                <i class="ti ti-lock me-2"></i>Change Password
                </a>
            </li>
            </ul>
        </div>
        <div class="card-body">
            <div class="tab-content">
            <div class="tab-pane show active" id="profile-1" role="tabpanel" aria-labelledby="profile-tab-1">
                <div class="row">
                <div class="col-lg-4 col-xxl-3">
                    <div class="card">
                    <div class="card-body position-relative">
                        <div class="position-absolute end-0 top-0 p-3">
                        <span class="badge bg-primary">Pro</span>
                        </div>
                        <div class="text-center mt-3">
                        <div class="chat-avtar d-inline-flex mx-auto">
                            <img class="rounded-circle img-fluid wid-70" src="{{ asset('mantis/dist/assets/images/user/avatar-5.jpg') }}"
                            alt="User image">
                        </div>
                        <h5 class="mb-0">Anshan H.</h5>
                        <p class="text-muted text-sm">Project Manager</p>
                        <hr class="my-3">
                        <div class="row g-3">
                            <div class="col-4">
                            <h5 class="mb-0">86</h5>
                            <small class="text-muted">Post</small>
                            </div>
                            <div class="col-4 border border-top-0 border-bottom-0">
                            <h5 class="mb-0">40</h5>
                            <small class="text-muted">Project</small>
                            </div>
                            <div class="col-4">
                            <h5 class="mb-0">4.5K</h5>
                            <small class="text-muted">Members</small>
                            </div>
                        </div>
                        <hr class="my-3">
                        <div class="d-inline-flex align-items-center justify-content-between w-100 mb-3">
                            <i class="ti ti-mail"></i>
                            <p class="mb-0">anshan@gmail.com</p>
                        </div>
                        <div class="d-inline-flex align-items-center justify-content-between w-100 mb-3">
                            <i class="ti ti-phone"></i>
                            <p class="mb-0">(+1-876) 8654 239 581</p>
                        </div>
                        <div class="d-inline-flex align-items-center justify-content-between w-100 mb-3">
                            <i class="ti ti-map-pin"></i>
                            <p class="mb-0">New York</p>
                        </div>
                        <div class="d-inline-flex align-items-center justify-content-between w-100">
                            <i class="ti ti-link"></i>
                            <a href="#" class="link-primary">
                            <p class="mb-0">https://anshan.dh.url</p>
                            </a>
                        </div>
                        </div>
                    </div>
                    </div>
                    <div class="card">
                    <div class="card-header">
                        <h5>Skills</h5>
                    </div>
                    <div class="card-body">
                        <div class="row align-items-center mb-3">
                        <div class="col-sm-6 mb-2 mb-sm-0">
                            <p class="mb-0">Junior</p>
                        </div>
                        <div class="col-sm-6">
                            <div class="d-flex align-items-center">
                            <div class="flex-grow-1 me-3">
                                <div class="progress progress-primary" style="height: 6px;">
                                <div class="progress-bar" style="width: 30%;"></div>
                                </div>
                            </div>
                            <div class="flex-shrink-0">
                                <p class="mb-0 text-muted">30%</p>
                            </div>
                            </div>
                        </div>
                        </div>
                        <div class="row align-items-center mb-3">
                        <div class="col-sm-6 mb-2 mb-sm-0">
                            <p class="mb-0">UX Researcher</p>
                        </div>
                        <div class="col-sm-6">
                            <div class="d-flex align-items-center">
                            <div class="flex-grow-1 me-3">
                                <div class="progress progress-primary" style="height: 6px;">
                                <div class="progress-bar" style="width: 80%;"></div>
                                </div>
                            </div>
                            <div class="flex-shrink-0">
                                <p class="mb-0 text-muted">80%</p>
                            </div>
                            </div>
                        </div>
                        </div>
                        <div class="row align-items-center mb-3">
                        <div class="col-sm-6 mb-2 mb-sm-0">
                            <p class="mb-0">Wordpress</p>
                        </div>
                        <div class="col-sm-6">
                            <div class="d-flex align-items-center">
                            <div class="flex-grow-1 me-3">
                                <div class="progress progress-primary" style="height: 6px;">
                                <div class="progress-bar" style="width: 90%;"></div>
                                </div>
                            </div>
                            <div class="flex-shrink-0">
                                <p class="mb-0 text-muted">90%</p>
                            </div>
                            </div>
                        </div>
                        </div>
                        <div class="row align-items-center mb-3">
                        <div class="col-sm-6 mb-2 mb-sm-0">
                            <p class="mb-0">HTML</p>
                        </div>
                        <div class="col-sm-6">
                            <div class="d-flex align-items-center">
                            <div class="flex-grow-1 me-3">
                                <div class="progress progress-primary" style="height: 6px;">
                                <div class="progress-bar" style="width: 30%;"></div>
                                </div>
                            </div>
                            <div class="flex-shrink-0">
                                <p class="mb-0 text-muted">30%</p>
                            </div>
                            </div>
                        </div>
                        </div>
                        <div class="row align-items-center mb-3">
                        <div class="col-sm-6 mb-2 mb-sm-0">
                            <p class="mb-0">Graphic Design</p>
                        </div>
                        <div class="col-sm-6">
                            <div class="d-flex align-items-center">
                            <div class="flex-grow-1 me-3">
                                <div class="progress progress-primary" style="height: 6px;">
                                <div class="progress-bar" style="width: 95%;"></div>
                                </div>
                            </div>
                            <div class="flex-shrink-0">
                                <p class="mb-0 text-muted">95%</p>
                            </div>
                            </div>
                        </div>
                        </div>
                        <div class="row align-items-center">
                        <div class="col-sm-6 mb-2 mb-sm-0">
                            <p class="mb-0">Code Style</p>
                        </div>
                        <div class="col-sm-6">
                            <div class="d-flex align-items-center">
                            <div class="flex-grow-1 me-3">
                                <div class="progress progress-primary" style="height: 6px;">
                                <div class="progress-bar" style="width: 75%;"></div>
                                </div>
                            </div>
                            <div class="flex-shrink-0">
                                <p class="mb-0 text-muted">75%</p>
                            </div>
                            </div>
                        </div>
                        </div>
                    </div>
                    </div>
                </div>
                <div class="col-lg-8 col-xxl-9">
                    <div class="card">
                    <div class="card-header">
                        <h5>About me</h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-0">Hello, I’m Anshan Handgun Creative Graphic Designer & User Experience Designer
                        based in Website, I create digital Products a more Beautiful and usable place. Morbid
                        accusant ipsum. Nam nec tellus at.</p>
                    </div>
                    </div>
                    <div class="card">
                    <div class="card-header">
                        <h5>Personal Details</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                        <li class="list-group-item px-0 pt-0">
                            <div class="row">
                            <div class="col-md-6">
                                <p class="mb-1 text-muted">Full Name</p>
                                <p class="mb-0">Anshan Handgun</p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-1 text-muted">Father Name</p>
                                <p class="mb-0">Mr. Deepen Handgun</p>
                            </div>
                            </div>
                        </li>
                        <li class="list-group-item px-0">
                            <div class="row">
                            <div class="col-md-6">
                                <p class="mb-1 text-muted">Phone</p>
                                <p class="mb-0">(+1-876) 8654 239 581</p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-1 text-muted">Country</p>
                                <p class="mb-0">New York</p>
                            </div>
                            </div>
                        </li>
                        <li class="list-group-item px-0">
                            <div class="row">
                            <div class="col-md-6">
                                <p class="mb-1 text-muted">Email</p>
                                <p class="mb-0">anshan.dh81@gmail.com</p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-1 text-muted">Zip Code</p>
                                <p class="mb-0">956 754</p>
                            </div>
                            </div>
                        </li>
                        <li class="list-group-item px-0 pb-0">
                            <p class="mb-1 text-muted">Address</p>
                            <p class="mb-0">Street 110-B Kalians Bag, Dewan, M.P. New York</p>
                        </li>
                        </ul>
                    </div>
                    </div>
                    <div class="card">
                    <div class="card-header">
                        <h5>Education</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                        <li class="list-group-item px-0 pt-0">
                            <div class="row">
                            <div class="col-md-6">
                                <p class="mb-1 text-muted">Master Degree (Year)</p>
                                <p class="mb-0">2014-2017</p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-1 text-muted">Institute</p>
                                <p class="mb-0">-</p>
                            </div>
                            </div>
                        </li>
                        <li class="list-group-item px-0">
                            <div class="row">
                            <div class="col-md-6">
                                <p class="mb-1 text-muted">Bachelor (Year)</p>
                                <p class="mb-0">2011-2013</p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-1 text-muted">Institute</p>
                                <p class="mb-0">Imperial College London</p>
                            </div>
                            </div>
                        </li>
                        <li class="list-group-item px-0 pb-0">
                            <div class="row">
                            <div class="col-md-6">
                                <p class="mb-1 text-muted">School (Year)</p>
                                <p class="mb-0">2009-2011</p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-1 text-muted">Institute</p>
                                <p class="mb-0">School of London, England</p>
                            </div>
                            </div>
                        </li>
                        </ul>
                    </div>
                    </div>
                    <div class="card">
                    <div class="card-header">
                        <h5>Employment</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                        <li class="list-group-item px-0 pt-0">
                            <div class="row">
                            <div class="col-md-6">
                                <p class="mb-1 text-muted">Senior</p>
                                <p class="mb-0">Senior UI/UX designer (Year)</p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-1 text-muted">Job Responsibility</p>
                                <p class="mb-0">Perform task related to project manager with the 100+ team under my
                                observation. Team management is key role in this company.</p>
                            </div>
                            </div>
                        </li>
                        <li class="list-group-item px-0">
                            <div class="row">
                            <div class="col-md-6">
                                <p class="mb-1 text-muted">Trainee cum Project Manager (Year)</p>
                                <p class="mb-0">2017-2019</p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-1 text-muted">Job Responsibility</p>
                                <p class="mb-0">Team management is key role in this company.</p>
                            </div>
                            </div>
                        </li>
                        <li class="list-group-item px-0 pb-0">
                            <div class="row">
                            <div class="col-md-6">
                                <p class="mb-1 text-muted">School (Year)</p>
                                <p class="mb-0">2009-2011</p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-1 text-muted">Institute</p>
                                <p class="mb-0">School of London, England</p>
                            </div>
                            </div>
                        </li>
                        </ul>
                    </div>
                    </div>
                </div>
                </div>
            </div>
            <div class="tab-pane" id="profile-2" role="tabpanel" aria-labelledby="profile-tab-2">
                <div class="row">
                <div class="col-lg-6">
                    <div class="card">
                    <div class="card-header">
                        <h5>Personal Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                        <div class="col-sm-12 text-center mb-3">
                            <div class="user-upload wid-75">
                            <img src="{{ asset('mantis/dist/assets/images/user/avatar-4.jpg') }}" alt="img" class="img-fluid">
                            <label for="uplfile" class="img-avtar-upload">
                                <i class="ti ti-camera f-24 mb-1"></i>
                                <span>Upload</span>
                            </label>
                            <input type="file" id="uplfile" class="d-none">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                            <label class="form-label">First Name</label>
                            <input type="text" class="form-control" value="Anshan">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                            <label class="form-label">Last Name</label>
                            <input type="text" class="form-control" value="Handgun">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                            <label class="form-label">Country</label>
                            <input type="text" class="form-control" value="New York">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                            <label class="form-label">Zip code</label>
                            <input type="text" class="form-control" value="956754">
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group">
                            <label class="form-label">Bio</label>
                            <textarea
                                class="form-control">Hello, I’m Anshan Handgun Creative Graphic Designer & User Experience Designer based in Website, I create digital Products a more Beautiful and usable place. Morbid accusant ipsum. Nam nec tellus at.</textarea>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group">
                            <label class="form-label">Experience</label>
                            <select class="form-control">
                                <option>Startup</option>
                                <option>2 year</option>
                                <option>3 year</option>
                                <option selected>4 year</option>
                                <option>5 year</option>
                            </select>
                            </div>
                        </div>
                        </div>
                    </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card">
                    <div class="card-header">
                        <h5>Social Network</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2">
                        <div class="flex-grow-1 me-3">
                            <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="avtar avtar-xs btn-light-twitter">
                                <i class="fab fa-twitter f-16"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-0">Twitter</h6>
                            </div>
                            </div>
                        </div>
                        <div class="flex-shrink-0">
                            <button class="btn btn-link-danger">Connect</button>
                        </div>
                        </div>
                        <div class="d-flex align-items-center mb-2">
                        <div class="flex-grow-1 me-3">
                            <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="avtar avtar-xs btn-light-facebook">
                                <i class="fab fa-facebook-f f-16"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-0">Facebook</h6>
                            </div>
                            </div>
                        </div>
                        <div class="flex-shrink-0">
                            <div class="text-facebook">Anshan Handgun</div>
                        </div>
                        </div>
                        <div class="d-flex align-items-center">
                        <div class="flex-grow-1 me-3">
                            <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="avtar avtar-xs btn-light-linkedin">
                                <i class="fab fa-linkedin-in f-16"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-0">Linkedin</h6>
                            </div>
                            </div>
                        </div>
                        <div class="flex-shrink-0">
                            <button class="btn btn-link-danger">Connect</button>
                        </div>
                        </div>
                    </div>
                    </div>
                    <div class="card">
                    <div class="card-header">
                        <h5>Contact Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                            <label class="form-label">Contact Phone</label>
                            <input type="text" class="form-control" value="(+99) 9999 999 999">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                            <label class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" value="demo@sample.com">
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group">
                            <label class="form-label">Portfolio Url</label>
                            <input type="text" class="form-control" value="https://demo.com">
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group">
                            <label class="form-label">Address</label>
                            <textarea
                                class="form-control">3379  Monroe Avenue, Fort Myers, Florida(33912)</textarea>
                            </div>
                        </div>
                        </div>
                    </div>
                    </div>
                </div>
                <div class="col-12 text-end btn-page">
                    <div class="btn btn-outline-secondary">Cancel</div>
                    <div class="btn btn-primary">Update Profile</div>
                </div>
                </div>
            </div>
            <div class="tab-pane" id="profile-4" role="tabpanel" aria-labelledby="profile-tab-4">
                <div class="card">
                <div class="card-header">
                    <h5>Change Password</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                        <label class="form-label">Old Password</label>
                        <input type="password" class="form-control">
                        </div>
                        <div class="form-group">
                        <label class="form-label">New Password</label>
                        <input type="password" class="form-control">
                        </div>
                        <div class="form-group">
                        <label class="form-label">Confirm Password</label>
                        <input type="password" class="form-control">
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <h5>New password must contain:</h5>
                        <ul class="list-group list-group-flush">
                        <li class="list-group-item"><i class="ti ti-minus me-2"></i> At least 8 characters</li>
                        <li class="list-group-item"><i class="ti ti-minus me-2"></i> At least 1 lower letter (a-z)
                        </li>
                        <li class="list-group-item"><i class="ti ti-minus me-2"></i> At least 1 uppercase letter
                            (A-Z)</li>
                        <li class="list-group-item"><i class="ti ti-minus me-2"></i> At least 1 number (0-9)</li>
                        <li class="list-group-item"><i class="ti ti-minus me-2"></i> At least 1 special characters
                        </li>
                        </ul>
                    </div>
                    </div>
                </div>
                <div class="card-footer text-end btn-page">
                    <div class="btn btn-outline-secondary">Cancel</div>
                    <div class="btn btn-primary">Update Profile</div>
                </div>
                </div>
            </div>
        </div>
    </div>
    <!-- [ sample-page ] end -->
</div>
@endsection

@push('js')
@endpush     
  