<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.
/**
 * Wifi-Resilient quiz mode, serviceworker generator page.
 *
 * @package   quizaccess_wifiresilience
 * @copyright 2017 ETH Zurich (amr.hourani@let.ethz.ch)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('NO_DEBUG_DISPLAY', true);
define('DEBUG', false);

require_once(__DIR__ . '/../../../../config.php');

$cmid = optional_param('cmid', -1, PARAM_INT);
$quizid = optional_param('quizid', 0, PARAM_INT);

$filenamefilename = "serviceworker.php";

if ($quizid == 0 || !$quizid) {
    header('Content-Disposition: inline; filename="' . $filenamefilename . '"');
    header('Last-Modified: '. gmdate('D, d M Y H:i:s', time()) . ' GMT');
    header('Expires: '. gmdate('D, d M Y H:i:s', time() + 86000) . ' GMT');
    header('Pragma: ');
    header('Accept-Ranges: none');
    header('Content-Type: application/javascript; charset=utf-8');
    echo "// NO QUIZ ID - Terminate Service Worker!!!!";
    die;
}

$wifisettings = get_config('quizaccess_wifiresilience');

$rev = md5('offline.html');
$precahcedfilesstr =
    "workboxSW.precache([
    {
        url: 'offline.html',
        revision: '$rev',
    },";

$extraroutes = '';
$token = '';
$excludelist = '';

if ($wifisettings) {

    $extraroutes = $wifisettings->extraroutes;
    $token = $wifisettings->wifitoken;
    $exclude = $wifisettings->excludelist;

    if ($exclude && !empty($exclude) && $exclude != '') {
        $excludepieces = preg_split("/\\r\\n|\\r|\\n/", $exclude);

        if (count($excludepieces) != 0) {
            $excludelist .= 'var Wifiexcludedlistarray = []; ';
        }
        foreach ($excludepieces as $values) {
            $excludelist .= 'Wifiexcludedlistarray.push("' . $values . '"); ';
        }
    }

    $precachefiles = $wifisettings->precachefiles;

    if ($precachefiles) {
        $precachefilespieces = preg_split("/\\r\\n|\\r|\\n/", $precachefiles);
        foreach ($precachefilespieces as $values) {
            $rev = md5($values);
            $precahcedfilesstr .= ",
                {
                    url: '$values',
                    revision: '$rev',
                }";
        }
    }
}

$precahcedfilesstr .= ']);';

header('Content-Disposition: inline; filename="' . $filenamefilename . '"');
header('Last-Modified: ' . gmdate('D, d M Y H:i:s', time()) . ' GMT');
header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 86000) . ' GMT');
header('Pragma: ');
header('Accept-Ranges: none');
header('Content-Type: application/javascript; charset=utf-8');
?>
//////////////////////// Start Service Worker ////////////////////////////////
//<script>

(global => {
    'use strict';

    importScripts('js/localforage.js');
    importScripts('js/startswith.js');

    const responses_store = localforage.createInstance({
        name: 'Wifiresilience-exams-responses'
    });
    const status_store = localforage.createInstance({
        name: 'Wifiresilience-exams-question-status'
    });
    const responses_details_store = localforage.createInstance({
        name: "Wifiresilience-exams-individual-questions"
    });
    const questions_store = localforage.createInstance({
        name: "Wifiresilience-exams-all-questions"
    });
    const json_store = localforage.createInstance({
        name: "Wifiresilience-exams-cached_post_json"
    });

// Ensure that our service worker takes control of the page as soon as possible.
global.addEventListener('install', event => event.waitUntil(global.skipWaiting()));

const OFFLINE_URL = 'offline.html';

////////////// New ATTEMPT Caching /////////////

const ATTEMPT_CACHE = 'Wifiresilience-SW-attempt_cache';

function attemptStripParams(url) {
    return url.split('?')[0];
}

function attemptupdateCache(request) {
    return caches.open(ATTEMPT_CACHE).then(function (cache) {
        return fetch(request).then(function (response) {
            return cache.put(attemptStripParams(request.url), response.clone()).then(function () {
                return true;
            });
        });
    });
}

function attemptnetworkOrCache(request) {
    return fetch(request).then(function (response) {
        if (response.ok) {
            attemptupdateCache(request);
            return response;
        } else {
            return attemptfromCache(request);
        }
    })
    .catch(function () {
        return attemptfromCache(request);
    });
}

var ATTEMPT_FALLBACK = '<div style="margin: 0; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);">' +
                       '<svg width="302pt" height="340" viewBox="0 0 302 255" xmlns="http://www.w3.org/2000/svg">' +
                       '<path fill="#505153" stroke="#505153" stroke-width=".1" d="M73.7 34.7C86 21 105 14.7 123 ' +
                       '16.3c18 1.4 35.3 11 44.7 26.6 15.2-9.8 37-8.3 49.8 4.5 5.6 5.4 9 12.6 10 20.3 14.3 2 28 10 ' +
                       '35 22.6 6.7 11 7.3 25.2 1.6 36.7-7.6 16.3-25.4 26-43 26H74c-15 0-30-7-39-19-7-9-9.6-21.2-7-32.3 ' +
                       '3.4-16 17-28.3 32.6-32.5-.3-12.7 4.5-25.3 13-34.5m120.8 29c-11 1.2-21 7.4-27.2 16.5-6 8.5-8.2 ' +
                       '19.5-6 29.7 2 10 8.3 19.3 17 24.8 10 6.6 23.2 7.8 34.4 3.7 8.2-3 15.2-8.8 19.7-16.3 5.2-8.3 ' +
                       '6.8-18.7 4.8-28.2-2-9.7-8-18.4-16-24-7.7-5.2-17.3-7.5-26.6-6.4m-126 31c-5.6 2.7-4.8 12.7 1.7 ' +
                       '13.6 5.3 1.4 9.7-4 8.4-8.8-.5-4.6-6-6.6-10-4.8M80 94v14.3h3.5v-5.5h4.8V100h-4.8v-3.2h5.3V94H80m11 ' +
                       '0v14.3h3.5v-5.5h4.8V100h-4.8v-3.2h5.3V94H91m11 0v14.3h8.7v-2.8h-5.2V94H102m11 0v14.3h3.5V94H113m6.2 ' +
                       '0v14.3h3.3v-9l5 9h3.8V94H128v8l-4.7-8h-4m14.7 0v14.3h9v-2.8h-5.5v-2.7h5V100h-5v-3.2h5.3V94H134z"/>' +
                       '<path fill="#f7f7f7" stroke="#f7f7f7" stroke-width=".1" d="M194.4 63.6c9.3-1 19 1.2 26.6 6.5 8 5.6 14 ' +
                       '14.3 16 24 2 9.5.5 20-4.7 28.2-4.5 7.5-11.5 13.3-19.7 16.3-11.2 4-24.4 ' +
                       '3-34.4-3.7-8.7-5.5-15-14.8-17-25-2.2-10 ' +
                       '0-21 6-29.6 6-9 16.3-15.3 27.2-16.6m14.4 22c3 2.5 7 4 9.5 7 2 2.2 3.6 5.4 6.8 6-1.2-3.8-3-7.7-6.2-10.' +
                       '5-2.8-2-6.5-3.3-10-2.3m-26.5.7c-5.2 2-7.6 7.5-9.3 12.4 3.8-1.3 5.3-5.2 8.2-7.6 2.4-2.3 6-3.2 7.6-6-2.2 ' +
                       '0-4.4.4-6.5 1.2m5 7.7c-4 2.5-4 9.5 0 11.7 3.8 1 5.8-3.7 5.3-6.7 0-2.6-2.5-6.4-5.4-5m18.4 5c-.7 3.2 2 ' +
                       '8.2 5.8 6.4 4-3 3-10-1.4-12.2-2 1.3-4.5 3-4.4 5.8m-30.3 16.4c-1.5 2.2-3.6 4.6-2.8 7.5.7 5.8 8.2 8.8 13 ' +
                       '5.4 3-2.2 4.8-7 2.4-10.3-2.8-4-5.6-8.2-7.3-13-1.4 3.7-3 7.3-5.4 ' +
                       '10.4M191 123c-1.3 1.8-1.7 4-2.4 6 2.8-1.2 ' +
                       '5.5-3 8.5-3.7 4.7-1 8.7 2 12.7 3.7-.8-2.3-1.3-5-3-6.6-4-4.3-12-4-15.6.7z"/><path fill="#505153" ' +
                       'stroke="#505153" stroke-width=".1" d="M208.8 85.7c3.5-1 7.2.4 10 2.4 3.2 3 5 6.8 6.3 10.7-3-.8-4.6-' +
                       '4-6.7-6-2.6-3-6.4-4.6-9.5-7zM182.3 86.4c2-.8 4.3-1 6.5-1.3-1.7 3-5.2 4-7.6 6.2-3 2.4-4.4 6.3-8.2 7.6 ' +
                       '1.7-5 4-10.5 9.3-12.4z"/><path fill="#e4e3e3" stroke="#e4e3e3" stroke-width=".1" d="M68.4 94.6c4-1.8 ' +
                       '9.5.2 10 4.8 1.3 5-3 10.2-8.3 8.8-6.4-1-7.2-11-1.6-13.6m0 7.4c.2 2 2.3 3 3.6 4.2 3.4-2.6 3.5-7.7 ' +
                       '0-10-2 1.3-4 3.2-3.5 5.8zM80 94h8.8v2.8h-5.3v3.2h4.8v2.8h-4.8v5.5H80V94zM91 94h8.8v2.8h-5.3v3.2h4.' +
                       '8v2.8h-4.8v5.5H91V94z"/><path fill="#9aba2f" stroke="#9aba2f" stroke-width=".1" d="M102 94h3.5v11.' +
                       '5h5.3v2.8H102V94zM113 94h3.5v14.3H113V94zM119.2 94h4c1.7 2.7 3.3 5.3 4.8 8v-8h3.3v14.3h-3.7l-5-' +
                       '9v9H119V94zM134 94h8.8v2.7h-5.3v3.3h5v2.7h-5v2.8h5.5v2.8h-9V94z"/>' +
                       '<path fill="#505153" stroke="#505153" stroke-width=".1" d="M187.2 94c3-1.4 5.5 2.4 5.4 5 .6 3-1.5 ' +
                       '7.7-5.2 6.7-4-2.2-4.2-9.2-.2-11.6zM205.6 99c0-2.8 2.3-4.5 4.4-5.8 4.4 2.3 5.5 9 1.3 12.2-3.7 1.8-6.4-' +
                       '3.2-5.7-6.4zM68.5 102c-.6-2.6 1.5-4.5 3.4-6 3.5 2.5 3.4 7.6 0 10.2-1.3-1.3-3.4-2-3.5-4.2zM175.3 115.' +
                       '4c2.4-3 4-6.7 5.4-10.4 1.7 4.8 4.5 9 7.2 13 2.3 3.4.5 8.2-2.6 10.4-4.7 3.4-12.2.3-13-5.5-.7-3 1.4-5.4 ' +
                       '3-7.6zM191 123c3.6-4.6 11.6-5 15.6-.6 1.8 1.7 2.3 4.3 3 6.6-4-1.7-8-4.8-12.5-3.7-3 ' +
                       '.8-5.6 2.5-8.4 3.7.7-2 1-4.2 2.5-6z"/></svg>' +
                       '<center><input type="button" value="Refresh Page" onClick="window.location.reload()"></center></div>';

function attemptuseFallback() {
    return Promise.resolve(new Response(ATTEMPT_FALLBACK, { headers: {
        'Content-Type': 'text/html'
    }}));
}

function attemptfromCache(request) {
    return caches.open(ATTEMPT_CACHE).then(function (cache) {
        return cache.match(attemptStripParams(request.url)).then(function (matching) {
            return matching || Promise.reject('request-not-in-cache');
        });
    });
}

////////////// End New Attempt Caching //////////

////////////// New HTML/PHP etc Caching /////////////

const HTML_TEXT_CACHE = 'Wifiresilience-SW-runtime-routes';

function htmlupdateCache(request) {
    return caches.open(HTML_TEXT_CACHE).then(function (cache) {
        return fetch(request).then(function (response) {
            return cache.put(request.url, response.clone()).then(function () {
                return true;
            });
        });
    });
}

function htmlnetworkOrCache(request) {
    return fetch(request).then(function (response) {
        if (response.ok) {
            htmlupdateCache(request);
            return response;
        } else {
            return htmlfromCache(request);
        }
    })
    .catch(function () {
        return htmlfromCache(request);
    });
}

var HTML_TEXT_CACHE_FALLBACK =
    '<svg xmlns="http://www.w3.org/2000/svg" width="200" height="180" stroke-linejoin="round">' +
    '  <path stroke="#DDD" stroke-width="25" d="M99,18 15,162H183z"/>' +
    '  <path stroke-width="17" fill="#FFF" d="M99,18 15,162H183z" stroke="#eee"/>' +
    '  <path d="M91,70a9,9 0 0,1 18,0l-5,50a4,4 0 0,1-8,0z" fill="#aaa"/>' +
    '  <circle cy="138" r="9" cx="100" fill="#aaa"/>' +
    '</svg>';

function htmluseFallback() {
    return Promise.resolve(new Response(HTML_TEXT_CACHE_FALLBACK, { headers: {
        'Content-Type': 'image/svg+xml'
    }}));
}

function htmlfromCache(request) {
    return caches.open(HTML_TEXT_CACHE).then(function (cache) {
        return cache.match(request.url).then(function (matching) {
            return matching || Promise.reject('request-not-in-cache');
        });
    });
}

////////////// End HTML Caching //////////

////////////// New plugin Caching /////////////

const PLUGIN_CACHE_DB = 'Wifiresilience-SW-pluginfiles';

function pluginupdateCache(request) {
    return caches.open(PLUGIN_CACHE_DB).then(function (cache) {
        return fetch(request).then(function (response) {
            return cache.put(request.url, response.clone()).then(function () {
                return true;
            });
        });
    });
}

function pluginnetworkOrCache(request) {
    return fetch(request).then(function (response) {

        if (response.ok) {
            pluginupdateCache(request);
            return response;
        } else {
            return pluginfromCache(request);
        }
    })
    .catch(function () {
        return pluginfromCache(request);
    });
}

var PLUGIN_CACHE_FALLBACK = '';

function themeuseFallback() {
    return Promise.resolve(new Response(THEME_CACHE_FALLBACK));
}

function pluginfromCache(request) {
    return caches.open(PLUGIN_CACHE_DB).then(function (cache) {
        return cache.match(request.url).then(function (matching) {
            return matching || Promise.reject('request-not-in-cache');
        });
    });
}

////////////// End plugin Caching //////////

////////////// New theme Caching /////////////

const THEME_CACHE_DB = 'Wifiresilience-SW-theme';

function themeupdateCache(request) {
    return caches.open(THEME_CACHE_DB).then(function (cache) {
        return fetch(request).then(function (response) {
            return cache.put(request.url, response.clone()).then(function () {
                return true;
            });
        });
    });
}

function themenetworkOrCache(request) {
    return fetch(request).then(function (response) {

        if (response.ok) {
            themeupdateCache(request);
            return response;
        } else {
            return themefromCache(request);
        }
    })
    .catch(function () {
        return themefromCache(request);
    });
}

var THEME_CACHE_FALLBACK = '';

function themeuseFallback() {
    return Promise.resolve(new Response(THEME_CACHE_FALLBACK));
}

function themefromCache(request) {
    return caches.open(THEME_CACHE_DB).then(function (cache) {
        return cache.match(request.url).then(function (matching) {
            return matching || Promise.reject('request-not-in-cache');
        });
    });
}

////////////// End theme Caching //////////

////////////// New IMAGE.PHP Caching /////////////

const IMAGE_CACHE_DB = 'Wifiresilience-SW-flags';

function imageupdateCache(request) {
    return caches.open(IMAGE_CACHE_DB).then(function (cache) {
        return fetch(request).then(function (response) {
            return cache.put(request.url, response.clone()).then(function () {
                return true;
            });
        });
    });
}

function imagenetworkOrCache(request) {
    return fetch(request).then(function (response) {
        if (response.ok) {
            imageupdateCache(request);
            return response;
        } else {
            return imagefromCache(request);
        }
    })
    .catch(function () {
        return imagefromCache(request);
    });
}

var IMAGE_CACHE_FALLBACK =
    '<svg xmlns="http://www.w3.org/2000/svg" width="200" height="180" stroke-linejoin="round">' +
    '  <path stroke="#DDD" stroke-width="25" d="M99,18 15,162H183z"/>' +
    '  <path stroke-width="17" fill="#FFF" d="M99,18 15,162H183z" stroke="#eee"/>' +
    '  <path d="M91,70a9,9 0 0,1 18,0l-5,50a4,4 0 0,1-8,0z" fill="#aaa"/>' +
    '  <circle cy="138" r="9" cx="100" fill="#aaa"/>' +
    '</svg>';

function imageuseFallback() {
    return Promise.resolve(new Response(IMAGE_CACHE_FALLBACK, { headers: {
        'Content-Type': 'image/svg+xml'
    }}));
}

function imagefromCache(request) {
    return caches.open(IMAGE_CACHE_DB).then(function (cache) {
        return cache.match(request.url).then(function (matching) {
            return matching || Promise.reject('request-not-in-cache');
        });
    });
}

////////////// End IMAGE.PHP Caching //////////

////////////// New POST Fetch Caching /////////////

const POST_CACHE = 'Wifiresilience-SW-folderlists';

function postnetworkOrCache(request) {
    // Specical case for sync.php - change the url in case no-cache is ignored
    if ('onLine' in navigator) {
        if (!navigator.onLine && request.url.indexOf('sync.php') !== -1) {
            return Promise.reject('cant-cache-sync');
        }
    }
    return fetch(request, {cache: "no-store"}).then(function (response) {
        if (response.ok) {
            return response;
        } else {
            return postfromCache(request);
        }
    })
    .catch(function () {
        return postfromCache(request);
    });
}

 function postuseFallback(url) {

    var postfallbackresult = '';
    var headertype = "'Content-Type': 'text/html'";

    // Mainly for file upload / essay qtype when it loads on window load but no internet.
    if (url.indexOf('draftfiles_ajax.php?action=list') !== -1) {
        postfallbackresult = `
            {
                "path" : [
                    {
                        "name":"Files",
                        "path":"\/",
                        "icon":""
                    }
                ],
                "itemid":0,
                "list":[],
                "filecount":0,
                "filesize":0,
                "tree":{
                    "children":[]
                }
            }`;
    }
    // Mainly for Atto Images not loaded when promise fails (service-nologin.php?info=core_output_load_template).
    if (url.indexOf('service-nologin.php') !== -1 && url.indexOf('info=core_output_load_template') !== -1) {
        // Normal Image support.
        postfallbackresult = `
            [
                {
                    "error":false,
                    "data":"<img class=\\"icon {{extraclasses}}\\" {{#attributes}}{{name}}=\\"{{value}}\\" {{\/attributes}}\/>"
                }
            ]`;
        headertype = "'Content-Type': 'application/json'";
    }

    return Promise.resolve(new Response(postfallbackresult, { headers: {
        headertype
    }}));
}

function postfromCache(request) {
    // Avoid returning results from sync cache (POST).
    if (request.url.indexOf('sync.php') !== -1) {
        return;
        return Promise.reject('request-not-in-cache');
    }

    // There is actually no cache :-) Just to pass for draft_files
    return caches.open(POST_CACHE).then(function (cache) {
        return cache.match(request).then(function (matching) {
        return matching || Promise.reject('request-not-in-cache');
        });
    });
}

////////////// End New POST fetch Caching //////////

<?php
// Exclude Files List.
if ($excludelist != '') {
    echo $excludelist;
}
?>

/**
 * Set up a fetch handler that uses caching strategy corresponding to the value
 * of the `strategy` URL parameter.
 */
