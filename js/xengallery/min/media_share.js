/*
 * XenForo media_share.min.js
 * Copyright 2010-2019 XenForo Ltd.
 * Released under the XenForo License Agreement: http://xenforo.com/license-agreement
 */
!function(c,d,e,f){XenForo.XenGalleryMediaShare=function(b){this.__construct(b)};XenForo.XenGalleryMediaShare.prototype={__construct:function(b){b.on("keydown",function(a){if(67!=a.keyCode&&91!==a.keyCode)return a.preventDefault(),a.stopPropagation(),!1});b.on("cut",function(a){a.preventDefault();a.stopPropagation();return!1});b.on("paste",function(a){a.preventDefault();a.stopPropagation();return!1});b.on("click",function(a){this.select()})}};XenForo.register(".CopyInput","XenForo.XenGalleryMediaShare")}(jQuery,
this,document);
