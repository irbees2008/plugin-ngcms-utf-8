/**
 * mangguo.js
 * @author tinyhill@163.com
 */
(function () {

	/**
	 * е®љд№‰е…Ёе±Ђе‘ЅеђЌз©єй—ґ
	 * @type {Object} mangguo
	 */
	var mangguo = {

		/**
		 * иЅ®ж’­е€‡жЌў
		 * @method slide
		 */
		slide: function () {

			// ењ†з‚№е€‡жЌў
			$('.slide-nav li').click(function (e) {

				var idx = $(e.currentTarget).text();

				$('.slide-content li').each(function (k, v) {
					if ((k + 1) == idx) {
						$(v).fadeIn();
					} else {
						$(v).hide();
					}
				});

			});

			// зї»йЎµе€‡жЌў
			$('#slide .prev, #slide .next').click(function (e) {

				var trigger = $(e.currentTarget),
					target = $('.slide-content li:visible');

				// йљђи—ЏеЅ“е‰ЌйЎµ
				target.hide();

				// е‰Ќзї»йЎµ
				if (trigger.hasClass('prev')) {

					var prev = target.prev();

					if (prev[0]) {
						prev.fadeIn();
					} else {
						$('.slide-content li:last').fadeIn();
					}
				}

				// еђЋзї»йЎµ
				if (trigger.hasClass('next')) {

					var next = target.next();

					if (next[0]) {
						next.fadeIn();
					} else {
						$('.slide-content li:first').fadeIn();
					}
				}

			});

			// и‡ЄеЉЁж’­ж”ѕ
			var duration = 5000,
				timeout = setTimeout(autoplay, duration);

			function autoplay () {
				$('#slide .next').trigger('click');
				timeout = setTimeout(autoplay, duration);
			}

			$('#slide').hover(
				function () {
					clearTimeout(timeout);
				},
				function () {
					timeout = setTimeout(autoplay, duration);
				}
			);

		},

		/**
		 * ж”¶зј©е±•ејЂ
		 * @method slideToggle
		 */
		slideToggle: function () {

			$('#slide-toggle .toggle').click(function (e) {

				var trigger = $(e.currentTarget);

				if (trigger.hasClass('toggle-mini')) {

					trigger.text('е€‡жЌўе€°зІѕз®ЂжЁЎејЏ');
					trigger.removeClass('toggle-mini');

					$('#slide').animate({
						height: '90px'
					}, 'fast', function () {
						$(this).removeClass('slide-mini');
					});

					$.cookie('slide_mini', null);

				} else {

					trigger.text('е€‡жЌўе€°е®Њж•ґжЁЎејЏ');
					trigger.addClass('toggle-mini');

					$('#slide').animate({
						height: '45px'
					}, 'fast', function () {
						$(this).addClass('slide-mini');
					});

					// е°†жЁЎејЏе†™е…Ґ cookie еЂј
					$.cookie('slide_mini', true);

				}

			});

		},

		/**
		 * е›ће€°йЎ¶йѓЁ
		 * @method scrollTop
		 */
		scrollTop: function () {

			var target = $('<div id="scroll-top" class="scroll-top">&uarr;</div>'),
				target = target.appendTo($('body'));

			// жЈЂжџҐж»љеЉЁе·®еЂј
			function checkTop () {

				if ($(window).scrollTop() > 200) {
					target.fadeIn(250);
				} else {
					target.fadeOut(500);
				}

			}

			checkTop();

			$(window).scroll(checkTop);

			target.click(function () {
				$('body, html').animate({scrollTop: 0}, 1000);
			});

		},

		/**
		 * иЇ„и®єе›ћеє”
		 * @method reply
		 */
		replyTo: function () {

			$('.comment-list dl').delegate('.reply-to', 'click', function (e) {

				var author = $(this).find('em:first-child').text(),
					text = 'е›ћеє” ' + author + ' зљ„еЏ‘иЁЂ';

				$('#comment-form').prev('h3').find('span').text(text);
				$('#comment').select();
				$('#comment_parent').val($(this).attr('rel'));

				$('html, body').animate({
					scrollTop: $('#comment').offset().top
				}, 'slow');

				return false;

			});

			$('.replied').click(function () {
				$('html, body').animate({
					scrollTop: $($(this).attr('href')).offset().top
				}, 'slow');
			});

		},

		/**
		 * дї®ж”№з”Ёж€·иµ„ж–™
		 * @method authorToggle
		 */
		authorToggle: function () {

			$('#author-toggle').toggle(

				function () {
					$('.comment-author').show('fast');
				}, function () {
					$('.comment-author').hide('fast');
				}

			);

		},

		/**
		 * е€ќе§‹еЊ–е…ҐеЏЈ
		 * @method init
		 */
		init: function () {

			// е€ќе§‹еЊ–иЅ®ж’­е€‡жЌў
			this.slide();

			// е€ќе§‹еЊ–ж”¶зј©е±•ејЂ
			this.slideToggle();

			// е€ќе§‹еЊ–е›ће€°йЎ¶йѓЁ
			this.scrollTop();

			// е€ќе§‹еЊ–иЇ„и®єе›ћеє”
			this.replyTo();

			// е€ќе§‹еЊ–дї®ж”№иµ„ж–™
			this.authorToggle();

		}

	};

	// ж‰§иЎЊе€ќе§‹еЊ–
	mangguo.init();

})();
