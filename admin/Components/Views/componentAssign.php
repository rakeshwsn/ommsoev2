<!DOCTYPE html>
<html>
<head>
    <style>
        /* Adding some basic styling */
        .dd-handle {
            height: auto !important;
            font-weight: normal;
        }

        .dd {
            max-width: none;
        }

        .typeahead {
            width: 81%;
            overflow: auto;
            max-height: 200px;
        }

        .typeahead .dropdown-item {
            white-space: normal !important;
        }
    </style>
</head>
<body>
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

    <!-- Adding the scripts at the end of the body tag -->
    <script type="text/javascript">
        $(function() {
            // Initializing the typeahead functionality
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

            // Initializing the nestable functionality
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

           
