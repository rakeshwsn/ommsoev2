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
                <?= form_open_multipart('', array('class' => 'form-horizontal', 'id' => 'form-cropcoverage', 'role' => 'form')); ?>
                <div class="row">
                    <div class="col-md-3">
                        <label>Choose year</label>
                        <select class="form-control" id="year" name="year" required>
                            <option disabled selected>Choose Year</option>
                            <option value="2022-23">2022-23</option>
                            <option value="2023-24">2023-24</option> 
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label>Choose Season</label>
                            <select class="form-control" id="season" name="season" required>
                                <option disabled selected>Choose Season</option>
                                <option value="Rabi">Rabi</option>
                                <option value="Kharif">Kharif</option> 
                            </select>
                    </div>
                    <div class="col-md-3">
                        <label>Start Date</label>
                            <?= form_input(array('class' => 'form-control', 'name' => 'start_date', 'type' => 'date', 'id' => 'start_date','step' =>'7','value'=>"2023-05-01")); ?>
                    </div>
                    <div class="col-md-3">
                        <label>End Date</label>
                        <?= form_input(array('class' => 'form-control', 'name' => 'end_date', 'type' => 'date', 'id' => 'end_date','step' =>'7','value'=>"2023-05-06" )); ?>
                    </div>
                    <div class="col-md-12" style="text-align: right;">
                        <button type="submit" data-toggle="tooltip" title="" class="btn btn-info" form="form-cropcoverage" name=""style="margin: 20px">Submit</button>
                    </div>
                </div>
                  <?= form_close(); ?>
            </div>
        </div>
    <div class="block">
        <div class="block-header block-header-default">
            <h3 class="block-title">Area Coverage Weeks</h3>
        </div>
        <div class="block-content">
            <table class="table table-vcenter text-center">
                  <thead>
                      <tr>
                          <th>Week Id</th>
                          <th>Year</th>
                          <th>Season</th>
                          <th>Start Date</th>
                          <th>End Date</th> 
                      </tr>
                  </thead>
                   <tbody>
        <?php  foreach($weeks as $week){ ?>
          <tr>
           <td><?= $week['id']; ?></td> 
           <td><?= $week['year']; ?></td>
            <td><?= $week['season']; ?></td>
            <td><?= $week['start_date']; ?></td>
            <td><?= $week['end_date']; ?></td>



          </tr>
          <?php } ?>
        </tbody>
                             
                            </table>
                        </div>
                    </div>
    
    </div>
  </div>
</div>

