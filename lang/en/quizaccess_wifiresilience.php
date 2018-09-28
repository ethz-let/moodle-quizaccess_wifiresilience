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
 * Strings for the quizaccess_wifiresilience plugin.
 *
 * @package   quizaccess_wifiresilience
 * @copyright 2017 ETH Zurich (amr.hourani@let.ethz.ch)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['answerchanged'] = 'Answer changed';
$string['backtothequiz'] = 'Back to the quiz';
$string['changesmadereallygoaway'] = 'Your responses have not been saved to the server. Are you sure you want to leave this attempt.';
$string['dataprocessedsuccessfully'] = 'Data processed successfully ({$a}).';
$string['inspectionprocessedsuccessfully'] = 'Data processed successfully.';
$string['description'] = 'This exam is in <strong>Wifi Resilience</strong> mode.';
$string['uploadresponsesadmin'] = 'Adminstrators can: ';
$string['or'] = 'or';
$string['descriptionlink'] = 'Upload exported responses';
$string['finishattemptsafterupload'] = 'Submit and finish the attempt contained in the response file?';
$string['uploadfinishtime'] = 'Attempt/submission Time';
$string['lastsaved'] = 'Last saved: {$a}';
$string['lastsavedtotheserver'] = 'Last saved to the server: {$a}';
$string['lastsavedtothiscomputer'] = 'Last saved to this computer: {$a}';
$string['loggedinaswronguser'] = 'You have logged in with an account other than the one that was attempting the quiz. That will not work. Click Continue to login again as the right user.';
$string['logindialogueheader'] = 'You may need to log in again';
$string['loginokagain'] = 'Your login is now ok.';
$string['wifiresilienceenabled'] = 'Wifi Resilience Mode';
$string['wifiresilienceenabled_desc'] = 'You can configure whether the Wifi Resilience Mode should be enabled by default for new quizzes or not.';
$string['wifiresilienceenabled_help'] = 'The goal of this option is to let students attempt a quiz even if the network connection is not reliable. The students can move between pages of the quiz even if the server is not available, and all their answers are stored locally, and sent to the server when possible.';
$string['wifiresilience:uploadresponses'] = 'Upload response files';
$string['wifiresilience:adminmessages'] = 'View administration messages';
$string['wifiresilience:browserchecks'] = 'View browser checks';
$string['wifiresilience:inspectresponses'] = 'Inspect responses';
$string['wifiresilience:localresponses'] = 'Inspect local responses (local storage)';
$string['wifiresilience:viewtechchecks'] = 'View technical checks (even when this option is disabled via quiz settings)';
$string['wifiresilience:viewlivedevices'] = 'View live devices';
$string['livedevices'] = 'Live devices';
$string['lastseen'] = 'Last seen';
$string['lastsync'] = 'Last synced';
$string['status'] = 'Status';
$string['currentissue'] = 'Current issue';
$string['pluginname'] = 'Quiz Wifi Resilience Mode';
$string['privatekey'] = 'Encryption private key';
$string['privatekey_desc'] = 'You can use public-key cryptography to protect the downloaded responses. To do that, you need to supply a private/public key pair. You can generate a private key using <code>openssl genrsa -out rsa_1024_priv.pem 1024</code> at the command-line (if you have OpenSSL installed from https://www.openssl.org/). Then paste the content of the rsa_1024_priv.pem file into this box.';
$string['processingcomplete'] = 'Processing complete';
$string['processingfile'] = 'Processing file {$a}';
$string['decryptingcomplete'] = 'Decrypting file complete';
$string['decryptingfile'] = 'Decrypting file {$a}';
$string['inspectingfile'] = 'Inspecting file {$a}';
$string['inspectingfiledesc'] = 'Here you can decrypt emergency files, and encrypt them again. Please use this tool with caution. The inspection tool is built to give the exam admins the option to modify exam attempts; such as, but not limited to, course ID, exam ID, submission time, answer modification, etc. ';
$string['inspect'] = 'Inspect response files';
$string['publickey'] = 'Encryption public key';
$string['publickey_desc'] = 'This must correspond to the private key. You can generate it from the private key using <code>openssl rsa -pubout -in rsa_1024_priv.pem -out rsa_1024_pub.pem</code> then past the contents of rsa_1024_pub.pem here.';
$string['responsefiles'] = 'Response files';
$string['responsefiles_help'] = 'During a quiz attempt you can download a response file by clicking on the blinking wireless network connection status icon. You may do this several times to save different versions of your response file. The filename contains the prefix ETHz, date and timestamp and the extension .eth. (example: ETHz-crs229-cm643-id558-u8-a25197-d201803010842.eth). If not configured otherwise the file is saved in the "download folder".';
$string['reviewthisattempt'] = 'Review this attempt';
$string['savefailed'] = 'Note: From time to time you should:';
$string['savetheresponses'] = 'Download copy of answers'; //Save the responses
$string['savingdots'] = 'Saving to server...';
$string['savingtryagaindots'] = 'Trying again to save to the server ...';
$string['submitfailed'] = 'Exam submission failed';
$string['submitfaileddownloadmessage'] = '<br /><strong>Or</strong><br />{$a}<br />(Note: NO data has been lost. Please let the exam invigilator know and they will take a copy of your downloaded file and process it.)';
$string['submitfailedmessage'] = 'Your responses could not be submitted. You can either try:';
$string['submitting'] = '<h3>Submitting.. Please wait..</h3>';
$string['submitallandfinishtryagain'] = 'Submit all and finish (try again)';
$string['uploadfailed'] = 'The upload failed';
$string['downloadedecryptedfile'] = 'Download decrypted File';
$string['testencryption'] = 'Test device and server encryption';
$string['uploadingresponsesfor'] = 'Uploading responses for {$a}';
$string['uploadmoreresponses'] = 'Upload more responses';
$string['uploadresponses'] = 'Upload response files';
$string['uploadresponsesfor'] = 'Upload response file for {$a}';
$string['uploadinspection'] = 'Inspect responses';
$string['uploadinspectionfor'] = 'Inspect responses for {$a}';
$string['localresponsesfor'] = 'Responses stored on <strong>this</strong> local machine for {$a}';
$string['loadlocalresponses'] = 'Check, download, save or delete responses stored on <strong>this</strong> computer';
$string['takeattemptfromjson'] = 'Use unencrypted attempt ID';
$string['takeattemptfromjson_help'] = '***READ CAREFULLY*** If an attempt is already damaged or if you want to create new attempt to continue on it and avoid question sequence integrity issues, then use this option with absolute care!. Please note that the attempt ID still need to match an actual attempt, whether created before, or just created by administrator (logged in as student) in order to create an attempt to build on it (in order to upload student original response file - emergency file - we need a valid attempt).<br /><br /><font color="red">Please DO NOT tick this box unless you fully understand how attempts are managed!</font>';
$string['dangeryes'] = 'Yes (<font color="red">!!Danger!!</font>)';
$string['prechecks'] = 'Display technical checks';
$string['prechecks_help'] = 'This option will display the technical details of the browser before commencing the exam. Checks are for service workers, local storage, request of increase of local storage and others.';
$string['techerrors'] = 'Display technical errors';
$string['techerrors_help'] = 'This feature is useful to understand the underlying technical reason for the failure of exam submission. It only shows the error at the bottom of the submission page if there is any.';
$string['navdetails'] = 'Display server and device status';
$string['navdetails_help'] = 'Show server and device status last time the data was saved (locally and on server). It also shows online status details (whether device/server is connected or not). It also displays a link for emergency file to be downloaded.';
$string['watchxhr'] = 'Watch live events';
$string['technicalchecks'] = 'Storage checks for current browser';
$string['watchxhr_help'] = 'One URL per line. Some question types require live checks or uploads with the server. If they are added here, the plugin will be able to count the time-offline for the user, and then add it automatically to the total extra time they get so they finish the exam with precise grace time or time limit.';
$string['fromfile'] = 'Submission time in uploaded file';
$string['syncedfiles'] = 'Synced response files in background';
$string['fetchandlog'] = 'Embedded files caching (attachements)';
$string['fetchandlog_help'] = 'One per line. If you want to cache attachements (of any static type; i.e. docx, pdf, xls, zip, html etc) that are embedded in some questions as extra resource or for further readings, then add the full URLs you want them to be cached. Example: https://example.org/instructions.docx will cache instructions.docx that is embedded in the question(s). Usually attachements in exams have "pluginfile.php" inside the link. Please note that for cross-origin (remote domains or different subdomains, please make sure both are served via https, and also those domains allow CORS. Important: This option can defeat security principles of cross domain communications, so please use with care, or better only use locally hosted files on same domain).';
$string['now'] = 'Now';
$string['reference'] = 'Reference';
$string['filetype'] = 'Type';
$string['wifitoken'] = 'Background sync token';
$string['serviceworkermgmt'] = 'Service worker management';
$string['resetserviceworker'] = 'Reset service worker';
$string['refreshserviceworker'] = 'Refresh service worker';
$string['stopserviceworker'] = 'Stop service worker';
$string['syncserviceworker'] = 'Fire backgroundsync';

