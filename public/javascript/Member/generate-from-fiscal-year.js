///////////////////////////////////
// -- Document ready listener -- //
///////////////////////////////////
$(document).ready(function() {
    $("#from-fiscal-year-form").submit(function(e) {
        e.preventDefault(); //Prevent Default action.

        // We show the loading message
        $('.loader-animated').removeClass('d-none');
        $('#from-fiscal-year-form-button').addClass('d-none');

        let formURL = $('#from-fiscal-year-form-button').data('submit-path');
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
                $('.loader-animated').addClass('d-none');
                $('#from-fiscal-year-form-button').removeClass('d-none');

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
            error: function(jqXHR, textStatus, errorThrown)
            {
                $('.loader-animated').addClass('d-none');
                $('#from-fiscal-year-form-button').removeClass('d-none');
            }
        });
    });
});
