<div class="row">
    <div class="col-8">
        <div class="block">
            <div class="block-header block-header-default">
                <h3 class="block-title"><?php echo $heading_title; ?></h3>
            </div>
            <div class="block-content block-content-full">
                <!-- DataTables functionality is initialized with .js-dataTable-full class in js/users/be_tables_datatables.min.js which was auto compiled from _es6/users/be_tables_datatables.js -->
                <table id="user_list" class="table table-bordered table-striped table-vcenter js-dataTable-full">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Date</th>
                            <th>Letter no</th>
                            <th>Name</th>
                            <th>Subject</th>
                            <th class="text-right no-sort">Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
    <div class="col-4">
        <form method="post" id="letter-form" onsubmit="return false;">
        <div class="block">
            <div class="block-header block-header-default">
                <h3 class="block-title">Add New Letter</h3>
            </div>
            <div class="block-content block-content-full">
                <div class="form-group row">
                    <label class="col-md-3 col-form-label" for="letter-no">Letter no</label>
                    <div class="col-md-9">
                        <?php echo form_input(array('class'=>'form-control','name' => 'letter_no', 'id' => 'letter-no','readonly'=>'true','value' => set_value('letter_no', $letter_no))); ?>
                    </div>
                </div>

                <div class="form-group row required">
                    <label class="col-md-3 control-label" for="input-email">Subject</label>
                    <div class="col-md-9">
                        <?php echo form_input(array('class'=>'form-control','name' => 'subject', 'id' => 'subject','value' => set_value('subject', $subject))); ?>
                    </div>
                </div>

                <div class="form-group row required">
                    <label class="col-md-3 control-label" for="input-user-group">User </label>
                    <div class="col-md-9">
                        <?php echo form_dropdown('user', option_array_value($users, 'id', 'user_name'), set_value('user_id', $user_id),"id='input-user-group' class='form-control js-select2'"); ?>
                    </div>
                </div>
                <div class="text-sm-right push">
                    <button id="btn-submit" class="btn btn-info mg-r-5">Add</button>
                </div>
            </div>
        </div>
        </form>
    </div>
</div>
<?php js_start(); ?>
<script type="text/javascript"><!--
    var letters;
$(function(){
	letters = $('#user_list').DataTable({
		"processing": true,
		"serverSide": true,
		"columnDefs": [
			{ targets: [0], visible: false },
			{ targets: 'no-sort', orderable: false }
		],
        'order':[[0,'desc']],
		"ajax":{
			url :"<?=$datatable_url?>", // json datasource
			type: "get",  // method  , by default get
			error: function(){  // error handling
				$(".user_list_error").html("");
				$("#user_list").append('<tbody class="user_list_error"><tr><th colspan="5">No data found.</th></tr></tbody>');
				$("#user_list_processing").css("display","none");
			},
			dataType:'json'
		},
	});

    $('#btn-submit').click(function () {
        $.ajax({
            url:'<?=$add_url?>',
            data:$('#letter-form').serializeArray(),
            type:'POST',
            dataType:'JSON',
            beforeSend:function (xhr, opts) {
                $('.error').text('');
                $('#letter-form input,#letter-form select').each(function (k,v) {
                    if($(v).val()==''){
                        $(v).next('.error').text('This is required');
                        xhr.abort();
                    }
                })
            },
            success:function (data) {
                if(data.success){
                    $('[name="letter_no"]').val(data.next_letter_no);
                    letters.ajax.reload();
                } else {
                    alert('Unable to submit');
                }
            },
            error:function () {
                alert('Something went wrong');
            },
            complete:function () {
                $('.error').text('');
                $('#subject').val('');
            },
        })
    });

    $(document).on('click','.btn-delete',function (e) {
        e.preventDefault();
        btn = $(this);
        var id = $(this).data('id');
        var letter = $(this).data('letter');
        if(confirm('Are you sure to delete letter no: '+letter)==true){
            offset = 0;
            $.ajax({
                url:'<?=$delete_url?>',
                data:{'id':id},
                type:'POST',
                dataType:'JSON',
                success:function (data) {
                    if(data.success){
                        letters.ajax.reload();
                        $('[name="letter_no"]').val(data.next_letter_no);
                    }
                },
                error:function (data) {
                    alert('Could not load data');
                },
            });
        }
    });
});
//--></script>
<?php js_end(); ?>