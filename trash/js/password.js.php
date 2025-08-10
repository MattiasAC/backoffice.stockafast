$("#searchList").on("keyup", function() {
    var value = $(this).val().toLowerCase();
    $("#hyresLista tr").filter(function() {
        $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
    });
});