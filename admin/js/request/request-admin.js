(function ($) {
  "use strict";

  let booking = [];

  function demo() {
    $.get(
      `${bookingApiSettings.restUrl}booking/requests`,
      function (data) {
        booking = data;
      },
    );
  }

  demo();

  function loadRequestData() {
    $.get(
      `${bookingApiSettings.restUrl}booking/requests`,
      function (data) {
        booking = data;

        let i = 0;
        let rowsHtml = "";

        while (i < booking.length) {
          let id = booking[i].id;
          let post_title = booking[i].post_title;
          let requester_name = booking[i].requester_name;
          let start_time = booking[i].start_time;
          let end_time = booking[i].end_time;
          let status = booking[i].status;

          let statusDropdown = "";

          if (status === "expired") {
            statusDropdown = `
            <select id="bookingStatus" data-id="${id}">
                <option selected value="expired">Expired</option>
            </select>
        `;
          } else {
            statusDropdown = `
            <select id="bookingStatus" data-id="${id}">
                <option value="pending" ${status === "pending" ? "selected" : ""}>
                    Pending
                </option>
                <option value="confirmed" ${status === "confirmed" ? "selected" : ""}>
                    Confirmed
                </option>
                <option value="cancelled" ${status === "cancelled" ? "selected" : ""}>
                    Cancelled
                </option>
            </select>
        `;
          }

          rowsHtml += `<tr>
                        <td>${id}</td>
                        <td>${post_title}</td>
                        <td>${requester_name}</td>
                        <td>${start_time}</td>
                        <td>${end_time}</td>
                        <td>${statusDropdown}</td>
                        <td>
                            <button class="view-btn" data-id="${id}">
                                View
                            </button>
                        </td>
                    </tr>`;
          i++;
        }

        $("#mycontent1").html(rowsHtml);
      },
    );
  }

  $(document).on("click", ".view-btn", function () {
    let id1,
      post_title1,
      requester_name1,
      start_time1,
      end_time1,
      status1,
      requester_email1,
      date1,
      resourceId1;

    let oldid = document.getElementById("request_Id");

    if (oldid) {
      oldid.remove();
    }

    let id = $(this).data("id");

    $("#view-request").fadeIn(400);

    let i = 0;
    while (i < booking.length) {
      if (id == booking[i]["id"]) {
        id1 = booking[i]["id"];
        post_title1 = booking[i]["post_title"];
        requester_name1 = booking[i]["requester_name"];
        start_time1 = booking[i]["start_time"];
        end_time1 = booking[i]["end_time"];
        status1 = booking[i]["status"];
        requester_email1 = booking[i]["requester_email"];
        date1 = booking[i]["date"];
        resourceId1 = booking[i]["resource_id"];
      }
      i++;
    }

    const newHiddenInput = document.createElement("input");
    const form = document.getElementById("view-request");

    newHiddenInput.type = "hidden";
    newHiddenInput.name = "requestId1";
    newHiddenInput.value = resourceId1;
    newHiddenInput.id = "request_Id";

    form.appendChild(newHiddenInput);
    // console.log(booking);

    $("#display_request_id").html(`Booking Request #${id1}`);
    $("#resource_name").text(`${post_title1}`);
    $("#requester_name").val(`${requester_name1}`);
    $("#email").val(`${requester_email1}`);
    $("#from-date").val(start_time1);
    $("#to-date").val(end_time1);
    $(`#bookingStatus`).val(status1);
    $("#date").val(date1);
    $("#update-booking").attr("data-id", id1);
    $("#resource_id").val(resourceId1);
  });

  $(document).on("change", "#bookingStatus", function () {
    let id = $(this).data("id");
    let status = $(this).val();

    console.log(status);

    $.ajax({
      url: `${bookingApiSettings.restUrl}booking/${id}`,
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
        loadRequestData();
        Swal.fire({
          icon: "success",
          title: "Success!",
          text: "The request status has been changed",
        });

        $("#view-request").fadeOut(400);
      },
      error: function (xhr) {
        console.error("Update failed:", xhr.responseText);
        alert("Failed to update booking status.");
      },
    });
  });

  $(document).on("click", "#update-booking", function () {
    let id = $(this).data("id");
    // let status = $("#bookingStatus").val();

    let requester_name = $("#requester_name").val();
    let email = $("#email").val();
    let date = $("#date").val();
    let from = $("#from-date").val();
    let to = $("#to-date").val();
    let resource_id = $("#request_Id").val();

    let data1 = {
      resource_id: resource_id,
      requester_name: requester_name,
      requester_email: email,
      date: date,
      start_time: from,
      end_time: to,
    };

    if (validateUpdateBooking(requester_name, email, date, from, to)) {
      console.log(data1);
        $.ajax({
        url: `${bookingApiSettings.restUrl}booking/${id}`,
        type: "PATCH",
        data: {
          resource_id: resource_id,
          requester_name: requester_name,
          requester_email: email,
          date: date,
          start_time: from,
          end_time: to,
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
    } else {
      return;
    }
  });

  $(document).on("click", "#close-request", function () {
    $("#view-request").fadeOut(400);
    $("#RequesternameErr").text("");
    $("#RemailErr").text("");
    $("#RdateErr").text("");
    $("#RfromErr").text("");
    $("#RtoErr").text("");
  });

  $(document).on(
    "change input",
    "#searchbooking ,#statusFilter, #sortBy, #resources",
    function () {
      let status = ($("#statusFilter").val() ?? "").trim();
      let resource = ($("#resources").val() ?? "").trim();
      let sortBy = ($("#sortBy").val() ?? "").trim();
      let search = ($("#searchbooking").val() ?? "").trim();

      let newUri = `${bookingApiSettings.restUrl}booking/requests`;
      let params = new URLSearchParams();

      if (status !== "") {
        params.append("status", status);
      }

      if (resource !== "") {
        params.append("resource_id", resource);
      }

      if (sortBy !== "") {
        params.append("sortBy", sortBy);
      }

      if (search !== "") {
        params.append("search", search);
      }

      newUri += "?" + params.toString();

      console.log(newUri);

      $.get(`${newUri}`, function (data) {
        booking = data;

        let i = 0;
        let rowsHtml = "";

        while (i < booking.length) {
          let id = booking[i].id;
          let post_title = booking[i].post_title;
          let requester_name = booking[i].requester_name;
          let start_time = booking[i].start_time;
          let end_time = booking[i].end_time;
          let status = booking[i].status;

          let statusDropdown = "";

          if (status === "expired") {
            statusDropdown = `
            <select id="bookingStatus" data-id="${id}">
                <option selected value="expired">Expired</option>
            </select>
        `;
          } else {
            statusDropdown = `
            <select id="bookingStatus" data-id="${id}">
                <option value="pending" ${status === "pending" ? "selected" : ""}>
                    Pending
                </option>
                <option value="confirmed" ${status === "confirmed" ? "selected" : ""}>
                    Confirmed
                </option>
                <option value="cancelled" ${status === "cancelled" ? "selected" : ""}>
                    Cancelled
                </option>
            </select>
        `;
          }

          rowsHtml += `<tr>
                        <td>${id}</td>
                        <td>${post_title}</td>
                        <td>${requester_name}</td>
                        <td>${start_time}</td>
                        <td>${end_time}</td>
                        <td>${statusDropdown}</td>
                        <td>
                            <button class="view-btn" data-id="${id}">
                                View
                            </button>
                        </td>
                    </tr>`;
          i++;
        }

        $("#mycontent1").html(rowsHtml);
      });
    },
  );
})(jQuery);
