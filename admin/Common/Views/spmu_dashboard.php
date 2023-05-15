<style>
    .red {
        background-color: rgb(255, 0, 0);
        color: black;
    }
    .orange {
        background-color: rgb(250, 192, 144);
        color: black;
    }
    .yellow {
        background-color: rgb(255, 255, 0);
        color: black;
    }
    .green {
        background-color: #77933C;
        color: black;
    }
    .table thead th {
        
        text-transform: none !important;
}
#chart-container {
  position: relative;
  height: 100vh;
  overflow: hidden;
}
    .form-check-input {
        display: none;
    }
</style>
<div class="content1">
    <div class="row mb-3">
        <div class="col-md-2">
            <select name="year" id="year" class="form-control">
                <?php foreach ($years as $year) { ?>
                    <option value="<?=$year['id']?>" <?php if($year['id']==$year_id){echo 'selected';} ?>><?=$year['name']?></option>
                <?php } ?>
            </select>
        </div>
    </div>
    <div class="row invisible" data-toggle="appear">
        <!-- Row #1 -->
        <div class="col-6 col-xl-3 abstract" id="ob-details">
            <a class="block block-rounded block-bordered block-link-shadow" href="javascript:void(0)">
                <div class="block-content block-content-full clearfix">
                    <div class="float-right mt-15 d-none d-sm-block">
                        <i class="fa fa-retweet fa-2x text-corporate"></i>
                    </div>
                    <div class="font-size-h3 font-w600 text-corporate"><i class="fa fa-rupee"></i> <span id="ob" data-toggle="countTo" data-speed="1000" data-to="<?=$abstract['ob']?>">0</span> Lakh</div>
                    <div class="font-size-sm font-w600 text-uppercase text-muted">Opening Balance</div>
                </div>
            </a>
        </div>
        <!-- Row #2 -->
        <div class="col-6 col-xl-3 abstract" id="fr-details">
            <a class="block block-rounded block-bordered block-link-shadow" href="javascript:void(0)">
                <div class="block-content block-content-full clearfix">
                    <div class="float-right mt-15 d-none d-sm-block">
                        <i class="si si-login fa-2x text-earth-light"></i>
                    </div>
                    <div class="font-size-h3 font-w600 text-earth"><i class="fa fa-rupee"></i> <span id="fr" data-toggle="countTo" data-speed="1000" data-to="<?=$abstract['fr']?>">0</span> Lakh</div>
                    <div class="font-size-sm font-w600 text-uppercase text-muted">Fund Receipt</div>
                </div>
            </a>
        </div>
        <div class="col-6 col-xl-3 abstract" id="ex-details">
            <a class="block block-rounded block-bordered block-link-shadow" href="javascript:void(0)">
                <div class="block-content block-content-full clearfix">
                    <div class="float-right mt-15 d-none d-sm-block">
                        <i class="si si-logout fa-2x text-elegance-light"></i>
                    </div>
                    <div class="font-size-h3 font-w600 text-elegance"><i class="fa fa-rupee"></i> <span id="ex" data-toggle="countTo" data-speed="1000" data-to="<?=$abstract['ex']?>">0</span> Lakh</div>
                    <div class="font-size-sm font-w600 text-uppercase text-muted">Expense</div>
                </div>
            </a>
        </div>
        <div class="col-6 col-xl-3 abstract" id="cb-details">
            <a class="block block-rounded block-bordered block-link-shadow" href="javascript:void(0)">
                <div class="block-content block-content-full clearfix">
                    <div class="float-right mt-15 d-none d-sm-block">
                        <i class="si si-briefcase fa-2x text-pulse"></i>
                    </div>
                    <div class="font-size-h3 font-w600 text-pulse"><i class="fa fa-rupee"></i> <span id="cb" data-toggle="countTo" data-speed="1000" data-to="<?=$abstract['cb']?>">0</span> Lakh</div>
                    <div class="font-size-sm font-w600 text-uppercase text-muted">Closing Balance <small>as on date</small></div>
                </div>
            </a>
        </div>

        <div class="col-12">
            <div class="block">
                <!-- Navigation -->
                <div class="block-content block-content-full border-b clearfix">
                    <div class="btn-group float-right" role="group" data-toggle="buttons">
                        <label class="btn btn-secondary form-check-label active">
                            <input class="form-check-input" type="radio" value="district"
                                   name="chart_type" id="district" autocomplete="off" checked> Districtwise
                        </label>
                        <!--<label class="btn btn-secondary form-check-label">
                            <input class="form-check-input" type="radio"
                                   name="chart_type" id="agency" value="agency"
                                   autocomplete="off"> Agency
                        </label>-->
                        <!--<label class="btn btn-secondary form-check-label">
                            <input class="form-check-input" type="radio" value="percentage"
                                   name="chart_type" id="percentage" autocomplete="off"> Percentage
                        </label>-->
                    </div>
                </div>
                <!-- END Navigation -->

                <!-- Project -->
                <div class="block-content block-content-full">
                    <div class="row py-20">
                        <div class="col-sm-12 invisible" data-toggle="appear">
                            <div id="container" style="height: 450px; margin: 0 auto"></div>
                        </div>
                    </div>
                </div>
                <!-- END Project -->
            </div>
        </div>

    </div>
