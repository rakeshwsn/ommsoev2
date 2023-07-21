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
                        <select class="search form-control" id="year_id" name="year_id">
                            <option value="">select</option>
                            <option value="2" selected>2023-24</option>
                            <option value="3">2024-25</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="block-content" id="appendHere">
                <?php $image_row = 0; ?>
                <div class="form-group row" id="fullDiv<?php echo $image_row; ?>">
                    <label class="col-lg-2 col-form-label" for="example-hf-email"><strong>Component Name</strong></label>
                    <div class="col-lg-8">
                        <select class="search form-control" id="block_id<?php echo $image_row; ?>"></select>
                    </div>
                    <?php if ($hasPlusbutton) { ?>
                        <div class="col-lg-2">
                            <button type="button" onclick="addImage();" data-toggle="tooltip" title="Banner Add" class="btn btn-primary"><i class="fa fa-plus"></i></button>
                        </div>
                    <?php } ?>
                    <input type="hidden" name="componentsdata[<?php echo $image_row; ?>][description]" id="block_id<?php echo $image_row; ?>_description" value="">
                    <input type="hidden" name="componentsdata[<?php echo $image_row; ?>][componentid]" id="block_id<?php echo $image_row; ?>_componentid" value="">
                </div>
            </div>

        </div>
    </div>
</div>
<?php echo form_close(); ?>
<?php js_start(); ?>
<script type="text/javascript">
    var image_row = <?php echo $image_row + 1; ?>;

    function addImage() {
        var html = '<div class="form-group row" id="fullDiv' + image_row + '">';
        html += '<label class="col-lg-2 col-form-label"></label>';
        html += '<div class="col-lg-8">';
        html += '<select class="search form-control" id="block_id' + image_row + '"></select>';
        html += '</div>';
        html += '<div class="col-lg-2">';
        html += '<button type="button" onclick="$(\'#fullDiv' + image_row + '\').remove();" data-toggle="tooltip" title="Remove" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button>';
        html += '</div>';
        html += '<input type="hidden" name="componentsdata[' + image_row + '][description]" id="block_id' + image_row + '_description" value="">';
        html += '<input type="hidden" name="componentsdata[' + image_row + '][componentid]" id="block_id' + image_row + '_componentid" value="">';
        html += '</div>';

        $('#appendHere').append(html);
        var newBlockId = 'block_id' + image_row;
        initializeSelect(newBlockId, image_row);
        image_row++;
    }

    function initializeSelect(block_idd, row) {
        $('#' + block_idd).select2({
            ajax: {
                url: "<?php echo admin_url("physicalcomponentsearch"); ?>",
                method: "GET",
                dataType: "json",
                data: function(params) {
                    return {
                        input_value: params.term
                    };
                },
                processResults: function(data) {
                    var results = [];

                    if (data) {
                        for (var i = 0; i < data.length; i++) {
                            results.push({
                                id: data[i]['id'],
                                text: data[i]['component']
                            });
                        }
                    }

                    return {
                        results: results
                    };
                },
                cache: true
            },
            placeholder: "Select Component"
        });

        $(document).on('change', '#' + block_idd, function() {
            var selectedValue = $(this).val();
            var selectedOption = $('#' + block_idd + ' option[value="' + selectedValue + '"]');
            $('#block_id' + row + '_description').val(selectedOption.text());
            $('#block_id' + row + '_componentid').val(selectedOption.val());
        });
    }



    $(document).ready(function() {
        initializeSelect('block_id<?php echo $image_row; ?>', <?php echo $image_row; ?>);
    });
</script>
<?php js_end(); ?>