$string['loadingstep1'] = 'Setting up {$a}';
$string['loadingstep2'] = 'Preparing exam structure..';
$string['loadingstep3'] = 'Preparing service worker static and dynamic routes..';
$string['loadingstep4'] = 'Preparing exam database..';
$string['loadingstep5'] = 'Preparing exam questions..';
$string['loadingstep6'] = 'Preparing exam navigation..';
$string['loadingstep7'] = 'Encrypting exam data..';
$string['loadingstep8'] = 'Verifying network status..';
$string['loadingstep9'] = 'Watching live network requests..';
$string['loadingstep10'] = 'Exam starting..';

$string['wifitoken_help'] = 'Web service token to send emergency files in background when the device is connected. This token can be generated from (webservicetokens / manage tokens) in site administration pages.';
$string['precachefiles'] = 'Precache files';
$string['precachefiles_help'] = 'Only static files. One link per line. If you would like the service worker to pre-cache specific files (only static file; ie. css, jpg, html etc), please add the direct link for them, one per line. Note: The precached URLs will automatically be served using a cache-first strategy.';
$string['excludelist'] = 'Exclude files';
$string['excludelist_help'] = 'Link/file per line. Exclude specific files/links from caching with service worker. This is useful when you want some files or links to be in NetworkOnly (never get cached) mode.';
$string['quizfinishtime'] = 'Quiz time limit (max allowed quiz time)';
$string['usefinalsubmissiontime'] = 'Use final submission time from file (if available)';
$string['usefinalsubmissiontime_help'] = 'When user attempt to submit and finish the test (or if auto submitted at the end by the quiz timer), a parameter called "final_submission_time" is added to the file showing when the submission has happened. If no final submission has happened, the value for "final_submission_time" is 0. if 0, then the script will ignore this parameter and use one of the below finish time options.';
$string['countrealofflinetime'] = 'Deduct offline time (if available)';
$string['countrealofflinetime_help'] = 'Deduct offline time from attempt time based on "real_offline_time". The attempt time, and regradless of the option (in the form below) you choose, will be minus the total offline time (time the user was not able to commence work in specific questions due to lack of internet).<br><br>Important: This value is already calculated in final submission time. This option only works with attempts that have not been selected as "finished".';
$string['extraroutes'] = 'Extra routes';
$string['extraroutes_help'] = 'Add extra routes to exam service worker. If you want exam service worker [ETHz-SW] to catch extra routes (file extensions, web addresses, etc) and treat them differently when the connection drops. you can add as an example:<br>
<code>
// We want no more than 1000 files with extension "XYZ" in the cache.<br />
// We check using a networkFirst Strategy.<br />
global.toolbox.router.get(/\.(?:XYZ)$/, global.toolbox.<strong>networkFirst</strong>, {<br />
&nbsp;&nbsp;&nbsp;cache: {<br />
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;name: \'ETHz-exams-<strong>XYZ</strong>\', // Local database name (indexedDb).<br />
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;maxEntries: 1000, // Max number of files to save.<br />
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;maxAgeSeconds: 86400 // Max time in seconds before they get deleted/expired.<br />
&nbsp;&nbsp;&nbsp;},<br />
&nbsp;&nbsp;&nbsp;origin: /\.googleapis\.com$/ //Optional, if files located outside moodle domain.<br />
});<br />
</code>
<h2 id="options"><a class="anchorjs-link " href="#options" aria-label="Anchor link for: options" style="font-style: normal; font-variant: normal; font-weight: normal; font-stretch: normal; font-size: 1em; line-height: inherit; font-family: anchorjs-icons; position: absolute; margin-left: -1em; padding-right: 0.5em;"></a>Routes options</h2>

