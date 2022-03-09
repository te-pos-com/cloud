(function ($) {

    "use strict";
    window.onload = function () {
        //Yearly Income Vs Expense
        var link = _url + "/dashboard/json_month_wise_income_expense";
        $.ajax({
            url: link,
            success: function (data) {
                var json = JSON.parse(data);
                // Area Chart Example
                var ctx = document.getElementById("yearly_income_expense");
                var areaChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: json['Months'],
                        datasets: [{
                            label: 'Penjualan',
                            data: json['Income'],
                            fill: false,
                            backgroundColor:'#4b7bec',
                            borderColor: '#4b7bec',
                            tension: 0.1
                        },
                        {
                            label: 'Retur Penjualan',
                            data: json['IncomeReturn'],
                            fill: false,
                            backgroundColor:'#DC143C',
                            borderColor: '#DC143C',
                            tension: 0.1
                        },
                        {
                            label: 'Pembelian',
                            data: json['Expense'],
                            fill: false,
                            backgroundColor:'#3CEE09',
                            borderColor: '#3CEE09',
                            tension: 0.1
                        },
                        {
                            label: 'Retur Pembelian',
                            data: json['ExpenseReturn'],
                            fill: false,
                            backgroundColor:'#FDD700',
                            borderColor: '#FDD700',
                            tension: 0.1
                        },
                    ]
                    },
                    options: {
                        scales: {
                            yAxes: [{
                                ticks: {
                                    min: 0,
                                    callback: function (value, index, values) {
                                        return _currency + value;
                                    }
                                },
                                gridLines: {
                                    color: "rgba(0, 0, 0, .125)",
                                }
                            }],
                        },
                        tooltips: {
                            callbacks: {
                                label: function (tooltipItems, data) {
                                    return ' ' + _currency + tooltipItems.yLabel.toString();
                                }
                            }
                        }
                    }
                });
            }
        });

        //Income Vs Expense Donut Chart
        var link2 = _url + "/dashboard/json_income_vs_expense";
        $.ajax({
            url: link2,
            success: function (data2) {
                var json2 = JSON.parse(data2);

                var ctx = document.getElementById("dn_income_expense");
                var donutChart = new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: [$lang_income, $lang_expense],
                        datasets: [{
                            label: $lang_income_vs_expense,
                            data: [json2['Income'], json2['Expense']],
                            backgroundColor: [
                                '#2962ff',
                                '#ff1744'
                            ],
                            hoverOffset: 4
                        }]
                    }
                });
            }
        });
    }

})(jQuery);