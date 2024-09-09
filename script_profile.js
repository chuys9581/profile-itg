jQuery(document).ready(function($) {
    $('#profile-form').on('submit', function(e) {
        e.preventDefault();

        var formData = new FormData(this);

        // Para depurar
        formData.forEach(function(value, key){
            console.log(key + ': ' + value);
        });

        $.ajax({
            url: feedInstagramProfileAjax.ajaxurl,
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                if (response.success) {
                    $('#profile-message').text(response.data.message);
                    $('#profile-name').text('Nombre: ' + response.data.name);
                    $('#profile-picture').html('<img src="' + response.data.avatar_url + '" alt="Avatar" />');
                } else {
                    $('#profile-message').text(response.data.message);
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.log(textStatus, errorThrown);
                $('#profile-message').text('Error: ' + textStatus);
            }
        });
    });
});

