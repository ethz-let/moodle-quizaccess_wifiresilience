/**
* Copyright 2016 Google Inc. All rights reserved.
*
* Licensed under the Apache License, Version 2.0 (the "License");
* you may not use this file except in compliance with the License.
* You may obtain a copy of the License at
*
*     http://www.apache.org/licenses/LICENSE-2.0
*
* Unless required by applicable law or agreed to in writing, software
* distributed under the License is distributed on an "AS IS" BASIS,
* WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
* See the License for the specific language governing permissions and
* limitations under the License.
*/
(function(global) {
  'use strict';
global.toolbox.options.debug = true;
global.toolbox.options.cacheName = 'ETHz-exams-SW-attempts';
global.toolbox.options.CompiledcacheName = 'ETHz-exams-SW-compiled-statics';
global.toolbox.options.DefaultcacheName = 'ETHz-exams-SW-default-route';


/***** attempt special case.... *****/
// Removes the search/query portion from a URL.
// E.g. stripSearchParameters("http://example.com/index.html?a=b&c=d")
//      ➔ "http://example.com/index.html"
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
  console.error('found ya man..');
  // Attempt to fetch(request). This will always make a network request, and will include the
  // full request URL, including the search parameters.
  return global.fetch(request).then(function(response) {
    if (response.ok) {
      // If we got back a successful response, great!
      return global.caches.open(global.toolbox.options.cacheName).then(function(cache) {
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
    // This code is executed when there's either a network error or a response with an error
    // status code was returned.
    return global.caches.open(global.toolbox.options.cacheName).then(function(cache) {
      // Normalize the request URL by stripping the search parameters, and then return a
      // previously cached response as a fallback.
      return cache.match(stripSearchParameters(request.url));
    });
  });
}

global.compiledFilesFetchHandler = function(request) {
  // Attempt to fetch(request). This will always make a network request, and will include the
  // full request URL, including the search parameters.
  return global.fetch(request).then(function(response) {
    if (response.ok) {
      // If we got back a successful response, great!
      return global.caches.open(global.toolbox.options.CompiledcacheName).then(function(cache) {
        // First, store the response in the cache, stripping away the search parameters to
        // normalize the URL key.
        return cache.put(request.url, response.clone()).then(function() {
          // Once that entry is written to the cache, return the response to the controlled page.
          return response;
        });
      });
    }
    // If we got back an error response, raise a new Error, which will trigger the catch().
    throw new Error('A response with an error status code was returned.',response);
  }).catch(function(error) {
    // This code is executed when there's either a network error or a response with an error
    // status code was returned.
    return global.caches.open(global.toolbox.options.CompiledcacheName).then(function(cache) {
      // Normalize the request URL by stripping the search parameters, and then return a
      // previously cached response as a fallback.
      return cache.match(request.url);
    });
  });
}


/***** end attempt special case.... *****/

// now catch attempt. WORKING ONE!!
global.toolbox.router.any('attempt(.*)', attemptFetchHandler, {
  cache: {
    name: 'ETHz-exams-SW-attempts',
    maxEntries: 500,
    maxAgeSeconds: 86400,
    queryOptions: {
      ignoreSearch: true
    }
  }
    });

  // The route for any requests from the googleapis origin
  global.toolbox.router.get('/(.*)', global.toolbox.networkFirst, {
    cache: {
      name: 'ETHz-exams-SW-googleapis',
      maxEntries: 500,
      maxAgeSeconds: 86400
    },
    origin: /\.googleapis\.com$/
  });
  // The route for any requests from the cloudflare origin
  global.toolbox.router.get('/(.*)', global.toolbox.networkFirst, {
    cache: {
      name: 'ETHz-exams-SW-cloudflare',
      maxEntries: 500,
      maxAgeSeconds: 86400
    },
    origin: /\.cloudflare\.com$/
  });
  // The route for any requests from the mathjax origin
  global.toolbox.router.get('/(.*)', global.toolbox.networkFirst, {
    cache: {
      name: 'ETHz-exams-SW-mathjax',
      maxEntries: 500,
      maxAgeSeconds: 86400
    },
    origin: /\.mathjax\.org$/
  });
  // The route for any requests from the ethz.ch origin
  global.toolbox.router.get('/(.*)', global.toolbox.networkFirst, {
    cache: {
      name: 'ETHz-exams-SW-ETHz_Website',
      maxEntries: 500,
      maxAgeSeconds: 86400
    },
    origin: /\.ethz\.ch$/
  });

/*
  global.toolbox.router.get('attempt.php', function(request) {
    console.log('Handled a request for ' + request.url);
  return new Response('Handled a request for ' + request.url);
});
*/


/*
  // The route for any requests from the pluginfile.php origin
  global.toolbox.router.get('/theme/', global.toolbox.networkFirst, {
    cache: {
      name: 'ETHz-exams-SW-compiled-pluginfile',
      maxEntries: 500,
      maxAgeSeconds: 86400
    }
  });

  // The route for any requests from the styles.php origin
  global.toolbox.router.get('/(.*)', global.toolbox.networkFirst, {
    cache: {
      name: 'ETHz-exams-SW-compiled-styles',
      maxEntries: 500,
      maxAgeSeconds: 86400
    },
    origin: /styles\.php$/
  });

  // The route for any requests from the javascript.php origin
  global.toolbox.router.get('/(.*)', global.toolbox.networkFirst, {
    cache: {
      name: 'ETHz-exams-SW-compiled-javascript',
      maxEntries: 500,
      maxAgeSeconds: 86400
    },
    origin: /javascript\.php$/
  });

  // The route for any requests from the yui_combo.php origin
  global.toolbox.router.get('/(.*)', global.toolbox.networkFirst, {
    cache: {
      name: 'ETHz-exams-SW-compiled-yui_combo',
      maxEntries: 500,
      maxAgeSeconds: 86400
    },
    origin: /yui_combo\.php$/
  });

  // The route for any requests from the styles_debug.php origin
  global.toolbox.router.get('/(.*)', global.toolbox.networkFirst, {
    cache: {
      name: 'ETHz-exams-SW-styles_debug',
      maxEntries: 500,
      maxAgeSeconds: 86400
    },
    origin: /styles_debug\.php$/
  });

  // The route for any requests from the draftfiles_ajax.php origin
  global.toolbox.router.get('/(.*)', global.toolbox.networkFirst, {
    cache: {
      name: 'ETHz-exams-SW-draftfiles_ajax',
      maxEntries: 500,
      maxAgeSeconds: 86400
    },
    origin: /draftfiles_ajax\.php$/
  });
  // The route for any requests from the repository_ajax.php origin
  global.toolbox.router.get('/(.*)', global.toolbox.networkFirst, {
    cache: {
      name: 'ETHz-exams-SW-repository_ajax',
      maxEntries: 500,
      maxAgeSeconds: 86400
    },
    origin: /repository_ajax\.php$/
  });
  // The route for any requests from the image.php origin
  global.toolbox.router.get('/(.*)', global.toolbox.networkFirst, {
    cache: {
      name: 'ETHz-exams-SW-compiled-images',
      maxEntries: 500,
      maxAgeSeconds: 86400
    },
    origin: /image\.php$/
  });
*/



// SPECIAL CASES:
/*
// works with local videos in MP4 and a network only strategy. We don't want large files to bloat the cache so we will only play the videos when we are online.
global.addEventListener('fetch', function(event) {
  if (event.request.headers.get('accept').includes('video/mp4')) {
    // respond with a network only request for the requested object
    event.respondWith(global.toolbox.networkFirst(event.request));
  }
  // you can add additional synchronous checks based on event.request.
});
// works with local videos in webm and a network only strategy. We don't want large files to bloat the cache so we will only play the videos when we are online.
self.addEventListener('fetch', function(event) {
  if (event.request.headers.get('accept').includes('video/webm')) {
    // respond with a network only request for the requested object
    event.respondWith(global.toolbox.networkFirst(event.request));
  }
  // you can add additional synchronous checks based on event.request.
});

*/
// matches all files from Youtube and Vimeo and uses the network only strategy. When working with external video sources we not only have to worry about cache size but also about potential copyright issues.
global.toolbox.router.get('(.+)', global.toolbox.cacheFirst, {
  cache: {
    name: 'ETHz-exams-SW-remote-videos',
    maxEntries: 100,
    maxAgeSeconds: 86400
  },
  origin: /\.(?:youtube|vimeo)\.com$/
});
/*
global.toolbox.router.get('/(.*)', global.toolbox.networkFirst, {
  cache: {
    name: 'ETHz-exams-SW-youtube-videos',
    maxEntries: 500,
    maxAgeSeconds: 86400
  },
  origin: /\.googlevideo\.com$/
});


global.toolbox.router.get('/(.*)', global.toolbox.networkFirst, {
  cache: {
    name: 'ETHz-exams-SW-google-others',
    maxEntries: 500,
    maxAgeSeconds: 86400
  },
  origin: /\.google\.com$/
});
*/



// Now catch image.php.
global.toolbox.router.any('image.php', compiledFilesFetchHandler, {
  cache: {
    name: 'ETHz-exams-SW-compiled_images',
    maxEntries: 1000,
    maxAgeSeconds: 86400
  }
});

// Now catch yui_combo.php.
global.toolbox.router.any('yui_combo.php', compiledFilesFetchHandler, {
  cache: {
    name: 'ETHz-exams-SW-yui_combo',
    maxEntries: 1000,
    maxAgeSeconds: 86400
  }
});
// Now catch javascript.php.
global.toolbox.router.any('javascript.php', compiledFilesFetchHandler, {
  cache: {
    name: 'ETHz-exams-SW-compiled_javascript',
    maxEntries: 1000,
    maxAgeSeconds: 86400
  }
});

// Now catch styles_debug.php.
global.toolbox.router.any('styles_debug.php', compiledFilesFetchHandler, {
  cache: {
    name: 'ETHz-exams-SW-compiled_styles',
    maxEntries: 1000,
    maxAgeSeconds: 86400
  }
});
// Now catch styles.php.
global.toolbox.router.any('styles.php', compiledFilesFetchHandler, {
  cache: {
    name: 'ETHz-exams-SW-styles',
    maxEntries: 1000,
    maxAgeSeconds: 86400
  }
});
// Now catch style.php.
global.toolbox.router.any('style.php', compiledFilesFetchHandler, {
  cache: {
    name: 'ETHz-exams-SW-style',
    maxEntries: 1000,
    maxAgeSeconds: 86400
  }
});
// Now catch draftfiles_ajax.php.
global.toolbox.router.any('draftfiles_ajax.php', compiledFilesFetchHandler, {
  cache: {
    name: 'ETHz-exams-SW-draftfiles_ajax',
    maxEntries: 1000,
    maxAgeSeconds: 86400
  }
});
// Now catch draftfiles_ajax.php.
global.toolbox.router.any('toggleflag.php', compiledFilesFetchHandler, {
  cache: {
    name: 'ETHz-exams-SW-toggleflag',
    maxEntries: 1000,
    maxAgeSeconds: 86400
  }
});
// Now catch service-nologin.php.
global.toolbox.router.any('service-nologin.php', compiledFilesFetchHandler, {
  cache: {
    name: 'ETHz-exams-SW-service-nologin',
    maxEntries: 1000,
    maxAgeSeconds: 86400
  }
});
// Now catch service-nologin.php.
global.toolbox.router.any('service.php', compiledFilesFetchHandler, {
  cache: {
    name: 'ETHz-exams-SW-service',
    maxEntries: 1000,
    maxAgeSeconds: 86400
  }
});


/*
    // attempt??!. the working one!!!!!!!
    global.toolbox.router.any('attempt.php', global.toolbox.networkFirst, {
      cache: {
        name: 'atteempptooo111111',
        maxEntries: 1000,
        maxAgeSeconds: 86400,
        ignoreSearch: true,
        queryOptions: {
          ignoreSearch: true
        }
      }
        });
*/

// We want no more than 500 videos in the cache. We check using a cache first strategy
global.toolbox.router.get(/\.(?:png|gif|jpg|svg|jpeg|tiff|bmp)$/, global.toolbox.networkFirst, {
  cache: {
    name: 'ETHz-exams-SW-images-cache',
    maxEntries: 1000,
    maxAgeSeconds: 86400
  }
});
// We want no more than 50 css in the cache. We check using a cache first strategy
global.toolbox.router.get(/\.(?:css)$/, global.toolbox.networkFirst, {
  cache: {
    name: 'ETHz-exams-SW-css-cache',
    maxEntries: 500,
    maxAgeSeconds: 86400
    }
  });
  // We want no more than 50 js in the cache. We check using a cache first strategy
  global.toolbox.router.get(/\.(?:js)$/, global.toolbox.networkFirst, {
    cache: {
      name: 'ETHz-exams-SW-js-cache',
      maxEntries: 500,
      maxAgeSeconds: 86400
    }
});

// We want no more than 500 videos in the cache. We check using a cache first strategy
global.toolbox.router.get(/\.(?:mp4|webm|ogg|flv|swf|mkv|vob|mng|avi|mov|qt|wmv|rm|rmvb|asf|m4p|m4v|mpg|mpeg|3gp|f4v|4p|f4a|f4b)$/, global.toolbox.networkFirst, {
  cache: {
    name: 'ETHz-exams-SW-locally_hosted_videos',
    maxEntries: 1000,
    maxAgeSeconds: 86400
  }
});
// We want no more than 1000 audios in the cache. We check using a cache first strategy
global.toolbox.router.get(/\.(?:aiff|wav|mp3|aac|wma|flac|mid|midi)$/, global.toolbox.networkFirst, {
  cache: {
    name: 'ETHz-exams-SW-locally_hosted_audios',
    maxEntries: 1000,
    maxAgeSeconds: 86400
  }
});
// We want no more than 1000 documents in the cache. We check using a cache first strategy
global.toolbox.router.get(/\.(?:pdf|doc|docx|xls|xlsx|ppt|pptx|ps|htm|html|txt|xml|odt|ods|odp|rtf|sxw|stw|csv|xps)$/, global.toolbox.networkFirst, {
  cache: {
    name: 'ETHz-exams-SW-documents',
    maxEntries: 1000,
    maxAgeSeconds: 86400
  }
});
// We want no more than 1000 zipped in the cache. We check using a cache first strategy
global.toolbox.router.get(/\.(?:zip|rar|tar|7z|tgz)$/, global.toolbox.networkFirst, {
  cache: {
    name: 'ETHz-exams-SW-zipped',
    maxEntries: 1000,
    maxAgeSeconds: 86400
  }
});

function check_watch_list(url) {

  if(url.indexOf('draftfiles_ajax.php?action=list') !== -1){
  console.error('found: draftfiles_ajax.php?action=list');
  }else{
  console.error('NOT: draftfiles_ajax.php?action=list');
  }
  return url;
}

// presents our default route. If the request did not match any prior routes it will match this one and run with a cache first strategy.
global.toolbox.router.get('/*', global.toolbox.networkFirst, {
  cache: {
    name: 'ETHz-exams-SW-default-route',
    maxEntries: 1000,
    maxAgeSeconds: 86400,
    queryOptions: {
      ignoreSearch: true
    }
  }
    });


})(self);
