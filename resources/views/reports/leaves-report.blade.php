@extends('layout')
@section('title')
    {{ get_label('leaves_report', 'Leaves Report') }} - {{ get_label('reports', 'Reports') }}
@endsection
@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between mt-4">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-style1">
                        <li class="breadcrumb-item">
                            <a href="{{ route('home.index') }}">{{ get_label('home', 'Home') }}</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="#">{{ get_label('reports', 'Reports') }}</a>
                        </li>
                        <li class="breadcrumb-item active">
                            <a href="{{ route('reports.leaves-report') }}">{{ get_label('leaves_report', 'Leaves Report') }}</a>
                        </li>
                    </ol>
                </nav>
            </div>
        </div>
        <!-- Summary Cards -->
        <div class="d-flex mb-4 flex-wrap gap-3">
            <div class="card flex-grow-1 border-0 shadow-sm">
                <div class="card-body d-flex align-items-center">
                    <i class="bx bx-user fs-2 text-primary me-3"></i>
                    <div>
                        <h6 class="card-title mb-1">{{ get_label('total_users', 'Total Users') }}</h6>
                        <p class="card-text mb-0" id="total-users">Loading...</p>
                    </div>
                </div>
            </div>
            <div class="card flex-grow-1 border-0 shadow-sm">
                <div class="card-body d-flex align-items-center">
                    <i class="bx bx-calendar fs-2 text-success me-3"></i>
                    <div>
                        <h6 class="card-title mb-1">{{ get_label('total_leaves', 'Total Leaves') }}</h6>
                        <p class="card-text mb-0" id="total-leaves">Loading...</p>
                    </div>
                </div>
            </div>
            <div class="card flex-grow-1 border-0 shadow-sm">
                <div class="card-body d-flex align-items-center">
                    <i class="bx bx-check-circle fs-2 text-warning me-3"></i>
                    <div>
                        <h6 class="card-title mb-1">{{ get_label('approved_leaves', 'Approved Leaves') }}</h6>
                        <p class="card-text mb-0" id="approved-leaves">Loading...</p>
                    </div>
                </div>
            </div>
            <div class="card flex-grow-1 border-0 shadow-sm">
                <div class="card-body d-flex align-items-center">
                    <i class="bx bx-time fs-2 text-info me-3"></i>
                    <div>
                        <h6 class="card-title mb-1">{{ get_label('pending_leaves', 'Pending Leaves') }}</h6>
                        <p class="card-text mb-0" id="pending-leaves">Loading...</p>
                    </div>
                </div>
            </div>
            <div class="card flex-grow-1 border-0 shadow-sm">
                <div class="card-body d-flex align-items-center">
                    <i class="bx bx-x-circle fs-2 text-danger me-3"></i>
                    <div>
                        <h6 class="card-title mb-1">{{ get_label('rejected_leaves', 'Rejected Leaves') }}</h6>
                        <p class="card-text mb-0" id="rejected-leaves">Loading...</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <!-- Filters Row -->
                <div class="row mb-3">
                    <!-- User Filter -->
                    @hasRole('admin')
                    <div class="col-md-4 col-lg-3 mb-md-0 mb-2">
                        <select id="filter_user" class="form-control">

                        </select>
                    </div>
                    @endhasRole
                    <!-- Status Filter -->
                    <div class="col-md-4 col-lg-3 mb-md-0 mb-2">
                        <select id="filter_status" class="form-control">
                            <option value="">{{ get_label('select_status', 'Select Status') }}</option>
                            <option value="approved">{{ get_label('approved', 'Approved') }}</option>
                            <option value="pending">{{ get_label('pending', 'Pending') }}</option>
                            <option value="rejected">{{ get_label('rejected', 'Rejected') }}</option>
                        </select>
                    </div>
                    <!-- Date Range Filter -->
                    <div class="col-md-4 col-lg-6 mb-md-0 mb-2">
                        <input type="text" id="filter_date_range" class="form-control" placeholder="{{ get_label('select_date_range', 'Select Date Range') }}">
                    </div>
                </div>
                <!-- Additional Filters Row -->
                <div class="row mb-3">
                    <!-- Export Button -->
                    <div class="col-md-12 col-lg-12 d-flex align-items-center justify-content-md-end mb-md-0 mb-2">
                        <button id="export_button" class="btn btn-primary">{{ get_label('export', 'Export') }}</button>
                    </div>
                </div>
                <!-- Table -->
                <div class="table-responsive text-nowrap">
                    <table id="leaves_report_table" data-toggle="table"
                        data-url="{{ route('reports.leaves-report-data') }}" data-loading-template="loadingTemplate"
                        data-icons-prefix="bx" data-icons="icons" data-show-refresh="true" data-total-field="total"
                        data-trim-on-search="false" data-data-field="users" data-page-list="[5, 10, 20, 50, 100, 200]"
                        data-search="true" data-side-pagination="server" data-show-columns="true" data-pagination="true"
                        data-sort-name="id" data-sort-order="desc" data-mobile-responsive="true"
                        data-query-params="leaves_report_query_params">
                        <thead>
                            <tr>
                                <th data-field="id" data-sortable="true">{{ get_label('id', 'ID') }}</th>
                                <th data-field="user_name" data-sortable="true">{{ get_label('user', 'User') }}</th>
                                <th data-field="total_leaves" data-sortable="true">{{ get_label('total_leaves', 'Total Leaves') }}</th>
                                <th data-field="approved_leaves" data-sortable="true">{{ get_label('approved_leaves', 'Approved Leaves') }}</th>
                                <th data-field="pending_leaves" data-sortable="true">{{ get_label('pending_leaves', 'Pending Leaves') }}</th>
                                <th data-field="rejected_leaves" data-sortable="true">{{ get_label('rejected_leaves', 'Rejected Leaves') }}</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <script>
        var leaves_report_export_url = "{{ route('reports.export-leaves-report') }}";
    </script>
    <script src="{{ asset('assets/js/pages/leaves-report.js') }}"></script>
@endsection
