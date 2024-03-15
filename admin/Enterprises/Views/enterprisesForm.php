<form action="<?= $action ?>" method="post">
    <div class="form-group">
        <label for="exampleInputEmail1">Enterprises Type</label>
        <input type="text" class="form-control" id="ename" value="<?= $name ?>" name="name" placeholder="Enter Enterprises name" required>
    </div>
    <div class="form-group">
        <label for="unit">Group Unit:</label>
        <?php echo form_dropdown('unit_group_id', $unit_groups, $unit_group_id, ['class' => 'form-control', 'id' => 'group_unit']); ?>

     
    </div>

    <div class="form-group text-right">
        <button type="submit" class="btn btn-primary">Submit</button>
    </div>

</form>