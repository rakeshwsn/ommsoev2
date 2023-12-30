<?php
$validation = \Config\Services::validation();
?>
<div class="row">
  <div class="col-xl-12">
    <div class="block">
      <div class="block-header block-header-default">
        <h3 class="block-title"><?= $text_form; ?></h3>
        <div class="block-options">
          <button type="submit" data-toggle="tooltip" title="<?= $button_save; ?>" class="btn btn-danger" form="form-banner"><i class="fa fa-save"></i></button>
          <a href="<?= $cancel; ?>" data-toggle="tooltip" title="<?= $button_cancel; ?>" class="btn btn-primary"><i class="fa fa-reply"></i></a>
        </div>
      </div>
      <div class="block-content">
        <?= form_open_multipart('', array('class' => 'form-horizontal', 'id' => 'form-banner', 'role' => 'form')); ?>
        <div class="form-group row <?= $validation->hasError('name') ? 'is-invalid' : '' ?>">
          <label class="col-sm-2 control-label" for="input-name">Name Of Event</label>
          <div class="col-sm-10">
            <input type="hidden" name="id" id="id" value="<?= $id ?>" />
            <?= form_input(array('class' => 'form-control', 'name' => 'name', 'id' => 'name', 'placeholder' => 'Name Of Event', 'value' => set_value('name', $name))); ?>
            <div class="invalid-feedback animated fadeInDown"><?= $validation->getError('name'); ?></div>
          </div>
        </div>
        <div class="form-group row <?= $validation->hasError('objective') ? 'is-invalid' : '' ?>">
          <label class="col-sm-2 control-label" for="input-name">Objective Of The Event</label>
          <div class="col-sm-10">

            <?= form_input(array('class' => 'form-control', 'name' => 'objective', 'type' => 'text', 'id' => 'objective', 'placeholder' => 'Objective Of The Event', 'value' => set_value('objective', $objective))); ?>
            <div class="invalid-feedback animated fadeInDown"><?= $validation->getError('objective'); ?></div>
          </div>
        </div>
        <div class="form-group row <?= $validation->hasError('occasion') ? 'is-invalid' : '' ?>">
          <label class="col-sm-2 control-label" for="input-name">Occasion Of Event</label>
          <div class="col-sm-10">

            <?= form_input(array('class' => 'form-control', 'name' => 'occasion', 'type' => 'text', 'id' => 'occasion', 'placeholder' => 'On Occasion Of', 'value' => set_value('occasion', $occasion))); ?>
            <div class="invalid-feedback animated fadeInDown"><?= $validation->getError('occasion'); ?></div>
          </div>
        </div>
        <div class="form-group row <?= $validation->hasError('event_date_from') ? 'is-invalid' : '' ?>">
          <label class="col-sm-2 control-label" for="input-name">Date Of Event From</label>
          <div class="col-sm-10">
            <?= form_input(array('class' => 'form-control', 'name' => 'event_date_from', 'type' => 'date', 'id' => 'event_date_from', 'placeholder' => 'Date Of Event From', 'value' => set_value('event_date_from', $event_date_from))); ?>
            <div class="invalid-feedback animated fadeInDown"><?= $validation->getError('event_date_from'); ?></div>
          </div>
        </div>
        <div class="form-group row <?= $validation->hasError('event_date_to') ? 'is-invalid' : '' ?>">
          <label class="col-sm-2 control-label" for="input-name">Date Of Event To</label>
          <div class="col-sm-10">
            <?= form_input(array('class' => 'form-control', 'name' => 'event_date_to', 'type' => 'date', 'id' => 'event_date_to', 'placeholder' => 'Date Of Event To', 'value' => set_value('event_date_to', $event_date_to))); ?>
            <div class="invalid-feedback animated fadeInDown"><?= $validation->getError('event_date_to'); ?></div>
          </div>
        </div>
        <div class="form-group row <?= $validation->hasError('place') ? 'is-invalid' : '' ?>">
          <label class="col-sm-2 control-label" for="input-name">Place Of Event</label>
          <div class="col-sm-10">
            <?= form_input(array('class' => 'form-control', 'name' => 'place', 'type' => 'text', 'id' => 'place', 'placeholder' => 'Place Of Event', 'value' => set_value('place', $place))); ?>
            <div class="invalid-feedback animated fadeInDown"><?= $validation->getError('place'); ?></div>
          </div>
        </div>

        <div class="form-group row <?= $validation->hasError('event_days') ? 'is-invalid' : '' ?>">
          <label class="col-sm-2 control-label" for="input-name">No Of Event Days</label>
          <div class="col-sm-10">

            <?= form_input(array('class' => 'form-control', 'name' => 'event_days', 'type' => 'number', 'id' => 'event_days', 'placeholder' => 'Event Days In Number', 'value' => set_value('event_days', $event_days))); ?>
            <div class="invalid-feedback animated fadeInDown"><?= $validation->getError('event_days'); ?></div>
          </div>
        </div>
        <div class="form-group row <?= $validation->hasError('no_visitor') ? 'is-invalid' : '' ?>">
          <label class="col-sm-2 control-label" for="input-name">No Of Visitor</label>
          <div class="col-sm-10">

            <?= form_input(array('class' => 'form-control', 'name' => 'no_visitor', 'type' => 'number', 'id' => 'no_visitor', 'placeholder' => 'People Visiting per day to event', 'value' => set_value('no_visitor', $no_visitor))); ?>
            <div class="invalid-feedback animated fadeInDown"><?= $validation->getError('no_visitor'); ?></div>
          </div>
        </div>
        <div class="form-group row <?= $validation->hasError('total_visitor') ? 'is-invalid' : '' ?>">
          <label class="col-sm-2 control-label" for="input-name">Tentative No Of Visitor</label>
          <div class="col-sm-10">

            <?= form_input(array('class' => 'form-control', 'name' => 'total_visitor', 'type' => 'number', 'id' => 'total_visitor', 'placeholder' => 'Total People Visit to event', 'value' => set_value('total_visitor', $total_visitor))); ?>
            <div class="invalid-feedback animated fadeInDown"><?= $validation->getError('total_visitor'); ?></div>
          </div>
        </div>
        <div class="form-group row <?= $validation->hasError('stakeholder') ? 'is-invalid' : '' ?>">
          <label class="col-sm-2 control-label" for="input-name">Other Stakeholder involved (Collaborations)</label>
          <div class="col-sm-10">

            <?= form_input(array('class' => 'form-control', 'name' => 'stakeholder', 'type' => 'text', 'id' => 'stakeholder', 'placeholder' => 'Collaboration With', 'value' => set_value('stakeholder', $stakeholder))); ?>
            <div class="invalid-feedback animated fadeInDown"><?= $validation->getError('stakeholder'); ?></div>
          </div>
        </div>


        <div class="form-group row <?= $validation->hasError('guest') ? 'is-invalid' : '' ?>">
          <label class="col-sm-2 control-label" for="input-name">Special Guest To Event(Name and Designation)</label>
          <div class="col-sm-10">

            <?= form_input(array('class' => 'form-control', 'name' => 'guest', 'type' => 'text', 'id' => 'guest', 'placeholder' => 'Special Guest To Event', 'value' => set_value('guest', $guest))); ?>
            <div class="invalid-feedback animated fadeInDown"><?= $validation->getError('guest'); ?></div>
          </div>
        </div>
        <div class="form-group row <?= $validation->hasError('feedback') ? 'is-invalid' : '' ?>">
          <label class="col-sm-2 control-label" for="input-name">Any Feedback</label>
          <div class="col-sm-10">

            <?= form_input(array('class' => 'form-control', 'name' => 'feedback', 'type' => 'text', 'id' => 'feedback', 'placeholder' => 'Feedback Of Event', 'value' => set_value('feedback', $feedback))); ?>
            <div class="invalid-feedback animated fadeInDown"><?= $validation->getError('feedback'); ?></div>
          </div>
        </div>
        <div class="form-group row <?= $validation->hasError('involved') ? 'is-invalid' : '' ?>">
          <label class="col-sm-2 control-label" for="input-name">SHG/FPO/FA (Involved):Name</label>
          <div class="col-sm-10">

            <?= form_input(array('class' => 'form-control', 'name' => 'involved', 'type' => 'text', 'id' => 'involved', 'placeholder' => 'Enter Involved Organisation Name', 'value' => set_value('involved', $involved))); ?>
            <div class="invalid-feedback animated fadeInDown"><?= $validation->getError('involved'); ?></div>
          </div>
        </div>
        <div class="form-group row <?= $validation->hasError('report') ? 'is-invalid' : '' ?>">
          <label class="col-sm-2 control-label" for="input-name">Event Report</label>
          <div class="col-sm-10">
            <!-- <input type="file" name="report" id="report"> -->
            <input type="file" name="report" value="<?= set_value('report', $report); ?>">
           
            <div class="invalid-feedback animated fadeInDown"><?= $validation->getError('report'); ?></div>
           
          </div>

        </div>
        <div class="form-group row">
          <label class="col-sm-2 control-label" for="input-status">Status</label>
          <div class="col-sm-10">
            <?= form_dropdown('status', array('1' => 'Enabled', '0' => 'Disabled'), set_value('status', $status), 'id=\'status\' class=\'form-control\'') ?>
          </div>
        </div>
        <table id="event_images" class="table table-striped table-bordered table-hover">
          <thead>
            <tr class="">
              <th style="width: 20px;"></th>
              <th class="text-left">Upload 2 Photos Per Day</th>
              <th style="width: 200px;" class="text-left">Title/link</th>
              <th class="text-left">Description</th>
              <th class="text-right">Action</th>
            </tr>
          </thead>
          <tbody>
            <?php $image_row = 0; ?>
            <?php foreach ($event_images as $event_image) { ?>
              <tr id="image-row<?= $image_row; ?>">
                <td class="drag_handle"></td>
                <td class="text-left">

                  <div class="fileinput">
                    <div class="options-container">
                      <img class="img-fluid options-item" src="<?= $event_image['thumb']; ?>" alt="" id="thumb-image<?= $image_row; ?>" />
                      <input type="hidden" name="event_image[<?= $image_row; ?>][image]" value="<?= $event_image['image']; ?>" id="input-image<?= $image_row; ?>" />
                      <div class="options-overlay bg-black-op-75">
                        <div class="options-overlay-content">
                          <a class="btn btn-sm btn-rounded btn-alt-primary min-width-75" onclick="image_upload('input-image<?= $image_row; ?>','thumb-image<?= $image_row; ?>')">Browse</a>
                          <a class="btn btn-sm btn-rounded btn-alt-danger min-width-75" onclick="$('#thumb-image<?= $image_row; ?>').attr('src', '<?= $no_image; ?>'); $('#input-image<?= $image_row; ?>').attr('value', '');">Clear</a>
                        </div>
                      </div>
                    </div>
                  </div>
                </td>
                <td class="text-left">
                  <input type="text" name="event_image[<?= $image_row; ?>][title]" value="<?= $event_image['title']; ?>" placeholder="Title" class="form-control" />
                  <input type="text" name="event_image[<?= $image_row; ?>][link]" value="<?= $event_image['link']; ?>" placeholder="Link" class="form-control" />
                </td>
                <td class="text-left">
                  <textarea name="event_image[<?= $image_row; ?>][description]" class="description form-control"><?= $event_image['description']; ?> </textarea>
                </td>
                <td class="text-right"><button type="button" onclick="$('#image-row<?= $image_row; ?>, .tooltip').remove();" data-toggle="tooltip" title="Remove" class="btn btn-danger"><i class="fa fa-minus"></i></button></td>
              </tr>
              <?php $image_row++; ?>
            <?php } ?>
          </tbody>
          <tfoot>
            <tr>
              <td colspan="4"></td>
              <td class="text-right"><button type="button" onclick="addImage();" data-toggle="tooltip" title="Event Add" class="btn btn-primary"><i class="fa fa-plus"></i></button></td>
            </tr>
          </tfoot>
        </table>
      </div>
      <?= form_close(); ?>
    </div>
  </div>
