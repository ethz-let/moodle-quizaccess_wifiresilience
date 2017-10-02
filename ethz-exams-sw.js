(global => {
  'use strict';
// Ensure that our service worker takes control of the page as soon as possible.
global.addEventListener('install', event => event.waitUntil(global.skipWaiting()));

const attemptCacheName = 'ETHz-SW-attempt_pages';
  /***** attempt special case.... *****/
  function stripSearchParameters(url) {
    var strippedUrl = new URL(url);
    var x = strippedUrl.search;
    strippedUrl.search = x.replace(/&?page=([^&]$|[^&]*)/i, "");

    var x = strippedUrl.search;
    strippedUrl.search = x.replace(/&?lang=([^&]$|[^&]*)/i, "");

    strippedUrl.search = strippedUrl.search + '&page=0';

    //strippedUrl.search = '';
    return strippedUrl.toString();
  }


  global.attemptFetchHandler = function(request) {
    // Attempt to fetch(request). This will always make a network request, and will include the
    // full request URL, including the search parameters.
    return global.fetch(request).then(function(response) {
      if (response.ok) {
        console.log("[ETHz-SW] ServiceWorker: Attempt page served from Server: " + request.url);
        // If we got back a successful response, great!
        return global.caches.open(attemptCacheName).then(function(cache) {
          // First, store the response in the cache, stripping away the search parameters to
          // normalize the URL key.
          return cache.put(stripSearchParameters(request.url), response.clone()).then(function() {
            // Once that entry is written to the cache, return the response to the controlled page.
            return response;
          });
        });
      }
      // If we got back an error response, raise a new Error, which will trigger the catch().
      throw new Error('Attempt page response with an error status code was returned.',response);
    }).catch(function(error) {
      console.log("[ETHz-SW] ServiceWorker: Attempt page served from Cache: " + attemptCacheName);
      // This code is executed when there's either a network error or a response with an error
      // status code was returned.
      return global.caches.open(attemptCacheName).then(function(cache) {
        // Normalize the request URL by stripping the search parameters, and then return a
        // previously cached response as a fallback.
        return cache.match(stripSearchParameters(request.url));
      });
    });
  }

  global.postFetchHandler = function(request) {
    return global.fetch(request).then(function(response) {
      if (response.ok) {
        console.log("[ETHz-SW] ServiceWorker: Fetching live-only ("+request.method+")... " + request.url);
        return response;
      }
      // If we got back an error response, raise a new Error, which will trigger the catch().
      throw new Error('('+request.method+') response with an error status code was returned.',response);
    }).catch(function(error) {
      console.log("[ETHz-SW] ServiceWorker: ("+request.method+") "+ request.url + " is OFFLINE. Return Nothing. ");
      // Final fallback in case there is no response.
      return new Response('Offline ('+request.method+') ' + request.url + ' and content unavailable.');
    });
  }

  const pluginCacheName = 'ETHz-SW-runtime-routes';
  global.pluginFetchHandler = function(request) {
    // Attempt to fetch(request). This will always make a network request, and will include the
    // full request URL, including the search parameters.
    return global.fetch(request).then(function(response) {
      if (response.ok) {
        console.log("[ETHz-SW] ServiceWorker: PluginFile page served from Server: " + request.url);
        // If we got back a successful response, great!
        return global.caches.open(pluginCacheName).then(function(cache) {
          // First, store the response in the cache, stripping away the search parameters to
          // normalize the URL key.
          return cache.put(request.url, response.clone()).then(function() {
            // Once that entry is written to the cache, return the response to the controlled page.
            return response;
          });
        });
      }
      // If we got back an error response, raise a new Error, which will trigger the catch().
      throw new Error('[ETHz-SW] ServiceWorker: FilePlugin page response with an error status code was returned.',response);
    }).catch(function(error) {
      console.log("[ETHz-SW] ServiceWorker: PluginFile (" + request.url + ") page served from CACHE: " + pluginCacheName);
      // This code is executed when there's either a network error or a response with an error
      // status code was returned.
      return global.caches.open(pluginCacheName).then(function(cache) {
        // Normalize the request URL by stripping the search parameters, and then return a
        // previously cached response as a fallback.
        return cache.match(request.url);
      });
    });
  }

  /**
   * Set up a fetch handler that uses caching strategy corresponding to the value
   * of the `strategy` URL parameter.
   */
  global.addEventListener('fetch', (event) => {
    if(event.request.url.indexOf('attempt.php') !== -1) {

      event.respondWith(attemptFetchHandler(event.request));

    } else if (event.request.method !== 'GET'){

      console.log("[ETHz-SW] ServiceWorker: Ignoring live-routes. ("+event.request.method+"): "+event.request.url);
      event.respondWith(postFetchHandler(event.request));
/*
      event.respondWith(
        caches.match(event.request).then(function(response) {

        return global.fetch(event.request).then(function(response) {
            console.log('[ETHz-SW] ServiceWorker: Response from network OK:  (' + event.request.url + ')', response);
            return response;
          }).catch(function(error) {
            console.log('[ETHz-SW] ServiceWorker: No Connectivity to: (' + event.request.url + ')', error);
            return null;
          });
       })
      );
*/

  }
  });

  // Load the sw-toolbox library.
  /*
  importScripts('https://unpkg.com/workbox-sw@1.3.0/build/importScripts/workbox-sw.dev.v1.1.0.js');
  importScripts('https://unpkg.com/workbox-routing@1.3.0/build/importScripts/workbox-routing.dev.v1.3.0.js');
  importScripts('https://unpkg.com/workbox-runtime-caching@1.3.0/build/importScripts/workbox-runtime-caching.dev.v1.3.0.js');
*/
 importScripts('js/workbox/workbox-sw.prod.v1.3.0.js?'+Math.random());
  importScripts('js/workbox/workbox-routing.prod.v1.3.0.js?'+Math.random());

  importScripts('js/localforage.js?'+Math.random());
  importScripts('js/startswith.js?'+Math.random());

const   responses_store = localforage.createInstance({
      name: 'ETHz-exams-responses'
  });
const  status_store = localforage.createInstance({
      name: 'ETHz-exams-question-status'
  });
  const responses_details_store = localforage.createInstance({
      name: "ETHz-exams-individual-questions"
  });
const   questions_store = localforage.createInstance({
      name: "ETHz-exams-all-questions"
  });


  const workboxSW = new self.WorkboxSW({clientsClaim: true});
  self.workbox.logLevel = self.workbox.LOG_LEVEL.none; //verbose;

  // Precache files & Extra Routes from DB, do NOT remove
  //[[PRECACHE_FILES]]
  //[[EXTRA_ROUTES]]
  // End of Precache files & Extra Routes from DB, do NOT remove

  // The route for any requests from the googleapis origin
  workboxSW.router.registerRoute(new RegExp('http(.*)://(.*).googleapis.com/(.*)'),
    workboxSW.strategies.networkFirst({
      cacheName: 'ETHz-SW-googleapis',
      cacheExpiration: {
        maxEntries: 1000,
        maxAgeSeconds: 4 * 60 * 60,
      },
      cacheableResponse: {
        statuses: [0, 200]
      },
      networkTimeoutSeconds: 4
    })
  );
  /*
  workboxSW.router.registerRoute(new RegExp('http(.*)'),
    workboxSW.strategies.networkFirst({
      cacheName: 'ETHz-SW-cross_origins',
      cacheExpiration: {
        maxEntries: 1000,
        maxAgeSeconds: 4 * 60 * 60,
      },
      cacheableResponse: {
        statuses: [0, 200]
      },
      networkTimeoutSeconds: 4
    })
  );
*/
  // The route for any requests from the cloudflare origin
  workboxSW.router.registerRoute('http(.*)://(.*).cloudflare.com/(.*)',
    workboxSW.strategies.networkFirst({
      cacheName: 'ETHz-SW-cloudflare',
      cacheExpiration: {
        maxEntries: 1000,
        maxAgeSeconds: 4 * 60 * 60,
      },
      cacheableResponse: {
        statuses: [0, 200]
      },
      networkTimeoutSeconds: 4
    })
  );
  // The route for any requests from the mathjax origin
  workboxSW.router.registerRoute('http(.*)://cdn.mathjax.org/(.*)',
    workboxSW.strategies.networkFirst({
      cacheName: 'ETHz-SW-mathjax',
      cacheExpiration: {
        maxEntries: 1000,
        maxAgeSeconds: 4 * 60 * 60,
      },
      cacheableResponse: {
        statuses: [0, 200]
      },
      networkTimeoutSeconds: 4
    })
  );

  // The route for any requests from the ethz.ch origin
  workboxSW.router.registerRoute('http(.*)://(.*).ethz.ch/(.*)',
    workboxSW.strategies.networkFirst({
      cacheName: 'ETHz-SW-corporate_site_1',
      cacheExpiration: {
        maxEntries: 1000,
        maxAgeSeconds: 4 * 60 * 60,
      },
      cacheableResponse: {
        statuses: [0, 200]
      },
      networkTimeoutSeconds: 4
    })
  );

  // The route for any requests from the ethz.ch origin
  workboxSW.router.registerRoute('http(.*)://ethz.ch/(.*)',
    workboxSW.strategies.networkFirst({
      cacheName: 'ETHz-SW-corporate_site_2',
      cacheExpiration: {
        maxEntries: 1000,
        maxAgeSeconds: 4 * 60 * 60,
      },
      cacheableResponse: {
        statuses: [0, 200]
      },
      networkTimeoutSeconds: 4
    })
  );

  // The route for any css requests
  workboxSW.router.registerRoute(/\.(?:css)$/,
    workboxSW.strategies.networkFirst({
      cacheName: 'ETHz-SW-css',
      cacheExpiration: {
        maxEntries: 1000,
        maxAgeSeconds: 4 * 60 * 60,
      },
      cacheableResponse: {
        statuses: [0, 200]
      },
      networkTimeoutSeconds: 4
    })
  );

  // The route for any .js requests
  workboxSW.router.registerRoute(/\.(?:js)$/,
    workboxSW.strategies.networkFirst({
      cacheName: 'ETHz-SW-js',
      cacheExpiration: {
        maxEntries: 1000,
        maxAgeSeconds: 4 * 60 * 60,
      },
      cacheableResponse: {
        statuses: [0, 200]
      },
      networkTimeoutSeconds: 30
    })
  );

  // The route for any videos requests
  workboxSW.router.registerRoute(/\.(?:mp4|webm|ogg|flv|swf|mkv|vob|mng|avi|mov|qt|wmv|rm|rmvb|asf|m4p|m4v|mpg|mpeg|3gp|f4v|4p|f4a|f4b)$/,
    workboxSW.strategies.networkFirst({
      cacheName: 'ETHz-SW-videos',
      cacheExpiration: {
        maxEntries: 1000,
        maxAgeSeconds: 4 * 60 * 60,
      },
      cacheableResponse: {
        statuses: [0, 200, 206]
      },
      networkTimeoutSeconds: 30
    })
  );

  // The route for any audio requests
  workboxSW.router.registerRoute(/\.(?:aiff|wav|mp3|aac|wma|flac|mid|midi)$/,
    workboxSW.strategies.networkFirst({
      cacheName: 'ETHz-SW-audio',
      cacheExpiration: {
        maxEntries: 1000,
        maxAgeSeconds: 4 * 60 * 60,
      },
      cacheableResponse: {
        statuses: [0, 200]
      },
      networkTimeoutSeconds: 4
    })
  );

  // The route for any documents requests
  workboxSW.router.registerRoute(/\.(?:pdf|doc|docx|xls|xlsx|ppt|pptx|ps|htm|html|txt|xml|odt|ods|odp|rtf|sxw|stw|csv|xps)$/,
    workboxSW.strategies.networkFirst({
      cacheName: 'ETHz-SW-documents',
      cacheExpiration: {
        maxEntries: 1000,
        maxAgeSeconds: 4 * 60 * 60,
      },
      cacheableResponse: {
        statuses: [0, 200, 206]
      },
      networkTimeoutSeconds: 30
    })
  );

  // The route for any zipped requests
  workboxSW.router.registerRoute(/\.(?:zip|rar|tar|7z|tgz)$/,
    workboxSW.strategies.networkFirst({
      cacheName: 'ETHz-SW-zipped',
      cacheExpiration: {
        maxEntries: 1000,
        maxAgeSeconds: 4 * 60 * 60,
      },
      cacheableResponse: {
        statuses: [0, 200, 206]
      },
      networkTimeoutSeconds: 30
    })
  );

  // The route for any zipped requests
  workboxSW.router.registerRoute(/\.(?:png|gif|jpg|svg|jpeg|tiff|bmp)$/,
    workboxSW.strategies.networkFirst({
      cacheName: 'ETHz-SW-images',
      cacheExpiration: {
        maxEntries: 1000,
        maxAgeSeconds: 4 * 60 * 60,
      },
      cacheableResponse: {
        statuses: [0, 200]
      },
      networkTimeoutSeconds: 30
    })
  );
  // The route for any youtube requests
  workboxSW.router.registerRoute(/\.(?:youtube|vimeo)\.com$/,
    workboxSW.strategies.networkFirst({
      cacheName: 'ETHz-SW-youtube',
      cacheExpiration: {
        maxEntries: 1000,
        maxAgeSeconds: 4 * 60 * 60,
      },
      cacheableResponse: {
        statuses: [0, 200]
      },
      networkTimeoutSeconds: 4
    })
  );


  // The route for image.php requests
  workboxSW.router.registerRoute(/image\.php$/,
    workboxSW.strategies.networkFirst({
      cacheName: 'ETHz-SW-image_compiled',
      cacheExpiration: {
        maxEntries: 1000,
        maxAgeSeconds: 4 * 60 * 60,
      },
      cacheableResponse: {
        statuses: [0, 200]
      },
      networkTimeoutSeconds: 4
    })
  );
  // The route for yui_combo.php requests
  workboxSW.router.registerRoute(/yui_combo\.php$/,
    workboxSW.strategies.networkFirst({
      cacheName: 'ETHz-SW-yui_combo',
      cacheExpiration: {
        maxEntries: 1000,
        maxAgeSeconds: 4 * 60 * 60,
      },
      cacheableResponse: {
        statuses: [0, 200]
      },
      networkTimeoutSeconds: 4
    })
  );
  // The route for javascript.php requests
  workboxSW.router.registerRoute(/javascript\.php$/,
    workboxSW.strategies.networkFirst({
      cacheName: 'ETHz-SW-javascript_compiled',
      cacheExpiration: {
        maxEntries: 1000,
        maxAgeSeconds: 4 * 60 * 60,
      },
      cacheableResponse: {
        statuses: [0, 200]
      },
      networkTimeoutSeconds: 4
    })
  );
  // The route for styles_debug.php requests
  workboxSW.router.registerRoute(/styles_debug\.php$/,
    workboxSW.strategies.networkFirst({
      cacheName: 'ETHz-SW-styles_debug',
      cacheExpiration: {
        maxEntries: 1000,
        maxAgeSeconds: 4 * 60 * 60,
      },
      cacheableResponse: {
        statuses: [0, 200]
      },
      networkTimeoutSeconds: 4
    })
  );
  // The route for styles.php requests
  workboxSW.router.registerRoute(/styles\.php$/,
    workboxSW.strategies.networkFirst({
      cacheName: 'ETHz-SW-styles_compiled',
      cacheableResponse: {
        statuses: [0, 200]
      },
      networkTimeoutSeconds: 4
    })
  );

  // The route for any css requests
  workboxSW.router.registerRoute(/\.(?:php)$/,
    workboxSW.strategies.networkFirst({
      cacheName: 'ETHz-SW-php',
      cacheExpiration: {
        maxEntries: 1000,
        maxAgeSeconds: 4 * 60 * 60,
      },
      cacheableResponse: {
        statuses: [0, 200]
      },
      networkTimeoutSeconds: 4
    })
  );


  // Catch all others!
  workboxSW.router.setDefaultHandler({
    handler: workboxSW.strategies.networkFirst({
      cacheName: 'ETHz-SW-default-route',
      cacheExpiration: {
        maxEntries: 1500,
        maxAgeSeconds: 4 * 60 * 60,
      },
      cacheableResponse: {
        statuses: [0, 200]
      },
      networkTimeoutSeconds: 4
    }),
    method: 'GET'
  });

  addEventListener('activate', function(event) {
    // Delete all old cache objects, but only for the blog.  Don't delete
    // cache objects for other github repo's hosted on same domain.
    event.waitUntil(caches.keys().then(function(cacheList) {
      return Promise.all(cacheList.filter(function(cacheName) {
        if (cacheName != attemptCacheName) {
          return;
        }
        console.warn('[ETHz-SW] ServiceWorker: Deleted all previous attempt pages in cache..');
        return global.caches.delete(cacheName);
      })).then(function() {
        global.clients.claim();
        console.log('[ETHz-SW] ServiceWorker: Claimed Clients upon activtation of Service Worker..');
      });
    }));
  });

addEventListener('sync', function(event) {
  console.log('[ETHz-SW] ServiceWorker: Firing Background Sync');
  if (event.tag === 'upload-responses') {
      event.waitUntil(wifi_uploadResponses([[CMID]],'[[TOKEN]]'));
      console.log('[ETHz-SW] ServiceWorker: wifi_uploadResponses function in Background Sync has been called. It will fail if Background Sync TOKEN is not avialable.');
  }
});

function wifi_uploadResponses(cmid, token){

  if(!token || token ==''){
    console.log('[ETHz-SW] ServiceWorker: Background Sync TOKEN is not avialable, background sync is terminated.');
    return;
  }
  //////////////// delete generic information about questions //////////

    responses_details_store.startsWith('ETHz-crs').then(function(results) {

       for (var ldbindex in results) {
           responses_details_store.removeItem(ldbindex);
           console.log('[ETHz-SW] ServiceWorker: Background Sync (ETHz-exams-individual-questions) - locally stored files. Deletion OK, Index: '+ldbindex);
      }
    });

      questions_store.startsWith('ETHz-crs').then(function(results) {

         for (var ldbindex in results) {
             questions_store.removeItem(ldbindex);
             console.log('[ETHz-SW] ServiceWorker: Background Sync (ETHz-exams-all-questions) - locally stored files. Deletion OK, Index: '+ldbindex);
          }


    });
  //////// end delete generic information about questions //////////

  responses_store.startsWith('ETHz-crs').then(function(results) {

    var localdatafiles = new FormData();

     var found = 0;
     for (var ldbindex in results) {
      found = 1;
    //  localforagedata = {key: ldbindex, responses: results[ldbindex]};
      var blob = new Blob([encodeURIComponent(results[ldbindex])], {type: "octet/stream"});

      localdatafiles.append('file[]', blob, ldbindex + '_encrypted');
    }

    if(found == 1){
        console.log('[ETHz-SW] ServiceWorker: Background Sync found data with ETHz-crs prefix in ETHz-exams-responses');
        fetchfilestoserver(token, localdatafiles, cmid, responses_store);
    }


  });

  status_store.startsWith('ETHz-crs').then(function(results) {

    var localdatafiles = new FormData();

     var found = 0;
     for (var ldbindex in results) {
      found = 1;
    //  localforagedata = {key: ldbindex, responses: results[ldbindex]};
      var blob = new Blob([encodeURIComponent(results[ldbindex])], {type: "octet/stream"});

      localdatafiles.append('file[]', blob, ldbindex + '_sequence');
    }

    if(found == 1){
        console.log('[ETHz-SW] ServiceWorker: Background Sync found data with ETHz-crs prefix in ETHz-exams-question-status');
        fetchfilestoserver(token, localdatafiles, cmid, status_store);
    }


  });


}

function fetchfilestoserver(token, data, cmid, whichstore){

  fetch('./sync_localresponses.php?cmid='+cmid+'&token='+token, {
        method: 'POST',
        body: data,

  }).then(function (response) {
    if(response.ok){

      console.log('[ETHz-SW] ServiceWorker: Background Sync Went OK.. Now delete locally stored files, EXCLUDING CMID: '+cmid);

      // After Successful uploading, Delete all responses that get uploaded, EXCLUDING current CMID!
      whichstore.startsWith('ETHz-crs').then(function(results) {

         for (var ldbindex in results) {
           var searchfor = '-cm' + cmid;
           if(ldbindex.indexOf(searchfor) !== -1){ // Found current CMID - ignore
             console.log('[ETHz-SW] ServiceWorker: Background Sync - IGNORE locally stored files in  with index: '+ldbindex);
           } else {
             whichstore.removeItem(ldbindex);
             console.log('[ETHz-SW] ServiceWorker: Background Sync - locally stored files in  Deletion OK, Index: '+ldbindex);
          }
        }

      });

    }
    return response;
  })
  .then(function (text) {
    console.log('[ETHz-SW] ServiceWorker: Background Sync Request successful', text);
  })
  .catch(function (error) {
    console.log('[ETHz-SW] ServiceWorker: Background Sync Request failed', error);
  });

}

//  global.addEventListener('activate', event => event.waitUntil(global.clients.claim()));

})(self);