<p>All options can be specified globally via properties of <code class="highlighter-rouge">toolbox.options</code>.
Any individual options can be configured on a per-handler basis, via the <code class="highlighter-rouge">Object</code> passed as the
third parameter to <code class="highlighter-rouge">toolbox.router</code> methods.</p>

<h3 id="debug-boolean"><a class="anchorjs-link " href="#debug-boolean" aria-label="Anchor link for: debug boolean"  style="font-style: normal; font-variant: normal; font-weight: normal; font-stretch: normal; font-size: 1em; line-height: inherit; font-family: anchorjs-icons; position: absolute; margin-left: -1em; padding-right: 0.5em;"></a>debug [Boolean]</h3>
<p>Determines whether extra information is logged to the browser’s console.</p>

<p><em>Default</em>: <code class="highlighter-rouge">false</code></p>

<h3 id="networktimeoutseconds-number"><a class="anchorjs-link " href="#networktimeoutseconds-number" aria-label="Anchor link for: networktimeoutseconds number"  style="font-style: normal; font-variant: normal; font-weight: normal; font-stretch: normal; font-size: 1em; line-height: inherit; font-family: anchorjs-icons; position: absolute; margin-left: -1em; padding-right: 0.5em;"></a>networkTimeoutSeconds [Number]</h3>
<p>A timeout that applies to the <code class="highlighter-rouge">toolbox.networkFirst</code> built-in handler.
If <code class="highlighter-rouge">networkTimeoutSeconds</code> is set, then any network requests that take longer than that amount of time
will automatically fall back to the cached response if one exists. When
<code class="highlighter-rouge">networkTimeoutSeconds</code> is not set, the browser’s native networking timeout logic applies.</p>

