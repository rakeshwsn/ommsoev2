<form action="<?= $action ?>" method="post">
    <div class="form-group">
        <label for="exampleInputEmail1">Enterprises Type</label>
        <input type="text" class="form-control" id="ename" value="<?= $name ?>" name="name" placeholder="Enter Enterprises name" required>
    </div>
    <div class="form-group">
        <label for="unit">Group Unit:</label>
        <select name="group_unit" id="unit" class="form-control" required>
            <option value="">Choose Unit</option>
            <option value="p" <?= $group_unit == "p" ? 'selected' : ''; ?>>p</option>
            <option value="f" <?= $group_unit == "f" ? 'selected' : ''; ?>>f</option>
        </select>
    </div>
   
        <div class="form-group text-right">
            <button type="submit" class="btn btn-primary">Submit</button>
        </div>
  
</form>