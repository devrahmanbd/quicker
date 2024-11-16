$('#navigation-buttons-container').hide();
// /**
// * Initializes the Gantt chart view for projects and tasks.
// *
// * @return {void}
// */
// $(document).ready(

//     function () {

//         var data = [];
//         var viewMode = 'Day'; // Default view mode
//         var currentDate = new Date();

//         /**
//          * Calculates the time-based progress of a project or task.
//          *
//          * @param {string} startDate - The start date of the project or task.
//          * @param {string} endDate - The end date of the project or task.
//          * @return {number} The progress of the project or task as a percentage.
//          */
//         function calculateTimeBasedProgress(startDate, endDate) {
//             var now = new Date();
//             var start = new Date(startDate);
//             var end = new Date(endDate);
//             var totalDuration = end - start;
//             var elapsed = now - start;
//             var progress = Math.min((elapsed / totalDuration) * 100, 100);
//             return progress;
//         }

//         $.each(projects, function (index, project) {
//             if (project.id && project.title && project.start_date && project.end_date) {
//                 var projectStart = new Date(project.start_date);
//                 var projectEnd = new Date(project.end_date);

//                 if (isNaN(projectStart.getTime()) || isNaN(projectEnd.getTime())) {
//                     console.error("Invalid date for project:", project);
//                     return;
//                 }

//                 var projectProgress = calculateTimeBasedProgress(project.start_date, project.end_date);

//                 data.push({
//                     id: project.id.toString(),
//                     name: project.title,
//                     start: projectStart,
//                     end: projectEnd,
//                     progress: projectProgress,
//                     dependencies: [],
//                     type: 'project',
//                     custom_class: 'gantt-project'
//                 });

//                 if (Array.isArray(project.tasks)) {
//                     $.each(project.tasks, function (index, task) {
//                         if (task.id && task.title && task.start_date && task.due_date) {
//                             var taskStart = new Date(task.start_date);
//                             var taskEnd = new Date(task.due_date);

//                             if (isNaN(taskStart.getTime()) || isNaN(taskEnd.getTime())) {
//                                 console.error("Invalid date for task:", task);
//                                 return;
//                             }

//                             var taskProgress = calculateTimeBasedProgress(task.start_date, task.due_date);

//                             data.push({
//                                 id: task.id.toString(),
//                                 name: task.title,
//                                 start: taskStart,
//                                 end: taskEnd,
//                                 progress: taskProgress,
//                                 type: 'task',
//                                 dependencies: [project.id.toString()],
//                                 custom_class: 'gantt-task'
//                             });
//                         } else {
//                             console.error("Task data is incomplete", task);
//                         }
//                     });
//                 }
//             } else {
//                 console.error("Project data is incomplete", project);
//             }
//         });

//         var initialStartDate = new Date(currentDate.getFullYear(), currentDate.getMonth(), 1);
//         var initialEndDate = new Date(currentDate.getFullYear(), currentDate.getMonth() + 1, 0);
//         const debounce = (func, delay) => {
//             let timeoutId;
//             return (...args) => {
//                 clearTimeout(timeoutId); // Clear the previous timeout
//                 timeoutId = setTimeout(() => func(...args), delay); // Set a new timeout
//             };
//         };

//         // Update module dates with confirmation modal and AJAX request
//         function updateModuleDates(task, start, end) {
//             // Show the confirmation modal
//             $('#confirmUpdateDates').modal('show');

//             // Ensure that we only bind the click event once
//             $('#confirm_update_dates').off('click').on('click', function () {
//                 // Hide the confirmation modal
//                 $('#confirmUpdateDates').modal('hide');

//                 // Send the AJAX request to update the dates
//                 $.ajax({
//                     type: "POST",
//                     url: "/master-panel/projects/gantt-chart-view/update-module-dates",
//                     data: {
//                         'module': task,
//                         'start_date': start,
//                         'end_date': end,
//                         '_token': $('meta[name="csrf-token"]').attr('content')
//                     },
//                     dataType: "JSON",
//                     success: function (response) {
//                         if (!response.error) {
//                             toastr.success(response.message);
//                         }
//                     },
//                     error: function (xhr) {
//                         var errors = xhr.responseJSON.errors;
//                         var errorMessages = [];
//                         $.each(errors, function (key, value) {
//                             errorMessages.push(value);
//                         });
//                         toastr.error(errorMessages.join('<br>'));
//                     }
//                 });
//             });
//         }

//         // Create the debounced version of updateModuleDates
//         const debouncedUpdateModuleDates = debounce(updateModuleDates, 500);

