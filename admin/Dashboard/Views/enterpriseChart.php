<style>
    .highcharts-figure,
    .highcharts-data-table table {
        min-width: 310px;
        max-width: 800px;

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
<div class="row">
    <div class="col-3">
        <label class="form-label">Year</label>
        <?php echo form_dropdown('year_id', $years, [], ['class' => 'form-control mb-3', 'id' => 'enterprise_years']); ?>
    </div>

    <div class="col-3">
        <label class="form-label">District</label>
        <?php echo form_dropdown('district_id', $districts, [], ['class' => 'form-control mb-3', 'id' => 'enterprise_districts']); ?>
    </div>
</div>
<div class="row">
    <div class="col-7">
        <div id="container"></div>
    </div>

    <div class="col-5">
        <div class="block" id="enterprise_table">
        </div>
    </div>
</div>
<?php js_start(); ?>
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/export-data.js"></script>
<script src="https://code.highcharts.com/modules/accessibility.js"></script>

<script>
    // Data retrieved from https://en.wikipedia.org/wiki/Winter_Olympic_Games
    var chartOptions = {

        chart: {
            type: 'column'
        },

        title: {
            text: 'Enterprise Chart ',

            align: 'center'
        },

        xAxis: {
            categories: [],
        },
        tooltip: {
            crosshairs: true,
            shared: true
        },

        yAxis: {
            allowDecimals: false,
            min: 0,
            title: {
                text: 'Total Units '
            }
        },

        tooltip: {
            format: '<b>{key}</b><br/>{series.name}: {y}<br/>' +
                'Total: {point.stackTotal}'
        },

        plotOptions: {
            column: {
                stacking: 'normal'
            }
        },

        series: [{
            name: ' WSHG',
            data: [],
            stack: 'North America'
        }, {
            name: 'FPOS',
            data: [],
            stack: 'North America'
        }]
    };
    var chart = Highcharts.chart('container', chartOptions);

    $(function() {

        $('#enterprise_districts,#enterprise_years').on('change', function() {
            district_id = $('#enterprise_districts').val();
            year_id = $('#enterprise_years').val();
            $.ajax({
                url: '<?= $enterprise_url ?>',
                data: {
                    'district_id': district_id,
                    'year_id': year_id

                },
                type: 'GET',
                dataType: 'JSON',
                success: function(response) {
                    chart.xAxis[0].setCategories(response.unit_name);
                    chart.series[0].setData(response.wshg);
                    chart.series[1].setData(response.fpos);
                    chart.setTitle({
                        text: response.heading
                    });
                    $('#enterprise_table').html(response.table);
                }
            });
        });
        $('#enterprise_districts').change();

    });
</script>

<?php js_end(); ?>