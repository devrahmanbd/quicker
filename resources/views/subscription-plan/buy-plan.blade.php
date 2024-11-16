@extends('layout')

@section('title')
    <?= get_label('buy_plan', 'Buy Plan') ?>
@endsection

@section('content')
    <div class="container-fluid mb-2">
        <div class="d-flex justify-content-between mt-4">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-style1">
                        <li class="breadcrumb-item">
                            <a href="{{ route('home.index') }}"><?= get_label('home', 'Home') ?></a>
                        </li>
                        <li class="breadcrumb-item">
                            <a
                                href="{{ route('subscription-plan.index') }}"><?= get_label('subscription_plan', 'Subscription Plan') ?></a>
                        </li>
                        <li class="breadcrumb-item active">
                            <?= get_label('buy_plan', 'Buy Plan') ?>
                        </li>
                    </ol>
                </nav>
            </div>
        </div>
        <!-- Inside your pricing.blade.php file -->
        @if (is_countable($plans) && count($plans) > 0)
            <section class = "section-py first-section-pt">
                <div class="container-fluid">
                    <h2 class="text-center mb-2">{{ get_label('pricing_plans', 'Pricing Plans') }}</h2>
                    <p class="text-center mb-4 pb-2">
                        {!! get_label(
                            'buy_plan_description1',
                            'All plans include advanced tools and features to boost your productivity<br>Choose the best plan to fit your needs',
                        ) !!}.
                    </p>

                    <!-- Pricing toggle -->
                    <div class="text-center  mb-3 mt-3 mb-5">
                        <div class="btn-group flex-wrap" role="group" aria-label="Tenure Options">
                            <input type="radio" class="btn-check" name="priceToggle" id="monthly" autocomplete="off"
                                checked>
                            <label class="btn btn-outline-primary"
                                for="monthly">{{ get_label('monthly', 'Monthly') }}</label>
                            <input type="radio" class="btn-check" name="priceToggle" id="yearly" autocomplete="off">
                            <label class="btn btn-outline-primary"
                                for="yearly">{{ get_label('yearly', 'Yearly') }}</label>
                            <input type="radio" class="btn-check" name="priceToggle" id="lifetime" autocomplete="off">
                            <label class="btn btn-outline-primary"
                                for="lifetime">{{ get_label('lifetime', 'Lifetime') }}</label>
                        </div>
                    </div>
                    <style>

                    </style>


                    <!-- Pricing plans -->
                    <div class="row mx-0 gy-3 px-lg-5">
                        @foreach ($plans as $plan)
                            <div class="col-lg-3 mb-md-0 mt-4 mb-4">
                                <div class="card border rounded shadow-none">
                                    <div class="card-body">
                                        <div class="mb-3 text-center">
                                            <div class="mb-3 text-center ">
                                                <img class="thumbnail-img"
                                                    src="{{ !empty($plan->image) ? asset('/storage/' . $plan->image) : '/assets/img/illustrations/man-with-laptop-light.png' }}"
                                                    alt="{{ $plan->name }}" height="50">
                                                <!-- Adjust the height value here -->
                                            </div>
                                        </div>

                                        <h3 class="card-title text-center text-capitalize mb-1">{{ $plan->name }}</h3>
                                        <p class="text-center">{{ $plan->description }}</p>
                                        <div class="text-center">
                                            <div class="d-flex justify-content-center">

                                                <h2 class="mb-0  monthly-price display-4 mb-0 text-primary fw-normal">
                                                    @if ($plan->monthly_discounted_price > 0)
                                                        {{ format_currency($plan->monthly_discounted_price) }} <p
                                                            class="text-strike-through fw-light">
                                                            /{{ format_currency($plan->monthly_price) }}
                                                        </p>
                                                    @else
                                                        {{ format_currency($plan->monthly_price) }}
                                                    @endif

                                                    <sub
                                                        class="h5 pricing-duration mt-auto mb-2  fw-normal">/{{ get_label('monthly', 'Montly') }}</sub>
                                                </h2>
                                                <h2 class=" mb-0 display-4 mb-0 text-primary fw-normal yearly-price d-none">
                                                    @if ($plan->yearly_discounted_price > 0)
                                                        {{ format_currency($plan->yearly_discounted_price) }} <p
                                                            class="text-strike-through fw-light">/{{ format_currency($plan->yearly_price) }}
                                                        </p>
                                                    @else
                                                        {{ format_currency($plan->yearly_price) }}
                                                    @endif
                                                    <sub class="h5 pricing-duration mt-auto mb-2  fw-normal">
                                                        /{{ get_label('yearly', 'Yearly') }}</sub>
                                                </h2>
                                                <h2 class=" mb-0 display-4  text-primary fw-normal lifetime-price d-none">
                                                    @if ($plan->lifetime_discounted_price > 0)
                                                        {{ format_currency($plan->lifetime_discounted_price) }} <p
                                                            class="text-strike-through fw-light">
                                                            /{{ format_currency($plan->lifetime_price) }}
                                                        </p>
                                                    @else
                                                        {{ format_currency($plan->lifetime_price) }}
                                                    @endif
                                                    <sub
                                                        class="h5 pricing-duration mt-auto mb-2  fw-normal">/{{ get_label('one_time_payment', 'One Time Payment') }}</sub>
                                                </h2>
                                            </div>
                                        </div>
                                        <ul class="ps-3 my-4 list-unstyled">
                                            <li class="mb-2">
                                                <span
                                                    class="badge badge-center w-px-20 h-px-20 rounded-pill bg-label-primary me-2"><i
                                                        class="bx bx-check bx-xs"></i></span>
                                                {{ get_label('max_projects', 'Max Projects') }}:
                                                {!! $plan->max_projects == -1
                                                    ? '<span class="fw-semibold">Unlimited</span>'
                                                    : '<span class="fw-semibold">' . $plan->max_projects . '</span>' !!}
                                            </li>
                                            <li class="mb-2">
                                                <span
                                                    class="badge badge-center w-px-20 h-px-20 rounded-pill bg-label-primary me-2"><i
                                                        class="bx bx-check bx-xs"></i></span>
                                                {{ get_label('max_clients', 'Max Clients') }}:
                                                {!! $plan->max_clients == -1
                                                    ? '<span class="fw-semibold">Unlimited</span>'
                                                    : '<span class="fw-semibold">' . $plan->max_clients . '</span>' !!}
                                            </li>
                                            <li class="mb-2">
                                                <span
                                                    class="badge badge-center w-px-20 h-px-20 rounded-pill bg-label-primary me-2"><i
                                                        class="bx bx-check bx-xs"></i></span>
                                                {{ get_label('max_team_members', 'Max Team Members') }}:
                                                {!! $plan->max_team_members == -1
                                                    ? '<span class="fw-semibold">Unlimited</span>'
                                                    : '<span class="fw-semibold">' . $plan->max_team_members . '</span>' !!}
                                            </li>
                                            <li class="mb-2">
                                                <span
                                                    class="badge badge-center w-px-20 h-px-20 rounded-pill bg-label-primary me-2"><i
                                                        class="bx bx-check bx-xs"></i></span>
                                                {{ get_label('max_workspaces', 'Max Workspaces') }}:
                                                {!! $plan->max_worksapces == -1
                                                    ? '<span class="fw-semibold">Unlimited</span>'
                                                    : '<span class="fw-semibold">' . $plan->max_worksapces . '</span>' !!}
                                            </li>
                                            @if ($plan->modules)
                                                <li class="mb-2">
                                                    <i
                                                        class="fas fa-check-circle me-2 text-success"></i>{{ get_label('modules', 'Modules') }}
                                                    <ul class="list-unstyled m-3 my-2 ps-0 text-smallcaps">
                                                        @php
                                                            $modules = json_decode($plan->modules);
                                                            $checkedModules = [];
                                                            $uncheckedModules = [];
                                                            foreach (
                                                                config('quicker.modules')
                                                                as $moduleName => $moduleData
                                                            ) {
                                                                $included = in_array($moduleName, $modules);
                                                                if ($included) {
                                                                    $checkedModules[] = [
                                                                        'name' => $moduleName,
                                                                        'icon' => $moduleData['icon'],
                                                                    ];
                                                                } else {
                                                                    $uncheckedModules[] = [
                                                                        'name' => $moduleName,
                                                                        'icon' => $moduleData['icon'],
                                                                    ];
                                                                }
                                                            }
                                                            $sortedModules = array_merge(
                                                                $checkedModules,
                                                                $uncheckedModules,
                                                            );
                                                        @endphp
                                                        @foreach ($sortedModules as $module)
                                                            @php
                                                                $iconClass = in_array($module['name'], $modules)
                                                                    ? 'bx bx-check-circle  text-success'
                                                                    : 'bx bxs-x-circle text-danger';
                                                            @endphp
                                                            <li class="mb-2 text-dark">

                                                                <i class=" {{ $iconClass }} me-2"></i>
                                                                <i class="{{ $module['icon'] }}"></i>
                                                                {{ ucfirst($module['name']) }}
                                                            </li>
                                                        @endforeach
                                                    </ul>

                                                </li>
                                            @endif
                                        </ul>
                                        <div class="d-flex justify-content-center">
                                            <button data-planId="{{ $plan->id }}"
                                                class="btn btn-outline-primary checkout_btn">
                                                {{ get_label('proceed', 'Proceed') }} <i class="bx bx-right-arrow-alt"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
        @else
            <div class="card text-center empty-state">
                <div class="card-body">
                    <div class="misc-wrapper">
                        <h2 class="mb-2 mx-2"><?= get_label('plans', 'Plans') . ' ' . get_label('not_found', 'Not Found') ?>
                        </h2>
                        <p class="mb-4 mx-2"><?= get_label('oops!', 'Oops!') ?> 😖
                            <?= get_label('data_does_not_exists', 'Data does not exists') ?>.</p>
                        <div class="mt-3">
                            <img src="{{ asset('/storage/no-result.png') }}" alt="page-misc-error-light" width="500"
                                class="img-fluid" data-app-dark-img="illustrations/page-misc-error-dark.png"
                                data-app-light-img="illustrations/page-misc-error-light.png" />
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <script src="{{ asset('assets/js/pages/subscription-plan.js') }}"></script>
@endsection
