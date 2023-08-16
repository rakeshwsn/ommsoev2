
<style>
 .block-content {
        width: 100%;
    }

    .table-container {
        overflow-y: scroll;
    }

    .table-header {
        display: flex;
        background-color: #f2f2f2;
        font-weight: bold;
        padding: 10px;
    }

    .header-cell {
        flex: 1;
        padding: 10px;
        text-align: center;
    }

    .table-body {
        overflow: auto;
    }

    .table-row {
        display: flex;
        border-bottom: 1px solid #ccc;
    }

    .table-row:last-child {
        border-bottom: none;
    }

    .table-cell {
        flex: 1;
        padding: 10px;
        text-align: center;
    }

    .table-cell a.btn {
        display: inline-block;
        padding: 5px;
        text-decoration: none;
    }
</style>

<?php
$user  = service('user');
?>
<section class="content">
    <form>

        <div class="block block-themed">
            <div class="block-header bg-primary">
                <h3 class="block-title">Filter</h3>
            </div>
            <div class="block-content block-content-full">
                <div class="row">
                    <div class="col-md-4">
                        <label>Year</label>
                        <select class="form-control" id="year_id" name="year_id">
                        <?php foreach($allYears as $allYear){?>
                                <option value="<?php echo $allYear->id;?>" <?php if ($year_id == $allYear->id) echo 'selected'; ?>><?php echo $allYear->name;?></option>
                                <?php } ?>
                        </select>
                    </div>
                    <div class="col-md-4" style="margin: 23px;">
                        <button id="btn-filter" class="btn btn-outline btn-primary"><i class="fa fa-filter"></i> Filter</button>
                    </div>
                    <?php if ($user->district_id && $checkExists!=0) { ?>

                    <div class="col-md-3" style="margin: 23px;">
                    <a href="<?php echo $addachForm; ?>" data-toggle="tooltip" title="Add" class="btn btn-outline btn-primary"><i class="fa fa-plus"></i> Add Achivements</a>
                    </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </form>

    <div class="block block-themed">

        <div class="block-header bg-muted">

            <h3 class="block-title float-left">Physical Components Target</h3>
            <?php if (!$user->district_id) { ?>
                <div class="block-options float-right">
                    <a href="<?php echo $add; ?>" data-toggle="tooltip" title="Add" class="btn btn-primary"><i class="fa fa-plus"></i></a>
                </div>

            <?php } ?>

            <?php if ($user->district_id && $checkExists!=0) { ?>
                <!-- <div class="block-options float-right">
                    <a href="<?php echo $addachForm; ?>" data-toggle="tooltip" title="Add" class="btn btn-primary"><i class="fa fa-plus"></i></a>
                </div> -->

            <?php } ?>
        </div>

        <div class="block-content block-content-full" style="overflow-y: scroll;">
            <table class="table table-bordered table-striped table-vcenter js-dataTable-full">
                <thead>
                    <tr>
                        <?php foreach ($headers as $header) : ?>
                            <td><?= $header ?></td>

                        <?php endforeach; ?>
                        <!-- <td height="101">Total</td> -->
                        <?php if (!$user->district_id) { ?> <td>Action</td> <?php } ?>
                    </tr>
                </thead>
                <?php
                if (!empty($target_data)) {
                    $serino = 1;
                ?>
                    <tbody>
                        <?php
                        $sum = 0;
                        foreach ($target_data as $key => $targetDatas) : ?>
                            <tr>
                                <td><?php echo $serino++; ?></td>
                                <td><?php echo $key; ?></td>
                                <?php $sum = 0; ?>
                                <?php foreach ($targetDatas as $row) : ?>
                                    <td class="totaldata"><?php echo $row['total']; ?></td>
                                    <?php $sum += $row['total']; ?>
                                <?php endforeach; ?>
                                <td><?php echo $sum; ?></td>
                                <?php if (!$user->district_id) { ?>
                                    <td>
                                        <?php if ($row['mprcomponents_master_id']) { ?>
                                            <a class="btn btn-sm btn-primary" href="<?php echo base_url('admin/Physicalcomponentstarget/edit/' . $row['mprcomponents_master_id']); ?>"><i class="fa fa-pencil"></i></a>
                                        <?php } ?>
                                    </td>
                                <?php } ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>

                <?php
                } else {
                    echo "<h1>No data available.</h1>";
                }
                ?>

                <tfoot>

                </tfoot>
            </table>
        </div>
    </div>

</section>

<?php js_start(); ?>
<script>
    $(document).ready(function() {
        var columnTotals = [];
        <?php foreach ($headers as $header) : ?>
            columnTotals.push(0);
        <?php endforeach; ?>

        $('tbody tr').each(function() {
            var columnIndex = 0;
            $(this).find('td').each(function() {
                var header = $(this).data('header');
                var value = parseFloat($(this).text());
                if (!isNaN(value)) {
                    columnTotals[columnIndex] += value;
                }
                columnIndex++;
            });
        });

        var allColumnSum = 0;
        $('tfoot tr td').each(function(index) {
            if (index < columnTotals.length) {
                $(this).text(columnTotals[index].toFixed(2));
                allColumnSum += columnTotals[index];
            }
        });
        $('#countall').text(allColumnSum.toFixed(2));
    });
</script>
<?php js_end(); ?>