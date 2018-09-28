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
 * @copyright 2017 ETH Zurich (amr.hourani@let.ethz.ch, kristina.isacson@let.ethz.ch)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['answerchanged'] = 'Antwort geändert';
$string['backtothequiz'] = 'Zurück zum Test';
$string['changesmadereallygoaway'] = 'Ihre Antworten wurden nicht auf dem Server gespeichert. Möchten Sie diesen Versuch wirklich abbrechen?';
$string['dataprocessedsuccessfully'] = 'Daten wurden erfolgreich bearbeitet ({$a}).';
$string['inspectionprocessedsuccessfully'] = 'Daten wurden erfolgreich bearbeitet.';
$string['description'] = 'Dieser Test ist im <strong>Wifi Resilience</strong> Modus.';
$string['uploadresponsesadmin'] = 'Adminstratoren können: ';
$string['or'] = 'oder';
$string['descriptionlink'] = 'Antwort-Dateien hochladen';
$string['finishattemptsafterupload'] = 'Versuch automatisch beenden und abgeben, nachdem die Antwortdatei erfolgreich hochgeladen wurde?';
$string['uploadfinishtime'] = 'Versuchs-/Abgabezeit';
$string['lastsaved'] = 'Zuletzt gespeichert: {$a}';
$string['lastsavedtotheserver'] = 'Zuletzt auf dem Server gespeichert: {$a}';
$string['lastsavedtothiscomputer'] = 'Zuletzt auf diesem Computer gespeichert: {$a}';
$string['loggedinaswronguser'] = 'Sie haben sich mit einem anderen Benutzer angemeldet als dem Benutzer, mit dem der Test bearbeitet wurde. Dies wird nicht funktionieren. Klicken Sie auf Weiterfahren, um nochmals mit dem richtigen Benutzer einzuloggen.';
$string['logindialogueheader'] = 'Möglicherweise müssen Sie sich nochmals anmelden';
$string['loginokagain'] = 'Ihr Login ist nun in Ordnung.';
$string['wifiresilienceenabled'] = 'Wifi Resilience Modus';
$string['wifiresilienceenabled_desc'] = 'Konfigurieren Sie, ob der Wifi Resilience Modus standardmässig für neue Tests aktiviert werden soll oder nicht.';
$string['wifiresilienceenabled_help'] = 'Das Ziel dieser Option ist es, dass Studierende an einem Test weiterarbeiten können, auch wenn die Netzverbindung unterbrochen wird. Studierende können weiterhin zwischen den Seiten des Tests navigieren. Alle Antworten werden lokal gespeichert und an den Server gesendet, sobald die Verbindung wieder vorhanden ist.';
$string['wifiresilience:uploadresponses'] = 'Antwort-Dateien hochladen';
$string['wifiresilience:adminmessages'] = 'Administrationsmeldungen ansehen';
$string['wifiresilience:browserchecks'] = 'Browserüberprüfung ansehen';
$string['wifiresilience:inspectresponses'] = 'Antworten überprüfen';
$string['wifiresilience:localresponses'] = 'Lokale Antworten überprüfen (Lokale Sicherung)';
$string['wifiresilience:viewtechchecks'] = 'Technische Überprüfung ansehen (auch wenn diese Option in den Testeinstellungen deaktiviert ist)';
$string['wifiresilience:viewlivedevices'] = 'Live-Geräte anzeigen';
$string['livedevices'] = 'Live-Geräte';
$string['lastseen'] = 'Zuletzt gesehen';
$string['lastsync'] = 'Zuletzt synchronisiert';
$string['status'] = 'Status';
$string['currentissue'] = 'Aktuelles Issue';
$string['pluginname'] = 'Quiz Wifi Resilience Modus';
$string['privatekey'] = 'Privater Verschlüsselungsschlüssel';
$string['privatekey_desc'] = 'Sie können Verschlüsselung mit öffentlichen Schlüsseln verwenden um die Antwort-Dateien zu schützen. Dafür brauchen Sie einen öffentlichen und einen privaten Schlüssel. Installieren Sie OpenSSL (https://www.openssl.org/) und verwenden Sie <code>openssl genrsa -out rsa_1024_priv.pem 1024</code> in der Command Shell um einen privaten Schlüssel zu erstellen. Danach fügen Sie den Inhalt der rsa_1024_priv.pem Datei in diese Box ein.';
$string['processingcomplete'] = 'Verarbeitung abgeschlossen';
$string['processingfile'] = 'Datei wird verarbeitet {$a}';
$string['decryptingcomplete'] = 'Datei entschlüsseln abgeschlossen';
$string['decryptingfile'] = 'Datei entschlüsseln {$a}';
$string['inspectingfile'] = 'Datei prüfen {$a}';
$string['inspectingfiledesc'] = 'Hier können Sie Notfall-Dateien entschlüsseln und wieder verschlüsseln. Benutzen Sie dieses Tool mit Vorsicht. Mit dem Inspektions-Tool können Prüfungsadministratoren Testversuche bearbeiten. Bearbeitet werden kann unter anderem die Kurs ID, die Test ID, die Abgabezeit etc. ';
$string['inspect'] = 'Antwort-Dateien prüfen';
$string['publickey'] = 'Öffentlicher Verschlüsselungsschlüssel';
$string['publickey_desc'] = 'Dies muss dem privaten Schlüssel entsprechen. Der öffentliche Schlüssel kann aus dem privaten Schlüssel generiert werden mittels <code>openssl rsa -pubout -in rsa_1024_priv.pem -out rsa_1024_pub.pem</code> kopieren Sie anschliessend den Inhalt von  rsa_1024_pub.pem und fügen ihn hier ein.';
$string['responsefiles'] = 'Antwort-Dateien';
$string['responsefiles_help'] = 'Durch Klicken auf das blinkende Icon für den Wireless Network Verbindungsstatus wird während eines Testversuchs eine Antwortdatei herunter geladen. Durch mehrmaliges Anklicken des Icons können unterschiedliche Versionen der Antwortdatei herunter geladen werden. Der Dateiname ist zusammengesetzt aus dem Präfix ETHz, Datums- und Zeitangabe und der Endung .eth (...). (Beispiel: ETHz-crs229-cm643-id558-u8-a25197-d201803010842.eth). Wenn nicht anders konfiguriert, wird die Datei im "Downloads"-Ordner gespeichert.';
$string['reviewthisattempt'] = 'Versuch erneut ansehen';
$string['savefailed'] = 'Hinweis: Von Zeit zu Zeit sollten Sie:';
$string['savetheresponses'] = 'Kopie der Antworten herunterladen'; //Save the responses
$string['savingdots'] = 'Auf dem Server speichern...';
$string['savingtryagaindots'] = 'Erneuter Versuch auf dem Server zu speichern ...';
$string['submitfailed'] = 'Abgabe des Tests ist fehlgeschlagen';
$string['submitfaileddownloadmessage'] = '<br /><strong>Oder</strong><br />{$a}<br />(Hinweis: Keine Daten sind verloren gegangen. Melden Sie sich bei der Prüfungsaufsicht, damit Ihre heruntergeladene Resultate-Datei gesichert und auf den Moodle Server hochgeladen werden kann.)';
$string['submitfailedmessage'] = 'Ihre Antworten konnten nicht abgegeben werden. Sie können versuchen zu:';
$string['submitting'] = '<h3>Abgabe.. Bitte warten..</h3>';
$string['submitallandfinishtryagain'] = 'Alles abgeben und (erneut) beenden';
$string['uploadfailed'] = 'Das Hochladen ist fehlgeschlagen';
$string['downloadedecryptedfile'] = 'Entschlüsselte Datei herunterladen';
$string['testencryption'] = 'Geräte- und Serververschlüsselung testen';
$string['uploadingresponsesfor'] = 'Antworten hochladen für {$a}';
$string['uploadmoreresponses'] = 'Weitere Antworten hochladen';
$string['uploadresponses'] = 'Antwort-Dateien hochladen';
$string['uploadresponsesfor'] = 'Antwort-Dateien hochladen für {$a}';
$string['uploadinspection'] = 'Antworten überprüfen';
$string['uploadinspectionfor'] = 'Antworten überprüfen für {$a}';
$string['localresponsesfor'] = 'Antwort-Dateien gespeichert auf <strong>this</strong> der lokalen Maschine für {$a}';
$string['loadlocalresponses'] = 'Antwort-Dateien prüfen, runterladen, speichern oder löschen, die auf <strong>diesem</strong> Computer gespeichert sind';
$string['takeattemptfromjson'] = 'Unverschlüsselte Versuch-ID verwenden';
$string['takeattemptfromjson_help'] = '***Sorgfältig lesen*** Falls ein Versuch bereits zerstört ist oder falls Sie einen neuen Versuch erstellen wollen um damit weiterzufahren und Sie Fragenfolgen Integritätsprobleme vermeiden wollen, verwenden Sie diese Möglichkeit mit äusserster Vorsicht! Bitte beachten Sie, dass die Versuch-ID einer tatsächlichen Versuch-ID entsprechen muss, welche zuvor erstellt worden ist oder gerade als Administrator (eingeloggt als Student) erstellt wurde,  um ein Versuchen zu erstellen, auf dem man aufbauen kann.  Damit man eine Antwort-Datei eines Studierenden hochlanden kann, eine sogenannte Notfall-Datei, brauchen wir einen validen Versuch.<br /><br /><font color="red">Wählen Sie diese Option nur aus, wenn Sie sich im Klaren sind, wie Versuche verwaltet werden!</font>';
$string['dangeryes'] = 'Ja (<font color="red">!!Gefahr!!</font>)';
$string['prechecks'] = 'Technische Überprüfungen anzeigen';
$string['prechecks_help'] = 'Diese Option zeigt die technischen Details des Browsers vor Beginn des Tests. Angezeigt werden Überprüfungen für Service Workers, lokaler Speicher, Anfragen zur Erhöhung des lokalen Speichers etc..';
$string['techerrors'] = 'Technische Fehler anzeigen';
$string['techerrors_help'] = 'Dieses Option hilft das Fehlschlagen einer Testabgabe zu verstehen. Fehler, falls vorhanden, werden am Ende der Abgabeseite angezeigt.';
$string['navdetails'] = 'Server & Geräte Status anzeigen';
$string['navdetails_help'] = 'Server & Geräte Status anzeigen zum letzten gespeicherten Zeitpunkt (lokal und auf dem Server). Ebenso werden online Status Details angezeigt, z.B. ob die Verbindung zur Maschine/zum Server vorhanden ist oder nicht. Auch wird ein Link zum Runterladen der Notfall-Datei angezeigt.';
$string['watchxhr'] = 'Live-Events ansehen';
$string['technicalchecks'] = 'Speicher Überprüfungen für den aktuellen Browser';
$string['watchxhr_help'] = 'Pro Reihe eine URL eintragen. Gewisse Fragetypen erfordern Live-Überprüfungen oder Hochladen auf den Server. Falls diese hier hinzugefügt werden, kann das Plugin die Offline-Zeit des Benutzers berechnen und dann automtisch zu der totalen zusätzlichen Zeit hinzufügen, so dass der Benutzer zur exakten Zeitlimite den Test beendet.';
$string['fromfile'] = 'Abgabezeit der Antwort-Datei';
$string['syncedfiles'] = 'Antwort-Dateien im Hintergrund synchronisieren';
$string['fetchandlog'] = 'Eingebettete Dateien cachen (Anhänge)';
$string['fetchandlog_help'] = 'Eine per Reihe. Falls Anhänge gecached werden sollen (alle statischen Formate; z.B. docx, pdf, xls, zip, html etc), welche in gewissen Fragen als eine zusätzliche Ressource oder zur weiterführenden Lektüre eingebettet sind, dann fügen Sie komplette URLs hinzu, welche gechached werden sollen. Z.B.: https://example.org/instructions.docx wird die Datei instructions.docx cachen, welche in der Frage/den Fragen eingebettet ist. Normalerweise haben Anhänge in Tests "pluginfile.php" innerhalb des Links. Bitte beachten Sie dies für Cross-Origin (stellen Sie bitte sicher, dass Remote Domains oder unterschiedliche Subdomains https verwenden und dass diese Domains auch CORS erlauben. Wichtig: Diese Option kann Sicherheitsprinzipien für Domainübergreifende Kommunikation aushebeln, seien Sie deshalb vorsichtig bei der Verwendung oder verwenden Sie besser lokal gehostete Dateien in der gleichen Domain).';
$string['now'] = 'Jetzt';
$string['reference'] = 'Referenz';
$string['filetype'] = 'Typ';
$string['wifitoken'] = 'Background Sync Token';
$string['serviceworkermgmt'] = 'Service Worker Management';
$string['resetserviceworker'] = 'Service Worker zurücksetzen';
$string['refreshserviceworker'] = 'Service Worker neu laden';
$string['stopserviceworker'] = 'Service Worker anhalten';
$string['syncserviceworker'] = 'Background Sync auslösen';

