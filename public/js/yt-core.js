(function(){var g,ba=ba||{},m=this;function n(a){return void 0!==a}
function p(a,b,c){a=a.split(".");c=c||m;a[0]in c||!c.execScript||c.execScript("var "+a[0]);for(var d;a.length&&(d=a.shift());)!a.length&&n(b)?c[d]=b:c[d]?c=c[d]:c=c[d]={}}
function r(a,b){for(var c=a.split("."),d=b||m,e;e=c.shift();)if(null!=d[e])d=d[e];else return null;return d}
function ca(){}
function da(a){a.getInstance=function(){return a.la?a.la:a.la=new a}}
function ea(a){var b=typeof a;if("object"==b)if(a){if(a instanceof Array)return"array";if(a instanceof Object)return b;var c=Object.prototype.toString.call(a);if("[object Window]"==c)return"object";if("[object Array]"==c||"number"==typeof a.length&&"undefined"!=typeof a.splice&&"undefined"!=typeof a.propertyIsEnumerable&&!a.propertyIsEnumerable("splice"))return"array";if("[object Function]"==c||"undefined"!=typeof a.call&&"undefined"!=typeof a.propertyIsEnumerable&&!a.propertyIsEnumerable("call"))return"function"}else return"null";
else if("function"==b&&"undefined"==typeof a.call)return"object";return b}
function fa(a){return"array"==ea(a)}
function ga(a){var b=ea(a);return"array"==b||"object"==b&&"number"==typeof a.length}
function t(a){return"string"==typeof a}
function ha(a){return"number"==typeof a}
function ia(a){return"function"==ea(a)}
function ka(a){var b=typeof a;return"object"==b&&null!=a||"function"==b}
function la(a){return a[na]||(a[na]=++oa)}
var na="closure_uid_"+(1E9*Math.random()>>>0),oa=0;function pa(a,b,c){return a.call.apply(a.bind,arguments)}
function qa(a,b,c){if(!a)throw Error();if(2<arguments.length){var d=Array.prototype.slice.call(arguments,2);return function(){var c=Array.prototype.slice.call(arguments);Array.prototype.unshift.apply(c,d);return a.apply(b,c)}}return function(){return a.apply(b,arguments)}}
function u(a,b,c){u=Function.prototype.bind&&-1!=Function.prototype.bind.toString().indexOf("native code")?pa:qa;return u.apply(null,arguments)}
function ra(a,b){var c=Array.prototype.slice.call(arguments,1);return function(){var b=c.slice();b.push.apply(b,arguments);return a.apply(this,b)}}
var v=Date.now||function(){return+new Date};
function w(a,b){function c(){}
c.prototype=b.prototype;a.J=b.prototype;a.prototype=new c;a.prototype.constructor=a;a.base=function(a,c,f){for(var h=Array(arguments.length-2),k=2;k<arguments.length;k++)h[k-2]=arguments[k];return b.prototype[c].apply(a,h)}}
;function sa(a){if(Error.captureStackTrace)Error.captureStackTrace(this,sa);else{var b=Error().stack;b&&(this.stack=b)}a&&(this.message=String(a))}
w(sa,Error);sa.prototype.name="CustomError";var ta;function ua(a){a=String(a.substr(0,3)).toLowerCase();return 0==("<tr"<a?-1:"<tr"==a?0:1)}
function va(a,b){for(var c=a.split("%s"),d="",e=Array.prototype.slice.call(arguments,1);e.length&&1<c.length;)d+=c.shift()+e.shift();return d+c.join("%s")}
function wa(a){return/^[\s\xa0]*$/.test(a)}
var xa=String.prototype.trim?function(a){return a.trim()}:function(a){return a.replace(/^[\s\xa0]+|[\s\xa0]+$/g,"")};
function ya(a){return a.replace(/[\s\xa0]+$/,"")}
function za(a){return encodeURIComponent(String(a))}
function Aa(a){return decodeURIComponent(a.replace(/\+/g," "))}
function Ba(a){if(!Ca.test(a))return a;-1!=a.indexOf("&")&&(a=a.replace(Da,"&amp;"));-1!=a.indexOf("<")&&(a=a.replace(Ea,"&lt;"));-1!=a.indexOf(">")&&(a=a.replace(Fa,"&gt;"));-1!=a.indexOf('"')&&(a=a.replace(Ga,"&quot;"));-1!=a.indexOf("'")&&(a=a.replace(Ha,"&#39;"));-1!=a.indexOf("\x00")&&(a=a.replace(Ia,"&#0;"));return a}
var Da=/&/g,Ea=/</g,Fa=/>/g,Ga=/"/g,Ha=/'/g,Ia=/\x00/g,Ca=/[\x00&<>"']/;function Ja(a){return-1!=a.indexOf("&")?"document"in m?Ka(a):Ma(a):a}
function Ka(a){var b={"&amp;":"&","&lt;":"<","&gt;":">","&quot;":'"'},c;c=m.document.createElement("div");return a.replace(Na,function(a,e){var f=b[a];if(f)return f;if("#"==e.charAt(0)){var h=Number("0"+e.substr(1));isNaN(h)||(f=String.fromCharCode(h))}f||(c.innerHTML=a+" ",f=c.firstChild.nodeValue.slice(0,-1));return b[a]=f})}
function Ma(a){return a.replace(/&([^;]+);/g,function(a,c){switch(c){case "amp":return"&";case "lt":return"<";case "gt":return">";case "quot":return'"';default:if("#"==c.charAt(0)){var d=Number("0"+c.substr(1));if(!isNaN(d))return String.fromCharCode(d)}return a}})}
var Na=/&([^;\s<&]+);?/g,Oa={"\x00":"\\0","\b":"\\b","\f":"\\f","\n":"\\n","\r":"\\r","\t":"\\t","\x0B":"\\x0B",'"':'\\"',"\\":"\\\\","<":"<"},Pa={"'":"\\'"};function Qa(a){return null==a?"":String(a)}
function Ra(a,b){for(var c=0,d=xa(String(a)).split("."),e=xa(String(b)).split("."),f=Math.max(d.length,e.length),h=0;0==c&&h<f;h++){var k=d[h]||"",l=e[h]||"",q=RegExp("(\\d*)(\\D*)","g"),z=RegExp("(\\d*)(\\D*)","g");do{var C=q.exec(k)||["","",""],W=z.exec(l)||["","",""];if(0==C[0].length&&0==W[0].length)break;c=Sa(0==C[1].length?0:parseInt(C[1],10),0==W[1].length?0:parseInt(W[1],10))||Sa(0==C[2].length,0==W[2].length)||Sa(C[2],W[2])}while(0==c)}return c}
function Sa(a,b){return a<b?-1:a>b?1:0}
function Ta(a){for(var b=0,c=0;c<a.length;++c)b=31*b+a.charCodeAt(c)>>>0;return b}
function Ua(a){return String(a).replace(/\-([a-z])/g,function(a,c){return c.toUpperCase()})}
function Va(a){var b=t(void 0)?"undefined".replace(/([-()\[\]{}+?*.$\^|,:#<!\\])/g,"\\$1").replace(/\x08/g,"\\x08"):"\\s";return a.replace(new RegExp("(^"+(b?"|["+b+"]+":"")+")([a-z])","g"),function(a,b,e){return b+e.toUpperCase()})}
;var Wa=Array.prototype.indexOf?function(a,b,c){return Array.prototype.indexOf.call(a,b,c)}:function(a,b,c){c=null==c?0:0>c?Math.max(0,a.length+c):c;
if(t(a))return t(b)&&1==b.length?a.indexOf(b,c):-1;for(;c<a.length;c++)if(c in a&&a[c]===b)return c;return-1},Ya=Array.prototype.lastIndexOf?function(a,b,c){return Array.prototype.lastIndexOf.call(a,b,null==c?a.length-1:c)}:function(a,b,c){c=null==c?a.length-1:c;
0>c&&(c=Math.max(0,a.length+c));if(t(a))return t(b)&&1==b.length?a.lastIndexOf(b,c):-1;for(;0<=c;c--)if(c in a&&a[c]===b)return c;return-1},x=Array.prototype.forEach?function(a,b,c){Array.prototype.forEach.call(a,b,c)}:function(a,b,c){for(var d=a.length,e=t(a)?a.split(""):a,f=0;f<d;f++)f in e&&b.call(c,e[f],f,a)},Za=Array.prototype.filter?function(a,b,c){return Array.prototype.filter.call(a,b,c)}:function(a,b,c){for(var d=a.length,e=[],f=0,h=t(a)?a.split(""):a,k=0;k<d;k++)if(k in h){var l=h[k];
b.call(c,l,k,a)&&(e[f++]=l)}return e},$a=Array.prototype.map?function(a,b,c){return Array.prototype.map.call(a,b,c)}:function(a,b,c){for(var d=a.length,e=Array(d),f=t(a)?a.split(""):a,h=0;h<d;h++)h in f&&(e[h]=b.call(c,f[h],h,a));
return e},ab=Array.prototype.some?function(a,b,c){return Array.prototype.some.call(a,b,c)}:function(a,b,c){for(var d=a.length,e=t(a)?a.split(""):a,f=0;f<d;f++)if(f in e&&b.call(c,e[f],f,a))return!0;
return!1},bb=Array.prototype.every?function(a,b,c){return Array.prototype.every.call(a,b,c)}:function(a,b,c){for(var d=a.length,e=t(a)?a.split(""):a,f=0;f<d;f++)if(f in e&&!b.call(c,e[f],f,a))return!1;
return!0};
function cb(a,b,c){b=db(a,b,c);return 0>b?null:t(a)?a.charAt(b):a[b]}
function db(a,b,c){for(var d=a.length,e=t(a)?a.split(""):a,f=0;f<d;f++)if(f in e&&b.call(c,e[f],f,a))return f;return-1}
function eb(a,b){var c;a:{c=t(a)?a.split(""):a;for(var d=a.length-1;0<=d;d--)if(d in c&&b.call(void 0,c[d],d,a)){c=d;break a}c=-1}return 0>c?null:t(a)?a.charAt(c):a[c]}
function fb(a,b){return 0<=Wa(a,b)}
function gb(){var a=ib;if(!fa(a))for(var b=a.length-1;0<=b;b--)delete a[b];a.length=0}
function jb(a,b){fb(a,b)||a.push(b)}
function kb(a,b){var c=Wa(a,b),d;(d=0<=c)&&lb(a,c);return d}
function lb(a,b){Array.prototype.splice.call(a,b,1)}
function nb(a,b){var c=db(a,b,void 0);0<=c&&lb(a,c)}
function ob(a){return Array.prototype.concat.apply(Array.prototype,arguments)}
function pb(a){var b=a.length;if(0<b){for(var c=Array(b),d=0;d<b;d++)c[d]=a[d];return c}return[]}
function qb(a,b){for(var c=1;c<arguments.length;c++){var d=arguments[c];if(ga(d)){var e=a.length||0,f=d.length||0;a.length=e+f;for(var h=0;h<f;h++)a[e+h]=d[h]}else a.push(d)}}
function rb(a,b,c,d){return Array.prototype.splice.apply(a,sb(arguments,1))}
function sb(a,b,c){return 2>=arguments.length?Array.prototype.slice.call(a,b):Array.prototype.slice.call(a,b,c)}
function tb(a,b,c){if(!ga(a)||!ga(b)||a.length!=b.length)return!1;var d=a.length;c=c||ub;for(var e=0;e<d;e++)if(!c(a[e],b[e]))return!1;return!0}
function vb(a,b){return a>b?1:a<b?-1:0}
function ub(a,b){return a===b}
function wb(a){for(var b=[],c=0;c<arguments.length;c++){var d=arguments[c];if(fa(d))for(var e=0;e<d.length;e+=8192)for(var f=sb(d,e,e+8192),f=wb.apply(null,f),h=0;h<f.length;h++)b.push(f[h]);else b.push(d)}return b}
function xb(a){for(var b=Math.random,c=a.length-1;0<c;c--){var d=Math.floor(b()*(c+1)),e=a[c];a[c]=a[d];a[d]=e}}
;function yb(a){if(a.classList)return a.classList;a=a.className;return t(a)&&a.match(/\S+/g)||[]}
function y(a,b){return a.classList?a.classList.contains(b):fb(yb(a),b)}
function A(a,b){a.classList?a.classList.add(b):y(a,b)||(a.className+=0<a.className.length?" "+b:b)}
function zb(a,b){if(a.classList)x(b,function(b){A(a,b)});
else{var c={};x(yb(a),function(a){c[a]=!0});
x(b,function(a){c[a]=!0});
a.className="";for(var d in c)a.className+=0<a.className.length?" "+d:d}}
function B(a,b){a.classList?a.classList.remove(b):y(a,b)&&(a.className=Za(yb(a),function(a){return a!=b}).join(" "))}
function Ab(a,b){a.classList?x(b,function(b){B(a,b)}):a.className=Za(yb(a),function(a){return!fb(b,a)}).join(" ")}
function D(a,b,c){c?A(a,b):B(a,b)}
function Bb(a,b,c){y(a,b)&&(B(a,b),A(a,c))}
function Cb(a,b){var c=!y(a,b);D(a,b,c);return c}
;function Db(a,b,c){for(var d in a)b.call(c,a[d],d,a)}
function Eb(a,b,c){var d={},e;for(e in a)b.call(c,a[e],e,a)&&(d[e]=a[e]);return d}
function Fb(a){var b=0,c;for(c in a)b++;return b}
function Gb(a,b){return Hb(a,b)}
function Ib(a){var b=[],c=0,d;for(d in a)b[c++]=a[d];return b}
function Jb(a){var b=[],c=0,d;for(d in a)b[c++]=d;return b}
function Kb(a){return null!==a&&"withCredentials"in a}
function Hb(a,b){for(var c in a)if(a[c]==b)return!0;return!1}
function Lb(a,b){for(var c in a)if(b.call(void 0,a[c],c,a))return c}
function Mb(a){for(var b in a)return!1;return!0}
function Nb(a,b){b in a&&delete a[b]}
function Ob(a,b){if(null!==a&&b in a)throw Error('The object already contains the key "'+b+'"');a[b]=!0}
function Pb(a,b){for(var c in a)if(!(c in b)||a[c]!==b[c])return!1;for(c in b)if(!(c in a))return!1;return!0}
function Qb(a){var b={},c;for(c in a)b[c]=a[c];return b}
function Rb(a){var b=ea(a);if("object"==b||"array"==b){if(ia(a.clone))return a.clone();var b="array"==b?[]:{},c;for(c in a)b[c]=Rb(a[c]);return b}return a}
var Sb="constructor hasOwnProperty isPrototypeOf propertyIsEnumerable toLocaleString toString valueOf".split(" ");function Tb(a,b){for(var c,d,e=1;e<arguments.length;e++){d=arguments[e];for(c in d)a[c]=d[c];for(var f=0;f<Sb.length;f++)c=Sb[f],Object.prototype.hasOwnProperty.call(d,c)&&(a[c]=d[c])}}
;var Ub={area:!0,base:!0,br:!0,col:!0,command:!0,embed:!0,hr:!0,img:!0,input:!0,keygen:!0,link:!0,meta:!0,param:!0,source:!0,track:!0,wbr:!0};var Vb=RegExp("^[^\u0591-\u06ef\u06fa-\u07ff\u200f\ufb1d-\ufdff\ufe70-\ufefc]*[A-Za-z\u00c0-\u00d6\u00d8-\u00f6\u00f8-\u02b8\u0300-\u0590\u0800-\u1fff\u200e\u2c00-\ufb1c\ufe00-\ufe6f\ufefd-\uffff]"),Wb=RegExp("^[^A-Za-z\u00c0-\u00d6\u00d8-\u00f6\u00f8-\u02b8\u0300-\u0590\u0800-\u1fff\u200e\u2c00-\ufb1c\ufe00-\ufe6f\ufefd-\uffff]*[\u0591-\u06ef\u06fa-\u07ff\u200f\ufb1d-\ufdff\ufe70-\ufefc]");var Xb;a:{var Yb=m.navigator;if(Yb){var Zb=Yb.userAgent;if(Zb){Xb=Zb;break a}}Xb=""}function E(a){return-1!=Xb.indexOf(a)}
;function $b(){return E("Opera")||E("OPR")}
function ac(){return(E("Chrome")||E("CriOS"))&&!$b()&&!E("Edge")}
;function bc(){this.f="";this.j=cc}
bc.prototype.ib=!0;bc.prototype.Va=function(){return this.f};
bc.prototype.toString=function(){return"Const{"+this.f+"}"};
function dc(a){return a instanceof bc&&a.constructor===bc&&a.j===cc?a.f:"type_error:Const"}
var cc={};function ec(a){var b=new bc;b.f=a;return b}
;function fc(){this.f="";this.j=gc}
fc.prototype.ib=!0;var gc={};fc.prototype.Va=function(){return this.f};
function hc(a){if(a instanceof fc&&a.constructor===fc&&a.j===gc)return a.f;ea(a);return"type_error:SafeStyle"}
function ic(a){var b=new fc;b.f=a;return b}
var jc=ic("");function kc(a){var b="",c;for(c in a){if(!/^[-_a-zA-Z0-9]+$/.test(c))throw Error("Name allows only [-_a-zA-Z0-9], got: "+c);var d=a[c];if(null!=d){if(d instanceof bc)d=dc(d);else if(lc.test(d)){for(var e=!0,f=!0,h=0;h<d.length;h++){var k=d.charAt(h);"'"==k&&f?e=!e:'"'==k&&e&&(f=!f)}e&&f||(d="zClosurez")}else d="zClosurez";b+=c+":"+d+";"}}return b?ic(b):jc}
var lc=/^([-,."'%_!# a-zA-Z0-9]+|(?:rgb|hsl)a?\([0-9.%, ]+\))$/;function mc(){this.f="";this.j=nc}
mc.prototype.ib=!0;mc.prototype.Va=function(){return this.f};
mc.prototype.Pc=!0;mc.prototype.Hb=function(){return 1};
function oc(a){if(a instanceof mc&&a.constructor===mc&&a.j===nc)return a.f;ea(a);return"type_error:SafeUrl"}
var pc=/^(?:(?:https?|mailto|ftp):|[^&:/?#]*(?:[/?#]|$))/i;function qc(a){if(a instanceof mc)return a;a=a.ib?a.Va():String(a);pc.test(a)||(a="about:invalid#zClosurez");return rc(a)}
var nc={};function rc(a){var b=new mc;b.f=a;return b}
rc("about:blank");function sc(){this.f="";this.j=tc}
sc.prototype.ib=!0;sc.prototype.Va=function(){return this.f};
sc.prototype.Pc=!0;sc.prototype.Hb=function(){return 1};
function uc(a){if(a instanceof sc&&a.constructor===sc&&a.j===tc)return a.f;ea(a);return"type_error:TrustedResourceUrl"}
var tc={};function vc(a){var b=new sc;b.f=a;return b}
;function wc(){this.f="";this.l=xc;this.j=null}
wc.prototype.Pc=!0;wc.prototype.Hb=function(){return this.j};
wc.prototype.ib=!0;wc.prototype.Va=function(){return this.f};
function yc(a){if(a instanceof wc&&a.constructor===wc&&a.l===xc)return a.f;ea(a);return"type_error:SafeHtml"}
var zc=/^[a-zA-Z0-9-]+$/,Ac={action:!0,cite:!0,data:!0,formaction:!0,href:!0,manifest:!0,poster:!0,src:!0},Bc={APPLET:!0,BASE:!0,EMBED:!0,IFRAME:!0,LINK:!0,MATH:!0,META:!0,OBJECT:!0,SCRIPT:!0,STYLE:!0,SVG:!0,TEMPLATE:!0};
function Cc(a,b,c){if(!zc.test(a))throw Error("Invalid tag name <"+a+">.");if(a.toUpperCase()in Bc)throw Error("Tag name <"+a+"> is not allowed for SafeHtml.");var d=null,e="<"+a;if(b)for(var f in b){if(!zc.test(f))throw Error('Invalid attribute name "'+f+'".');var h=b[f];if(null!=h){var k,l=a;k=f;if(h instanceof bc)h=dc(h);else if("style"==k.toLowerCase()){if(!ka(h))throw Error('The "style" attribute requires goog.html.SafeStyle or map of style properties, '+typeof h+" given: "+h);h instanceof fc||
(h=kc(h));h=hc(h)}else{if(/^on/i.test(k))throw Error('Attribute "'+k+'" requires goog.string.Const value, "'+h+'" given.');if(k.toLowerCase()in Ac)if(h instanceof sc)h=uc(h);else if(h instanceof mc)h=oc(h);else if(t(h))h=qc(h).Va();else throw Error('Attribute "'+k+'" on tag "'+l+'" requires goog.html.SafeUrl, goog.string.Const, or string, value "'+h+'" given.');}h.ib&&(h=h.Va());k=k+'="'+Ba(String(h))+'"';e=e+(" "+k)}}null!=c?fa(c)||(c=[c]):c=[];!0===Ub[a.toLowerCase()]?e+=">":(d=Dc(c),e+=">"+yc(d)+
"</"+a+">",d=d.Hb());(a=b&&b.dir)&&(/^(ltr|rtl|auto)$/i.test(a)?d=0:d=null);return Ec(e,d)}
function Dc(a){function b(a){if(fa(a))x(a,b);else{var f;a instanceof wc?f=a:(f=null,a.Pc&&(f=a.Hb()),a=Ba(a.ib?a.Va():String(a)),f=Ec(a,f));d+=yc(f);f=f.Hb();0==c?c=f:0!=f&&c!=f&&(c=null)}}
var c=0,d="";x(arguments,b);return Ec(d,c)}
var xc={};function Ec(a,b){var c=new wc;c.f=a;c.j=b;return c}
var Fc=Ec("<!DOCTYPE html>",0);Ec("",0);var Gc=Ec("<br>",0);function Hc(a){Ic();return Ec(a,null)}
var Ic=ca;function Jc(a,b){this.x=n(a)?a:0;this.y=n(b)?b:0}
Jc.prototype.clone=function(){return new Jc(this.x,this.y)};
function Kc(a,b){return new Jc(a.x-b.x,a.y-b.y)}
Jc.prototype.ceil=function(){this.x=Math.ceil(this.x);this.y=Math.ceil(this.y);return this};
Jc.prototype.floor=function(){this.x=Math.floor(this.x);this.y=Math.floor(this.y);return this};
Jc.prototype.round=function(){this.x=Math.round(this.x);this.y=Math.round(this.y);return this};function Lc(a,b){this.width=a;this.height=b}
g=Lc.prototype;g.clone=function(){return new Lc(this.width,this.height)};
g.area=function(){return this.width*this.height};
g.aspectRatio=function(){return this.width/this.height};
g.isEmpty=function(){return!this.area()};
g.ceil=function(){this.width=Math.ceil(this.width);this.height=Math.ceil(this.height);return this};
g.floor=function(){this.width=Math.floor(this.width);this.height=Math.floor(this.height);return this};
g.round=function(){this.width=Math.round(this.width);this.height=Math.round(this.height);return this};function Mc(){return E("iPhone")&&!E("iPod")&&!E("iPad")}
;var Nc=$b(),F=E("Trident")||E("MSIE"),Oc=E("Edge"),Pc=Oc||F,Qc=E("Gecko")&&!(-1!=Xb.toLowerCase().indexOf("webkit")&&!E("Edge"))&&!(E("Trident")||E("MSIE"))&&!E("Edge"),Rc=-1!=Xb.toLowerCase().indexOf("webkit")&&!E("Edge"),Sc=E("Macintosh"),Tc=E("Windows");function Uc(){var a=m.document;return a?a.documentMode:void 0}
var Vc;a:{var Wc="",Xc=function(){var a=Xb;if(Qc)return/rv\:([^\);]+)(\)|;)/.exec(a);if(Oc)return/Edge\/([\d\.]+)/.exec(a);if(F)return/\b(?:MSIE|rv)[: ]([^\);]+)(\)|;)/.exec(a);if(Rc)return/WebKit\/(\S+)/.exec(a);if(Nc)return/(?:Version)[ \/]?(\S+)/.exec(a)}();
Xc&&(Wc=Xc?Xc[1]:"");if(F){var Yc=Uc();if(null!=Yc&&Yc>parseFloat(Wc)){Vc=String(Yc);break a}}Vc=Wc}var Zc=Vc,$c={};function ad(a){return $c[a]||($c[a]=0<=Ra(Zc,a))}
function bd(a){return Number(cd)>=a}
var dd=m.document,cd=dd&&F?Uc()||("CSS1Compat"==dd.compatMode?parseInt(Zc,10):5):void 0;var ed=!F||bd(9),fd=!Qc&&!F||F&&bd(9)||Qc&&ad("1.9.1"),gd=F&&!ad("9"),hd=F||Nc||Rc;function id(a,b){a.innerHTML=yc(b)}
function jd(a,b){var c;c=b instanceof mc?b:qc(b);a.href=oc(c)}
function kd(a,b){a.src=uc(b)}
function ld(a,b){var c;c=b instanceof mc?b:qc(b);a.href=oc(c)}
;function md(a){return a?new nd(od(a)):ta||(ta=new nd)}
function G(a){return t(a)?document.getElementById(a):a}
function pd(a){var b=document;return t(a)?b.getElementById(a):a}
function qd(a,b){var c=b||document;return c.querySelectorAll&&c.querySelector?c.querySelectorAll("."+a):rd("*",a,b)}
function H(a,b){var c=b||document,d=null;c.getElementsByClassName?d=c.getElementsByClassName(a)[0]:c.querySelectorAll&&c.querySelector?d=c.querySelector("."+a):d=rd("*",a,b)[0];return d||null}
function rd(a,b,c){var d=document;c=c||d;a=a&&"*"!=a?a.toUpperCase():"";if(c.querySelectorAll&&c.querySelector&&(a||b))return c.querySelectorAll(a+(b?"."+b:""));if(b&&c.getElementsByClassName){c=c.getElementsByClassName(b);if(a){for(var d={},e=0,f=0,h;h=c[f];f++)a==h.nodeName&&(d[e++]=h);d.length=e;return d}return c}c=c.getElementsByTagName(a||"*");if(b){d={};for(f=e=0;h=c[f];f++)a=h.className,"function"==typeof a.split&&fb(a.split(/\s+/),b)&&(d[e++]=h);d.length=e;return d}return c}
function sd(a,b){Db(b,function(b,d){"style"==d?a.style.cssText=b:"class"==d?a.className=b:"for"==d?a.htmlFor=b:td.hasOwnProperty(d)?a.setAttribute(td[d],b):0==d.lastIndexOf("aria-",0)||0==d.lastIndexOf("data-",0)?a.setAttribute(d,b):a[d]=b})}
var td={cellpadding:"cellPadding",cellspacing:"cellSpacing",colspan:"colSpan",frameborder:"frameBorder",height:"height",maxlength:"maxLength",nonce:"nonce",role:"role",rowspan:"rowSpan",type:"type",usemap:"useMap",valign:"vAlign",width:"width"};function ud(a){a=a.document;a=vd(a)?a.documentElement:a.body;return new Lc(a.clientWidth,a.clientHeight)}
function wd(a){var b=xd(a);a=yd(a);return F&&ad("10")&&a.pageYOffset!=b.scrollTop?new Jc(b.scrollLeft,b.scrollTop):new Jc(a.pageXOffset||b.scrollLeft,a.pageYOffset||b.scrollTop)}
function xd(a){return a.scrollingElement?a.scrollingElement:!Rc&&vd(a)?a.documentElement:a.body||a.documentElement}
function yd(a){return a.parentWindow||a.defaultView}
function zd(a,b,c){return Ad(document,arguments)}
function Ad(a,b){var c=b[0],d=b[1];if(!ed&&d&&(d.name||d.type)){c=["<",c];d.name&&c.push(' name="',Ba(d.name),'"');if(d.type){c.push(' type="',Ba(d.type),'"');var e={};Tb(e,d);delete e.type;d=e}c.push(">");c=c.join("")}c=a.createElement(c);d&&(t(d)?c.className=d:fa(d)?c.className=d.join(" "):sd(c,d));2<b.length&&Bd(a,c,b,2);return c}
function Bd(a,b,c,d){function e(c){c&&b.appendChild(t(c)?a.createTextNode(c):c)}
for(;d<c.length;d++){var f=c[d];!ga(f)||ka(f)&&0<f.nodeType?e(f):x(Cd(f)?pb(f):f,e)}}
function Dd(a){var b=document,c=b.createElement("DIV");F?(id(c,Dc(Gc,a)),c.removeChild(c.firstChild)):id(c,a);if(1==c.childNodes.length)c=c.removeChild(c.firstChild);else{for(a=b.createDocumentFragment();c.firstChild;)a.appendChild(c.firstChild);c=a}return c}
function vd(a){return"CSS1Compat"==a.compatMode}
function Ed(a,b){Bd(od(a),a,arguments,1)}
function Fd(a){for(var b;b=a.firstChild;)a.removeChild(b)}
function Gd(a,b){b.parentNode&&b.parentNode.insertBefore(a,b.nextSibling)}
function Hd(a,b){a.insertBefore(b,a.childNodes[0]||null)}
function Id(a){a&&a.parentNode&&a.parentNode.removeChild(a)}
function Jd(a){var b,c=a.parentNode;if(c&&11!=c.nodeType)if(a.removeNode)a.removeNode(!1);else{for(;b=a.firstChild;)c.insertBefore(b,a);Id(a)}}
function Kd(a){return fd&&void 0!=a.children?a.children:Za(a.childNodes,function(a){return 1==a.nodeType})}
function Ld(a){return n(a.firstElementChild)?a.firstElementChild:Md(a.firstChild,!0)}
function Nd(a){return n(a.nextElementSibling)?a.nextElementSibling:Md(a.nextSibling,!0)}
function Md(a,b){for(;a&&1!=a.nodeType;)a=b?a.nextSibling:a.previousSibling;return a}
function Od(a){if(!a)return null;if(a.firstChild)return a.firstChild;for(;a&&!a.nextSibling;)a=a.parentNode;return a?a.nextSibling:null}
function Pd(a){if(!a)return null;if(!a.previousSibling)return a.parentNode;for(a=a.previousSibling;a&&a.lastChild;)a=a.lastChild;return a}
function Qd(a){return ka(a)&&1==a.nodeType}
function Rd(a){var b;if(hd&&!(F&&ad("9")&&!ad("10")&&m.SVGElement&&a instanceof m.SVGElement)&&(b=a.parentElement))return b;b=a.parentNode;return Qd(b)?b:null}
function Sd(a,b){if(!a||!b)return!1;if(a.contains&&1==b.nodeType)return a==b||a.contains(b);if("undefined"!=typeof a.compareDocumentPosition)return a==b||!!(a.compareDocumentPosition(b)&16);for(;b&&a!=b;)b=b.parentNode;return b==a}
function od(a){return 9==a.nodeType?a:a.ownerDocument||a.document}
function Td(a,b){if("textContent"in a)a.textContent=b;else if(3==a.nodeType)a.data=b;else if(a.firstChild&&3==a.firstChild.nodeType){for(;a.lastChild!=a.firstChild;)a.removeChild(a.lastChild);a.firstChild.data=b}else{Fd(a);var c=od(a);a.appendChild(c.createTextNode(String(b)))}}
function Ud(a,b){var c=[];return Vd(a,b,c,!0)?c[0]:void 0}
function Vd(a,b,c,d){if(null!=a)for(a=a.firstChild;a;){if(b(a)&&(c.push(a),d)||Vd(a,b,c,d))return!0;a=a.nextSibling}return!1}
var Wd={SCRIPT:1,STYLE:1,HEAD:1,IFRAME:1,OBJECT:1},Xd={IMG:" ",BR:"\n"};function Yd(a){var b;if((b="A"==a.tagName||"INPUT"==a.tagName||"TEXTAREA"==a.tagName||"SELECT"==a.tagName||"BUTTON"==a.tagName?!a.disabled&&(!Zd(a)||$d(a)):Zd(a)&&$d(a))&&F){var c;!ia(a.getBoundingClientRect)||F&&null==a.parentElement?c={height:a.offsetHeight,width:a.offsetWidth}:c=a.getBoundingClientRect();a=null!=c&&0<c.height&&0<c.width}else a=b;return a}
function Zd(a){a=a.getAttributeNode("tabindex");return null!=a&&a.specified}
function $d(a){a=a.tabIndex;return ha(a)&&0<=a&&32768>a}
function ae(a){if(gd&&null!==a&&"innerText"in a)a=a.innerText.replace(/(\r\n|\r|\n)/g,"\n");else{var b=[];be(a,b,!0);a=b.join("")}a=a.replace(/ \xAD /g," ").replace(/\xAD/g,"");a=a.replace(/\u200B/g,"");gd||(a=a.replace(/ +/g," "));" "!=a&&(a=a.replace(/^\s*/,""));return a}
function ce(a){var b=[];be(a,b,!1);return b.join("")}
function be(a,b,c){if(!(a.nodeName in Wd))if(3==a.nodeType)c?b.push(String(a.nodeValue).replace(/(\r\n|\r|\n)/g,"")):b.push(a.nodeValue);else if(a.nodeName in Xd)b.push(Xd[a.nodeName]);else for(a=a.firstChild;a;)be(a,b,c),a=a.nextSibling}
function Cd(a){if(a&&"number"==typeof a.length){if(ka(a))return"function"==typeof a.item||"string"==typeof a.item;if(ia(a))return"function"==typeof a.item}return!1}
function de(a,b,c,d){if(!b&&!c)return null;var e=b?b.toUpperCase():null;return ee(a,function(a){return(!e||a.nodeName==e)&&(!c||t(a.className)&&fb(a.className.split(/\s+/),c))},!0,d)}
function I(a,b){return de(a,null,b,void 0)}
function ee(a,b,c,d){c||(a=a.parentNode);for(c=0;a&&(null==d||c<=d);){if(b(a))return a;a=a.parentNode;c++}return null}
function nd(a){this.f=a||m.document||document}
g=nd.prototype;g.Od=function(a){return t(a)?this.f.getElementById(a):a};
g.Ff=function(a,b,c){return Ad(this.f,arguments)};
g.createElement=function(a){return this.f.createElement(a)};
g.appendChild=function(a,b){a.appendChild(b)};
g.isElement=Qd;g.contains=Sd;var fe="StopIteration"in m?m.StopIteration:{message:"StopIteration",stack:""};function ge(){}
ge.prototype.next=function(){throw fe;};
ge.prototype.Ka=function(){return this};
function he(a){if(a instanceof ge)return a;if("function"==typeof a.Ka)return a.Ka(!1);if(ga(a)){var b=0,c=new ge;c.next=function(){for(;;){if(b>=a.length)throw fe;if(b in a)return a[b++];b++}};
return c}throw Error("Not implemented");}
function ie(a,b,c){if(ga(a))try{x(a,b,c)}catch(d){if(d!==fe)throw d;}else{a=he(a);try{for(;;)b.call(c,a.next(),void 0,a)}catch(d){if(d!==fe)throw d;}}}
function je(a){if(ga(a))return pb(a);a=he(a);var b=[];ie(a,function(a){b.push(a)});
return b}
;function ke(a,b){this.j={};this.f=[];this.$a=this.l=0;var c=arguments.length;if(1<c){if(c%2)throw Error("Uneven number of arguments");for(var d=0;d<c;d+=2)this.set(arguments[d],arguments[d+1])}else if(a){a instanceof ke?(c=a.Ea(),d=a.ha()):(c=Jb(a),d=Ib(a));for(var e=0;e<c.length;e++)this.set(c[e],d[e])}}
g=ke.prototype;g.fa=function(){return this.l};
g.ha=function(){le(this);for(var a=[],b=0;b<this.f.length;b++)a.push(this.j[this.f[b]]);return a};
g.Ea=function(){le(this);return this.f.concat()};
g.Cb=function(a){for(var b=0;b<this.f.length;b++){var c=this.f[b];if(me(this.j,c)&&this.j[c]==a)return!0}return!1};
g.equals=function(a,b){if(this===a)return!0;if(this.l!=a.fa())return!1;var c=b||ne;le(this);for(var d,e=0;d=this.f[e];e++)if(!c(this.get(d),a.get(d)))return!1;return!0};
function ne(a,b){return a===b}
g.isEmpty=function(){return 0==this.l};
g.clear=function(){this.j={};this.$a=this.l=this.f.length=0};
g.remove=function(a){return me(this.j,a)?(delete this.j[a],this.l--,this.$a++,this.f.length>2*this.l&&le(this),!0):!1};
function le(a){if(a.l!=a.f.length){for(var b=0,c=0;b<a.f.length;){var d=a.f[b];me(a.j,d)&&(a.f[c++]=d);b++}a.f.length=c}if(a.l!=a.f.length){for(var e={},c=b=0;b<a.f.length;)d=a.f[b],me(e,d)||(a.f[c++]=d,e[d]=1),b++;a.f.length=c}}
g.get=function(a,b){return me(this.j,a)?this.j[a]:b};
g.set=function(a,b){me(this.j,a)||(this.l++,this.f.push(a),this.$a++);this.j[a]=b};
g.forEach=function(a,b){for(var c=this.Ea(),d=0;d<c.length;d++){var e=c[d],f=this.get(e);a.call(b,f,e,this)}};
g.clone=function(){return new ke(this)};
function oe(a){le(a);for(var b={},c=0;c<a.f.length;c++){var d=a.f[c];b[d]=a.j[d]}return b}
g.Ka=function(a){le(this);var b=0,c=this.$a,d=this,e=new ge;e.next=function(){if(c!=d.$a)throw Error("The map has changed since the iterator was created");if(b>=d.f.length)throw fe;var e=d.f[b++];return a?e:d.j[e]};
return e};
function me(a,b){return Object.prototype.hasOwnProperty.call(a,b)}
;function pe(a){var b=new ke;qe(a,b,re);return b}
function se(a){var b=[];qe(a,b,te);return b.join("&")}
function qe(a,b,c){for(var d=a.elements,e,f=0;e=d[f];f++)if(e.form==a&&!e.disabled&&"FIELDSET"!=e.tagName){var h=e.name;switch(e.type.toLowerCase()){case "file":case "submit":case "reset":case "button":break;case "select-multiple":e=ue(e);if(null!=e)for(var k,l=0;k=e[l];l++)c(b,h,k);break;default:k=ue(e),null!=k&&c(b,h,k)}}d=a.getElementsByTagName("INPUT");for(f=0;e=d[f];f++)e.form==a&&"image"==e.type.toLowerCase()&&(h=e.name,c(b,h,e.value),c(b,h+".x","0"),c(b,h+".y","0"))}
function re(a,b,c){var d=a.get(b);d||(d=[],a.set(b,d));d.push(c)}
function te(a,b,c){a.push(encodeURIComponent(b)+"="+encodeURIComponent(c))}
function ue(a){var b=a.type;if(!n(b))return null;switch(b.toLowerCase()){case "checkbox":case "radio":return a.checked?a.value:null;case "select-one":return b=a.selectedIndex,0<=b?a.options[b].value:null;case "select-multiple":for(var b=[],c,d=0;c=a.options[d];d++)c.selected&&b.push(c.value);return b.length?b:null;default:return n(a.value)?a.value:null}}
;function ve(a,b,c){a&&(a.dataset?a.dataset[we(b)]=c:a.setAttribute("data-"+b,c))}
function J(a,b){return a?a.dataset?a.dataset[we(b)]:a.getAttribute("data-"+b):null}
function xe(a,b){a&&(a.dataset?delete a.dataset[we(b)]:a.removeAttribute("data-"+b))}
function ye(a,b){return a?a.dataset?we(b)in a.dataset:a.hasAttribute?!!a.hasAttribute("data-"+b):!!a.getAttribute("data-"+b):!1}
var ze={};function we(a){return ze[a]||(ze[a]=String(a).replace(/\-([a-z])/g,function(a,c){return c.toUpperCase()}))}
;var Ae=Rc?"webkit":Qc?"moz":F?"ms":Nc?"o":"";function Be(a){var b=a.__yt_uid_key;b||(b=Ce(),a.__yt_uid_key=b);return b}
var Ce=r("yt.dom.getNextId_");if(!Ce){Ce=function(){return++De};
p("yt.dom.getNextId_",Ce,void 0);var De=0}function Ee(a){var b=a.cloneNode(!1);"TR"==b.tagName||"SELECT"==b.tagName?x(a.childNodes,function(a){b.appendChild(Ee(a))}):b.innerHTML=a.innerHTML;
return b}
function Fe(a){a=Ee(G(a));a.removeAttribute("id");return a}
function Ge(a,b){var c=Ee(a);for(Fd(b);0<c.childNodes.length;)b.appendChild(c.childNodes[0])}
function He(a,b,c){a=G(a);b=G(b);return!!ee(a,function(a){return a===b},!0,c)}
function Ie(a,b){var c=rd(a,null,b);return c.length?c[0]:null}
function Je(a){a=a.replace(/^[\s\xa0]+/,"");var b=ua(a);b&&(a="<table>"+a+"</table>");a=Dd(Hc(a));var c=document.createDocumentFragment();if(b)return b=rd("tr",null,a),x(b,function(a){c.appendChild(a)}),c;
c.appendChild(a);return c}
function Ke(){var a=document,b;ab(["fullscreenElement","fullScreenElement"],function(c){c in a?b=a[c]:(c=Ae+c.charAt(0).toUpperCase()+c.substr(1),b=c in a?a[c]:void 0);return!!b});
return b}
function Le(a){D(document.body,"hide-players",!0);a&&D(a,"preserve-players",!0)}
function Me(){D(document.body,"hide-players",!1);var a=qd("preserve-players");x(a,function(a){B(a,"preserve-players")})}
;function Ne(a,b,c,d){this.top=a;this.right=b;this.bottom=c;this.left=d}
g=Ne.prototype;g.getHeight=function(){return this.bottom-this.top};
g.clone=function(){return new Ne(this.top,this.right,this.bottom,this.left)};
g.contains=function(a){return Oe(this,a)};
function Oe(a,b){return a&&b?b instanceof Ne?b.left>=a.left&&b.right<=a.right&&b.top>=a.top&&b.bottom<=a.bottom:b.x>=a.left&&b.x<=a.right&&b.y>=a.top&&b.y<=a.bottom:!1}
function Pe(a,b){return a.left<=b.right&&b.left<=a.right&&a.top<=b.bottom&&b.top<=a.bottom}
g.ceil=function(){this.top=Math.ceil(this.top);this.right=Math.ceil(this.right);this.bottom=Math.ceil(this.bottom);this.left=Math.ceil(this.left);return this};
g.floor=function(){this.top=Math.floor(this.top);this.right=Math.floor(this.right);this.bottom=Math.floor(this.bottom);this.left=Math.floor(this.left);return this};
g.round=function(){this.top=Math.round(this.top);this.right=Math.round(this.right);this.bottom=Math.round(this.bottom);this.left=Math.round(this.left);return this};function Qe(){return Rc?"Webkit":Qc?"Moz":F?"ms":Nc?"O":null}
function Re(){return Rc?"-webkit":Qc?"-moz":F?"-ms":Nc?"-o":null}
;function Se(a,b,c,d){this.left=a;this.top=b;this.width=c;this.height=d}
g=Se.prototype;g.clone=function(){return new Se(this.left,this.top,this.width,this.height)};
g.contains=function(a){return a instanceof Se?this.left<=a.left&&this.left+this.width>=a.left+a.width&&this.top<=a.top&&this.top+this.height>=a.top+a.height:a.x>=this.left&&a.x<=this.left+this.width&&a.y>=this.top&&a.y<=this.top+this.height};
g.ceil=function(){this.left=Math.ceil(this.left);this.top=Math.ceil(this.top);this.width=Math.ceil(this.width);this.height=Math.ceil(this.height);return this};
g.floor=function(){this.left=Math.floor(this.left);this.top=Math.floor(this.top);this.width=Math.floor(this.width);this.height=Math.floor(this.height);return this};
g.round=function(){this.left=Math.round(this.left);this.top=Math.round(this.top);this.width=Math.round(this.width);this.height=Math.round(this.height);return this};function Te(a){Te[" "](a);return a}
Te[" "]=ca;function Ue(a,b){try{return Te(a[b]),!0}catch(c){}return!1}
;function Ve(a,b,c){if(t(b))(b=We(a,b))&&(a.style[b]=c);else for(var d in b){c=a;var e=b[d],f=We(c,d);f&&(c.style[f]=e)}}
var Xe={};function We(a,b){var c=Xe[b];if(!c){var d=Ua(b),c=d;void 0===a.style[d]&&(d=Qe()+Va(d),void 0!==a.style[d]&&(c=d));Xe[b]=c}return c}
function Ye(a,b){var c=od(a);return c.defaultView&&c.defaultView.getComputedStyle&&(c=c.defaultView.getComputedStyle(a,null))?c[b]||c.getPropertyValue(b)||"":""}
function Ze(a,b){return Ye(a,b)||(a.currentStyle?a.currentStyle[b]:null)||a.style&&a.style[b]}
function $e(a){var b;try{b=a.getBoundingClientRect()}catch(c){return{left:0,top:0,right:0,bottom:0}}F&&a.ownerDocument.body&&(a=a.ownerDocument,b.left-=a.documentElement.clientLeft+a.body.clientLeft,b.top-=a.documentElement.clientTop+a.body.clientTop);return b}
function af(a){if(F&&!bd(8))return a.offsetParent;var b=od(a),c=Ze(a,"position"),d="fixed"==c||"absolute"==c;for(a=a.parentNode;a&&a!=b;a=a.parentNode)if(11==a.nodeType&&a.host&&(a=a.host),c=Ze(a,"position"),d=d&&"static"==c&&a!=b.documentElement&&a!=b.body,!d&&(a.scrollWidth>a.clientWidth||a.scrollHeight>a.clientHeight||"fixed"==c||"absolute"==c||"relative"==c))return a;return null}
function bf(a){for(var b=new Ne(0,Infinity,Infinity,0),c=md(a),d=c.f.body,e=c.f.documentElement,f=xd(c.f);a=af(a);)if(!(F&&0==a.clientWidth||Rc&&0==a.clientHeight&&a==d)&&a!=d&&a!=e&&"visible"!=Ze(a,"overflow")){var h=cf(a),k=new Jc(a.clientLeft,a.clientTop);h.x+=k.x;h.y+=k.y;b.top=Math.max(b.top,h.y);b.right=Math.min(b.right,h.x+a.clientWidth);b.bottom=Math.min(b.bottom,h.y+a.clientHeight);b.left=Math.max(b.left,h.x)}d=f.scrollLeft;f=f.scrollTop;b.left=Math.max(b.left,d);b.top=Math.max(b.top,f);
c=ud(yd(c.f)||window);b.right=Math.min(b.right,d+c.width);b.bottom=Math.min(b.bottom,f+c.height);return 0<=b.top&&0<=b.left&&b.bottom>b.top&&b.right>b.left?b:null}
function cf(a){var b=od(a),c=new Jc(0,0),d;d=b?od(b):document;d=!F||bd(9)||vd(md(d).f)?d.documentElement:d.body;if(a==d)return c;a=$e(a);b=wd(md(b).f);c.x=a.left+b.x;c.y=a.top+b.y;return c}
function df(a){return cf(a).x}
function ef(a){a=$e(a);return new Jc(a.left,a.top)}
function ff(a,b){"number"==typeof a&&(a=(b?Math.round(a):a)+"px");return a}
function gf(a,b){if("none"!=Ze(b,"display"))return a(b);var c=b.style,d=c.display,e=c.visibility,f=c.position;c.visibility="hidden";c.position="absolute";c.display="inline";var h=a(b);c.display=d;c.position=f;c.visibility=e;return h}
function hf(a){var b=a.offsetWidth,c=a.offsetHeight,d=Rc&&!b&&!c;return n(b)&&!d||!a.getBoundingClientRect?new Lc(b,c):(a=$e(a),new Lc(a.right-a.left,a.bottom-a.top))}
function jf(a){if(!a.getBoundingClientRect)return null;a=gf($e,a);return new Lc(a.right-a.left,a.bottom-a.top)}
function kf(a){var b=cf(a);a=gf(hf,a);return new Se(b.x,b.y,a.width,a.height)}
function lf(a,b){a.style.display=b?"":"none"}
function mf(a){return"rtl"==Ze(a,"direction")}
function nf(a,b){if(/^\d+px?$/.test(b))return parseInt(b,10);var c=a.style.left,d=a.runtimeStyle.left;a.runtimeStyle.left=a.currentStyle.left;a.style.left=b;var e=a.style.pixelLeft;a.style.left=c;a.runtimeStyle.left=d;return e}
function of(a,b){var c=a.currentStyle?a.currentStyle[b]:null;return c?nf(a,c):0}
var pf={thin:2,medium:4,thick:6};function qf(a,b){if("none"==(a.currentStyle?a.currentStyle[b+"Style"]:null))return 0;var c=a.currentStyle?a.currentStyle[b+"Width"]:null;return c in pf?pf[c]:nf(a,c)}
;var rf=window.yt&&window.yt.config_||window.ytcfg&&window.ytcfg.data_||{};p("yt.config_",rf,void 0);p("yt.tokens_",window.yt&&window.yt.tokens_||{},void 0);var sf=window.yt&&window.yt.msgs_||r("window.ytcfg.msgs")||{};p("yt.msgs_",sf,void 0);function tf(a){uf(rf,arguments)}
function K(a,b){return a in rf?rf[a]:b}
function L(a,b){ia(a)&&(a=vf(a));return window.setTimeout(a,b)}
function wf(a,b){ia(a)&&(a=vf(a));return window.setInterval(a,b)}
function M(a){window.clearTimeout(a)}
function xf(a){window.clearInterval(a)}
function vf(a){return a&&window.yterr?function(){try{return a.apply(this,arguments)}catch(b){throw yf(b),b;}}:a}
function yf(a,b){var c=r("yt.logging.errors.log");c?c(a,b,void 0,void 0):(c=K("ERRORS",[]),c.push([a,b,void 0,void 0]),tf("ERRORS",c))}
function zf(a,b,c){var d=b||{};if(a=a in sf?sf[a]:c)for(var e in d)a=a.replace(new RegExp("\\$"+e,"gi"),function(){return d[e]});
return a}
function Af(a){var b="CHARACTERS_REMAINING"in sf?sf.CHARACTERS_REMAINING:{},c=K("I18N_PLURAL_RULES")||function(a){return 1==a?"one":"other"};
return(b=b["case"+a]||b[c(a)])?b.replace("#",a.toString()):a+""}
function uf(a,b){if(1<b.length){var c=b[0];a[c]=b[1]}else{var d=b[0];for(c in d)a[c]=d[c]}}
var Bf=window.performance&&window.performance.timing&&window.performance.now&&window.__yt_experimental_now?function(){return window.performance.timing.navigationStart+window.performance.now()}:function(){return(new Date).getTime()},Cf="Microsoft Internet Explorer"==navigator.appName;function Df(a){this.type="";this.state=this.source=this.data=this.currentTarget=this.relatedTarget=this.target=null;this.charCode=this.keyCode=0;this.shiftKey=this.ctrlKey=this.altKey=!1;this.clientY=this.clientX=0;this.changedTouches=null;if(a=a||window.event){this.event=a;for(var b in a)b in Ef||(this[b]=a[b]);(b=a.target||a.srcElement)&&3==b.nodeType&&(b=b.parentNode);this.target=b;if(b=a.relatedTarget)try{b=b.nodeName?b:null}catch(c){b=null}else"mouseover"==this.type?b=a.fromElement:"mouseout"==
this.type&&(b=a.toElement);this.relatedTarget=b;this.clientX=void 0!=a.clientX?a.clientX:a.pageX;this.clientY=void 0!=a.clientY?a.clientY:a.pageY;this.keyCode=a.keyCode?a.keyCode:a.which;this.charCode=a.charCode||("keypress"==this.type?this.keyCode:0);this.altKey=a.altKey;this.ctrlKey=a.ctrlKey;this.shiftKey=a.shiftKey;this.f=a.pageX;this.j=a.pageY}}
function Ff(a){if(document.body&&document.documentElement){var b=document.body.scrollTop+document.documentElement.scrollTop;a.f=a.clientX+(document.body.scrollLeft+document.documentElement.scrollLeft);a.j=a.clientY+b}}
Df.prototype.preventDefault=function(){this.event&&(this.event.returnValue=!1,this.event.preventDefault&&this.event.preventDefault())};
Df.prototype.stopPropagation=function(){this.event&&(this.event.cancelBubble=!0,this.event.stopPropagation&&this.event.stopPropagation())};
Df.prototype.stopImmediatePropagation=function(){this.event&&(this.event.cancelBubble=!0,this.event.stopImmediatePropagation&&this.event.stopImmediatePropagation())};
var Ef={stopImmediatePropagation:1,stopPropagation:1,preventMouseEvent:1,preventManipulation:1,preventDefault:1,layerX:1,layerY:1,scale:1,rotation:1,webkitMovementX:1,webkitMovementY:1};var Gf=r("yt.events.listeners_")||{};p("yt.events.listeners_",Gf,void 0);var Hf=r("yt.events.counter_")||{count:0};p("yt.events.counter_",Hf,void 0);function If(a,b,c,d){return Lb(Gf,function(e){return e[0]==a&&e[1]==b&&e[2]==c&&e[4]==!!d})}
function N(a,b,c,d){if(!a||!a.addEventListener&&!a.attachEvent)return"";d=!!d;var e=If(a,b,c,d);if(e)return e;var e=++Hf.count+"",f=!("mouseenter"!=b&&"mouseleave"!=b||!a.addEventListener||"onmouseenter"in document),h;h=f?function(d){d=new Df(d);if(!ee(d.relatedTarget,function(b){return b==a},!0))return d.currentTarget=a,d.type=b,c.call(a,d)}:function(b){b=new Df(b);
b.currentTarget=a;return c.call(a,b)};
h=vf(h);Gf[e]=[a,b,c,h,d];a.addEventListener?"mouseenter"==b&&f?a.addEventListener("mouseover",h,d):"mouseleave"==b&&f?a.addEventListener("mouseout",h,d):"mousewheel"==b&&"MozBoxSizing"in document.documentElement.style?a.addEventListener("MozMousePixelScroll",h,d):a.addEventListener(b,h,d):a.attachEvent("on"+b,h);return e}
function Jf(a,b,c){var d;return d=N(a,b,function(){O(d);c.apply(a,arguments)},void 0)}
function P(a,b,c,d){return Kf(a,b,c,function(a){return y(a,d)})}
function Kf(a,b,c,d){var e=a||document;return N(e,b,function(a){var b=ee(a.target,function(a){return a===e||d(a)},!0);
b&&b!==e&&!b.disabled&&(a.currentTarget=b,c.call(b,a))})}
function Lf(a,b,c,d){(a=If(a,b,c,!!d))&&O(a)}
function O(a){a&&("string"==typeof a&&(a=[a]),x(a,function(a){if(a in Gf){var c=Gf[a],d=c[0],e=c[1],f=c[3],c=c[4];d.removeEventListener?d.removeEventListener(e,f,c):d.detachEvent&&d.detachEvent("on"+e,f);delete Gf[a]}}))}
function Mf(a){for(var b in Gf)Gf[b][0]==a&&O(b)}
function Nf(a){a=a||window.event;a=a.target||a.srcElement;3==a.nodeType&&(a=a.parentNode);return a}
function Of(a){a=a||window.event;a.cancelBubble=!0;a.stopPropagation&&a.stopPropagation()}
function Pf(a){a=a||window.event;a.returnValue=!1;a.preventDefault&&a.preventDefault();return!1}
function Qf(a,b){if(document.createEvent){var c=document.createEvent("HTMLEvents");c.initEvent(b,!0,!0);a.dispatchEvent(c)}else c=document.createEventObject(),a.fireEvent("on"+b,c)}
;function Rf(a){m.setTimeout(function(){throw a;},0)}
var Sf;
function Tf(){var a=m.MessageChannel;"undefined"===typeof a&&"undefined"!==typeof window&&window.postMessage&&window.addEventListener&&!E("Presto")&&(a=function(){var a=document.createElement("IFRAME");a.style.display="none";a.src="";document.documentElement.appendChild(a);var b=a.contentWindow,a=b.document;a.open();a.write("");a.close();var c="callImmediate"+Math.random(),d="file:"==b.location.protocol?"*":b.location.protocol+"//"+b.location.host,a=u(function(a){if(("*"==d||a.origin==d)&&a.data==
c)this.port1.onmessage()},this);
b.addEventListener("message",a,!1);this.port1={};this.port2={postMessage:function(){b.postMessage(c,d)}}});
if("undefined"!==typeof a&&!E("Trident")&&!E("MSIE")){var b=new a,c={},d=c;b.port1.onmessage=function(){if(n(c.next)){c=c.next;var a=c.Bd;c.Bd=null;a()}};
return function(a){d.next={Bd:a};d=d.next;b.port2.postMessage(0)}}return"undefined"!==typeof document&&"onreadystatechange"in document.createElement("SCRIPT")?function(a){var b=document.createElement("SCRIPT");
b.onreadystatechange=function(){b.onreadystatechange=null;b.parentNode.removeChild(b);b=null;a();a=null};
document.documentElement.appendChild(b)}:function(a){m.setTimeout(a,0)}}
;function Uf(a,b,c){this.B=c;this.l=a;this.A=b;this.j=0;this.f=null}
Uf.prototype.get=function(){var a;0<this.j?(this.j--,a=this.f,this.f=a.next,a.next=null):a=this.l();return a};
function Vf(a,b){a.A(b);a.j<a.B&&(a.j++,b.next=a.f,a.f=b)}
;function Wf(){this.j=this.f=null}
var Yf=new Uf(function(){return new Xf},function(a){a.reset()},100);
Wf.prototype.remove=function(){var a=null;this.f&&(a=this.f,this.f=this.f.next,this.f||(this.j=null),a.next=null);return a};
function Xf(){this.next=this.scope=this.Oa=null}
Xf.prototype.set=function(a,b){this.Oa=a;this.scope=b;this.next=null};
Xf.prototype.reset=function(){this.next=this.scope=this.Oa=null};function Zf(a,b){$f||ag();bg||($f(),bg=!0);var c=cg,d=Yf.get();d.set(a,b);c.j?c.j.next=d:c.f=d;c.j=d}
var $f;function ag(){if(m.Promise&&m.Promise.resolve){var a=m.Promise.resolve(void 0);$f=function(){a.then(dg)}}else $f=function(){var a=dg;
!ia(m.setImmediate)||m.Window&&m.Window.prototype&&!E("Edge")&&m.Window.prototype.setImmediate==m.setImmediate?(Sf||(Sf=Tf()),Sf(a)):m.setImmediate(a)}}
var bg=!1,cg=new Wf;function dg(){for(var a=null;a=cg.remove();){try{a.Oa.call(a.scope)}catch(b){Rf(b)}Vf(Yf,a)}bg=!1}
;function eg(){this.U=this.U;this.N=this.N}
eg.prototype.U=!1;eg.prototype.isDisposed=function(){return this.U};
eg.prototype.dispose=function(){this.U||(this.U=!0,this.M())};
function fg(a,b){a.U?b.call(void 0):(a.N||(a.N=[]),a.N.push(n(void 0)?u(b,void 0):b))}
eg.prototype.M=function(){if(this.N)for(;this.N.length;)this.N.shift()()};
function gg(a){a&&"function"==typeof a.dispose&&a.dispose()}
;function hg(a){eg.call(this);this.A=1;this.j=[];this.l=0;this.f=[];this.ua={};this.B=!!a}
w(hg,eg);g=hg.prototype;g.subscribe=function(a,b,c){var d=this.ua[a];d||(d=this.ua[a]=[]);var e=this.A;this.f[e]=a;this.f[e+1]=b;this.f[e+2]=c;this.A=e+3;d.push(e);return e};
function ig(a,b){var c=!1,d=a.subscribe("ROOT_MENU_REMOVED",function(a){c||(c=!0,this.va(d),b.apply(void 0,arguments))},a)}
g.unsubscribe=function(a,b,c){if(a=this.ua[a]){var d=this.f;if(a=cb(a,function(a){return d[a+1]==b&&d[a+2]==c}))return this.va(a)}return!1};
g.va=function(a){var b=this.f[a];if(b){var c=this.ua[b];0!=this.l?(this.j.push(a),this.f[a+1]=ca):(c&&kb(c,a),delete this.f[a],delete this.f[a+1],delete this.f[a+2])}return!!b};
g.K=function(a,b){var c=this.ua[a];if(c){for(var d=Array(arguments.length-1),e=1,f=arguments.length;e<f;e++)d[e-1]=arguments[e];if(this.B)for(e=0;e<c.length;e++){var h=c[e];jg(this.f[h+1],this.f[h+2],d)}else{this.l++;try{for(e=0,f=c.length;e<f;e++)h=c[e],this.f[h+1].apply(this.f[h+2],d)}finally{if(this.l--,0<this.j.length&&0==this.l)for(;c=this.j.pop();)this.va(c)}}return 0!=e}return!1};
function jg(a,b,c){Zf(function(){a.apply(b,c)})}
g.clear=function(a){if(a){var b=this.ua[a];b&&(x(b,this.va,this),delete this.ua[a])}else this.f.length=0,this.ua={}};
g.fa=function(a){if(a){var b=this.ua[a];return b?b.length:0}a=0;for(b in this.ua)a+=this.fa(b);return a};
g.M=function(){hg.J.M.call(this);this.clear();this.j.length=0};var kg=r("yt.pubsub.instance_")||new hg;hg.prototype.subscribe=hg.prototype.subscribe;hg.prototype.unsubscribeByKey=hg.prototype.va;hg.prototype.publish=hg.prototype.K;hg.prototype.clear=hg.prototype.clear;p("yt.pubsub.instance_",kg,void 0);var lg=r("yt.pubsub.subscribedKeys_")||{};p("yt.pubsub.subscribedKeys_",lg,void 0);var mg=r("yt.pubsub.topicToKeys_")||{};p("yt.pubsub.topicToKeys_",mg,void 0);var ng=r("yt.pubsub.isSynchronous_")||{};p("yt.pubsub.isSynchronous_",ng,void 0);
var og=r("yt.pubsub.skipSubId_")||null;p("yt.pubsub.skipSubId_",og,void 0);function Q(a,b,c){var d=pg();if(d){var e=d.subscribe(a,function(){if(!og||og!=e){var d=arguments,h=function(){lg[e]&&b.apply(c||window,d)};
try{ng[a]?h():L(h,0)}catch(k){yf(k)}}},c);
lg[e]=!0;mg[a]||(mg[a]=[]);mg[a].push(e);return e}return 0}
function qg(a,b){var c=Q("dispose",function(d){a.apply(b,arguments);rg(c)},b)}
function rg(a){var b=pg();b&&("number"==typeof a?a=[a]:"string"==typeof a&&(a=[parseInt(a,10)]),x(a,function(a){b.unsubscribeByKey(a);delete lg[a]}))}
function R(a,b){var c=pg();return c?c.publish.apply(c,arguments):!1}
function sg(a,b){ng[a]=!0;var c=pg(),c=c?c.publish.apply(c,arguments):!1;ng[a]=!1;return c}
function tg(a){mg[a]&&(a=mg[a],x(a,function(a){lg[a]&&delete lg[a]}),a.length=0)}
function ug(a){var b=pg();if(b)if(b.clear(a),a)tg(a);else for(var c in mg)tg(c)}
function pg(){return r("yt.pubsub.instance_")}
;function vg(a,b){isNaN(b)&&(b=void 0);var c=r("yt.scheduler.instance.addJob");return c?c(a,0,b):void 0===b?(a(),NaN):L(a,b||0)}
;function wg(a){this.f=a}
var xg=/\s*;\s*/;g=wg.prototype;g.isEnabled=function(){return navigator.cookieEnabled};
g.set=function(a,b,c,d,e,f){if(/[;=\s]/.test(a))throw Error('Invalid cookie name "'+a+'"');if(/[;\r\n]/.test(b))throw Error('Invalid cookie value "'+b+'"');n(c)||(c=-1);e=e?";domain="+e:"";d=d?";path="+d:"";f=f?";secure":"";c=0>c?"":0==c?";expires="+(new Date(1970,1,1)).toUTCString():";expires="+(new Date(v()+1E3*c)).toUTCString();this.f.cookie=a+"="+b+e+d+c+f};
g.get=function(a,b){for(var c=a+"=",d=(this.f.cookie||"").split(xg),e=0,f;f=d[e];e++){if(0==f.lastIndexOf(c,0))return f.substr(c.length);if(f==a)return""}return b};
g.remove=function(a,b,c){var d=n(this.get(a));this.set(a,"",0,b,c);return d};
g.Ea=function(){return yg(this).keys};
g.ha=function(){return yg(this).values};
g.isEmpty=function(){return!this.f.cookie};
g.fa=function(){return this.f.cookie?(this.f.cookie||"").split(xg).length:0};
g.Cb=function(a){for(var b=yg(this).values,c=0;c<b.length;c++)if(b[c]==a)return!0;return!1};
g.clear=function(){for(var a=yg(this).keys,b=a.length-1;0<=b;b--)this.remove(a[b])};
function yg(a){a=(a.f.cookie||"").split(xg);for(var b=[],c=[],d,e,f=0;e=a[f];f++)d=e.indexOf("="),-1==d?(b.push(""),c.push(e)):(b.push(e.substring(0,d)),c.push(e.substring(d+1)));return{keys:b,values:c}}
var zg=new wg(document);zg.j=3950;function Ag(a,b,c,d,e){zg.set(""+a,b,c,d,e||"youtube.com")}
function Bg(a,b){return zg.get(""+a,b)}
function Cg(a,b,c){return zg.remove(""+a,b||"/",c||"youtube.com")}
;function Dg(){var a=Bg("PREF");if(a)for(var a=unescape(a).split("&"),b=0;b<a.length;b++){var c=a[b].split("="),d=c[0];(c=c[1])&&(Eg[d]=c.toString())}}
da(Dg);var Eg=r("yt.prefs.UserPrefs.prefs_")||{};p("yt.prefs.UserPrefs.prefs_",Eg,void 0);function Fg(a){if(/^f([1-9][0-9]*)$/.test(a))throw"ExpectedRegexMatch: "+a;}
function Gg(a){if(!/^\w+$/.test(a))throw"ExpectedRegexMismatch: "+a;}
function Hg(a){a=void 0!==Eg[a]?Eg[a].toString():null;return null!=a&&/^[A-Fa-f0-9]+$/.test(a)?parseInt(a,16):null}
Dg.prototype.get=function(a,b){Gg(a);Fg(a);var c=void 0!==Eg[a]?Eg[a].toString():null;return null!=c?c:b?b:""};
Dg.prototype.set=function(a,b){Gg(a);Fg(a);if(null==b)throw"ExpectedNotNull";Eg[a]=b.toString()};
function Ig(a,b){return!!((Hg("f"+(Math.floor(b/31)+1))||0)&1<<b%31)}
Dg.prototype.remove=function(a){Gg(a);Fg(a);delete Eg[a]};
function Jg(){var a;a=[];for(var b in Eg)a.push(b+"="+escape(Eg[b]));a=a.join("&");Ag("PREF",a,63072E3,"/")}
Dg.prototype.clear=function(){Eg={}};function Kg(a){if(!Lg||a)Lg=ud(window);return Lg}
function Mg(){return Ng=wd(document)}
function Og(a){n(a.f)||Ff(a);var b=a.f;n(a.j)||Ff(a);Pg=new Jc(b,a.j)}
function Qg(){var a=Pg;if(a){var b=Rg,c=v();if(0==b.time)b.Gc=Array(4);else{var d=a.x-b.position.x,e=a.y-b.position.y,d=Math.sqrt(d*d+e*e)/(c-b.time);b.Gc[b.index]=.5<Math.abs((d-b.fd)/b.fd)?1:0;for(var f=e=0;4>f;f++)e+=b.Gc[f]||0;3<=e&&!Sg&&(Sg=vg(Tg,100));b.fd=d}b.time=c;b.position=a;b.index=(b.index+1)%4}}
function Tg(){Sg=0;Pg||(Pg=new Jc);sg("page-mouse",Pg)}
function Ug(){Vg||(Vg=vg(Wg,200))}
function Wg(){Vg=0;var a=Kg(!0);sg("page-resize",a)}
function Xg(){Yg||(Yg=vg(Zg,200))}
function Zg(){Yg=0;var a=Mg();sg("page-scroll",a)}
var Lg=null,Ng=null,Pg=null,Rg={time:0,position:null,fd:0,Gc:null,index:0},$g=[],Sg=0,Vg=0,Yg=0;var ah,bh,ch,dh,eh,fh,gh=0,hh=!1;function ih(){eh=jh();kh()}
function lh(){eh=jh();mh()}
function jh(){var a=Mg(),b=Kg();return new Ne(a.y,a.x+b.width-1,a.y+b.height-1,a.x)}
function nh(a){var b=Be(a),c=ah[b];if(c)return c;c=N(a,"scroll",oh);return c=ah[b]={el:a,j:c,xb:null}}
function oh(a){kh(a.target)}
function ph(a,b){var c=[Be(a),b.complete];if(b.transform){var d=b.transform;c.push(d.top,d.right,d.bottom,d.left)}return c.join(":")}
function qh(a,b){var c;b?c=a:c=Rd(a);return c?(c=I(c,"yt-viewport"))?nh(c):null:null}
function rh(a,b){if(a.xb&&!b)return a.xb;var c=sh(a.el),d=qh(a.el);d&&(d=rh(d,b),c=th(c,d));return a.xb=c}
function sh(a){var b=cf(a);a=new Lc(a.offsetWidth,a.offsetHeight);return new Ne(b.y,b.x+a.width-1,b.y+a.height-1,b.x)}
function th(a){var b=[],c=[],d=[],e=[];x(arguments,function(a){b.push(a.top);c.push(a.right);d.push(a.bottom);e.push(a.left)});
var f=Math.max.apply(Math,b),h=Math.min.apply(Math,c),k=Math.min.apply(Math,d),l=Math.max.apply(Math,e);return f>k||l>h?new Ne(0,0,0,0):new Ne(f,h,k,l)}
function uh(a,b){var c=eh,d=sh(a);if(b.transform){var e=b.transform;ka(e)?(d.top-=e.top,d.right+=e.right,d.bottom+=e.bottom,d.left-=e.left):(d.top-=e,d.right+=Number(void 0),d.bottom+=Number(void 0),d.left-=Number(void 0))}var f;b.complete?f=Oe:f=Pe;if(!f.call(Ne,c,d))return!1;e=qh(a);if(!e)return!0;rh(e);c=th(c,e.xb);return f.call(Ne,c,d)}
function vh(a,b,c){var d=ph(a,c);b.hasOwnProperty(d)||(b[d]=uh(a,c));return b[d]}
function wh(a,b,c){a=ph(a,c);if(!!dh[a]!=b)return b?"visible":"hidden"}
function xh(a,b){Db(bh,function(c){if(!b||Sd(b,c.el)){var d=vh(c.el,a,c.options);(d=wh(c.el,d,c.options))&&d==c.type&&L(ra(c.Oa,c.el),0)}})}
function yh(a,b){Db(ch,function(c){if(!b||Sd(c.el,b)||Sd(b,c.el)){var d=c.filter(c.el);if(d&&d.length){var e=[],f=[],h=[];x(d,function(b){var d=vh(b,a,c.options);d?f.push(b):h.push(b);(d=wh(b,d,c.options))&&d==c.type&&e.push(b)});
e.length&&L(ra(c.Oa,e,f,h),0)}}})}
function mh(a){var b={};xh(b,a);yh(b,a);Tb(dh,b)}
function zh(a,b,c,d){return Lb(bh,function(e){return e.el==a&&e.type==b&&e.Oa==c&&Pb(e.options,d)})}
function Ah(a,b,c,d,e){return Lb(ch,function(f){return f.el==a&&f.type==b&&f.Oa==c&&f.className==d&&Pb(f.options,e)})}
function Bh(a,b){var c=qd("yt-viewport",b);x(c,a)}
function Ch(a){Bh(function(a){nh(a)},a);
Qd(a)&&qh(a,!0)}
function Dh(a,b){Db(ah,function(c){b&&!Sd(b,c.el)||b==c.el||a(c)})}
function Eh(a){var b=rh(a,!0);a=a.xb;return!(a==b||a&&b&&a.top==b.top&&a.right==b.right&&a.bottom==b.bottom&&a.left==b.left)}
function kh(a){if(hh){var b;if(a)for(b=qh(a,!0);b&&Eh(b);)b=qh(a);Dh(function(a){delete a.xb},b?b.el:a);
mh(a)}}
p("yt.dom.VisibilityMonitor.refresh",kh,void 0);p("yt.dom.VisibilityMonitor.isVisible",function(a,b){if(!hh)throw Error("yt.dom.VisibilityMonitor is not initialized.");return uh(a,b||{})},void 0);
p("yt.dom.VisibilityMonitor.listen",function(a,b,c,d){if(!hh)return"";d=d||{transform:void 0,complete:void 0};var e=zh(a,b,c,d);if(e)return e;Ch(a);e=++gh+"";bh[e]={el:a,type:b,Oa:c,options:d};return e},void 0);
p("yt.dom.VisibilityMonitor.delegateByClass",function(a,b,c,d,e){if(!hh)return"";a=a||document;e=e||{transform:void 0,complete:void 0};var f=Ah(a,b,c,d,e);if(f)return f;Ch(a);f=++gh+"";ch[f]={el:a,type:b,Oa:c,filter:function(a){return qd(d,a)},
className:d,options:e};return f},void 0);
p("yt.dom.VisibilityMonitor.unlistenByKey",function(a){hh&&(delete bh[a],delete ch[a])},void 0);
p("yt.dom.VisibilityMonitor.States.VISIBLE","visible",void 0);p("yt.dom.VisibilityMonitor.States.HIDDEN","hidden",void 0);var Fh=function(a){var b=!1,c;return function(){b||(c=a(),b=!0);return c}}(function(){if(F)return ad("10.0");
var a=document.createElement("DIV"),b=Re(),c={transition:"opacity 1s linear"};b&&(c[b+"-transition"]="opacity 1s linear");id(a,Cc("div",{style:c}));a=a.firstChild;b=a.style[Ua("transition")];return""!=("undefined"!==typeof b?b:a.style[We(a,"transition")]||"")});function Gh(a,b){(a=G(a))&&a.style&&(lf(a,b),D(a,"hid",!b))}
function Hh(a){return(a=G(a))?!("none"==a.style.display||y(a,"hid")):!1}
function Ih(a){if(a=G(a))Hh(a)?(lf(a,!1),A(a,"hid")):(lf(a,!0),B(a,"hid"))}
function S(a){x(arguments,function(a){!ga(a)||a instanceof Element?Gh(a,!0):x(a,function(a){S(a)})})}
function T(a){x(arguments,function(a){!ga(a)||a instanceof Element?Gh(a,!1):x(a,function(a){T(a)})})}
function Jh(a){x(arguments,function(a){ga(a)?x(a,function(a){Jh(a)}):Ih(a)})}
var Kh={};function Lh(a){if(a in Kh)return Kh[a];var b;if((b=document.body.style)&&a in b)b=a;else{var c=Qe();c?(c=c.toLowerCase(),c+=Va(a),b=!n(b)||c in b?c:null):b=null}return Kh[a]=b}
function Mh(a){if(!Fh())return null;var b=Lh("transitionProperty"),b=Ye(a,b),c=Lh("transitionDuration");a=Ye(a,c);if(!b||!a)return null;var d={},e=b.split(",");x(a.split(","),function(a,b){var c=e[b].trim();c&&(d[c]=-1<a.indexOf("ms")?parseInt(a,10):Math.round(1E3*parseFloat(a)))});
return d}
;var Nh=/^(?:([^:/?#.]+):)?(?:\/\/(?:([^/?#]*)@)?([^/#?]*?)(?::([0-9]+))?(?=[/#?]|$))?([^?#]+)?(?:\?([^#]*))?(?:#(.*))?$/;function Oh(a){return a?decodeURI(a):a}
function Ph(a,b){return b.match(Nh)[a]||null}
function Qh(a){return Oh(Ph(3,a))}
function Rh(a,b){if(a)for(var c=a.split("&"),d=0;d<c.length;d++){var e=c[d].indexOf("="),f=null,h=null;0<=e?(f=c[d].substring(0,e),h=c[d].substring(e+1)):f=c[d];b(f,h?Aa(h):"")}}
function Sh(a){if(a[1]){var b=a[0],c=b.indexOf("#");0<=c&&(a.push(b.substr(c)),a[0]=b=b.substr(0,c));c=b.indexOf("?");0>c?a[1]="?":c==b.length-1&&(a[1]=void 0)}return a.join("")}
function Th(a,b,c){if(fa(b))for(var d=0;d<b.length;d++)Th(a,String(b[d]),c);else null!=b&&c.push("&",a,""===b?"":"=",za(b))}
function Uh(a,b,c){for(c=c||0;c<b.length;c+=2)Th(b[c],b[c+1],a);return a}
function Vh(a,b){for(var c in b)Th(c,b[c],a);return a}
function Wh(a){a=Vh([],a);a[0]="";return a.join("")}
function Xh(a,b){return Sh(2==arguments.length?Uh([a],arguments[1],0):Uh([a],arguments,1))}
function Yh(a,b){return Sh(Vh([a],b))}
;function Zh(){var a=[];Db($h,function(b,c){var d=za(c),e;fa(b)?e=b:e=[b];x(e,function(b){""==b?a.push(d):a.push(d+"="+za(b))})});
return a.join("&")}
function ai(a){"?"==a.charAt(0)&&(a=a.substr(1));a=a.split("&");for(var b={},c=0,d=a.length;c<d;c++){var e=a[c].split("=");if(1==e.length&&e[0]||2==e.length){var f=Aa(e[0]||""),e=Aa(e[1]||"");f in b?fa(b[f])?qb(b[f],e):b[f]=[b[f],e]:b[f]=e}}return b}
function bi(a){return-1!=a.indexOf("?")?(a=(a||"").split("#")[0],a=a.split("?",2),ai(1<a.length?a[1]:a[0])):{}}
function ci(a,b){var c,d=b||{},e=a.split("#",2);c=e[0];var e=1<e.length?"#"+e[1]:"",f=c.split("?",2);c=f[0];var f=ai(f[1]||""),h;for(h in d)f[h]=d[h];return Yh(c,f)+e}
function di(){var a;a||(a=document.location.href);a=Ph(1,a);return null!==a&&"https"==a}
function ei(a){a=fi(a);return gi(a)||hi(a)}
function ii(a){a=fi(a);return gi(a)}
function gi(a){return null===a?!1:"com"==a[0]&&a[1].match(/^youtube(?:-nocookie)?$/)?!0:!1}
function hi(a){return null===a?!1:"google"==a[1]?!0:"google"==a[2]?"au"==a[0]&&"com"==a[1]?!0:"uk"==a[0]&&"co"==a[1]?!0:!1:!1}
function fi(a){a=Qh(a);return null===a?null:a.split(".").reverse()}
;function ji(a){a=a||{};this.url=a.url||"";this.urlV9As2=a.url_v9as2||"";this.args=a.args||Qb(ki);this.assets=a.assets||{};this.attrs=a.attrs||Qb(li);this.params=a.params||Qb(mi);this.minVersion=a.min_version||"8.0.0";this.fallback=a.fallback||null;this.fallbackMessage=a.fallbackMessage||null;this.html5=!!a.html5;this.disable=a.disable||{};this.loaded=!!a.loaded;this.messages=a.messages||{}}
var ki={enablejsapi:1},li={},mi={allowscriptaccess:"always",allowfullscreen:"true",bgcolor:"#000000"};function ni(a){a instanceof ji||(a=new ji(a));return a}
ji.prototype.clone=function(){var a=new ji,b;for(b in this)if(this.hasOwnProperty(b)){var c=this[b];"object"==ea(c)?a[b]=Qb(c):a[b]=c}return a};function oi(){this.l=this.j=this.f=0;this.A="";var a=r("window.navigator.plugins"),b=r("window.navigator.mimeTypes"),a=a&&a["Shockwave Flash"],b=b&&b["application/x-shockwave-flash"],b=a&&b&&b.enabledPlugin&&a.description||"";if(a=b){var c=a.indexOf("Shockwave Flash");0<=c&&(a=a.substr(c+15));for(var c=a.split(" "),d="",a="",e=0,f=c.length;e<f;e++)if(d)if(a)break;else a=c[e];else d=c[e];d=d.split(".");c=parseInt(d[0],10)||0;d=parseInt(d[1],10)||0;e=0;if("r"==a.charAt(0)||"d"==a.charAt(0))e=parseInt(a.substr(1),
10)||0;a=[c,d,e]}else a=[0,0,0];this.A=b;b=a;this.f=b[0];this.j=b[1];this.l=b[2];if(0>=this.f){var h,k,l,q;if(Cf)try{h=new ActiveXObject("ShockwaveFlash.ShockwaveFlash")}catch(z){h=null}else l=document.body,q=document.createElement("object"),q.setAttribute("type","application/x-shockwave-flash"),h=l.appendChild(q);if(h&&"GetVariable"in h)try{k=h.GetVariable("$version")}catch(z){k=""}l&&q&&l.removeChild(q);(h=k||"")?(h=h.split(" ")[1].split(","),h=[parseInt(h[0],10)||0,parseInt(h[1],10)||0,parseInt(h[2],
10)||0]):h=[0,0,0];this.f=h[0];this.j=h[1];this.l=h[2]}}
da(oi);oi.prototype.getVersion=function(){return[this.f,this.j,this.l]};
function pi(a,b,c,d){b="string"==typeof b?b.split("."):[b,c,d];b[0]=parseInt(b[0],10)||0;b[1]=parseInt(b[1],10)||0;b[2]=parseInt(b[2],10)||0;return a.f>b[0]||a.f==b[0]&&a.j>b[1]||a.f==b[0]&&a.j==b[1]&&a.l>=b[2]}
function qi(a){return-1<a.A.indexOf("Gnash")&&-1==a.A.indexOf("AVM2")||9==a.f&&1==a.j||9==a.f&&0==a.j&&1==a.l?!1:9<=a.f}
function ri(a){return Tc?!pi(a,11,2):Sc?!pi(a,11,3):!qi(a)}
;function si(a,b,c){if(b){a=t(a)?pd(a):a;var d=Qb(c.attrs);d.tabindex=0;var e=Qb(c.params);e.flashvars=Wh(c.args);if(Cf){d.classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000";e.movie=b;b=document.createElement("object");for(var f in d)b.setAttribute(f,d[f]);for(f in e)d=document.createElement("param"),d.setAttribute("name",f),d.setAttribute("value",e[f]),b.appendChild(d)}else{d.type="application/x-shockwave-flash";d.src=b;b=document.createElement("embed");b.setAttribute("name",d.id);for(f in d)b.setAttribute(f,
d[f]);for(f in e)b.setAttribute(f,e[f])}e=document.createElement("div");e.appendChild(b);a.innerHTML=e.innerHTML}}
function ti(a,b){a=t(a)?pd(a):a;b=ni(b);if(window!=window.top){var c=null;document.referrer&&(c=document.referrer.substring(0,128));b.args.framer=c}c=oi.getInstance();pi(c,b.minVersion)?(c=ui(b,c),si(a,c,b)):vi(a,b,c)}
function wi(a,b,c){if(a&&a.attrs&&a.attrs.id){a=ni(a);var d=!!b,e=G(a.attrs.id),f=e?e.parentNode:null;if(e&&f){if(window!=window.top){var h=null;if(document.referrer){var k=document.referrer.substring(0,128);ei(k)||(h=k)}else h="unknown";h&&(d=!0,a.args.framer=h)}h=oi.getInstance();if(pi(h,a.minVersion)){var k=ui(a,h),l="";-1<navigator.userAgent.indexOf("Sony/COM2")||(l=e.getAttribute("src")||e.movie);(l!=k||d)&&si(f,k,a);ri(h)&&xi()}else vi(f,a,h);c&&c()}else L(function(){wi(a,b,c)},50)}}
function vi(a,b,c){0==c.f&&b.fallback?b.fallback():0==c.f&&b.fallbackMessage?b.fallbackMessage():a.innerHTML='<div id="flash-upgrade">'+zf("FLASH_UPGRADE",void 0,'You need to upgrade your Adobe Flash Player to watchthis video. <br> <a href="http://get.adobe.com/flashplayer/">Download it from Adobe.</a>')+"</div>"}
function ui(a,b){return qi(b)&&a.url||(-1<navigator.userAgent.indexOf("Sony/COM2")&&!pi(b,9,1,58)?!1:!0)&&a.urlV9As2||a.url}
function xi(){var a=G("flash10-promo-div"),b=Ig(Dg.getInstance(),107);a&&!b&&S(a)}
;var yi;function zi(a,b,c){this.A=this.l=this.f=null;this.j=a;this.C=Ai?b:null;this.F=c||window;this.B=this.F.location;this.H=this.B.href.split("#")[0];this.D=u(this.O,this)}
var Bi=F&&8<=document.documentMode||Qc&&ad("1.9.2")||Rc&&ad("532.1"),Ai=F&&!Bi;zi.prototype.U=function(a,b){this.l&&(O(this.l),delete this.l);this.A&&(xf(this.A),delete this.A);if(a){this.f=Ci(this);if(Ai){var c=this.C.contentWindow.document.body;c&&c.innerHTML||Di(this,this.f)}b||this.j(this.f);Bi?this.l=N(this.F,"hashchange",this.D):this.A=wf(this.D,200)}};
zi.prototype.O=function(){if(Ai){var a;a=(a=this.C.contentWindow.document.body)?Aa(ae(a).substring(1)):"";a!=this.f?(this.f=a,Ei(this,a),this.j(a)):(a=Ci(this),a!=this.f&&(this.f=a,Di(this,a),this.j(a)))}else a=Ci(this),a!=this.f&&(this.f=a,this.j(a))};
function Ci(a){a=a.B.href;var b=a.indexOf("#");return 0>b?"":a.substring(b+1)}
function Ei(a,b){var c=a.H+"#"+b,d=a.B.href;d!=c&&d+"#"!=c&&ld(a.B,c)}
function Di(a,b){var c=a.C.contentWindow.document,d="#"+za(b);if((c.body?c.body.innerHTML:"")!=d){var e=Dc(Cc("title",{},window.document.title||""),Cc("body"));c.open("text/html");c.write(yc(e));Td(c.body,d);c.close()}}
zi.prototype.N=function(a,b,c){this.f=""+a;Ai&&Di(this,a);Ei(this,a);c||this.j(this.f)};function Fi(a,b){this.j=this.C=this.f=null;this.A=a;this.B=b||window;this.l=this.B.location;this.D=u(this.F,this)}
var Gi=!!window.history.pushState&&(!Rc||Rc&&ad("534.11"));Fi.prototype.H=function(a,b){this.j&&(O(this.j),delete this.j);this.C&&(xf(this.C),delete this.C);a&&Gi&&(this.f=this.l.href,b||this.A(this.f),this.j=N(this.B,"popstate",this.D))};
Fi.prototype.F=function(a){var b=this.l.href;if((a=a.state)||b!=this.f)this.f=b,this.A(b,a)};
Fi.prototype.N=function(a,b,c){if(a||b)a=a||this.l.href,this.B.history.pushState(b,"",a),this.f=a,c||this.A(a,b)};
Fi.prototype.replace=function(a,b,c){if(a||b)a=a||this.l.href,this.B.history.replaceState(b,"",a),this.f=a,c||this.A(a,b)};function Hi(a,b){var c=Ii(b);c.setEnabled.call(c,!0,a)}
function Ji(a){var b=Ii();b.replace.call(b,a,window.history&&window.history.state,!0)}
function Ii(a){a=a||"hash";var b=r("yt.history.instance_");b||("state"==a?(b=new Fi(Ki),Fi.prototype.setEnabled=Fi.prototype.H,Fi.prototype.add=Fi.prototype.N,Fi.prototype.replace=Fi.prototype.replace):(b=new zi(Ki,G("legacy-history-iframe")),zi.prototype.setEnabled=zi.prototype.U,zi.prototype.add=zi.prototype.N,zi.prototype.replace=zi.prototype.N),yi=b,p("yt.history.instance_",yi,void 0));return b}
function Ki(a,b){R("navigate",a,b)}
;function Li(a,b){dc(a);dc(a);return Ec(b,null)}
;function Mi(a){return a.fa&&"function"==typeof a.fa?a.fa():ga(a)||t(a)?a.length:Fb(a)}
function Ni(a){if(a.ha&&"function"==typeof a.ha)return a.ha();if(t(a))return a.split("");if(ga(a)){for(var b=[],c=a.length,d=0;d<c;d++)b.push(a[d]);return b}return Ib(a)}
function Oi(a){if(a.Ea&&"function"==typeof a.Ea)return a.Ea();if(!a.ha||"function"!=typeof a.ha){if(ga(a)||t(a)){var b=[];a=a.length;for(var c=0;c<a;c++)b.push(c);return b}return Jb(a)}}
function Pi(a,b,c){if(a.forEach&&"function"==typeof a.forEach)a.forEach(b,c);else if(ga(a)||t(a))x(a,b,c);else for(var d=Oi(a),e=Ni(a),f=e.length,h=0;h<f;h++)b.call(c,e[h],d&&d[h],a)}
function Qi(a,b){if("function"==typeof a.every)return a.every(b,void 0);if(ga(a)||t(a))return bb(a,b,void 0);for(var c=Oi(a),d=Ni(a),e=d.length,f=0;f<e;f++)if(!b.call(void 0,d[f],c&&c[f],a))return!1;return!0}
;function Ri(a){this.f=new ke;if(a){a=Ni(a);for(var b=a.length,c=0;c<b;c++){var d=a[c];this.f.set(Si(d),d)}}}
function Si(a){var b=typeof a;return"object"==b&&a||"function"==b?"o"+la(a):b.substr(0,1)+a}
g=Ri.prototype;g.fa=function(){return this.f.fa()};
g.removeAll=function(a){a=Ni(a);for(var b=a.length,c=0;c<b;c++)this.remove(a[c])};
g.remove=function(a){return this.f.remove(Si(a))};
g.clear=function(){this.f.clear()};
g.isEmpty=function(){return this.f.isEmpty()};
g.contains=function(a){a=Si(a);return me(this.f.j,a)};
g.ha=function(){return this.f.ha()};
g.clone=function(){return new Ri(this)};
g.equals=function(a){return this.fa()==Mi(a)&&Ti(this,a)};
function Ti(a,b){var c=Mi(b);if(a.fa()>c)return!1;!(b instanceof Ri)&&5<c&&(b=new Ri(b));return Qi(a,function(a){var c=b;return c.contains&&"function"==typeof c.contains?c.contains(a):c.Cb&&"function"==typeof c.Cb?c.Cb(a):ga(c)||t(c)?fb(c,a):Hb(c,a)})}
g.Ka=function(){return this.f.Ka(!1)};function Ui(a){a=String(a);if(/^\s*$/.test(a)?0:/^[\],:{}\s\u2028\u2029]*$/.test(a.replace(/\\["\\\/bfnrtu]/g,"@").replace(/(?:"[^"\\\n\r\u2028\u2029\x00-\x08\x0a-\x1f]*"|true|false|null|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?)[\s\u2028\u2029]*(?=:|,|]|}|$)/g,"]").replace(/(?:^|:|,)(?:[\s\u2028\u2029]*\[)+/g,"")))try{return eval("("+a+")")}catch(b){}throw Error("Invalid JSON string: "+a);}
function Vi(a){return eval("("+a+")")}
function Wi(a){return(new Xi(void 0)).nc(a)}
function Xi(a){this.f=a}
Xi.prototype.nc=function(a){var b=[];Yi(this,a,b);return b.join("")};
function Yi(a,b,c){if(null==b)c.push("null");else{if("object"==typeof b){if(fa(b)){var d=b;b=d.length;c.push("[");for(var e="",f=0;f<b;f++)c.push(e),e=d[f],Yi(a,a.f?a.f.call(d,String(f),e):e,c),e=",";c.push("]");return}if(b instanceof String||b instanceof Number||b instanceof Boolean)b=b.valueOf();else{c.push("{");f="";for(d in b)Object.prototype.hasOwnProperty.call(b,d)&&(e=b[d],"function"!=typeof e&&(c.push(f),Zi(d,c),c.push(":"),Yi(a,a.f?a.f.call(b,d,e):e,c),f=","));c.push("}");return}}switch(typeof b){case "string":Zi(b,
c);break;case "number":c.push(isFinite(b)&&!isNaN(b)?String(b):"null");break;case "boolean":c.push(String(b));break;case "function":c.push("null");break;default:throw Error("Unknown type: "+typeof b);}}}
var $i={'"':'\\"',"\\":"\\\\","/":"\\/","\b":"\\b","\f":"\\f","\n":"\\n","\r":"\\r","\t":"\\t","\x0B":"\\u000b"},aj=/\uffff/.test("\uffff")?/[\\\"\x00-\x1f\x7f-\uffff]/g:/[\\\"\x00-\x1f\x7f-\xff]/g;function Zi(a,b){b.push('"',a.replace(aj,function(a){var b=$i[a];b||(b="\\u"+(a.charCodeAt(0)|65536).toString(16).substr(1),$i[a]=b);return b}),'"')}
;var bj=null;"undefined"!=typeof XMLHttpRequest?bj=function(){return new XMLHttpRequest}:"undefined"!=typeof ActiveXObject&&(bj=function(){return new ActiveXObject("Microsoft.XMLHTTP")});function cj(a,b,c,d,e,f,h){function k(){4==(l&&"readyState"in l?l.readyState:0)&&b&&vf(b)(l)}
var l=bj&&bj();if(!("open"in l))return null;"onloadend"in l?l.addEventListener("loadend",k,!1):l.onreadystatechange=k;c=(c||"GET").toUpperCase();d=d||"";l.open(c,a,!0);f&&(l.responseType=f);h&&(l.withCredentials=!0);f="POST"==c;if(e=dj(a,e))for(var q in e)l.setRequestHeader(q,e[q]),"content-type"==q.toLowerCase()&&(f=!1);f&&l.setRequestHeader("Content-Type","application/x-www-form-urlencoded");l.send(d);return l}
function dj(a,b){b=b||{};var c;c||(c=window.location.href);var d=Ph(1,a),e=Qh(a);d&&e?(d=c,c=a.match(Nh),d=d.match(Nh),c=c[3]==d[3]&&c[1]==d[1]&&c[4]==d[4]):c=e?Qh(c)==e&&(Number(Ph(4,c))||null)==(Number(Ph(4,a))||null):!0;for(var f in ej){if((e=d=K(ej[f]))&&!(e=c)){var e=f,h=K("CORS_HEADER_WHITELIST")||{},k=Qh(a);e=k?(h=h[k])?fb(h,e):!1:!0}e&&(b[f]=d)}return b}
function fj(a,b){var c=K("XSRF_FIELD_NAME",void 0),d;b.headers&&(d=b.headers["Content-Type"]);return!b.Oi&&(!Qh(a)||b.withCredentials||Qh(a)==document.location.hostname)&&"POST"==b.method&&(!d||"application/x-www-form-urlencoded"==d)&&!(b.S&&b.S[c])}
function U(a,b){var c=b.format||"JSON";b.Qi&&(a=document.location.protocol+"//"+document.location.hostname+(document.location.port?":"+document.location.port:"")+a);var d=K("XSRF_FIELD_NAME",void 0),e=K("XSRF_TOKEN",void 0),f=b.Z;f&&(f[d]&&delete f[d],a=ci(a,f));var h=b.postBody||"",f=b.S;fj(a,b)&&(f||(f={}),f[d]=e);f&&t(h)&&(d=ai(h),Tb(d,f),h=Wh(d));var k=!1,l,q=cj(a,function(a){if(!k){k=!0;l&&M(l);var d;a:switch(a&&"status"in a?a.status:-1){case 200:case 201:case 202:case 203:case 204:case 205:case 206:case 304:d=
!0;break a;default:d=!1}var e=null;if(d||400<=a.status&&500>a.status)e=gj(c,a,b.Na);if(d)a:{switch(c){case "XML":d=0==parseInt(e&&e.return_code,10);break a;case "RAW":d=!0;break a}d=!!e}var e=e||{},f=b.context||m;d?b.R&&b.R.call(f,a,e):b.onError&&b.onError.call(f,a,e);b.Fa&&b.Fa.call(f,a,e)}},b.method,h,b.headers,b.responseType,b.withCredentials);
b.rb&&0<b.timeout&&(l=L(function(){k||(k=!0,q.abort(),M(l),b.rb.call(b.context||m,q))},b.timeout));
return q}
function gj(a,b,c){var d=null;switch(a){case "JSON":a=b.responseText;b=b.getResponseHeader("Content-Type")||"";a&&0<=b.indexOf("json")&&(d=Vi(a));break;case "XML":if(b=(b=b.responseXML)?hj(b):null)d={},x(b.getElementsByTagName("*"),function(a){d[a.tagName]=ij(a)})}c&&jj(d);
return d}
function jj(a){if(ka(a))for(var b in a){var c;(c="html_content"==b)||(c=b.length-5,c=0<=c&&b.indexOf("_html",c)==c);c?a[b]=Li(ec("HTML that is escaped and sanitized server-side and passed through yt.net.ajax"),a[b]):jj(a[b])}}
function hj(a){return a?(a=("responseXML"in a?a.responseXML:a).getElementsByTagName("root"))&&0<a.length?a[0]:null:null}
function kj(a,b){if(!a)return null;var c=a.getElementsByTagName(b);return c&&0<c.length?ij(c[0]):null}
function ij(a){var b="";x(a.childNodes,function(a){b+=a.nodeValue});
return b}
var ej={"X-YouTube-Client-Name":"INNERTUBE_CONTEXT_CLIENT_NAME","X-YouTube-Client-Version":"INNERTUBE_CONTEXT_CLIENT_VERSION","X-YouTube-Page-CL":"PAGE_CL","X-YouTube-Page-Label":"PAGE_BUILD_LABEL","X-YouTube-Variants-Checksum":"VARIANTS_CHECKSUM"};var lj={},mj=0;function nj(a,b){this.l=this.F=this.A="";this.D=null;this.B=this.f="";this.C=!1;var c;a instanceof nj?(this.C=n(b)?b:a.C,oj(this,a.A),this.F=a.F,pj(this,a.l),qj(this,a.D),this.f=a.f,rj(this,a.j.clone()),this.B=a.B):a&&(c=String(a).match(Nh))?(this.C=!!b,oj(this,c[1]||"",!0),this.F=sj(c[2]||""),pj(this,c[3]||"",!0),qj(this,c[4]),this.f=sj(c[5]||"",!0),rj(this,c[6]||"",!0),this.B=sj(c[7]||"")):(this.C=!!b,this.j=new tj(null,0,this.C))}
nj.prototype.toString=function(){var a=[],b=this.A;b&&a.push(uj(b,vj,!0),":");var c=this.l;if(c||"file"==b)a.push("//"),(b=this.F)&&a.push(uj(b,vj,!0),"@"),a.push(za(c).replace(/%25([0-9a-fA-F]{2})/g,"%$1")),c=this.D,null!=c&&a.push(":",String(c));if(c=this.f)this.l&&"/"!=c.charAt(0)&&a.push("/"),a.push(uj(c,"/"==c.charAt(0)?wj:xj,!0));(c=this.j.toString())&&a.push("?",c);(c=this.B)&&a.push("#",uj(c,yj));return a.join("")};
nj.prototype.resolve=function(a){var b=this.clone(),c=!!a.A;c?oj(b,a.A):c=!!a.F;c?b.F=a.F:c=!!a.l;c?pj(b,a.l):c=null!=a.D;var d=a.f;if(c)qj(b,a.D);else if(c=!!a.f){if("/"!=d.charAt(0))if(this.l&&!this.f)d="/"+d;else{var e=b.f.lastIndexOf("/");-1!=e&&(d=b.f.substr(0,e+1)+d)}e=d;if(".."==e||"."==e)d="";else if(-1!=e.indexOf("./")||-1!=e.indexOf("/.")){for(var d=0==e.lastIndexOf("/",0),e=e.split("/"),f=[],h=0;h<e.length;){var k=e[h++];"."==k?d&&h==e.length&&f.push(""):".."==k?((1<f.length||1==f.length&&
""!=f[0])&&f.pop(),d&&h==e.length&&f.push("")):(f.push(k),d=!0)}d=f.join("/")}else d=e}c?b.f=d:c=""!==a.j.toString();c?rj(b,sj(a.j.toString())):c=!!a.B;c&&(b.B=a.B);return b};
nj.prototype.clone=function(){return new nj(this)};
function oj(a,b,c){a.A=c?sj(b,!0):b;a.A&&(a.A=a.A.replace(/:$/,""))}
function pj(a,b,c){a.l=c?sj(b,!0):b}
function qj(a,b){if(b){b=Number(b);if(isNaN(b)||0>b)throw Error("Bad port number "+b);a.D=b}else a.D=null}
function rj(a,b,c){b instanceof tj?(a.j=b,zj(a.j,a.C)):(c||(b=uj(b,Aj)),a.j=new tj(b,0,a.C))}
function Bj(a,b,c){a.j.set(b,c)}
function Cj(a,b,c){fa(c)||(c=[String(c)]);Dj(a.j,b,c)}
function Ej(a){Bj(a,"zx",Math.floor(2147483648*Math.random()).toString(36)+Math.abs(Math.floor(2147483648*Math.random())^v()).toString(36));return a}
function Fj(a){return a instanceof nj?a.clone():new nj(a,void 0)}
function Gj(a,b,c,d){var e=new nj(null,void 0);a&&oj(e,a);b&&pj(e,b);c&&qj(e,c);d&&(e.f=d);return e}
function sj(a,b){return a?b?decodeURI(a.replace(/%25/g,"%2525")):decodeURIComponent(a):""}
function uj(a,b,c){return t(a)?(a=encodeURI(a).replace(b,Hj),c&&(a=a.replace(/%25([0-9a-fA-F]{2})/g,"%$1")),a):null}
function Hj(a){a=a.charCodeAt(0);return"%"+(a>>4&15).toString(16)+(a&15).toString(16)}
var vj=/[#\/\?@]/g,xj=/[\#\?:]/g,wj=/[\#\?]/g,Aj=/[\#\?@]/g,yj=/#/g;function tj(a,b,c){this.j=this.f=null;this.l=a||null;this.A=!!c}
function Ij(a){a.f||(a.f=new ke,a.j=0,a.l&&Rh(a.l,function(b,c){Jj(a,Aa(b),c)}))}
g=tj.prototype;g.fa=function(){Ij(this);return this.j};
function Jj(a,b,c){Ij(a);a.l=null;b=Kj(a,b);var d=a.f.get(b);d||a.f.set(b,d=[]);d.push(c);a.j=a.j+1}
g.remove=function(a){Ij(this);a=Kj(this,a);return me(this.f.j,a)?(this.l=null,this.j=this.j-this.f.get(a).length,this.f.remove(a)):!1};
g.clear=function(){this.f=this.l=null;this.j=0};
g.isEmpty=function(){Ij(this);return 0==this.j};
function Lj(a,b){Ij(a);b=Kj(a,b);return me(a.f.j,b)}
g.Cb=function(a){var b=this.ha();return fb(b,a)};
g.Ea=function(){Ij(this);for(var a=this.f.ha(),b=this.f.Ea(),c=[],d=0;d<b.length;d++)for(var e=a[d],f=0;f<e.length;f++)c.push(b[d]);return c};
g.ha=function(a){Ij(this);var b=[];if(t(a))Lj(this,a)&&(b=ob(b,this.f.get(Kj(this,a))));else{a=this.f.ha();for(var c=0;c<a.length;c++)b=ob(b,a[c])}return b};
g.set=function(a,b){Ij(this);this.l=null;a=Kj(this,a);Lj(this,a)&&(this.j=this.j-this.f.get(a).length);this.f.set(a,[b]);this.j=this.j+1;return this};
g.get=function(a,b){var c=a?this.ha(a):[];return 0<c.length?String(c[0]):b};
function Dj(a,b,c){a.remove(b);0<c.length&&(a.l=null,a.f.set(Kj(a,b),pb(c)),a.j=a.j+c.length)}
g.toString=function(){if(this.l)return this.l;if(!this.f)return"";for(var a=[],b=this.f.Ea(),c=0;c<b.length;c++)for(var d=b[c],e=za(d),d=this.ha(d),f=0;f<d.length;f++){var h=e;""!==d[f]&&(h+="="+za(d[f]));a.push(h)}return this.l=a.join("&")};
g.clone=function(){var a=new tj;a.l=this.l;this.f&&(a.f=this.f.clone(),a.j=this.j);return a};
function Kj(a,b){var c=String(b);a.A&&(c=c.toLowerCase());return c}
function zj(a,b){b&&!a.A&&(Ij(a),a.l=null,a.f.forEach(function(a,b){var e=b.toLowerCase();b!=e&&(this.remove(b),Dj(this,e,a))},a));
a.A=b}
g.extend=function(a){for(var b=0;b<arguments.length;b++)Pi(arguments[b],function(a,b){Jj(this,b,a)},this)};var Mj=/^https?.*#ocr$|^https?:\/\/(secure\-..\.imrworldwide\.com\/|cdn\.imrworldwide\.com\/|aksecure\.imrworldwide\.com\/)/,Nj=/^https?:\/\/(www\.google\.com\/pagead\/sul|www\.youtube\.com\/pagead\/sul)/;var Oj=E("Firefox"),Pj=Mc()||E("iPod"),Qj=E("iPad"),Rj=E("Android")&&!(ac()||E("Firefox")||$b()||E("Silk")),Sj=ac(),Tj=E("Safari")&&!(ac()||E("Coast")||$b()||E("Edge")||E("Silk")||E("Android"))&&!(Mc()||E("iPad")||E("iPod"));var Uj={},Vj=0,Wj=r("yt.net.ping.workerUrl_")||null;p("yt.net.ping.workerUrl_",Wj,void 0);function Xj(a,b,c){a&&(c&&(c=Xb,c=!(c&&0<=c.toLowerCase().indexOf("cobalt"))),c?a&&(a=zd("iframe",{src:'javascript:"data:text/html,<body><img src=\\"'+a+'\\"></body>"',style:"display:none"}),od(a).body.appendChild(a)):Yj(a,b))}
function Zj(a){try{window.navigator&&window.navigator.sendBeacon&&window.navigator.sendBeacon(a,"")||Xj(a,void 0)}catch(b){Xj(a,void 0)}}
function ak(a){return wa(Qa(a))?!1:Nj.test(a)||Mj.test(a)}
function Yj(a,b){var c=new Image,d=""+Vj++;Uj[d]=c;c.onload=c.onerror=function(){b&&Uj[d]&&b();delete Uj[d]};
c.src=a}
;function bk(a,b){if(window.spf){var c="";if(a){var d=a.indexOf("jsbin/"),e=a.lastIndexOf(".js"),f=d+6;-1<d&&-1<e&&e>f&&(c=a.substring(f,e),c=c.replace(ck,""),c=c.replace(dk,""),c=c.replace("debug-",""),c=c.replace("tracing-",""))}spf.script.load(a,c,b);return null}return ek(a,b)}
function ek(a,b){var c=fk(a),d=document.getElementById(c),e=d&&J(d,"loaded"),f=d&&!e;if(e)return b&&b(),d;if(b){var e=Q(c,b),h=""+la(b);gk[h]=e}return f?d:d=hk(a,c,function(){J(d,"loaded")||(ve(d,"loaded","true"),R(c),L(ra(ug,c),0))})}
function hk(a,b,c){var d=document.createElement("script");d.id=b;d.onload=function(){c&&setTimeout(c,0)};
d.onreadystatechange=function(){switch(d.readyState){case "loaded":case "complete":d.onload()}};
d.src=a;a=document.getElementsByTagName("head")[0]||document.body;a.insertBefore(d,a.firstChild);return d}
function fk(a){var b=document.createElement("a");jd(b,a);a=b.href.replace(/^[a-zA-Z]+:\/\//,"//");return"js-"+Ta(a)}
var ck=/\.vflset|-vfl[a-zA-Z0-9_+=-]+/,dk=/-[a-zA-Z]{2,3}_[a-zA-Z]{2,3}(?=(\/|$))/,gk={};function ik(a,b){if(window.spf){var c=a.match(jk);spf.style.load(a,c?c[1]:"",b);return null}return kk(a,b)}
function kk(a,b){var c=lk(a),d=document.getElementById(c),e=d&&J(d,"loaded"),f=d&&!e;if(e)return b&&b(),d;b&&(Q(c,b),la(b));return f?d:d=mk(a,c,function(){J(d,"loaded")||(ve(d,"loaded","true"),R(c),L(ra(ug,c),0))})}
function mk(a,b,c){var d=document.createElement("link");d.id=b;d.rel="stylesheet";d.onload=function(){c&&setTimeout(c,0)};
jd(d,a);(document.getElementsByTagName("head")[0]||document.body).appendChild(d);return d}
function lk(a){var b=document.createElement("a");jd(b,a);a=b.href.replace(/^[a-zA-Z]+:\/\//,"//");return"css-"+Ta(a)}
var jk=/cssbin\/(?:debug-)?([a-zA-Z0-9_-]+?)(?:-2x|-web|-rtl|-vfl|.css)/;function nk(){return!1}
function ok(){return null}
;function pk(){return parseInt(K("DCLKSTAT",0),10)}
;function qk(){if(null==r("_lact",window)){var a=parseInt(K("LACT"),10),a=isFinite(a)?v()-Math.max(a,0):-1;p("_lact",a,window);-1==a&&rk();N(document,"keydown",rk);N(document,"keyup",rk);N(document,"mousedown",rk);N(document,"mouseup",rk);Q("page-mouse",rk);Q("page-scroll",rk);Q("page-resize",rk)}}
function rk(){null==r("_lact",window)&&(qk(),r("_lact",window));var a=v();p("_lact",a,window);R("USER_ACTIVE")}
function sk(){var a=r("_lact",window);return null==a?-1:Math.max(v()-a,0)}
;function tk(a){this.f=a;this.j=this.l=void 0}
;function uk(a,b,c){var d=K("VALID_SESSION_TEMPDATA_DOMAINS",[]),e=Qh(window.location.href);e&&d.push(e);e=Qh(a);if(fb(d,e)||!e&&0==a.lastIndexOf("/",0)){e=a.match(Nh);a=e[5];var d=e[6],e=e[7],f="";a&&(f+=a);d&&(f+="?"+d);e&&(f+="#"+e);a=f;d=a.indexOf("#");if(a=0>d?a:a.substr(0,d))c?(c=parseInt(c,10),isFinite(c)&&0<c&&(vk(a,b,c),wk(b))):(vk(a,b),wk(b))}}
function vk(a,b,c){var d=K("ST_BASE36",!0),e;e=K("ST_SHORT",!0)?"ST-":"s_tempdata-";a=e=d?e+Ta(a).toString(36):e+Ta(a);c=c||5;b=b?Wh(b):"";Ag(a,b,c,"/")}
function wk(a){if(a){a=a.itct||a.ved;var b=r("yt.logging.screenreporter.storeParentElement");a&&b&&b(new tk(a))}}
;function xk(a,b,c){c=c||"";ld(window.location,Yh(a,b||{})+c)}
function yk(a,b){b&&uk(a,b);(window.ytspf||{}).enabled?spf.navigate(a):xk(a)}
function zk(a,b,c){var d=K("EVENT_ID");d&&(b||(b={}),b.ei||(b.ei=d));b&&uk(a,b);if(c)return!1;yk(a);return!0}
;var Ak=F?"focusin":"DOMFocusIn",Bk=F?"focusout":"DOMFocusOut",Ck=Rc?"webkitTransitionEnd":Nc?"otransitionend":"transitionend";var Dk;var Ek=Xb,Ek=Ek.toLowerCase();if(-1!=Ek.indexOf("android")){var Fk=Ek.match(/android\D*(\d\.\d)[^\;|\)]*[\;\)]/);if(Fk)Dk=Number(Fk[1]);else{var Gk={cupcake:1.5,donut:1.6,eclair:2,froyo:2.2,gingerbread:2.3,honeycomb:3,"ice cream sandwich":4,jellybean:4.1},Hk=Ek.match("("+Jb(Gk).join("|")+")");Dk=Hk?Gk[Hk[0]]:0}}else Dk=void 0;var Ik=['video/mp4; codecs="avc1.42001E, mp4a.40.2"','video/webm; codecs="vp8.0, vorbis"'],Jk=['audio/mp4; codecs="mp4a.40.2"'];function Kk(a){eg.call(this);this.f=[];this.j=a||this}
w(Kk,eg);function Lk(a,b,c,d){d=vf(u(d,a.j));b.addEventListener(c,d);a.f.push({target:b,name:c,Vb:d})}
Kk.prototype.pc=function(a){for(var b=0;b<this.f.length;b++)if(this.f[b]==a){this.f.splice(b,1);a.target.removeEventListener(a.name,a.Vb);break}};
function Mk(a){for(;a.f.length;){var b=a.f.pop();b.target.removeEventListener(b.name,b.Vb)}}
Kk.prototype.M=function(){Mk(this);Kk.J.M.call(this)};function Nk(a,b){this.version=a;this.args=b}
function Ok(a){if(!a.$a){var b={};a.call(b);a.$a=b.version}return a.$a}
function Pk(a,b){function c(){a.apply(this,b.args)}
if(!b.args||!b.version)throw Error("yt.pubsub2.Data.deserialize(): serializedData is incomplete.");var d;try{d=Ok(a)}catch(e){}if(!d||b.version!=d)throw Error("yt.pubsub2.Data.deserialize(): serializedData version is incompatible.");c.prototype=a.prototype;try{return new c}catch(e){throw e.message="yt.pubsub2.Data.deserialize(): "+e.message,e;}}
Nk.prototype.nc=function(){return{version:this.version,args:this.args}};
function Qk(a,b){this.topic=a;this.Yb=b}
Qk.prototype.toString=function(){return this.topic};var Rk=r("yt.pubsub2.instance_")||new hg;hg.prototype.subscribe=hg.prototype.subscribe;hg.prototype.unsubscribeByKey=hg.prototype.va;hg.prototype.publish=hg.prototype.K;hg.prototype.clear=hg.prototype.clear;p("yt.pubsub2.instance_",Rk,void 0);var Sk=r("yt.pubsub2.subscribedKeys_")||{};p("yt.pubsub2.subscribedKeys_",Sk,void 0);var Tk=r("yt.pubsub2.topicToKeys_")||{};p("yt.pubsub2.topicToKeys_",Tk,void 0);var Uk=r("yt.pubsub2.isAsync_")||{};p("yt.pubsub2.isAsync_",Uk,void 0);
p("yt.pubsub2.skipSubKey_",null,void 0);function Vk(a,b){var c=Wk();return c?c.publish.call(c,a.toString(),a,b):!1}
function Xk(a,b,c){var d=Wk();if(!d)return 0;var e=d.subscribe(a.toString(),function(d,h){if(!window.yt.pubsub2.skipSubKey_||window.yt.pubsub2.skipSubKey_!=e){var k=function(){if(Sk[e])try{if(h&&a instanceof Qk&&a!=d)try{h=Pk(a.Yb,h)}catch(k){throw k.message="yt.pubsub2 cross-binary conversion error for "+a.toString()+": "+k.message,k;}b.call(c||window,h)}catch(k){yf(k)}};
Uk[a.toString()]?r("yt.scheduler.instance")?vg(k,void 0):L(k,0):k()}});
Sk[e]=!0;Tk[a.toString()]||(Tk[a.toString()]=[]);Tk[a.toString()].push(e);return e}
function Yk(a){var b=Wk();b&&(ha(a)&&(a=[a]),x(a,function(a){b.unsubscribeByKey(a);delete Sk[a]}))}
function Wk(){return r("yt.pubsub2.instance_")}
;function Zk(a){Nk.call(this,1,arguments)}
w(Zk,Nk);var $k=new Qk("timing-sent",Zk);function al(a){var b=a||window;if(!(b.performance&&b.performance.timing&&b.performance.getEntriesByType))return{Rb:0,Oe:0};a=ud(b||window);for(var c=[],d=b.document.getElementsByTagName("*"),e=0,f=d.length;e<f;e++){var h=d[e],k;"IMG"==h.tagName&&(k=bl(h,h.src,a))&&c.push(k);var l=b.getComputedStyle(h)["background-image"];l&&(l=cl.exec(l))&&1<l.length&&(k=bl(h,l[1],a))&&c.push(k);if("IFRAME"==h.tagName)try{if(k=dl(h,a)){var q=al(h.contentWindow);0<q.Rb&&(k.timing=q.Rb,c.push(k))}}catch(z){}}k=b.performance.getEntriesByType("resource");
q={};d=0;for(e=k.length;d<e;d++)f=k[d],q[f.name]=f.responseEnd;d=0;for(e=c.length;d<e;d++)f=c[d],f.timing||(f.timing=q[f.url]||0);b=el(b,k);a=fl(b,a,c);f=c=0;if(a.length)for(q=k=0,d=a.length;q<d;q++)e=a[q],f=e.timing-f,0<f&&1>k&&(c+=(1-k)*f),f=e.timing,k=e.progress;return{Rb:Math.round(c||b),Oe:f}}
function dl(a,b){if(a.getBoundingClientRect){var c=a.getBoundingClientRect(),d=Math.max(c.top,0),e=Math.min(c.right,b.width),f=Math.min(c.bottom,b.height),c=Math.max(c.left,0);if(f>d&&e>c)return new gl(d,e,f,c)}return null}
function bl(a,b,c){return b&&(a=dl(a,c))?(a.url=b,a):null}
function el(a,b){var c,d=a.performance.timing.navigationStart;if("msFirstPaint"in a.performance.timing)c=a.performance.timing.f-d;else if("chrome"in a&&"loadTimes"in a.chrome){var e=a.chrome.loadTimes(),f=e.firstPaintTime;if(0<f){var h=e.startLoadTime;"requestTime"in e&&(h=e.requestTime);f>=h&&(c=1E3*(f-h))}}if(void 0===c||0>c||12E4<c){c=a.performance.timing.responseStart-d;for(var k={},d=a.document.getElementsByTagName("head")[0].children,e=0,f=d.length;e<f;e++)h=d[e],"SCRIPT"==h.tagName&&h.src&&
!h.async?k[h.src]=!0:"LINK"==h.tagName&&"stylesheet"==h.rel&&h.href&&(k[h.href]=!0);b.some(function(a){if(!k[a.name]||"script"!=a.initiatorType&&"link"!=a.initiatorType)return!0;a=a.responseEnd;if(void 0===c||a>c)c=a})}return Math.max(c,0)||0}
function fl(a,b,c){for(var d={0:0},e=0,f=0,h=c.length;f<h;f++){var k=c[f],l=a;k.timing>a&&(l=k.timing);d[l]||(d[l]=0);k=(k.f-k.A)*(k.l-k.j);d[l]+=k;e+=k}f=b.width*b.height;0<f&&(f=.1*Math.max(f-e,0),a in d||(d[a]=0),d[a]+=f,e+=f);a=[];if(e){for(var q in d)b=new hl(parseFloat(q),d[q]),a.push(b);a.sort(function(a,b){return a.timing-b.timing});
f=d=0;for(h=a.length;f<h;f++)b=a[f],d+=b.area,b.progress=d/e}return a}
function gl(a,b,c,d){this.f=c;this.j=d;this.l=b;this.A=a}
function hl(a,b){this.area=b;this.timing=a}
var cl=/url\((http[^\)]+)\)/i;var il=window.performance||window.mozPerformance||window.msPerformance||window.webkitPerformance||{},jl=u(il.clearResourceTimings||il.webkitClearResourceTimings||il.mozClearResourceTimings||il.msClearResourceTimings||il.oClearResourceTimings||ca,il),kl=il.mark?function(a){il.mark(a)}:ca;
function ll(a){ml(void 0)[a]=Bf();kl(a);nl(!1,void 0)}
function ol(a){pl(a);jl();p("yt.timing.pingSent_",!1,void 0)}
function ql(a){a=ml(a);if(a.aft)return a.aft;for(var b=K("TIMING_AFT_KEYS",["ol"]),c=b.length,d=0;d<c;d++){var e=a[b[d]];if(e)return e}return NaN}
function rl(a){return Math.round(il.timing.navigationStart+a)}
function sl(a){var b=window.location.protocol,c=il.getEntriesByType("resource"),d=c.filter(function(a){return 0==a.name.indexOf(b+"//fonts.googleapis.com/css?family=")})[0],c=c.filter(function(a){return 0==a.name.indexOf(b+"//fonts.gstatic.com/s/")}).reduce(function(a,b){return b.duration>a.duration?b:a},{duration:0});
d&&0<d.startTime&&0<d.responseEnd&&(a.wfcs=rl(d.startTime),a.wfce=rl(d.responseEnd));c&&0<c.startTime&&0<c.responseEnd&&(a.wffs=rl(c.startTime),a.wffe=rl(c.responseEnd))}
function tl(a){var b=ml(a),c=ul(a).span,d=vl(a),e=r("yt.timing.reportbuilder_");if(e){if(e=e(b,c,d,a))wl(e),ol(a)}else{var f=K("TIMING_ACTION",void 0),h=K("CSI_SERVICE_NAME","youtube"),e={v:2,s:h,action:f};if(il.now&&il.timing){var k=il.timing.navigationStart+il.now(),k=Math.round(v()-k);d.yt_hrd=k}var k=K("TIMING_INFO",{}),l;for(l in k)d[l]=k[l];l=d.srt;delete d.srt;var q;l||0===l||(q=il.timing||{},l=Math.max(0,q.responseStart-q.navigationStart),isNaN(l)&&d.pt&&(l=d.pt));if(l||0===l)d.srt=l;d.h5jse&&
(k=window.location.protocol+r("ytplayer.config.assets.js"),(k=il.getEntriesByName?il.getEntriesByName(k)[0]:null)?d.h5jse=Math.round(d.h5jse-k.responseEnd):delete d.h5jse);b.aft=ql(a);k=b._start;if("cold"==d.yt_lt){q||(q=il.timing||{});var z;a:if(z=q,z.msFirstPaint)z=Math.max(0,z.msFirstPaint);else{var C=window.chrome;if(C&&(C=C.loadTimes,ia(C))){var C=C(),W=1E3*Math.min(C.requestTime||Infinity,C.startLoadTime||Infinity),W=Infinity===W?0:z.navigationStart-W;z=Math.max(0,Math.round(1E3*C.firstPaintTime+
W)||0);break a}z=0}0<z&&z>k&&(b.fpt=z);z=c||ul(void 0).span;C=q.redirectEnd-q.redirectStart;0<C&&(z.rtime_=C);C=q.domainLookupEnd-q.domainLookupStart;0<C&&(z.dns_=C);C=q.connectEnd-q.connectStart;0<C&&(z.tcp_=C);C=q.connectEnd-q.secureConnectionStart;q.secureConnectionStart>=q.navigationStart&&0<C&&(z.stcp_=C);C=q.responseStart-q.requestStart;0<C&&(z.req_=C);C=q.responseEnd-q.responseStart;0<C&&(z.rcv_=C);il.getEntriesByType&&sl(b);(q=K("SPEEDINDEX_FOR_ACTIONS",void 0))&&-1<q.indexOf(f)&&(f=Bf(),
q=al(),f=Bf()-f,0<q.Rb&&(d.si=q.Rb,b.vsc=rl(q.Oe),c.sid=f))}z=ml(a);f=z.pbr;q=z.vc;z=z.pbs;f&&q&&z&&f<q&&q<z&&1==vl(a).yt_vis&&"youtube"==h&&(vl(a).yt_lt="hot_bg",h=b.vc,f=b.pbs,delete b.aft,c.aft=Math.round(f-h));(h=K("PREVIOUS_ACTION"))&&(d.pa=h);d.p=K("CLIENT_PROTOCOL")||"unknown";d.t=K("CLIENT_TRANSPORT")||"unknown";window.navigator&&window.navigator.sendBeacon&&(d.ba=1);for(var aa in d)"_"!=aa.charAt(0)&&(e[aa]=d[aa]);b.ps=Bf();d={};aa=[];for(var ma in b)"_"!=ma.charAt(0)&&(z=Math.max(Math.round(b[ma]-
k),0),d[ma]=z,aa.push(ma+"."+z));e.rt=aa.join(",");b=e;ma=[];for(var Xa in c)"_"!=Xa.charAt(0)&&ma.push(Xa+"."+Math.round(c[Xa]));b.it=ma.join(",");(Xa=r("ytdebug.logTiming"))&&Xa(e,d,c);ol(a);K("EXP_DEFER_CSI_PING")?(xl(),p("yt.timing.deferredPingArgs_",e,void 0),a=L(xl,0),p("yt.timing.deferredPingTimer_",a,void 0)):wl(e);Vk($k,new Zk(d.aft+(l||0)))}}
function nl(a,b){var c=K("TIMING_ACTION",void 0),d=ml(b);if(r("yt.timing.ready_")&&c&&d._start&&ql(b)){var c=!0,e=K("TIMING_WAIT",[]);if(!a&&e.length)for(var f=0,h=e.length;f<h;++f)if(!(e[f]in d)){c=!1;break}(c||b)&&tl(b)}}
function wl(a){if(K("DEBUG_CSI_DATA")){var b=r("yt.timing.csiData");b||(b=[],p("yt.timing.csiData",b,void 0));b.push({page:location.href,time:new Date,args:a})}K("EXP_DEFER_CSI_PING")&&(M(r("yt.timing.deferredPingTimer_")),p("yt.timing.deferredPingArgs_",null,void 0));var c="https:"==window.location.protocol?"https://gg.google.com/csi":"http://csi.gstatic.com/csi",c=K("CSI_LOG_WITH_YT")?"/csi_204":c,b="",d;for(d in a)b+="&"+d+"="+a[d];a=c+"?"+b.substring(1);b=K("DOUBLE_LOG_CSI")?"/csi_204?"+b.substring(1):
null;window.navigator&&window.navigator.sendBeacon?(Zj(a),b&&Zj(b)):(Xj(a),b&&Xj(b));p("yt.timing.pingSent_",!0,void 0)}
function xl(a){if(K("EXP_DEFER_CSI_PING")){var b=r("yt.timing.deferredPingArgs_");b&&(a&&(b.yt_fss=a),wl(b))}}
function ml(a){return ul(a).tick}
function vl(a){return ul(a).info}
function ul(a){return r("ytcsi."+(a||"")+"data_")||pl(a)}
function pl(a){var b={tick:{},span:{},info:{}};p("ytcsi."+(a||"")+"data_",b,void 0);return b}
;var yl={"api.invalidparam":2,auth:150,"drm.auth":150,heartbeat:150,"html5.unsupportedads":5,"fmt.noneavailable":5,"fmt.decode":5,"fmt.unplayable":5,"html5.missingapi":5,"drm.unavailable":5};function zl(a,b){eg.call(this);this.B=this.A=a;this.ca=b;this.F=!1;this.api={};this.Ja=this.X=null;this.ga=new hg;fg(this,ra(gg,this.ga));this.l={};this.C=this.Ra=this.j=this.Bc=this.f=null;this.qa=!1;this.H=this.D=this.P=this.V=null;this.nb={};this.qf=["onReady"];this.Ha=new Kk(this);fg(this,ra(gg,this.Ha));this.zc=null;this.od=NaN;this.Ia={};Al(this);this.La("onDetailedError",u(this.Ug,this));this.La("onTabOrderChange",u(this.rf,this));this.La("onTabAnnounce",u(this.nd,this));this.La("WATCH_LATER_VIDEO_ADDED",
u(this.Vg,this));this.La("WATCH_LATER_VIDEO_REMOVED",u(this.Wg,this));Oj||(this.La("onMouseWheelCapture",u(this.Ng,this)),this.La("onMouseWheelRelease",u(this.Og,this)));this.La("onAdAnnounce",u(this.nd,this));this.O=new Kk(this);fg(this,ra(gg,this.O));this.Ac=!1;this.Ab=null}
w(zl,eg);var Bl=["drm.unavailable","fmt.noneavailable","html5.missingapi","html5.unsupportedads","html5.unsupportedlive"];g=zl.prototype;g.getId=function(){return this.ca};
g.hd=function(a,b){this.isDisposed()||(Cl(this,a),b||Dl(this),El(this,b),this.F&&Fl(this))};
function Cl(a,b){a.Bc=b;a.f=b.clone();a.j=a.f.attrs.id||a.j;"video-player"==a.j&&(a.j=a.ca,a.f.attrs.id=a.ca);a.B.id==a.j&&(a.j+="-player",a.f.attrs.id=a.j);a.f.args.enablejsapi="1";a.f.args.playerapiid=a.ca;a.Ra||(a.Ra=Gl(a,a.f.args.jsapicallback||"onYouTubePlayerReady"));a.f.args.jsapicallback=null;var c=a.f.attrs.width;c&&(a.B.style.width=ff(Number(c)||c,!0));if(c=a.f.attrs.height)a.B.style.height=ff(Number(c)||c,!0)}
g.zf=function(){return this.Bc};
function Fl(a){a.f.loaded||(a.f.loaded=!0,"0"!=a.f.args.autoplay?a.api.loadVideoByPlayerVars(a.f.args):a.api.cueVideoByPlayerVars(a.f.args))}
function Hl(a){if(!n(a.f.disable.flash)){var b=a.f.disable,c;c=pi(oi.getInstance(),a.f.minVersion);b.flash=!c}return!a.f.disable.flash}
function Dl(a){var b;if(!(b=!a.f.html5&&Hl(a))){if(!n(a.f.disable.html5)){var c;b=!0;void 0!=a.f.args.deviceHasDisplay&&(b=a.f.args.deviceHasDisplay);if(2.2==Dk)c=!0;else{a:{var d=b;b=r("yt.player.utils.videoElement_");b||(b=document.createElement("video"),p("yt.player.utils.videoElement_",b,void 0));try{if(b.canPlayType)for(var d=d?Ik:Jk,e=0;e<d.length;e++)if(b.canPlayType(d[e])){c=null;break a}c="fmt.noneavailable"}catch(f){c="html5.missingapi"}}c=!c}c&&(c=Il(a)||a.f.assets.js);a.f.disable.html5=
!c;c||(a.f.args.html5_unavailable="1")}b=!!a.f.disable.html5}return b?Hl(a)?"flash":"unsupported":"html5"}
function Jl(a,b){if((!b||(5!=(yl[b.errorCode]||5)?0:-1!=Bl.indexOf(b.errorCode)))&&Hl(a)){var c=Kl(a);c&&c.stopVideo&&c.stopVideo();var d=a.f;c&&c.getUpdatedConfigurationData&&(c=c.getUpdatedConfigurationData(),d=ni(c));d.args.autoplay=1;d.args.html5_unavailable="1";Cl(a,d);El(a,"flash")}}
function El(a,b){a.isDisposed()||(b||(b=Dl(a)),("flash"==b?a.Lh:"html5"==b?a.Mh:a.Nh).call(a))}
function Il(a){var b=!0,c=Kl(a);c&&a.f&&(a=a.f,b=J(c,"version")==a.assets.js);return b&&!!r("yt.player.Application.create")}
g.Mh=function(){if(!this.qa){var a=Il(this);a&&"html5"==Ll(this)?(this.C="html5",this.F||this.sb()):(Ml(this),this.C="html5",a&&this.P?(this.A.appendChild(this.P),this.sb()):(this.f.loaded=!0,this.V=u(function(){var a=this.A,c=this.f.clone();r("yt.player.Application.create")(a,c);this.sb()},this),this.qa=!0,a?this.V():(bk(this.f.assets.js,this.V),ik(this.f.assets.css))))}};
g.Lh=function(){var a=this.f.clone();if(!this.D){var b=Kl(this);b&&(this.D=document.createElement("span"),this.D.tabIndex=0,Lk(this.Ha,this.D,"focus",this.ke),this.H=document.createElement("span"),this.H.tabIndex=0,Lk(this.Ha,this.H,"focus",this.ke),b.parentNode&&b.parentNode.insertBefore(this.D,b),Gd(this.H,b))}a.attrs.width=a.attrs.width||"100%";a.attrs.height=a.attrs.height||"100%";"flash"==Ll(this)?(this.C="flash",this.F||wi(a,!1,u(this.sb,this))):(Ml(this),this.C="flash",this.f.loaded=!0,ti(this.A,
a),this.sb())};
g.ke=function(){Kl(this).focus()};
function Kl(a){var b=G(a.j);!b&&a.B&&a.B.querySelector&&(b=a.B.querySelector("#"+a.j));return b}
g.sb=function(){if(!this.isDisposed()){var a=Kl(this),b=!1;try{a&&a.getApiInterface&&a.getApiInterface()&&(b=!0)}catch(f){}if(b)if(this.qa=!1,a.isNotServable&&a.isNotServable(this.f.args.video_id))Jl(this);else{Al(this);this.F=!0;a=Kl(this);a.addEventListener&&(this.X=Nl(this,a,"addEventListener"));a.removeEventListener&&(this.Ja=Nl(this,a,"removeEventListener"));for(var b=a.getApiInterface(),b=b.concat(a.getInternalApiInterface()),c=0;c<b.length;c++){var d=b[c];this.api[d]||(this.api[d]=Nl(this,
a,d))}for(var e in this.l)this.X(e,this.l[e]);Fl(this);this.Ra&&this.Ra(this.api);this.ga.K("onReady",this.api)}else this.od=L(u(this.sb,this),50)}};
function Nl(a,b,c){var d=b[c];return function(){try{return a.zc=null,d.apply(b,arguments)}catch(e){"Bad NPObject as private data!"!=e.message&&"sendAbandonmentPing"!=c&&(e.message+=" ("+c+")",a.zc=e,yf(e,"WARNING"))}}}
function Al(a){a.F=!1;if(a.Ja)for(var b in a.l)a.Ja(b,a.l[b]);for(var c in a.Ia)M(parseInt(c,10));a.Ia={};a.X=null;a.Ja=null;for(var d in a.api)a.api[d]=null;a.api.addEventListener=u(a.La,a);a.api.removeEventListener=u(a.yh,a);a.api.destroy=u(a.dispose,a);a.api.getLastError=u(a.Af,a);a.api.getPlayerType=u(a.Bf,a);a.api.getCurrentVideoConfig=u(a.zf,a);a.api.loadNewVideoConfig=u(a.hd,a);a.api.isReady=u(a.Yh,a)}
g.Yh=function(){return this.F};
g.La=function(a,b){if(!this.isDisposed()){var c=Gl(this,b);if(c){if(!fb(this.qf,a)&&!this.l[a]){var d=Ol(this,a);this.X&&this.X(a,d)}this.ga.subscribe(a,c);"onReady"==a&&this.F&&L(ra(c,this.api),0)}}};
g.yh=function(a,b){if(!this.isDisposed()){var c=Gl(this,b);c&&this.ga.unsubscribe(a,c)}};
function Gl(a,b){var c=b;if("string"==typeof b){if(a.nb[b])return a.nb[b];c=function(){var a=r(b);a&&a.apply(m,arguments)};
a.nb[b]=c}return c?c:null}
function Ol(a,b){var c="ytPlayer"+b+a.ca;a.l[b]=c;m[c]=function(c){var e=L(function(){a.isDisposed()||(a.ga.K(b,c),Nb(a.Ia,String(e)))},0);
Ob(a.Ia,String(e))};
return c}
g.rf=function(a){a=a?Pd:Od;for(var b=a(document.activeElement);b&&(1!=b.nodeType||b==this.D||b==this.H||(b.focus(),b!=document.activeElement));)b=a(b)};
g.nd=function(a){R("a11y-announce",a)};
g.Ug=function(a){Jl(this,a)};
g.Vg=function(a){R("WATCH_LATER_VIDEO_ADDED",a)};
g.Wg=function(a){R("WATCH_LATER_VIDEO_REMOVED",a)};
g.Ng=function(){this.Ac||(Sj?(this.Ab=wd(document),Lk(this.O,window,"scroll",this.ph),Lk(this.O,this.A,"touchmove",this.hh)):(Lk(this.O,this.A,"mousewheel",this.oe),Lk(this.O,this.A,"wheel",this.oe)),this.Ac=!0)};
g.Og=function(){Mk(this.O);this.Ac=!1};
g.oe=function(a){Pf(a)};
g.ph=function(){window.scrollTo(this.Ab.x,this.Ab.y)};
g.hh=function(a){a.preventDefault()};
g.Nh=function(){Ml(this);this.C="unsupported";var a='Adobe Flash Player or an HTML5 supported browser is required for video playback. <br> <a href="http://get.adobe.com/flashplayer/">Get the latest Flash Player</a> <br> <a href="/html5">Learn more about upgrading to an HTML5 browser</a>',b=navigator.userAgent.match(/Version\/(\d).*Safari/);b&&5<=parseInt(b[1],10)&&(a='Adobe Flash Player or QuickTime is required for video playback. <br> <a href="http://get.adobe.com/flashplayer/"> Get the latest Flash Player</a> <br> <a href="http://www.apple.com/quicktime/download/">Get the latest version of QuickTime</a>');
b=this.f.messages.player_fallback||a;a=G("player-unavailable");G("unavailable-submessage")&&a&&(G("unavailable-submessage").innerHTML=b,(b=H("icon",a))&&ye(b,"icon")&&(b.src=J(b,"icon")),lf(a,!0),A(G("player"),"off-screen-trigger"))};
g.Bf=function(){return this.C||Ll(this)};
g.Af=function(){return this.zc};
function Ll(a){return(a=Kl(a))?"div"==a.tagName.toLowerCase()?"html5":"flash":null}
function Ml(a){ll("dcp");a.cancel();Al(a);a.C=null;a.f&&(a.f.loaded=!1);var b=Kl(a);"html5"==Ll(a)?a.P=b:b&&b.destroy&&b.destroy();Fd(a.A);Mk(a.Ha);a.D=null;a.H=null}
g.cancel=function(){if(this.V){var a=this.V;this.f.assets.js&&a&&(a=""+la(a),(a=gk[a])&&rg(a))}M(this.od);this.qa=!1};
g.M=function(){Ml(this);if(this.P&&this.f)try{this.P.destroy()}catch(b){yf(b)}this.nb=null;for(var a in this.l)m[this.l[a]]=null;this.Bc=this.f=this.api=null;delete this.A;delete this.B;zl.J.M.call(this)};var Pl={},Ql="player_uid_"+(1E9*Math.random()>>>0);function Rl(a,b){a=t(a)?pd(a):a;b=ni(b);var c=Ql+"_"+la(a),d=Pl[c];if(d)return d.hd(b),d.api;d=new zl(a,c);Pl[c]=d;R("player-added",d.api);fg(d,ra(Sl,d));L(function(){d.hd(b)},0);
return d.api}
function Sl(a){Pl[a.getId()]=null}
function Tl(a){a=G(a);if(!a)return null;var b=Ql+"_"+la(a),c=Pl[b];c||(c=new zl(a,b),Pl[b]=c);return c.api}
;var Ul=r("yt.abuse.botguardInitialized")||nk;p("yt.abuse.botguardInitialized",Ul,void 0);var Vl=r("yt.abuse.invokeBotguard")||ok;p("yt.abuse.invokeBotguard",Vl,void 0);var Wl=r("yt.abuse.dclkstatus.checkDclkStatus")||pk;p("yt.abuse.dclkstatus.checkDclkStatus",Wl,void 0);var Xl=r("yt.player.exports.navigate")||zk;p("yt.player.exports.navigate",Xl,void 0);var Yl=r("yt.player.embed")||Rl;p("yt.player.embed",Yl,void 0);var Zl=r("yt.player.getPlayerByElement")||Tl;p("yt.player.getPlayerByElement",Zl,void 0);
var $l=r("yt.util.activity.init")||qk;p("yt.util.activity.init",$l,void 0);var am=r("yt.util.activity.getTimeSinceActive")||sk;p("yt.util.activity.getTimeSinceActive",am,void 0);var bm=r("yt.util.activity.setTimestamp")||rk;p("yt.util.activity.setTimestamp",bm,void 0);function cm(){}
;function dm(){}
w(dm,cm);dm.prototype.fa=function(){var a=0;ie(this.Ka(!0),function(){a++});
return a};
dm.prototype.clear=function(){var a=je(this.Ka(!0)),b=this;x(a,function(a){b.remove(a)})};function em(a){this.f=a}
w(em,dm);g=em.prototype;g.isAvailable=function(){if(!this.f)return!1;try{return this.f.setItem("__sak","1"),this.f.removeItem("__sak"),!0}catch(a){return!1}};
g.set=function(a,b){try{this.f.setItem(a,b)}catch(c){if(0==this.f.length)throw"Storage mechanism: Storage disabled";throw"Storage mechanism: Quota exceeded";}};
g.get=function(a){a=this.f.getItem(a);if(!t(a)&&null!==a)throw"Storage mechanism: Invalid value was encountered";return a};
g.remove=function(a){this.f.removeItem(a)};
g.fa=function(){return this.f.length};
g.Ka=function(a){var b=0,c=this.f,d=new ge;d.next=function(){if(b>=c.length)throw fe;var d=c.key(b++);if(a)return d;d=c.getItem(d);if(!t(d))throw"Storage mechanism: Invalid value was encountered";return d};
return d};
g.clear=function(){this.f.clear()};
g.key=function(a){return this.f.key(a)};function fm(){var a=null;try{a=window.localStorage||null}catch(b){}this.f=a}
w(fm,em);function gm(){var a=null;try{a=window.sessionStorage||null}catch(b){}this.f=a}
w(gm,em);function hm(a){this.f=a}
hm.prototype.set=function(a,b){n(b)?this.f.set(a,Wi(b)):this.f.remove(a)};
hm.prototype.get=function(a){var b;try{b=this.f.get(a)}catch(c){return}if(null!==b)try{return Ui(b)}catch(c){throw"Storage: Invalid value was encountered";}};
hm.prototype.remove=function(a){this.f.remove(a)};function im(a){this.f=a}
w(im,hm);function jm(a){this.data=a}
function km(a){return!n(a)||a instanceof jm?a:new jm(a)}
im.prototype.set=function(a,b){im.J.set.call(this,a,km(b))};
im.prototype.j=function(a){a=im.J.get.call(this,a);if(!n(a)||a instanceof Object)return a;throw"Storage: Invalid value was encountered";};
im.prototype.get=function(a){if(a=this.j(a)){if(a=a.data,!n(a))throw"Storage: Invalid value was encountered";}else a=void 0;return a};function lm(a){this.f=a}
w(lm,im);function mm(a){var b=a.creation;a=a.expiration;return!!a&&a<v()||!!b&&b>v()}
lm.prototype.set=function(a,b,c){if(b=km(b)){if(c){if(c<v()){lm.prototype.remove.call(this,a);return}b.expiration=c}b.creation=v()}lm.J.set.call(this,a,b)};
lm.prototype.j=function(a,b){var c=lm.J.j.call(this,a);if(c)if(!b&&mm(c))lm.prototype.remove.call(this,a);else return c};function nm(a){this.f=a}
w(nm,lm);function om(a,b){var c=[];ie(b,function(a){var b;try{b=nm.prototype.j.call(this,a,!0)}catch(f){if("Storage: Invalid value was encountered"==f)return;throw f;}n(b)?mm(b)&&c.push(a):c.push(a)},a);
return c}
function pm(a,b){var c=om(a,b);x(c,function(a){nm.prototype.remove.call(this,a)},a)}
;function qm(a,b,c){var d=c&&0<c?c:0;c=d?v()+1E3*d:0;if((d=d?rm:sm)&&window.JSON){t(b)||(b=JSON.stringify(b,void 0));try{d.set(a,b,c)}catch(e){d.remove(a)}}}
function um(a){if(!sm&&!rm||!window.JSON)return null;var b;try{b=sm.get(a)}catch(c){}if(!t(b))try{b=rm.get(a)}catch(c){}if(!t(b))return null;try{b=JSON.parse(b,void 0)}catch(c){}return b}
function vm(a){sm&&sm.remove(a);rm&&rm.remove(a)}
function wm(){if(rm){var a=rm;pm(a,a.f.Ka(!0))}}
var rm,xm=new fm;rm=xm.isAvailable()?new nm(xm):null;var sm,ym=new gm;sm=ym.isAvailable()?new nm(ym):null;function zm(a,b,c){Xj("/gen_204?"+("a="+a+(b?"&"+b:"")),c)}
;function Am(a,b,c,d,e,f,h){var k,l;if(k=c.offsetParent){var q="HTML"==k.tagName||"BODY"==k.tagName;q&&"static"==Ze(k,"position")||(l=cf(k),q||(q=(q=mf(k))&&Qc?-k.scrollLeft:!q||Pc&&ad("8")||"visible"==Ze(k,"overflowX")?k.scrollLeft:k.scrollWidth-k.clientWidth-k.scrollLeft,l=Kc(l,new Jc(q,k.scrollTop))))}k=l||new Jc;l=kf(a);if(q=bf(a)){var z=new Se(q.left,q.top,q.right-q.left,q.bottom-q.top),q=Math.max(l.left,z.left),C=Math.min(l.left+l.width,z.left+z.width);if(q<=C){var W=Math.max(l.top,z.top),z=
Math.min(l.top+l.height,z.top+z.height);W<=z&&(l.left=q,l.top=W,l.width=C-q,l.height=z-W)}}q=md(a);W=md(c);if(q.f!=W.f){C=q.f.body;var W=yd(W.f),z=new Jc(0,0),aa;aa=(aa=od(C))?yd(aa):window;if(Ue(aa,"parent")){var ma=C;do{var Xa=aa==W?cf(ma):ef(ma);z.x+=Xa.x;z.y+=Xa.y}while(aa&&aa!=W&&aa!=aa.parent&&(ma=aa.frameElement)&&(aa=aa.parent))}C=Kc(z,cf(C));!F||bd(9)||vd(q.f)||(C=Kc(C,wd(q.f)));l.left+=C.x;l.top+=C.y}a=Bm(a,b);b=l.left;a&4?b+=l.width:a&2&&(b+=l.width/2);b=new Jc(b,l.top+(a&1?l.height:0));
b=Kc(b,k);e&&(b.x+=(a&4?-1:1)*e.x,b.y+=(a&1?-1:1)*e.y);var La;h&&(La=bf(c))&&(La.top-=k.y,La.right-=k.x,La.bottom-=k.y,La.left-=k.x);return Cm(b,c,d,f,La,h,void 0)}
function Cm(a,b,c,d,e,f,h){a=a.clone();var k=Bm(b,c);c=gf(hf,b);h=h?h.clone():c.clone();a=a.clone();h=h.clone();var l=0;if(d||0!=k)k&4?a.x-=h.width+(d?d.right:0):k&2?a.x-=h.width/2:d&&(a.x+=d.left),k&1?a.y-=h.height+(d?d.bottom:0):d&&(a.y+=d.top);if(f){if(e){d=a;k=h;l=0;65==(f&65)&&(d.x<e.left||d.x>=e.right)&&(f&=-2);132==(f&132)&&(d.y<e.top||d.y>=e.bottom)&&(f&=-5);d.x<e.left&&f&1&&(d.x=e.left,l|=1);if(f&16){var q=d.x;d.x<e.left&&(d.x=e.left,l|=4);d.x+k.width>e.right&&(k.width=Math.min(e.right-d.x,
q+k.width-e.left),k.width=Math.max(k.width,0),l|=4)}d.x+k.width>e.right&&f&1&&(d.x=Math.max(e.right-k.width,e.left),l|=1);f&2&&(l=l|(d.x<e.left?16:0)|(d.x+k.width>e.right?32:0));d.y<e.top&&f&4&&(d.y=e.top,l|=2);f&32&&(q=d.y,d.y<e.top&&(d.y=e.top,l|=8),d.y+k.height>e.bottom&&(k.height=Math.min(e.bottom-d.y,q+k.height-e.top),k.height=Math.max(k.height,0),l|=8));d.y+k.height>e.bottom&&f&4&&(d.y=Math.max(e.bottom-k.height,e.top),l|=2);f&8&&(l=l|(d.y<e.top?64:0)|(d.y+k.height>e.bottom?128:0));e=l}else e=
256;l=e}f=new Se(0,0,0,0);f.left=a.x;f.top=a.y;f.width=h.width;f.height=h.height;e=l;if(e&496)return e;a=new Jc(f.left,f.top);a instanceof Jc?(h=a.x,a=a.y):(h=a,a=void 0);b.style.left=ff(h,!1);b.style.top=ff(a,!1);h=new Lc(f.width,f.height);c==h||c&&h&&c.width==h.width&&c.height==h.height||(c=h,h=od(b),a=vd(md(h).f),!F||ad("10")||a&&ad("8")?(b=b.style,Qc?b.MozBoxSizing="border-box":Rc?b.WebkitBoxSizing="border-box":b.boxSizing="border-box",b.width=Math.max(c.width,0)+"px",b.height=Math.max(c.height,
0)+"px"):(h=b.style,a?(F?(a=of(b,"paddingLeft"),f=of(b,"paddingRight"),d=of(b,"paddingTop"),k=of(b,"paddingBottom"),a=new Ne(d,f,k,a)):(a=Ye(b,"paddingLeft"),f=Ye(b,"paddingRight"),d=Ye(b,"paddingTop"),k=Ye(b,"paddingBottom"),a=new Ne(parseFloat(d),parseFloat(f),parseFloat(k),parseFloat(a))),F&&!bd(9)?(f=qf(b,"borderLeft"),d=qf(b,"borderRight"),k=qf(b,"borderTop"),b=qf(b,"borderBottom"),b=new Ne(k,d,b,f)):(f=Ye(b,"borderLeftWidth"),d=Ye(b,"borderRightWidth"),k=Ye(b,"borderTopWidth"),b=Ye(b,"borderBottomWidth"),
b=new Ne(parseFloat(k),parseFloat(d),parseFloat(b),parseFloat(f))),h.pixelWidth=c.width-b.left-a.left-a.right-b.right,h.pixelHeight=c.height-b.top-a.top-a.bottom-b.bottom):(h.pixelWidth=c.width,h.pixelHeight=c.height)));return e}
function Bm(a,b){return(b&8&&mf(a)?b^4:b)&-9}
;var Dm={},Em="ontouchstart"in document;function Fm(a,b,c){b in Dm||(Dm[b]=new hg);Dm[b].subscribe(a,c)}
function Gm(a,b,c){if(b in Dm){var d=Dm[b];d.unsubscribe(a,c);0>=d.fa()&&(d.dispose(),delete Dm[b])}}
function Hm(a,b,c){var d;switch(a){case "mouseover":case "mouseout":d=3;break;case "mouseenter":case "mouseleave":d=9}return ee(c,function(a){return y(a,b)},!0,d)}
function Im(a){var b="mouseover"==a.type&&"mouseenter"in Dm||"mouseout"==a.type&&"mouseleave"in Dm,c=a.type in Dm||b;if("HTML"!=a.target.tagName&&c){if(b){var b="mouseover"==a.type?"mouseenter":"mouseleave",c=Dm[b],d;for(d in c.ua){var e=Hm(b,d,a.target);e&&!ee(a.relatedTarget,function(a){return a==e},!0)&&c.K(d,e,b,a)}}if(b=Dm[a.type])for(d in b.ua)(e=Hm(a.type,d,a.target))&&b.K(d,e,a.type,a)}}
N(document,"blur",Im,!0);N(document,"change",Im,!0);N(document,"click",Im);N(document,"focus",Im,!0);N(document,"mouseover",Im);N(document,"mouseout",Im);N(document,"mousedown",Im);N(document,"keydown",Im);N(document,"keyup",Im);N(document,"keypress",Im);N(document,"cut",Im);N(document,"paste",Im);Em&&(N(document,"touchstart",Im),N(document,"touchend",Im),N(document,"touchcancel",Im));function V(a){this.C=a;this.F={};this.H=[];this.N=[]}
g=V.prototype;g.Y=function(a){return I(a,X(this))};
function X(a,b){return"yt-uix"+(a.C?"-"+a.C:"")+(b?"-"+b:"")}
g.unregister=function(){rg(this.H);this.H.length=0;Yk(this.N);this.N.length=0};
g.init=ca;g.dispose=ca;g.na=function(a,b,c){this.H.push(Q(a,b,c||this))};
function Jm(a,b,c){a.N.push(Xk(b,c,a))}
function Y(a,b,c,d){d=X(a,d);var e=u(c,a);Fm(d,b,e);a.F[c]=e}
function Z(a,b,c,d){Gm(X(a,d),b,a.F[c]);delete a.F[c]}
g.Ba=function(a,b,c){var d=this.G(a,b);if(d&&(d=r(d))){var e=sb(arguments,2);rb(e,0,0,a);d.apply(null,e)}};
g.G=function(a,b){return J(a,b)};
function Km(a,b){ve(a,"tooltip-text",b)}
function Lm(a,b,c){return H(X(a,b),c)}
;function Mm(){V.call(this,"button");this.f=null;this.l=[];this.j={}}
w(Mm,V);da(Mm);g=Mm.prototype;g.register=function(){Y(this,"click",this.Ue);Y(this,"keydown",this.Yd);Y(this,"keypress",this.Ve);this.na("page-scroll",this.Tf)};
g.unregister=function(){Z(this,"click",this.Ue);Z(this,"keydown",this.Yd);Z(this,"keypress",this.Ve);Nm(this);this.j={};Mm.J.unregister.call(this)};
g.Ue=function(a){a&&!a.disabled&&(Om(this,a),this.click(a))};
g.Yd=function(a,b,c){if(!(c.altKey||c.ctrlKey||c.shiftKey)&&(b=Pm(this,a))){var d=function(a){var b="";a.tagName&&(b=a.tagName.toLowerCase());return"ul"==b||"table"==b},e;
d(b)?e=b:e=Ud(b,d);if(e){e=e.tagName.toLowerCase();var f;"ul"==e?f=this.ig:"table"==e&&(f=this.hg);f&&Qm(this,a,b,c,u(f,this))}}};
g.Tf=function(){var a=this.j;if(0!=Fb(a))for(var b in a){var c=a[b],d=Rm(this,c);if(void 0==d||void 0==c)break;Sm(this,d,c,!0)}};
function Qm(a,b,c,d,e){var f=Hh(c),h=9==d.keyCode;h||32==d.keyCode||13==d.keyCode?(d=Tm(a,c))?(b=Ld(d),"a"==b.tagName.toLowerCase()?xk(b.href):Qf(b,"click")):h&&Um(a,b):f?27==d.keyCode?(Tm(a,c),Um(a,b)):e(b,c,d):(a=y(b,X(a,"reverse"))?38:40,d.keyCode==a&&(Qf(b,"click"),d.preventDefault()))}
g.Ve=function(a,b,c){c.altKey||c.ctrlKey||c.shiftKey||(a=Pm(this,a),Hh(a)&&c.preventDefault())};
function Tm(a,b){var c=X(a,"menu-item-highlight"),d=H(c,b);d&&B(d,c);return d}
function Vm(a,b,c){A(c,X(a,"menu-item-highlight"));var d=c.getAttribute("id");d||(d=X(a,"item-id-"+la(c)),c.setAttribute("id",d));b.setAttribute("aria-activedescendant",d)}
g.hg=function(a,b,c){var d=Tm(this,b);b=Ie("table",b);var e=Ie("tr",b),e=rd("td",null,e).length;b=rd("td",null,b);d=Wm(d,b,e,c);-1!=d&&(Vm(this,a,b[d]),c.preventDefault())};
g.ig=function(a,b,c){if(40==c.keyCode||38==c.keyCode){var d=Tm(this,b);b=Za(rd("li",null,b),Hh);d=Wm(d,b,1,c);Vm(this,a,b[d]);c.preventDefault()}};
function Wm(a,b,c,d){var e=b.length;a=Wa(b,a);if(-1==a)if(38==d.keyCode)a=e-c;else{if(37==d.keyCode||38==d.keyCode||40==d.keyCode)a=0}else 39==d.keyCode?(a%c==c-1&&(a-=c),a+=1):37==d.keyCode?(0==a%c&&(a+=c),--a):38==d.keyCode?(a<c&&(a+=e),a-=c):40==d.keyCode&&(a>=e-c&&(a-=e),a+=c);return a}
function Xm(a,b){var c=b.iframeMask;c||(c=document.createElement("iframe"),c.src='javascript:""',c.className=X(a,"menu-mask"),T(c),b.iframeMask=c);return c}
function Sm(a,b,c,d){var e=I(b,X(a,"group")),f=!!a.G(b,"button-menu-ignore-group"),e=e&&!f?e:b,f=9,h=8,k=kf(b);if(y(b,X(a,"reverse"))){f=8;h=9;k=k.top+"px";try{c.style.maxHeight=k}catch(z){}}y(b,"flip")&&(y(b,X(a,"reverse"))?(f=12,h=13):(f=13,h=12));var l;a.G(b,"button-has-sibling-menu")?l=af(e):a.G(b,"button-menu-root-container")&&(l=Ym(a,b));F&&!ad("8")&&(l=null);var q;l&&(q=kf(l),q=new Ne(-q.top,q.left,q.top,-q.left));l=new Jc(0,1);y(b,X(a,"center-menu"))&&(l.x-=Math.round((gf(hf,c).width-gf(hf,
b).width)/2));d&&(l.y+=wd(document).y);if(a=Xm(a,b))b=gf(hf,c),a.style.width=b.width+"px",a.style.height=b.height+"px",Am(e,f,a,h,l,q,197),d&&Ve(a,"position","fixed");Am(e,f,c,h,l,q,197)}
function Ym(a,b){if(a.G(b,"button-menu-root-container")){var c=a.G(b,"button-menu-root-container");return I(b,c)}return document.body}
g.Xe=function(a){if(a){var b=Pm(this,a);if(b){a.setAttribute("aria-pressed","true");a.setAttribute("aria-expanded","true");b.originalParentNode=b.parentNode;b.activeButtonNode=a;b.parentNode.removeChild(b);var c;this.G(a,"button-has-sibling-menu")?c=a.parentNode:c=Ym(this,a);c.appendChild(b);b.style.minWidth=a.offsetWidth-2+"px";var d=Xm(this,a);d&&c.appendChild(d);(c=!!this.G(a,"button-menu-fixed"))&&(this.j[Be(a).toString()]=b);Sm(this,a,b,c);sg("yt-uix-button-menu-before-show",a,b);S(b);d&&S(d);
this.Ba(a,"button-menu-action",!0);A(a,X(this,"active"));b=u(this.We,this,a,!1);d=u(this.We,this,a,!0);c=u(this.Ih,this,a,void 0);this.f&&Pm(this,this.f)==Pm(this,a)||Nm(this);R("yt-uix-button-menu-show",a);O(this.l);this.l=[N(document,"click",d),N(document,"contextmenu",b),N(window,"resize",c)];this.f=a}}};
function Um(a,b){if(b){var c=Pm(a,b);if(c){a.f=null;b.setAttribute("aria-pressed","false");b.setAttribute("aria-expanded","false");b.removeAttribute("aria-activedescendant");T(c);a.Ba(b,"button-menu-action",!1);var d=Xm(a,b),e=Be(c).toString();delete a.j[e];L(function(){d&&d.parentNode&&(T(d),d.parentNode.removeChild(d));c.originalParentNode&&(c.parentNode.removeChild(c),c.originalParentNode.appendChild(c),c.originalParentNode=null,c.activeButtonNode=null)},1)}var e=I(b,X(a,"group")),f=[X(a,"active")];
e&&f.push(X(a,"group-active"));Ab(b,f);R("yt-uix-button-menu-hide",b);O(a.l);a.l.length=0}}
g.Ih=function(a,b){var c=Pm(this,a);if(c){b&&(b instanceof wc?id(c,b):Td(c,b));var d=!!this.G(a,"button-menu-fixed");Sm(this,a,c,d)}};
function Rm(a,b){return I(b.activeButtonNode||b.parentNode,X(a))}
g.We=function(a,b,c){c=Nf(c);var d=I(c,X(this));if(d){var d=Pm(this,d),e=Pm(this,a);if(d==e)return}var d=I(c,X(this,"menu")),e=d==Pm(this,a),f=y(c,X(this,"menu-item")),h=y(c,X(this,"menu-close"));if(!d||e&&(f||h))Um(this,a),d&&b&&this.G(a,"button-menu-indicate-selected")&&((a=H(X(this,"content"),a))&&Td(a,ae(c)),Zm(this,d,c))};
function Zm(a,b,c){var d=X(a,"menu-item-selected");a=qd(d,b);x(a,function(a){B(a,d)});
A(c.parentNode,d)}
function Pm(a,b){if(!b.widgetMenu){var c=a.G(b,"button-menu-id"),c=c&&G(c),d=X(a,"menu");c?zb(c,[d,X(a,"menu-external")]):c=H(d,b);b.widgetMenu=c}return b.widgetMenu}
g.isToggled=function(a){return y(a,X(this,"toggled"))};
function Om(a,b){if(a.G(b,"button-toggle")){var c=I(b,X(a,"group")),d=X(a,"toggled"),e=y(b,d);if(c&&a.G(c,"button-toggle-group")){var f=a.G(c,"button-toggle-group"),c=qd(X(a),c);x(c,function(a){a!=b||"optional"==f&&e?(B(a,d),a.removeAttribute("aria-pressed")):(A(b,d),a.setAttribute("aria-pressed","true"))})}else e?b.removeAttribute("aria-pressed"):b.setAttribute("aria-pressed","true"),Cb(b,d)}}
g.click=function(a){if(Pm(this,a)){var b=Pm(this,a),c=Rm(this,b);c&&c!=a?(Um(this,c),L(u(this.Xe,this,a),1)):Hh(b)?Um(this,a):this.Xe(a);a.focus()}this.Ba(a,"button-action")};
function Nm(a){a.f&&Um(a,a.f)}
;function $m(a){return Ja(xa(a.replace(an,function(a,c){return bn.test(c)?"":" "}).replace(/[\t\n ]+/g," ")))}
var bn=/^(?:abbr|acronym|address|b|em|i|small|strong|su[bp]|u)$/i,an=/<[!\/]?([a-z0-9]+)([\/ ][^>]*)?>/gi;function cn(){V.call(this,"char-counter")}
w(cn,V);da(cn);cn.prototype.register=function(){Y(this,"keydown",this.f,"input");Y(this,"paste",this.f,"input");Y(this,"cut",this.f,"input");Y(this,"blur",this.f,"input")};
cn.prototype.unregister=function(){Z(this,"keydown",this.f,"input");Z(this,"paste",this.f,"input");Z(this,"cut",this.f,"input");Z(this,"blur",this.f,"input")};
cn.prototype.f=function(a){var b=this.Y(a);if(b){var c="true"==this.G(b,"count-char-by-size"),d=parseInt(this.G(b,"char-limit"),10);isNaN(d)||0>=d||L(u(function(){var e="true"==this.G(b,"use-plaintext-length"),f=parseInt(a.getAttribute("maxlength"),10);if(!isNaN(f)){var h=dn(a,c,e);if(c){if(h>f){var k=a.value,l=k.length,q=0,f=h-f,h="",z=0;do h+=k[l-q],z=unescape(encodeURIComponent(h)).length,q++;while(z<f);a.value=a.value.substring(0,l-q)}}else h>f&&(a.value=a.value.substring(0,f))}k=parseInt(this.G(b,
"warn-at-chars-remaining"),10);isNaN(k)&&(k=0);e=d-dn(a,c,e);D(b,X(this,"maxed-out"),e<k);"true"==this.G(b,"maxed-out-as-positive")&&(e=Math.abs(e));k=H(X(this,"remaining"),b);Td(k,e)},this),0)}};
function dn(a,b,c){a=a.value;c&&(a=$m(a));return b?unescape(encodeURIComponent(a)).length:a.length}
;function en(a){V.call(this,a);this.l=null}
w(en,V);g=en.prototype;g.Y=function(a){var b=V.prototype.Y.call(this,a);return b?b:a};
g.register=function(){this.na("yt-uix-kbd-nav-move-out-done",this.Ga)};
g.dispose=function(){en.J.dispose.call(this);fn(this)};
g.G=function(a,b){var c=en.J.G.call(this,a,b);return c?c:(c=en.J.G.call(this,a,"card-config"))&&(c=r(c))&&c[b]?c[b]:null};
g.xc=function(a){var b=this.Y(a);if(b){A(b,X(this,"active"));var c=gn(this,a,b);if(c){c.cardTargetNode=a;c.cardRootNode=b;hn(this,a,c);var d=X(this,"card-visible"),e=this.G(a,"card-delegate-show")&&this.G(b,"card-action");this.Ba(b,"card-action",a);this.l=a;T(c);L(u(function(){e||(S(c),R("yt-uix-card-show",b,a,c));jn(c);A(c,d);R("yt-uix-kbd-nav-move-in-to",c)},this),10)}}};
function gn(a,b,c){var d=c||b,e=X(a,"card");c=kn(a,d);var f=G(X(a,"card")+Be(d));if(f)return a=H(X(a,"card-body"),f),Sd(a,c)||(Id(c),a.appendChild(c)),f;f=document.createElement("div");f.id=X(a,"card")+Be(d);f.className=e;(d=a.G(d,"card-class"))&&zb(f,d.split(/\s+/));d=document.createElement("div");d.className=X(a,"card-border");b=a.G(b,"orientation")||"horizontal";e=document.createElement("div");e.className="yt-uix-card-border-arrow yt-uix-card-border-arrow-"+b;var h=document.createElement("div");
h.className=X(a,"card-body");a=document.createElement("div");a.className="yt-uix-card-body-arrow yt-uix-card-body-arrow-"+b;Id(c);h.appendChild(c);d.appendChild(a);d.appendChild(h);f.appendChild(e);f.appendChild(d);document.body.appendChild(f);return f}
function hn(a,b,c){var d=a.G(b,"orientation")||"horizontal",e=a.G(b,"position"),f=!!a.G(b,"force-position"),h=a.G(b,"position-fixed"),d="horizontal"==d,k="bottomright"==e||"bottomleft"==e,l="topright"==e||"bottomright"==e,q,z;l&&k?(z=13,q=8):l&&!k?(z=12,q=9):!l&&k?(z=9,q=12):(z=8,q=13);var C=mf(document.body),e=mf(b);C!=e&&(z^=4);var W;d?(e=b.offsetHeight/2-12,W=new Jc(-12,b.offsetHeight+6)):(e=b.offsetWidth/2-6,W=new Jc(b.offsetWidth+6,-12));var aa=gf(hf,c),e=Math.min(e,(d?aa.height:aa.width)-24-
6);6>e&&(e=6,d?W.y+=12-b.offsetHeight/2:W.x+=12-b.offsetWidth/2);var ma=null;f||(ma=10);aa=X(a,"card-flip");a=X(a,"card-reverse");D(c,aa,l);D(c,a,k);ma=Am(b,z,c,q,W,null,ma);!f&&ma&&(ma&48&&(l=!l,z^=4,q^=4),ma&192&&(k=!k,z^=1,q^=1),D(c,aa,l),D(c,a,k),Am(b,z,c,q,W));h&&(b=parseInt(c.style.top,10),f=wd(document).y,Ve(c,"position","fixed"),Ve(c,"top",b-f+"px"));C&&(c.style.right="",b=kf(c),b.left=b.left||parseInt(c.style.left,10),f=ud(window),c.style.left="",c.style.right=f.width-b.left-b.width+"px");
b=H("yt-uix-card-body-arrow",c);f=H("yt-uix-card-border-arrow",c);d=d?k?"top":"bottom":!C&&l||C&&!l?"left":"right";b.setAttribute("style","");f.setAttribute("style","");b.style[d]=e+"px";f.style[d]=e+"px";k=H("yt-uix-card-arrow",c);l=H("yt-uix-card-arrow-background",c);k&&l&&(c="right"==d?gf(hf,c).width-e-13:e+11,e=c/Math.sqrt(2),k.style.left=c+"px",k.style.marginLeft="1px",l.style.marginLeft=-e+"px",l.style.marginTop=e+"px")}
g.Ga=function(a){if(a=this.Y(a)){var b=G(X(this,"card")+Be(a));b&&(B(a,X(this,"active")),B(b,X(this,"card-visible")),T(b),this.l=null,b.cardTargetNode=null,b.cardRootNode=null,b.cardMask&&(Id(b.cardMask),b.cardMask=null))}};
function fn(a){a.l&&a.Ga(a.l)}
g.Fh=function(a,b){var c=this.Y(a);if(c){if(b){var d=kn(this,c);if(!d)return;b instanceof wc?id(d,b):Td(d,b)}y(c,X(this,"active"))&&(c=gn(this,a,c),hn(this,a,c),S(c),jn(c))}};
g.isActive=function(a){return(a=this.Y(a))?y(a,X(this,"active")):!1};
function kn(a,b){var c=b.cardContentNode;if(!c){var d=X(a,"content"),e=X(a,"card-content");(c=(c=a.G(b,"card-id"))?G(c):H(d,b))||(c=document.createElement("div"));var f=c;B(f,d);A(f,e);b.cardContentNode=c}return c}
function jn(a){var b=a.cardMask;b||(b=document.createElement("iframe"),b.src='javascript:""',zb(b,["yt-uix-card-iframe-mask"]),a.cardMask=b);b.style.position=a.style.position;b.style.top=a.style.top;b.style.left=a.offsetLeft+"px";b.style.height=a.clientHeight+"px";b.style.width=a.clientWidth+"px";document.body.appendChild(b)}
;function ln(){en.call(this,"clickcard");this.f={};this.j={}}
w(ln,en);da(ln);g=ln.prototype;g.register=function(){ln.J.register.call(this);Y(this,"click",this.Jd,"target");Y(this,"click",this.Fd,"close")};
g.unregister=function(){ln.J.unregister.call(this);Z(this,"click",this.Jd,"target");Z(this,"click",this.Fd,"close");for(var a in this.f)O(this.f[a]);this.f={};for(a in this.j)O(this.j[a]);this.j={}};
g.Jd=function(a,b,c){c.preventDefault();b=de(c.target,"button");b&&b.disabled||(a=(b=this.G(a,"card-target"))?G(b):a,b=this.Y(a),this.G(b,"disabled")||(y(b,X(this,"active"))?(this.Ga(a),B(b,X(this,"active"))):(this.xc(a),A(b,X(this,"active")))))};
g.xc=function(a){ln.J.xc.call(this,a);var b=this.Y(a);if(!J(b,"click-outside-persists")){var c=la(a);if(this.f[c])return;var b=N(document,"click",u(this.Kd,this,a)),d=N(window,"blur",u(this.Kd,this,a));this.f[c]=[b,d]}a=N(window,"resize",u(this.Fh,this,a,void 0));this.j[c]=a};
g.Ga=function(a){ln.J.Ga.call(this,a);a=la(a);var b=this.f[a];b&&(O(b),this.f[a]=null);if(b=this.j[a])O(b),this.j[a]=null};
g.Kd=function(a,b){I(b.target,"yt-uix"+(this.C?"-"+this.C:"")+"-card")||this.Ga(a)};
g.Fd=function(a){(a=I(a,X(this,"card")))&&(a=a.cardTargetNode)&&this.Ga(a)};function mn(){V.call(this,"close")}
w(mn,V);da(mn);mn.prototype.register=function(){Y(this,"click",this.f)};
mn.prototype.unregister=function(){Z(this,"click",this.f)};
mn.prototype.f=function(a){var b,c=this.G(a,"close-parent-class"),d=this.G(a,"close-parent-id");d?b=G(d):c&&(b=I(a,c));b&&(T(b),c=this.G(a,"close-focus-target-id"))&&(c=G(c))&&(d=Mm.getInstance(),d.isToggled(c)&&Om(d,c),c.focus());this.Ba(a,"close-callback",b)};function nn(){V.call(this,"expander")}
w(nn,V);da(nn);nn.prototype.register=function(){Y(this,"click",this.f,"head");Y(this,"keypress",this.j,"head")};
nn.prototype.unregister=function(){Z(this,"click",this.f,"head");Z(this,"keypress",this.j,"head")};
nn.prototype.f=function(a){on(this,a)};
nn.prototype.j=function(a,b,c){c&&13==c.keyCode&&on(this,a)};
function on(a,b){var c=a.Y(b);if(c){c&&(Yd(c)||sd(c,{tabIndex:"0"}),c.focus());Cb(c,X(a,"collapsed"));var d=!y(c,X(a,"collapsed"));R("yt-uix-expander-toggle",c,d);R("yt-dom-content-change",c);a.Ba(c,"expander-action")}}
;function pn(){V.call(this,"form-input")}
w(pn,V);da(pn);g=pn.prototype;
g.register=function(){F&&!ad(9)&&(Y(this,"click",this.Xa,"checkbox"),Y(this,"keypressed",this.Xa,"checkbox"),Y(this,"click",this.mc,"radio"),Y(this,"keypressed",this.mc,"radio"));F&&!ad(10)&&Y(this,"click",this.Ud,"placeholder");Y(this,"change",this.Xa,"checkbox");Y(this,"blur",this.xd,"select-element");Y(this,"change",this.Za,"select-element");Y(this,"keyup",this.Za,"select-element");Y(this,"focus",this.Md,"select-element");Y(this,"keyup",this.tb,"text");Y(this,"keyup",this.tb,"textarea");Y(this,
"keyup",this.tb,"bidi");Y(this,"click",this.Uf,"reset")};
g.unregister=function(){F&&!ad(9)&&(Z(this,"click",this.Xa,"checkbox"),Z(this,"keypressed",this.Xa,"checkbox"),Z(this,"click",this.mc,"radio"),Z(this,"keypressed",this.mc,"radio"));F&&!ad(10)&&Z(this,"click",this.Ud,"placeholder");Z(this,"change",this.Xa,"checkbox");Z(this,"blur",this.xd,"select-element");Z(this,"change",this.Za,"select-element");Z(this,"keyup",this.Za,"select-element");Z(this,"focus",this.Md,"select-element");Z(this,"keyup",this.tb,"text");Z(this,"keyup",this.tb,"textarea");Z(this,
"keyup",this.tb,"bidi");pn.J.unregister.call(this)};
g.Xa=function(a){var b=I(a,X(this,"checkbox-container"));a.checked&&y(b,"partial")&&(a.checked=!1,a.indeterminate=!1,B(b,"partial"));D(b,"checked",a.checked)};
g.xh=function(a){var b=I(a,X(this,"radio-container"));b&&D(b,"checked",a.checked)};
g.mc=function(){qn()};
g.tb=function(a){var b=a.value;Wb.test(b)?a.dir="rtl":Vb.test(b)?a.dir="ltr":a.removeAttribute("dir");F&&!ad(10)&&(b=I(a,X(this,"container")))&&D(b,X(this,"non-empty"),!!a.value)};
g.Ud=function(a){a=I(a,X(this,"container"));(a=Lm(this,"text",a)||Lm(this,"textarea",a))&&a.focus()};
g.Md=function(a){var b=I(a,X(this,"select"));A(b,"focused");this.Za(a)};
g.xd=function(a){var b=I(a,X(this,"select"));B(b,"focused");this.Za(a)};
g.Za=function(a){var b=I(a,X(this,"select")),c=H(X(this,"select-value"),b),d=a.options[Math.max(a.selectedIndex,0)];d&&(""!=c.innerHTML&&d.innerHTML!=c.innerHTML&&this.Ba(a,"onchange-callback"),Ge(d,c));D(b,X(this,"select-disabled"),a.disabled)};
g.Uf=function(){var a=pn.getInstance(),b=qd(X(a,"checkbox"));x(b,a.Xa,a);qn();rn()};
function sn(a,b){a.checked=b;pn.getInstance().Xa(a)}
function qn(){var a=pn.getInstance(),b=qd(X(a,"radio"));x(b,a.xh,a)}
function rn(){var a=pn.getInstance(),b=qd(X(a,"select-element"));x(b,a.Za,a)}
;function tn(){en.call(this,"hovercard")}
w(tn,en);da(tn);g=tn.prototype;g.register=function(){Y(this,"mouseenter",this.$d,"target");Y(this,"mouseleave",this.be,"target");Y(this,"mouseenter",this.ae,"card");Y(this,"mouseleave",this.ce,"card")};
g.unregister=function(){Z(this,"mouseenter",this.$d,"target");Z(this,"mouseleave",this.be,"target");Z(this,"mouseenter",this.ae,"card");Z(this,"mouseleave",this.ce,"card")};
g.$d=function(a){if(un!=a){un&&(this.Ga(un),un=null);var b=u(this.xc,this,a),c=parseInt(this.G(a,"delay-show"),10),b=L(b,-1<c?c:200);ve(a,"card-timer",b.toString());un=a;a.alt&&(ve(a,"card-alt",a.alt),a.alt="");a.title&&(ve(a,"card-title",a.title),a.title="")}};
g.be=function(a){var b=parseInt(this.G(a,"card-timer"),10);M(b);this.Y(a).isCardHidable=!0;b=parseInt(this.G(a,"delay-hide"),10);b=-1<b?b:200;L(u(this.Yf,this,a),b);if(b=this.G(a,"card-alt"))a.alt=b;if(b=this.G(a,"card-title"))a.title=b};
g.Yf=function(a){this.Y(a).isCardHidable&&(this.Ga(a),un=null)};
g.ae=function(a){a&&(a.cardRootNode.isCardHidable=!1)};
g.ce=function(a){a&&this.Ga(a.cardTargetNode)};
var un=null;var vn=!F;function wn(a,b){return vn&&a.dataset?b in a.dataset?a.dataset[b]:null:a.getAttribute("data-"+String(b).replace(/([A-Z])/g,"-$1").toLowerCase())}
;function xn(){V.call(this,"kbd-nav")}
var yn;w(xn,V);da(xn);g=xn.prototype;g.register=function(){Y(this,"keydown",this.Ye);this.na("yt-uix-kbd-nav-move-in",this.ee);this.na("yt-uix-kbd-nav-move-in-to",this.jg);this.na("yt-uix-kbd-move-next",this.fe);this.na("yt-uix-kbd-nav-move-to",this.ac)};
g.unregister=function(){Z(this,"keydown",this.Ye);O(yn)};
g.Ye=function(a,b,c){var d=c.keyCode;if(a=I(a,X(this)))switch(d){case 13:case 32:this.ee(a);break;case 27:c.preventDefault();c.stopImmediatePropagation();a:{for(c=wn(a,"kbdNavMoveOut");!c;){c=I(a.parentElement,X(this));if(!c)break a;c=wn(c,"kbdNavMoveOut")}c=G(c);this.ac(c);R("yt-uix-kbd-nav-move-out-done",c)}break;case 40:case 38:if((b=c.target)&&y(a,X(this,"list")))switch(d){case 40:this.fe(b,a);break;case 38:d=document.activeElement==a,a=zn(a),b=a.indexOf(b),0>b&&!d||(b=d?a.length-1:(a.length+
b-1)%a.length,a[b].focus(),An(this,a[b]))}c.preventDefault()}};
g.ee=function(a){var b=wn(a,"kbdNavMoveIn"),b=G(b);Bn(this,a,b);this.ac(b)};
g.jg=function(a){var b;a:{var c=document;try{b=c&&c.activeElement;break a}catch(d){}b=null}Bn(this,b,a);this.ac(a)};
g.ac=function(a){if(a)if(Yd(a))a.focus();else{var b=Ud(a,function(a){return Qd(a)?Yd(a):!1});
b?b.focus():(a.setAttribute("tabindex","-1"),a.focus())}};
function Bn(a,b,c){b&&c&&(A(c,X(a)),a=b.id,a||(a="kbd-nav-"+Math.floor(1E6*Math.random()+1),b.id=a),b=a,vn&&c.dataset?c.dataset.kbdNavMoveOut=b:c.setAttribute("data-"+"kbdNavMoveOut".replace(/([A-Z])/g,"-$1").toLowerCase(),b))}
g.fe=function(a,b){var c=document.activeElement==b,d=zn(b),e=d.indexOf(a);0>e&&!c||(c=c?0:(e+1)%d.length,d[c].focus(),An(this,d[c]))};
function An(a,b){if(b){var c=de(b,"LI");c&&(A(c,X(a,"highlight")),yn=N(b,"blur",u(function(a){B(a,X(this,"highlight"));O(yn)},a,c)))}}
function zn(a){if("UL"!=a.tagName.toUpperCase())return[];a=Za(Kd(a),function(a){return"LI"==a.tagName.toUpperCase()});
return Za($a(a,function(a){return Hh(a)?Ud(a,function(a){return Qd(a)?Yd(a):!1}):!1}),function(a){return!!a})}
;function Cn(){V.call(this,"languagepicker");this.f={}}
w(Cn,V);da(Cn);g=Cn.prototype;g.register=function(){Y(this,"click",this.Td,"menu-item");Y(this,"keyup",this.Ee,"search-input");Y(this,"keydown",this.Wd,"search-input");Y(this,"blur",this.Vd,"search-input");Y(this,"focus",this.Sd);this.na("yt-uix-button-menu-before-show",this.Of);this.na("yt-uix-button-menu-hide",this.Nf)};
g.unregister=function(){Z(this,"click",this.Td,"menu-item");Z(this,"keyup",this.Ee,"search-input");Z(this,"keydown",this.Wd,"search-input");Z(this,"blur",this.Vd,"search-input");Z(this,"focus",this.Sd);O(Ib(this.f));this.f={};Cn.J.unregister.call(this)};
g.Of=function(a){if(y(a,"yt-languagepicker-button")){var b=la(a);a=N(a,"keydown",u(this.Jf,this));this.f[b]=a}};
g.Nf=function(a){y(a,"yt-languagepicker-button")&&(a=la(a),O(this.f[a]),delete this.f[a])};
function Dn(a,b){return bb(b,function(b){return!bb(a,function(a){return 0!=a.lastIndexOf(b,0)})})}
function En(a,b,c){x(a,function(a){var e=wn(a,"value"),f=Fn(a);Gh(a,e!=c&&f&&Dn(f,b))})}
function Fn(a){if("undefined"===typeof a.f){var b=wn(a,"searchTerms");b?(a.f=[],x(b.split(";"),function(b){qb(a.f,Gn(b))})):a.f=Gn(ae(a))}return a.f}
function Gn(a){return a.toLowerCase().match(/[^ \(\)\[\]]+/g)||[]}
function Hn(a,b){var c=Mm.getInstance(),d=Rm(c,a);Tm(c,a);Vm(c,d,b)}
g.Ee=function(a){var b=this.Y(a),c=Lm(this,"search-result",b),d=Gn(a.value);if(d){var e=Kd(c);a=wn(b,"fallbackOption");En(e,d,a);d=cb(e,Hh);a=c.querySelector('li[data-value="'+a+'"]');Gh(c,!(!d&&!a));d?Hn(b,d):a&&(S(a),Hn(b,a))}else Gh(c,!1)};
g.Wd=function(a,b,c){b=Mm.getInstance();a=this.Y(a);var d=Rm(b,a);switch(c.keyCode){case 13:case 9:(b=Tm(b,a))&&Qf(Ld(b),"click");c.preventDefault();break;case 27:Tm(b,a);Um(b,d);c.preventDefault();break;case 38:case 40:d.focus(),c.preventDefault()}};
g.Td=function(a){var b=wn(a,"value"),c=this.Y(a),d=wn(c,"languagepickerInputId"),d=G(d);d.value=b;Qf(d,"change");(d=Lm(this,"suggestions",c))&&!d.querySelector('li[data-value="'+b+'"]')&&((b=Lm(this,"selected",c))&&Id(b),a=a.cloneNode(!0),A(a,X(this,"selected")),Hd(d,a))};
function In(a,b){var c=Mm.getInstance(),d=a.Y(b);Rm(c,d).focus()}
g.Vd=function(a){In(this,a)};
g.Sd=function(a,b,c){"INPUT"!=c.target.tagName&&In(this,a)};
g.Jf=function(a){if(38!=a.keyCode&&40!=a.keyCode){var b=a.target,c=Mm.getInstance(),b=Pm(c,b),b=Lm(this,"search-input",b);13!=a.keyCode&&9!=a.keyCode&&32!=a.keyCode&&(b.value="");b.focus()}};function Jn(){V.call(this,"menu");this.j=this.f=null;this.l={};this.B={};this.A=null}
w(Jn,V);da(Jn);g=Jn.prototype;g.register=function(){Y(this,"click",this.Ze);Y(this,"mouseenter",this.Pf);this.na("page-scroll",this.Zh);this.na("yt-uix-kbd-nav-move-out-done",function(a){a=this.Y(a);Kn(this,a)});
this.A=new hg};
g.unregister=function(){Z(this,"click",this.Ze);this.j=this.f=null;O(wb(Ib(this.l)));this.l={};Db(this.B,function(a){Id(a)},this);
this.B={};gg(this.A);this.A=null;Jn.J.unregister.call(this)};
g.Ze=function(a,b,c){a&&(b=Ln(this,a),!b.disabled&&He(c.target,b)&&Mn(this,a))};
g.Pf=function(a,b,c){a&&y(a,X(this,"hover"))&&(b=Ln(this,a),He(c.target,b)&&Mn(this,a,!0))};
g.Zh=function(){this.f&&this.j&&Nn(this,this.j,this.f)};
function Nn(a,b,c){var d=On(a,b);if(d){var e=gf(hf,c),f;if(e instanceof Lc)f=e.height,e=e.width;else throw Error("missing height argument");d.style.width=ff(e,!0);d.style.height=ff(f,!0)}c==a.f&&(e=9,f=8,y(b,X(a,"reversed"))&&(e^=1,f^=1),y(b,X(a,"flipped"))&&(e^=4,f^=4),a=new Jc(0,1),d&&Am(b,e,d,f,a,null,197),Am(b,e,c,f,a,null,197))}
function Mn(a,b,c){Pn(a,b)&&!c?Kn(a,b):(Qn(a,b),!a.f||He(b,a.f)?a.$e(b):ig(a.A,u(a.$e,a,b)))}
g.$e=function(a){if(a){var b=Rn(this,a);if(b){sg("yt-uix-menu-before-show",a,b);if(this.f)He(a,this.f)||Kn(this,this.j);else{this.j=a;this.f=b;y(a,X(this,"sibling-content"))||(Id(b),document.body.appendChild(b));var c=Ln(this,a).offsetWidth-2;b.style.minWidth=c+"px"}(c=On(this,a))&&Gd(c,b);B(b,X(this,"content-hidden"));Nn(this,a,b);zb(Ln(this,a),[X(this,"trigger-selected"),"yt-uix-button-toggled"]);R("yt-uix-menu-show",a);Sn(b);Tn(this,a);R("yt-uix-kbd-nav-move-in-to",b);var d=u(this.$h,this,a),e=
u(this.fg,this,a),c=la(a).toString();this.l[c]=[N(b,"click",e),N(document,"click",d)];y(a,X(this,"indicate-selected"))&&(d=u(this.gg,this,a),this.l[c].push(N(b,"click",d)));y(a,X(this,"hover"))&&(a=u(this.Qf,this,a),this.l[c].push(N(document,"mousemove",a)))}}};
g.Qf=function(a,b){var c=Nf(b);if(c){var d=Ln(this,a);He(c,d)||Un(this,c)||Vn(this,a)}};
g.$h=function(a,b){var c=Nf(b);if(c){if(Un(this,c)){var d=I(c,X(this,"content")),e=de(c,"LI");e&&d&&Sd(d,e)&&sg("yt-uix-menu-item-clicked",c);c=I(c,X(this,"close-on-select"));if(!c)return;d=Jn.getInstance();d=y(c,X(d))?c:(e=d.Y(c))?e:I(c,X(d,"content"))==d.f?d.j:null}Kn(this,d||a)}};
function Qn(a,b){if(b){var c=I(b,X(a,"content"));c&&(c=qd(X(a),c),x(c,function(a){!He(a,b)&&Pn(this,a)&&Vn(this,a)},a))}}
function Kn(a,b){if(b){var c=[];c.push(b);var d=Rn(a,b);d&&(d=qd(X(a),d),d=pb(d),c=c.concat(d),x(c,function(a){Pn(this,a)&&Vn(this,a)},a))}}
function Vn(a,b){if(b){var c=Rn(a,b);Ab(Ln(a,b),[X(a,"trigger-selected"),"yt-uix-button-toggled"]);A(c,X(a,"content-hidden"));var d=Rn(a,b);d&&sd(d,{"aria-expanded":"false"});(d=On(a,b))&&d.parentNode&&Id(d);c&&c==a.f&&(a.j.appendChild(c),a.f=null,a.j=null,a.A.K("ROOT_MENU_REMOVED"));R("yt-uix-menu-hide",b);c=la(b).toString();O(a.l[c]);delete a.l[c]}}
g.fg=function(a,b){var c=Nf(b);c&&Wn(this,a,c)};
g.gg=function(a,b){var c=Nf(b);if(c){var d=Ln(this,a);if(d&&(c=de(c,"LI")))if(c=ae(c).trim(),d.hasChildNodes()){var e=Mm.getInstance();(d=H(X(e,"content"),d))&&Td(d,c)}else Td(d,c)}};
function Tn(a,b){var c=Rn(a,b);if(c){x(c.children,function(a){"LI"==a.tagName&&sd(a,{role:"menuitem"})});
sd(c,{"aria-expanded":"true"});var d=c.id;d||(d="aria-menu-id-"+la(c),c.id=d);(c=Ln(a,b))&&sd(c,{"aria-controls":d})}}
function Wn(a,b,c){var d=Rn(a,b);d&&y(b,X(a,"checked"))&&(a=de(c,"LI"))&&(a=H("yt-ui-menu-item-checked-hid",a))&&(d=qd("yt-ui-menu-item-checked",d),x(d,function(a){Bb(a,"yt-ui-menu-item-checked","yt-ui-menu-item-checked-hid")}),Bb(a,"yt-ui-menu-item-checked-hid","yt-ui-menu-item-checked"))}
function Pn(a,b){var c=Rn(a,b);return c?!y(c,X(a,"content-hidden")):!1}
function Sn(a){a=rd("UL",null,a);x(a,function(a){a.tabIndex="0";var c=xn.getInstance();zb(a,[X(c),X(c,"list")])})}
function Rn(a,b){var c=J(b,"menu-content-id");return c&&(c=G(c))?(zb(c,[X(a,"content"),X(a,"content-external")]),c):b==a.j?a.f:H(X(a,"content"),b)}
function On(a,b){var c=la(b).toString(),d=a.B[c];if(!d){d=document.createElement("IFRAME");d.src='javascript:""';var e=[X(a,"mask")];x(yb(b),function(a){e.push(a+"-mask")});
zb(d,e);a.B[c]=d}return d||null}
function Ln(a,b){return H(X(a,"trigger"),b)}
function Un(a,b){return He(b,a.f)||He(b,a.j)}
;function Xn(a,b,c,d){this.f=a;this.D=null;this.l=H("yt-dialog-fg",this.f)||this.f;if(a=H("yt-dialog-title",this.l)){var e="yt-dialog-title-"+la(this.l);a.setAttribute("id",e);this.l.setAttribute("aria-labelledby",e)}this.l.setAttribute("tabindex","-1");this.N=H("yt-dialog-focus-trap",this.f);this.H=!1;this.A=new hg;this.F=[];this.F.push(P(this.f,"click",u(this.xg,this),"yt-dialog-dismiss"));this.F.push(N(this.N,"focus",u(this.xf,this),!0));Yn(this,"content");this.U=b;this.P=c;this.O=d;this.C=this.B=
null}
var Zn={LOADING:"loading",pi:"content",Ji:"working"};function $n(a,b){a.isDisposed()||a.A.subscribe("post-all",b)}
function Yn(a,b){var c=H("yt-dialog-fg-content",a.f),d=[];Db(Zn,function(a){d.push("yt-dialog-show-"+a)});
Ab(c,d);A(c,"yt-dialog-show-"+b)}
function ao(a){if(!a.isDisposed()){a.D=document.activeElement;if(!a.O){a.j||(a.j=G("yt-dialog-bg"),a.j||(a.j=document.createElement("div"),a.j.id="yt-dialog-bg",a.j.className="yt-dialog-bg",document.body.appendChild(a.j)));var b;a:{var c=window,d=c.document;b=0;if(d){b=d.body;var e=d.documentElement;if(!e||!b){b=0;break a}c=ud(c).height;if(vd(d)&&e.scrollHeight)b=e.scrollHeight!=c?e.scrollHeight:e.offsetHeight;else{var d=e.scrollHeight,f=e.offsetHeight;e.clientHeight!=f&&(d=b.scrollHeight,f=b.offsetHeight);
b=d>c?d>f?d:f:d<f?d:f}}}a.j.style.height=b+"px";S(a.j)}Le(a.f);b=bo(a);co(b);a.B=N(document,"keydown",u(a.eg,a));b=a.f;e=Q("player-added",a.Xf,a);ve(b,"player-ready-pubsub-key",e);a.P&&(a.C=N(document,"click",u(a.rh,a)));S(a.f);a.l.setAttribute("tabindex","0");eo(a);A(document.body,"yt-dialog-active");Nm(Mm.getInstance());fn(ln.getInstance());fn(tn.getInstance())}}
function fo(){var a=qd("yt-dialog");return ab(a,function(a){return Hh(a)})}
g=Xn.prototype;g.Xf=function(){Le(this.f)};
function bo(a){var b=rd("iframe",null,a.f);x(b,function(a){var b=J(a,"onload");b&&(b=r(b))&&N(a,"load",b);if(b=J(a,"src"))a.src=b},a);
return pb(b)}
function co(a){x(document.getElementsByTagName("iframe"),function(b){-1==Wa(a,b)&&A(b,"iframe-hid")})}
function go(){var a=qd("iframe-hid");x(a,function(a){B(a,"iframe-hid")})}
g.xg=function(a){a=a.currentTarget;a.disabled||(a=J(a,"action")||"",this.dismiss(a))};
g.dismiss=function(a){if(!this.isDisposed()){this.A.K("pre-all");this.A.K("pre-"+a);T(this.f);fn(ln.getInstance());fn(tn.getInstance());this.l.setAttribute("tabindex","-1");fo()||(T(this.j),B(document.body,"yt-dialog-active"),Me(),go());this.B&&(O(this.B),this.B=null);this.C&&(O(this.C),this.C=null);var b=this.f;if(b){var c=J(b,"player-ready-pubsub-key");c&&(rg(c),xe(b,"player-ready-pubsub-key"))}this.A.K("post-all");R("yt-ui-dialog-hide-complete",this);"cancel"==a&&R("yt-ui-dialog-cancelled",this);
this.A&&this.A.K("post-"+a);this.D&&this.D.focus()}};
g.setTitle=function(a){Td(H("yt-dialog-title",this.f),a)};
g.eg=function(a){L(u(function(){this.U||27!=a.keyCode||this.dismiss("cancel")},this),0);
9==a.keyCode&&a.shiftKey&&y(document.activeElement,"yt-dialog-fg")&&a.preventDefault()};
g.rh=function(a){"yt-dialog-base"==a.target.className&&this.dismiss("cancel")};
g.isDisposed=function(){return this.H};
g.dispose=function(){Hh(this.f)&&this.dismiss("dispose");O(this.F);this.F.length=0;L(u(function(){this.D=null},this),0);
this.N=this.l=null;this.A.dispose();this.A=null;this.H=!0};
g.xf=function(a){a.stopPropagation();eo(this)};
function eo(a){L(u(function(){this.l&&this.l.focus()},a),0)}
p("yt.ui.Dialog",Xn,void 0);function ho(){V.call(this,"overlay");this.B=this.j=this.l=this.f=null}
w(ho,V);da(ho);ho.prototype.register=function(){Y(this,"click",this.D,"target");Y(this,"click",this.A,"close");io(this)};
ho.prototype.unregister=function(){ho.J.unregister.call(this);Z(this,"click",this.D,"target");Z(this,"click",this.A,"close");this.B&&(rg(this.B),this.B=null);this.j&&(O(this.j),this.j=null)};
ho.prototype.D=function(a){if(!this.f||!Hh(this.f.f)){var b=this.Y(a);a=jo(b,a);b||(b=a?a.overlayParentNode:null);if(b&&a){var c=!!this.G(b,"disable-shortcuts")||!1,d=!!this.G(b,"disable-outside-click-dismiss")||!1;this.f=new Xn(a,c);this.l=b;var e=H("yt-dialog-fg",a);if(e){var f=this.G(b,"overlay-class")||"",h=this.G(b,"overlay-style")||"default",k=this.G(b,"overlay-shape")||"default",f=f?f.split(" "):[];f.push(X(this,h));f.push(X(this,k));zb(e,f)}ao(this.f);R("yt-uix-kbd-nav-move-to",e||a);io(this);
c||d||(c=u(function(a){y(a.target,"yt-dialog-base")&&ko(this)},this),a=H("yt-dialog-base",a),this.j=N(a,"click",c));
this.Ba(b,"overlay-shown");R("yt-uix-overlay-shown",b)}}};
function io(a){a.B||(a.B=Q("yt-uix-overlay-hide",lo));a.f&&$n(a.f,function(){var a=ho.getInstance();a.l=null;a.f.dispose();a.f=null})}
function ko(a){if(a.f){var b=a.l;a.f.dismiss("overlayhide");a.Ba(b,"overlay-hidden");a.l=null;a.j&&(O(a.j),a.j=null);a.f=null}}
function jo(a,b){var c;if(a)if(c=H("yt-dialog",a)){var d=G("body-container");d&&(d.appendChild(c),a.overlayContentNode=c,c.overlayParentNode=a)}else c=a.overlayContentNode;else b&&(c=I(b,"yt-dialog"));return c}
function mo(){var a=ho.getInstance();if(a.l)a=H("yt-dialog-fg-content",a.l.overlayContentNode);else a:{if(a=qd("yt-dialog-fg-content"))for(var b=0;b<a.length;b++){var c=I(a[b],"yt-dialog");if(Hh(c)){a=a[b];break a}}a=null}return a}
ho.prototype.A=function(a){a&&a.disabled||R("yt-uix-overlay-hide")};
function lo(){ko(ho.getInstance())}
;function no(){V.call(this,"redirect-link")}
w(no,V);da(no);no.prototype.register=function(){Y(this,"click",this.f)};
no.prototype.unregister=function(){Z(this,"click",this.f)};
no.prototype.f=function(a){if(!J(a,"redirect-href-updated")){ve(a,"redirect-href-updated","true");var b=K("XSRF_REDIRECT_TOKEN");if(b){var c={};c.q=a.href;c.redir_token=b;jd(a,Yh("/redirect",c))}}};function oo(){V.call(this,"scroller");this.f={}}
w(oo,V);da(oo);g=oo.prototype;g.register=function(){Y(this,"mouseenter",this.de);Y(this,"mouseleave",this.fc)};
g.unregister=function(){Z(this,"mouseenter",this.de);Z(this,"mouseleave",this.fc);for(var a in this.f)this.fc(this.f[a]);this.f={};oo.J.unregister.call(this)};
g.dispose=function(){for(var a in this.f)this.fc(this.f[a]);this.f={}};
g.de=function(a){var b=N(a,"mousewheel",u(this.Rf,this,a));ve(a,"scroller-mousewheel-listener",b);b=N(a,"scroll",u(this.ai,this,a));ve(a,"scroller-scroll-listener",b);a&&(b=la(a).toString(),this.f[b]=a)};
g.fc=function(a){var b=this.G(a,"scroller-mousewheel-listener")||"";ve(a,"scroller-mousewheel-listener","");var c=this.G(a,"scroller-scroll-listener")||"";ve(a,"scroller-scroll-listener","");O(b);O(c);ve(a,"scroller-scroll-listener","");a&&(a=la(a).toString(),delete this.f[a])};
g.Rf=function(a,b){var c;c=b||window.event;var d=0;"MozMousePixelScroll"==c.type?d=0==(c.axis==c.HORIZONTAL_AXIS)?c.detail:0:window.opera?d=c.detail:d=0==c.wheelDelta%120?"WebkitTransform"in document.documentElement.style?window.chrome&&0==navigator.platform.indexOf("Mac")?c.wheelDeltaY/-30:c.wheelDeltaY/-1.2:c.wheelDelta/-1.6:c.wheelDeltaY/-3;if(c=d)d=a.scrollTop,a.scrollTop+=c,d==a.scrollTop&&this.G(a,"scroller-allow-pagescroll")||b.preventDefault()};
g.ai=function(a){this.Ba(a,"scroll-action");R("yt-dom-content-change",a)};function po(){V.call(this,"sessionlink")}
w(po,V);da(po);po.prototype.register=function(){Y(this,"mousedown",this.f);Y(this,"click",this.f)};
po.prototype.unregister=function(){Z(this,"mousedown",this.f);Z(this,"click",this.f)};
po.prototype.f=function(a){qo(a)};
function qo(a){var b;if(b=J(a,"sessionlink-target")||a.href||""){var c;c=J(a,"sessionlink")||"";c=ai(c);(a=parseInt(J(a,"sessionlink-lifetime")||"",10))?uk(b,c,a):uk(b,c)}}
;function ro(){var a=K("PLAYER_CONFIG");return a&&a.args&&void 0!==a.args.authuser?!0:!(!K("SESSION_INDEX")&&!K("LOGGED_IN"))}
;function so(){V.call(this,"simple-thumb-wrap")}
var to,uo;w(so,V);da(so);var vo="",wo=/__VIDEO_ID__/g;so.prototype.register=function(){if(vo=K("WATCH_LATER_BUTTON",void 0))to=K("WATCH_QUEUE_BUTTON",void 0),uo=K("WATCH_QUEUE_MENU",void 0),Y(this,"mouseover",this.f),Y(this,Ak,this.f),Y(this,"focus",this.f)};
so.prototype.unregister=function(){Z(this,"mouseover",this.f);Z(this,Ak,this.f);Z(this,"focus",this.f);so.J.unregister.call(this);uo=to=vo=""};
so.prototype.f=function(a){var b=ro(),c=I(a,"thumb-wrapper");if(!(c&&(b?H("addto-watch-later-button",c):H("addto-watch-later-button-sign-in",c)))){var d=this.G(a,"vid"),b=vo.replace(wo,d),c=to.replace(wo,d),d=uo.replace(wo,d),e=I(a,"thumb-wrapper");e.appendChild(Dd(Hc(b)));e.appendChild(Dd(Hc(d)));e.appendChild(Dd(Hc(c)));(a=I(a,"video-list-item"))&&B(a,"show-video-time")}};function xo(a){V.call(this,a||"slider");this.j=0;this.f=null}
w(xo,V);da(xo);g=xo.prototype;g.register=function(){Y(this,"click",this.Hd,"prev");Y(this,"click",this.Gd,"next");Y(this,"keyup",this.le,"item");this.f=N(window,"resize",u(this.Sf,this))};
g.unregister=function(){Z(this,"click",this.Hd,"prev");Z(this,"click",this.Gd,"next");Z(this,"click",this.le,"item");O(this.f);xo.J.unregister.call(this)};
g.cd=function(){var a=qd(X(this));x(a,function(a){yo(this,a)},this)};
g.Sf=function(){M(this.j);this.j=L(u(this.cd,this),200)};
function zo(a,b,c){var d=a.Y(b);if(!y(d,X(a,"is-moving"))){b=Lm(a,"list",d);var e=Lm(a,"body",d),f=pb(qd(X(a,"item"),d));if(f){var h=f[0];if("forward"==c){var k=Ao(d,e,f);c=Nd(k);if(!c)return;k=Bo(a,c,e,f);h=c}else if("back"==c){k=Co(d,e,f);c=n(k.previousElementSibling)?k.previousElementSibling:Md(k.previousSibling,!1);if(!c)return;k=Do(a,c,e,f);h=c}A(d,X(a,"is-moving"));mf(d)?b.style.right=(parseInt(b.style.right,10)||0)-k+"px":b.style.left=(parseInt(b.style.left,10)||0)+k+"px";var l=L(u(a.Ae,a,
d,h),510);Jf(b,Ck,u(function(a){M(l);this.Ae(d,a)},a,h))}}}
g.Gd=function(a){zo(this,a,"forward")};
g.Hd=function(a){zo(this,a,"back")};
g.le=function(a){(a=this.Y(a))&&R("yt-dom-content-change",a)};
function Co(a,b,c){return mf(a)?Eo(a,b,c):Fo(a,b,c)}
function Ao(a,b,c){return mf(a)?Fo(a,b,c):Eo(a,b,c)}
function Fo(a,b,c){function d(a){return df(a)>e-1}
var e=df(b);return mf(a)?eb(c,d):cb(c,d)}
function Eo(a,b,c){function d(a){a=df(a)+a.offsetWidth;return e>a-1}
var e=df(b)+b.offsetWidth;return mf(a)?cb(c,d):eb(c,d)}
function Do(a,b,c,d){var e,f=a.Y(b);e=df(c);var h=e+c.offsetWidth,k=df(b);b=k+b.offsetWidth;var l=d[0];d=df(l);l=d+l.offsetWidth;mf(f)?(c=a.Jc(f,c)-k,e=h-l):(c=a.Kc(f,c)-b,e-=d);D(f,X(a,"at-tail"),!1);if(Math.abs(c)+1<Math.abs(e))return c;D(f,X(a,"at-head"),!0);return e}
function Bo(a,b,c,d){var e,f=a.Y(b);e=df(c);var h=e+c.offsetWidth,k=df(b);b=k+b.offsetWidth;var l=d[d.length-1];d=df(l);l=d+l.offsetWidth;mf(f)?(c=a.Kc(f,c)-b,e-=d):(c=a.Jc(f,c)-k,e=h-l);D(f,X(a,"at-head"),!1);if(Math.abs(c)+1<Math.abs(e))return c;D(f,X(a,"at-tail"),!0);return e}
g.Jc=function(a,b){return df(b)};
g.Kc=function(a,b){return df(b)+b.offsetWidth};
g.Ae=function(a,b){a&&(B(a,X(this,"is-moving")),yo(this,a),R("yt-uix-slider-slide-shown",b),R("yt-dom-content-change",a))};
function yo(a,b){var c=Lm(a,"body",b),d=pb(qd(X(a,"item"),b)),e;d&&d.length?(e=Co(b,c,d),c=Ao(b,c,d),e=e==d[0],d=c==d[d.length-1]):e=d=!0;D(b,X(a,"at-head"),e);D(b,X(a,"at-tail"),d);if(a.G(b,"disable-slider-buttons")){if(c=Lm(a,"prev",b))c.disabled=e;if(e=Lm(a,"next",b))e.disabled=d}}
;function Go(a){Nk.call(this,1,arguments);this.f=a}
w(Go,Nk);function Ho(a){Nk.call(this,1,arguments);this.f=a}
w(Ho,Nk);function Io(a,b){Nk.call(this,1,arguments);this.f=a;this.isEnabled=b}
w(Io,Nk);function Jo(a,b){Nk.call(this,1,arguments);this.button=a;this.f=b}
w(Jo,Nk);function Ko(a,b,c,d,e){Nk.call(this,2,arguments);this.j=a;this.f=b;this.A=c||null;this.l=d||null;this.source=e||null}
w(Ko,Nk);function Lo(a,b,c){Nk.call(this,1,arguments);this.f=a;this.subscriptionId=b}
w(Lo,Nk);function Mo(a,b,c,d,e,f,h){Nk.call(this,1,arguments);this.j=a;this.subscriptionId=b;this.f=c;this.B=d||null;this.A=e||null;this.l=f||null;this.source=h||null}
w(Mo,Nk);
var No=new Qk("subscription-batch-subscribe",Go),Oo=new Qk("subscription-batch-unsubscribe",Go),Po=new Qk("subscription-pref-email",Io),Qo=new Qk("subscription-show-pref-overlay",Jo),Ro=new Qk("subscription-subscribe",Ko),So=new Qk("subscription-subscribe-loading",Ho),To=new Qk("subscription-subscribe-loaded",Ho),Uo=new Qk("subscription-subscribe-success",Lo),Vo=new Qk("subscription-subscribe-external",Ko),Wo=new Qk("subscription-unsubscribe",Mo),Xo=new Qk("subscription-unsubscirbe-loading",Ho),Yo=
new Qk("subscription-unsubscribe-loaded",Ho),Zo=new Qk("subscription-unsubscribe-success",Ho),$o=new Qk("subscription-external-unsubscribe",Mo),ap=new Qk("subscription-enable-ypc",Ho),bp=new Qk("subscription-disable-ypc",Ho);function cp(a){var b=document.location.protocol+"//"+document.domain+"/post_login",b=Xh(b,"mode","subscribe"),b=Xh("/signin?context=popup","next",b),b=Xh(b,"feature","sub_button");if(b=window.open(b,"loginPopup","width=375,height=440,resizable=yes,scrollbars=yes",!0)){var c=Q("LOGGED_IN",function(b){rg(K("LOGGED_IN_PUBSUB_KEY",void 0));tf("LOGGED_IN",!0);a(b)});
tf("LOGGED_IN_PUBSUB_KEY",c);b.moveTo((screen.width-375)/2,(screen.height-440)/2)}}
p("yt.pubsub.publish",R,void 0);function dp(){V.call(this,"tooltip");this.f=0;this.j={}}
w(dp,V);da(dp);g=dp.prototype;g.register=function(){Y(this,"mouseover",this.yc);Y(this,"mouseout",this.jb);Y(this,"focus",this.Ld);Y(this,"blur",this.wd);Y(this,"click",this.jb);Y(this,"touchstart",this.Le);Y(this,"touchend",this.oc);Y(this,"touchcancel",this.oc)};
g.unregister=function(){Z(this,"mouseover",this.yc);Z(this,"mouseout",this.jb);Z(this,"focus",this.Ld);Z(this,"blur",this.wd);Z(this,"click",this.jb);Z(this,"touchstart",this.Le);Z(this,"touchend",this.oc);Z(this,"touchcancel",this.oc);this.dispose();dp.J.unregister.call(this)};
g.dispose=function(){for(var a in this.j)this.jb(this.j[a]);this.j={}};
g.yc=function(a){if(!(this.f&&1E3>v()-this.f)){var b=parseInt(this.G(a,"tooltip-hide-timer"),10);b&&(xe(a,"tooltip-hide-timer"),M(b));var b=u(function(){ep(this,a);xe(a,"tooltip-show-timer")},this),c=parseInt(this.G(a,"tooltip-show-delay"),10)||0,b=L(b,c);
ve(a,"tooltip-show-timer",b.toString());a.title&&(Km(a,fp(this,a)),a.title="");b=la(a).toString();this.j[b]=a}};
g.jb=function(a){var b=parseInt(this.G(a,"tooltip-show-timer"),10);b&&(M(b),xe(a,"tooltip-show-timer"));b=u(function(){if(a){var b=G(gp(this,a));b&&(hp(b),Id(b),xe(a,"content-id"));b=G(gp(this,a,"arialabel"));Id(b)}xe(a,"tooltip-hide-timer")},this);
b=L(b,50);ve(a,"tooltip-hide-timer",b.toString());if(b=this.G(a,"tooltip-text"))a.title=b;b=la(a).toString();delete this.j[b]};
g.Ld=function(a){this.f=0;this.yc(a)};
g.wd=function(a){this.f=0;this.jb(a)};
g.Le=function(a,b,c){c.changedTouches&&(this.f=0,a=Hm(b,X(this),c.changedTouches[0].target),this.yc(a))};
g.oc=function(a,b,c){c.changedTouches&&(this.f=v(),a=Hm(b,X(this),c.changedTouches[0].target),this.jb(a))};
function ip(a,b,c){Km(b,c);a=a.G(b,"content-id");(a=G(a))&&Td(a,c)}
function fp(a,b){return a.G(b,"tooltip-text")||b.title}
function ep(a,b){if(b){var c=fp(a,b);if(c){var d=G(gp(a,b));if(!d){d=document.createElement("div");d.id=gp(a,b);d.className=X(a,"tip");var e=document.createElement("div");e.className=X(a,"tip-body");var f=document.createElement("div");f.className=X(a,"tip-arrow");var h=document.createElement("div");h.setAttribute("aria-hidden","true");h.className=X(a,"tip-content");var k=kp(a,b),l=gp(a,b,"content");h.id=l;ve(b,"content-id",l);e.appendChild(h);k&&d.appendChild(k);d.appendChild(e);d.appendChild(f);
var l=ae(b),q=gp(a,b,"arialabel"),f=document.createElement("div");A(f,X(a,"arialabel"));f.id=q;"rtl"==document.body.getAttribute("dir")?Td(f,c+" "+l):Td(f,l+" "+c);b.setAttribute("aria-labelledby",q);l=Ke()||document.body;l.appendChild(f);l.appendChild(d);ip(a,b,c);(c=parseInt(a.G(b,"tooltip-max-width"),10))&&e.offsetWidth>c&&(e.style.width=c+"px",A(h,X(a,"normal-wrap")));h=y(b,X(a,"reverse"));lp(a,b,d,e,k,h)||lp(a,b,d,e,k,!h);var z=X(a,"tip-visible");L(function(){A(d,z)},0)}}}}
function lp(a,b,c,d,e,f){D(c,X(a,"tip-reverse"),f);var h=0;f&&(h=1);var k=gf(hf,b);f=new Jc((k.width-10)/2,f?k.height:0);var l=cf(b);Cm(new Jc(l.x+f.x,l.y+f.y),c,h);f=ud(window);var q;1==c.nodeType?q=ef(c):(c=c.changedTouches?c.changedTouches[0]:c,q=new Jc(c.clientX,c.clientY));c=gf(hf,d);var z=Math.floor(c.width/2),h=!!(f.height<q.y+k.height),k=!!(q.y<k.height),l=!!(q.x<z);f=!!(f.width<q.x+z);q=(c.width+3)/-2- -5;a=a.G(b,"force-tooltip-direction");if("left"==a||l)q=-5;else if("right"==a||f)q=20-
c.width-3;a=Math.floor(q)+"px";d.style.left=a;e&&(e.style.left=a,e.style.height=c.height+"px",e.style.width=c.width+"px");return!(h||k)}
function gp(a,b,c){a=X(a)+Be(b);c&&(a+="-"+c);return a}
function kp(a,b){var c=null;Tc&&y(b,X(a,"masked"))&&((c=G("yt-uix-tooltip-shared-mask"))?(c.parentNode.removeChild(c),S(c)):(c=document.createElement("iframe"),c.src='javascript:""',c.id="yt-uix-tooltip-shared-mask",c.className=X(a,"tip-mask")));return c}
function hp(a){var b=G("yt-uix-tooltip-shared-mask"),c=b&&ee(b,function(b){return b==a},!1,2);
b&&c&&(b.parentNode.removeChild(b),T(b),document.body.appendChild(b))}
;function mp(){V.call(this,"subscription-button")}
w(mp,V);da(mp);mp.prototype.register=function(){Y(this,"click",this.jd);Jm(this,So,this.me);Jm(this,To,this.af);Jm(this,Uo,this.fh);Jm(this,Xo,this.me);Jm(this,Yo,this.af);Jm(this,Zo,this.lh);Jm(this,ap,this.Hg);Jm(this,bp,this.Fg)};
mp.prototype.unregister=function(){Z(this,"click",this.jd);mp.J.unregister.call(this)};
var np={kd:"hover-enabled",ef:"yt-uix-button-subscribe",ff:"yt-uix-button-subscribed",ni:"ypc-enabled",gf:"yt-uix-button-subscription-container",hf:"yt-subscription-button-disabled-mask-container"},op={oi:"channel-external-id",jf:"subscriber-count-show-when-subscribed",kf:"subscriber-count-tooltip",lf:"subscriber-count-title",vi:"href",ld:"is-subscribed",Ai:"parent-url",Di:"clicktracking",mf:"style-type",md:"subscription-id",Hi:"target",nf:"ypc-enabled"};g=mp.prototype;
g.jd=function(a){var b=this.G(a,"href"),c=ro();if(b)a=this.G(a,"target")||"_self",window.open(b,a);else if(c){var b=this.cc(a),c=this.G(a,"clicktracking"),d;if(this.G(a,"ypc-enabled")){d=this.G(a,"ypc-item-type");var e=this.G(a,"ypc-item-id");d={itemType:d,itemId:e,subscriptionElement:a}}else d=null;e=this.G(a,"parent-url");if(this.G(a,"is-subscribed")){var f=this.G(a,"subscription-id");Vk(Wo,new Mo(b,f,d,a,c,e))}else Vk(Ro,new Ko(b,d,c,e))}else pp(this,a)};
g.me=function(a){this.zb(a.f,this.Ge,!0)};
g.af=function(a){this.zb(a.f,this.Ge,!1)};
g.fh=function(a){this.zb(a.f,this.He,!0,a.subscriptionId)};
g.lh=function(a){this.zb(a.f,this.He,!1)};
g.Hg=function(a){this.zb(a.f,this.vf)};
g.Fg=function(a){this.zb(a.f,this.uf)};
g.He=function(a,b,c){b?(ve(a,op.ld,"true"),c&&ve(a,op.md,c)):(xe(a,op.ld),xe(a,op.md));qp(this,a)};
g.cc=function(a){return this.G(a,"channel-external-id")};
g.Ge=function(a,b){var c=I(a,np.gf);D(c,np.hf,b);a.setAttribute("aria-busy",b?"true":"false");a.disabled=b};
function qp(a,b){var c=a.G(b,op.mf),d=!!a.G(b,"is-subscribed"),c="-"+c,e=np.ff+c;D(b,np.ef+c,!d);D(b,e,d);a.G(b,op.kf)&&!a.G(b,op.jf)&&(c=X(dp.getInstance()),D(b,c,!d),b.title=d?"":a.G(b,op.lf));d?L(function(){A(b,np.kd)},1E3):B(b,np.kd)}
g.vf=function(a){var b=!!this.G(a,"ypc-item-type"),c=!!this.G(a,"ypc-item-id");!this.G(a,"ypc-enabled")&&b&&c&&(A(a,"ypc-enabled"),ve(a,op.nf,"true"))};
g.uf=function(a){this.G(a,"ypc-enabled")&&(B(a,"ypc-enabled"),xe(a,"ypc-enabled"))};
function rp(a,b){var c=qd(X(a));return Za(c,function(a){return b==this.cc(a)},a)}
g.bi=function(a,b,c){var d=sb(arguments,2);x(a,function(a){b.apply(this,ob(a,d))},this)};
g.zb=function(a,b,c){var d=rp(this,a),d=ob([d],sb(arguments,1));this.bi.apply(this,d)};
function pp(a,b){var c=u(function(a){a.discoverable_subscriptions&&tf("SUBSCRIBE_EMBED_DISCOVERABLE_SUBSCRIPTIONS",a.discoverable_subscriptions);this.jd(b)},a);
cp(c)}
;function sp(){V.call(this,"subscription-preferences-button")}
w(sp,V);da(sp);sp.prototype.register=function(){Y(this,"click",this.f)};
sp.prototype.unregister=function(){Z(this,"click",this.f);sp.J.unregister.call(this)};
sp.prototype.f=function(a){var b=this.cc(a);Vk(Qo,new Jo(a,b))};
sp.prototype.cc=function(a){return this.G(a,"channel-external-id")};function tp(){V.call(this,"tabs")}
w(tp,V);da(tp);tp.prototype.register=function(){Y(this,"click",this.f,"tab");Y(this,"keydown",this.j,"tab")};
tp.prototype.unregister=function(){Z(this,"click",this.f,"tab");Z(this,"keydown",this.j,"tab");tp.J.unregister.call(this)};
tp.prototype.f=function(a){if(!y(a,"disabled")){var b=this.Y(a),c=X(this,"selected"),d=this.G(b,"uix-tabs-selected-extra-class");if(b=H(c,b)){var e=up(this,b);B(b,c);d&&B(b,d);T(e)}b=up(this,a);A(a,c);d&&A(a,d);S(b);R("yt-uix-tabs-after-shown",a,b)}};
tp.prototype.j=function(a,b,c){13==c.keyCode&&this.f(a)};
function up(a,b){var c=a.G(b,"uix-tabs-target-id");return G(c)}
;function vp(){V.call(this,"tile")}
w(vp,V);da(vp);vp.prototype.register=function(){Y(this,"click",this.f)};
vp.prototype.unregister=function(){Z(this,"click",this.f)};
vp.prototype.f=function(a,b,c){de(c.target,"a")||de(c.target,"button")||!(a=H(X(this,"link"),a))||(F&&!ad(9)?a.click():(y(a,"yt-uix-sessionlink")&&qo(a),y(a,"spf-link")?yk(a.href):xk(a.href)))};var wp=window.yt&&window.yt.uix&&window.yt.uix.widgets_||{};p("yt.uix.widgets_",wp,void 0);function xp(a){a=a.getInstance();var b=X(a);b in wp||(a.register(),a.na("yt-uix-init-"+b,a.init),a.na("yt-uix-dispose-"+b,a.dispose),wp[b]=a)}
;function yp(){this.l=[]}
g=yp.prototype;g.je=function(){};
g.dispose=function(){this.l&&(O(this.l),this.l=[]);this.je()};
function zp(a,b,c,d,e){a.l.push(P(b,c,u(d,a),e))}
g.xa=function(a,b,c,d){this.l.push(N(a,b,u(c,d||this)))};
function Ap(a){if(!a)return!1;var b=a.redirect_url;if(!b)try{var c=hj(a),b=kj(c,"redirect_url")}catch(d){b=null}if(!b)return!1;xk(b);return!0}
g.gc=function(){};
g.mg=function(a,b,c){(c=Ui(c.responseText))&&(a&&Ap(c)||b&&b.call(this,c))};
function Bp(a,b,c,d,e,f,h){a={format:b,method:"POST",onError:h||u(a.gc,a),Fa:ca,R:f||ca,S:e||{},Z:d||{},Na:!0};U(c,a)}
;function Cp(){var a=r("yt.player.getPlayerByElement");return a?a("player-api"):null}
;function Dp(){this.l=[];this.F=this.D="";this.H=null;this.N=this.B=!1;this.U=null;this.P=this.O=""}
w(Dp,yp);g=Dp.prototype;g.ie=function(){};
g.close=function(a){this.N=!1;this.j.dismiss(a||"close");this.dispose()};
g.create=function(a,b,c,d){this.N&&(b&&(this.H=b),c&&(this.U=c),a&&!this.B?this.Ah({},d):this.Ec())};
g.open=function(a,b,c,d,e,f,h,k,l){this.D=a;this.O=b;if(this.C=G(this.D+"-lb")){(a=Cp())&&a.pauseVideo&&a.pauseVideo();if(this.B)this.reset();else{this.j=new Xn(this.C,k);try{this.j.setTitle("")}catch(q){}}Ep(this,"loading");ao(this.j);this.N=!0}d&&this.create(e,f,h,l)};
g.reset=function(){this.B&&Fp(this)};
function Gp(a,b){$n(a.j,b)}
g.Ah=function(a,b,c,d,e){arguments.length&&Ep(this,e||"loading");var f=a||{};this.H&&(f.feature=this.H);this.U&&(f.next=this.U);Bp(this,"XML",this.O,f,b||{},u(this.ng,this,c||null),d)};
function Ep(a,b){switch(b){case "content":Yn(a.j,"content");break;case "loading":Yn(a.j,"loading");break;case "working":Yn(a.j,"working")}}
function Fp(a,b){if(a.B){var c=b||a.P;if(c){if(a.F){var d=a.f;B(d,a.F);A(d,c)}else A(a.f,c);a.F=c}}}
g.Ec=function(a,b){a&&id(G(this.D+"-dialog"),a);if(b)try{this.j.setTitle(b)}catch(d){}this.f=H("yt-dialog-fg",this.C);var c=H("yt-pd-params",this.C);this.P=J(c,"start-page")||"";zp(this,this.f,"click",this.yg,"yt-pd-close");zp(this,this.f,"click",this.wg,"yt-pd-setclass");zp(this,this.f,"click",this.Rg,"yt-pd-setpage");this.ie();Ep(this,"content");this.B=!0;Fp(this)};
g.yg=function(){this.close("cancel")};
g.wg=function(a){a=I(a.target,"yt-pd-setclass");var b=J(a,"off");b&&D(this.f,b,!1);(a=J(a,"on"))&&D(this.f,a,!0)};
g.Rg=function(a){a=I(a.target,"yt-pd-setpage");(a=J(a,"state-container-id"))&&Fp(this,a)};
g.gc=function(a){Dp.J.gc.call(this,a);this.close()};
g.ng=function(a,b,c){var d=hj(b);if(d){var e=kj(d,"not_eligible"),f=kj(d,"error_message");e||f?this.gc(b):Ap(b)||(c=c.html_content||void 0,d=kj(d,"title")||void 0,a?a(b,u(this.Ec,this,c,d)):this.Ec(c,d))}};function Hp(){this.l=[];this.F=this.C=null;this.D=this.A=!1}
w(Hp,yp);g=Hp.prototype;g.Wc=function(){};
g.jc=function(){};
g.init=function(a,b,c,d,e){this.C=a||null;this.F=b||null;c?d&&e&&(zp(this,d,"mousedown",this.cf,e),zp(this,d,"click",this.Ie,e)):(this.cf(),this.Ie())};
g.Zb=function(a,b){var c=Array.prototype.slice.call(arguments,1);(t(a)?r(a+".init"):a.init).apply(this,c)};
g.cf=function(a){this.A||(this.C&&ik(this.C),this.F&&bk(this.F,u(function(){(this.A=!0,this.D)&&this.jc(a)},this)))};
g.Ie=function(a){this.Wc(a);this.D=!0;this.A&&this.D&&this.jc(a)};var Ip={};function Jp(a){var b=la(a),c=Ip[b];c||(c=new a,Ip[b]=c);return c}
;function Kp(){Hp.call(this);this.B=null}
w(Kp,Hp);Kp.prototype.Wc=function(a){a&&(this.B=J(a.currentTarget,"pageid"))};
Kp.prototype.jc=function(){this.Zb("yt.www.account.AddNewChannelDialog",this.B)};function Lp(){Hp.call(this);this.j=this.f=null;this.H=!1}
w(Lp,Hp);function Mp(a,b){var c=Jp(Lp);b&&(c.f=b);c.init(K("CREATE_CHANNEL_CSS_URL",void 0),K("CREATE_CHANNEL_JS_URL",void 0),!a,G("body-container"),"create-channel-lightbox")}
Lp.prototype.Wc=function(a){this.f||(a&&(a=J(a.currentTarget,"upsell"),"settings"==a||"upload"==a||"playlist"==a||"guide"==a||"comment"==a||"message"==a||"captions"==a)&&(this.f=a),this.f||(this.f="default"))};
Lp.prototype.jc=function(){switch(this.f){case "settings":this.j="/profile";break;case "guide":this.j=K("CREATE_CHANNEL_NEXT_URL_GUIDE",void 0);break;case "upload":this.j=K("CREATE_CHANNEL_NEXT_URL_UPLOAD",void 0);break;default:this.j=document.location.href}K("CREATE_CHANNEL_NEXT_URL")&&(this.j=K("CREATE_CHANNEL_NEXT_URL",void 0));if(K("CREATE_CHANNEL_USERNAME_MODE"))this.Zb("yt.www.account.CreateChannelDialog",this.N,this.f,this.j);else if(!this.H){this.H=!0;var a=u(this.mg,this,!1,this.U||null);
Bp(this,"JSON","/create_channel_ajax",{action_get_type:1},{},a)}};
Lp.prototype.N=function(){var a=K("CREATE_CHANNEL_NEXT_URL",void 0);a&&("/"==a?xk(a):window.history.back())};
Lp.prototype.U=function(a){this.H=!1;if(a.open_generic_dialog)Np(this);else if("success"in a&&a.success)switch(a.type){case 15:case 16:this.Zb("yt.www.account.CreateCoreIdChannelDialog",this.N,this.f,this.j);break;case 8:xk("/oops");case 5:xk("/create_channel?action_blocked_misc=1");default:this.Zb("yt.www.account.CreateChannelDialog",this.N,this.f,this.j)}else"redirect_url"in a&&a.redirect_url?xk(a.redirect_url):xk("/oops")};
function Np(a){var b=Jp(Dp),c=a.j;c&&(-1<c.indexOf("create_channel")||-1<c.indexOf("upload")||-1<c.indexOf("profile"))&&(c="/");b.open("create-channel-identity","/create_channel_ajax","create_channel_ajax",!0,!0,a.f,c);c&&Gp(b,function(){xk(c)})}
;var Op,Pp,Qp,Rp,Sp=[],Tp=!1,Up=!1,Vp=[];
function Wp(){var a=G("body-container");Sp.push(P(a,"mousedown",Xp,"link-gplus-lightbox"));Sp.push(P(a,"click",Yp,"link-gplus-lightbox"));Vp.push(Q("LINK-GPLUS-LOADER-DISMISS",Zp));Vp.push(Q("LINK-GPLUS-LOADER-CANCEL",$p));Vp.push(Q("LINK-GPLUS-LOADER-GOTO-CONTENT-STATE",aq));Vp.push(Q("LINK-GPLUS-LOADER-GOTO-WORKING-STATE",bq));Vp.push(Q("LINK-GPLUS-LOADER-SET-WAIT-CURSOR",cq));Vp.push(Q("LINK-GPLUS-LOADER-SHOW-DIALOG",dq));K("SHOW_LINK_GPLUS_LIGHTBOX")&&(Xp(),eq())}
function Zp(){Up=!0;Pp.dismiss("cancel")}
function $p(){Pp.dismiss("cancel")}
function aq(){Yn(Pp,"content")}
function bq(){Yn(Pp,"working")}
function cq(a){a?document.body.style.cursor="wait":"wait"==document.body.style.cursor&&(document.body.style.cursor="default")}
function Xp(){if(!G("link-gplus-css")){bk(K("LINK_GPLUS_JS_URL",void 0),fq);var a=K("LINK_GPLUS_CSS_URL",void 0),b=rd("head",void 0,void 0)[0],a=zd("link",{id:"link-gplus-css",rel:"stylesheet",href:a});Hd(b,a)}}
function fq(){Tp=!0;Op&&Tp&&gq()}
function Yp(a){var b=I(a.target,"link-gplus-lightbox");Qp=y(b,"ignore-opt-out");Rp=J(b,"upsell");a.preventDefault();hq()}
function eq(a){if(a)Qp=!0,Rp=a;else if(Qp=!1,Rp="signin",(a=K("ID_MERGE_FEATURE_TYPE"))&&(Rp=a),"channel"==a||"settings"==a)Qp=!0;hq()}
function hq(){if(!Pp){var a=G("link-gplus-lb");if(!a)return;Pp=new Xn(a,!0)}Up?"upload"==Rp&&xk("/upload"):(Op=!0,cq(!0),Xp(),Tp&&gq())}
function gq(){var a="";if("upload"==Rp)a="/upload";else if("settings"==Rp)a="/account";else if("fans"==Rp)a="/audience";else if("active_signin"==Rp||"channel"==Rp||"comment"==Rp||"plus_page"==Rp)a=K("LINK_GPLUS_NEXT_URL");r("yt.www.account.LinkGplusDialog.fetchAndShow")(Rp,a,Qp)}
function iq(a){Wp();Xp();eq(a)}
function dq(){Yn(Pp,"content");ao(Pp);var a=H("yt-dialog-fg",G("link-gplus-lb")),b;if(b=G(a)){var c=0,d=0;if(b.offsetParent){do c+=b.offsetLeft,d+=b.offsetTop;while(b=b.offsetParent)}b=new Jc(c,d)}else b=null;a.style.position="fixed";a.style.top="95px";b.x&&(a.style.left=b.x+"px")}
;function jq(a,b){if(Fh()){var c=Mh(a),d=0,e=Re()+"-opacity";c&&(c.opacity||c[e])&&(d=c.opacity||c[e]);var f=L(function(){O(h);b.call(a)},d+100),h=N(a,Ck,function(c){c.target==a&&"opacity"==c.propertyName&&(O(h),M(f),b.call(a))})}else L(function(){b.call(a)},0)}
;var kq=Ib({Gi:"yt-alert-success",ERROR:"yt-alert-error",Ii:"yt-alert-warn",INFO:"yt-alert-info",Bi:"yt-alert-promo"});function lq(a,b,c){if(c){var d=qd("yt-alert",c);x(d,function(a){jq(a,function(){a.parentNode&&a.parentNode.removeChild(a)});
A(a,"yt-alert-fading")});
b.removeAttribute("id");Ab(b,kq);A(b,"yt-alert-success");var d=H("yt-alert-message",b),e=H("yt-alert-content",b);(d||e).innerHTML=a;c.appendChild(b);S(b)}}
;function mq(a,b){this.f=new Xn(a,!0);this.B=b;this.A=this.j=this.C=""}
var nq=[],oq=[],pq=null;function qq(a,b,c,d){var e=G("feed-privacy-lb");e&&(pq=new mq(e,a),pq.C=b||"",pq.j=c||"",pq.A=d||"",a=pq,ik(K("FEED_PRIVACY_CSS_URL",void 0)),rq(a,null,{channel_id:a.j,setting_type:a.B,playlist_id:a.A,video_id:a.C}))}
function sq(a){qq("SUBSCRIPTIONS",void 0,a)}
function tq(a){qq("LIKES",a)}
function uq(a){qq("FAVORITES",a)}
function vq(a,b){qq("PLAYLISTS",a,void 0,b)}
function wq(a){qq("LIKE_PLAYLISTS",void 0,void 0,a)}
mq.prototype.dismiss=function(){this.f.dismiss("cancel")};
function rq(a,b,c){a={method:"POST",format:"XML",Z:b||{},S:c||{},Na:!0,R:u(a.D,a),onError:u(a.F,a)};U("/feed_privacy_ajax",a)}
mq.prototype.D=function(a,b){var c=hj(a),d=kj(c,"invalid_request"),e=kj(c,"missing_setting_type"),f=kj(c,"already_seen_dialog");if(!(d||e||f))if(d=G("feed-privacy-dialog"),e=Cp(),c=kj(c,"success_message")){var f=G("alerts"),h;h=yc(b.alert_template_html);h=h.replace(/^[\s\xa0]+/,"");if(ua(h))h=Dd(Hc("<table><tbody>"+h+"</tbody></table>")),h=Ie("tr",h);else{var k=document.createElement("div");k.innerHTML=h;h=Ld(k)}lq(c,h,f);Mf(d);window.scroll(0,0);this.dismiss();e&&e.playVideo&&e.playVideo()}else e&&
e.pauseVideo&&e.pauseVideo(),id(d,b.html_content),oq.push(P(d,"click",u(this.l,this,!1),"make-activity-public-button")),oq.push(P(d,"click",u(this.l,this,!0),"make-activity-private-button")),R("yt-dom-content-change",d),Yn(this.f,"content"),ao(this.f)};
mq.prototype.F=function(){this.dismiss()};
mq.prototype.l=function(a){var b={};b[a?"action_make_private":"action_make_public"]="1";a={setting_type:this.B};Yn(this.f,"working");rq(this,b,a)};function xq(){Dp.call(this);this.A=[];ik(K("IDENTITY_PROMPT_CSS_URL",void 0))}
w(xq,Dp);g=xq.prototype;g.ie=function(){this.A.push(P(this.f,"click",u(this.Kf,this),"identity-prompt-account-list-item"));this.A.push(P(this.f,"click",u(this.Wf,this),"specialized-identity-prompt-account-item"));this.A.push(P(this.f,"click",u(this.Hf,this),"authuser-mismatch-identity-prompt-account-item"))};
g.je=function(){O(this.A);this.A.length=0};
g.Kf=function(a){var b=G("identity-prompt-confirm-button");b?b.disabled=!1:(b=G("identity-prompt-form"),a=rd("input",void 0,a.currentTarget),b&&a&&1==a.length&&(a[0].checked=!0,b.submit()))};
g.Wf=function(a){a=J(a.currentTarget,"switch-url");G("dont_ask_again").checked&&(a+="&dont_ask_again=on");xk(a)};
g.Hf=function(a){a=J(a.currentTarget,"switch-url");xk(a)};function yq(a){a.o=K("CREATOR_CONTEXT","U");return a}
;function zq(a,b,c){this.l=a;this.j=null;(a=b||null)||(a=Aq(this.l));a="("+a.join("|")+")";a=va("__%s__",a);this.j=new RegExp(a,"g");this.f=c||{}}
var Bq=/__([a-z]+(?:_[a-z]+)*)__/g;function Aq(a){var b=[],c={};a.replace(Bq,function(a,e){e in c||(c[e]=!0,b.push(e))});
return b}
zq.prototype.render=function(a,b,c){var d=u(function(d,f){b&&(f=b(f));return c?a[f]||this.f[f]||"":Ba(a[f]||this.f[f]||"")},this);
return this.l.replace(this.j,d)};function Cq(){this.C=null;this.f=[];this.promo=null;this.B="";this.j=null;this.O=pd("mcn-affiliate-agreement-overlay-template");var a=G(this.O).innerHTML,a=a.replace(/^\s*(\x3c!--\s*)?/,""),a=a.replace(/(\s*--\x3e)?\s*$/,"");this.H=new zq(a,void 0,void 0);this.l=!1}
w(Cq,eg);g=Cq.prototype;g.init=function(a,b,c){this.promo=a;this.B=b;this.j=c;this.f.push(N(this.promo,"click",u(this.he,this)));this.f.push(P(this.j,"click",u(this.ge,this),this.B));this.f.push(P(this.j,"click",u(this.Xc,this),"yt-uix-overlay-close"));a=mo();this.f.push(P(a,"click",u(this.Xc,this),"yt-dialog-close"));this.f.push(P(this.j,"click",u(this.Me,this),"mcn-affiliate-agreement-checkbox"));this.f.push(P(this.j,"change",u(this.Me,this),"commerce-creator-signup-text-fields"))};
g.he=function(){throw Error();};
g.ge=function(){throw Error();};
g.Xc=function(){};
g.Me=function(){var a=pd("agreement-checkbox-1"),b=pd("agreement-checkbox-2"),c=pd("agreement-checkbox-3"),d=!0;this.l&&(d=pd("sign-contract-form"),d=y(d,"ng-valid"));H(this.B,void 0).disabled=!(a.checked&&b.checked&&c.checked&&d)};
function Dq(a,b,c,d,e){U("/account_mcn_affiliate_monetization_ajax",{method:"POST",Z:yq({action_load_agreement:1,is_modal:b}),context:a,R:u(function(a,b){this.C=b.contract_tags;this.l="user_contact_info_form"in b;c(b);this.l&&(angular.module("mcnAffiliateAgreement",[]),angular.bootstrap(document,["mcnAffiliateAgreement"]))},a),
onError:u(function(){this.C=null;d()},a),
Fa:u(function(){e()},a)})}
function Eq(){var a=Ba(pd("full_name").value),b=Ba(pd("title").value),c=Ba(pd("email_address").value),d=Ba(pd("phone_number").value),e=Ba(pd("company_name").value);return{full_name:a,title:b,email_address:c,phone_number:d,company_name:e}}
function Fq(a,b,c){pd("agreement-checkbox-1").disabled=!0;pd("agreement-checkbox-2").disabled=!0;pd("agreement-checkbox-3").disabled=!0;var d=pd("agreement-email-yes"),e=yq({action_sign_up:1}),d={contract_tags:a.C.join(),has_commerce_feature:a.l,receive_emails:d.checked};if(a.l){var f=Eq();Tb(d,f)}U("/account_mcn_affiliate_monetization_ajax",{method:"POST",Z:e,S:d,context:a,R:u(function(){b()},a),
onError:u(function(){c()},a)})}
g.M=function(){O(this.f);this.f.length=0;Cq.J.M.call(this)};function Gq(a){Cq.call(this);var b=pd("mcn-affiliate-signup-button"),c=pd("mcn-affiliate-agreement");this.init(b,"mcn-affiliate-sign-agreement-button",c);this.D=a;this.A=!1;K("SHOW_MCNA_YPE_MODAL")&&(this.A=!0,a=K("MCNA_YPE_CONTRACT_CSS_URL",void 0),c=K("ACCOUNT_MONETIZATION_CSS_URL",void 0),ik(K("MCNA_YPE_COMMERCE_CREATOR_CSS_URL",void 0)),ik(a),ik(c),bk(K("ANGULAR_JS",void 0)),b.click())}
w(Gq,Cq);
Gq.prototype.he=function(a){a.preventDefault();a.stopPropagation();var b=a.currentTarget;b.disabled=!0;Dq(this,this.A,u(function(b){a.currentTarget.disabled=!1;var d=a.currentTarget;ho.getInstance().D(d);d=mo();H("yt-dialog-content",d).innerHTML=this.H.render({agreements_title:b.agreements_title,agreements_disclaimer:b.agreements_disclaimer,agreements:b.agreements,review_disclaimer:b.review_disclaimer,underage_message:b.underage_message,agreements_action_buttons:b.agreements_action_buttons,user_contact_info_form:b.user_contact_info_form},
null,!0);a.currentTarget.disabled=!0},this),function(){S(pd("mcn-affiliate-promo-error-msg"))},function(){b.disabled=!1})};
Gq.prototype.ge=function(a){a.preventDefault();a.stopPropagation();a=a.currentTarget;a.disabled||(a.disabled=!0,this.A||(pd("agreement-close-button").disabled=!0),Fq(this,u(this.F,this),function(){ho.getInstance().A();S(pd("mcn-affiliate-promo-error-msg"))}))};
Gq.prototype.Xc=function(){this.A&&U("/account_mcn_affiliate_monetization_ajax",{method:"POST",Z:yq({action_ask_me_later:1}),context:this})};
Gq.prototype.F=function(){this.D?xk(this.D):(ho.getInstance().A(),Gh(this.promo,!1))};function Hq(a,b){eg.call(this);this.j=a;this.B=b;this.f=null;this.l=G("page");this.f=N(G("premium-yva-close"),"click",u(this.Ne,this));var c=this.j;Dg.getInstance().get("HIDDEN_MASTHEAD_ID")==c||A(this.l,"masthead-ad-expanded");this.A=N(window,"message",u(this.Gf,this))}
w(Hq,eg);var Iq=/^https?:\/\/(ad.doubleclick|s0.2mdn).net$/;g=Hq.prototype;g.Gf=function(a){a&&a.origin&&Iq.test(a.origin)&&"creative2yt_requestClose"==a.data&&this.bf()};
g.M=function(){Hq.J.M.call(this);O(this.f);O(this.A);this.A=this.f=null};
g.bf=function(){T("ad_creative_1");R("ads-masthead-hide");R("yt-dom-content-change");this.B&&T("ad_creative_collapse_btn_1");S("ad_creative_expand_btn_1");B(this.l,"masthead-ad-expanded");Jq(this.j);zm("homepage_collapse_masthead_ad",void 0,void 0)};
g.Ne=function(){S("ad_creative_expand_btn_1");Id(G("premium-yva"));Id(G("video-masthead"));R("yt-dom-content-change");Jq(this.j)};
g.Jh=function(){var a=document.getElementById("premium-yva");B(a,"premium-yva-unexpanded");A(a,"premium-yva-expanded")};
g.Kh=function(){var a=document.getElementById("premium-yva");B(a,"premium-yva-expanded");A(a,"premium-yva-unexpanded")};
function Jq(a){Dg.getInstance().set("HIDDEN_MASTHEAD_ID",a);Jg()}
g.pf=function(){T("premium-yva");B(G("premium-yva"),"premium-yva-unexpanded")};
g.ci=function(){Dg.getInstance().set("HIDDEN_MASTHEAD_ID",!1);Jg();zm("homepage_expand_masthead_ad",void 0,void 0);xk(document.location.href)};function Kq(){xo.call(this,"shelfslider")}
w(Kq,xo);da(Kq);Kq.prototype.Jc=function(a){a=mf(a)?Lm(this,"next",a):Lm(this,"prev",a);return window.matchMedia&&0<=document.body.className.indexOf("exp-responsive")&&(void 0).matches?df(a)+a.offsetWidth-NaN:df(a)+a.offsetWidth};
Kq.prototype.Kc=function(a){a=mf(a)?Lm(this,"prev",a):Lm(this,"next",a);return window.matchMedia&&0<=document.body.className.indexOf("exp-responsive")&&(void 0).matches?df(a)+void 0:df(a)};var Lq=!F||bd(9),Mq=F&&!ad("9");!Rc||ad("528");Qc&&ad("1.9b")||F&&ad("8")||Nc&&ad("9.5")||Rc&&ad("528");Qc&&!ad("8")||F&&ad("9");function Nq(a,b){this.type=a;this.currentTarget=this.target=b;this.defaultPrevented=this.f=!1;this.De=!0}
Nq.prototype.stopPropagation=function(){this.f=!0};
Nq.prototype.preventDefault=function(){this.defaultPrevented=!0;this.De=!1};function Oq(a,b){Nq.call(this,a?a.type:"");this.relatedTarget=this.currentTarget=this.target=null;this.charCode=this.keyCode=this.button=this.screenY=this.screenX=this.clientY=this.clientX=0;this.metaKey=this.shiftKey=this.altKey=this.ctrlKey=!1;this.j=this.state=null;a&&this.init(a,b)}
w(Oq,Nq);
Oq.prototype.init=function(a,b){var c=this.type=a.type,d=a.changedTouches?a.changedTouches[0]:null;this.target=a.target||a.srcElement;this.currentTarget=b;var e=a.relatedTarget;e?Qc&&(Ue(e,"nodeName")||(e=null)):"mouseover"==c?e=a.fromElement:"mouseout"==c&&(e=a.toElement);this.relatedTarget=e;null===d?(this.clientX=void 0!==a.clientX?a.clientX:a.pageX,this.clientY=void 0!==a.clientY?a.clientY:a.pageY,this.screenX=a.screenX||0,this.screenY=a.screenY||0):(this.clientX=void 0!==d.clientX?d.clientX:d.pageX,
this.clientY=void 0!==d.clientY?d.clientY:d.pageY,this.screenX=d.screenX||0,this.screenY=d.screenY||0);this.button=a.button;this.keyCode=a.keyCode||0;this.charCode=a.charCode||("keypress"==c?a.keyCode:0);this.ctrlKey=a.ctrlKey;this.altKey=a.altKey;this.shiftKey=a.shiftKey;this.metaKey=a.metaKey;this.state=a.state;this.j=a;a.defaultPrevented&&this.preventDefault()};
Oq.prototype.stopPropagation=function(){Oq.J.stopPropagation.call(this);this.j.stopPropagation?this.j.stopPropagation():this.j.cancelBubble=!0};
Oq.prototype.preventDefault=function(){Oq.J.preventDefault.call(this);var a=this.j;if(a.preventDefault)a.preventDefault();else if(a.returnValue=!1,Mq)try{if(a.ctrlKey||112<=a.keyCode&&123>=a.keyCode)a.keyCode=-1}catch(b){}};var Pq="closure_listenable_"+(1E6*Math.random()|0),Qq=0;function Rq(a,b,c,d,e){this.listener=a;this.f=null;this.src=b;this.type=c;this.Wb=!!d;this.ec=e;this.key=++Qq;this.ub=this.Ub=!1}
function Sq(a){a.ub=!0;a.listener=null;a.f=null;a.src=null;a.ec=null}
;function Tq(a){this.src=a;this.f={};this.j=0}
function Uq(a,b,c,d,e){var f=b.toString();b=a.f[f];b||(b=a.f[f]=[],a.j++);var h=Vq(b,c,d,e);-1<h?(a=b[h],a.Ub=!1):(a=new Rq(c,a.src,f,!!d,e),a.Ub=!1,b.push(a));return a}
Tq.prototype.remove=function(a,b,c,d){a=a.toString();if(!(a in this.f))return!1;var e=this.f[a];b=Vq(e,b,c,d);return-1<b?(Sq(e[b]),lb(e,b),0==e.length&&(delete this.f[a],this.j--),!0):!1};
function Wq(a,b){var c=b.type;c in a.f&&kb(a.f[c],b)&&(Sq(b),0==a.f[c].length&&(delete a.f[c],a.j--))}
Tq.prototype.removeAll=function(a){a=a&&a.toString();var b=0,c;for(c in this.f)if(!a||c==a){for(var d=this.f[c],e=0;e<d.length;e++)++b,Sq(d[e]);delete this.f[c];this.j--}return b};
function Xq(a,b,c,d,e){a=a.f[b.toString()];b=-1;a&&(b=Vq(a,c,d,e));return-1<b?a[b]:null}
function Vq(a,b,c,d){for(var e=0;e<a.length;++e){var f=a[e];if(!f.ub&&f.listener==b&&f.Wb==!!c&&f.ec==d)return e}return-1}
;var Yq="closure_lm_"+(1E6*Math.random()|0),Zq={},$q=0;
function ar(a,b,c,d,e){if(fa(b)){for(var f=0;f<b.length;f++)ar(a,b[f],c,d,e);return null}c=br(c);if(a&&a[Pq])a=a.xa(b,c,d,e);else{if(!b)throw Error("Invalid event type");var f=!!d,h=cr(a);h||(a[Yq]=h=new Tq(a));c=Uq(h,b,c,d,e);if(!c.f){d=dr();c.f=d;d.src=a;d.listener=c;if(a.addEventListener)a.addEventListener(b.toString(),d,f);else if(a.attachEvent)a.attachEvent(er(b.toString()),d);else throw Error("addEventListener and attachEvent are unavailable.");$q++}a=c}return a}
function dr(){var a=fr,b=Lq?function(c){return a.call(b.src,b.listener,c)}:function(c){c=a.call(b.src,b.listener,c);
if(!c)return c};
return b}
function gr(a,b,c,d,e){if(fa(b))for(var f=0;f<b.length;f++)gr(a,b[f],c,d,e);else c=br(c),a&&a[Pq]?a.pc(b,c,d,e):a&&(a=cr(a))&&(b=Xq(a,b,c,!!d,e))&&hr(b)}
function hr(a){if(!ha(a)&&a&&!a.ub){var b=a.src;if(b&&b[Pq])Wq(b.l,a);else{var c=a.type,d=a.f;b.removeEventListener?b.removeEventListener(c,d,a.Wb):b.detachEvent&&b.detachEvent(er(c),d);$q--;(c=cr(b))?(Wq(c,a),0==c.j&&(c.src=null,b[Yq]=null)):Sq(a)}}}
function er(a){return a in Zq?Zq[a]:Zq[a]="on"+a}
function ir(a,b,c,d){var e=!0;if(a=cr(a))if(b=a.f[b.toString()])for(b=b.concat(),a=0;a<b.length;a++){var f=b[a];f&&f.Wb==c&&!f.ub&&(f=jr(f,d),e=e&&!1!==f)}return e}
function jr(a,b){var c=a.listener,d=a.ec||a.src;a.Ub&&hr(a);return c.call(d,b)}
function fr(a,b){if(a.ub)return!0;if(!Lq){var c=b||r("window.event"),d=new Oq(c,this),e=!0;if(!(0>c.keyCode||void 0!=c.returnValue)){a:{var f=!1;if(0==c.keyCode)try{c.keyCode=-1;break a}catch(l){f=!0}if(f||void 0==c.returnValue)c.returnValue=!0}c=[];for(f=d.currentTarget;f;f=f.parentNode)c.push(f);for(var f=a.type,h=c.length-1;!d.f&&0<=h;h--){d.currentTarget=c[h];var k=ir(c[h],f,!0,d),e=e&&k}for(h=0;!d.f&&h<c.length;h++)d.currentTarget=c[h],k=ir(c[h],f,!1,d),e=e&&k}return e}return jr(a,new Oq(b,this))}
function cr(a){a=a[Yq];return a instanceof Tq?a:null}
var kr="__closure_events_fn_"+(1E9*Math.random()>>>0);function br(a){if(ia(a))return a;a[kr]||(a[kr]=function(b){return a.handleEvent(b)});
return a[kr]}
;function lr(){eg.call(this);this.l=new Tq(this);this.Ra=this;this.O=null}
w(lr,eg);lr.prototype[Pq]=!0;g=lr.prototype;g.ad=function(a){this.O=a};
g.addEventListener=function(a,b,c,d){ar(this,a,b,c,d)};
g.removeEventListener=function(a,b,c,d){gr(this,a,b,c,d)};
function mr(a,b){var c,d=a.O;if(d){c=[];for(var e=1;d;d=d.O)c.push(d),++e}var d=a.Ra,e=b,f=e.type||e;if(t(e))e=new Nq(e,d);else if(e instanceof Nq)e.target=e.target||d;else{var h=e,e=new Nq(f,d);Tb(e,h)}var h=!0,k;if(c)for(var l=c.length-1;!e.f&&0<=l;l--)k=e.currentTarget=c[l],h=nr(k,f,!0,e)&&h;e.f||(k=e.currentTarget=d,h=nr(k,f,!0,e)&&h,e.f||(h=nr(k,f,!1,e)&&h));if(c)for(l=0;!e.f&&l<c.length;l++)k=e.currentTarget=c[l],h=nr(k,f,!1,e)&&h}
g.M=function(){lr.J.M.call(this);this.removeAllListeners();this.O=null};
g.xa=function(a,b,c,d){return Uq(this.l,String(a),b,c,d)};
g.pc=function(a,b,c,d){return this.l.remove(String(a),b,c,d)};
g.removeAllListeners=function(a){return this.l?this.l.removeAll(a):0};
function nr(a,b,c,d){b=a.l.f[String(b)];if(!b)return!0;b=b.concat();for(var e=!0,f=0;f<b.length;++f){var h=b[f];if(h&&!h.ub&&h.Wb==c){var k=h.listener,l=h.ec||h.src;h.Ub&&Wq(a.l,h);e=!1!==k.call(l,d)&&e}}return e&&0!=d.De}
;function or(a,b){this.f=0;this.D=void 0;this.A=this.j=this.l=null;this.B=this.C=!1;if(a!=ca)try{var c=this;a.call(b,function(a){pr(c,2,a)},function(a){pr(c,3,a)})}catch(d){pr(this,3,d)}}
function qr(){this.next=this.context=this.j=this.l=this.f=null;this.A=!1}
qr.prototype.reset=function(){this.context=this.j=this.l=this.f=null;this.A=!1};
var rr=new Uf(function(){return new qr},function(a){a.reset()},100);
function sr(a,b,c){var d=rr.get();d.l=a;d.j=b;d.context=c;return d}
function tr(a){if(a instanceof or)return a;var b=new or(ca);pr(b,2,a);return b}
function ur(a,b,c){vr(a,b,c,null)||Zf(ra(b,a))}
function wr(){var a=xr;return new or(function(b,c){var d=a.length,e=[];if(d)for(var f=function(a,c){d--;e[a]=c;0==d&&b(e)},h=function(a){c(a)},k=0,l;k<a.length;k++)l=a[k],ur(l,ra(f,k),h);
else b(e)})}
or.prototype.then=function(a,b,c){return yr(this,ia(a)?a:null,ia(b)?b:null,c)};
or.prototype.then=or.prototype.then;or.prototype.$goog_Thenable=!0;or.prototype.cancel=function(a){0==this.f&&Zf(function(){var b=new zr(a);Ar(this,b)},this)};
function Ar(a,b){if(0==a.f)if(a.l){var c=a.l;if(c.j){for(var d=0,e=null,f=null,h=c.j;h&&(h.A||(d++,h.f==a&&(e=h),!(e&&1<d)));h=h.next)e||(f=h);e&&(0==c.f&&1==d?Ar(c,b):(f?(d=f,d.next==c.A&&(c.A=d),d.next=d.next.next):Br(c),Cr(c,e,3,b)))}a.l=null}else pr(a,3,b)}
function Dr(a,b){a.j||2!=a.f&&3!=a.f||Er(a);a.A?a.A.next=b:a.j=b;a.A=b}
function yr(a,b,c,d){var e=sr(null,null,null);e.f=new or(function(a,h){e.l=b?function(c){try{var e=b.call(d,c);a(e)}catch(q){h(q)}}:a;
e.j=c?function(b){try{var e=c.call(d,b);!n(e)&&b instanceof zr?h(b):a(e)}catch(q){h(q)}}:h});
e.f.l=a;Dr(a,e);return e.f}
or.prototype.N=function(a){this.f=0;pr(this,2,a)};
or.prototype.H=function(a){this.f=0;pr(this,3,a)};
function pr(a,b,c){0==a.f&&(a==c&&(b=3,c=new TypeError("Promise cannot resolve to itself")),a.f=1,vr(c,a.N,a.H,a)||(a.D=c,a.f=b,a.l=null,Er(a),3!=b||c instanceof zr||Fr(a,c)))}
function vr(a,b,c,d){if(a instanceof or)return Dr(a,sr(b||ca,c||null,d)),!0;var e;if(a)try{e=!!a.$goog_Thenable}catch(h){e=!1}else e=!1;if(e)return a.then(b,c,d),!0;if(ka(a))try{var f=a.then;if(ia(f))return Gr(a,f,b,c,d),!0}catch(h){return c.call(d,h),!0}return!1}
function Gr(a,b,c,d,e){function f(a){k||(k=!0,d.call(e,a))}
function h(a){k||(k=!0,c.call(e,a))}
var k=!1;try{b.call(a,h,f)}catch(l){f(l)}}
function Er(a){a.C||(a.C=!0,Zf(a.F,a))}
function Br(a){var b=null;a.j&&(b=a.j,a.j=b.next,b.next=null);a.j||(a.A=null);return b}
or.prototype.F=function(){for(var a=null;a=Br(this);)Cr(this,a,this.f,this.D);this.C=!1};
function Cr(a,b,c,d){if(3==c&&b.j&&!b.A)for(;a&&a.B;a=a.l)a.B=!1;if(b.f)b.f.l=null,Hr(b,c,d);else try{b.A?b.l.call(b.context):Hr(b,c,d)}catch(e){Ir.call(null,e)}Vf(rr,b)}
function Hr(a,b,c){2==b?a.l.call(a.context,c):a.j&&a.j.call(a.context,c)}
function Fr(a,b){a.B=!0;Zf(function(){a.B&&Ir.call(null,b)})}
var Ir=Rf;function zr(a){sa.call(this,a)}
w(zr,sa);zr.prototype.name="cancel";function Jr(a,b){lr.call(this);this.f=a||1;this.j=b||m;this.A=u(this.Cf,this);this.B=v()}
w(Jr,lr);g=Jr.prototype;g.enabled=!1;g.sa=null;function Kr(a,b){a.f=b;a.sa&&a.enabled?(a.stop(),a.start()):a.sa&&a.stop()}
g.Cf=function(){if(this.enabled){var a=v()-this.B;0<a&&a<.8*this.f?this.sa=this.j.setTimeout(this.A,this.f-a):(this.sa&&(this.j.clearTimeout(this.sa),this.sa=null),mr(this,"tick"),this.enabled&&(this.sa=this.j.setTimeout(this.A,this.f),this.B=v()))}};
g.start=function(){this.enabled=!0;this.sa||(this.sa=this.j.setTimeout(this.A,this.f),this.B=v())};
g.stop=function(){this.enabled=!1;this.sa&&(this.j.clearTimeout(this.sa),this.sa=null)};
g.M=function(){Jr.J.M.call(this);this.stop();delete this.j};
function Lr(a,b,c){if(ia(a))c&&(a=u(a,c));else if(a&&"function"==typeof a.handleEvent)a=u(a.handleEvent,a);else throw Error("Invalid listener argument");return 2147483647<Number(b)?-1:m.setTimeout(a,b||0)}
;function Mr(a,b,c){eg.call(this);this.f=a;this.A=b||0;this.j=c;this.l=u(this.Df,this)}
w(Mr,eg);g=Mr.prototype;g.Ib=0;g.M=function(){Mr.J.M.call(this);this.stop();delete this.f;delete this.j};
g.start=function(a){this.stop();this.Ib=Lr(this.l,n(a)?a:this.A)};
g.stop=function(){this.isActive()&&m.clearTimeout(this.Ib);this.Ib=0};
g.isActive=function(){return 0!=this.Ib};
g.Df=function(){this.Ib=0;this.f&&this.f.call(this.j)};var Nr={},Or=null;function Pr(a){a=la(a);delete Nr[a];Mb(Nr)&&Or&&Or.stop()}
function Qr(){Or||(Or=new Mr(function(){Rr()},20));
var a=Or;a.isActive()||a.start()}
function Rr(){var a=v();Db(Nr,function(b){Sr(b,a)});
Mb(Nr)||Qr()}
;function Tr(){lr.call(this);this.f=0;this.endTime=this.startTime=null}
w(Tr,lr);Tr.prototype.A=function(){this.Da("begin")};
Tr.prototype.j=function(){this.Da("end")};
Tr.prototype.Fa=function(){this.Da("finish")};
Tr.prototype.Da=function(a){mr(this,a)};function Ur(a,b,c,d){Tr.call(this);if(!fa(a)||!fa(b))throw Error("Start and end parameters must be arrays");if(a.length!=b.length)throw Error("Start and end points must be the same length");this.C=a;this.X=b;this.duration=c;this.P=d;this.B=[];this.progress=this.V=0;this.H=null}
w(Ur,Tr);g=Ur.prototype;g.play=function(a){if(a||0==this.f)this.progress=0,this.B=this.C;else if(1==this.f)return!1;Pr(this);this.startTime=a=v();-1==this.f&&(this.startTime-=this.duration*this.progress);this.endTime=this.startTime+this.duration;this.H=this.startTime;this.progress||this.A();this.Da("play");-1==this.f&&this.Da("resume");this.f=1;var b=la(this);b in Nr||(Nr[b]=this);Qr();Sr(this,a);return!0};
g.stop=function(a){Pr(this);this.f=0;a&&(this.progress=1);Vr(this,this.progress);this.Da("stop");this.j()};
g.pause=function(){1==this.f&&(Pr(this),this.f=-1,this.Da("pause"))};
g.M=function(){0==this.f||this.stop(!1);this.Da("destroy");Ur.J.M.call(this)};
function Sr(a,b){a.progress=(b-a.startTime)/(a.endTime-a.startTime);1<=a.progress&&(a.progress=1);a.V=1E3/(b-a.H);a.H=b;Vr(a,a.progress);1==a.progress?(a.f=0,Pr(a),a.Fa(),a.j()):1==a.f&&a.Vc()}
function Vr(a,b){ia(a.P)&&(b=a.P(b));a.B=Array(a.C.length);for(var c=0;c<a.C.length;c++)a.B[c]=(a.X[c]-a.C[c])*b+a.C[c]}
g.Vc=function(){this.Da("animate")};
g.Da=function(a){mr(this,new Wr(a,this))};
function Wr(a,b){Nq.call(this,a);this.x=b.B[0];this.y=b.B[1];this.duration=b.duration;this.progress=b.progress;this.fps=b.V;this.state=b.f}
w(Wr,Nq);function Xr(a,b,c,d,e){Ur.call(this,b,c,d,e);this.element=a}
w(Xr,Ur);Xr.prototype.F=ca;Xr.prototype.Vc=function(){this.F();Xr.J.Vc.call(this)};
Xr.prototype.j=function(){this.F();Xr.J.j.call(this)};
Xr.prototype.A=function(){this.F();Xr.J.A.call(this)};
function Yr(a,b,c,d,e){ha(b)&&(b=[b]);ha(c)&&(c=[c]);Xr.call(this,a,b,c,d,e);if(1!=b.length||1!=c.length)throw Error("Start and end points must be 1D");this.D=-1}
w(Yr,Xr);var Zr=1/1024;Yr.prototype.F=function(){var a=this.B[0];if(Math.abs(a-this.D)>=Zr){var b=this.element.style;"opacity"in b?b.opacity=a:"MozOpacity"in b?b.MozOpacity=a:"filter"in b&&(b.filter=""===a?"":"alpha(opacity="+100*Number(a)+")");this.D=a}};
Yr.prototype.A=function(){this.D=-1;Yr.J.A.call(this)};
Yr.prototype.j=function(){this.D=-1;Yr.J.j.call(this)};
function $r(a,b,c){Yr.call(this,a,1,0,b,c)}
w($r,Yr);$r.prototype.A=function(){this.element.style.display="";$r.J.A.call(this)};
$r.prototype.j=function(){this.element.style.display="none";$r.J.j.call(this)};var as=window,bs=document,cs=as.location;function ds(){}
var es=/\[native code\]/;function fs(a,b,c){return a[b]=a[b]||c}
function gs(a){for(var b=0;b<this.length;b++)if(this[b]===a)return b;return-1}
function hs(a){a=a.sort();for(var b=[],c=void 0,d=0;d<a.length;d++){var e=a[d];e!=c&&b.push(e);c=e}return b}
function is(){var a;if((a=Object.create)&&es.test(a))a=a(null);else{a={};for(var b in a)a[b]=void 0}return a}
var js=fs(as,"gapi",{});var ks;ks=fs(as,"___jsl",is());fs(ks,"I",0);fs(ks,"hel",10);function ls(){var a=cs.href,b;if(ks.dpo)b=ks.h;else{b=ks.h;var c=RegExp("([#].*&|[#])jsh=([^&#]*)","g"),d=RegExp("([?#].*&|[?#])jsh=([^&#]*)","g");if(a=a&&(c.exec(a)||d.exec(a)))try{b=decodeURIComponent(a[2])}catch(e){}}return b}
function ms(a){var b=fs(ks,"PQ",[]);ks.PQ=[];var c=b.length;if(0===c)a();else for(var d=0,e=function(){++d===c&&a()},f=0;f<c;f++)b[f](e)}
function ns(a){return fs(fs(ks,"H",is()),a,is())}
;var os=fs(ks,"perf",is());fs(os,"g",is());var ps=fs(os,"i",is());fs(os,"r",[]);is();is();function qs(a,b,c){b&&0<b.length&&(b=rs(b),c&&0<c.length&&(b+="___"+rs(c)),28<b.length&&(b=b.substr(0,28)+(b.length-28)),c=b,b=fs(ps,"_p",is()),fs(b,c,is())[a]=(new Date).getTime(),b=os.r,"function"===typeof b?b(a,"_p",c):b.push([a,"_p",c]))}
function rs(a){return a.join("__").replace(/\./g,"_").replace(/\-/g,"_").replace(/\,/g,"_")}
;var ts=is(),us=[];function vs(a){throw Error("Bad hint"+(a?": "+a:""));}
;us.push(["jsl",function(a){for(var b in a)if(Object.prototype.hasOwnProperty.call(a,b)){var c=a[b];"object"==typeof c?ks[b]=fs(ks,b,[]).concat(c):fs(ks,b,c)}if(b=a.u)a=fs(ks,"us",[]),a.push(b),(b=/^https:(.*)$/.exec(b))&&a.push("http:"+b[1])}]);var ws=/^(\/[a-zA-Z0-9_\-]+)+$/,xs=/^[a-zA-Z0-9\-_\.,!]+$/,ys=/^gapi\.loaded_[0-9]+$/,zs=/^[a-zA-Z0-9,._-]+$/;function As(a,b,c,d){var e=a.split(";"),f=e.shift(),h=ts[f],k=null;h?k=h(e,b,c,d):vs("no hint processor for: "+f);k||vs("failed to generate load url");b=k;c=b.match(Bs);(d=b.match(Cs))&&1===d.length&&Ds.test(b)&&c&&1===c.length||vs("failed sanity: "+a);return k}
function Es(a,b,c,d){function e(a){return encodeURIComponent(a).replace(/%2C/g,",")}
a=Fs(a);ys.test(c)||vs("invalid_callback");b=Gs(b);d=d&&d.length?Gs(d):null;return[encodeURIComponent(a.sh).replace(/%2C/g,",").replace(/%2F/g,"/"),"/k=",e(a.version),"/m=",e(b),d?"/exm="+e(d):"","/rt=j/sv=1/d=1/ed=1",a.pd?"/am="+e(a.pd):"",a.Ce?"/rs="+e(a.Ce):"",a.df?"/t="+e(a.df):"","/cb=",e(c)].join("")}
function Fs(a){"/"!==a.charAt(0)&&vs("relative path");for(var b=a.substring(1).split("/"),c=[];b.length;){a=b.shift();if(!a.length||0==a.indexOf("."))vs("empty/relative directory");else if(0<a.indexOf("=")){b.unshift(a);break}c.push(a)}a={};for(var d=0,e=b.length;d<e;++d){var f=b[d].split("="),h=decodeURIComponent(f[0]),k=decodeURIComponent(f[1]);2==f.length&&h&&k&&(a[h]=a[h]||k)}b="/"+c.join("/");ws.test(b)||vs("invalid_prefix");c=Hs(a,"k",!0);d=Hs(a,"am");e=Hs(a,"rs");a=Hs(a,"t");return{sh:b,version:c,
pd:d,Ce:e,df:a}}
function Gs(a){for(var b=[],c=0,d=a.length;c<d;++c){var e=a[c].replace(/\./g,"_").replace(/-/g,"_");zs.test(e)&&b.push(e)}return b.join(",")}
function Hs(a,b,c){a=a[b];!a&&c&&vs("missing: "+b);if(a){if(xs.test(a))return a;vs("invalid: "+b)}return null}
var Ds=/^https?:\/\/[a-z0-9_.-]+\.google\.com(:\d+)?\/[a-zA-Z0-9_.,!=\-\/]+$/,Cs=/\/cb=/g,Bs=/\/\//g;function Is(){var a=ls();if(!a)throw Error("Bad hint");return a}
ts.m=function(a,b,c,d){(a=a[0])||vs("missing_hint");return"https://apis.google.com"+Es(a,b,c,d)};var Js=decodeURI("%73cript");function Ks(a,b){for(var c=[],d=0;d<a.length;++d){var e=a[d];e&&0>gs.call(b,e)&&c.push(e)}return c}
function Ls(a){"loading"!=bs.readyState?Ms(a):bs.write("<"+Js+' src="'+encodeURI(a)+'"></'+Js+">")}
function Ms(a){var b=bs.createElement(Js);b.setAttribute("src",a);b.async="true";(a=bs.getElementsByTagName(Js)[0])?a.parentNode.insertBefore(b,a):(bs.head||bs.body||bs.documentElement).appendChild(b)}
function Ns(a,b){var c=b&&b._c;if(c)for(var d=0;d<us.length;d++){var e=us[d][0],f=us[d][1];f&&Object.prototype.hasOwnProperty.call(c,e)&&f(c[e],a,b)}}
function Os(a,b,c){Ps(function(){var c;c=b===ls()?fs(js,"_",is()):is();c=fs(ns(b),"_",c);a(c)},c)}
function Qs(a,b){var c=b||{};"function"==typeof b&&(c={},c.callback=b);Ns(a,c);var d=a?a.split(":"):[],e=c.h||Is(),f=fs(ks,"ah",is());if(f["::"]&&d.length){for(var h=[],k=null;k=d.shift();){var l=k.split("."),l=f[k]||f[l[1]&&"ns:"+l[0]||""]||e,q=h.length&&h[h.length-1]||null,z=q;q&&q.hint==l||(z={hint:l,features:[]},h.push(z));z.features.push(k)}var C=h.length;if(1<C){var W=c.callback;W&&(c.callback=function(){0==--C&&W()})}for(;d=h.shift();)Rs(d.features,c,d.hint)}else Rs(d||[],c,e)}
function Rs(a,b,c){function d(a,b){if(C)return 0;as.clearTimeout(z);W.push.apply(W,ma);var d=((js||{}).config||{}).update;d?d(f):f&&fs(ks,"cu",[]).push(f);if(b){qs("me0",a,aa);try{Os(b,c,q)}finally{qs("me1",a,aa)}}return 1}
a=hs(a)||[];var e=b.callback,f=b.config,h=b.timeout,k=b.ontimeout,l=b.onerror,q=void 0;"function"==typeof l&&(q=l);var z=null,C=!1;if(h&&!k||!h&&k)throw"Timeout requires both the timeout parameter and ontimeout parameter to be set";var l=fs(ns(c),"r",[]).sort(),W=fs(ns(c),"L",[]).sort(),aa=[].concat(l);0<h&&(z=as.setTimeout(function(){C=!0;k()},h));
var ma=Ks(a,W);if(ma.length){var ma=Ks(a,l),Xa=fs(ks,"CP",[]),La=Xa.length;Xa[La]=function(a){function b(){var a=Xa[La+1];a&&a()}
function c(b){Xa[La]=null;d(ma,a)&&ms(function(){e&&e();b()})}
if(!a)return 0;qs("ml1",ma,aa);0<La&&Xa[La-1]?Xa[La]=function(){c(b)}:c(b)};
if(ma.length){var tm="loaded_"+ks.I++;js[tm]=function(a){Xa[La](a);js[tm]=null};
a=As(c,ma,"gapi."+tm,l);l.push.apply(l,ma);qs("ml0",ma,aa);b.sync||as.___gapisync?Ls(a):Ms(a)}else Xa[La](ds)}else d(ma)&&e&&e()}
;function Ps(a,b){if(ks.hee&&0<ks.hel)try{return a()}catch(c){b&&b(c),ks.hel--,Qs("debug_error",function(){try{window.___jsl.hefn(c)}catch(a){throw c;}})}else try{return a()}catch(c){throw b&&b(c),c;
}}
;js.load=function(a,b){return Ps(function(){return Qs(a,b)})};function Ss(a,b){var c=ia(b)?{callback:b}:b||{};c._c&&c._c.jsl&&c._c.jsl.h||Tb(c,{_c:{jsl:{h:K("GAPI_HINT_PARAMS",void 0)}}});if(c.gapiHintOverride||K("GAPI_HINT_OVERRIDE")){var d=bi(document.location.href).gapi_jsh;d&&Tb(c,{_c:{jsl:{h:d}}})}Qs(a,c)}
function Ts(a,b,c){var d=K("GAPI_HINT_PARAMS",void 0),e=K("LOGGED_IN"),f=K("SESSION_INDEX",void 0),h=K("DELEGATED_SESSION_ID",void 0),k={lang:K("GAPI_LOCALE",void 0),"googleapis.config":{signedIn:e},iframes:{":socialhost:":K("GAPI_HOST",void 0)}};b&&c&&(k.iframes[b]={url:c});e&&(f&&(k["googleapis.config"].sessionIndex=f),h&&(k["googleapis.config"].sessionDelegate=h));return{callback:a,config:k,_c:{jsl:{h:d}}}}
;function Us(a){eg.call(this);this.j=a;this.f={}}
w(Us,eg);var Vs=[];g=Us.prototype;g.xa=function(a,b,c,d){fa(b)||(b&&(Vs[0]=b.toString()),b=Vs);for(var e=0;e<b.length;e++){var f=ar(a,b[e],c||this.handleEvent,d||!1,this.j||this);if(!f)break;this.f[f.key]=f}return this};
g.pc=function(a,b,c,d,e){if(fa(b))for(var f=0;f<b.length;f++)this.pc(a,b[f],c,d,e);else c=c||this.handleEvent,e=e||this.j||this,c=br(c),d=!!d,b=a&&a[Pq]?Xq(a.l,String(b),c,d,e):a?(a=cr(a))?Xq(a,b,c,d,e):null:null,b&&(hr(b),delete this.f[b.key]);return this};
g.removeAll=function(){Db(this.f,function(a,b){this.f.hasOwnProperty(b)&&hr(a)},this);
this.f={}};
g.M=function(){Us.J.M.call(this);this.removeAll()};
g.handleEvent=function(){throw Error("EventHandler.handleEvent not implemented");};function Ws(){}
da(Ws);Ws.prototype.f=0;function Xs(a){lr.call(this);this.B=a||md();this.X=null;this.Wa=!1;this.f=null;this.j=void 0;this.D=this.F=this.C=null;this.ca=!1}
w(Xs,lr);g=Xs.prototype;g.Zf=Ws.getInstance();g.getId=function(){return this.X||(this.X=":"+(this.Zf.f++).toString(36))};
g.Od=function(){return this.f};
function Ys(a,b){return a.f?H(b,a.f||a.B.f):null}
function Zs(a){a.j||(a.j=new Us(a));return a.j}
g.ad=function(a){if(this.C&&this.C!=a)throw Error("Method not supported");Xs.J.ad.call(this,a)};
g.render=function(a){if(this.Wa)throw Error("Component already rendered");this.f||(this.f=this.B.createElement("DIV"));a?a.insertBefore(this.f,null):this.B.f.body.appendChild(this.f);this.C&&!this.C.Wa||this.Db()};
function $s(a,b){if(a.Wa)throw Error("Component already rendered");if(b&&a.yd(b)){a.ca=!0;var c=od(b);a.B&&a.B.f==c||(a.B=md(b));a.Hc(b);a.Db()}else throw Error("Invalid element to decorate");}
g.yd=function(){return!0};
g.Hc=function(a){this.f=a};
g.Db=function(){this.Wa=!0;at(this,function(a){!a.Wa&&a.Od()&&a.Db()})};
g.Eb=function(){at(this,function(a){a.Wa&&a.Eb()});
this.j&&this.j.removeAll();this.Wa=!1};
g.M=function(){this.Wa&&this.Eb();this.j&&(this.j.dispose(),delete this.j);at(this,function(a){a.dispose()});
!this.ca&&this.f&&Id(this.f);this.C=this.f=this.D=this.F=null;Xs.J.M.call(this)};
function at(a,b){a.F&&x(a.F,b,void 0)}
g.removeChild=function(a,b){if(a){var c=t(a)?a:a.getId(),d;this.D&&c?(d=this.D,d=(null!==d&&c in d?d[c]:void 0)||null):d=null;a=d;if(c&&a){Nb(this.D,c);kb(this.F,a);b&&(a.Eb(),a.f&&Id(a.f));c=a;if(null==c)throw Error("Unable to set parent component");c.C=null;Xs.J.ad.call(c,null)}}if(!a)throw Error("Child is not in parent component");return a};function bt(a){Xs.call(this,a);this.qa=[];this.ga=[]}
w(bt,Xs);bt.prototype.Eb=function(){x(this.qa,O);rg(this.ga);bt.J.Eb.call(this)};
bt.prototype.na=function(a,b,c){a=Q(a,u(b,c||this));this.ga.push(a)};function ct(a){bt.call(this,a);this.H=this.A=this.Ca=this.aa=this.submitButton=null;this.V=0;this.P=[];this.Sc=!1}
w(ct,bt);var dt=[];ct.init=ra(function(a,b,c){0<c.length||(b=qd(b),x(b,function(b){var e=new a;$s(e,b);c.push(e)}))},ct,"legacy-comment-form",dt);
ct.dispose=ra(function(a){x(a,function(a){a.dispose()});
a.length=0},dt);
g=ct.prototype;g.yd=function(a){return a instanceof HTMLFormElement};
g.Hc=function(a){ct.J.Hc.call(this,a);this.aa=Ys(this,"comments-textarea");this.submitButton=Ys(this,"post-button");this.A=Ys(this,"comments-remaining");ye(this.A,"max-count")};
g.Db=function(){ct.J.Db.call(this);Zs(this).xa(this.f,"submit",this.ji).xa(this.aa,"change",this.we).xa(this.aa,"keyup",this.we).xa(this.aa,"focus",this.ii).xa(this.aa,"blur",this.hi);Zs(this).xa(this.aa,"input",this.Uc);this.V=this.aa.offsetHeight};
g.M=function(){this.A=this.Ca=this.aa=this.submitButton=null;ct.J.M.call(this)};
g.reset=function(){this.Ca&&Fd(this.Ca);B(this.f,"has-focus");this.aa.value="";et(this);ft(this);this.aa.blur();this.H=null;R("comment-form-reset")};
g.focus=function(){this.aa.focus()};
function gt(a){return!!a.f.reply_parent_id.value}
function ht(a,b){return parseInt(J(a.A,"max-count"),10)-b.length}
function et(a){var b=ht(a,a.aa.value),c=a.A,d;d=Af(b);Td(c,d);Gh(a.A,15>=b);D(a.A,"too-many",0>b)}
g.we=function(){this.aa.readOnly||(et(this),ft(this))};
function ft(a){0>ht(a,a.aa.value)?a.submitButton.disabled=!0:a.submitButton.disabled=!1}
g.isExpanded=function(){return y(this.f,"has-focus")};
g.ii=function(){this.isExpanded()||T(Ys(this,"comments-post-message"));A(this.f,"has-focus");this.Uc()};
g.hi=function(){this.Uc()};
g.Uc=function(){var a=this.aa.offsetHeight;a!=this.V&&(this.V=a,sg("comment-form-height-changed"))};
g.yf=function(){var a={};return function(){return a}}();
g.ji=function(a){a.preventDefault();a=this.aa.value;if(this.submitButton.disabled||this.aa.readOnly||0==a.length||0>ht(this,a))return!1;this.submitButton.disabled=!0;this.aa.readOnly=!0;a=ai(se(this.f));a.screen=Wh({h:window.screen.height,w:window.screen.width,d:window.screen.colorDepth});var b=a.comment,b={return_ajax:"true",len:b.length,wc:b.split(/\s+/).length};gt(this)&&(b.reply=1);this.H&&(b.tag=this.H);var c=K("PLAYBACK_ID");c&&(a.plid=c);(c=Cp())&&(c=c.getCurrentVideoConfig())&&c.args&&"of"in
c.args&&(a.of=c.args.of);Tb(a,this.yf());c="local-"+la(a);this.P.push({Z:b,S:a,tf:c});R("comment-submit-request-enqueued",a,c);it(this);return!0};
function it(a){if(!a.Sc&&a.P.length){a.Sc=!0;var b=a.P.shift(),c=b.tf;U(a.f.action,{format:"XML",method:"POST",Z:b.Z,S:b.S,R:function(a,b){this.aa.readOnly=!1;this.reset();b.inline_message&&jt(this,b.inline_message);R("comment-submit-success",this,b)},
onError:function(a,b){kt(this,b)},
Fa:function(){this.Sc=!1;it(this)},
context:a});R("comment-submit-request-sent",c)}}
function jt(a,b,c){var d=Ys(a,"comments-post-message");D(d,"yt-alert-error",!!c);D(d,"yt-alert-info",!c);Ys(a,"yt-alert-content").innerHTML=b;S(d)}
function kt(a,b){a.aa.readOnly=!1;var c=b.inline_message||zf("DEFAULT_COMMENT_ERROR_MESSAGE");jt(a,c,!0);b.needs_captcha?U("/comment_servlet?action_gimme_captcha=1",{format:"XML",method:"POST",Na:!0,R:function(a,b){this.Ca||(this.Ca=document.createElement("div"),this.Ca.className="comment-captcha",Gd(this.Ca,this.aa));id(this.Ca,b.html_content);ft(this)},
context:a}):(ft(a),a.Ca&&Fd(a.Ca))}
;function lt(a){this.f=a;this.Je=H("yt-uix-pager-show-more");this.A=[];(a=H("comments-pagination",this.f))&&J(a,"ajax-enabled")&&this.A.push(P(this.f,"click",u(this.l,this),"yt-uix-pager-button"))}
lt.prototype.l=function(a){a.preventDefault();y(a.currentTarget,"yt-uix-button-toggled")||(T(this.Je),R("comments-page-changing"),this.Lb?this.j(2):mt(this,u(this.j,this,2)))};
lt.prototype.j=function(a){var b=G("comments-view");b.appendChild(this.Lb);R("yt-dom-content-change",b);if(b=G("comments-textarea"))b.disabled=!1,b.id="";if(b=H("comment-list",this.f)){var c=H("live-comments-setting",this.f);R("comments-page-changed",b,c,a)}else R("comments-page-changed");mt(this)};
function mt(a,b){var c=rd("li","comment",void 0),c=J(c[c.length-1],"id"),d=a.Lb?!1:!0;S("comments-loading");nt(a,c,function(a,c){c.html_content&&(T("comments-loading"),this.Lb=document.createElement("div"),id(this.Lb,c.html_content),!d&&0<rd("li","comment",this.Lb).length&&S(this.Je),b&&b())})}
function nt(a,b,c){U("/watch_ajax?action_get_comments=1",{format:"XML",Z:{v:K("VIDEO_ID",void 0),p:1,commentthreshold:K("COMMENTS_THRESHHOLD",void 0),commenttype:"everything",last_comment_id:b,page_size:K("COMMENTS_PAGE_SIZE",void 0),source:K("COMMENT_SOURCE",void 0)},Na:!0,R:c,context:a})}
;function ot(a){a=a.currentTarget;var b=I(a,"comment");b||(b=I(a,"comment-actions-menu"),b="comment-"+J(b,"target"),b=G(b));switch(J(a,"action")){case "approve":pt(b);break;case "block":a=b;confirm(zf("BLOCK_USER"))&&(qt(a,!0),A(a,"blocked"));break;case "unblock":a=b;qt(a,!1);B(a,"blocked");break;case "flag":rt(b,"action_mark_comment_as_spam");break;case "flag-profile-pic":rt(b,"action_flag_profile_pic");break;case "unflag":a=J(b,"id");U("/comment_servlet",{format:"XML",method:"POST",S:{action_unmark_comment_as_spam:"1",
comment_id:a,entity_id:K("VIDEO_ID",void 0)}});break;case "hide":A(b,"hidden");break;case "show":B(b,"hidden");break;case "remove":tt(b);break;case "reply":ut(b);break;case "vote-up":vt(b,!0);break;case "vote-down":vt(b,!1);break;case "show-parent":wt(b)}}
function xt(a){return K("COMMENTS_SIGNIN_URL")?(xk(K("COMMENTS_SIGNIN_URL",void 0)),!1):a&&K("COMMENTS_CHANNEL_CREATE_URL")?(xk(K("COMMENTS_CHANNEL_CREATE_URL",void 0)),!1):!0}
function pt(a){var b=J(a,"id"),c=K("VIDEO_ID",void 0);B(a,"pending");U("/comment_servlet?action_approve_comment=1",{format:"XML",method:"POST",S:{comment_id:b,entity_id:c},onError:function(){A(a,"pending")}})}
function qt(a,b){var c={};c["action_"+(b?"":"un")+"block_commenter"]=1;var d=J(a,"id");U("/link_ajax",{format:"XML",method:"POST",Z:c,S:{comment_id:d},R:function(a,b){b&&b.success_message&&window.alert(b.success_message)},
onError:function(a,b){b&&b.error_message&&window.alert(b.error_message)}})}
function rt(a,b){if(xt(!1)){var c=J(a,"id"),d=K("VIDEO_ID",void 0);T(a);A(a,"flagged");c={entity_id:d,comment_id:c};c[b]="1";U("/comment_servlet",{format:"XML",method:"POST",Z:c,onError:function(){S(a);B(a,"flagged")}})}}
function tt(a){var b=J(a,"id"),c=K("VIDEO_ID",void 0);T(a);U("/comment_servlet?action_remove_comment=1",{format:"XML",method:"POST",S:{comment_id:b,entity_id:c},onError:function(){S(a)}})}
function ut(a){if(xt(!0))if(y(a,"replying"))zt(a);else{A(a,"replying");var b=H("comments-post",G("watch-discussion")),c=Fe(b),b=document.createElement("div");b.className="comments-post-container";a.appendChild(b);b.appendChild(c);b=new ct;$s(b,c);b.reset();c=J(a,"id");b.f.reply_parent_id.value=c;At[c]=b;if(a=J(a,"tag"))b.H=a;b.focus()}}
function Bt(a,b){if(b.html_content){var c=pd("all-comments"),d=Dd(Hc(b.html_content));if(gt(a)){gt(a);var e=I(a.f,"comment");H("comments-post-container",e);var f=!!Nd(e);Gd(d,e);d=Nd(e);A(d,"child");D(d,"last",!f);zt(e);A(e,"has-child")}else Hd(c,d);R("yt-dom-content-change",c)}}
function zt(a){if(y(a,"replying")){B(a,"replying");var b=H("comments-post-container",a);Id(b);a=J(a,"id");gg(At[a]);Nb(At,a)}}
function vt(a,b){if(xt(!1)&&!J(a,"voted")){var c=J(a,"id"),d=K("VIDEO_ID",void 0),e=J(a,"score")||"0",f=b?1:-1;ve(a,"voted",f+"");b?(B(a,"voted-down"),A(a,"voted-up")):(B(a,"voted-up"),A(a,"voted-down"));c={a:f,id:c,video_id:d,old_vote:e};(d=J(a,"tag"))&&(c.tag=d);U("/comment_voting",{format:"XML",method:"POST",Z:c})}}
function wt(a){var b=J(a,"id"),c=K("VIDEO_ID",void 0);B(a,"has-child");A(a,"child");var d=G("parent-comment-loading");if(d){var e=Fe(d);a.parentNode&&a.parentNode.insertBefore(e,a);S(e)}U("/comment_servlet?action_get_comment_parent=1",{format:"XML",method:"POST",Na:!0,S:{comment_id:b,entity_id:c},R:function(b,c){var d=document.createElement("ul");id(d,c.html_content);a.parentNode&&a.parentNode.insertBefore(Ld(d),a);Id(e)},
onError:function(b,c){A(a,"has-child");B(a,"child");Id(e);c&&c.error_message?window.alert(c.error_message):window.alert("Request failed, please try later.")}})}
var At={},Ct=[],Dt=[];function Et(a,b,c){this.F=a;this.Ic=b;this.Gh=c;this.j=[];this.f=G("yt-comments-batch");this.D=G("yt-comments-batch-sa");this.l=G("yt-comments-batch-a");this.B=G("yt-comments-batch-r");this.C=G("yt-comments-batch-rs");this.lb=0;this.f&&(this.j.push(P(this.f,"click",u(this.A,this),"batch-button")),Ft(this))}
function Ft(a,b){if(a.f){b&&(a.lb-=b);var c=0>=a.lb;c&&sn(a.D,!1);a.l.disabled=c;a.B.disabled=c;a.C.disabled=c}}
Et.prototype.dispose=function(){O(this.j);this.j=[]};
Et.prototype.A=function(a){var b=I(a.target,"batch-button"),c=J(b,"action");if(c)if("select_all"==c){a.target.blur();var d=a.target.checked,e=0;x(this.Ic(),function(a){sn(a,d);e++});
this.lb=d?e:0;Ft(this)}else H("yt-uix-button",b).blur(),a={},x(this.Ic(),ra(function(a,b,c){if(c.checked){c=I(c,"comment-item");a="reject"==a&&!!J(c,"own");var d=y(c,"reply"),e=J(c,"cid");c=J(c,"vid")||J(c,"is-message")&&"messages"||"@";b[c]||(b[c]=[[],[],[],[]]);b[c][(a?2:0)+(d?1:0)].push(e)}},c,a),this),Gt(this,c,a)};
function Gt(a,b,c){function d(a){if(a.checked){a=I(a,"comment-item");var c=y(a,"reply");this.Gh(a,b,c)}}
var e={action:b},f=[],h;for(h in c)f.push(h+":"+c[h][0].join(",")+"&"+c[h][1].join(",")+"&"+c[h][2].join(",")+"&"+c[h][3].join(","));(c=f.join("/"))&&(e.ids=c);a.F(a,e,{action_batch:"1"},function(){x(this.Ic(),d,this);this.lb=0;Ft(this)})}
;function Ht(a,b,c,d,e){this.f=!!b;this.node=null;this.j=0;this.A=!1;this.l=!c;a&&It(this,a,d);this.depth=void 0!=e?e:this.j||0;this.f&&(this.depth*=-1)}
w(Ht,ge);function It(a,b,c){if(a.node=b)a.j=ha(c)?c:1!=a.node.nodeType?0:a.f?-1:1;ha(void 0)&&(a.depth=void 0)}
Ht.prototype.clone=function(){return new Ht(this.node,this.f,!this.l,this.j,this.depth)};
Ht.prototype.next=function(){var a;if(this.A){if(!this.node||this.l&&0==this.depth)throw fe;a=this.node;var b=this.f?-1:1;if(this.j==b){var c=this.f?a.lastChild:a.firstChild;c?It(this,c):It(this,a,-1*b)}else(c=this.f?a.previousSibling:a.nextSibling)?It(this,c):It(this,a.parentNode,-1*b);this.depth+=this.j*(this.f?-1:1)}else this.A=!0;a=this.node;if(!this.node)throw fe;return a};
Ht.prototype.equals=function(a){return a.node==this.node&&(!this.node||a.j==this.j)};
Ht.prototype.splice=function(a){var b=this.node,c=this.f?1:-1;this.j==c&&(this.j=-1*c,this.depth+=this.j*(this.f?-1:1));this.f=!this.f;Ht.prototype.next.call(this);this.f=!this.f;for(var c=ga(arguments[0])?arguments[0]:arguments,d=c.length-1;0<=d;d--)Gd(c[d],b);Id(b)};function Jt(a,b,c,d){Ht.call(this,a,b,c,null,d)}
w(Jt,Ht);Jt.prototype.next=function(){do Jt.J.next.call(this);while(-1==this.j);return this.node};function Kt(a,b,c){this.f=null;c?bk(c,u(function(){this.f=new botguard.bg(a)},this)):b&&(eval(b),this.f=new botguard.bg(a))}
Kt.prototype.invoke=function(){return this.f?this.f.invoke():null};function Lt(){var a=Nc&&!ad("15");return!F&&!a}
function Mt(){var a=document.createElement("div");try{a.contentEditable="plaintext-only"}catch(b){return!1}return"plaintext-only"==a.contentEditable}
;function Nt(a,b,c){this.X=a;this.ga=b||ca;this.ca=c||null;this.A=[];this.U=this.N=null;this.V=this.H=!1;this.P=this.f=null;this.B=!1;this.C=this.F=null;this.botguard=new Kt(K("COMMENTS_BG_P"),K("COMMENTS_BG_I",""),K("COMMENTS_BG_IU",""));this.A.push(P(null,"click",u(this.reply,this),"yt-commentbox-show-reply"));this.A.push(P(null,"click",u(this.edit,this),"yt-commentbox-show-edit"))}
g=Nt.prototype;g.Tb=function(){if(this.f&&!this.B){var a=I(this.f,"comment-item");S(H("content",a));S(H("actions",a));T(this.D);T(this.l);T(this.f);this.C=null;Fd(this.j)}};
g.edit=function(a){if(this.f||Ot(this)){var b=a.target;y(b,"yt-commentbox-show-edit")||(b=I(a.target,"yt-commentbox-show-edit"));a=I(b,"comment-item");this.Tb();this.V=y(a,"reply");this.F="action_update";this.N=J(a,"cid");this.U=J(a,"vid");this.H=!!J(a,"is-message");T(H("content",a));T(H("actions",a));T(H("yt-commentbox-photo",this.f));a.appendChild(this.f);S(this.D);S(this.f);a=H("comment-editable-text-content",a);Ge(a,this.j);this.j.focus()}};
g.reply=function(a){if(!this.B)if(this.ca)xk(this.ca);else if(this.f||Ot(this)){var b=a.target;y(b,"yt-commentbox-show-reply")||(b=I(a.target,"yt-commentbox-show-reply"));this.Tb();this.F="action_reply";a=J(b,"cid");var c=J(b,"replies"),d=I(b,"yt-commentbox-top"),e=J(b,"vid"),f=!!J(b,"is-message"),h=H("yt-commentbox-container",d);if(c=c?G(c):H("yt-commentbox-replies",d)){h.appendChild(this.f);this.N=a;this.P=c;this.U=e;this.H=f;S(this.l);S(this.f);S(H("yt-commentbox-photo",this.f));Fd(this.j);this.j.focus();
Lt()&&this.l.setAttribute("disabled","disabled");if(b=(this.C=J(b,"reply-to-name"))?"+"+this.C+" ":"")Td(this.j,b),this.l.removeAttribute("disabled");document.createRange?(b=document.createRange(),b.selectNodeContents(this.j),b.collapse(!1),a=window.getSelection(),a.removeAllRanges(),a.addRange(b)):document.selection&&(b=document.body.createTextRange(),b.moveToElementText(this.j),b.collapse(!1),b.select());R("yt-www-comments-sharebox-open")}}};
g.dispose=function(){O(this.A);this.A=[]};
function Ot(a){if(a.f)return!0;a.f=G("ytcb-root");a.j=G("ytcb-text");a.O=G("ytcb-cancel");a.l=G("ytcb-reply");a.D=G("ytcb-save");if(!(a.f&&a.j&&a.O&&a.l&&a.D))return a.f=null,!1;a.A.push(N(a.O,"click",u(a.Tb,a)));a.A.push(N(a.l,"click",u(a.ve,a)));a.A.push(N(a.D,"click",u(a.ve,a)));a.A.push(N(a.j,"input",u(function(){0<this.j.textContent.trim().length?(this.l.removeAttribute("disabled"),this.D.removeAttribute("disabled")):(this.l.setAttribute("disabled","disabled"),this.D.setAttribute("disabled",
"disabled"))},a)));
return!0}
function Pt(a){var b=[];a=new Jt(a.j);a.next();ie(a,function(a){if(3===a.nodeType)b.push(a.textContent);else switch(a.tagName){case "BR":case "DIV":b.push("\n")}});
return ya(b.join(""))}
g.ve=function(){if(!this.B&&this.f&&this.N){var a=Pt(this);if(a&&ya(a)!==ya(this.C?"+"+this.C+" ":"")){this.B=!0;A(this.f,"posting");var b=this.U,a={comment_id:this.N,content:a};b&&(a.video_id=b);this.H&&(a.is_message="1");b={};b[this.F]="1";"action_reply"==this.F?this.X(this,a,b,this.$g,this.Xb):"action_update"==this.F&&(this.V&&(a.is_reply="1"),this.X(this,a,b,this.mh,this.Xb))}}};
g.Xb=function(){this.B=!1;B(this.f,"posting")};
g.$g=function(a,b){if(this.B&&this.f){this.Xb();this.Tb();var c=b.html_content;if(c){var d=zd("DIV");id(d,c);this.P.appendChild(d);c=Ld(d);Jd(d);this.ga(c)}}};
g.mh=function(a,b){if(this.B&&this.f){this.Xb();this.Tb();var c=b.html_content,d=b.editable_content_text;if(c&&d){var e=I(this.f,"comment-item"),f=H("comment-text-content",e);f&&(e=H("comment-editable-text-content",e))&&(id(f,c),c=Je(d),Fd(e),Ed(e,c))}}};function Qt(a){var b=r("yt.www.watch.player.seekTo");b&&b(a)}
function Rt(){R("yt-www-comments-sharebox-open")}
function St(){var a=G("distiller-spinner");a&&T(a)}
function Tt(){var a=G("distiller-alert");a&&S(a)}
function Ut(a,b){U("/comment_voting",{format:"XML",method:"POST",Z:{a:1,id:b,video_id:a,old_vote:0}})}
function Vt(a,b){if(a)if("#"==a){var c=r(b);c&&c("comment")}else xk(a);else Tt()}
function Wt(a){var b=a.channel_id,c=a.create_channel_url,d=a.query,e=a.id_merge_url,f=a.owner_id,h=a.page_size,k=a.privacy_setting,l=a.reauth,q=a.signin_url,z=a.video_id;a=a.viewer_id;var C=!!K("DISTILLER_CONFIG"),W=!z,aa=null;n(q)?aa=l&&C?Tt:ra(xk,q,null,null):n(c)?aa=ra(Vt,c,"yt.www.account.CreateChannelLoader.show"):n(e)&&(aa=ra(Vt,e,"yt.www.account.LinkGplusLoader.show"));b={first_party_property:"YOUTUBE",href:window.top.location,onthumbsup:ra(Ut,z),ontimestampclicked:Qt,onshareboxopen:Rt,onready:St,
owner_id:f,query:d,stream_id:b,substream_id:W?"channel":z,view_type:"FILTERED",width:Xt()};aa&&(b.onupgradeaccount=aa);h&&(b.page_size=h);k&&(b.youtube_video_acl=k);a&&(b.viewer_id=a);W||(b.onallcommentsclicked=ra(xk,"/all_comments",{v:z}));r("gapi.comments.render")("comments-test-iframe",b);Q("page-resize",Yt)}
function Yt(){var a=G("comments-test-iframe"),b=Xt();a&&b&&(a=rd("iframe",null,a),a.length&&(a[0].style.maxWidth=b+"px"))}
function Xt(){var a=G("comments-test-iframe");return(a=a&&Rd(a))?Math.floor(jf(a).width):0}
;function Zt(a){var b=G("yt-comments-sb-standin"),c=b&&!!J(b,"upsell"),d=b&&y(b,"signin");if(c&&!d)return null;c=d?function(){xk(J(b,"url"))}:function(){b&&A(b,"opening");
new $t("yt-comments-sb",a)};
if(b)return Jf(b,"click",c);c();return null}
function au(){var a=G("yt-comments-sb-container");return a?J(a,"vid"):null}
function $t(a,b){this.f=a;this.j=b||null;var c=Ts(u(this.l,this),"ytshare",":socialhost:/:im_prefix::session_prefix:_/widget/render/ytshare?usegapi=1");Ss("ytshare",c);R("yt-www-comments-sharebox-open")}
$t.prototype.l=function(){var a=r("gapi.ytshare.render"),b=K("DISTILLER_CONFIG");if(a&&b){b={first_party_property:"YOUTUBE",href:window.top.location.href,owner_id:b.owner_id,query:b.query,stream_id:b.channel_id,substream_id:b.video_id,youtube_video_acl:b.privacy_setting};this.j&&(b.oncreatecomment=this.j);var c=G(this.f);c&&(b.width=Math.floor(gf(hf,c).width));a(this.f,b)}};function bu(a){for(var b=0;b<a.length;b++){var c=a[b];"send_follow_on_ping_action"==c.name&&c.data&&c.data.follow_on_url&&Xj(c.data.follow_on_url)}}
;function cu(a){this.l=[];this.U=a||"";this.O=new Kt(K("COMMENTS_BG_P"),K("COMMENTS_BG_I",""),K("COMMENTS_BG_IU",""));this.j=pd("yt-comments-sb-container");this.F=pd("yt-comments-sb-standin");this.B=H("yt-simplebox",this.j);this.f=H("yt-simplebox-text",this.j);this.N=H("yt-simplebox-error",this.j);this.D=H("yt-simplebox-dynamic-error",this.j);this.H=H("yt-simplebox-generic-error",this.j);this.C=H("yt-sb-cancel",this.j);this.A=H("yt-sb-post",this.j);this.P=J(this.j,"video-id");this.l.push(N(this.F,
"click",u(this.Dh,this)));this.l.push(N(this.A,"click",u(this.gi,this)));this.l.push(N(this.C,"click",u(this.zd,this)));this.l.push(N(this.f,"input",u(this.fi,this)));Mt()||(this.l.push(N(this.f,"paste",u(this.Sg,this))),this.l.push(N(this.f,"drop",u(this.Gg,this))))}
g=cu.prototype;g.dispose=function(){O(this.l);this.l=[]};
g.Gg=function(a){a.stopPropagation();a.preventDefault()};
g.Sg=function(a){a.stopPropagation();a.preventDefault();J(this.j,"clipboard-exp")&&(a=window.clipboardData&&window.clipboardData.getData("Text")||a.clipboardData&&a.clipboardData.getData("text/plain")||null)&&Td(this.f,a)};
g.fi=function(){this.A.disabled=wa(ce(this.f))};
g.Dh=function(){if(this.U)xk(this.U);else{var a;di()?a=!1:(a=new nj(document.location.href),oj(a,"https"),xk(a.toString()),a=!0);a||(Lt()&&(this.A.disabled=!0),du(this),T(this.F),S(this.B),Fd(this.f),this.f.focus())}};
g.zd=function(){S(this.F);T(this.B);Fd(this.f);S(H("yt-simplebox-dasher-warning",this.j));T(this.N);eu(this)};
g.gi=function(){var a=ce(this.f);wa(a)||(A(this.B,"posting"),this.C.disabled=!0,this.A.disabled=!0,this.f.disabled=!0,this.f.contentEditable=!1,a={content:a,video_id:this.P,bgr:this.O.invoke()},U("/comment_ajax",{format:"JSON",method:"POST",context:this,R:function(a,c){var d=c.html_content,e=G("yt-comments-list"),f=zd("DIV");id(f,d);Hd(e,f);d=H("comment-text-content",f);d.scrollHeight>d.clientHeight+5&&A(H("comment-text",f),"long");Jd(f);this.zd();c.response&&c.response.actions&&bu(c.response.actions)},
onError:function(a,c){eu(this);T(H("yt-simplebox-dasher-warning",this.j));null!=c&&c.errors?(Td(this.D,c.errors[0]),S(this.D),T(this.H)):(S(this.H),T(this.D));S(this.N)},
S:a,Z:{action_create:"1"},Na:!0}))};
function eu(a){B(a.B,"posting");a.A.disabled=!1;a.C.disabled=!1;a.f.disabled=!1;du(a)}
function du(a){Mt()?a.f.setAttribute("contenteditable","plaintext-only"):a.f.contentEditable=!0}
;function fu(){this.f=[];this.N=[];this.ea=G("yt-comments-list");this.H=!!this.ea&&y(Rd(this.ea),"embedded");this.C=J(this.ea,"url");this.D=this.l=null;this.B=new Et(gu,ra(function(a){return qd("yt-uix-form-input-checkbox",a)},this.ea),u(this.ed,this));
this.F=null;this.U=new Nt(gu,u(this.Qc,this),this.C);this.A=G("yt-comments-vi");this.j=G("yt-comments-abuse");hu(this);this.Qc();Ft(this.B)}
function gu(a,b,c,d,e){b=b||{};c=c||{};if(!iu()){if("action_reply"in c){var f=a.botguard.invoke();f&&(b.bgr=f)}U("/comment_ajax",{format:"JSON",method:"POST",context:a,R:d,onError:e,S:b,Z:c,Na:!0})}}
g=fu.prototype;g.di=function(a){return new or(function(b,c){var d=au();d&&gu(this,{content:a,video_id:d},{action_create:"1"},function(a,c){var d=c.html_content;d&&ju(this,d,void 0,!0);b()},function(){c(Error())})},this)};
g.dispose=function(){this.B.dispose();this.U.dispose();this.F&&this.F.dispose();O(this.f);this.f=[];rg(this.N);this.N=[]};
function hu(a){a.f.push(P(a.ea,"click",u(a.zg,a),"comment-checkbox"));a.f.push(P(a.ea,"mouseout",u(a.Ag,a),"comment-entry"));a.f.push(P(a.ea,"click",u(a.Dg,a),"comment-text-toggle"));a.f.push(P(a.ea,"click",u(a.ne,a),"mod-button"));a.f.push(P(a.ea,"keypress",u(a.ne,a),"mod-button"));a.f.push(P(null,"click",u(a.Lg,a),"load-comments"));a.f.push(P(null,"click",u(a.Jg,a),"hide-comments"));a.f.push(P(a.ea,"click",u(a.Cg,a),"comment-text-content"));a.f.push(P(a.ea,"click",u(a.Mg,a),"comments-retry"));a.f.push(P(null,
"click",u(a.Qg,a),"yt-comments-order-menu-button"));a.f.push(N(document,"keyup",u(a.Ig,a)));a.ea&&(a.f.push(N(a.ea,"click",u(a.Bg,a),!0)),a.A&&a.f.push(P(a.ea,"click",u(a.nh,a),"visibility-link")));if(ye(G("yt-comments-sb-container"),"identity-web"))a.F=new cu(a.C);else{var b=Zt(u(a.di,a));b&&a.f.push(b)}a.N.push(Q("yt-www-comments-sharebox-open",iu))}
function iu(){if(di())return!1;var a=new nj(document.location.href);oj(a,"https");xk(a.toString());return!0}
g.Bg=function(){this.A&&T(this.A);this.j&&T(this.j)};
function ku(a,b,c,d,e,f,h){if(b=I(b.target,"comment-item")){b.appendChild(a.j);var k=J(b,"cid")||void 0,l=J(b,"aid")||void 0,q=J(c,"vid")||void 0,z=J(b,"name"),C=u(a.lg,a,c,d,e,f,k,q,h);c=Ts(function(){var a=r("gapi.reportabuse.render");a&&a("yt-comments-abuse-content",{itemId:k,itemOwnerGaiaId:l,itemOwnerName:z,isItemUpdate:!0,location:"DISTILLER",onreportabusecompleted:C})},"reportabuse",":socialhost:/:im_prefix::session_prefix:_/widget/render/reportabuse");
Ss("reportabuse",c);S(a.j)}}
g.lg=function(a,b,c,d,e,f,h){if("report_spam_and_reject"==b){var k={};lu(k,"is_reply",c&&"1");lu(k,"is_message",d&&"1");lu(k,"comment_id",e);lu(k,"video_id",f);d={};lu(d,"action_reject","1");lu(d,"undo",h&&"1");gu(this,k,d,function(){this.ed(a,b,c)})}};
g.Qg=function(a){var b=I(a.target,"yt-comments-order-menu-button"),c=H("comments-order-menu");a=J(b,"value");var d=J(c,"value");if(a!==d){ve(c,"value",a);d=J(b,"vid");b=J(b,"search-terms");ve(c,"search-terms",b);var e=H("comments-wait");e&&S(e);c={};lu(c,"video_id",d);"/comments"==Oh(Ph(5,window.location.href))&&lu(c,"chub",!0);var f={};lu(f,"action_load_comments","1");lu(f,"order_by_time",a);lu(f,"filter",d);lu(f,"search_terms",b);lu(f,"order_menu",!0);var h=function(){T(e)};
gu(this,c,f,function(a,b){h();var c=b.html_content,d=b.page_token,e=G("yt-comments-paginator");c&&(Fd(this.ea),ju(this,c,e),e&&(d?ve(e,"token",d):T(e)))},h)}};
g.nh=function(a){a.target.appendChild(this.A);if(a=I(a.target,"comment-item")){var b=J(a,"cid");a=Ts(function(){var a=r("gapi.visibility.render");a&&a("yt-comments-vi-content",{location:"DISTILLER",updateId:b})},"visibility",":socialhost:/:im_prefix::session_prefix:_/widget/render/visibility");
Ss("visibility",a);S(this.A)}};
g.Qc=function(a){a=qd("comment-text-content",a||this.ea);x(a,function(a){a.scrollHeight>a.clientHeight+5&&(a=I(a,"comment-text"))&&A(a,"long")},this)};
function ju(a,b,c,d){var e=zd("DIV");id(e,b);if(b=c&&J(c,"cid")){d=G("ytcb-"+b);if(!d)return;J(c,"token")||Fd(d);Hd(d,e)}else d?Hd(a.ea,e):a.ea.appendChild(e);a.Qc(e);Jd(e)}
g.Ig=function(a){"keyup"==a.type&&27==a.keyCode&&mu(this)};
function mu(a,b){if(a.l){var c=!!b&&a.l==b,d=H("mod-list",a.l);T(d);a.l=null;return c}return!1}
g.zg=function(a){var b=this.B;b.f&&(a.target.blur(),I(a.target,"comment-item")&&n(a.target.checked)&&(b.lb+=a.target.checked?1:-1,Ft(b)))};
g.Ag=function(a){(a=I(a.relatedTarget,"comment-entry"))?(a=(a=H("comment-item",a))&&J(a,"cid"),a!=this.D&&(this.D=a,mu(this))):(this.D=null,mu(this))};
g.Dg=function(a){var b=I(a.target,"comment-text");b&&(a=wd(document).y,!Cb(b,"expanded")&&(b=H("comment-text-content",b)))&&(a-=wd(document).y,window.scrollBy(0,a-(b.scrollHeight-b.clientHeight)))};
g.Jg=function(a){var b=I(a.target,"comment-replies-header"),c=I(a.target,"comment-entry");a=I(a.target,"hide-comments");if(b&&c&&a){for(var d=H("load-comments",c),e=J(b,"default-reply-count"),e=e?parseInt(e,10):2,c=qd("reply",c),f=0;f<c.length-e;f++)T(c[f]);d&&S(d);T(a);ve(b,"hidden-replies","true")}};
g.Cg=function(a){var b=r("yt.www.watch.player.seekTo"),c=Nf(a);if(b&&"A"===c.tagName){var d=c.getAttribute("href");if(ii(d)&&"/watch"===Oh(Ph(5,d))&&"/watch"===Oh(Ph(5,window.location.href))){var c=bi(d),e;"#"==d.charAt(0)&&(d="!"==d.charAt(1)?d.substr(2):d.substr(1));e=ai(d);d=K("DISTILLER_CONFIG");e=c.t||e.t;d&&c.v===d.video_id&&e&&(c=e.match(/(\d+h)?(\d+m)?(\d+s)/))&&(c=3600*parseInt(c[1]||"0",10)+60*parseInt(c[2]||"0",10)+parseInt(c[3]||"0",10),isNaN(c)||(Pf(a),b(c)))}}};
g.Lg=function(a){var b=I(a.target,"load-comments");if(b){var c=I(b,"comment-replies-header");if(c&&J(c,"hidden-replies")){if(c=I(a.target,"comment-entry"))c=qd("comment-item",c),x(c,function(a){S(a)}),J(b,"token")||T(b),(c=H("hide-comments",Rd(b)))&&S(c)}else{b.disabled=!0;
A(b,"activated");var d=function(){B(b,"activated");b.disabled=!1},e=J(b,"cid"),c=e?"load_replies":"load_comments";
a=(a=de(a.target,"form"))?oe(pe(a)):{};var f=J(H("comments-order-menu"),"value"),h={};lu(h,"comment_id",e);lu(h,"video_id",J(b,"vid"));lu(h,"can_reply",y(b,"can-reply")&&"1");lu(h,"can_moderate",y(b,"can-moderate"));lu(h,"is_message",J(b,"is-message"));lu(h,"page_token",J(b,"token"));lu(h,"search_terms",J(b,"search-terms"));"/comments"==Oh(Ph(5,window.location.href))&&lu(h,"chub",!0);e={};lu(e,"action_"+c,"1");lu(e,"order_by_time",f);lu(e,"tab",J(b,"tab"));Tb(e,a);nu();gu(this,h,e,function(a,c){var e=
c.html_content,f=c.page_token;f&&ve(b,"token",f);if(c.retry)ju(this,e,null),T(b);else{if(e){ju(this,e,b);var h=H("hide-comments",Rd(b));h&&S(h)}e&&!f&&T(b);d()}},d)}}};
g.Mg=function(a){Pf(a);a=G("yt-comments-paginator");S(a);B(a,"activated");a.disabled=!1;Qf(a,"click")};
function nu(){var a=qd("comments-errors");x(a,function(a){Id(a)})}
g.ne=function(a){if("keypress"!=a.type||13==a.keyCode){var b=I(a.target,"mod-list-button")||I(a.target,"mod-button");if(!(!b||this.H&&y(b,"disabled-e")||!this.H&&y(b,"disabled-s"))){var c;if(y(b,"toggle-button")){var d=Cb(b,"is-checked");y(b,"approved-container")?c=d?"add_approved":"remove_approved":y(b,"moderator-container")&&(c=d?"add_moderator":"remove_moderator")}else c=J(b,"action");if(c)if("flag"==c)mu(this,b)||(c=H("mod-list",b),S(c),this.l=b);else if(mu(this),!iu())if(this.C)xk(this.C);else{var e=
I(b,"comment-item"),d=J(e,"aid")||void 0,f=J(e,"cid")||void 0,h=J(e,"vid")||void 0,k=!!J(e,"is-message"),l=y(e,"reply"),q=y(b,"on");if("edit"!=c)if("report_spam"!=c&&"report_spam_and_reject"!=c||!this.j){switch(c){case "approve":case "reject":case "report_spam_and_reject":d=void 0;break;case "delete":case "dislike":case "like":case "report_spam":h=d=void 0}var z,C;if("dislike"==c||"like"==c)B(Nd(b)||(n(b.previousElementSibling)?b.previousElementSibling:Md(b.previousSibling,!1)),"on"),D(b,"on",!q),
b.disabled=!0,D(e,"liked","like"==c&&!q),C=function(){b.disabled=!1},z=function(){D(b,"on",q);
C()};
else if("approve"==c||"ban"==c||"delete"==c||"reject"==c||"report_spam_and_reject"==c)C=u(this.ed,this,e,c,l);a={};lu(a,"is_reply",l&&"1");lu(a,"is_message",k&&"1");lu(a,"user_id",d);lu(a,"comment_id",f);lu(a,"video_id",h);d={};lu(d,"action_"+c,"1");lu(d,"undo",q&&"1");gu(this,a,d,C,z)}else ku(this,a,e,c,l,k,q)}}}};
function lu(a,b,c){c&&(a[b]=c)}
g.ed=function(a,b,c){var d=G("yt-comments-removed-feedback");"delete"==b?d=G("yt-comments-deleted-feedback"):"approve"==b?d=G("yt-comments-approved-feedback"):"ban"==b&&(d=G("yt-comments-banned-feedback"));if(d&&a){a="approve"==b||"add_approved"==b||"add_moderator"==b||"remove_approved"==b||"remove_moderator"==b||c?a:I(a,"comment-entry");if(c=qd("yt-uix-form-input-checkbox",a)){var e=0;x(c,function(a){a.checked&&(sn(a,!1),e++)},this);
Ft(this.B,e)}d=Fe(d);"add_approved"==b||"add_moderator"==b||"remove_approved"==b||"remove_moderator"==b?b=d:(Fd(a),b=a);a.appendChild(d);S(d);(new $r(b,2500)).play()}};var ou=!1,pu=!1;var qu,ru=[];function tu(a){uu("keyboard");27!=a.keyCode||a.event&&!1===a.event.returnValue||!document.activeElement||document.activeElement.blur()}
function vu(){uu("mouse")}
function uu(a){qu!==a&&(qu=a,O(ru),ru.length=0,"keyboard"==qu?(wu(!0),ru=[N(window,"click",vu),N(window,"mousemove",vu)]):"mouse"==qu&&(wu(!1),ru=[N(window,"keydown",tu)]))}
function wu(a){D(document.documentElement,"no-focus-outline",!a)}
;function xu(a){var b=["guide"],b=yu(b);if(b.length){var c=a||{};c.frags=b.join(",");a=K("XSRF_FIELD_NAME",void 0);var d=K("XSRF_TOKEN",void 0),c=Yh("/watch_fragments_ajax",c),e={};e[a]=d;e.client_url=window.location.href;a=Wh(e);zu.push(spf.load(c,{method:"POST",postData:a,onDone:function(){if(fb(b,"guide")){var a=r("yt.www.guide.setup");a&&a(!1);var a=K("GUIDE_SELECTED_ITEM",void 0),c=r("yt.www.guide.selectGuideItem");c&&c(a);R("appbar-guide-delay-load")}R("yt-www-pageFrameCssNotifications-load")}}));
Au=ob(Au,b)}}
function Bu(){var a=bi(window.location.href);a.tr="nonwatch";xu(a)}
function yu(a){return Za(a,function(a){return!fb(Au,a)})}
var zu=[],Au=[],Cu=[];function Du(){return r("gapi.iframes.getContext")()}
function Eu(){var a=Fu;return r("gapi.iframes.makeWhiteListIframesFilter")(a)}
;function Gu(a){if(ga(a))return Hu(a);if(ka(a)&&!ia(a)&&!(ka(a)&&0<a.nodeType))return Iu(a);try{return m.JSON.stringify(a),a}catch(b){}}
function Iu(a){var b={};Db(a,function(a,d){b[d]=Gu(a)});
return b}
function Hu(a){var b=[];x(a,function(a,d){b[d]=Gu(a)});
return b}
;var Fu="http://www.youtube.com https://www.youtube.com https://plus.google.com https://plus.googleapis.com https://plus.sandbox.google.com https://plusone.google.com https://plusone.sandbox.google.com https://apis.google.com https://apis.sandbox.google.com".split(" "),Ju=[So,To,Uo,Xo,Yo,Zo,Vo,$o],Ku=[So,To,Uo,Xo,Yo,Zo,ap,bp];function Lu(a,b){this.j={};this.l=a;this.f=b;var c=u(this.A,this),d=this.f;Du().addOnConnectHandler("ytsubscribe",c,["ytapi"],d)}
Lu.prototype.dispose=function(){Du().removeOnConnectHandler("ytsubscribe")};
Lu.prototype.A=function(a,b){var c=b.id;this.j[c]=a;var d={iframe:a,role:"yt"};Du().connectIframes(d);c=u(this.B,this,c);a.registerWasClosed(c,this.f);a.register("msg-youtube-pubsub",this.l,this.f)};
Lu.prototype.B=function(a){delete this.j[a]};
Lu.prototype.send=function(a,b){Db(this.j,function(c){c.send(a,b,void 0,this.f)},this)};function Mu(){this.f=null;this.j=[]}
da(Mu);g=Mu.prototype;g.init=function(){if(K("UNIVERSAL_HOVERCARDS")){var a=u(this.ki,this),b=K("LOGGED_IN"),c=K("SESSION_INDEX",void 0),d=K("DELEGATED_SESSION_ID",void 0),e={callback:a,"googleapis.config":{signedIn:b},iframes:{card:{url:K("GAPI_HOST",void 0)+"/:session_prefix:_/hovercard/internalcard?p=36&hl="+K("GAPI_LOCALE",void 0)}}};b&&(c&&(e["googleapis.config"].sessionIndex=c),d&&(e["googleapis.config"].sessionDelegate=d));Ss("card:gapi.iframes",{callback:a,config:e})}};
g.dispose=function(){this.f&&(this.f.dispose(),this.f=null);Yk(this.j);this.j.length=0;var a=r("gapi.card.unwatch");a&&a()};
g.ki=function(){var a=r("gapi.config.update");if(a){var b=(Oh(Ph(5,window.location.href))||"/").split("/");a("card/source","youtube"+(b[1]?"."+b[1]:""));a("card/hoverDelay",450);a("card/loadDelay",250);a("card/closeDelay",200);a("card/usegapi",1);a("card",{p:36})}(a=r("gapi.card.watch"))&&a();Nu(this)};
function Nu(a){var b=Eu(),c=u(a.Kg,a);a.f=new Lu(c,b);x(Ku,function(a){this.j.push(Xk(a,ra(this.vh,a),this))},a)}
g.Kg=function(a){if("pubsub2"==a.eventType){var b=cb(Ju,function(b){return b.toString()==a.topicString}),c=a.serializedData;
if(b&&(!b.Yb||c)){var d;if(b.Yb)try{d=Pk(b.Yb,c)}catch(e){return}Vk(b,d)}}};
g.vh=function(a,b){if(this.f){var c=b?b.nc():null,c={eventType:"pubsub2",topicString:a.toString(),serializedData:Gu(c)};this.f.send("cmd-youtube-pubsub",c)}};function Ou(a,b,c){Pu("add_to_watch_later_list",a,b,c)}
function Qu(a){Pu("delete_from_watch_later_list",a,void 0,void 0)}
function Pu(a,b,c,d){U(c?c+"playlist_video_ajax?action_"+a+"=1":"/playlist_video_ajax?action_"+a+"=1",{method:"POST",Z:{feature:b.feature||null,authuser:b.Mi||null,pageid:b.pageId||null},S:{video_ids:b.videoIds||null,source_playlist_id:b.sourcePlaylistId||null,full_list_id:b.fullListId||null,delete_from_playlists:b.Ti||null,add_to_playlists:b.Ki||null,plid:K("PLAYBACK_ID")||null},context:b.context,onError:b.onError,R:function(a,c){var d=c.result;d&&d.actions&&bu(d.actions);b.R.call(this,a,c)},
Fa:b.Fa,withCredentials:!!d})}
;var Ru=[],Su="";function Tu(){Fm("addto-watch-later-button","click",Uu);Fm("addto-watch-later-button-success","click",Vu);Fm("addto-watch-later-button-remove","click",Wu);Fm("addto-watch-later-button-sign-in","click",Xu);var a=G("shared-addto-watch-later-login");Ru.push(P(a,"click",Yu,"sign-in-link"));Ru.push(P(a,Bk,Zu,"sign-in-link"))}
function Xu(a){Su=J(a,"video-ids");var b=H("sign-in-link",G("shared-addto-watch-later-login"));b&&(A(a,"addto-wl-focused"),L(function(){b.focus()},0))}
function Zu(){var a=H("addto-wl-focused");a&&(B(a,"addto-wl-focused"),L(function(){a.focus()},0))}
function Yu(a){var b=ci("/addto_ajax",{action_redirect_to_signin_with_add:1,video_ids:Su,next_url:document.location}),c=document.createElement("form");c.action=b;c.method="POST";b=document.createElement("input");b.type="hidden";b.name=K("XSRF_FIELD_NAME",void 0);b.value=K("XSRF_TOKEN",void 0);c.appendChild(b);document.body.appendChild(c);c.submit();a.preventDefault()}
function Uu(a){Bb(a,"addto-watch-later-button","addto-watch-later-button-loading");sd(a,{"aria-pressed":"true"});var b=J(a,"video-ids");Ou({videoIds:b,R:function(c,d){var e=d.list_id;$u(e,b,a);R("playlist-addto",b,e)},
onError:function(c,d){6==d.return_code?$u(d.list_id,b,a):av(a,d)}})}
function Vu(a){Bb(a,"addto-watch-later-button-success","addto-watch-later-button-loading");var b=J(a,"video-ids");Qu({videoIds:b,R:function(){Bb(a,"addto-watch-later-button-loading","addto-watch-later-button");var b=zf("ADDTO_WATCH_LATER");ip(dp.getInstance(),a,b);R("WATCH_LATER_VIDEO_REMOVED")},
onError:function(b,d){av(a,d)}})}
function Wu(a){var b=J(a,"video-ids");Qu({videoIds:b,R:function(b,d){R("WATCH_LATER_VIDEO_REMOVED",a,d.result.video_count)},
onError:function(b,d){av(a,d)}})}
function $u(a,b,c){Bb(c,"addto-watch-later-button-loading","addto-watch-later-button-success");var d=zf("ADDTO_WATCH_LATER_ADDED");ip(dp.getInstance(),c,d);R("WATCH_LATER_VIDEO_ADDED",a,b.split(","))}
function av(a,b){Bb(a,"addto-watch-later-button-loading","addto-watch-later-button-error");var c=b.error_message||zf("ADDTO_WATCH_LATER_ERROR");ip(dp.getInstance(),a,c)}
;var bv,cv=[],dv=[],ev=[];function fv(a){a=I(a.currentTarget,"overlay-confirmation-content");var b=H("updates-frequency-menu",a);b&&(b.disabled=!H("email-on-upload",a).checked)}
function gv(a){var b=a.currentTarget;a=J(b,"frequency");var c=Mm.getInstance(),b=I(b,X(c,"menu")),c=Mm.getInstance(),b=Rm(c,b);J(b,"frequency")!=a&&ve(b,"frequency",a)}
function hv(a){a=Rd(a);return H("yt-dialog",a)}
function iv(a){bv||(bv=hv(a.button));jv(!0);ho.getInstance().D(bv);var b={};b.c=a.f;U("/subscription_ajax?action_get_subscription_preferences_overlay=1",{method:"POST",S:b,Na:!0,R:function(a,b){var e=b.content_html;jv(!1);var f=H("subscription-preferences-overlay-content",bv);id(f,e);e=mo();f=H("overlay-confirmation-save-button",e);O(ev);ev=[N(f,"click",kv)];ev.push(P(e,"click",fv,"email-on-upload"));ev.push(P(e,"keypressed",fv,"email-on-upload"));ev.push(P(document.body,"click",gv,"updates-frequency-choice"))},
onError:function(){ho.getInstance().A()}})}
function jv(a){var b=bv,c=H("subscription-preferences-overlay-loading",b),b=H("subscription-preferences-overlay-content",b);Gh(c,a);Gh(b,!a)}
function kv(a){var b=I(a.currentTarget,"yt-dialog-fg");if(b){a=J(a.currentTarget,"channel-external-id");var c=null,d=null;(c=H("receive-all-updates",b))?c=c.checked:(d=lv(b),c=d["email-on-upload"],d=d["receive-no-updates"]);R("subscription-prefs",a,c,d,function(){sn(H("email-on-upload",b),null["email-on-upload"])});
ho.getInstance().A()}}
function lv(a){var b=H("email-on-upload",a);a=H("updates-frequency-menu",a);var c=!1,d=!a||y(a,"hidden");d||"occasionally"!=J(a,"frequency")||(c=!0);return{"email-on-upload":b.checked&&!c,"receive-no-updates":d?!1:!b.checked}}
;function mv(a,b){Nk.call(this,1,arguments);this.f=a;this.offerId=b||null}
w(mv,Nk);function nv(a){Nk.call(this,1,arguments);this.Vb=a}
w(nv,Nk);function ov(a,b){Nk.call(this,2,arguments);this.j=a;this.f=b}
w(ov,Nk);function pv(a,b,c,d){Nk.call(this,1,arguments);this.f=b;this.l=c||null;this.j=d||null}
w(pv,Nk);function qv(a,b){Nk.call(this,1,arguments);this.j=a;this.f=b||null}
w(qv,Nk);function rv(a){Nk.call(this,1,arguments);this.f=a}
w(rv,Nk);var tv=new Qk("ypc-init-purchase-for-container",mv),uv=new Qk("ypc-core-load",nv),vv=new Qk("ypc-guide-sync-success",ov),wv=new Qk("ypc-purchase-success",pv),xv=new Qk("ypc-subscription-cancel",rv),yv=new Qk("ypc-subscription-cancel-success",qv),zv=new Qk("ypc-init-subscription",rv);var Av=!1,Bv=[],Cv=[];function Dv(a){a.f?Av?Vk(Vo,a):Vk(uv,new nv(function(){Vk(zv,new rv(a.f))})):Ev(a.j,a.A,a.l,a.source)}
function Fv(a){a.f?Av?Vk($o,a):Vk(uv,new nv(function(){Vk(xv,new rv(a.f))})):Gv(a.j,a.subscriptionId,a.A,a.l,a.source)}
function Hv(a){Iv(pb(a.f))}
function Jv(a){Kv(pb(a.f))}
function Lv(a){Mv(a.f,a.isEnabled,null)}
function Nv(a,b,c,d){Mv(a,b,c,d)}
function Ov(a){var b=a.j,c=a.f.subscriptionId;b&&c&&Vk(Uo,new Lo(b,c,a.f.channelInfo))}
function Pv(a){var b=a.f;Db(a.j,function(a,d){Vk(Uo,new Lo(d,a,b[d]))})}
function Qv(a){Vk(Zo,new Ho(a.j.itemId));a.f&&a.f.length&&(Rv(a.f,Zo),Rv(a.f,ap))}
function Ev(a,b,c,d){var e=new Ho(a);Vk(So,e);var f={};f.c=a;c&&(f.eurl=c);d&&(f.source=d);c={};(d=K("PLAYBACK_ID"))&&(c.plid=d);(d=K("EVENT_ID"))&&(c.ei=d);b&&Sv(b,c);U("/subscription_ajax?action_create_subscription_to_channel=1",{method:"POST",Z:f,S:c,R:function(b,c){var d=c.response;Vk(Uo,new Lo(a,d.id,d.channel_info));d.show_feed_privacy_dialog&&R("SHOW-FEED-PRIVACY-SUBSCRIBE-DIALOG",a);d.actions&&bu(d.actions)},
Fa:function(){Vk(To,e)}})}
function Gv(a,b,c,d,e){var f=new Ho(a);Vk(Xo,f);var h={};d&&(h.eurl=d);e&&(h.source=e);d={};d.c=a;d.s=b;(a=K("PLAYBACK_ID"))&&(d.plid=a);(a=K("EVENT_ID"))&&(d.ei=a);c&&Sv(c,d);U("/subscription_ajax?action_remove_subscriptions=1",{method:"POST",Z:h,S:d,R:function(a,b){var c=b.response;Vk(Zo,f);c.actions&&bu(c.actions)},
Fa:function(){Vk(Yo,f)}})}
function Mv(a,b,c,d){if(null!==b||null!==c){var e={};a&&(e.channel_id=a);null===b||(e.email_on_upload=b);null===c||(e.receive_no_updates=c);U("/subscription_ajax?action_update_subscription_preferences=1",{method:"POST",S:e,onError:function(){d&&d()}})}}
function Iv(a){if(a.length){var b=rb(a,0,40);Vk("subscription-batch-subscribe-loading");Rv(b,So);var c={};c.a=b.join(",");var d=function(){Vk("subscription-batch-subscribe-loaded");Rv(b,To)};
U("/subscription_ajax?action_create_subscription_to_all=1",{method:"POST",S:c,R:function(c,f){d();var h=f.response,k=h.id;if(fa(k)&&k.length==b.length){var l=h.channel_info_map;x(k,function(a,c){var d=b[c];Vk(Uo,new Lo(d,a,l[d]))});
a.length?Iv(a):Vk("subscription-batch-subscribe-finished")}},
onError:function(){d();Vk("subscription-batch-subscribe-failure")}})}}
function Kv(a){if(a.length){var b=rb(a,0,40);Vk("subscription-batch-unsubscribe-loading");Rv(b,Xo);var c={};c.c=b.join(",");var d=function(){Vk("subscription-batch-unsubscribe-loaded");Rv(b,Yo)};
U("/subscription_ajax?action_remove_subscriptions=1",{method:"POST",S:c,R:function(){d();Rv(b,Zo);a.length&&Kv(a)},
onError:function(){d()}})}}
function Rv(a,b){x(a,function(a){Vk(b,new Ho(a))})}
function Sv(a,b){var c=ai(a),d;for(d in c)b[d]=c[d]}
;function Tv(){var a=window.navigator.userAgent.match(/Chrome\/([0-9]+)/);return a?50<=parseInt(a[1],10):!1}
var Uv=document.currentScript&&-1!=document.currentScript.src.indexOf("?loadGamesSDK")?"/cast_game_sender.js":"/cast_sender.js",Vv=["boadgeojelhgndaghljhdicfkmllpafd","dliochdbjfkdbacpmhlcpmleaejidimm","enhhojjnijigcajfphajepfemndkmdlo","fmfcbgogabcbclcofgocippekhfcmgfj"],Wv=["pkedcjkdefgpdelpbcmbmeomcjbeemfm","fjhoaacokmgbjemoflkofnenfaiekifl"],Xv=Tv()?Wv.concat(Vv):Vv.concat(Wv);function Yv(a,b){var c=new XMLHttpRequest;c.onreadystatechange=function(){4==c.readyState&&200==c.status&&b(!0)};
c.onerror=function(){b(!1)};
try{c.open("GET",a,!0),c.send()}catch(d){b(!1)}}
function Zv(a){if(a>=Xv.length)$v();else{var b=Xv[a],c="chrome-extension://"+b+Uv;0<=Vv.indexOf(b)?Yv(c,function(d){d?(window.chrome.cast=window.chrome.cast||{},window.chrome.cast.extensionId=b,aw(c,$v)):Zv(a+1)}):aw(c,function(){Zv(a+1)})}}
function aw(a,b){var c=document.createElement("script");c.onerror=b;c.src=a;(document.head||document.documentElement).appendChild(c)}
function $v(){var a=window.__onGCastApiAvailable;a&&"function"==typeof a&&a(!1,"No cast extension found")}
function bw(){if(window.chrome){var a=window.navigator.userAgent;if(0<=a.indexOf("Android")&&0<=a.indexOf("Chrome/")&&window.navigator.presentation)a=Tv()?"50":"",aw("//www.gstatic.com/eureka/clank"+a+Uv,$v);else{if(0<=window.navigator.userAgent.indexOf("CriOS")&&(a=window.__gCrWeb&&window.__gCrWeb.message&&window.__gCrWeb.message.invokeOnHost)){a({command:"cast.sender.init"});return}Zv(0)}}else $v()}
;var cw=v(),dw=null,ew=Array(50),fw=-1,gw=!1;function hw(a){iw();dw.push(a);jw(dw)}
function kw(a){var b=r("yt.mdx.remote.debug.handlers_");kb(b||[],a)}
function lw(a,b){iw();var c=dw,d=mw(a,String(b));0==c.length?nw(d):(jw(c),x(c,function(a){a(d)}))}
function iw(){dw||(dw=r("yt.mdx.remote.debug.handlers_")||[],p("yt.mdx.remote.debug.handlers_",dw,void 0))}
function nw(a){var b=(fw+1)%50;fw=b;ew[b]=a;gw||(gw=49==b)}
function jw(a){var b=ew;if(b[0]){var c=fw,d=gw?c:-1;do{var d=(d+1)%50,e=b[d];x(a,function(a){a(e)})}while(d!=c);
ew=Array(50);fw=-1;gw=!1}}
function mw(a,b){var c=(v()-cw)/1E3;c.toFixed&&(c=c.toFixed(3));var d=[];d.push("[",c+"s","] ");d.push("[","yt.mdx.remote","] ");d.push(a+": "+b,"\n");return d.join("")}
;function ow(a){a=a||{};this.name=a.name||"";this.id=a.id||a.screenId||"";this.token=a.token||a.loungeToken||"";this.uuid=a.uuid||a.dialId||""}
function pw(a,b){return!!b&&(a.id==b||a.uuid==b)}
function qw(a,b){return a||b?!a!=!b?!1:a.id==b.id&&a.token==b.token&&a.name==b.name&&a.uuid==b.uuid:!0}
function rw(a){return{name:a.name,screenId:a.id,loungeToken:a.token,dialId:a.uuid}}
function tw(a){return new ow(a)}
function uw(a){return fa(a)?$a(a,tw):[]}
function vw(a){return a?'{name:"'+a.name+'",id:'+a.id.substr(0,6)+"..,token:"+(a.token?".."+a.token.slice(-6):"-")+",uuid:"+(a.uuid?".."+a.uuid.slice(-6):"-")+"}":"null"}
function ww(a){return fa(a)?"["+$a(a,vw).join(",")+"]":"null"}
;var xw={mi:"atp",Fi:"ska",Ci:"que",zi:"mus",Ei:"sus"};function yw(a){this.A=this.l="";this.f="/api/lounge";this.j=!0;a=a||document.location.href;var b=Number(Ph(4,a))||null||"";b&&(this.A=":"+b);this.l=Qh(a)||"";a=Xb;0<=a.search("MSIE")&&(a=a.match(/MSIE ([\d.]+)/)[1],0>Ra(a,"10.0")&&(this.j=!1))}
function zw(a,b,c,d){var e=a.f;if(n(d)?d:a.j)e="https://"+a.l+a.A+a.f;return Yh(e+b,c||{})}
function Aw(a,b,c,d,e){a={format:"JSON",method:"POST",context:a,timeout:5E3,withCredentials:!1,R:ra(a.C,d,!0),onError:ra(a.B,e),rb:ra(a.D,e)};c&&(a.S=c,a.headers={"Content-Type":"application/x-www-form-urlencoded"});return U(b,a)}
yw.prototype.C=function(a,b,c,d){b?a(d):a({text:c.responseText})};
yw.prototype.B=function(a,b){a(Error("Request error: "+b.status))};
yw.prototype.D=function(a){a(Error("request timed out"))};function Bw(a){this.f=this.name=this.id="";this.status="UNKNOWN";a&&(this.id=a.id||"",this.name=a.name||"",this.f=a.activityId||"",this.status=a.status||"UNKNOWN")}
function Cw(a){return{id:a.id,name:a.name,activityId:a.f,status:a.status}}
Bw.prototype.toString=function(){return"{id:"+this.id+",name:"+this.name+",activityId:"+this.f+",status:"+this.status+"}"};
function Dw(a){a=a||[];return"["+$a(a,function(a){return a?a.toString():"null"}).join(",")+"]"}
;function Ew(){return"xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx".replace(/[xy]/g,function(a){var b=16*Math.random()|0;return("x"==a?b:b&3|8).toString(16)})}
function Fw(a){return $a(a,function(a){return Cw(a)})}
function Gw(){return $a(ib,function(a){return new Bw(a)})}
function Hw(a,b){return a||b?a&&b?a.id==b.id&&a.name==b.name:!1:!0}
function Iw(a,b){return cb(a,function(a){return a.id==b})}
function Jw(a,b){return cb(a,function(a){return a||b?!a!=!b?!1:a.id==b.id:!0})}
function Kw(a,b){return cb(a,function(a){return pw(a,b)})}
;function Lw(){eg.call(this);this.A=new hg;fg(this,ra(gg,this.A))}
w(Lw,eg);Lw.prototype.subscribe=function(a,b,c){return this.isDisposed()?0:this.A.subscribe(a,b,c)};
Lw.prototype.unsubscribe=function(a,b,c){return this.isDisposed()?!1:this.A.unsubscribe(a,b,c)};
Lw.prototype.va=function(a){return this.isDisposed()?!1:this.A.va(a)};
Lw.prototype.K=function(a,b){return this.isDisposed()?!1:this.A.K.apply(this.A,arguments)};function Mw(a){Lw.call(this);this.D=a;this.screens=[]}
w(Mw,Lw);Mw.prototype.ra=function(){return this.screens};
Mw.prototype.contains=function(a){return!!Jw(this.screens,a)};
Mw.prototype.get=function(a){return a?Kw(this.screens,a):null};
function Nw(a,b){var c=a.get(b.uuid)||a.get(b.id);if(c){var d=c.name;c.id=b.id||c.id;c.name=b.name;c.token=b.token;c.uuid=b.uuid||c.uuid;return c.name!=d}a.screens.push(b);return!0}
function Ow(a,b){var c=a.screens.length!=b.length;a.screens=Za(a.screens,function(a){return!!Jw(b,a)});
for(var d=0,e=b.length;d<e;d++)c=Nw(a,b[d])||c;return c}
function Pw(a,b){var c=a.screens.length;a.screens=Za(a.screens,function(a){return!(a||b?!a!=!b?0:a.id==b.id:1)});
return a.screens.length<c}
Mw.prototype.info=function(a){lw(this.D,a)};function Qw(a,b,c,d){Lw.call(this);this.F=a;this.D=b;this.B=c;this.C=d;this.l=0;this.f=null;this.j=NaN}
w(Qw,Lw);var Rw=[2E3,2E3,1E3,1E3,1E3,2E3,2E3,5E3,5E3,1E4];g=Qw.prototype;g.start=function(){!this.f&&isNaN(this.j)&&this.ye()};
g.stop=function(){this.f&&(this.f.abort(),this.f=null);isNaN(this.j)||(M(this.j),this.j=NaN)};
g.M=function(){this.stop();Qw.J.M.call(this)};
g.ye=function(){this.j=NaN;this.f=U(zw(this.F,"/pairing/get_screen"),{method:"POST",S:{pairing_code:this.D},timeout:5E3,R:u(this.Qh,this),onError:u(this.Ph,this),rb:u(this.Rh,this)})};
g.Qh=function(a,b){this.f=null;var c=b.screen||{};c.dialId=this.B;c.name=this.C;this.K("pairingComplete",new ow(c))};
g.Ph=function(a){this.f=null;a.status&&404==a.status?this.l>=Rw.length?this.K("pairingFailed",Error("DIAL polling timed out")):(a=Rw[this.l],this.j=L(u(this.ye,this),a),this.l++):this.K("pairingFailed",Error("Server error "+a.status))};
g.Rh=function(){this.f=null;this.K("pairingFailed",Error("Server not responding"))};function Sw(a){this.app=this.name=this.id="";this.type="REMOTE_CONTROL";this.avatar=this.username="";this.capabilities=new Ri;this.theme="u";a&&(this.id=a.id||a.name,this.name=a.name,this.app=a.app,this.type=a.type||"REMOTE_CONTROL",this.username=a.user||"",this.avatar=a.userAvatarUri||"",this.theme=a.theme||"u",this.capabilities=new Ri(Za((a.capabilities||"").split(","),ra(Gb,xw))))}
Sw.prototype.equals=function(a){return a?this.id==a.id:!1};var Tw;function Uw(){var a=Vw(),b=Ww();fb(a,b);if(Xw()){var c=a,d;d=0;for(var e=c.length,f;d<e;){var h=d+e>>1,k;k=vb(b,c[h]);0<k?d=h+1:(e=h,f=!k)}d=f?d:~d;0>d&&rb(c,-(d+1),0,b)}a=Yw(a);if(0==a.length)try{Cg("remote_sid")}catch(l){}else try{Ag("remote_sid",a.join(","),-1,"/")}catch(l){}}
function Vw(){var a=um("yt-remote-connected-devices")||[];a.sort(vb);return a}
function Yw(a){if(0==a.length)return[];var b=a[0].indexOf("#"),c=-1==b?a[0]:a[0].substring(0,b);return $a(a,function(a,b){return 0==b?a:a.substring(c.length)})}
function Zw(a){qm("yt-remote-connected-devices",a,86400)}
function Ww(){if($w)return $w;var a=um("yt-remote-device-id");a||(a=Ew(),qm("yt-remote-device-id",a,31536E3));for(var b=Vw(),c=1,d=a;fb(b,d);)c++,d=a+"#"+c;return $w=d}
function ax(){return um("yt-remote-session-browser-channel")}
function Xw(){return um("yt-remote-session-screen-id")}
function bx(a){5<a.length&&(a=a.slice(a.length-5));var b=$a(cx(),function(a){return a.loungeToken}),c=$a(a,function(a){return a.loungeToken});
bb(c,function(a){return!fb(b,a)})&&dx();
qm("yt-remote-local-screens",a,31536E3)}
function cx(){return um("yt-remote-local-screens")||[]}
function dx(){qm("yt-remote-lounge-token-expiration",!0,86400)}
function ex(){return!um("yt-remote-lounge-token-expiration")}
function fx(a){qm("yt-remote-online-screens",a,60)}
function gx(){return um("yt-remote-online-screens")||[]}
function hx(a){qm("yt-remote-online-dial-devices",a,30)}
function ix(a,b){qm("yt-remote-session-browser-channel",a);qm("yt-remote-session-screen-id",b);var c=Vw(),d=Ww();fb(c,d)||c.push(d);Zw(c);Uw()}
function jx(a){a||(vm("yt-remote-session-screen-id"),vm("yt-remote-session-video-id"));Uw();a=Vw();kb(a,Ww());Zw(a)}
function kx(){if(!Tw){var a;a=new fm;(a=a.isAvailable()?a:null)&&(Tw=new hm(a))}return Tw?!!Tw.get("yt-remote-use-staging-server"):!1}
var $w="";function lx(a){Mw.call(this,"LocalScreenService");this.j=a;this.f=NaN;mx(this);this.info("Initializing with "+ww(this.screens))}
w(lx,Mw);g=lx.prototype;g.start=function(){mx(this)&&this.K("screenChange");ex()&&nx(this);M(this.f);this.f=L(u(this.start,this),1E4)};
g.uc=function(a,b){mx(this);Nw(this,a);ox(this,!1);this.K("screenChange");b(a);a.token||nx(this)};
g.remove=function(a,b){var c=mx(this);Pw(this,a)&&(ox(this,!1),c=!0);b(a);c&&this.K("screenChange")};
g.qc=function(a,b,c,d){var e=mx(this),f=this.get(a.id);f?(f.name!=b&&(f.name=b,ox(this,!1),e=!0),c(a)):d(Error("no such local screen."));e&&this.K("screenChange")};
g.M=function(){M(this.f);lx.J.M.call(this)};
function nx(a){if(a.screens.length){var b=$a(a.screens,function(a){return a.id}),c=zw(a.j,"/pairing/get_lounge_token_batch");
Aw(a.j,c,{screen_ids:b.join(",")},u(a.Mf,a),u(a.Lf,a))}}
g.Mf=function(a){mx(this);var b=this.screens.length;a=a&&a.screens||[];for(var c=0,d=a.length;c<d;++c){var e=a[c],f=this.get(e.screenId);f&&(f.token=e.loungeToken,--b)}ox(this,!b);b&&lw(this.D,"Missed "+b+" lounge tokens.")};
g.Lf=function(a){lw(this.D,"Requesting lounge tokens failed: "+a)};
function mx(a){var b=uw(cx()),b=Za(b,function(a){return!a.uuid});
return Ow(a,b)}
function ox(a,b){bx($a(a.screens,rw));b&&dx()}
;function px(a,b){Lw.call(this);this.C=b;for(var c=um("yt-remote-online-screen-ids")||"",c=c?c.split(","):[],d={},e=this.C(),f=0,h=e.length;f<h;++f){var k=e[f].id;d[k]=fb(c,k)}this.f=d;this.D=a;this.l=this.B=NaN;this.j=null;qx("Initialized with "+Wi(this.f))}
w(px,Lw);g=px.prototype;g.start=function(){var a=parseInt(um("yt-remote-fast-check-period")||"0",10);(this.B=v()-144E5<a?0:a)?rx(this):(this.B=v()+3E5,qm("yt-remote-fast-check-period",this.B),this.Yc())};
g.isEmpty=function(){return Mb(this.f)};
g.update=function(){qx("Updating availability on schedule.");var a=this.C(),b=Eb(this.f,function(b,d){return b&&!!Kw(a,d)},this);
sx(this,b)};
function tx(a,b,c){var d=zw(a.D,"/pairing/get_screen_availability");Aw(a.D,d,{lounge_token:b.token},u(function(a){a=a.screens||[];for(var d=0,h=a.length;d<h;++d)if(a[d].loungeToken==b.token){c("online"==a[d].status);return}c(!1)},a),u(function(){c(!1)},a))}
g.M=function(){M(this.l);this.l=NaN;this.j&&(this.j.abort(),this.j=null);px.J.M.call(this)};
function sx(a,b){var c;a:if(Fb(b)!=Fb(a.f))c=!1;else{c=Jb(b);for(var d=0,e=c.length;d<e;++d)if(!a.f[c[d]]){c=!1;break a}c=!0}c||(qx("Updated online screens: "+Wi(a.f)),a.f=b,a.K("screenChange"));ux(a)}
function rx(a){isNaN(a.l)||M(a.l);a.l=L(u(a.Yc,a),0<a.B&&a.B<v()?2E4:1E4)}
g.Yc=function(){M(this.l);this.l=NaN;this.j&&this.j.abort();var a=vx(this);if(Fb(a)){var b=zw(this.D,"/pairing/get_screen_availability"),c={lounge_token:Jb(a).join(",")};this.j=Aw(this.D,b,c,u(this.bh,this,a),u(this.ah,this))}else sx(this,{}),rx(this)};
g.bh=function(a,b){this.j=null;var c=Jb(vx(this));if(tb(c,Jb(a))){for(var c=b.screens||[],d={},e=0,f=c.length;e<f;++e)d[a[c[e].loungeToken]]="online"==c[e].status;sx(this,d);rx(this)}else this.W("Changing Screen set during request."),this.Yc()};
g.ah=function(a){this.W("Screen availability failed: "+a);this.j=null;rx(this)};
function qx(a){lw("OnlineScreenService",a)}
g.W=function(a){lw("OnlineScreenService",a)};
function vx(a){var b={};x(a.C(),function(a){a.token?b[a.token]=a.id:this.W("Requesting availability of screen w/o lounge token.")});
return b}
function ux(a){var b=Jb(Eb(a.f,function(a){return a}));
b.sort(vb);b.length?qm("yt-remote-online-screen-ids",b.join(","),60):vm("yt-remote-online-screen-ids");a=Za(a.C(),function(a){return!!this.f[a.id]},a);
fx($a(a,rw))}
;function wx(a){Mw.call(this,"ScreenService");this.C=a;this.f=this.j=null;this.l=[];this.B={};xx(this)}
w(wx,Mw);g=wx.prototype;g.start=function(){this.j.start();this.f.start();this.screens.length&&(this.K("screenChange"),this.f.isEmpty()||this.K("onlineScreenChange"))};
g.uc=function(a,b,c){this.j.uc(a,b,c)};
g.remove=function(a,b,c){this.j.remove(a,b,c);this.f.update()};
g.qc=function(a,b,c,d){this.j.contains(a)?this.j.qc(a,b,c,d):(a="Updating name of unknown screen: "+a.name,lw(this.D,a),d(Error(a)))};
g.ra=function(a){return a?this.screens:ob(this.screens,Za(this.l,function(a){return!this.contains(a)},this))};
g.Qe=function(){return Za(this.ra(!0),function(a){return!!this.f.f[a.id]},this)};
function yx(a,b,c,d,e,f){a.info("getAutomaticScreenByIds "+c+" / "+b);c||(c=a.B[b]);var h=a.ra();if(h=(c?Kw(h,c):null)||Kw(h,b)){h.uuid=b;var k=zx(a,h);tx(a.f,k,function(a){e(a?k:null)})}else c?Ax(a,c,u(function(a){var f=zx(this,new ow({name:d,
screenId:c,loungeToken:a,dialId:b||""}));tx(this.f,f,function(a){e(a?f:null)})},a),f):e(null)}
g.Re=function(a,b,c,d,e){this.info("getDialScreenByPairingCode "+a+" / "+b);var f=new Qw(this.C,a,b,c);f.subscribe("pairingComplete",u(function(a){gg(f);d(zx(this,a))},this));
f.subscribe("pairingFailed",function(a){gg(f);e(a)});
f.start();return u(f.stop,f)};
function Bx(a,b){for(var c=0,d=a.screens.length;c<d;++c)if(a.screens[c].name==b)return a.screens[c];return null}
g.Qd=function(a,b){for(var c=2,d=b(a,c);Bx(this,d);){c++;if(20<c)return a;d=b(a,c)}return d};
g.Th=function(a,b,c,d){U(zw(this.C,"/pairing/get_screen"),{method:"POST",S:{pairing_code:a},timeout:5E3,R:u(function(a,d){var h=new ow(d.screen||{});if(!h.name||Bx(this,h.name))h.name=this.Qd(h.name,b);c(zx(this,h))},this),
onError:u(function(a){d(Error("pairing request failed: "+a.status))},this),
rb:u(function(){d(Error("pairing request timed out."))},this)})};
g.M=function(){gg(this.j);gg(this.f);wx.J.M.call(this)};
function Ax(a,b,c,d){a.info("requestLoungeToken_ for "+b);var e={S:{screen_ids:b},method:"POST",context:a,R:function(a,e){var k=e&&e.screens||[];k[0]&&k[0].screenId==b?c(k[0].loungeToken):d(Error("Missing lounge token in token response"))},
onError:function(){d(Error("Request screen lounge token failed"))}};
U(zw(a.C,"/pairing/get_lounge_token_batch"),e)}
function Cx(a){a.screens=a.j.ra();var b=a.B,c={},d;for(d in b)c[b[d]]=d;b=0;for(d=a.screens.length;b<d;++b){var e=a.screens[b];e.uuid=c[e.id]||""}a.info("Updated manual screens: "+ww(a.screens))}
g.Vf=function(){Cx(this);this.K("screenChange");this.f.update()};
function xx(a){Dx(a);a.j=new lx(a.C);a.j.subscribe("screenChange",u(a.Vf,a));Cx(a);a.l=uw(um("yt-remote-automatic-screen-cache")||[]);Dx(a);a.info("Initializing automatic screens: "+ww(a.l));a.f=new px(a.C,u(a.ra,a,!0));a.f.subscribe("screenChange",u(function(){this.K("onlineScreenChange")},a))}
function zx(a,b){var c=a.get(b.id);c?(c.uuid=b.uuid,b=c):((c=Kw(a.l,b.uuid))?(c.id=b.id,c.token=b.token,b=c):a.l.push(b),qm("yt-remote-automatic-screen-cache",$a(a.l,rw)));Dx(a);a.B[b.uuid]=b.id;qm("yt-remote-device-id-map",a.B,31536E3);return b}
function Dx(a){a.B=um("yt-remote-device-id-map")||{}}
wx.prototype.dispose=wx.prototype.dispose;function Ex(a,b,c){Lw.call(this);this.X=c;this.O=a;this.j=b;this.l=null}
w(Ex,Lw);g=Ex.prototype;g.ic=function(a){this.l=a;this.K("sessionScreen",this.l)};
g.ma=function(a){this.isDisposed()||(a&&Fx(this,""+a),this.l=null,this.K("sessionScreen",null))};
g.info=function(a){lw(this.X,a)};
function Fx(a,b){lw(a.X,b)}
g.Te=function(){return null};
g.$c=function(a){var b=this.j;a?(b.displayStatus=new chrome.cast.ReceiverDisplayStatus(a,[]),b.displayStatus.showStop=!0):b.displayStatus=null;chrome.cast.setReceiverDisplayStatus(b,u(function(){this.info("Updated receiver status for "+b.friendlyName+": "+a)},this),u(function(){Fx(this,"Failed to update receiver status for: "+b.friendlyName)},this))};
g.M=function(){this.$c("");Ex.J.M.call(this)};function Gx(a,b){Ex.call(this,a,b,"CastSession");this.f=null;this.C=0;this.B=null;this.F=u(this.Uh,this);this.D=u(this.qh,this);this.C=L(u(function(){Hx(this,null)},this),12E4)}
w(Gx,Ex);g=Gx.prototype;g.Zc=function(a){if(this.f){if(this.f==a)return;Fx(this,"Overriding cast sesison with new session object");this.f.removeUpdateListener(this.F);this.f.removeMessageListener("urn:x-cast:com.google.youtube.mdx",this.D)}this.f=a;this.f.addUpdateListener(this.F);this.f.addMessageListener("urn:x-cast:com.google.youtube.mdx",this.D);this.B&&Ix(this);Jx(this,"getMdxSessionStatus")};
g.qb=function(a){this.info("launchWithParams: "+Wi(a));this.B=a;this.f&&Ix(this)};
g.stop=function(){this.f?this.f.stop(u(function(){this.ma()},this),u(function(){this.ma(Error("Failed to stop receiver app."))},this)):this.ma(Error("Stopping cast device witout session."))};
g.$c=ca;g.M=function(){this.info("disposeInternal");M(this.C);this.C=0;this.f&&(this.f.removeUpdateListener(this.F),this.f.removeMessageListener("urn:x-cast:com.google.youtube.mdx",this.D));this.f=null;Gx.J.M.call(this)};
function Ix(a){var b=a.B.videoId||a.B.videoIds[a.B.index];b&&Jx(a,"flingVideo",{videoId:b,currentTime:a.B.currentTime||0});a.B=null}
function Jx(a,b,c){a.info("sendYoutubeMessage_: "+b+" "+Wi(c));var d={};d.type=b;c&&(d.data=c);a.f?a.f.sendMessage("urn:x-cast:com.google.youtube.mdx",d,ca,u(function(){Fx(this,"Failed to send message: "+b+".")},a)):Fx(a,"Sending yt message without session: "+Wi(d))}
g.qh=function(a,b){if(!this.isDisposed())if(b){var c=Vi(b);if(c){var d=""+c.type,c=c.data||{};this.info("onYoutubeMessage_: "+d+" "+Wi(c));switch(d){case "mdxSessionStatus":Hx(this,c.screenId);break;default:Fx(this,"Unknown youtube message: "+d)}}else Fx(this,"Unable to parse message.")}else Fx(this,"No data in message.")};
function Hx(a,b){M(a.C);if(b){if(a.info("onConnectedScreenId_: Received screenId: "+b),!a.l||a.l.id!=b){var c=u(a.ic,a),d=u(a.ma,a);a.Pd(b,c,d,5)}}else a.ma(Error("Waiting for session status timed out."))}
g.Pd=function(a,b,c,d){yx(this.O,this.j.label,a,this.j.friendlyName,u(function(e){e?b(e):0<=d?(Fx(this,"Screen "+a+" appears to be offline. "+d+" retries left."),L(u(this.Pd,this,a,b,c,d-1),300)):c(Error("Unable to fetch screen."))},this),c)};
g.Te=function(){return this.f};
g.Uh=function(a){this.isDisposed()||a||(Fx(this,"Cast session died."),this.ma())};function Kx(a,b){Ex.call(this,a,b,"DialSession");this.C=this.H=null;this.P="";this.B=null;this.F=ca;this.D=NaN;this.V=u(this.Xh,this);this.f=ca}
w(Kx,Ex);g=Kx.prototype;g.Zc=function(a){this.C=a;this.C.addUpdateListener(this.V)};
g.qb=function(a){this.B=a;this.F()};
g.stop=function(){this.f();this.f=ca;M(this.D);this.C?this.C.stop(u(this.ma,this,null),u(this.ma,this,"Failed to stop DIAL device.")):this.ma()};
g.M=function(){this.f();this.f=ca;M(this.D);this.C&&this.C.removeUpdateListener(this.V);this.C=null;Kx.J.M.call(this)};
function Lx(a){a.f=a.O.Re(a.P,a.j.label,a.j.friendlyName,u(function(a){this.f=ca;this.ic(a)},a),u(function(a){this.f=ca;
this.ma(a)},a))}
g.Xh=function(a){this.isDisposed()||a||(Fx(this,"DIAL session died."),this.f(),this.f=ca,this.ma())};
function Mx(a){var b={};b.pairingCode=a.P;if(a.B){var c=a.B.index||0,d=a.B.currentTime||0;b.v=a.B.videoId||a.B.videoIds[c];b.t=d}kx()&&(b.env_useStageMdx=1);return Wh(b)}
g.Rc=function(a){this.P=Ew();if(this.B){var b=new chrome.cast.DialLaunchResponse(!0,Mx(this));a(b);Lx(this)}else this.F=u(function(){M(this.D);this.F=ca;this.D=NaN;var b=new chrome.cast.DialLaunchResponse(!0,Mx(this));a(b);Lx(this)},this),this.D=L(u(function(){this.F()},this),100)};
g.$f=function(a,b){yx(this.O,this.H.receiver.label,a,this.j.friendlyName,u(function(a){a&&a.token?(this.ic(a),b(new chrome.cast.DialLaunchResponse(!1))):this.Rc(b)},this),u(function(a){Fx(this,"Failed to get DIAL screen: "+a);
this.Rc(b)},this))};function Nx(a,b){Ex.call(this,a,b,"ManualSession");this.f=L(u(this.qb,this,null),150)}
w(Nx,Ex);Nx.prototype.stop=function(){this.ma()};
Nx.prototype.Zc=ca;Nx.prototype.qb=function(){M(this.f);this.f=NaN;var a=Kw(this.O.ra(),this.j.label);a?this.ic(a):this.ma(Error("No such screen"))};
Nx.prototype.M=function(){M(this.f);this.f=NaN;Nx.J.M.call(this)};function Ox(a){Lw.call(this);this.j=a;this.f=null;this.C=!1;this.l=[];this.B=u(this.Yg,this)}
w(Ox,Lw);g=Ox.prototype;
g.init=function(a,b){chrome.cast.timeout.requestSession=3E4;var c=new chrome.cast.SessionRequest("233637DE");c.dialRequest=new chrome.cast.DialRequest("YouTube");var d=chrome.cast.AutoJoinPolicy.TAB_AND_ORIGIN_SCOPED,e=a?chrome.cast.DefaultActionPolicy.CAST_THIS_TAB:chrome.cast.DefaultActionPolicy.CREATE_SESSION,c=new chrome.cast.ApiConfig(c,u(this.re,this),u(this.Zg,this),d,e);c.customDialLaunchCallback=u(this.Eg,this);chrome.cast.initialize(c,u(function(){this.isDisposed()||(chrome.cast.addReceiverActionListener(this.B),
hw(Px),this.j.subscribe("onlineScreenChange",u(this.Se,this)),this.l=Qx(this),chrome.cast.setCustomReceivers(this.l,ca,u(function(a){this.W("Failed to set initial custom receivers: "+Wi(a))},this)),this.K("yt-remote-cast2-availability-change",Rx(this)),b(!0))},this),u(function(a){this.W("Failed to initialize API: "+Wi(a));
b(!1)},this))};
g.Bh=function(a,b){Sx("Setting connected screen ID: "+a+" -> "+b);if(this.f){var c=this.f.l;if(!a||c&&c.id!=a)Sx("Unsetting old screen status: "+this.f.j.friendlyName),gg(this.f),this.f=null}if(a&&b){if(!this.f){c=Kw(this.j.ra(),a);if(!c){Sx("setConnectedScreenStatus: Unknown screen.");return}var d=Tx(this,c);d||(Sx("setConnectedScreenStatus: Connected receiver not custom..."),d=new chrome.cast.Receiver(c.uuid?c.uuid:c.id,c.name),d.receiverType=chrome.cast.ReceiverType.CUSTOM,this.l.push(d),chrome.cast.setCustomReceivers(this.l,
ca,u(function(a){this.W("Failed to set initial custom receivers: "+Wi(a))},this)));
Sx("setConnectedScreenStatus: new active receiver: "+d.friendlyName);Ux(this,new Nx(this.j,d),!0)}this.f.$c(b)}else Sx("setConnectedScreenStatus: no screen.")};
function Tx(a,b){return b?cb(a.l,function(a){return pw(b,a.label)},a):null}
g.Ch=function(a){this.isDisposed()?this.W("Setting connection data on disposed cast v2"):this.f?this.f.qb(a):this.W("Setting connection data without a session")};
g.Wh=function(){this.isDisposed()?this.W("Stopping session on disposed cast v2"):this.f?(this.f.stop(),gg(this.f),this.f=null):Sx("Stopping non-existing session")};
g.requestSession=function(){chrome.cast.requestSession(u(this.re,this),u(this.eh,this))};
g.M=function(){this.j.unsubscribe("onlineScreenChange",u(this.Se,this));window.chrome&&chrome.cast&&chrome.cast.removeReceiverActionListener(this.B);kw(Px);gg(this.f);Ox.J.M.call(this)};
function Sx(a){lw("Controller",a)}
g.W=function(a){lw("Controller",a)};
function Px(a){window.chrome&&chrome.cast&&chrome.cast.logMessage&&chrome.cast.logMessage(a)}
function Rx(a){return a.C||!!a.l.length||!!a.f}
function Ux(a,b,c){gg(a.f);(a.f=b)?(c?a.K("yt-remote-cast2-receiver-resumed",b.j):a.K("yt-remote-cast2-receiver-selected",b.j),b.subscribe("sessionScreen",u(a.se,a,b)),b.l?a.K("yt-remote-cast2-session-change",b.l):c&&a.f.qb(null)):a.K("yt-remote-cast2-session-change",null)}
g.se=function(a,b){this.f==a&&(b||Ux(this,null),this.K("yt-remote-cast2-session-change",b))};
g.Yg=function(a,b){if(!this.isDisposed())if(a)switch(Sx("onReceiverAction_ "+a.label+" / "+a.friendlyName+"-- "+b),b){case chrome.cast.ReceiverAction.CAST:if(this.f)if(this.f.j.label!=a.label)Sx("onReceiverAction_: Stopping active receiver: "+this.f.j.friendlyName),this.f.stop();else{Sx("onReceiverAction_: Casting to active receiver.");this.f.l&&this.K("yt-remote-cast2-session-change",this.f.l);break}switch(a.receiverType){case chrome.cast.ReceiverType.CUSTOM:Ux(this,new Nx(this.j,a));break;case chrome.cast.ReceiverType.DIAL:Ux(this,
new Kx(this.j,a));break;case chrome.cast.ReceiverType.CAST:Ux(this,new Gx(this.j,a));break;default:this.W("Unknown receiver type: "+a.receiverType)}break;case chrome.cast.ReceiverAction.STOP:this.f&&this.f.j.label==a.label?this.f.stop():this.W("Stopping receiver w/o session: "+a.friendlyName)}else this.W("onReceiverAction_ called without receiver.")};
g.Eg=function(a){if(this.isDisposed())return Promise.reject(Error("disposed"));var b=a.receiver;b.receiverType!=chrome.cast.ReceiverType.DIAL&&(this.W("Not DIAL receiver: "+b.friendlyName),b.receiverType=chrome.cast.ReceiverType.DIAL);var c=this.f?this.f.j:null;if(!c||c.label!=b.label)return this.W("Receiving DIAL launch request for non-clicked DIAL receiver: "+b.friendlyName),Promise.reject(Error("illegal DIAL launch"));if(c&&c.label==b.label&&c.receiverType!=chrome.cast.ReceiverType.DIAL){if(this.f.l)return Sx("Reselecting dial screen."),
this.K("yt-remote-cast2-session-change",this.f.l),Promise.resolve(new chrome.cast.DialLaunchResponse(!1));this.W('Changing CAST intent from "'+c.receiverType+'" to "dial" for '+b.friendlyName);Ux(this,new Kx(this.j,b))}b=this.f;b.H=a;return b.H.appState==chrome.cast.DialAppState.RUNNING?new Promise(u(b.$f,b,(b.H.extraData||{}).screenId||null)):new Promise(u(b.Rc,b))};
g.re=function(a){if(!this.isDisposed()){Sx("New cast session ID: "+a.sessionId);var b=a.receiver;if(b.receiverType!=chrome.cast.ReceiverType.CUSTOM){if(!this.f)if(b.receiverType==chrome.cast.ReceiverType.CAST)Sx("Got resumed cast session before resumed mdx connection."),Ux(this,new Gx(this.j,b),!0);else{this.W("Got non-cast session without previous mdx receiver event, or mdx resume.");return}var c=this.f.j,d=Kw(this.j.ra(),c.label);d&&pw(d,b.label)&&c.receiverType!=chrome.cast.ReceiverType.CAST&&
b.receiverType==chrome.cast.ReceiverType.CAST&&(Sx("onSessionEstablished_: manual to cast session change "+b.friendlyName),gg(this.f),this.f=new Gx(this.j,b),this.f.subscribe("sessionScreen",u(this.se,this,this.f)),this.f.qb(null));this.f.Zc(a)}}};
g.Vh=function(){return this.f?this.f.Te():null};
g.eh=function(a){this.isDisposed()||(this.W("Failed to estabilish a session: "+Wi(a)),a.code!=chrome.cast.ErrorCode.CANCEL&&Ux(this,null))};
g.Zg=function(a){Sx("Receiver availability updated: "+a);if(!this.isDisposed()){var b=Rx(this);this.C=a==chrome.cast.ReceiverAvailability.AVAILABLE;Rx(this)!=b&&this.K("yt-remote-cast2-availability-change",Rx(this))}};
function Qx(a){var b=a.j.Qe(),c=a.f&&a.f.j;a=$a(b,function(a){c&&pw(a,c.label)&&(c=null);var b=a.uuid?a.uuid:a.id,f=Tx(this,a);f?(f.label=b,f.friendlyName=a.name):(f=new chrome.cast.Receiver(b,a.name),f.receiverType=chrome.cast.ReceiverType.CUSTOM);return f},a);
c&&(c.receiverType!=chrome.cast.ReceiverType.CUSTOM&&(c=new chrome.cast.Receiver(c.label,c.friendlyName),c.receiverType=chrome.cast.ReceiverType.CUSTOM),a.push(c));return a}
g.Se=function(){if(!this.isDisposed()){var a=Rx(this);this.l=Qx(this);Sx("Updating custom receivers: "+Wi(this.l));chrome.cast.setCustomReceivers(this.l,ca,u(function(){this.W("Failed to set custom receivers.")},this));
var b=Rx(this);b!=a&&this.K("yt-remote-cast2-availability-change",b)}};
Ox.prototype.setLaunchParams=Ox.prototype.Ch;Ox.prototype.setConnectedScreenStatus=Ox.prototype.Bh;Ox.prototype.stopSession=Ox.prototype.Wh;Ox.prototype.getCastSession=Ox.prototype.Vh;Ox.prototype.requestSession=Ox.prototype.requestSession;Ox.prototype.init=Ox.prototype.init;Ox.prototype.dispose=Ox.prototype.dispose;function Vx(a,b){Wx()?Yx(a)&&(Zx(!0),window.chrome&&chrome.cast&&chrome.cast.isAvailable?$x(b):(window.__onGCastApiAvailable=function(a,d){a?$x(b):(ay("Failed to load cast API: "+d),by(!1),Zx(!1),vm("yt-remote-cast-available"),vm("yt-remote-cast-receiver"),cy(),b(!1))},bw())):Xx("Cannot initialize because not running Chrome")}
function cy(){Xx("dispose");var a=dy();a&&a.dispose();ey=null;p("yt.mdx.remote.cloudview.instance_",null,void 0);fy(!1);rg(gy);gy.length=0}
function hy(){return um("yt-remote-cast-installed")?dy()?ey.getCastSession():(ay("getCastSelector: Cast is not initialized."),null):(ay("getCastSelector: Cast API is not installed!"),null)}
function iy(){var a=jy();ky()?dy().setConnectedScreenStatus(a,"YouTube TV"):ay("setConnectedScreenStatus called before ready.")}
var ey=null;function Wx(){var a;a=0<=Xb.search(/\ (CrMo|Chrome|CriOS)\//);return Sj||a}
function Yx(a){var b=!1;if(!ey){var c=r("yt.mdx.remote.cloudview.instance_");c||(c=new Ox(a),c.subscribe("yt-remote-cast2-availability-change",function(a){qm("yt-remote-cast-available",a);R("yt-remote-cast2-availability-change",a)}),c.subscribe("yt-remote-cast2-receiver-selected",function(a){Xx("onReceiverSelected: "+a.friendlyName);
qm("yt-remote-cast-receiver",a);R("yt-remote-cast2-receiver-selected",a)}),c.subscribe("yt-remote-cast2-receiver-resumed",function(a){Xx("onReceiverResumed: "+a.friendlyName);
qm("yt-remote-cast-receiver",a)}),c.subscribe("yt-remote-cast2-session-change",function(a){Xx("onSessionChange: "+vw(a));
a||vm("yt-remote-cast-receiver");R("yt-remote-cast2-session-change",a)}),p("yt.mdx.remote.cloudview.instance_",c,void 0),b=!0);
ey=c}Xx("cloudview.createSingleton_: "+b);return b}
function dy(){ey||(ey=r("yt.mdx.remote.cloudview.instance_"));return ey}
function $x(a){by(!0);Zx(!1);ey.init(!1,function(b){b?(fy(!0),R("yt-remote-cast2-api-ready")):(ay("Failed to initialize cast API."),by(!1),vm("yt-remote-cast-available"),vm("yt-remote-cast-receiver"),cy());a(b)})}
function Xx(a){lw("cloudview",a)}
function ay(a){lw("cloudview",a)}
function by(a){Xx("setCastInstalled_ "+a);qm("yt-remote-cast-installed",a)}
function ky(){return!!r("yt.mdx.remote.cloudview.apiReady_")}
function fy(a){Xx("setApiReady_ "+a);p("yt.mdx.remote.cloudview.apiReady_",a,void 0)}
function Zx(a){p("yt.mdx.remote.cloudview.initializing_",a,void 0)}
var gy=[];function ly(){if(!("cast"in window))return!1;var a=window.cast||{};return"ActivityStatus"in a&&"Api"in a&&"LaunchRequest"in a&&"Receiver"in a}
function my(a){lw("CAST",a)}
function ny(a){var b=oy();b&&b.logMessage&&b.logMessage(a)}
function py(a){if(a.event.source==window&&a.event.data&&"CastApi"==a.event.data.source&&"Hello"==a.event.data.event)for(;qy.length;)qy.shift()()}
function ry(){if(!r("yt.mdx.remote.castv2_")&&!sy&&(0==ib.length&&qb(ib,um("yt-remote-online-dial-devices")||[]),ly())){var a=oy();a?(a.removeReceiverListener("YouTube",ty),a.addReceiverListener("YouTube",ty),my("API initialized in the other binary")):(a=new cast.Api,uy(a),a.addReceiverListener("YouTube",ty),a.setReloadTabRequestHandler&&a.setReloadTabRequestHandler(function(){L(function(){window.location.reload(!0)},1E3)}),hw(ny),my("API initialized"));
sy=!0}}
function vy(){var a=oy();a&&(my("API disposed"),kw(ny),a.setReloadTabRequestHandler&&a.setReloadTabRequestHandler(ca),a.removeReceiverListener("YouTube",ty),uy(null));sy=!1;qy=null;Lf(window,"message",py)}
function wy(a){var b=db(ib,function(b){return b.id==a.id});
0<=b&&(ib[b]=Cw(a))}
function ty(a){a.length&&my("Updating receivers: "+Wi(a));yy(a);R("yt-remote-cast-device-list-update");x(zy(),function(a){Ay(a.id)});
x(a,function(a){if(a.isTabProjected){var c=By(a.id);my("Detected device: "+c.id+" is tab projected. Firing DEVICE_TAB_PROJECTED event.");L(function(){R("yt-remote-cast-device-tab-projected",c.id)},1E3)}})}
function Cy(a,b){my("Updating "+a+" activity status: "+Wi(b));var c=By(a);c?(b.activityId&&(c.f=b.activityId),c.status="running"==b.status?"RUNNING":"stopped"==b.status?"STOPPED":"error"==b.status?"ERROR":"UNKNOWN","RUNNING"!=c.status&&(c.f=""),wy(c),R("yt-remote-cast-device-status-update",c)):my("Device not found")}
function zy(){ry();return Gw()}
function yy(a){a=$a(a,function(a){var c={id:a.id,name:Ja(a.name)};if(a=By(a.id))c.activityId=a.f,c.status=a.status;return c});
gb();qb(ib,a)}
function By(a){var b=zy();return cb(b,function(b){return b.id==a})||null}
function Ay(a){var b=By(a),c=oy();c&&b&&b.f&&c.getActivityStatus(b.f,function(b){"error"==b.status&&(b.status="stopped");Cy(a,b)})}
function Dy(a){ry();var b=By(a),c=oy();c&&b&&b.f?(my("Stopping cast activity"),c.stopActivity(b.f,ra(Cy,a))):my("Dropping cast activity stop")}
function oy(){return r("yt.mdx.remote.castapi.api_")}
function uy(a){p("yt.mdx.remote.castapi.api_",a,void 0)}
var sy=!1,qy=null,ib=r("yt.mdx.remote.castapi.devices_")||[];p("yt.mdx.remote.castapi.devices_",ib,void 0);function Ey(a,b){this.action=a;this.params=b||null}
;function Fy(){}
;function Gy(){this.f=v()}
new Gy;Gy.prototype.set=function(a){this.f=a};
Gy.prototype.reset=function(){this.set(v())};
Gy.prototype.get=function(){return this.f};function Hy(a,b){this.j=new Xi(a);this.f=b?Vi:Ui}
Hy.prototype.stringify=function(a){return this.j.nc(a)};
Hy.prototype.parse=function(a){return this.f(a)};function Iy(a,b,c){eg.call(this);this.A=null!=c?u(a,c):a;this.l=b;this.j=u(this.gh,this);this.f=[]}
w(Iy,eg);g=Iy.prototype;g.vb=!1;g.Ob=0;g.eb=null;g.Ef=function(a){this.f=arguments;this.eb||this.Ob?this.vb=!0:Jy(this)};
g.stop=function(){this.eb&&(m.clearTimeout(this.eb),this.eb=null,this.vb=!1,this.f=[])};
g.pause=function(){this.Ob++};
g.resume=function(){this.Ob--;this.Ob||!this.vb||this.eb||(this.vb=!1,Jy(this))};
g.M=function(){Iy.J.M.call(this);this.stop()};
g.gh=function(){this.eb=null;this.vb&&!this.Ob&&(this.vb=!1,Jy(this))};
function Jy(a){a.eb=Lr(a.j,a.l);a.A.apply(null,a.f)}
;function Ky(){}
Ky.prototype.f=null;function Ly(a){var b;(b=a.f)||(b={},My(a)&&(b[0]=!0,b[1]=!0),b=a.f=b);return b}
;var Ny;function Oy(){}
w(Oy,Ky);function Py(a){return(a=My(a))?new ActiveXObject(a):new XMLHttpRequest}
function My(a){if(!a.j&&"undefined"==typeof XMLHttpRequest&&"undefined"!=typeof ActiveXObject){for(var b=["MSXML2.XMLHTTP.6.0","MSXML2.XMLHTTP.3.0","MSXML2.XMLHTTP","Microsoft.XMLHTTP"],c=0;c<b.length;c++){var d=b[c];try{return new ActiveXObject(d),a.j=d}catch(e){}}throw Error("Could not create ActiveXObject. ActiveX might be disabled, or MSXML might not be installed");}return a.j}
Ny=new Oy;function Qy(a,b,c,d,e){this.f=a;this.l=c;this.F=d;this.D=e||1;this.B=45E3;this.A=new Us(this);this.j=new Jr;Kr(this.j,250)}
g=Qy.prototype;g.fb=null;g.za=!1;g.yb=null;g.gd=null;g.Pb=null;g.wb=null;g.Sa=null;g.Ya=null;g.kb=null;g.da=null;g.Sb=0;g.Aa=null;g.tc=null;g.gb=null;g.Kb=-1;g.Fe=!0;g.ab=!1;g.Oc=0;g.kc=null;var Ry={},Sy={};g=Qy.prototype;g.setTimeout=function(a){this.B=a};
function Ty(a,b,c){a.wb=1;a.Sa=Ej(b.clone());a.kb=c;a.C=!0;Uy(a,null)}
function Vy(a,b,c,d,e){a.wb=1;a.Sa=Ej(b.clone());a.kb=null;a.C=c;e&&(a.Fe=!1);Uy(a,d)}
function Uy(a,b){a.Pb=v();Wy(a);a.Ya=a.Sa.clone();Cj(a.Ya,"t",a.D);a.Sb=0;a.da=a.f.Fc(a.f.Qb()?b:null);0<a.Oc&&(a.kc=new Iy(u(a.Pe,a,a.da),a.Oc));a.A.xa(a.da,"readystatechange",a.wh);var c=a.fb?Qb(a.fb):{};a.kb?(a.tc="POST",c["Content-Type"]="application/x-www-form-urlencoded",a.da.send(a.Ya,a.tc,a.kb,c)):(a.tc="GET",a.Fe&&!Rc&&(c.Connection="close"),a.da.send(a.Ya,a.tc,null,c));a.f.ya(1)}
g.wh=function(a){a=a.target;var b=this.kc;b&&3==Xy(a)?b.Ef():this.Pe(a)};
g.Pe=function(a){try{if(a==this.da)a:{var b=Xy(this.da),c=this.da.B,d=this.da.getStatus();if(F&&!bd(10)||Rc&&!ad("420+")){if(4>b)break a}else if(3>b||3==b&&!Nc&&!Yy(this.da))break a;this.ab||4!=b||7==c||(8==c||0>=d?this.f.ya(3):this.f.ya(2));Zy(this);var e=this.da.getStatus();this.Kb=e;var f=Yy(this.da);(this.za=200==e)?(4==b&&$y(this),this.C?(az(this,b,f),Nc&&this.za&&3==b&&(this.A.xa(this.j,"tick",this.uh),this.j.start())):bz(this,f),this.za&&!this.ab&&(4==b?this.f.hc(this):(this.za=!1,Wy(this)))):
(this.gb=400==e&&0<f.indexOf("Unknown SID")?3:0,cz(),$y(this),dz(this))}}catch(h){this.da&&Yy(this.da)}finally{}};
function az(a,b,c){for(var d=!0;!a.ab&&a.Sb<c.length;){var e=ez(a,c);if(e==Sy){4==b&&(a.gb=4,cz(),d=!1);break}else if(e==Ry){a.gb=4;cz();d=!1;break}else bz(a,e)}4==b&&0==c.length&&(a.gb=1,cz(),d=!1);a.za=a.za&&d;d||($y(a),dz(a))}
g.uh=function(){var a=Xy(this.da),b=Yy(this.da);this.Sb<b.length&&(Zy(this),az(this,a,b),this.za&&4!=a&&Wy(this))};
function ez(a,b){var c=a.Sb,d=b.indexOf("\n",c);if(-1==d)return Sy;c=Number(b.substring(c,d));if(isNaN(c))return Ry;d+=1;if(d+c>b.length)return Sy;var e=b.substr(d,c);a.Sb=d+c;return e}
function fz(a,b){a.Pb=v();Wy(a);var c=b?window.location.hostname:"";a.Ya=a.Sa.clone();Bj(a.Ya,"DOMAIN",c);Bj(a.Ya,"t",a.D);try{a.Aa=new ActiveXObject("htmlfile")}catch(q){$y(a);a.gb=7;cz();dz(a);return}var d="<html><body>";if(b){for(var e="",f=0;f<c.length;f++){var h=c.charAt(f);if("<"==h)e=e+"\\x3c";else if(">"==h)e=e+"\\x3e";else{if(h in Pa)h=Pa[h];else if(h in Oa)h=Pa[h]=Oa[h];else{var k=h,l=h.charCodeAt(0);if(31<l&&127>l)k=h;else{if(256>l){if(k="\\x",16>l||256<l)k+="0"}else k="\\u",4096>l&&(k+=
"0");k+=l.toString(16).toUpperCase()}h=Pa[h]=k}e+=h}}d+='<script>document.domain="'+e+'"\x3c/script>'}d+="</body></html>";c=Li(ec("b/12014412"),d);a.Aa.open();a.Aa.write(yc(c));a.Aa.close();a.Aa.parentWindow.m=u(a.kh,a);a.Aa.parentWindow.d=u(a.xe,a,!0);a.Aa.parentWindow.rpcClose=u(a.xe,a,!1);c=a.Aa.createElement("DIV");a.Aa.parentWindow.document.body.appendChild(c);d=qc(a.Ya.toString());d=Ba(oc(d));d=Li(ec("b/12014412"),'<iframe src="'+d+'"></iframe>');id(c,d);a.f.ya(1)}
g.kh=function(a){gz(u(this.jh,this,a),0)};
g.jh=function(a){this.ab||(Zy(this),bz(this,a),Wy(this))};
g.xe=function(a){gz(u(this.ih,this,a),0)};
g.ih=function(a){this.ab||($y(this),this.za=a,this.f.hc(this),this.f.ya(4))};
g.cancel=function(){this.ab=!0;$y(this)};
function Wy(a){a.gd=v()+a.B;hz(a,a.B)}
function hz(a,b){if(null!=a.yb)throw Error("WatchDog timer not null");a.yb=gz(u(a.oh,a),b)}
function Zy(a){a.yb&&(m.clearTimeout(a.yb),a.yb=null)}
g.oh=function(){this.yb=null;var a=v();0<=a-this.gd?(2!=this.wb&&this.f.ya(3),$y(this),this.gb=2,cz(),dz(this)):hz(this,this.gd-a)};
function dz(a){a.f.Xd()||a.ab||a.f.hc(a)}
function $y(a){Zy(a);gg(a.kc);a.kc=null;a.j.stop();a.A.removeAll();if(a.da){var b=a.da;a.da=null;iz(b);b.dispose()}a.Aa&&(a.Aa=null)}
function bz(a,b){try{a.f.qe(a,b),a.f.ya(4)}catch(c){}}
;function jz(a,b,c,d,e){if(0==d)c(!1);else{var f=e||0;d--;kz(a,b,function(e){e?c(!0):m.setTimeout(function(){jz(a,b,c,d,f)},f)})}}
function kz(a,b,c){var d=new Image;d.onload=function(){try{lz(d),c(!0)}catch(a){}};
d.onerror=function(){try{lz(d),c(!1)}catch(a){}};
d.onabort=function(){try{lz(d),c(!1)}catch(a){}};
d.ontimeout=function(){try{lz(d),c(!1)}catch(a){}};
m.setTimeout(function(){if(d.ontimeout)d.ontimeout()},b);
d.src=a}
function lz(a){a.onload=null;a.onerror=null;a.onabort=null;a.ontimeout=null}
;function mz(a){this.f=a;this.j=new Hy(null,!0)}
g=mz.prototype;g.Mc=null;g.oa=null;g.lc=!1;g.Ke=null;g.$b=null;g.Tc=null;g.Nc=null;g.ta=null;g.Pa=-1;g.Jb=null;g.Bb=null;g.connect=function(a){this.Nc=a;a=nz(this.f,null,this.Nc);cz();this.Ke=v();var b=this.f.F;null!=b?(this.Jb=b[0],(this.Bb=b[1])?(this.ta=1,oz(this)):(this.ta=2,pz(this))):(Cj(a,"MODE","init"),this.oa=new Qy(this,0,void 0,void 0,void 0),this.oa.fb=this.Mc,Vy(this.oa,a,!1,null,!0),this.ta=0)};
function oz(a){var b=nz(a.f,a.Bb,"/mail/images/cleardot.gif");Ej(b);jz(b.toString(),5E3,u(a.sf,a),3,2E3);a.ya(1)}
g.sf=function(a){if(a)this.ta=2,pz(this);else{cz();var b=this.f;b.wa=b.Ta.Pa;qz(b,9)}a&&this.ya(2)};
function pz(a){var b=a.f.N;if(null!=b)cz(),b?(cz(),rz(a.f,a,!1)):(cz(),rz(a.f,a,!0));else if(a.oa=new Qy(a,0,void 0,void 0,void 0),a.oa.fb=a.Mc,b=a.f,b=nz(b,b.Qb()?a.Jb:null,a.Nc),cz(),!F||bd(10))Cj(b,"TYPE","xmlhttp"),Vy(a.oa,b,!1,a.Jb,!1);else{Cj(b,"TYPE","html");var c=a.oa;a=!!a.Jb;c.wb=3;c.Sa=Ej(b.clone());fz(c,a)}}
g.Fc=function(a){return this.f.Fc(a)};
g.Xd=function(){return!1};
g.qe=function(a,b){this.Pa=a.Kb;if(0==this.ta)if(b){try{var c=this.j.parse(b)}catch(d){c=this.f;c.wa=this.Pa;qz(c,2);return}this.Jb=c[0];this.Bb=c[1]}else c=this.f,c.wa=this.Pa,qz(c,2);else if(2==this.ta)if(this.lc)cz(),this.Tc=v();else if("11111"==b){if(cz(),this.lc=!0,this.$b=v(),c=this.$b-this.Ke,!F||bd(10)||500>c)this.Pa=200,this.oa.cancel(),cz(),rz(this.f,this,!0)}else cz(),this.$b=this.Tc=v(),this.lc=!1};
g.hc=function(){this.Pa=this.oa.Kb;if(this.oa.za)0==this.ta?this.Bb?(this.ta=1,oz(this)):(this.ta=2,pz(this)):2==this.ta&&(a=!1,(a=!F||bd(10)?this.lc:200>this.Tc-this.$b?!1:!0)?(cz(),rz(this.f,this,!0)):(cz(),rz(this.f,this,!1)));else{0==this.ta?cz():2==this.ta&&cz();var a=this.f;a.wa=this.Pa;qz(a,2)}};
g.Qb=function(){return this.f.Qb()};
g.isActive=function(){return this.f.isActive()};
g.ya=function(a){this.f.ya(a)};function sz(a){lr.call(this);this.headers=new ke;this.ca=a||null;this.j=!1;this.X=this.f=null;this.Ha=this.H="";this.B=0;this.C="";this.A=this.qa=this.F=this.ga=!1;this.D=0;this.P=null;this.Ia="";this.V=this.Ja=!1}
w(sz,lr);var tz=/^https?$/i,uz=["POST","PUT"];g=sz.prototype;
g.send=function(a,b,c,d){if(this.f)throw Error("[goog.net.XhrIo] Object is active with another request="+this.H+"; newUri="+a);b=b?b.toUpperCase():"GET";this.H=a;this.C="";this.B=0;this.Ha=b;this.ga=!1;this.j=!0;this.f=this.ca?Py(this.ca):Py(Ny);this.X=this.ca?Ly(this.ca):Ly(Ny);this.f.onreadystatechange=u(this.pe,this);try{Fy(vz(this,"Opening Xhr")),this.qa=!0,this.f.open(b,String(a),!0),this.qa=!1}catch(f){Fy(vz(this,"Error opening Xhr: "+f.message));wz(this,f);return}a=c||"";var e=this.headers.clone();
d&&Pi(d,function(a,b){e.set(b,a)});
d=cb(e.Ea(),xz);c=m.FormData&&a instanceof m.FormData;!fb(uz,b)||d||c||e.set("Content-Type","application/x-www-form-urlencoded;charset=utf-8");e.forEach(function(a,b){this.f.setRequestHeader(b,a)},this);
this.Ia&&(this.f.responseType=this.Ia);Kb(this.f)&&(this.f.withCredentials=this.Ja);try{yz(this),0<this.D&&(this.V=zz(this.f),Fy(vz(this,"Will abort after "+this.D+"ms if incomplete, xhr2 "+this.V)),this.V?(this.f.timeout=this.D,this.f.ontimeout=u(this.Rd,this)):this.P=Lr(this.Rd,this.D,this)),Fy(vz(this,"Sending request")),this.F=!0,this.f.send(a),this.F=!1}catch(f){Fy(vz(this,"Send error: "+f.message)),wz(this,f)}};
function zz(a){return F&&ad(9)&&ha(a.timeout)&&n(a.ontimeout)}
function xz(a){return"content-type"==a.toLowerCase()}
g.Rd=function(){"undefined"!=typeof ba&&this.f&&(this.C="Timed out after "+this.D+"ms, aborting",this.B=8,vz(this,this.C),mr(this,"timeout"),iz(this,8))};
function wz(a,b){a.j=!1;a.f&&(a.A=!0,a.f.abort(),a.A=!1);a.C=b;a.B=5;Az(a);Bz(a)}
function Az(a){a.ga||(a.ga=!0,mr(a,"complete"),mr(a,"error"))}
function iz(a,b){a.f&&a.j&&(vz(a,"Aborting"),a.j=!1,a.A=!0,a.f.abort(),a.A=!1,a.B=b||7,mr(a,"complete"),mr(a,"abort"),Bz(a))}
g.M=function(){this.f&&(this.j&&(this.j=!1,this.A=!0,this.f.abort(),this.A=!1),Bz(this,!0));sz.J.M.call(this)};
g.pe=function(){this.isDisposed()||(this.qa||this.F||this.A?Cz(this):this.Xg())};
g.Xg=function(){Cz(this)};
function Cz(a){if(a.j&&"undefined"!=typeof ba)if(a.X[1]&&4==Xy(a)&&2==a.getStatus())vz(a,"Local request error detected and ignored");else if(a.F&&4==Xy(a))Lr(a.pe,0,a);else if(mr(a,"readystatechange"),4==Xy(a)){vz(a,"Request complete");a.j=!1;try{var b=a.getStatus(),c;a:switch(b){case 200:case 201:case 202:case 204:case 206:case 304:case 1223:c=!0;break a;default:c=!1}var d;if(!(d=c)){var e;if(e=0===b){var f=Ph(1,String(a.H));if(!f&&m.self&&m.self.location)var h=m.self.location.protocol,f=h.substr(0,
h.length-1);e=!tz.test(f?f.toLowerCase():"")}d=e}if(d)mr(a,"complete"),mr(a,"success");else{a.B=6;var k;try{k=2<Xy(a)?a.f.statusText:""}catch(l){k=""}a.C=k+" ["+a.getStatus()+"]";Az(a)}}finally{Bz(a)}}}
function Bz(a,b){if(a.f){yz(a);var c=a.f,d=a.X[0]?ca:null;a.f=null;a.X=null;b||mr(a,"ready");try{c.onreadystatechange=d}catch(e){}}}
function yz(a){a.f&&a.V&&(a.f.ontimeout=null);ha(a.P)&&(m.clearTimeout(a.P),a.P=null)}
g.isActive=function(){return!!this.f};
function Xy(a){return a.f?a.f.readyState:0}
g.getStatus=function(){try{return 2<Xy(this)?this.f.status:-1}catch(a){return-1}};
function Yy(a){try{return a.f?a.f.responseText:""}catch(b){return""}}
function vz(a,b){return b+" ["+a.Ha+" "+a.H+" "+a.getStatus()+"]"}
;function Dz(a,b,c){this.D=a||null;this.f=1;this.j=[];this.A=[];this.B=new Hy(null,!0);this.F=b||null;this.N=null!=c?c:null}
function Ez(a,b){this.f=a;this.map=b;this.context=null}
g=Dz.prototype;g.Fb=null;g.ka=null;g.$=null;g.Lc=null;g.bc=null;g.vd=null;g.dc=null;g.Mb=0;g.kg=0;g.ia=null;g.Ua=null;g.Ma=null;g.cb=null;g.Ta=null;g.sc=null;g.pb=-1;g.Zd=-1;g.wa=-1;g.Gb=0;g.ob=0;g.bb=8;var Fz=new lr;function Gz(a){Nq.call(this,"statevent",a)}
w(Gz,Nq);function Hz(a,b){Nq.call(this,"timingevent",a);this.size=b}
w(Hz,Nq);function Iz(a){Nq.call(this,"serverreachability",a)}
w(Iz,Nq);g=Dz.prototype;g.connect=function(a,b,c,d,e){cz();this.Lc=b;this.Fb=c||{};d&&n(e)&&(this.Fb.OSID=d,this.Fb.OAID=e);this.Ta=new mz(this);this.Ta.Mc=null;this.Ta.j=this.B;this.Ta.connect(a)};
function Jz(a){Kz(a);if(3==a.f){var b=a.Mb++,c=a.bc.clone();Bj(c,"SID",a.l);Bj(c,"RID",b);Bj(c,"TYPE","terminate");Lz(a,c);b=new Qy(a,0,a.l,b,void 0);b.wb=2;b.Sa=Ej(c.clone());(new Image).src=b.Sa;b.Pb=v();Wy(b)}Mz(a)}
function Kz(a){if(a.Ta){var b=a.Ta;b.oa&&(b.oa.cancel(),b.oa=null);b.Pa=-1;a.Ta=null}a.$&&(a.$.cancel(),a.$=null);a.Ma&&(m.clearTimeout(a.Ma),a.Ma=null);Nz(a);a.ka&&(a.ka.cancel(),a.ka=null);a.Ua&&(m.clearTimeout(a.Ua),a.Ua=null)}
function Oz(a,b){if(0==a.f)throw Error("Invalid operation: sending map when state is closed");a.j.push(new Ez(a.kg++,b));2!=a.f&&3!=a.f||Pz(a)}
g.Xd=function(){return 0==this.f};
function Pz(a){a.ka||a.Ua||(a.Ua=gz(u(a.ue,a),0),a.Gb=0)}
g.ue=function(a){this.Ua=null;Qz(this,a)};
function Qz(a,b){if(1==a.f){if(!b){a.Mb=Math.floor(1E5*Math.random());var c=a.Mb++,d=new Qy(a,0,"",c,void 0);d.fb=null;var e=Rz(a),f=a.bc.clone();Bj(f,"RID",c);a.D&&Bj(f,"CVER",a.D);Lz(a,f);Ty(d,f,e);a.ka=d;a.f=2}}else 3==a.f&&(b?Sz(a,b):0!=a.j.length&&(a.ka||Sz(a)))}
function Sz(a,b){var c,d;b?6<a.bb?(a.j=a.A.concat(a.j),a.A.length=0,c=a.Mb-1,d=Rz(a)):(c=b.F,d=b.kb):(c=a.Mb++,d=Rz(a));var e=a.bc.clone();Bj(e,"SID",a.l);Bj(e,"RID",c);Bj(e,"AID",a.pb);Lz(a,e);c=new Qy(a,0,a.l,c,a.Gb+1);c.fb=null;c.setTimeout(Math.round(1E4)+Math.round(1E4*Math.random()));a.ka=c;Ty(c,e,d)}
function Lz(a,b){if(a.ia){var c=a.ia.Nd(a);c&&Db(c,function(a,c){Bj(b,c,a)})}}
function Rz(a){var b=Math.min(a.j.length,1E3),c=["count="+b],d;6<a.bb&&0<b?(d=a.j[0].f,c.push("ofs="+d)):d=0;for(var e=0;e<b;e++){var f=a.j[e].f,h=a.j[e].map,f=6>=a.bb?e:f-d;try{Pi(h,function(a,b){c.push("req"+f+"_"+b+"="+encodeURIComponent(a))})}catch(k){c.push("req"+f+"_type="+encodeURIComponent("_badmap"))}}a.A=a.A.concat(a.j.splice(0,b));
return c.join("&")}
function Tz(a){a.$||a.Ma||(a.C=1,a.Ma=gz(u(a.te,a),0),a.ob=0)}
function Uz(a){if(a.$||a.Ma||3<=a.ob)return!1;a.C++;a.Ma=gz(u(a.te,a),Vz(a,a.ob));a.ob++;return!0}
g.te=function(){this.Ma=null;this.$=new Qy(this,0,this.l,"rpc",this.C);this.$.fb=null;this.$.Oc=0;var a=this.vd.clone();Bj(a,"RID","rpc");Bj(a,"SID",this.l);Bj(a,"CI",this.sc?"0":"1");Bj(a,"AID",this.pb);Lz(this,a);if(!F||bd(10))Bj(a,"TYPE","xmlhttp"),Vy(this.$,a,!0,this.dc,!1);else{Bj(a,"TYPE","html");var b=this.$,c=!!this.dc;b.wb=3;b.Sa=Ej(a.clone());fz(b,c)}};
function rz(a,b,c){a.sc=c;a.wa=b.Pa;a.wf(1,0);a.bc=nz(a,null,a.Lc);Pz(a)}
g.qe=function(a,b){if(0!=this.f&&(this.$==a||this.ka==a))if(this.wa=a.Kb,this.ka==a&&3==this.f)if(7<this.bb){var c;try{c=this.B.parse(b)}catch(f){c=null}if(fa(c)&&3==c.length)if(0==c[0])a:{if(!this.Ma){if(this.$)if(this.$.Pb+3E3<this.ka.Pb)Nz(this),this.$.cancel(),this.$=null;else break a;Uz(this);cz()}}else this.Zd=c[1],0<this.Zd-this.pb&&37500>c[2]&&this.sc&&0==this.ob&&!this.cb&&(this.cb=gz(u(this.og,this),6E3));else qz(this,11)}else"y2f%"!=b&&qz(this,11);else if(this.$==a&&Nz(this),!wa(b)){c=
this.B.parse(b);fa(c);for(var d=0;d<c.length;d++){var e=c[d];this.pb=e[0];e=e[1];2==this.f?"c"==e[0]?(this.l=e[1],this.dc=e[2],e=e[3],null!=e?this.bb=e:this.bb=6,this.f=3,this.ia&&this.ia.Ed(this),this.vd=nz(this,this.Qb()?this.dc:null,this.Lc),Tz(this)):"stop"==e[0]&&qz(this,7):3==this.f&&("stop"==e[0]?qz(this,7):"noop"!=e[0]&&this.ia&&this.ia.Dd(this,e),this.ob=0)}}};
g.og=function(){null!=this.cb&&(this.cb=null,this.$.cancel(),this.$=null,Uz(this),cz())};
function Nz(a){null!=a.cb&&(m.clearTimeout(a.cb),a.cb=null)}
g.hc=function(a){var b;if(this.$==a)Nz(this),this.$=null,b=2;else if(this.ka==a)this.ka=null,b=1;else return;this.wa=a.Kb;if(0!=this.f)if(a.za)1==b?(v(),mr(Fz,new Hz(Fz,a.kb?a.kb.length:0)),Pz(this),this.A.length=0):Tz(this);else{var c=a.gb,d;if(!(d=3==c||7==c||0==c&&0<this.wa)){if(d=1==b)this.ka||this.Ua||1==this.f||2<=this.Gb?d=!1:(this.Ua=gz(u(this.ue,this,a),Vz(this,this.Gb)),this.Gb++,d=!0);d=!(d||2==b&&Uz(this))}if(d)switch(c){case 1:qz(this,5);break;case 4:qz(this,10);break;case 3:qz(this,
6);break;case 7:qz(this,12);break;default:qz(this,2)}}};
function Vz(a,b){var c=5E3+Math.floor(1E4*Math.random());a.isActive()||(c*=2);return c*b}
g.wf=function(a){if(!fb(arguments,this.f))throw Error("Unexpected channel state: "+this.f);};
function qz(a,b){if(2==b||9==b){var c=null;a.ia&&(c=null);var d=u(a.Eh,a);c||(c=new nj("//www.google.com/images/cleardot.gif"),Ej(c));kz(c.toString(),1E4,d)}else cz();Wz(a,b)}
g.Eh=function(a){a?cz():(cz(),Wz(this,8))};
function Wz(a,b){a.f=0;a.ia&&a.ia.Cd(a,b);Mz(a);Kz(a)}
function Mz(a){a.f=0;a.wa=-1;if(a.ia)if(0==a.A.length&&0==a.j.length)a.ia.Cc(a);else{var b=pb(a.A),c=pb(a.j);a.A.length=0;a.j.length=0;a.ia.Cc(a,b,c)}}
function nz(a,b,c){var d=Fj(c);if(""!=d.l)b&&pj(d,b+"."+d.l),qj(d,d.D);else var e=window.location,d=Gj(e.protocol,b?b+"."+e.hostname:e.hostname,e.port,c);a.Fb&&Db(a.Fb,function(a,b){Bj(d,b,a)});
Bj(d,"VER",a.bb);Lz(a,d);return d}
g.Fc=function(a){if(a)throw Error("Can't create secondary domain capable XhrIo object.");a=new sz;a.Ja=!1;return a};
g.isActive=function(){return!!this.ia&&this.ia.isActive(this)};
function gz(a,b){if(!ia(a))throw Error("Fn must not be null and must be a function");return m.setTimeout(function(){a()},b)}
g.ya=function(){mr(Fz,new Iz(Fz))};
function cz(){mr(Fz,new Gz(Fz))}
g.Qb=function(){return!(!F||bd(10))};
function Xz(){}
g=Xz.prototype;g.Ed=function(){};
g.Dd=function(){};
g.Cd=function(){};
g.Cc=function(){};
g.Nd=function(){return{}};
g.isActive=function(){return!0};function Yz(a,b){Jr.call(this);this.C=0;if(ia(a))b&&(a=u(a,b));else if(a&&ia(a.handleEvent))a=u(a.handleEvent,a);else throw Error("Invalid listener argument");this.F=a;ar(this,"tick",u(this.D,this));this.stop();Kr(this,5E3+2E4*Math.random())}
w(Yz,Jr);Yz.prototype.D=function(){if(500<this.f){var a=this.f;24E4>2*a&&(a*=2);Kr(this,a)}this.F()};
Yz.prototype.start=function(){Yz.J.start.call(this);this.C=v()+this.f};
Yz.prototype.stop=function(){this.C=0;Yz.J.stop.call(this)};function Zz(a,b){this.U=a;this.A=b;this.l=new hg;this.j=new Yz(this.Oh,this);this.f=null;this.H=!1;this.C=null;this.N="";this.F=this.B=0;this.D=[]}
w(Zz,Xz);g=Zz.prototype;g.subscribe=function(a,b,c){return this.l.subscribe(a,b,c)};
g.unsubscribe=function(a,b,c){return this.l.unsubscribe(a,b,c)};
g.va=function(a){return this.l.va(a)};
g.K=function(a,b){return this.l.K.apply(this.l,arguments)};
g.dispose=function(){this.H||(this.H=!0,this.l.clear(),$z(this),gg(this.l))};
g.isDisposed=function(){return this.H};
function aA(a){return{firstTestResults:[""],secondTestResults:!a.f.sc,sessionId:a.f.l,arrayId:a.f.pb}}
g.connect=function(a,b,c){if(!this.f||2!=this.f.f){this.N="";this.j.stop();this.C=a||null;this.B=b||0;a=this.U+"/test";b=this.U+"/bind";var d=new Dz("1",c?c.firstTestResults:null,c?c.secondTestResults:null),e=this.f;e&&(e.ia=null);d.ia=this;this.f=d;e?this.f.connect(a,b,this.A,e.l,e.pb):c?this.f.connect(a,b,this.A,c.sessionId,c.arrayId):this.f.connect(a,b,this.A)}};
function $z(a,b){a.F=b||0;a.j.stop();a.f&&(3==a.f.f&&Qz(a.f),Jz(a.f));a.F=0}
g.sendMessage=function(a,b){var c={_sc:a};b&&Tb(c,b);this.j.enabled||2==(this.f?this.f.f:0)?this.D.push(c):this.f&&3==this.f.f&&Oz(this.f,c)};
g.Ed=function(){var a=this.j;a.stop();Kr(a,5E3+2E4*Math.random());this.C=null;this.B=0;if(this.D.length){a=this.D;this.D=[];for(var b=0,c=a.length;b<c;++b)Oz(this.f,a[b])}this.K("handlerOpened")};
g.Cd=function(a,b){var c=2==b&&401==this.f.wa;if(4!=b&&!c){if(6==b||410==this.f.wa)c=this.j,c.stop(),Kr(c,500);this.j.start()}this.K("handlerError",b)};
g.Cc=function(a,b,c){if(!this.j.enabled)this.K("handlerClosed");else if(c)for(a=0,b=c.length;a<b;++a){var d=c[a].map;d&&this.D.push(d)}};
g.Nd=function(){var a={v:2};this.N&&(a.gsessionid=this.N);0!=this.B&&(a.ui=""+this.B);0!=this.F&&(a.ui=""+this.F);this.C&&Tb(a,this.C);return a};
g.Dd=function(a,b){if("S"==b[0])this.N=b[1];else if("gracefulReconnect"==b[0]){var c=this.j;c.stop();Kr(c,500);this.j.start();Jz(this.f)}else this.K("handlerMessage",new Ey(b[0],b[1]))};
function bA(a,b){(a.A.loungeIdToken=b)||a.j.stop()}
g.Oh=function(){this.j.stop();var a=this.f,b=0;a.$&&b++;a.ka&&b++;0!=b?this.j.start():this.connect(this.C,this.B)};function cA(){this.f=[];this.j=[]}
g=cA.prototype;g.fa=function(){return this.f.length+this.j.length};
g.isEmpty=function(){return 0==this.f.length&&0==this.j.length};
g.clear=function(){this.f=[];this.j=[]};
g.contains=function(a){return fb(this.f,a)||fb(this.j,a)};
g.remove=function(a){var b;b=this.f;var c=Ya(b,a);0<=c?(lb(b,c),b=!0):b=!1;return b||kb(this.j,a)};
g.ha=function(){for(var a=[],b=this.f.length-1;0<=b;--b)a.push(this.f[b]);for(var c=this.j.length,b=0;b<c;++b)a.push(this.j[b]);return a};function dA(a){this.videoIds=null;this.index=-1;this.videoId=this.j="";this.volume=this.f=-1;this.l=!1;this.audioTrackId=null;this.C=this.B=0;this.A=null;this.reset(a)}
function eA(a,b){if(a.j)throw Error(b+" is not allowed in V3.");}
function fA(a){a.audioTrackId=null;a.A=null;a.f=-1;a.B=0;a.C=v()}
dA.prototype.reset=function(a){this.videoIds=[];this.j="";gA(this);this.volume=-1;this.l=!1;a&&(this.videoIds=a.videoIds,this.index=a.index,this.j=a.listId,this.videoId=a.videoId,this.f=a.playerState,this.volume=a.volume,this.l=a.muted,this.audioTrackId=a.audioTrackId,this.A=a.trackData,this.B=a.playerTime,this.C=a.playerTimeAt)};
function gA(a){a.index=-1;a.videoId="";fA(a)}
function hA(a){return a.j?a.videoId:a.videoIds[a.index]}
function iA(a,b){a.B=b;a.C=v()}
function jA(a){switch(a.f){case 1:return(v()-a.C)/1E3+a.B;case -1E3:return 0}return a.B}
dA.prototype.setVideoId=function(a){eA(this,"setVideoId");var b=this.index;this.index=Wa(this.videoIds,a);b!=this.index&&fA(this);return-1!=b};
function kA(a,b,c){eA(a,"setPlaylist");c=c||hA(a);tb(a.videoIds,b)&&c==hA(a)||(a.videoIds=pb(b),a.setVideoId(c))}
function lA(a,b){eA(a,"add");return b&&!fb(a.videoIds,b)?(a.videoIds.push(b),!0):!1}
dA.prototype.remove=function(a){eA(this,"remove");var b=hA(this);return kb(this.videoIds,a)?(this.index=Wa(this.videoIds,b),!0):!1};
function mA(a){var b={};b.videoIds=pb(a.videoIds);b.index=a.index;b.listId=a.j;b.videoId=a.videoId;b.playerState=a.f;b.volume=a.volume;b.muted=a.l;b.audioTrackId=a.audioTrackId;b.trackData=Rb(a.A);b.playerTime=a.B;b.playerTimeAt=a.C;return b}
dA.prototype.clone=function(){return new dA(mA(this))};function nA(a,b){Lw.call(this);this.f=0;this.B=a;this.F=[];this.D=new cA;this.C=NaN;this.l=this.j=null;this.P=u(this.qg,this);this.H=u(this.Nb,this);this.O=u(this.pg,this);var c=0;a?(c=a.getProxyState(),3!=c&&(a.subscribe("proxyStateChange",this.bd,this),oA(this))):c=3;0!=c&&(b?this.bd(c):L(u(function(){this.bd(c)},this),0));
pA(this,hy())}
w(nA,Lw);function qA(a){return new dA(a.B.getPlayerContextData())}
g=nA.prototype;g.play=function(){1==this.f?(this.j?this.j.play(null,ca,u(function(){this.W("Failed to play video with cast v2 channel.");rA(this,"play")},this)):rA(this,"play"),sA(this,1,jA(qA(this))),tA(this)):uA(this,this.play)};
g.pause=function(){1==this.f?(this.j?this.j.pause(null,ca,u(function(){this.W("Failed to pause video with cast v2 channel.");rA(this,"pause")},this)):rA(this,"pause"),sA(this,2,jA(qA(this))),tA(this)):uA(this,this.pause)};
g.stop=function(){if(1==this.f){this.j?this.j.stop(null,ca,u(function(){this.W("Failed to stop video with cast v2 channel.");rA(this,"stopVideo")},this)):rA(this,"stopVideo");
var a=qA(this);gA(a);vA(this,a);tA(this)}else uA(this,this.stop)};
g.setVolume=function(a,b){if(1==this.f){var c=qA(this);if(this.l){if(c.volume!=a){var d=Math.round(a)/100;this.l.setReceiverVolumeLevel(d,u(function(){wA("set receiver volume: "+d)},this),u(function(){this.W("failed to set receiver volume.")},this))}c.l!=b&&this.l.setReceiverMuted(b,u(function(){wA("set receiver muted: "+b)},this),u(function(){this.W("failed to set receiver muted.")},this))}else{var e={volume:a,
muted:b};-1!=c.volume&&(e.delta=a-c.volume);rA(this,"setVolume",e)}c.l=b;c.volume=a;vA(this,c);tA(this)}else uA(this,ra(this.setVolume,a,b))};
g.rd=function(a){if(1==this.f){rA(this,"addVideo",{videoId:a});var b=qA(this);b.j||(lA(b,a),vA(this,b))}else uA(this,ra(this.rd,a))};
g.qd=function(a){1==this.f?rA(this,"addVideos",{listId:a}):uA(this,ra(this.qd,a))};
g.sd=function(a){0==a.length?this.W("Ignore add videos request due to empty list"):1==this.f?rA(this,"addVideos",{videoIds:a.join(",")}):uA(this,ra(this.sd,a))};
g.Be=function(a){if(1==this.f){rA(this,"removeVideo",{videoId:a});var b=qA(this);b.j||(b.remove(a),vA(this,b))}else uA(this,ra(this.Be,a))};
g.dispose=function(){if(3!=this.f){var a=this.f;this.f=3;this.K("proxyStateChange",a,this.f)}nA.J.dispose.call(this)};
g.M=function(){M(this.C);this.C=NaN;xA(this);this.B=null;this.D.clear();pA(this,null);nA.J.M.call(this)};
function oA(a){x(["remotePlayerChange","remoteQueueChange"],function(a){this.F.push(this.B.subscribe(a,ra(this.Tg,a),this))},a)}
function xA(a){x(a.F,function(a){this.B.unsubscribeByKey(a)},a);
a.F.length=0}
function uA(a,b){50>a.D.fa()&&a.D.j.push(b)}
function sA(a,b,c){var d=qA(a);iA(d,c);-1E3!=d.f&&(d.f=b);vA(a,d)}
function rA(a,b,c){a.B.sendMessage(b,c)}
function vA(a,b){xA(a);a.B.setPlayerContextData(mA(b));oA(a)}
g.bd=function(a){if((a!=this.f||2==a)&&3!=this.f&&0!=a){var b=this.f;this.f=a;this.K("proxyStateChange",b,a);if(1==a)for(;!this.D.isEmpty();)b=a=this.D,0==b.f.length&&(b.f=b.j,b.f.reverse(),b.j=[]),a.f.pop().apply(this);else 3==a&&this.dispose()}};
function tA(a){M(a.C);a.C=L(u(function(){this.K("remotePlayerChange");this.C=NaN},a),2E3)}
g.Tg=function(a){("remotePlayerChange"!=a||isNaN(this.C))&&this.K(a)};
function pA(a,b){a.l&&(a.l.removeUpdateListener(a.P),a.l.removeMediaListener(a.H),a.Nb(null));a.l=b;a.l&&(wA("Setting cast session: "+a.l.sessionId),a.l.addUpdateListener(a.P),a.l.addMediaListener(a.H),a.l.media.length&&a.Nb(a.l.media[0]))}
g.qg=function(a){if(!a)this.Nb(null),pA(this,null);else if(this.l.receiver.volume){a=this.l.receiver.volume;var b=qA(this);if(b.volume!=a.level||b.l!=a.muted)wA("Cast volume update: "+a.level+(a.muted?" muted":"")),b.volume=Math.round(100*a.level||0),b.l=!!a.muted,vA(this,b),tA(this)}};
g.Nb=function(a){wA("Cast media: "+!!a);this.j&&this.j.removeUpdateListener(this.O);if(this.j=a)this.j.addUpdateListener(this.O),yA(this),tA(this)};
function yA(a){var b=a.j.customData;if(a.j.media){var c=a.j.media,d=qA(a);c.contentId!=d.videoId&&wA("Cast changing video to: "+c.contentId);d.videoId=c.contentId;d.f=b.playerState;iA(d,a.j.getEstimatedTime());vA(a,d)}else wA("No cast media video. Ignoring state update.")}
g.pg=function(a){a?(yA(this),tA(this)):this.Nb(null)};
function wA(a){lw("CP",a)}
g.W=function(a){lw("CP",a)};function zA(a,b,c){Lw.call(this);this.C=NaN;this.X=!1;this.H=this.F=this.V=this.O=NaN;this.ca=[];this.l=this.L=this.f=null;this.nb=a;this.ca.push(N(window,"beforeunload",u(this.If,this)));this.j=[];this.L=new dA;3==c["mdx-version"]&&(this.L.j="RQ"+b.token);this.ga=b.id;this.f=AA(this,c);this.f.subscribe("handlerOpened",this.vg,this);this.f.subscribe("handlerClosed",this.rg,this);this.f.subscribe("handlerError",this.sg,this);this.L.j?this.f.subscribe("handlerMessage",this.tg,this):this.f.subscribe("handlerMessage",
this.ug,this);bA(this.f,b.token);this.subscribe("remoteQueueChange",function(){var a=this.L.videoId;Xw()&&qm("yt-remote-session-video-id",a)},this)}
w(zA,Lw);g=zA.prototype;
g.connect=function(a,b){if(b){if(this.L.j){var c=b.listId,d=b.videoId,e=b.index,f=b.currentTime||0;5>=f&&(f=0);h={videoId:d,currentTime:f};c&&(h.listId=c);n(e)&&(h.currentIndex=e);c&&(this.L.j=c);this.L.videoId=d;this.L.index=e||0}else{var d=b.videoIds[b.index],f=b.currentTime||0;5>=f&&(f=0);var h={videoIds:d,videoId:d,currentTime:f};this.L.videoIds=[d];this.L.index=0}this.L.state=3;iA(this.L,f);this.T("Connecting with setPlaylist and params: "+Wi(h));this.f.connect({method:"setPlaylist",params:Wi(h)},
a,ax())}else this.T("Connecting without params"),this.f.connect({},a,ax());BA(this)};
g.dispose=function(){this.isDisposed()||(this.K("beforeDispose"),CA(this,3));zA.J.dispose.call(this)};
g.M=function(){DA(this);EA(this);FA(this);M(this.F);this.F=NaN;M(this.H);this.H=NaN;this.l=null;O(this.ca);this.ca.length=0;this.f.dispose();zA.J.M.call(this);this.j=this.L=this.f=null};
g.T=function(a){lw("conn",a)};
g.If=function(){this.B(2)};
function AA(a,b){return new Zz(zw(a.nb,"/bc",void 0,!1),b)}
function CA(a,b){a.K("proxyStateChange",b)}
function BA(a){a.C=L(u(function(){this.T("Connecting timeout");this.B(1)},a),2E4)}
function DA(a){M(a.C);a.C=NaN}
function FA(a){M(a.O);a.O=NaN}
function GA(a){EA(a);a.V=L(u(function(){HA(this,"getNowPlaying")},a),2E4)}
function EA(a){M(a.V);a.V=NaN}
function IA(a){var b=a.f;return!!b.f&&3==b.f.f&&isNaN(a.C)}
g.vg=function(){this.T("Channel opened");this.X&&(this.X=!1,FA(this),this.O=L(u(function(){this.T("Timing out waiting for a screen.");this.B(1)},this),15E3));
ix(aA(this.f),this.ga)};
g.rg=function(){this.T("Channel closed");isNaN(this.C)?jx(!0):jx();this.dispose()};
g.sg=function(a){jx();isNaN(this.D())?(this.T("Channel error: "+a+" without reconnection"),this.dispose()):(this.X=!0,this.T("Channel error: "+a+" with reconnection in "+this.D()+" ms"),CA(this,2))};
function JA(a,b){b&&(DA(a),FA(a));b==IA(a)?b&&(CA(a,1),HA(a,"getSubtitlesTrack")):b?(a.P()&&a.L.reset(),CA(a,1),HA(a,"getNowPlaying"),KA(a)):a.B(1)}
function LA(a,b){var c=b.params.videoId;delete b.params.videoId;c==a.L.videoId&&(Mb(b.params)?a.L.A=null:a.L.A=b.params,a.K("remotePlayerChange"))}
function MA(a,b){var c=b.params.videoId||b.params.video_id,d=parseInt(b.params.currentIndex,10);a.L.j=b.params.listId||a.L.j;var e=a.L,f=e.videoId;e.videoId=c;e.index=d;c!=f&&fA(e);a.K("remoteQueueChange")}
function NA(a,b){b.params=b.params||{};MA(a,b);OA(a,b)}
function OA(a,b){var c=parseInt(b.params.currentTime||b.params.current_time,10);iA(a.L,isNaN(c)?0:c);c=parseInt(b.params.state,10);c=isNaN(c)?-1:c;-1==c&&-1E3==a.L.f&&(c=-1E3);a.L.f=c;1==a.L.f?GA(a):EA(a);a.K("remotePlayerChange")}
function PA(a,b){var c="true"==b.params.muted;a.L.volume=parseInt(b.params.volume,10);a.L.l=c;a.K("remotePlayerChange")}
g.tg=function(a){a.params?this.T("Received: action="+a.action+", params="+Wi(a.params)):this.T("Received: action="+a.action+" {}");switch(a.action){case "loungeStatus":a=Ui(a.params.devices);this.j=$a(a,function(a){return new Sw(a)});
a=!!cb(this.j,function(a){return"LOUNGE_SCREEN"==a.type});
JA(this,a);break;case "loungeScreenConnected":JA(this,!0);break;case "loungeScreenDisconnected":nb(this.j,function(a){return"LOUNGE_SCREEN"==a.type});
JA(this,!1);break;case "remoteConnected":var b=new Sw(Ui(a.params.device));cb(this.j,function(a){return a.equals(b)})||jb(this.j,b);
break;case "remoteDisconnected":b=new Sw(Ui(a.params.device));nb(this.j,function(a){return a.equals(b)});
break;case "gracefulDisconnect":break;case "playlistModified":MA(this,a);break;case "nowPlaying":NA(this,a);break;case "onStateChange":OA(this,a);break;case "onVolumeChanged":PA(this,a);break;case "onSubtitlesTrackChanged":LA(this,a);break;default:this.T("Unrecognized action: "+a.action)}};
g.ug=function(a){a.params?this.T("Received: action="+a.action+", params="+Wi(a.params)):this.T("Received: action="+a.action);QA(this,a);RA(this,a);if(IA(this)){var b=this.L.clone(),c=!1,d,e,f,h,k,l;a.params&&(d=a.params.videoId||a.params.video_id,e=a.params.videoIds||a.params.video_ids,f=a.params.state,h=a.params.currentTime||a.params.current_time,k=a.params.volume,l=a.params.muted,n(a.params.currentError)&&Ui(a.params.currentError));if("onSubtitlesTrackChanged"==a.action)d==hA(this.L)&&(delete a.params.videoId,
Mb(a.params)?this.L.A=null:this.L.A=a.params,this.K("remotePlayerChange"));else if(hA(this.L)||"onStateChange"!=a.action)"playlistModified"!=a.action&&"nowPlayingPlaylist"!=a.action||e?(d||"nowPlaying"!=a.action&&"nowPlayingPlaylist"!=a.action?d||(d=hA(this.L)):this.L.setVideoId(""),e&&(e=e.split(","),kA(this.L,e,d))):kA(this.L,[]),lA(this.L,d)&&HA(this,"getPlaylist"),d&&this.L.setVideoId(d),b.index==this.L.index&&tb(b.videoIds,this.L.videoIds)?"playlistModified"!=a.action&&"nowPlayingPlaylist"!=
a.action||this.K("remoteQueueChange"):this.K("remoteQueueChange"),n(f)&&(a=parseInt(f,10),a=isNaN(a)?-1:a,-1==a&&-1E3==this.L.f&&(a=-1E3),0==a&&"0"==h&&(a=-1),c=c||a!=this.L.f,this.L.f=a,1==this.L.f?GA(this):EA(this)),h&&(a=parseInt(h,10),iA(this.L,isNaN(a)?0:a),c=!0),n(k)&&(a=parseInt(k,10),isNaN(a)||(c=c||this.L.volume!=a,this.L.volume=a),n(l)&&(l="true"==l,c=c||this.L.l!=l,this.L.l=l)),c&&this.K("remotePlayerChange")}};
function QA(a,b){switch(b.action){case "loungeStatus":var c=Ui(b.params.devices);a.j=$a(c,function(a){return new Sw(a)});
break;case "loungeScreenDisconnected":nb(a.j,function(a){return"LOUNGE_SCREEN"==a.type});
break;case "remoteConnected":var d=new Sw(Ui(b.params.device));cb(a.j,function(a){return a.equals(d)})||jb(a.j,d);
break;case "remoteDisconnected":d=new Sw(Ui(b.params.device)),nb(a.j,function(a){return a.equals(d)})}}
function RA(a,b){var c=!1;if("loungeStatus"==b.action)c=!!cb(a.j,function(a){return"LOUNGE_SCREEN"==a.type});
else if("loungeScreenConnected"==b.action)c=!0;else if("loungeScreenDisconnected"==b.action)c=!1;else return;if(!isNaN(a.O))if(c)FA(a);else return;c==IA(a)?c&&CA(a,1):c?(DA(a),a.P()&&a.L.reset(),CA(a,1),HA(a,"getNowPlaying"),KA(a)):a.B(1)}
g.zh=function(){if(this.l){var a=this.l;this.l=null;this.L.videoId!=a&&HA(this,"getNowPlaying")}};
zA.prototype.subscribe=zA.prototype.subscribe;zA.prototype.unsubscribeByKey=zA.prototype.va;zA.prototype.Ia=function(){var a=3;this.isDisposed()||(a=0,isNaN(this.D())?IA(this)&&(a=1):a=2);return a};
zA.prototype.getProxyState=zA.prototype.Ia;zA.prototype.B=function(a){this.T("Disconnecting with "+a);DA(this);this.K("beforeDisconnect",a);1==a&&jx();$z(this.f,a);this.dispose()};
zA.prototype.disconnect=zA.prototype.B;zA.prototype.Ha=function(){var a=this.L;if(this.l){var b=a=this.L.clone(),c=this.l,d=a.index,e=b.videoId;b.videoId=c;b.index=d;c!=e&&fA(b)}return mA(a)};
zA.prototype.getPlayerContextData=zA.prototype.Ha;zA.prototype.Ra=function(a){var b=new dA(a);b.videoId&&b.videoId!=this.L.videoId&&(this.l=b.videoId,M(this.F),this.F=L(u(this.zh,this),5E3));var c=[];this.L.j==b.j&&this.L.videoId==b.videoId&&this.L.index==b.index&&tb(this.L.videoIds,b.videoIds)||c.push("remoteQueueChange");this.L.f==b.f&&this.L.volume==b.volume&&this.L.l==b.l&&jA(this.L)==jA(b)&&Wi(this.L.A)==Wi(b.A)||c.push("remotePlayerChange");this.L.reset(a);x(c,function(a){this.K(a)},this)};
zA.prototype.setPlayerContextData=zA.prototype.Ra;zA.prototype.qa=function(){return this.f.A.loungeIdToken};
zA.prototype.getLoungeToken=zA.prototype.qa;zA.prototype.P=function(){var a=this.f.A.id,b=cb(this.j,function(b){return"REMOTE_CONTROL"==b.type&&b.id!=a});
return b?b.id:""};
zA.prototype.getOtherConnectedRemoteId=zA.prototype.P;zA.prototype.D=function(){var a=this.f;return a.j.enabled?a.j.C-v():NaN};
zA.prototype.getReconnectTimeout=zA.prototype.D;zA.prototype.Ab=function(){if(!isNaN(this.D())){var a=this.f.j;a.enabled&&(a.stop(),a.start(),a.D())}};
zA.prototype.reconnect=zA.prototype.Ab;function KA(a){M(a.H);a.H=L(u(a.B,a,1),864E5)}
function HA(a,b,c){c?a.T("Sending: action="+b+", params="+Wi(c)):a.T("Sending: action="+b);a.f.sendMessage(b,c)}
zA.prototype.Ja=function(a,b){HA(this,a,b);KA(this)};
zA.prototype.sendMessage=zA.prototype.Ja;function SA(a){Lw.call(this);this.B=0;this.Qa=TA();this.C=NaN;this.D=a;this.T("Initializing local screens: "+ww(this.Qa));this.l=UA();this.T("Initializing account screens: "+ww(this.l));this.Dc=null;this.f=[];this.j=[];VA(this,zy()||[]);this.T("Initializing DIAL devices: "+Dw(this.j));a=uw(gx());WA(this,a);this.T("Initializing online screens: "+ww(this.f));this.B=v()+3E5;XA(this)}
w(SA,Lw);g=SA.prototype;g.T=function(a){lw("RM",a)};
g.W=function(a){lw("RM",a)};
function UA(){var a=TA(),b=uw(gx());return Za(b,function(b){return!Jw(a,b)})}
function TA(){var a=uw(cx());return Za(a,function(a){return!a.uuid})}
function XA(a){Q("yt-remote-cast-device-list-update",function(){var a=zy();VA(this,a||[])},a);
Q("yt-remote-cast-device-status-update",a.Hh,a);a.ze();var b=v()>a.B?2E4:1E4;wf(u(a.ze,a),b)}
g.K=function(a,b){if(this.isDisposed())return!1;this.T("Firing "+a);return this.A.K.apply(this.A,arguments)};
g.ze=function(){var a=zy()||[];0==a.length||VA(this,a);a=YA(this);0==a.length||(ab(a,function(a){return!Jw(this.l,a)},this)&&ex()?ZA(this):$A(this,a))};
function aB(a,b){var c=YA(a);return Za(b,function(a){return a.uuid?(a=Iw(this.j,a.uuid),!!a&&"RUNNING"==a.status):!!Jw(c,a)},a)}
function VA(a,b){var c=!1;x(b,function(a){var b=Kw(this.Qa,a.id);b&&b.name!=a.name&&(this.T("Renaming screen id "+b.id+" from "+b.name+" to "+a.name),b.name=a.name,c=!0)},a);
c&&(a.T("Renaming due to DIAL."),bB(a));hx(Fw(b));var d=!tb(a.j,b,Hw);d&&a.T("Updating DIAL devices: "+Dw(a.j)+" to "+Dw(b));a.j=b;WA(a,a.f);d&&a.K("onlineReceiverChange")}
g.Hh=function(a){var b=Iw(this.j,a.id);b&&(this.T("Updating DIAL device: "+b.id+"("+b.name+") from status: "+b.status+" to status: "+a.status+" and from activityId: "+b.f+" to activityId: "+a.f),b.f=a.f,b.status=a.status,hx(Fw(this.j)));WA(this,this.f)};
function WA(a,b,c){var d=aB(a,b),e=!tb(a.f,d,qw);if(e||c)0==b.length||fx($a(d,rw));e&&(a.T("Updating online screens: "+ww(a.f)+" -> "+ww(d)),a.f=d,a.K("onlineReceiverChange"))}
function $A(a,b){var c=[],d={};x(b,function(a){a.token&&(d[a.token]=a,c.push(a.token))});
var e={method:"POST",S:{lounge_token:c.join(",")},context:a,R:function(a,b){var c=[];x(b.screens||[],function(a){"online"==a.status&&c.push(d[a.loungeToken])});
var e=this.Dc?cB(this,this.Dc):null;e&&!Jw(c,e)&&c.push(e);WA(this,c,!0)}};
U(zw(a.D,"/pairing/get_screen_availability"),e)}
function ZA(a){var b=YA(a),c=$a(b,function(a){return a.id});
0!=c.length&&(a.T("Updating lounge tokens for: "+Wi(c)),U(zw(a.D,"/pairing/get_lounge_token_batch"),{S:{screen_ids:c.join(",")},method:"POST",context:a,R:function(a,c){dB(this,c.screens||[]);this.Qa=Za(this.Qa,function(a){return!!a.token});
bB(this);$A(this,b)}}))}
function dB(a,b){x(ob(a.Qa,a.l),function(a){var d=cb(b,function(b){return a.id==b.screenId});
d&&(a.token=d.loungeToken)})}
function bB(a){var b=TA();tb(a.Qa,b,qw)||(a.T("Saving local screens: "+ww(b)+" to "+ww(a.Qa)),bx($a(a.Qa,rw)),WA(a,a.f,!0),VA(a,zy()||[]),a.K("managedScreenChange",YA(a)))}
g.Qd=function(a,b){for(var c=YA(this),c=$a(c,function(a){return a.name}),d=a,e=2;fb(c,d);)d=b.call(m,e),e++;
return d};
function cB(a,b){var c=Kw(YA(a),b);a.T("Found screen: "+vw(c)+" with key: "+b);return c}
function YA(a){return ob(a.l,Za(a.Qa,function(a){return!Jw(this.l,a)},a))}
;function eB(a){Mw.call(this,"ScreenServiceProxy");this.la=a;this.f=[];this.f.push(this.la.$_s("screenChange",u(this.Sh,this)));this.f.push(this.la.$_s("onlineScreenChange",u(this.Pg,this)))}
w(eB,Mw);g=eB.prototype;g.ra=function(a){return this.la.$_gs(a)};
g.contains=function(a){return!!this.la.$_c(a)};
g.get=function(a){return this.la.$_g(a)};
g.start=function(){this.la.$_st()};
g.uc=function(a,b,c){this.la.$_a(a,b,c)};
g.remove=function(a,b,c){this.la.$_r(a,b,c)};
g.qc=function(a,b,c,d){this.la.$_un(a,b,c,d)};
g.M=function(){for(var a=0,b=this.f.length;a<b;++a)this.la.$_ubk(this.f[a]);this.f.length=0;this.la=null;eB.J.M.call(this)};
g.Sh=function(){this.K("screenChange")};
g.Pg=function(){this.K("onlineScreenChange")};
wx.prototype.$_st=wx.prototype.start;wx.prototype.$_gspc=wx.prototype.Th;wx.prototype.$_gsppc=wx.prototype.Re;wx.prototype.$_c=wx.prototype.contains;wx.prototype.$_g=wx.prototype.get;wx.prototype.$_a=wx.prototype.uc;wx.prototype.$_un=wx.prototype.qc;wx.prototype.$_r=wx.prototype.remove;wx.prototype.$_gs=wx.prototype.ra;wx.prototype.$_gos=wx.prototype.Qe;wx.prototype.$_s=wx.prototype.subscribe;wx.prototype.$_ubk=wx.prototype.va;function fB(){var a=!!K("MDX_ENABLE_CASTV2"),b=!!K("MDX_ENABLE_QUEUE");a?p("yt.mdx.remote.castv2_",!0,void 0):ry();wm();Uw();gB||(gB=new yw,kx()&&(gB.f="/api/loungedev"));hB||a||(hB=new SA(gB),hB.subscribe("screenPair",iB),hB.subscribe("managedScreenChange",jB),hB.subscribe("onlineReceiverChange",function(){R("yt-remote-receiver-availability-change")}));
kB||(kB=r("yt.mdx.remote.deferredProxies_")||[],p("yt.mdx.remote.deferredProxies_",kB,void 0));lB(b);b=mB();if(a&&!b){var c=new wx(gB);p("yt.mdx.remote.screenService_",c,void 0);b=mB();Vx(c,function(a){a?jy()&&iy():c.subscribe("onlineScreenChange",function(){R("yt-remote-receiver-availability-change")})})}}
function nB(){rg(oB);oB.length=0;gg(pB);pB=null;kB&&(x(kB,function(a){a(null)}),kB.length=0,kB=null,p("yt.mdx.remote.deferredProxies_",null,void 0));
hB&&(gg(hB),hB=null);gB=null;vy()}
function qB(){var a=jy();if(!a)return null;if(!hB){var b=mB().ra();return Kw(b,a)}return cB(hB,a)}
function rB(){var a=sB();return a&&3!=a.getProxyState()?new nA(a,void 0):null}
function tB(a){lw("remote",a)}
function uB(){return r("yt.mdx.remote.screenService_")}
function mB(){if(!pB){var a=uB();pB=a?new eB(a):null}return pB}
function jy(){return r("yt.mdx.remote.currentScreenId_")}
function vB(a){p("yt.mdx.remote.currentScreenId_",a,void 0);if(hB){var b=hB;b.B=v()+3E5;if((b.Dc=a)&&(a=cB(b,a))&&!Jw(b.f,a)){var c=pb(b.f);c.push(a);WA(b,c,!0)}}}
function wB(){p("yt.mdx.remote.currentDialId_","",void 0)}
function sB(){return r("yt.mdx.remote.connection_")}
function xB(a){var b=sB();p("yt.mdx.remote.connectData_",null,void 0);a?sB():(vB(""),wB());p("yt.mdx.remote.connection_",a,void 0);kB&&(x(kB,function(b){b(a)}),kB.length=0);
b&&!a?sg("yt-remote-connection-change",!1):!b&&a&&R("yt-remote-connection-change",!0)}
function yB(){var a=Xw();if(!a)return null;if(uB()){var b=mB().ra();return Kw(b,a)}return hB?cB(hB,a):null}
function zB(a){jy();vB(a.id);a=new zA(gB,a,AB());a.connect(1,r("yt.mdx.remote.connectData_"));a.subscribe("beforeDisconnect",function(a){sg("yt-remote-before-disconnect",a)});
a.subscribe("beforeDispose",function(){sB()&&(sB(),xB(null))});
xB(a)}
function iB(a){tB("Paired with: "+vw(a));a?zB(a):xB(null)}
function jB(){var a=jy();if(a&&!qB()){tB("Dropping current screen with id: "+a);hB&&(a=hB,M(a.C),a.C=NaN);a:{if(a=sB())if(a=a.getOtherConnectedRemoteId()){tB("Do not stop DIAL due to "+a);wB();break a}(a=r("yt.mdx.remote.currentDialId_"))?(tB("Stopping DIAL: "+a),Dy(a),wB()):(a=qB())&&a.uuid&&(tB("Stopping DIAL: "+a.uuid),Dy(a.uuid))}ky()?dy().stopSession():ay("stopSession called before API ready.");(a=sB())?a.disconnect(1):(sg("yt-remote-before-disconnect",1),sg("yt-remote-connection-change",!1));
xB(null)}yB()||jx()}
var gB=null,kB=null,pB=null,hB=null;function lB(a){var b=AB();if(Mb(b)){var b=Ww(),c=um("yt-remote-session-name")||"",d=um("yt-remote-session-app")||"",b={device:"REMOTE_CONTROL",id:b,name:c,app:d};a&&(b["mdx-version"]=3);p("yt.mdx.remote.channelParams_",b,void 0)}}
function AB(){return r("yt.mdx.remote.channelParams_")||{}}
var oB=[];function BB(a){this.f=a;a.then(u(function(){},this))}
function CB(a,b,c){for(var d=Array(arguments.length-2),e=2;e<arguments.length;e++)d[e-2]=arguments[e];e=DB(a,b).then(function(a){return a.apply(null,d)});
return new BB(e)}
var EB={};function DB(a,b){var c=EB[b];if(c)return c;c=(c=r(b))?tr(c):(new or(function(b,c){var f=document.createElement("script");f.async=!0;kd(f,vc(dc(a)));f.onload=f.onreadystatechange=function(){f.readyState&&"loaded"!=f.readyState&&"complete"!=f.readyState||b()};
f.onerror=c;(document.head||document.getElementsByTagName("head")[0]).appendChild(f)})).then(function(){var c=r(b);
if(!c)throw Error("Failed to load "+b+" from "+a);return c});
return EB[b]=c}
function FB(a,b,c){a.f.then(function(a){var e=a[b];if(!e)throw Error("Method not found: "+b);return e.apply(a,c)})}
;function GB(a){this.f=a}
function HB(a,b){var c=b||{},c={apiKey:c.Li||c.apiKey,environment:c.Ni||c.environment,helpCenterPath:c.Pi||c.helpCenterPath,locale:c.locale||c.locale||"en".replace(/-/g,"_"),productData:c.Ri||c.productData,receiverUri:c.Si||c.receiverUri,theme:c.theme||c.theme,window:c.window||c.window},c=CB(IB,"help.service.Lazy.create",a,c);return new GB(c)}
var IB=ec("https://www.gstatic.com/feedback/js/help/prod/service/lazy.min.js");GB.prototype.l=function(a){FB(this.f,"startFeedback",arguments)};
GB.prototype.A=function(a){FB(this.f,"startHelp",arguments)};
GB.prototype.j=function(a){FB(this.f,"loadChatSupport",arguments)};function JB(a,b,c){a.timeOfStartCall=(new Date).getTime();var d=c||m;if("help"==a.flow){var e=r("document.location.href",d);!a.helpCenterContext&&e&&(a.helpCenterContext=e.substring(0,1200));e=!0;if(b&&JSON&&JSON.stringify){var f=JSON.stringify(b);(e=1200>=f.length)&&(a.psdJson=f)}e||(b={invalidPsd:!0})}b=[a,b,c];d.GOOGLE_FEEDBACK_START_ARGUMENTS=b;c=a.serverUri||"//www.google.com/tools/feedback";if(e=d.GOOGLE_FEEDBACK_START)e.apply(d,b);else{b=c+"/load.js?";for(var h in a)c=a[h],null!=c&&!ka(c)&&
(b+=encodeURIComponent(h)+"="+encodeURIComponent(c)+"&");a=d.document;d=a.createElement("script");d.src=b;a.body.appendChild(d)}}
p("userfeedback.api.startFeedback",JB,void 0);var KB=!1,LB="";function MB(a){a=a.match(/[\d]+/g);if(!a)return"";a.length=3;return a.join(".")}
(function(){if(navigator.plugins&&navigator.plugins.length){var a=navigator.plugins["Shockwave Flash"];if(a&&(KB=!0,a.description)){LB=MB(a.description);return}if(navigator.plugins["Shockwave Flash 2.0"]){KB=!0;LB="2.0.0.11";return}}if(navigator.mimeTypes&&navigator.mimeTypes.length&&(a=navigator.mimeTypes["application/x-shockwave-flash"],KB=!!a&&a.enabledPlugin)){LB=MB(a.enabledPlugin.description);return}try{var b=new ActiveXObject("ShockwaveFlash.ShockwaveFlash.7");KB=!0;LB=MB(b.GetVariable("$version"));
return}catch(c){}try{b=new ActiveXObject("ShockwaveFlash.ShockwaveFlash.6");KB=!0;LB="6.0.21";return}catch(c){}try{b=new ActiveXObject("ShockwaveFlash.ShockwaveFlash"),KB=!0,LB=MB(b.GetVariable("$version"))}catch(c){}})();
var NB=LB;function OB(a){return(a=a.exec(Xb))?a[1]:""}
var PB=function(){if(Oj)return OB(/Firefox\/([0-9.]+)/);if(F||Oc||Nc)return Zc;if(Sj)return OB(/Chrome\/([0-9.]+)/);if(Tj&&!(Mc()||E("iPad")||E("iPod")))return OB(/Version\/([0-9.]+)/);if(Pj||Qj){var a=/Version\/(\S+).*Mobile\/(\S+)/.exec(Xb);if(a)return a[1]+"."+a[2]}else if(Rj)return(a=OB(/Android\s+([0-9.]+)/))?a:OB(/Version\/([0-9.]+)/);return""}();p("userfeedback.api.isBrowserSupportedForGenie",function(){return QB()},void 0);
function QB(){return F?0<=Ra(PB,"8"):Oj?0<=Ra(PB,"15"):Tj?0<=Ra(PB,"5"):Sj||Oc}
p("userfeedback.api.isBrowserSupportedForHelp",QB,void 0);function RB(a){return K("EXPERIMENT_FLAGS",{})[a]}
;var SB=[],TB={},UB=[],VB=!1;function WB(){UB.push(Q("player-ad-start",XB));SB.push(P(document.body,"click",YB,"yt-google-help-link"))}
function YB(a){a.preventDefault();var b=J(a.currentTarget,"ghelp-anchor")||a.currentTarget,c=J(a.currentTarget,"ghelp-tracking-param")||"",b=document.getElementById(b),d=K("GOOGLE_HELP_CONTEXT",void 0),e=K("GOOGLE_HELP_PRODUCT_ID",void 0);a=!!J(a.currentTarget,"load-chat-support");ZB(b,e,d,K("GOOGLE_HELP_PRODUCT_DATA"),a,!1,c)}
function $B(a,b){var c=K("FEEDBACK_LOCALE_LANGUAGE"),d=TB;Tb(d,K("FEEDBACK_LOCALE_EXTRAS",{}));a&&Tb(d,a);try{var e,f=r("yt.player.getPlayerByElement");(e=f?f("player-api"):null)&&e.pauseVideo&&e.pauseVideo();var h=oi.getInstance();d.flashVersion=h.getVersion().join(".");e&&(d.playback_id=e.getVideoData().cpn)}catch(k){}b&&Tb(d,{trackingParam:b});return{helpCenterPath:"/youtube",locale:c,productData:d}}
function aC(){var a=K("SESSION_INDEX"),b=K("FEEDBACK_BUCKET_ID"),c={abuseLink:"https://support.google.com/youtube/bin/answer.py?answer=140536",customZIndex:"2000000005"};RB("gfeedback_for_signed_out_users_enabled")&&(c.allowNonLoggedInFeedback=!0);a&&(c.authuser=a+"");b&&(c.bucket=b);return c}
function ZB(a,b,c,d,e,f,h,k){b=(b||"59")+"";d=$B(d,h);a={context:c,anchor:a,enableSendFeedback:f?!1:!0,defaultHelpArticleId:k};Tb(a,aC());try{var l=HB(b,d);if(e&&!VB)try{l.j(a),VB=!0}catch(q){}l.A(a);return!1}catch(q){return!0}}
function bC(){if(!(F?0<=Ra(PB,"7")&&0<=Ra(NB,"9"):Oj?0<=Ra(PB,"3.6"):Tj?0<=Ra(PB,"5"):Sj||Oc)){var a=qd("reportbug");x(a,function(a){Gh(a,!1)})}}
function XB(){var a=G("movie_player");if(a&&a.currentAdInformation){var b={};try{b=a.currentAdInformation()}catch(c){}a=b;a.adIds&&(TB.ad_ids=a.adIds.join());a.adSystems&&(TB.ad_systems=a.adSystems.join())}}
;function cC(){ll("ol");K("CSI_TICK_PAINT_TIME")?window.requestAnimationFrame&&!document.hidden?window.requestAnimationFrame(function(){setTimeout(function(){ll("cpt");dC()},0)}):document.hidden?(ll("cpt"),dC()):setTimeout(function(){ll("cpt");
dC()},0):dC()}
function dC(){eC("init");var a=K("PAGE_NAME",void 0);a&&eC("init-"+a)}
function eC(a){r("yt.scheduler.instance")?fC.push(vg(ra(sg,a),0)):R(a)}
var fC=[];var gC;function hC(){if(!gC){var a=G("watch-queue");if(!a)return[];gC=H("watch-queue-items-list",a)}var b=[],a=Kd(gC);x(a,function(a){(a=J(a,"video-id"))&&b.push(a)});
return b}
;var iC;function jC(a,b){this.type=a;this.videoIds=b||[]}
function kC(a,b){U("/watch_queue_ajax",{method:"POST",Z:{action_check_playability:1},S:{video_ids:a.join(",")},R:function(a,d){d&&fa(d)?b(d):b([])},
onError:function(){b([])}})}
function lC(a,b,c){kC(t(a)?[a]:a,function(d){0==d.length?c&&c():t(a)?mC(a,b,c):mC(d,b,c)})}
function mC(a,b,c){var d="";t(a)&&(d=a,a=[d]);var e=new jC(0,a);iC?nC(function(){d?iC.rd(d):iC.sd(a)},e,b,c):oC(e,{action_add_to_watch_queue:1},a,b,c)}
function pC(a,b,c){t(a)&&(a=[a]);var d=new jC(1,a);iC?nC(function(){x(a,function(a){iC.Be(a)})},d,b,c):oC(d,{action_remove_from_watch_queue:1},a,b,c)}
function qC(a,b,c){var d=new jC(2);iC?nC(function(){iC.qd(a)},d,b,c):c&&L(function(){c("Not implemented")},0)}
function rC(a,b){var c=new jC(2);iC?nC(function(){},c,a,b):b&&L(function(){b("Not implemented")},0)}
function sC(a){iC=a;iC.subscribe("remoteQueueChange",function(){R("queue-change",new jC(2))})}
function tC(){var a=rB();gg(iC);iC=null;a?sC(a):R("queue-change",new jC(2))}
function nC(a,b,c,d){iC&&1==iC.f?(a.call(m),c&&L(function(){c()},0),n(b)&&R("queue-change",b)):d&&L(function(){d()},0)}
function oC(a,b,c,d,e){U("/watch_queue_ajax",{method:"POST",Z:b,S:{list:"WQ",video_ids:c.join(",")},R:function(){d&&d();R("queue-change",a)},
onError:function(){e&&e()}})}
var uC=[];function vC(a,b){var c=K("RESUME_COOKIE_NAME",void 0);if(c){var d=Bg(c,"").split(","),d=Za(d,function(b){return 0!=b.indexOf(a)&&!!b.length});
4<=d.length&&d.shift();d.push(a+":"+b);Ag(c,d.join(","),1814400,"/")}}
function wC(a){var b=K("RESUME_COOKIE_NAME",void 0);if(b){var c=Bg(b,"").split(","),c=Za(c,function(b){return 0!=b.indexOf(a)});
0==c.length?Cg(b):Ag(b,c.join(","),1814400,"/")}}
;function xC(){fB();uC.push(Q("yt-remote-connection-change",tC));var a=rB();a&&sC(a);Fm("addto-watch-queue-button","click",yC);Fm("addto-tv-queue-button","click",yC);Fm("addto-watch-queue-button-success","click",zC);Fm("addto-watch-queue-menu-choice","click",AC);BC.push(Q("watch-queue-update",CC));CC()}
function DC(a){return"tv-queue"==J(a,"style")?"addto-tv-queue-button":"addto-watch-queue-button"}
function yC(a){var b=DC(a);Bb(a,b,"addto-watch-queue-button-loading");var c=J(a,"video-ids"),d=J(a,"list-id"),e=fp(dp.getInstance(),a);d?qC(d,function(){EC(a)},function(c){FC(a,b,e,c)}):lC(c,function(){EC(a)},function(c){FC(a,b,e,c)})}
function AC(a){var b=J(a,"action");GC(a,HC[b])}
function zC(a){Bb(a,"addto-watch-queue-button-success","addto-watch-queue-button-loading");var b=J(a,"video-ids"),c=J(a,"list-id"),d=fp(dp.getInstance(),a);c?rC(function(){IC(a)},function(b){FC(a,"addto-watch-queue-button-success",d,b)}):pC(b,function(){IC(a)},function(b){FC(a,"addto-watch-queue-button-success",d,b)})}
function EC(a){Bb(a,"addto-watch-queue-button-loading","addto-watch-queue-button-success");var b=zf("ADDTO_WATCH_QUEUE_ADDED");ip(dp.getInstance(),a,b);J(a,"list-id")?R("watch-queue-addto-playlist-added"):R("watch-queue-addto-video-added")}
function IC(a){var b=DC(a);Bb(a,"addto-watch-queue-button-loading",b);b="addto-watch-queue-button"==b?zf("ADDTO_WATCH_QUEUE"):zf("ADDTO_TV_QUEUE");ip(dp.getInstance(),a,b);J(a,"list-id")?R("watch-queue-addto-playlist-removed"):R("watch-queue-addto-video-removed")}
function FC(a,b,c,d){Bb(a,"addto-watch-queue-button-loading","addto-watch-queue-button-error");d=d||zf("ADDTO_WATCH_QUEUE_ERROR");ip(dp.getInstance(),a,d);L(function(){Bb(a,"addto-watch-queue-button-error",b);ip(dp.getInstance(),a,c)},5E3)}
function GC(a,b){var c=J(a,"video-ids");c&&(t(c)&&(c=[c]),kC(c,function(d){d.length==c.length&&b(a)}))}
function CC(){var a=hC();if(!tb(JC,a)){JC=a;var b={};x(JC,function(a){b[a]=!0});
a=qd("addto-queue-button");x(a,function(a){var d=J(a,"video-ids");if(d&&t(d)){var e=DC(a);b[d]?(Bb(a,e,"addto-watch-queue-button-success"),d=zf("ADDTO_WATCH_QUEUE_ADDED")):(Bb(a,"addto-watch-queue-button-success",e),d="addto-watch-queue-button"==e?zf("ADDTO_WATCH_QUEUE"):zf("ADDTO_TV_QUEUE"));ip(dp.getInstance(),a,d)}})}}
var HC={"play-next":function(a){var b=J(a,"list-id");a=J(a,"video-ids");b?R("watch-queue-addto-playlist-play-next",b,a):R("watch-queue-addto-video-play-next",a)},
"play-now":function(a){var b=J(a,"list-id");a=J(a,"video-ids");b?R("watch-queue-addto-playlist-play-now",b,a):R("watch-queue-addto-video-play-now",a)}},BC=[],JC=[];var xr=[],KC=!1;function LC(a){var b=K("YPC_LOADER_CSS",void 0),c=K("YPC_LOADER_JS",void 0);KC&&(c="www/ypc_core");xr.length||(xr.push(new or(function(a){ik(b,a)})),xr.push(new or(function(a){bk(c,a)})));
wr().then(function(){a&&a()})}
;function MC(a){this.f=a||{apiaryHost:K("APIARY_HOST",void 0),ud:K("APIARY_HOST_FIRSTPARTY",void 0),gapiHintOverride:K("GAPI_HINT_OVERRIDE"),gapiHintParams:K("GAPI_HINT_PARAMS",void 0),innertubeApiKey:K("INNERTUBE_API_KEY",void 0),innertubeApiVersion:K("INNERTUBE_API_VERSION",void 0),ag:K("INNERTUBE_CONTEXT_CLIENT_NAME","WEB"),innertubeContextClientVersion:K("INNERTUBE_CONTEXT_CLIENT_VERSION",void 0),dg:K("INNERTUBE_CONTEXT_HL",void 0),cg:K("INNERTUBE_CONTEXT_GL",void 0)};NC||(NC=OC(this.f))}
var NC=null;function OC(a){return(new or(function(b){try{Ss("client",{gapiHintOverride:a.gapiHintOverride,_c:{jsl:{h:a.gapiHintParams}},callback:b})}catch(c){yf(c)}})).then(function(){})}
MC.prototype.j=function(){var a=r("gapi.config.update");a("googleapis.config/auth/useFirstPartyAuth",!0);wa(Qa(this.f.apiaryHost))||a("googleapis.config/root","//"+this.f.apiaryHost);wa(Qa(this.f.ud))||a("googleapis.config/root-1p","//"+this.f.ud);a("googleapis.config/sessionIndex",K("SESSION_INDEX"));r("gapi.client.setApiKey")(this.f.innertubeApiKey)};
function PC(a,b,c,d){var e={path:"/youtubei/"+a.f.innertubeApiVersion+"/"+b,headers:{"X-Goog-Visitor-Id":K("VISITOR_DATA")},method:"POST",body:Wi(c)},f=u(a.j,a);NC.then(function(){f();r("gapi.client.request")(e).execute(d||ca)})}
function QC(a,b){var c=RC,d={},e,f=!1;0<d.timeout&&(e=L(function(){f||(f=!0,d.rb&&d.rb())},d.timeout));
PC(c,a,b,function(a){if(!f)if(f=!0,e&&M(e),a)d.R&&d.R(a);else if(d.onError)d.onError()})}
;var RC,SC={log_event:"events",log_interaction:"interactions"},TC=0,UC=r("yt.logging.transport.logsQueue_")||{};p("yt.logging.transport.logsQueue_",UC,void 0);function VC(){M(TC);if(!Mb(UC)){RC||(RC=new MC);for(var a in UC){var b;b=RC;b={client:{hl:b.f.dg,gl:b.f.cg,clientName:b.f.ag,clientVersion:b.f.innertubeContextClientVersion}};K("DELEGATED_SESSION_ID")&&(b.user={onBehalfOfUser:K("DELEGATED_SESSION_ID")});b={context:b};b.requestTimeMs=v();b[SC[a]]=UC[a];QC(a,b);delete UC[a]}}}
;function WC(a,b,c,d,e){if(ro())LC(function(){r("yt.www.ypc.checkout.showYpcOverlay")(a,b,c,d,e)});
else{var f={ypc_it:a,ypc_ii:b,ypc_ft:c};d&&(f.ypc_irp=d);e&&(f.ypc_cc=e);f=XC(f);xk(f)}}
function YC(a,b){if(ro())LC(function(){r("yt.www.ypc.checkout.showYpcOverlayForInnertubeRequestParams")(a,b)});
else{var c=XC({ypc_ft:a,ypc_irp:b});xk(c)}}
function ZC(a,b,c,d,e,f){if(ro())LC(function(){r("yt.www.ypc.checkout.offerpurchaser.purchaseOffer")(a,b,c,"D",d,e,f)});
else{var h={ypc_it:b,ypc_ii:c,ypc_ft:"D"};f&&(h.ypc_irp=f);h=XC(h);xk(h)}}
function $C(a,b){if(ro())LC(function(){r("yt.www.ypc.subscription.openUnsubscribeOverlay")(a,b)});
else throw Error("Unsubscribe triggered when user not signed in.");}
function XC(a){a=ci(window.location.href,a);var b=K("YPC_SIGNIN_URL",void 0),c=bi(b)["continue"],c=ci(c,{next:a});return ci(b,{"continue":c})}
;var aD=[],bD=[];
function cD(a){var b=a.currentTarget;if(b){a=J(b,"ypc-offer-id");var c=J(b,"ypc-item-type"),d=J(b,"ypc-item-id"),e=J(b,"ypc-offer-jwt"),f=J(b,"ypc-offer-encrypted-purchase-params"),h=J(b,"ypc-irp");try{var k=J(b,"innertube-clicktracking");if(k&&!K("SERVICE_CLICKTRACKING_ENABLED")){var l=new tk(k),k={};void 0!==l.f?k.trackingParams=l.f:(k.veType=l.l,k.veCounter=l.j);var q={click:{csn:K("EVENT_ID",void 0),visualElement:k}};q.eventTimeMs=v();q.lactMs=sk();UC.log_interaction=UC.log_interaction||[];var z=
UC.log_interaction;z.push(q);20<=z.length?VC():(M(TC),TC=L(VC,K("VISIBILITY_TIMEOUT",1E4)))}}catch(C){dD("offer-button-click-logging-failed")}ZC(a,c,d,e,f,h)}}
function eD(a){(a=a.currentTarget)&&fD(a)}
function gD(a){LC(a.Vb)}
function hD(a){var b;dD("container-button-click-attempt");b=H("ypc-checkout-button",a.f);a=H("ytr-purchase-button",a.f);if(b||a&&a.href)a&&a.href?yk(a.href):b&&fD(b)}
function iD(a){var b=a.f;a=b.itemId;var c=b.itemType,b=b.flowType;dD("paid-subscribe-button-click",{itemType:c,itemId:a});WC(c,a,b)}
function fD(a){var b=J(a,"ypc-item-type"),c=J(a,"ypc-item-id"),d=J(a,"ypc-flow-type");a=J(a,"ypc-irp")||void 0;ro()?dD("purchase-button-click",{itemId:c,itemType:b}):dD("signin-button-click");WC(b,c,d,a)}
function jD(a){var b=a.currentTarget;a=J(b,"ypc-item-type");b=J(b,"ypc-item-id");a&&b&&(dD("unsubscribe-button-click",{itemId:b,itemType:a}),$C(a,b))}
function kD(a){var b=a.f;a=b.itemType;b=b.itemId;dD("paid-unsubscribe-button-click",{itemType:a,itemId:b});$C(a,b)}
function dD(a,b){var c={};Tb(c,{label:a,pageName:K("PAGE_NAME")});b&&Tb(c,b);c=Wh(c);zm("ypc-checkout",c,void 0)}
;var lD={ri:"ypc_cc",ti:"ypc_ft",wi:"ypc_irp",xi:"ypc_ii",yi:"ypc_it"};function mD(){var a=bi(window.location.href);if(Gi){Db(lD,function(b){Nb(a,b)});
var b=Yh(window.location.href.split("?",2)[0],a);Hi(!0,"state");Ji(b)}}
;function nD(a){KC=!!a;H("ypc-delayedloader-target")&&LC();a=bi(window.location.href);var b=a.ypc_it,c=a.ypc_ii,d=a.ypc_ft||"D",e=a.ypc_irp,f=a.ypc_cc;"channel"==K("PAGE_NAME")&&"fan_fund"in a&&(d="T",b="U",c=K("CHANNEL_ID",void 0));"channel"==K("PAGE_NAME")&&"ypc_cc"in a&&(b="U",c=K("CHANNEL_ID",void 0));if(e||c&&b)mD(),c&&b?WC(b,c,d,e,f):YC(d,e);aD.push(P(document.body,"click",eD,"ypc-checkout-button"),P(document.body,"click",cD,"ypc-offer-button"),P(document.documentElement,"click",jD,"ypc-unsubscribe-link"),
P(document.documentElement,"click",jD,"ypc-unsubscribe-button"));bD.push(Xk(uv,gD),Xk(tv,hD),Xk(zv,iD),Xk(xv,kD))}
;function oD(){(function(){try{for(var a=this;a.parent!=a;){if("$"==a.frameElement.src)throw"odd";a=a.parent}if(null!=a.frameElement)throw"busted";}catch(b){document.close(),document.open(),window.open("/","_top"),ld(top.location,"/")}})()}
function pD(a){"block"==a.responseText&&oD()}
if(window!=window.top){var qD=document.referrer;if(window.parent!=window.top)oD();else if(ii(qD))oD();else{var rD=fi(qD);hi(rD)||U("/roger_rabbit",{format:"RAW",method:"POST",Z:{location:encodeURIComponent(qD),self:encodeURIComponent(window.location.href),user_agent:encodeURIComponent(navigator.userAgent)},R:pD})}};function sD(a){for(var b=0,c=a.length;b<c;b++){var d=rd("img",null,a[b])[0];if(d){var e=J(d,"thumb");e&&(d.src=e,xe(d,"thumb"))}}}
;var tD=[];var uD=F?'javascript:""':"about:blank";function vD(a,b,c,d){eg.call(this);this.A=a;this.j=b;this.O=c;this.B=d;this.l=zd("div",{"class":"ads-mute-button"});Td(this.l,String.fromCharCode(215));this.f=zd("div");this.f.innerHTML=wD.render({mute_gone:this.j.mute_gone,mute_question:this.j.mute_question,mute_undo:this.j.mute_undo});this.C=H("ads-mute-undo",this.f);N(this.l,"click",u(this.D,this));this.A.firstElementChild.appendChild(this.l);a=Jb(this.j.mute_survey);xb(a);x(a,function(a){var b=zd("input",{"class":"yt-uix-form-input-radio",type:"radio"}),
c=zd("span",{"class":"yt-uix-form-input-radio-element"}),b=zd("span",{"class":"yt-uix-form-input-radio-container"},b,c),b=zd("label","ads-mute-option",b,a);N(b,"click",u(this.F,this,this.j.mute_survey[a]));this.f.firstChild.appendChild(b)},this);
N(this.f,"click",Of);N(this.C,"click",u(this.H,this));qg(this.dispose,this)}
w(vD,eg);var wD=new zq('<div class="ads-mute-survey"><span class="ads-mute-check"></span><b>__mute_gone__</b> __mute_question__<div class="ads-mute-undo">__mute_undo__</div></div>');vD.prototype.M=function(){x(qd("ads-mute-option",this.f),function(a){Mf(a)});
Mf(this.l);Id(this.l);Mf(this.f);Id(this.f);Mf(this.C)};
vD.prototype.D=function(a){a.stopPropagation();a.preventDefault();this.B&&Xj(this.j.mute_url);this.A.firstElementChild.appendChild(this.f);A(Rd(this.f),"contains-mute-survey")};
vD.prototype.H=function(a){a.stopPropagation();a.preventDefault();this.j.mute_undo_url&&this.B&&Xj(this.j.mute_undo_url);B(Rd(this.f),"contains-mute-survey");Id(this.f)};
vD.prototype.F=function(a,b){b.stopPropagation();b.preventDefault();this.B&&Xj(a);Id(this.A);this.O();this.dispose()};var xD=["pyv-feed-item-headline-dest-url","pyv-feed-item-thumb-dest-url","pyv-feed-item-channel-thumb-dest-url"],yD="",zD="",AD=[],$h={};function BD(a,b,c,d){var e=cb(b.media_template_data,function(a){return!!a.imageUrl});
e&&(a={video_id:e.videoId,ad_type:a,headline:Ja(b.line1),image_url:e.imageUrl,description1:Ja(b.line2),description2:Ja(b.line3),channel_title:e.channelName,visible_url:Ja(b.visible_url)},RB("desktop_home_pyv_skip_redirect")?yD=zD=e=(new nj(Ja(b.url))).j.get("adurl")||"":(yD=Ja(b.url),zD=(new nj(yD)).j.get("adurl")||""),wa(Qa(b.creative_view_url))||AD.push(CD(Ja(b.creative_view_url))),wa(Qa(b.p_creative_view_url))||AD.push(CD(Ja(b.p_creative_view_url))),wa(Qa(b.engaged_view_url))||($h.part2viewed=
CD(Ja(b.engaged_view_url))),wa(Qa(b.p_engaged_view_url))||($h.part2viewedgaia=CD(Ja(b.p_engaged_view_url))),wa(Qa(b.videoplaytime_25_url))||($h.videoplaytime25=CD(Ja(b.videoplaytime_25_url))),wa(Qa(b.p_videoplaytime_25_url))||($h.videoplaytime25gaia=CD(Ja(b.p_videoplaytime_25_url))),wa(Qa(b.videoplaytime_50_url))||($h.videoplaytime50=CD(Ja(b.videoplaytime_50_url))),wa(Qa(b.p_videoplaytime_50_url))||($h.videoplaytime50gaia=CD(Ja(b.p_videoplaytime_50_url))),wa(Qa(b.videoplaytime_75_url))||($h.videoplaytime75=
CD(Ja(b.videoplaytime_75_url))),wa(Qa(b.p_videoplaytime_75_url))||($h.videoplaytime75gaia=CD(Ja(b.p_videoplaytime_75_url))),wa(Qa(b.videoplaytime_100_url))||($h.videoplaytime100=CD(Ja(b.videoplaytime_100_url))),wa(Qa(b.p_videoplaytime_100_url))||($h.videoplaytime100gaia=CD(Ja(b.p_videoplaytime_100_url))),U("/pyv?"+Wh(a),{format:"XML",R:function(a,b){c&&b.html_content&&id(G(c),b.html_content);d&&d(a,b)},
Na:!0}))}
function DD(a){0==a.length?ED():BD("watch_related",a[0],null,function(b,c){var d=c.html_content,e=G(window.pyv_related_box_id||"watch-related");if(e){var f=e.innerHTML;d&&0!=f.indexOf(yc(d))&&e.insertBefore(Dd(Hc(yc(d))),e.firstChild);if(d=G("pyv-watch-related-dest-url"))e=!K("PYV_DISABLE_MUTE")&&a[0].mute_url&&a[0].mute_survey,d.setAttribute("href",yD),e&&(d=Rd(d),A(d,"contains-mute-button"),new vD(d,a[0],ca,!0));x(AD,function(a){Xj(a,void 0,ak(a))})}})}
function ED(){var a=qd("related-video-featured");x(a,function(a){S(a)})}
function FD(){var a=qd("related-video-featured");2==a.length?y(a[0],"related-video-featured-booster")?S(a[1]):S(a[0]):x(a,function(a){S(a)})}
function GD(){var a={};a.adpings=Zh();uk(zD,a)}
function CD(a){if(RB("desktop_force_https_pyv_tracking")&&"https:"==window.location.protocol){var b=new nj(a);if("https"==b.A)return a;oj(b,"https");return b.toString()}return a}
;var HD="",ID="",JD=!1;function KD(a,b){a||(a="");b||(b=!1);var c=G("watch-channel-brand-div");c&&D(c,"collapsible",b);if(c=G("google_companion_ad_div"))c.innerHTML=a}
function LD(a){var b=Math.round(1E4*Math.random());return['<iframe src="',a,'" name="ifr_300x250ad',b,'" id="ifr_300x250ad',b,'" width="300" height="250" marginwidth="0" marginheight="0" hspace="0" vspace="0" frameborder="0" scrolling="no"></iframe>'].join("")}
function MD(a){return!!a.match("/ad_companion.*render=video_wall_companion")}
function ND(a,b){var c=250;"video"==a&&(c=60);var d=decodeURIComponent(b);KD(['<iframe name="fw_ad" id="fw_ad" ','width="300" height="'+c+'" ','marginwidth="0" marginheight="0" hspace="0" vspace="0" frameborder="0" scrolling="no"></iframe>'].join(""));var e=G("fw_ad");if(e){var e=e.contentWindow?e.contentWindow:e.contentDocument&&e.contentDocument.document?e.contentDocument.document:e.contentDocument,f=navigator.userAgent.toLowerCase(),c=-1!=f.indexOf("msie"),f=-1!=f.indexOf("opera");e.document.open();
e.document.write(d);c||f?L(function(){e.document.close()},7500):e.document.close()}}
function OD(){window.google_ad_output="html";window.google_ad_height="250";window.google_ad_format="300x250_as";window.google_container_id="google_companion_ad_div"}
function PD(){var a=document.getElementById("google_companion_ad_div");if(a&&(a=a.firstElementChild)){"DIV"==a.nodeName&&(a=a.firstElementChild);try{a.contentWindow.postMessage(encodeURIComponent(HD)+","+encodeURIComponent(ID),"*"),a.width=370,a.height=null!=HD?210:185}catch(b){}}}
function QD(){var a=document.getElementById("movie_player");a&&a.pauseVideo()}
function RD(a){if(!JD&&a){var b=r("google.ads.Ad");b?(JD=!0,new b(a.afv_vars.google_ad_client,"google_companion_ad_div",a.afc_vars)):L(function(){RD(a)},200)}}
var SD=[];p("yt.www.watch.ads.setAdId",function(a){window.ad_debug=a},void 0);
p("yt.www.watch.ads.setCompanion",KD,void 0);p("yt.www.watch.ads.showForcedMpu",function(a){var b=LD(a);a=MD(a);KD(b,a)},void 0);
p("yt.www.watch.ads.setGutSlotSizes",function(){},void 0);
p("yt.www.watch.ads.handleSetCompanion",function(a){a=a.replace(";dc_seed=",";kmyd=watch-channel-brand-div;dc_seed=");var b=LD(a);a=MD(a);KD(b,a)},void 0);
p("yt.www.watch.ads.handleSetCompanionForFreewheel",ND,void 0);p("yt.www.watch.ads.handleSetAfvCompanionVars",OD,void 0);p("yt.www.watch.ads.companionSetupComplete",PD,void 0);p("yt.www.watch.ads.pauseVideo",QD,void 0);p("yt.www.watch.ads.loadAfc",RD,void 0);p("yt.www.watch.ads.isAfcAdVisible",function(){var a=G("google_companion_ad_div");return a?(a=Ld(a))&&"IFRAME"==a.tagName?-1!=a.src.indexOf("youtube.com%2Fad_frame"):!1:!1},void 0);
p("yt.www.watch.ads.checkInit",function(){},void 0);
p("yt.www.watch.ads.initVideoWallCompanionListeners",function(a,b){HD=a;ID=b;window.addEventListener("message",function(a){"companion-setup-complete"==a.data?PD():"pause-video"==a.data&&QD()},!1)},void 0);
p("yt.www.watch.ads.getGlobals",function(){return SD},void 0);
p("yt.www.ads.pyv.pyvWatchAfcWithPpvCallback",function(a){DD(a);0<a.length&&FD()},void 0);
p("yt.www.ads.pyv.pyvWatchAfcCallback",DD,void 0);p("yt.www.ads.pyv.pyvHomeAfcCallback",function(a){var b="feed",c="feed-pyv-container";G("feed-pyv-container")&&(b="feed",c="feed-pyv-container");var d=G(c);d&&0!=a.length||"feed"!=b?BD(b,a[0],d,function(){x(xD,function(a){if(a=document.getElementById(a))a.setAttribute("href",yD),N(a,"click",GD)});
R("yt-dom-content-change",d);x(AD,function(a){Xj(a,void 0,ak(a))})}):Id(d)},void 0);
p("yt.www.ads.pyv.showPpvOnWatch",ED,void 0);
p("yt.www.ads.pyv.loadPyvIframe",function(a){if(null!=document){var b=window.location.href;b.indexOf("#")==b.length-1&&Rc&&(window.location.hash="#!");var b=document.body,c=Hc(a),d=kc({display:"none"});a=md(b);c=Cc("html",{},Dc(Cc("head",{},c),Cc("body",{},void 0)));c=Dc(Fc,c);d=d?hc(d):"";a=a.Ff("iframe",{frameborder:0,style:"border:0;vertical-align:bottom;"+d,src:uD});b.appendChild(a);b=c;a=a.contentDocument||a.contentWindow.document;a.open();a.write(yc(b));a.close()}},void 0);
p("yt.www.watch.ads.restrictioncookie.spr",function(a){Xj(a+"mac_204?action_fcts=1");return!0},void 0);
p("setFreewheelCompanion",ND,void 0);p("setAfvCompanionVars",OD,void 0);window.onload=function(){cC()};
window.onunload=function(){K("TIMING_REPORT_ON_UNLOAD")&&nl(!0);r("yt.timing.pingSent_")||(ll("aa"),vl(void 0).ap=1,vl(void 0).yt_fss="u",tl());xl("u");for(var a=fC,b=0,c=a.length;b<c;b++){var d=a[b];if(!isNaN(d)){var e=r("yt.scheduler.instance.cancelJob");e?e(d):M(d)}}fC.length=0;(a=K("PAGE_NAME",void 0))&&sg("dispose-"+a);sg("dispose");sg("pageunload")};
window.onerror=function(a,b,c,d,e){for(var f=document.getElementsByTagName("script"),h=!1,k=0,l=f.length;k<l;k++)if(0<f[k].src.indexOf("/debug-")){h=!0;break}h&&(e||(e=Error(),e.message=a,e.fileName=b,e.lineNumber=c,isNaN(d)||(e.columnNumber=d)),yf(e))};
window.yt=window.yt||{};p("_gel",G,void 0);p("_hasclass",y,void 0);p("_addclass",A,void 0);p("_removeclass",B,void 0);p("_toggleclass",Cb,void 0);p("_showdiv",S,void 0);p("_hidediv",T,void 0);p("_ajax",cj,void 0);p("yt.style.show",S,void 0);p("yt.style.hide",T,void 0);p("goog.bind",u,void 0);p("goog.dom.getElementByClass",H,void 0);p("goog.dom.getElementsByTagNameAndClass",function(a,b,c){return rd(a,b,c)},void 0);
p("goog.dom.getFirstElementChild",Ld,void 0);p("goog.array.forEach",x,void 0);p("goog.array.indexOf",Wa,void 0);p("goog.array.contains",fb,void 0);p("yt.dom.hasAncestor",He,void 0);p("yt.setConfig",tf,void 0);p("yt.getConfig",K,void 0);
p("yt.logging.errors.log",function(a,b,c,d){if(a&&window&&window.yterr&&!(5<=mj)){var e=a.stacktrace,f=a.columnNumber;var h=r("window.location.href");if(t(a))a={message:a,name:"Unknown error",lineNumber:"Not available",fileName:h,stack:"Not available"};else{var k,l,q=!1;try{k=a.lineNumber||a.line||"Not available"}catch(z){k="Not available",q=!0}try{l=a.fileName||a.filename||a.sourceURL||m.$googDebugFname||h}catch(z){l="Not available",q=!0}a=!q&&a.lineNumber&&a.fileName&&a.stack&&a.message&&a.name?
a:{message:a.message||"Not available",name:a.name||"UnknownError",lineNumber:k,fileName:l,stack:a.stack||"Not available"}}e=e||a.stack;d=d||K("INNERTUBE_CONTEXT_CLIENT_VERSION",void 0);k=a.lineNumber.toString();isNaN(k)||isNaN(f)||(k=k+":"+f);lj[a.message]||0<=e.indexOf("/YouTubeCenter.js")||0<=e.indexOf("/mytube.js")||(b={Z:{a:"logerror",t:"jserror",type:a.name,msg:a.message.substr(0,1E3),line:k,level:b||"ERROR"},S:{url:K("PAGE_NAME",window.location.href),file:a.fileName,"client.name":c||"WEB"},
method:"POST"},e&&(b.S.stack=e),d&&(b.S["client.version"]=d),U("/error_204",b),lj[a.message]=!0,mj++)}},void 0);
p("yt.setTimeout",L,void 0);p("yt.setInterval",wf,void 0);p("yt.clearTimeout",M,void 0);p("yt.clearInterval",xf,void 0);p("yt.setMsg",function(a){uf(sf,arguments)},void 0);
p("yt.setGoogMsg",function(a){uf(sf,arguments)},void 0);
p("yt.getMsg",zf,void 0);p("yt.events.listen",N,void 0);p("yt.events.unlisten",Lf,void 0);p("yt.events.stopPropagation",Of,void 0);p("yt.events.preventDefault",Pf,void 0);p("yt.events.getTarget",Nf,void 0);p("yt.events.clear",function(){for(var a in Gf)O(a)},void 0);
p("yt.events.Event",Df,void 0);Df.prototype.preventDefault=Df.prototype.preventDefault;Df.prototype.stopPropagation=Df.prototype.stopPropagation;p("yt.pubsub.subscribe",Q,void 0);p("yt.pubsub.unsubscribeByKey",rg,void 0);p("yt.pubsub.publish",R,void 0);p("yt.pubsub2.publish",Vk,void 0);
Q("init",function(){$g.push(N(window,"mousemove",Og));wf(Qg,25);$g.push(N(window,"resize",Ug));$g.push(N(window,"scroll",Xg));A(document.body,"page-loaded");Dg.getInstance();var a=1<window.devicePixelRatio;if(Ig(0,119)!=a){var b="f"+(Math.floor(119/31)+1),c=Hg(b)||0,c=a?c|67108864:c&-67108865;0==c?delete Eg[b]:(a=c.toString(16),Eg[b]=a.toString());Jg()}});
Q("dispose",wm);Q("init",wm);uu("keyboard");p("yt.uix.FormInput.selectOnChangeActionIE",function(a){pn.getInstance().Za(a)},void 0);
Q("init",function(){rn()});
p("goog.i18n.bidi.setDirAttribute",function(a,b){var c=b.value,d="";Wb.test(c)?d="rtl":Wb.test(c)||(d="ltr");b.dir=d},void 0);
p("yt.style.toggle",Jh,void 0);p("yt.style.setDisplayed",Gh,void 0);p("yt.style.isDisplayed",Hh,void 0);p("yt.style.setVisible",function(a,b){if(a=G(a))a.style.visibility=b?"visible":"hidden"},void 0);
p("yt.net.ajax.sendWithOptionsFromUncompiled",function(a,b){return U(a,{format:b.format,method:b.method,postBody:b.postBody,R:b.onSuccess})},void 0);
p("yt.net.ajax.ResponseFormat.JSON","JSON",void 0);p("yt.net.ajax.ResponseFormat.RAW","RAW",void 0);p("yt.net.ajax.ResponseFormat.LEGACY_XML","XML",void 0);p("yt.net.ajax.getRootNode",hj,void 0);p("yt.net.ajax.getNodeValue",kj,void 0);p("yt.net.scriptloader.load",bk,void 0);p("yt.net.styleloader.load",ik,void 0);p("goog.dom.forms.getFormDataString",se,void 0);p("yt.uri.buildQueryData",Wh,void 0);p("yt.uri.appendQueryData",Yh,void 0);p("yt.www.feedback.init",WB,void 0);
p("yt.www.feedback.start",function(a,b){try{var c=(a||"59")+"",d=$B(b),e=aC();RB("gfeedback_for_signed_out_users_enabled")?(e.productId=c,e.locale=d.locale,e.helpCenterPath=d.helpCenterPath,JB(e,d.productData)):HB(c,d).l(e);return!1}catch(f){return!0}},void 0);
p("yt.www.feedback.startHelp",ZB,void 0);p("yt.www.feedback.displayLink",bC,void 0);Q("init",WB);Q("init",bC);Q("dispose",function(){rg(UB);O(SB);UB.length=0;SB.length=0;TB={}});
p("yt.net.cookies.set",Ag,void 0);p("yt.net.cookies.get",Bg,void 0);p("yt.net.cookies.remove",Cg,void 0);p("yt.window.redirect",xk,void 0);
p("yt.window.popup",function(a,b){var c=b||{};c.target=c.target||"YouTube";c.width=c.width||"600";c.height=c.height||"600";var d=c;d||(d={});var c=window,e;e=a instanceof mc?a:qc("undefined"!=typeof a.href?a.href:String(a));var f=d.target||a.target,h=[],k;for(k in d)switch(k){case "width":case "height":case "top":case "left":h.push(k+"="+d[k]);break;case "target":case "noreferrer":break;default:h.push(k+"="+(d[k]?1:0))}k=h.join(",");(Mc()||E("iPad")||E("iPod"))&&c.navigator&&c.navigator.standalone&&
f&&"_self"!=f?(k=c.document.createElement("A"),jd(k,e),k.setAttribute("target",f),d.noreferrer&&k.setAttribute("rel","noreferrer"),e=document.createEvent("MouseEvent"),e.initMouseEvent("click",!0,!0,c,1),k.dispatchEvent(e),c={}):d.noreferrer?(c=c.open("",f,k),e=oc(e),c&&(Pc&&-1!=e.indexOf(";")&&(e="'"+e.replace(/'/g,"%27")+"'"),c.opener=null,d=ec("b/12014412, meta tag with sanitized URL"),e=Li(d,'<META HTTP-EQUIV="refresh" content="0; url='+Ba(e)+'">'),c.document.write(yc(e)),c.document.close())):
c=c.open(oc(e),f,k);if(!c)return null;c.opener||(c.opener=window);c.focus();return c},void 0);
p("yt.window.navigate",yk,void 0);Q("init",function(){xo.getInstance().cd();Kq.getInstance().cd()});
Q("init",function(){var a=!!H("guide-module-loading");window.spf&&spf.load&&a&&(K("GUIDE_DELAY_LOAD")||Bu(),Cu.push(Q("appbar-show-guide",Bu)))});
xp(Mm);xp(cn);xp(ln);xp(mn);xp(nn);xp(pn);xp(tn);xp(xn);xp(Cn);xp(Jn);xp(ho);xp(no);xp(oo);xp(po);xp(so);xp(Kq);xp(xo);xp(mp);xp(sp);xp(tp);xp(vp);xp(dp);p("yt.flash.embed",ti,void 0);p("yt.www.watch.player.seekTo",function(){},void 0);
p("yt.www.watch.player.saveResumeOffset",function(a){var b;(b=K("PAGE_NAME"))&&"watch"!=b?(yf(Error("getMoviePlayer called on "+b),"WARNING"),b=null):b=Cp();if(b&&b.isReady()){a&&tf("RESUME_COOKIE_NAME",a);a=K("VIDEO_ID",void 0);var c=b.getDuration();b=Math.floor(b.getCurrentTime());0==c||120>=b||b+120>=c?wC(a):vC(a,Math.floor(b))}},void 0);
p("yt.history.enable",Hi,void 0);p("yt.history.disable",function(){var a=Ii();a.setEnabled.call(a,!1)},void 0);
p("yt.www.lists.data.addto.saveToWatchLater",Ou,void 0);p("yt.www.lists.addtowatchlater.init",Tu,void 0);Q("init",Tu);Q("dispose",function(){Gm("addto-watch-later-button","click",Uu);Gm("addto-watch-later-button-success","click",Vu);Gm("addto-watch-later-button-remove","click",Wu);Gm("addto-watch-later-button-sign-in","click",Xu);O(Ru);Ru=[]});
p("yt.www.watchqueue.addtowatchqueue.init",xC,void 0);Q("init",xC);Q("dispose",function(){rg(BC);BC.length=0;JC=[];Gm("addto-watch-queue-button","click",yC);Gm("addto-tv-queue-button","click",yC);Gm("addto-watch-queue-button-success","click",zC);Gm("addto-watch-queue-menu-choice","click",AC);gg(iC);iC=null;rg(uC);uC.length=0;nB()});
p("yt.www.comments.init",function(a){if(!G("comment-section-renderer"))if(G("yt-comments-list"))new fu;else{var b;b=K("DISTILLER_CONFIG");var c=G("comments-test-iframe");if(b&&c){var d="signin_url"in b,c=Ts(ra(Wt,b),"comments",d?":socialhost:/:im_prefix::session_prefix:_/widget/render/comments?usegapi=1":":socialhost:/:im_prefix::session_prefix:wm/4/_/widget/render/comments?usegapi=1");d&&(c.config["googleapis.config"].signedIn=!1);(b=b.host_override)&&(c.config.iframes[":socialhost:"]=b);500>window.location.search.length+
window.location.hash.length&&(c.config.iframes.comments.params={location:["search","hash"]});Ss("comments:iframes",c);b=!0}else b=!1;if(!b){new lt(G("watch-discussion"));Ct.push(P(document.body,"click",ot,"comment-action"));if(b=G("comments-textarea"))b.disabled=!1,b.id="";K("COMMENT_OPEN_REPLY_BOX")&&(b=qd("comment",G("linked-comments-header")),ut(b[b.length-1]));ct.init();Dt.push(Q("comment-submit-success",Bt));a&&R("yt-dom-content-change",G("comments-view"))}}},void 0);
p("yt.dom.datasets.get",J,void 0);p("yt.dom.datasets.set",ve,void 0);
var TD=Q("init",function(){hh||(bh={},ch={},ah={},dh={},fh=[],eh=jh(),fh.push(Q("page-resize",ih)),fh.push(Q("page-scroll",lh)),fh.push(Q("yt-dom-content-change",kh)),hh=!0);r("yt.dom.VisibilityMonitor.delegateByClass")(null,r("yt.dom.VisibilityMonitor.States.VISIBLE"),sD,"yt-thumb");r("yt.dom.VisibilityMonitor.delegateByClass")(null,r("yt.dom.VisibilityMonitor.States.VISIBLE"),sD,"yt-uix-simple-thumb-wrap");r("yt.dom.VisibilityMonitor.refresh")();ll("tdl");rg(TD)});
Q("init",function(){for(var a=0;a<document.forms.length;a++){for(var b=!1,c=0;c<tD.length;c++)document.forms[a].name==tD[c]&&(b=!0);c=document.forms[a];if("post"==c.method.toLowerCase()&&0==b){for(var b=!1,d=0;d<c.elements.length;d++)c.elements[d].name==K("XSRF_FIELD_NAME")&&(b=!0);b||(b=void 0,b=K("XSRF_TOKEN"),d=document.createElement("input"),d.setAttribute("name",K("XSRF_FIELD_NAME")),d.setAttribute("type","hidden"),d.setAttribute("value",b),c.appendChild(d))}}});
p("yt.www.ads.MastheadAd",Hq,void 0);Hq.prototype.autoCollapsePremiumYva=Hq.prototype.pf;Hq.prototype.collapse_ad=Hq.prototype.bf;Hq.prototype.expand_ad=Hq.prototype.ci;Hq.prototype.userCollapsePremiumYva=Hq.prototype.Ne;Hq.prototype.userExpandPremiumYva=Hq.prototype.Jh;Hq.prototype.userUnexpandPremiumYva=Hq.prototype.Kh;p("yt.www.feed.ui.ads.workaroundIE",function(a){!pu&&ou&&(pu=!0,L(function(){a.focus()},0))},void 0);
p("yt.www.feed.ui.ads.workaroundLoad",function(){ou=!0},void 0);
p("yt.www.feed.ui.ads.writeAdsContentToIframe",function(a,b){var c=G(a);c&&(c=c.contentDocument||c.contentWindow.document,c.open(),c.write("<!DOCTYPE html><html><head></head><body>"+b+"</body></html>"),F||c.close())},void 0);
p("yt.net.ping.send",Xj,void 0);p("yt.tracking.doubleclick.trackActivity",function(a,b,c){a=("https:"==document.location.protocol?"https://":"http://")+"fls.doubleclick.net/activityi;src="+za(K("DBLCLK_ADVERTISER_ID"))+";type="+za(a)+";cat="+za(b);c&&!c.ord&&(a+=";ord=1");for(var d in c)a+=";"+za(d)+"="+za(c[d]);a+=";num="+v();c=document.createElement("iframe");c.src=a;c.style.display="none";document.body.appendChild(c)},void 0);
p("yt.tracking.track",function(a,b,c){zm(a,b,c)},void 0);
p("yt.tracking.resolution",function(){var a="CSS1Compat"==document.compatMode?document.documentElement:document.body,a=Wh({a:"resolution",width:screen.width,height:screen.height,depth:screen.colorDepth,pixel_ratio:window.devicePixelRatio||1,win_width:a.clientWidth,win_height:a.clientHeight});Xj("/gen_204?"+a,void 0)},void 0);
p("yt.tracking.share",function(a,b,c,d,e,f){var h={};b&&(h.v=b);c&&(h.list=c);d&&(h.url=d);a={name:a,locale:e,feature:f};for(var k in h)a[k]=h[k];h=Yh("/sharing_services",a);Xj(h)},void 0);
p("yt.timing.send",tl,void 0);p("yt.www.subscription.autoaction.continueAction",function(a,b,c){Vk(Ro,new Ko(a,{itemType:b,itemId:c}))},void 0);
Q("init",function(a){Av=!!a;Cv.push(Xk(Ro,Dv),Xk(Wo,Fv));Av||(Cv.push(Xk(Vo,Dv),Xk($o,Fv),Xk(No,Hv),Xk(Oo,Jv),Xk(Po,Lv)),Bv.push(Q("subscription-prefs",Nv)),Cv.push(Xk(wv,Ov),Xk(yv,Qv),Xk(vv,Pv)))});
Q("init",function(){dv.push(Xk(Qo,iv))});
Q("dispose",function(){rg(cv);cv.length=0;Yk(dv);dv.length=0;O(ev);ev.length=0;bv&&(Id(bv),bv=null)});
Q("init",function(){K("CREATE_CHANNEL_LIGHTBOX")&&Mp();K("FEED_PRIVACY_LIGHTBOX_ENABLED")&&(nq.push(Q("SHOW-FEED-PRIVACY-FAVORITE-DIALOG",uq)),nq.push(Q("SHOW-FEED-PRIVACY-LIKE-DIALOG",tq)),nq.push(Q("SHOW-FEED-PRIVACY-ADD-TO-PLAYLIST-DIALOG",vq)),nq.push(Q("SHOW-FEED-PRIVACY-LIKE-PLAYLIST-DIALOG",wq)),nq.push(Q("SHOW-FEED-PRIVACY-SUBSCRIBE-DIALOG",sq)));if(K("SHOW_IDENTITY_PROMPT_LIGHTBOX")){var a;a=Jp(xq);var b=K("IDENTITY_PROMPT_NEXT_URL",document.location.href),c=K("IDENTITY_PROMPT_AUTHUSER",
void 0),d=K("IDENTITY_PROMPT_PAGEID",void 0),e={};c&&(e.authuser=c);d&&(e.pageid=d);a.open("identity-prompt","/identity_prompt_ajax","identity_prompt_ajax",!0,!0,void 0,b,!0,e)}(K("SHOW_LINK_GPLUS_LIGHTBOX")||K("LINK_GPLUS_LIGHTBOX_ENABLED"))&&Wp();K("SHOW_MCNA_YPE_MODAL")&&new Gq("")});
Q("dispose",function(){if(K("SHOW_LINK_GPLUS_LIGHTBOX")||K("LINK_GPLUS_LIGHTBOX_ENABLED"))rg(Vp),Vp.length=0,O(Sp),Sp.length=0,Op=!1});
p("yt.www.account.AddNewChannelLoader.init",function(){var a=Jp(Kp);a.B=K("ADD_NEW_CHANNEL_PAGE_ID",void 0)||null;a.init(K("ADD_NEW_CHANNEL_CSS_URL",void 0),K("ADD_NEW_CHANNEL_JS_URL",void 0),!a.B,G("body-container"),"add-secondary-channel")},void 0);
p("yt.www.account.CreateChannelLoader.show",function(a){Mp(!0,a)},void 0);
p("yt.www.account.LinkGplusLoader.cancel",$p,void 0);p("yt.www.account.LinkGplusLoader.dismiss",Zp,void 0);p("yt.www.account.LinkGplusLoader.show",iq,void 0);p("yt.www.account.LinkGplusLoader.showOnce",function(){iq();rg(K("PUBSUB_INIT_KEY",void 0))},void 0);
Q("init",nD);p("yt.www.ypc.bootstrap.init",nD,void 0);p("yt.www.user.unblockUserLinkByExternalId",function(a,b){if(!confirm(zf("UNBLOCK_USER")))return!1;U("/link_ajax?action_unblock_user=1",{format:"XML",method:"POST",postBody:K("BLOCK_USER_AJAX_XSRF")+"&uid="+a,R:function(){b&&window.location.reload()}});
return!0},void 0);
p("yt.www.user.blockUserLinkByExternalId",function(a,b){if(!confirm(zf("BLOCK_USER")))return!1;U("/link_ajax?action_block_user=1",{format:"XML",method:"POST",postBody:K("BLOCK_USER_AJAX_XSRF")+"&uid="+a,R:function(){b&&window.location.reload()}});
return!0},void 0);
var UD=Q("init",function(){K("PAGEFRAME_JS")&&bk(K("PAGEFRAME_JS",void 0),function(){r("ytbin.www.pageframe.setup")()});
rg(UD)});
Q("init",function(){Mu.getInstance().init()});})();