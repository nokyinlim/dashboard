let needsToUpdate = false;


function updateAndFetch(linkType) {
    needsToUpdate = true;
    fetchLinks(linkType);
}

function fetchLinks(linkType) {
    
    fetch(`navbar.php?type=${linkType}`)
        .then(response => response.text())
        .then(data => {
            document.getElementById('nav-left').innerHTML = data;
        })
        .catch(error => console.error('Error fetching links:', error));
}

function slideOut() {
    const elements = document.querySelectorAll('.navbar-link-from-left');
    elements.forEach(element => {
        element.classList.add('slide-out-to-right'); // Add the slide-out class to each element
        console.log("Element Counter")
    });
    setTimeout(fetchLinks, 100, "")
}

let timer;
function startTimer() {
    // Check if the timer is already running
    if (timer) {
        return; // Do not start a new timer if one is running
    }

    // Start a 5-second countdown
    let timeLeft = 5;
    timer = setInterval(() => {
        timeLeft--;

        if (timeLeft < 0) {
            clearInterval(timer); 
            timer = null; 

            timerComplete(); 
        }
    }, 1000);
}

function resetTimer() {
    
    if (timer) {
        clearInterval(timer); 
        timer = null; 
    }
    startTimer(); 
}

function timerComplete() {
    if (needsToUpdate) {
        slideOut();
    }
}