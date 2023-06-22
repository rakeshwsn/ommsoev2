<?php
$user  = service('user');
//printr($user->getId());
?>
<div class="block">
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

                                <th>District</th>
                                <th>Block</th>
                                <th>Year</th>
                                <th>Season</th>
                                <th>Filter</th>

                            </tr>
                            <tr>
                                <?php
                                if ($user->district_id) {
                                    $main = "disabled";
                                } else {
                                    $main = "";
                                }
                                ?>
                                <td>
                                    <?php echo form_dropdown('district_id', option_array_value($districts, 'id', 'name', array("0" => "select District")), set_value('district_id', $user->district_id), "id='district_id' class='form-control select2' $main"); ?>
                                </td>


                                <td>
                                    <?php echo form_dropdown('block_id', option_array_value($blocks, 'id', 'name', array("0" => "Select Block")), set_value('block_id', ''), "id='block_id' class='form-control select2'"); ?>
                                </td>

                                <td>
                                    <select class="form-control" id="year" name="year">
                                            <option value="">select</option>
                                            <option value="1">2017-18</option>
                                            <option value="2">2018-19</option>
                                            <option value="3">2019-20</option>
                                            <option value="4">2020-21</option>
                                            <option value="5">2021-22</option>

                                    </select>
                                </td>
                                <!-- <td>
                                <select class="form-control" id="month" name="month">
                                    <?php foreach ($months as $month) { ?>
                                        <option value="<?= $month['id'] ?>" <?php if ($month['id'] == $month_id) {
                                                                                echo 'selected';
                                                                            } ?>><?= $month['name'] ?></option>
                                    <?php } ?>
                                </select>
                            </td> -->
                                <td>
                                    <select class="form-control" id="season" name="season">
                                        <option value="">select</option>
                                        <?php foreach ($seasons as $key => $_season) { ?>
                                            <option value="<?= $key ?>"><?= $_season ?></option>
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
</div>
<div class="block">
    <a href="<?php echo $mergedUrl; ?>"><button id="btn-filter" class="btn btn-outline btn-primary"><i class="fa fa-download"></i> Download</button>
        <div class="block-header block-header-default">
    </a>
    <h3 class="block-title"><?php echo $heading_title; ?></h3>
</div>
<div class="block-content block-content-full" id="tableId">
    <!-- DataTables functionality is initialized with .js-dataTable-full class in js/datatable/be_tables_datatables.min.js which was auto compiled from _es6/datatable/be_tables_datatables.js -->
    <form action="" method="post" enctype="multipart/form-data" id="form-datatable" style="overflow-x: scroll;">
        <table id="datatable_list" class="table table-bordered table-striped table-vcenter js-dataTable-full">
            <thead>
                <tr>
                    <th>SL No</th>
                    <th>District</th>
                    <th>Block</th>
                    <th>Year</th>
                    <th>Season</th>
                    <th>GP</th>
                    <th>Village</th>
                    <th>Farmer</th>
                    <th>Spouse Name</th>
                    <th>Gender</th>
                    <th>CASTE</th>
                    <th>Mobile</th>
                    <th>AADHAAR</th>
                    <th>Year of Support</th>
                    <th>Area in Hectare</th>
                    <th>Bank Name</th>
                    <th>Account Number</th>
                    <th>IFSC Code</th>
                    <th>Amount</th>
                    <th>Phase</th>
                    <!-- <th class="text-right no-sort">Actions</th> -->
                </tr>
            </thead>

            <tbody>
                <?php
                $sl = 1;
                foreach ($datatable as $district => $phases) {
                    foreach ($phases as $phase => $records) {
                        foreach ($records as $key => $record) {

                ?>
                            <tr>
                                <td><?php echo $sl; ?></td>
                                <td><?php echo $record['district_name']; ?></td>
                                <td><?php echo $record['block_name']; ?></td>
                                <td><?php echo $record['year']; ?></td>
                                <td><?php echo $record['season']; ?></td>
                                <td><?php echo $record['gp']; ?></td>
                                <td><?php echo $record['village']; ?></td>
                                <td><?php echo $record['name']; ?></td>
                                <td><?php echo $record['spouse_name']; ?></td>
                                <td><?php echo $record['gender']; ?></td>
                                <td><?php echo $record['caste']; ?></td>
                                <td><?php echo $record['phone_no']; ?></td>
                                <td><?php echo $record['aadhar_no']; ?></td>
                                <td><?php echo $record['year_support']; ?></td>
                                <td><?php echo $record['area_hectare']; ?></td>
                                <td><?php echo $record['bank_name']; ?></td>
                                <td><?php echo $record['account_no']; ?></td>
                                <td><?php echo $record['ifsc']; ?></td>
                                <td><?php echo $record['amount']; ?></td>

                                <?php if ($key == 0) { ?>
                                    <td rowspan="<?php echo count($records) ?>">
                                        <?php if (!empty($record['pdf'])) : ?>
                                            <a target="__blank" href="<?php echo base_url() . '/uploads/farmerincentive/' . $record['pdf'] ?>">
                                                <i class="fa fa-file-pdf-o" style="font-size:48px;color:red"></i>
                                            </a>
                                        <?php else : ?>
                                            No PDF
                                        <?php endif; ?>
                                    </td>
                                <? } ?>


                            </tr>
                <?php
                            $sl++;
                        }
                    }
                } ?>
            </tbody>
        </table>
    </form>
</div>
</div>


<?php js_start(); ?>


<script type="text/javascript">
    <!--
    $(document).ready(function() {
        $('select[name=\'district_id\']').bind('change', function() {
            district_id = $(this).val()
            $.ajax({
                url: '<?php echo admin_url("district/block"); ?>/' + district_id,
                dataType: 'json',
                beforeSend: function() {},
                complete: function() {
                    //$('.wait').remove();
                },
                success: function(json) {

                    html = '<option value="0">Select Block</option>';

                    if (json['block'] != '') {
                        for (i = 0; i < json.length; i++) {
                            html += '<option value="' + json[i]['id'] + '"';
                            html += '>' + json[i]['name'] + '</option>';
                        }
                    } else {
                        html += '<option value="0" selected="selected">Select Block</option>';
                    }

                    $('select[name=\'block_id\']').html(html);
                    $('select[name=\'block_id\']').select2();
                },
                error: function(xhr, ajaxOptions, thrownError) {
                    alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                }
            });
        });
        $('select[name=\'district_id\']').trigger('change');
        Codebase.helpers(['select2']);
    });
    //
    -->
</script>


<script>
    $('#btn-download').on('click', function(e) {
        var table = document.querySelector('#tableId'); // Replace 'tableId' with the actual ID of your table
        var rows = table.querySelectorAll('tr');
        var data = [];

        // Loop through rows
        for (var i = 0; i < rows.length; i++) {
            var cells = rows[i].querySelectorAll('td, th');
            var rowData = [];

            // Loop through cells
            for (var j = 0; j < cells.length; j++) {
                rowData.push(cells[j].textContent.trim());
            }

            data.push(rowData);
        }

        var csvContent = data.map(row => row.join(',')).join('\n');

        var link = document.createElement('a');
        link.setAttribute('href', 'data:text/csv;charset=utf-8,' + encodeURIComponent(csvContent));
        link.setAttribute('download', 'tableData.csv');
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);

    });