$string['loadingstep1'] = 'Aufsetzen von {$a}';
$string['loadingstep2'] = 'Die Prüfungs-Struktur wird vorbereitet...';
$string['loadingstep3'] = 'Die Service Worker statische, sowie dynamische Routes werden vorbereitet...';
$string['loadingstep4'] = 'Die Prüfungsdatenbank wird vorbereitet...';
$string['loadingstep5'] = 'Die Prüfungsfragen werden vorbereitet...';
$string['loadingstep6'] = 'Die Prüfungsnavigation wird vorbereitet...';
$string['loadingstep7'] = 'Die Prüfungsdaten werden verschlüsselt...';
$string['loadingstep8'] = 'Verifizierung des Netzwerk Status...';
$string['loadingstep9'] = 'Live-Network-Anfragen verfolgen...';
$string['loadingstep10'] = 'Starten der Prüfung...';

$string['wifitoken_help'] = 'Web Service Token um Notfall-Dateien im Hintergrund zu senden, während die Netzwerkverbindung vorhanden ist. Dieser Token kann in der Website-Administration (webservicetokens/ Manage Tokens) erstellt werden.';
$string['precachefiles'] = 'Precache-Dateien';
$string['precachefiles_help'] = 'Falls Sie wollen, dass der Service Worker spezifische Dateien precached (nur statische Dateien  wie css, jpg, html etc), geben Sie den direkten Link für diese ein. Pro Zeile einen Link. Hinweis: Die precached URLs werden automatisch gemäss einer cache-first Strategie geliefert.';
$string['excludelist'] = 'Dateien ausschliessen';
$string['excludelist_help'] = 'Einen Link/eine Datei pro Zeile eingeben, um auszuschliessen, dass der Service Worker spezifische Links oder Dateien cached. Dies ist nützlich, wenn gewünscht wird, dass auf einzelne Dateien oder Links nur via Netzwerk und nie aus dem Cache geladen werden soll.';
$string['quizfinishtime'] = 'Quiz Zeitbegrenzung der Prüfung (max Prüfungszeit erlaubt)';
$string['usefinalsubmissiontime'] = 'Endgültige Einreichungszeit aus der Antwort-Datei verwenden (falls vorhanden)';
$string['usefinalsubmissiontime_help'] = 'Sobald ein Benutzer seinen Versuch beendet und abgibt (oder wenn der Versuch automtisch abgegeben wird), wird ein Parameter namens "final_submission_time" zur Datei hinzugefügt: Dieser Parameter enthält die Abgabe-Zeit. Falls keine endgültige Abgabezeit vorhanden ist, ist der Wert für "final_submission_time" 0. Falls 0, ignoriert das Skript diesen Paramater und eine der untenstehenden Optionen wird verwendet.';
$string['countrealofflinetime'] = 'Offline-Zeit abziehen (falls vorhanden)';
$string['countrealofflinetime_help'] = 'Offline-Zeit von der Versuchszeit abziehen basierend auf der "richtigen Offline-Zeit". Von der Versuchszeit wird die Offline-Zeit abgezogen. Die Offline-Zeit ist die Zeit, in der der Benutzer eine spezifische Frage nicht bearbeiten konnte, weil keine Internetverbindung vorhanden war.<br><br>Wichtig: Dieser Wert wird bereits in der endgültigen Einreichungszeit berechnet. Diese Option funktioniert nur mit Versuchen, bei denen NICHT "beendet" ausgewählt worden ist.';
$string['extraroutes'] = 'Extra Routes';
$string['extraroutes_help'] = 'Extra Routes zum Exam Service Worker hinzufügen. Falls Sie wollen, dass der Exam Service Worker [ETHz-SW] extra Routes (Dateierweiterungen, Web Adressen, etc) cachen und anders behandeln soll, wenn die Verbindung unterbrochen wird, können Sie z.B. hinzufügen:<br>
<code>
// We want no more than 1000 files with extension "XYZ" in the cache.<br />
// We check using a networkFirst Strategy.<br />
global.toolbox.router.get(/\.(?:XYZ)$/, global.toolbox.<strong>networkFirst</strong>, {<br />
&nbsp;&nbsp;&nbsp;cache: {<br />
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;name: \'ETHz-exams-<strong>XYZ</strong>\', // Local Database Name (indexedDb).<br />
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;maxEntries: 1000, // Max number of files to save.<br />
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;maxAgeSeconds: 86400 // Max time in seconds before they get deleted/expired.<br />
&nbsp;&nbsp;&nbsp;},<br />
&nbsp;&nbsp;&nbsp;origin: /\.googleapis\.com$/ //Optional, if files located outside moodle domain.<br />
});<br />
</code>
<h2 id="options"><a class="anchorjs-link " href="#options" aria-label="Anchor link for: options" style="font-style: normal; font-variant: normal; font-weight: normal; font-stretch: normal; font-size: 1em; line-height: inherit; font-family: anchorjs-icons; position: absolute; margin-left: -1em; padding-right: 0.5em;"></a>Routes Optionen</h2>

