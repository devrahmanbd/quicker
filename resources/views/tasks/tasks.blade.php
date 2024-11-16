@extends('layout')

@section('title')
<?= get_label('tasks', 'Tasks') ?> - <?= get_label('list_view', 'List view') ?>
@endsection

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between mb-2 mt-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb breadcrumb-style1">
                    <li class="breadcrumb-item">
                        <a href="{{route('home.index')}}"><?= get_label('home', 'Home') ?></a>
                    </li>
                    @isset($project->id)
                    @php
                              $defaultView = getUserPreferences('projects', 'default_view');
                                if ($defaultView == 'list') {
                                    $url = route('projects.list_view');
                                } elseif ($defaultView == 'grid') {
                                    $url = route('projects.index');
                                } elseif ($defaultView == 'kanban_view') {
                                    $url = route('projects.kanban_view');
                                } else {
                                    $url = route('projects.index');
                                }
                            @endphp
                    <li class="breadcrumb-item">
                        <a href="{{$url}}"><?= get_label('projects', 'Projects') ?></a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{route('projects.info' ,['id' => $project->id])}}">{{$project->title}}</a>
                    </li>
                    @endisset
                    <li class="breadcrumb-item active"><?= get_label('tasks', 'Tasks') ?></li>
                </ol>
            </nav>
        </div>
        <div>
            @php
            $taskDefaultView = getUserPreferences('tasks', 'default_view');
            @endphp
            @if (!$taskDefaultView || $taskDefaultView === 'tasks')
            <span class="badge bg-primary"><?= get_label('default_view', 'Default View') ?></span>
            @else
            <a href="javascript:void(0);"><span class="badge bg-secondary" id="set-default-view" data-type="tasks" data-view="list"><?= get_label('set_as_default_view', 'Set as Default View') ?></span></a>
            @endif
        </div>

        <div>
            @php
            $projectId = isset($project->id) ? $project->id : (request()->has('project') ? request('project') : '');
            $url = isset($project->id) || request()->has('project') ? route('projects.tasks.draggable' , ['id' =>$project->id]) : route('tasks.draggable');
            if (request()->has('status')) {
            $url .= '?status=' . request()->status;
            }
            @endphp

            <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#create_task_modal"><button type="button" class="btn btn-sm btn-primary action_create_tasks" data-bs-toggle="tooltip" data-bs-placement="right" data-bs-original-title=" <?= get_label('create_task', 'Create task') ?>"><i class="bx bx-plus"></i></button></a>
            <a href="{{ $url }}"><button type="button" class="btn btn-sm btn-primary" data-bs-toggle="tooltip" data-bs-placement="left" data-bs-original-title="<?= get_label('draggable', 'Draggable') ?>"><i class="bx bxs-dashboard"></i></button></a>
              <a href="{{ route('tasks.calendar_view') }}"><button type="button" class="btn btn-sm btn-primary" data-bs-toggle="tooltip" data-bs-placement="left" data-bs-original-title="<?= get_label('calendar_view', 'Calendar View') ?>"><i class="bx bxs-calendar"></i></button></a>
        </div>
    </div>
    <?php
    $id = isset($project->id) ? 'project_' . $project->id : '';
    ?>
    <x-tasks-card :tasks="$tasks" :id="$id" :users="$users" :clients="$clients" :projects="$projects" :project="$project" />
</div>

@endsection