</script>


<script>
    function newexportaction(e, dt, button, config) {
        var self = this;
        var oldStart = dt.settings()[0]._iDisplayStart;
        dt.one('preXhr', function(e, s, data) {
            // Just this once, load all data from the server...
            data.start = 0;
            data.length = 2147483647;
            dt.one('preDraw', function(e, settings) {
                // Call the original action function
                if (button[0].className.indexOf('buttons-copy') >= 0) {
                    $.fn.dataTable.ext.buttons.copyHtml5.action.call(self, e, dt, button, config);
                } else if (button[0].className.indexOf('buttons-excel') >= 0) {
                    $.fn.dataTable.ext.buttons.excelHtml5.available(dt, config) ?
                        $.fn.dataTable.ext.buttons.excelHtml5.action.call(self, e, dt, button, config) :
                        $.fn.dataTable.ext.buttons.excelFlash.action.call(self, e, dt, button, config);
                } else if (button[0].className.indexOf('buttons-csv') >= 0) {
                    $.fn.dataTable.ext.buttons.csvHtml5.available(dt, config) ?
                        $.fn.dataTable.ext.buttons.csvHtml5.action.call(self, e, dt, button, config) :
                        $.fn.dataTable.ext.buttons.csvFlash.action.call(self, e, dt, button, config);
                } else if (button[0].className.indexOf('buttons-pdf') >= 0) {
                    $.fn.dataTable.ext.buttons.pdfHtml5.available(dt, config) ?
                        $.fn.dataTable.ext.buttons.pdfHtml5.action.call(self, e, dt, button, config) :
                        $.fn.dataTable.ext.buttons.pdfFlash.action.call(self, e, dt, button, config);
                } else if (button[0].className.indexOf('buttons-print') >= 0) {
                    $.fn.dataTable.ext.buttons.print.action(e, dt, button, config);
                }
                dt.one('preXhr', function(e, s, data) {
                    // DataTables thinks the first item displayed is index 0, but we're not drawing that.
                    // Set the property to what it was before exporting.
                    settings._iDisplayStart = oldStart;
                    data.start = oldStart;
                });
                // Reload the grid with the original page. Otherwise, API functions like table.cell(this) don't work properly.
                setTimeout(dt.ajax.reload, 0);
                // Prevent rendering of the full data to the DOM
                return false;
            });
        });
        // Requery the server with the new one-time export settings
        dt.ajax.reload();
    };
</script>

<?php js_end(); ?>