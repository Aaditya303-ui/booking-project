(function ($) {
  "use strict";

  $(document).ready(function () {
    $(document).on("click", ".view-resource-link", function (e) {
      e.preventDefault();

      let allBookings = [];
      let oldid = document.getElementById("resource_id1");

      $.get(
        "http://localhost/wordpress/wp-json/myapi/v1/bookings",
        function (data) {
          allBookings = data; 
        },
      );

      if (oldid) {
        oldid.remove();
      }
      let resourceId = $(this).data("id");

        $("#start_time, #end_time").on("change", function () {
            let start_time = $("#start_time").val();
            let end_time = $("#end_time").val();

            console.log(start_time);

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
