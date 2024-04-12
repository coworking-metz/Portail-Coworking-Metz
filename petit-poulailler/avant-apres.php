<?php


$w = $_GET['w']??1;

if($w == 1) {
  $avant = 'https://www.coworking-metz.fr/wp-content/uploads/2024/03/1-avant.jpg';
  $apres = 'https://www.coworking-metz.fr/wp-content/uploads/2024/03/1-apres.jpg';
} else {
  $avant = 'https://www.coworking-metz.fr/wp-content/uploads/2024/03/2-avant.jpg';
  $apres = 'https://www.coworking-metz.fr/wp-content/uploads/2024/03/2-apres.jpg';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Avant/Après 1</title>
    <link rel="stylesheet" href="style.css" type="text/css" media="screen" />
    <script type="text/javascript" defer async src="https://cloudflare.coworking-metz.fr/cf.js"></script>
        <style>


    </style>
</head>
<body>
  <img class="logo" src="/favicon/apple-touch-icon.png">
    <div id="comparison">
        <figure style="background-image:url(<?=$avant;?>)">
          <div id="divisor" style="background-image:url(<?=$apres;?>)"></div>
        </figure>
        <input type="range" min="0" max="100" value="0" id="slider" oninput="moveDivisor()">
      </div>
      
</body>
</html>
<script>

var divisor = document.getElementById("divisor"),
slider = document.getElementById("slider");
function moveDivisor() { 
	divisor.style.width = slider.value+"%";
}

document.querySelector('#comparison').addEventListener('click', e => {
  
  if(e.target.closest('#slider')) return;
  console.log(e.target)
  switchView()
})
document.addEventListener('keyup', e => {
  switchView()
})

function switchView() {
  slider.value = slider.value == 100 ? 0 : 100;
    divisor.classList.add('transition')
  setTimeout(() => {
    divisor.classList.remove('transition')
  }, 1000)
  moveDivisor()

}
moveDivisor()
window.addEventListener('load', e =>{
  setTimeout(() => {
    document.querySelector('#comparison').click();
  }, 2000)
})
</script>