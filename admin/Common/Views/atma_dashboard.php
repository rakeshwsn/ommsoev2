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
        <div class="col-6 col-xl-3" data-toggle="modal" data-target="#myModalone">
            <a class="block block-rounded block-bordered block-link-shadow" href="javascript:void(0)">
                <div class="block-content block-content-full clearfix">
                    <div class="float-right mt-15 d-none d-sm-block">
                        <i class="si si-login fa-2x text-earth-light"></i>
                    </div>
                    <div class="font-size-h3 font-w600 text-earth"><i class="fa fa-rupee"></i> <span data-toggle="countTo" data-speed="1000" data-to="<?=$fr?>">0</span></div>
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
        <div class="col-6 col-xl-3" data-toggle="modal" data-target="#myModalone">
            <a class="block block-rounded block-bordered block-link-shadow" href="javascript:void(0)">
                <div class="block-content block-content-full clearfix">
                    <div class="float-right mt-15 d-none d-sm-block">
                        <i class="si si-logout fa-2x text-elegance-light"></i>
                    </div>
                    <div class="font-size-h3 font-w600 text-elegance"><i class="fa fa-rupee"></i> <span data-toggle="countTo" data-speed="1000" data-to="<?=$ex?>">0</span></div>
                    <div class="font-size-sm font-w600 text-uppercase text-muted">Expense</div>
                </div>
            </a>
        </div>

        <div class="col-6 col-xl-3" data-toggle="modal" data-target="#myModalone">
            <a class="block block-rounded block-bordered block-link-shadow" href="javascript:void(0)">
                <div class="block-content block-content-full clearfix">
                    <div class="float-right mt-15 d-none d-sm-block">
                        <i class="si si-briefcase fa-2x text-pulse"></i>
                    </div>
                    <div class="font-size-h3 font-w600 text-pulse"><i class="fa fa-rupee"></i> <span  data-toggle="countTo" data-speed="1000" data-to="<?=$cb?>">0</span></div>
                    <div class="font-size-sm font-w600 text-uppercase text-muted">Closing Balance</div>
                </div>
            </a>
        </div>
        <!-- END Row #1 -->

        <div class="col-6 col-xl-3">
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
<script src="https://fastly.jsdelivr.net/npm/echarts@5.4.1/dist/echarts.min.js"></script>

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
</script>
<?php js_end(); ?>


                