(function ($) {
  "use strict";

  $(document).ready(function () {
    $(document).on("click", ".view-resource-link", function (e) {
      e.preventDefault();

      let allBookings = [];
      let oldid = document.getElementById("resource_id1");
      // let oldRecord = document.getElementById("time-occupied");

      $.get(
        "http://localhost/wordpress/wp-json/myapi/v1/bookings",
        function (data) {
          allBookings = data;
        },
      );

      if (oldid) {
        oldid.remove();
      }

      // if( oldRecord ) {
      //   oldRecord.remove();
      // }

      let resourceId = $(this).data("id");

      // let format = date + " " + time + ":00";
      // console.log(format);

      $("#start_time, #end_time").on("change", function () {
        let start_time = $("#start_time").val();
        let end_time = $("#end_time").val();

        let parts = start_time.split("T");
        let date = parts[0];
        // let time = parts[1];
        // let bookedData = [];

        $.get(
          `http://localhost/wordpress/wp-json/myapi/v1/booking/booked/${resourceId}/${date}`,
          function (data) {
            let bookedData = data;
            let html = "";

            if (bookedData.length === 0) {
              html = "";
            } else {
              bookedData.forEach(function (booking) {
                let start_time = booking.start_time;
                let end_time = booking.end_time;

                let end_time_arr = end_time.split(":");
                let start_time_arr = start_time.split(":");

                let sHours = parseInt(start_time_arr[0], 10);
                let sMinutes = start_time_arr[1];

                let eHours = parseInt(end_time_arr[0], 10);
                let eMinutes = end_time_arr[1];

                let period = sHours >= 12 ? "PM" : "AM";
                let period1 = eHours >= 12 ? "PM" : "AM";

                if (sHours > 12) {
                  sHours = sHours - 12;
                }

                if (eHours > 12) {
                  eHours = eHours - 12;
                }

                if (sHours == 0) {
                  sHours = 12;
                }

                if (eHours == 0) {
                  eHours = 12;
                }

                let updated_start_time = `${sHours}:${sMinutes} ${period}`;
                let updated_end_time = `${eHours}:${eMinutes}:${period1}`;

                console.log(updated_start_time);

                html += `
                    <div class="occupied-slot">
                        <span class="occupied-label">This slots are booked</span>
                        <strong>${updated_start_time} - ${updated_end_time}</strong>
                    </div>
                `;
              });
            }

            $("#time-occupied").html(html);
          },
        );

        let newStart = new Date(start_time).getTime();
        let newEnd = new Date(end_time).getTime();

        let submitbtn = document.getElementById("btn1");

        let statusBox = $("#ajax-slot-status1");

        if (newEnd <= newStart) {
          statusBox
            .text("End time must be later than start time.")
            .css({ background: "#ffebee", color: "#c62828" });
          submitbtn.style.display = "none";
          return;
        } else {
          statusBox.text("");
          submitbtn.removeAttribute("style");
        }

        let isOverlapping = false;

        allBookings.forEach(function (booking) {
          if (
            booking.resource_id == resourceId &&
            booking.status == "confirmed"
          ) {
            let bookedStart = new Date(booking.start_time).getTime();
            let bookedEnd = new Date(booking.end_time).getTime();

            if (newStart < bookedEnd && newEnd > bookedStart) {
              isOverlapping = true;
            }
          }
        });

        if (isOverlapping) {
          statusBox
            .text(
              "This time slot overlaps with an existing booking. Please choose another time.",
            )
            .css({
              background: "#ffebee",
              color: "#c62828",
              border: "1px solid #ef9a9a",
            });
          submitbtn.style.display = "none";
          return;
        } else {
          statusBox.style = "none";
          submitbtn.removeAttribute("style");
        }
      });
    });
  });
})(jQuery);
