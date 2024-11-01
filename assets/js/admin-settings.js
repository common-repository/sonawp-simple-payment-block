jQuery(document).ready(function ($) {
  // Tab click event handler
  $(".sona-admin-tab .tablinks").click(function () {
    var stripe = $(this).data("sonatabs");

    // Hide all tab contents
    $(".tabcontent").hide();

    // Remove 'active' class from all tab links
    $(".tablinks").removeClass("active");

    // Show the selected tab content
    $("#" + stripe).show();

    // Add 'active' class to the clicked tab link
    $(this).addClass("active");
  });

  $(".tablinks:first").addClass("active");

  // Function to show/hide input fields based on radio button selection
  function toggleStripeKeys() {
    var selectedMode = $(".sonawp_stripe_mode:checked").val();

    if (selectedMode === "test") {
      $(".live-key-fields").hide();
      $(".test-key-fields").show();
    } else if (selectedMode === "live") {
      $(".test-key-fields").hide();
      $(".live-key-fields").show();
    }
  }

  // Initial toggle on page load
  toggleStripeKeys();

  // Bind toggle function to change event of radio buttons
  $(".sonawp_stripe_mode").change(function () {
    toggleStripeKeys();
  })

  $('.copy-shortcode').on('click', function() {
    var copyText = $('.sonawp-product-shortcode').text();
    var $temp = $("<input>");
    $("body").append($temp);
    $temp.val(copyText).select();
    document.execCommand("copy");
    $temp.remove();
    $(this).next().text('Copied');
  });

  $('.copy-shortcode').on('mouseout', function() {
    $(this).next().text('');
  });

  jQuery(document).ready(function ($) {
    // Function to show/hide elements based on payment gateway selection
    function togglePaymentGateway() {
      if ($('input[name="sona_payment_gateway"]:checked').val() == 'paypal') {
        $('.if-paypal-checked').show();
      } else {
        $('.if-paypal-checked').hide();
      }

      if ($('input[name="sona_payment_gateway"]:checked').val() == 'stripe') {
        $('.if-stripe-checked').show();
      } else {
        $('.if-stripe-checked').hide();
      }
    }

    // Initial toggle on page load
    togglePaymentGateway();

    // Bind toggle function to change event of payment gateway radio buttons
    $('input[name="sona_payment_gateway"]').change(function () {
      togglePaymentGateway();
    });
  });

  
});

jQuery(function ($) {
  /*
   * Select/Upload image(s) event
   */
  $("body").on("click", ".gallery-metabox-upload-button", function (e) {
    e.preventDefault();

    var button = $(this),
      custom_uploader = wp.media({
        title: "Insert image",
        button: {
          text: "Use this image",
        },
        multiple: true,
      });
    custom_uploader.on("select", function () {
      var attech_ids = "";
      attachments;
      var attachments = custom_uploader.state().get("selection"),
        attachment_ids = new Array(),
        i = 0;
      $(button).siblings("ul").empty();
      attachments.each(function (attachment) {
        attachment_ids[i] = attachment["id"];
        attech_ids += "," + attachment["id"];
        if (attachment.attributes.type == "image") {
          $(button)
            .siblings("ul")
            .append(
              '<li data-attachment-id="' +
                attachment["id"] +
                '"><a href="' +
                attachment.attributes.url +
                '" target="_blank"><img class="true_pre_image" src="' +
                attachment.attributes.url +
                '" /></a><i class=" dashicons  dashicons-no delete-img"></i></li>'
            );
        } else {
          $(button)
            .siblings("ul")
            .append(
              '<li data-attachment-id="' +
                attachment["id"] +
                '"><a href="' +
                attachment.attributes.url +
                '" target="_blank"><img class="true_pre_image" src="' +
                attachment.attributes.icon +
                '" /></a><i class=" dashicons  dashicons-no delete-img"></i></li>'
            );
        }

        i++;
      });

      $(button).siblings(".attachment-ids").attr("value", attachment_ids);
      $(button).siblings(".gallery-metabox-remove-button").show();
    });
    custom_uploader.on("open", function () {
      var selection = custom_uploader.state().get("selection");
      var ids_value = $(button).siblings(".attachment-ids").val();

      if (ids_value.length > 0) {
        var ids = ids_value.split(",");

        ids.forEach(function (id) {
          attachment = wp.media.attachment(id);
          attachment.fetch();
          selection.add(attachment ? [attachment] : []);
        });
      }
    });
    custom_uploader.open();
  });

  /*
   * Remove image event
   */
  $("body").on("click", ".gallery-metabox-remove-button", function () {
    $(this).hide().prev().val("").prev().addClass("button").html("Add  Media");
    $(this).parent().find("ul").empty();
    return false;
  });

  jQuery(document).on(
    "click",
    ".gallery-metabox-main ul li i.delete-img",
    function () {
      var ids = [];
      var attach_id = jQuery(this).parents("li").attr("data-attachment-id");
      jQuery(".gallery-metabox-main ul li").each(function () {
        if (attach_id != jQuery(this).attr("data-attachment-id")) {
          ids.push(jQuery(this).attr("data-attachment-id"));
        }
      });
      jQuery(this)
        .parents(".gallery-metabox-main")
        .find('input[type="hidden"]')
        .val(ids);
      jQuery(this).parent().remove();
    }
  );
});
