<?php
$validation = \Config\Services::validation();
?>

<div class="row">
    <div class="col-xl-12">
        <div class="block">
            <div class="block-header block-header-default">
                <h3 class="block-title">Bulk Budget </h3>
                
            </div>
            <div class="block-content">
        
                <?php echo form_open_multipart('',array('class' => 'form-horizontal', 'id' => 'form-budget','name'=>'form-budget','role'=>'form')); ?>
                <div class="budgetplan" <?=$details?"disable-div":""?>>
                    <div class="form-group row">
                        <label class="col-sm-2 control-label" for="input-status">Fund Agency</label>
                        <div class="col-sm-10">
                            <?php echo form_dropdown('fund_agency_id', option_array_values($fund_agencies, 'fund_agency_id', 'fund_agency'), set_value('fund_agency_id', $fund_agency_id), "id='fund_agency_id' class='form-control'")?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-2 control-label" for="input-category">Year</label>
                        <div class="col-sm-10">
                            <?php echo form_dropdown('year', option_array_value($years, 'id', 'name'), set_value('year', $year),"id='year' class='form-control'"); ?>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 control-label" for="input-status">District</label>
                        <div class="col-sm-10">
                           <?php 
                            $select_attributes = array(
                                'class' => 'form-control js-select2',
                                'id' => 'district_id',
                            );
                            if ($active_district) {
                                $select_attributes = array_merge($select_attributes, array('readonly' => 'readonly'));
                            }
                            echo form_dropdown('district_id', option_array_value($districts, 'id', 'name',['0'=>'Select District']), set_value('district_id', $district_id), $select_attributes); ?>
						
                            <div class="invalid-feedback animated fadeInDown"><?= $validation->getError('district_id'); ?></div>
                        
                        </div>
                        
                    </div>
                   <?php if($block_id || !$fund_agency_id){?>
                    <div class="form-group row <?=$validation->hasError('block_id')?'is-invalid':''?>">
                        <label class="col-sm-2 control-label" for="input-status">Block</label>
                        <div class="col-sm-10">
                            <?php echo form_dropdown('block_id[]', array(), set_value('block_id[]', ''), "id='block_id' class='form-control select2' multiple='multiple'")?>
                            <div class="invalid-feedback animated fadeInDown"><?= $validation->getError('block_id'); ?></div>
                        </div>
                    </div>
                    <?}?>
                </div>
                <?php if($details){?>
                <hr/>
                <div class="tableFixHead">
                    <?php foreach($components as $key=> $component){?>
                        <table class="table table-striped custom-table" id="block-components">
                            <thead>
                            <tr>
                                <th width="5%">Number</th>
                                <th width="40%">Component</th>
                                <th width="15%">Rate</th>
                                <th width="5%">Physical</th>
                                <th width="20%">Financial</th>
                            </tr>
                            </thead>
                            <tbody>
                            <input type="hidden" name="fund_agency_id" value="<?=$component['fund_agency_id']?>">
                            <input type="hidden" name="phase" value="<?=$component['phase']?>">
                            <input type="hidden" name="year" value="<?=$component['year']?>">

                            <?=$component['budgets']?>
                            </tbody>
                        </table>
                    
                    <?}?>
                    <div class="form-group text-right">
                        <button id="submitButton" class="btn btn-alt-primary ">Submit</button>
                    </div>
                    <?}else{?>
                        <div class="form-group text-right">
                            <button id="nextButton" class="btn btn-alt-primary ">Next</button>
                        </div>
                    <?}?>
                </div>
            </div>
           
            <?php echo form_close(); ?>
        </div>
    </div>
</div>



