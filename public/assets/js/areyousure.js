(function() {
    'use strict';
    
    const linksToConfirm = document.querySelectorAll('.needs-confirmation-link');
    const modalElement = document.getElementById('areYouSureModal');
    const confirmButton = document.getElementById('confirmLinkBtn');
    const spinner = document.getElementById('linkSpinner');
    const buttonText = document.getElementById('linkButtonText');
    
    if (!linksToConfirm.length || !modalElement || !confirmButton) {
        console.warn('Are You Sure: No links to confirm or modal not found.');
        return;
    }

    const areYouSureModal = new bootstrap.Modal(modalElement);
    let linkToNavigate = null;
    let isNavigating = false;

    function setLoadingState(show) {
        isNavigating = show;
        confirmButton.disabled = show;
        
        if (show) {
            if (spinner) spinner.classList.remove('visually-hidden');
            if (buttonText) {
                buttonText.textContent = 'Processing...';
            } else {
                confirmButton.textContent = 'Processing...';
            }
        } else {
            if (spinner) spinner.classList.add('visually-hidden');
            if (buttonText) {
                buttonText.textContent = 'Yes, I\'m Sure';
            } else {
                confirmButton.textContent = 'Yes, I\'m Sure';
            }
        }
    }

    linksToConfirm.forEach(link => {
        link.addEventListener('click', function(event) {
            event.preventDefault();
            event.stopPropagation();

            const href = link.getAttribute('href');
            if (!href || href === '#') {
                console.error('Link has no valid href attribute');
                return;
            }

            linkToNavigate = href;
            
            const customMessage = link.getAttribute('data-message');
            const modalBody = modalElement.querySelector('.modal-body p');
            if (customMessage && modalBody) {
                modalBody.textContent = customMessage;
            } else if (modalBody) {
                modalBody.textContent = 'Are you sure you want to proceed with this action?';
            }

            areYouSureModal.show();
        });
    });

    confirmButton.addEventListener('click', function() {
        if (isNavigating || !linkToNavigate) return;

        setLoadingState(true);

        const allElements = document.querySelectorAll('*');
        for (const element of allElements) {
            element.style.pointerEvents = 'none';
            element.style.cursor = 'wait';
        }

        window.location.href = linkToNavigate;
    });

    modalElement.addEventListener('hidden.bs.modal', function() {
        linkToNavigate = null;
        if (isNavigating) {
            setLoadingState(false);
            
            const allElements = document.querySelectorAll('*');
            for (const element of allElements) {
                element.style.pointerEvents = '';
                element.style.cursor = '';
            }
        }
    });
})();