//         // Initialize the Gantt chart
//         var gantt = new Gantt("#gantt", data, {
//             view_mode: viewMode,
//             date_format: 'YYYY-MM-DD',
//             start_date: initialStartDate,
//             end_date: initialEndDate,
//             year_view_pixel_per_day: 0.5, // New setting for year view
//             custom_popup_html: null,
//             on_click: function (task) {
//                 redirectToInfoPage(task);
//             },

//             on_date_change: function (task, start, end) {
//                 debouncedUpdateModuleDates(task, start, end); // Call the debounced function
//             },
//             on_progress_change: function (task, progress) {
//                 // Handle progress change
//             },
//             on_view_change: function (mode) {
//                 // Handle view mode change
//             }
//         });


//         /**
//          * Updates the date display in the Gantt chart view.
//          *
//          * @param {Date} startDate - The start date to display.
//          * @param {Date} endDate - The end date to display.
//          * @return {void}
//          */
//         function updateDateDisplay(startDate, endDate) {
//             $('#current-date').text(
//                 startDate.toLocaleString('default', { month: 'long', year: 'numeric' }) +
//                 " - " +
//                 endDate.toLocaleString('default', { month: 'long', year: 'numeric' })
//             );

//         }

//         /**
//          * Changes the current month by a specified delta.
//          *
//          * @param {number} delta - The number of months to change. A positive value moves forward in time,
//          * a negative value moves backward in time.
//          * @return {void}
//          */
//         function changeMonth(delta) {
//             currentDate.setMonth(currentDate.getMonth() + delta);
//             updateGanttDates();
//         }


//         /**
//          * Updates the Gantt chart view based on the current view mode and selected month.
//          *
//          * @return {void}
//          */
//         function updateGanttDates() {
//             var startDate, endDate;

//             switch (viewMode) {
//                 case 'Day':
//                     startDate = new Date(currentDate.getFullYear(), currentDate.getMonth(), currentDate.getDate());
//                     endDate = new Date(startDate);
//                     endDate.setDate(endDate.getDate() + 1);
//                     break;
//                 case 'Week':
//                     startDate = new Date(currentDate.getFullYear(), currentDate.getMonth(), currentDate.getDate() -
//                         currentDate.getDay());
//                     endDate = new Date(startDate);
//                     endDate.setDate(endDate.getDate() + 7);
//                     break;
//                 case 'Month':
//                     startDate = new Date(currentDate.getFullYear(), currentDate.getMonth(), 1);
//                     endDate = new Date(currentDate.getFullYear(), currentDate.getMonth() + 1, 0);
//                     break;
//             }

//             var filteredData = $.grep(data, function (task) {
//                 var taskStart = new Date(task.start);
//                 var taskEnd = new Date(task.end);
//                 return (taskStart <= endDate && taskEnd >= startDate) ||
//                     (taskStart >= startDate && taskStart <= endDate) ||
//                     (taskEnd >= startDate && taskEnd <= endDate);
//             });

//             if (filteredData.length === 0) {
//                 console.warn("No tasks found for the selected date range. Adding a dummy task.");
//                 filteredData.push({
//                     id: 'dummy_task',
//                     name: 'No tasks in this range',
//                     start: startDate,
//                     end: endDate,
//                     progress: 0,
//                     custom_class: 'gantt-task'
//                 });
//             }

//             gantt.refresh(filteredData);
//             updateDateDisplay(startDate, endDate);
//         }

//         $('#prev').on('click', function () {
//             changeMonth(-1);

//         });

//         $('#next').on('click', function () {

//             changeMonth(1);

//         });

//         /**
//          * Changes the view mode of the Gantt chart view.
//          *
//          * @param {string} mode - The new view mode.
//          * @return {void}
//          */
//         function changeViewMode(mode) {
//             viewMode = mode;
//             updateGanttDates();
//             console.log(viewMode);


//             gantt.change_view_mode(mode);
//         }

//         $('#day-view').on('click', function () {
//             $('.view-btns').removeClass('btn-primary');
//             $('#navigation-buttons-container').hide();
//             $(this).addClass('btn-primary');
//             changeViewMode('Day');
//         });

//         $('#week-view').on('click', function () {
//             $('.view-btns').removeClass('btn-primary');
//             $('#navigation-buttons-container').show();
//             $(this).addClass('btn-primary');
//             changeViewMode('Week');
//         });

//         $('#month-view').on('click', function () {
//             $('.view-btns').removeClass('btn-primary');
//             $('#navigation-buttons-container').show();
//             $(this).addClass('btn-primary');
//             changeViewMode('Month');
//         });


//         changeViewMode('Day');

