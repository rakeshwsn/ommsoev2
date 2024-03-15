<div class="main-container">
	<div class="block">
		<div class="block-header ">
			<h3 class="block-header"></h3>
			<div class="block-options">
				<a href="admin/dashboard/establishment/add" data-toggle="tooltip" title="" class="btn btn-primary js-tooltip-enabled">
					<i class="fa fa-plus"></i>
				</a>
			</div>
		</div>
	
	
		<div class="block-content block-content-full">
			<div class="row">
				<div class="col-sm-12">
					<table id="page_list" class="table table-bordered table-striped table-vcenter js-dataTable-full dataTable no-footer" aria-describedby="page_list_info">
						<thead>
							<tr>
								<th>Date</th>
								<th>Establishment Type</th>
								<th class="text-right no-sort sorting_disabled" aria-label="Actions">Actions</th>
							</tr>
						</thead>
						<tbody>
							<?php if ($establishes) { ?>
								<?php foreach ($establishes as $establish) { ?>
									<tr class="odd">
										<td><?= $establish['created_at'] ?></td>
										<td><?= $establish['establishment_type'] ?></td>
										<td>
											<div class="btn-group btn-group-lg pull-right"><a class="btn btn-sm btn-primary" href="<?= $establish['edit_url'] ?>"><i class="fa fa-pencil"></i></a></div>
										</td>
									</tr>
								<?php } ?>
						</tbody>
					<?php  } else { ?>
						<tbody>
							<tr class="odd">
								<td colspan="7">
									<h3 class="text-center">data not avaliable</h3>
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
