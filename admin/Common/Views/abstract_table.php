<table class="table table-striped">
    <thead>
        <tr>
            <th>Sl</th>
            <th>Agency</th>
            <th>Total OB</th>
            <th>Total FR</th>
            <th>Total EX</th>
            <th>Total CB</th>
            <th>Percentage</th>
        </tr>
    </thead>
    <tbody>
        <?php
        if (!empty($rows) && is_array($rows) && count($rows) > 0) {
            foreach ($rows as $row) {
                // Validate and sanitize input
                $sl = filter_var($row['sl'], FILTER_VALIDATE_INT) ? $row['sl'] : '';
                $agency = filter_var($row['agency'], FILTER_SANITIZE_STRING) ?: '';
                $ob_in_lakh = filter_var($row['ob_in_lakh'], FILTER_VALIDATE_INT) ? $row['ob_in_lakh'] : '';
                $fr_in_lakh = filter_var($row['fr_in_lakh'], FILTER_VALIDATE_INT) ? $row['fr_in_lakh'] : '';
                $ex_in_lakh = filter_var($row['ex_in_lakh'], FILTER_VALIDATE_INT) ? $row['ex_in_lakh'] : '';
                $cb_in_lakh = filter_var($row['cb_in_lakh'], FILTER_VALIDATE_INT) ? $row['cb_in_lakh'] : '';
                $percentage = filter_var($row['percentage'], FILTER_VALIDATE_INT) ?: 0;

                // Check if bg_color is a valid hex color
                $bg_color = isset($row['bg_color']) && !empty($row['bg_color']) && preg_match('/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/', $row['bg_color']) ? $row