<p>Alle Optionen können global via Eigenschaften von <code class="highlighter-rouge">toolbox.options</code> spezifiziert werden.
Individuelle Optionen können auf einer Handler Basis mittels <code class="highlighter-rouge">Object</code> konfiguriert werden und als dritter Parameter zu
 <code class="highlighter-rouge">toolbox.router</code> Methoden übergeben werden.</p>

<h3 id="debug-boolean"><a class="anchorjs-link " href="#debug-boolean" aria-label="Anchor link for: debug boolean"  style="font-style: normal; font-variant: normal; font-weight: normal; font-stretch: normal; font-size: 1em; line-height: inherit; font-family: anchorjs-icons; position: absolute; margin-left: -1em; padding-right: 0.5em;"></a>debug [Boolean]</h3>
<p>Bestimmt, ob zusätzliche Informationen in der Browserkonsole geloggt werden.</p>

<p><em>Standard</em>: <code class="highlighter-rouge">false</code></p>

<h3 id="networktimeoutseconds-number"><a class="anchorjs-link " href="#networktimeoutseconds-number" aria-label="Anchor link for: networktimeoutseconds number"  style="font-style: normal; font-variant: normal; font-weight: normal; font-stretch: normal; font-size: 1em; line-height: inherit; font-family: anchorjs-icons; position: absolute; margin-left: -1em; padding-right: 0.5em;"></a>networkTimeoutSeconds [Number]</h3>
<p>Ein Timeout, welcher im <code class="highlighter-rouge">toolbox.networkFirst</code> integrierten Handler verwendet wird.
Falls <code class="highlighter-rouge">networkTimeoutSeconds</code> gesetzt ist, wird jede Netzwerkanfrage, die länger als die konfigurierte Zeit dauert, automatisch auf die gecachte Antwort zurückgreifen, falls eine existiert. Falls
<code class="highlighter-rouge">networkTimeoutSeconds</code> nicht gesetzt ist, kommt die eingebaute Netzwerk-Timeout Logik des Browsers zur Anwendung.</p>

