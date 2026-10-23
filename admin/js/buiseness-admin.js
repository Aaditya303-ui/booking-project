(function ($) {
  "use strict";

  function display_buisness_hours() {
    $.get(
      "http://localhost/wordpress/wp-json/myapi/v1/buisness-hours",
      function (data) {
        let i = 0;
        let rowsHtml = "";

        while (i < data.length) {
          let id = data[i]["id"];
          let day = data[i]["day"];
          let open_time = data[i]["open_time"];
          let close_time = data[i]["close_time"];
          let is_close = data[i]["is_closed"];

          let u_open_time  = $(`u_start_time-${id}`).val();
          let u_close_time = $(`u_close_time-${id}`).val();

          rowsHtml += `<tr>
                          <td>${id}</td>
                          <td>${day}</td>
                          <td>${open_time}</td>
                          <td>${close_time}</td>
                           <td>
                        <button class="view-buisness-link"
                            data-id="${id}">
                            update
                        </button>
                    </td>
                    <div
                        id="buisness-modal-${id}"
                        class="booking-modal-overlay"
                        style="display:none;">
                        <div class="booking-modal-content">

                            <span class="booking-modal-close">×</span>

                            <h2>Select Time</h2>

                            <div class="booking-field">
                                <label>Day</label>
                                <h3><${day}</h3>
                            </div>

                            <div class="booking-field">
                                <label>Start Time</label>
                                <input
                                    type="time"
                                    id="start-time-${id}"
                                    value="${u_open_time}">
                            </div>

                            <div class="booking-field">
                                <label>End Time</label>
                                <input
                                    type="time"
                                    id="end-time-${id}"
                                    value="${u_close_time}">
                            </div>

                            <button
                                data-id="${id}"
                                id="submit-buiseness-btn"
                                type="button">
                                Save
                            </button>
                        </div>
                    </div>
                        </tr>`;
          i++;
        }
        $("#mycontent3").html(rowsHtml);
      },
    );
  }

  $(document).on("click", ".view-buisness-link", function () {
    let id = $(this).data("id");

    $(`#buisness-modal-${id}`).fadeIn(300);
    console.log(id);
  });

  $(document).on("click", "#submit-buiseness-btn", function () {
    let id = $(this).data("id");

    let start_time = $(`#start-time-${id}`).val();
    let end_time = $(`#end-time-${id}`).val();

    $.ajax({
      url: `http://localhost/wordpress/wp-json/myapi/v1/buisness-hours/${id}`,
      type: "PATCH",
      data: {
        open_time: start_time,
        close_time: end_time
      },

      beforeSend: function (xhr) {
        if (
          typeof bookingApiSettings !== "undefined" &&
          bookingApiSettings.nonce
        ) {
          xhr.setRequestHeader("X-WP-Nonce", bookingApiSettings.nonce);
        }
      },

      success: function () {
        Swal.fire({
          icon: "success",
          title: "Success!",
          text: "The request status has been changed",
        });

        $(".booking-modal-overlay").fadeOut(400);
        display_buisness_hours();
      },

      error: function (xhr) {
        console.error("Update failed:", xhr.responseText);
        alert("Failed to update booking status.");
      },
    });
  });

  $(document).on("click", ".booking-modal-close", function () {
    $(this).closest(".booking-modal-overlay").fadeOut(300);
  });
})(jQuery);
