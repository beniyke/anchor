$(document).ready(function() {
  $('.download-pdf').on('click', function() {
     $(window).scrollTop(0);
     var $button = $(this);
     $button.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Downloading <span class="visually-hidden">Downloading...</span>')
        .addClass('disabled')
        .prop('disabled', true)
        .css('pointer-events', 'none');

    var target = $button.data('downloadarea');
    var filename = $button.data('filename');

    const stylePromises = $('link[rel="stylesheet"]').map(function() {
      return $.get(this.href);
    }).get();

    $.when(...stylePromises).done(function(...styles) {
      const headStyles = styles.map(style => style[0]).join('\n');
      const originalContent = $(target).clone();
      originalContent.find('.download-pdf').remove();
      originalContent.find('.d-print-none').remove();
      originalContent.find('.d-print-show').removeClass('d-none').css('display', 'flex');
      originalContent.find('table.table-striped tbody tr:even').css('background-color', '#f2f2f2');

      const updatedHtmlContent = `
        <html>
          <head>
            <style>
              ${headStyles}
              @media print {
                * {
                  -webkit-print-color-adjust: exact !important;
                  print-color-adjust: exact !important;
                }

                .d-print-show {
                  display: block !important;
                }
              }
            </style>
          </head>
          <body>
            <div class="pdf-content">
              ${originalContent.html()}
            </div>
          </body>
        </html>
      `;

      const element = document.createElement('div');
      element.innerHTML = updatedHtmlContent;

      const options = {
        margin: [7, 7, 7, 7],
        autoPaging: 'text',
        filename: filename + '.pdf',
        image: { type: 'jpeg', quality: 1 },
        html2canvas: {
            scale: 2,
            allowTaint: true,
            useCORS: true,
            letterRendering: true
        },
        jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' },
        pagebreak: { mode: ['avoid-all', 'css', 'legacy'] }
      };

      html2pdf().from(element).set(options).save();
    setTimeout(function() {
         $button.html('<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-download"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg> Download')
            .removeClass('disabled')
            .prop('disabled', false)
            .css('pointer-events', 'auto');
      }, 3000);
    });
  });
});