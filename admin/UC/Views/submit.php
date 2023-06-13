
<div class="content">
    <?php if($dmf): ?>
    <div class="row invisible" data-toggle="appear">
        <!-- Row #1 -->
        <div class="col-12 text-right mb-3">
            <button id="add-new" class="btn btn-success">Add Allocation</button>
        </div>
        <!-- END Row #1 -->
    </div>
    <?php endif; ?>

    <div class="row invisible" data-toggle="appear">
        <!-- Row #1 -->
        <div class="col-6 col-xl-4">
            <a class="block block-rounded block-bordered block-link-shadow" href="javascript:void(0)">
                <div class="block-content block-content-full clearfix">
                    <div class="float-right mt-15 d-none d-sm-block">
                        <i class="si si-bag fa-2x text-primary-light"></i>
                    </div>
                    <div class="font-size-h3 font-w600 text-primary"> <span data-toggle="countTo" data-speed="1000" data-to="<?=$total_allotment?>">0</span></div>
                    <div class="font-size-sm font-w600 text-uppercase text-muted">Allotment From SPMU</div>
                </div>
            </a>
        </div>
        <div class="col-6 col-xl-4">
            <a class="block block-rounded block-bordered block-link-shadow" href="javascript:void(0)">
                <div class="block-content block-content-full clearfix">
                    <div class="float-right mt-15 d-none d-sm-block">
                        <i class="si si-wallet fa-2x text-earth-light"></i>
                    </div>
                    <div class="font-size-h3 font-w600 text-earth"><span data-toggle="countTo" data-speed="1000" data-to="<?=$total_uc_submitted?>">0</span></div>
                    <div class="font-size-sm font-w600 text-uppercase text-muted">UC Submitted</div>
                </div>
            </a>
        </div>
        <div class="col-6 col-xl-4">
            <a class="block block-rounded block-bordered block-link-shadow" href="javascript:void(0)">
                <div class="block-content block-content-full clearfix">
                    <div class="float-right mt-15 d-none d-sm-block">
                        <i class="si si-envelope-open fa-2x text-elegance-light"></i>
                    </div>
                    <div class="font-size-h3 font-w600 text-elegance" data-toggle="countTo" data-speed="1000" data-to="<?=$total_uc_balance?>">0</div>
                    <div class="font-size-sm font-w600 text-uppercase text-muted">Balance UC to be Submitted</div>
                </div>
            </a>
        </div>
        <!-- END Row #1 -->
    </div>

    <div class="row invisible" data-toggle="appear">
        <!-- Row #2 -->
        <div class="col-md-12">
            <div class="block block-rounded block-bordered">
                <div class="block-header block-header-default border-b">
                    <h3 class="block-title">
                        UC Details
                    </h3>
                </div>
                <div class="block-content block-content-full">
                    <table class="table table-striped" id="block-components">
                        <thead>
                        <tr>
                            <th>Year</th>
                            <th>UC Date</th>
                            <th>Letter No</th>
                            <th>Allotment</th>
                            <th>UC Submitted</th>
                            <th>UC Balance</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if($allotments): ?>
                        <?php foreach ($allotments as $allotment): ?>
                            <tr>
                                <td><?=$allotment['year']?></td>
                                <td><?=$allotment['uc_date']?></td>
                                <td><?=$allotment['letter_no']?></td>
                                <td><?=in_rupees($allotment['allotment'])?></td>
                                <td><?=in_rupees($allotment['uc_submitted'])?></td>
                                <td><?=in_rupees($allotment['uc_balance'])?></td>
                                <td><?=$allotment['action']?></td>
                            </tr>
                        <?php endforeach; ?>
                            <tr>
                                <td colspan="3">Total</td>
                                <td><?=in_rupees($total_allotment)?></td>
                                <td><?=in_rupees($total_uc_submitted)?></td>
                                <td><?=in_rupees($total_uc_balance)?></td>
                            </tr>
                        <?php else: ?>
                            <tr>
                                <td colspan="7">No Data Found</td>
                            </tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add new Modal -->
<div class="modal fade" id="modal-add-new" tabindex="-1" role="dialog" aria-labelledby="modal-popout" aria-hidden="true">
    <div class="modal-dialog modal-dialog-popout" role="document">
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
                    Hello
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-alt-secondary" data-dismiss="modal">Close</button>
                <button type="button" id="btn-add" class="btn btn-alt-success">
                    <i class="fa fa-check"></i> Save
                </button>
            </div>
        </div>
    </div>
</div>


<?php if($dmf): ?>
    <?php js_start(); ?>
    <script>
        $(function () {
            //add new
            $('#add-new').click(function (e) {
                e.preventDefault();
                fai = $('#fund_agency_id').val() || '';
                $.ajax({
                    headers: {'X-Requested-With': 'XMLHttpRequest'},
                    data: {year:$('#year_id').val(),fund_agency_id:fai},
                    url :"<?=$add_url?>", // json datasource
                    type: "get",  // method  , by default get
                    dataType:'json',
                    beforeSend:function () {
                        //                    $('#main-container').loading();
                        $("#main-container").LoadingOverlay('show');
                        $('#res-message').text('');
                    },
                    success:function (json) {
                        if(json.status==false){
                            $('#res-message').text(json.message);
                        } else {
                            $('#modal-title').html(json.title);
                            $('#modal-content').html(json.html);
                            $("#modal-add-new").modal({
                                backdrop: 'static',
                            });
                        }
                    },
                    error: function(){  // error handling
                        $("#main-container").LoadingOverlay("hide");
                    },
                    complete:function () {
                        //                    $('#main-container').loading('stop');
                        $("#main-container").LoadingOverlay("hide");
                    }
                });
            });

            $(document).on('click','#btn-add',function () {
                formdata = $(this).closest('.modal-content').find('form').serialize();
                year = $('#year').val()||'';
                $.ajax({
                    headers: {'X-Requested-With': 'XMLHttpRequest'},
                    url:'<?=$add_url?>',
                    data:formdata,
                    type:'POST',
                    dataType:'JSON',
                    before:function () {
                        $("#main-container").LoadingOverlay('show');
                    },
                    success:function (json) {
                        location.reload();
                    },
                    error:function () {
                        $("#main-container").LoadingOverlay("hide");
                    },
                    complete:function () {
                        $("#main-container").LoadingOverlay("hide");
                    }
                })
            });

            $(document).on('focus',".js-datepicker", function() {
                $(this).datepicker({
                    autoclose:true,
                    orientation: 'bottom',
                    todayHighlight:true
                });
            });
        });
    </script>
    <?php js_end(); ?>
<?php endif; ?>