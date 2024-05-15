/*
 * XenForo album_permissions.min.js
 * Copyright 2010-2019 XenForo Ltd.
 * Released under the XenForo License Agreement: http://xenforo.com/license-agreement
 */
!function(b,c,d,e){XenForo.XenGalleryPermissionsList=function(a){this.__construct(a)};XenForo.XenGalleryPermissionsList.prototype={__construct:function(a){this.$select=a;this.$className=".CustomUsers"+this.$select.data("type");this.$select.bind({change:b.context(this,"permissionSet")})},permissionSet:function(a){if("shared"==this.$select.val())return b(this.$className).xfSlideDown();b(this.$className).xfSlideUp()}};XenForo.register(".PermissionsList","XenForo.XenGalleryPermissionsList")}(jQuery,this,
document);