<p><em>Standard</em>: <code class="highlighter-rouge">null</code></p>

<h3 id="cache-object"><a class="anchorjs-link " href="#cache-object" aria-label="Anchor link for: cache object" style="font-style: normal; font-variant: normal; font-weight: normal; font-stretch: normal; font-size: 1em; line-height: inherit; font-family: anchorjs-icons; position: absolute; margin-left: -1em; padding-right: 0.5em;"></a>cache [Object]</h3>
<p>Verschiedene Eigenschaften von <code class="highlighter-rouge">cache</code> kontrollieren das Verhalten des Standardcache, oder den Cache, der von einem spezifischen Request Handler verwendet wird, sofern sie via
<code class="highlighter-rouge">toolbox.options.cache</code> gesetzt wurden.</p>

<h3 id="cachename-string"><a class="anchorjs-link " href="#cachename-string" aria-label="Anchor link for: cachename string"  style="font-style: normal; font-variant: normal; font-weight: normal; font-stretch: normal; font-size: 1em; line-height: inherit; font-family: anchorjs-icons; position: absolute; margin-left: -1em; padding-right: 0.5em;"></a>cache.name [String]</h3>
<p>Der Name von <a href="https://developer.mozilla.org/en-US/docs/Web/API/Cache" target="_blank"><code class="highlighter-rouge">Cache</code></a>,
 der verwendet wird <a href="https://fetch.spec.whatwg.org/#response-class" target="_blank"><code class="highlighter-rouge">Antwort</code></a> um Objekte zu speichern. Die Verwendung eines eindeutigen Namens erlaubt es Ihnen, die maximale Grösse und das Alter der Einträge im Cache anzupassen.</p>