global.addEventListener('fetch', (event) => {

    if (event.request.url.indexOf('attempt.php') !== -1) {
        event.respondWith(attemptnetworkOrCache(event.request).catch(function () {
            return caches.match(OFFLINE_URL);
        }));
    } else if (event.request.method !== 'GET') {
        event.respondWith(postnetworkOrCache(event.request).catch(function () {
            return postuseFallback(event.request.url);
        }));
    } else if (event.request.url.indexOf('examfile.php') !== -1) {
        event.respondWith(htmlnetworkOrCache(event.request).catch(function () {
            return htmluseFallback(event.request.url);
        }));
    } else if (event.request.url.indexOf('image.php') !== -1) {
        event.respondWith(imagenetworkOrCache(event.request).catch(function () {
            return imageuseFallback(event.request.url);
        }));
    } else if (event.request.mode === "navigate"
        || (event.request.method === "GET" && event.request.headers.get("accept").indexOf("text/html") > -1)) {
        event.respondWith(
            fetch(event.request).catch(error => {
                /*
                The catch is only triggered if fetch() throws an exception, which will most likely
                happen due to the server being unreachable.
                If fetch() returns a valid HTTP response with an response code in the 4xx or 5xx
                range, the catch() will NOT be called.
                */
                console.log('Fetch failed; returning offline page instead.', error);
                return caches.match(OFFLINE_URL);
            })
        );
    }
<?php
// Exclude Files List.
if ($excludelist != '') {
    ?>
    else if (Wifiexcludedlistarray.indexOf(event.request.url) !== -1) {
        console.log('[Wifiresilience-SW] ServiceWorker: In Exclude List. (' + event.request.method + '): ' + event.request.url);
        event.respondWith(fetch(event.request));
    }
    <?php
}
?>

});

