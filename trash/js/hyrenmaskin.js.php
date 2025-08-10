$(document).ready(function(){
    $('#modalContract').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget); // Knappen som utlöste modalen
        //var button = $("#test"); // Knappen som utlöste modalen
        var id = button.attr('contractid'); // Extrahera id-attributet
  
        // Skicka AJAX-begäran för att hämta data baserat på id
        $.ajax({
            url: 'https://admin.altahr.se/html/hyrenmaskin/ajax_contract.php', // Din PHP-fil som hämtar data
            type: 'POST',
            data: { id: id },
            success: function(response) {
                // Uppdatera modalens body med den hämtade HTML-koden
                $('#modalContract .modal-body').html(response);
            }
        });
    });

        $('#modalClient').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget); // Knappen som utlöste modalen
        //var button = $("#test"); // Knappen som utlöste modalen
        var id = button.attr('clientid'); // Extrahera id-attributet

        // Skicka AJAX-begäran för att hämta data baserat på id
        $.ajax({
            url: 'https://admin.altahr.se/html/hyrenmaskin/ajax_client.php', // Din PHP-fil som hämtar data
            type: 'POST',
            data: { id: id },
            success: function(response) {
                // Uppdatera modalens body med den hämtade HTML-koden
                $('#modalClient .modal-body').html(response);
            }
        });
    });
});
