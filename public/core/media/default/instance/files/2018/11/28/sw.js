// This is the "Offline copy of pages" service worker

// Install stage sets up the index page (home page) in the cache and opens a new cache
self.addEventListener('install', function(event) {
  self.skipWaiting();

  var indexPage = new Request('/');

  event.waitUntil(
    fetch(indexPage).then(function(response) {
      return caches.open('opennemas-offline').then(function(cache) {
        console.log('[Opennemas] Cached index page during install ' + response.url);
        return cache.put(indexPage, response);
      });
    })
  );
});

// If any fetch fails, it will look for the request in the cache and serve it from there first
self.addEventListener('fetch', function(event) {
  var updateCache = function(request) {
    var toIgnore = [
      /^chrome-extension:.*/,
      /.*.(png|jpg)/,
      /.*\/(admin|api|auth|entityws|login|manager|managerws)\/.*/
    ];

    for (var i = 0; i < toIgnore.length; i++) {
      if (toIgnore[i].test(request.url)) {
        return;
      }
    }

    return caches.open('opennemas-offline').then(function(cache) {
      return fetch(request).then(function(response) {
        console.log('[Opennemas] Adding page to offline cache ', request.url);

        return cache.put(request, response);
      });
    });
  };

  event.waitUntil(updateCache(event.request));

  event.respondWith(
    fetch(event.request).catch(function(error) {
      console.log('[Opennemas] Network request Failed. Serving content from cache: ' + error);

      return caches.open('opennemas-offline').then(function(cache) {
        return cache.match(event.request).then(function(matching) {
          return !matching || matching.status == 404 ?
            Promise.reject('no-match') : matching;
        });
      });
    })
  );
});
