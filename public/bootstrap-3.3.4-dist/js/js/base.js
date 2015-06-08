$(document).ready(function() {
   /*
    $().ajaxStart(function() {
        $('#loading').show();
        $('#result').hide();
    }).ajaxStop(function() {
        $('#loading').hide();
        $('#result').fadeIn('slow');
    });
    */
    $('#login-form').submit(function() {
        $.ajax({
            type: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'text/plain'
            },
            dataType: 'json',
            url: $(this).attr('action'),
            data: $(this).serialize(),
            success: function(data) {
                //$('#result').html(data);
                console.log(data);
            }
        });
        return false;
    }); 
})