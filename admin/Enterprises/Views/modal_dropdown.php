<div class="form-group row">
    <label class="col-lg-3 col-form-label"><?=$label?></label>
    <div class="col-lg-9">
        <?=form_dropdown($id, option_array_values($gps,'lgd_code','name'), [], 'id="dropdown" class="form-control"')?>
    </div>
</div>
<div class="form-group row">
    <div class="col-lg-12 text-right">
        <button type="button" class="btn btn-alt-primary" id="btn-add">Add</button>
    </div>
</div>
<input type="hidden" id="block_id" name="block_id" value="<?=$block_id?>">
<input type="hidden" id="gp_id" name="gp_id" value="<?=$gp_id?>">

<script>
    $(function () {
        $('#btn-add').click(function () {
            var lgd_code = $('#dropdown').val();
            var name = $('#dropdown option:selected').text();
            var block_id = $('input[name="block_id"]').val();
            var gp_id = $('input[name="gp_id"]').val();
            var id = '<?=$gp_id?>';

            $.ajax({
                url: '<?=$post_url?>',
                type: 'POST',
                data: {
                    id: id,
                    lgd_code: lgd_code,
                    name: name,
                    block_id: block_id,
                    gp_id: gp_id
                },
                dataType: 'json',
                success: function (data) {
                    if (data.status) {
                        //set selected value to a global variable
                        if(gp_id){
                            selectedVillage = data.id;
                        } else {
                            selectedGP = data.id;
                        }
                        $('.modal').modal('hide');
                    } else {
                        alert(data.message);
                    }
                },
                error: function () {
                    console.log('Something went wrong');
                },
                complete: function () {

                }
            });
        });
    });
</script>