(function ($) {
  "use strict";

  let booking = [];

  function loadRequestData() {
    $.get(
      "http://localhost/wordpress/wp-json/myapi/v1/booking/requests",
      function (data) {
        let i = 0;
        let rowsHtml = "";
        booking = data;
        //console.log(booking);

        while (i < data.length) {
          let id = data[i].id;
          let post_title = data[i].post_title;
          let requester_name = data[i].requester_name;
          let start_time = data[i].start_time;
          let end_time = data[i].end_time;
          let status = data[i].status;

          rowsHtml += `<tr>
                          <td>${id}</td>
                          <td>${post_title}</td>
                          <td>${requester_name}</td>
                          <td>${start_time}</td>
                          <td>${end_time}</td>
                          <td>${status}</td>
                          <td>
                            <button id="view-btn" data-id=${id}>
                              view
                            </button>
                          <td>
                        </tr>`;
          i++;
        }
        $("#mycontent1").html(rowsHtml);
      },
    );
  }

  loadRequestData();

  $(document).on("click", "#view-btn", function (e) {
    e.preventDefault();

    let id1,
      post_title1,
      requester_name1,
      start_time1,
      end_time1,
      status1,
      requester_email1;

    let oldid = document.getElementById("request_Id");

    if (oldid) {
      oldid.remove();
    }

    let resourceId = $(this).data("id");

    const newHiddenInput = document.createElement("input");
    const form = document.getElementById("view-request");

    newHiddenInput.type = "hidden";
    newHiddenInput.name = "requestId1";
    newHiddenInput.value = resourceId;
    newHiddenInput.id = "request_Id";

    form.appendChild(newHiddenInput);

    $("#view-request").fadeIn(400);

    let i = 0;
    while (i < booking.length) {
      if (resourceId == booking[i]["id"]) {
        id1 = booking[i]["id"];
        post_title1 = booking[i]["post_title"];
        requester_name1 = booking[i]["requester_name"];
        start_time1 = booking[i]["start_time"];
        end_time1 = booking[i]["end_time"];
        status1 = booking[i]["status"];
        requester_email1 = booking[i]["requester_email"];
      }
      i++;
    }

    $("#display_request_id").html(`Booking Request #${id1}`);
    $("#resource_name").text(`${post_title1}`);
    $("#requester_name").text(`${requester_name1}`);
    $("#email").text(`${requester_email1}`);
    $("#from-date").text(start_time1);
    $("#to-date").text(end_time1);
    $("#bookingStatus").val(status1);

    $("#update-booking").attr("data-id", id1);
  });

  $(document).on("click", "#update-booking", function () {
    let id = $(this).data("id");
    let status = $("#bookingStatus").val();

    $.ajax({
      url: `http://localhost/wordpress/wp-json/myapi/v1/booking/${id}`,
      type: "PATCH",
      data: {
        status: status,
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

        $("#view-request").fadeOut(400);
        loadRequestData();
      },
      error: function (xhr) {
        console.error("Update failed:", xhr.responseText);
        alert("Failed to update booking status.");
      },
    });
  });

  $(document).on("click", "#close-request", function () {
    $("#view-request").fadeOut(400);
  });
})(jQuery);
