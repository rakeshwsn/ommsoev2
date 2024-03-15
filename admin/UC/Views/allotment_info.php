<div class="row">
    <div class="col-xl-12">
        <div class="block">
            <div class="block-header block-header-default">
                <h3 class="block-title">Allotments</h3>
            </div>

            <div class="block-content">
                <table class="table table-striped" id="block-components">
                    <thead>
                    <tr>
                        <th>District/Agency</th>
                        <th>Year</th>
                        <th>Allotment Date</th>
                        <th>Allotment From SPMU</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if($allotments): ?>
                    <?php foreach ($allotments as $allotment): ?>
                        <tr>
                            <td><?=$allotment['recipient']?></td>
                            <td><?=$allotment['year']?></td>
                            <td><?=$allotment['allotment_date']?></td>
                            <td><?=in_rupees($allotment['amount'])?></td>
                            <td><?=$allotment['action']?></td>
                        </tr>
                    <?php endforeach; ?>
                        <tr>
                            <td colspan="3">Total</td>
                            <td><?=in_rupees($total_allotment)?></td>
                            <td><?=$allotment['submit']?></td>
                        </tr>
                    <?php else: ?>
                        <tr>
                            <td colspan="5">No Data</td>
                        </tr>
                    <?php endif; ?>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-xl-12">
        <div class="block">
            <div class="block-header block-header-default">
                <h3 class="block-title">UC Submissions</h3>
            </div>

            <div class="block-content">
                <table class="table table-striped" id="block-components">
                    <thead>
                    <tr>
                        <th>Date</th>
                        <th>Letter No</th>
                        <th>Page No</th>
                        <th>UC Submitted</th>
                        <th>Document</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if($submissions): ?>
                        <?php foreach ($submissions as $allotment): ?>
                            <tr>
                                <td><?=$allotment['date_submit']?></td>
                                <td><?=$allotment['letter_no']?></td>
                                <td><?=$allotment['page_no']?></td>
                                <td><?=in_rupees($allotment['amount'])?></td>
                                <td><?php if($allotment['document']) { ?><a href="<?=$allotment['document']?>" class="btn btn-primary" target="_blank"><i class="fa fa-file-pdf-o"></i></a><?php } ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <tr>
                            <td colspan="3">Total</td>
                            <td><?=in_rupees($total_uc)?></td>
                            <td></td>
                        </tr>
                    <?php else: ?>
                        <tr>
                            <td colspan="5">No Data</td>
                        </tr>
                    <?php endif; ?>

                    </tbody>
                </table>
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

<!-- Edit Modal -->
<div class="modal fade" id="modal-edit" tabindex="-1" role="dialog" aria-labelledby="modal-popout" aria-hidden="true">
    <div class="modal-dialog modal-dialog-popout" role="document">
        <div class="modal-content">
            <div class="block block-themed block-transparent mb-0">
                <div class="block-header bg-primary-dark">
                    <h3 class="block-title" id="modal-title-edit"></h3>
                    <div class="block-options">
                        <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                            <i class="si si-close"></i>
                        </button>
                    </div>
                </div>
                <div class="block-content" id="modal-content-edit">
                    Hello
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-alt-secondary" data-dismiss="modal">Close</button>
                <button type="button" id="btn-edit" class="btn btn-alt-success">
                    <i class="fa fa-check"></i> Save
                </button>
            </div>
        </div>
    </div>
</div>


