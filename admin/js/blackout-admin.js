(function ($) {
  "use strict";

  function loadblackoutData() {
    $.get(
      "http://localhost/wordpress/wp-json/myapi/v1/blackout",
      function (data) {
        let i = 0;
        let rowsHtml = "";
        console.log(data);
        while (i < data.length) {
          let blackout_id = data[i]["blackout_id"];
          let blackout_date = data[i]["date"];
          let reason = data[i]["reason"];

          rowsHtml += `<tr>
                              <td>${blackout_id}</td>
                              <td>${blackout_date}</td>
                              <td>${reason}</td>
                              <td>
                                <button id="delete-btn" data-id=${blackout_id}>
                                    Delete
                                </button>
                              </td>
                          </tr>`;
          i++;
        }
        $("#mycontent2").html(rowsHtml);
      },
    );
  }
  loadblackoutData();

  $(document).on("click", "#delete-btn", function () {
    let id = $(this).data("id");

    Swal.fire({
      icon: "warning",
      title: "Are you sure?",
      text: "Do you want to delete this blackout date?",
      showCancelButton: true,
      confirmButtonText: "Yes, delete it!",
      cancelButtonText: "Cancel",
      confirmButtonColor: "#d33",
      cancelButtonColor: "#3085d6",
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          url: `http://localhost/wordpress/wp-json/myapi/v1/blackout/${id}`,
          type: "DELETE",

          beforeSend: function (xhr) {
            if (
              typeof bookingApiSettings !== "undefined" &&
              bookingApiSettings.nonce
            ) {
              xhr.setRequestHeader("X-WP-Nonce", bookingApiSettings.nonce);
            }
          },

          success: function (response) {
            if (response.success) {
              Swal.fire({
                icon: "success",
                title: "Deleted!",
                text: "The blackout date has been deleted.",
              });

              loadblackoutData();
            } else {
              Swal.fire({
                icon: "error",
                title: "Failed!",
                text: response.message || "Could not delete the blackout date.",
              });
            }
          },

          error: function (xhr) {
            console.error("Delete failed:", xhr.responseText);

            Swal.fire({
              icon: "error",
              title: "Error!",
              text: "Failed to delete the blackout date.",
            });
          },
        });
      }
    });
  });

  $(document).on("click", "#add-blackout-btn", function () {
    $("#blackout-modal").fadeIn(200);
  });

  $(document).on("click", "#cancel-blackout", function () {
    $("#blackout-modal").fadeOut(200);
  });

  $(document).on("submit", "#blackout-form", function (e) {
    e.preventDefault();

    let date = $("#blackout_date").val();
    let reason = $("#blackout_reason").val();

    $.ajax({
      url: "http://localhost/wordpress/wp-json/myapi/v1/blackout",
      type: "POST",
      data: {
        blackout_date: date,
        reason: reason,
      },
      success: function (response) {
        if (response.success) {
          Swal.fire({
            icon: "success",
            title: "Added !",
            text: "The blackout date has been Added",
          });

          $("#blackout-form")[0].reset();
          $("#blackout-modal").fadeOut(200);

          loadblackoutData();
        }
      },
      error: function (xhr) {
        console.error(xhr.responseText);
        alert("Failed to add blackout date.");
      },
    });
  });
})(jQuery);
