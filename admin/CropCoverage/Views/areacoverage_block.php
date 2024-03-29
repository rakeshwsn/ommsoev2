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
                <div class="col-md-2 down-btn">
                    <?php
                    if ($isActiveDay) {
                        echo '<a href="' . $download_url . '" class="btn btn-square btn-info min-width-125 mb-10"><i class="fa fa-download mr-5"></i> Download</a>';
                    } else {
                        echo '<button class="btn btn-square btn-danger min-width-125" disabled><i class="fa fa-download mr-5"></i> Download</button>';
                    }
                    ?>
                </div>
                <div class="col-md-2 up-btn">
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
                <p style="color:red;font-size: 15px;padding-top:2px;">Please Download and Upload data Monday-Friday</p>
            </div>

        </div>

        <form action="">
            <div class="block">
                <div class="block-header block-header-default bg-warning">
                    <h3 class="block-title">
                        Area Coverage WeekWise
                    </h3>
                </div>
                <div class="block-header-content" style="display:flex;padding:20px 0 20px 0">
                    <div class="col-lg-3">
                        <div class="form-group mg-b-10-force">
                            <label>Week</label>
                            <?= form_dropdown('start_date', $weeks, $get_date, "id='filter_week' class='form-control js-select2'"); ?>
                        </div>
                    </div><!-- col-4 -->
                    <div class="col-lg-3 center">
                        <label class="form-control-label">&nbsp;</label>
                        <div class="form-layout-footer">
                            <button id="btn-filter" class="btn btn-primary">Filter</button>
                        </div><!-- form-layout-footer -->
                    </div>
                </div>
            </div>
        </form>


        <div class="block">
            <div class="block-header block-header-default  bg-primary">
                <h3 class="block-title"> Area Coverage History</h3>
            </div>

            <div class="block-content">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-vcenter">
                        <thead>
                            <tr>
                                <th>Week</th>
                                <th>GP</th>
                                <th>Total Farmer</th>
                                <th>Nursery Raised For Covearge Of Area(In Hectare)</th>
                                <th>Balance SMI</th>
                                <th>Balance LT</th>
                                <th>Total Ragi</th>
                                <th>Total Non Ragi</th>
                                <th>Total Followup Crop</th>
                                <th>Total Area</th>
                                <th>Total Crop Diversification Area</th>
                                <?php if ($season === 'Rabi') { ?>
                                    <th>Total Rice Fallow during Rabi (in Ha)</th>
                                <?php } ?>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="table-body">

                            <?php if ($gps) { ?>
                                <?php foreach ($gps as $key => $gp) { ?>
                                    <tr>
                                        <td>
                                            <?= $gp['start_date'] ?>
                                        </td>
                                        <td>
                                            <?= $gp['gp_name'] ?>
                                        </td>
                                        <td>
                                            <?= $gp['farmer_covered'] ?>
                                        </td>
                                        <td>
                                            <?= $gp['nursery_raised'] ?>
                                        </td>
                                        <td>
                                            <?= $gp['balance_smi'] ?>
                                        </td>
                                        <td>
                                            <?= $gp['balance_lt'] ?>
                                        </td>

                                        <td>
                                            <?= $gp['total_ragi']; ?>
                                        </td>
                                        <td>
                                            <?= $gp['total_non_ragi']; ?>
                                        </td>
                                        <td>
                                            <?= $gp['follow_area']; ?>
                                        </td>
                                        <td>
                                            <?= $gp['total_area']; ?>
                                        </td>
                                        <td>
                                            <?= $gp['crop_div_area']; ?>
                                        </td>
                                        <?php if ($season == 'Rabi') { ?>
                                            <td>
                                                <?= $gp['fallow_area'] ?>
                                            </td>
                                        <?php } ?>

                                        <td>
                                            <?= $gp['status']; ?>
                                        </td>
                                        <td>
                                            <?= $gp['action']; ?>
                                        </td>
                                    </tr>
                                <?php } ?>

                            <?php } else { ?>
                                <tr>
                                    <td colspan="4">Data not available.</td>
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