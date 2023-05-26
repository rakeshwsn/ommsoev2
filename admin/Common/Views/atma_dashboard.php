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

</style>
<div class="content">
    <div class="row invisible" data-toggle="appear">
        <!-- Row #1 -->
        <div class="col-6 col-xl-2 abstract" id="ob-details">
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
        <!-- Row #1 -->
        <div class="col-6 col-xl-2" data-toggle="modal" data-target="#myModalone">
            <a class="block block-rounded block-bordered block-link-shadow" href="javascript:void(0)">
                <div class="block-content block-content-full clearfix">
                    <div class="float-right mt-15 d-none d-sm-block">
                        <i class="si si-login fa-2x text-earth-light"></i>
                    </div>
                    <div class="font-size-h3 font-w600 text-earth"><i class="fa fa-rupee"></i> <span data-toggle="countTo" data-speed="1000" data-to="<?=$abstract['fr']?>">0</span></div>
                    <div class="font-size-sm font-w600 text-uppercase text-muted">Fund Receipt</div>
                </div>
            </a>
        </div>
        <?php /*
        <div class="col-6 col-xl-3">
            <a class="block block-rounded block-bordered block-link-shadow" href="javascript:void(0)">
                <div class="block-content block-content-full clearfix">
                    <div class="float-right mt-15 d-none d-sm-block">
                        <i class="si si-login fa-2x text-earth-light"></i>
                    </div>
                    <div class="font-size-h3 font-w600 text-earth"><i class="fa fa-rupee"></i> <span data-toggle="countTo" data-speed="1000" data-to="<?=$frel?>">0</span></div>
                    <div class="font-size-sm font-w600 text-uppercase text-muted">Fund Release</div>
                </div>
            </a>
        </div>
        */ ?>
        <div class="col-6 col-xl-2" data-toggle="modal" data-target="#myModalone">
            <a class="block block-rounded block-bordered block-link-shadow" href="javascript:void(0)">
                <div class="block-content block-content-full clearfix">
                    <div class="float-right mt-15 d-none d-sm-block">
                        <i class="si si-logout fa-2x text-elegance-light"></i>
                    </div>
                    <div class="font-size-h3 font-w600 text-elegance"><i class="fa fa-rupee"></i> <span data-toggle="countTo" data-speed="1000" data-to="<?=$abstract['ex']?>">0</span></div>
                    <div class="font-size-sm font-w600 text-uppercase text-muted">Expense</div>
                </div>
            </a>
        </div>

        <div class="col-6 col-xl-2" data-toggle="modal" data-target="#myModalone">
            <a class="block block-rounded block-bordered block-link-shadow" href="javascript:void(0)">
                <div class="block-content block-content-full clearfix">
                    <div class="float-right mt-15 d-none d-sm-block">
                        <i class="si si-briefcase fa-2x text-pulse"></i>
                    </div>
                    <div class="font-size-h3 font-w600 text-pulse"><i class="fa fa-rupee"></i> <span  data-toggle="countTo" data-speed="1000" data-to="<?=$abstract['cb']?>">0</span></div>
                    <div class="font-size-sm font-w600 text-uppercase text-muted">Closing Balance</div>
                </div>
            </a>
        </div>
        <!-- END Row #1 -->

        <div class="col-6 col-xl-2">
            <a class="block block-rounded block-bordered block-link-shadow" href="<?= $pendingstatus_url ?>">
                <div class="block-content block-content-full clearfix">
                    <div class="float-right mt-15 d-none d-sm-block">
                        <i class="si si-notebook fa-2x text-corporate"></i>
                    </div>
                    <div class="font-size-h3 font-w600 text-corporate"><span data-toggle="countTo" data-speed="1000"
                                                                             data-to="<?= $pendingstatus ?>">0</span>
                    </div>
                    <div class="font-size-sm font-w600 text-uppercase text-muted">Pending Uploads</div>
                </div>
            </a>
        </div>
        <!-- END Row #1 -->
		
		 <div class="col-md-12 col-sm-4">
        <!-- Bars Chart -->
        <div class="block" style="margin-bottom: -300px;">
                <div class="block-header block-header-default">
                    <h3 class="block-title">FUND RECEIPT/EXPENSE</h3>
                    <div class="block-options">
                        <button type="button" class="btn-block-option" data-toggle="block-option" data-action="state_toggle" data-action-mode="demo">
                        
                        </button>
                    </div>
                </div>
                <div class="block-content block-content-full text-center">
                    <!-- Bars Chart Container -->
                    <div id="chart-container"></div>
                </div>
            </div>
            <!-- END Bars Chart -->

        </div>
    </div>

    <div class="row invisible" data-toggle="appear">
        <?=$upload_status?>
    </div>

</div>


<?php js_start(); ?>
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/highcharts-more.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/export-data.js"></script>

<script type="text/javascript">
    $(function () {

        <?php if($fr_check): ?>
        $('#modal-fr').modal('show');

        $('.btn-fr').click(function () {
            choice = $(this).data('value');
            $.ajax({
                data:{choice:choice},
                type:'GET',
                dataType:'JSON',
                success:function (json) {
                    $('#modal-fr').modal('hide');
                    if(choice=='yes') {
                        location.href = "<?=$fr_url?>";
                    }
                },
                error:function () {
                    alert('Request not succussful');
                    $('#modal-fr').modal('hide');
                }
            });
        });
        <?php endif; ?>

        <?php if($or_check): ?>

        $('#modal-or').modal('show');

        $('.btn-or').click(function () {
            choice = $(this).data('value');
            $.ajax({
                data:{choice:choice,check_type:'or'},
                type:'GET',
                dataType:'JSON',
                success:function (json) {
                    $('#modal-or').modal('hide');
                    if(choice=='yes') {
                        location.href = "<?=$or_url?>";
                    }
                },
                error:function () {
                    alert('Request not succussful');
                    $('#modal-or').modal('hide');
                }
            });
        });
        <?php endif; ?>

    });

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
                            options.title.text = 'Fund Received vs Expenditure: '+data.year;
                        }
                        options.yAxis.title.text = 'Amount (in Lakhs)';
                        // Redraw the chart with the updated configuration
                        chart = new Highcharts.Chart('container',options);
                        reloadAbstract(data);
                        createPieChart(data.piechart);
                    },
                    error:function () {

                    }

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

    function createPieChart(data) {
        // Prepare the data for the chart
        var chartData = [];
        for (var i = 0; i < data.length; i++) {
            chartData.push({
                name: data[i].name,
                y: data[i].value
            });
        }

        // Create the pie chart
        Highcharts.chart('piechart', {
            chart: {
                type: 'pie'
            },
            title: {
                text: 'Districtwise Achievement'
            },
            series: [{
                name: 'Percentage',
                data: chartData
            }]
        });
    }
</script>
<?php js_end(); ?>


                