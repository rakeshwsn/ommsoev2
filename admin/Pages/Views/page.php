<div class="block">
    <div class="block-header block-header-default">
        <h3 class="block-title"><?= htmlspecialchars($heading_title) ?></h3>
        <div class="block-options">
            <a href="<?= htmlspecialchars($add) ?>" class="btn btn-primary" title="<?= htmlspecialchars($button_add) ?>" data-toggle="tooltip">
                <i class="fa fa-plus"></i>
            </a>
            <button type="button" class="btn btn-danger" title="<?= htmlspecialchars($button_delete) ?>" data-toggle="tooltip" onclick="confirm('<?= htmlspecialchars($text_confirm) ?>') ? submitForm('form-pages') : false;">
                <i class="fa fa-trash-o"></i>
            </button>
        </div>
    </div>
    <div class="block-content block-content-full">
        <form action="<?= htmlspecialchars($delete) ?>" method="post" enctype="multipart/form-data" id="form-pages">
            <table id="page_list" class="table table-bordered table-striped table-vcenter js-dataTable-full">
                <thead>
                    <tr>
                        <th style="width: 1px;" class="text-center no-sort"><input type="checkbox" onclick="toggleCheckboxes('input[name*=\'selected\']')" /></th>
                        <th>Title</th>
                        <th>URL</th>
                        <th>Template</th>
                        <th>Status</th>
                        <th class="text-right no-sort">Actions</th>
                    </tr>
                </thead>
            </table>
        </form>
    </div>
</div>

<script type="text/javascript">
    function toggleCheckboxes(selector) {
        $(selector).prop('checked', $(selector).first().prop('checked'));
    }

    function submitForm(formId) {
        const form = document.getElementById(formId);
        form.submit();
    }

    $(function() {
        const table = $('#page_list').DataTable({
            processing: true,
            serverSide: true,
            columnDefs: [{
                targets: 'no-sort',
                orderable: false
            }],
            ajax: {
                url: '<?= $datatable_url ?>',
                type: 'post',
                error: function() {
                    $('#page_list').append('<tbody class="page_list_error"><tr><th colspan="5">No data found.</th></tr></tbody>');
                    $("#page_list_processing").css("display", "none");
                },
                dataType: 'json'
            },
        });

        function deletePage(title, id) {
            const gbox = new Gbox({
                content: `<h2>Delete Manager</h2>Are you sure you want to delete this Manager?<br><b>${title}`
            });

            gbox.show();

            gbox.buttons = {
                'Yes': function() {
                    $.post('<?= admin_url('members.delete') ?>', {
                        user_id: id
                    }, function(data) {
                        if (data.success) {
                            gbox.hide();
                            table.ajax.reload();
                        } else {
                            gbox.show({
                                content: 'Failed to delete this Manager.'
                            });
                        }
                    });
                },
                'No': gbox.hide
            };
        }

        $('#page_list').on('click', 'a.delete-page', function(e) {
            e.preventDefault();
            const link = $(this);
            const title = link.data('title');
            const id = link.data('id');

            deletePage(title, id);
        });
    });
</script>
