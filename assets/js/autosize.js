(function($){var
defaults={className:'autosizejs',append:'',callback:false,resizeDelay:10},copy='<textarea tabindex="-1" style="position:absolute; top:-999px; left:0; right:auto; bottom:auto; border:0; -moz-box-sizing:content-box; -webkit-box-sizing:content-box; box-sizing:content-box; word-wrap:break-word; height:0 !important; min-height:0 !important; overflow:hidden; transition:none; -webkit-transition:none; -moz-transition:none;"/>',typographyStyles=['fontFamily','fontSize','fontWeight','fontStyle','letterSpacing','textTransform','wordSpacing','textIndent'],mirrored,mirror=$(copy).data('autosize',true)[0];mirror.style.lineHeight='99px';if($(mirror).css('lineHeight')==='99px'){typographyStyles.push('lineHeight');}
mirror.style.lineHeight='';$.fn.autosize=function(options){options=$.extend({},defaults,options||{});if(mirror.parentNode!==document.body){$(document.body).append(mirror);}
return this.each(function(){var
ta=this,$ta=$(ta),maxHeight,minHeight,boxOffset=0,callback=$.isFunction(options.callback),originalStyles={height:ta.style.height,overflow:ta.style.overflow,overflowY:ta.style.overflowY,wordWrap:ta.style.wordWrap,resize:ta.style.resize};if($ta.data('autosize')){return;}
$ta.data('autosize',true);if($ta.css('box-sizing')==='border-box'||$ta.css('-moz-box-sizing')==='border-box'||$ta.css('-webkit-box-sizing')==='border-box'){boxOffset=$ta.outerHeight()-$ta.height();}
minHeight=Math.max(parseInt($ta.css('minHeight'),10)-boxOffset||0,$ta.height());$ta.css({overflow:'hidden',overflowY:'hidden',wordWrap:'break-word',resize:($ta.css('resize')==='none'||$ta.css('resize')==='vertical')?'none':'horizontal'});function initMirror(){var styles={},ignore;mirrored=ta;mirror.className=options.className;maxHeight=parseInt($ta.css('maxHeight'),10);$.each(typographyStyles,function(i,val){styles[val]=$ta.css(val);});$(mirror).css(styles);if('oninput'in ta){var width=ta.style.width;ta.style.width='0px';ignore=ta.offsetWidth;ta.style.width=width;}}
function adjust(){var height,original,width,style;if(mirrored!==ta){initMirror();}
mirror.value=ta.value+options.append;mirror.style.overflowY=ta.style.overflowY;original=parseInt(ta.style.height,10);if('getComputedStyle'in window){style=window.getComputedStyle(ta);width=ta.getBoundingClientRect().width;$.each(['paddingLeft','paddingRight','borderLeftWidth','borderRightWidth'],function(i,val){width-=parseInt(style[val],10);});mirror.style.width=width+'px';}
else{mirror.style.width=Math.max($ta.width(),0)+'px';}
mirror.scrollTop=0;mirror.scrollTop=9e4;height=mirror.scrollTop;if(maxHeight&&height>maxHeight){ta.style.overflowY='scroll';height=maxHeight;}else{ta.style.overflowY='hidden';if(height<minHeight){height=minHeight;}}
height+=boxOffset;if(original!==height){ta.style.height=height+'px';if(callback){options.callback.call(ta,ta);}}}
if('onpropertychange'in ta){if('oninput'in ta){$ta.on('input.autosize keyup.autosize',adjust);}else{$ta.on('propertychange.autosize',function(){if(event.propertyName==='value'){adjust();}});}}else{$ta.on('input.autosize',adjust);}
if(options.resizeDelay!==false){var timeout;var width=$(ta).width();$(window).on('resize.autosize',function(){clearTimeout(timeout);timeout=setTimeout(function(){if($(ta).width()!==width){adjust();}},parseInt(options.resizeDelay,10));});}
$ta.on('autosize.resize',adjust);$ta.on('autosize.resizeIncludeStyle',function(){mirrored=null;adjust();});$ta.on('autosize.destroy',function(){mirrored=null;$ta.off('autosize').off('.autosize').css(originalStyles).removeData('autosize');});adjust();});};}(window.jQuery||window.Zepto));


/*
* WRAPPER FOR THE ABOVE PLUGIN
* AUTOMATIC RESIZE OF TEXTAREA BASED ON THE CONTENT OR NEW LINE CHARACTER
*/
jQuery.fn.space_autoresize = function(){
	return this.each(function() {
		var $el = jQuery(this);
		$el.attr('rows', 1);
		$el.autosize();
	});
};