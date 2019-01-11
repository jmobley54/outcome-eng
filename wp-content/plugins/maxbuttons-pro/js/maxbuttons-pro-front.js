
jQuery(document).ready(function($){
	function mbProInit()
	{
		$('.maxbutton[data-mbga]').on('click',  mbTrackEvent );

	}

	function mbTrackEvent(event) //mbTrackEvent(event,id, cat, action, label, value, noninteraction)
	{
	 	event.preventDefault();
	 	var href = this.href;
	 	var element = $(this);
	 	var data = $(this).data('mbga');
	 	var cat = data.cat;
	 	var action = data.action;
	 	var label = data.label;
	 	var value = data.value;
	 	var noninteraction = data.noninteraction;

 		var target = $(this).prop('target');


		if (typeof gtag == 'function')
		{
				gtag('event', action, {
			  	'event_category': cat,
			  	'event_label': label,
			  	'value': value,
					'noninteraction' : noninteraction,
			});

		}
		else if (typeof ga == 'function')
		{
			if (typeof dataLayer == 'object') // GTM
			{
				dataLayer.push ({ 'event': 'event',
					'eventCategory': cat,
					    'eventAction': action,
					    'eventLabel': label,
				});
			}
			else
			{
		       ga(function (tracker)
		   	   {
		            tracker.send('event', cat, action, label,value, {'nonInteraction' : noninteraction} );
		   	   });
			}
		}
		else if (typeof __gaTracker == 'function')  // yoast analytics uses another var.
		{

			__gaTracker('send', 'event', cat, action, label, value, {'nonInteraction' : noninteraction} );

		}
		else if (typeof _gaq == 'object') // GA classic
		{
			_gaq.push(['_trackEvent', cat, action, label, value, noninteraction]);

		}

		if(href)
		{
			if (typeof target !== 'undefined' && target != '')
				window.open(href, target);
			else
				window.location = href;
		}

  }


	mbProInit();
}); // jquery
