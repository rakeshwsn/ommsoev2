<form>
    <div class="block">
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

                <?php if($districts): ?>
                    <div class="col-md-2">
                        <label>District</label>
                        <select class="form-control" id="district_id" name="district_id">
                            <option value="">Select District</option>
                            <?php foreach ($districts as $district): ?>
                                <option value="<?=$district['id']?>" <?php if($district['id']==$district_id){echo 'selected';} ?>><?=$district['name']?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                <?php endif; ?>
                    <div class="col-md-2">
                        <label>Block</label>
                        <select class="form-control" id="block_id" name="block_id">
                            <option value="">Select Block</option>
                            <?php foreach ($blocks as $block): ?>
                                <option value="<?=$block['id']?>" <?php if($block['id']==$block_id){echo 'selected';} ?>><?=$block['name']?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
            </div>
            <div class="row mt-3">
                <div class="col-md-2">
                    <button id="btn-filter" class="btn btn-outline btn-primary"><i class="fa fa-filter"></i> Filter</button>
                </div>
            </div>
        </div>
    </div>
</form>
<script>

    $(function () {
       $('#district_id').on('change',function () {
           district_id = $(this).val();
           $.ajax({
               url:'<?=$get_block_url?>',
               data:{district_id:district_id},
               success:function (json) {
                   html = '<option value="">Select Block</option>';
                   $.each(json,function (i,v) {
                       html += '<option value="'+v.id+'">'+v.name+'</option>';
                   });
                   $('#block_id').html(html);
               },
               error:function () {
                   alert('Unable to fetch blocks');
               }
           });
       });
    });
</script>