importScripts('js/workbox/workbox-sw.prod.v2.0.1.js');
importScripts('js/workbox/workbox-routing.prod.v2.0.0.js');
importScripts('js/workbox/workbox-cache-expiration.prod.v2.0.0.js');

const workboxSW = new self.WorkboxSW({clientsClaim: true});
self.workbox.logLevel = self.workbox.LOG_LEVEL.none;

// Precache files & Extra Routes from DB, do NOT remove.
<?php
if ($precahcedfilesstr != '') {
    echo $precahcedfilesstr;
}

if (!empty($extraroutes) && $extraroutes != '') {
    echo $extraroutes;
}
?>
// End of Precache files & Extra Routes from DB, do NOT remove

// The route for any requests from the googleapis origin
workboxSW.router.registerRoute(new RegExp('http(.*)://(.*).googleapis.com/(.*)'),
    workboxSW.strategies.networkFirst({
        cacheName: 'Wifiresilience-SW-googleapis',
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

// The route for any requests from the google statics origin
workboxSW.router.registerRoute(new RegExp('http(.*)://(.*).gstatic.com/(.*)'),
    workboxSW.strategies.networkFirst({
        cacheName: 'Wifiresilience-SW-gstatic',
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

// The route for any requests from the cloudflare origin
workboxSW.router.registerRoute('http(.*)://(.*).cloudflare.com/(.*)',
    workboxSW.strategies.networkFirst({
        cacheName: 'Wifiresilience-SW-cloudflare',
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
// The route for any requests from the mathjax origin
workboxSW.router.registerRoute('http(.*)://cdn.mathjax.org/(.*)',
    workboxSW.strategies.networkFirst({
        cacheName: 'Wifiresilience-SW-mathjax',
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

// The route for any css requests
workboxSW.router.registerRoute(/\.(?:css)$/,
    workboxSW.strategies.networkFirst({
        cacheName: 'Wifiresilience-SW-css',
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

// The route for any .js requests
workboxSW.router.registerRoute(/\.(?:js)$/,
    workboxSW.strategies.networkFirst({
        cacheName: 'Wifiresilience-SW-js',
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
workboxSW.router.registerRoute(
    /\.(?:mp4|webm|ogg|flv|swf|mkv|vob|mng|avi|mov|qt|wmv|rm|rmvb|asf|m4p|m4v|mpg|mpeg|3gp|f4v|4p|f4a|f4b)$/,
    workboxSW.strategies.networkFirst({
        cacheName: 'Wifiresilience-SW-videos',
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

// The route for any audio requests
workboxSW.router.registerRoute(/\.(?:aiff|wav|mp3|aac|wma|flac|mid|midi)$/,
    workboxSW.strategies.networkFirst({
        cacheName: 'Wifiresilience-SW-audio',
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

// The route for any documents requests
workboxSW.router.registerRoute(/\.(?:pdf|doc|docx|xls|xlsx|ppt|pptx|ps|htm|html|txt|xml|odt|ods|odp|rtf|sxw|stw|csv|xps)$/,
    workboxSW.strategies.networkFirst({
        cacheName: 'Wifiresilience-SW-documents',
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

// The route for any zipped requests
workboxSW.router.registerRoute(/\.(?:zip|rar|tar|7z|tgz)$/,
    workboxSW.strategies.networkFirst({
        cacheName: 'Wifiresilience-SW-zipped',
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

// The route for any zipped requests
workboxSW.router.registerRoute(/\.(?:png|gif|jpg|svg|jpeg|tiff|bmp)$/,
    workboxSW.strategies.networkFirst({
        cacheName: 'Wifiresilience-SW-images',
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
        cacheName: 'Wifiresilience-SW-youtube',
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

// The route for image.php requests
workboxSW.router.registerRoute(/image\.php$/,
    workboxSW.strategies.networkFirst({
        cacheName: 'Wifiresilience-SW-image_compiled',
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

// The route for yui_combo.php requests
workboxSW.router.registerRoute(/yui_combo\.php$/,
    workboxSW.strategies.networkFirst({
        cacheName: 'Wifiresilience-SW-yui_combo',
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

// The route for javascript.php requests
workboxSW.router.registerRoute(/javascript\.php$/,
    workboxSW.strategies.networkFirst({
        cacheName: 'Wifiresilience-SW-javascript_compiled',
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

// The route for styles_debug.php requests
workboxSW.router.registerRoute(/styles_debug\.php$/,
    workboxSW.strategies.networkFirst({
        cacheName: 'Wifiresilience-SW-styles_debug',
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

// The route for styles.php requests
workboxSW.router.registerRoute(/styles\.php$/,
    workboxSW.strategies.networkFirst({
        cacheName: 'Wifiresilience-SW-styles_compiled',
        cacheableResponse: {
            statuses: [0, 200]
        },
        networkTimeoutSeconds: 30
    })
);

/*
Catch all others!
You can also create an optional default handler that can respond to requests
that don't match anything.
If you don't create a default handler, then requests that don't match will
just be passed along to the network without service worker involvement.
*/
workboxSW.router.setDefaultHandler({
    handler: workboxSW.strategies.networkFirst({
    cacheName: 'Wifiresilience-SW-default-route',
    cacheExpiration: {
        maxEntries: 1500,
        maxAgeSeconds: 4 * 60 * 60,
    },
    cacheableResponse: {
        statuses: [0, 200]
    },
    networkTimeoutSeconds: 30,
    }),
    method: 'GET'
});

addEventListener('activate', function(event) {
    global.clients.claim();
});

global.addEventListener("traditionaluploadresponses", function(event) {
    console.error('traditional upload fired..');
    event.waitUntil(wifi_uploadResponses(<?php echo $cmid;?>, '<?php echo $token;?>'));
});

addEventListener('sync', function(event) {
    console.log('[Wifiresilience-SW] ServiceWorker: Firing Background Sync: ' + event.tag);
    if (event.tag === 'upload-responses' || event.tag === 'test-tag-from-devtools') {
        event.waitUntil(wifi_uploadResponses(<?php echo $cmid;?>, '<?php echo $token;?>'));
        console.log('[Wifiresilience-SW] ServiceWorker: wifi_uploadResponses function in Background Sync has been called. ' +
        'It will fail if Background Sync TOKEN is not available.');
    }
});

// In case Background Sync is not supported (ie FFX) use traditional upload.
if (!'SyncManager' in self) {
    wifi_uploadResponses(<?php echo $cmid;?>, '<?php echo $token;?>');
    console.log('[Wifiresilience-SW] Background Sync: Traditional Upload Function finished execution.');
}

function wifi_uploadResponses(cmid, token) {

    if (!token || token =='') {
        console.log('[Wifiresilience-SW] ServiceWorker: Background Sync TOKEN is not available, background sync is terminated.');
        return;
    }
    if (!cmid || cmid =='' || cmid == -1) {
        console.log('[Wifiresilience-SW] ServiceWorker: Background Sync CMID is not available, background sync is terminated.');
        return;
    }

    responses_store.startsWith('Wifiresilience-crs').then(function(results) {

        var localdatafiles = new FormData();

        var found = 0;
        var cntr = 0;
        for (var ldbindex in results) {
            found = 1;
            var blob = new Blob([results[ldbindex]], {type: "octet/stream"});

            localdatafiles.append('file' + cntr, blob, ldbindex + '_encrypted');
            cntr++;
        }

        if (found == 1) {
            console.log('[Wifiresilience-SW] ServiceWorker: Background Sync found data with Wifiresilience-crs ' +
                'prefix in Wifiresilience-exams-responses');
            fetchfilestoserver(token, localdatafiles, cmid, responses_store);
        }
    });

    status_store.startsWith('Wifiresilience-crs').then(function(results) {

        var localdatafiles = new FormData();
        var found = 0;
        var cntr = 0;

        for (var ldbindex in results) {
            found = 1;
            var blob = new Blob([results[ldbindex]], {type: "octet/stream"});

            localdatafiles.append('file' + cntr, blob, ldbindex + '_sequence');
            cntr++;
        }

        if (found == 1) {
            console.log('[Wifiresilience-SW] ServiceWorker: Background Sync found data with Wifiresilience-crs ' +
                'prefix in Wifiresilience-exams-question-status');
            fetchfilestoserver(token, localdatafiles, cmid, status_store);
        }
    });

    // Start: Delete generic information about questions.
    responses_details_store.startsWith('Wifiresilience-crs').then(function(results) {
        for (var ldbindex in results) {
            responses_details_store.removeItem(ldbindex);
            console.log('[Wifiresilience-SW] ServiceWorker: Background Sync (Wifiresilience-exams-individual-questions) ' +
                '- locally stored files. Deletion OK, Index: ' + ldbindex);
        }
    });

    questions_store.startsWith('Wifiresilience-crs').then(function(results) {
        for (var ldbindex in results) {
            questions_store.removeItem(ldbindex);
            console.log('[Wifiresilience-SW] ServiceWorker: Background Sync (Wifiresilience-exams-all-questions) ' +
                '- locally stored files. Deletion OK, Index: ' + ldbindex);
        }
    });
    // End: delete generic information about questions.
}

function fetchfilestoserver(token, data, cmid, whichstore) {
    if (token == '' || !token) {
        return;
    }
    fetch('./sync_localresponses.php?cmid=' + cmid + '&token=' + token, {
        method: 'POST',
        body: data,
    }).then(function (response) {
        if (response.ok) {
            console.log('[Wifiresilience-SW] ServiceWorker: Background Sync Went OK.. ' +
                'Now delete locally stored files, EXCLUDING CMID: ' + cmid);

            // After Successful uploading, Delete all responses that get uploaded, EXCLUDING current CMID!
            whichstore.startsWith('Wifiresilience-crs').then(function(results) {
                for (var ldbindex in results) {
                    var searchfor = '-cm' + cmid;
                    if (ldbindex.indexOf(searchfor) !== -1) { // Found current CMID - ignore
                        console.log('[Wifiresilience-SW] ServiceWorker: Background Sync ' +
                            '- IGNORE locally stored files in with index: '+ldbindex);
                    } else {
                        whichstore.removeItem(ldbindex);  
                        console.log('[Wifiresilience-SW] ServiceWorker: Background Sync ' +
                            '- locally stored files in Deletion OK, Index: '+ldbindex);
                    }
                }
            });
        }
        return response;
    })
    .then(function (text) {
        console.log('[Wifiresilience-SW] ServiceWorker: Background Sync Request successful', text);
    })
    .catch(function (error) {
        console.log('[Wifiresilience-SW] ServiceWorker: Background Sync Request failed', error);
    });
}

// Inside service worker errors.
global.addEventListener('error', function(e) {
    console.log('[Wifiresilience-SW] ServiceWorker Errors: ', e.filename, e.lineno, e.colno, e.message);
});

})(self);
//////////////////////// End Service Worker ////////////////////////////////