//         /**
//          * Redirects to the information page of a project or task based on the provided data.
//          *
//          * @param {object} data - An object containing the type and id of the module to redirect to.
//          * @return {void}
//          */
//         function redirectToInfoPage(data) {
//             if (data.type === 'project') {
//                 window.location.href = '/master-panel/projects/information/' + data.id;
//             } else if (data.type === 'task') {
//                 window.location.href = '/master-panel/tasks/information/' + data.id;
//             } else {
//                 console.log('Unknown module type: ' + data.type);
//             }
//         }
//         /**
//          * Updates the dates of a module in the gantt chart view.
//          *
//          * @param {string} task - The module to be updated.
//          * @param {string} start - The new start date of the module.
//          * @param {string} end - The new end date of the module.
//          * @return {void}
//          */


//     });



$(document).ready(function () {
    var viewMode = 'Day'; // Default view mode
    var currentDate = new Date();
    var gantt;
    var initialStartDate = new Date(currentDate.getFullYear(), currentDate.getMonth(), 1);
    var initialEndDate = new Date(currentDate.getFullYear(), currentDate.getMonth() + 1, 0);

    // Initialize Gantt chart
    function initGanttChart(data) {
        if (!gantt) {
            gantt = new Gantt("#gantt", data, {
                view_mode: viewMode,
                date_format: 'YYYY-MM-DD',
                start_date: initialStartDate,
                end_date: initialEndDate,
                year_view_pixel_per_day: 0.5,
                custom_popup_html: null,
                on_click: function (task) {
                    redirectToInfoPage(task);
                },
                on_date_change: function (task, start, end) {
                    debouncedUpdateModuleDates(task, start, end);
                },
                on_progress_change: function (task, progress) {
                    // Handle progress change if needed
                },
                on_view_change: function (mode) {
                    // Handle view mode change if needed
                }
            });
        } else {
            gantt.refresh(data); // Refresh existing gantt instance
        }
    }
    function validateDateRange(start, end) {
        return start <= end;
    }

    // Fetch data and initialize/update Gantt chart
    function fetchDataAndUpdateGantt(startDate, endDate) {
        $.ajax({
            type: "GET",
            url: "/master-panel/projects/fetch-gantt-data",
            data: {
                'start_date': startDate.toISOString().split('T')[0],
                'end_date': endDate.toISOString().split('T')[0]
            },
            dataType: "JSON",
            success: function (response) {
                var projects = response;
                var data = processProjectsData(projects, startDate, endDate);
                initGanttChart(data);
                updateDateDisplay(startDate, endDate);
            },
            error: function (xhr) {
                var errors = xhr.responseJSON.errors;
                var errorMessages = [];
                $.each(errors, function (key, value) {
                    errorMessages.push(value);
                });
                toastr.error(errorMessages.join('<br>'));
            }
        });
    }

    // Process and validate projects data
    function processProjectsData(projects, startDate, endDate) {
        var processedData = [];
        $.each(projects, function (index, project) {
            if (project.id && project.title && project.start_date && project.end_date) {
                var projectStart = new Date(project.start_date);
                var projectEnd = new Date(project.end_date);

                if (!validateDateRange(projectStart, projectEnd)) {
                    console.error("Invalid date range for project:", project);
                    return;
                }

                processedData.push({
                    id: project.id.toString(),
                    name: project.title,
                    start: projectStart,
                    end: projectEnd,
                    progress: calculateTimeBasedProgress(project.start_date, project.end_date),
                    dependencies: [],
                    type: 'project',
                    custom_class: 'gantt-project'
                });

                if (Array.isArray(project.tasks)) {
                    $.each(project.tasks, function (index, task) {
                        if (task.id && task.title && task.start_date && task.due_date) {
                            var taskStart = new Date(task.start_date);
                            var taskEnd = new Date(task.due_date);

                            if (!validateDateRange(taskStart, taskEnd)) {
                                console.error("Invalid date range for task:", task);
                                return;
                            }

                            processedData.push({
                                id: task.id.toString(),
                                name: task.title,
                                start: taskStart,
                                end: taskEnd,
                                progress: calculateTimeBasedProgress(task.start_date, task.due_date),
                                type: 'task',
                                dependencies: [project.id.toString()],
                                custom_class: 'gantt-task'
                            });
                        } else {
                            console.error("Task data is incomplete", task);
                        }
                    });
                }
            } else {
                console.error("Project data is incomplete", project);
            }
        });

        if (processedData.length === 0) {
            processedData.push({
                id: 'no_tasks',
                name: 'No tasks or projects available',
                start: startDate,
                end: endDate,
                progress: 100,
                custom_class: 'gantt-task no-data'
            });
        }

        return processedData;
    }


    // Validate date ranges
    function validateDateRange(start, end) {
        return start <= end;
    }

    // Calculate time-based progress
    function calculateTimeBasedProgress(startDate, endDate) {
        var now = new Date();
        var start = new Date(startDate);
        var end = new Date(endDate);
        var totalDuration = end - start;
        var elapsed = now - start;
        var progress = Math.min((elapsed / totalDuration) * 100, 100);
        return progress;
    }

    // Update module dates with AJAX request
    function updateModuleDates(task, start, end) {
        $('#confirmUpdateDates').modal('show');
        $('#confirm_update_dates').off('click').on('click', function () {
            $('#confirmUpdateDates').modal('hide');
            $.ajax({
                type: "POST",
                url: "/master-panel/projects/gantt-chart-view/update-module-dates",
                data: {
                    'module': task,
                    'start_date': start,
                    'end_date': end,
                    '_token': $('meta[name="csrf-token"]').attr('content')
                },
                dataType: "JSON",
                success: function (response) {
                    if (!response.error) {
                        toastr.success(response.message);
                    }
                },
                error: function (xhr) {
                    var errors = xhr.responseJSON.errors;
                    var errorMessages = [];
                    $.each(errors, function (key, value) {
                        errorMessages.push(value);
                    });
                    toastr.error(errorMessages.join('<br>'));
                }
            });
        });
    }

    // Debounce function to limit update requests
    const debounce = (func, delay) => {
        let timeoutId;
        return (...args) => {
            clearTimeout(timeoutId);
            timeoutId = setTimeout(() => func(...args), delay);
        };
    };

    // Create the debounced version of updateModuleDates
    const debouncedUpdateModuleDates = debounce(updateModuleDates, 500);

    // Update Gantt chart view based on the current view mode and selected month
    function updateGanttDates() {
        var startDate, endDate;

        switch (viewMode) {
            case 'Day':
                startDate = new Date(currentDate.getFullYear(), currentDate.getMonth(), currentDate.getDate());
                endDate = new Date(startDate);
                endDate.setDate(endDate.getDate() + 1);
                break;
            case 'Week':
                startDate = new Date(currentDate.getFullYear(), currentDate.getMonth(), currentDate.getDate() - currentDate.getDay());
                endDate = new Date(startDate);
                endDate.setDate(endDate.getDate() + 7);
                break;
            case 'Month':
                startDate = new Date(currentDate.getFullYear(), currentDate.getMonth(), 1);
                endDate = new Date(currentDate.getFullYear(), currentDate.getMonth() + 1, 0);
                break;
        }

        fetchDataAndUpdateGantt(startDate, endDate);
    }

    function formatDate(date) {
        return date.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });
    }
    // Update the date display
    function updateDateDisplay(startDate, endDate) {
        console.log(startDate, endDate);
        $('#current-date').text(
            formatDate(startDate) + " to " + formatDate(endDate)
        );

    }

    // Change the month view
    function changeMonth(delta) {
        currentDate.setMonth(currentDate.getMonth() + delta);
        updateGanttDates();
    }

    // Change view mode
    function changeViewMode(mode) {
        viewMode = mode;
        if (gantt) {
            gantt.change_view_mode(mode); // Call method only if gantt is initialized
        }
        updateGanttDates();
    }

    // Button event handlers
    $('#prev').on('click', function () {
        changeMonth(-1);
    });

    $('#next').on('click', function () {
        changeMonth(1);
    });

    $('#day-view').on('click', function () {
        $('.view-btns').removeClass('btn-primary');
        $('#navigation-buttons-container').hide();
        $(this).addClass('btn-primary');
        changeViewMode('Day');
    });

    $('#week-view').on('click', function () {
        $('.view-btns').removeClass('btn-primary');
        $('#navigation-buttons-container').show();
        $(this).addClass('btn-primary');
        changeViewMode('Week');
    });

    $('#month-view').on('click', function () {
        $('.view-btns').removeClass('btn-primary');
        $('#navigation-buttons-container').show();
        $(this).addClass('btn-primary');
        changeViewMode('Month');
    });
    function redirectToInfoPage(data) {
        const baseUrl = '/master-panel/';

        if (data && data.type && data.id) {
            let url;
            switch (data.type) {
                case 'project':
                    url = baseUrl + 'projects/information/' + data.id;
                    break;
                case 'task':
                    url = baseUrl + 'tasks/information/' + data.id;
                    break;
                default:
                    console.error('Unknown module type:', data.type);
                    return; // Exit the function early if the type is unknown
            }

            // Open the URL in a new tab
            window.open(url, '_blank');
        } else {
            console.error('Invalid data provided. Make sure it has "type" and "id" properties.');
        }
    }

    // Initial load
    updateGanttDates();
});




