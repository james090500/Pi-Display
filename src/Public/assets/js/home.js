var timeLeft = 30;
var timerId = setInterval(countdown, 1000);

function countdown() {
  if(timeLeft == 0) {
    location.reload();
  } else {
    let progress = (100 / 30) * (30 - timeLeft);
    document.getElementById("progressBar").style.width = progress + "%";    
    timeLeft--;
  }
}
