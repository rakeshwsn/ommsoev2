<form method="post" action="<?=$action?>" id="fpo-form" onsubmit="return false;">

    <div class="form-group row">
        <label class="col-12" for="all-date">Block Name</label>
        <div class="col-lg-12">
            <div class="input-group">
                <input type="hidden" name="district_id" value="<?=$district_id?>"/>
                <input type="hidden" name="block_id" value="<?=$block_id?>"/>

                <input type="text" class="form-control"
                       id="block"
                       name="block"
                       disabled="disabled"
                       value="<?=$block->name?>"
                >
            </div>
        </div>
    </div>
    <div class="form-group row">
        <label class="col-12" for="all-date">FPO Registered</label>
        <div class="col-lg-12">
            <div class="input-group">
                <select class="form-control" name="registered" id="registered">
                    <option value="-1">Choose </option>
                    <?php foreach ($yes_no as $key=>$yesno): ?>
                        <option value="<?=$key?>" <?php if ($key==$registered){echo 'selected';} ?>><?=$yesno?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
    </div>
    <div class="form-group row d-none" id="no-div">
        <label class="col-12" for="all-date">Current Status</label>
        <div class="col-lg-12">
            <div class="input-group">
                <select class="form-control" name="register_status" id="register_status">
                    <?php foreach ($current_status as $key=>$cstatus): ?>
                        <option value="<?=$key?>" <?php if ($key==$register_status){echo 'selected';} ?>><?=$cstatus?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
    </div>
    <div id="yes-div" class="d-none">
        <div class="form-group row">
            <label class="col-12" for="all-date"> FPO already involved in the programme in near by blocks (under OMM)</label>
            <div class="col-lg-12">
                <div class="input-group">
                    <select class="form-control" name="other_fpo" id="other_fpo">
                        <?php foreach ($yes_no as $key=>$yesno): ?>
                            <option value="<?=$key?>" <?php if ($key==$other_fpo){echo 'selected';} ?>><?=$yesno?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>
        <div class="form-group row d-none" id="oblock">
            <label class="col-12" for="all-date">Block Name</label>
            <div class="col-lg-12">
                <div class="input-group">
                    <select class="form-control" name="other_block_id" id="other_block_id">
                            <option value="">Choose Block Name</option>
                        <?php foreach ($blocks as $key=>$block): ?>
                            <option value="<?=$block->id?>" <?php if ($block->id==$other_block_id){echo 'selected';} ?>><?=$block->name?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-12" for="all-date">FPO Name</label>
            <div class="col-lg-12">
                <div class="input-group">
                    <input type="text" class="form-control"
                           id="name"
                           name="name"
                           value="<?=$name?>"
                    >
                </div>
            </div>
        </div>
        <div class="form-group row">
        <label class="col-12" for="all-date">FPO Act</label>
        <div class="col-lg-12">
            <div class="input-group">
                <select class="form-control" name="act" id="act">
                    <?php foreach ($acts as $key=>$actname): ?>
                        <option value="<?=$key?>" <?php if ($key==$act){echo 'selected';} ?>><?=$actname?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
    </div>
    </div>

</form>
<script>

</script>