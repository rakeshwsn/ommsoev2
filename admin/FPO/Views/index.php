<section class="content">
    <div class="row invisible" data-toggle="appear">
        <!-- Row #1 -->
        <div class="col-6 col-xl-3">
            <a class="block block-link-rotate text-right" href="javascript:void(0)">
                <div class="block-content block-content-full clearfix">

                    <div class="font-size-h3 font-w600 text-primary-darker" data-toggle="countTo" data-speed="1000" data-to="<?=$fpo_status['apply'];?>"><?=$fpo_status['apply'];?></div>
                    <div class="font-size-sm font-w600 text-uppercase text-primary-dark">No of FPO Apply</div>
                </div>
            </a>
        </div>
        <div class="col-6 col-xl-3">
            <a class="block block-link-rotate text-right" href="javascript:void(0)">
                <div class="block-content block-content-full clearfix">

                    <div class="font-size-h3 font-w600 text-primary-darker" data-toggle="countTo" data-speed="1000" data-to="<?=$fpo_status['registered'];?>"><?=$fpo_status['registered'];?></div>
                    <div class="font-size-sm font-w600 text-uppercase text-primary-dark">No of FPO Registered</div>
                </div>
            </a>
        </div>
        <div class="col-6 col-xl-3">
            <a class="block block-link-rotate text-right" href="javascript:void(0)">
                <div class="block-content block-content-full clearfix">

                    <div class="font-size-h3 font-w600 text-primary-darker" data-toggle="countTo" data-speed="1000" data-to="<?=$fpo_status['company'];?>"><?=$fpo_status['company'];?></div>
                    <div class="font-size-sm font-w600 text-uppercase text-primary-dark">No of FPO(Company Act)</div>
                </div>
            </a>
        </div>
        <div class="col-6 col-xl-3">
            <a class="block block-link-rotate text-right" href="javascript:void(0)">
                <div class="block-content block-content-full clearfix">

                    <div class="font-size-h3 font-w600 text-primary-darker" data-toggle="countTo" data-speed="1000" data-to="<?=$fpo_status['socity'];?>"><?=$fpo_status['socity'];?></div>
                    <div class="font-size-sm font-w600 text-uppercase text-primary-dark">No of FPO(Society Act)</div>
                </div>
            </a>
        </div>
        <!-- END Row #1 -->
    </div>

    <div class="row invisible" data-toggle="appear">
        <!-- Row #3 -->
        <?php if(!$district_id){?>
        <div class="col-md-12">
            <div class="block block-rounded block-bordered">
                <div class="block-header block-header-default border-b">
                    <h3 class="block-title">Districtwise FPO Progress Status</h3>
                    <div class="block-options">
                        <button type="button" class="btn-block-option" data-toggle="block-option" data-action="state_toggle" data-action-mode="demo">
                            <i class="si si-refresh"></i>
                        </button>

                    </div>
                </div>
                <div class="block-content">

                    <table class="table table-borderless table-striped">
                        <thead>
                        <tr>
                            <th>District</th>
                            <th>Total Block</th>
                            <th>Total Registered</th>

                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach($fpo_districts as $district){?>
                        <tr>
                            <td>
                                <?=$district['name']?>
                            </td>
                            <td>
                                <?=$district['total_block']?>
                            </td>
                            <td>
                                <?=$district['total_register']?>
                            </td>

                        </tr>
                        <?}?>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?}?>
        <div class="col-md-12">
            <div class="block block-rounded block-bordered">
                <div class="block-header block-header-default border-b">
                    <h3 class="block-title">Blockwise FPO Progress Status</h3>
                    <div class="block-options">
                        <button type="button" class="btn-block-option" data-toggle="block-option" data-action="state_toggle" data-action-mode="demo">
                            <i class="si si-refresh"></i>
                        </button>

                    </div>
                </div>
                <div class="block-content">
                    <div class="block block-rounded row g-0 overflow-hidden" style="margin: -20px">
                        <ul class="nav nav-tabs nav-tabs-block flex-md-column col-md-2 rounded-0" data-toggle="tabs" role="tablist" style="padding-right:0px">
                            <?php foreach ($districts as $key=>$district){?>
                                <li class="nav-item d-md-flex flex-md-column">
                                    <a class="nav-link <?=$key==0?'active':''?>" href="#district-<?=$district->id?>"><?=$district->name;?></a>
                                </li>
                            <?}?>
                        </ul>
                        <div class="tab-content col-md-10">
                            <?php foreach ($districts as $key=>$district){?>

                                <div class="block-content tab-pane <?=$key==0?'active':''?>" id="district-<?=$district->id?>" role="tabpanel" >
                                <table class="table table-bordered table-vcenter">
                                    <thead>
                                    <tr>
                                        <th>Block Name</th>
                                        <th>FPO Name</th>
                                        <th>Legal Form of FPO</th>
                                        <th>Fpo Registered</th>
                                        <th>Current Status</th>
                                       <th>Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach ($district->fpos as $fpo) {?>
                                    <tr>
                                        <td><?=$fpo['block_name']?></td>
                                        <td><?=$fpo['fpo_name']?></td>
                                        <td><?=$fpo['legal_form']?></td>
                                        <td><?=$fpo['registered']?"Yes":"No"?></td>
                                        <td><?php
                                            $fstatus="";
                                            if($fpo['registered']){
                                                if($fpo['other_fpo']){
                                                    $fstatus="Nearest Block";
                                                }else{
                                                    $fstatus="Progress...";
                                                }
                                            }else{
                                                $fstatus=$fpo['register_status'];
                                            }
                                            echo $fstatus;
                                            ?>
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group">
                                                <?php if($fpo['fpo_id']){?>
                                                    <a type="button" data-district="<?=$district->id?>" data-block="<?=$fpo['block_id']?>" href="<?=admin_url("fpo/edit/{$fpo['fpo_id']}")?>" class="btn fpo_form btn-sm btn-secondary js-tooltip-enabled" data-toggle="tooltip" title="" data-original-title="Edit">
                                                        <i class="fa fa-pencil"></i>
                                                    </a>
                                                    <a type="button"  href="<?=admin_url("fpo/details/{$fpo['fpo_id']}")?>" class="btn btn-sm btn-secondary js-tooltip-enabled" data-toggle="tooltip" title="" data-original-title="Details">
                                                        <i class="fa fa-list-alt"></i>
                                                    </a>
                                                <?}else {?>
                                                    <a type="button" data-district="<?=$district->id?>" data-block="<?=$fpo['block_id']?>" href="<?=admin_url("fpo/add")?>" class="btn btn-sm btn-primary fpo_form js-tooltip-enabled" data-toggle="tooltip" title="" data-original-title="Add">
                                                        <i class="fa fa-plus"></i>
                                                    </a>
                                                <?}?>
                                            </div>
                                        </td>
                                    </tr>
                                    <?}?>
                                    </tbody>
                                </table>
                            </div>
                            <?}?>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>


