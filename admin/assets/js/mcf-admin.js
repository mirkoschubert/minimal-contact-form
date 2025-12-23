jQuery(document).ready(function ($) {
  function toggleBorder() {
    $(".form-layout .item .toggle~label").toggleClass("bottom-border", $("#item-border-bottom").is(":checked"));
  }
  
  function toggleButtonBorder() {
    $(".form-layout .button").toggleClass("border-none", $("#button-border-none").is(":checked"));
  }

  function toggleButtonAlignment() {
    $(".form-layout .button").toggleClass(
      "right",
      $("#button-alignment").is(":checked")
    );
  }

  function toggleColumns() {
    $(".form-layout").toggleClass("single-column", $("#item-single-column").is(":checked"));
  };

  function togglePrivacy() {
    if ($("#dashboard_options label input#gdpr").is(":checked")) {
      $(".form-layout .item.privacy .with-checkbox").show();
      $(".form-layout .item.privacy .without-checkbox").hide();
    } else {
      $(".form-layout .item.privacy .with-checkbox").hide();
      $(".form-layout .item.privacy .without-checkbox").show();
    }
  }

  // Accordion
  $("#accordion").accordion({
    collapsable: true,
    heightStyle: "content",
  });

  // Slider
  $(".styling-slider").each(function () {
    var $this = $(this);
    var $input = $this.next(".styling-slider-value");
    var $amount = $this.nextAll(".styling-slider-amount");

    var initialSliderValue = parseFloat($this.data("value"));

    document.documentElement.style.setProperty(
      $input.data("css-var"),
      initialSliderValue + $amount.data("unit")
    );

    $this.slider({
      value: initialSliderValue,
      min: parseFloat($input.attr("min")),
      max: parseFloat($input.attr("max")),
      step: parseFloat($input.attr("step")),
      slide: function (event, ui) {
        $input.val(ui.value);
        $amount.text(ui.value + $amount.data("unit"));

        document.documentElement.style.setProperty(
          $input.data("css-var"),
          ui.value + $amount.data("unit")
        );
      },
    });
  });

  // Color Picker
  $(".color-picker").wpColorPicker({
    change: function (event, ui) {
      var cssVar = $(this).data("css-var");
      document.documentElement.style.setProperty(cssVar, ui.color.toString());
    },
  });

  $(".color-picker").each(function () {
    var cssVar = $(this).data("css-var");
    var color = $(this).wpColorPicker("color");
    document.documentElement.style.setProperty(cssVar, color);
  });

  // Check Border Bottom Setting
  $("#item-border-bottom").change(function () {
    $(".form-layout .item .toggle:checked~label").toggleClass(
      "bottom-border",
      $(this).is(":checked")
    );
  });

  // Toggle first/last name and name
  $(".form-layout .first-name .toggle, .form-layout .last-name .toggle").change(
    function () {
      if (
        $(".form-layout .first-name .toggle").is(":checked") ||
        $(".form-layout .last-name .toggle").is(":checked")
      ) {
        $(".form-layout .name .toggle").prop("checked", false);
      }
    }
  );

  $(".form-layout .name .toggle").change(function () {
    if ($(this).is(":checked")) {
      $(
        ".form-layout .first-name .toggle, .form-layout .last-name .toggle"
      ).prop("checked", false);
    }
  });

  // Führen Sie die Funktion beim laden der Seite aus
  toggleBorder();
  toggleButtonBorder();
  toggleButtonAlignment();
  toggleColumns();
  togglePrivacy();

  // Führen Sie die Funktion aus, wenn die Checkbox geändert wird
  $("#item-border-bottom").change(toggleBorder);
  $("#button-border-none").change(toggleButtonBorder);
  $("#button-alignment").change(toggleButtonAlignment);
  $("#item-single-column").change(toggleColumns);
  $("#dashboard_options label input#gdpr").change(togglePrivacy);
});
