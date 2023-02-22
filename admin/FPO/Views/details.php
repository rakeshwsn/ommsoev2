<div class="bg-image bg-image-bottom" id="banner" style="position:relative;background-image: url('<?=$fpo->banner?>');">
    <div class="bg-primary-dark-op py-30">
        <div class="content content-full text-center">
            <!-- Avatar -->
            <div class="mb-15">
                <a class="img-link" href="javascript:void(0);">
                    <img class="img-avatar img-avatar96 img-avatar-thumb" id="image" src="<?=$fpo->image?>" alt="">
                    <input type="file" name="image" class="image" id="fpo_image" style="display:none" />
                </a>
                <a class="fpo-logo-edit btn btn-rounded btn-sm btn-alt-secondary mb-5 px-10 cropimage" data-fpo="<?=$fpo->id?>" data-target="image" data-width="100" data-height="100" href="javascript:void(0);">
                    <i class="fa fa-pencil"></i>
                </a>
            </div>
            <!-- END Avatar -->

            <!-- Personal -->
            <h1 class="h3 text-white font-w700 mb-10">
                <?=$fpo->name;?>
            </h1>
            <h2 class="h5 text-white-op">
                <?=$fpo->district;?> , <?=$fpo->block;?> <br>
                Act: <?=$fpo->act;?>
            </h2>
            <!-- END Personal -->

            <!-- Actions -->
            <button type="button" class="btn btn-rounded btn-hero btn-sm btn-alt-success mb-5">
                <i class="fa fa-plus mr-5"></i> Master Data
            </button>
            <button type="button" class="btn btn-rounded btn-hero btn-sm btn-alt-primary mb-5">
                <i class="fa fa-envelope-o mr-5"></i> Compliance Tracker
            </button>
            <a class="btn btn-rounded btn-hero btn-sm btn-alt-secondary mb-5 px-20 cropimage" data-fpo="<?=$fpo->id?>" data-target="banner" data-height="350" data-width="1000" href="javascript:void(0)">
                <i class="fa fa-camera"></i> Banner Edit
            </a>
            <!-- END Actions -->
        </div>
    </div>
</div>
<div class="content">
    <!-- Projects -->
    <h2 class="content-heading">
        <i class="si si-briefcase mr-5"></i> Master Data
    </h2>
    <?php foreach($fpo_basic_columns as $column){?>
        <div class="block block-rounded">
            <div class="block-header block-header-default">
                <h3 class="block-title"><?=$column['label']?></h3>
                <div class="block-options">
                    <button type="button" class="btn-block-option gedit" data-toggle="block-option" data-href="<?=admin_url('fpo/gedit/'.$id.'?group='.$column['name']);?>" ><i class="fa fa-edit"></i></button>
                </div>
            </div>
            <div class="block-content">
                <table class="table table-border table-vcenter mb-30">
                    <tbody>
                    <?php foreach($column['children'] as $children){?>
                        <tr>
                            <td>
                                <a href="javascript:void(0)"><?=$children['label']?></a>
                            </td>
                            <td class="text-right">
                                <?=$children['value']?>
                            </td>
                        </tr>
                    <?}?>
                    </tbody>
                </table>
            </div>
        </div>

    <?}?>

    
    <!-- END Projects -->

    <!-- Colleagues -->
    <h2 class="content-heading">
        <i class="si si-users mr-5"></i> Compliance Tracker
    </h2>
    <?php foreach($fpo_compliance_columns as $column){?>
    <div class="block block-rounded">
        <div class="block-header block-header-default">
            <h3 class="block-title"><?=$column['label']?></h3>
            <div class="block-options">
                <button type="button" class="btn-block-option gedit" data-toggle="block-option" data-href="<?=admin_url('fpo/gedit/'.$id.'?group='.$column['name']);?>" ><i class="fa fa-edit"></i></button>
            </div>
        </div>
        <div class="block-content">
            <table class="table table-border table-vcenter mb-30">
                <tbody>
                <?php foreach($column['children'] as $children){?>
                    <tr>
                        <td>
                            <a href="javascript:void(0)"><?=$children['label']?></a>
                        </td>
                        <td class="text-right">
                            <?=$children['value']?>
                        </td>
                    </tr>
                <?}?>
                </tbody>
            </table>
        </div>
    </div>

    <?}?><!-- END Colleagues -->

    <!-- Articles -->
    <!-- END Articles -->
