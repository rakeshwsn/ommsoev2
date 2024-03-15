<!-- Main content -->
<section class="content">

    <div class="block" id="upload-controls">
        <div class="block-content block-content-full">
            <form method="post" action="" novalidate>
                <div class="row">
                    <div class="col-md-2">
                        <label for="year">Choose Year</label>
                        <select class="form-control" id="year" name="year" required>
                            <option value="">Choose Year</option>
                            <?php foreach ($years as $year) : ?>
                                <option value="<?=$year['id']?>" <?php if($year['id']==$year_id){echo 'selected';} ?>><?=$year['name']?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="month">Choose Month</label>
                        <select class="form-control" id="month" name="month" required>
                            <option value="">Choose Month</option>
                            <?php foreach ($months as $month) : ?>
                                <option value="<?=$month['id']?>" <?php if($month['id']==$month_id){echo 'selected';} ?>><?=$month['name']?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <?php if($agency_types): ?>
                        <div class="col-md-2">
                            <label for="agency_type_id">Choose Agency Type</label>
                            <select class="form-control" id="agency_type_id" name="agency_type_id">
                                <option value="">Choose Agency Type</option>
                                <?php foreach ($agency_types as $agency_type) : ?>
                                    <option value="<?=$agency_type['id']?>" <?php if($agency_type['id']==$agency_type_id){echo 'selected';} ?>><?=$agency_type['name']?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    <?php endif; ?>
                    <div class="col-md-2">
                        <label for="txn_type">Choose Module</label>
                        <select class="form-control" id="txn_type" name="txn_type">
                            <option value="">Choose Module</option>
                            <?php foreach ($modules as $module): ?>
                                <option value="<?=$module['modulecode']?>" <?php if($txn_type==$module['modulecode']){echo 'selected';} ?>><?=$module['module']?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <?php if($districts): ?>
                        <div class="col-md-2">
                            <label for="district_id">Choose District (if district level)</label>
                            <select class="form-control" id="district_id" name="district_id">
                                <option value="">Choose District (if district level)</option>
                                <?php foreach ($districts as $district): ?>
                                    <option value="<?=$district['id']?>" <?php if($district['id']==$district_id){echo 'selected';} ?>><?=$district['name']?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    <?php endif; ?>
                    <?php if($fund_agencies): ?>
                        <div class="col-md-2">
                            <label for="fund_agency_id">Choose Fund Agency</label>
                            <select class="form-control" id="fund_agency_id" name="fund_agency_id">
                                <?php foreach ($fund_agencies as $agency): ?>
                                    <option value="<?=$agency['fund_agency_id']?>" <?php if($agency['fund_agency_id']==$fund_agency_id){echo 'selected';} ?>><?=$agency['fund_agency']?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    <?php endif; ?>
                    <?php if($blocks): ?>
                        <div class="col-md-2">
                            <label for="block_id">Choose Block (if block level)</label>
                            <select class="form-control" id="block_id" name="block_id">
                                <option value="">Choose Block (if block level)</option>
                                <?php foreach ($blocks as $block): ?>
                                    <option value="<?=$block['id']?>" <?php if($block['id']==$block_id){echo 'selected';} ?>><?=$block['name']?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="row mt-3">
                    <div class="col-md-2">
                        <button id="btn-add" class="btn btn-outline btn-primary" name="filter"><i class="fa fa
