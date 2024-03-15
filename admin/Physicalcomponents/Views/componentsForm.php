<?php
$validation = \Config\Services::validation();
?>
<?php echo form_open_multipart('', 'id="form-usergroup"'); ?>
<div class="row">
    <div class="col-xl-12">
        <div class="block">
            <div class="block-header block-header-default">
                <h3 class="block-title"><?php echo $text_form; ?></h3>
                <div class="block-options">
                    <button type="submit" form="form-usergroup" class="btn btn-primary">Save</button>
                    <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="Cancel" class="btn btn-primary">Cancel</a>
                </div>
            </div>
            <div class="block-content">
                <div class="form-group row">
                    <label class="col-lg-2 col-form-label" for="input-category">Year</label>
                    <div class="col-sm-8">
                        <select class="search form-control" id="year_id" name="year_id" required>
                            <option value="">select</option>
                            <option value="2" selected>2023-24</option>
                            <option value="3">2024-25</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="block-content">
            <table id="banner_images" class="table table-striped table-bordered table-hover">
                <thead>
                    <tr class="">
                        <th class="text-left">Component Name</th>
                        <th class="text-left">Component Category</th>
                        <th class="text-right">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $image_row = 0; ?>

                    <tr id="image-row<?php echo $image_row; ?>">
                        <td class="text-left">
                            <input type="text" name="componentsdata[<?php echo $image_row; ?>][description]" value="<?php echo $description; ?>" placeholder="Component" class="form-control" />
                        </td>
                        <td class="text-left">
                        <select class="search form-control" id="categoryid" name="componentsdata[<?php echo $image_row; ?>][categoryid]" required>
                            <option value="">select</option>
                            <option value="1" <?php echo ($comp_categoryid == 1) ? 'selected' : ''; ?>>Enterprises</option>
                            <option value="2" <?php echo ($comp_categoryid == 2) ? 'selected' : ''; ?>>Training</option>

                        </select>
                        </td>
                        <?php if($hasPlusbutton){ ?>
                            <td class="text-right"><button type="button" onclick="addImage();" data-toggle="tooltip" title="Banner Add" class="btn btn-primary"><i class="fa fa-plus"></i></button></td>
                        <?php }?>


                    </tr>
                    <?php $image_row++; ?>

                </tbody>
                <?php if($hasPlusbutton){ ?>
                <tfoot>
                    <tr>
                        <td colspan="3"></td>
                        </tr>
                </tfoot>
                <?php }?>
            </table>
            </div>
        </div>
    </div>
</div>
<?php echo form_close(); ?>
<?php js_start(); ?>
<script type="text/javascript">
    // var compoEdit = 20;
    // console.log(compoEdit);
    var image_row = <?php echo $image_row++; ?>;

    function addImage() {
        var html = '<tr id="image-row' + image_row + '">';
    html += '<td class="text-left">';
    html += '<input type="text" name="componentsdata[' + image_row + '][description]" value="" placeholder="Component" class="form-control" />';
    html += '</td>';
    html += '<td class="text-left">';
    html += '<select class="search form-control" id="categoryid" name="componentsdata[' + image_row + '][categoryid]" required>';
    html += '<option value="">select</option>';
    html += '<option value="1">Enterprises</option>';
    html += '<option value="2">Training</option>';
    html += '</select>';
    html += '</td>';
    html += '<td class="text-right">';
    html += '<button type="button" onclick="$(\'#image-row' + image_row + ', .tooltip\').remove();" data-toggle="tooltip" title="Remove" class="btn btn-danger"><i class="fa fa-minus"></i></button>';
    html += '</td>';
    html += '</tr>';

    $('#banner_images tbody').append(html);
        image_row++;
    }

    // function initializeSelect(block_idd, row) {
    //     console.log(block_idd, row);
    //     // Make AJAX call to fetch the data
    //     $.ajax({
    //         url: "<?php echo admin_url('physicalcomponentsearch'); ?>",
    //         method: "GET",
    //         dataType: "json",
    //         data: {
    //             input_value: ''
    //         },
    //         success: function(data) {
    //             var results = [];
    //            // console.log(data.length);
    //             if (data) {
    //                 for (var i = 0; i < data.length; i++) {
    //                     results.push({
    //                         id: data[i]['id'],
    //                         text: data[i]['component']
    //                     });
    //                 }
    //             }
    //             // Initialize select2 once you have the data
    //             $('#' + block_idd).select2({
    //                 data: results,
    //                 placeholder: "Select Component"
    //             });
    //             // Set the default selected value
    //             var selectedValue = <?php echo $components_info->componentid ?? 'null'; ?>;
    //             if (selectedValue) {
    //                 $('#' + block_idd).val(selectedValue).trigger('change');
    //             }
    //         },
    //         error: function(xhr, status, error) {
    //             console.error(error);
    //         }
    //     });

    //     $(document).on('change', '#' + block_idd, function() {

    //         var selectedValue = $(this).val();
    //         $('#block_id' + row + '_description').val("");
    //         var selectedOption = $('#' + block_idd + ' option[value="' + selectedValue + '"]');
    //         console.log(selectedOption);
    //         $('#block_id' + row + '_description').val(selectedOption.text());
    //         $('#block_id' + row + '_componentid').val(selectedOption.val());
    //     });
    // }

    // $(document).ready(function() {
    //     initializeSelect('block_id<?php echo $image_row; ?>', <?php echo $image_row; ?>);
    // });
</script>




<?php js_end(); ?>