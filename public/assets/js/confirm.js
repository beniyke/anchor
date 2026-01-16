(function() {
    'use strict';
    const formsToConfirm = document.querySelectorAll('.needs-confirmation'); 
    const modalElement = document.getElementById('confirmationModal');
    const confirmButton = document.getElementById('confirmSubmissionBtn');
    const spinner = document.getElementById('spinner');
    const buttonText = document.getElementById('buttonText');
    
    if (!formsToConfirm.length || !modalElement || !confirmButton) {
        console.error('CRITICAL ERROR: Confirmation modal or forms not found. Script terminated.');
        return; 
    }

    const confirmationModal = new bootstrap.Modal(modalElement);
    let formToSubmit = null;
    let isSubmitting = false;

    function setLoadingState(show) {
        isSubmitting = show;
        confirmButton.disabled = show;
        if (show) {
            spinner.classList.remove('visually-hidden');
            buttonText.textContent = 'Processing...';
        } else {
            spinner.classList.add('visually-hidden');
            buttonText.textContent = 'Proceed';
        }
    }

    formsToConfirm.forEach(form => {
        form.addEventListener('submit', function(event) {
            
            event.preventDefault(); 
            event.stopPropagation();
    
            if (!form.checkValidity()) {
                form.classList.add('was-validated'); 
                return; 
            }

            formToSubmit = form;
            confirmationModal.show();
        });
    });

    confirmButton.addEventListener('click', function() {
        if (isSubmitting || !formToSubmit) return; 

        setLoadingState(true); 

        const allElements = document.querySelectorAll('*');
        for (const element of allElements) {
            element.style.pointerEvents = 'none';
            element.style.cursor = 'wait';
        }

        formToSubmit.submit();
    });

    modalElement.addEventListener('hidden.bs.modal', function () {
        formToSubmit = null; 
        if (isSubmitting) {
            setLoadingState(false);
            
            const allElements = document.querySelectorAll('*');
            for (const element of allElements) {
                element.style.pointerEvents = '';
                element.style.cursor = '';
            }
        }
    });

})();