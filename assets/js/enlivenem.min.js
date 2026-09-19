/*************************************************************************************
 * @name: enlivenem.js - the main file of Enliven'em script
 * @version: 1.4
 * @URL: http://enlivenem.com
 * @copyright: (c) 2014 DeeThemes (http://codecanyon.net/user/DeeThemes)
 * @licenses: http://codecanyon.net/licenses/regular
			  http://codecanyon.net/licenses/extended
*************************************************************************************/

;(function() {

	"use strict";

	//convert SVG primitives (rect, circle, ellipse, line, polygon, polyline) to path
	Snap.plugin(function (Snap, Element, Paper, glob, Fragment) {
		Element.prototype.cloneToPath = function () {
			var w, h, rx, ry, d, path,
				x = 0,
				y = 0;
			if (this.parent().type.toLowerCase() === 'a') {
				var parent = this.parent().parent(),
					g = parent.g();
			} else {
				var parent = this.parent(),
					g = parent.g();
			};
			if (this.node.attributes.transform) {
				if (this.node.attributes.transform.value) {
					var trf = this.node.attributes.transform.value;
				} else {
					var trf = this.node.attributes.transform.nodeValue;
				};
			} else {
				var trf = '';
			};
			switch(this.type.toLowerCase()) {
				case 'rect': {
					w  = this.attr('width');
					h  = this.attr('height');
					rx = this.attr('rx');
					ry = this.attr('ry');
					//normalising
					if (rx < 0) {rx = 0};
					if (ry < 0) {ry = 0};
					rx = rx || ry;
					ry = ry || rx;
					if (rx > w/2) {rx = w/2};
					if (ry > h/2) {ry = h/2};
					if (rx && ry) {
						d = [
							'M'+ rx +' '+ y,
							'H'+ (w - rx),
							'A'+ rx +' '+ ry +' 0 0 1 '+ w +' '+ ry,
							'V'+ (h - ry),
							'A'+ rx +' '+ ry +' 0 0 1 '+ (w - rx) +' '+ h,
							'H'+ rx,
							'A'+ rx +' '+ ry +' 0 0 1 '+ x +' ' +(h - ry),
							'V'+ ry,
							'A'+ rx +' '+ ry +' 0 0 1 '+ rx +' '+ y,
							'z'
						];
					} else {
						d = [
							'M'+ x +' '+ y,
							'H'+ w,
							'V'+ h,
							'H'+ x,
							'V'+ y,
							'z'
						];
					};
					x = this.attr('x');
					y = this.attr('y');
					break;
				}
				case 'circle': 
				case 'ellipse': {
					rx = this.type == 'ellipse' ? this.attr('rx') : this.attr('r');
					ry = this.type == 'ellipse' ? this.attr('ry') : this.attr('r');
					d = [
						'M'+ rx +' '+ y,
						'A'+ rx +' '+ ry +' 0 0 1 '+ (rx * 2) +' '+ ry,
						'A'+ rx +' '+ ry +' 0 0 1 '+ rx +' '+ (ry * 2),
						'A'+ rx +' '+ ry +' 0 0 1 '+ x +' '+ ry,
						'A'+ rx +' '+ ry +' 0 0 1 '+ rx +' '+ y,
						'z'
					];
					x = this.attr('cx') - rx;
					y = this.attr('cy') - ry;
					break;
				}
				case 'polygon':
				case 'polyline': {
					d = [];
					var points = this.attr('points');
					for (var i = 0; i < points.length; i=i+2)
						d.push((i == 0 ? 'M' : 'L') + points[i] + ',' + points[i+1]);
					if (this.type == 'polygon') {d.push('Z')};
					break;
				}
				case 'line': {
					d = ['M'+ this.attr('x1') +' '+ this.attr('y1'), 'L'+ this.attr('x2') +' '+ this.attr('y2')];
					break;
				}
				case 'path': {
					d = this.attr('d');
					break;
				}
			};

			if (Snap.is(d, 'array')) {
				if (this.type.toLowerCase() === 'line' || this.type.toLowerCase() === 'polyline' || this.type.toLowerCase() === 'polygon') {
					path = parent.path(d.join(''));
				} else {
					path = parent.path(d.join('')).transform('t'+ x +','+ y);
				};
			} else if (this.type.toLowerCase() === 'path') {
				path = parent.path(d);
			};
			this.after(path);
			path.after(g);
			this.addClass('elvn-source');
			g.add(path).addClass('elvn-clon');
			g.node.setAttribute('transform', trf);

			return g;
		};//end clonToPath
	});//end Snap.plugin

	jQuery.fn.extend({
		//main jQuery method
		enlivenEm: function(callback) {
			return this.each(function() {
				var $src = jQuery(this),
					$parent = $src.parent(),
					$tag = $src.prop('tagName').toLowerCase(),
					classes = $src.attr('class'),
					id = $src.attr('id');
				if ($tag === 'img') {
					var svg_src = $src.attr('src'),
						img_tag_is_used = true;
					$src.css('visibility','hidden');
					var new_wrapper = jQuery('<div></div>').insertAfter($src);
					//replace SVG in <img> with in-line SVG code from this <img> src 
					$parent = new_wrapper.load(svg_src, onSvgReady);
				} else if ($tag === 'svg') {
					var img_tag_is_used = false;
					onSvgReady();
				};

				function onSvgReady(){
					var parent = Snap($parent[0]),//new Snap instance
						svg = parent.select('svg'),
						w = svg.attr('width'),
						h = svg.attr('height'),
						done_once = false,
						timeouts = [],
						is = Snap.is,
						shift;
					if (!w && !h) {
						w = svg.attr('viewBox').w;
						h = svg.attr('viewBox').h;
					};
					if (is(w, 'string')) {
						w = +w.replace(/[^0-9]/g, '');
					}
					if (is(h, 'string')){
						h = +h.replace(/[^0-9]/g, '');
					}
					
					svg.addClass(classes);
					svg.attr('overflow', 'hidden');
					if (typeof id !== 'undefined') {
						svg.attr('id', id);
					} else {
						svg.attr('id', 'elvn-'+ uniqueNum());
					};
					if ($tag === 'img') {
						$src.remove();
					};
					
					var $svg = $parent.children('svg');
					var glob_options = svg.attr('data-global-elvn');
					
					//setting global options
					if (glob_options) {
						glob_options = glob_options.replace(/\s+/g, '').split(',');
						var disable_viewport, on_click, startVisible, responsive, globalDelay, loop, loopDelay;
						glob_options[0] === 'disableViewport' ? disable_viewport = true : disable_viewport = false;
						glob_options[1] === 'enableClick' ? on_click = true : on_click = false;
						var viewport_shift = glob_options[2];
						glob_options[3] === 'startVisible' ? startVisible = true : startVisible = false;
						glob_options[4] === 'responsive' ? responsive = true : responsive = false;
						glob_options[5] == undefined ? globalDelay = 0 : globalDelay = +glob_options[5];
						glob_options[6] === 'loop' ? loop = true : loop = false;
						glob_options[7] === undefined ? loopDelay = 500 :  loopDelay = +glob_options[7];
						if (loopDelay <= 500) {loopDelay = 500;};
					} else {
						var disable_viewport = false,
							on_click = false,
							viewport_shift = 'none',
							startVisible = false,
							responsive = false,
							loop = false,
							loopDelay = 500;
					};
					
					//make svg responsive
					if (responsive) {
						$svg.wrap('<div class="elvn-responsive"></div>');
						$parent = $svg.parent();//getting new jQuery parent
						parent = Snap($parent[0]);//getting new Snap parent
						$parent.css('padding-bottom', (h/w*100).toFixed(2) + '%');
						svg.attr({ viewBox:'0 0 '+ w +' '+ h, preserveAspectRatio:"xMinYMin meet" });
						$svg.removeAttr('width');
						$svg.removeAttr('height');
						var code = svg.innerSVG();
						var attrs = {};
						jQuery($svg[0].attributes).each(function() {
						  if (this.value) {
							attrs[this.nodeName] = this.value;
						  } else {
							attrs[this.nodeName] = this.nodeValue;
						  };
						});
						$parent.empty();
						svg = parent.append(Snap.parse('<svg></svg>')).select('svg');
						svg.select('desc').remove();
						svg.append(Snap.parse(code));
						for (var key in attrs) {
							if (attrs.hasOwnProperty(key)) {
								svg.attr(key, attrs[key]);
							};
						};
						$svg = $parent.children('svg'); 
					} else {
						$svg.wrap('<div class="elvn"></div>');
					};

					//fix sub-pixel render bug in Firefox and IE
					var pos = svg.node.getScreenCTM();
					if (pos) {
						var left_shift = (-pos.e % 1),
							top_shift = (-pos.f % 1);
						if (left_shift == 0) {
							left_shift = 0;
						} else if (left_shift <= -0.5) {
							left_shift = left_shift+1;
						};
						if (top_shift == 0) {
							top_shift = 0;
						} else if (top_shift <= -0.5) {
							top_shift = top_shift+1;
						};
						jQuery(svg.node).css({left: left_shift + 'px', top: top_shift + 'px'});
					};
					
					//Hide on start if global is set to "invisible"
					if (!startVisible) {
						svg.attr('visibility', 'hidden');
					} else {
						svg.attr('visibility', 'visible');
					};

					//calculating viewport's shift
					var s_h = verge.rectangle(svg.parent().node).height;
					switch (viewport_shift) {
						case 'none': {
							shift = 0.1;
							break;
						}
						case 'oneFourth': {
							shift = s_h/4;
							break;
						}
						case 'oneThird': {
							shift = s_h/3;
							break;
						}
						case 'oneHalf': {
							shift = s_h/2;
							break;
						}
						case 'twoThird': {
							shift = s_h*2/3;
							break;
						}
						case 'full': {
							shift = s_h;
							break;
						}
						default: {//one-half
							shift = s_h/2;
							break;
						}
					};

					//execute a callback
					if (typeof callback == 'function') {
						callback();
					};

					//calculating total loop's duration
					if (loop) {
						var time = 0;
						svg.selectAll('.elvn-layer').forEach(function (elem) {
							var options = elem.attr('data-elvn');
							if (options && options.match(/drawLines/)) {
								options = options.replace(/\s+/g, '').split(',');
								if (time < ((+options[1]) + (+options[2]))) {
									time = (+options[1]) + (+options[2]);
								};
							} else if (options) {
								options = options.replace(/\s+/g, '').split(',');
								if (time < ((+options[2]) + (+options[3]))) {
									time = (+options[2]) + (+options[3]);
								};
							};
						});
						loopDelay = loopDelay + time;
					};

					//bind viewport event
					if (!disable_viewport) {
						jQuery(window).on('resize, scroll', viewportEvent());
					};

					function viewportEvent() {
						return function () {
							if (verge.inViewport(svg.parent().node, -shift)) {
								if (!done_once) {
									done_once = true;
									if (loop) {
										var sto = setTimeout(function(){
											//svg.attr('visibility','visible');
											doLoop();
											clearTimeout(sto);
										}, globalDelay);
									} else {
										svg.attr('visibility','visible');
										svg.selectAll('.elvn-layer').forEach(function (elem) {
											enlivenEm(elem, svg, w, h, timeouts, globalDelay);
										}, svg, w, h);
									};
								};
							};
						};
					};
					
					function repeatAnimation() {
						svg.attr('visibility','visible');
						for (var i = 0; i < timeouts.length; i++) {
							clearTimeout(timeouts[i]);
						};
						timeouts =[];
						svg.selectAll('.elvn-source').forEach(function (elem) {
							elem.stop();
						});
						svg.selectAll('.elvn-clon').remove();
						svg.selectAll('.elvn-wrapper').forEach(function (wrapper) {
							wrapper.stop();
							wrapper.transform('');
						});
						svg.selectAll('.elvn-mask').remove();
						svg.selectAll('.elvn-gradient').remove();
						svg.selectAll('.elvn-filter').remove();
						svg.selectAll('.elvn-layer').forEach(function (elem) {
							if (elem.attr('data-elvnopacity')) {
								var op = elem.attr('data-elvnopacity');
								elem.attr('opacity',op);
							};
							//proceed animation for each layer
							enlivenEm(elem, svg, w, h, timeouts, 0);
						}, svg, w, h);
					};

					function doLoop(){
						if (verge.inViewport(svg.parent().node, -shift)) {
							repeatAnimation();
						};
						setTimeout(function(){ doLoop(); }, loopDelay);
					};

					if (!loop && on_click) {
						//animate when a user clicks svg's parent (if global option is "enableClick" and not "loop")
						$parent.click(function() {
							repeatAnimation();
						});
					} else if (loop && on_click && disable_viewport) {
						//if globals are "disableViewport", "enableClick" and "loop", a looped animation starts when user clicks 
						$parent.click(function() {
							if (!done_once) {
								done_once = true;
								var sto = setTimeout(function(){
									svg.attr('visibility','visible');
									doLoop();
									clearTimeout(sto);
								}, globalDelay);
							};
						});
					};

					if (img_tag_is_used) {
						jQuery(document).ajaxComplete(function() {
							jQuery(window).scroll();
						});
					} else {
						jQuery(window).scroll();
					};

				};//end onSvgReady
			});//end return
		},//end enlivenEm
		
		//private method for using in the Animation Editor
		_elvn: function (elem, svg, w, h, instant) {
			var tm = [],
				globalDelay = 0;
			enlivenEm (elem, svg, w, h, tm, globalDelay, instant);
		},//end _elvn

		//jQuery method .trigger('click') doesn't work with SVG. 
		//So this is a special method for invoke 'click' event programmatically.
		svgClick: function () {
			this.each(function (i, e) {
				var evt = document.createEvent("MouseEvents");
				evt.initMouseEvent("click", true, true, window, 0, 0, 0, 0, 0, false, false, false, false, 0, null);
				e.dispatchEvent(evt);
			});
		}
	});//end jQuery plugin

	//The main function
	function enlivenEm (elem, svg, w, h, timeouts, globalDelay, instant) {
		//function for line drawing effect (must be declared here in strict mode)
		function _drawStroke(elem, rnd, stored_opacity) {
			if(['circle', 'ellipse', 'line', 'path', 'polygon', 'polyline', 'rect'].indexOf(elem.type.toLowerCase()) !== -1) {
				var clon = elem.cloneToPath(),
					cloned_path = clon.select('path'),
					totalLength = cloned_path.getTotalLength();
				elem.attr('opacity', 0);
				cloned_path.attr({fill:'none', stroke:color, strokeOpacity:0, strokeWidth: strokeWidth, strokeLinecap: 'round', strokeDashoffset: totalLength, strokeDasharray: '0 '+ totalLength +' '+ totalLength +' 0'});

				//animating stroke drawing
				var tmp = [];
				if (rnd) {
					var dur = getRandomInt(0.5*duration, 0.8*duration);
				} else {
					var dur = 0.8*duration;
				};
				tmp[0] = setTimeout(function() {
					cloned_path.attr({strokeOpacity: 1});
					//cloned_path.animate({strokeDashoffset: 0}, dur, mina.easeinout);
					Snap.animate(0, -totalLength, function( value ){ 
						cloned_path.attr({'strokeDashoffset': value})
					}, dur, mina.easeinout);
				}, delay);
				timeouts.push(tmp[0]);

				//animating disappearing of cloned path and appearing of main shape
				tmp[1] = setTimeout(function() {
					elem.animate({opacity: stored_opacity}, duration-dur*1.1);
					cloned_path.animate({opacity: 0}, duration-dur, mina.linear, function () {
						clon.remove();
					});
				}, delay + dur);
				timeouts.push(tmp[1]);
			};
		};//end drawStroke

		//function for removing mask url from elem when animation is finished
		function _rM(){ 
			return function () {
				jQuery(wrap_g.node).removeAttr('mask');
			}
		};
		
		var options = elem.attr('data-elvn');
		if (elem.attr('data-elvncustom')) {
			var custom_command = elem.attr('data-elvncustom');
		} else {
			var custom_command = 't0,0';
		};
		if (elem.attr('data-elvnmorph')) {
			var morph_command = elem.attr('data-elvnmorph');
		} else {
			var morph_command = 'M0,0';
		};

		//IE9 gaussBlur fallback to fade
		if (navigator.userAgent.toUpperCase().match(/MSIE 9/)) {
			if (options && options.match(/gaussBlur/)) {
				options = options.replace(/gaussBlur/, 'fade');
			};
		};
				
		//fade, scales, slides and other various effects
		if (options && !options.match(/drawLines/) && !options.match(/mask/) && !options.match(/gaussBlur/) && !options.match(/custom/) && !options.match(/morph/)) {
			//for ex.: class="elvn-layer" data-elvn="fadeLongB,in,0,1000,easein"
			
			//check for IE
			if ('ActiveXObject' in window ) {
				var thisIE = true;
			} else {
				var thisIE = false;
			};

			options = options.replace(/\s+/g, '').split(',');
			var effect = options[0],
				direction = options[1],
				start_delay = +options[2] + globalDelay,
				duration = +options[3],
				easing = options[4],
				anims = [],
				delays = [],
				bb = elem.getBBox();
			if (instant) {
				start_delay = 0;
			};
			var cur_g = elem.parent();
			if (cur_g.hasClass('elvn-wrapper')) {
				var g = cur_g;
			} else {
				var g = svg.g();
				g.insertBefore(elem).add(elem).addClass('elvn-wrapper');
			};

			delays[0] = start_delay;

			//case of easing func
			switch (easing) {
				case 'backin': {
					var ease = mina.backin;
					break;
				}
				case 'backout': {
					var ease = mina.backout;
					break;
				}
				case 'bounce': {
					var ease = mina.bounce;
					break;
				}
				case 'easein': {
					var ease = mina.easein;
					break;
				}
				case 'easeinout': {
					var ease = mina.easeinout;
					break;
				}
				case 'easeout': {
					var ease = mina.easeout;
					break;
				}
				case 'elastic': {
					var ease = mina.elastic;
					break;
				}
				default: { //linear
					var ease = mina.linear;
					break;
				}
			};

			//create an array of Snap.animations
			var animdata = getData(g, effect, w , h, bb);//now animdata is Array
			if (direction === 'out') {
				animdata = reverseArr(animdata);
			};

			//correct zero values of scale for IE
			if (thisIE) {
				for (var i = 0; i < animdata.length; i++) {
					if (animdata[i].a) {
						if (animdata[i].a.transform && animdata[i].a.transform.match(/s0,0,/)) {
							animdata[i].a.transform = animdata[i].a.transform.replace(/s0,0,/g, 's0.001,0.001,');
						};
						if (animdata[i].a.transform && animdata[i].a.transform.match(/s0,1,/)) {
							animdata[i].a.transform = animdata[i].a.transform.replace(/s0,1,/g, 's0.001,1.0,');
						};
						if (animdata[i].a.transform && animdata[i].a.transform.match(/s1,0,/)) {
							animdata[i].a.transform = animdata[i].a.transform.replace(/s1,0,/g, 's1.0,0.001,');
						};
					};
				};
			};

			//set element to start position
			g.attr(animdata[0].a);
			
			for (var i = 1; i < animdata.length; i++) {
				if (direction === 'in') {
					if (animdata[i].aI) {
						var attrs = animdata[i].aI;
					} else {
						var attrs = animdata[i].a;
					};
					if (i === animdata.length-1) {
						anims.push(Snap.animation(attrs, duration * animdata[i].dI/100, ease));
					} else {
						anims.push(Snap.animation(attrs, duration * animdata[i].dI/100));
					};
					delays.push(duration * animdata[i].dI/100);
				} else {
					if (animdata[i].aO) {
						var attrs = animdata[i].aO;
					} else {
						var attrs = animdata[i].a;
					};
					if (i === animdata.length-1) {
						anims.push(Snap.animation(attrs, duration * animdata[i].dO/100, ease));
					} else {
						anims.push(Snap.animation(attrs, duration * animdata[i].dO/100));
					};
					delays.push(duration * animdata[i].dO/100);
				};
			};
			//calculate corresponding delays for each key frame
			for (var i = 1; i < delays.length; i++) {
				delays[i] = delays[i]+delays[i-1];
			};

			//proceed animation steps with delays 
			for(var i = 0; i < anims.length; i++) {
				(function(index, timeouts) {
					var tmp = setTimeout(function() {
						g.animate(anims[index]);
					}, delays[index]);
					timeouts.push(tmp);
				})(i, timeouts);
			};
		
		} else if (options && options.match(/drawLines/)) {//line drawing effect
			//for ex.: class="elvn-layer" data-elvn="drawLines, 0, 2000, red, 3, random"
			options = options.replace(/\s+/g, '').split(',');
			var delay = +options[1] + globalDelay,
				duration = +options[2],
				color = options[3],
				strokeWidth = options[4],
				random_dur = options[5];
			
			if (instant) {
				delay = 0;
			};
			
			if (elem.type.toLowerCase() !== 'g') {
				if (jQuery(elem.node).data('first-opacity')) {
					var cur_opacity = jQuery(elem.node).data('first-opacity');
				} else {
					var cur_opacity = elem.attr('opacity');
					jQuery(elem.node).data('first-opacity', cur_opacity);
				};
				_drawStroke(elem, false, cur_opacity);
			} else {
				elem.selectAll('*').forEach(function (single_elem) {
					if (!single_elem.hasClass('elvn-layer')) {
						if (jQuery(single_elem.node).data('first-opacity')) {
							var cur_opacity = jQuery(single_elem.node).data('first-opacity');
						} else {
							var cur_opacity = single_elem.attr('opacity');
							jQuery(single_elem.node).data('first-opacity', cur_opacity);
						};
						if (random_dur === 'random') {
							_drawStroke(single_elem, true, cur_opacity);
						} else {
							_drawStroke(single_elem, false, cur_opacity);
						};
					};
				});
			};
		
		} else if (options && options.match(/mask/)) {//mask effects
			//for ex.: class="elvn-layer" data-elvn="maskRect,in,0,1000,easein"
			options = options.replace(/\s+/g, '').split(',');
			var effect = options[0],
				direction = options[1],
				delay = +options[2] + globalDelay,
				duration = +options[3],
				easing = options[4],
				anims = [],
				delays = [],
				g = svg.g(),
				mask,
				mask_url,
				ease,
				animation,
				prnt = elem.parent();
			if (instant) {
				delay = 0;
			};
			if (prnt.hasClass('elvn-wrapper')) {
				var wrap_g = prnt;
			} else {
				var wrap_g = svg.g();
				wrap_g.insertBefore(elem).add(elem).addClass('elvn-wrapper');
			};

			var bb = wrap_g.getBBox(),
				max = Math.max(bb.w, bb.h),
				stroke_w = 0;
			wrap_g.selectAll('*').forEach(function (elem) {
				if (elem.attr('stroke-width')) {
					var tmp = +(elem.attr('stroke-width')+'').replace(/[^0-9.]/g, '');
					if (tmp > stroke_w) {
						stroke_w = tmp;
					};
				};
			});
			
			//case of easing func
			switch (easing) {
				case 'backin': {
					ease = mina.backin;
					break;
				}
				case 'backout': {
					ease = mina.backout;
					break;
				}
				case 'bounce': {
					ease = mina.bounce;
					break;
				}
				case 'easein': {
					ease = mina.easein;
					break;
				}
				case 'easeinout': {
					ease = mina.easeinout;
					break;
				}
				case 'easeout': {
					ease = mina.easeout;
					break;
				}
				case 'elastic': {
					ease = mina.elastic;
					break;
				}
				default: { //linear
					ease = mina.linear;
					break;
				}
			};
			
			//recalculate coordinates including stroke width
			var x = bb.x-stroke_w/2-1,
				y = bb.y-stroke_w/2-1,
				w = bb.w+stroke_w+2,
				h = bb.h+stroke_w+2,
				x2= x+w,
				y2= y+h,
				cx= bb.cx,
				cy= bb.cy,
				r = bb.r0+stroke_w+1;
			
			switch (effect) {
				case 'maskStairsL': {
					var s = h/6;
					mask = svg.path(
						'M'+ x2 +','+ y2 +
						'L'+ x2 +','+ (y2-s) +
						'L'+ (x2+s) +','+ (y2-s) +
						'L'+ (x2+s) +','+ (y2-2*s) +
						'L'+ (x2+2*s) +','+ (y2-2*s) +
						'L'+ (x2+2*s) +','+ (y2-3*s) +
						'L'+ (x2+3*s) +','+ (y2-3*s) +
						'L'+ (x2+3*s) +','+ (y2-4*s) +
						'L'+ (x2+4*s) +','+ (y2-4*s) +
						'L'+ (x2+4*s) +','+ (y2-5*s) +
						'L'+ (x2+5*s) +','+ (y2-5*s) +
						'L'+ (x2+5*s) +','+ y +
						'L'+ x +','+ y +
						'L'+ x +','+ y2 +
						'z'
					).attr('fill', '#fff');
					if (direction === 'in') {
						mask.attr({transform:'t'+ -(w+h) +',0'});
						animation = Snap.animation({transform: 't0,0'}, duration, ease, _rM());
					} else {//out
						mask.attr({transform:'t0,0'});
						animation = Snap.animation({transform: 't'+ -(w+h) +',0'}, duration, ease);
					};
					break;
				}
				case 'maskStairsR': {
					var s = h/6;
					mask = svg.path(
						'M'+ x +','+ y2 +
						'L'+ x +','+ (y2-s) +
						'L'+ (x-s) +','+ (y2-s) +
						'L'+ (x-s) +','+ (y2-2*s) +
						'L'+ (x-2*s) +','+ (y2-2*s) +
						'L'+ (x-2*s) +','+ (y2-3*s) +
						'L'+ (x-3*s) +','+ (y2-3*s) +
						'L'+ (x-3*s) +','+ (y2-4*s) +
						'L'+ (x-4*s) +','+ (y2-4*s) +
						'L'+ (x-4*s) +','+ (y2-5*s) +
						'L'+ (x-5*s) +','+ (y2-5*s) +
						'L'+ (x-5*s) +','+ y +
						'L'+ x2 +','+ y +
						'L'+ x2 +','+ y2 +
						'z'
					).attr('fill', '#fff');
					if (direction === 'in') {
						mask.attr({transform:'t'+ (w+h) +',0'});
						animation = Snap.animation({transform: 't0,0'}, duration, ease, _rM());
					} else {//out
						mask.attr({transform:'t0,0'});
						animation = Snap.animation({transform: 't'+ (w+h) +',0'}, duration, ease);
					};
					break;
				}
				case 'maskStairsT': {
					var s = w/6;
					mask = svg.path(
						'M'+ x2 +','+ y2 +
						'L'+ (x2-s) +','+ y2 +
						'L'+ (x2-s) +','+ (y2+s) +
						'L'+ (x2-2*s) +','+ (y2+s) +
						'L'+ (x2-2*s) +','+ (y2+2*s) +
						'L'+ (x2-3*s) +','+ (y2+2*s) +
						'L'+ (x2-3*s) +','+ (y2+3*s) +
						'L'+ (x2-4*s) +','+ (y2+3*s) +
						'L'+ (x2-4*s) +','+ (y2+4*s) +
						'L'+ (x2-5*s) +','+ (y2+4*s) +
						'L'+ (x2-5*s) +','+ (y2+5*s) +
						'L'+ (x) +','+ (y2+5*s) +
						'L'+ x +','+ y +
						'L'+ x2 +','+ y +
						'z'
					).attr('fill', '#fff');
					if (direction === 'in') {
						mask.attr({transform:'t0,'+ -(h+w)});
						animation = Snap.animation({transform: 't0,0'}, duration, ease, _rM());
					} else {//out
						mask.attr({transform:'t0,0'});
						animation = Snap.animation({transform: 't0,'+ -(h+w)}, duration, ease);
					};
					break;
				}
				case 'maskStairsB': {
					var s = w/6;
					mask = svg.path(
						'M'+ x2 +','+ y +
						'L'+ (x2-s) +','+ y +
						'L'+ (x2-s) +','+ (y-s) +
						'L'+ (x2-2*s) +','+ (y-s) +
						'L'+ (x2-2*s) +','+ (y-2*s) +
						'L'+ (x2-3*s) +','+ (y-2*s) +
						'L'+ (x2-3*s) +','+ (y-3*s) +
						'L'+ (x2-4*s) +','+ (y-3*s) +
						'L'+ (x2-4*s) +','+ (y-4*s) +
						'L'+ (x2-5*s) +','+ (y-4*s) +
						'L'+ (x2-5*s) +','+ (y-5*s) +
						'L'+ (x) +','+ (y-5*s) +
						'L'+ x +','+ y2 +
						'L'+ x2 +','+ y2 +
						'z'
					).attr('fill', '#fff');
					if (direction === 'in') {
						mask.attr({transform:'t0,'+ (h+w)});
						animation = Snap.animation({transform: 't0,0'}, duration, ease, _rM());
					} else {//out
						mask.attr({transform:'t0,0'});
						animation = Snap.animation({transform: 't0,'+ (h+w)}, duration, ease);
					};
					break;
				}
				case 'maskStackX': {
					h = h/6;
					var d1 = 
						'M'+ x +','+ y +
						'L'+ (x+w) +','+ y +
						'L'+ (x+w) +','+ (y+h) +
						'L'+ (x) +','+ (y+h) +
						'z'+
						'M'+ x +','+ (y+h) +
						'L'+ (x+w) +','+ (y+h) +
						'L'+ (x+w) +','+ (y+h*2) +
						'L'+ (x) +','+ (y+h*2) +
						'z'+
						'M'+ x +','+ (y+h*2) +
						'L'+ (x+w) +','+ (y+h*2) +
						'L'+ (x+w) +','+ (y+h*3) +
						'L'+ (x) +','+ (y+h*3) +
						'z'+
						'M'+ x +','+ (y+h*3) +
						'L'+ (x+w) +','+ (y+h*3) +
						'L'+ (x+w) +','+ (y+h*4) +
						'L'+ (x) +','+ (y+h*4) +
						'z'+
						'M'+ x +','+ (y+h*4) +
						'L'+ (x+w) +','+ (y+h*4) +
						'L'+ (x+w) +','+ (y+h*5) +
						'L'+ (x) +','+ (y+h*5) +
						'z'+
						'M'+ x +','+ (y+h*5) +
						'L'+ (x+w) +','+ (y+h*5) +
						'L'+ (x+w) +','+ (y+h*6) +
						'L'+ (x) +','+ (y+h*6) +
						'z';
					var d2 = 
						'M'+ (x-w) +','+ y +
						'L'+ x +','+ y +
						'L'+ x +','+ (y+h) +
						'L'+ (x-w) +','+ (y+h) +
						'z'+
						'M'+ (x2) +','+ (y+h) +
						'L'+ (x2+w) +','+ (y+h) +
						'L'+ (x2+w) +','+ (y+h*2) +
						'L'+ (x2) +','+ (y+h*2) +
						'z'+
						'M'+ (x-w) +','+ (y+h*2) +
						'L'+ (x) +','+ (y+h*2) +
						'L'+ (x) +','+ (y+h*3) +
						'L'+ (x-w) +','+ (y+h*3) +
						'z'+
						'M'+ (x2) +','+ (y+h*3) +
						'L'+ (x2+w) +','+ (y+h*3) +
						'L'+ (x2+w) +','+ (y+h*4) +
						'L'+ (x2) +','+ (y+h*4) +
						'z'+
						'M'+ (x-w) +','+ (y+h*4) +
						'L'+ (x) +','+ (y+h*4) +
						'L'+ (x) +','+ (y+h*5) +
						'L'+ (x-w) +','+ (y+h*5) +
						'z'+
						'M'+ (x2) +','+ (y+h*5) +
						'L'+ (x2+w) +','+ (y+h*5) +
						'L'+ (x2+w) +','+ (y+h*6) +
						'L'+ (x2) +','+ (y+h*6) +
						'z';
					mask = svg.path(d2).attr('fill', '#fff');
					if (direction === 'in') {
						mask.attr({d: d2});
						animation = Snap.animation({d: d1}, duration, ease, _rM());
					} else {//out
						mask.attr({d: d1});
						animation = Snap.animation({d: d2}, duration, ease);
					};
					break;
				}
				case 'maskStackY': {
					w = w/6;
					var d1 = 
						'M'+ x +','+ y +
						'L'+ (x+w) +','+ y +
						'L'+ (x+w) +','+ (y2) +
						'L'+ x +','+ (y2) +
						'z'+
						'M'+ (x+w) +','+ (y) +
						'L'+ (x+w*2) +','+ (y) +
						'L'+ (x+w*2) +','+ (y2) +
						'L'+ (x+w) +','+ (y2) +
						'z'+
						'M'+ (x+w*2) +','+ (y) +
						'L'+ (x+w*3) +','+ (y) +
						'L'+ (x+w*3) +','+ (y2) +
						'L'+ (x+w*2) +','+ (y2) +
						'z'+
						'M'+ (x+w*3) +','+ (y) +
						'L'+ (x+w*4) +','+ (y) +
						'L'+ (x+w*4) +','+ (y2) +
						'L'+ (x+w*3) +','+ (y2) +
						'z'+
						'M'+ (x+w*4) +','+ (y) +
						'L'+ (x+w*5) +','+ (y) +
						'L'+ (x+w*5) +','+ (y2) +
						'L'+ (x+w*4) +','+ (y2) +
						'z'+
						'M'+ (x+w*5) +','+ (y) +
						'L'+ (x+w*6) +','+ (y) +
						'L'+ (x+w*6) +','+ (y2) +
						'L'+ (x+w*5) +','+ (y2) +
						'z';
					var d2 = 
						'M'+ x +','+ (y-h) +
						'L'+ (x+w) +','+ (y-h) +
						'L'+ (x+w) +','+ y +
						'L'+ x +','+ y +
						'z'+
						'M'+ (x+w) +','+ y2 +
						'L'+ (x+w*2) +','+ y2 +
						'L'+ (x+w*2) +','+ (y2+h) +
						'L'+ (x+w) +','+ (y2+h) +
						'z'+
						'M'+ (x+w*2) +','+ (y-h) +
						'L'+ (x+w*3) +','+ (y-h) +
						'L'+ (x+w*3) +','+ y +
						'L'+ (x+w*2) +','+ y +
						'z'+
						'M'+ (x+w*3) +','+ y2 +
						'L'+ (x+w*4) +','+ y2 +
						'L'+ (x+w*4) +','+ (y2+h) +
						'L'+ (x+w*3) +','+ (y2+h) +
						'z'+
						'M'+ (x+w*4) +','+ (y-h) +
						'L'+ (x+w*5) +','+ (y-h) +
						'L'+ (x+w*5) +','+ y +
						'L'+ (x+w*4) +','+ y +
						'z'+
						'M'+ (x+w*5) +','+ y2 +
						'L'+ (x+w*6) +','+ y2 +
						'L'+ (x+w*6) +','+ (y2+h) +
						'L'+ (x+w*5) +','+ (y2+h) +
						'z';
					mask = svg.path(d2).attr('fill', '#fff');
					if (direction === 'in') {
						mask.attr({d: d2});
						animation = Snap.animation({d: d1}, duration, ease, _rM());
					} else {//out
						mask.attr({d: d1});
						animation = Snap.animation({d: d2}, duration, ease);
					};
					break;
				}
				case 'maskTighten': {
					var d1 = 
						'M'+ x +','+ y +
						'L'+ x2 +','+ y +
						'L'+ cx +','+ cy +
						'z'+
						'M'+ x2 +','+ y +
						'L'+ x2 +','+ y2 +
						'L'+ cx +','+ cy +
						'z'+
						'M'+ x2 +','+ y2 +
						'L'+ x +','+ y2 +
						'L'+ cx +','+ cy +
						'z'+
						'M'+ x +','+ y2 +
						'L'+ x +','+ y +
						'L'+ cx +','+ cy +
						'z';
					var d2 = 
						'M'+ x +','+ y +
						'L'+ x2 +','+ y +
						'L'+ cx +','+ y +
						'z'+
						'M'+ x2 +','+ y +
						'L'+ x2 +','+ y2 +
						'L'+ x2 +','+ cy +
						'z'+
						'M'+ x2 +','+ y2 +
						'L'+ x +','+ y2 +
						'L'+ cx +','+ y2 +
						'z'+
						'M'+ x +','+ y2 +
						'L'+ x +','+ y +
						'L'+ x +','+ cy +
						'z';
					mask = svg.path(d1).attr('fill', '#fff');
					if (direction === 'in') {
						mask.attr({d: d2});
						animation = Snap.animation({d: d1}, duration, ease, _rM());
					} else {//out
						mask.attr({d: d1});
						animation = Snap.animation({d: d2}, duration, ease);
					};
					break;
				}
				case 'maskRect': {
					mask = svg.rect(x, y, w, h);
					mask.attr({fill:"#fff"});
					if (direction === 'in') {
						mask.attr({transform: 's0,0'});
						animation = Snap.animation({transform:'s1,1'}, duration, ease, _rM());
					} else {//out
						mask.attr({transform:'s1,1'});
						animation = Snap.animation({transform: 's0,0'}, duration, ease);
					};
					break;
				}
				case 'maskEllipse': {
					mask = svg.ellipse(bb.cx, bb.cy, 10, 5);
					mask.attr({fill:"#fff"});
					if (direction === 'in') {
						mask.attr({transform: 's0,0'});
						animation = Snap.animation({transform:'s'+ w/14 +','+ h/7}, duration, ease, _rM());
					} else {
						mask.attr({transform:'s'+ w/14 +','+ h/7});
						animation = Snap.animation({transform: 's0,0'}, duration, ease);
					};
					break;
				}
				case 'maskCircle': {
					mask = svg.circle(bb.cx, bb.cy, r);
					mask.attr({fill:"#fff"});
					if (direction === 'in') {
						mask.attr({r: 0});
						animation = Snap.animation({r: r}, duration, ease, _rM());
					} else {
						mask.attr({r: r});
						animation = Snap.animation({r: 0}, duration, ease);
					};
					break;
				}
				case 'maskRhomb': {
					mask = svg.path(
						'M' + bb.cx +','+ (bb.cy-h) +
						'L' + (bb.cx+w) +','+ bb.cy +
						'L' + bb.cx +','+ (bb.cy+h) +
						'L' + (bb.cx-w) +','+ bb.cy +
						'z'
						).attr('fill', '#fff');
					if (direction === 'in') {
						mask.attr({transform:'s0,0'});
						animation = Snap.animation({transform: 's1,1'}, duration, ease, _rM());
					} else {
						mask.attr({transform:'s1,1'});
						animation = Snap.animation({transform: 's0,0'}, duration, ease);
					};
					break;
				}
				case 'maskPlus': {
					var d1 = 
						'M' + cx +','+ cy +
						'L' + cx +','+ y +
						'L' + cx +','+ y +
						'L' + cx +','+ cy +
						'L' + x2 +','+ cy +
						'L' + x2 +','+ cy +
						'L' + cx +','+ cy +
						'L' + cx +','+ y2 +
						'L' + cx +','+ y2 +
						'L' + cx +','+ cy +
						'L' + x +','+ cy +
						'L' + x +','+ cy +
						'z';
					var d2 = 
						'M' + x +','+ y +
						'L' + x +','+ y +
						'L' + x2 +','+ y +
						'L' + x2 +','+ y +
						'L' + x2 +','+ y +
						'L' + x2 +','+ y2 +
						'L' + x2 +','+ y2 +
						'L' + x2 +','+ y2 +
						'L' + x +','+ y2 +
						'L' + x +','+ y2 +
						'L' + x +','+ y2 +
						'L' + x +','+ y +
						'z';
					mask = svg.path(d2).attr('fill', '#fff');
					if (direction === 'in') {
						mask.attr({d: d1});
						animation = Snap.animation({d: d2}, duration, ease, _rM());
					} else {//out
						mask.attr({d: d2});
						animation = Snap.animation({d: d1}, duration, ease);
					};
					break;
				}
				case 'maskPlusRotate': {
					var d1 = 
						'M' + cx +','+ cy +
						'L' + cx +','+ y +
						'L' + cx +','+ y +
						'L' + cx +','+ cy +
						'L' + x2 +','+ cy +
						'L' + x2 +','+ cy +
						'L' + cx +','+ cy +
						'L' + cx +','+ y2 +
						'L' + cx +','+ y2 +
						'L' + cx +','+ cy +
						'L' + x +','+ cy +
						'L' + x +','+ cy +
						'z';
					var d2 = 
						'M' + x +','+ y +
						'L' + x +','+ y +
						'L' + x2 +','+ y +
						'L' + x2 +','+ y +
						'L' + x2 +','+ y +
						'L' + x2 +','+ y2 +
						'L' + x2 +','+ y2 +
						'L' + x2 +','+ y2 +
						'L' + x +','+ y2 +
						'L' + x +','+ y2 +
						'L' + x +','+ y2 +
						'L' + x +','+ y +
						'z';
					mask = svg.path(d2).attr('fill', '#fff');
					if (direction === 'in') {
						mask.attr({d: d1, transform: 't0,0r-180'});
						animation = Snap.animation({d: d2, transform: 't0,0r0'}, duration, ease, _rM());
					} else {//out
						mask.attr({d: d2, transform: 't0,0r0'});
						animation = Snap.animation({d: d1, transform: 't0,0r-180'}, duration, ease);
					};
					break;
				}
				case 'maskCross': {
					var d1 = 
						'M' + cx +','+ cy +
						'L' + x2 +','+ y +
						'L' + x2 +','+ y +
						'L' + cx +','+ cy +
						'L' + x2 +','+ y2 +
						'L' + x2 +','+ y2 +
						'L' + cx +','+ cy +
						'L' + x +','+ y2 +
						'L' + x +','+ y2 +
						'L' + cx +','+ cy +
						'L' + x +','+ y +
						'L' + x +','+ y +
						'z';
					var d2 = 
						'M' + cx +','+ y +
						'L' + x2 +','+ (y-h/2) +
						'L' + (x2+w/2) +','+ y +
						'L' + x2 +','+ cy +
						'L' + (x2+w/2) +','+ y2 +
						'L' + x2 +','+ (y2+h/2) +
						'L' + cx +','+ y2 +
						'L' + x +','+ (y2+h/2) +
						'L' + (x-w/2) +','+ y2 +
						'L' + x +','+ cy +
						'L' + (x-w/2) +','+ y +
						'L' + x +','+ (y-h/2) +
						'z';
					mask = svg.path(d2).attr('fill', '#fff');
					if (direction === 'in') {
						mask.attr({d: d1});
						animation = Snap.animation({d: d2}, duration, ease, _rM());
					} else {//out
						mask.attr({d: d2});
						animation = Snap.animation({d: d1}, duration, ease);
					};
					break;
				}
				case 'maskCrossRotate': {
					var d1 = 
						'M' + cx +','+ cy +
						'L' + x2 +','+ y +
						'L' + x2 +','+ y +
						'L' + cx +','+ cy +
						'L' + x2 +','+ y2 +
						'L' + x2 +','+ y2 +
						'L' + cx +','+ cy +
						'L' + x +','+ y2 +
						'L' + x +','+ y2 +
						'L' + cx +','+ cy +
						'L' + x +','+ y +
						'L' + x +','+ y +
						'z';
					var d2 = 
						'M' + cx +','+ y +
						'L' + x2 +','+ (y-h/2) +
						'L' + (x2+w/2) +','+ y +
						'L' + x2 +','+ cy +
						'L' + (x2+w/2) +','+ y2 +
						'L' + x2 +','+ (y2+h/2) +
						'L' + cx +','+ y2 +
						'L' + x +','+ (y2+h/2) +
						'L' + (x-w/2) +','+ y2 +
						'L' + x +','+ cy +
						'L' + (x-w/2) +','+ y +
						'L' + x +','+ (y-h/2) +
						'z';
					mask = svg.path(d2).attr('fill', '#fff');
					if (direction === 'in') {
						mask.attr({d: d1, transform: 't0,0r-180'});
						animation = Snap.animation({d: d2, transform: 't0,0r0'}, duration, ease, _rM());
					} else {//out
						mask.attr({d: d2, transform: 't0,0r0'});
						animation = Snap.animation({d: d1, transform: 't0,0r-180'}, duration, ease);
					};
					break;
				}
				case 'maskExpand': {
					var d1 = 
						'M' + (bb.x-stroke_w) +','+ bb.cy +
						'L' + (bb.x-stroke_w) +','+ (bb.y-stroke_w) +
						'L' + bb.cx +','+ (bb.y-stroke_w) +
						'L' + (bb.x2+stroke_w) +','+ (bb.y-stroke_w) +
						'L' + (bb.x2+stroke_w) +','+ bb.cy +
						'L' + (bb.x2+stroke_w) +','+ (bb.y2+stroke_w) +
						'L' + bb.cx +','+ (bb.y2+stroke_w) +
						'L' + (bb.x-stroke_w) +','+ (bb.y2+stroke_w) +
						'z';
					var d2 = 
						'M' + (bb.x-stroke_w) +','+ bb.cy +
						'L' + bb.cx +','+ bb.cy +
						'L' + bb.cx +','+ (bb.y-stroke_w) +
						'L' + bb.cx +','+ bb.cy +
						'L' + (bb.x2+stroke_w) +','+ bb.cy +
						'L' + bb.cx +','+ bb.cy +
						'L' + bb.cx +','+ (bb.y2+stroke_w) +
						'L' + bb.cx +','+ bb.cy +
						'z';
					mask = svg.path(d1).attr('fill', '#fff');
					if (direction === 'in') {
						mask.attr({d: d2});
						animation = Snap.animation({d: d1}, duration, ease, _rM());
					} else {//out
						mask.attr({d: d1});
						animation = Snap.animation({d: d2}, duration, ease);
					};
					break;
				}
				case 'maskClockOne': {
					var d1 = 
						'M' + bb.cx +','+ bb.cy +
						'L' + bb.cx +','+ (bb.cy-bb.h-stroke_w-2) +
						'L' + (bb.cx+bb.w+stroke_w+2) +','+ bb.cy +
						'z' +
						'M' + bb.cx +','+ bb.cy +
						'L' + (bb.cx+bb.w+stroke_w+2) +','+ bb.cy +
						'L' + bb.cx +','+ (bb.cy+bb.h+stroke_w+2) +
						'z' +
						'M' + bb.cx +','+ bb.cy +
						'L' + bb.cx +','+ (bb.cy+bb.h+stroke_w+2) +
						'L' + (bb.cx-bb.w-stroke_w-2) +','+ bb.cy +
						'z' +
						'M' + bb.cx +','+ bb.cy +
						'L' + (bb.cx-bb.w-stroke_w-2) +','+ bb.cy +
						'L' + bb.cx +','+ (bb.cy-bb.h-stroke_w-2) +
						'z';
					var d2 = 
						'M' + bb.cx +','+ bb.cy +
						'L' + (bb.cx+bb.w+stroke_w+2) +','+ bb.cy +
						'L' + (bb.cx+bb.w+stroke_w+2) +','+ bb.cy +
						'z' +
						'M' + bb.cx +','+ bb.cy +
						'L' + bb.cx +','+ (bb.cy+bb.h+stroke_w+2) +
						'L' + bb.cx +','+ (bb.cy+bb.h+stroke_w+2) +
						'z' +
						'M' + bb.cx +','+ bb.cy +
						'L' + (bb.cx-bb.w-stroke_w-2) +','+ bb.cy +
						'L' + (bb.cx-bb.w-stroke_w-2) +','+ bb.cy +
						'z' +
						'M' + bb.cx +','+ bb.cy +
						'L' + bb.cx +','+ (bb.cy-bb.h-stroke_w-2) +
						'L' + bb.cx +','+ (bb.cy-bb.h-stroke_w-2) +
						'z';
					mask = svg.path(d1).attr('fill', '#fff');
					if (direction === 'in') {
						mask.attr({d: d2});
						animation = Snap.animation({d: d1}, duration, ease, _rM());
					} else {
						mask.attr({d: d1});
						animation = Snap.animation({d: d2}, duration, ease);
					};
					break;
				}
				case 'maskClockTwo': {
					var d1 = 
						'M' + bb.cx +','+ bb.cy +
						'L' + bb.cx +','+ (bb.cy-bb.h-stroke_w-2) +
						'L' + (bb.cx+bb.w+stroke_w+2) +','+ bb.cy +
						'z' +
						'M' + bb.cx +','+ bb.cy +
						'L' + (bb.cx+bb.w+stroke_w+2) +','+ bb.cy +
						'L' + bb.cx +','+ (bb.cy+bb.h+stroke_w+2) +
						'z' +
						'M' + bb.cx +','+ bb.cy +
						'L' + bb.cx +','+ (bb.cy+bb.h+stroke_w+2) +
						'L' + (bb.cx-bb.w-stroke_w-2) +','+ bb.cy +
						'z' +
						'M' + bb.cx +','+ bb.cy +
						'L' + (bb.cx-bb.w-stroke_w-2) +','+ bb.cy +
						'L' + bb.cx +','+ (bb.cy-bb.h-stroke_w-2) +
						'z';
					var d2 = 
						'M' + bb.cx +','+ bb.cy +
						'L' + bb.cx +','+ (bb.cy-bb.h-stroke_w-2) +
						'L' + bb.cx +','+ (bb.cy-bb.h-stroke_w-2) +
						'z' +
						'M' + bb.cx +','+ bb.cy +
						'L' + (bb.cx+bb.w+stroke_w+2) +','+ bb.cy +
						'L' + (bb.cx+bb.w+stroke_w+2) +','+ bb.cy +
						'z' +
						'M' + bb.cx +','+ bb.cy +
						'L' + bb.cx +','+ (bb.cy+bb.h+stroke_w+2) +
						'L' + bb.cx +','+ (bb.cy+bb.h+stroke_w+2) +
						'z' +
						'M' + bb.cx +','+ bb.cy +
						'L' + (bb.cx-bb.w-stroke_w-2) +','+ bb.cy +
						'L' + (bb.cx-bb.w-stroke_w-2) +','+ bb.cy +
						'z';
					mask = svg.path(d1).attr('fill', '#fff');
					if (direction === 'in') {
						mask.attr({d: d2});
						animation = Snap.animation({d: d1}, duration, ease, _rM());
					} else {//out
						mask.attr({d: d1});
						animation = Snap.animation({d: d2}, duration, ease);
					};
					break;
				}
				case 'maskLouversY': {
					h = h/6;
					var d1 = 
						'M'+ x +','+ y +
						'L'+ (x+w) +','+ y +
						'L'+ (x+w) +','+ (y+h) +
						'L'+ (x) +','+ (y+h) +
						'z'+
						'M'+ x +','+ (y+h) +
						'L'+ (x+w) +','+ (y+h) +
						'L'+ (x+w) +','+ (y+h*2) +
						'L'+ (x) +','+ (y+h*2) +
						'z'+
						'M'+ x +','+ (y+h*2) +
						'L'+ (x+w) +','+ (y+h*2) +
						'L'+ (x+w) +','+ (y+h*3) +
						'L'+ (x) +','+ (y+h*3) +
						'z'+
						'M'+ x +','+ (y+h*3) +
						'L'+ (x+w) +','+ (y+h*3) +
						'L'+ (x+w) +','+ (y+h*4) +
						'L'+ (x) +','+ (y+h*4) +
						'z'+
						'M'+ x +','+ (y+h*4) +
						'L'+ (x+w) +','+ (y+h*4) +
						'L'+ (x+w) +','+ (y+h*5) +
						'L'+ (x) +','+ (y+h*5) +
						'z'+
						'M'+ x +','+ (y+h*5) +
						'L'+ (x+w) +','+ (y+h*5) +
						'L'+ (x+w) +','+ (y+h*6) +
						'L'+ (x) +','+ (y+h*6) +
						'z';
					var d2 = 
						'M'+ x +','+ y +
						'L'+ (x+w) +','+ y +
						'L'+ (x+w) +','+ y +
						'L'+ (x) +','+ (y) +
						'z'+
						'M'+ x +','+ (y+h) +
						'L'+ (x+w) +','+ (y+h) +
						'L'+ (x+w) +','+ (y+h) +
						'L'+ (x) +','+ (y+h) +
						'z'+
						'M'+ x +','+ (y+h*2) +
						'L'+ (x+w) +','+ (y+h*2) +
						'L'+ (x+w) +','+ (y+h*2) +
						'L'+ (x) +','+ (y+h*2) +
						'z'+
						'M'+ x +','+ (y+h*3) +
						'L'+ (x+w) +','+ (y+h*3) +
						'L'+ (x+w) +','+ (y+h*3) +
						'L'+ (x) +','+ (y+h*3) +
						'z'+
						'M'+ x +','+ (y+h*4) +
						'L'+ (x+w) +','+ (y+h*4) +
						'L'+ (x+w) +','+ (y+h*4) +
						'L'+ (x) +','+ (y+h*4) +
						'z'+
						'M'+ x +','+ (y+h*5) +
						'L'+ (x+w) +','+ (y+h*5) +
						'L'+ (x+w) +','+ (y+h*5) +
						'L'+ (x) +','+ (y+h*5) +
						'z';
					mask = svg.path(d2).attr('fill', '#fff');
					if (direction === 'in') {
						mask.attr({d: d2});
						animation = Snap.animation({d: d1}, duration, ease, _rM());
					} else {//out
						mask.attr({d: d1});
						animation = Snap.animation({d: d2}, duration, ease);
					};
					break;
				}
				case 'maskLouversX': {
					w = w/6;
					var d1 = 
						'M'+ x +','+ y +
						'L'+ (x+w) +','+ y +
						'L'+ (x+w) +','+ (y+h) +
						'L'+ x +','+ (y+h) +
						'z'+
						'M'+ (x+w) +','+ (y) +
						'L'+ (x+w*2) +','+ (y) +
						'L'+ (x+w*2) +','+ (y+h) +
						'L'+ (x+w) +','+ (y+h) +
						'z'+
						'M'+ (x+w*2) +','+ (y) +
						'L'+ (x+w*3) +','+ (y) +
						'L'+ (x+w*3) +','+ (y+h) +
						'L'+ (x+w*2) +','+ (y+h) +
						'z'+
						'M'+ (x+w*3) +','+ (y) +
						'L'+ (x+w*4) +','+ (y) +
						'L'+ (x+w*4) +','+ (y+h) +
						'L'+ (x+w*3) +','+ (y+h) +
						'z'+
						'M'+ (x+w*4) +','+ (y) +
						'L'+ (x+w*5) +','+ (y) +
						'L'+ (x+w*5) +','+ (y+h) +
						'L'+ (x+w*4) +','+ (y+h) +
						'z'+
						'M'+ (x+w*5) +','+ (y) +
						'L'+ (x+w*6) +','+ (y) +
						'L'+ (x+w*6) +','+ (y+h) +
						'L'+ (x+w*5) +','+ (y+h) +
						'z';
					var d2 = 
						'M'+ x +','+ y +
						'L'+ x +','+ y +
						'L'+ x +','+ (y+h) +
						'L'+ x +','+ (y+h) +
						'z'+
						'M'+ (x+w) +','+ (y) +
						'L'+ (x+w) +','+ (y) +
						'L'+ (x+w) +','+ (y+h) +
						'L'+ (x+w) +','+ (y+h) +
						'z'+
						'M'+ (x+w*2) +','+ (y) +
						'L'+ (x+w*2) +','+ (y) +
						'L'+ (x+w*2) +','+ (y+h) +
						'L'+ (x+w*2) +','+ (y+h) +
						'z'+
						'M'+ (x+w*3) +','+ (y) +
						'L'+ (x+w*3) +','+ (y) +
						'L'+ (x+w*3) +','+ (y+h) +
						'L'+ (x+w*3) +','+ (y+h) +
						'z'+
						'M'+ (x+w*4) +','+ (y) +
						'L'+ (x+w*4) +','+ (y) +
						'L'+ (x+w*4) +','+ (y+h) +
						'L'+ (x+w*4) +','+ (y+h) +
						'z'+
						'M'+ (x+w*5) +','+ (y) +
						'L'+ (x+w*5) +','+ (y) +
						'L'+ (x+w*5) +','+ (y+h) +
						'L'+ (x+w*5) +','+ (y+h) +
						'z';
					mask = svg.path(d2).attr('fill', '#fff');
					if (direction === 'in') {
						mask.attr({d: d2});
						animation = Snap.animation({d: d1}, duration, ease, _rM());
					} else {//out
						mask.attr({d: d1});
						animation = Snap.animation({d: d2}, duration, ease);
					};
					break;
				}
				case 'maskPanX': {
					mask = svg.path(
						'M'+ x +','+ y +
						'L'+ (x+w) +','+ y +
						'L'+ (x+w) +','+ (y+h) +
						'L'+ x +','+ (y+h) +
						'z'
					).attr('fill', '#fff');
					if (direction === 'in') {
						mask.attr({transform:'s0,1,'+ bb.cx +','+ bb.cy});
						animation = Snap.animation({transform: 's1,1,'+ bb.cx +','+ bb.cy}, duration, ease, _rM());
					} else {//out
						mask.attr({transform:'s1,1,'+ bb.cx +','+ bb.cy});
						animation = Snap.animation({transform: 's0,1,'+ bb.cx +','+ bb.cy}, duration, ease);
					};
					break;
				}
				case 'maskPanY': {
					mask = svg.path(
						'M'+ x +','+ y +
						'L'+ (x+w) +','+ y +
						'L'+ (x+w) +','+ (y+h) +
						'L'+ x +','+ (y+h) +
						'z'
					).attr('fill', '#fff');
					if (direction === 'in') {
						mask.attr({transform:'s1,0,'+ bb.cx +','+ bb.cy});
						animation = Snap.animation({transform: 's1,1,'+ bb.cx +','+ bb.cy}, duration, ease, _rM());
					} else {//out
						mask.attr({transform:'s1,1,'+ bb.cx +','+ bb.cy});
						animation = Snap.animation({transform: 's1,0,'+ bb.cx +','+ bb.cy}, duration, ease);
					};
					break;
				}
				case 'maskSlideT': {
					mask = svg.path(
						'M'+ x +','+ y +
						'L'+ (x+w) +','+ y +
						'L'+ (x+w) +','+ (y+h) +
						'L'+ x +','+ (y+h) +
						'z'
					).attr('fill', '#fff');
					if (direction === 'in') {
						mask.attr({transform:'t0,'+ -h});
						animation = Snap.animation({transform: 't0,0'}, duration, ease, _rM());
					} else {//out
						mask.attr({transform:'t0,0'});
						animation = Snap.animation({transform: 't0,'+ -h}, duration, ease);
					};
					break;
				}
				case 'maskSlideTR': {
					mask = svg.path(
						'M'+ x +','+ y +
						'L'+ (x+w) +','+ y +
						'L'+ (x+w) +','+ (y+h) +
						'L'+ x +','+ (y+h) +
						'z'
					).attr('fill', '#fff');
					if (direction === 'in') {
						mask.attr({transform:'t'+ w +','+ -h});
						animation = Snap.animation({transform: 't0,0'}, duration, ease, _rM());
					} else {//out
						mask.attr({transform:'t0,0'});
						animation = Snap.animation({transform: 't'+ w +','+ -h}, duration, ease);
					};
					break;
				}
				case 'maskSlideTL': {
					mask = svg.path(
						'M'+ x +','+ y +
						'L'+ (x+w) +','+ y +
						'L'+ (x+w) +','+ (y+h) +
						'L'+ x +','+ (y+h) +
						'z'
					).attr('fill', '#fff');
					if (direction === 'in') {
						mask.attr({transform:'t'+ -w +','+ -h});
						animation = Snap.animation({transform: 't0,0'}, duration, ease, _rM());
					} else {//out
						mask.attr({transform:'t0,0'});
						animation = Snap.animation({transform: 't'+ -w +','+ -h}, duration, ease);
					};
					break;
				}
				case 'maskSlideR': {
					mask = svg.path(
						'M'+ x +','+ y +
						'L'+ (x+w) +','+ y +
						'L'+ (x+w) +','+ (y+h) +
						'L'+ x +','+ (y+h) +
						'z'
					).attr('fill', '#fff');
					if (direction === 'in') {
						mask.attr({transform:'t'+ w +',0'});
						animation = Snap.animation({transform: 't0,0'}, duration, ease, _rM());
					} else {
						mask.attr({transform:'t0,0'});
						animation = Snap.animation({transform: 't'+ w +',0'}, duration, ease);
					};
					break;
				}
				case 'maskSlideBR': {
					mask = svg.path(
						'M'+ x +','+ y +
						'L'+ (x+w) +','+ y +
						'L'+ (x+w) +','+ (y+h) +
						'L'+ x +','+ (y+h) +
						'z'
					).attr('fill', '#fff');
					if (direction === 'in') {
						mask.attr({transform:'t'+ w +','+ h});
						animation = Snap.animation({transform: 't0,0'}, duration, ease, _rM());
					} else {
						mask.attr({transform:'t0,0'});
						animation = Snap.animation({transform: 't'+ w +','+ h}, duration, ease);
					};
					break;
				}
				case 'maskSlideB': {
					mask = svg.path(
						'M'+ x +','+ y +
						'L'+ (x+w) +','+ y +
						'L'+ (x+w) +','+ (y+h) +
						'L'+ x +','+ (y+h) +
						'z'
					).attr('fill', '#fff');
					if (direction === 'in') {
						mask.attr({transform:'t0,'+ h});
						animation = Snap.animation({transform: 't0,0'}, duration, ease, _rM());
					} else {//out
						mask.attr({transform:'t0,0'});
						animation = Snap.animation({transform: 't0,'+ h}, duration, ease);
					};
					break;
				}
				case 'maskSlideBL': {
					mask = svg.path(
						'M'+ x +','+ y +
						'L'+ (x+w) +','+ y +
						'L'+ (x+w) +','+ (y+h) +
						'L'+ x +','+ (y+h) +
						'z'
					).attr('fill', '#fff');
					if (direction === 'in') {
						mask.attr({transform:'t'+ -w +','+ h});
						animation = Snap.animation({transform: 't0,0'}, duration, ease, _rM());
					} else {//out
						mask.attr({transform:'t0,0'});
						animation = Snap.animation({transform: 't'+ -w +','+ h}, duration, ease);
					};
					break;
				}
				case 'maskSlideL': {
					mask = svg.path(
						'M'+ x +','+ y +
						'L'+ (x+w) +','+ y +
						'L'+ (x+w) +','+ (y+h) +
						'L'+ x +','+ (y+h) +
						'z'
					).attr('fill', '#fff');
					if (direction === 'in') {
						mask.attr({transform:'t'+ -w +',0'});
						animation = Snap.animation({transform: 't0,0'}, duration, ease, _rM());
					} else {//out
						mask.attr({transform:'t0,0'});
						animation = Snap.animation({transform: 't'+ -w +',0'}, duration, ease);
					};
					break;
				}
				case 'maskGradL': {
					var grad = svg.gradient('l(0, 0, 1, 0)#fff-#fff:34-#000:60-#000:100');
					grad.addClass('elvn-gradient');
					mask = svg.path(
						'M'+ x +','+ y +
						'L'+ (x+w*3) +','+ y +
						'L'+ (x+w*3) +','+ (y+h) +
						'L'+ x +','+ (y+h) +
						'z'
					).attr({fill: grad});
					if (direction === 'in') {
						mask.attr({transform:'t'+ -w*2 +',0'});
						animation = Snap.animation({transform: 't0,0'}, duration, ease, _rM());
					} else {
						mask.attr({transform:'t0,0'});
						animation = Snap.animation({transform: 't'+ -w*2 +',0'}, duration, ease);
					};
					break;
				}
				case 'maskGradT': {
					var grad = svg.gradient('l(0, 0, 0, 1)#fff-#fff:34-#000:60-#000:100');
					grad.addClass('elvn-gradient');
					mask = svg.path(
						'M'+ x +','+ y +
						'L'+ (x+w) +','+ y +
						'L'+ (x+w) +','+ (y+h*3) +
						'L'+ x +','+ (y+h*3) +
						'z'
					).attr({fill: grad});
					if (direction === 'in') {
						mask.attr({transform:'t0,'+ -h*2});
						animation = Snap.animation({transform: 't0,0'}, duration, ease, _rM());
					} else {
						mask.attr({transform:'t0,0'});
						animation = Snap.animation({transform: 't0,'+ -h*2}, duration, ease);
					};
					break;
				}
				case 'maskGradR': {
					var grad = svg.gradient('l(0, 0, 1, 0)#000-#000:34-#fff:60-#fff:100');
					grad.addClass('elvn-gradient');
					mask = svg.path(
						'M'+ x +','+ y +
						'L'+ (x+w*3) +','+ y +
						'L'+ (x+w*3) +','+ (y+h) +
						'L'+ x +','+ (y+h) +
						'z'
					).attr({fill: grad});
					if (direction === 'in') {
						mask.attr({transform:'t0,0'});
						animation = Snap.animation({transform: 't'+ -w*2 +',0'}, duration, ease, _rM());
					} else {//out
						mask.attr({transform:'t'+ -w*2 +',0'});
						animation = Snap.animation({transform: 't0,0'}, duration, ease);
					};
					break;
				}
				case 'maskGradB': {
					var grad = svg.gradient('l(0, 0, 0, 1)#000-#000:34-#fff:60-#fff:100');
					grad.addClass('elvn-gradient');
					mask = svg.path(
						'M'+ x +','+ y +
						'L'+ (x+w) +','+ y +
						'L'+ (x+w) +','+ (y+h*3) +
						'L'+ x +','+ (y+h*3) +
						'z'
					).attr({fill: grad});
					if (direction === 'in') {
						mask.attr({transform: 't0,0'});
						animation = Snap.animation({transform: 't0,'+ -h*2}, duration, ease, _rM());
					} else {
						mask.attr({transform: 't0,'+ -h*2});
						animation = Snap.animation({transform: 't0,0'}, duration, ease);
					};
					break;
				}
				default: {//maskRect
					mask = svg.rect(x, y, w, h);
					mask.attr({fill:"#fff"});
					if (direction === 'in') {
						mask.attr({transform: 's0,0'});
						animation = Snap.animation({transform:'s1,1'}, duration, ease, _rM());
					} else {//out
						mask.attr({transform:'s1,1'});
						animation = Snap.animation({transform: 's0,0'}, duration, ease);
					};
					break;
				}
			};

			g.insertBefore(wrap_g).add(mask);
			var m = svg.mask();
			m.add(g).toDefs();
			wrap_g.attr({mask:m});
			Snap.select('#'+ m.id).addClass('elvn-mask');

			var tmp = setTimeout(function() {
				mask.animate(animation);
			}, delay);
			timeouts.push(tmp);
			
		} else if (options && options.match(/gaussBlur/)) {//gaussian blur
			//for ex.: class="elvn-layer" data-elvn="gaussBlur, in, 0, 1000, 20"
			
			options = options.replace(/\s+/g, '').split(',');
			var direction = options[1],
				delay = +options[2] + globalDelay,
				duration = +options[3],
				std = options[4],
				defs = svg.select('defs'),
				cur_iniq = uniqueNum(),
				tmp,
				filter = Snap.parse('<filter id="elvn-gauss'+ cur_iniq +'" filterUnits="userSpaceOnUse" class="elvn-filter" x="-50%" y="-50%" width="300%" height="300%"><feGaussianBlur id="elvn-stdev'+ cur_iniq +'" in="SourceGraphic" stdDeviation="'+ std +'"/></filter>');
			if (instant) {
				delay = 0;
			};
			defs.append(filter);
			var filter = svg.select('#elvn-gauss' + cur_iniq);
			var cur_g = elem.parent();
			if (cur_g.hasClass('elvn-wrapper')) {
				var g = cur_g;
			} else {
				var g = svg.g();
				g.insertBefore(elem).add(elem).addClass('elvn-wrapper');
			};
			g.attr('filter', filter);
			var stdDev = svg.select('#elvn-stdev' + cur_iniq);
			if (direction === 'in') {
				stdDev.attr({stdDeviation: std});
				g.attr({opacity: 0});
				tmp = setTimeout(function() {
					g.animate({opacity: 1}, duration*0.2, mina.linear, function () {
						stdDev.animate({stdDeviation: 0}, duration*0.8, mina.linear, function () {
							g.attr({filter: ''});
						});
					});

				}, delay);
				timeouts.push(tmp);
			} else {
				stdDev.attr({stdDeviation: 0});
				g.attr({opacity: 1});
				tmp = setTimeout(function() {
					stdDev.animate({stdDeviation: std}, duration*0.8, mina.linear, function () {
						g.animate({opacity: 0}, duration*0.2, mina.linear, function () {
							g.attr({filter: ''});
						});
					});
				}, delay);
				timeouts.push(tmp);
			};

		} else if (options && options.match(/custom/)) {//custom transform and opacity
			//when "custom" animation is used, the second data attribute "data-elvncustom" must be added with transform string in Snap format
			//for ex.: class="elvn-layer" data-elvn="custom, in, 0, 1000, 100, easeinout" data-elvncustom="t100,0s2,2r-270"
			
			options = options.replace(/\s+/g, '').split(',');
			var direction = options[1],
				delay = +options[2] + globalDelay,
				duration = +options[3],
				opacity = +options[4],
				easing = options[5],
				tmp;
			if (opacity > 1) {
				opacity = opacity/100;
			};
			//case of easing func
			switch (easing) {
				case 'backin': {
					var ease = mina.backin;
					break;
				}
				case 'backout': {
					var ease = mina.backout;
					break;
				}
				case 'bounce': {
					var ease = mina.bounce;
					break;
				}
				case 'easein': {
					var ease = mina.easein;
					break;
				}
				case 'easeinout': {
					var ease = mina.easeinout;
					break;
				}
				case 'easeout': {
					var ease = mina.easeout;
					break;
				}
				case 'elastic': {
					var ease = mina.elastic;
					break;
				}
				default: { //linear
					var ease = mina.linear;
					break;
				}
			};

			if (instant) {
				delay = 0;
			};

			var cur_g = elem.parent();
			if (cur_g.hasClass('elvn-wrapper')) {
				var g = cur_g;
			} else {
				var g = svg.g();
				g.insertBefore(elem).add(elem).addClass('elvn-wrapper');
			};
			
			if (direction === 'in') {
				g.attr({transform: custom_command, opacity: opacity});
				tmp = setTimeout(function() {
					g.animate({transform: '', opacity: 1}, duration, ease);
				}, delay);
				timeouts.push(tmp);
			} else {
				g.attr({transform: '', opacity: 1});
				tmp = setTimeout(function() {
					g.animate({transform: custom_command, opacity: opacity}, duration, ease);
				}, delay);
				timeouts.push(tmp);
			};

		} else if (options && options.match(/morph/)) {//morphing effect for "path" elements (new from ver. 1.1)

			//when "morph" animation is used, the second data attribute "data-elvnmorph" must be added
			//with the value of "d" attribute of <path> tag is a starting (if "in") or ending (if "out") path data

			//for ex.: class="elvn-layer" data-elvn="morph, in, 0, 1000, easeinout" data-elvnmorph="M171.207611,110.342499c39.094391,-97.125 192.262817,0 0,124.875c-192.263035,-124.875 -39.094742,-222 0,-124.875z"

			options = options.replace(/\s+/g, '').split(',');
			var direction = options[1],
				delay = +options[2] + globalDelay,
				duration = +options[3],
				easing = options[4],
				tmp;
			
			//case of easing func
			switch (easing) {
				case 'backin': {
					var ease = mina.backin;
					break;
				}
				case 'backout': {
					var ease = mina.backout;
					break;
				}
				case 'bounce': {
					var ease = mina.bounce;
					break;
				}
				case 'easein': {
					var ease = mina.easein;
					break;
				}
				case 'easeinout': {
					var ease = mina.easeinout;
					break;
				}
				case 'easeout': {
					var ease = mina.easeout;
					break;
				}
				case 'elastic': {
					var ease = mina.elastic;
					break;
				}
				default: { //linear
					var ease = mina.linear;
					break;
				}
			};

			if (instant) {
				delay = 0;
			};

			if(elem.type.toLowerCase() === 'path') {
				var cur_opacity = elem.attr('opacity');
				elem.attr('data-elvnopacity', cur_opacity);
				var clon = elem.clone();
				elem.attr('opacity',0);
				clon.attr({'data-elvn': '', 'data-elvnopacity': '', 'data-elvnmorph': ''})
					.removeClass('elvn-layer')
					.addClass('elvn-clon');
				
				if (direction === 'in') {
					var cur_d = elem.attr('d');
					clon.attr({d: morph_command});
					tmp = setTimeout(function() {
						clon.animate({d: cur_d}, duration, ease);
					}, delay);
					timeouts.push(tmp);
				} else {
					tmp = setTimeout(function() {
						clon.animate({d: morph_command}, duration, ease);
					}, delay);
					timeouts.push(tmp);
				};
			};

		};//end of all "if else"

	};//end enlivenEm

	//data for various animations
	function getData (elem, effect, svg_w, svg_h, bb) {
		var tl = bb.x +','+ bb.y,//top-left
			t = bb.cx +','+ bb.y,//top
			tr = bb.x2 +','+ bb.y,//top-right
			r = bb.x2 +','+ bb.cy,//right
			br = bb.x2 +','+ bb.y2,//bottom-right
			b = bb.cx +','+ bb.y2,//bottom
			bl = bb.x +','+ bb.y2,//bottom-left
			l = bb.x +','+ bb.cy,//left
			c = bb.cx +','+ bb.cy,//center
			w = bb.w,//width of elem
			h = bb.h;//height of elem
		var animdata = JSON.stringify({
			expandT:[//top
				{
					dI:0, //dI is duration in (% of total)
					dO:100, //dO is duration out (% of total)
					a:{t:'s1,0,'+ t} //a is attrs, t: is transform, o is opacity
				},
				{
					dI:100, //dI is duration in (% of total)
					dO:0, //dO is duration out (% of total)
					a:{t:'s1,1,'+ t} //a is attrs, t: is transform, o is opacity
				}
			],
			expandR:[//right
				{
					dI:0,
					dO:100,
					a:{t:'s0,1,'+ r}
				},
				{
					dI:100, 
					dO:0, 
					a:{t:'s1,1,'+ r}
				}
			],
			expandB:[//bottom
				{
					dI:0,
					dO:100,
					a:{t:'s1,0,'+ b}
				},
				{
					dI:100, 
					dO:0, 
					a:{t:'s1,1,'+ b}
				}
			],
			expandL:[//left
				{
					dI:0,
					dO:100,
					a:{t:'s0,1,'+ l}
				},
				{
					dI:100, 
					dO:0, 
					a:{t:'s1,1,'+ l}
				}
			],
			expandY:[//vertical
				{
					dI:0,
					dO:100,
					a:{t:'s1,0,'+ l}
				},
				{
					dI:100, 
					dO:0, 
					a:{t:'s1,1,'+ l}
				}
			],
			expandX:[//horizontal
				{
					dI:0,
					dO:100,
					a:{t:'s0,1,'+ t}
				},
				{
					dI:100, 
					dO:0, 
					a:{t:'s1,1,'+ t}
				}
			],
			fade:[
				{
					dI:0, 
					dO:100,
					a:{o:0}
				},
				{
					dI:100,
					dO:0,
					a:{o:1}
				}
			],
			fadeShortTL:[//top-left
				{
					dI:0,
					dO:100,
					a:{t:'t'+ -(w/4<20 ? 20: w/4) +','+ -(h/4<20 ? 20: h/4), o:0}
				},
				{
					dI:100, 
					dO:0, 
					a:{t: '', o:1}
				}
			],
			fadeShortT:[//top
				{
					dI:0,
					dO:100,
					a:{t:'t0,'+ -(h/4<20 ? 20: h/4), o:0}
				},
				{
					dI:100, 
					dO:0, 
					a:{t: '', o:1}
				}
			],
			fadeShortTR:[//top-right
				{
					dI:0,
					dO:100,
					a:{t:'t'+ (w/4<20 ? 20: w/4) +','+ -(h/4<20 ? 20: h/4), o:0}
				},
				{
					dI:100, 
					dO:0, 
					a:{t: '', o:1}
				}
			],
			fadeShortR:[//right
				{
					dI:0,
					dO:100,
					a:{t:'t'+ (w/4<20 ? 20: w/4) +',0', o:0}
				},
				{
					dI:100, 
					dO:0, 
					a:{t: '', o:1}
				}
			],
			fadeShortBR:[//bottom-right
				{
					dI:0,
					dO:100,
					a:{t:'t'+ (w/4<20 ? 20: w/4) +','+ (h/4<20 ? 20: h/4), o:0}
				},
				{
					dI:100, 
					dO:0, 
					a:{t: '', o:1}
				}
			],
			fadeShortB:[//bottom
				{
					dI:0,
					dO:100,
					a:{t:'t0,'+ (h/4<20 ? 20: h/4), o:0}
				},
				{
					dI:100, 
					dO:0, 
					a:{t: '', o:1}
				}
			],
			fadeShortBL:[//bottom-left
				{
					dI:0,
					dO:100,
					a:{t:'t'+ -(w/4<20 ? 20: w/4) +','+ (h/4<20 ? 20: h/4), o:0}
				},
				{
					dI:100, 
					dO:0, 
					a:{t: '', o:1}
				}
			],
			fadeShortL:[//left
				{
					dI:0,
					dO:100,
					a:{t:'t'+ -(w/4<20 ? 20: w/4) +',0', o:0}
				},
				{
					dI:100, 
					dO:0, 
					a:{t: '', o:1}
				}
			],
			fadeLongTL:[
				{
					dI:0,
					dO:100,
					a:{t:'t'+ -bb.x2 +','+ -bb.y2, o:0}
				},
				{
					dI:100, 
					dO:0, 
					a:{t: '', o:1}
				}
			],
			fadeLongT:[
				{
					dI:0,
					dO:100,
					a:{t:'t0,'+ -bb.y2, o:0}
				},
				{
					dI:100, 
					dO:0, 
					a:{t: '', o:1}
				}
			],
			fadeLongTR:[
				{
					dI:0,
					dO:100,
					a:{t:'t'+ (svg_w-bb.x) +','+ -bb.y2, o:0}
				},
				{
					dI:100, 
					dO:0, 
					a:{t: '', o:1}
				}
			],
			fadeLongR:[
				{
					dI:0,
					dO:100,
					a:{t:'t'+ (svg_w-bb.x) +',0', o:0}
				},
				{
					dI:100, 
					dO:0, 
					a:{t: '', o:1}
				}
			],
			fadeLongBR:[
				{
					dI:0,
					dO:100,
					a:{t:'t'+ (svg_w-bb.x) +','+ (svg_h-bb.y), o:0}
				},
				{
					dI:100, 
					dO:0, 
					a:{t: '', o:1}
				}
			],
			fadeLongB:[
				{
					dI:0,
					dO:100,
					a:{t:'t0,'+ (svg_w-bb.y), o:0}
				},
				{
					dI:100, 
					dO:0, 
					a:{t: '', o:1}
				}
			],
			fadeLongBL:[
				{
					dI:0,
					dO:100,
					a:{t:'t'+ -bb.x2 +','+ (svg_h-bb.y), o:0}
				},
				{
					dI:100, 
					dO:0, 
					a:{t: '', o:1}
				}
			],
			fadeLongL:[
				{
					dI:0,
					dO:100,
					a:{t:'t'+ -bb.x2 +',0', o:0}
				},
				{
					dI:100, 
					dO:0, 
					a:{t: '', o:1}
				}
			],
			flipX:[
				{
					dI:0,
					dO:100,
					a:{t:'s-1,1.3', o:0}
				},
				{
					dI:100, 
					dO:0, 
					a:{t: 's1,1', o:1}
				}
			],
			flipY:[
				{
					dI:0,
					dO:100,
					a:{t:'s1.3,-1', o:0}
				},
				{
					dI:100, 
					dO:0, 
					a:{t: 's1,1', o:1}
				}
			],
			flyC:[
				{
					dI:0,
					dO:100,
					a:{t: 't'+ (svg_w/2-bb.cx) +','+ (svg_h/2-bb.cy), o:0}
				},
				{
					dI:100, 
					dO:0, 
					a:{t: '', o:1}
				}
			],
			flyRotateC:[
				{
					dI:0,
					dO:100,
					a:{t: 't'+ (svg_w/2-bb.cx) +','+ (svg_h/2-bb.cy) +'r0', o:0}
				},
				{
					dI:100, 
					dO:0, 
					a:{t: 't0,0r360', o:1}
				}
			],
			flyScaleC:[
				{
					dI:0,
					dO:100,
					a:{t: 't'+ (svg_w/2-bb.cx) +','+ (svg_h/2-bb.cy) +'s2,2', o:0}
				},
				{
					dI:100, 
					dO:0, 
					a:{t: '', o:1}
				}
			],
			pulse:[
				{
					dI:0,
					dO:30,
					a:{t:'s0,0', o:0}
				},
				{
					dI:30,
					dO:10,
					aI:{t:'s0.8,0.8', o:0.8},
					aO:{t:'s0.8,0.8', o:0.8}
				},
				{
					dI:10,
					dO:20,
					aI:{t:'s0.5,0.5', o:0.6},
					aO:{t:'s0.5,0.5', o:0.6}
				},
				{
					dI:20,
					dO:10,
					aI:{t:'s1,1', o:1},
					aO:{t:'s1,1', o:1}
				},
				{
					dI:10,
					dO:20,
					aI:{t:'s0.7,0.7', o:0.8},
					aO:{t:'s0.7,0.7', o:0.8}
				},
				{
					dI:20,
					dO:10,
					aI:{t:'s1.2,1.2', o:1},
					aO:{t:'s1.2,1.2', o:1}
				},
				{
					dI:10,
					dO:0, 
					a:{t:'s1,1', o:1}
				}
			],
			rubberX:[
				{
					dI:0,
					dO:100,
					a:{t:'s2,0.62', o:0}
				},
				{
					dI:100, 
					dO:0, 
					a:{t: 's1,1', o:1}
				}
			],
			rubberY:[
				{
					dI:0,
					dO:100,
					a:{t:'s0.62,2', o:0}
				},
				{
					dI:100, 
					dO:0, 
					a:{t: 's1,1', o:1}
				}
			],
			scaleTL:[
				{
					dI:0,
					dO:100,
					a:{t:'s0,0,'+ tl}
				},
				{
					dI:100, 
					dO:0, 
					a:{t:'s1,1,'+ tl}
				}
			],
			scaleT:[
				{
					dI:0,
					dO:100,
					a:{t:'s0,0,'+ t}
				},
				{
					dI:100, 
					dO:0, 
					a:{t:'s1,1,'+ t}
				}
			],
			scaleTR:[
				{
					dI:0,
					dO:100,
					a:{t:'s0,0,'+ tr}
				},
				{
					dI:100, 
					dO:0, 
					a:{t:'s1,1,'+ tr}
				}
			],
			scaleR:[
				{
					dI:0,
					dO:100,
					a:{t:'s0,0,'+ r}
				},
				{
					dI:100, 
					dO:0, 
					a:{t:'s1,1,'+ r}
				}
			],
			scaleBR:[
				{
					dI:0,
					dO:100,
					a:{t:'s0,0,'+ br}
				},
				{
					dI:100, 
					dO:0, 
					a:{t:'s1,1,'+ br}
				}
			],
			scaleB:[
				{
					dI:0,
					dO:100,
					a:{t:'s0,0,'+ b}
				},
				{
					dI:100, 
					dO:0, 
					a:{t:'s1,1,'+ b}
				}
			],
			scaleBL:[
				{
					dI:0,
					dO:100,
					a:{t:'s0,0,'+ bl}
				},
				{
					dI:100, 
					dO:0, 
					a:{t:'s1,1,'+ bl}
				}
			],
			scaleL:[
				{
					dI:0,
					dO:100,
					a:{t:'s0,0,'+ l}
				},
				{
					dI:100, 
					dO:0, 
					a:{t:'s1,1,'+ l}
				}
			],
			scaleC:[
				{
					dI:0,
					dO:100,
					a:{t:'s0,0,'+ c}
				},
				{
					dI:100, 
					dO:0, 
					a:{t:'s1,1,'+ c}
				}
			],
			stamp:[
				{
					dI:0,
					dO:70,
					a:{t:'s3,3', o:0}
				},
				{
					dI:70,
					dO:30,
					aI:{t:'s0.7,0.7', o:0.3},
					aO:{t:'s0.7,0.7', o:0.3}
				},
				{
					dI:30, 
					dO:0, 
					a:{t: 's1,1', o:1}
				}
			],
			wheelC:[
				{
					dI:0,
					dO:100,
					a:{t:'t0,0r0', o:0}
				},
				{
					dI:100, 
					dO:0, 
					a:{t: 't0,0r720', o:1}
				}
			],
			wheelL:[
				{
					dI:0,
					dO:100,
					a:{t:'t'+ -bb.x2 +',0r0', o:0}
				},
				{
					dI:100, 
					dO:0, 
					a:{t: 't0,0r720', o:1}
				}
			],
			wheelR:[
				{
					dI:0,
					dO:100,
					a:{t:'t'+(svg_w-bb.x) +',0r0', o:0}
				},
				{
					dI:100, 
					dO:0, 
					a:{t: 't0,0r-720', o:1}
				}
			],
			wheelB:[
				{
					dI:0,
					dO:100,
					a:{t:'t0,'+ (svg_w-bb.y) +'r0', o:0}
				},
				{
					dI:100, 
					dO:0, 
					a:{t: 't0,0r-720', o:1}
				}
			],
			wheelT:[
				{
					dI:0,
					dO:100,
					a:{t:'t0,'+ -bb.y2 +'r0', o:0}
				},
				{
					dI:100, 
					dO:0, 
					a:{t: 't0,0r720', o:1}
				}
			]
		});//end JSON.stringify
		
		//replace all short names in object to their full names
		animdata = animdata.replace(/\"o\":/g, "\"opacity\":").replace(/\"t\":/g, "\"transform\":");
		
		//restore object from json format
		animdata = JSON.parse(animdata);
		
		if (effect in animdata) {
			return clone(animdata[effect]);
		} else {
			return clone(animdata['fadeShortL']);
		};
	};

	//HELPER FUNCTIONS
	//random integer from range
	function getRandomInt (min, max) {
		return Math.floor(Math.random() * (max - min + 1)) + min;
	};
	//unique number from 1
	uniqueNum.counter = 1;
	function uniqueNum () {
		return uniqueNum.counter++;
	};
	// function for cloning object
	function clone(obj){
		if(obj == null || typeof(obj) != "object") {
		return obj;
		};
		var temp = new obj.constructor();
		for(var key in obj) {
		temp[key] = clone(obj[key]);
		};
		return temp;
	};
	//cloning reversed array
	function reverseArr(a) {
		var temp = [],
			len = a.length;
		for (var i = len - 1; i >= 0; i--) {
				temp.push(a[i]);
		}
		return temp;
	};

})();//end call of anonymous func


