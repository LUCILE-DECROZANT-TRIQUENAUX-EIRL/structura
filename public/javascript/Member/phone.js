$(document).ready(function(){

  $('[name="app_user[cellPhoneNumber]"]').keypress(function (e) {
      if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
        return false;
      }
      var curchr = this.value.length;
      var curval = $(this).val();
      var phonenumber = "";
      phonenumber = phonenumber+curval;
      if (curchr == 2 && (phonenumber[1] == 6 || phonenumber[1] == 7) && phonenumber[0] == 0) {
        $(this).attr('maxlength', '10');
      } else if (curchr == 2 && (phonenumber[1] != 6 || phonenumber[1] != 7 || phonenumber[0] != 0)) {
        e.preventDefault();
      }
  });

  $('[name="app_user[homePhoneNumber]"]').keypress(function (e) {
      if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
        return false;
      }
      var curchr = this.value.length;
      var curval = $(this).val();
      var phonenumber = "";
      phonenumber = phonenumber+curval;
      if (curchr == 2
        && (phonenumber[1] == 1 || phonenumber[1] == 2 || phonenumber[1] == 3 || phonenumber[1] == 4 || phonenumber[1] == 5)
        && phonenumber[0] == 0) {
        $(this).attr('maxlength', '10');
      } else if (curchr == 2 &&
        ( phonenumber[1] != 1 || phonenumber[1] != 2 || phonenumber[1] != 3
        || phonenumber[1] != 4 || phonenumber[1] != 5 || phonenumber[0] != 0 )) {
        e.preventDefault();
      }
  });

  $('[name="app_user[workPhoneNumber]"]').keypress(function (e) {
      if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
        return false;
      }
      var curchr = this.value.length;
      var curval = $(this).val();
      var phonenumber = "";
      phonenumber = phonenumber+curval;
      if (curchr == 2 && phonenumber[0] == 0) {
        $(this).attr('maxlength', '10');
      } else if (curchr == 2 && phonenumber[0] != 0 ) {
        e.preventDefault();
      }
  });

  $('[name="app_user[workFaxNumber]"]').keypress(function (e) {
      if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
        return false;
      }
      var curchr = this.value.length;
      var curval = $(this).val();
      var phonenumber = "";
      phonenumber = phonenumber+curval;
      if (curchr == 2 && phonenumber[0] == 0) {
        $(this).attr('maxlength', '10');
      } else if (curchr == 2 && phonenumber[0] != 0 ) {
        e.preventDefault();
      }
  });

});

/*else if (curchr == 3 && (curval == 6 || curval == 7) && phonenumber[0] == + && phonenumber[1] == 0) {
$(this).val( curval + " ");*/
