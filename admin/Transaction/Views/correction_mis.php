<style>
    .w-50p{width:50px;}
</style>
<!-- Main content -->
<section class="content">

    <div class="block block-themed">
        <div class="block-header bg-info">
            <h3 class="block-title">Summary</h3>
        </div>
        <div class="block-content block-content-full">
            <table class="table table-bordered table-striped table-vcenter js-dataTable-full">
                <thead>
                <tr>
                    <th>District</th>
                    <th>Block</th>
                    <th>Agency Type</th>
                    <th>Month/Year</th>
                    <th>Date Added</th>
                    <th>Status</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td><?=$district?></td>
                    <td><?=$block?></td>
                    <td><?=$agency_type?></td>
                    <td><?=$month?> / <?=$year?></td>
                    <td><?=$date_added?></td>
                    <?php if(isset($approval) && $approval): ?>
                        <td><button class="btn btn-primary" id="btn-action"><?=$status?></button> </td>
                    <?php else: ?>
                        <td><?=$status?></td>
                    <?php endif; ?>
                    
                </tr>
                <?php if(!empty($remarks)): ?>
                <tr>
                    <td>Remarks:</td>
                    <td colspan="7"><?=$remarks?></td>
                </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="block block-themed">
        <div class="block-header bg-muted">
            <h3 class="block-title">MIS Details</h3>
        </div>
        <div class="block-content block-content-full">
            <?php echo form_open(); ?>
            <div class="tableFixHead">
                <table class="table custom-table " id="txn-table">
                    <thead>
                    <tr>
                        <th width="10%">Sl no</th>
                        <th width="30%">Component</th>
                        <th width="15%">Unit Type</th>
                        <th width="15%">Output Indicator</th>
                        <th width="15%">Achievement</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?=$components?>
                    </tbody>
                </table>
            </div>
            <div class="row">
                <div class="col mt-4">
                    <button type="submit" class="btn btn-alt-primary float-right">Submit</button>
                </div>
            </div>
            </form>
        </div>
    </div>
</section>

<?php js_start(); ?>
<script>

    $('.dm-uploader').dmUploader({
        dnd:false,
        url: '<?=$upload_url?>',
        dataType:'json',
        maxFileSize: 50000000, // 20MB
        multiple: false,
//        allowedTypes: 'image/*',
//        extFilter: ['jpg','png','jpeg','JPG','PNG','JPEG'],
        onInit: function(){
            // Plugin is ready to use
            console.log('initialized')
        },
        onComplete: function(){
            // All files in the queue are processed (success or error)
//            $('.tableFixHead').loading('stop');
            $('.tableFixHead').LoadingOverlay("hide",true);
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
//            $('.tableFixHead').loading();
            $('.tableFixHead').LoadingOverlay("show");
        },
        onUploadSuccess: function(id, data){
            //console.log(data.message);
            // A file was successfully uploaded server response
            if(data.status) {
                $(this).find('.status').html(data.message);
                $(this).find('.filepath').val(data.filepath)
            } else {
                show_error(this,data.message);
            }

        },
        onUploadError: function(id, xhr, status, message){
            show_error(this,message)
//            $('.tableFixHead').loading('stop');
            $('.tableFixHead').LoadingOverlay("hide",true);
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
    function setProgress(percent) {
        $('#progress-bar').width(percent+'%')
        $('#progress-percent').text(percent+'%')
    }

    $(function () {
        numOnly();
        decimalOnly();
    });

    //rakesh
    function numOnly() {
        //input type text to number
        // Get the input field
        var input = $('.rate,.physical');

        // Attach keypress event handler
        input.keypress(function(event) {
            // Get the key code of the pressed key
            var keyCode = event.which;

            // Check if the key is a number
            if (keyCode < 48 || keyCode > 57) {
                // Prevent the input if the key is not a number
                event.preventDefault();
            }
        });
    }

    function decimalOnly() {
        // Get the input field
        var input = $('.financial');

        $('.financial').on('keypress',function (e) {
            // Get the key code of the pressed key
            var keyCode = event.which;

            // Allow decimal point (.) and numbers (48-57) only
            if (keyCode !== 46 && (keyCode < 48 || keyCode > 57)) {
                // Prevent the input if the key is not a number or decimal point
                event.preventDefault();
            }

            // Allow only one decimal point
            if (keyCode === 46 && $(this).val().indexOf('.') !== -1) {
                // Prevent the input if there is already a decimal point
                event.preventDefault();
            }
            // Disallow comma (,)
            if (keyCode === 44) {
                // Prevent the input if the key is a comma
                event.preventDefault();
            }
        });
    }
</script>
<?php js_end(); ?>

<?php if(isset($approval)) {
    echo $approve_form;
} ?>

