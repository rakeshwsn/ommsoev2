<section class="content">
    <form>
    <div class="block block-themed">
        <div class="block-header bg-primary">
            <h3 class="block-title">Filter</h3>
        </div>
        <div class="block-content block-content-full">
            <div class="row">
                <div class="col-md-2">
                    <label>Year</label>
                    <select class="form-control" id="year" name="year" required>
                        <?php foreach ($years as $year) { ?>
                            <option value="<?=$year['id']?>" <?php if($year['id']==$year_id){echo 'selected';} ?>><?=$year['name']?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label>Month</label>
                    <select class="form-control" id="month" name="month">
                        <?php foreach ($months as $month) { ?>
                            <option value="<?=$month['id']?>" <?php if($month['id']==$month_id){echo 'selected';} ?>><?=$month['name']?></option>
                        <?php } ?>
                    </select>
                </div>
                <?php if($agency_types): ?>
                    <div class="col-md-2">
                        <label>Agency Type</label>
                        <select class="form-control" id="agency_type_id" name="agency_type_id">
                            <option value="">All</option>
                            <?php foreach ($agency_types as $agency_type) : ?>
                                <option value="<?=$agency_type['id']?>" <?php if($agency_type['id']==$agency_type_id){echo 'selected';} ?>><?=$agency_type['name']?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                <?php endif; ?>
            </div>
            <div class="row mt-3">
                <div class="col-md-2">
                    <button id="btn-filter" class="btn btn-outline btn-primary"><i class="fa fa-filter"></i> Filter</button>
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
            <table class="table table-bordered table-vcenter">
                <thead>
                <tr>
                    <th class="text-center" style="width: 50px;">Sl. No.</th>
                    <th>Agency</th>
                    <th class="">Interest upto last month</th>
                    <th class="">Interest during the month</th>
                    <th class="">Total interest</th>
                    <th class="">Refund during the month</th>
                    <th class="text-right">Balance </th>
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
                    <td><?=$item['balance']?></td>
                </tr>
                <?php $i++; endforeach; ?>
                </tbody>
                <tfoot>
                <tr>
                    <th colspan="2"><?=$total['agency']?></th>
                    <th><?=$total['int_upto']?></th>
                    <th><?=$total['int_mon']?></th>
                    <th><?=$total['int_total']?></th>
                    <th><?=$total['tot_ref_mon']?></th>
                    <th><?=$total['balance']?></th>
                </tr>
                </tfoot>
            </table>
        </div>
    </div>

</section>