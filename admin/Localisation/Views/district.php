<div class="block">
    <div class="block-header block-header-default">
        <h3 class="block-title"><?= htmlspecialchars($heading_title) ?></h3>
        <div class="block-options">
            <a href="<?= htmlspecialchars($add) ?>" title="<?= htmlspecialchars($button_add) ?>" class="btn btn-primary">
                <i class="fa fa-plus"></i>
            </a>
            <button type="button" class="btn btn-danger" data-toggle="tooltip" title="<?= htmlspecialchars($button_delete) ?>" onclick="confirm('<?= htmlspecialchars($text_confirm) ?>') ? submitForm('form-district') : false;">
                <i class="fa fa-trash-o"></i>
            </button>
        </div>
    </div>
    <div class="block-content block-content-full">
        <form action="<?= htmlspecialchars($delete) ?>" method="post" enctype="multipart/form-data" id="form-district">
            <table id="district_list" class="table table-bordered table-striped table-vcenter js-dataTable-full">
                <thead>
                    <tr>
                        <th style="width: 1px;" class="text-center no-sort"><input type="checkbox" onclick="toggleCheckboxes('input[name*=\'selected\']')" /></th>
                        <th>District Name</th>
                        <th class="text-right no-sort">Actions</th>
                    </tr>
                </thead>
            </table>
        </form>
    </div>
</div>

<script type="text/javascript">
    function toggleCheckboxes(selector) {
        $(selector).prop('checked', $('#district_list input[type=checkbox]:first').prop('checked'));
    }

    function submitForm(formId) {
        $('#' + formId).submit();
    }

    $(function() {
        $('#district_list').DataTable({
            "processing": true,
            "serverSide": true,
            "columnDefs": [{
                targets: 'no-sort',
                orderable: false
            }],
            "ajax": {
                url: "<?= $datatable_url ?>",
                type: "post",
                error: function() {
                    $("#district_list").append('<tbody class="district_list_error"><tr><th colspan="5">No data found.</th></tr></tbody>');
                    $("#district_list_processing").css("display", "none");
                },
                dataType: 'json'
            },
        });

        function delete_district(title, id) {
            gbox.show({
                content: '<h2>Delete Manager</h2>Are you sure you want to delete this Manager?<br><b>' + title,
                buttons: {
                    'Yes': function() {
                        $.post('<?= admin_url('members.delete') ?>', {
                            user_id: id
                        }, function(data) {
                            if (data.success) {
                                gbox.hide();
                                $('#district_list').DataTable().ajax.reload();
                            } else {
                                gbox.show({
                                    content: 'Failed to delete this Manager.'
                                });
                            }
                        });
                    },
                    'No': gbox.hide
                }
            });
            return false;
        }
    });
</script>
