jQuery(document).ready(function(){
	//load tabs
	jQuery( "#tabs" ).tabs();
	
	//export
	jQuery('#su_ui_export').bind('click', function() {
		if (jQuery("#sc_to_ex").val().length > 0){
			jQuery(".export_code").hide('fast');
			jQuery("#export_code").val('');
			jQuery(".sc_ex_status").show('fast');
			
			jQuery(".sc_ui").html('');
			jQuery.ajaxSetup({ cache: false });
			jQuery.getJSON(ajaxurl,
			{  	sc_ids: jQuery("#sc_to_ex").val(),
			    action: "ba_sb_get_ex_code",
			    seq: jQuery("#sc_ui_Get_Export_code").val()
			},
			function(data) {
				if (data.errors){
					alert('Error in Export! :(');						
				}else{
					//update nonce 
					jQuery("#sc_ui_Get_Export_code").val(data.nonce);
					jQuery("#export_code").val(data.code);
					jQuery(".export_code").show();
				}
			});
			
			jQuery(".sc_ex_status").hide('3500');
			jQuery.ajaxSetup({ cache: true });
		}else{
			alert('Please Select a Shortcode to export');
		}
	});
	
	//Import
	var htmls = '';
	jQuery('#su_ui_import').bind('click', function() {
		jQuery(".sc_im_status").show('fast');
		jQuery(".im-results").hide('fast');
		jQuery.ajaxSetup({ cache: false });
		jQuery.getJSON(ajaxurl,
		{  	import_code: jQuery("#import_code").val(),
		    action: "ba_sb_Import_sc",
		    seq2: jQuery("#sc_ui_Import_sc").val()
		},
			function(data) {
				if (data.errors){
					alert('Error in Import! :(');						
				}else{
					if (data.status){
						htmls = 'Import Status: <br/>';
						$.each(data.status, function(i,item){
							htmls = htmls + "<strong>" + item.sc_title + "</strong>" + " " + item.status + " Tag Used: " + item.tag + "<br/>";
						});
						jQuery(".im-results").html(htmls);
					}
					
					//jQuery(".im-results").html(data.dump);
					jQuery(".im-results").show('2000');
					jQuery(".sc_im_status").hide('3200');
					//update nonce 
					jQuery("#sc_ui_Import_sc").val(data.nonce);
					//jQuery("#export_code").val(data.code);
				}
			});
		});
});