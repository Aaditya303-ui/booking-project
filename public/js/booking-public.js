(function ($) {
  "use strict";

  $(document).ready(function () {
    $.get(`${bookingApiSettings.restUrl}resources`, function (data) {
      let i = 0;
      let cardsHtml = "";

      while (i < data.length) {
        let title = data[i].title;
        let capacity = data[i]._capacity;
        let content = data[i].content;
        let excerpt = content.slice(0, 30);

        cardsHtml += `
                <div class="resource-card" style="
                    width: 200px;
                    border: 1px solid #ddd;
                    border-radius: 10px;
                    overflow: hidden;
                    background: #fff;
                    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
                    margin: 10px;
                ">

                    <img
                        style="
                            width: 100%;
                            height: 160px;
                            object-fit: cover;
                            display: block;
                        "
                        class="images"
                        src="${data[i].thumbnail}"
                        alt="${title}"
                    >

                    <div class="resource-card-content" style="padding: 15px;">

                        <h3 style="
                            margin: 0 0 10px;
                            font-size: 18px;
                        ">${title}</h3>

                        <p style="
                            color: #666;
                            font-size: 14px;
                            margin: 0 0 12px;
                        ">${excerpt}</p>
                        
                        <p style="margin: 0 0 15px;">
                            <strong>Capacity:</strong> ${capacity} people
                        </p>

                        <button
                            class="view-resource-link"
                            data-id="${data[i].id}"
                            style="
                                width: 100%;
                                padding: 10px;
                                background: #2563eb;
                                color: white;
                                border: none;
                                border-radius: 6px;
                                cursor: pointer;
                            "
                        >
                            View
                        </button>

                    </div>
                </div>
        `;
        i++;
      }
      $("#content1").html(cardsHtml);
    });

    $(document).on("click", ".view-resource-link", function (e) {
      e.preventDefault();
      let oldid = document.getElementById("resource_Id");

      let data2 = [];

      if (oldid) {
        oldid.remove();
      }
      let resourceId = $(this).data("id");
      console.log(resourceId);

      const newHiddenInput = document.createElement("input");
      const form = document.getElementById("booking-form");

      newHiddenInput.type = "hidden";
      newHiddenInput.name = "resourceId1";
      newHiddenInput.value = resourceId;
      newHiddenInput.id = "resource_Id";

      let data = newHiddenInput;
      form.appendChild(data);

      $.get(
        `${bookingApiSettings.restUrl}resource/${resourceId}`,
        function (resource) {
          data2 = resource;

          let i = 0;

          while (i < data2.length) {
            let title = data2[i].title;
            let content = data2[i].content;
            let capacity = data2[i]._capacity;
            let thumbnail = data2[i].thumbnail;

            $("#booking-resource-title").text(title);
            $("#booking-resource-content").html(content);
            $("#booking-resource-capacity").text(capacity);

            if (thumbnail) {
              $("#booking-resource-image").html(`
                    <img
                        src="${thumbnail}"
                        style="
                            width: 100%;
                            max-height: 250px;
                            object-fit: cover;
                            border-radius: 8px;
                        "
                    >
                `);
            } else {
              $("#booking-resource-image").html("");
            }
            $("#booking-resource-id").val(resourceId);
            i++;
          }
          $("#booking-modal").fadeIn(200);
        },
      );
    });

    $(document).on("submit", "#booking-form", function (e) {
      e.preventDefault();

      let formData = new FormData();

      let resource_id = $("#resource_Id").val() ?? "";
      let requester_name = $("#requester_name").val() ?? "";
      let requester_email = $("#requester_email").val() ?? "";
      let start_time = $("#start_time").val() ?? "";
      let end_time = $("#end_time").val() ?? "";
      let date = $("#date").val() ?? "";

      formData.append("resource_id", resource_id);
      formData.append("requester_name", requester_name);
      formData.append("requester_email", requester_email);
      formData.append("start_time", start_time);
      formData.append("end_time", end_time);
      formData.append("date", date);

      const dataObject = Object.fromEntries(formData.entries());
      const jsonString = JSON.stringify(dataObject);
      console.log(jsonString);

      $.ajax({
        url: `http://localhost/wordpress/wp-json/myapi/v1/booking`,
        type: "POST",
        data: formData,
        processData: false,
        contentType: false,
        beforeSend: function (xhr) {
          if (
            typeof bookingApiSettings !== "undefined" &&
            bookingApiSettings.nonce
          ) {
            xhr.setRequestHeader("X-WP-Nonce", bookingApiSettings.nonce);
          }
          console.log(bookingApiSettings);
        },
        success: function (response) {
          $("#booking-modal").fadeOut(200);
          $("#resource_id1").val("");
          $("#requester_name").val("");
          $("#requester_email").val("");
          $("#start_time").val("");
          $("#end_time").val("");
          $("#requester_name").val("");
          $("#date").val("");

          Swal.fire({
            icon: "success",
            title: "Success!",
            text: "You will be notified soon",
          });
        },
      });
    });

    $(document).on("change", "#searchRes, #filter", function () {
      let search = ($("#searchRes").val() ?? "").trim();
      let sortBy = ($("#filter").val() ?? "").trim();

      let newUri = `${bookingApiSettings.restUrl}resourcess/categorise`;

      let params = new URLSearchParams();

      if (search !== "") {
        params.append("search", search);
      }

      if (sortBy !== "") {
        params.append("sortBy", sortBy);
      }

      newUri += "?" + params.toString();

      $.get(`${newUri}`, function (data) {
        let i = 0;
        let cardsHtml = "";

        while (i < data.length) {
          let title = data[i].title;
          let capacity = data[i]._capacity;
          let content = data[i].content;
          let excerpt = content.slice(0, 30);

          cardsHtml += `
                <div class="resource-card" style="
                    width: 200px;
                    border: 1px solid #ddd;
                    border-radius: 10px;
                    overflow: hidden;
                    background: #fff;
                    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
                    margin: 10px;
                ">

                    <img
                        style="
                            width: 100%;
                            height: 160px;
                            object-fit: cover;
                            display: block;
                        "
                        class="images"
                        src="${data[i].thumbnail}"
                        alt="${title}"
                    >

                    <div class="resource-card-content" style="padding: 15px;">

                        <h3 style="
                            margin: 0 0 10px;
                            font-size: 18px;
                        ">${title}</h3>

                        <p style="
                            color: #666;
                            font-size: 14px;
                            margin: 0 0 12px;
                        ">${excerpt}</p>
                        
                        <p style="margin: 0 0 15px;">
                            <strong>Capacity:</strong> ${capacity} people
                        </p>

                        <button
                            class="view-resource-link"
                            data-id="${data[i].id}"
                            style="
                                width: 100%;
                                padding: 10px;
                                background: #2563eb;
                                color: white;
                                border: none;
                                border-radius: 6px;
                                cursor: pointer;
                            "
                        >
                            View
                        </button>

                    </div>
                </div>
        `;
          i++;
        }
        $("#content1").html(cardsHtml);
      });
    });
  });
})(jQuery);
