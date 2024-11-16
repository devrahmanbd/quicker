/**
 * Dashboard Analytics
 */
'use strict';

function queryParamsTaskMedia(p) {
    return {
        page: p.offset / p.limit + 1,
        limit: p.limit,
        sort: p.sort,
        order: p.order,
        offset: p.offset,
        search: p.search
    };
}

function queryParams(p) {
    return {
        "user_id": $('#user_filter').val(),
        "client_id": $('#client_filter').val(),
        "activity": $('#activity_filter').val(),
        "type": 'task',
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

function actionsFormatter(value, row, index) {
    return [
        '<button title=' + label_delete + ' type="button" class="btn delete" data-id=' + row.id + ' data-type="activity-log" data-table="activity_log_table">' +
        '<i class="bx bx-trash text-danger mx-1"></i>' +
        '</button>'
    ]
}




$('#activity_log_between_date').on('apply.daterangepicker', function (ev, picker) {
    var startDate = picker.startDate.format('YYYY-MM-DD');
    var endDate = picker.endDate.format('YYYY-MM-DD');

    $('#activity_log_between_date_from').val(startDate);
    $('#activity_log_between_date_to').val(endDate);

    $('#activity_log_table').bootstrapTable('refresh');
});

$('#activity_log_between_date').on('cancel.daterangepicker', function (ev, picker) {
    $('#activity_log_between_date_from').val('');
    $('#activity_log_between_date_to').val('');
    $('#activity_log_table').bootstrapTable('refresh');
    $('#activity_log_between_date').val('');
});


$('#user_filter,#client_filter,#activity_filter').on('change', function (e) {
    e.preventDefault();
    $('#activity_log_table').bootstrapTable('refresh');
});

$(document).ready(function () {
    // Constants and cache DOM elements
    const imageBaseUrl = window.location.origin;
    const $commentModal = new bootstrap.Modal($('#task_commentModal')[0]);
    const $replyModal = new bootstrap.Modal($('#task-reply-modal')[0]);
    const $commentForm = $('#comment-form');
    const $replyForm = $('#replyForm');
    const $commentThread = $('.comment-thread');
    const $loadMoreButton = $('#load-more-comments');
    const $hideButton = $('#hide-comments');
    let visibleCommentsCount = 5;

    // Event Handlers
    $(document).on('click', '.open-task-reply-modal', openReplyModal);
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
                    ${isMainComment ? `<button type="button" class="open-task-reply-modal mt-3" data-comment-id="${data.comment.id}">Reply</button>` : ''}
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
$(document).on('click', '.edit-task-comment', function () {
    var commentId = $(this).data('comment-id');
    $.ajax({
        type: "GET",
        url: "/master-panel/tasks/comments/get/" + commentId,
        dataType: "JSON",
        success: function (response) {
            console.log(response);
            $('#comment_id').val(response.comment.id);
            $('#task-comment-edit-content').val(stripHtml(response.comment.content));
            $('#TaskEditCommentModal').modal('show');
        }
    });
});
$(document).on('click', '.delete-task-comment', function () {
    var commentId = $(this).data('comment-id');
    $.ajax({
        type: "GET",
        url: "/master-panel/tasks/comments/get/" + commentId,
        dataType: "JSON",
        success: function (response) {
            console.log(response);
            $('#delete_comment_id').val(response.comment.id);
            $('#TaskDeleteCommentModal').modal('show');
        }
    });
});
$(document).ready(function () {
    // Initialize for different textareas
    initializeMentionTextarea($('#task-reply-content'));     // For reply textarea
    initializeMentionTextarea($('#task-comment-content'));     // For reply textarea
    initializeMentionTextarea($('#task-comment-edit-content'));     // For reply textarea
});