</section>

<div class="modal fade" id="modal-fpo" tabindex="-1" role="dialog" aria-labelledby="modal-popout" aria-hidden="true">
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
                <button type="button" id="btn-save" class="btn btn-alt-success">
                    <i class="fa fa-check"></i> Save
                </button>
            </div>
        </div>
    </div>
</div>
<!-- content -->
<?php js_start(); ?>
<script>
    $(function () {
        //add new
        $('.fpo_form').click(function (e) {
            e.preventDefault();
            url = $(this).attr('href');
            district_id=$(this).data('district');
            block_id=$(this).data('block');
            //alert(block_id);
            $.ajax({
                headers: {'X-Requested-With': 'XMLHttpRequest'},
                url :url, // json datasource
                type: "get",  // method  , by default get
                data:{district_id:district_id,block_id:block_id},
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
                        $('#modal-content').contents().find("#registered").trigger("change");
                        $('#modal-content').contents().find("#other_fpo").trigger("change");

                        //console.log($('#modal-content').contents().find("#registered"));
                        $("#modal-fpo").modal({
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

        $(document).on('click','#btn-save',function () {
            formdata = $(this).closest('.modal-content').find('form').serialize();
            action=$(this).closest('.modal-content').find('form').attr('action');
            $.ajax({
                headers: {'X-Requested-With': 'XMLHttpRequest'},
                url:action,
                data:formdata,
                type:'POST',
                dataType:'JSON',
                beforeSend:function () {
//                $('#main-container').loading();

                    $("#modal-fpo").LoadingOverlay('show');
                },
                success:function (json) {
                    location.reload();
                },
                error:function () {
//                $('#main-container').loading('stop');
                    $("#modal-fpo").LoadingOverlay("hide");
                },
                complete:function () {
                    $("#modal-fpo").LoadingOverlay("hide");
//                $('#main-container').loading('stop');
                }
            })
        });



    });
    $(function(){
        $(document).on('change','#registered',function () {
            if($(this).val()==0){
                $("#no-div").removeClass("d-none");
            }else if($(this).val()==1){
                $("#no-div").addClass("d-none");
                $("#yes-div").removeClass("d-none");
            }else{
                $("#no-div").addClass("d-none");
                $("#yes-div").addClass("d-none");
            }
        });
        $(document).on('change','#other_fpo',function () {
            if($(this).val()==0){
                $("#oblock").addClass("d-none");
            }else{
                $("#oblock").removeClass("d-none");
            }
        });
    })
</script>
<?php js_end(); ?>