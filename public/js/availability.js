(function ($) {
  "use strict";

  $(document).ready(function () {
    $(document).on("click", ".view-resource-link", function () {
      let allBookings = [];
      let blackout = [];
      let allBuisnessHours = [];

      let oldid = document.getElementById("resource_id1");
      // let oldRecord = document.getElementById("time-occupied");

      $.get(
        "http://localhost/wordpress/wp-json/myapi/v1/bookings",
        function (data) {
          allBookings = data;
        },
      );

      $.get(
        `http://localhost/wordpress/wp-json/myapi/v1/blackout`,
        function (data) {
          blackout = data;
        },
      );

      $.get(
        `http://localhost/wordpress/wp-json/myapi/v1/buisness-hours`,
        function (data) {
          allBuisnessHours = data;
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

      $(document).on("change", "#date", function () {
        let date = $("#date").val();
        let statusBox = $("#ajax-slot-status1");
        let submitbtn = $("#btn1");

        let u_date = new Date(date);
        let weekday = u_date.toLocaleDateString("en-US", { weekday: "long" });
        let lweekday = weekday.toLowerCase();

        allBuisnessHours.forEach(function (data) {
          if (data.day == lweekday) {
            let buisness_start = data.open_time;
            let buiseness_end = data.close_time;
            console.log("Selected weekday:", lweekday);
            console.log("Business day:", data.day);
            console.log("Business start:", buisness_start);
            console.log("Business end:", buiseness_end);

            $("#buisness-hours").text(
              `Select within ${buisness_start} - ${buiseness_end}`,
            );
          }
        });

        $.get(
          `http://localhost/wordpress/wp-json/myapi/v1/booking/booked/${resourceId}/${date}`,
          function (data) {
            let bookedData = data;
            let html = "";

            let isBlackOut = false;
            blackout.forEach(function (b) {
              if (date == b.exdate) {
                isBlackOut = true;
              }
            });

            if (isBlackOut) {
              statusBox.text("We are closed durring that day").css({
                display: "block",
                background: "#ffebee",
                color: "#c62828",
                border: "1px solid #ef9a9a",
              });
              $("#time-occupied").text("");
              submitbtn.css({
                display: "none",
              });
              return;
            } else {
              statusBox.css({
                display: "none",
              });
              submitbtn.css({
                display: "block",
              });
            }

            if (bookedData.length === 0) {
              html = "";
            } else {
              bookedData.forEach(function (booking) {
                let start_time = booking.start_time;
                let end_time = booking.end_time;

                let updated_start_time = `${start_time}`;
                let updated_end_time = `${end_time}`;

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
      });

      $("#start_time, #end_time").on("change", function () {
        let statusBox = $("#ajax-slot-status1");
        let submitbtn = $("#btn1");

        let date = $("#date").val();
        let start_time = $("#start_time").val();
        let end_time = $("#end_time").val();

        let u_date = new Date(date);
        let weekday = u_date.toLocaleDateString("en-US", { weekday: "long" });
        let lweekday = weekday.toLowerCase();

        console.log(allBuisnessHours);
        console.log(lweekday);

        $("#end-time").css("display", "block");

        if (end_time !== "") {
          if (end_time <= start_time) {
            statusBox.text("End time must be later than start time.").css({
              display: "block",
              background: "#ffebee",
              color: "#c62828",
            });

            submitbtn.css({
              display: "none",
            });
            return;
          } else {
            statusBox.css({
              display: "none",
            });
            submitbtn.css({
              display: "block",
            });
          }
        }

        let NotinBuisnessHours = false;

        allBuisnessHours.forEach(function (buisnesshours) {
          if (buisnesshours.day == lweekday) {
            let b_start_time = buisnesshours.open_time;
            let b_close_time = buisnesshours.close_time;

            let [stime, speriod]   = b_start_time.split(/\s+/);
            let [shours, sminutes] = stime.split(":");

            let [etime, eperiod]   = b_close_time.split(/\s+/);
            let [ehours, eminutes] = etime.split(":");

            shours = parseInt(shours, 10);
            ehours = parseInt(ehours, 10);

            if (eperiod === "PM" && ehours !== 12) {
              ehours += 12;
            }

            if (eperiod === "AM" && ehours === 12) {
              ehours = 0;
            }

            if (speriod === "PM" && shours !== 12) {
              shours += 12;
            }

            if (speriod === "AM" && shours === 12) {
              shours = 0;
            }

            let new_start_time = `${String(shours).padStart(2,"0")}:${sminutes}`;
            let new_end_time = `${String(ehours).padStart(2,"0")}:${eminutes}`;

            
            if (start_time >= new_start_time && end_time <= new_end_time) {
              NotinBuisnessHours = false;
            } else {
              NotinBuisnessHours = true;
            }
          }
        });

        if (NotinBuisnessHours) {
          statusBox.text(`Please select a slot within business hours`).css({
            display: "block",
            background: "#ffebee",
            color: "#c62828",
            border: "1px solid #ef9a9a",
          });
          submitbtn.css({
            display: "none",
          });
          return;
        } else {
          statusBox.css({
            display: "none",
          });
          submitbtn.css({
            display: "block",
          });
        }

        // Check for overlapping bookings
        let isOverlapping = false;

        allBookings.forEach(function (booking) {
          if (
            booking.date == date &&
            booking.status == "confirmed" &&
            booking.resource_id == resourceId
          ) {
            console.log("Hell there");
            let booked_start = booking.start_time;
            let booked_end = booking.end_time;

            if (start_time < booked_end && end_time > booked_start) {
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
              display: "block",
              background: "#ffebee",
              color: "#c62828",
              border: "1px solid #ef9a9a",
            });
          submitbtn.css({
            display: "none",
          });
          return;
        } else {
          statusBox.css({
            display: "none",
          });
          submitbtn.css({
            display: "block",
          });
        }
      });
    });
  });
})(jQuery);
