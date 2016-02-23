jQuery( document ).ready( function ( $ )
{

	/**
	 * Split string into multiple values, separated by commas
	 *
	 * @param val
	 *
	 * @return array
	 */
	function split( val )
	{
		return val.split( /,\s*/ );
	}

	/**
	 * Extract string Last into multiple values
	 * @param term
	 *
	 */
	function extract_last( term )
	{
		return split( term ).pop();
	}

	$( '#recipient' ).autocomplete( {
		source: function ( request, response )
		{
			var data = {
				action: 'ypm_get_users',
				term  : extract_last( request.term )
			};
			$.post( ajaxurl, data, function ( r )
			{
				response( r );
			}, 'json' );
		},
		select: function ( event, ui )
		{
			var terms = split( this.value );
			terms.pop();
			terms.push( ui.item.value );
			terms.push( "" );
			this.value = terms.join( "," );
			return false;
		}
	} );

//tab选择器
		// Loads tabbed sections if they exist
	if ( $('.nav-tab-wrapper').length > 0 ) {
		options_framework_tabs();
	}

	function options_framework_tabs() {

		var $group = $('.group'),
			$navtabs = $('.nav-tab-wrapper a'),
			active_tab = '';

		// Hides all the .group sections to start
		$group.hide();

		// Find if a selected tab is saved in localStorage
		if ( typeof(localStorage) != 'undefined' ) {
			active_tab = localStorage.getItem('active_tab');
		}

		// If active tab is saved and exists, load it's .group
		if ( active_tab != '' && $(active_tab).length ) {
			$(active_tab).fadeIn();
			$(active_tab + '-tab').addClass('nav-tab-active');
		} else {
			$('.group:first').fadeIn();
			$('.nav-tab-wrapper a:first').addClass('nav-tab-active');
		}

		// Bind tabs clicks
		$navtabs.click(function(e) {

			e.preventDefault();

			// Remove active class from all tabs
			$navtabs.removeClass('nav-tab-active');

			$(this).addClass('nav-tab-active').blur();

			if (typeof(localStorage) != 'undefined' ) {
				localStorage.setItem('active_tab', $(this).attr('href') );
			}

			var selected = $(this).attr('href');

			$group.hide();
			$(selected).fadeIn();

		});
	}
} );