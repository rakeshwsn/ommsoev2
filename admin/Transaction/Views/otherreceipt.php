<!-- Main content -->
<section class="content">
    <h2 class="content-heading">Other Receipt</h2>
    <div id="message"></div>
    <div class="block" id="upload-controls">
            <div class="block-content block-content-full">
                <div class="row">
                    <div class="col-2">
                        <select class="form-control" id="year" name="year">
                            <option value="">Choose Year</option>
                            <?php foreach ($years as $_year) { ?>
                                <option value="<?=$_year['id']?>" <?php if($year==$_year['id']) {echo 'selected';} ?>><?=$_year['name']?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="col-2">
                        <select class="form-control" id="month" name="month">
                            <option value="">Choose Month</option>
                            <?php foreach ($months as $_month) { ?>
                                <option value="<?=$_month['id']?>" <?php if($month==$_month['id']) {echo 'selected';} ?>><?=$_month['name']?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <?php if($agency_types): ?>
                    <div class="col-2">
                        <select class="form-control" name="agency_type_id" id="agency-type">
                            <?php foreach ($agency_types as $agency_type): ?>
                                <option value="<?=$agency_type['id']?>" <?php if ($agency_type['id']==$agency_type_id){echo 'selected';} ?>><?=$agency_type['name']?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <?php endif; ?>

                    <div class="col-2">
                        <button class="btn btn-primary" id="add-new"><i class="si-plus"></i> New</button>
                    </div>
                </div>
            </div>
        </div>

    <div class="block">
        <div class="block-content block-content-full">
            <table class="table table-bordered table-striped table-vcenter js-dataTable-full" id="datatable">
                <thead>
                <tr>
                    <th>Month</th>
                    <th>Year</th>
                    <th>Agency Type</th>
                    <th>Fund Agency</th>
                    <th>Date Added</th>
                    <th>Credit</th>
                    <th>Debit</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
                </thead>
            </table>
        </div>
    </div>

</section>
<!-- content -->
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
        $('#datatable').dataTable({
            "processing": true,
            "serverSide": true,
            "responsive": false,
            "filter":false,
            "columnDefs": [
                { targets: [3,4,5,6], orderable: false },
                { targets: [], visible: false },
            ],
            "ajax":{
                headers: {'X-Requested-With': 'XMLHttpRequest'},
                url :"<?=$datatable_url?>", // json datasource
                type: "get",  // method  , by default get
                dataType:'json',
                beforeSend:function () {
                    $("#main-container").LoadingOverlay('show');
                },
                error: function(){  // error handling
                    $(".datatable-error").html("");
                    $("#datatable").append('<tbody class="datatable-error"><tr><th colspan="3">No data found.</th></tr></tbody>');
                    $("#datatable_processing").css("display","none");

                },
                complete:function () {
                    $("#main-container").LoadingOverlay("hide");
                }
            }
        });
        
        //add new
        $('#add-new').click(function () {
            if($('#month').val()==''){
                $('#res-message').text('Please choose a month');
                return false;
            } else if($('#year').val()==''){
                $('#res-message').text('Please choose a year');
                return false;
            }
            ati = $('#agency-type').val() || '';
            fai = $('#fund_agency_id').val() || '';
            $.ajax({
                headers: {'X-Requested-With': 'XMLHttpRequest'},
                data: {month:$('#month').val(),year:$('#year').val(),agency_type_id:ati,fund_agency_id:fai},
                url :"<?=$add_url?>", // json datasource
                type: "get",  // method  , by default get
                dataType:'json',
                beforeSend:function () {
                    $("#main-container").LoadingOverlay('show');
                    $('#res-message').text('');
                },
                success:function (json) {
                    if(json.status==false){
                        html = '<div class="alert alert-danger alert-dismissable" role="alert">'+
                            '<button type="button" class="close" data-dismiss="alert" aria-label="Close">'+
                            '<span aria-hidden="true">×</span>'+
                        '</button>'+
                        '<h3 class="alert-heading font-size-h4 font-w400">Alert</h3>'+
                            '<p class="mb-0">'+json.message+'</p>'+
                        '</div>';
                        $('#message').html(html);
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
                    $("#main-container").LoadingOverlay("hide");
                    numOnly();
                    decimalOnly();
                }
            });
        });
    });

    //submit new form
    $(document).on('click','#btn-add',function () {
        formdata = $(this).closest('.modal-content').find('form').serialize();
        month = $('#month').val()||'';
        year = $('#year').val()||'';
        agency_type_id = $('#agency-type').val()||'';
        fund_agency_id = $('#fund_agency_id').val()||'';
        $.ajax({
            headers: {'X-Requested-With': 'XMLHttpRequest'},
            url:'<?=$add_url?>?month='+month+'&year='+year+'&agency_type_id='+agency_type_id+'&fund_agency_id='+fund_agency_id,
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

    //submit edit
    $(document).on('click','#btn-edit',function () {
        formdata = $(this).closest('.modal-content').find('form').serialize();

        $.ajax({
            headers: {'X-Requested-With': 'XMLHttpRequest'},
            url:url,
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
                numOnly();
                decimalOnly();
            }
        })
    });

    //get edit form
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
                $("#main-container").LoadingOverlay("hide");
            },
            complete:function () {
                $("#main-container").LoadingOverlay("hide");
            }
        });
    });

    //rakesh
    function numOnly() {
        //input type text to number
        // Get the input field
        var input = $('.physical');

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
