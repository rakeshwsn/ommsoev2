<form method="post" id="allotment-form" onsubmit="return false;" novalidate>
    <div class="form-group row">
        <label class="col-12 col-form-label" for="year_id">Year</label>
        <div class="col-lg-12">
            <select class="form-control" name="year" id="year" aria-label="Select a year">
                <?php foreach ($years as $year): ?>
                    <option value="<?=$year['id']?>" <?php if ($year['id']==$year_id){echo 'selected';} ?>><?=$year['name']?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
    <div class="form-group row">
        <label class="col-12 col-form-label" for="recipient_id">District/Agency</label>
        <div class="col-lg-12">
            <select class="form-control" name="recipient_id" id="recipient_id" aria-label="Select a district or agency">
                <?php foreach ($recipients as $recipient): ?>
                    <option value="<?=$recipient['id']?>" <?php if ($recipient['id']==$recipient_id){echo 'selected';} ?>><?=$recipient['name']?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
    <div class="form-group row">
        <label class="col-12 col-form-label" for="all-date">Allotment Date</label>
        <div class="col-lg-12">
            <div class="input-group">
                <input type="text" class="form-control js-datepicker"
                       id="all-date"
                       name="allotment_date"
                       placeholder="Date" autocomplete="off"
                       data-today-highlight="true" data-date-format="dd/mm/yyyy"
                       aria-describedby="all-date-label"
                       value="<?=$allotment_date?>"
                >
                <small id="all-date-label" class="form-text text-muted">Enter the allotment date</small>
            </div>
        </div>
    </div>
    <div class="form-group row">
        <label class="col-12 col-form-label" for="all-amount">Allotment Amount</label>
        <div class="col-lg-12">
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text">
                        <i class="fa fa-rupee"></i>
                    </span>