<p><em>Standard</em>: wird basierend auf der Laufzeit des Service Worker <code class="highlighter-rouge">registration.scope</code> Wertes erstellt.</p>

<h3 id="cachemaxentries-number"><a class="anchorjs-link " href="#cachemaxentries-number" aria-label="Anchor link for: cachemaxentries number"  style="font-style: normal; font-variant: normal; font-weight: normal; font-stretch: normal; font-size: 1em; line-height: inherit; font-family: anchorjs-icons; position: absolute; margin-left: -1em; padding-right: 0.5em;"></a>cache.maxEntries [Number]</h3>
<p>Legt zum Verfallsdatum des Cache eine zuletzt-verwendet-Richtlinie fest für Einträge, die über die verschiedenen integrierten Handler gecached wurden.
Sie können dies mit einem Cache verwenden, welcher dafür vorgesehen ist,
Einträge für ein dynamisches Set von Ressourcen ohne natürliche Limite zu speichern. Setzen Sie für <code class="highlighter-rouge">cache.maxEntries</code> z.B.,
<code class="highlighter-rouge">10</code> ein, bedeutet das, dass nachdem der elfte Eintrag gecached wurde, der älteste verwendete Eintrag automatisch gelöscht wird.
Der Cache wird nie mehr Einträge als <code class="highlighter-rouge">cache.maxEntries</code> umfassen.
Diese Option wird nur wirksam, wenn <code class="highlighter-rouge">cache.name</code> ebenfalls konfiguriert ist.
Sie kann alleine oder in Verbindung mit <code class="highlighter-rouge">cache.maxAgeSeconds</code> verwendet werden.</p>