//when DOM is ready hide all <svg> or <img> with class .enlivenem
jQuery(document).ready(function() {
	jQuery('.enlivenem').css('visibility', 'hidden');
});
//when document is fully loaded animate <svg> or <img> with class .enlivenem
jQuery(window).on('load', function() {
	jQuery('.enlivenem').enlivenEm();
});

/*!
 * verge 1.9.1+201402130803
 * https://github.com/ryanve/verge
 * MIT License 2013 Ryan Van Etten
 */
!function(a,b,c){"undefined"!=typeof module&&module.exports?module.exports=c():a[b]=c()}(this,"verge",function(){function a(){return{width:k(),height:l()}}function b(a,b){var c={};return b=+b||0,c.width=(c.right=a.right+b)-(c.left=a.left-b),c.height=(c.bottom=a.bottom+b)-(c.top=a.top-b),c}function c(a,c){return a=a&&!a.nodeType?a[0]:a,a&&1===a.nodeType?b(a.getBoundingClientRect(),c):!1}function d(b){b=null==b?a():1===b.nodeType?c(b):b;var d=b.height,e=b.width;return d="function"==typeof d?d.call(b):d,e="function"==typeof e?e.call(b):e,e/d}var e={},f="undefined"!=typeof window&&window,g="undefined"!=typeof document&&document,h=g&&g.documentElement,i=f.matchMedia||f.msMatchMedia,j=i?function(a){return!!i.call(f,a).matches}:function(){return!1},k=e.viewportW=function(){var a=h.clientWidth,b=f.innerWidth;return b>a?b:a},l=e.viewportH=function(){var a=h.clientHeight,b=f.innerHeight;return b>a?b:a};return e.mq=j,e.matchMedia=i?function(){return i.apply(f,arguments)}:function(){return{}},e.viewport=a,e.scrollX=function(){return f.pageXOffset||h.scrollLeft},e.scrollY=function(){return f.pageYOffset||h.scrollTop},e.rectangle=c,e.aspect=d,e.inX=function(a,b){var d=c(a,b);return!!d&&d.right>=0&&d.left<=k()},e.inY=function(a,b){var d=c(a,b);return!!d&&d.bottom>=0&&d.top<=l()},e.inViewport=function(a,b){var d=c(a,b);return!!d&&d.bottom>=0&&d.right>=0&&d.top<=l()&&d.left<=k()},e});