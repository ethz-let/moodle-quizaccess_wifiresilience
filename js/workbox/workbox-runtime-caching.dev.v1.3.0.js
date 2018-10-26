/*
 Copyright 2016 Google Inc. All Rights Reserved.
 Licensed under the Apache License, Version 2.0 (the "License");
 you may not use this file except in compliance with the License.
 You may obtain a copy of the License at

     http://www.apache.org/licenses/LICENSE-2.0

 Unless required by applicable law or agreed to in writing, software
 distributed under the License is distributed on an "AS IS" BASIS,
 WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 See the License for the specific language governing permissions and
 limitations under the License.
*/

this.workbox = this.workbox || {};
(function (exports) {
'use strict';

class ErrorFactory$1{constructor(a){this._errors=a;}createError(a,b){if(!(a in this._errors))throw new Error(`Unable to generate error '${a}'.`);let c=this._errors[a].replace(/\s+/g,' '),d=null;b&&(c+=` [${b.message}]`,d=b.stack);const e=new Error;return e.name=a,e.message=c,e.stack=d,e}}

const errors={"multiple-cache-will-update-plugins":'You cannot register more than one plugin that implements cacheWillUpdate.',"multiple-cache-will-match-plugins":'You cannot register more than one plugin that implements cacheWillMatch.',"invalid-response-for-caching":'The fetched response could not be cached due to an invalid response code.',"no-response-received":'No response received; falling back to cache.',"bad-cache-id":`The 'cacheId' parameter must be a string with at least `+`one character.`};var ErrorFactory = new ErrorFactory$1(errors);

var commonjsGlobal = typeof window !== 'undefined' ? window : typeof global !== 'undefined' ? global : typeof self !== 'undefined' ? self : {};





function createCommonjsModule(fn, module) {
	return module = { exports: {} }, fn(module, module.exports), module.exports;
}

var stackframe=createCommonjsModule(function(a){(function(c,d){'use strict';a.exports=d();})(commonjsGlobal,function(){'use strict';function c(t){return!isNaN(parseFloat(t))&&isFinite(t)}function d(t){return t[0].toUpperCase()+t.substring(1)}function e(t){return function(){return this[t]}}function f(t){if(t instanceof Object)for(var u=0;u<o.length;u++)t.hasOwnProperty(o[u])&&void 0!==t[o[u]]&&this['set'+d(o[u])](t[o[u]]);}var g=['isConstructor','isEval','isNative','isToplevel'],h=['columnNumber','lineNumber'],l=['fileName','functionName','source'],o=g.concat(h,l,['args']);f.prototype={getArgs:function(){return this.args},setArgs:function(t){if('[object Array]'!==Object.prototype.toString.call(t))throw new TypeError('Args must be an Array');this.args=t;},getEvalOrigin:function(){return this.evalOrigin},setEvalOrigin:function(t){if(t instanceof f)this.evalOrigin=t;else if(t instanceof Object)this.evalOrigin=new f(t);else throw new TypeError('Eval Origin must be an Object or StackFrame')},toString:function(){var t=this.getFunctionName()||'{anonymous}',u='('+(this.getArgs()||[]).join(',')+')',w=this.getFileName()?'@'+this.getFileName():'',x=c(this.getLineNumber())?':'+this.getLineNumber():'',y=c(this.getColumnNumber())?':'+this.getColumnNumber():'';return t+u+w+x+y}};for(var q=0;q<g.length;q++)f.prototype['get'+d(g[q])]=e(g[q]),f.prototype['set'+d(g[q])]=function(t){return function(u){this[t]=!!u;}}(g[q]);for(var r=0;r<h.length;r++)f.prototype['get'+d(h[r])]=e(h[r]),f.prototype['set'+d(h[r])]=function(t){return function(u){if(!c(u))throw new TypeError(t+' must be a Number');this[t]=+u;}}(h[r]);for(var s=0;s<l.length;s++)f.prototype['get'+d(l[s])]=e(l[s]),f.prototype['set'+d(l[s])]=function(t){return function(u){this[t]=u+'';}}(l[s]);return f});});

var errorStackParser=createCommonjsModule(function(a){(function(c,d){'use strict';a.exports=d(stackframe);})(commonjsGlobal,function(d){'use strict';var f=/(^|@)\S+\:\d+/,g=/^\s*at .*(\S+\:\d+|\(native\))/m,h=/^(eval@)?(\[native code\])?$/;return{parse:function(k){if('undefined'!=typeof k.stacktrace||'undefined'!=typeof k['opera#sourceloc'])return this.parseOpera(k);if(k.stack&&k.stack.match(g))return this.parseV8OrIE(k);if(k.stack)return this.parseFFOrSafari(k);throw new Error('Cannot parse given Error object')},extractLocation:function(k){if(-1===k.indexOf(':'))return[k];var l=/(.+?)(?:\:(\d+))?(?:\:(\d+))?$/,m=l.exec(k.replace(/[\(\)]/g,''));return[m[1],m[2]||void 0,m[3]||void 0]},parseV8OrIE:function(k){var l=k.stack.split('\n').filter(function(m){return!!m.match(g)},this);return l.map(function(m){-1<m.indexOf('(eval ')&&(m=m.replace(/eval code/g,'eval').replace(/(\(eval at [^\()]*)|(\)\,.*$)/g,''));var n=m.replace(/^\s+/,'').replace(/\(eval code/g,'(').split(/\s+/).slice(1),o=this.extractLocation(n.pop()),p=n.join(' ')||void 0,q=-1<['eval','<anonymous>'].indexOf(o[0])?void 0:o[0];return new d({functionName:p,fileName:q,lineNumber:o[1],columnNumber:o[2],source:m})},this)},parseFFOrSafari:function(k){var l=k.stack.split('\n').filter(function(m){return!m.match(h)},this);return l.map(function(m){if(-1<m.indexOf(' > eval')&&(m=m.replace(/ line (\d+)(?: > eval line \d+)* > eval\:\d+\:\d+/g,':$1')),-1===m.indexOf('@')&&-1===m.indexOf(':'))return new d({functionName:m});var n=m.split('@'),o=this.extractLocation(n.pop()),p=n.join('@')||void 0;return new d({functionName:p,fileName:o[0],lineNumber:o[1],columnNumber:o[2],source:m})},this)},parseOpera:function(k){return!k.stacktrace||-1<k.message.indexOf('\n')&&k.message.split('\n').length>k.stacktrace.split('\n').length?this.parseOpera9(k):k.stack?this.parseOpera11(k):this.parseOpera10(k)},parseOpera9:function(k){for(var q,l=/Line (\d+).*script (?:in )?(\S+)/i,m=k.message.split('\n'),n=[],o=2,p=m.length;o<p;o+=2)q=l.exec(m[o]),q&&n.push(new d({fileName:q[2],lineNumber:q[1],source:m[o]}));return n},parseOpera10:function(k){for(var q,l=/Line (\d+).*script (?:in )?(\S+)(?:: In function (\S+))?$/i,m=k.stacktrace.split('\n'),n=[],o=0,p=m.length;o<p;o+=2)q=l.exec(m[o]),q&&n.push(new d({functionName:q[3]||void 0,fileName:q[2],lineNumber:q[1],source:m[o]}));return n},parseOpera11:function(k){var l=k.stack.split('\n').filter(function(m){return!!m.match(f)&&!m.match(/^Error created at/)},this);return l.map(function(m){var n=m.split('@'),o=this.extractLocation(n.pop()),p=n.shift()||'',q=p.replace(/<anonymous function(: (\w+))?>/,'$2').replace(/\([^\)]*\)/g,'')||void 0,r;p.match(/\(([^\)]*)\)/)&&(r=p.replace(/^[^\(]+\(([^\)]*)\)$/,'$1'));var s=r===void 0||'[arguments not available]'===r?void 0:r.split(',');return new d({functionName:q,args:s,fileName:o[0],lineNumber:o[1],columnNumber:o[2],source:m})},this)}}});});

function atLeastOne(a){const b=Object.keys(a);b.some((c)=>a[c]!==void 0)||throwError('Please set at least one of the following parameters: '+b.map((c)=>`'${c}'`).join(', '));}function isInstance(a,b){const c=Object.keys(a).pop();a[c]instanceof b||throwError(`The '${c}' parameter must be an instance of
      '${b.name}'`);}function isType(a,b){const c=Object.keys(a).pop(),d=typeof a[c];d!==b&&throwError(`The '${c}' parameter has the wrong type. (Expected:
      ${b}, actual: ${d})`);}function isArrayOfType(a,b){const c=Object.keys(a).pop(),d=`The '${c}' parameter should be an array containing
    one or more '${b}' elements.`;Array.isArray(a[c])||throwError(d);for(let e of a[c])typeof e!==b&&throwError(d);}function throwError(a){a=a.replace(/\s+/g,' ');const b=new Error(a);b.name='assertion-failed';const c=errorStackParser.parse(b);throw 3<=c.length&&(b.message=`Invalid call to ${c[2].functionName}() — `+a),b}

class LogGroup{constructor(){this._logs=[],this._childGroups=[],this._isFallbackMode=!1;const a=/Firefox\/(\d*)\.\d*/.exec(navigator.userAgent);if(a)try{const b=parseInt(a[1],10);55>b&&(this._isFallbackMode=!0);}catch(b){this._isFallbackMode=!0;}/Edge\/\d*\.\d*/.exec(navigator.userAgent)&&(this._isFallbackMode=!0);}addPrimaryLog(a){this._primaryLog=a;}addLog(a){this._logs.push(a);}addChildGroup(a){0===a._logs.length||this._childGroups.push(a);}print(){return 0===this._logs.length&&0===this._childGroups.length?void this._printLogDetails(this._primaryLog):void(this._primaryLog&&(this._isFallbackMode?this._printLogDetails(this._primaryLog):console.groupCollapsed(...this._getLogContent(this._primaryLog))),this._logs.forEach((a)=>{this._printLogDetails(a);}),this._childGroups.forEach((a)=>{a.print();}),this._primaryLog&&!this._isFallbackMode&&console.groupEnd())}_printLogDetails(a){const b=a.logFunc?a.logFunc:console.log;b(...this._getLogContent(a));}_getLogContent(a){let b=a.message;this._isFallbackMode&&'string'==typeof b&&(b=b.replace(/%c/g,''));let c=[b];return!this._isFallbackMode&&a.colors&&(c=c.concat(a.colors)),a.args&&(c=c.concat(a.args)),c}}

function isDevBuild(){return`dev`==`dev`}

self.workbox=self.workbox||{},self.workbox.LOG_LEVEL=self.workbox.LOG_LEVEL||{none:-1,verbose:0,debug:1,warn:2,error:3};const LIGHT_GREY=`#bdc3c7`; const DARK_GREY=`#7f8c8d`; const LIGHT_GREEN=`#2ecc71`; const LIGHT_YELLOW=`#f1c40f`; const LIGHT_RED=`#e74c3c`; const LIGHT_BLUE=`#3498db`;class LogHelper{constructor(){this._defaultLogLevel=isDevBuild()?self.workbox.LOG_LEVEL.debug:self.workbox.LOG_LEVEL.warn;}log(a){this._printMessage(self.workbox.LOG_LEVEL.verbose,a);}debug(a){this._printMessage(self.workbox.LOG_LEVEL.debug,a);}warn(a){this._printMessage(self.workbox.LOG_LEVEL.warn,a);}error(a){this._printMessage(self.workbox.LOG_LEVEL.error,a);}_printMessage(a,b){if(this._shouldLogMessage(a,b)){const c=this._getAllLogGroups(a,b);c.print();}}_getAllLogGroups(a,b){const c=new LogGroup,d=this._getPrimaryMessageDetails(a,b);if(c.addPrimaryLog(d),b.error){const f={message:b.error,logFunc:console.error};c.addLog(f);}const e=new LogGroup;if(b.that&&b.that.constructor&&b.that.constructor.name){const f=b.that.constructor.name;e.addLog(this._getKeyValueDetails('class',f));}return b.data&&('object'!=typeof b.data||b.data instanceof Array?e.addLog(this._getKeyValueDetails('additionalData',b.data)):Object.keys(b.data).forEach((f)=>{e.addLog(this._getKeyValueDetails(f,b.data[f]));})),c.addChildGroup(e),c}_getKeyValueDetails(a,b){return{message:`%c${a}: `,colors:[`color: ${LIGHT_BLUE}`],args:b}}_getPrimaryMessageDetails(a,b){let c,d;a===self.workbox.LOG_LEVEL.verbose?(c='Info',d=LIGHT_GREY):a===self.workbox.LOG_LEVEL.debug?(c='Debug',d=LIGHT_GREEN):a===self.workbox.LOG_LEVEL.warn?(c='Warn',d=LIGHT_YELLOW):a===self.workbox.LOG_LEVEL.error?(c='Error',d=LIGHT_RED):void 0;let e=`%c🔧 %c[${c}]`;const f=[`color: ${LIGHT_GREY}`,`color: ${d}`];let g;return'string'==typeof b?g=b:b.message&&(g=b.message),g&&(g=g.replace(/\s+/g,' '),e+=`%c ${g}`,f.push(`color: ${DARK_GREY}; font-weight: normal`)),{message:e,colors:f}}_shouldLogMessage(a,b){if(!b)return!1;let c=this._defaultLogLevel;return self&&self.workbox&&'number'==typeof self.workbox.logLevel&&(c=self.workbox.logLevel),c===self.workbox.LOG_LEVEL.none||a<c?!1:!0}}var logHelper = new LogHelper;

class CacheableResponse{constructor({statuses:a,headers:b}={}){atLeastOne({statuses:a,headers:b}),a!==void 0&&isArrayOfType({statuses:a},'number'),b!==void 0&&isType({headers:b},'object'),this.statuses=a,this.headers=b;}isResponseCacheable({request:a,response:b}={}){isInstance({response:b},Response);let c=!0;if(this.statuses&&(c=this.statuses.includes(b.status)),this.headers&&c&&(c=Object.keys(this.headers).some((d)=>{return b.headers.get(d)===this.headers[d]})),!c){const d={response:b};this.statuses&&(d['valid-status-codes']=JSON.stringify(this.statuses)),this.headers&&(d['valid-headers']=JSON.stringify(this.headers)),a&&(d.request=a),logHelper.debug({message:`The response does not meet the criteria for being added to the
          cache.`,data:d});}return c}}

class CacheableResponsePlugin extends CacheableResponse{cacheWillUpdate({request:a,response:b}={}){return this.isResponseCacheable({request:a,response:b})}}

const getDefaultCacheName=({cacheId:a}={})=>{let b=`workbox-runtime-caching`;return a&&(b=`${a}-${b}`),self&&self.registration&&(b+=`-${self.registration.scope}`),b};
const pluginCallbacks=['cacheDidUpdate','cacheWillMatch','cacheWillUpdate','fetchDidFail','requestWillFetch'];

var cleanResponseCopy = (({response:a})=>{isInstance({response:a},Response);const b=a.clone(),c='body'in b?Promise.resolve(b.body):b.blob();return c.then((d)=>{return new Response(d,{headers:b.headers,status:b.status,statusText:b.statusText})})});

class RequestWrapper{constructor({cacheName:a,cacheId:b,plugins:c,fetchOptions:d,matchOptions:e}={}){if(b&&('string'!=typeof b||0===b.length))throw ErrorFactory.createError('bad-cache-id');a?(isType({cacheName:a},'string'),this.cacheName=a,b&&(this.cacheName=`${b}-${this.cacheName}`)):this.cacheName=getDefaultCacheName({cacheId:b}),d&&(isType({fetchOptions:d},'object'),this.fetchOptions=d),e&&(isType({matchOptions:e},'object'),this.matchOptions=e),this.plugins=new Map,c&&(isArrayOfType({plugins:c},'object'),c.forEach((f)=>{for(let g of pluginCallbacks)if('function'==typeof f[g]){if(!this.plugins.has(g))this.plugins.set(g,[]);else if('cacheWillUpdate'===g)throw ErrorFactory.createError('multiple-cache-will-update-plugins');else if('cacheWillMatch'===g)throw ErrorFactory.createError('multiple-cache-will-match-plugins');this.plugins.get(g).push(f);}})),this.plugins.has('cacheWillUpdate')&&(this._userSpecifiedCachableResponsePlugin=this.plugins.get('cacheWillUpdate')[0]);}getDefaultCacheableResponsePlugin(){return this._defaultCacheableResponsePlugin||(this._defaultCacheableResponsePlugin=new CacheableResponsePlugin({statuses:[200]})),this._defaultCacheableResponsePlugin}async getCache(){return this._cache||(this._cache=await caches.open(this.cacheName)),this._cache}async match({request:a}){atLeastOne({request:a});const b=await this.getCache();let c=await b.match(a,this.matchOptions);if(this.plugins.has('cacheWillMatch')){const d=this.plugins.get('cacheWillMatch')[0];c=d.cacheWillMatch({request:a,cache:b,cachedResponse:c,matchOptions:this.matchOptions,cacheName:this.cacheName});}return c}async fetch({request:a}){'string'==typeof a?a=new Request(a):isInstance({request:a},Request);const b=this.plugins.has('fetchDidFail')?a.clone():null;if(this.plugins.has('requestWillFetch'))for(let c of this.plugins.get('requestWillFetch')){const d=c.requestWillFetch({request:a});isInstance({returnedPromise:d},Promise);const e=await d;isInstance({returnedRequest:e},Request),a=e;}try{return await fetch(a,this.fetchOptions)}catch(c){if(this.plugins.has('fetchDidFail'))for(let d of this.plugins.get('fetchDidFail'))d.fetchDidFail({request:b.clone()});throw c}}async fetchAndCache({request:a,waitOnCache:b,cacheKey:c,cacheResponsePlugin:d,cleanRedirects:e}){atLeastOne({request:a});let f;const g=await this.fetch({request:a}),h=this._userSpecifiedCachableResponsePlugin||d||this.getDefaultCacheableResponsePlugin(),i=h.cacheWillUpdate({request:a,response:g});if(i){const j=e&&g.redirected?await cleanResponseCopy({response:g}):g.clone();f=this.getCache().then(async(k)=>{let l;const m=c||a;if('opaque'!==g.type&&this.plugins.has('cacheDidUpdate')&&(l=await this.match({request:m})),await k.put(m,j),this.plugins.has('cacheDidUpdate'))for(let n of this.plugins.get('cacheDidUpdate'))await n.cacheDidUpdate({cacheName:this.cacheName,oldResponse:l,newResponse:j,url:'url'in m?m.url:m});});}else if(!i&&b)throw ErrorFactory.createError('invalid-response-for-caching');return b&&f&&(await f),g}}

class Handler{constructor({requestWrapper:a,waitOnCache:b}={}){this.requestWrapper=a?a:new RequestWrapper,this.waitOnCache=!!b;}handle({event:a,params:b}={}){throw Error('This abstract method must be implemented in a subclass.')}}

class CacheFirst extends Handler{async handle({event:a}={}){isInstance({event:a},FetchEvent);const b=await this.requestWrapper.match({request:a.request});return b||(await this.requestWrapper.fetchAndCache({request:a.request,waitOnCache:this.waitOnCache}))}}

class CacheOnly extends Handler{async handle({event:a}={}){return isInstance({event:a},FetchEvent),await this.requestWrapper.match({request:a.request})}}

class NetworkFirst extends Handler{constructor(a={}){super(a),this._cacheablePlugin=new CacheableResponsePlugin({statuses:[0,200]});const{networkTimeoutSeconds:b}=a;b&&(isType({networkTimeoutSeconds:b},'number'),this.networkTimeoutSeconds=b);}async handle({event:a}={}){isInstance({event:a},FetchEvent);const b=[];let c;this.networkTimeoutSeconds&&b.push(new Promise((e)=>{c=setTimeout(()=>{e(this.requestWrapper.match({request:a.request}));},1e3*this.networkTimeoutSeconds);}));const d=this.requestWrapper.fetchAndCache({request:a.request,waitOnCache:this.waitOnCache,cacheResponsePlugin:this._cacheablePlugin}).then((e)=>{return c&&clearTimeout(c),e?e:Promise.reject(ErrorFactory.createError('no-response-received'))}).catch(()=>this.requestWrapper.match({request:a.request}));return b.push(d),Promise.race(b)}}

class NetworkOnly extends Handler{async handle({event:a}={}){return isInstance({event:a},FetchEvent),await this.requestWrapper.fetch({request:a.request})}}

class StaleWhileRevalidate extends Handler{constructor(a={}){super(a),this._cacheablePlugin=new CacheableResponsePlugin({statuses:[0,200]});}async handle({event:a}={}){isInstance({event:a},FetchEvent);const b=this.requestWrapper.fetchAndCache({request:a.request,waitOnCache:this.waitOnCache,cacheResponsePlugin:this._cacheablePlugin}).catch(()=>Response.error()),c=await this.requestWrapper.match({request:a.request});return c||(await b)}}

exports.CacheFirst = CacheFirst;
exports.CacheOnly = CacheOnly;
exports.Handler = Handler;
exports.NetworkFirst = NetworkFirst;
exports.NetworkOnly = NetworkOnly;
exports.RequestWrapper = RequestWrapper;
exports.StaleWhileRevalidate = StaleWhileRevalidate;
exports.getDefaultCacheName = getDefaultCacheName;

}((this.workbox.runtimeCaching = this.workbox.runtimeCaching || {})));
//# sourceMappingURL=workbox-runtime-caching.dev.v1.3.0.js.map
