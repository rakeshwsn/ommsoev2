<style>
    .highcharts-figure,
    .highcharts-data-table table {
        min-width: 310px;
        max-width: 1000px;
        margin: 1em auto;
    }

    #container {
        height: 500px;

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
        font-size: 10em;
        color: #555;
    }

    .highcharts-data-table th {
        font-weight: 600;
        padding: 7em;
    }

    .highcharts-data-table td,
    .highcharts-data-table th,
    .highcharts-data-table caption {
        padding: 5em;
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
        <label class="form-label">District</label>
        <?php
        echo form_dropdown('district_id', $districts, [], ['class' => 'form-control mb-3', 'id' => 'districts_proc']); ?>
    </div>
</div>
<div id="container">


    <figure class="highcharts-figure">
    </figure>
</div>


<?php js_start(); ?>
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/export-data.js"></script>
<script src="https://code.highcharts.com/modules/accessibility.js"></script>
<script>
    var chartOptions = {
        pchart: {
            type: 'line'
        },
        title: {
            text: 'PROCUREMENT'

        },
        xAxis: {
            categories: [],
        },
        tooltip: {
            crosshairs: true,
            shared: true
        },


        plotOptions: {
            line: {
                dataLabels: {
                    enabled: true
                },
                enableMouseTracking: true
            }
        },
        series: [{
            name: 'Farmers',
            marker: {
                symbol: 'square'
            },
            data: [],
        }, {
            name: 'Quantity',
            marker: {
                symbol: 'diamond'
            },
            data: [],
        }, ]
    };
    var pchart = Highcharts.chart('container', chartOptions);

    $(function() {
        $('#districts_proc').on('change', function() {
            district_id = $(this).val();
            $.ajax({
                url: '<?= $procurement_url ?>',
                data: {
                    'district_id': district_id,
                    chart_type: 'pchart'
                },
                type: 'GET',
                dataType: 'JSON',
                success: function(response) {
                    pchart.xAxis[0].setCategories(response.pyears);
                    pchart.series[0].setData(response.pfarmers);
                    pchart.series[1].setData(response.pquantity);
                    pchart.setTitle({
                        text: response.heading
                    });

                }

            });
        });
        $('#districts_proc').change();

    });
</script>

<?php js_end(); ?>