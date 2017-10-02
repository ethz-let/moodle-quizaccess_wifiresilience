self.addEventListener('install', function(event) {
    self.skipWaiting();
});

self.addEventListener('activate', function(event) {
    if (self.clients && clients.claim) {
        clients.claim();
    }
});

self.addEventListener('sync', function(event) {
  console.log('Firing: sync');
  if (event.tag === 'image-fetch') {
    console.log('Sync event fired');
    return event.waitUntil(fetchDogImage());
  } else {
    console.log('Sync been fired with event:' +event.tag);
  }
});

function fetchDogImage()
{
  console.log('Firing: doSomeStuff()');
  fetch('./image.png')
    .then(function(response) {
      return response;
    })
    .then(function(text) {
      console.log('Sync Request successful', text);
    })
    .catch(function(error) {
      console.log('Sync Request failed', error);
    });
}
