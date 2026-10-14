(function ($) {
  "use strict";

  function loadResourceList() {
    $.get(
      "http://localhost/wordpress/wp-json/myapi/v1/resources",
      function (data) {
        let i = 0;
        let rowsHtml = "";

        while (i < data.length) {
          let title = data[i].title;
          let capacity = data[i]._capacity;

          rowsHtml += `<tr>
                        <td>${data[i].id}</td>
                        <td>${title}</td>
                        <td>${capacity}</td>
                        <td>
                            <button class="view-resource-link" data-id="${data[i].id}">
                              view
                            </button>
                        </td>
                        <td>
                            <button class="update-resource-link" data-id="${data[i].id}">
                              update
                            </button>
                        </td>
                        <td>
                            <button class="delete-resource" data-id="${data[i].id}">
                              Delete
                            </button>
                        </td>
                      </tr>`;
          i++;
        }
        $("#mycontent").html(rowsHtml);
      },
    );
  }

  loadResourceList();

  $(document).on("click", ".view-resource-link", function (e) {
    e.preventDefault();
    const resourceId = $(this).data("id");

    $.get(
      `http://localhost/wordpress/wp-json/myapi/v1/resource/${resourceId}`,
      function (resource) {
        let title = resource[0].title;
        let content = resource[0].content;
        let capacity = resource[0]._capacity;
        let thumbnail = resource[0].thumbnail;

        $("#modal-title").text(title);
        $("#modal-content").html(content);
        $("#modal-capacity").text(capacity);

        if (thumbnail) {
          $("#modal-image-container").html(
            `<img src="${thumbnail}" style="max-width: 100%; height: auto; border-radius: 4px; margin-top: 10px;" />`,
          );
        } else {
          $("#modal-image-container").html("");
        }

        $("#resource-modal").fadeIn(200);
      },
    );
  });

  $(document).on("click", ".delete-resource", function (e) {
    e.preventDefault();
    const resourceId = $(this).data("id");

    $.ajax({
      url: `http://localhost/wordpress/wp-json/myapi/v1/resource/${resourceId}`,
      type: "DELETE",
      beforeSend: function (xhr) {
        if (
          typeof bookingApiSettings !== "undefined" &&
          bookingApiSettings.nonce
        ) {
          xhr.setRequestHeader("X-WP-Nonce", bookingApiSettings.nonce);
        }
        console.log(bookingApiSettings);
      },
      success: function () {
        alert("Resource deleted successfully!");
        loadResourceList();
      },
      error: function (xhr) {
        console.error("Delete failed:", xhr.responseText);
        alert("Failed to delete the resource. Check console for details.");
      },
    });
  });

  $(document).on("submit", function (e) {
    e.preventDefault();

    let formData = new FormData();

    let title = document.getElementById("resource_title").value;
    let content = document.getElementById("resource_content").value;
    let capacity = document.getElementById("resource_capacity").value;
    let file = document.getElementById("file").value;

    if (title.trim() === "") {
      $("#titleerr").text("* title is required");
    } else {
      $("#titleerr").text("");
    }

    if (content.trim() === "") {
      $("#contenterr").text("* content is required");
    } else {
      $("#contenterr").text("");
    }

    if (capacity.trim() < 4) {
      $("#capacityerr").text("* capacity minimum 4 is required");
    } else {
      $("#capacityerr").text("");
    }

    if (file.trim() === "") {
      $("#fileerr").text("* file is required");
    } else {
      $("#fileerr").text("");
    }

    formData.append("title", title);
    formData.append("content", content);
    formData.append("capacity", capacity);

    let fileInput = $("#file")[0].files[0];
    if (fileInput) {
      formData.append("file", fileInput);
    }

    $.ajax({
      url: `http://localhost/wordpress/wp-json/myapi/v1/resource`,
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
        alert("Resource added successfully!");
        $("#add-resource-form")[0].reset();
        $("#titleerr").text("");
        $("#contenterr").text("");
        $("#fileerr").text("");
      },
      error: function (xhr) {
        console.error("Add failed:", xhr.responseText);
        //  alert("Failed to add resource. Check console for details.");
      },
    });
  });

  $(document).on("click", ".booking-modal-close", function () {
    $("#resource-modal").fadeOut(200);
  });

  $(document).on("click", ".update-resource-link", function (e) {
    e.preventDefault();
    const resourceId = $(this).data("id");

    $.get(
      `http://localhost/wordpress/wp-json/myapi/v1/resource/${resourceId}`,
      function (resource) {
        let title = resource[0].title;
        let content = resource[0].content;
        let capacity = resource[0]._capacity;
        let thumbnail = resource[0].thumbnail;

        $("#edit-resource-id").val(resourceId);
        $("#edit-resource-title").val(title);
        $("#edit-resource-content").val(content);
        $("#edit-resource-capacity").val(capacity);

        if (thumbnail) {
          $("#edit-image-preview").html(`
                <p style="font-size: 12px; color: #666; margin: 0 0 5px 0;">Current Image:</p>
                <img src="${thumbnail}" id="curr_img" style="max-width: 80px; height: auto; border-radius: 4px;" />
            `);
        } else {
          $("#edit-image-preview").html(
            "<p style='font-size: 12px; color: #666;'>No image uploaded</p>",
          );
        }

        $("#edit-resource-file").val("");

        $("#resource-edit-modal").fadeIn(200);
        console.log(resource);
      },
    );

    $(document).on("submit", "#resource-edit-form", function (e) {
      e.preventDefault();

      const resourceId = $("#edit-resource-id").val();
      let formData = new FormData();

      formData.append("title", $("#edit-resource-title").val());
      formData.append("content", $("#edit-resource-content").val());
      formData.append("_capacity", $("#edit-resource-capacity").val());

      let fileInput = $("#edit-resource-file")[0].files[0];
      if (fileInput) {
        formData.append("file", fileInput);
      }

      $.ajax({
        url: `http://localhost/wordpress/wp-json/myapi/v1/resource/${resourceId}`,
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
          alert("Resource updated successfully!");
          $("#profileDisplay").attr("src", "");
          $("#resource-edit-modal").fadeOut(200);
          loadResourceList();
        },
        error: function (xhr) {
          console.error("Update failed:", xhr.responseText);
          alert("Failed to update resource. Check console for details.");
        },
      });
    });
  });

  // Image preview code

  $(document).on("change", "#file", function (e) {
    if (e.target.files && e.target.files[0]) {
      let reader = new FileReader();
      reader.onload = function (event) {
        let img = document.getElementById("profileDisplay");
        img.src = event.target.result;
        img.style.display = "block";
      };
      reader.readAsDataURL(e.target.files[0]);
    }
    console.log("Yess whose this");
  });

  
  $(document).on("change", "#edit-resource-file", function (e) {
    if (e.target.files && e.target.files[0]) {
      let reader = new FileReader();
      reader.onload = function (event) {
        let img = document.getElementById("curr_img");
        let upimg = document.getElementById("profileDisplay");
        upimg.src = event.target.result;
        upimg.style.display = "block";
        img.style.display = "none";
      };
      reader.readAsDataURL(e.target.files[0]);
    }
    console.log("I am triggered");
  });

  $(window).on("click", function (e) {
    if ($(e.target).is("#resource-edit-modal")) {
      $("#resource-edit-modal").fadeOut(200);
      $("#profileDisplay").attr("src", "");
    }
  });

  $(window).on("click", function (e) {
    if ($(e.target).is("#resource-modal")) {
      $("#resource-modal").fadeOut(200);
    }
  });
})(jQuery);
