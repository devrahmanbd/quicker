/**
 * Dashboard Analytics
 */
'use strict';
function queryParamsProjectMedia(p) {
    return {
        page: p.offset / p.limit + 1,
        limit: p.limit,
        sort: p.sort,
        order: p.order,
        offset: p.offset,
        search: p.search
    };
}
document.addEventListener('DOMContentLoaded', function () {
    var series = JSON.parse(seriesData);
    var labels = JSON.parse(labelsData);
    var colors = JSON.parse(statusColors);
    var options = {
        chart: {
            type: 'donut'
        },
        series: series,
        labels: labels,
        colors: colors.map(color => getBootstrapColor(color)), // Map color names to their Bootstrap equivalents
        legend: {
            position: 'top'
        },
        plotOptions: {
            pie: {
                donut: {
                    size: '70%'
                }
            }
        },
        responsive: [{
            breakpoint: 480,
            options: {
                chart: {
                    width: 200
                },
                legend: {
                    position: 'bottom'
                }
            }
        }]
    };
    var chart = new ApexCharts(document.querySelector("#taskSummaryChart"), options);
    chart.render();
});
// Function to map color names to Bootstrap color codes
function getBootstrapColor(colorName) {
    const colorMap = {
        'primary': '#0d6efd',
        'secondary': '#6c757d',
        'success': '#198754',
        'danger': '#dc3545',
        'warning': '#ffc107',
        'info': '#0dcaf0',
        'light': '#f8f9fa',
        'dark': '#212529'
    };
    return colorMap[colorName] || colorName; // Return the mapped color or the original if not found
}
function queryParams(p) {
    return {
        "user_id": $('#user_filter').val(),
        "client_id": $('#client_filter').val(),
        "activity": $('#activity_filter').val(),
        "type": 'project',
        "type_id": $('#type_id').val(),
        "date_from": $('#activity_log_between_date_from').val(),
        "date_to": $('#activity_log_between_date_to').val(),
        page: p.offset / p.limit + 1,
        limit: p.limit,
        sort: p.sort,
        order: p.order,
        offset: p.offset,
        search: p.search
    };
}
function queryParamsProjectMilestones(p) {
    return {
        "type_id": $('#type_id').val(),
        "start_date_from": $('#start_date_from').val(),
        "start_date_to": $('#start_date_to').val(),
        "end_date_from": $('#end_date_from').val(),
        "end_date_to": $('#end_date_to').val(),
        "status": $('#status_filter').val(),
        page: p.offset / p.limit + 1,
        limit: p.limit,
        sort: p.sort,
        order: p.order,
        offset: p.offset,
        search: p.search
    };
}
function actionsFormatterProjectMilestones(value, row, index) {
    return [
        '<a href="javascript:void(0);" class="edit-milestone" data-bs-toggle="modal" data-bs-target="#edit_milestone_modal" data-id=' + row.id + ' title=' + label_update + ' class="card-link"><i class="bx bx-edit mx-1"></i></a>' +
        '<button title=' + label_delete + ' type="button" class="btn delete" data-id=' + row.id + ' data-type="milestone" data-table="project_milestones_table">' +
        '<i class="bx bx-trash text-danger mx-1"></i>' +
        '</button>'
    ]
}
function actionsFormatter(value, row, index) {
    return [
        '<button title=' + label_delete + ' type="button" class="btn delete" data-id=' + row.id + ' data-type="activity-log" data-table="activity_log_table">' +
        '<i class="bx bx-trash text-danger mx-1"></i>' +
        '</button>'
    ]
}
$('#start_date_between').on('apply.daterangepicker', function (ev, picker) {
    var startDate = picker.startDate.format('YYYY-MM-DD');
    var endDate = picker.endDate.format('YYYY-MM-DD');
    $('#start_date_from').val(startDate);
    $('#start_date_to').val(endDate);
    $('#project_milestones_table').bootstrapTable('refresh');
});
$('#start_date_between').on('cancel.daterangepicker', function (ev, picker) {
    $('#start_date_from').val('');
    $('#start_date_to').val('');
    $('#project_milestones_table').bootstrapTable('refresh');
    $('#start_date_between').val('');
});
$('#end_date_between').on('apply.daterangepicker', function (ev, picker) {
    var startDate = picker.startDate.format('YYYY-MM-DD');
    var endDate = picker.endDate.format('YYYY-MM-DD');
    $('#end_date_from').val(startDate);
    $('#end_date_to').val(endDate);
    $('#project_milestones_table').bootstrapTable('refresh');
});
$('#end_date_between').on('cancel.daterangepicker', function (ev, picker) {
    $('#end_date_from').val('');
    $('#end_date_to').val('');
    $('#project_milestones_table').bootstrapTable('refresh');
    $('#end_date_between').val('');
});
$('#status_filter').on('change', function (e) {
    e.preventDefault();
    $('#project_milestones_table').bootstrapTable('refresh');
});
$('#milestone_progress').on('change', function (e) {
    var rangeValue = $(this).val();
    $('.milestone-progress').text(rangeValue + '%');
});
$(document).ready(function () {
    // Constants and cache DOM elements
    const imageBaseUrl = window.location.origin;
    const $commentModal = new bootstrap.Modal($('#commentModal')[0]);
    const $replyModal = new bootstrap.Modal($('#replyModal')[0]);
    const $commentForm = $('#comment-form');
    const $replyForm = $('#replyForm');
    const $commentThread = $('.comment-thread');
    const $loadMoreButton = $('#load-more-comments');
    const $hideButton = $('#hide-comments');
    let visibleCommentsCount = 5;
    // Event Handlers
    $(document).on('click', '.open-reply-modal', openReplyModal);
    $commentForm.on('submit', handleCommentSubmit);
    $replyForm.on('submit', handleReplySubmit);
    $(document).on('click', '#cancel-comment-btn', () => cancelForm($commentForm, $commentModal));
    $(document).on('click', '#cancel-reply-btn', () => cancelForm($replyForm, $replyModal));
    $(document).on('mouseenter', '.attachment-link', function () { togglePreview($(this), true); });
    $(document).on('mouseleave', '.attachment-link', function () { togglePreview($(this), false); });
    $loadMoreButton.on('click', loadMoreComments);
    $hideButton.on('click', hideComments);
    // Initialize comment visibility
    initializeCommentVisibility();
    function openReplyModal() {
        const parentId = $(this).data('comment-id');
        $replyForm.find('input[name="parent_id"]').val(parentId);
        $replyModal.show();
    }
    function handleCommentSubmit(event) {
        event.preventDefault();
        submitForm($(this), $commentModal, prependNewComment);
    }
    function handleReplySubmit(event) {
        event.preventDefault();
        submitForm($(this), $replyModal, prependNewReply);
    }
    function submitForm($form, modal, successCallback) {
        $.ajax({
            url: $form.attr('action'),
            type: 'POST',
            data: new FormData($form[0]),
            processData: false,
            contentType: false,
            success: function (data) {
                if (data.success) {
                    modal.hide();
                    successCallback(data);
                    $('.no_comments').hide();
                    toastr.success(data.message);
                    $form[0].reset();
                }
            },
            error: function (xhr) {
                console.error('An error occurred:', xhr.responseText);
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    const errors = xhr.responseJSON.errors;
                    Object.keys(errors).forEach(function (field) {
                        if (Array.isArray(errors[field])) {
                            errors[field].forEach(function (message) {
                                toastr.error(message);
                            });
                        } else if (typeof errors[field] === 'object') {
                            Object.values(errors[field]).forEach(function (nestedErrors) {
                                if (Array.isArray(nestedErrors)) {
                                    nestedErrors.forEach(function (message) {
                                        toastr.error(message);
                                    });
                                } else {
                                    toastr.error(nestedErrors);
                                }
                            });
                        } else {
                            toastr.error(errors[field]);
                        }
                    });
                } else {
                    toastr.error('An error occurred while processing your request.');
                }
            }
        });
    }
    function prependNewComment(data) {
        $commentThread.prepend(createCommentHTML(data, true));
    }
    function prependNewReply(data) {
        const $parentComment = $(`#comment-${data.comment.parent_id}`);
        let $repliesContainer = $parentComment.find('.replies');
        if ($repliesContainer.length === 0) {
            $repliesContainer = $('<div class="replies"></div>');
            $parentComment.append($repliesContainer);
        }
        $repliesContainer.prepend(createCommentHTML(data, false));
    }
    function createCommentHTML(data, isMainComment) {
        return `
            <details open class="comment" id="comment-${data.comment.id}">
                <a href="#comment-${data.comment.id}" class="comment-border-link">
                    <span class="sr-only">Jump to comment-${data.comment.id}</span>
                </a>
                <summary>
                    <div class="comment-heading">
                        <div class="comment-avatar">
                            <img src="${data.user.photo ? `${imageBaseUrl}/storage/${data.user.photo}` : `${imageBaseUrl}/storage/photos/no-image.jpg`}"
                                 alt="${data.user.first_name} ${data.user.last_name}"
                                 class="bg-footer-theme rounded-circle border" width="40">
                        </div>
                        <div class="comment-info">
                            <a href="${imageBaseUrl}/users/${data.user.id}"
                               class="comment-author ${isMainComment ? 'fw-semibold' : 'fw-light'} text-body">
                                ${data.user.first_name} ${data.user.last_name}
                            </a>
                            <p class="m-0">${data.created_at}</p>
                        </div>
                    </div>
                </summary>
                <div class="comment-body">
                    <p ${!isMainComment ? 'class="text-secondary"' : ''}>${data.comment.content}</p>
                    ${createAttachmentsHTML(data.comment.attachments)}
                    ${isMainComment ? `<button type="button" class="open-reply-modal mt-3" data-comment-id="${data.comment.id}">Reply</button>` : ''}
                </div>
            </details>
        `;
    }
    function createAttachmentsHTML(attachments) {
        if (!attachments || attachments.length === 0) return '';
        return `
            <div class="attachments mt-2">
                ${attachments.map(att => `
                    <div class="attachment-item d-flex align-items-center justify-content-between">
                        <div class="attachment-preview-container">
                            <a href="${imageBaseUrl}/storage/${att.file_path}" target="_blank"
                               class="attachment-link" data-preview-url="${imageBaseUrl}/storage/${att.file_path}">
                                ${att.file_name}
                            </a>
                            <div class="attachment-preview"></div>
                        </div>
                        <a class="btn btn-sm btn-outline-dark mb-2"
                           href="${imageBaseUrl}/storage/${att.file_path}" download="${att.file_name}">
                            Download
                        </a>
                    </div>
                `).join('')}
            </div>
        `;
    }
    function cancelForm($form, modal) {
        $form[0].reset();
        modal.hide();
    }
    function togglePreview($link, show) {
        const $previewContainer = $link.next('.attachment-preview');
        if (show) {
            const previewUrl = $link.data('preview-url');
            $previewContainer.empty();
            if (previewUrl.match(/\.(jpeg|jpg|gif|png)$/i)) {
                $('<img>', { src: previewUrl, css: { maxWidth: '300px', maxHeight: '200px' } }).appendTo($previewContainer);
            } else if (previewUrl.match(/\.(pdf)$/i)) {
                $('<iframe>', { src: previewUrl, width: '250', height: '150' }).appendTo($previewContainer);
            } else {
                $previewContainer.text('Preview not available');
            }
            $previewContainer.show();
        } else {
            $previewContainer.hide();
        }
    }
    function initializeCommentVisibility() {
        const $comments = $commentThread.find('.comment');
        $comments.each(function (index) {
            $(this).toggle(index < visibleCommentsCount);
        });
        $hideButton.hide(); // Hide the "Hide" button initially
        $loadMoreButton.toggle($comments.length > visibleCommentsCount);
    }
    function loadMoreComments() {
        visibleCommentsCount += 5;
        const $comments = $commentThread.find('.comment');
        $comments.each(function (index) {
            $(this).toggle(index < visibleCommentsCount);
        });
        $hideButton.toggle(visibleCommentsCount > 5); // Show the "Hide" button if more than 5 comments are visible
        $loadMoreButton.toggle(visibleCommentsCount < $comments.length); // Hide the "Load More" button if all comments are visible
    }
    function hideComments() {
        visibleCommentsCount = 5;
        const $comments = $commentThread.find('.comment');
        $comments.each(function (index) {
            $(this).toggle(index < visibleCommentsCount);
        });
        $hideButton.hide(); // Hide the "Hide" button
        $loadMoreButton.show(); // Show the "Load More" button
    }
});
$(document).ready(function () {
    // Check if the URL contains the specific hash
    if (window.location.hash === '#navs-top-discussions') {
        console.log('Activating Discussions tab');
        // Select the tab trigger
        var discussionsTabTrigger = document.querySelector('[data-bs-target="#navs-top-discussions"]');
        if (discussionsTabTrigger) {
            // Activate the tab
            var tabInstance = new bootstrap.Tab(discussionsTabTrigger);
            tabInstance.show();
        } else {
            console.error('Discussions tab trigger not found.');
        }
    }
});
$(document).on('click', '.edit-comment', function () {
    var commentId = $(this).data('comment-id');
    $.ajax({
        type: "GET",
        url: "/master-panel/projects/comments/get/" + commentId,
        dataType: "JSON",
        success: function (response) {
            console.log(response);
            $('#comment_id').val(response.comment.id);
            $('#edit-project-comment-content').val(stripHtml(response.comment.content));
            $('#EditCommentModal').modal('show');
        }
    });
});
$(document).on('click', '.delete-comment', function () {
    var commentId = $(this).data('comment-id');
    $.ajax({
        type: "GET",
        url: "/master-panel/projects/comments/get/" + commentId,
        dataType: "JSON",
        success: function (response) {
            console.log(response);
            $('#delete_comment_id').val(response.comment.id);
            $('#DeleteCommentModal').modal('show');
        }
    });
});
$(document).ready(function () {
    // Initialize for different textareas
    initializeMentionTextarea($('#project-comment-content'));  // For general mention textarea
    initializeMentionTextarea($('#edit-project-comment-content'));      // For edit comment textarea
    initializeMentionTextarea($('#project-reply-content'));    // For create comment textarea
});