<p><em>Default</em>: <code class="highlighter-rouge">null</code></p>

<h3 id="cache-object"><a class="anchorjs-link " href="#cache-object" aria-label="Anchor link for: cache object" style="font-style: normal; font-variant: normal; font-weight: normal; font-stretch: normal; font-size: 1em; line-height: inherit; font-family: anchorjs-icons; position: absolute; margin-left: -1em; padding-right: 0.5em;"></a>cache [Object]</h3>
<p>Various properties of <code class="highlighter-rouge">cache</code> control the behavior of the default cache when set via
<code class="highlighter-rouge">toolbox.options.cache</code>, or the cache used by a specific request handler.</p>

<h3 id="cachename-string"><a class="anchorjs-link " href="#cachename-string" aria-label="Anchor link for: cachename string"  style="font-style: normal; font-variant: normal; font-weight: normal; font-stretch: normal; font-size: 1em; line-height: inherit; font-family: anchorjs-icons; position: absolute; margin-left: -1em; padding-right: 0.5em;"></a>cache.name [String]</h3>
<p>The name of the <a href="https://developer.mozilla.org/en-US/docs/Web/API/Cache" target="_blank"><code class="highlighter-rouge">Cache</code></a>
used to store <a href="https://fetch.spec.whatwg.org/#response-class" target="_blank"><code class="highlighter-rouge">Response</code></a> objects. Using a unique name
allows you to customize the cache’s maximum size and age of entries.</p>

