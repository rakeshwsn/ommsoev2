<div class="row">
    <div class="col-xl-12">
        <?php echo form_open(); ?>
        <div class="block">
            <div class="block-header block-header-default">
                <h3 class="block-title">Budget Details</h3>
            </div>

            <div class="block-content">
                <ul class="nav nav-tabs nav-tabs-block" data-toggle="tabs"   role="tablist">
                    <?php foreach($agencyphase as $key=> $agencyp){?>
                    <li class="nav-item">
                        <a class="nav-link <?=$key==0?'active':''?>" href="#budget-<?=$key?>"><?=$agencyp['name']?></a>
                    </li>
                    <?}?>
                </ul>
                <div class="tab-content">
                    <?php foreach($components as $key=> $component){?>
                    <div class="tab-pane <?=$key==0?'active':''?>" id="budget-<?=$key?>" role="tabpanel">
                        <table class="table table-striped" id="block-components">
                            <thead>
                            <tr>
                                <th width="5%">Number</th>
                                <th width="40%">Component</th>
                                <th width="10%">To be Released to</th>
                                <th width="10%">Units</th>
                                <th width="15%">Rate</th>
                                <th width="5%">Physical</th>
                                <th width="20%">Financial</th>
                            </tr>
                            </thead>
                            <tbody>
                            <input type="hidden" name="phase[<?=$key?>][fund_agency_id]" value="<?=$component['fund_agency_id']?>">
                            <input type="hidden" name="phase[<?=$key?>][phase]" value="<?=$component['phase']?>">
                            <input type="hidden" name="phase[<?=$key?>][year]" value="<?=$component['year']?>">

                            <?=$component['budgets']?>
                            </tbody>
                        </table>
                    </div>
                    <?}?>

                </div>


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
        function calculation_financial(obj){
            var rate=$(obj).parents('tr').find('td input.rate').val();
            var physical=$(obj).parents('tr').find('td input.physical').val();
            var financial=$(obj).parents('tr').find('td input.financial');
            var financial_val=parseFloat(rate*physical) ;
            financial.val(financial_val);
        }

        //--></script>
<?php js_end(); ?>

