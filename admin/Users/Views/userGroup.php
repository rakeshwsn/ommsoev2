<div class="block">
    <div class="block-header block-header-default">
        <?php if (isset($heading_title)): ?>
            <h3 class="block-title"><?php echo $heading_title; ?></h3>
        <?php endif; ?>
        <div class="block-options">
            <?php if (isset($add)): ?>
                <a href="<?php echo $add; ?>" data-toggle="tooltip" title="<?php echo $button_add; ?>" class="btn btn-primary"><i class="fa fa-plus"></i></a>
            <?php endif; ?>
            <button type="button" data-toggle="tooltip" title="<?php echo $button_delete; ?>" class="btn btn-danger" onclick="confirm('<?php echo isset($text_confirm) ? $text_confirm : ''; ?>') ? $('#form-usergroup').submit() : false;"><i class="fa fa-trash-o"></i></button>
        </div>
    </div>
    <div class="block-content block-content-full">
        <form action="<?php echo isset($delete) ? $delete : ''; ?>" method="post" enctype="multipart/form-data" id="form-usergroup">
            <table id="usergroup_list" class="table table-bordered table-striped table-vcenter js-dataTable-full">
                <thead>
                    <tr>
                        <th style="width: 1px;" class="text-center no-sort"><input type="checkbox" onclick="$('input[name*=\'selected\']').prop('checked', this.checked);" /></th>
                        <th>Name</th>
                        <th>Agency</th>
                        <th>Status</th>
                        <th class="text-right no-sort">Actions</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </form>
    </div>
</div>

<?php if (isset($datatable_url)): ?>
    <?php js_start(); ?>
    <script type="text/javascript"><!--
    $(function(){
        $('#usergroup_list').DataTable({
            "processing": true,
            "serverSide": true,
            "columnDefs": [
                { targets: 'no-sort', orderable: false }
            ],
            "ajax":{
                url :"<?=$datatable_url?>", // json datasource
                type: "post",  // method  , by default get
                error: function(){  // error handling
                    $(".usergroup_list_error").html("");
                    $("#usergroup_list").append('<tbody class="usergroup_list_error"><tr><th colspan="5">No data found.</th></tr></tbody>');
                    $("#usergroup_list_processing").css("display","none");
                },
                dataType:'json'
            },
        });
    });
    //--></script>
    <?php js_end(); ?>
<?php endif;
