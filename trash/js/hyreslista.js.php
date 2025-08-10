$( document ).ready(function() {
    $(".edit").click(function(){
        console.log($(this).attr("clientid"));
        $("#edit").val($(this).attr("clientid"));
        $("#form").submit();
    });
});
function toggleVisibility() {
    var outerDiv = document.getElementById("hyresMenu");
    if (outerDiv.style.display === "none") {
        outerDiv.style.display = "block";
    } else {
        outerDiv.style.display = "none";
    }
}
function sortColumns(column){
    document.getElementById("orderby").value=column;
    document.getElementById("order").value=document.getElementById("order").value == "ASC" ? "DESC":"ASC";
    document.getElementById("form").submit();
}
