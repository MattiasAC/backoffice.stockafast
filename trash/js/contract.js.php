$( document ).ready(function() {
    console.log("aaa");
    $(".default").dblclick(function(){
        console.log($(this).attr("destination"));
        var obj = $("#" + $(this).attr("destination"));
        if(obj.val() == ""){
            obj.val($(this).html());
        }
    });
});