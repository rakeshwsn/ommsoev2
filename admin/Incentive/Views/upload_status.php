
<div class="col-12">
    <div class="block block-themed">
        <div class="block-header bg-muted">
            <h3 class="block-title">Farmer Incentive Upload Status</h3>
        </div>
        <div class="block-content block-content-full">
            <form>
                <div class="row">
                    <div class="col-md-2">
                        <select class="form-control" id="year" name="year">
                                        <option value="">select</option>
                                        <option value="1" <?php if ($selectedYear==1){ echo 'selected'; } ?>>2017-18</option>
                                        <option value="2" <?php if ($selectedYear==2){ echo 'selected'; } ?>>2018-19</option>
                                        <option value="3" <?php if ($selectedYear==3){ echo 'selected'; } ?>>2019-20</option>
                                        <option value="4" <?php if ($selectedYear==4){ echo 'selected'; } ?>>2020-21</option>
                                        <option value="5" <?php if ($selectedYear==5){ echo 'selected'; } ?>>2021-22</option>
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
                    <th class="text-center">Year</th>
                    <th class="text-center">Season</th>
                    <th class="text-center">Farmer Incentive Upload</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($farmerData as $farmerDatas): ?>
                    <tr>
                        <td class="text-center"><?=$farmerDatas['district_name']?></td>
                        <td class="text-center"><?=$farmerDatas['block_name']?></td>
                        <td class="text-center"><label class="<?php echo $farmerDatas['year'] === 0 ? 'badge badge-danger' : 'text-center'; ?>"><?php echo $farmerDatas['year'] === 0 ? 'Not Uploaded' : $farmerDatas['year']; ?></label></td>
                        <td class="text-center"><label class="<?php echo $farmerDatas['season'] === 0 ? 'badge badge-danger' : 'text-center'; ?>"><?php echo $farmerDatas['season'] === 0 ? 'Not Uploaded' : $farmerDatas['season']; ?></label></td>
                        <td class="text-center"><label class="text-center <?php echo $farmerDatas['incentiveid'] == null ? 'badge badge-danger' : 'badge badge-success'; ?>"><?php echo $farmerDatas['incentiveid'] == null ? 'Not Uploaded' : 'Uploaded'; ?></label></td>
                        
                        
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>