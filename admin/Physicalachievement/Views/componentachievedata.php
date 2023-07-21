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
                            <option value="">select</option>
                            <option value="2" selected>2023-24</option>
                            <!-- <option value="2">2024-25</option> -->
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label>Month</label>
                        <select class="form-control" id="month_id" name="month_id">
                            <option value="">select</option>
                            <?php foreach ($get_months as $get_month) { ?>
                                <option value="<?= $get_month['number'] ?>" <?php if (date('n') - 1 == $get_month['number']) echo 'selected'; ?>>
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

            <h3 class="block-title float-left">Acheivements Data</h3>
            <?php if (!$user->district_id) { ?>
                <button id="btnExport"><i class="fa fa-download" aria-hidden="true"></i>
                </button>
                <div class="block-options float-right">
                    <a href="<?php echo $addach; ?>" data-toggle="tooltip" title="Add" class="btn btn-primary"><i class="fa fa-plus"></i></a>
                </div>

            <?php } ?>
        </div>

        <div class="block-content block-content-full" style="overflow-y: scroll;">
            <table class="table table-bordered table-striped table-vcenter js-dataTable-full" id="testTable">
                <colgroup width="26"></colgroup>
                <colgroup width="112"></colgroup>
                <colgroup width="63"></colgroup>
                <colgroup span="18" width="67"></colgroup>
                <tr>
                    <td colspan=20 height="57"><b>
                            <font face="Verdana" size=4 color="#000000">Target vs Ach. for different Training programme, Awareness and other activity under OMM during 2023-24
                        </b></td>
                    <td align="left" valign=middle><b>
                            <font face="Verdana" size=4 color="#000000"><br>
                        </b></td>
                </tr>
                <tr>
                    <td colspan=4 height="46"><b>
                            <font face="Arial" size=3 color="#000000">Month- May-2023
                        </b></td>

                </tr>
                <tr>
                    <td colspan=3 height="101"><b>Componet details</b></td>
                    <?php foreach ($componentsAll as $componentsAlls) { ?>
                        <td colspan=3 height="101"><b><?php echo $componentsAlls['description'] ?></b></td>
                    <?php } ?>
                </tr>
                <tr>
                    <td height="35" align="left" valign=middle><b>SN</b></td>
                    <td align="left" valign=middle><b>Name of the District</b></td>
                    <td align="left" valign=middle><b>No. of Blocks</b></td>
                    <td><b>Physical Target</b></td>
                    <td colspan=2><b>Ach.<br></b></td>
                    <td><b>Physical Target</b></td>
                    <td colspan=2><b>Ach.<br></b></td>
                    <td><b>Physical Target</b></td>
                    <td colspan=2><b>Ach.<br></b></td>
                    <td><b>Physical Target</b></td>
                    <td colspan=2><b>Ach.<br></b></td>
                    <td><b>Physical Target</b></td>
                    <td colspan=2><b>Ach.<br></b></td>
                </tr>
                <tr>
                    <td height="19"><b><br></b></td>
                    <td align="left" valign=middle><b><br></b></td>
                    <td><b><br></b></td>
                    <td><b><br></b></td>
                    <?php foreach ($componentsAll as $componentsAlls) { ?>
                        <td sdval="45039" sdnum="1033;0;MMMM-D"><b>Cumulative</b></td>
                        <td sdval="45069" sdnum="1033;0;MMMM-D"><b>May-23</b></td>
                        <td><b><br></b></td>
                    <?php } ?>

                </tr>

                <?php
                $index = 1;
                foreach ($target_acv_data as $target_acv_datas) { ?>
                    <tr>
                        <td height="19" sdval="1" sdnum="1033;"><?php echo $index++ ?></td>
                        <td align="left" valign=middle><?php echo $target_acv_datas['district'] ?></td>
                        <td sdval="5" sdnum="1033;"><?php echo $target_acv_datas['total_block'] ?></td>
                        <?php $arraysecond = $target_acv_datas['arraysecond']; ?>
                        <?php foreach ($arraysecond as $value) { ?>
                            <td><?php echo $value; ?></td>
                        <?php } ?>
                    </tr>
                <?php } ?>
                <?php
                // printr(array_map(null, ...array_column($target_acv_data, 'arraysecond')));
                //exit;
                ?>
                <tr>
                    <td colspan="2" height="21"><b>Total</b></td>
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