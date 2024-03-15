
<div class="col-12">
    <div class="block block-themed">
        <div class="block-header bg-muted">
            <h3 class="block-title">Allow Upload</h3>
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
                    <div class="col-md-2">
                        <select class="form-control" id="agency_type_id" name="agency_type_id">
                            <option value="">Choose Agency</option>
                            <?php foreach ($agency_types as $_agency) { ?>
                                <option value="<?=$_agency['id']?>" <?php if ($_agency['id']==$agency_type_id){ echo 'selected'; } ?>><?=$_agency['name']?></option>
                            <?php } ?>
                        </select>
                    </div>
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
                    <th>From Date</th>
                    <th>To Date</th>
                    <th>Extended Date</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?=$user['district']?></td>
                        <td><?=$user['block']?></td>
                        <td><?=$user['firstname']?></td>
                        <td><?=$user['from_date']?></td>
                        <td><?=$user['to_date']?></td>
                        <td><input type="text"
                                   data-upload-id="<?=$user['upload_id']?>"
                                   data-user-id="<?=$user['user_id']?>"
                                   data-date-format="dd/mm/yyyy"
                                   class="js-datepicker form-control"
                                   value="<?=$user['extended_date']?>"></td>
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
        // Init datepicker (with .js-datepicker and .input-daterange class)
        $('.js-datepicker:not(.js-datepicker-enabled)').add('.input-daterange:not(.js-datepicker-enabled)').each(function(){
            var el = $(this);

            // Add .js-datepicker-enabled class to tag it as activated
            el.addClass('js-datepicker-enabled');

            // Init
            el.datepicker({
                weekStart: el.data('week-start') || 0,
                autoclose: true,
                todayHighlight: true,
                orientation: 'bottom', // Position issue when using BS4, set it to bottom until officially supported
            }).on('changeDate', function (e) {
                upload_id = $(this).data('upload-id');
                user_id = $(this).data('user-id');
                to_date = $(this).val();
                $.ajax({
                    url:'<?=$allow_upload_url?>',
                    data:{upload_id:upload_id,user_id:user_id,to_date:to_date},
                    type:'POST',
                    dataType:'JSON',
                    success:function (json) {
                        alert('Date extended successfully');
                    },
                    error:function () {
                        alert('Unable to update date');
                    }
                });
            });
        });

    });
</script>
<?php js_end(); ?>