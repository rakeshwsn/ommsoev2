<div class="row">
    <div class="col-4">
        <div class="block">
            <div class="block-header block-header-default"> Enterprises</div>
            <div class="content">
                <?= $form ?>
            </div>
        </div>
    </div>

    <div class="col-8">
        <div class="block">
            <div class="block-header block-header-default"> Enterprises List</div>
            <div class="content">
                <table class="table" id="datatable">
                    <thead>
                        <tr>
                            <th scope="col">slno</th>
                            <th scope="col">Unit Name</th>
                            <th scope="col">Group Unit </th>
                            <th scope="col">Total</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($e_units as $key => $unit): ?>
                        <tr>
                            <td><?=++$key?></td>
                            <td><?=$unit['name']?></td>
                            <td><?=$unit['unit_group']?></td>
                            <td><?=$unit['total_units']?></td>
                            <td><?=$unit['action']?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php js_start(); ?>
<script type="text/javascript">
    $(function() {
       $('#datatable').DataTable();
    });
</script>
<?php js_end(); ?>