var WebApp = {
	settings: {
		titleStructure: '[title] - WebApp'
	},
	stats: {
		pageLoads: 0,
	},
	previousUrl: location.href,
	registerClickHandler: function(selector) {
		$('a', selector).click(WebApp.clickHandler);
	},
	clickHandler: function() {
		return !WebApp.loadPage($(this).attr('href'));
	},
	loadPage: function (href) {
		NProgress.start();
		console.log('Load page:' + href);
		
		var load = function (json) {
			try {
				WebApp.loadContent(json, href);
			}
			catch (exception) {
				success = false;
			}
		};
		
		var success = true;
		$.ajax({
			url: href + '?web-app=true',
			cache: false,
			success: function(json) {
				load(json);
			},
			error: function (jqXHR, textStatus, errorThrown) {
				switch (jqXHR.status) {
					case 403:
					case 404:					
						var json;
						try {
							json = JSON.parse(jqXHR.responseText);
						}
						catch (exception) {
							success = false;
							
							return;
						}
						load(json);
						
						break;
					default:
						success = false;
				}
			},
			progress: function(evt) {
				if (evt.lengthComputable) {
					NProgress.set(evt.loaded / evt.total);
					//console.log(evt.loaded / evt.total);
					//console.log("Loaded " + parseInt( (evt.loaded / evt.total * 100), 10) + "%");
				}
				else {
					//console.log("Length not computable.");
				}
			},
			complete: function () {
				NProgress.done();
			},
			async: false
			//dataType: 'json'
		});
		
		WebApp.previousUrl = href;
		
		return success;
	},
	loadContent: function (page, href) {	
		console.log(page);
		
		++WebApp.stats.pageLoads;
		console.log('Page loads: ' + WebApp.stats.pageLoads);
		
		$('title').text(
			WebApp.settings.titleStructure.replace(
				'[title]',
				(page.variables.title) ? page.variables.title : 'Unknown'
			)
		);
			
		History.pushState({href: href}, $('title').text(), href);
		
		$('*[data-block]').each(function (index, element) {
			var block = $(this).data('block');
			
			WebApp.registerClickHandler($(this));
			
			if (($(this).data('block-empty') !== undefined) && (page.blocks[block] === undefined)) {
				$(this).html('');
			} else {
				$(this).html(page.blocks[block]);
			}
		});
	}
}

$(window).load(function () {
	NProgress.configure({ trickle: false });
	NProgress.start();
});
$(function () {	
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
	
	// Bind to StateChange Event
    History.Adapter.bind(window,'statechange',function(){ // Note: We are using statechange instead of popstate
        var State = History.getState(); // Note: We are using History.getState() instead of event.state
		//console.log(State);
		//WebApp.loadPage(State.url);
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
	
	WebApp.registerClickHandler($(document));
	
	NProgress.done();
});