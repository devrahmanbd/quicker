@extends('layout')

@section('title')
    <?= get_label('refund_policy', 'Refund policy') ?>
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
                        <li class="breadcrumb-item">
                            <?= get_label('settings', 'Settings') ?>
                        </li>
                        <li class="breadcrumb-item active">
                            <?= get_label('refund_policy', 'Refund Policy') ?>
                        </li>
                    </ol>
                </nav>
            </div>
        </div>
        <div class="col-12">
            <div class="card">

                <div class="card-body">
                    <form action="{{ route('refund_policy.store') }}" class="form-submit-event" method="POST"
                        enctype="multipart/form-data">
                        <input type="hidden" name="redirect_url" value="{{ route('refund_policy.index') }}">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <textarea class="form-control" name="refund_policy" id="refund_policy">
                            @isset($refund_policy['refund_policy'])
{!! $refund_policy['refund_policy'] !!}
@endisset
                            </textarea>
                            </textarea>

                        </div>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <button type="submit" class="btn btn-primary">{{ get_label('save', 'Save') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
