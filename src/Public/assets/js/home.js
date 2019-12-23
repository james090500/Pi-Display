var timeLeft = 45;
var timerId = setInterval(countdown, 1000);

function countdown() {
  if(timeLeft == 0) {
    location.reload();
  } else {
    let progress = (100 / 45) * (45 - timeLeft);
    document.getElementById("progressBar").style.width = progress + "%";
    timeLeft--;
  }
}
