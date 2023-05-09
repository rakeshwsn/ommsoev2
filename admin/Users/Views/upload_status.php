
<div class="col-12">
    <div class="block block-themed">
        <div class="block-header bg-muted">
            <h3 class="block-title">Upload Status</h3>
        </div>
        <div class="block-content block-content-full">
            <form>
                <div class="row">
                    <div class="col-md-2">
                        <select class="form-control" id="year" name="year">
                            <option value="">Choose Year</option>
                            <?php foreach (getAllYears() as $_year) { ?>
                                <option value="<?=$_year['id']?>" <?php if ($_year['id']==$year_id){ echo 'selected'; } ?>><?=$_year['name']?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select class="form-control" id="month" name="month">
                            <option value="">Choose Month</option>
                            <?php foreach (getAllMonths() as $_month) { ?>
                                <option value="<?=$_month['id']?>" <?php if ($_month['id']==$month_id){ echo 'selected'; } ?>><?=$_month['name']?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <!--<div class="col-md-2">
                        <select class="form-control" id="agency_type_id" name="agency_type_id">
                            <option value="">Choose Agency</option>
                            <?php /*foreach ($agency_types as $_agency) { */?>
                                <option value="<?/*=$_agency['id']*/?>" <?php /*if ($_agency['id']==$agency_type_id){ echo 'selected'; } */?>><?/*=$_agency['name']*/?></option>
                            <?php /*} */?>
                        </select>
                    </div>-->
                    <?php if(isset($districts)): ?>
                        <div class="col-md-2">
                            <select class="form-control" id="district_id" name="district_id">
                                <option value="">Choose District</option>
                                <?php foreach ($districts as $_district) { ?>
                                    <option value="<?=$_district['id']?>" <?php if ($_district['id']==$district_id){ echo 'selected'; } ?>><?=$_district['name']?></option>
                                <?php } ?>
                            </select>
                        </div>
                    <?php endif; ?>
                    <div class="col-md-2">
                        <button class="btn btn-primary"><i class="si si-magnifier"></i> Submit</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="block">
        <div class="block-content block-content-full">
            <table class="table table-striped table-vcenter">
                <thead>
                <tr>
                    <th class="text-center">District</th>
                    <th class="text-center">Block</th>
                    <th>Agency</th>
                    <th>Module</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?=$user['district']?></td>
                        <td><?=$user['block']?></td>
                        <td><?=$user['firstname']?></td>
                        <td><?=$user['module']?></td>
                        <td>
                            <?php $remove = range(2,5); $dd_statuses = array_diff_key($statuses, array_flip($remove)); ?>
                            <?php if(in_array($user['status'],[0,1])): ?>
                            <select class="form-control status" data-upload_id="<?=$user['upload_id']?>" data-module="<?=$user['modulecode']?>">
                                <?php foreach ($dd_statuses as $key => $status): ?>
                                    <option value="<?=$key?>"<?php if($key==$user['status']){ echo 'selected';} ?>><?=$status?></option>
                                <?php endforeach; ?>
                            </select>
                            <?php else: ?>
                                <div class="badge badge-secondary"><?=$statuses[$user['status']]?></div>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php js_start(); ?>
<script>
    $(function () {
        $('.status').on('change',function (i,e) {
            upload_id = $(this).data('upload_id');
            modulecode = $(this).data('module');
            status = $(this).val();
            dd = $(this);
            $.ajax({
                url:'<?=$status_update_url?>',
                data:{upload_id:upload_id,modulecode:modulecode,status:status},
                type:'POST',
                dataType:'JSON',
                success:function (json) {
                    dd.after('<span class="badge badge-success badge-status">Updated</span>');
                    window.setTimeout(function () {
                        dd.closest('td').find('.badge-status').remove();
                    },3000);
                },
                error:function () {
                    dd.after('<span class="badge badge-error badge-status">Unable to update status</span>');
                }
            })
        });

    });
</script>
<?php js_end(); ?>