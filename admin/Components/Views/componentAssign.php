<style>
    .dd-handle {
        height: auto !important;
        font-weight: normal;
    }
    .dd{max-width: none;}
    .typeahead{
        width:81%;
        overflow: auto;
        max-height: 200px;
    }
    .typeahead .dropdown-item{
        white-space: normal !important;
    }
</style>
<div class="block">
    <ul class="nav nav-tabs nav-tabs-block js-tabs-enabled" data-toggle="tabs" role="tablist">
        <?php foreach((array)$fund_agencies as $key => $value) : ?>
            <li id="group-<?php echo $value['fund_agency_id']; ?>" class="nav-item">
                <a class="nav-link <?=($value['fund_agency_id']==$fund_agency_id)?'active':''?>" href="<?php echo admin_url("components/assign/{$value['fund_agency_id']}"); ?>"> <?php echo $value['fund_agency']; ?> </a>
            </li>
        <?php endforeach; ?>

    </ul>
</div>
<div class="row">
	<div class="col-md-4">
        <div class="block">
            <div class="block-header block-header-default">
                <h3 class="block-title">Assign Components</h3>
            </div>
            <div class="block-content">
                <div id="menu-left" class="mb-4">

                    <div class="form-group">
                        <label for="slug" >Search Component</label>
                        <input type="hidden" name="component_id" id="component_id" value="">
                        <?php echo form_input(array('class'=>'form-control js-autocomplete','name' => 'component_name', 'id' => 'component_name', 'placeholder'=>'Component Name','value' => set_value('component_name', ''))); ?>
                    </div>
                    <div class="form-group">
                        <label for="slug" >Component Number</label>
                        <?php echo form_input(array('class'=>'form-control','name' => 'number', 'id' => 'number', 'placeholder'=>'Component Number','value' => set_value('number', ''))); ?>
                    </div>
                    <p>
                        <button type="button" class="btn btn-light waves-effect addtocomponent" >Add to Component</button>
                    </p>

			    </div>
		    </div>
	    </div>
    </div>
	<div class="col-md-8">
		<div class="block">
            <div class="block-header block-header-default">
                <h3 class="block-title">Components</h3>
            </div>
			<div class="block-content">
                <?php echo form_open('',array('class' => 'form-horizontal', 'id' => 'form-component','role'=>'form')); ?>
                <input type="hidden" name="fund_agency_id" id="fund_agency_id" value="<?php echo $fund_agency_id;?>">
                <input type="hidden" name="component_data" id="component_data" value="">
                <p>Drag each item into the order you prefer.</p>
                <div id="component_area" class="dd">
                    <?php if($fund_agency_id) echo $components; ?>
                </div>

                <div class="text-right my-3">
                    <button type="submit" class="btn btn-primary" id="btn-save-component">Save</button>
                </div>

                <?php echo form_close(); ?>
		    </div>
	    </div>
    </div>
</div>
<?php js_start(); ?>
<script type="text/javascript">
	$(function() {
        /*jQuery(".js-autocomplete").autoComplete({
            minChars: 1,
            source: function( term, suggest ) {
                term = term.toLowerCase();
                try { xhr.abort(); } catch(e){}
                xhr = $.getJSON('<?=$component_url?>', { q: term }, function(data){ suggest(data); });

            },
            renderItem: function (item, search){
                search = search.replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&');
                console.log(search);
                var re = new RegExp("(" + search.split(' ').join('|') + ")", "gi");
                return '<div class="autocomplete-suggestion" data-component="'+item[0]+'" data-id="'+item[1]+'" data-val="'+search+'">'+item[0].replace(re, "<b>$1</b>")+'</div>';
            },
            onSelect: function(e, term, item){
                //console.log('Item "'+item.data('component')+' ('+item.data('lang')+')" selected by '+(e.type == 'keydown' ? 'pressing enter or tab' : 'mouse click')+'.');
                $('#component_name').val(item.data('component'));
                $('#component_id').val(item.data('id'));
            }
        })*/

        jQuery('input#component_name').typeahead({
            displayText: function(item) {
                return item.component_name
            },
            afterSelect: function(item) {
                this.$element[0].value = item.component_name;
                jQuery("input#component_id").val(item.component_id);
            },
            source: function (query, process) {
                jQuery.ajax({
                    url: "<?=$component_url?>",
                    data: {query:query},
                    dataType: "json",
                    type: "POST",
                    success: function (data) {
                        process(data)
                    }
                })
            }
        });
    });
    $(function() {
        var component_serialized;
        var updateOutput = function(e) {

            var list = e.length ? e : $(e.target),
                output = list.data('output');
            if(window.JSON) {
                component_serialized=window.JSON.stringify(list.nestable('serialize'));//, null, 2));
            }
            else {
                component_serialized='';
            }
            $("#component_data").val(component_serialized);
            //console.log(menu_serialized);
        };
        $('#component_area').nestable({
            listNodeName:'ul',
            group: 1,
            collapsedClass:'',

        }).on('change', updateOutput);

        var $form = $('#form-component').on('submit', function (e) {
            var $input = $form.find('[name=component_data]');
            var json = JSON.stringify($('#component_area').nestable('serialize'));
            $input.val(json);
        });

        // add new component
        $(".addtocomponent").click(function(){
            $.ajax({
                type: 'POST',
                url: '<?=$add_url?>',
                data: {
                    number:$('#number').val(),
                    component_id:$('#component_id').val(),
                    component_name:$('#component_name').val(),
                    fund_agency_id:'<?php echo $fund_agency_id;?>'
                },
                dataType:'json',
                error: function() {
                    Swal.fire({
                        title: 'Error',
                        text: "Uh aww. Request failed",
                        icon: 'error',
                    })
                },
                success: function(json) {
                    if(json.error){
                        Swal.fire({
                            title: 'Error',
                            text: json.error,
                            icon: 'error',
                        })
                    } else {
                        $('#component_area > ul').append(json.li);
                    }
                },
                complete:function () {
                    $('#number').val('');
                    $('#component_id').val('');
                    $('#component_name').val('');
                }
            });
        });

        //delete
        $('#component_area').on('mousedown',"a" ,function(event) {
            //alert("ok");
            event.preventDefault();
            return false;
        });

        $(document).on('click','.btn-remove',function (e) {
            e.preventDefault();

            li = $(this).closest('li');
            cid = $(li).data('id');

            Swal.fire({
                title: 'Are you sure?',
                text: "Delete this component",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.value) {
                    $.post('<?=$delete_url?>', {id:cid}, function(data) {
                        if (data.status) {
                            li.remove();
                            Swal.fire(
                                'Deleted!',
                                'Delete successul',
                                'success'
                            )

                        } else {
                            Swal.fire(
                                'Error!',
                                'Failed to delete this component.',
                                'warning'
                            )
                        }
                    });
                }
            });

        });

    });

</script>
<?php js_end(); ?>