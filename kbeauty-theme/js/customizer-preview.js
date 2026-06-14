/**
 * Customizer live-preview bindings — notice bar and trust signals update
 * without a full page reload (transport: postMessage).
 */
(function ($) {
	'use strict';

	var noticeSelectors = {
		glow_notice_1: '.notice-bar .container span:nth-child(1)',
		glow_notice_2: '.notice-bar .container span.notice-extra:nth-of-type(1)',
		glow_notice_3: '.notice-bar .container span.notice-extra:last-of-type',
	};

	Object.keys(noticeSelectors).forEach(function (key) {
		wp.customize(key, function (value) {
			value.bind(function (newVal) {
				$(noticeSelectors[key]).text(newVal);
			});
		});
	});

	wp.customize('glow_trust_1', function (v) { v.bind(function (val) { $('.hero-footnote span:nth-child(1)').text(val); }); });
	wp.customize('glow_trust_2', function (v) { v.bind(function (val) { $('.hero-footnote span:nth-child(2)').text(val); }); });
	wp.customize('glow_trust_3', function (v) { v.bind(function (val) { $('.hero-footnote span:nth-child(3)').text(val); }); });

}(jQuery));
