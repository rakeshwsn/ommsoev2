<?php
$validation = \Config\Services::validation();
?>

<?= form_open_multipart('', 'id="form-district"'); ?>
<div class="row">
	<div class="col-xl-12">
		
		<div class="block">
			<div class="block-header block-header-default">
				<h3 class="block-title"><?= $text_form; ?></h3>
				<div class="block-options">
					<button type="submit" form="form-district" class="btn btn-primary">Save</button>
					<a href="<?= $cancel; ?>" data-toggle="tooltip" title="Cancel" class="btn btn-primary">Cancel</a>
				</div>
			</div>
			
			<div class="block-content">
				<div class="form-group <?=$validation->hasError('district')?'is-invalid':''?>">
					<label for="code">District</label>
					<?= form_dropdown('district_id', option_array_value($districts, 'id', 'name'), set_value('district_id', $district_id),"id='district_id' class='form-control js-select2'"); ?>
					<div class="invalid-feedback animated fadeInDown"><?= $validation->getError('district_id'); ?></div>
							
				</div>
                <div class="form-group <?=$validation->hasError('block_id')?'is-invalid':''?>">
                    <label for="code">Block</label>
                    <?= form_dropdown('block_id', array(), set_value('block_id', ''),"id='block_id' class='form-control js-select2'"); ?>
                    <div class="invalid-feedback animated fadeInDown"><?= $validation->getError('block_id'); ?></div>

                </div>
				<div class="form-group <?=$validation->hasError('name')?'is-invalid':''?>">
					<label for="name" >Cluster Name</label>
					<?= form_input(array('class'=>'form-control','name' => 'name', 'id' => 'name', 'placeholder'=>'Cluster Name','value' => set_value('name', $name))); ?>
					<div class="invalid-feedback animated fadeInDown"><?= $validation->getError('name'); ?></div>		
				</div>
				<div class="form-group <?=$validation->hasError('gp_id')?'is-invalid':''?>">
					<label for="code">Grampanchayat</label>
					<?= form_dropdown('gp_id[]', array(), set_value('gp_id[]', ''),"id='gp_id' class='form-control js-select2' multiple='multiple'"); ?>
					<div class="invalid-feedback animated fadeInDown"><?= $validation->getError('gp_id'); ?></div>
				</div>				
			</div> 
		</div>
	</div> 
</div>
<?= form_close(); ?>
<?php js_start(); ?>
<script type="text/javascript">
$(function(){
	$('select[name=\'district_id\']').bind('change', function() {
		$.ajax({
			url: '<?= admin_url("district/block"); ?>/' + this.value,
			dataType: 'json',
			beforeSend: function() {
				//$('select[name=\'country_id\']').after('<span class="wait">&nbsp;<img src="view/image/loading.gif" alt="" /></span>');
			},
			complete: function() {
				//$('.wait').remove();
			},
			success: function(json) {

				html = '<option value="">Select Block</option>';

				if (json['block'] != '') {
					for (i = 0; i < json['block'].length; i++) {
						html += '<option value="' + json['block'][i]['id'] + '"';

                        if (json['block'][i]['id'] == '<?= $block_id; ?>') {
                            html += ' selected="selected"';
                        }
						html += '>' + json['block'][i]['name'] + '</option>';
					}
				} else {
					html += '<option value="0" selected="selected">Select Block</option>';
				}

				$('select[name=\'block_id\']').html(html);
				$('select[name=\'block_id\']').select2();
                $('select[name=\'block_id\']').trigger('change');
			},
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
	});
	$('select[name=\'district_id\']').trigger('change');
    $('select[name=\'block_id\']').bind('change', function() {
        $.ajax({
            url: '<?= admin_url("block/grampanchayat"); ?>/' + this.value,
            dataType: 'json',
            beforeSend: function() {
                //$('select[name=\'country_id\']').after('<span class="wait">&nbsp;<img src="view/image/loading.gif" alt="" /></span>');
            },
            complete: function() {
                //$('.wait').remove();
            },
            success: function(json) {
                var grampanchayats=<?=json_encode($grampanchayats)?>;
                html = '<option value="">Select Grampanchayat</option>';

                if (json['grampanchayat'] != '') {
                    for (i = 0; i < json['grampanchayat'].length; i++) {
                        html += '<option value="' + json['grampanchayat'][i]['id'] + '"';

                        if ($.inArray(json['grampanchayat'][i]['id'], grampanchayats) != -1){
                            html += ' selected="selected"';
                        }
                        html += '>' + json['grampanchayat'][i]['name'] + '</option>';
                    }
                } else {
                    //html += '<option value="0" selected="selected">Select Grampanchayat</option>';
                }

                $('select[name=\'gp_id[]\']').html(html);
                $('select[name=\'gp_id[]\']').select2();
            },
            error: function(xhr, ajaxOptions, thrownError) {
                alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
        });
    });

    Codebase.helpers([ 'select2']);
});
</script>


<?php js_end(); ?>