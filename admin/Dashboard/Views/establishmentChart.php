<style>
    .highcharts-figure,
    .highcharts-data-table table {
        min-width: 310px;
        max-width: 800px;
        margin: 1em auto;
    }

    #container {
        height: 600px;
    }

    .highcharts-data-table table {
        font-family: Verdana, sans-serif;
        border-collapse: collapse;
        border: 1px solid #ebebeb;
        margin: 10px auto;
        text-align: center;
        width: 100%;
        max-width: 500px;
    }

    .highcharts-data-table caption {
        padding: 1em 0;
        font-size: 1.2em;
        color: #555;
    }

    .highcharts-data-table th {
        font-weight: 600;
        padding: 0.5em;
    }

    .highcharts-data-table td,
    .highcharts-data-table th,
    .highcharts-data-table caption {
        padding: 0.5em;
    }

    .highcharts-data-table thead tr,
    .highcharts-data-table tr:nth-child(even) {
        background: #f8f8f8;
    }

    .highcharts-data-table tr:hover {
        background: #f1f7ff;
    }
</style>

<div id="container">


    <figure class="highcharts-figure">
    </figure>
</div>

<?php js_start() ?>
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/export-data.js"></script>
<script src="https://code.highcharts.com/modules/accessibility.js"></script>
<script>
    var chartOptions = {
        chart: {
            zoomType: 'xy'
        },
        title: {
            text: 'Establishment',
            align: 'center'
        },

        xAxis: [{
            categories: [],
            crosshair: true
        }],
        yAxis: [{ // Primary yAxis
            labels: {
                format: '',
                style: {
                    color: Highcharts.getOptions().colors[1]
                }
            },
            title: {
                text: 'Blocks',
                style: {
                    color: Highcharts.getOptions().colors[1]
                }
            }
        }, { // Secondary yAxis
            title: {
                text: 'CHC/CMSC',
                style: {
                    color: Highcharts.getOptions().colors[0]
                }
            },

            opposite: true
        }],
        tooltip: {
            shared: true
        },
        legend: {
            align: 'bottom',
            // x: 80,
            verticalAlign: 'center',
            // y: 60,
            // floating: true,
            backgroundColor: Highcharts.defaultOptions.legend.backgroundColor || // theme
                'rgba(255,255,255,0.25)'
        },
        series: [{
                name: 'Total Blocks',
                type: 'column',
                yAxis: 1,
                data: [],
                tooltip: {
                    valueSuffix: ' '
                }

            }, {
                name: 'Total CHC',
                type: 'spline',
                data: [],
                tooltip: {
                    valueSuffix: ''
                }
            },
            {
                name: 'Total CMSC',
                type: 'spline',
                data: [],
                tooltip: {
                    valueSuffix: ''
                }
            }
        ]
    };
    var chart = Highcharts.chart('container', chartOptions);
    $.ajax({
        url: '<?= $establish_url ?>',
        data: {

        },
        type: 'GET',
        dataType: 'JSON',
        success: function(response) {
            chart.xAxis[0].setCategories(response.estdistrict);
            chart.series[0].setData(response.chc);
            chart.series[1].setData(response.cmsc);
            chart.series[2].setData(response.blocks);
        }


    });
</script>
<?php js_end() ?>