<p><em>Standard</em>: <code class="highlighter-rouge">null</code></p>

<h3 id="cachemaxageseconds-number"><a class="anchorjs-link " href="#cachemaxageseconds-number" aria-label="Anchor link for: cachemaxageseconds number"  style="font-style: normal; font-variant: normal; font-weight: normal; font-stretch: normal; font-size: 1em; line-height: inherit; font-family: anchorjs-icons; position: absolute; margin-left: -1em; padding-right: 0.5em;"></a>cache.maxAgeSeconds [Number]</h3>
<p>Legt ein maximales Alter für Cache Einträge in Sekunden fest.
Sie können dies mit einem Cache verwenden, welcher dafür vorgesehen ist,
Einträge für ein dynamisches Set von Ressourcen ohne natürliche Limite zu speichern. Setzen Sie für <code class="highlighter-rouge">cache.maxAgeSeconds</code> z.B. <code class="highlighter-rouge">60 * 60 * 24</code> ein, bedeutet das, dass
Einträge älter als einen Tag automatisch gelöscht werden.
Diese Option wird nur wirksam, wenn <code class="highlighter-rouge">cache.name</code> ebenfalls konfiguriert ist.
Sie kann alleine oder in Verbindung mit  <code class="highlighter-rouge">cache.maxEntries</code> verwendet werden.</p>

<p><em>Standard</em>: <code class="highlighter-rouge">null</code></p>

<h2 id="handlers"><a class="anchorjs-link " href="#handlers" aria-label="Anchor link for: handlers"  style="font-style: normal; font-variant: normal; font-weight: normal; font-stretch: normal; font-size: 1em; line-height: inherit; font-family: anchorjs-icons; position: absolute; margin-left: -1em; padding-right: 0.5em;"></a>Handlers</h2>

<p> Es gibt fünf integrierte Handler, um die gebräuchlichsten Netzwerk-Strategien abzudecken. Mehr Informationen über offline Strategien finden Sie im <a href="http://jakearchibald.com/2014/offline-cookbook/">Offline-Cookbook</a>.</p>

<h3 id="toolboxnetworkfirst"><a class="anchorjs-link " href="#toolboxnetworkfirst" aria-label="Anchor link for: toolboxnetworkfirst"  style="font-style: normal; font-variant: normal; font-weight: normal; font-stretch: normal; font-size: 1em; line-height: inherit; font-family: anchorjs-icons; position: absolute; margin-left: -1em; padding-right: 0.5em;"></a><code class="highlighter-rouge">toolbox.networkFirst</code></h3>
<p>Der Handler versucht, die Anfrage über Abruf aus dem Netzwerk zu bearbeiten. Gelingt dies, wird die Antwort im Cache gespeichert. Ansonsten wird vom Cache geladen. Dies ist die Strategie, die für das grundlegende Read-Through-Caching verwendet wird. Sie ist auch gut für API Anfragen, bei denen man immer die neuesten Daten möchte, aber falls diese nicht vorhanden sind, greift man lieber auf veraltete Daten zurück als auf gar keine.</p>

<h3 id="toolboxcachefirst"><a class="anchorjs-link " href="#toolboxcachefirst" aria-label="Anchor link for: toolboxcachefirst"  style="font-style: normal; font-variant: normal; font-weight: normal; font-stretch: normal; font-size: 1em; line-height: inherit; font-family: anchorjs-icons; position: absolute; margin-left: -1em; padding-right: 0.5em;"></a><code class="highlighter-rouge">toolbox.cacheFirst</code></h3>
<p>Wenn die Anfrage mit einem Cache-Eintrag übereinstimmt, antwortet dieser Handler damit. Ansonsten wird versucht, die Ressource via Netzwerkverbindung zu holen. Gelingt die Anfrage via Netzwerk, wird der Cache aktualisiert. Diese Option ist gut für Ressourcen, die sich nicht verändern oder andere Aktualisierungsmechanismen haben.</p>

