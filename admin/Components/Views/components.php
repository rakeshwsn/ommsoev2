<div class="block">
    <div class="block-header block-header-default">
        <h3 class="block-title"><?php echo $heading_title; ?></h3>
        <div class="block-options">
            <a href="<?php echo $add; ?>" data-toggle="tooltip" title="" class="btn btn-primary"><i class="fa fa-plus"></i></a>
            <button type="button" data-toggle="tooltip" title="" class="btn btn-danger" onclick="confirm('Are you sure to delete !') ? $('#form-components').submit() : false;"><i class="fa fa-trash-o"></i></button>
        </div>
    </div>
    <div class="block-content block-content-full">
        <form action="<?php echo $delete; ?>" method="post" enctype="multipart/form-data" id="form-components">
            <table id="datatable" class="table table-bordered table-striped table-vcenter js-dataTable-full">
                <thead>
                <tr>
                    <th style="width: 1px;" class="text-center no-sort"><input type="checkbox" onclick="$('input[name*=\'selected\']').prop('checked', this.checked);" /></th>
                    <th>Name</th>
                    <th>Slug</th>
                    <th>Type</th>
                    <th>Category</th>
                    <th>Tags</th>
                    <th class="text-right no-sort">Actions</th>
                </tr>
                </thead>
            </table>
        </form>
    </div>
</div>
<?php js_start(); ?>
<script type="text/javascript"><!--
    $(function(){
        $('#datatable').DataTable({
            "processing": true,
            "serverSide": true,
            "columnDefs": [
                { targets: 'no-sort', orderable: false }
            ],
            "ajax":{
                url :"<?=$datatable_url?>", // json datasource
                type: "post",  // method  , by default get
                error: function(){  // error handling
                    $(".datatable_error").html("");
                    $("#datatable").append('<tbody class="datatable_error"><tr><th colspan="7">No data found.</th></tr></tbody>');
                    $("#datatable_processing").css("display","none");
                },
                dataType:'json'
            },
        });
    });

    //--></script>
<?php js_end(); ?>