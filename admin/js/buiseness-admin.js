(function ($) {
  "use strict";

  $.get(
    "http://localhost/wordpress/wp-json/myapi/v1/buisness",
    function (data) {
      let i = 0;
      let rowsHtml = "";

      while (i < data.length) {
        let id = data[i]["id"];
        let day = data[i]["day"];
        let open_time = data[i]["open_time"];
        let close_time = data[i]["close_time"];
        let is_close = data[i]["is_closed"];

        rowsHtml += `<tr>
                        <td>${id}</td>
                        <td>${day}</td>
                        <td>${open_time}</td>
                        <td>${close_time}</td>
                        <td>
                            <button class="view-buisness-link" data-id="${id}">
                              update
                            </button>
                        </td>
                      </tr>`;
        i++;
      }
      $("#mycontent3").html(rowsHtml);
    },
  );
})(jQuery);
