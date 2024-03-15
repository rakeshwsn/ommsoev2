<div class="main-container">
	<div class="block">
		<div class="block-header block-header-default">
			<h3 class="block-title">Enterprise List</h3>
		</div>
		<div class="block-content block-content-full">

			<div id="page_list_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
				<?php echo form_open('', ['method' => 'get']); ?>
				<div class="row">
					<div class="col-3">
						<label class="form-label">Year</label>
						<?php echo form_dropdown('year_id', $years, $year_id, ['class' => 'form-control mb-3']);  ?>

					</div>

					<div class="col-3 mt-4">
						<button class="btn btn-primary">Submit</button>
					</div>
				</div>
				<?php echo form_close(); ?>

			</div>

			<div class="row row-sm">
				<div class="col-lg-12">
					<div class="card pd-2 mg-b-5">
						<div class="table-responsive">
							<table id="page_list" class="table table-bordered table-striped table-vcenter js-dataTable-full dataTable no-footer" aria-describedby="page_list_info">
								<thead class="bg-light text-dark">
									<tr>
										<th>Unit Name</th>
										<th>Total units</th>
										<th>Action</th>
									</tr>

								</thead>
								<tbody>

									<?php foreach ($enterprises as $enterprise) : ?>
										<tr>
											<td><?= $enterprise['entunits'] ?></td>
											<td><?= $enterprise['total_units'] ?></td>

											<td>
												<div class="btn-group btn-group-sm pull-right"><a class="btn btn-sm btn-primary" href="<?= $enterprise['edit_url'] ?>"><i class="fa fa-pencil"></i></a></div>
											</td>

										</tr>
									<?php endforeach; ?>

								</tbody>
							</table>
						</div>
					</div>
				</div>

			</div>
		</div>
	</div>