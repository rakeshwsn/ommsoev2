<section class="content">
    <form autocomplete="off" novalidate>
    <div class="block block-themed">
        <div class="block-header bg-primary">
            <h3 class="block-title">Filter</h3>
        </div>
        <div class="block-content block-content-full">
            <div class="row">
                <div class="col-md-2">
                    <label for="year">Year</label>
                    <select class="form-control" id="year" name="year" required>
                        <?php foreach ($years as $year) { ?>
                            <option value="<?=$year['id']?>" <?php if($year['id']==$year_id){echo 'selected';} ?>><?=$year['name']?></option>
                        <?php } ?>
                    </select>
                    <p id="year-description" class="form-text text-muted">Select the year to filter the report.</p>
                </div>
                <div class="col-md-2">
                    <label for="month">Month</label>
                    <select class="form-control" id="month" name="month">
                        <?php foreach ($months as $month) { ?>
                            <option value="<?=$month['id']?>" <?php if($month['id']==$month_id){echo 'selected';} ?>><?=$month['name']?></option>
                        <?php } ?>
                    </select>
                    <p id="month-description" class="form-text text-muted">Select the month to filter the report.</p>
                </div>
                <?php if($agency_types): ?>
                    <div class="col-md-2">
                        <label for="agency_type_id">Agency Type</label>
                        <select class="form-control" id="agency_type_id" name="agency_type_id" aria-label="Select the agency type to filter the report." aria-describedby="agency_type_id-description">
                            <option value="">All</option>
                            <?php foreach ($agency_types as $agency_type) : ?>
                                <option value="<?=$agency_type['id']?>" <?php if($agency_type['id']==$agency_type_id){echo 'selected';} ?>><?=$agency_type['name']?></option>
                            <?php endforeach; ?>
                        </select>
                        <p id="agency_type_id-description" class="form-text text-muted">Select the agency type to filter the report.</p>
                    </div>
                <?php endif; ?>
            </div>
            <div class="row mt-3">
                <div class="col-md-2">
                    <button type="submit" id="btn-filter" class="btn btn-outline btn-primary"><i class="fa fa-filter"></i> Filter</button>
                </div>
            </div>
        </div>
    </div>
    </form>

    <div class="block block-themed">
        <div class="block-header bg-muted">
            <h3 class="block-title">Bank Interest Report</h3>
        </div>
        <div class="block-content block-content-full">
            <table class="table table-bordered table-vcenter" id="report-table">
                <thead>
                <tr>
                    <th class="text-center" style="width: 50px;">Sl. No.</th>
                    <th scope="col">Agency</th>
                    <th scope="col">Interest upto last month</th>
                    <th scope="col">Interest during the month</th>
                    <th scope="col">Total interest</th>
                    <th scope="col">Refund during the month</th>
                    <th class="text-right" scope="col">Balance </th>
                </tr>
                </thead>
                <tbody>
                <?php $i = 1; foreach ($agencies as $item): ?>
                <tr>
                    <td><?=$i?></td>
                    <td><?=$item['agency']?></td>
                    <td><?=$item['int_upto']?></td>
                    <td><?=$item['int_mon']?></td>
                    <td><?=$item['int_total']?></td>
                    <td><?=$item['tot_ref_mon']?></td>
                    <td class="text-right"><?=$item['balance']?></td>
                </tr>
                <?php $i++; endforeach; ?>
                </tbody>
                <tfoot>
                <tr>
                    <th colspan="2" scope="rowgroup">Total</th>
                    <th scope="rowgroup"><?=$total['int
