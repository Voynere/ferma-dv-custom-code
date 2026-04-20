function setCookie(cname, cvalue, exdays) {
  var a = document.getElementById("counterball").value;
  if(a == '') {
    alert("Не указано количество");
  } else {
    document.cookie = "ball=5";
    document.cookie="balik=" + a + ";path=/";
    window.location.reload();
  }
}
function handleChange(input) {
  
  var a = document.getElementById("textbonuses").value /2;

if (input.value < 0) input.value = 0;
if (input.value > a) input.value = a;
}
function showCookie(){
document.write(document.cookie);
}