(function ($) {
  "use strict";

  window.validateUpdateBooking = function (name, email, date, from, to) {

    if (name.trim() === "") {
      $("#RequesternameErr").text("* Name is required");
      return false;
    } else {
      $("#RequesternameErr").text("");
    }

    if (email.trim() === "") {
      $("#RemailErr").text("* Email is required");
      return false;
    } else {
      $("#RemailErr").text("");
    }

    if (date === "") {
      $("#RdateErr").text("* Date is required");
      return false;
    }

    if (from === "") {
      $("#RfromErr").text("* From is required");
      return false;
    } else {
      $("#RfromErr").text("");
    }

    if (to === "") {
      $("#RtoErr").text("* To is required");
      return false;
    } else {
      $("#RtoErr").text("");
    }

    return true; 
  };

})(jQuery);