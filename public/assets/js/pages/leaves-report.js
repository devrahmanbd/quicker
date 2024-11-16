$(function () {

    $('#leaves_report_table').on('load-success.bs.table', function (e, data) {
        $('#rejected-leaves').text(data.summary.total_rejected_leaves);
        $('#pending-leaves').text(data.summary.total_pending_leaves);
        $('#approved-leaves').text(data.summary.total_approved_leaves);
        $('#total-leaves').text((data.summary.total_leaves));
        $('#total-users').text(data.summary.total_users);
    });
});
$(document).ready(function () {
    $('#export_button').click(function () {
        // Prepare query parameters
        const queryParams = leaves_report_query_params({ offset: 0, limit: 1000, sort: 'id', order: 'desc', search: '' });
        // Construct the export URL
        const exportUrl = leaves_report_export_url + '?' + $.param(queryParams);
        // Open the export URL in a new tab or window
        window.open(exportUrl, '_blank');
    });
    $('#filter_date_range').daterangepicker({
        autoUpdateInput: false,
        locale: {
            cancelLabel: 'Clear'
        }
    });
    $('#filter_date_range').on('apply.daterangepicker', function (ev, picker) {
        // Set the value of the input field to the selected range
        $(this).val(picker.startDate.format('YYYY-MM-DD') + ' to ' + picker.endDate.format(
            'YYYY-MM-DD'));
        // Update the hidden input fields for start and end dates
        $('#filter_start_date').val(picker.startDate.format('YYYY-MM-DD'));
        $('#filter_end_date').val(picker.endDate.format('YYYY-MM-DD'));
        // Trigger a change event to refresh the table
        $('#filter_user, #filter_client, #filter_date_range, #filter_status, #filter_start_date, #filter_end_date')
            .change();
    });
    $('#filter_date_range').on('cancel.daterangepicker', function (ev, picker) {
        // Clear the input field and hidden fields
        $(this).val('');
        $('#filter_start_date').val('');
        $('#filter_end_date').val('');
        // Trigger a change event to refresh the table
        $(' #filter_user, #filter_client, #filter_status,#filter_date_range, #filter_start_date, #filter_end_date')
            .change();
    });
});
function leaves_report_query_params(p) {
    // Extract start_date and end_date from the date range picker input
    const dateRange = $('#filter_date_range').val().split(' to ');
    const startDate = dateRange[0] ? dateRange[0] : ''; // Handle if no start date is set
    const endDate = dateRange[1] ? dateRange[1] : ''; // Handle if no end date is set
    return {
        user_id: $('#filter_user').val(),
        status_id: $('#filter_status').val(),
        start_date: startDate, // Use extracted start date
        end_date: endDate, // Use extracted end date
        page: p.offset / p.limit + 1,
        limit: p.limit,
        sort: p.sort,
        order: p.order,
        offset: p.offset,
        search: p.search
    };
}
$('#filter_date_range,#filter_status,#filter_user').change(
    function () {
        $('#leaves_report_table').bootstrapTable('refresh');
    });

$(function () {
    $('#filter_status').select2();
    initSelect2Ajax(
        '#filter_user',
        '/master-panel/users/search-users',
        label_select_user,
        true,
        0,
    );
})
