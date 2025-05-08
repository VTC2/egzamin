function dodaj(){
const guzik = document.getElementById("dodaj")
const dodawane =  document.getElementById("wprowadzanie")
if(dodawane.style.display=="block")
{
dodawane.style.display="none"
}
else{
  dodawane.style.display="block"
  guzik.innerText="dodaj pomiar"

}
}