<h3 id="toolboxfastest"><a class="anchorjs-link " href="#toolboxfastest" aria-label="Anchor link for: toolboxfastest"  style="font-style: normal; font-variant: normal; font-weight: normal; font-stretch: normal; font-size: 1em; line-height: inherit; font-family: anchorjs-icons; position: absolute; margin-left: -1em; padding-right: 0.5em;"></a><code class="highlighter-rouge">toolbox.fastest</code></h3>
<p>Dieser Handler fragt die Ressource parallel sowohl via Cache als auch via Netzwerk an. Es wird mit dem reagiert, was zuerst zurückkommt. Meistens ist dies die gecachte Version, falls eine vorhanden ist. Einerseits wird mittels dieser Strategie immer eine Netzwerk-Anfrage gemacht, auch wenn die Ressource im Cache vorhanden ist, andererseits wird der Cache aktualisiert, falls bzw. sobald die Netzwerk-Anfrage beantwortet wird. Somit sind zukünftige Zugriffe auf Versionen im Cache aktueller.</p>

<h3 id="toolboxcacheonly"><a class="anchorjs-link " href="#toolboxcacheonly" aria-label="Anchor link for: toolboxcacheonly"  style="font-style: normal; font-variant: normal; font-weight: normal; font-stretch: normal; font-size: 1em; line-height: inherit; font-family: anchorjs-icons; position: absolute; margin-left: -1em; padding-right: 0.5em;"></a><code class="highlighter-rouge">toolbox.cacheOnly</code></h3>
<p> Dieser Handler beantwortet die Anfrage via Cache oder misslingt. Diese Option eignet sich, wenn garantiert werden soll, dass keine Netzwerk-Anfrage gemacht wird, z.B um auf einem Gerät Akku zu sparen.</p>

<h3 id="toolboxnetworkonly"><a class="anchorjs-link " href="#toolboxnetworkonly" aria-label="Anchor link for: toolboxnetworkonly"  style="font-style: normal; font-variant: normal; font-weight: normal; font-stretch: normal; font-size: 1em; line-height: inherit; font-family: anchorjs-icons; position: absolute; margin-left: -1em; padding-right: 0.5em;"></a><code class="highlighter-rouge">toolbox.networkOnly</code></h3>
<p>Dieser Handler versucht die Anfrage zu beantworten, indem die URL aus dem Netzwerk geholt wird. Falls dies nicht gelingt, misslingt auch die Anfrage. Dies ist im Grunde das Gleiche wie keine Route für die URL zu kreieren.</p>

<h2 id="expressive-approach"><a class="anchorjs-link " href="#expressive-approach" aria-label="Anchor link for: expressive approach"  style="font-style: normal; font-variant: normal; font-weight: normal; font-stretch: normal; font-size: 1em; line-height: inherit; font-family: anchorjs-icons; position: absolute; margin-left: -1em; padding-right: 0.5em;"></a>Methoden</h2>

<h3 id="toolboxroutergetpostputdeleteheadurlpattern-handler-options"><a class="anchorjs-link " href="#toolboxroutergetpostputdeleteheadurlpattern-handler-options" aria-label="Anchor link for: toolboxroutergetpostputdeleteheadurlpattern handler options"  style="font-style: normal; font-variant: normal; font-weight: normal; font-stretch: normal; font-size: 1em; line-height: inherit; font-family: anchorjs-icons; position: absolute; margin-left: -1em; padding-right: 0.5em;"></a><code class="highlighter-rouge">toolbox.router.&lt;get|post|put|delete|head&gt;(urlPattern, handler, options)</code></h3>

<p>Dieser Handler erstellt eine Route, welche Anfragen für übereinstimmende URLs <code class="highlighter-rouge">urlPattern</code> durch den Aufruf von <code class="highlighter-rouge">handler</code> bewirkt. Vergleicht die Anfragen über die Methoden GET, POST, PUT, DELETE oder HEAD HTTP.</p>

