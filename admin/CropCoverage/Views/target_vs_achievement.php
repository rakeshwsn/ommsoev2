<style>
    #container {
        height: 500px;
    }

    #container1 {
        height: 500px;
    }

    .highcharts-figure,
    .highcharts-data-table table {
        min-width: 320px;
        max-width: 100%;
        margin: 1em auto;
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

    .form-check-input {
        display: none;
    }
</style>
<div class="content1">
    <div class="row mb-3">
        <div class="col-md-2">
            <select name="year" id="year" class="form-control" disabled>
                <?php foreach ($years as $year) { ?>
                    <option value="<?= $year['id'] ?>" <?php if ($year['id'] == $year['id']) {
                          echo 'selected';
                      } ?>><?= $year['name'] ?>
                    </option>
                <?php } ?>
            </select>
        </div>
        <div class="col-md-2">
            <select name="season" id="season" class="form-control">
                <option value="">Select Season</option>
                <?php foreach ($seasons as $value => $season) { ?>
                    <option value="<?= $value ?>" <?php if ($value == $current_season) {
                          echo 'selected';
                      } ?>>
                        <?= $season ?>
                    </option>
                <?php } ?>
            </select>
        </div>

    </div>
</div>
<?php if ($block_id) { ?>
    <figure class="highcharts-figure">
        <div id="milletContainer"></div>
        <p class="highcharts-description">

        </p>
    </figure>
<?php } elseif ($district_id) { ?>
    <figure class="highcharts-figure">
        <div id="container"></div>

    </figure>
<?php } else {
    echo "Data is in dashboard of all district";
} ?>



<script>
    var milletChartOptions = {
        chart: {
            type: 'column'
        },
        title: {
            text: 'Millet Wise Target vs Achievement',
            align: 'left'
        },
        subtitle: {
            text: '',
            align: 'left'
        },
        xAxis: {
            categories: [],
            crosshair: true,
            accessibility: {
                description: 'Districts'
            }
        },
        yAxis: {
            min: 0,
            title: {
                text: 'Millet Wise Target vs Achievement'
            }
        },
        tooltip: {
            valueSuffix: ''
        },
        plotOptions: {
            column: {
                pointPadding: 0.2,
                borderWidth: 0
            }
        },
        series: [
            {
                name: 'Area Target(In Hect)',
                data: []
            },
            {
                name: 'Area Achievement(In Hect)',
                data: []
            }
        ]
    };
    var bchart = Highcharts.chart('milletContainer', milletChartOptions);
    $(document).ready(function () {
        $('[name="year"],[name="season"]').on('change', function () {
            year_id = $('#year').val();
            season = $('#season').val();
            $.ajax({
                url: '<?= $milletchart_url ?>',
                data: { year_id: year_id, season: season },
                type: 'GET',
                dataType: 'JSON',
                success: function (response) {
                    console.log(response);
                    bchart.xAxis[0].setCategories(response.xaxis);
                    bchart.series[0].setData(response.series_target);
                    bchart.series[1].setData(response.series_achievement);
                }

            });
        });
        $('[name="year"]').trigger('change');
    });
</script>
<script>
    var chartOptions = {
        chart: {
            type: 'column'
        },
        title: {
            text: 'Block Wise Millet Target vs Achievement'
        },

        xAxis: {
            categories: [],
            crosshair: true
        },

        yAxis: {
            title: {
                text: 'Millet Target Vs Achievement(Area in Hectare)'
            },
            labels: {
                format: '{value}'
            }
        },
        legend: {
            enabled: true
        },
        series: [
            {
                name: 'Area Target(In Hect)',
                data: [], // Replace this value with the millet target for the one district
                color: 'rgba(165,170,217,1)',
                borderRadius: 3,
                pointPadding: 0.2,
                borderWidth: 0
            },
            {
                name: 'Area Achievement(In Hect)',
                data: [], // Replace this value with the millet achievement for the one district
                color: 'rgba(126,86,134,.9)',
                borderRadius: 3,
                pointPadding: 0.2,
                borderWidth: 0
            }
        ]
    };
    var achart = Highcharts.chart('container', chartOptions);
    $(document).ready(function () {
        $('[name="year"],[name="season"]').on('change', function () {
            year_id = $('#year').val();
            season = $('#season').val();

            $.ajax({
                url: '<?= $distchart_url ?>',
                data: { year_id: year_id, season: season },
                type: 'GET',
                dataType: 'JSON',
                success: function (response) {
                    achart.xAxis[0].setCategories(response.xaxis);
                    achart.series[0].setData(response.series_target);
                    achart.series[1].setData(response.series_achievement);
                }

            });
        });
        $('[name="year"]').trigger('change');
    });
</script>