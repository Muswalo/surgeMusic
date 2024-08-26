var currentAudio = null;  // Reference to the currently playing audio element

async function togglePlay(button, recordId, viewElementId, metaData) {
  const audio = button.closest('.buttons').querySelector('.song');
  button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

  await new Promise(res=>{
    if (audio.readyState >= 4) {
      res()
    } else {
      audio.addEventListener('canplay', res);
    }
  });

  if (audio.paused) {
    stopCurrentAudio(button, recordId); // Stop the currently playing audio, if any
    await startAudio(audio, recordId, button, viewElementId, metaData);
    button.innerHTML = '<i class="fas fa-pause"></i>';
  } else {
    stopAudio(audio, button, recordId);
  }
}

function stopCurrentAudio(button, recordId) {
  if (currentAudio) {
    stopAudio(currentAudio, button, recordId);
    currentAudio = null;  // Reset the currentAudio reference
  }
}

async function startAudio(audio, recordId, button, viewElementId, metaData) {
  // console.log('startAudio()'+recordId);
  let audioLength = audio.duration;

  audio.addEventListener('play', async ()=>{
    let remainingDuration = getCookie(`audioDuration_${recordId}`);

    if (!remainingDuration) {
      // Create a new cookie with the length of the audio
      setCookie(`audioDuration_${recordId}`, audioLength, 1);

      try {
        const updateValue = await incrementValue(recordId, 'plays');
        // console.log('updateValue()'+updateValue)
       document.getElementById(viewElementId).innerHTML = updateValue
      } catch (error) {
        console.error(error);
      }
    }

    // Start the timer to track the elapsed time
    var startTime = Date.now();

    // Update the remaining duration every second
    var interval = setInterval(function() {
      if (audio.paused) {
        // Audio paused internally, update the play button icon
        clearInterval(interval);
        button.innerHTML = '<i class="fas fa-play"></i>';
      } else {
        var elapsedTime = Math.floor((Date.now() - startTime) / 1000);
        remainingDuration = audioLength - elapsedTime;

        if (remainingDuration <= 0) {
          // Audio playback completed, destroy the cookie
          destroyCookie(`audioDuration_${recordId}`);
          clearInterval(interval);
        } else {
          // Update the cookie with the remaining duration
          setCookie(`audioDuration_${recordId}`, remainingDuration, 1);
        }
      }
    }, 1000);

    // Update Media Session metadata
    if ('mediaSession' in navigator) {
      navigator.mediaSession.metadata = new MediaMetadata({
        title: metaData.songTitle, 
        artist: metaData.artistName, 
        album: metaData.album, 
        artwork: [
          { src: `img/${metaData.artwork}` },
        ]
      });

      navigator.mediaSession.setActionHandler('play', function() {
        if (currentAudio) {
          currentAudio.play();
        }
      });

      navigator.mediaSession.setActionHandler('pause', function() {
        if (currentAudio) {
          currentAudio.pause();
        }
      });
      
    // TO DO
    //   navigator.mediaSession.setActionHandler('seekbackward', function(details) {
    //     if (currentAudio) {
    //       currentAudio.currentTime = Math.max(currentAudio.currentTime - (details.seekOffset || 10), 0);
    //     }
    //   });

    //   navigator.mediaSession.setActionHandler('seekforward', function(details) {
    //     if (currentAudio) {
    //       currentAudio.currentTime = Math.min(currentAudio.currentTime + (details.seekOffset || 10), currentAudio.duration);
    //     }
    //   });
    //   navigator.mediaSession.setActionHandler('previoustrack', function() {});
    //   navigator.mediaSession.setActionHandler('nexttrack', function() {});
    }
  });

  // Update the currentAudio reference
  currentAudio = audio;
  audio.play();
}

function stopAudio(audio, button, recordId) {
  // Get the remaining duration from the cookie
  var remainingDuration = getCookie(`audioDuration_${recordId}`);

  if (remainingDuration) {
    // Update the cookie with the remaining duration
    setCookie(`audioDuration_${recordId}`, remainingDuration, 1);
  }

  audio.pause();
  button.innerHTML = '<i class="fas fa-play"></i>'; // Update the play button icon
}

async function incrementValue(id, type) {
    // console.log('incrementValue()'+id)
    const url = "add.php";
    const params = "type=" + encodeURIComponent(type) + "&id=" + encodeURIComponent(id);
    const XHR = new XMLHttpRequest();
    XHR.open("POST", url, true);
    XHR.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    XHR.send(params);
  
    return new Promise((resolve, reject) => {
      XHR.onreadystatechange = function () {
        if (this.readyState === 4 && this.status === 200) {
          const responseData = JSON.parse(this.responseText);
          if (responseData.status === "success") {
            // console.table(responseData)
            resolve(responseData.newNumber);
          } else {
            reject(new Error("Error: " + responseData.message));
          }
        }
      };
    });
}
// Function to create a cookie
function setCookie(name, value, days) {
    var expires = '';

    if (days) {
      var date = new Date();
      date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
      expires = '; expires=' + date.toUTCString();
    }
  
    document.cookie = name + '=' + value + expires + '; path=/';
  }

// Function to retrieve a cookie value
function getCookie(name) {
    var nameEQ = name + '=';
    var cookies = document.cookie.split(';');
  
    for (var i = 0; i < cookies.length; i++) {
      var cookie = cookies[i];
      while (cookie.charAt(0) === ' ') {
        cookie = cookie.substring(1, cookie.length);
      }
      if (cookie.indexOf(nameEQ) === 0) {
        return cookie.substring(nameEQ.length, cookie.length);
      }
    }
  
    return null;
  }

  function destroyCookie(recordId) {
    document.cookie = `audioDuration_${recordId}=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;`;
  }
  
  async function download(elem, conuter, recordId) {

    const downloadFile = elem.closest('.buttons').querySelector('.songDownload');
    const downloadCount = document.getElementById(conuter)

    try {
      const updatedCount = await incrementValue(recordId, 'downloads');
      downloadCount.innerHTML = updatedCount;
    } catch (error) {
      console.error(error);
    }

    downloadFile.click();

  }

window.addEventListener('beforeunload', ()=>{
  const cookies = document.cookie.split(';');
  const prefix = 'audioDuration_';

  for (let i=0; i < cookies.length; i++) {
    const cookie = cookies[i].trim();

    if (cookie.startsWith(prefix)) {
      const cookieName = cookie.split('=')[0];
      document.cookie = `${cookieName}=; expires=Thu, 01 jan 1970 00:00:00 UTC; path=/;`
    }
  }
});