</div>
<?php js_start(); ?>
<script type="text/javascript">
  var thin_config = {
    toolbar: [{
      name: 'basicstyles',
      items: ['Bold', 'Italic', '-', 'NumberedList', 'BulletedList', '-', 'Link', 'Unlink', 'Source']
    }],
    entities: true,
    entities_latin: false,
    allowedContent: true,
    enterMode: CKEDITOR.ENTER_BR,
    resize_maxWidth: '400px',
    width: '550px',
    height: '120px'
  };

  $(document).ready(function() {
    initDnD = function() {

      // Sort images (table sort)
      $('#event_images').tableDnD({
        onDrop: function(table, row) {
          order = $('#event_images').tableDnDSerialize()
          $.post("<?= admin_url('banner/images_order') ?>", order, function() {

          });
        },
        //dragHandle: ".drag_handle"
      });
    }
    initDnD();

    $('textarea.description').ckeditor(thin_config);
  });

  function image_upload1(field, thumb) {
    window.KCFinder = {
      callBack: function(url) {

        window.KCFinder = null;
        var lastSlash = url.lastIndexOf("uploads/");

        var fileName = url.substring(lastSlash + 8);
        url = url.replace("images", ".thumbs/images");
        $('#' + thumb).attr('src', url);
        $('#' + field).attr('value', fileName);
        $.colorbox.close();
      }
    };
    $.colorbox({
      href: BASE_URL + "/plugins/kcfinder/browse.php?type=images",
      width: "850px",
      height: "550px",
      iframe: true,
      title: "Image Manager"
    });
  };

  function image_upload(field, thumb) {
    CKFinder.modal({
      chooseFiles: true,
      width: 800,
      height: 600,
      onInit: function(finder) {
        console.log(finder);
        finder.on('files:choose', function(evt) {
          var file = evt.data.files.first();
          url = file.getUrl();

          var lastSlash = url.lastIndexOf("uploads/");
          var fileName = url.substring(lastSlash + 8);
          //url=url.replace("images", ".thumbs/images"); 
          $('#' + thumb).attr('src', decodeURI(url));
          $('#' + field).attr('value', decodeURI(fileName));

        });




        finder.on('file:choose:resizedImage', function(evt) {
          var output = document.getElementById(field);
          output.value = evt.data.resizedUrl;
          console.log(evt.data.resizedUrl);
        });
      }
    });

  };


  function image_uploadd(field, thumb) {
    CKFinder.modal({
      chooseFiles: true,
      width: 800,
      height: 600,
      onInit: function(finder) {
        console.log(finder);
        finder.on('files:choose', function(evt) {
          var file = evt.data.files.first();
          url = file.getUrl();

          var lastSlash = url.lastIndexOf("uploads/");
          var fileName = url.substring(lastSlash + 8);
          //url=url.replace("images", ".thumbs/images"); 
          $('#' + thumb).attr('src', decodeURI(url));
          $('#' + field).attr('value', decodeURI(fileName));

        });




        finder.on('file:choose:resizedImage', function(evt) {
          var output = document.getElementById(field);
          output.value = evt.data.resizedUrl;
          console.log(evt.data.resizedUrl);
        });
      }
    });

  };
