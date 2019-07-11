$(document).ready(function(){

  $('[name="appbundle_user[cellPhoneNumber]"]').keypress(function (e) {
      if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
        return false;
      }
      var curchr = this.value.length;
      var curval = $(this).val();
      var phonenumber = "";
      phonenumber = phonenumber+curval;
      if (curchr == 2 && (curval == 6 || curval == 7) && phonenumber[0] == 0) {
        $(this).val( curval + " ");
      } else if (curchr == 2 && (curval != 6 || curval != 7) && phonenumber[0] != 0) {
        e.preventDefault();
      } else if (curchr == 5) {
        $(this).val(curval + " ");
      } else if (curchr == 8) {
        $(this).val(curval + " ");
      } else if (curchr == 11) {
        $(this).val(curval + " ");
        $(this).attr('maxlength', '14');
    }
  });

  $('[name="appbundle_user[homePhoneNumber]"]').keypress(function (e) {
      if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
        return false;
      }
      var curchr = this.value.length;
      var curval = $(this).val();
      var phonenumber = "";
      phonenumber = phonenumber+curval;
      if (curchr == 2 && phonenumber[0] == 0) {
        $(this).val( curval + " ");
      } else if (curchr == 2 && phonenumber[0] != 0) {
        e.preventDefault();
      } else if (curchr == 5) {
        $(this).val(curval + " ");
      } else if (curchr == 8) {
        $(this).val(curval + " ");
      } else if (curchr == 11) {
        $(this).val(curval + " ");
        $(this).attr('maxlength', '14');
    }
  });

  $('[name="appbundle_user[workPhoneNumber]"]').keypress(function (e) {
      if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
        return false;
      }
      var curchr = this.value.length;
      var curval = $(this).val();
      var phonenumber = "";
      phonenumber = phonenumber+curval;
      if (curchr == 2 && phonenumber[0] == 0) {
        $(this).val( curval + " ");
      } else if (curchr == 2 && phonenumber[0] != 0) {
        e.preventDefault();
      } else if (curchr == 5) {
        $(this).val(curval + " ");
      } else if (curchr == 8) {
        $(this).val(curval + " ");
      } else if (curchr == 11) {
        $(this).val(curval + " ");
        $(this).attr('maxlength', '14');
    }
  });

  $('[name="appbundle_user[workFaxNumber]"]').keypress(function (e) {
      if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
        return false;
      }
      var curchr = this.value.length;
      var curval = $(this).val();
      var phonenumber = "";
      phonenumber = phonenumber+curval;
      if (curchr == 2 && phonenumber[0] == 0) {
        $(this).val( curval + " ");
      } else if (curchr == 2 && phonenumber[0] != 0) {
        e.preventDefault();
      } else if (curchr == 5) {
        $(this).val(curval + " ");
      } else if (curchr == 8) {
        $(this).val(curval + " ");
      } else if (curchr == 11) {
        $(this).val(curval + " ");
        $(this).attr('maxlength', '14');
    }
  });

});

/*else if (curchr == 3 && (curval == 6 || curval == 7) && phonenumber[0] == + && phonenumber[1] == 0) {
$(this).val( curval + " ");*/
