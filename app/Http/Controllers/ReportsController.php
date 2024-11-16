<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Client;
use App\Models\Status;
use App\Models\Project;
use App\Models\Priority;
use App\Models\Workspace;
use Illuminate\Support\Str;
use App\Models\LeaveRequest;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\EstimatesInvoice;
use App\Models\Expense;
use Illuminate\Support\Facades\DB;

class ReportsController extends Controller
{
    protected $workspace;
    protected $user;
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            // fetch session and use it in entire class with constructor
            $this->workspace = Workspace::find(session()->get('workspace_id'));
            $this->user = getAuthenticatedUser();
            return $next($request);
        });
    }
    public function showProjectReport()
    {
        $projects = $this->workspace->projects()->pluck('title', 'id');
        $users = $this->workspace->users;
        $clients = $this->workspace->clients;
        $statuses = Status::where('admin_id', getAdminIdByUserRole())->get();
        return view('reports.projects-report', [
            'workspace' => $this->workspace,
            'projects' => $projects,
            'users' => $users,
            'clients' => $clients,
            'statuses' => $statuses,
        ]);
    }
    public function getProjectReportData(Request $request)
    {
        // Debugging: Check the request data
        // dd($request->all());
        // Determine the base query based on user's access level
        $query = isAdminOrHasAllDataAccess() ? $this->workspace->projects() : $this->user->projects();
        // Apply filters only if they have values
        if ($request->filled('project_id')) {
            $query->whereIn('id', explode(',', $request->project_id)); // Handle comma-separated string
        }
        if ($request->filled('user_id')) {
            $query->whereHas('users', function ($q) use ($request) {
                $q->whereIn('users.id', explode(',', $request->user_id)); // Handle comma-separated string
            });
        }
        if ($request->filled('client_id')) {
            $query->whereHas('clients', function ($q) use ($request) {
                $q->whereIn('clients.id', explode(',', $request->client_id)); // Handle comma-separated string
            });
        }
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('start_date', [$request->start_date, $request->end_date]);
        }
        if ($request->filled('status_id')) {
            $query->whereIn('status_id', explode(',', $request->status_id)); // Handle comma-separated string
        }
        if ($request->filled('search')) {
            $searchTerm = '%' . $request->search . '%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'like', $searchTerm)
                    ->orWhere('description', 'like', $searchTerm)
                    ->orWhereHas('status', function ($q) use ($searchTerm) {
                        $q->where('title', 'like', $searchTerm);
                    })
                    ->orWhereHas('priority', function ($q) use ($searchTerm) {
                        $q->where('title', 'like', $searchTerm);
                    })
                    ->orWhereHas('users', function ($q) use ($searchTerm) {
                        $q->where(function ($q) use ($searchTerm) {
                            $q->where('first_name', 'like', $searchTerm)
                                ->orWhere('last_name', 'like', $searchTerm);
                        });
                    })
                    ->orWhereHas('clients', function ($q) use ($searchTerm) {
                        $q->where(function ($q) use ($searchTerm) {
                            $q->where('first_name', 'like', $searchTerm)
                                ->orWhere('last_name', 'like', $searchTerm);
                        });
                    });
            });
        }
        // Apply sorting
        $sort = $request->input('sort', 'id'); // Default sort column
        $order = $request->input('order', 'desc'); // Default sort order
        // Sorting logic
        switch ($sort) {
            case 'status':
                $query->join('statuses', 'projects.status_id', '=', 'statuses.id')
                    ->select('projects.*', 'statuses.title as status_title')
                    ->orderBy('status_title', $order);
                break;
            case 'priority':
                $query->join('priorities', 'projects.priority_id', '=', 'priorities.id')
                    ->select('projects.*', 'priorities.title as priority_title')
                    ->orderBy('priority_title', $order);
                break;
            case 'title':
            case 'start_date':
            case 'end_date':
                $query->orderBy($sort, $order);
                break;
            default:
                $query->orderBy('id', $order); // Default sort column
        }
        // Pagination setup
        $perPage = $request->input('limit', 10);
        $page = $request->input('offset', 0) / $perPage + 1;
        // Get the total count before pagination
        $total = $query->count();
        // Fetch paginated results with related models
        $projects = $query->with(['tasks', 'users', 'clients', 'status', 'priority', 'tags'])
            ->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get();
        // Transform project data into the desired report format
        $report = $projects->map(function ($project) {
            $now = now();
            $startDate = Carbon::parse($project->start_date);
            $endDate = Carbon::parse($project->end_date);
            $totalProjectDays = $startDate->diffInDays($endDate) + 1;
            $daysElapsed = $now->diffInDays($startDate);
            $daysRemaining = $endDate->isPast() ? 0 : $now->diffInDays($endDate);
            $tasks = $project->tasks;
            $totalTasks = $tasks->count();
            $dueTasks = $tasks->where('due_date', '<=', $now)->count();
            $overdueTasks = $tasks->where('due_date', '<', $now)->count();
            $overdueDays = $tasks->where('due_date', '<', $now)->map(function ($task) use ($now) {
                return $now->diffInDays(Carbon::parse($task->due_date));
            })->sum();
            $totalBudget = $project->budget ?? 0;
            // Format clients' HTML
            $clientHtml = $project->clients->map(function ($client) {
                return "<a href='" . route('clients.profile', ['id' => $client->id]) . "' target='_blank'>
                    <li class='avatar avatar-sm pull-up' title='" . e($client->first_name . " " . $client->last_name) . "'>
                        <img src='" . ($client->photo ? asset('storage/' . $client->photo) : asset('storage/photos/no-image.jpg')) . "' alt='Avatar' class='rounded-circle' />
                    </li>
                </a>";
            })->implode('');
            // Format users' HTML
            $userHtml = $project->users->map(function ($user) {
                return "<a href='" . route('users.show', ['id' => $user->id]) . "' target='_blank'>
                    <li class='avatar avatar-sm pull-up' title='" . e($user->first_name . " " . $user->last_name) . "'>
                        <img src='" . ($user->photo ? asset('storage/' . $user->photo) : asset('storage/photos/no-image.jpg')) . "' class='rounded-circle' />
                    </li>
                </a>";
            })->implode('');
            return [
                'id' => $project->id,
                'title' => Str::limit(ucfirst($project->title), 25, '...'),
                'description' => $project->description,
                'start_date' => $project->start_date,
                'end_date' => $project->end_date,
                'status' => "<span class='badge bg-label-" . e($project->status->color) . "'>" . e($project->status->title) . "</span>",
                'priority' => $project->priority ? "<span class='badge bg-label-" . e($project->priority->color) . "'>" . e($project->priority->title) . "</span>" : '-',
                'budget' => [
                    'total' => $totalBudget,
                ],
                'time' => [
                    'total_days' => $totalProjectDays,
                    'days_elapsed' => $daysElapsed,
                    'days_remaining' => $daysRemaining,
                ],
                'tasks' => [
                    'total' => $totalTasks,
                    'due' => $dueTasks,
                    'overdue' => $overdueTasks,
                    'overdue_days' => $overdueDays,
                ],
                'team' => [
                    'users' => $project->users->map(function ($user) use ($project) {
                        return [
                            'id' => $user->id,
                            'name' => $user->first_name . ' ' . $user->last_name,
                            'tasks_assigned' => $user->tasks()->where('project_id', $project->id)->count(),
                        ];
                    }),
                    'total_members' => $project->users->count()
                ],
                'users' => $userHtml,
                'clients' => $clientHtml,
                'tags' => $project->tags->pluck('title'),
                'is_favorite' => $project->is_favorite,
                'task_accessibility' => $project->task_accessibility,
                'created_at' => format_date($project->created_at),
                'updated_at' => format_date($project->updated_at),
            ];
        });
        $teamMembers = $projects->flatMap(function ($project) {
            return $project->users;
        })->unique('id')->count();
        // Generate summary data
        $summary = [
            'total_projects' => $report->count(),
            'on_time_projects' => $report->where('tasks.overdue', 0)->count(),
            'projects_with_due_tasks' => $report->where('tasks.due', '>', 0)->count(),
            'projects_with_overdue_tasks' => $report->where('tasks.overdue', '>', 0)->count(),
            'average_days_remaining' => round($report->avg('time.days_remaining'), 2),
            'average_task_progress' => round($report->avg(function ($project) {
                if ($project['tasks']['total'] > 0) {
                    return ($project['tasks']['total'] - $project['tasks']['overdue']) / $project['tasks']['total'] * 100;
                }
                return 0; // Return 0 if there are no tasks in the project
            }), 2),

            'average_overdue_days_per_project' => round($report->where('tasks.overdue_days', '>', 0)->avg('tasks.overdue_days'), 2),
            'total_team_members' => $teamMembers,
            'overdue_projects_percentage' => round(($report->where('tasks.overdue', '>', 0)->count() / $report->count()) * 100, 2),
            'total_overdue_days' => $report->sum('tasks.overdue_days'),
            'average_task_duration' => round($report->avg(function ($project) {
                // Ensure tasks are an array or collection
                $tasks = collect($project['tasks']);

                return $tasks->count() > 0 ? $tasks->avg(function ($task) {
                    // Ensure that start_date and due_date are accessible
                    return isset($task['start_date'], $task['due_date'])
                        ? Carbon::parse($task['start_date'])->diffInDays(Carbon::parse($task['due_date']))
                        : 0;
                }) : 0;
            }), 2),


            'total_tasks' => $projects->flatMap(function ($project) {
                return $project->tasks;
            })->count(),

        ];

        return response()->json([
            'projects' => $report,
            'total' => $total,
            'summary' => $summary,
        ]);
    }
    public function exportProjectReport(Request $request)
    {
        $projectsData = $this->getProjectReportData($request)->getData();
        // dd($projectsData);
        $pdf = Pdf::loadView('reports.projects-report-pdf', ['projects' => $projectsData->projects, 'summary' => $projectsData->summary])
            ->setPaper([0, 0, 2000, 900], 'mm');
        return $pdf->download('projects_report.pdf');
    }

    public function showTaskReport()
    {
        $projects = $this->workspace->projects()->pluck('title', 'id');
        $users = $this->workspace->users;
        $clients = $this->workspace->clients;
        $statuses = Status::where('admin_id', getAdminIdByUserRole())->get()->pluck('title', 'id');
        $priorities = Priority::where('admin_id', getAdminIdByUserRole())->get()->pluck('title', 'id');
        return view('reports.tasks-report', [
            'workspace' => $this->workspace,
            'projects' => $projects,
            'users' => $users,
            'clients' => $clients,
            'statuses' => $statuses,
            'priorities' => $priorities,
        ]);
    }

    public function getTaskReportData(Request $request)
    {
        // Determine the base query based on user's access level
        $query = isAdminOrHasAllDataAccess() ? $this->workspace->tasks() : $this->user->tasks();

        // Apply filters
        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }
        if ($request->filled('user_id')) {
            $query->whereHas('users', function ($q) use ($request) {
                $q->whereIn('users.id', explode(',', $request->user_id));
            });
        }
        if ($request->filled('client_id')) {
            $query->whereHas('project.clients', function ($q) use ($request) {
                $q->whereIn('clients.id', explode(',', $request->client_id));
            });
        }
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('due_date', [$request->start_date, $request->end_date]);
        }
        if ($request->filled('status_id')) {
            $query->whereIn('status_id', explode(',', $request->status_id));
        }
        if ($request->filled('priority_id')) {
            $query->whereIn('priority_id', explode(',', $request->priority_id));
        }
        if ($request->filled('search')) {
            $searchTerm = '%' . $request->search . '%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'like', $searchTerm)
                    ->orWhere('description', 'like', $searchTerm)
                    ->orWhereHas('status', function ($q) use ($searchTerm) {
                        $q->where('title', 'like', $searchTerm);
                    })
                    ->orWhereHas('priority', function ($q) use ($searchTerm) {
                        $q->where('title', 'like', $searchTerm);
                    });
            });
        }

        // Apply sorting
        $sort = $request->input('sort', 'id');
        $order = $request->input('order', 'desc');
        $query->orderBy($sort, $order);

        // Pagination setup
        $perPage = $request->input('limit', 10);
        $page = $request->input('offset', 0) / $perPage + 1;
        $total = $query->count();
        $tasks = $query->with(['project', 'status', 'priority',  'project.clients'])
            ->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get();

        // Transform task data into the desired report format
        $report = $tasks->map(function ($task) {

            $now = now();
            $startDate = Carbon::parse($task->start_date);
            $dueDate = Carbon::parse($task->due_date);
            $daysElapsed = $now->diffInDays($startDate);
            $daysRemaining = $dueDate->isPast() ? 0 : $now->diffInDays($dueDate);
            $overdueDays = $dueDate->isPast() ? $now->diffInDays($dueDate) : 0;

            // Format clients' HTML
            $clientHtml = $task->project->clients->map(function ($client) {
                return "<a href='" . route('clients.profile', ['id' => $client->id]) . "' target='_blank'>
                    <li class='avatar avatar-sm pull-up' title='" . e($client->first_name . " " . $client->last_name) . "'>
                        <img src='" . ($client->photo ? asset('storage/' . $client->photo) : asset('storage/photos/no-image.jpg')) . "' alt='Avatar' class='rounded-circle' />
                    </li>
                </a>";
            })->implode('');

            // Format users' HTML
            $userHtml = $task->users->map(function ($user) {
                return "<a href='" . route('users.show', ['id' => $user->id]) . "' target='_blank'>
                    <li class='avatar avatar-sm pull-up' title='" . e($user->first_name . " " . $user->last_name) . "'>
                        <img src='" . ($user->photo ? asset('storage/' . $user->photo) : asset('storage/photos/no-image.jpg')) . "' class='rounded-circle' />
                    </li>
                </a>";
            })->implode('');

            return [
                'id' => $task->id,
                'title' => Str::limit(ucfirst($task->title), 25, '...'),
                'description' => $task->description,
                'start_date' => $task->start_date,
                'due_date' => $task->due_date,
                'status' => "<span class='badge bg-label-" . e($task->status->color) . "'>" . e($task->status->title) . "</span>",
                'priority' => $task->priority ? "<span class='badge bg-label-" . e($task->priority->color) . "'>" . e($task->priority->title) . "</span>" : '-',
                'project' => $task->project ? $task->project->title : '-',
                'assigned_to' => $task->assignedTo ? $task->assignedTo->first_name . ' ' . $task->assignedTo->last_name : '-',
                'time' => [
                    'days_elapsed' => $daysElapsed,
                    'days_remaining' => $daysRemaining,
                    'overdue_days' => $overdueDays,
                ],
                'users' => $userHtml,
                'clients' => $clientHtml,

                'is_urgent' => $task->priority && $task->priority->title === 'High' && $dueDate->isPast(),
                'created_at' => format_date($task->created_at),
                'updated_at' => format_date($task->updated_at),
            ];
        });

        // Generate summary data
        $summary = [
            'total_tasks' => $total,
            'overdue_tasks' => $report->where('time.overdue_days', '>', 0)->count(),
            'urgent_tasks' => $report->where('is_urgent', true)->count(),
            'average_task_duration' => round($report->avg(function ($task) {
                return Carbon::parse($task['start_date'])->diffInDays(Carbon::parse($task['due_date']));
            }), 2),
        ];

        return response()->json([
            'tasks' => $report,
            'total' => $total,
            'summary' => $summary,
        ]);
    }


    public function exportTaskReport(Request $request)
    {
        $tasksData = $this->getTaskReportData($request)->getData();
        $pdf = Pdf::loadView('reports.tasks-report-pdf', ['tasks' => $tasksData->tasks, 'summary' => $tasksData->summary])
            ->setPaper([0, 0, 2000, 900], 'mm');
        return $pdf->download('tasks_report.pdf');
    }

    public function showInvoicesReport()
    {
        $clients = $this->workspace->clients;
        $invoice_statuses = [
            'sent' => get_label('sent', 'Sent'),
            'accepted' => get_label('accepted', 'Accepted'),
            'partially_paid' => get_label('partially_paid', 'Partially Paid'),
            'fully_paid' => get_label('fully_paid', 'Fully Paid'),
            'draft' => get_label('draft', 'Draft'),
            'declined' => get_label('declined', 'Declined'),
            'expired' => get_label('expired', 'Expired'),
            'not_specified' => get_label('not_specified', 'Not Specified'),
            'due' => get_label('due', 'Due')
        ];
        return view('reports.invoices-report', compact('clients', 'invoice_statuses',));
    }

    public function getInvoicesReportData(Request $request)
    {
        // dd($request);
        // Determine the base query based on user's access level
        $query = EstimatesInvoice::query()
            ->select(
                'estimates_invoices.*',
                DB::raw('CONCAT(clients.first_name, " ", clients.last_name) AS client_name')
            )
            ->leftJoin('clients', 'estimates_invoices.client_id', '=', 'clients.id')
            ->where('estimates_invoices.workspace_id', $this->workspace->id);

        if (!isAdminOrHasAllDataAccess()) {
            $query->where(function ($q) {
                $q->where('estimates_invoices.created_by', isClient() ? 'c_' . $this->user->id : 'u_' . $this->user->id)
                    ->orWhere('estimates_invoices.client_id', $this->user->id);
            });
        }
        $query->where('estimates_invoices.type', 'invoice');
        // Apply filters
        if ($request->filled('status_id')) {
            $query->where('estimates_invoices.status', $request->status_id);
        }

        if ($request->filled('client_id')) {
            $query->whereIn('estimates_invoices.client_id', explode(',', $request->client_id));
        }
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('estimates_invoices.from_date', [$request->start_date, $request->end_date]);
        }
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('estimates_invoices.to_date', [$request->start_date, $request->end_date]);
        }
        if ($request->filled('search')) {
            $searchTerm = '%' . $request->search . '%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('estimates_invoices.id', 'like', $searchTerm)
                    ->orWhere('estimates_invoices.name', 'like', $searchTerm);
            });
        }

        // Apply sorting
        $sort = $request->input('sort', 'id');
        $order = $request->input('order', 'DESC');
        $query->orderBy($sort, $order);

        // Pagination setup
        $perPage = $request->input('limit', 10);
        $page = $request->input('offset', 0) / $perPage + 1;
        $total = $query->count();

        // Calculate totals
        $totalAmount = $query->sum('total');
        $totalTax = $query->sum('tax_amount');
        $totalFinal = $query->sum('final_total');

        $invoices = $query->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get();

        // Transform invoice data into the desired report format
        $report = $invoices->map(function ($invoice) {


            return [
                'id' => $invoice->id,
                'type' => ucfirst($invoice->type),
                'client' => $invoice->client_name,
                'total' => format_currency($invoice->total),
                'tax_amount' => format_currency($invoice->tax_amount),
                'final_total' => format_currency($invoice->final_total),
                'from_date' => format_date($invoice->from_date),
                'to_date' => format_date($invoice->to_date),
                'status' => $this->getStatusBadge($invoice->status),
                'created_by' => $this->getCreatorName($invoice->created_by),
                'created_at' => format_date($invoice->created_at),
                'updated_at' => format_date($invoice->updated_at),

            ];
        });

        // Generate summary data
        $summary = [
            'total_invoices' => $total,
            'total_amount' => format_currency($totalAmount),
            'total_tax' => format_currency($totalTax),
            'total_final' => format_currency($totalFinal),
            'average_invoice_value' => $total > 0 ? format_currency($totalFinal / $total) : format_currency(0),
        ];

        return response()->json([
            'invoices' => $report,
            'total' => $total,
            'summary' => $summary,
        ]);
    }

    private function getStatusBadge($status)
    {
        // Generate status badge HTML based on status
        $badges = [
            'sent' => 'bg-primary',
            'accepted' => 'bg-success',
            'partially_paid' => 'bg-warning',
            'fully_paid' => 'bg-success',
            'draft' => 'bg-secondary',
            'declined' => 'bg-danger',
            'expired' => 'bg-warning',
            'not_specified' => 'bg-secondary',
            'due' => 'bg-danger'
        ];

        return isset($badges[$status]) ? '<span class="badge ' . $badges[$status] . '">' . get_label($status, ucfirst(str_replace('_', ' ', $status))) . '</span>' : '';
    }

    private function getCreatorName($createdBy)
    {
        // Extract creator's name from ID
        $userId = substr($createdBy, 2);
        $user = User::find($userId);
        return $user ? $user->first_name . ' ' . $user->last_name : '-';
    }

    public function exportInvoicesReport(Request $request)
    {
        // dd($this->getInvoicesReportData($request)->getData());
        $invoicesData = $this->getInvoicesReportData($request)->getData();
        $pdf = Pdf::loadView('reports.invoices-report-pdf', ['invoices' => $invoicesData->invoices, 'summary' => $invoicesData->summary])
            ->setPaper([0, 0, 2000, 900], 'mm');
        // dd($pdf);
        return $pdf->download('invoices_report.pdf');
    }

    public function showLeavesReport()
    {
        $users = isAdminOrHasAllDataAccess() ? $this->workspace->users : $this->user;

        return view('reports.leaves-report', ['users' => $users]);
    }

    public function getLeavesReportData(Request $request)
    {
        // dd($request);
        // Get all users in the workspace using the relationship
        $users = is_admin_or_leave_editor() ? $this->workspace->users() : $this->user;

        // Apply user filter if provided
        if ($request->filled('user_id')) {
            $users->where('users.id',  $request->user_id);
        }

        // Apply date range filter if provided
        $dateFilter = [];
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $dateFilter = [$request->start_date, $request->end_date];
        }

        // Fetch users with their leave requests
        $users = $users->with(['leave_requests' => function ($query) use ($dateFilter) {
            if (!empty($dateFilter)) {
                $query->whereBetween('from_date', $dateFilter);
            }
        }])->get();

        // Transform user data into the desired report format
        $report = $users->map(function ($user) use ($request) {
            $leaveRequests = $user->leave_requests;
            if ($request->filled('status_id')) {
                $leaveRequests = $leaveRequests->where('status', $request->status_id);
            }

            return [
                'id' => $user->id,
                'user_name' => $this->formatUserHtml($user),
                'total_leaves' => $leaveRequests->count(),
                'approved_leaves' => $leaveRequests->where('status', 'approved')->count(),
                'pending_leaves' => $leaveRequests->where('status', 'pending')->count(),
                'rejected_leaves' => $leaveRequests->where('status', 'rejected')->count(),
            ];
        });

        // Apply sorting
        $sort = $request->input('sort', 'user_name');
        $order = $request->input('order', 'asc');

        if ($sort === 'user_name') {
            $report = $report->sortBy(function ($item) {
                return strtolower($item['user_name']);
            }, SORT_NATURAL | SORT_FLAG_CASE);
        } else {
            $report = $report->sortBy($sort);
        }
        if ($order === 'desc') {
            $report = $report->reverse();
        }

        // Pagination
        $perPage = $request->input('limit', 10);
        $page = $request->input('offset', 0) / $perPage + 1;
        $total = $report->count();

        $paginatedReport = $report->forPage($page, $perPage);

        // Generate summary data
        $summary = [
            'total_users' => $report->count(),
            'total_leaves' => $report->sum('total_leaves'),
            'total_approved_leaves' => $report->sum('approved_leaves'),
            'total_pending_leaves' => $report->sum('pending_leaves'),
            'total_rejected_leaves' => $report->sum('rejected_leaves'),
            'average_leaves_per_user' => $report->sum('total_leaves') > 0 ? round($report->sum('total_leaves') / $report->count(), 2) : 0,
        ];

        return response()->json([
            'users' => $paginatedReport->values(),
            'total' => $total,
            'summary' => $summary,
        ]);
    }

    private function formatUserHtml($user)
    {
        $profileLink = route('users.show', ['id' => $user->id]);
        $photoUrl = $user->photo ? asset('storage/' . $user->photo) : asset('storage/photos/no-image.jpg');

        return "<div class='d-flex justify-content-start align-items-center user-name'>
        <div class='avatar-wrapper me-3'>
            <div class='avatar avatar-sm pull-up'>
                <img src='{$photoUrl}' alt='Avatar' class='rounded-circle'>
            </div>
        </div>
        <div class='d-flex flex-column'>
            <a href='{$profileLink}' target='_blank'>
                <span class='fw-semibold'>{$user->first_name} {$user->last_name}</span>
            </a>
            <small class='text-muted'>{$user->email}</small>
        </div>
    </div>";
    }

    public function exportLeavesReport(Request $request)
    {
        $leavesData = $this->getLeavesReportData($request)->getData();
        $pdf = Pdf::loadView('reports.leaves-report-pdf', ['users' => $leavesData->users, 'summary' => $leavesData->summary])
            ->setPaper([0, 0, 2000, 900], 'mm');

        return $pdf->download('leaves_report.pdf');
    }
    public function showIncomeVsExpenseReport(Request $request)
    {
        $reportData = $this->getIncomeVsExpenseReportData($request)->getData();

        // Pass data to view
        return view('reports.income-vs-expense-report', [
            'report' => $reportData,
        ]);
    }
    public function getIncomeVsExpenseReportData(Request $request)
    {
        // Initialize the query for total income from invoices
        $invoicesQuery = EstimatesInvoice::query()
            ->select('id', 'final_total', 'from_date', 'to_date')
            ->where('status', 'fully_paid')
            ->where('type', 'invoice')
            ->where('workspace_id', $this->workspace->id);

        // Apply date filters if provided
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $invoicesQuery->whereBetween('to_date', [$request->start_date, $request->end_date]);
            $invoicesQuery->orWhereBetween('from_date', [$request->start_date, $request->end_date]);
        }

        // Get detailed income data
        $invoices = $invoicesQuery->get();
        $totalIncome = $invoices->sum('final_total');

        // Initialize the query for total expenses
        $expensesQuery = Expense::query()
            ->select('id', 'title', 'amount', 'expense_date')
            ->where('workspace_id', $this->workspace->id);

        // Apply date filters if provided
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $expensesQuery->whereBetween('expense_date', [$request->start_date, $request->end_date]);
        }

        // Get detailed expense data
        $expenses = $expensesQuery->get();
        $totalExpenses = $expenses->sum('amount');

        // Calculate profit or loss
        $profitOrLoss = $totalIncome - $totalExpenses;

        // Prepare detailed report data
        $report = [
            'total_income' => format_currency($totalIncome),
            'total_expenses' => format_currency($totalExpenses),
            'profit_or_loss' => format_currency($profitOrLoss),
            'invoices' => $invoices->map(function ($invoice) {
                return [
                    'id' => $invoice->id,
                    "view_route" => route('estimates-invoices.view', ['id' => $invoice->id]),
                    'amount' => format_currency($invoice->final_total),
                    'to_date' => $invoice->to_date,
                    'from_date' => $invoice->from_date,
                ];
            }),
            'expenses' => $expenses->map(function ($expense) {
                return [
                    'id' => $expense->id,
                    'title' => $expense->title,
                    'amount' => format_currency($expense->amount),
                    'expense_date' => $expense->expense_date,
                ];
            }),
        ];

        return response()->json($report);
    }


    public function exportIncomeVsExpenseReport(Request $request)
    {
        $reportData = $this->getIncomeVsExpenseReportData($request)->getData();
        $pdf = Pdf::loadView('reports.income-vs-expense-report-pdf', ['report' => $reportData])
            ->setPaper([0, 0, 2000, 900], 'mm');

        return $pdf->download('income_vs_expense_report.pdf');
    }
}
