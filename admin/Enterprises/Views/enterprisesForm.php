<form action="<?=$action?>" method="post">
    <div class="form-group">
        <label for="exampleInputEmail1">Enterprises Type</label>
        <input type="text" class="form-control" id="ename" value="<?=$name ?>" name="name" placeholder="Enter Enterprises name">
    </div>
    <div class="form-group">
        <label for="unit">Group Unit:</label>
        <select name="group_unit" id="unit" class="form-control" >
            <option value="Choose Unit">Choose Unit</option>
            <option value="P" <?= $group_unit=="P" ? 'selected' : ''; ?>>P</option>
            <option value="F" <?= $group_unit=="F" ? 'selected' : ''; ?>>F</option>
        </select>
    </div>
    <button type="submit" class="btn btn-primary">Submit</button>
</form>