</div>
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
                <div id="res-message"></div>
                <button type="button" class="btn btn-alt-secondary" data-dismiss="modal">Close</button>
                <button type="button" id="btn-save" class="btn btn-alt-success">
                    <i class="fa fa-check"></i> Save
                </button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="croppermodal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Crop Image Before Upload</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="img-container">
                    <div class="row">
                        <div class="col-md-8">
                            <img src="" id="sample_image" />
                        </div>
                        <div class="col-md-4">
                            <div class="preview"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="crop" class="btn btn-primary">Crop</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>
<style>

    img {
        display: block;
        max-width: 100%;
    }

    .preview {
        overflow: hidden;
        width: 160px;
        height: 160px;
        margin: 10px;
        border: 1px solid red;
    }

    .modal-lg{
        max-width: 1000px !important;
    }

</style>
<?php js_start(); ?>
<script>
    $(function () {
        //add new
        $('.gedit').click(function (e) {
            e.preventDefault();
            url = $(this).data('href');
            //alert(block_id);
            $.ajax({
                headers: {'X-Requested-With': 'XMLHttpRequest'},
                url :url, // json datasource
                type: "get",  // method  , by default get
                dataType:'json',
                beforeSend:function () {
                    //$('#main-container').loading();
                    $("#main-container").LoadingOverlay('show');
                    $('#res-message').text('');
                },
                success:function (json) {
                    if(json.status==false){
                        $('#res-message').text(json.message);
                    } else {
                        $('#modal-title').html(json.title);
                        $('#modal-content').html(json.html);
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
            forms = $("#fpo-form-details")[0];
            action=$(this).closest('.modal-content').find('form').attr('action');
            var formdata=new FormData(forms);
            $.ajax({
                headers: {'X-Requested-With': 'XMLHttpRequest'},
                enctype: 'multipart/form-data',
                url:action,
                data:formdata,
                type:'POST',
                dataType:'JSON',
                processData: false,
                contentType: false,
                cache: false,
                beforeSend:function () {
//                  $('#main-container').loading();
                    $("#modal-fpo .modal-content").LoadingOverlay('show');
                },
                success:function (json) {
                    if(json.status==false){
                        $('#res-message').text(json.errors.fpodoc);
                    } else {
                        location.reload();
                    }

                },
                error:function () {
//                $('#main-container').loading('stop');
                    $("#modal-fpo .modal-content").LoadingOverlay("hide");
                },
                complete:function () {
                    $("#modal-fpo .modal-content").LoadingOverlay("hide");
//                $('#main-container').loading('stop');
                }
            })
        });
    });




    $(document).ready(function(){

        var $modal = $('#croppermodal');
        var image = document.getElementById('sample_image');
        var cropper;
        var target;
        var ratio,width,height;
        var fpo_id;


        $(".cropimage").click(function() {
            fpo_id=$(this).data('fpo');
            target=$(this).data('target');
            width=$(this).data('width');
            height=$(this).data('height');
            ratio=width/height;
            $("input[id='fpo_image']").click();
        });

        $('#fpo_image').change(function(event){
            var files = event.target.files;

            var done = function(url){
                image.src = url;
                $modal.modal('show');
            };

            if(files && files.length > 0)
            {
                reader = new FileReader();
                reader.onload = function(event)
                {
                    done(reader.result);
                };
                reader.readAsDataURL(files[0]);
            }
        });

        $modal.on('shown.bs.modal', function() {

            cropper = new Cropper(image, {
                aspectRatio: ratio,
                viewMode: 3,
                preview:'.preview'
            });
        }).on('hidden.bs.modal', function(){
            cropper.destroy();
            cropper = null;
        });

        $('#crop').click(function(){
            canvas = cropper.getCroppedCanvas({
                width:width,
                height:height
            });

            canvas.toBlob(function(blob){
                url = URL.createObjectURL(blob);
                var reader = new FileReader();
                reader.readAsDataURL(blob);
                reader.onloadend = function(){
                    var base64data = reader.result;
                    $.ajax({
                        url:'<?=admin_url('fpo/upload')?>',
                        method:'POST',
                        data:{image:base64data,target:target,fpo_id:fpo_id,width:width,height:height},
                        success:function(data)
                        {
                            $modal.modal('hide');
                            if(target=="banner"){
                                $('#'+target).css('background-image', 'url("' + data + '")');
                            }else {
                                $('#' + target).attr('src', data);
                            }
                        }
                    });
                };
            });
        });

    });

</script>
<?php js_end(); ?>