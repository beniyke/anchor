 <form action="<?= url(route('import'))?>" method="POST" enctype="multipart/form-data">
	<div class="modal modal-blur modal-sm fade" id="import-user" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered" role="document">
		    <div class="modal-content">
		    	<div class="modal-header">
					<h5 class="modal-title"><span class="fas fa-cloud-upload fa-fw"></span> Import User</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
			    <div class="modal-body">
					<div class="mb-3">
						<?= component('label')->content('Document')->attributes(['class' => 'form-label fw-bold'])->render()?>
						<?= component('input')->attributes(['type' => 'file', 'class' => 'form-control form-control-lg file', 'name' => 'document', 'required' => true, 'autocomplete' => 'off', 'data-formats' => 'xlsx', 'data-maxsize' => '1mb', 'data-info' => '.document-upload-box', 'accept' => '.xlsx, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'])->render()?>
						<div class="document-upload-box my-2"></div>
		                <small class="text-secondary">Only Excel document (.xlsx) formats are accepted. The maximum upload size is 1MB.</small>
					</div>
					<div class="mb-3">
						<?= component('label')->content('Role')->attributes(['class' => 'form-label fw-bold'])->render()?>
						<?= component('select')->attributes(['class' => 'form-select form-control-lg choices-select', 'name' => 'role', 'required' => true])->options(arr($create_user_view_model->getRolesForDropdown())->prepend(['' => 'SELECT'])->get())->render()?>
					</div>
			    </div>
			    <div class="modal-footer">
			    	<?= $this->importantFormFields('put')?>
			    	<button type="button" class="btn btn-outline-danger me-auto btn-lg" data-bs-dismiss="modal">Cancel</button>
			    	<button type="submit" class="btn btn-success btn-lg">Import</button>
			    </div>
		    </div>
		</div>
	</div>
</form>
