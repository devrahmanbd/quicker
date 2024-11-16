@extends('layout')
@section('title')
    <?= get_label('tasks', 'Tasks') ?> - <?= get_label('calendar_view', 'Calendar View') ?>
@endsection
@section('content')
    @php
        $routePrefix = Route::getCurrentRoute()->getPrefix();
    @endphp
    <div class="container-fluid">
        <div class="d-flex justify-content-between mb-2 mt-4">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-style1">
                        <li class="breadcrumb-item">
                            <a href="{{ route('home.index') }}"><?= get_label('home', 'Home') ?></a>
                        </li>
                        <li class="breadcrumb-item">
                            <?= get_label('tasks', 'Tasks') ?>
                        </li>
                        <li class="breadcrumb-item active">
                            <?= get_label('calendar_view', 'Calendar View') ?>
                        </li>
                    </ol>
                </nav>
            </div>
            <div>
                @php
                    $taskDefaultView = getUserPreferences('tasks', 'default_view');
                @endphp
                @if ($taskDefaultView === 'calendar-view')
                    <span class="badge bg-primary"><?= get_label('default_view', 'Default View') ?></span>
                @else
                    <a href="javascript:void(0);"><span class="badge bg-secondary" id="set-default-view" data-type="tasks"
                            data-view="calendar-view"><?= get_label('set_as_default_view', 'Set as Default View') ?></span></a>
                @endif
            </div>
            <div>
                @php
                    $url = isset($project->id)
                        ? route('projects.tasks.index', ['id' => $project->id])
                        : route('tasks.index');
                    if (request()->has('status')) {
                        $url .= '?status=' . request()->status;
                    }
                @endphp
                <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#create_task_modal"><button
                        type="button" class="btn btn-sm btn-primary" data-bs-toggle="tooltip" data-bs-placement="right"
                        data-bs-original-title=" <?= get_label('create_task', 'Create task') ?>"><i
                            class="bx bx-plus"></i></button></a>
                <a href="{{ $url }}"><button type="button" class="btn btn-sm btn-primary" data-bs-toggle="tooltip"
                        data-bs-placement="left" data-bs-original-title="<?= get_label('list_view', 'List view') ?>"><i
                            class="bx bx-list-ul"></i></button></a>
                @php
                    $projectId = isset($project->id)
                        ? $project->id
                        : (request()->has('project')
                            ? request('project')
                            : '');
                    $url =
                        isset($project->id) || request()->has('project')
                            ? route('projects.tasks.draggable', ['id' => $project->id])
                            : route('tasks.draggable');
                    if (request()->has('status')) {
                        $url .= '?status=' . request()->status;
                    }
                @endphp
                  <a href="{{ $url }}"><button type="button" class="btn btn-sm btn-primary" data-bs-toggle="tooltip" data-bs-placement="left" data-bs-original-title="<?= get_label('draggable', 'Draggable') ?>"><i class="bx bxs-dashboard"></i></button></a>
            </div>
        </div>
        <div class="card mb-4">
            <div class="card-body">
                <div id="taskCalenderDiv"></div>
            </div>
        </div>
    </div>
@endsection

