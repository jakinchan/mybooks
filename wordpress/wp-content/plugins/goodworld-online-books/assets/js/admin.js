(function ($) {
  $(function () {
    var frame;

    $('#gwob_select_pdf').on('click', function (event) {
      event.preventDefault();

      if (frame) {
        frame.open();
        return;
      }

      frame = wp.media({
        title: 'PDFを選択',
        button: { text: 'このPDFを使用' },
        library: { type: 'application/pdf' },
        multiple: false
      });

      frame.on('select', function () {
        var attachment = frame.state().get('selection').first().toJSON();
        $('#gwob_pdf_url').val(attachment.url).trigger('change');
      });

      frame.open();
    });

    $('#gwob_flipbook_shortcode').on('change blur', function () {
      var value = $(this).val().trim();
      if (value && value.indexOf('[fptf-flipbook') !== 0) {
        window.alert('Free PDF to Flipbook の [fptf-flipbook ...] ショートコードのみ利用できます。');
      }
    });
  });
})(jQuery);
