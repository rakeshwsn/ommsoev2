<?php
$validation = \Config\Services::validation();
?>
<div class="row">
    <div class="col-xl-12">
        <div class="block">
            <div class="block-header block-header-default bg-success">
                <h3 class="block-title"><?= $heading_title; ?></h3>
            </div>
			<div class="block-header-content" style="display:flex;padding:20px 0 20px 0">
				<div class="col-md-3">
                <label>From Date</label>
				<input type="text"  class="form-control" value="<?=$from_date?>" readonly>
				</div>
				<div class="col-md-3">
				<label>To Date</label>
				<input type="text" readonly value="<?=$to_date?>" class="form-control">
				</div>
				<div class="col-md-2 mt-4">
					<a href="<?=$download_url?>" class="btn btn-square btn-info min-width-125 mb-10"><i class="fa fa-download mr-5"></i> Download</a>
				</div>
				<div class="col-md-2 mt-4">
					<form class="dm-uploader" id="uploader">
						<div role="button" class="btn btn-outline btn-warning">
							<i class="fa fa-folder-o fa-fw"></i> Upload Excel
							<input type="file" title="Click to add Files">
						</div>
					</form>	
				</div>		
			</div>
           
        </div>
    </div>
    <div class="col-xl-12">
        <div class="block">
            <div class="block-header block-header-default  bg-primary">
                <h3 class="block-title"> Area Coverage History</h3>
            </div>
			
            <div class="block-content">
                <table class="table table-vcenter text-center">
                    <thead>
                        <tr>
                            <th>Week</th>
                            <th>Total Farmer</th>
                            <th>Total Area</th>
                            <th>Upload Status</th>
                            <th>Date Added</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td style="display: flex;">
                               
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div id="loading-overlay">
    <div class="progress" style="width: 80%">
        <div class="progress-bar progress-bar-striped progress-bar-animated" id="progress-bar" style="width:0%">
            <span id="progress-percent">0</span>
        </div>
    </div>
</div>
<?php js_start(); ?>

<script type="text/javascript">
    $('#uploader').dmUploader({
        dnd:false,
        url: '<?=$upload_url?>',
        dataType:'json',
        maxFileSize: 1000000, // 1MB
        multiple: false,
        allowedTypes: 'application/*',
        extFilter: ['xls'],
        onInit: function(){
            // Plugin is ready to use
//            console.log('initialized')
        },
        onComplete: function(){
            // All files in the queue are processed (success or error)
            $('#upload-controls').loading('stop');
        },
        onNewFile: function(id, file){
            // When a new file is added using the file selector or the DnD area
            show_error('')
        },
        onBeforeUpload: function(id){
            // about tho start uploading a file
            setProgress(0)
            if(typeof(loading)=='undefined') {
                loading = $('#upload-controls').loading({
                    overlay: $('#loading-overlay')
                });
            } else {
                $('#upload-controls').loading();
            }
        },
        onUploadCanceled: function(id) {
            // Happens when a file is directly canceled by the user.
        },
        onUploadProgress: function(id, percent){
            // Updating file progress
            setProgress(percent)
        },
        onUploadSuccess: function(id, data){
            // A file was successfully uploaded server response
            if(data.status) {
                show_error('File uploaded successfully');
                $('.dm-uploader .status').addClass('text-success')
            } else {
                show_error(data.message)
            }
            $('#datatable').DataTable().ajax.reload();
        },
        onUploadError: function(id, xhr, status, message){
            console.log(message);
            show_error(message);
            $('#upload-controls').loading('stop');
        },
        onFileSizeError: function(file){
            // file.name
            show_error('Invalid file size')
        },
        onFileExtError: function(file){
            // file.name
            show_error('Invalid file type')
        },
        onFileTypeError: function(file){
            // file.name
            show_error('Invalid file type')
        }
    });

    function show_error(msg){
        $('.dm-uploader .status').addClass('text-danger').text(msg)
    }
    function setProgress(percent) {
        $('#progress-bar').width(percent+'%')
        $('#progress-percent').text(percent+'%')
    }
</script>
<?php js_end(); ?>    



