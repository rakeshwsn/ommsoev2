<div class="block">
    <form id="formfilter">
        <div class="block-header block-header-default">
            <h3 class="block-title"><?php echo $heading_title; ?></h3>
            <div class="block-options">
                <a href="<?php echo $addform; ?>" data-toggle="tooltip" title="<?php echo $button_add; ?>" class="btn btn-primary">Add Incentive</a>
                <a href="<?php echo $searchview; ?>" data-toggle="tooltip" title="<?php echo $button_view; ?>" class="btn btn-primary">View All Incentive</a>
            </div>
        </div>
        <div class="block-content block-content-full">
            <div class="row">
                <div class="col-md-12">
                    <table class="table table-bordered">
                        <tr>
                            <th>District</th>
                            <th>Block</th>
                            <th>Year</th>
                            <th>Season</th>
                            <th>Filter</th>
                        </tr>
                        <tr>
                            <?php 
                            $main = $user->district_id ? "disabled" : "";
                            ?>
                            <td>
                                <?php echo form_dropdown('district_id', option_array_value($districts, 'id', 'name',array("0"=>"select District")), set_value('district_id', $user->district_id),"id='district_id' class='form-control select2' $main"); ?>
                            </td>
                            <td>
                                <?php echo form_dropdown('block_id', option_array_value($blocks, 'id', 'name',array("0"=>"Select Block")), set_value('block_id', ''),"id='block_id' class='form-control select2'"); ?>
                            </td>
                            <td>
                                <select class="form-control" id="year" name="year" required>
                                    <option value="">select</option>
                                    <option value="1">2017-18</option>
                                    <option value="2">2018-19</option>
                                    <option value="3">2
