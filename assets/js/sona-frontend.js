jQuery(document).ready(function ($) {
  $(".sona-payments-product-wrapper-paypal").each(function () {
    const id = $(this).data("id");
    const unique_id = $(this).data("unique");
    $.ajax({
      type: "POST",
      dataType: "json",
      url: "/wp-admin/admin-ajax.php",
      data: {
        action: "get_sona",
        blockid: id,
        nonce: ajax_object.nonce,
      },

      success: function (response) {
        $(".loader").hide();
        if (response.data["sona_email"]) {

          let FUNDING_SOURCES = [];

          let paypalbutton = response.data["sona_paypal_button"];
          let paylaterbutton = response.data["sona_paylater_button"];
          let cardbutton = response.data["sona_debit_credit_button"];
          let venmobutton = response.data["sona_venmo_button"];

          if (paypalbutton != null && paypalbutton != undefined && paypalbutton != "") {
            FUNDING_SOURCES.push(paypalbutton);
          }

          if (paylaterbutton != null && paylaterbutton != undefined && paylaterbutton != "") {
            FUNDING_SOURCES.push(paylaterbutton);
          }

          if (cardbutton != null && cardbutton != undefined && cardbutton != "") {
            FUNDING_SOURCES.push(cardbutton);
          }

          if (venmobutton != null && venmobutton != undefined && venmobutton != "") {
            FUNDING_SOURCES.push(venmobutton);
          }

          FUNDING_SOURCES.forEach(function (fundingSource) {
            paypal
              .Buttons({
                fundingSource: fundingSource,

                style: {
                  shape: "rect",
                  layout: "vertical",
                  label: "paypal",
                },

                createOrder: function (data, actions) {
                  return actions.order.create({
                    purchase_units: [
                      {
                        amount: {
                          currency_code: response.data["sonawp_paypal_currency"],
                          value: response.data["sona_price"],
                        },
                        payee: {
                          email_address: response.data["sona_email"],
                        },
                      },
                    ],
                  });
                },

                onApprove: function (data, actions) {
                  return actions.order.capture().then(function (orderData) {
                    $.ajax({
                      type: "POST",
                      dataType: "json",
                      url: "/wp-admin/admin-ajax.php",
                      data: {
                        action: "get_sona_order",
                        nonce: ajax_object.nonce,
                        order_product_name:
                          response.data["sona_product_title"],
                        order_method: fundingSource,
                        order_data: orderData,
                      },

                      success: function (response) {
                        actions.redirect(response.data);
                      },
                    });
                  });
                },

                onError: function (err) {
                  const element = document.getElementById("sona-" + unique_id);
                  element.innerHTML = "Failed: Reload Page to Try Again. or contact site admin.";
                },

                onCancel: function (data) {
                  const element = document.getElementById("sona-" + unique_id);
                  element.innerHTML = "Failed: Reload Page to Try Again.";
                },
              })
              .render("#sona-" + unique_id);
          });
        } else {
          const element = document.getElementById("sona-" + unique_id);
          element.innerHTML = "";
          if (ajax_object.admin == "admin_true") {
            element.innerHTML =
              "<h5>The PayPal email address is required to accept and recieve payments. Please goto dashboard, SonaWP, settings, Paypal tab and enter email. (This message is only visible to admin.)</h5>";
          }
        }
      },
    });
  });
});
