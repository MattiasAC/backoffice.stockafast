function myFunction(copyText) {
  //var copyText = document.getElementById("myInput");
  var res = document.getElementById("copy");
  copyText.select();
  copyText.setSelectionRange(0, 99999); // For mobile devices
  navigator.clipboard.writeText(copyText.value);
  copyText.style.backgroundColor = 'red';
}

$("#thisMonth").click(function(){
    $("#invoiceDate").val('<?=date("Y-m-d",mktime(0,0,0,date("m"),1,date("Y")));?>');
    $("#settings").trigger('click');
});
$("#nextMonth").click(function(){
    $("#invoiceDate").val('<?=date("Y-m-d",mktime(0,0,0,date("m")+1,1,date("Y")));?>');
    $("#settings").trigger('click');
});

