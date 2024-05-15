/*
 * XenForo media_view_navigation.min.js
 * Copyright 2010-2019 XenForo Ltd.
 * Released under the XenForo License Agreement: http://xenforo.com/license-agreement
 */
!function(a,b,f,g){XenForo.XenGalleryMediaViewNextPrev=function(f){var c=a("a.PreviousMedia")[0],d=a("a.NextMedia")[0];a(b).keydown(function(e){if(a(".mfp-ready").length)return!1;a("textarea, input").is(":focus")||(37===e.which?c&&(b.location.href=c.href):39===e.which&&d&&(b.location.href=d.href))})};XenForo.register(".buttonToolbar","XenForo.XenGalleryMediaViewNextPrev")}(jQuery,this,document);
