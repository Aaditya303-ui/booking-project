(function ($) {
  "use strict";

  let all_data = [];
  let cat_data = [];

  function loadResourceList() {
    $.get(`${bookingApiSettings.restUrl}resources`, function (data) {
      let i = 0;
      let rowsHtml = "";
      all_data = data;
      while (i < all_data.length) {
        let id = all_data[i].id;
        let title = all_data[i].title;
        let content = all_data[i].content;
        let capacity = all_data[i]._capacity;
        let thumbnail = all_data[i].thumbnail;

        rowsHtml += `<tr>
                    <td>${id}</td>
                    <td>${title}</td>
                    <td>${capacity}</td>
                    <td>
                        <button
                            class="view-resource-link"
                            data-id="${id}">
                            view
                        </button>
                        <div
                            id="resource-modal-${id}"
                            class="booking-modal-overlay"
                            style="display:none;">
                            <div class="booking-modal-content">
                                <span class="booking-modal-close">×</span>
                                <h2>
                                    ${title}
                                </h2>
                                <div>
                                    ${content}
                                </div>
                                <p>
                                    <strong>Capacity:</strong>
                                    ${capacity}
                                </p>
                                <?php if (!empty(${thumbnail})) { ?>
                                    <img
                                        src="${thumbnail}"
                                        alt="">
                                <?php } ?>
                            </div>
                        </div>
                    </td>
                    <td>
                        <button
                            class="update-resource-link"
                            data-id="${id}">
                            update
                        </button>
                    </td>
                    <div id="resource-edit-modal-${id}" class="booking-modal-overlay" style="display: none;">
                        <div class="booking-modal-content">
                            <h2 style="margin-top: 0;">Edit Resource</h2>

                            <form id="resource-edit-form" enctype="multipart/form-data">
                                <input type="hidden"
                                    id="edit-resource-id"
                                    value="${id}">
                                <p>
                                    <label for="edit-resource-title"><strong>Title:</strong></label><br>

                                    <?php if (!empty(${thumbnail})) { ?>
                                        <input
                                            type="text"
                                            id="edit-resource-title"
                                            class="regular-text"
                                            value="${title}"
                                            style="width: 100%; padding: 6px; margin-top: 5px;">
                                    <?php } ?>
                                <p style="color:red" id="titleerr"></p>
                                </p>
                                <p>
                                    <label for="edit-resource-content"><strong>Content:</strong></label><br>
                                    <textarea
                                        id="edit-resource-content"
                                        rows="4"
                                        style="width: 100%; padding: 6px; margin-top: 5px;"><?php echo esc_textarea(${content}); ?></textarea>
                                <p style="color:red" id="contenterr"></p>
                                </p>

                                <p>
                                    <label><strong>Featured Image:</strong></label><br>
                                <div id="edit-image-preview" style="margin: 8px 0;"></div>
                                <input
                                    type="hidden"
                                    id="old-image-${id}"
                                    value="${thumbnail}">
                                <img
                                    id="profileDisplay-${id}"
                                    src="${thumbnail}"
                                    width="200">
                                <input type="file" id="edit-resource-file" accept="image/*" style="margin-top: 5px;">
                                </p>
                                <p>
                                    <label for="edit-resource-capacity"><strong>Capacity:</strong></label><br>
                                    <input
                                        type="number"
                                        id="edit-resource-capacity"
                                        class="regular-text"
                                        style="width: 100%; padding: 6px; margin-top: 5px;"
                                        value="${capacity}">
                                <p style="color:red" id="capacityerr"></p>
                                </p>

                                <p style="margin-top: 20px; text-align: right;">
                                    <button type="submit" class="button button-primary work">Save Changes</button>
                                </p>
                            </form>
                        </div>
                    </div>
                    <td>
                        <button
                            class="delete-resource"
                            data-id="${id}">
                            Delete
                        </button>
                    </td>
                </tr>`;
        i++;
      }
      $("#mycontent").html(rowsHtml);
    });
  }




  $(document).on("click", ".view-resource-link", function () {
    const resourceId = $(this).data("id");

    $(`#resource-modal-${resourceId}`).fadeIn(200);

    $(document).on("click", ".booking-modal-close", function () {
      $(`#resource-modal-${resourceId}`).fadeOut(200);
    });

    $(window).on("click", function (e) {
      if ($(e.target).is(`#resource-modal-${resourceId}`)) {
        $(`#resource-modal-${resourceId}`).fadeOut(200);
      }
    });
  });

  $(document).on("click", ".delete-resource", function (e) {
    e.preventDefault();
    const resourceId = $(this).data("id");

    Swal.fire({
      icon: "warning",
      title: "Are you sure?",
      text: "Do you want to delete this resource data",
      showCancelButton: true,
      confirmButtonText: "Yes, delete it!",
      cancelButtonText: "Cancel",
      confirmButtonColor: "#d33",
      cancelButtonColor: "#3085d6",
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          url: `${bookingApiSettings.restUrl}resource/${resourceId}`,
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
          success: function (response) {
            if (response.success) {
              Swal.fire({
                icon: "success",
                title: "Deleted!",
                text: "The resource data has been deleted",
              });

              loadResourceList();
            } else {
              Swal.fire({
                icon: "error",
                title: "Failed!",
                text: response.message || "Could not delete the resource data.",
              });

              loadResourceList();
            }
          },
          error: function (xhr) {
            console.error("Delete failed:", xhr.responseText);
            Swal.fire({
              icon: "error",
              title: "Error!",
              text: "Failed to delete the blackout date.",
            });

            loadResourceList();
          },
        });
      }
    });
  });

  $(document).on("submit", "#add-resource-form", function (e) {
    e.preventDefault();

    let formData = new FormData();

    let title = document.getElementById("resource_title").value ?? "";
    let content = document.getElementById("resource_content").value ?? "";
    let capacity = document.getElementById("resource_capacity").value ?? 1;
    let file = document.getElementById("file").value ?? "";

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
      url: `${bookingApiSettings.restUrl}resource`,
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
        //console.log(bookingApiSettings);
      },
      success: function (response) {
        Swal.fire({
          icon: "success",
          title: "Added !",
          text: "The resource data has been Updated successfully",
        });
        $("#add-resource-form")[0].reset();
        $("#titleerr").text("");
        $("#contenterr").text("");
        $("#fileerr").text("");
        $("#profileDisplay").attr("src", "");
      },
      error: function (xhr) {
        console.error("Add failed:", xhr.responseText);
        //  alert("Failed to add resource. Check console for details.");
      },
    });
  });

  $(document).on("click", ".update-resource-link", function () {
    const resourceId = $(this).data("id");

    $(`#resource-edit-modal-${resourceId}`).fadeIn(200);

    $(document).on("click", ".booking-modal-close", function () {
      $(`resource-edit-modal-${resourceId}`).fadeOut(200);
      $("#profileDisplay").css({
        display: "block",
      });
    });

    $(window).on("click", function (e) {
      if ($(e.target).is(`#resource-edit-modal-${resourceId}`)) {
        let old_image = $(`#old-image-${resourceId}`).val();
        $(`#profileDisplay-${resourceId}`).attr("src", old_image);
        $(`#resource-edit-modal-${resourceId}`).fadeOut(200);
      }
    });

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
        url: `${bookingApiSettings.restUrl}resource/${resourceId}`,
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
          console.log(`${bookingApiSettings.restUrl}resources`);
        },
        success: function (response) {
          if (response.success) {
            Swal.fire({
              icon: "success",
              title: "Added !",
              text: "The resource data has been Updated successfully",
            });
            $("#profileDisplay").attr("src", "");
            $(`old-image-${resourceId}`).attr("value", fileInput);
            $(`#resource-edit-modal-${resourceId}`).fadeOut(200);
            loadResourceList();
          }
        },
        error: function (xhr) {
          console.error("Update failed:", xhr.responseText);
          alert("Failed to update resource. Check console for details.");
        },
      });
    });
  });

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
    let resourceId = $("#edit-resource-id").val();

    if (e.target.files && e.target.files[0]) {
      let reader = new FileReader();
      reader.onload = function (event) {
        let upimg = document.getElementById(`profileDisplay-${resourceId}`);
        upimg.src = event.target.result;
        upimg.style.display = "block";
      };
      reader.readAsDataURL(e.target.files[0]);
    }
  });

  // Filteration

  $(document).on("change", "#search, #sortBy, #capacity", function () {
    let search = ($("#search").val() ?? '').trim();
    let sortBy = ($("#sortBy").val() ?? '').trim();

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
      let rowsHtml = "";
      cat_data = data;
      
      while (i < cat_data.length) {
        let id = cat_data[i].id;
        let title = cat_data[i].title;
        let content = cat_data[i].content;
        let capacity = cat_data[i]._capacity;
        let thumbnail = cat_data[i].thumbnail;

        rowsHtml += `<tr>
                    <td>${id}</td>
                    <td>${title}</td>
                    <td>${capacity}</td>
                    <td>
                        <button
                            class="view-resource-link"
                            data-id="${id}">
                            view
                        </button>
                        <div
                            id="resource-modal-${id}"
                            class="booking-modal-overlay"
                            style="display:none;">
                            <div class="booking-modal-content">
                                <span class="booking-modal-close">×</span>
                                <h2>
                                    ${title}
                                </h2>
                                <div>
                                    ${content}
                                </div>
                                <p>
                                    <strong>Capacity:</strong>
                                    ${capacity}
                                </p>
                                <?php if (!empty(${thumbnail})) { ?>
                                    <img
                                        src="${thumbnail}"
                                        alt="">
                                <?php } ?>
                            </div>
                        </div>
                    </td>
                    <td>
                        <button
                            class="update-resource-link"
                            data-id="${id}">
                            update
                        </button>
                    </td>
                    <div id="resource-edit-modal-${id}" class="booking-modal-overlay" style="display: none;">
                        <div class="booking-modal-content">
                            <h2 style="margin-top: 0;">Edit Resource</h2>

                            <form id="resource-edit-form" enctype="multipart/form-data">
                                <input type="hidden"
                                    id="edit-resource-id"
                                    value="${id}">
                                <p>
                                    <label for="edit-resource-title"><strong>Title:</strong></label><br>

                                    <?php if (!empty(${thumbnail})) { ?>
                                        <input
                                            type="text"
                                            id="edit-resource-title"
                                            class="regular-text"
                                            value="${title}"
                                            style="width: 100%; padding: 6px; margin-top: 5px;">
                                    <?php } ?>
                                <p style="color:red" id="titleerr"></p>
                                </p>
                                <p>
                                    <label for="edit-resource-content"><strong>Content:</strong></label><br>
                                    <textarea
                                        id="edit-resource-content"
                                        rows="4"
                                        style="width: 100%; padding: 6px; margin-top: 5px;"><?php echo esc_textarea(${content}); ?></textarea>
                                <p style="color:red" id="contenterr"></p>
                                </p>

                                <p>
                                    <label><strong>Featured Image:</strong></label><br>
                                <div id="edit-image-preview" style="margin: 8px 0;"></div>
                                <input
                                    type="hidden"
                                    id="old-image-${id}"
                                    value="${thumbnail}">
                                <img
                                    id="profileDisplay-${id}"
                                    src="${thumbnail}"
                                    width="200">
                                <input type="file" id="edit-resource-file" accept="image/*" style="margin-top: 5px;">
                                </p>
                                <p>
                                    <label for="edit-resource-capacity"><strong>Capacity:</strong></label><br>
                                    <input
                                        type="number"
                                        id="edit-resource-capacity"
                                        class="regular-text"
                                        style="width: 100%; padding: 6px; margin-top: 5px;"
                                        value="${capacity}">
                                <p style="color:red" id="capacityerr"></p>
                                </p>

                                <p style="margin-top: 20px; text-align: right;">
                                    <button type="submit" class="button button-primary work">Save Changes</button>
                                </p>
                            </form>
                        </div>
                    </div>
                    <td>
                        <button
                            class="delete-resource"
                            data-id="${id}">
                            Delete
                        </button>
                    </td>
                </tr>`;
        i++;
      }
      $("#mycontent").html(rowsHtml);
    });
  });
})(jQuery);
