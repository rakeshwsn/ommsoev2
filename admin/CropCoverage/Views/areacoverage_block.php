<div class="row">
    <div class="col-xl-12">
        <div class="block">
            <div class="block-header block-header-default bg-success">
                <h3 class="block-title">
                    <?= $heading_title; ?>
                </h3>
            </div>
            <div class="block-header-content" style="display:flex;padding:20px 0 20px 0">
                <div class="col-md-3">
                    <label>From Date</label>
                    <input type="text" class="form-control" value="<?= $from_date ?>" readonly>
                </div>
                <div class="col-md-3">
                    <label>To Date</label>
                    <input type="text" readonly value="<?= $to_date ?>" class="form-control">
                </div>
                <div class="col-md-2 mt-4">
                    <?php
                    if ($isActiveDay) {
                        echo '<a href="' . $download_url . '" class="btn btn-square btn-info min-width-125 mb-10"><i class="fa fa-download mr-5"></i> Download</a>';
                    } else {
                        echo '<button class="btn btn-square btn-danger min-width-125 mb-10" disabled><i class="fa fa-download mr-5"></i> Download</button>';
                    }
                    ?>

                </div>
                <div class="col-md-2 mt-4">
                    <form class="dm-uploader" id="uploader">
                        <div role="button" class="btn btn-outline <?= $isActiveDay ? 'btn-warning' : 'btn-danger'; ?>">
                            <i class="fa fa-folder-o fa-fw"></i> Upload Excel
                            <?php if ($isActiveDay): ?>
                                <input type="file" title="Click to add Files">
                            <?php else: ?>
                                <input type="file" title="File upload is disabled" disabled>
                            <?php endif; ?>
                        </div>
                        <div class="status"></div>
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
                <table class="table">
                    <thead>
                        <tr>
                            <th>Week</th>
                            <th>GP</th>
                            <th>Total Farmer</th>
                            <th>Nursery Raised</th>
                            <th>Balance SMI</th>
                            <th>Balance LT</th>
                            <th>Total Ragi</th>
                            <th>Total Non Ragi</th>
                            <th>Total Followup Crop</th>
                            <th>Total Area</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($blocks))
                            foreach ($blocks as $block) { ?>
                                <tr>
                                    <td>
                                        <?= $block['week'] ?>
                                    </td>
                                    <td>
                                        <?= $block['gp'] ?>
                                    </td>
                                    <td>
                                        <?= $block['farmers_covered'] ?>
                                    </td>
                                    <td>
                                        <?= $block['nursery_raised'] ?>
                                    </td>
                                    <td>
                                        <?= $block['balance_smi'] ?>
                                    </td>
                                    <td>
                                        <?= $block['balance_lt'] ?>
                                    </td>
                                    <td>
                                        <?= $block['total_ragi'] ?>
                                    </td>
                                    <td>
                                        <?= $block['total_non_ragi'] ?>
                                    </td>
                                    <td>
                                        <?= $block['total_fc'] ?>
                                    </td>
                                    <td>
                                        <?= $block['total_area'] ?>
                                    </td>
                                    <td>
                                        <?= $block['status'] ?>
                                    </td>
                                    <td style="display: flex;">
                                        <div class="btn-group btn-group-sm pull-right">
                                            <?= $block['action'] ?>
                                        </div>
                                    </td>
                                </tr>

                            <?php } else { ?>
                            <tr>
                                <td colspan="12">Data not available.</td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div id="loading-overlay">
    <div class="progress" style="width: 100%">
        <div class="progress-bar progress-bar-striped progress-bar-animated" id="progress-bar" style="width:0%">
            <span id="progress-percent">0</span>
        </div>
    </div>
</div>
<style>
    #loading-overlay {
        background: rgb(255 255 255 / 80%);
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        z-index: 9999;
    }
</style>
<?php js_start(); ?>

<script type="text/javascript">
    $('#uploader').dmUploader({
        dnd: false,
        url: '<?= $upload_url ?>',
        dataType: 'json',
        maxFileSize: 1000000, // 1MB
        multiple: false,
        allowedTypes: 'application/*',
        extFilter: ['xlsx'],
        onInit: function () {
            // Plugin is ready to use
            //            console.log('initialized')
        },
        onComplete: function () {
            // All files in the queue are processed (success or error)
            $('#upload-controls').loading('stop');
        },
        onNewFile: function (id, file) {
            // When a new file is added using the file selector or the DnD area
            show_error('')
        },
        onBeforeUpload: function (id) {
            // about tho start uploading a file
            setProgress(0)
            if (typeof (loading) == 'undefined') {
                loading = $('#upload-controls').loading({
                    overlay: $('#loading-overlay')
                });
            } else {
                $('#upload-controls').loading();
            }
        },
        onUploadCanceled: function (id) {
            // Happens when a file is directly canceled by the user.
        },
        onUploadProgress: function (id, percent) {
            // Updating file progress
            setProgress(percent)
        },
        onUploadSuccess: function (id, data) {
            // A file was successfully uploaded server response
            if (data.status) {
                show_error('File uploaded successfully');
                $('.dm-uploader .status').addClass('text-success');
                location.href = data.url;
            } else {
                show_error(data.message)
            }
            $('#progress-bar').width(0 + '%');
        },
        onUploadError: function (id, xhr, status, message) {
            console.log(message);
            show_error(message);
            $('#upload-controls').loading('stop');
        },
        onFileSizeError: function (file) {
            // file.name
            show_error('Invalid file size');
        },
        onFileExtError: function (file) {
            // file.name
            show_error('Invalid file type');
        },
        onFileTypeError: function (file) {
            // file.name
            show_error('Invalid file type');
        }
    });

    function show_error(msg) {
        $('.dm-uploader .status').addClass('text-danger').text(msg)
    }
    function setProgress(percent) {
        $('#progress-bar').width(percent + '%')
        $('#progress-percent').text(percent + '%')
    }
</script>


<?php js_end(); ?>