<p><em>Default</em>: Generated at runtime based on the service worker’s <code class="highlighter-rouge">registration.scope</code> value.</p>

<h3 id="cachemaxentries-number"><a class="anchorjs-link " href="#cachemaxentries-number" aria-label="Anchor link for: cachemaxentries number"  style="font-style: normal; font-variant: normal; font-weight: normal; font-stretch: normal; font-size: 1em; line-height: inherit; font-family: anchorjs-icons; position: absolute; margin-left: -1em; padding-right: 0.5em;"></a>cache.maxEntries [Number]</h3>
<p>Imposes a least-recently used cache expiration policy
on entries cached via the various built-in handlers. You can use this with a cache that’s dedicated
to storing entries for a dynamic set of resources with no natural limit. Setting <code class="highlighter-rouge">cache.maxEntries</code> to, e.g.,
<code class="highlighter-rouge">10</code> would mean that after the 11th entry is cached, the least-recently used entry would be
automatically deleted. The cache should never end up growing beyond <code class="highlighter-rouge">cache.maxEntries</code> entries.
This option will only take effect if <code class="highlighter-rouge">cache.name</code> is also set.
It can be used alone or in conjunction with <code class="highlighter-rouge">cache.maxAgeSeconds</code>.</p>

<p><em>Default</em>: <code class="highlighter-rouge">null</code></p>

<h3 id="cachemaxageseconds-number"><a class="anchorjs-link " href="#cachemaxageseconds-number" aria-label="Anchor link for: cachemaxageseconds number"  style="font-style: normal; font-variant: normal; font-weight: normal; font-stretch: normal; font-size: 1em; line-height: inherit; font-family: anchorjs-icons; position: absolute; margin-left: -1em; padding-right: 0.5em;"></a>cache.maxAgeSeconds [Number]</h3>
<p>Imposes a maximum age for cache entries, in seconds.
You can use this with a cache that’s dedicated to storing entries for a dynamic set of resources
with no natural limit. Setting <code class="highlighter-rouge">cache.maxAgeSeconds</code> to, e.g., <code class="highlighter-rouge">60 * 60 * 24</code> would mean that any
entries older than a day would automatically be deleted.
This option will only take effect if <code class="highlighter-rouge">cache.name</code> is also set.
It can be used alone or in conjunction with <code class="highlighter-rouge">cache.maxEntries</code>.</p>

<p><em>Default</em>: <code class="highlighter-rouge">null</code></p>

<h2 id="handlers"><a class="anchorjs-link " href="#handlers" aria-label="Anchor link for: handlers"  style="font-style: normal; font-variant: normal; font-weight: normal; font-stretch: normal; font-size: 1em; line-height: inherit; font-family: anchorjs-icons; position: absolute; margin-left: -1em; padding-right: 0.5em;"></a>Handlers</h2>

<p>There are five built-in handlers to cover the most common network strategies. For more information about offline strategies see the <a href="http://jakearchibald.com/2014/offline-cookbook/">Offline Cookbook</a>.</p>

<h3 id="toolboxnetworkfirst"><a class="anchorjs-link " href="#toolboxnetworkfirst" aria-label="Anchor link for: toolboxnetworkfirst"  style="font-style: normal; font-variant: normal; font-weight: normal; font-stretch: normal; font-size: 1em; line-height: inherit; font-family: anchorjs-icons; position: absolute; margin-left: -1em; padding-right: 0.5em;"></a><code class="highlighter-rouge">toolbox.networkFirst</code></h3>
<p>Try to handle the request by fetching from the network. If it succeeds, store the response in the cache. Otherwise, try to fulfill the request from the cache. This is the strategy to use for basic read-through caching. It’s also good for API requests where you always want the freshest data when it is available but would rather have stale data than no data.</p>

