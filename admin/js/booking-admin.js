(function ($) {
  "use strict";

  function loadResourceList() {
    $.get(
      "http://localhost/wordpress/wp-json/myapi/v1/resources",
      function (data) {
        let i = 0;
        let rowsHtml = "";

        while (i < data.length) {
          let title = typeof data[i].title === 'object' ? data[i].title.rendered : data[i].title;
          let capacity = data[i]._capacity || data[i].capacity || 'N/A';

          rowsHtml += `<tr>
                        <td>${data[i].id}</td>
                        <td>${title}</td>
                        <td>${capacity}</td>
                        <td><button class="view-resource-link" data-id="${data[i].id}">view</button></td>
                      </tr>`;
          i++;
        }
        $("#mycontent").html(rowsHtml);
      }
    );
  }

  loadResourceList();
  
  $(document).on("click", ".view-resource-link", function (e) {
    e.preventDefault();
    const resourceId = $(this).data("id");

    $.get(
      `http://localhost/wordpress/wp-json/myapi/v1/resource/${resourceId}`,
      function (resource) {
        console.log(resource[0]._capacity);
        let title     = resource[0].title;
        let content   = resource[0].content;
        let capacity  = resource[0]._capacity;
        let thumbnail = resource[0].thumbnail;

        $("#modal-title").text(title);
        $("#modal-content").html(content);
        $("#modal-capacity").text(capacity);

        if (thumbnail) {
          $("#modal-image-container").html(
            `<img src="${thumbnail}" style="max-width: 100%; height: auto; border-radius: 4px; margin-top: 10px;" />`
          );
        } else {
          $("#modal-image-container").html("");
        }

        $("#resource-modal").fadeIn(200);
      }
    );
  });

  
  $(document).on("click", ".booking-modal-close", function () {
    $("#resource-modal").fadeOut(200);
  });

  
  $(window).on("click", function (e) {
    if ($(e.target).is("#resource-modal")) {
      $("#resource-modal").fadeOut(200);
    }
  });

})(jQuery);