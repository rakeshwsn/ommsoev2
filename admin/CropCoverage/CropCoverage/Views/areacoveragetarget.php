<div class="block">
	<div class="block-header block-header-default">
		<h3 class="block-title"><?= $heading_title; ?></h3>
		<div class="block-options">
			<a href="<?= $add; ?>" data-toggle="tooltip" title="<?= $button_add; ?>" class="btn btn-primary"><i class="fa fa-plus"></i></a>
			<button type="button" data-toggle="tooltip" title="<?= $button_delete; ?>" class="btn btn-danger" onclick="confirm('<?= $text_confirm; ?>') ? $('#form-district').submit() : false;"><i class="fa fa-trash-o"></i></button>
		</div>
	</div>
	<div class="block-content block-content-full">
		<form id="form-filter" class="form-horizontal">
			<div class="form-layout">
				<div class="row mg-b-25">
					<div class="col-lg-3">
						<div class="form-group mg-b-10-force">
							<label class="form-control-label">Year:</label>
							<select class="form-control" id="year" name="year" required>
                                <option disabled selected>Choose Year</option>
                                <option value="2022-23">2022-23</option>
                                <option value="2023-24">2023-24</option> 
                            </select>
						</div>
					</div><!-- col-4 -->
					<div class="col-lg-3">
						<div class="form-group mg-b-10-force">
							<label class="form-control-label">Season:</label>
                            <select class="form-control" id="season" name="season" required>
                                <option disabled selected>Choose Season</option>
                                <option value="Rabi">Rabi</option>
                                <option value="Kharif">Kharif</option> 
                            </select>
						</div>
					</div><!-- col-4 -->
					
					<!-- col-4 -->
					<div class="col-lg-3 center">
						<label class="form-control-label">&nbsp;</label>
						<div class="form-layout-footer">
							<button type="button" id="btn-filter" class="btn btn-primary">Filter</button>
							<button type="button" id="btn-reset" class="btn btn-secondary">Reset</button>
						</div><!-- form-layout-footer -->
					</div>
				</div><!-- row -->
			</div>
		</form>
		<hr/>
		<!-- DataTables functionality is initialized with .js-dataTable-full class in js/district/be_tables_datatables.min.js which was auto compiled from _es6/district/be_tables_datatables.js -->
		<form action="<?= $delete; ?>" method="post" enctype="multipart/form-data" id="form-grampanchayat">
			<table id="datatable" class="table table-bordered table-striped table-vcenter ">
				<thead>
					<tr>
						<th rowspan="2">Block</th>
                        <?php foreach ($heading as $crop => $practices) : ?>
                        <th colspan="<?=count($practices)?>"><?=$crop?></th>
                        <?php endforeach; ?>
                        <th class="text-right no-sort">Actions</th>
					</tr>
                    <tr>
                    <?php foreach ($heading as $crop => $practices) : ?>
                        <?php foreach($practices as $practice): ?>
                            <th><?=$practice?></th>
                        <?php endforeach; ?>  
                    <?php endforeach; ?>  
                    </tr>
				</thead>
                <tbody>
                    
                </tbody>
            </table>
		</form>
	</div>
</div>
