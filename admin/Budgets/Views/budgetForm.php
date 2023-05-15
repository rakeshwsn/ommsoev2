<div class="row">
    <div class="col-xl-12">
        <?php echo form_open(); ?>
        <div class="block">
            <div class="block-header block-header-default">
                <h3 class="block-title"><?=$text_form?></h3>
            </div>

            <div class="block-content">
                
               
                    <?php foreach($components as $key=> $component){?>
                        <table class="table table-striped" id="block-components">
                            <thead>
                            <tr>
                                <th width="5%">Number</th>
                                <th width="40%">Component</th>
                                <th width="10%">Units</th>
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

                <div class="text-right my-3">
                    <button type="submit" class="btn btn-primary" id="btn-save-menu">Save</button>
                </div>
            </div>
        </div>
        <?php form_close(); ?>
    </div>
</div>

<?php js_start(); ?>
    <script type="text/javascript"><!--
        $(document).ready(function() {

            $('select[name=\'district_id\']').bind('change', function() {
                $.ajax({
                    url: '<?php echo admin_url("district/block"); ?>/' + this.value,
                    dataType: 'json',
                    beforeSend: function() {},
                    complete: function() {},
                    success: function(json) {
                        html = '<option value="0">Select Block</option>';

                        if (json) {
                            $.each(json,function (i,v) {
                                html += '<option value="' + v.id + '">' + v.name + '</option>';
                            })
                        } else {
                            html += '<option value="">Select Block</option>';
                        }

                        $('select[name=\'block_id\']').html(html);
                        $('select[name=\'block_id\']').select2();
                    },
                    error: function(xhr, ajaxOptions, thrownError) {
                        alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                    }
                });
            });



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

        //--></script>
<?php js_end(); ?>

