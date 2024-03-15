<form method="post" id="allotment-form" novalidate onsubmit="return false;">
    <!-- Submit Date -->
    <div class="form-group row">
        <label class="col-12 col-form-label" for="sub-date">Submit Date</label>
        <div class="col-lg-12">
            <div class="input-group">
                <input type="text" class="form-control js-datepicker"
                       id="sub-date"
                       name="date_submit"
                       required autofocus
                       placeholder="Date" autocomplete="off"
                       data-today-highlight="true" data-date-format="dd/mm/yyyy"
                       value="<?=$date_submit?>"
                       title="Enter the date of submission in the format dd/mm/yyyy"
                       aria-label="Submit Date"
                       aria-describedby="sub-date-status"
                       minlength="10" maxlength="10"
                       pattern="(0[1-9]|[12][0-9]|3[01])/(0[1-9]|1[012])/((19|20)\d\d)"
                       inputmode="numeric"
                       list="date-format"
                       form="allotment-form"
                >
                <datalist id="date-format">
                    <option label="dd/mm/yyyy">
                </datalist>
            </div>
            <small id="sub-date-status" class="status text-muted"></small>
        </div>
    </div>

    <!-- UC Amount -->
    <div class="form-group row">
        <label class="col-12 col-form-label" for="all-amount">UC Amount</label>
        <div class="col-lg-12">
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text">
                        <i class="fa fa-rupee" aria-label="Rupee Symbol"></i>
                    </span>
                </div>
                <input type="text" class="form-control"
                       id="all-amount
