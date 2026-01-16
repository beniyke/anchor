<div class="modal fade" id="areYouSureModal" tabindex="-1" role="dialog" aria-labelledby="areYouSureModal" aria-modal="true">
    <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
        <div class="modal-content">
             <div class="modal-body text-center py-4">
                <div class="mb-3"><span class="fas fa-exclamation-circle fa-3x text-danger"></span></div>
                <h3 class="fw-bold">Are you sure?</h3>
                <p class="text-secondary">Are you sure you want to proceed with this action?</p>
            </div>
            <div class="modal-footer d-block">
                <div class="row">
                    <div class="col-md mb-2">
                         <button type="button" class="btn btn-outline-danger w-100 btn-lg" data-bs-dismiss="modal">Cancel</button>
                    </div>
                    <div class="col-md mb-2">
                        <button type="button" class="btn btn-primary w-100 btn-lg" id="confirmLinkBtn">
                            <span id="spinner" class="spinner-border spinner-border-sm visually-hidden" role="status" aria-hidden="true"></span>
                            <span id="linkButtonText">Yes, I'm Sure</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
