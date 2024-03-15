<h2 class="content-heading">MIS</h2>

<form action="your_action_url_here" method="post" novalidate>
    <div class="block" id="upload-controls">
        <div class="block-content block-content-full">
            <div class="row">
                <div class="col-md-2">
                    <label for="year">Choose Year</label>
                    <select class="form-control" id="year" name="year">
                        <option value="">Select</option>
                        <?php foreach ($years as $year) { ?>
                            <option value="<?= $year['id'] ?>"><?= $year['name'] ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="month">Choose Month</label>
                    <select class="form-control" id="month" name="month">
                        <option value="">Select</option>
                        <?php foreach ($months as $month) { ?>
                            <option value="<?= $month['id'] ?>"><?= $month['name'] ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-md-2">
                    <button type="button" id="btn-add" class="btn btn-outline btn-primary"><i class="fa fa-table" aria-hidden="true" title="Add New"></i> Add New</button>
                </div>
            </div>
        </div>
    </div>
</form>

<div class="block">
    <div class="block-content block-content-full">
        <table class="table table-bordered table-striped table-vcenter js-dataTable-full" id="datatable" style="display: none;">
            <thead>
            <tr>
                <th>ID</th>
                <th>Month</th>
                <th>Year</th>
                <th>Date Added</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
</div>

<script type="text/javascript" defer>
    $(function () {
        $('#datatable').dataTable({
            processing: true,
            serverSide: true,
            responsive: false,
            filter: false,
            columnDefs: [
                { targets: [3, 4], orderable: false },
                { targets: [0], visible: false },
            ],
            order: [0, 'desc'],
            ajax: {
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                url: "your_datatable_url_here",
                type: "post",
                dataType: 'json',
                beforeSend: function () {
                    $("#main-container").LoadingOverlay("show");
                },
                error: function () {
                    $(".datatable-error").html("");
                    $("#datatable tbody").html('<tr><th colspan="3">No data found.</th></tr>');
                    $("#datatable_processing").css("display", "none");
                },
                complete: function () {
                    $("#main-container").LoadingOverlay("hide");
                }
            },
        });

        $(document).on('click', '.btn-delete', function (e) {
            if (confirm('Are you sure, you want to delete this record?') === false) {
                e.preventDefault();
            }
        });
    });

    var add_url = 'your_add_url_here';

    $('#btn-add').click(function (e) {
        e.preventDefault();
        setLocation(add_url);
    });

    function setLocation(url) {
        var _year = $('#year').val();
        var month = $('#month').val();
        var block_id = $('#block_id').val() || '';
