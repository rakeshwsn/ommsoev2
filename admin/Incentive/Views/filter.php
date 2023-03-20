<form id="formfilter">
    <div class="block">
        <div class="block-header block-header-default">
            <h3 class="block-title">Data Filter</h3>
        </div>
    </div>
    <div class="block">
        <div class="block-content block-content-full">
            <div class="row">
                <div class="col-md-12">
                    <table class="table table-bordered">
                        <tr>
                            <th>Year</th>
                            <th>Season</th>
                            <th>Filter</th>
                           
                        </tr>
                        <tr>
                            <td>
                                <select class="form-control" id="year" name="year" required>
                                <option value="">select</option>
                                <option value="1">2017-18</option>
                                <option value="2">2018-19</option>
                                <option value="3">2020-21</option>
                                <option value="4">2021-22</option>
                                </select>
                            </td>
                            <!-- <td>
                                <select class="form-control" id="month" name="month">
                                    <?php foreach ($months as $month) { ?>
                                        <option value="<?=$month['id']?>" <?php if($month['id']==$month_id){echo 'selected';} ?>><?=$month['name']?></option>
                                    <?php } ?>
                                </select>
                            </td> -->
                            <td>
                                <select class="form-control" id="season" name="season">
                                    <option value="">select</option>
                                    <?php foreach ($seasons as $key=>$_season) { ?>
                                        <option value="<?=$key?>"><?=$_season?></option>
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
