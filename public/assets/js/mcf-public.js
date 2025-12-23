jQuery(document).ready(function ($) {
  if ($('.item-first-name').length && $('.item-last-name').length) {
    $(".item-first-name, .item-last-name").css("grid-column", "span 1");
  }
  if ($('.item-phone').length && $('.item-email').length) {
    $('.item-phone, .item-email').css('grid-column', 'span 1');
  }

  $("#minimal-contact-form input").on("focusout", function () {
    this.blur();
  });

  // Form AJAX
  $("#minimal-contact-form form").on("submit", function (e) {
    e.preventDefault();

    var form = $(this)[0];
    var data = $(form).serializeArray();
    var jsonData = {};

    $.each(data, function (i, field) {
      jsonData[field.name] = field.value;
    });

    fetch(scriptData.ajax_url, {
      method: "POST",
      body: JSON.stringify(jsonData),
      headers: {
        "Content-Type": "application/json",
      },
    })
      .then((response) => response.json())
      .then((response) => {
        console.log(response)
        if (response.message) {
          
          $("#minimal-contact-form .notice").text(response.message)

          if (response.data && response.data.status === 400) {
            $("#minimal-contact-form .notice").addClass('error')
            response.data.fields.forEach(el => {
              $("#minimal-contact-form #" + el).addClass('error')
            })
          }

          $("#minimal-contact-form .error").on('input', (el) => {
            if ($(el.target).val() !== '') $(el.target).removeClass('error')
          })
          
            console.log(response.message);
        } else if (response.data) {
          // error
          console.log(response.data);
        }
      });
  });
});