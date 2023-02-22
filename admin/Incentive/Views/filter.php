<form>
    <div class="block">
        <div class="block-header block-header-default">
            <h3 class="block-title">Filter</h3>
        </div>
    </div>
    <div class="block">
        <div class="block-content block-content-full">
            <div class="row">
                <div class="col-md-12">
                    <table class="table table-bordered">
                        <tr>
                            <?php if($show_district): ?>
                            <th>District</th>
                            <?php endif; ?>
                            <th>Year</th>
                            <th>Month</th>
                            <th>Season</th>
                            <th>&nbsp;</th>
                        </tr>
                        <tr>
                            <?php if($show_district): ?>
                            <td>
                                <select class="form-control" id="year" name="year" required>
                                    <?php foreach ($districts as $district) { ?>
                                        <option value="<?=$district['id']?>" <?php if($district['id']==$district_id){echo 'selected';} ?>><?=$district['name']?></option>
                                    <?php } ?>
                                </select>
                            </td>
                            <?php endif; ?>
                            <td>
                                <select class="form-control" id="year" name="year" required>
                                    <?php foreach ($years as $year) { ?>
                                        <option value="<?=$year['id']?>" <?php if($year['id']==$year_id){echo 'selected';} ?>><?=$year['name']?></option>
                                    <?php } ?>
                                </select>
                            </td>
                            <td>
                                <select class="form-control" id="month" name="month">
                                    <?php foreach ($months as $month) { ?>
                                        <option value="<?=$month['id']?>" <?php if($month['id']==$month_id){echo 'selected';} ?>><?=$month['name']?></option>
                                    <?php } ?>
                                </select>
                            </td>
                            <td>
                                <select class="form-control" id="season" name="season">
                                    <?php foreach ($seasons as $_season) { ?>
                                        <option value="<?=$_season?>" <?php if($_season==$season){echo 'selected';} ?>><?=$_season?></option>
                                    <?php } ?>
                                </select>
                            </td>
                            <td>
                                <button id="btn-filter" class="btn btn-outline btn-primary"><i class="fa fa-filter"></i> Filter</button>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</form>