(function ($) {
  "use strict";

  let blackouts = [];

  function loadblackoutData(page = 1) {
    let search = ($("#search_reason").val() ?? "").trim();
    let blackout_date = ($("#select_date").val() ?? "").trim();
    let sort_by = ($("#sort_by").val() ?? "").trim();
    let month = ($("#month").val() ?? "").trim();

    let newUri = `${bookingApiSettings.restUrl}blackout`;

    let params = new URLSearchParams();

    if (search !== "") {
      params.append("search", search);
    }

    if(page !== "") {
      params.append("page",page);
    }

    if (blackout_date !== "") {
      params.append("blackout_date", blackout_date);
    }

    if (sort_by !== "") {
      params.append("sort_by", sort_by);
    }

    if (month !== "") {
      params.append("month", month);
    }

    newUri += "?" + params.toString();

    console.log(newUri);
    
    $.get(`${newUri}`, function (data) {
      blackouts = data["data"];

      let i = 0;
      let rowsHtml = "";
      while (i < blackouts.length) {
        let blackout_id = blackouts[i]["blackout_id"];
        let blackout_date = blackouts[i]["date"];
        let reason = blackouts[i]["reason"];

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

      let counts = data["counts"];
      let limit = 4;

      let total_pages = Math.ceil(counts / limit);

      let pagLink = "";

      for (let i = 1; i <= total_pages; i++) {
        if (i == page) {
          pagLink += `<li class='active' >
            <a
            class='page-link'  
            id='page1' 
            data-lid=${i}>${i}</a>
            </li>`;
        } else {
          pagLink += `<li>
            <a 
            class='page-link' 
            id='page1' data-lid=${i}>${i}</a>
            </li>`;
        }
      }

      $("#pagination-link").html(pagLink);

      $("#mycontent2").html(rowsHtml);
    });
  }

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
          url: `${bookingApiSettings.restUrl}blackout/${id}`,
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
      url: `${bookingApiSettings.restUrl}blackout`,
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

  $(document).on(
    "change",
    "#select_date, #sort_by, #month",
    function () {
      loadblackoutData(1);
    },
  );

  $(document).on("click","#searchbtn, #search_reason", function(){
    loadblackoutData(1);
  });

  $(document).on("click", "#page1", function () {
    let page = Number($(this).data("lid")) ?? 1;
    console.log(page);

    loadblackoutData(page);
  });
})(jQuery);
