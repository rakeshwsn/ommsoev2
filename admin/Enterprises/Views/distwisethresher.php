<div class="main-container">
    <div class="block">
        <div class="block-header block-header-default">
            <h3 class="block-title">Filter</h3>
        </div>
        <div class="block-content block-content-full">
            <div id="page_list_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
                <?php echo form_open('', ['method' => 'get']); ?>
                <!-- DataTables functionality is initialized with .js-dataTable-full class in js/pages/be_tables_datatables.min.js which was auto compiled from _es6/pages/be_tables_datatables.js -->
                <div class="row">
                    <div class="col-2">
                        <label class="form-label">Year</label>
                        <span id="em1" class="text-danger"></span>

                    </div>
                    <div class="col-2">
                        <label class="form-label">District</label>
                        <span id="em2" class="text-danger"></span>

                    </div>
                    <div class="col-2">
                        <label class="form-label">Months</label>
                        <span id="em3" class="text-danger"></span>

                    </div>

                    <div class="col-4 mt-4">
                        <button class="btn btn-primary">Filter</button>
                    </div>

                </div>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>

<div class="block">
    <div class="block-header block-header-default">
        <h3 class="block-title">Data List</h3>
        <div class="block-options d-flex">
            <div>

                <a href="" id="btn-excel" class="btn btn-outline-danger"><i class="fa fa-file-excel-o"></i> Download Form</a>
            </div>
            <div class="ml-3">
                <form class="dm-uploader" id="uploader">
                    <div role="button" class="btn btn-outline btn-warning">
                        <i class="fa fa-folder-o fa-fw"></i> Upload Excel
                        <input type="file" title="Click to add Files">
                    </div>
                    <div class="status"></div>
                </form>
            </div>

        </div>
    </div>
    <div class="block-content block-content-full">
        <div class="block">
            <div class="row">

                <div class="col-sm-12">
                    <table id="page_list" class="table table-bordered table-striped table-vcenter js-dataTable-full dataTable no-footer" aria-describedby="page_list_info">
                        <thead>
                            <tr>
                                <th> Sl no.</th>
                                <th> District</th>
                                <th>No. of Threshers Procured under OMM</th>
                                <th>No. of Threshers Given to Mission Shakti SHGs</th>
                                <th>Target</th>
                                <th> SHG selected</th>
                                <th>SHG selection pending</th>
                                <th>No. of Threshers Given to OMM FPOs</th>
                               
                                <th class="text-right no-sort sorting_disabled" aria-label="Actions">Actions</th>
                            </tr>
                        </thead>
                        <tbody>

                            <tr class="odd">
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                               
                               

                                <td>
                                    <div class="btn-group btn-group pull-right"><a class="btn btn-sm btn-primary" href=""><i class="fa fa-pencil"></i></a></div>
                                </td>
                            </tr>

                        </tbody>
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
    var download_url = "http://ommsoev2.local/admin/enterprises/download";

    $(function() {
        function main() {
            var href = download_url + '?district_id=' + $('#districts').val() + '&month_id=' + $('#month').val() + '&year_id=' + $('#year').val() + '&period=' + $('#period').val();
            $('#btn-excel').attr('href', href);
        }
        $('#districts, #month, #year, #period').on('change', main);
        main();
    });



    $(document).ready(function() {
        var table = $('#page_list').DataTable({
            "paging": true,
            "pageLength": 10
        });
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
            if ($("#period").val() === "all") {
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
    })


    $('#uploader').dmUploader({
        dnd: false,
        url: '',
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
            if (data.status) {
                show_error('File uploaded successfully');
                console.log(data.data);
                $('.dm-uploader .status').addClass('text-success');
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