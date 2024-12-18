/**
 * Dashboard Analytics
 */
'use strict';

(function () {
    let cardColor, headingColor, axisColor, shadeColor, borderColor;

    cardColor = config.colors.white;
    headingColor = config.colors.headingColor;
    axisColor = config.colors.axisColor;
    borderColor = config.colors.borderColor;

    // Projects Statistics Chart

    var options = {
        series: project_data,
        colors: bg_colors,
        labels: labels,
        chart: {
            type: 'pie',
            height: 400,  // Increased height
            width: 400,
        },
        responsive: [{
            breakpoint: 480,
            options: {
                chart: {
                    width: 300
                },
                legend: {
                    position: 'bottom'
                }
            }
        }]
    };

    var chart = new ApexCharts(document.querySelector("#projectStatisticsChart"), options);
    chart.render();

    // Tasks Statistics Chart
    var options = {
        labels: labels,
        series: task_data,
        colors: bg_colors,
        chart: {
            type: 'donut',
            height: 400,  // Increased height
            width: 400,   // Increased width
        },
        responsive: [{
            breakpoint: 480,
            options: {
                chart: {
                    width: 300  // Increased width for responsive
                },
                legend: {
                    position: 'bottom'
                }
            }
        }]
    };

    var chart = new ApexCharts(document.querySelector("#taskStatisticsChart"), options);
    chart.render();


    // Todos Statistics Chart
    var options = {
        labels: [done, pending],
        series: todo_data,
        colors: [config.colors.success, config.colors.danger],
        chart: {
            type: 'donut',
            height: 300,
            width: 300,
        },
        responsive: [{
            breakpoint: 480,
            options: {
                chart: {
                    width: 200
                },

            }
        }]
    };

    var chart = new ApexCharts(document.querySelector("#todoStatisticsChart"), options);
    chart.render();
})();

window.icons = {
    refresh: 'bx-refresh',
    toggleOn: 'bx-toggle-right',
    toggleOff: 'bx-toggle-left'
}

function loadingTemplate(message) {
    return '<i class="bx bx-loader-alt bx-spin bx-flip-vertical" ></i>'
}


function queryParamsUpcomingBirthdays(p) {
    return {
        "upcoming_days": $('#upcoming_days_bd').val(),
        "user_id": $('#birthday_user_filter').val(),
        page: p.offset / p.limit + 1,
        limit: p.limit,
        sort: p.sort,
        order: p.order,
        offset: p.offset,
        search: p.search
    };
}

$('#upcoming_days_birthday_filter').on('click', function (e) {
    e.preventDefault();
    $('#birthdays_table').bootstrapTable('refresh');


})

$('#birthday_user_filter').on('change', function (e) {
    e.preventDefault();
    $('#birthdays_table').bootstrapTable('refresh');


})


function queryParamsUpcomingWa(p) {
    return {
        "upcoming_days": $('#upcoming_days_wa').val(),
        "user_id": $('#wa_user_filter').val(),
        page: p.offset / p.limit + 1,
        limit: p.limit,
        sort: p.sort,
        order: p.order,
        offset: p.offset,
        search: p.search
    };
}

$('#upcoming_days_wa_filter').on('click', function (e) {
    e.preventDefault();
    $('#wa_table').bootstrapTable('refresh');


})

$('#wa_user_filter').on('change', function (e) {
    e.preventDefault();
    $('#wa_table').bootstrapTable('refresh');

})

function queryParamsMol(p) {
    return {
        "upcoming_days": $('#upcoming_days_mol').val(),
        "user_id": $('#mol_user_filter').val(),
        page: p.offset / p.limit + 1,
        limit: p.limit,
        sort: p.sort,
        order: p.order,
        offset: p.offset,
        search: p.search
    };
}

$('#upcoming_days_mol_filter').on('click', function (e) {
    e.preventDefault();
    $('#mol_table').bootstrapTable('refresh');

})

$('#mol_user_filter').on('change', function (e) {
    e.preventDefault();
    $('#mol_table').bootstrapTable('refresh');

})

document.addEventListener("DOMContentLoaded", function () {
    $.ajax({
        type: "GET",
        url: "/master-panel/reports/income-vs-expense-report-data",
        dataType: "JSON",
        success: function (response) {
            // Sample data from your AJAX response

            // Configure chart options
            var options = {
                series: [{
                    name: 'Income',
                    data: [response.total_income.replace(/[^0-9.]/g, '').trim()]
                }, {
                    name: 'Expenses',
                    data: [response.total_expenses.replace(/[^0-9.]/g, '').trim()]
                }],
                chart: {
                    type: 'bar',
                    height: 250
                },
                plotOptions: {
                    bar: {
                        horizontal: true,
                        endingShape: 'rounded'
                    }
                },
                dataLabels: {
                    enabled: false
                },
                colors: ['#005B41', '#ED2B2A'], // Green for income, red for expenses
                xaxis: {
                    categories: ['Total']
                },
                yaxis: {
                    title: {
                        text: 'Amount'
                    }
                },
                tooltip: {
                    y: {
                        formatter: function (val) {
                            return val;
                        }
                    }
                }
            };

            // Render chart

            var chart = new ApexCharts(document.querySelector("#income-expense-chart"), options);
            chart.render().catch(error => console.error("Chart Render Error:", error));
        },
        error: function (xhr, status, error) {
            console.error("AJAX Error: ", status, error);
        }
    });
});
