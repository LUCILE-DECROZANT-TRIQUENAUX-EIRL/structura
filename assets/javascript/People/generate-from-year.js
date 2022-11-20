///////////////////////////////////
// -- Document ready listener -- //
///////////////////////////////////
$(document).ready(function() {
    $("#from-year-form").submit(function(e) {
        e.preventDefault(); // prevent form submission

        // show the loading message
        $('#from-year-form-button').attr('disabled', 'disable');
        $('#from-year-form-button .spinner-border').removeClass('d-none');
        $('#from-year-form-button .ion-md-document').addClass('d-none');

        let formURL = $('#from-year-form-button').data('submit-path');
        let formData = new FormData(this);

        $.ajax({
            url: formURL,
            type: 'POST',
            data:  formData,
            mimeType:"multipart/form-data",
            contentType: false,
            cache: false,
            processData:false,
            xhr:function(){// Seems like the only way to get access to the xhr object
                var xhr = new XMLHttpRequest();
                xhr.responseType= 'blob'
                return xhr;
            },
            success: function(data, textStatus, jqXHR)
            {
                if(jqXHR.status === 200)
                {
                    // Try to find out the filename from the content disposition `filename` value
                    let disposition = jqXHR.getResponseHeader('content-disposition');
                    let matches = /"([^"]*)"/.exec(disposition);
                    let filename = (matches != null && matches[1] ? matches[1] : 'file.pdf');

                    // The actual download
                    let blob = new Blob([data], { type: 'application/pdf' });
                    let link = document.createElement('a');
                    link.href = window.URL.createObjectURL(blob);
                    link.download = filename;

                    document.body.appendChild(link);

                    link.click();

                    document.body.removeChild(link);
                }
            },
            complete: function(jqXHR, textStatus, errorThrown)
            {
                $('button .spinner-border').addClass('d-none');
                $('button .ion-md-document').removeClass('d-none');
                $('#from-year-form-button').removeAttr('disabled');
            },
        });
    });
});
