jQuery(document).ready(function(e) {
  // Validation
  e('#minimal-contact-form button.submit').click(function() {
    var name = e('#minimal-contact-form input.name');
    var email = e('#minimal-contact-form input.email');
    var phone = e('#minimal-contact-form input.phone');
    var message = e('#minimal-contact-form textarea.message');
    var consent = e('#minimal-contact-form label.consent-caption');

    if (name.val() === null || name.val() === '') {
      name.addClass('not-valid');
    } else {
      name.removeClass('not-valid');
    }
    if (email.val() === null || email.val() === '' || email.val().match(/^([a-z0-9_\.-]+)@([\da-z\.-]+)\.([a-z\.]{2,6})$/) === null) {
      email.addClass('not-valid');
    } else {
      email.removeClass('not-valid');
    }
    if (phone.val() !== undefined && phone.val() !== '' && phone.val().match(/^[\+\(]?[0-9\ ]*[\)\/-]?[0-9\ ]*$/) === null) {
      phone.addClass('not-valid');
    } else {
      phone.removeClass('not-valid');
    }
    if (message.val() === null || message.val() === '') {
      message.addClass('not-valid');
    } else {
      message.removeClass('not-valid');
    }
    if (!e('#minimal-contact-form input.consent').prop('checked')) {
      consent.addClass('not-valid');
    } else {
      consent.removeClass('not-valid');
    }
  });

  // AJAX
  e('#minimal-contact-form button.submit').click(function() {
    var data = {};
    data.name = e('#minimal-contact-form input.name').val();
    data.email = e('#minimal-contact-form input.email').val();
    data.phone = e('#minimal-contact-form input.phone').val();
    data.address = e('#minimal-contact-form input.address').val();
    data.address = (typeof data.address === 'undefined') ? '' : data.address;
    data.subject = e('#minimal-contact-form input.subject').val();
    data.message = e('#minimal-contact-form textarea.message').val();
    if (typeof e('#minimal-contact-form input.consent') !== 'undefined' &&
        e('#minimal-contact-form input.consent').length > 0) {
      data.consent =
          (e('#minimal-contact-form input.consent').prop('checked')) ? 1 : 0;
    } else
      data.consent = 1;

    var is_email = data.email.match(/^([a-z0-9_\.-]+)@([\da-z\.-]+)\.([a-z\.]{2,6})$/) !== null;
    if (data.phone !== undefined) {
      var is_phone = (data.phone !== '' && data.phone.match(/^[\+\(]?[0-9\ ]*[\)\/-]?[0-9\ ]*$/) !== null);
      console.log('Is phone number:',is_phone);
    }

    if (data.name !== '' && data.email !== '' && is_email &&
        data.message !== '' && data.consent === 1) {
      e.ajax({
        type: 'POST',
        url: minimal_contact_form.mcf_ajaxurl,
        data: {
          action: 'mcf_ajax_send_mail',
          data: data,
        },
        success: function(t) {
          // success message
          e('#minimal-contact-form .notice').html(t);

          // reset form
          if (t.indexOf('success') !== -1 || t.indexOf('warning') !== -1) {
            e('#minimal-contact-form input.name').val('');
            e('#minimal-contact-form input.email').val('');
            e('#minimal-contact-form input.phone').val('');
            e('#minimal-contact-form input.address').val('');
            e('#minimal-contact-form input.subject').val('');
            e('#minimal-contact-form textarea.message').val('');
            e('#minimal-contact-form input.consent').prop('checked', false);
          }
        }
      });
    } else {
      // Something went wrong
      e.ajax({
        type: 'POST',
        url: minimal_contact_form.mcf_ajaxurl,
        data: {action: 'mcf_ajax_translate_message', type: 'validation_error'},
        success: function(t) {
          e('#minimal-contact-form .notice')
              .html('<p class="error">' + t + '</p>');
        }
      });
    }
  });
});