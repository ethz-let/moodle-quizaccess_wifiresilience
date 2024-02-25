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
$string['attempt'] = 'Versuch';
$string['backtothequiz'] = 'Zurück zum Test';
$string['changesmadereallygoaway'] = 'Ihre Antworten wurden nicht auf dem Server gespeichert. Möchten Sie diesen Versuch wirklich abbrechen?';
$string['createasnewattempt'] = 'Als neuen Versuch anlegen';
$string['createasnewattempt_help'] = '***Vorsicht*** Als neuen Versuch anlegen';
$string['createnewattempt'] = 'Neuen Versuch anlegen';
$string['currentissue'] = 'Aktuelles Problem';
$string['dangeryes'] = 'Ja';
$string['dataprocessedsuccessfully'] = 'Daten wurden erfolgreich bearbeitet ({$a}).';
$string['decryptingcomplete'] = 'Datei entschlüsseln abgeschlossen';
$string['decryptingfile'] = 'Datei entschlüsseln {$a}';
$string["delete"] = 'Löschen';
$string["download"] = 'Herunterladen';
$string["downloadfile"] = 'Als Datei herunterladen';
$string['description'] = 'Dieser Test ist im <strong>Wifi Resilience</strong> Modus.<br />
<p style="text-align:left">
Verwenden Sie den Wifi Resilience Modus nur, wenn
<ul style="text-align:left">
<li>alle Inhalte des Tests direkt in Moodle hochgeladen wurden, d.h. es gibt keine Links zu Dateien, Webseiten oder Bildern, die auf externe Server verweisen,
<li>die im Test eingesetzten Fragetypen keinen externen Server benötigen, z. B. Stack, Code Expert, Code Runner,
<li>der Test keine Videos enthält.
</ul>
<p style="text-align:left">Die Verwendung des Safe Exam Browsers wird dringend empfohlen.</p>
</p>';
$string['descriptionlink'] = 'Antwort-Dateien hochladen';
$string['downloadedecryptedfile'] = 'Entschlüsselte Datei herunterladen';
$string['excludelist'] = 'Dateien ausschliessen';
$string['excludelist_help'] = 'Einen Link/eine Datei pro Zeile eingeben, um auszuschliessen, dass der Service Worker spezifische Links oder Dateien cached. Dies ist nützlich, wenn gewünscht wird, dass auf einzelne Dateien oder Links nur via Netzwerk und nie aus dem Cache geladen werden soll.';
$string['extraroutes'] = 'Extra Routes';
$string['extraroutes_help'] = 'Extra Routes zum Exam Service Worker hinzufügen. Falls Sie wollen, dass der Exam Service Worker [Wifiresilience-SW] extra Routes (Dateierweiterungen, Web Adressen, etc) cachen und anders behandeln soll, wenn die Verbindung unterbrochen wird, können Sie z.B. hinzufügen:<br>
<code>
// We want no more than 1000 files with extension "XYZ" in the cache.<br />
// We check using a networkFirst Strategy.<br />
global.toolbox.router.get(/\.(?:XYZ)$/, global.toolbox.<strong>networkFirst</strong>, {<br />
&nbsp;&nbsp;&nbsp;cache: {<br />
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;name: \'Wifiresilience-exams-<strong>XYZ</strong>\', // Local Database Name (indexedDb).<br />
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
$string['fetchandlog'] = 'Eingebettete Dateien cachen (Anhänge)';
$string['fetchandlog_help'] = 'Eine per Reihe. Falls Anhänge gecached werden sollen (alle statischen Formate; z.B. docx, pdf, xls, zip, html etc), welche in gewissen Fragen als eine zusätzliche Ressource oder zur weiterführenden Lektüre eingebettet sind, dann fügen Sie komplette URLs hinzu, welche gechached werden sollen. Z.B.: https://example.org/instructions.docx wird die Datei instructions.docx cachen, welche in der Frage/den Fragen eingebettet ist. Normalerweise haben Anhänge in Tests "pluginfile.php" innerhalb des Links. Bitte beachten Sie dies für Cross-Origin (stellen Sie bitte sicher, dass Remote Domains oder unterschiedliche Subdomains https verwenden und dass diese Domains auch CORS erlauben. Wichtig: Diese Option kann Sicherheitsprinzipien für Domainübergreifende Kommunikation aushebeln, seien Sie deshalb vorsichtig bei der Verwendung oder verwenden Sie besser lokal gehostete Dateien in der gleichen Domain).';
$string['filearraystyle'] = '<br><h3>Array Ansicht</h3>';
$string['fileencryptedinitvaluenobase64'] = 'Der verschlüsselte Startwert ist nicht base-64 formatiert.';
$string['fileencryptedkeynobase64'] = 'Der verschlüsselte AES Schlüssel ist nicht base-64 formatiert.';
$string['fileinitvaluenobase64'] = 'Der Startwert ist nicht base-64 formatiert.';
$string['filejsondecode'] = 'JSON Data Dekodierung: {$a}';
$string['filejsondecodeerror'] = 'JSON Fehler: {$a}';
$string['filekeynobase64'] = 'Der AES Schlüssel ist nicht base-64 formatiert.';
$string['filenoattemptid'] = 'Die hochgeladenen Daten enthalten keine Attempt ID.';
$string['filenoattemptidupload'] = 'Diese Datei scheint keine verschlüsselte Attempt ID (attemptid) zu beinhalten. Sie haben ausgewählt die Attempt ID aus verschlüsselten Paramtern zu verwenden. Bitte wählen Sie diese Option ab und versuchen und starten Sie den Prozess neu.';
$string['filenodecryptionkey'] = 'Wie es scheint liegen verschlüsselte Antworten vor, aber es wurde kein Decryption-Key gefunden.';
$string['filenoresponses'] = 'Wie es scheint enthält diese Datei keine Antworten.';
$string['filenoturlencoded'] = 'Die Data Datei ist nicht URL-ENCODED. Daten werden verwendet, wie sie sind.';
$string['filetype'] = 'Typ';
$string['fileunabledecrypt'] = 'Die Antworten konnten nicht entschlüsselt werden: {$a}';
$string['fileunabledecryptkey'] = 'Der AES Schlüssel konnte nicht entschlüsselt werden: {$a}';
$string['fileurlencoded'] = 'Die Data Datei ist URL-ENCODED. Es werden URL-DECODED Daten verwendet.';
$string['filewithkeyandiv'] = '<br><h3>Original Ansicht (mit Schlüssel und IV)</h3>';
$string['filewithoutkeyandiv'] = '<br><h3>Original Ansicht (ohne Schlüssel oder IV) - zur Anzeige in anderen Moodle Instanzen oder falls Public/Private Keys beschädigt sind.</h3>';
$string['filewrongquiz'] = 'Die hochgeladenen Daten gehören nicht zu diesem Quiz.';
$string['finishattemptsafterupload'] = 'Versuch automatisch beenden und abgeben, nachdem die Antwortdatei erfolgreich hochgeladen wurde?';
$string['fromfile'] = 'Abgabezeit der Antwort-Datei';
$string['inspect'] = 'Antwort-Dateien prüfen';
$string['inspectingfile'] = 'Datei prüfen {$a}';
$string['inspectingfiledesc'] = 'Hier können Sie Notfall-Dateien entschlüsseln und wieder verschlüsseln. Benutzen Sie dieses Tool mit Vorsicht. Mit dem Inspektions-Tool können Prüfungsadministratoren Testversuche bearbeiten. Bearbeitet werden kann unter anderem die Kurs ID, die Test ID, die Abgabezeit etc. ';
$string['inspectionprocessedsuccessfully'] = 'Daten wurden erfolgreich bearbeitet.';
$string['lastsaved'] = 'Zuletzt gespeichert: {$a}';
$string['lastsavedtotheserver'] = 'Zuletzt auf dem Server gespeichert: {$a}';
$string['lastsavedtothiscomputer'] = 'Zuletzt auf diesem Computer gespeichert: {$a}';
$string['lastseen'] = 'Zuletzt gesehen';
$string['lastsync'] = 'Zuletzt synchronisiert';
$string['livedevices'] = 'Live-Geräte';
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
$string['loadlocalresponses'] = 'Antwort-Dateien prüfen, herunterladen, speichern oder löschen, die auf <strong>diesem</strong> Computer gespeichert sind';
$string["localconfirmdeletelocal"] = 'Sind Sie sicher, dass Sie den lokalen Eintrag der Antwort löschen möchten: {$a}?';
$string["localconfirmdeletestatus"] = 'Sind Sie sicher, dass Sie den Status Eintrag löschen möchten: {$a}?';
$string["localnorecordsfound"] = 'Es wurden keinen lokalen Einträge gefunden';
$string['localresponsesfor'] = 'Auf diesem Gerät gespeicherte Antwort-Dateien für {$a}';
$string['localstorage'] = '<br>Lokaler Speicher: ';
$string["localtableheaderattempts"] = '<h3>indexedDB / WebSQL (Attempts Status)</h3>';
$string["localtableheaderencryptedattempts"] = '<h3>indexedDB / WebSQL (Verschlüsselte Attempts)</h3>';
$string["localtableinfo"] = 'Ergebnisse sind LEDIGLICH für Schlüssel: {$a->startwithkey} (Test: <strong>{$a->name}</strong>)';
$string["localtabledelete"] = 'Löschen';
$string["localtabledownload"] = 'Download';
$string["localtablelastchangelocal"] = 'Letzte lokale Änderung';
$string["localtablelastsavedserver"] = 'Letzte Sicherung am Server';
$string["localtablerecord"] = 'Eintrag';
$string['loggedinaswronguser'] = 'Sie haben sich mit einem anderen Benutzer angemeldet als dem Benutzer, mit dem der Test bearbeitet wurde. Dies wird nicht funktionieren. Klicken Sie auf Weiterfahren, um nochmals mit dem richtigen Benutzer einzuloggen.';
$string['logindialogueheader'] = 'Möglicherweise müssen Sie sich nochmals anmelden';
$string['loginokagain'] = 'Ihr Login ist nun in Ordnung.';
$string['navdetails'] = 'Server & Geräte Status anzeigen';
$string['navdetails_help'] = 'Server & Geräte Status anzeigen zum letzten gespeicherten Zeitpunkt (lokal und auf dem Server). Ebenso werden online Status Details angezeigt, z.B. ob die Verbindung zur Maschine/zum Server vorhanden ist oder nicht. Auch wird ein Link zum Runterladen der Notfall-Datei angezeigt.';
$string['now'] = 'Jetzt';
$string['or'] = 'oder';
$string['pluginname'] = 'Quiz Wifi Resilience Modus';
$string['precachefiles'] = 'Precache-Dateien';
$string['precachefiles_help'] = 'Falls Sie wollen, dass der Service Worker spezifische Dateien precached (nur statische Dateien  wie css, jpg, html etc), geben Sie den direkten Link für diese ein. Pro Zeile einen Link. Hinweis: Die precached URLs werden automatisch gemäss einer cache-first Strategie geliefert.';
$string['prechecks'] = 'Technische Überprüfungen anzeigen';
$string['prechecks_help'] = 'Diese Option zeigt die technischen Details des Browsers vor Beginn des Tests. Angezeigt werden Überprüfungen für Service Workers, lokaler Speicher, Anfragen zur Erhöhung des lokalen Speichers etc..';
$string['privatekey'] = 'Privater Verschlüsselungsschlüssel';
$string['privatekey_desc'] = 'Sie können Verschlüsselung mit öffentlichen Schlüsseln verwenden um die Antwort-Dateien zu schützen. Dafür benötigen Sie einen öffentlichen und einen privaten Schlüssel. Installieren Sie OpenSSL (https://www.openssl.org/) und verwenden Sie den Befehl <code>openssl genrsa -out rsa_1024_priv.pem 1024</code> in der Command Shell um einen privaten Schlüssel zu erzeugen. Daraufhin fügen Sie den Inhalt der Datei rsa_1024_priv.pem in diese Box ein.';
$string['processingcomplete'] = 'Verarbeitung abgeschlossen';
$string['processingfile'] = 'Datei wird verarbeitet {$a}';
$string['publickey'] = 'Öffentlicher Verschlüsselungsschlüssel';
$string['publickey_desc'] = 'Dies muss dem privaten Schlüssel entsprechen. Der öffentliche Schlüssel kann aus dem privaten Schlüssel mittels <code>openssl rsa -pubout -in rsa_1024_priv.pem -out rsa_1024_pub.pem</code> generiert werden. Kopieren Sie anschliessend den Inhalt der Datei rsa_1024_pub.pem und um ihn hier einzufügen.';
$string['quizfinishtime'] = 'Quiz Zeitbegrenzung der Prüfung (max Prüfungszeit erlaubt)';
$string['reference'] = 'Referenz';
$string['refreshserviceworker'] = 'Service Worker neu laden';
$string['resetserviceworker'] = 'Service Worker zurücksetzen';
$string['responsefiles'] = 'Antwort-Dateien';
$string['responsefiles_help'] = 'Durch Klicken auf das blinkende Icon für den Wireless Network Verbindungsstatus wird während eines Testversuchs eine Antwortdatei herunter geladen. Durch mehrmaliges Anklicken des Icons können unterschiedliche Versionen der Antwortdatei herunter geladen werden. Der Dateiname ist zusammengesetzt aus dem Präfix Wifiresilience, Datums- und Zeitangabe und der Endung .eth (...). (Beispiel: Wifiresilience-crs229-cm643-id558-u8-a25197-d201803010842.eth). Wenn nicht anders konfiguriert, wird die Datei im "Downloads"-Ordner gespeichert.';
$string['reviewthisattempt'] = 'Versuch erneut ansehen';
$string['rule1start'] = '1. <font color=grey>[Wifiresilience-SW] Wifiresilience-exams-sw.js startet Registrierung..</font>';
$string['rule1success'] = '1. <font color=green>[Wifiresilience-SW] Service-Worker Registrierung war erfolgreich. <span id="sw_kind"></span>';
$string['rule1fail'] = '1. <font color=red>[Wifiresilience-SW] Service-Worker Registrierung war nicht erfolgreich. Fehler: {$a} <span id="sw_kind"></span></font>';
$string['rule1error'] = '1. <font color=red>[Wifiresilience-SW] Service-Worker werden von diesem Browser nicht unterstützt. <span id="sw_kind"></span></font>';
$string['rule1statusactive'] = '(Status: Aktiv)';
$string['rule1statusinstalling'] = '(Status: Wird installiert)';
$string['rule1statuswaiting'] = '(Status: Wartend)';
$string['rule2start'] = '<br>2. <font color=grey>[Wifiresilience-SW] IndexedDB ist unbekannt.</font>';
$string['rule2success'] = '<br>2. <font color=green>[Wifiresilience-SW] IndexedDB wird von diesem Browser unterstützt.</font>';
$string['rule2error'] = '<br>2. <font color=red>[Wifiresilience-SW] IndexedDB wird von diesem Browser nicht unterstützt.</font>';
$string['rule3start'] = '<br>3. <font color=grey>[Wifiresilience-SW] Storage Persistance ist unbekannt.</font>';
$string['rule3success'] = '<br>3. <font color=green>[Wifiresilience-SW] Der Speicher wird nur durch explizite Benutzeraktion gelöscht.</font>';
$string['rule3error'] = '<br>3. <font color=red>[Wifiresilience-SW] Alte Aufzeichnungen im Speicher können vom UA unter Lastdruck gelöscht werden.</font>';
$string['rule4start'] = '<br>4. <font color=grey>[Wifiresilience-SW] Jetzige available Storage Quota ist unbekannt.</font>';
$string['rule4success'] = '<br>4. <font color=green>[Wifiresilience-SW] Im Browserspeicher werden bereits {$a->usedbytes} von {$a->grantedbytes} verwendet.</font>';
$string['rule4fail'] = '<br>4. <font color=red>[Wifiresilience-SW] Der Browserspeciher (webkitTemporaryStorage) kann nicht berechnet werden.</font>';
$string['rule4error'] = '<br>4. <font color=red>[Wifiresilience-SW] Der Browserspeciher kann nicht berechnet werden.</font>';
$string['rule5start'] = '<br>5. <font color=grey>[Wifiresilience-SW] Der Aufruf von zusätzlichem Speicherkontingent (1GB) ergab: unbekannt.</font>';
$string['rule5success'] = '<br>5. <font color=green>[Wifiresilience-SW] Der Aufruf von zusätzlichem Speicherkontingent (1GB) war erfolgreich.</font>';
$string['rule5fail'] = '<br>5. <font color=red>[Wifiresilience-SW] Der Aufruf von zusätzlichem Speicherkontingent (1GB) war nicht erfolgreich. webkitPersistentStorage wird nicht unterstützt.</font>';
$string['rule5error'] = '<br>5. <font color=red>[Wifiresilience-SW] Der Aufruf von zusätzlichem Speicherkontingent (1GB) war nicht erfolgreich.</font>';
$string['rule6start'] = '<br>6. <font color=grey>[Wifiresilience-SW] Der Aufruf von Informationen über die cacheAPI ergab: unbekannt.</font>';
$string['rule6success'] = '<br>6. <font color=green>[Wifiresilience-SW] Die CacheAPI wird unterstützt.</font>';
$string['rule6error'] = '<br>6. <font color=red>[Wifiresilience-SW] Die CacheAPI wird nicht unterstützt.</font>';
$string['rule7start'] = '<br>7. <font color=grey>[Wifiresilience-SW] Der Aufruf von Informationen über Background Sync ergab: unbekannt.</font>';
$string['rule7success'] = '<br>7. <font color=green>[Wifiresilience-SW] Background Sync wird unterstützt.</font>';
$string['rule7error'] = '<br>7. <font color=red>[Wifiresilience-SW] Background Sync wird nicht unterstützt.</font>';
$string['rulebgsyncsuccess'] = 'Background Sync wurde erfolgreich ausgelöst.';
$string['rulebgsyncfail'] = 'Background Sync fehlgeschlagen.';
$string['rulebgsyncsupported'] = 'Background Sync wird nicht unterstützt.';
$string['ruleswnotregisteredreset'] = 'Service Worker ist nicht registriert (möglicherweise fehlerhaft oder nicht im Page Scope enthalten oder bereits abgemeldet). Sie können ihn jetzt nicht zurücksetzen.';
$string['ruleswnotregisteredstop'] = 'Service Worker ist nicht registriert (möglicherweise fehlerhaft oder nicht im Page Scope enthalten oder bereits abgemeldet). Sie können ihn jetzt nicht stoppen.';
$string['ruleswnotregisteredupdate'] = 'Service Worker ist nicht registriert (möglicherweise fehlerhaft oder nicht im Page Scope enthalten oder bereits abgemeldet). Sie können ihn jetzt nicht aktualisieren.';
$string['savefailed'] = 'Hinweis: Von Zeit zu Zeit sollten Sie:';
$string['savetheresponses'] = 'Kopie der Antworten herunterladen';
$string['emergencyfileoptions'] = 'Sie können auch eine Kopie der Antworten herunterladen';
$string['savingdots'] = 'Auf dem Server speichern...';
$string['savingtryagaindots'] = 'Erneuter Versuch auf dem Server zu speichern ...';
$string['serviceworkermgmt'] = 'Service Worker Management';
$string['status'] = 'Status';
$string['stopserviceworker'] = 'Service Worker anhalten';
$string['submitfailed'] = 'Abgabe des Tests ist fehlgeschlagen';
$string['submitfaileddownloadmessage'] = '<br /><strong>Oder</strong><br />{$a}<br />(Hinweis: Keine Daten sind verloren gegangen. Melden Sie sich bei der Prüfungsaufsicht, damit Ihre heruntergeladene Resultate-Datei gesichert und auf den Moodle Server hochgeladen werden kann.)';
$string['submitfailedmessage'] = 'Ihre Antworten konnten nicht abgegeben werden. Sie können versuchen zu:';
$string['submitting'] = '<h3>Abgabe.. Bitte warten..</h3>';
$string['submitallandfinishtryagain'] = 'Alles abgeben und (erneut) beenden';
$string['syncedfiles'] = 'Antwort-Dateien im Hintergrund synchronisieren';
$string['syncserviceworker'] = 'Background Sync auslösen';
$string['takeattemptfromjson'] = 'Unverschlüsselte Versuch-ID verwenden';
$string['takeattemptfromjson_help'] = '***Bitte sorgfältig lesen:*** Wenn ein Versuch bereits beschädigt ist oder Sie einen neuen Versuch erstellen möchten, um damit fortzufahren und Probleme mit der Integrität der Fragensequenz zu vermeiden, verwenden Sie diese Option mit absoluter Sorgfalt! Bitte beachten Sie, dass die Versuchs-ID immer mit einem tatsächlichen Versuch übereinstimmen muss. Unabhängig davon, ob dieser zuvor erstellt oder vom Administrator (als Schüler angemeldet) erstellt wurde, um  damit die ursprüngliche Antwortdatei eines Schülers (Notfalldatei) hochzuladen. Es wird immer ein gültiger Versuch benötigt.<br><font color="red">Bitte kreuzen Sie dieses Kästchen NICHT an, es sei denn, Sie verstehen vollständig, wie Versuche verwaltet werden!</font>';
$string['techerrors'] = 'Technische Fehler anzeigen';
$string['techerrors_help'] = 'Dieses Option hilft das Fehlschlagen einer Testabgabe zu verstehen. Fehler, falls vorhanden, werden am Ende der Abgabeseite angezeigt.';
$string['technicalchecks'] = 'Speicher Überprüfungen für den aktuellen Browser';
$string['technicalinspection'] = 'Technische Inspektion:<br>';
$string['testencryption'] = 'Geräte- und Serververschlüsselung testen';
$string['uploadfailed'] = 'Das Hochladen ist fehlgeschlagen';
$string['uploadfinishtime'] = 'Versuchs-/Abgabezeit';
$string['uploadingresponsesfor'] = 'Antworten hochladen für {$a}';
$string['uploadinspection'] = 'Antworten überprüfen';
$string['uploadinspectionfor'] = 'Antworten überprüfen für {$a}';
$string['uploadmoreresponses'] = 'Weitere Antworten hochladen';
$string['uploadresponses'] = 'Antwort-Dateien hochladen';
$string['uploadresponsesadmin'] = 'Adminstrator/innen können: ';
$string['uploadresponsesfor'] = 'Antwort-Dateien hochladen für {$a}';
$string['usefinalsubmissiontime'] = 'Endgültige Einreichungszeit aus der Antwort-Datei verwenden (falls vorhanden)';
$string['usefinalsubmissiontime_help'] = 'Sobald ein Benutzer seinen Versuch beendet und abgibt (oder wenn der Versuch automtisch abgegeben wird), wird ein Parameter namens "final_submission_time" zur Datei hinzugefügt: Dieser Parameter enthält die Abgabe-Zeit. Falls keine endgültige Abgabezeit vorhanden ist, ist der Wert für "final_submission_time" 0. Falls 0, ignoriert das Skript diesen Paramater und eine der untenstehenden Optionen wird verwendet.';
$string['watchxhr'] = 'Live-Events ansehen';
$string['watchxhr_help'] = 'Pro Reihe eine URL eintragen. Gewisse Fragetypen erfordern Live-Überprüfungen oder Hochladen auf den Server. Falls diese hier hinzugefügt werden, kann das Plugin die Offline-Zeit des Benutzers berechnen und dann automtisch zu der totalen zusätzlichen Zeit hinzufügen, so dass der Benutzer zur exakten Zeitlimite den Test beendet.';
$string['webservicedisabled'] = 'Web Services sind nicht aktiviert. Background Sync (das automatische Versenden von Student Emergency Respones zum Server) funktioniert nur mit aktivierten Mobile Web Services. Sie können dies mit folgenden Schritten beheben:<br>';
$string['webserviceenablemobile'] = 'Aktivieren Sie Web Services <a href="{$a}/admin/search.php?query=enablewebservices">hier</a>';
$string['webserviceaddtoken'] = 'Fügen Sie dem Plugin ein Token auf <a href="{$a->wwwroot}/admin/settings.php?section=modsettingsquizcatwifiresilience">Plugin Ebene</a> oder <a href="{$a->wwwroot}/course/modedit.php?update={$a->quizcmid}&return=1">Quiz Ebene</a> hinzu. (Quiz Einstellungen haben eine höhere Priorität als die der Seitenebene)';
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
$string['wifitoken'] = 'Background Sync Token';
$string['wifitoken_help'] = 'Web Service Token um Notfall-Dateien im Hintergrund zu senden, während die Netzwerkverbindung vorhanden ist. Dieser Token kann in der Website-Administration (Suche: "webservicetokens" / Token verwalten) erstellt werden.';
