/*
 * XenForo rating.min.js
 * Copyright 2010-2019 XenForo Ltd.
 * Released under the XenForo License Agreement: http://xenforo.com/license-agreement
 */
var $jscomp=$jscomp||{};$jscomp.scope={};$jscomp.findInternal=function(a,c,b){a instanceof String&&(a=String(a));for(var e=a.length,d=0;d<e;d++){var f=a[d];if(c.call(b,f,d,a))return{i:d,v:f}}return{i:-1,v:void 0}};$jscomp.ASSUME_ES5=!1;$jscomp.ASSUME_NO_NATIVE_MAP=!1;$jscomp.ASSUME_NO_NATIVE_SET=!1;$jscomp.defineProperty=$jscomp.ASSUME_ES5||"function"==typeof Object.defineProperties?Object.defineProperty:function(a,c,b){a!=Array.prototype&&a!=Object.prototype&&(a[c]=b.value)};
$jscomp.getGlobal=function(a){return"undefined"!=typeof window&&window===a?a:"undefined"!=typeof global&&null!=global?global:a};$jscomp.global=$jscomp.getGlobal(this);$jscomp.polyfill=function(a,c,b,e){if(c){b=$jscomp.global;a=a.split(".");for(e=0;e<a.length-1;e++){var d=a[e];d in b||(b[d]={});b=b[d]}a=a[a.length-1];e=b[a];c=c(e);c!=e&&null!=c&&$jscomp.defineProperty(b,a,{configurable:!0,writable:!0,value:c})}};
$jscomp.polyfill("Array.prototype.find",function(a){return a?a:function(a,b){return $jscomp.findInternal(this,a,b).v}},"es6","es3");
!function(a,c,b,e){XenForo.RatingWidget=function(d){var b=null,c=null,e=d.find(".Hint").each(function(){var b=a(this);b.data("text",b.text())}),g=d.find(".RatingValue .Number"),h=d.find("button").each(function(){var b=a(this);b.data("hint",b.attr("title")).removeAttr("title")}),k=function(b){h.each(function(c){a(this).toggleClass("Full",b>=c+1).toggleClass("Half",b>=c+.5&&b<c+1)})},l=function(){k(g.text());e.text(e.data("text"))};h.bind({mouseenter:function(b){b.preventDefault();k(a(this).val());
e.text(a(this).data("hint"))},click:function(f){f.preventDefault();c?c.load():b=XenForo.ajax(d.attr("action"),{rating:a(this).val()},function(a,d){XenForo.hasResponseError(a)||(a._redirectMessage&&XenForo.alert(a._redirectMessage,"",1E3),a.newRating&&g.text(a.newRating),a.hintText&&e.data("text",a.hintText),a.templateHtml&&new XenForo.ExtLoader(a,function(){c=XenForo.createOverlay(null,a.templateHtml,{title:a.h1||a.title}).load();c.getOverlay().find(".OverlayCloser").click(function(){c=null})}));
l();b=null})}});d.mouseleave(function(a){null===b&&l()})};XenForo.register("form.RatingWidget","XenForo.RatingWidget")}(jQuery,this,document);
