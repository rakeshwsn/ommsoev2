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
                    <div class="col-md-2" style="margin: 23px;">
                        <button id="btn-filter" class="btn btn-outline btn-primary"><i class="fa fa-filter"></i> Filter</button>
                    </div>

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
                <div class="block-options float-right">
                    <a href="<?php echo $addachForm; ?>" data-toggle="tooltip" title="Add" class="btn btn-primary"><i class="fa fa-plus"></i></a>
                </div>

            <?php } ?>
        </div>


        <div class="block-content block-content-full" style="overflow-x: scroll;">
            <table class="table table-bordered table-vcenter">
                <thead>
                    <tr>
                        <?php foreach ($headers as $header) : ?>
                            <th><?= $header ?></th>
                        <?php endforeach; ?>
                        <?php if (!$user->district_id) { ?> <th>Action</th> <?php } ?>
                    </tr>
                </thead>
                <?php
                if (!empty($target_data)) {
                    $serino = 1;
                ?>

                    <tbody>
                        <?php foreach ($target_data as $key => $targetDatas) : ?>
                            <tr>
                                <td><?php echo $serino++; ?></td>
                                <td><?php echo $key; ?></td>
                                <?php foreach ($targetDatas as $row) : ?>
                                    <td><?php echo $row['total']; ?></td>
                                <?php endforeach; ?>
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
                    // Handle the case when $target_data is empty or not defined
                    echo "<h1>No data available.</h1>";
                }
                ?>

                <tfoot>

                </tfoot>
            </table>
        </div>
    </div>

</section>