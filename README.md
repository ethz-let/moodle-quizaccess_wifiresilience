
#  Wifiresilience (ETH)



##  What it is:

The Wifiresilience accessrule is designed to allow students to continue work on deferred-feedback quiz attempts even if the network connection goes down. For this purpose Wifiresilience makes use of localstorage. Stored answers can be accessed, synced to server and also be downloaded manually in case connection does not come back.

Note:
Wifiresilience is not designed for offline use per se. A working connection is required to start quizzes. Wifiresilience will intervene when connection breaks down.

The Wifiresilience accessrule is built on top of Quiz fault-tolerant mode (quizaccess_offlinemode) by Tim Hunt but adding additional features. Credits here go to Tim Hunt for creating Quiz fault-tolerant mode!



##  Installation:

1. Extract the contents of the downloaded zip to `mod/quiz/accessrule`.
1. Rename the extracted folder to `wifiresilience`.
1. Start the Moodle upgrade procedure.



## Admin Settings:

- **quizaccess_wifiresilience | defaultenabled**
  You can configure whether the Wifi Resilience Mode should be enabled by default for new quizzes or not.

- **quizaccess_wifiresilience | privatekey**
  You can use public-key cryptography to protect the downloaded responses. To do that, you need to supply a private/public key pair. You can generate a private key using `openssl genrsa -out rsa_1024_priv.pem 1024` at the command-line (if you have OpenSSL installed from https://www.openssl.org/). Then paste the content of the rsa_1024_priv.pem file into this box.

- **quizaccess_wifiresilience | publickey**
  This must correspond to the private key. You can generate it from the private key using `openssl rsa -pubout -in rsa_1024_priv.pem -out rsa_1024_pub.pem` then past the contents of rsa_1024_pub.pem here.

- **quizaccess_wifiresilience | wifitoken**
  Web service token to send emergency files in background when the device is connected. This token can be generated from (Search "webservicetokens" / manage tokens) in site administration pages.

- **quizaccess_wifiresilience | prechecks**
  This option will display the technical details of the browser before commencing the exam. Checks are for service workers, local storage, request of increase of local storage and others.

- **quizaccess_wifiresilience | techerrors**
  This feature is useful to understand the underlying technical reason for the failure of exam submission. It only shows the error at the bottom of the submission page if there is any.

- **quizaccess_wifiresilience | navdetails**
  Show server and device status last time the data was saved (locally and on server). It also shows online status details (whether device/server is connected or not). It also displays a link for emergency file to be downloaded.

- **quizaccess_wifiresilience | watchxhr**
  One URL per line. Some question types require live checks or uploads with the server. If they are added here, the plugin will be able to count the time-offline for the user, and then add it automatically to the total extra time they get so they finish the exam with precise grace time or time limit.

- **quizaccess_wifiresilience | fetchandlog**
  One per line. If you want to cache attachements (of any static type; i.e. docx, pdf, xls, zip, html etc) that are embedded in some questions as extra resource or for further readings, then add the full URLs you want them to be cached. Example: https://example.org/instructions.docx will cache instructions.docx that is embedded in the question(s). Usually attachements in exams have "pluginfile.php" inside the link. Please note that for cross-origin (remote domains or different subdomains, please make sure both are served via https, and also those domains allow CORS. Important: This option can defeat security principles of cross domain communications, so please use with care, or better only use locally hosted files on same domain).

- **quizaccess_wifiresilience | precachefiles**
  Only static files. One link per line. If you would like the service worker to pre-cache specific files (only static file; ie. css, jpg, html etc), please add the direct link for them, one per line. Note: The precached URLs will automatically be served using a cache-first strategy.

- **quizaccess_wifiresilience | excludelist**
  Link/file per line. Exclude specific files/links from caching with service worker. This is useful when you want some files or links to be in NetworkOnly (never get cached) mode.

- **quizaccess_wifiresilience | extraroutes**
  Add extra routes to exam service worker. If you want exam service worker [Wifiresilience-SW] to catch extra routes (file extensions, web addresses, etc) and treat them differently when the connection drops.


##  Todo:

- GitHub Actions integration
- ...



##  Credits:

Quiz fault-tolerant mode from Tim Hunt: https://github.com/timhunt/moodle-quizaccess_offlinemode



##  Third-party libraries used:

1. The Stanford Javascript Crypto Library from https://github.com/bitwiseshiftleft/sjcl
1. JSEncrypt https://github.com/travist/jsencrypt



##  Contributors:

ETH Zürich (Lead maintainer)
Antonia Bonaccorso (Service owner, antonia.bonaccorso@id.ethz.ch)