<?php js_start(); ?>
    <script type="text/javascript"><!--
        $(document).ready(function() {
            $(".select2").select2();
            $('select[name=\'district_id\']').bind('change', function() {
                $.ajax({
                    url: '<?php echo admin_url("district/block"); ?>/' + this.value,
                    dataType: 'json',
                    beforeSend: function() {},
                    complete: function() {},
                    success: function(json) {
                        var blocks=<?=json_encode($block_id)?>;
                       // console.log(blocks);
                        html = '<option value="0">Select Block</option>';

                        if (json) {
                            $.each(json,function (i,v) {

                                html += '<option value="' + v.id + '"' ;
                                //if ($.inArray(v.id, $.map(blocks, function(obj) { return obj.id; })) !== -1) {
                                if ($.inArray(v.id, blocks) !== -1) {    
                                    html += ' selected="selected"';
                                }
                                html += '>' +  v.name + '</option>';
                    
                            })
                        } else {
                            html += '<option value="">Select Block</option>';
                        }

                        $('select[name=\'block_id[]\']').html(html);
                        $('select[name=\'block_id[]\']').select2();
                    },
                    error: function(xhr, ajaxOptions, thrownError) {
                        alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                    }
                });
            });
            $('select[name=\'district_id\']').trigger('change');

            numOnly();
            decimalOnly();
        });
       
        $('#nextButton').click(function() {
            $('form[name="form-budget"]').attr('method', 'GET').submit();
        });

        $('.mon_phy').keyup(function (e) {
            var ctx = $(this);
            parent = $(ctx).closest('tr');
            parent_id = $(ctx).closest('tr').data('parent');
            
            //grand total
            gt_mon_phy = 0;
            $('.mon_phy').each(function () {
                mon_phy = parseInt($(this).find('input').val()) || 0;
                gt_mon_phy += mon_phy;
            });
            $('#gt_mon_phy').text(gt_mon_phy.toFixed(2));
            //console.log(gt_mon_phy);
            //sub total
            sub_mon_phy = 0;
            $('tr[data-parent="'+parent_id+'"]').each(function () {
                mon_phy = parseInt($(this).find('.mon_phy').find('input').val()) || 0;
                sub_mon_phy += mon_phy;
            });
            $('tr[data-parent="'+parent_id+'"].subtotal').find('.sub_mon_phy').text(sub_mon_phy.toFixed(2));
    
        });

        $('.mon_fin').keyup(function (e) {
            var ctx = $(this);
            parent = $(ctx).closest('tr');
            parent_id = $(ctx).closest('tr').data('parent');

            //grand total
            gt_mon_fin = 0;
            $('.mon_fin').each(function () {
                mon_fin = parseFloat($(this).find('input').val()) || 0;
                gt_mon_fin += mon_fin;

            });
            $('#gt_mon_fin').text(gt_mon_fin.toFixed(2));
        
            //sub total
            sub_mon_fin = 0;
            $('tr[data-parent="'+parent_id+'"]').each(function () {
                mon_fin = parseFloat($(this).find('.mon_fin').find('input').val()) || 0;
                sub_mon_fin += mon_fin;
            });
            $('tr[data-parent="'+parent_id+'"].subtotal').find('.sub_mon_fin').text(sub_mon_fin.toFixed(2));
        });
    
        function calculation_financial(obj){
            var rate=$(obj).parents('tr').find('td input.rate').val();
            var physical=$(obj).parents('tr').find('td input.physical').val();
            var financial=$(obj).parents('tr').find('td input.financial');
            var financial_val=parseFloat(rate*physical) ;
            financial.val(financial_val);
        }

        function numOnly() {
            //input type text to number
            // Get the input field
            var input = $('.rate,.physical');

            // Attach keypress event handler
            input.keypress(function(event) {
                // Get the key code of the pressed key
                var keyCode = event.which;

                // Check if the key is a number
                if (keyCode < 48 || keyCode > 57) {
                    // Prevent the input if the key is not a number
                    event.preventDefault();
                }
            });
        }

        function decimalOnly() {
            // Get the input field
            var input = $('.financial');

            $('.financial').on('keypress',function (e) {
                // Get the key code of the pressed key
                var keyCode = event.which;

                // Allow decimal point (.) and numbers (48-57) only
                if (keyCode !== 46 && (keyCode < 48 || keyCode > 57)) {
                    // Prevent the input if the key is not a number or decimal point
                    event.preventDefault();
                }

                // Allow only one decimal point
                if (keyCode === 46 && $(this).val().indexOf('.') !== -1) {
                    // Prevent the input if there is already a decimal point
                    event.preventDefault();
                }
                // Disallow comma (,)
                if (keyCode === 44) {
                    // Prevent the input if the key is a comma
                    event.preventDefault();
                }
            });
        }
        //--></script>
<?php js_end(); ?>

