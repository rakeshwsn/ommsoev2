<div class="main-container">
	<div class="block">
		<div class="block-header block-header-default">
			<h3 class="block-title">Filter</h3>

		</div>
		<div class="block-content block-content-full">
			<!-- DataTables functionality is initialized with .js-dataTable-full class in js/pages/be_tables_datatables.min.js which was auto compiled from _es6/pages/be_tables_datatables.js -->

			<div id="page_list_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
				<?php echo form_open('', ['method' => 'get']); ?>
				<div class="row">
					<div class="col-4">
						<label class="form-label">Year</label>
						<?php echo form_dropdown('year_id', $years, $year_id, ['class' => 'form-control mb-3']); ?>

					</div>
					<div class="col-4">
						<label class="form-label">District</label>

						<?php echo form_dropdown('district_id', $districts, $district_id, ['class' => 'form-control mb-3']); ?>

					</div>
					<div class="col-4 mt-4">
						<button class="btn btn-primary">Submit</button>
					</div>
				</div>
				<?php echo form_close(); ?>

			</div>
		</div>
	</div>

	<div class="block">
		<div class="block-content block-content-full">
			<div class="row">
				<div class="col-sm-12">
					<table id="page_list" class="table table-bordered table-striped table-vcenter js-dataTable-full dataTable no-footer" aria-describedby="page_list_info">
						<thead>
							<tr>
								<th>Year</th>
								<th>Date</th>

								<th class="text-right no-sort sorting_disabled" aria-label="Actions">Actions</th>
							</tr>
						</thead>
						<tbody>
							<?php if ($enterprises) { ?>
								<?php foreach ($enterprises as $enterprise) { ?>
									<tr class="odd">
										<td><?= $enterprise['year'] ?></td>
										<td><?= $enterprise['district_name'] ?></td>
										<td>
											<div class="btn-group btn-group-lg pull-right"><a class="btn btn-sm btn-primary" href="<?= $enterprise['edit_url'] ?>"><i class="fa fa-pencil"></i></a></div>
										</td>
									</tr>
								<?php } ?>
						</tbody>
					<?php  } else { ?>
						<tbody>
							<tr class="odd">
								<td colspan="7">
									<h3 class="text-center">Data not avaliable</h3>
								</td>
							</tr>
						</tbody>
					<?php  } ?>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>