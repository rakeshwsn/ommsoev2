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
        if (is_array($rows) && count($rows) > 0) {
            foreach ($rows as $row) {
                // Validate and sanitize input
                $sl = (isset($row['sl']) && is_numeric($row['sl'])) ? $row['sl'] : '';
                $agency = (isset($row['agency']) && !empty($row['agency'])) ? $row['agency'] : '';
                $ob_in_lakh = (isset($row['ob_in_lakh']) && is_numeric($row['ob_in_lakh'])) ? $row['ob_in_lakh'] : '';
                $fr_in_lakh = (isset($row['fr_in_lakh']) && is_numeric($row['fr_in_lakh'])) ? $row['fr_in_lakh'] : '';
                $ex_in_lakh = (isset($row['ex_in_lakh']) && is_numeric($row['ex_in_lakh'])) ? $row['ex_in_lakh'] : '';
                $cb_in_lakh = (isset($row['cb_in_lakh']) && is_numeric($row['cb_in_lakh'])) ? $row['cb_in_lakh'] : '';
                $percentage = (isset($row['percentage']) && is_numeric($row['percentage'])) ? $row['percentage'] : 0;
                $bg_color = (isset($row['bg_color']) && !empty($row['bg_color']) && preg_match('/^#([A-Fa-f
