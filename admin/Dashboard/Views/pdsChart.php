<style>
    .highcharts-figure,
    .highcharts-data-table table {
        min-width: 310px;
        max-width: 800px;
        margin: 1em auto;
    }

    #container {
        height: 400px;

    }


    .highcharts-data-table table {
        font-family: Verdana, sans-serif;
        border-collapse: collapse;
        border: 1px solid #ebebeb;
        margin: 10px auto;
        text-align: center;
        width: 100%;
        height: 250px;
        max-width: 1000px;
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

<figure class="highcharts-figure">
    <div id="container"></div>

</figure>

<?php js_start(); ?>
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/export-data.js"></script>
<script src="https://code.highcharts.com/modules/accessibility.js"></script>
<script>
    var pdschartOptions = {
        pdschart: {
            zoomType: 'xy'
        },
        title: {
            text: 'PDS',
            align: 'center'
        },

        xAxis: [{
            categories: [],
            crosshair: true
        }],
        yAxis: [{ // Primary yAxis
            labels: {
                format: '{value}qtl',
                style: {
                    color: Highcharts.getOptions().colors[1]
                }
            },
            title: {
                text: 'Quantity',
                style: {
                    color: Highcharts.getOptions().colors[1]
                }
            }
        }, { // Secondary yAxis
            title: {
                text: 'Benificiary',
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
            align: 'center',
            verticalAlign: 'bottom',
            backgroundColor: Highcharts.defaultOptions.legend.backgroundColor || // theme
                'rgba(255,255,255,0.25)'
        },
        series: [{
            name: 'Quantiry',
            type: 'column',
            yAxis: 1,
            data: [],
            tooltip: {
                valueSuffix: ' Qtl'
            }

        }, {
            name: 'Benificiary',
            type: 'spline',
            data: [],
            color: '#1e820c',
            tooltip: {
                valueSuffix: ''
            }
        }]
    };

    var pdschart = Highcharts.chart('container', pdschartOptions);
    $.ajax({
        url: '<?= $pds_url ?>',
        data: {

        },
        type: 'GET',
        dataType: 'JSON',
        success: function(response) {
            pdschart.xAxis[0].setCategories(response.pdsyear);
            pdschart.series[0].setData(response.pdsquantity);
            pdschart.series[1].setData(response.card_holders_benifited);

        }

    });
</script>

<?php js_end(); ?>