<div class="modal fade" id="confirmationModal" tabindex="-1" role="dialog" aria-labelledby="confirmDeleteModal" aria-modal="true">
    <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
        <div class="modal-content">
             <div class="modal-body text-center py-4">
                <div class="mb-3"><span class="fas fa-exclamation-circle fa-3x text-danger"></span></div>
                <h3 class="fw-bold">Are you sure?</h3>
                <div class="text-secondary">Once you proceed this action cannot be undone.</div>
            </div>
            <div class="modal-footer d-block">
                <div class="row">
                    <div class="col-md mb-2">
                         <button type="button" class="btn btn-outline-danger w-100 btn-lg" data-bs-dismiss="modal">Cancel</button>
                    </div>
                    <div class="col-md mb-2">
                        <button type="button" class="btn btn-primary w-100 btn-lg" id="confirmSubmissionBtn">
                            <span id="spinner" class="spinner-border spinner-border-sm visually-hidden" role="status" aria-hidden="true"></span>
                            <span id="buttonText">Proceed</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
