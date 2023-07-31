<div class="block block-themed">
    <div class="block-header bg-primary-op">
        <h3 class="block-title">Filter</h3>
    </div>
    <form>
        <div class="block">
            <div class="block-content block-content-full">
                <div class="row">
                    <div class="col-md-2">
                        <label>Year</label>
                        <select class="form-control" id="year_id" name="year_id" required>
                            <?php foreach ($years as $year) { ?>
                                <option value="<?=$year['id']?>" <?php if($year['id']==$year_id){echo 'selected';} ?>><?=$year['name']?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label>Season</label>
                        <select class="form-control" id="season" name="season">
                            <?php foreach ($seasons as $value => $season) { ?>
                                <option value="<?=$value?>" <?php if($value==$current_season){echo 'selected';} ?>><?=$season?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="col-md-2 mt-4">
                        <button id="btn-filter" class="btn btn-outline btn-primary">
                            <i class="fa fa-filter"></i> Filter</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>