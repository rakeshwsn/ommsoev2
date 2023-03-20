<?php
$validation = \Config\Services::validation();
?>
<?php echo form_open_multipart('', 'id="form-user"'); ?>
<div class="content-heading pt-0">
    <!-- <div class="dropdown float-right">
        <button type="submit" form="form-user" class="btn btn-primary">Save</button>
        <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="Cancel" class="btn btn-primary">Cancel</a>
    </div> -->
    <?php echo $text_form; ?>
</div>
<?php if (isset($_SESSION['errorupload'])) : ?>
    <div class="alert alert-warning alert-dismissible fade show <?php echo $msgclass ?>" role="alert">
        <?php echo $_SESSION['errorupload']; ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    </div>
<?php endif; ?>
<div class="row">
<div class="col-xl-12">
<div class="row">
	<div class="col-xl-12">

   
    <div class="block">
        <div class="block-header block-header-default">
            <h3 class="block-title">Data Upload</h3>
        </div>
    </div>
    <div class="block">
        <div class="block-content block-content-full">
            <div class="row">
                <h5 style="text-align: center;">Before Uploading Data Please Download the file and set Your data into downloadble File then upload it.</h5>
                <div class="col-md-12">
                    <table class="table table-bordered">
                        <tr>
                       
                            <th>District</th>
                            <th>Block</th>
                            <th>Year</th>
                            <th>Season</th>
                            <th>File</th>
                            <th>Upload</th>
                            <th>Download</th>
                         
                        </tr>
                        <tr>
                           
                        <td>
                            <?php echo form_dropdown('district_id', option_array_value($districts, 'id', 'name',array("0"=>"select District")), set_value('district_id', ''),"id='district_id' class='form-control js-select2'"); ?>
                        </td>

                        <td>
                            <?php echo form_dropdown('block_id', option_array_value($blocks, 'id', 'name',array("0"=>"Select Block")), set_value('block_id', ''),"id='block_id' class='form-control select2'"); ?>
                        </td>
                          
                            <td>
                            <select class="form-control" id="" name="year" required>
                               <option value="">select</option>
                                <option value="1">2017-18</option>
                                <option value="2">2018-19</option>
                                <option value="3">2020-21</option>
                                <option value="4">2021-22</option>
                            </select>
                            </td>
                          
                            <td>
                            <select class="form-control" id="" name="season">
                               
                                    <option value="1">kharif</option>
                                    <option value="2">Rabi</option>
                             
                            </select>
                            </td>
                            <td>
                               <input type="file" class="from-control" name="file"/>
                            </td>
                            <td>
                                <button id="" class="btn btn-outline btn-primary"><i class="fa fa-upload"></i> Upload</button>
                            </td>
                            <td>
                               <a href="<?php echo theme_url('assets/farmerin.xlsx'); ?>" download target="__blank"  class="btn btn-outline btn-primary"><i class="fa fa-download"></i> Download</a>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    </div>
</div>
<?php echo form_close(); ?>
<?php js_start(); ?>
<script type="text/javascript"><!--
    $(document).ready(function() {
        $('select[name=\'district_id\']').bind('change', function() {
            district_id = $(this).val()
            $.ajax({
                url: '<?php echo admin_url("district/block"); ?>/' + district_id,
                dataType: 'json',
                beforeSend: function() {
                },
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

        Codebase.helpers([ 'select2']);
    });
    //--></script>
<?php js_end(); ?>