<?php
$validation = \Config\Services::validation();
?>

<div class="row">
    <div class="col-xl-12">
        <div class="block">
            <div class="block-header block-header-default">
               <h3 class="block-title"><?= $heading_title; ?></h3>
            </div>
            <div class="block-content block-content-full">    
                <?= form_open_multipart('', array('class' => 'form-horizontal', 'id' => 'form-crops', 'role' => 'form')); ?>
                <div class="row">
                    <div class="col-md-6">
                        <label>Add Crop</label>
                        <?= form_input(array('class' => 'form-control', 'name' => 'crops', 'type' => 'text', 'id' => 'crops')); ?>
                    </div>
                    <div class="col-md-6">
                        <button type="submit" data-toggle="tooltip" title="" class="btn btn-info" form="form-crops" name=""style="margin-top: 25px">Submit</button>
                    </div>
                </div>
                <?= form_close(); ?>
            </div>
        </div>
        <div class="block">
        <div class="block-header block-header-default">
            <h3 class="block-title">Crops Data</h3>
        </div>
        <div class="block-content">
            <table class="table table-vcenter text-center">
                <thead>
                    <tr>
                        <th>Crop Id</th>
                        <th>Crop Name</th>
                    </tr>
                </thead>
                   <tbody>
        <?php  foreach($crops as $crop){ ?>
          <tr>
           <td><?= $crop['id']; ?></td> 
           <td><?= $crop['crops']; ?></td>
           



          </tr>
          <?php } ?>
        </tbody>
            </table>
        </div>
    </div>
</div>

