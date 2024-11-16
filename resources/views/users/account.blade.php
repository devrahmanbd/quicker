@extends('layout')

@section('title')
    <?= get_label('update_profile', 'Update profile') ?>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between mt-4">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-style1">
                        <li class="breadcrumb-item">
                            <a href="{{ route('home.index') }}"><?= get_label('home', 'Home') ?></a>
                        </li>
                        <li class="breadcrumb-item active">
                            <?= get_label('profile', 'Profile') ?>
                        </li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card mb-4">
                    <h5 class="card-header"><?= get_label('profile_details', 'Profile details') ?></h5>
                    <!-- Account -->
                    <div class="card-body">
                        <form action="{{ route('profile.update_photo', ['userOrClient' => getAuthenticatedUser()->id]) }}"
                            class="form-submit-event" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="redirect_url"
                                value="{{ route('profile.show', ['user' => getAuthenticatedUser()->id]) }}">

                            @csrf
                            @method('PUT')
                            <div class="d-flex align-items-start align-items-sm-center gap-4">
                                <img src="{{ getAuthenticatedUser()->photo ? asset('storage/' . getAuthenticatedUser()->photo) : asset('storage/photos/no-image.jpg') }}"
                                    alt="user-avatar" class="d-block rounded" height="100" width="100"
                                    id="uploadedAvatar" />
                                <div class="button-wrapper">
                                    <div class="input-group d-flex">
                                        <input type="file" class="form-control" id="inputGroupFile02" name="upload">
                                        <button class="btn btn-outline-primary" type="submit"
                                            id="submit_btn"><?= get_label('update_profile_photo', 'Update profile photo') ?></button>
                                    </div>

                                    @error('upload')
                                        <p class="text-danger mt-1 text-xs">{{ $message }}</p>
                                    @enderror

                                    <p class="text-muted mt-2"><?= get_label('allowed_jpg_png', 'Allowed JPG or PNG.') ?>
                                    </p>
                                </div>

                            </div>
                        </form>
                    </div>
                    <hr class="my-0" />
                    <div class="card-body">
                        <form id="formAccountSettings" method="POST" class="form-submit-event"
                            action="{{ route('profile.update', ['userOrClient' => getAuthenticatedUser()->id]) }}">


                            <input type="hidden" name="redirect_url"
                                value="{{ route('profile.show', ['user' => getAuthenticatedUser()->id]) }}">

                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="firstName" class="form-label"><?= get_label('first_name', 'First name') ?>
                                        <span class="asterisk">*</span></label>
                                    <input class="form-control" type="text" id="first_name" name="first_name"
                                        placeholder="Enter first name" value="{{ getAuthenticatedUser()->first_name }}"
                                        autofocus />

                                    @error('first_name')
                                        <p class="text-danger mt-1 text-xs">{{ $message }}</p>
                                    @enderror

                                </div>



                                <div class="col-md-6 mb-3">
                                    <label for="lastName" class="form-label"><?= get_label('last_name', 'Last name') ?>
                                        <span class="asterisk">*</span></label>
                                    <input class="form-control" type="text" name="last_name"
                                        placeholder="Enter last name" id="last_name"
                                        value="{{ getAuthenticatedUser()->last_name }}" />


                                    @error('last_name')
                                        <p class="text-danger mt-1 text-xs">{{ $message }}</p>
                                    @enderror

                                </div>


                                <div class="col-md-6 mb-3">
                                    <label
                                        class="form-label"><?= get_label('country_code_and_phone_number', 'Country code and phone number') ?></label>
                                    <div class="input-group">
                                        <!-- Country Code Input -->

                                        <input type="tel" class="form-control" id="phone-input-edit"
                                            value="{{ getAuthenticatedUser()->phone }}" name="phone">
                                        <input type="hidden" name="country_code" id="country_code"
                                            value="{{ getAuthenticatedUser()->country_code }}">
                                        <input type="hidden" name="phone" id="phone_number">
                                        <input type="hidden" name="country_iso_code" id="country_iso_code"
                                            value="{{ getAuthenticatedUser()->country_iso_code }}">
                                    </div>


                                    @error('phone')
                                        <p class="text-danger mt-1 text-xs">{{ $message }}</p>
                                    @enderror

                                </div>


                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="email"><?= get_label('email', 'E-mail') ?></label>
                                    <input class="form-control" type="text" name="email" placeholder="Enter email"
                                        value="{{ getAuthenticatedUser()->email }}"
                                        @if (!getAuthenticatedUser()->hasRole('admin')) readonly @endif>

                                    @error('email')
                                        <p class="text-danger mt-1 text-xs">{{ $message }}</p>
                                    @enderror

                                </div>

                                <div class="col-md-6 form-password-toggle mb-3">
                                    <label for="password" class="form-label"><?= get_label('password', 'Password') ?> <span
                                            class="asterisk">*</span></label>
                                    <div class="input-group input-group-merge">
                                        <input class="form-control" type="password" id="password" name="password"
                                            placeholder="<?= get_label('please_enter_password', 'Please enter password') ?>"
                                            autocomplete="new-password">
                                        <span class="input-group-text toggle-password cursor-pointer"><i
                                                class="bx bx-hide"></i></span>
                                        <span class="input-group-text cursor-pointer"
                                                    id="generate-password"><i
                                                class="bx bxs-magic-wand"></i></span>
                                    </div>
                                </div>
                                <div class="col-md-6 form-password-toggle mb-3">
                                    <label for="password_confirmation"
                                        class="form-label"><?= get_label('confirm_password', 'Confirm password') ?> <span
                                            class="asterisk">*</span></label>
                                    <div class="input-group input-group-merge">
                                        <input class="form-control" type="password" id="password_confirmation"
                                            name="password_confirmation"
                                            placeholder="<?= get_label('please_re_enter_password', 'Please re enter password') ?>"
                                            autocomplete="new-password">
                                        <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                                    </div>
                                </div>

                                @if (getAuthenticatedUser()->getRoleNames()->first() == 'admin')
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label" for="role"><?= get_label('role', 'Role') ?> <span
                                                class="asterisk">*</span></label>
                                        <div class="input-group">

                                            <select class="form-select text-capitalize" id="role" name="role">

                                                @foreach ($roles->filter(fn($role) => $role->name !== 'superadmin') as $role)
                                                    <option value="{{ $role->id }}"
                                                        {{ $user->getRoleNames()->first() == $role->name ? 'selected' : '' }}>
                                                        {{ $role->name }}
                                                    </option>
                                                @endforeach
                                            </select>




                                        </div>

                                        @error('role')
                                            <p class="text-danger mt-1 text-xs">{{ $message }}</p>
                                        @enderror

                                    </div>
                                @else
                                    <div class="col-md-6 mb-3">
                                        <input type="hidden" name="role" value="<?= $user->roles->pluck('id')[0] ?>">
                                        <label class="form-label" for="role"><?= get_label('role', 'Role') ?> <span
                                                class="asterisk">*</span></label>
                                        <input class="form-control" type="text" id="exampleFormControlReadOnlyInput1"
                                            placeholder="" value="{{ getAuthenticatedUser()->getRoleNames()->first() }}"
                                            readonly="">

                                        @error('role')
                                            <p class="text-danger mt-1 text-xs">{{ $message }}</p>
                                        @enderror

                                    </div>
                                @endif



                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="address"><?= get_label('address', 'Address') ?> <span
                                            class="asterisk">*</span></label>
                                    <input class="form-control" type="text" id="address"
                                        placeholder="Enter address" name="address"
                                        value="{{ getAuthenticatedUser()->address }}">

                                    @error('address')
                                        <p class="text-danger mt-1 text-xs">{{ $message }}</p>
                                    @enderror

                                </div>



                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="city"><?= get_label('city', 'City') ?> <span
                                            class="asterisk">*</span></label>
                                    <input class="form-control" type="text" id="city" placeholder="Enter city"
                                        name="city" value="{{ getAuthenticatedUser()->city }}">

                                    @error('city')
                                        <p class="text-danger mt-1 text-xs">{{ $message }}</p>
                                    @enderror

                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="state"><?= get_label('state', 'State') ?> <span
                                            class="asterisk">*</span></label>
                                    <input class="form-control" type="text" id="state" placeholder="Enter state"
                                        name="state" value="{{ getAuthenticatedUser()->state }}">

                                    @error('state')
                                        <p class="text-danger mt-1 text-xs">{{ $message }}</p>
                                    @enderror

                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="country"><?= get_label('country', 'Country') ?> <span
                                            class="asterisk">*</span></label>
                                    <input class="form-control" type="text" id="country"
                                        placeholder="Enter country" name="country"
                                        value="{{ getAuthenticatedUser()->country }}">

                                    @error('country')
                                        <p class="text-danger mt-1 text-xs">{{ $message }}</p>
                                    @enderror

                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="zip"><?= get_label('zip_code', 'ZIP code') ?>
                                        <span class="asterisk">*</span></label>
                                    <input class="form-control" type="text" id="zip"
                                        placeholder="Enter ZIP code" name="zip"
                                        value="{{ getAuthenticatedUser()->zip }}">

                                    @error('zip')
                                        <p class="text-danger mt-1 text-xs">{{ $message }}</p>
                                    @enderror

                                </div>

                                <div class="mt-2">
                                    <button type="submit" id="submit_btn"
                                        class="btn btn-primary me-2"><?= get_label('update', 'Update') ?></button>
                                    <button type="reset"
                                        class="btn btn-outline-secondary"><?= get_label('cancel', 'Cancel') ?></button>
                                </div>
                        </form>
                    </div>
                    <!-- /Account -->
                </div>
                <div class="card">
                    <h5 class="card-header"><?= get_label('delete_account', 'Delete account') ?></h5>
                    <div class="card-body">
                        <div class="col-12 mb-0 mb-3">
                            <div class="alert alert-warning">
                                <h6 class="alert-heading fw-bold mb-1">
                                    <?= get_label('delete_account_alert', 'Are you sure you want to delete your account?') ?>
                                </h6>
                                <p class="mb-0">
                                    <?= get_label('delete_account_alert_sub_text', 'Once you delete your account, there is no going back. Please be certain.') ?>
                                </p>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-danger" data-bs-toggle="modal"
                            data-bs-target="#deleteAccountModal"><?= get_label('delete_account', 'Delete account') ?></button>

                    </div>

                </div>
            </div>
        </div>
    </div>
    <script src="{{ asset('assets/js/pages/account.js') }}"></script>
@endsection
