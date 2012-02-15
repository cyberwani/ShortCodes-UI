/****************************************
	jQuery Simple Box
	by Ohad Raz
	http://en.bainternet.info
****************************************/

// display the SimpleBox
function SimpleBox(insertContent, ajaxContentUrl,BoxTitle){

	// jQuery wrapper (optional, for compatibility only)
	(function($) {
		
		/*
		* defualts
		*/
		
		//inline content
		insertContent = typeof(insertContent) != 'undefined' ? insertContent : null;
		//ajax content
		ajaxContentUrl = typeof(ajaxContentUrl) != 'undefined' ? ajaxContentUrl : null;
		//box title
		BoxTitle = typeof(BoxTitle) != 'undefined' ? BoxTitle : null;
		
		// add SimpleBox/shadow <div/>'s if not previously added
		if($('#SimpleBox').size() == 0){
			var theSimpleBox = $('<div id="SimpleBox"/>');
			var theShadow = $('<div id="SimpleBox-shadow"/>');
			$(theShadow).click(function(e){
				closeSimpleBox();
			});
			$('body').append(theShadow);
			$('body').append(theSimpleBox);
		}
		
		// remove any previously added content
		$('#SimpleBox').empty();
		
		// insert HTML content
		if(insertContent != null){
			$('#SimpleBox').append(insertContent);
		}
		
		//title
		if (BoxTitle != null){
			$('#SimpleBox').append('<div id="SimpleBoxTitle">' + BoxTitle + '</div>');
			$('#SimpleBoxTitle').append('<img id="closeImg" alt="close" style="float:right;" width="16" height="16" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAMAAAAoLQ9TAAAAA3NCSVQICAjb4U/gAAAAGXRFWHRTb2Z0d2FyZQB3d3cuaW5rc2NhcGUub3Jnm+48GgAAAOdQTFRF////UFpatrq6uLy8UFpaUFpagYiIhIuLs7e3trq6oKamoaenmaGhmqGhm6Ghm6KiKDIyLjg4Mz09OEJCPkhIQ01NWGJiWWNjWmRkW2VlXWZmXWdnXmhoX2lpYmxsY21ta3Nza3R0bnZ2b3d3c3p6dX19eH9/fISEfYODho2NipSUi5WVkJaWprCwsLq6sry8tb+/tsDAuL+/ub+/ucLCucPDu8TEvMTEvMXFvcfHvsfHv8fHv8nJwcvLxM7Oxs7OyNLSy9XVzNbWzdXVz9nZ0Nra1N3d2OHh2uPj4Obm4efn8fT09Pb2nryFrQAAABB0Uk5TADxcXcfM1tbY2fn5+vr6+hAXMfYAAAClSURBVBgZXcFBTgJREEXRW/+XbQ8kQUlsTJoluP99uIYegAMMCdGoVfXEARPOgRtm/fmeq+/3dH99Ma60f/PHKYUwhGHTxocQubSZpeaODZ5JLSfEB5obcgqVOAKlgqYq7da6WO9UJUdQEqA0wAnqcOLimFNDTRF9lLZbaewRuP1abKw/0PPpE+F3aXZe1RdjOyO5H1YGAQT/9taG1sDAMKJ+uPUHWWdWhrbgEeoAAAAASUVORK5CYII%3D" />');
			$('#closeImg').click(function(e){
				closeSimpleBox();
			});
			
		}
		
		
		// insert AJAX content
		if(ajaxContentUrl != null){
			// temporarily add a "Loading..." message in the SimpleBox
			$('#SimpleBox').append('<p class="SimpleBoxLoading">Loading...</p>');
			
			// request AJAX content
			$.ajax({
				type: 'GET',
				url: ajaxContentUrl,
				success:function(data){
					// remove "Loading..." message and append AJAX content
					$('.SimpleBoxLoading').remove();
					$('#SimpleBox').append(data);
				},
				error:function(){
					alert('AJAX Failure!');
				}
			});
		}
		
		// move the SimpleBox to the current window top + 100px
		$('#SimpleBox').css('top', '100px');
		$('#SimpleBox').css('marginLeft', '-' + $('#SimpleBox').width() / 2 + 'px');
		$('#SimpleBox-shadow').css('height', $(document).height()+'px');
		
		// display the SimpleBox
		$('#SimpleBox-shadow').fadeIn('fast', function(){
			$('#SimpleBox').fadeIn('fast');
		});
	
	})(jQuery); // end jQuery wrapper
	
}

// close the SimpleBox
function closeSimpleBox(){
	(function($) {
		
		// hide SimpleBox/shadow <div/>'s
		$('#SimpleBox').hide();
		$('#SimpleBox-shadow').fadeOut('slow');
		
		// remove contents of SimpleBox in case a video or other content is actively playing
		$('#SimpleBox').empty();
	})(jQuery); // end jQuery wrapper
}