</script>
<script type="text/javascript">
  <!--
  var image_row = <?= $image_row; ?>;

  function addImage() {

    html = '<tr id="image-row' + image_row + '">';
    html += ' <td class="drag_handle"></td>';
    html += '   <td class="text-left">';
    html += '   <div class="fileinput">';
    html += '     <div class="options-container">';
    html += '       <img class="img-fluid options-item" src="<?= $no_image; ?>" alt="" id="thumb-image' + image_row + '" />';
    html += '       <input type="hidden" name="event_image[' + image_row + '][image]" value="" id="input-image' + image_row + '" />';
    html += '       <div class="options-overlay bg-black-op-75">';
    html += '         <div class="options-overlay-content">';
    html += '           <a class="btn btn-sm btn-rounded btn-alt-primary min-width-75" onclick="image_upload(\'input-image' + image_row + '\',\'thumb-image' + image_row + '\')">Browse</a>';
    html += '           <a class="btn btn-sm btn-rounded btn-alt-danger min-width-75" onclick="$(\'#thumb-image' + image_row + '\').attr(\'src\',  \'<?= $no_image; ?>\'); $(\'#input-image' + image_row + '\').attr(\'value\', \'\');">Clear</a>';
    html += '         </div>';
    html += '       </div>';
    html += '     </div>';
    html += '   </div>';
    html += ' </td>';
    html += '   <td class="text-left">';
    html += '   <input type="text" name="event_image[' + image_row + '][title]" value="" placeholder="Title" class="form-control" />';
    html += '   <input type="text" name="event_image[' + image_row + '][link]" value="" placeholder="Link" class="form-control" />';
    html += ' </td>';
    html += ' <td class="text-left"><textarea name="event_image[' + image_row + '][description]" class="description form-control"></textarea></td>  ';
    html += '   <td class="text-right"><button type="button" onclick="$(\'#image-row' + image_row + '\').remove();" data-toggle="tooltip" title="Remove" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button></td>';
    html += '</tr>';

    $('#event_images tbody').append(html);
    $('textarea.description').ckeditor(thin_config);
    initDnD();
    image_row++;
  }

  function removeimage(j) {
    $(".image-row" + j).remove();
    var instance = "event_image[" + j + "][description]";
    var editor = CKEDITOR.instances[instance];
    if (editor) {
      editor.destroy(true);
    }
    //$('textarea.description').ckeditor(thin_config);

  }
  //
  -->
</script>
<?php js_end(); ?>