<?php js_start(); ?>
<script>
    var url;
    $(function () {
        //add new
        $('.btn-submit').click(function (e) {
            e.preventDefault();
            url = $(this).attr('href');
            $.ajax({
                headers: {'X-Requested-With': 'XMLHttpRequest'},
                url : url, // json datasource
                type: "get",  // method  , by default get
                dataType:'json',
                beforeSend:function () {
                    $("#main-container").LoadingOverlay('show');
                },
                success:function (json) {
                    $('#modal-title').html(json.title);
                    $('#modal-content').html(json.html);
                    $("#modal-add-new").modal({
                        backdrop: 'static',
                    });
                    bindUploader()
                },
                error: function(){  // error handling
                    $("#main-container").LoadingOverlay("hide",true);
                },
                complete:function () {
                    $("#main-container").LoadingOverlay("hide",true);
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

        $(document).on('click','#btn-edit',function () {
            formdata = $(this).closest('.modal-content').find('form').serialize();

            $.ajax({
                headers: {'X-Requested-With': 'XMLHttpRequest'},
                url:url,
                data:formdata,
                type:'POST',
                dataType:'JSON',
                before:function () {
//                $('#main-container').loading();
                    $("#main-container").LoadingOverlay('show');
                },
                success:function (json) {
                    location.reload();
                },
                error:function () {
                    $("#main-container").LoadingOverlay("hide");
//                $('#main-container').loading('stop');
                },
                complete:function () {
//                $('#main-container').loading('stop');
                    $("#main-container").LoadingOverlay("hide");
                }
            })
        });

        $(document).on('click','.btn-edit',function (e){
            e.preventDefault();
            url = $(this).attr('href')
            $.ajax({
                headers: {'X-Requested-With': 'XMLHttpRequest'},
                url:url,
                data:{},
                type:'GET',
                dataType:'JSON',
                before:function () {
//                $('#main-container').loading();
                    $("#main-container").LoadingOverlay('show');
                },
                success:function (json) {
                    if(json.status==false){
                        $('#res-message').text(json.message);
                    } else {
                        $('#modal-title-edit').html(json.title)
                        $('#modal-content-edit').html(json.html)
                        $("#modal-edit").modal({
                            backdrop: 'static',
                        });
                    }
                },
                error:function () {
//                $('#main-container').loading('stop');
                    $("#main-container").LoadingOverlay("hide");
                },
                complete:function () {
//                $('#main-container').loading('stop');
                    $("#main-container").LoadingOverlay("hide");
                }
            });
        });

        $(document).on('click','.btn-delete',function (e){
            row = $(this).closest('tr');
            e.preventDefault();
            url = $(this).attr('href');
            if(confirm('Are you sure to delete this allotment?')!==false){
                $.ajax({
                    url:url,
                    type:'POST',
                    success:function (json) {
                        if(json.status==true){
                            $(row).remove();
                            alert('Allotment has been deleted.');
                        } else {
                            alert('Unable to delete. Contact admin');
                        }
                    },
                    error:function () {
                        alert('Unable to delete. Contact admin');
                    }
                });
            }
        });

    });

    function bindUploader() {
        $('.dm-uploader').dmUploader({
            dnd:false,
            url: '<?=$upload_url?>',
            dataType:'json',
            maxFileSize: 12000000, // 12MB
            multiple: false,
            fieldName: 'document',
            allowedTypes: 'application/pdf',
            extFilter: ['pdf'],
            onInit: function(){
                // Plugin is ready to use
//            console.log('initialized')
            },
            onComplete: function(){
                $('.modal-dialog').LoadingOverlay("hide",true);
            },
            onNewFile: function(id, file){
                // When a new file is added using the file selector or the DnD area
                show_error(this,'')
            },
            onBeforeUpload: function(id){
                // about tho start uploading a file

            },
            onUploadCanceled: function(id) {
                // Happens when a file is directly canceled by the user.
            },
            onUploadProgress: function(id, percent){
                // Updating file progress
//            $('#closing-balance-breakup').loading();
                $('.modal-dialog').LoadingOverlay("show");
            },
            onUploadSuccess: function(id, data){
                // A file was successfully uploaded server response
                if(data.status) {
                    $(this).find('.status').html(data.message);
                    $(this).find('.filepath').val(data.filepath)
                    $(this).find('.document-name').val(data.filename)
                } else {
                    show_error(this,data.message);
                }

            },
            onUploadError: function(id, xhr, status, message){
                show_error(this,message)
//            $('#closing-balance-breakup').loading('stop');
                $('.modal-dialog').LoadingOverlay("hide",true);
            },
            onFileSizeError: function(file){
                // file.name
                show_error(this,'Invalid file size')
            },
            onFileExtError: function(file){
                // file.name
                show_error(this,'Invalid file type')
            },
            onFileTypeError: function(file){
                // file.name
                show_error(this,'Invalid file type')

            }
        });
        function show_error(obj,msg){
            $(obj).find('.status').addClass('text-danger').text(msg)
        }
    }
</script>
<?php js_end(); ?>