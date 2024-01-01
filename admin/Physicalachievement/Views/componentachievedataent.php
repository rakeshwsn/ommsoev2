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
                    <div class="col-md-3">
                        <label>Year</label>
                        <select class="form-control" id="year_id" name="year_id">
                            <?php foreach ($allYears as $allYear) { ?>
                                <option value="<?php echo $allYear->id; ?>" <?php if ($year_id == $allYear->id) echo 'selected'; ?>><?php echo $allYear->name; ?></option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label>Month</label>
                        <select class="form-control" id="month_id" name="month_id">
                            <option value="">select</option>
                            <?php foreach ($get_months as $get_month) { ?>
                                <option value="<?= $get_month['number'] ?>" <?php if ($monthId == $get_month['number']) echo 'selected'; ?>>
                                    <?= $get_month['name'] ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="col-md-2" style="margin: 23px;">
                        <button id="btn-filter" class="btn btn-outline btn-primary"><i class="fa fa-filter"></i> Filter</button>
                    </div>

                </div>

            </div>
        </div>
    </form>

    <div class="block block-themed">

        <div class="block-header bg-muted">

            <h3 class="block-title float-left">Acheivements Data Enterprises</h3>
            <?php if (!$user->district_id) { ?>
                <button id="btnExport"><i class="fa fa-download" aria-hidden="true"></i>
                </button>
                <div class="block-options float-right">
                    <a href="<?php echo $addach; ?>" data-toggle="tooltip" title="Add" class="btn btn-primary"><i class="fa fa-plus"></i></a>
                </div>
            <?php } ?>
            <?php if (!empty($user->district_id) && $checkExists != 0) { ?>
                <div class="block-options float-right">
                    <a href="<?php echo $addach; ?>" data-toggle="tooltip" title="Add" class="btn btn-primary"><i class="fa fa-plus"></i></a>
                </div>
            <?php } ?>
        </div>

        <div class="block-content block-content-full" style="overflow-y: scroll;">
            <table class="table table-bordered table-striped table-vcenter js-dataTable-full" id="testTable">
                <thead>
                    <tr>
                        <td colspan="<?php echo count($componentsAll) * 9 + 3; ?>">Target vs Ach. for different Training programme, Awareness and other activity under OMM during <?php echo $year_name?></td>

                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="<?php echo count($componentsAll) * 9 + 3; ?>">Month- <?php echo $getMonths['name'] ?>-<?php echo $first_year_name?></td>

                    </tr>
                    <tr>
                        <td colspan="3">Componet details</td>
                        <?php foreach ($componentsAll as $componentsAlls) { ?>
                            <td colspan="7"><?php echo $componentsAlls['description'] ?></td>
                        <?php } ?>
                    </tr>
                    <tr>
                        <th rowspan="3">SN</th>
                        <th rowspan="3">Name of the District</th>
                        <th rowspan="3">No. of Blocks</th>
                        <?php foreach ($componentsAll as $componentsAlls) { ?>
                            <td rowspan="3">Physical Target</td>
                            <td colspan="6">Ach.</td>
                        <?php } ?>
                    </tr>
                    <tr>

                        <?php foreach ($componentsAll as $componentsAlls) { ?>
                            <td colspan="2">Up to prev.</td>
                            <td colspan="2"><?php echo $getMonths['name'] ?></td>
                            <td colspan="2">Cumulative<br></td>
                        <?php } ?>
                    </tr>

                    <tr>

                        <?php foreach ($componentsAll as $componentsAlls) { ?>
                            <td>Fpo</td>
                            <td>wshg</td>
                            <td>Fpo</td>
                            <td>wshg</td>
                            <td>Fpo</td>
                            <td>wshg</td>
                        <?php } ?>
                    </tr>
                    <?php
                $index = 1;
                foreach ($target_acv_data as $target_acv_datas) { ?>
                    <tr>
                        <td><?php echo $index++ ?></td>
                        <td><?php echo $target_acv_datas['district'] ?></td>
                        <td><?php echo $target_acv_datas['total_block'] ?></td>
                        <?php $arraysecond = $target_acv_datas['arraysecond']; ?>
                        <?php foreach ($arraysecond as $value) { ?>
                        <td><?php echo $value; ?></td>
                        <?php } ?>
                    </tr>
                    <?php } ?>

                    <tr>
                    <td colspan="2">Total</td>
                    <td sdval="<?php echo array_sum(array_column($target_acv_data, 'total_block')); ?>" sdnum="1033;"><b><?php echo array_sum(array_column($target_acv_data, 'total_block')); ?></b></td>
                    <?php if (!$user->district_id) { ?>

                        <?php
                        foreach (array_map('array_sum', array_map(null, ...array_column($target_acv_data, 'arraysecond'))) as $sumValue) { ?>
                            <td sdval="<?php echo $sumValue; ?>" sdnum="1033;"><b><?php echo $sumValue; ?></b></td>
                        <?php } ?>

                    <?php } else { ?>
                        <?php
                        $sumValues = array_map('intval', array_map(null, ...array_column($target_acv_data, 'arraysecond')));

                        foreach ($sumValues as $sumValue) { ?>
                            <td sdval="<?php echo $sumValue; ?>" sdnum="1033;"><b><?php echo $sumValue; ?></b></td>
                        <?php } ?>

                    <?php } ?>
                    </tr>

                </tbody>
            </table>

        </div>
    </div>

</section>


<?php js_start(); ?>
<script src="https://cdn.jsdelivr.net/gh/linways/table-to-excel@v1.0.4/dist/tableToExcel.js"></script>
<script>
    $(document).ready(function() {
        $("#btnExport").click(function() {
            let table = document.getElementsByTagName("table");
            TableToExcel.convert(table[0], { // html code may contain multiple tables so here we are refering to 1st table tag
                name: `export.xlsx`, // fileName you could use any name
                sheet: {
                    name: 'Sheet 1' // sheetName
                }
            });
        });
    });
</script>
<?php js_end(); ?>