'use strict';
$('#status_filter').on('change', function (e) {
    var status = $(this).val();
    location.href = setUrlParameter(location.href, 'status', status);
});
$('#sort').on('change', function (e) {
    var sort = $(this).val();
    location.href = setUrlParameter(location.href, 'sort', sort);
});
$(document).ready(function () {
    $('#sort').select2();
});

$('#tags_filter').on("click", function () {
    var routePrefix = $(this).data('routePrefix');
    // Get the selected values from status select and other filters
    var status = $('#status_filter').val();
    var sort = $('#sort').val();
    // Get selected tags using Select2
    var selectedTags = $('#selected_tags').val();

    // Form the URL with the selected filters
    var url = "/master-panel/projects";
    var params = [];

    if (status) {
        params.push("status=" + status);
    }

    if (sort) {
        params.push("sort=" + sort);
    }

    if (selectedTags && selectedTags.length > 0) {
        params.push("tags[]=" + selectedTags.join("&tags[]="));
    }

    if (params.length > 0) {
        url += "?" + params.join("&");
    }
    // Redirect to the URL
    window.location.href = url;
});


function setUrlParameter(url, paramName, paramValue) {
    paramName = paramName.replace(/\s+/g, '-');
    if (paramValue == null || paramValue == '') {
        return url.replace(new RegExp('[?&]' + paramName + '=[^&#]*(#.*)?$'), '$1')
            .replace(new RegExp('([?&])' + paramName + '=[^&]*&'), '$1');
    }
    var pattern = new RegExp('\\b(' + paramName + '=).*?(&|#|$)');
    if (url.search(pattern) >= 0) {
        return url.replace(pattern, '$1' + paramValue + '$2');
    }
    url = url.replace(/[?#]$/, '');
    return url + (url.indexOf('?') > 0 ? '&' : '?') + paramName + '=' + paramValue;
}

function userFormatter(value, row, index) {
    return '<div class="d-flex">' +
        row.profile +
        '</div>';

}

function clientFormatter(value, row, index) {
    return '<div class="d-flex">' +
        row.profile +
        '</div>';

}
document.addEventListener('DOMContentLoaded', function () {
    const columns = Array.from(document.querySelectorAll('.kanban-column-body'));

    // Get the create project button
    const createProjectBtn = document.querySelector('.create-project-btn');

    // Exclude the create project button from drag-and-drop
    const drake = dragula(columns, {
        direction: 'vertical',
        moves: function (el, container, handle) {
            return !el.classList.contains('create-project-btn'); // Exclude the button
        },
        accepts: function (el, target) {
            return !el.classList.contains('create-project-btn'); // Exclude the button
        },
        invalid: function (el, handle) {
            return el.classList.contains('create-project-btn'); // Exclude the button
        }
    });
    // Event when dragging starts
    drake.on('drag', function (el) {
        el.classList.add('dragging'); // Add visual style to the dragged element
    });

    // Event when dragging ends
    drake.on('dragend', function (el) {
        el.classList.remove('dragging'); // Remove visual style from the dragged element
        el.classList.add('dropped'); // Add dropped style
        document.querySelectorAll('.drop-target').forEach(target => {
            target.classList.remove('drop-target'); // Remove highlight from all columns
        });
    });

    // Event when dragging over a container
    drake.on('over', function (el, container) {
        container.classList.add('drop-target'); // Add highlight to the container
    });

    // Event when dragging out of a container
    drake.on('out', function (el, container) {
        container.classList.remove('drop-target'); // Remove highlight from the container
    });
    drake.on('drop', function (el, target, source, sibling) {
        // Get the new status based on the target column's data attribute
        const newStatus = target.closest('.kanban-column').dataset.statusId;

        // Extract card ID from the element
        const cardId = el.dataset.cardId;

        // Update project status in the backend using jQuery AJAX
        $.ajax({
            url: '/master-panel/update-project-status',
            type: 'POST',
            dataType: 'json',
            contentType: 'application/json',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            data: JSON.stringify({
                id: cardId,
                statusId: newStatus
            }),
            success: function (response) {
                if (response.error === false) {
                    toastr.success(response.message);

                    // Optionally, update the frontend to reflect any changes
                    // For example, updating the count of items in the column headers
                    updateColumnCounts();
                } else {
                    toastr.error(response.message);
                }
            },
            error: function (xhr, status, error) {
                console.error('Error:', error);
            }
        });
    });

    function updateColumnCounts() {
        // Function to update the counts of items in each column header
        document.querySelectorAll('.kanban-column').forEach(column => {
            const statusId = column.dataset.statusId;
            const count = column.querySelectorAll('.kanban-card').length;
            column.querySelector('.column-count').textContent = `${count}/${totalProjectsCount}`;
        });
    }

    // Optionally, calculate the total number of projects if needed
    const totalProjectsCount = document.querySelectorAll('.kanban-card').length;
});


$(document).ready(function () {
    const urlParams = new URLSearchParams(window.location.search);
    const selectedStatus = urlParams.get('status');
    let initialSelection = true;  // Flag to prevent reload on initial selection
    // alert(selectedStatus);
    initSelect2Ajax(
        '#status_filter',
        '/master-panel/status/search',
        label_filter_status,
        true,                                  // Allow clear
        0,                                     // Minimum input length
        true                                   // Allow Initials Options
    );
    if (selectedStatus) {
        $.ajax({
            url: '/master-panel/status/search', // Reuse the same route for fetching the single status
            data: { q: '', page: 1 },
            dataType: 'json',
            success: function (data) {
                // Look for the status that matches the selectedStatus from URL
                const matchedStatus = data.items.find(item => item.id == selectedStatus);

                if (matchedStatus && initialSelection == true) {
                    // Create a new option dynamically and set it as selected
                    let option = new Option(matchedStatus.text, matchedStatus.id, true, true);
                    $('#status_filter').append(option);

                    // Only trigger change if it's not the initial selection
                    initialSelection = false;  // Reset flag after setting initial option
                }
            }
        });
    }
});