</div>

<!-- Abstract Modal -->
<div class="modal fade" id="modal-abstract" tabindex="-1" role="dialog" aria-labelledby="modal-popout" aria-hidden="true">
    <div class="modal-dialog modal-dialog-popout modal-xl" role="document">
        <div class="modal-content">
            <div class="block block-themed block-transparent mb-0">
                <div class="block-header bg-primary-dark">
                    <h3 class="block-title" id="modal-title"></h3>
                    <div class="block-options">
                        <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                            <i class="si si-close"></i>
                        </button>
                    </div>
                </div>
                <div class="block-content" id="modal-content">
                    <div class="text-centered p-3">Loading...</div>
                </div>
            </div>
        </div>
    </div>
</div>


<?php js_start(); ?>

<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/highcharts-more.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/export-data.js"></script>

<script>

    var options = {
        chart: {
            type: 'column'
        },
        title: {
            text: 'Fund Receipt vs Expense'
        },
        xAxis: {
            categories: []
        },
        yAxis: {
            title: {
                text: 'Amount'
            },
            labels: {
                format: '{value}'
            }
        },
        tooltip: {
            pointFormat: '<span style="color:{point.color}">\u25CF</span> {series.name}: <b>{point.y}</b><br/>'
        },
        series: []
    };

    // create the chart
    var chart = Highcharts.chart('container', options);

    // add an event listener to detect when the user selects a new option
    $(function () {
        $('[name="chart_type"]').on('change',function () {
            chart_type = $(this).val();
            year = $('#year').val();
            // update the chart options
            if (chart_type === 'district') {
                $.ajax({
                    url:'<?=$chart_url?>',
                    data:{year:year,chart_type:chart_type},
                    type:'GET',
                    dataType:'JSON',
                    beforeSend:function () {

                    },
                    success:function (data) {
                        if(data.xaxis){
                            options.xAxis.categories = data.xaxis;
                        }
                        if(data.series){
                            options.series = data.series;
                        }
                        if(data.year){
                            options.title.text = 'Expense vs Fund Received: '+data.year;
                        }
                        options.yAxis.title.text = 'Amount (in Lakhs)';
                        // Redraw the chart with the updated configuration
                        chart = new Highcharts.Chart('container',options);
                        reloadAbstract(data);
                    },
                    error:function () {

                    }

                });
            }
            if (chart_type === 'agency') {
                $.ajax({

                });
            }
        });

        $('[name="chart_type"]').trigger('change');

        $('#year').on('change',function () {
            $('[name="chart_type"]').trigger('change');
        });

        $('.abstract').click(function () {
            year = $('#year').val();

            $.ajax({
                url:'<?=$abstract_url?>',
                data:{year:year},
                type:'GET',
                dataType:'JSON',
                beforeSend:function () {
                    $('#modal-content').html('Loading...');
                },
                success:function (data) {
                    $('#modal-content').html(data.html);
                },
                error:function () {

                },
                complete:function () {
                    $("#modal-abstract").modal({
                        backdrop: 'static',
                    });
                }
            });
        });
    });
    function reloadAbstract(data) {
        $('#ob').text(data.abstract.ob);
        $('#fr').text(data.abstract.fr);
        $('#ex').text(data.abstract.ex);
        $('#cb').text(data.abstract.cb);
        //coreAppearCountTo();
    }
</script>


<?php js_end(); ?>