<h3 id="toolboxcachefirst"><a class="anchorjs-link " href="#toolboxcachefirst" aria-label="Anchor link for: toolboxcachefirst"  style="font-style: normal; font-variant: normal; font-weight: normal; font-stretch: normal; font-size: 1em; line-height: inherit; font-family: anchorjs-icons; position: absolute; margin-left: -1em; padding-right: 0.5em;"></a><code class="highlighter-rouge">toolbox.cacheFirst</code></h3>
<p>If the request matches a cache entry, respond with that. Otherwise try to fetch the resource from the network. If the network request succeeds, update the cache. This option is good for resources that don’t change, or have some other update mechanism.</p>

<h3 id="toolboxfastest"><a class="anchorjs-link " href="#toolboxfastest" aria-label="Anchor link for: toolboxfastest"  style="font-style: normal; font-variant: normal; font-weight: normal; font-stretch: normal; font-size: 1em; line-height: inherit; font-family: anchorjs-icons; position: absolute; margin-left: -1em; padding-right: 0.5em;"></a><code class="highlighter-rouge">toolbox.fastest</code></h3>
<p>Request the resource from both the cache and the network in parallel. Respond with whichever returns first. Usually this will be the cached version, if there is one. On the one hand this strategy will always make a network request, even if the resource is cached. On the other hand, if/when the network request completes the cache is updated, so that future cache reads will be more up-to-date.</p>

<h3 id="toolboxcacheonly"><a class="anchorjs-link " href="#toolboxcacheonly" aria-label="Anchor link for: toolboxcacheonly"  style="font-style: normal; font-variant: normal; font-weight: normal; font-stretch: normal; font-size: 1em; line-height: inherit; font-family: anchorjs-icons; position: absolute; margin-left: -1em; padding-right: 0.5em;"></a><code class="highlighter-rouge">toolbox.cacheOnly</code></h3>
<p>Resolve the request from the cache, or fail. This option is good for when you need to guarantee that no network request will be made, for example saving battery on mobile.</p>

<h3 id="toolboxnetworkonly"><a class="anchorjs-link " href="#toolboxnetworkonly" aria-label="Anchor link for: toolboxnetworkonly"  style="font-style: normal; font-variant: normal; font-weight: normal; font-stretch: normal; font-size: 1em; line-height: inherit; font-family: anchorjs-icons; position: absolute; margin-left: -1em; padding-right: 0.5em;"></a><code class="highlighter-rouge">toolbox.networkOnly</code></h3>
<p>Handle the request by trying to fetch the URL from the network. If the fetch fails, fail the request. Essentially the same as not creating a route for the URL at all.</p>

<h2 id="expressive-approach"><a class="anchorjs-link " href="#expressive-approach" aria-label="Anchor link for: expressive approach"  style="font-style: normal; font-variant: normal; font-weight: normal; font-stretch: normal; font-size: 1em; line-height: inherit; font-family: anchorjs-icons; position: absolute; margin-left: -1em; padding-right: 0.5em;"></a>Methods</h2>

<h3 id="toolboxroutergetpostputdeleteheadurlpattern-handler-options"><a class="anchorjs-link " href="#toolboxroutergetpostputdeleteheadurlpattern-handler-options" aria-label="Anchor link for: toolboxroutergetpostputdeleteheadurlpattern handler options"  style="font-style: normal; font-variant: normal; font-weight: normal; font-stretch: normal; font-size: 1em; line-height: inherit; font-family: anchorjs-icons; position: absolute; margin-left: -1em; padding-right: 0.5em;"></a><code class="highlighter-rouge">toolbox.router.&lt;get|post|put|delete|head&gt;(urlPattern, handler, options)</code></h3>

<p>Create a route that causes requests for URLs matching <code class="highlighter-rouge">urlPattern</code> to be resolved by calling <code class="highlighter-rouge">handler</code>. Matches requests using the GET, POST, PUT, DELETE or HEAD HTTP methods respectively.</p>

