/*
 * XenForo media_user_tag.min.js
 * Copyright 2010-2019 XenForo Ltd.
 * Released under the XenForo License Agreement: http://xenforo.com/license-agreement
 */
var $jscomp=$jscomp||{};$jscomp.scope={};$jscomp.findInternal=function(a,k,g){a instanceof String&&(a=String(a));for(var e=a.length,b=0;b<e;b++){var n=a[b];if(k.call(g,n,b,a))return{i:b,v:n}}return{i:-1,v:void 0}};$jscomp.ASSUME_ES5=!1;$jscomp.ASSUME_NO_NATIVE_MAP=!1;$jscomp.ASSUME_NO_NATIVE_SET=!1;$jscomp.defineProperty=$jscomp.ASSUME_ES5||"function"==typeof Object.defineProperties?Object.defineProperty:function(a,k,g){a!=Array.prototype&&a!=Object.prototype&&(a[k]=g.value)};
$jscomp.getGlobal=function(a){return"undefined"!=typeof window&&window===a?a:"undefined"!=typeof global&&null!=global?global:a};$jscomp.global=$jscomp.getGlobal(this);$jscomp.polyfill=function(a,k,g,e){if(k){g=$jscomp.global;a=a.split(".");for(e=0;e<a.length-1;e++){var b=a[e];b in g||(g[b]={});g=g[b]}a=a[a.length-1];e=g[a];k=k(e);k!=e&&null!=k&&$jscomp.defineProperty(g,a,{configurable:!0,writable:!0,value:k})}};
$jscomp.polyfill("Array.prototype.find",function(a){return a?a:function(a,g){return $jscomp.findInternal(this,a,g).v}},"es6","es3");$jscomp.SYMBOL_PREFIX="jscomp_symbol_";$jscomp.initSymbol=function(){$jscomp.initSymbol=function(){};$jscomp.global.Symbol||($jscomp.global.Symbol=$jscomp.Symbol)};$jscomp.Symbol=function(){var a=0;return function(k){return $jscomp.SYMBOL_PREFIX+(k||"")+a++}}();
$jscomp.initSymbolIterator=function(){$jscomp.initSymbol();var a=$jscomp.global.Symbol.iterator;a||(a=$jscomp.global.Symbol.iterator=$jscomp.global.Symbol("iterator"));"function"!=typeof Array.prototype[a]&&$jscomp.defineProperty(Array.prototype,a,{configurable:!0,writable:!0,value:function(){return $jscomp.arrayIterator(this)}});$jscomp.initSymbolIterator=function(){}};
$jscomp.initSymbolAsyncIterator=function(){$jscomp.initSymbol();var a=$jscomp.global.Symbol.asyncIterator;a||(a=$jscomp.global.Symbol.asyncIterator=$jscomp.global.Symbol("asyncIterator"));$jscomp.initSymbolAsyncIterator=function(){}};$jscomp.arrayIterator=function(a){var k=0;return $jscomp.iteratorPrototype(function(){return k<a.length?{done:!1,value:a[k++]}:{done:!0}})};
$jscomp.iteratorPrototype=function(a){$jscomp.initSymbolIterator();a={next:a};a[$jscomp.global.Symbol.iterator]=function(){return this};return a};$jscomp.iteratorFromArray=function(a,k){$jscomp.initSymbolIterator();a instanceof String&&(a+="");var g=0,e={next:function(){if(g<a.length){var b=g++;return{value:k(b,a[b]),done:!1}}e.next=function(){return{done:!0,value:void 0}};return e.next()}};e[Symbol.iterator]=function(){return e};return e};
$jscomp.polyfill("Array.prototype.keys",function(a){return a?a:function(){return $jscomp.iteratorFromArray(this,function(a){return a})}},"es6","es3");
!function(a,k,g,e){XenForo.XenGalleryImageData=function(a){this.__construct(a)};XenForo.XenGalleryImageData.prototype={__construct:function(b){this.image=b;var e=this;a(g).ready(function(){setTimeout(function(){e.updateMultiplier()},1500)});a(k).bind({resize:a.context(this,"updateMultiplier")});this.cropButton=a(".CropActivate");this.cropButton.bind({click:a.context(this,"updateMultiplier")});this.tagButton=a(".TagActivate");this.tagButton.bind({click:a.context(this,"updateMultiplier")});this.showTagLink=
a(".ShowTags");this.showTagLink.bind({click:a.context(this,"showTags")});a("#mediaTaggedUsers li .TaggedUser").hover(function(){a(".mediaContainer").find(".tagBox."+a(this).attr("id")).addClass("hover");a(".mediaContainer").find(".tagBox."+a(this).attr("id")).xfFadeIn(XenForo.speed.fast).addClass("selected")},function(){a(".mediaContainer").find(".tagBox."+a(this).attr("id")).removeClass("hover");a(".mediaContainer").find(".tagBox."+a(this).attr("id")).xfFadeOut(XenForo.speed.fast).addClass("selected")});
a(".mediaContainer").hover(function(){a(this).find(".tagBox").xfFadeIn(XenForo.speed.fast)},function(){a(this).find(".tagBox").xfFadeOut(XenForo.speed.fast)})},showTags:function(){a(".TagBox").toggleClass("tagsShown")},updateMultiplier:function(){a(this.image).attr("data-scalewidth",this.image.width());a(this.image).attr("data-scaleheight",this.image.height());var b=a(this.image).attr("data-scalewidth")/a(this.image).attr("data-realwidth");a("#CropMultiplier").val(b);a("#TagMultiplier").val(b);a(".TagBox").each(function(e,
k){tagBox=a(k);tagBox.css({width:Math.round(tagBox.data("tagwidth")*b),height:Math.round(tagBox.data("tagheight")*b),top:Math.round(tagBox.data("tagtop")*b),left:Math.round(tagBox.data("tagleft")*b)});tagBoxInner=a(".TagBoxInner_"+tagBox.data("tagid"));tagBoxInner.css({width:Math.round(tagBox.data("tagwidth")*b)-6,height:Math.round(tagBox.data("tagheight")*b)-6});tagBoxName=a(".TagBoxName_"+tagBox.data("tagid"));tagBoxName.css({width:Math.round(tagBox.data("tagwidth")*b)})});a(".tagContainer").xfShow()}};
XenForo.register(".Image","XenForo.XenGalleryImageData")}(jQuery,this,document);
!function(a,k,g,e){XenForo.XenGalleryCrop=function(a){this.__construct(a)};XenForo.XenGalleryCrop.prototype={__construct:function(b){this.element=b;this.element.bind({click:a.context(this,"beginCrop")})},beginCrop:function(b){b.preventDefault();b=a(b.target);var e=b.closest(".Menu").data("XenForo.PopupMenu");e.hideMenu&&e.hideMenu(b,!0);if(XenForo.isTouchBrowser())return XenForo.alert(XenForo.phrases.xengallery_touch_error);this.element.addClass("primary");XenForo.alert(this.element.data("activatephrase"),
"info",1500,"");a("img.Tag").imgAreaSelect({disable:!0,hide:!0});a(".TagActivate").removeClass("primary");a("img.Crop").imgAreaSelect({disable:!1,handles:!0,aspectRatio:!1,maxWidth:!1,maxHeight:!1,fadeSpeed:200,onSelectEnd:function(b,c){a("#CropX1").val(c.x1);a("#CropY1").val(c.y1);a("#CropX2").val(c.x2);a("#CropY2").val(c.y2);a("#CropWidth").val(c.width);a("#CropHeight").val(c.height);this.queryString={media_id:a("#CropMediaId").val(),crop_x1:a("#CropX1").val(),crop_y1:a("#CropY1").val(),crop_x2:a("#CropX2").val(),
crop_y2:a("#CropY2").val(),crop_width:a("#CropWidth").val(),crop_height:a("#CropHeight").val(),crop_multiplier:a("#CropMultiplier").val()};0<c.width&&0<c.height&&(this.trigger={href:"index.php?xengallery/crop-confirm&"+a.param(this.queryString)},this.options={},this.OverlayLoader=new XenForo.OverlayLoader(a(this.trigger),!1,this.options),this.OverlayLoader.load())}})},endCrop:function(){a(".CropActivate").removeClass("primary");a("img.Crop").imgAreaSelect({disable:!0,hide:!0})}};XenForo.register(".CropActivate",
"XenForo.XenGalleryCrop")}(jQuery,this,document);
!function(a,k,g,e){XenForo.XenGalleryTag=function(a){this.__construct(a)};XenForo.XenGalleryTag.prototype={__construct:function(b){this.element=b;this.element.bind({click:a.context(this,"beginTag")})},beginTag:function(b){b.preventDefault();b=a(b.target);var e=b.closest(".Menu").data("XenForo.PopupMenu");e.hideMenu&&e.hideMenu(b,!0);if(XenForo.isTouchBrowser())return XenForo.alert(XenForo.phrases.xengallery_touch_error);this.element.addClass("primary");XenForo.alert(this.element.data("activatephrase"),
"info",1500,"");a("img.Crop").imgAreaSelect({disable:!0,hide:!0});a(".CropActivate").removeClass("primary");a("img.Tag").imgAreaSelect({disable:!1,handles:!0,aspectRatio:"1:1",maxWidth:200,maxHeight:200,fadeSpeed:200,onSelectEnd:function(b,c){a("#TagX1").val(c.x1);a("#TagY1").val(c.y1);a("#TagX2").val(c.x2);a("#TagY2").val(c.y2);a("#TagWidth").val(c.width);a("#TagHeight").val(c.height);this.queryString={media_id:a("#TagMediaId").val(),tag_x1:a("#TagX1").val(),tag_y1:a("#TagY1").val(),tag_x2:a("#TagX2").val(),
tag_y2:a("#TagY2").val(),tag_width:a("#TagWidth").val(),tag_height:a("#TagHeight").val(),tag_multiplier:a("#TagMultiplier").val()};0<c.width&&0<c.height&&(this.trigger={href:"index.php?xengallery/tag-input&"+a.param(this.queryString)},this.options={},this.OverlayLoader=new XenForo.OverlayLoader(a(this.trigger),!1,this.options),this.OverlayLoader.load())},onSelectStart:function(b,c){a("#InlineTagOverlay").hide()}})},endTag:function(){a(".TagActivate").removeClass("primary");a("img.Tag").imgAreaSelect({disable:!0,
hide:!0})}};XenForo.register(".TagActivate","XenForo.XenGalleryTag")}(jQuery,this,document);
(function(a){function k(){return a("<div/>")}var g=Math.abs,e=Math.max,b=Math.min,n=Math.round;a.imgAreaSelect=function(B,c){function O(a){return a+C.left-x.left}function P(a){return a+C.top-x.top}function J(a){return a-C.left+x.left}function K(a){return a-C.top+x.top}function F(a){var c=a||U;a=a||V;return{x1:n(d.x1*c),y1:n(d.y1*a),x2:n(d.x2*c),y2:n(d.y2*a),width:n(d.x2*c)-n(d.x1*c),height:n(d.y2*a)-n(d.y1*a)}}function ka(a,c,b,e,f){var aa=f||U;f=f||V;d={x1:n(a/aa||0),y1:n(c/f||0),x2:n(b/aa||0),y2:n(e/
f||0)};d.width=d.x2-d.x1;d.height=d.y2-d.y1}function L(){ba&&y.width()&&(C={left:n(y.offset().left),top:n(y.offset().top)},z=y.innerWidth(),w=y.innerHeight(),C.top+=y.outerHeight()-w>>1,C.left+=y.outerWidth()-z>>1,W=n(c.minWidth/U)||0,X=n(c.minHeight/V)||0,la=n(b(c.maxWidth/U||16777216,z)),ma=n(b(c.maxHeight/V||16777216,w)),"1.3.2"!=a().jquery||"fixed"!=ca||na.getBoundingClientRect||(C.top+=e(document.body.scrollTop,na.scrollTop),C.left+=e(document.body.scrollLeft,na.scrollLeft)),x=/absolute|relative/.test(Q.css("position"))?
{left:n(Q.offset().left)-Q.scrollLeft(),top:n(Q.offset().top)-Q.scrollTop()}:"fixed"==ca?{left:a(document).scrollLeft(),top:a(document).scrollTop()}:{left:0,top:0},r=O(0),t=P(0),(d.x2>z||d.y2>w)&&da())}function ea(aa){if(fa){p.css({left:O(d.x1),top:P(d.y1)}).add(R).width(G=d.width).height(M=d.height);R.add(v).add(u).css({left:0,top:0});v.width(e(G-v.outerWidth()+v.innerWidth(),0)).height(e(M-v.outerHeight()+v.innerHeight(),0));a(q[0]).css({left:r,top:t,width:d.x1,height:w});a(q[1]).css({left:r+d.x1,
top:t,width:G,height:d.y1});a(q[2]).css({left:r+d.x2,top:t,width:z-d.x2,height:w});a(q[3]).css({left:r+d.x1,top:t+d.y2,width:G,height:w-d.y2});G-=u.outerWidth();M-=u.outerHeight();switch(u.length){case 8:a(u[4]).css({left:G>>1}),a(u[5]).css({left:G,top:M>>1}),a(u[6]).css({left:G>>1,top:M}),a(u[7]).css({top:M>>1});case 4:u.slice(1,3).css({left:G}),u.slice(2,4).css({top:M})}if(!1!==aa&&(a.imgAreaSelect.onKeyPress!=wa&&a(document).unbind(a.imgAreaSelect.keyPress,a.imgAreaSelect.onKeyPress),c.keys))a(document)[a.imgAreaSelect.keyPress](a.imgAreaSelect.onKeyPress=
wa);S&&2==v.outerWidth()-v.innerWidth()&&(v.css("margin",0),setTimeout(function(){v.css("margin","auto")},0))}}function oa(a){L();ea(a);f=O(d.x1);h=P(d.y1);l=O(d.x2);m=P(d.y2)}function pa(a,b){c.fadeSpeed?a.fadeOut(c.fadeSpeed,b):a.hide()}function N(a){var b=J(a.pageX-x.left)-d.x1;a=K(a.pageY-x.top)-d.y1;qa||(L(),qa=!0,p.one("mouseout",function(){qa=!1}));A="";c.resizable&&(a<=c.resizeMargin?A="n":a>=d.height-c.resizeMargin&&(A="s"),b<=c.resizeMargin?A+="w":b>=d.width-c.resizeMargin&&(A+="e"));p.css("cursor",
A?A+"-resize":c.movable?"move":"");ha&&ha.toggle()}function xa(b){a("body").css("cursor","");(c.autoHide||0==d.width*d.height)&&pa(p.add(q),function(){a(this).hide()});a(document).unbind("mousemove",ra);p.mousemove(N);c.onSelectEnd(B,F())}function ya(b){if(1!=b.which)return!1;L();A?(a("body").css("cursor",A+"-resize"),f=O(d[/w/.test(A)?"x2":"x1"]),h=P(d[/n/.test(A)?"y2":"y1"]),a(document).mousemove(ra).one("mouseup",xa),p.unbind("mousemove",N)):c.movable?(sa=r+d.x1-(b.pageX-x.left),ta=t+d.y1-(b.pageY-
x.top),p.unbind("mousemove",N),a(document).mousemove(za).one("mouseup",function(){c.onSelectEnd(B,F());a(document).unbind("mousemove",za);p.mousemove(N)})):y.mousedown(b);return!1}function Y(a){H&&(a?(l=e(r,b(r+z,f+g(m-h)*H*(l>f||-1))),m=n(e(t,b(t+w,h+g(l-f)/H*(m>h||-1)))),l=n(l)):(m=e(t,b(t+w,h+g(l-f)/H*(m>h||-1))),l=n(e(r,b(r+z,f+g(m-h)*H*(l>f||-1)))),m=n(m)))}function da(){f=b(f,r+z);h=b(h,t+w);g(l-f)<W&&(l=f-W*(l<f||-1),l<r?f=r+W:l>r+z&&(f=r+z-W));g(m-h)<X&&(m=h-X*(m<h||-1),m<t?h=t+X:m>t+w&&(h=
t+w-X));l=e(r,b(l,r+z));m=e(t,b(m,t+w));Y(g(l-f)<g(m-h)*H);g(l-f)>la&&(l=f-la*(l<f||-1),Y());g(m-h)>ma&&(m=h-ma*(m<h||-1),Y(!0));d={x1:J(b(f,l)),x2:J(e(f,l)),y1:K(b(h,m)),y2:K(e(h,m)),width:g(l-f),height:g(m-h)};ea();c.onSelectChange(B,F())}function ra(a){l=/w|e|^$/.test(A)||H?a.pageX-x.left:O(d.x2);m=/n|s|^$/.test(A)||H?a.pageY-x.top:P(d.y2);da();return!1}function Z(b,e){l=(f=b)+d.width;m=(h=e)+d.height;a.extend(d,{x1:J(f),y1:K(h),x2:J(l),y2:K(m)});ea();c.onSelectChange(B,F())}function za(a){f=e(r,
b(sa+(a.pageX-x.left),r+z-d.width));h=e(t,b(ta+(a.pageY-x.top),t+w-d.height));Z(f,h);a.preventDefault();return!1}function ua(){a(document).unbind("mousemove",ua);L();l=f;m=h;da();A="";q.is(":visible")||p.add(q).hide().fadeIn(c.fadeSpeed||0);fa=!0;a(document).unbind("mouseup",ia).mousemove(ra).one("mouseup",xa);p.unbind("mousemove",N);c.onSelectStart(B,F())}function ia(){a(document).unbind("mousemove",ua).unbind("mouseup",ia);pa(p.add(q));ka(J(f),K(h),J(f),K(h));this instanceof a.imgAreaSelect||(c.onSelectChange(B,
F()),c.onSelectEnd(B,F()))}function Aa(c){if(1!=c.which||q.is(":animated"))return!1;L();sa=f=c.pageX-x.left;ta=h=c.pageY-x.top;a(document).mousemove(ua).mouseup(ia);return!1}function Ba(){oa(!1)}function Ca(){ba=!0;va(c=a.extend({classPrefix:"imgareaselect",movable:!0,parent:"body",resizable:!0,resizeMargin:10,onInit:function(){},onSelectStart:function(){},onSelectChange:function(){},onSelectEnd:function(){}},c));p.add(q).css({visibility:""});c.show&&(fa=!0,L(),ea(),p.add(q).hide().fadeIn(c.fadeSpeed||
0));setTimeout(function(){c.onInit(B,F())},0)}function ja(a,b){for(var e in b)void 0!==c[e]&&a.css(b[e],c[e])}function va(b){b.parent&&(Q=a(b.parent)).append(p.add(q));a.extend(c,b);L();if(null!=b.handles){u.remove();u=a([]);for(T=b.handles?"corners"==b.handles?4:8:0;T--;)u=u.add(k());u.addClass(c.classPrefix+"-handle").css({position:"absolute",fontSize:0,zIndex:I+1||1});0<=!parseInt(u.css("width"))&&u.width(5).height(5);(E=c.borderWidth)&&u.css({borderWidth:E,borderStyle:"solid"});ja(u,{borderColor1:"border-color",
borderColor2:"background-color",borderOpacity:"opacity"})}U=c.imageWidth/z||1;V=c.imageHeight/w||1;null!=b.x1&&(ka(b.x1,b.y1,b.x2,b.y2),b.show=!b.hide);b.keys&&(c.keys=a.extend({shift:1,ctrl:"resize"},b.keys));q.addClass(c.classPrefix+"-outer");R.addClass(c.classPrefix+"-selection");for(T=0;4>T++;)a(v[T-1]).addClass(c.classPrefix+"-border"+T);ja(R,{selectionColor:"background-color",selectionOpacity:"opacity"});ja(v,{borderOpacity:"opacity",borderWidth:"border-width"});ja(q,{outerColor:"background-color",
outerOpacity:"opacity"});(E=c.borderColor1)&&a(v[0]).css({borderStyle:"solid",borderColor:E});(E=c.borderColor2)&&a(v[1]).css({borderStyle:"dashed",borderColor:E});p.append(R.add(v).add(ha)).append(u);S&&((E=(q.css("filter")||"").match(/opacity=(\d+)/))&&q.css("opacity",E[1]/100),(E=(v.css("filter")||"").match(/opacity=(\d+)/))&&v.css("opacity",E[1]/100));b.hide?pa(p.add(q)):b.show&&ba&&(fa=!0,p.add(q).fadeIn(c.fadeSpeed||0),oa());H=(Da=(c.aspectRatio||"").split(/:/))[0]/Da[1];y.add(q).unbind("mousedown",
Aa);if(c.disable||!1===c.enable)p.unbind("mousemove",N).unbind("mousedown",ya),a(window).unbind("resize",Ba);else{if(c.enable||!1===c.disable)(c.resizable||c.movable)&&p.mousemove(N).mousedown(ya),a(window).resize(Ba);c.persistent||y.add(q).mousedown(Aa)}c.enable=c.disable=void 0}var y=a(B),ba,p=k(),R=k(),v=k().add(k()).add(k()).add(k()),q=k().add(k()).add(k()).add(k()),u=a([]),ha,r,t,C={left:0,top:0},z,w,Q,x={left:0,top:0},I=0,ca="absolute",sa,ta,U,V,A,W,X,la,ma,H,fa,f,h,l,m,d={x1:0,y1:0,x2:0,y2:0,
width:0,height:0},na=document.documentElement,D=navigator.userAgent,Da,T,E,G,M,qa,wa=function(a){var d=c.keys,k=a.keyCode;var g=isNaN(d.alt)||!a.altKey&&!a.originalEvent.altKey?!isNaN(d.ctrl)&&a.ctrlKey?d.ctrl:!isNaN(d.shift)&&a.shiftKey?d.shift:isNaN(d.arrows)?10:d.arrows:d.alt;if("resize"==d.arrows||"resize"==d.shift&&a.shiftKey||"resize"==d.ctrl&&a.ctrlKey||"resize"==d.alt&&(a.altKey||a.originalEvent.altKey)){switch(k){case 37:g=-g;case 39:a=e(f,l);f=b(f,l);l=e(a+g,f);Y();break;case 38:g=-g;case 40:a=
e(h,m);h=b(h,m);m=e(a+g,h);Y(!0);break;default:return}da()}else switch(f=b(f,l),h=b(h,m),k){case 37:Z(e(f-g,r),h);break;case 38:Z(f,e(h-g,t));break;case 39:Z(f+b(g,z-J(l)),h);break;case 40:Z(f,h+b(g,w-K(m)));break;default:return}return!1};this.remove=function(){va({disable:!0});p.add(q).remove()};this.getOptions=function(){return c};this.setOptions=va;this.getSelection=F;this.setSelection=ka;this.cancelSelection=ia;this.update=oa;var S=(/msie ([\w.]+)/i.exec(D)||[])[1],Ea=/opera/i.test(D),Fa=/webkit/i.test(D)&&
!/chrome/i.test(D);for(D=y;D.length;)I=e(I,isNaN(D.css("z-index"))?I:D.css("z-index")),"fixed"==D.css("position")&&(ca="fixed"),D=D.parent(":not(body)");I=c.zIndex||I;S&&y.attr("unselectable","on");a.imgAreaSelect.keyPress=S||Fa?"keydown":"keypress";Ea&&(ha=k().css({width:"100%",height:"100%",position:"absolute",zIndex:I+2||2}));p.add(q).css({visibility:"hidden",position:ca,overflow:"hidden",zIndex:I||"0"});p.css({zIndex:I+2||2});R.add(v).css({position:"absolute",fontSize:0});B.complete||"complete"==
B.readyState||!y.is("img")?Ca():y.one("load",Ca);!ba&&S&&7<=S&&(B.src=B.src)};a.fn.imgAreaSelect=function(b){b=b||{};this.each(function(){a(this).data("imgAreaSelect")?b.remove?(a(this).data("imgAreaSelect").remove(),a(this).removeData("imgAreaSelect")):a(this).data("imgAreaSelect").setOptions(b):b.remove||(void 0===b.enable&&void 0===b.disable&&(b.enable=!0),a(this).data("imgAreaSelect",new a.imgAreaSelect(this,b)))});return b.instance?a(this).data("imgAreaSelect"):this}})(jQuery);
