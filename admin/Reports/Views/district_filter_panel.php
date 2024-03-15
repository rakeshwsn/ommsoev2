<form method="post" action="submit_form.php" novalidate>
    <div class="block">
        <div class="block-content block-content-full">
            <div class="row">
                <div class="col-md-12">
                    <table class="table table-bordered">
                        <tr>
                            <th>Year</th>
                            <th>Month</th>
                            <?php if($blocks): ?>
                            <th>Block</th>
                            <?php endif; ?>
                            <th>Agency Type</th>
                            <th>Filter</th>
                        </tr>
                        <tr>
                            <td>
                                <label for="year">Year</label>
                                <select class="form-control" id="year" name="year" required>
                                    <?php foreach ($years as $year) { ?>
                                        <option value="<?=$year['id']?>" <?=($year['id']==$year_id)?'selected':''?>><?=$year['name']?></option>
                                    <?php } ?>
                                </select>
                            </td>
                            <td>
                                <label for="month">Month</label>
                                <select class="form-control" id="month" name="month">
                                    <?php foreach ($months as $month) { ?>
                                        <option value="<?=$month['id']?>" <?=($month['id']==$month_id)?'selected':''?>><?=$month['name']?></option>
                                    <?php } ?>
                                </select>
                            </td>
                            <?php if($blocks): ?>
                            <td>
                                <label for="block_id">Block</label>
                                <select class="form-control" id="block_id" name="block_id">
                                    <option value="">All Blocks</option>
                                    <?php foreach ($blocks as $block): ?>
                                        <option value="<?=$block['id']?>" <?=($block['id']==$block_id)?'selected':''?>><?=$block['name']?></option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <?php endif; ?>
                            <td>
                                <label for="agency_type_id">Agency Type</label>
                                <select class="form-control" id="agency_type_id" name="agency_type_id">
                                    <option value="">All Agency</option>
                                    <?php foreach ($agency_types as $agency_type): ?>
                                        <option value="<?=$agency_type['id']?>" <?=($agency_type['id']==$agency_type_id)?'selected':''?>><?=$agency_type['name']?></option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td>
                                <label for="btn-filter">Filter</label>
                                <button id="btn-filter" class="btn btn-outline btn-primary" name="filter_btn"><i class="fa fa-filter"></i> Filter</button>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</form>