<ul>
  <li><code class="highlighter-rouge">urlPattern</code> - ist eine Express Style Route. Siehe <a href="https://github.com/pillarjs/path-to-regexp"  target="_blank">path-to-regexp</a> Modul für die gesamte Syntax</li>
  <li><code class="highlighter-rouge">handler</code> - ist ein Request Handler, wie <a href="#handlers">weiter oben beschrieben</a></li>
  <li><code class="highlighter-rouge">options</code> - ist ein Objekt, welches Optionen für eine Route enthält. Diese Optionen werden dem Request Handler übergeben. Die <code class="highlighter-rouge">origin</code> Option ist spezifisch für die Router Methoden und kann entweder ein exakter String oder eine Regexp sein, gegen die der Ursprung der Anfrage für die zu verwendende Route übereinstimmen muss.</li>
</ul>

<h3 id="toolboxrouteranyurlpattern-handler-options"><a class="anchorjs-link " href="#toolboxrouteranyurlpattern-handler-options" aria-label="Anchor link for: toolboxrouteranyurlpattern handler options"  style="font-style: normal; font-variant: normal; font-weight: normal; font-stretch: normal; font-size: 1em; line-height: inherit; font-family: anchorjs-icons; position: absolute; margin-left: -1em; padding-right: 0.5em;"></a><code class="highlighter-rouge">toolbox.router.any(urlPattern, handler, options)</code></h3>
<p>Diese Methode ist wie <code class="highlighter-rouge">toolbox.router.get</code> etc., aber passend für jede HTTP Methode.</p>

<h3 id="toolboxrouterdefault"><a class="anchorjs-link " href="#toolboxrouterdefault" aria-label="Anchor link for: toolboxrouterdefault"  style="font-style: normal; font-variant: normal; font-weight: normal; font-stretch: normal; font-size: 1em; line-height: inherit; font-family: anchorjs-icons; position: absolute; margin-left: -1em; padding-right: 0.5em;"></a><code class="highlighter-rouge">toolbox.router.default</code></h3>
<p>Diese Methode nimmt eine Funktion als Request-Handler für jede GET-Anfrage, die nicht zu einer Route passt.</p>

<h3 id="toolboxprecachearrayofurls"><a class="anchorjs-link " href="#toolboxprecachearrayofurls" aria-label="Anchor link for: toolboxprecachearrayofurls"  style="font-style: normal; font-variant: normal; font-weight: normal; font-stretch: normal; font-size: 1em; line-height: inherit; font-family: anchorjs-icons; position: absolute; margin-left: -1em; padding-right: 0.5em;"></a><code class="highlighter-rouge">toolbox.precache(arrayOfURLs)</code></h3>
<p>Diese Methode fügt jede URL in arrayOfURLs zur Liste der Ressourcen hinzu, welche während der Service Worker Installation gecached werden sollen. Hinweis: Diese Funktion muss aufgerufen werden, bevor die Installation ausgelöst wird, deshalb sollte dies beim ersten Aufruf des Skriptes gemacht werden.</p>

<h3 id="toolboxcacheurl-options"><a class="anchorjs-link " href="#toolboxcacheurl-options" aria-label="Anchor link for: toolboxcacheurl options"  style="font-style: normal; font-variant: normal; font-weight: normal; font-stretch: normal; font-size: 1em; line-height: inherit; font-family: anchorjs-icons; position: absolute; margin-left: -1em; padding-right: 0.5em;"></a><code class="highlighter-rouge">toolbox.cache(url, options)</code></h3>
<p>Diese Methode bewirkt, dass die Ressource bei <code class="highlighter-rouge">url</code> dem Cache hinzugefügt wird und gibt ein Promise zurück, das mit Void aufgelöst wird. Der <code class="highlighter-rouge">options</code> Parameter unterstützt die <code class="highlighter-rouge">debug</code> und <code class="highlighter-rouge">cache</code> <a href="#options">globalen Optionen</a>.</p>

<h3 id="toolboxuncacheurl-options"><a class="anchorjs-link " href="#toolboxuncacheurl-options" aria-label="Anchor link for: toolboxuncacheurl options"  style="font-style: normal; font-variant: normal; font-weight: normal; font-stretch: normal; font-size: 1em; line-height: inherit; font-family: anchorjs-icons; position: absolute; margin-left: -1em; padding-right: 0.5em;"></a><code class="highlighter-rouge">toolbox.uncache(url, options)</code></h3>
<p>Diese Methode bewirkt, dass die Ressource bei der <code class="highlighter-rouge">url</code> vom Cache entfernt wird und gibt ein Promise zurück, das auf true gesetzt wird, wenn der Cache Eintrag gelöscht wird. Der <code class="highlighter-rouge">options</code> Parameter unterstützt die <code class="highlighter-rouge">debug</code> und <code class="highlighter-rouge">cache</code> <a href="#options">globalem Optionen</a>.</p>

';
