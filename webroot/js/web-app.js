var WebApp = {
	init: function () {
		if (!WebApp.blocksExist()) {
			console.log('[WebApp] No blocks defined. Stopping the initialization');

			return;
		}

		//$("body").append('<div id="web-page-loads" style="position: absolute; top: 1em; right: 1em; width: 1.5em; height: 1.5em; font-size: 100px;">0</div>');

		WebApp.ProgressBar.init();

		(function addXhrProgressEvent($) {
			var originalXhr = $.ajaxSettings.xhr;
			$.ajaxSetup({
				progress: function() { console.log("standard progress callback"); },
				xhr: function() {
					var req = originalXhr(), that = this;
					if (req) {
						if (typeof req.addEventListener == "function") {
							req.addEventListener("progress", function(evt) {
								that.progress(evt);
							},false);
						}
					}
					return req;
				}
			});
		})(jQuery);

		WebApp.registerClickHandler($('html'));

		setInterval(function () {
			var links = $('a');
			var randomNum = Math.floor(Math.random()*links.length)
			//links.get(randomNum).click();
		}, 1000);

		var appCache = window.applicationCache;

		switch (appCache.status) {
			case appCache.IDLE:

				console.log("Page loaded using application cache. Fetching WebApp data from server");
				//this.loadPage(location.href);

				break;
		}
	},
	ProgressBar: {
		_percentage: 0,
		init: function () {
			//NProgress.configure({
//				trickle: false
			//});
		},
		begin: function () {
			this._percentage = 0;

			console.log("[ProgressBar] Begin!");

			NProgress.start();
		},
		end: function () {
			this._percentage = 100;

			console.log("[ProgressBar] End!");

			NProgress.done();
		},
		setProgress: function (percentage) {
			this._percentage = percentage;

			console.log("[ProgressBar] Set percentage: " + parseInt(percentage, 10) + "%");

//			NProgress.set(percentage / 100);
		},
		increase: function (percentage) {
			this._percentage += percentage;

			console.log("[ProgressBar] Increase percentage: " + percentage + " total: " + this._percentage);

//			NProgress.inc(percentage / 100);
		}
	},
	settings: {
		titleStructure: '[title] - WebApp'
	},
	stats: {
		pageLoads: 0,
		lastHistoryPush: 0
	},
	previousUrl: location.href,
	blocksExist: function () {
		return $('*[data-block]').length != 0;
	},
	registerClickHandler: function (selector) {
		return;
		$('a[target!="_blank"]', selector).filter(function () {
			if ($(this).attr('href') === undefined) {
				return false;
			}

			return true;
		}).click(WebApp.clickHandler);

		$('form.web-app-form', selector).submit(function (event) {
			var href = $(this).attr('action') + '?' + $(this).serialize();

			WebApp.loadPage(href, {
				ignoredBlocks: [
					$(this).closest('*[data-block]').data('block')
				],
				success: function (href) {
					WebApp.stats.lastHistoryPush = (new Date).getTime();
					History.pushState({href: href}, $('title').text(), href);
				}
			});

			return false;
		});

		$('.web-app-form .web-app-change-submit', selector).change(function () {
			var form = $(this).closest('form');
			setTimeout(function () {
				form.trigger('submit');
			}, 100);
		});
		$('.web-app-form .web-app-change-submit', selector).keyup(function () {
			var form = $(this).closest('form');
			setTimeout(function () {
				form.trigger('submit');
			}, 100);
		});
	},
	clickHandler: function() {
		WebApp.loadPage($(this).attr('href'), {
			success: function (href) {
				console.log('Success :o');

				WebApp.stats.lastHistoryPush = (new Date).getTime();
				History.pushState({href: href}, $('title').text(), href);
			},
			error: function () {
				console.log('Error :o');
			}
		});

		return false;
	},
	loadPage: function (href, options) {
		if (options == undefined) {
			options = {};
		}
		if (options.ignoredBlocks == undefined) {
			options.ignoredBlocks = [];
		}

		options.spinner = setTimeout(function () {
			WebApp.ProgressBar.begin();

			$('title').text(
				WebApp.settings.titleStructure.replace(
					'[title]',
					'Loading...'
				)
			);

			$('*[data-block]').each(function (index, element) {
				if (options.ignoredBlocks.indexOf($(this).data('block')) !== -1) {
					console.log('Ignore clear ' + $(this).data('block'));

					return;
				}
				$(this).html(
					$("<div/>", {
						height: $(this).innerHeight(),
						width: $(this).innerWidth(),
						style: "display: block;",
						html: '<div class="web-app"><div class="spinner" role="spinner"><div class="spinner-icon"></div></div></div>'
					})
				);
			});
		}, 200);

		$.ajax({
			url: href,
			//cache: false,
			beforeSend: function (request) {
				request.setRequestHeader("X-Web-App", true);
			},
			success: function(json) {
				WebApp.ProgressBar.setProgress(75);

				WebApp.loadContent(json, href, options);
			},
			error: function (jqXHR, textStatus, errorThrown) {
				if (jqXHR.getResponseHeader('X-Web-App') === 'true') {

					var response = jqXHR.responseText;

					try {
						var json = JSON.parse(response);
					} catch (e) {
						console.log('Invalid WebApp response: ' + response);

						return;
					}

					console.log('Actual WebApp');

					WebApp.ProgressBar.increase(75);

					WebApp.loadContent(json, href, ignoredBlocks);
				} else {
					console.log('Not WebApp');

					options.error(href);
				}
			},
			progress: function(evt) {
				if (evt.lengthComputable) {
					console.log("Loaded " + parseInt( (evt.loaded / evt.total * 100), 10) + "%");

					WebApp.ProgressBar.setProgress((evt.loaded / evt.total * 100) / 100 * 75);
				}
			},
			async: true
			//dataType: 'json'
		});
	},
	loadContent: function (page, href, options) {
		++WebApp.stats.pageLoads;
		//console.log('Page loads: ' + WebApp.stats.pageLoads);

		$('#web-page-loads').text(WebApp.stats.pageLoads);
		
		$('title').text(
			WebApp.settings.titleStructure.replace(
				'[title]',
				(page.variables.title) ? page.variables.title : 'Unknown'
			)
		);

		var blocks = $('*[data-block]');

		var stepPercentage = 100 / blocks.length;
		blocks.each(function (index, element) {
			if (options.ignoredBlocks.indexOf($(this).data('block')) === -1) {
				console.log('Ignore update ' + $(this).data('block'));

				var block = $(this).data('block');

				var blockEmpty = $(this).data('block-empty');
				if (blockEmpty === undefined) {
					blockEmpty = false;
				}

				if ((blockEmpty) && (page.blocks[block] === undefined)) {
					$(this).html('');
				} else {
					$(this).html(page.blocks[block]);
				}

				WebApp.registerClickHandler($(element));

				WebApp.ProgressBar.increase((stepPercentage) / 100 * 25);
			}

			if (blocks.length === index + 1) {
				WebApp.ProgressBar.end();

				clearTimeout(options.spinner);

				options.success(href);
			}
		});
	}
}
$(function () {
	// Bind to StateChange Event
    History.Adapter.bind(window,'popstate',function(){ // Note: We are using statechange instead of popstate
	    if ((new Date).getTime() - WebApp.stats.lastHistoryPush < 200) {
		    console.log('Last history push was les then 200 ms ago. Ignore.')

		    return;
	    }
        var State = History.getState(); // Note: We are using History.getState() instead of event.state
		//console.log(State);
		WebApp.loadPage(State.url);
		//History.log('statechange:', State.data, State.title, State.url);
    });

    // Change our States
    /*History.pushState({state:1}, "State 1", "?state=1"); // logs {state:1}, "State 1", "?state=1"
    History.pushState({state:2}, "State 2", "?state=2"); // logs {state:2}, "State 2", "?state=2"
    History.replaceState({state:3}, "State 3", "?state=3"); // logs {state:3}, "State 3", "?state=3"
    History.pushState(null, null, "?state=4"); // logs {}, '', "?state=4"
    History.back(); // logs {state:3}, "State 3", "?state=3"
    History.back(); // logs {state:1}, "State 1", "?state=1"
    History.back(); // logs {}, "Home Page", "?"
    History.go(2); // logs {state:3}, "State 3", "?state=3"*/
	
	WebApp.init();
});
