var counter =1;
$("#image").click(function(e) {
    $("#tt").css('top', e.pageY);
    $("#tt").css('left', e.pageX);
    //$("#tt").text(e.offsetX + ", " + e.offsetY);
    $("#x_"+counter).val(e.offsetX);
    $("#y_"+counter).val(e.offsetY);

    counter ++;
});
function generate() {
    var ps = "[";
    var sumx = 0;
    var sumy = 0;
    var elements = 0;
    for(i=1;i<=7;i++){
        if($("#x_"+i).val() !== ""){
            ps +=  $("#x_"+i).val();
            ps +=  ",";
            ps +=  $("#y_"+i).val();
            ps +=  ",";
            sumx += parseInt($("#x_"+i).val());
            sumy += parseInt($("#y_"+i).val());
            elements += 1;
        }
    }
    ps = ps.slice(0,-1);
    ps +=  "]";

    $("#pointString").val(ps);
    $("#idString").val("[12,"+Math.round(sumx/elements)+","+Math.round(sumy/elements)+"]");
};

$("#reset").click(function(e) {
    counter = 1;
    for(i=1;i<=7;i++){
        $("#x_"+i).val("");
        $("#y_"+i).val("");
    }
        $("#pointString").val("");
    $("#idString").val("")
});

$(".premise").click(function(e) {
    var points = '[';
    var minX = 500;
    var minY = 500;
    var maxX = 0;
    var maxY = 0;
    for(i=1;i<=7;i++){
        if($("#x_"+i).val() !== ""){
            minX = $("#x_"+i).val() < minX ? $("#x_"+i).val() : minX;
            minY = $("#y_"+i).val() < minY ? $("#y_"+i).val() : minY;
            maxX = $("#x_"+i).val() > maxX ? $("#x_"+i).val() : maxX;
            maxY = $("#y_"+i).val() > maxY ? $("#y_"+i).val() : maxY;
            points += $("#x_"+i).val() + "," + $("#y_"+i).val()+",";
        }
    }
    points = points.slice(0,-1);
    points +="]";
    var text ="[12,"+Math.round(+minX -11 + +(maxX-minX)/2)+","+Math.round(+minY +5 + +(maxY-minY)/2)+"]";
    $("#points_"+$(this).attr("data")).val(points);
    $("#text_"+$(this).attr("data")).val(text);

});