<ul>
  <li><code class="highlighter-rouge">urlPattern</code> - an Express style route. See the docs for the <a href="https://github.com/pillarjs/path-to-regexp"  target="_blank">path-to-regexp</a> module for the full syntax</li>
  <li><code class="highlighter-rouge">handler</code> - a request handler, as <a href="#handlers">described above</a></li>
  <li><code class="highlighter-rouge">options</code> - an object containing options for the route. This options object will be passed to the request handler. The <code class="highlighter-rouge">origin</code> option is specific to the router methods, and can be either an exact string or a Regexp against which the origin of the Request must match for the route to be used.</li>
</ul>

<h3 id="toolboxrouteranyurlpattern-handler-options"><a class="anchorjs-link " href="#toolboxrouteranyurlpattern-handler-options" aria-label="Anchor link for: toolboxrouteranyurlpattern handler options"  style="font-style: normal; font-variant: normal; font-weight: normal; font-stretch: normal; font-size: 1em; line-height: inherit; font-family: anchorjs-icons; position: absolute; margin-left: -1em; padding-right: 0.5em;"></a><code class="highlighter-rouge">toolbox.router.any(urlPattern, handler, options)</code></h3>
<p>Like <code class="highlighter-rouge">toolbox.router.get</code>, etc., but matches any HTTP method.</p>

<h3 id="toolboxrouterdefault"><a class="anchorjs-link " href="#toolboxrouterdefault" aria-label="Anchor link for: toolboxrouterdefault"  style="font-style: normal; font-variant: normal; font-weight: normal; font-stretch: normal; font-size: 1em; line-height: inherit; font-family: anchorjs-icons; position: absolute; margin-left: -1em; padding-right: 0.5em;"></a><code class="highlighter-rouge">toolbox.router.default</code></h3>
<p>Takes a function to use as the request handler for any GET request that does not match a route.</p>

<h3 id="toolboxprecachearrayofurls"><a class="anchorjs-link " href="#toolboxprecachearrayofurls" aria-label="Anchor link for: toolboxprecachearrayofurls"  style="font-style: normal; font-variant: normal; font-weight: normal; font-stretch: normal; font-size: 1em; line-height: inherit; font-family: anchorjs-icons; position: absolute; margin-left: -1em; padding-right: 0.5em;"></a><code class="highlighter-rouge">toolbox.precache(arrayOfURLs)</code></h3>
<p>Add each URL in arrayOfURLs to the list of resources that should be cached during the service worker install step. Note that this needs to be called before the install event is triggered, so you should do it on the first run of your script.</p>

<h3 id="toolboxcacheurl-options"><a class="anchorjs-link " href="#toolboxcacheurl-options" aria-label="Anchor link for: toolboxcacheurl options"  style="font-style: normal; font-variant: normal; font-weight: normal; font-stretch: normal; font-size: 1em; line-height: inherit; font-family: anchorjs-icons; position: absolute; margin-left: -1em; padding-right: 0.5em;"></a><code class="highlighter-rouge">toolbox.cache(url, options)</code></h3>
<p>Causes the resource at <code class="highlighter-rouge">url</code> to be added to the cache and returns a Promise that resolves with void. The <code class="highlighter-rouge">options</code> parameter supports the <code class="highlighter-rouge">debug</code> and <code class="highlighter-rouge">cache</code> <a href="#options">global options</a>.</p>

<h3 id="toolboxuncacheurl-options"><a class="anchorjs-link " href="#toolboxuncacheurl-options" aria-label="Anchor link for: toolboxuncacheurl options"  style="font-style: normal; font-variant: normal; font-weight: normal; font-stretch: normal; font-size: 1em; line-height: inherit; font-family: anchorjs-icons; position: absolute; margin-left: -1em; padding-right: 0.5em;"></a><code class="highlighter-rouge">toolbox.uncache(url, options)</code></h3>
<p>Causes the resource at <code class="highlighter-rouge">url</code> to be removed from the cache and returns a promise that resolves to true if the cache entry is deleted. The <code class="highlighter-rouge">options</code> parameter supports  the <code class="highlighter-rouge">debug</code> and <code class="highlighter-rouge">cache</code> <a href="#options">global options</a>.</p>

';
