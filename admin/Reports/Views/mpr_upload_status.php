
<div class="col-12">
    <div class="block block-themed">
        <div class="block-header bg-muted">
            <h3 class="block-title">MPR Status</h3>
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
                    <th>District</th>
                    <th>Status</th>
                    <th>Date Uploaded</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                    <?php foreach($mpruploadstatus as $mprs){
                        if($mprs->file){
                            $action="<a class='btn btn-primary' href='".site_url($mprs->file)."'>Download</a>";
                        }else{
                            $action="";
                        }
                        ?>
                    <tr>
                        <td><?=$mprs->district?></td>
                        <td><?=$mprs->file?'<label class="badge badge-primary">Uploaded</label>':'<label class="badge badge-danger">Not Uploaded</label>'?></td>
                        <td><?=$mprs->created_at?ymdToDmy($mprs->created_at):''?></td>
                        <td><?=$action?></td>
                    </tr>
                    <?}?>
                </tbody>
            </table>
        </div>
    </div>
</div>