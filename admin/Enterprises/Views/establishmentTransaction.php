<div class="main-container">
    <div class="block">
        <div class="block-header block-header-default">
            <h3 class="block-title">Add Enterprise Transactions</h3>
        </div>
        <div class="block-content block-content-full">
            <div id="datatable_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
                <!-- DataTables functionality is initialized with .js-dataTable-full class in js/pages/be_tables_datatables.min.js which was auto compiled from _es6/pages/be_tables_datatables.js -->
                <div class="row">
                    <div class="col-3">
                        <label class="form-label">Year</label>
                        <?php echo form_dropdown('year_id', $years, set_value('year_id', $year_id), ['class' => 'form-control mb-3', 'id' => 'year']); ?>
                        <span id="em1"></span>
                    </div>
                    <div class="col-3">
                        <label class="form-label">District</label>
                        <?php echo form_dropdown('district_id', $districts, set_value('district_id', $district_id), ['class' => 'form-control mb-3', 'id' => 'districts']); ?>
                        <span id="em2" class="text-danger"></span>
                    </div>
                    <div class="col-3">
                        <label class="form-label">Months</label>
                        <?php echo form_dropdown('month_id', $months, set_value('month_id', $month_id), ['class' => 'form-control mb-3', 'id' => 'month']); ?>
                        <span id="em3" class="text-danger"></span>

                    </div>
                    <div class="col-3">
                        <label class="form-label">Fortnight</label>
                        <select class="form-control" name="period" id="period">
                            <option value="0">all</option>
                            <option value="1" <?= $period == "1" ? 'selected' : ''; ?>>1st fortnight</option>
                            <option value="2" <?= $period == "2" ? 'selected' : ''; ?>>2nd fortnight</option>
                        </select>
                        <span id="em" class="text-danger"></span>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <div class="float-right d-flex">
                            <div class="d-inline mx-2">
                                <a href="<?= $excel_link ?>" id="btn-excel" class="btn btn-outline-danger"><i class="fa fa-file-excel-o"></i> Download Form</a>
                            </div>
                            <div class="d-inline mx-2">
                                <form class="dm-uploader" id="uploader">
                                    <div role="button" class="btn btn-outline btn-warning">
                                        <i class="fa fa-folder-o fa-fw"></i> Upload Excel
                                        <input type="file" title="Click to add Files">
                                    </div>
                                    <span class="status"></span>

                                </form>

                            </div>
                        </div>
                        
                    </div>
                </div>


            </div>
        </div>
    </div>

    <div class="block">
        <div class="block-header block-header-default">
            <h3 class="block-title">Filter</h3>
        </div>
        <div class="block-content block-content-full">
            <div id="datatable_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
                <?php echo form_open('', ['method' => 'get']); ?>
                <!-- DataTables functionality is initialized with .js-dataTable-full class in js/pages/be_tables_datatables.min.js which was auto compiled from _es6/pages/be_tables_datatables.js -->
                <div class="row">
                    <div class="col-3">
                        <label class="form-label">Year</label>
                        <?php echo form_dropdown('year_id', $years, set_value('year_id', $year_id), ['class' => 'form-control mb-3', 'id' => 'year']); ?>
                       
                    </div>
                    <div class="col-3">
                        <label class="form-label">District</label>
                        <?php echo form_dropdown('district_id', $districts, set_value('district_id', $district_id), ['class' => 'form-control mb-3', 'id' => 'districts']); ?>
                        

                    </div>
                    <div class="col-3">
                        <label class="form-label">Block</label>
                        <?php echo form_dropdown('block_id', $blocks, set_value('block_id', $block_id), ['class' => 'form-control mb-3', 'id' => 'blocks']); ?>
                       

                    </div>
                    <div class="col-3">
                        <label class="form-label">Gp</label>
                        <?php echo form_dropdown('gp_id', $gps, set_value('gp_id', $gp_id), ['class' => 'form-control mb-3', 'id' => 'gps']); ?>
                       

                    </div>
                </div>
                <div class="row">
                    <div class="col-3">
                        <label class="form-label">Months</label>
                        <?php echo form_dropdown('month_id', $months, set_value('month_id', $month_id), ['class' => 'form-control mb-3', 'id' => 'month']); ?>
                       

                    </div>
                    <div class="col-3">
                        <label class="form-label">Unit type</label>
                        <?php echo form_dropdown('unit_id', $units, set_value('unit_id', $unit_id), ['class' => 'form-control mb-3', 'id' => 'unit']); ?>
                       

                    </div>
                    <div class="col-3">
                        <label class="form-label">Fortnight</label>
                        <select class="form-control" name="period" id="period">
                            <option value="0">all</option>
                            <option value="1" <?= $period == "1" ? 'selected' : ''; ?>>1st fortnight</option>
                            <option value="2" <?= $period == "2" ? 'selected' : ''; ?>>2nd fortnight</option>
                        </select>
                       
                    </div>
                    <div class="col-3 mt-4">
                        <button class="btn btn-primary">Filter</button>
                    </div>

                </div>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>


    <div class="block">
        <div class="block-header block-header-default">
            <h3 class="block-title">Enterprise Transaction List</h3>
            <div class="block-options d-flex">

                <div class="ml-3">
                    
                </div>

            </div>
        </div>
        <div class="block-content block-content-full">
            <div class="block">
                <div class="row">

                    <div class="col-sm-12">
                        <table id="datatable" class="table table-bordered table-striped table-vcenter js-dataTable-full dataTable no-footer" aria-describedby="datatable_info">
                            <thead>
                                <tr>
                                    <th>Date Upload</th>
                                    <th>Unit Type</th>
                                    <th>District</th>
                                    <th>Block</th>
                                    <th>GP</th>
                                    <th>Village</th>
                                    <th>Month</th>
                                    <th>Financial Year</th>
                                    <th>Fortnight</th>
                                    <th class="text-right no-sort sorting_disabled" aria-label="Actions">Actions</th>
                                </tr>
                            </thead>

                        </table>
                    </div>
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
        // var download_url = "http://ommsoev2.local/admin/enterprises/download";

        $(function() {
            function main() {
                var href = download_url + '?district_id=' + $('#districts').val() + '&month_id=' + $('#month').val() + '&year_id=' + $('#year').val() + '&period=' + $('#period').val();
                $('#btn-excel').attr('href', href);
            }
            $('#districts, #month, #year, #period').on('change', main);
            main();
        });


        var download_url = "<?= $excel_link ?>";

        $(document).ready(function() {

            $("#btn-excel").click(function() {
                if ($("#year").val() === "0") {
                    $("#em1").html("This field could not be empty!");
                    $("#year").css("border-color", "red");
                    $("#year").focus();
                    return false;
                }
                if ($("#districts").val() === "0") {
                    $("#em2").html("This field could not be empty!");
                    $("#districts").css("border-color", "red");
                    $("#districts").focus();
                    return false;
                }
                if ($("#month").val() === "0") {
                    $("#em3").html("This field could not be empty!");
                    $("#month").css("border-color", "red");
                    $("#month").focus();
                    return false;
                }
                if ($("#period").val() === "0") {
                    $("#em").html("This field could not be empty!");
                    $("#period").css("border-color", "red");
                    $("#period").focus();
                    return false;
                }
            })

            $("#year").change(function() {
                $("#em1").hide();
                $("#year").css("border-color", "green");

            })
            $("#districts").change(function() {
                $("#em2").hide();
                $("#districts").css("border-color", "green");

            })
            $("#month").change(function() {
                $("#em3").hide();
                $("#month").css("border-color", "green");

            })
            $("#period").change(function() {
                $("#em").hide();
                $("#period").css("border-color", "green");
            })
            var table = $('#datatable').DataTable({
                "processing": true,
                "serverSide": true,
                "columnDefs": [{
                    targets: 'no-sort',
                    orderable: false
                }],
                "ajax": {
                    url: "<?= $datatable_url ?>", // json datasource
                    type: "post", // method  , by default get
                    data: function(data) {
                        data.district_id = $('#districts').val();
                        data.block_id = $('#blocks').val();
                        data.gp_id = $('#gps').val();
                        data.month_id = $('#month').val();
                        data.unit_id = $('#unit').val();
                        data.period = $('#period').val();
                        data.year_id = $('#year').val();

                    },
                    beforeSend: function() {
                        // $('.alert-dismissible, .text-danger').remove();
                        $("#datatable_wrapper").LoadingOverlay("show");
                    },
                    complete: function() {
                        $("#datatable_wrapper").LoadingOverlay("hide");
                    },
                    error: function() { // error handling
                        $(".datatable_error").html("");
                        $("#datatable_processing").css("display", "none");

                    },
                    dataType: 'json'
                }
            });
            $(function() {

                $('#districts').on('change', function() {

                    var d_id = $(this).val(); // Declare d_id with var

                    $.ajax({
                        url: 'admin/enterprises/blocks',
                        data: {
                            district_id: d_id
                        },
                        type: 'GET',
                        dataType: 'JSON',
                        beforeSend: function() {},
                        success: function(response) {
                            if (response.blocks) {

                                var html = '<option value="">Select Block</option>'; // Declare html with var
                                $.each(response.blocks, function(k, v) {
                                    html += '<option value="' + v.id + '">' + v.name + '</option>';
                                });
                                $('#blocks').html(html);

                            }

                        },

                        error: function() {
                            alert('something went wrong');
                        },
                        complete: function() {

                        }
                    });



                });
                $('#blocks').on('change', function() {
                    var b_id = $(this).val();
                    $.ajax({
                        url: 'admin/enterprises/gps',
                        data: {
                            block_id: b_id
                        },
                        type: 'GET',
                        dataType: 'JSON',
                        beforeSend: function() {},
                        success: function(response) {
                            if (response.gps) {
                                var html = '<option value="">Select GP</option>';
                                $.each(response.gps, function(k, v) {
                                    html += '<option value="' + v.id + '">' + v.name + '</option>';
                                });
                                $('#gps').html(html);
                            }
                        },
                        error: function() {
                            alert('something went wrong');
                        },
                        complete: function() {}
                    });
                });

            });
        })


        $('#uploader').dmUploader({
            dnd: false,
            url: '<?= $upload_url ?>',
            dataType: 'json',
            maxFileSize: 1000000, // 1MB
            multiple: false,
            allowedTypes: 'application/*',
            extFilter: ['xlsx'],
            onInit: function() {
                // Plugin is ready to use
                //            console.log('initialized')
            },
            onComplete: function() {
                // All files in the queue are processed (success or error)
                $('#upload-controls').loading('stop');
            },
            onNewFile: function(id, file) {
                // When a new file is added using the file selector or the DnD area
                show_error('')
            },
            onBeforeUpload: function(id) {
                // about tho start uploading a file
                setProgress(0)
                if (typeof(loading) == 'undefined') {
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
            onUploadProgress: function(id, percent) {
                // Updating file progress
                setProgress(percent)
            },
            onUploadSuccess: function(id, data) {
                // A file was successfully uploaded server response
                if (data.url) {
                    location.href = data.url;
                } else {
                    show_error(data.message)
                }
                $('#progress-bar').width(0 + '%');
            },
            onUploadError: function(id, xhr, status, message) {
                console.log(message);
                show_error(message);
                $('#upload-controls').loading('stop');
            },
            onFileSizeError: function(file) {
                // file.name
                show_error('Invalid file size');
            },
            onFileExtError: function(file) {
                // file.name
                show_error('Invalid file type');
            },
            onFileTypeError: function(file) {
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