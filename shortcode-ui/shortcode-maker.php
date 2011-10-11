<?php
/*
Plugin Name: ShortCodes UI
Plugin URI: http://en.bainternet.info
Description: A Brand New Plugin
Version: 1.0
Author: Bainternet
Author URI: http://en.bainternet.info
*/
if ( !class_exists('BA_ShortCode_Maker')){
	class BA_ShortCode_Maker {
		
		/**
	 	* Holds the shortcodes tags
	 	*
	 	* @var array()
	 	* @access protected
	 	*/
		protected $sc_tags;
		
		/**
	 	* Holds the css,js per shortcode
	 	*
	 	* @var array()
	 	* @access protected
	 	*/
		protected $sc_media;
		
		/**
	 	* Holds the css,js per shortcode per location
	 	*
	 	* @var array()
	 	* @access protected
	 	*/
		protected $sc_external;
		
		//constarctor
	    public function __construct() {
	    	
	    	$this->sc_media = array();
	    	$this->sc_external = array();
	    	$this->sc_tags = array();
	    	$isadmin = is_admin();
	    	//register shortcode type
	    	add_action( 'init', array($this,'register_customs') );
	    	
	    	//manage columns
	    	add_filter('manage_edit-ba_sh_columns', array($this,'add_new_sc_columns'));
			add_action('manage_ba_sh_posts_custom_column', array($this, 'manage_sc_columns'), 10, 2);
	    	
	    	//fix translation
		    if ($isadmin){
				add_filter('gettext',array($this,'custom_enter_title'));
				add_filter('admin_init',array($this,'load_meta_box'));
			}
			
			//register shortcodes
			add_action('plugins_loaded',array($this,'load_shortcodes'));
			
			
			//add scripts and style
			add_action('wp_footer',array($this,'print_footer_Scripts'));
			add_action('wp_footer',array($this,'external_print_footer_Scripts'));
			add_action('wp_head',array($this,'external_print_head_Scripts'));
			//setup scripts and styles // the_posts gets triggered before wp_head
			add_filter('the_posts', array($this, 'conditionally_add_scripts_and_styles'));

			
			//tinymce button
			global $pagenow,$typenow; 
			if ($isadmin && $typenow !='ba_sh' && ($pagenow=='post-new.php' OR $pagenow=='post.php')){
				add_filter('admin_footer',array($this,'insert_shortcode_button'));
				add_filter( 'mce_buttons', array($this,'Add_custom_buttons' ));
				add_filter( 'tiny_mce_before_init', array($this,'Insert_custom_buttons' ));
			}
			
			//ajax tinymce functions
			add_action('wp_ajax_sh_ui_panel', array($this,'load_tinymce_panel'));
			add_action('wp_ajax_ba_sb_shortcodes', array($this,'get_shortcode_list'));
			add_action('wp_ajax_ba_sb_shortcode', array($this,'get_shortcode_fields'));
			
			//export import functions
			add_filter('post_row_actions',array($this,'Export_shortcodes_Row_action'), 10, 2);
			if ($isadmin){
				add_action('admin_menu', array($this,'sc_ui_import_export_menupage'));
				add_action('admin_print_scripts-ba_sh_page_sc_ui_ie',array($this,'sc_ui_import_export_scripts'));
				add_action('admin_print_styles-ba_sh_page_sc_ui_ie',array($this,'sc_ui_import_export_styles'));
				
				
				//ajax export/Import function
				add_action('wp_ajax_ba_sb_get_ex_code', array($this,'sc_ui_ajax_export'));
				add_action('wp_ajax_ba_sb_Import_sc', array($this,'sc_ui_ajax_import'));
				
			}
			
	    }
	    
		/* 
		 ****************************
		 * tinymce button functions *
		 ****************************
		 */
	    
	    	    
		//add buttons
		public function Add_custom_buttons( $mce_buttons ){
			$mce_buttons[] = '|';
			$mce_buttons[] = 'ShortCodeUI';
			return $mce_buttons;
		}
		
		public function insert_shortcode_button(){
			echo '<!-- ShortCode UI insert_shortcode_button -->
			<script>
			//insert shortcode
			jQuery(document).ready(function() {
		    	jQuery(".insert_shortcode").live(\'click\', function() {
		    		var shortcode = "";
		    		var attr_val = "";
		    		shortcode = "[" + walker.tag;
		    		if (walker.fields){
			    		jQuery.each(walker.fields, function(i,item){
			    			attr_val = "";
			    			attr_val = jQuery("#" + item.name).val();
			    			//if ( attr_val != "" && attr_val.lenght > 0){
			    				shortcode = shortcode + " " + item.name + "=\"" + attr_val + "\"";
			    			//}
						});
					}
					
					if (walker.content){
					    var con = "";
					    con = jQuery(".sc_content").val();
					}
					if (walker.content && $.trim(con).length){
						shortcode = shortcode + "]" + jQuery(".sc_content").val();
						shortcode = shortcode + "[/"+ walker.tag + "]"; 
					}else{
						shortcode = shortcode + "]";
					}
					tinyMCE.activeEditor.execCommand("mceInsertContent", 0, shortcode);
					// closes Thickbox
					tb_remove();
		    	});
		    });
		    function microtime(get_as_float) {  
     			var now = new Date().getTime() / 1000;  
        		var s = parseInt(now);  
		        return (get_as_float) ? now : (Math.round((now - s) * 1000) / 1000) + " " + s;  
    		}  
			</script>
			<style>
			 .sc-desc{background: none repeat scroll 0 0 #F1fc5c;border-radius: 8px 8px 8px 8px;color: #777777;display: block;float: right;margin: 3px 0 10px 5px;max-width: 240px;padding: 15px;}
			 .sc_att{width: 650px;}
			 	.sc_container{border:1px solid #ddd;border-bottom:0;background:#f9f9f9;}
			#sc_f_table label{font-size:12px;font-weight:700;width:200px;display:block;float:left;}
			#sc_f_table input {padding:30px 10px;border-bottom:1px solid #ddd;border-top:1px solid #fff;}
			#sc_f_table small{display:block;float:right;width:200px;color:#999;}
			#sc_f_table input[type="text"], #sc_f_table select{width:280px;font-size:12px;padding:4px;	color:#333;line-height:1em;background:#f3f3f3;}
			#sc_f_table input:focus, .#sc_f_table textarea:focus{background:#fff;}
			#sc_f_table textarea{width:280px;height:175px;font-size:12px;padding:4px;color:#333;line-height:1.5em;background:#f3f3f3;}
			#sc_f_table h3 {cursor:pointer;font-size:1em;text-transform: uppercase;margin:0;font-weight:bold;color:#232323;float:left;width:80%;padding:14px 4px;}
			#sc_f_table th, #sc_f_table td{border:1px solid #bbb;padding:10px;text-align:center;}
			#sc_f_table th, .#sc_f_table td.feature{border-color:#888;}
			@import "http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/themes/cupertino/jquery-ui.css";
			</style>';
		}
		
		
		//set button
		public function Insert_custom_buttons( $initArray ){
			$initArray['setup'] = <<<JS
[function(ed) {
    ed.addButton('ShortCodeUI', {
        title : 'ShortCodeUI',
        image : 'http://i.imgur.com/cdru8.png',
        onclick : function() {
        	//insert shortcode
			tb_show("ShortCode UI","admin-ajax.php?action=sh_ui_panel&height=500&width=650");
        }
    });
}][0]
JS;
			return $initArray;
		}
		
		
		//load tinymce panel
		public function load_tinymce_panel(){
			?>
			<div class="sc_container">
				<div class="sc_selector">
					<span class="sc_category" style="width: 50%;">Shortcode Categories: <select id="sc_cat" name="sc_cat"><?php 
						$cats = get_categories(array('taxonomy' => 'bs_sh_cats','type' => 'ba_sh'));
						echo '<option vlaue="0">'.__('Select Category').'</option>';
						echo '<option vlaue="0">'.__('Select All ShortCodes').'</option>';
						foreach ($cats as $category) {
	  						$option = '<option value="'.$category->term_id.'">';
							$option .= $category->name;
							$option .= '</option>';
							echo $option;
  						}
						?>
						</select>
					</span>
					<span class="sc_names" style="width: 50%;"> Select Shortcode:
						<select class="sc_name" name="sc_name" id="sc_name">
							<option>Please Select A Category First</option>
						</select>
					</span>
				</div>
				<div class="sc_status" style="display: none;"><img src="http://i.imgur.com/l4pWs.gif" alt="loading..."/></div>
				<div class="sc_ui"></div>
				<div class="sc_atts"></div>
				<div class="sc_insert"></div>
			</div>
			<script>
				//declare walker object
				var walker = new Array();
		    	//select shortcode category			
				$("#sc_cat").change(function() {
					//before ajax
					if ($("sc_cat").val() != -1) {
						$(".sc_status").show('fast');
						$(".sc_ui").html('');
						$.ajaxSetup({ cache: false });
						$.getJSON(ajaxurl,
						{  	cat: $("#sc_cat").val(),
							rnd: microtime(false), //hack to avoid request cache
						    action: "ba_sb_shortcodes",
						    seq: "<?php echo wp_create_nonce("list_sh_by_cat");?>"
						},
						function(data) {
							if (data.errors){
								alert('Error in getting shortcode list! :(');						
							}else{
								$("#sc_name >option").remove();
								var myCombo= $('#sc_name');
	
								$.each(data.items, function(i,item){
									myCombo.append($('<option> </option>').val(item.id).html(item.title));
								});
							}
						});
						$(".sc_status").hide('3500');
						$.ajaxSetup({ cache: true });
						
					}
		    	});
		    	
		    	//select shortcode
				$("#sc_name").change(function() {
					$(".sc_status").show('fast');
					$(".sc_ui").html('');
					$.ajaxSetup({ cache: false });
					
					$.getJSON(ajaxurl,
					{  	sc_id: $("#sc_name").val(),
						rnd: microtime(false), //hack to avoid request cache
					    action: "ba_sb_shortcode",
					    seq: "<?php echo wp_create_nonce("get_shortcode_fields");?>"
					},
					function(data) {
						if (data){
							walker = data;
							$(".sc_ui").append('<h2>'+ $('#sc_name>option:selected').text() +' Shortcode</h2>');
							if (data.errors){
								alert('Error in getting shortcode! ):(');						
							}else{
								if(data.preview){
									$(".sc_ui").append('<div>Preview <br/><img src="' + data.preview + '"/></div>');
								}
								if (data.fields){
									$(".sc_ui").append('<h3>ShortCode Attributes</h3>');								
									$(".sc_ui").append($('<table> </table>').attr('id','sc_f_table').attr('width' ,'100%'));
									if (data.headers){
										$("#sc_f_table").append(data.headers);	
									}
									$.each(data.fields, function(i,item){
										$(".sc_ui").append(item.html +'<hr/>');
									});
								}
								if (data.content){
									$(".sc_ui").append('<h3>ShortCode Content</h3>');
									$(".sc_ui").append('<div><textarea class="sc_content" style="width: 398px; height: 70px;"></textarea><br/>Enter The Content that needs to be inside the shortcode tags here</div>');
								}
								if (data.submit){
									$(".sc_ui").append('<div>'+data.submit + '</div>');
								}
							}
						}
					});
					$(".sc_status").hide('3500');
					$.ajaxSetup({ cache: true });
					
		    	});

			</script>
			<style>
				.sc_selector{margin-top: 10px;}
				.sc_ui{overflow: auto; width: 630px; padding: 0 3px;}
				.sc_ui table{Border: 2px solid;}
				.sc_ui tr{Border: 2px solid;}
				.sc_ui td{Border: 2px solid;}
				.sc-label{font-weight: bloder; font-size: 14px,text-align: center;}
				
			</style>
			<?php 
			die();
		}
		
		//get shortcode list for panel
		public function get_shortcode_list(){
			check_ajax_referer( 'list_sh_by_cat', 'seq' );				
			global $wpdb;
			$args = array( 'posts_per_page' => -1, 'post_type' => 'ba_sh', 'fields' =>'ids' );
			if (isset($_GET['cat']) && $_GET['cat'] != -1){
				if ($_GET['cat'] != 0){
					$args['tax_query'] =array(
						array(
							'taxonomy' => 'bs_sh_cats',
							'field' => 'id',
							'terms' => $_GET['cat']
						)
					);
				}
			}
			
			$myshortcodes = get_posts( $args );
			$re = array();
			if (count($myshortcodes) > 0){
				$myshortcodes = implode(',', $myshortcodes);
				$ids_with_titles = $wpdb->get_results( 
					"
						SELECT ID, post_title 
						FROM $wpdb->posts
						WHERE ID IN  ({$myshortcodes})
					"
				); 
				$prefix = '_basc';
				$re['items'][] = array('id' => 0,'title' => __('Select A ShortCode')); 
				foreach( $ids_with_titles as $p){
					$item = array('id' => $p->ID,'title' => $p->post_title);
					$re['items'][] = $item;
				}
			}else{
				$re['errors'] = __('No ShortCodes were found! try looking for something else');
			}
			echo json_encode($re);
			die();
		}
		
		//get shortcode fields for panel
		public function get_shortcode_fields(){
			check_ajax_referer( 'get_shortcode_fields', 'seq' );
			$sc_id = intval($_REQUEST['sc_id']);
			$sc_meta = get_post_custom($sc_id);
			$prefix = '_basc';
			$re = array();
			
			$sc_type = $sc_meta[$prefix.'sh_type'][0];
			if(isset($sc_meta['_bascsh_attr'][0]) ){
				$shortcode_attributes = unserialize($sc_meta['_bascsh_attr'][0]);
				if (is_array($shortcode_attributes) && count($shortcode_attributes > 0)){
					$fields = array();
					
					foreach ($shortcode_attributes as $at){
						$field = array();
						$field['name'] = $at[$prefix.'_name'];
						$field['std'] = $at[$prefix.'_std'];
						$field['options'] = explode("\n", $at[$prefix.'_options']);
						$field['description'] = $at[$prefix.'_desc'];
						$html ='';
						$html = '<div class="sc_att"><div class="sc-label" style="float: left;width: 150px;">
							<p id="'.$field['name'].'-lable">'.$field['name'].'</p></div>';
						
						if (!is_array($field['options']) || count($field['options']) == 1 ){
							$html .= '<div class="sc-field" style="float: left;width: 200px;"><input name="'.$field['name'].'" id="'.$field['name'].'" value="'.$field['std'].'"></div>';
						}else{
							$html .='<div class="sc-field" style="float: left;width: 200px;"><select name="'.$field['name'].'" id="'.$field['name'].'">';
							foreach ($field['options'] as $op){
								$op = trim($op);
								$selected = ($op == $field['std'])? ' selected="selected"' : ''; 
								$html .= '<option value="'.$op.'"'.$selected.'>'.$op.'</option>';
							}
							$html .= '</select></div>';
						}
						$html .= '<div class="sc-desc" style="float:left;">'.$field['description'].'</div></div><div style="clear: both;"></div>';
						$field['html'] = $html;
						$re['fields'][] = $field; 
					}
				}
			}
			//preview image
			if (isset($sc_meta[$prefix.'sh_preview_image'][0]))
				$re['preview'] = $sc_meta[$prefix.'sh_preview_image'][0];
			//content field
			if (in_array($sc_type,array('content','advanced')))
				$re['content'] = true;
			
			$re['submit'] = '<br/><br/><input type="submit" value="Insert Shortcode" id="insert_sc" class="button-primary insert_shortcode">';
			
			//shortcode Tag
			$re['tag'] = $sc_meta[$prefix.'sh_tag'];
			
			echo json_encode($re);
			die();
		}
		
		/* 
		 ********************************
		 * End tinymce button functions *
		 ********************************
		 */
		
		/* 
		 ****************************
		 * manage columns functions *
		 ****************************
		 */
		
		//add columns function 
		public function add_new_sc_columns($columns){
			$new_columns['cb'] = '<input type="checkbox" />';
			//$new_columns['id'] = __('ID');
			$new_columns['title'] = _x('ShortCode Name', 'column name');
			$new_columns['sc_tag'] = __('Shortcode Tag');
			$new_columns['image'] = __('Preview');
			$new_columns['cats'] = __('Categories');
			$new_columns['sc_Author'] = __('ShortCode Author');
			return $new_columns;
		}

		//rader columns function 
		public function manage_sc_columns($column_name, $id) {
			global $wpdb;
			$prefix = '_basc';
			switch ($column_name) {
			case 'id':
				echo $id;
			        break;
			case 'image':
				// Get number preview image
				$image = get_post_meta($id,$prefix.'sh_preview_image',true);
				if (false != $image && !empty($image)){
					echo '<img src="'.$image.'" width="80px" height="80px"/>';
				}else {
					echo '<img src="http://i.imgur.com/W8R4m.jpg" width="80px" height="80px"/>';
				}
				break;
			case 'sc_tag':
				//get tag
				$tag = get_post_meta($id,$prefix.'sh_tag',true); 
				if (false != $tag && !empty($tag))
					echo '<p>['.$tag.']</p>';
				break;
			case 'cats':
				$cats = wp_get_object_terms($id, 'bs_sh_cats');
				$re ='';
				foreach ((array)$cats as $c){
					$re .=  $c->name .', ';
				}
				if (strlen($re) > 2)
					echo substr($re,0,-2);
				break;
			case 'sc_Author':
				$author = get_post_meta($id,$prefix.'_Author_Name',true);
				$author_url = get_post_meta($id,$prefix.'_Author_url',true);
				$support_url =  get_post_meta($id,$prefix.'_Support_url',true);
				if (!empty($author_url) && !empty($author)){
					echo '<a href="'.$author_url.'" target="_blank"><strong>'.$author.'</strong></a>';
				}elseif(!empty($author)){
					echo '<strong>'.$author.'</strong>';
				}
				if (!empty($support_url)){
					echo '<br/><a href="'.$support_url.'" target="_blank">Shortcode Support</a>';
				}
				break;
			default:
					break;
				} // end switch
		}
		
		/* 
		 ********************************
		 * End manage columns functions *
		 ********************************
		 */
		
		
	    //setup scripts and styles
		public function conditionally_add_scripts_and_styles($posts){
			if (empty($posts)) return $posts;
		 
			if (!isset($this->sc_tags) && !is_array($this->sc_tags))
				return $posts;
			
			$tags = array_keys($this->sc_tags);
			
			foreach ($tags as $index => $t){
				foreach ($posts as $post) {
					if (stripos($post->post_content, $t)) {
						$this->sc_external[$t]['found'] = true; 
						break;
					}
				}
			}
		 
			return $posts;
		}

		
	    //fix translation
	    public function custom_enter_title( $input ) {
		    global $post_type;
		    if( 'Enter title here' == $input && 'ba_sh' == $post_type )
		        return __('Enter Shortcode Name','ba_shcode');
		    return $input;
		}
		
		//register shortcodes
		public function load_shortcodes(){
			global $post;
			$tmp_post = $post;
			$args = array( 'posts_per_page' => -1, 'post_type' => 'ba_sh', 'fields' =>'ids' );
			$prefix = '_basc';
			$myshortcodes = get_posts( $args );
			
			foreach( $myshortcodes as $p){
				$sc_meta = get_post_custom($p);
				$sc_tag = $sc_type = '';
				if (isset($sc_meta[$prefix.'sh_type']))
					$sc_type = $sc_meta[$prefix.'sh_type'][0];
				if (isset($sc_meta[$prefix.'sh_tag'])){
					$sc_tag = $sc_meta[$prefix.'sh_tag'][0];
					//avoid duplicate shortcode tags
					$sc_tag = $this->avoid_duplicate_shortcode_tags($sc_tag);
					
					
					$this->sc_tags[$sc_tag]= array( 'id' => $p,'head'=>false,'body'=>false); 
					$this->sc_tags[$sc_tag]['id'] = $p; 
					//add_shortcode handler
					switch($sc_type) {
					    case 'simple':
					      	//add_shortcode($sc_tag, create_function('',"return '".$p->post_content ."';"));
					      	add_shortcode($sc_tag, array($this,'simple_shortcode_handler'));
					      	break;
					    case 'content':
					      	add_shortcode($sc_tag, array($this,'content_shortcode_handler'));
					      	break;
					    case 'advanced':
					      	add_shortcode($sc_tag, array($this,'advanced_shortcode_handler'));
					      	break;
					    default:
					    case 'snippet':
					      	add_shortcode($sc_tag, array($this,'snippet_shortcode_handler'));
					      break;
					}
					
					if (isset($sc_meta[$prefix.'sh_external'][0])){
						$externals = unserialize($sc_meta[$prefix.'sh_external'][0]);
						
						if (is_array($externals) && count($externals > 0)){
							foreach ($externals as $ex){
								$this->sc_external[$sc_tag][$ex['_bascsh_location']][] = array($ex['_bascsh_ex_f_type'] => $ex['_basc_url']);
							}
						}
					}
				}
			}
		}
		
		//register post type and custom taxonomy on init
		public function register_customs(){
			$this->register_cpt_ba_sh();
			$this->register_ct_bs_sh_cats();
		}
		
		//register custom taxonomy for shortcodes categories
		public function register_ct_bs_sh_cats(){
			$labels = array(
			    'name' => _x( 'Categories', 'taxonomy general name' ),
			    'singular_name' => _x( 'Category', 'taxonomy singular name' ),
			    'search_items' =>  __( 'Search shortcode categories' ),
			    'popular_items' => __( 'Popular shortcode categories' ),
			    'all_items' => __( 'All Shortcode categories' ),
			    'parent_item' => null,
			    'parent_item_colon' => null,
			    'edit_item' => __( 'Edit shortcode category' ), 
			    'update_item' => __( 'Update shortcode category' ),
			    'add_new_item' => __( 'Add shortcode category' ),
			    'new_item_name' => __( 'New shortcode category name' ),
			); 
			
			register_taxonomy('bs_sh_cats',	array('ba_sh'),
				array(
			    	'hierarchical' => true,
			    	'labels' => $labels,
			    	'show_ui' => true,
			    	'query_var' => true,
			    	'rewrite' => array( 'slug' => 'bs_sh_cats' ),
			));
		}
		
		//register shortcode post type
		public function register_cpt_ba_sh() {

			$labels = array( 
				'name' => _x( 'Short Codes', 'ba_sh' ),
				'singular_name' => _x( 'Shortcode', 'ba_sh' ),
				'add_new' => _x( 'Add New', 'ba_sh' ),
				'add_new_item' => _x( 'Add New Shortcode', 'ba_sh' ),
				'edit_item' => _x( 'Edit Shortcode', 'ba_sh' ),
				'new_item' => _x( 'New Shortcode', 'ba_sh' ),
				'view_item' => _x( 'View Shortcode', 'ba_sh' ),
				'search_items' => _x( 'Search Short Codes', 'ba_sh' ),
				'not_found' => _x( 'No short codes found', 'ba_sh' ),
				'not_found_in_trash' => _x( 'No short codes found in Trash', 'ba_sh' ),
				'parent_item_colon' => _x( 'Parent Shortcode:', 'ba_sh' ),
				'menu_name' => _x( 'Short Codes', 'ba_sh' ),
			);

			$args = array( 
				'labels' => $labels,
				'hierarchical' => false,
				'supports' => array( 'title', 'editor', 'custom-fields' ),
				'public' => false,
				'show_ui' => true,
				'show_in_menu' => true,
				'menu_icon' => 'http://i.imgur.com/cdru8.png',
				'show_in_nav_menus' => false,
				'publicly_queryable' => false,
				'exclude_from_search' => true,
				'has_archive' => false,
				'query_var' => true,
				'can_export' => true,
				'rewrite' => false,
				'capability_type' => 'post'
			);

			register_post_type( 'ba_sh', $args );
		}
		
		//do shortcode metaboxes
		public function load_meta_box(){
			require_once 'meta-box-class/my-meta-box-class.php';
			
			//settings metabox
			$prefix = '_basc';
			$config = array(
				'id' => 'sc_meta_box',					// meta box id, unique per meta box
				'title' => 'ShortCode Settings',					// meta box title
				'pages' => array('ba_sh'),			// post types, accept custom post types as well, default is array('post'); optional
				'context' => 'normal',						// where the meta box appear: normal (default), advanced, side; optional
				'priority' => 'high',						// order of meta box: high (default), low; optional
				'fields' => array()							// list of meta fields (can be added by field arrays)
			);
			$my_meta =  new AT_Meta_Box($config);
			$my_meta->addSelect($prefix.'sh_type',
				array(
					'snippet' => 'Simple Snippet',
					'simple'=>'Simple One Tag ShortCode',
					'content'=>'Simple ShortCode with content',
					'advanced'=>'Advanced ShortCode'),
				array('desc'=> '<ul>
					<li><strong>Simple Snippet</strong> - will return the value palced in the Editor above.</br> eg: <strong>[shortcode_tag]</strong></li>
					<li><strong>Simple One Tag ShortCode</strong> - will return the value palced in the Editor above or usign the template below, this type can have attributes. </br> eg: <strong>[shortcode_tag attribute1="value1" attribute2="value2"]</strong></li>
					<li><strong>Simple ShortCode with content</strong> - Must have {CONTENT} token in the Editor Above or in template below, will return the value palced in the Editor above</br> eg: <strong>[shortcode_tag]content[/shortcode_tag]</strong></li>
					<li><strong>Advanced ShortCode</strong> - Used for creating advanced ShortCodes (php functions,JavaScript functions ...)</li><ul>','name'=> 'ShortCode Type', 'std'=> array('snippet')));
			$my_meta->addText($prefix.'sh_tag',array('name'=> 'ShortCode Tag','desc'=>'This tag will be used to call this shortcode'));
			$my_meta->addTextarea($prefix.'sh_template',array('name'=> 'ShortCode Template','desc' => 'Used for shortcodes with HTML tags, CSS and JavaScript Code.<br/>Use this to avoid WordPress from Striping tags.<br/> if you want to include the editor content(the one from above) then place {SC_CONTENT} token in your template,<br/>If this shortcode uses content in his tags then place {CONTENT} token in your template,<br/> You can Also include any attribute you have added to this shortcode using a token eg: {attribute name}'));
			$dsec ='<br/>
<span class="sc_p_img"></span>
<script>
if (jQuery(\'input[name="_bascsh_preview_image"]\').val() != \'\'){
	jQuery(".sc_p_img").append(\'Preview:<br/> \');
	jQuery(".sc_p_img").append(\'<img id="theImg" src="\' + jQuery(\'input[name="_bascsh_preview_image"]\').val() + \'" />\');
}
</script>';
			$my_meta->addText($prefix.'sh_preview_image',array('name'=> 'Preview Image','desc' =>$dsec));
			$my_meta->Finish();
			
			//advanced metabox
			$prefix = '_basc';
			$config = array(
				'id' => 'sc_advanced_meta_box',					// meta box id, unique per meta box
				'title' => 'ShortCode Advanced Settings',					// meta box title
				'pages' => array('ba_sh'),			// post types, accept custom post types as well, default is array('post'); optional
				'context' => 'normal',						// where the meta box appear: normal (default), advanced, side; optional
				'priority' => 'high',						// order of meta box: high (default), low; optional
				'fields' => array()							// list of meta fields (can be added by field arrays)
			);
			$my_meta_2 =  new AT_Meta_Box($config);
			$repeater_fields[] = $my_meta_2->addText($prefix.'_name',array('name'=> 'Attribute Name','group' =>'start' ),true);
			$repeater_fields[] = $my_meta_2->addText($prefix.'_std',array('name'=> 'Attribute Default Value','group' =>'end'),true);
			$repeater_fields[] = $my_meta_2->addTextarea($prefix.'_options',array('name'=> 'Attribute Value Options','desc' => 'Insert One option in each line' ),true);
			$repeater_fields[] = $my_meta_2->addTextarea($prefix.'_desc',array('name'=> 'Attribute Description','desc' => 'Enter a short description of this attribute field' ),true);
			
			$my_meta_2->addRepeaterBlock($prefix.'sh_attr',array('name' => 'ShortCode Attributes','fields' => $repeater_fields,'inline'=>true));
			$my_meta_2->addTextarea($prefix.'sh_style',array('name'=> 'CSS Style','desc' => 'If your Shortcode have stylesheet classes defined, you can add style sheet definitions here. <br/>This will be output in the page footer. Also leave out opening and ending &lt;style&gt;&lt;/style&gt; tags'));
			$my_meta_2->addTextarea($prefix.'sh_js',array('name'=> 'JavaScipt','desc' => 'Must Be a valid JavaScript Code in order for it to Work.<br/>This will be output in the page footer. Also leave out opening and ending &lt;script&gt;&lt;/script&gt; tags'));
			$my_meta_2->addTextarea($prefix.'sh_php',array('name'=> 'PHP Code','desc' => 'Must Be a valid PHP Code in order for it to Work, Also leave out opening and ending &lt;?php ?&gt; tags'));
			$my_meta_2->addRadio($prefix.'php_type',array('echo'=>'my code uses PHP echo','return'=>'my code uses PHP return'),array('name'=> 'What does this code do?', 'std'=> array('echo')));
			
			
			$my_meta_2->Finish();
			
			//external metabox
			$prefix = '_basc';
			$config = array(
				'id' => 'sc_external_meta_box',					// meta box id, unique per meta box
				'title' => 'External Files Section',					// meta box title
				'pages' => array('ba_sh'),			// post types, accept custom post types as well, default is array('post'); optional
				'context' => 'normal',						// where the meta box appear: normal (default), advanced, side; optional
				'priority' => 'high',						// order of meta box: high (default), low; optional
				'fields' => array()							// list of meta fields (can be added by field arrays)
			);
			$my_meta_3 =  new AT_Meta_Box($config);
			$repeater_fields2[] = $my_meta_3->addText($prefix.'_url',array('name'=> 'External File URL'),true);
			$repeater_fields2[] = $my_meta_3->addSelect($prefix.'sh_ex_f_type',array('script'=>'JavaScript','link'=>'CSS Stylesheet'),array('desc'=> 'Select External File Type','name'=> 'External File Type', 'std'=> array('script')),true);
			$repeater_fields2[] = $my_meta_3->addSelect($prefix.'sh_location',array('head'=>'Before &lt;/HEAD&gt; Tag','body'=>'Before &lt;/BODY&gt; Tag'),array('desc'=> 'Use this to add external JS and CSS Files','name'=> 'Where to Include', 'std'=> array('body')),true);
			
			$my_meta_3->addRepeaterBlock($prefix.'sh_external',array('name' => 'External Files','fields' => $repeater_fields2,'inline'=>true));

			$my_meta_3->Finish();
			
			
			//author metabox
			$prefix = '_basc';
			$config = array(
				'id' => 'sc_Author_meta_box',					// meta box id, unique per meta box
				'title' => 'ShortCode Author',					// meta box title
				'pages' => array('ba_sh'),			// post types, accept custom post types as well, default is array('post'); optional
				'context' => 'side',						// where the meta box appear: normal (default), advanced, side; optional
				'priority' => 'high',						// order of meta box: high (default), low; optional
				'fields' => array()							// list of meta fields (can be added by field arrays)
			);
			$my_meta_4 =  new AT_Meta_Box($config);
			$my_meta_4->addText($prefix.'_Author_Name',array('name'=> 'Author Name'));
			$my_meta_4->addText($prefix.'_Author_url',array('name'=> 'Author Url'));
			$my_meta_4->addText($prefix.'_Support_url',array('name'=> 'ShortCode Support Url'));
			
			$my_meta_4->Finish();
	
		}
		
		//simple shortcode handler
		public function simple_shortcode_handler($attr,$content = null,$tag=''){
			$sc_id = $this->sc_tags[$tag]['id'];
			$sc_meta = get_post_custom($sc_id);
			
			$sc_content = $sc_template = '';
			
			if (isset($sc_meta['_bascsh_template'][0]) && $sc_meta['_bascsh_template'][0] != '' )
				$sc_template = $sc_meta['_bascsh_template'][0];
				
			$sc_content = $this->get_sc_content($sc_id);
			
			if ($sc_content == '' && $sc_template == '') return '';
			
			if(isset($sc_meta['_bascsh_attr'][0]) ){
				$shortcode_attributes = unserialize($sc_meta['_bascsh_attr'][0]);
				if (is_array($shortcode_attributes) && count($shortcode_attributes > 0)){
					$args = array();
					foreach ($shortcode_attributes as $at){
						if (isset($attr[$at['_basc_name']])){
							//$content = str_replace('{'.$at['_basc_name'].'}',$attr[$at['_basc_name']],$content);
							$sc_content  = str_replace('{'.$at['_basc_name'].'}',$attr[$at['_basc_name']],$sc_content);
							$sc_template  = str_replace('{'.$at['_basc_name'].'}',$attr[$at['_basc_name']],$sc_template);
						}else{
							//$content = str_replace('{'.$at['_basc_name'].'}',$at['_basc_std'],$content);
							$sc_content  = str_replace('{'.$at['_basc_name'].'}',$at['_basc_std'],$sc_content);
							$sc_template = str_replace('{'.$at['_basc_name'].'}',$at['_basc_std'],$sc_template);
						}
						$args[$at['_basc_name']] = $at['_basc_std'];
					}
					extract(shortcode_atts($args, $attr));
				}
			}
					
			$content = str_replace('{SC_CONTENT}',$sc_content,$sc_template);
			
			//check for JavaScript and Css code
			if (!isset($this->sc_media[$tag])){
				if (isset($sc_meta['_bascsh_js'][0]))
					$this->sc_media[$tag]['js'] = $sc_meta['_bascsh_js'][0];
				if (isset($sc_meta['_bascsh_style'][0]))
					$this->sc_media[$tag]['css'] = $sc_meta['_bascsh_style'][0];
			}
			
			return do_shortcode($content);
		}
		
		//snippet shortcode handler
		public function snippet_shortcode_handler($attr,$content = null,$tag=''){
			$sc_id = $this->sc_tags[$tag]['id'];
			$con = ''; 
			$con = $this->get_sc_content($sc_id);
			return do_shortcode($con);
		}
		
		//content shortcode handler
		public function content_shortcode_handler($attr,$content = null,$tag=''){
			$sc_id = $this->sc_tags[$tag]['id'];
			$sc_meta = get_post_custom($sc_id);
			
			$sc_content = $sc_template = '';
			
			if (isset($sc_meta['_bascsh_template'][0]) && $sc_meta['_bascsh_template'][0] != '' ){
				//shortcode template
				$sc_template = $sc_meta['_bascsh_template'][0];
			}

			//shortcode editor content
			$sc_content = $this->get_sc_content($sc_id);
			
			if ($sc_content == '' && $sc_template == '' && $content == null) return '';
			

			
			if(isset($sc_meta['_bascsh_attr'][0]) ){
				$shortcode_attributes = unserialize($sc_meta['_bascsh_attr'][0]);
				if (is_array($shortcode_attributes) && count($shortcode_attributes > 0)){
					$args = array();
					foreach ($shortcode_attributes as $at){
						if (isset($attr[$at['_basc_name']])){
							$content = str_replace('{'.$at['_basc_name'].'}',$attr[$at['_basc_name']],$content);
							$sc_content  = str_replace('{'.$at['_basc_name'].'}',$attr[$at['_basc_name']],$sc_content);
							$sc_template  = str_replace('{'.$at['_basc_name'].'}',$attr[$at['_basc_name']],$sc_template);
						}else{
							$content = str_replace('{'.$at['_basc_name'].'}',$at['_basc_std'],$content);
							$sc_content  = str_replace('{'.$at['_basc_name'].'}',$at['_basc_std'],$sc_content);
							$sc_template = str_replace('{'.$at['_basc_name'].'}',$at['_basc_std'],$sc_template);
						}
						$args[$at['_basc_name']] = $at['_basc_std'];
					}
					extract(shortcode_atts($args, $attr));
				}
			}
			
			
			$contenti = str_replace('{SC_CONTENT}',$sc_content,$sc_template);
			if (!empty($content) && $content != '' && false !== strpos($contenti, '{CONTENT}')){
				$content = str_replace('{CONTENT}',$content,$contenti);
			}else{
				$content = $contenti;
			}	
				
			
			
			
			//check for JavaScript and Css code
			if (!isset($this->sc_media[$tag])){
				if (isset($sc_meta['_bascsh_js'][0]))
					$this->sc_media[$tag]['js'] = $sc_meta['_bascsh_js'][0];
				if (isset($sc_meta['_bascsh_style'][0]))
					$this->sc_media[$tag]['css'] = $sc_meta['_bascsh_style'][0];
			}
			
			return do_shortcode($content);
		}
		
		//advanced shortcode handler
		public function advanced_shortcode_handler($attr,$content = null,$tag=''){
			$sc_id = $this->sc_tags[$tag]['id'];
			$sc_meta = get_post_custom($sc_id);
			if (!isset($sc_meta['_bascsh_php'][0])) return '';
			
			$code = trim($sc_meta['_bascsh_php'][0]);
			if ($code == '') return '';
			
			
			//$sc_content = $sc_template = '';
			
			//if (isset($sc_meta['_bascsh_template'][0]) && $sc_meta['_bascsh_template'][0] != '' )
				//$sc_template = $sc_meta['_bascsh_template'][0];
				
			$sc_content = $this->get_sc_content($sc_id);
			
			//if ($sc_content == '' && $sc_template == '' && $content == null) return '';
			
			if(isset($sc_meta['_bascsh_attr'][0]) ){
				$shortcode_attributes = unserialize($sc_meta['_bascsh_attr'][0]);
				if (is_array($shortcode_attributes) && count($shortcode_attributes > 0)){
					$args = array();
					foreach ($shortcode_attributes as $at){
						/*
						if (isset($attr[$at['_basc_name']])){
							$content = str_replace('{'.$at['_basc_name'].'}',$attr[$at['_basc_name']],$content);
							$sc_content  = str_replace('{'.$at['_basc_name'].'}',$attr[$at['_basc_name']],$sc_content);
							$sc_template  = str_replace('{'.$at['_basc_name'].'}',$attr[$at['_basc_name']],$sc_template);
						}else{
							$content = str_replace('{'.$at['_basc_name'].'}',$at['_basc_std'],$content);
							$sc_content  = str_replace('{'.$at['_basc_name'].'}',$at['_basc_std'],$sc_content);
							$sc_template = str_replace('{'.$at['_basc_name'].'}',$at['_basc_std'],$sc_template);
						}
						*/
						$args[$at['_basc_name']] = $at['_basc_std'];
					}
					extract(shortcode_atts($args, $attr));
				}
			}
			
			$return_val = '';
			
			//echo php
			if (isset($sc_meta['_bascphp_type']) && $sc_meta['_bascphp_type'] == 'echo'){
			
				try{
					ob_start();
					eval($code);
					$return_val = ob_get_contents();
					ob_end_clean();			
				}catch(Exception $e){
					
				}
			}else{//return php
				try{
				$return_val = eval($code);
				}catch(Exception $e){
					
				}
			}
			
			
			//check for JavaScript and Css code
			if (!isset($this->sc_media[$tag])){
				if (isset($sc_meta['_bascsh_js'][0]))
					$this->sc_media[$tag]['js'] = $sc_meta['_bascsh_js'][0];
				if (isset($sc_meta['_bascsh_style'][0]))
					$this->sc_media[$tag]['css'] = $sc_meta['_bascsh_style'][0];
			}
			
			return do_shortcode($return_val);
		}
		
		//helper function to get shortcode content (the one from the editor)
		public function get_sc_content($pid){
			global $wpdb;
			$sc_content = $wpdb->get_var( $wpdb->prepare( 
			"
				SELECT post_content 
				FROM $wpdb->posts 
				WHERE post_type = 'ba_sh' 
				AND post_status = 'publish'
				AND  ID = %s
				LIMIT 1
			", 
			$pid
			) );
			
			return $sc_content;
		}
		
		//helper debug function pre_var_dump
		public function pre_var_dump($var){
			echo '<pre>';
			var_dump($var);
			echo '</pre>';
		} 
		
		//print JavaScript
		public function print_footer_Scripts(){
			$js = $css =  '';
			foreach($this->sc_media as $sc){
				if (isset($sc['js']))
					$js .= "\n" . $sc['js']; 
				if (isset($sc['css']))
					$css .= "\n" . $sc['css'];
			}
			if ($css != '')
				echo '<style>'.$css.'</style>';
			if ($js != '')
				echo '<script>'.$js.'</script>';
		}
		
		//print head scripts and styles
		public function external_print_head_Scripts(){
			if (isset($this->sc_external)){
				if (is_array($this->sc_external) && count($this->sc_external) > 0){
					foreach ($this->sc_external as $key => $arr){
						if (isset($arr['found']) && $arr['found'] && isset($arr['head']) ){
							foreach ((array)$arr['head'] as $pair){
								foreach ($pair as $type => $url)
								if ('script' == $type){
									echo '<script type="text/javascript" src="'.$url.'"></script>';
								}else{
									echo '<link href="'.$url.'" media="all" type="text/css" rel="stylesheet">';
								}	
							}
						}
					}
				}
			}
		}
		
		//print footer scripts and styles
		public function external_print_footer_Scripts(){
			if (isset($this->sc_external)){
				if (is_array($this->sc_external) && count($this->sc_external) > 0){
					foreach ($this->sc_external as $key => $arr){
						if (isset($arr['found']) && $arr['found'] && isset($arr['body']) ){
							foreach ((array)$arr['body'] as $pair){
								foreach ($pair as $type => $url)
								if ('script' == $type){
									echo '<script type="text/javascript" src="'.$url.'"></script>';
								}else{
									echo '<link href="'.$url.'" media="all" type="text/css" rel="stylesheet">';
								}	
							}
						}
					}
				}
			}
		}
		
		/*
		 * Export Import Functions
		 */
		
		//export action Row
		public function Export_shortcodes_Row_action($actions, $post){
    		if ($post->post_type =="ba_sh"){
        		$actions['Export'] = '<a href="#" sc_id="'.$post->ID.'">Export ShortCode</a>';
    		}
    		return $actions;
		}
		
		public function sc_ui_import_export_menupage() {
	 	   add_submenu_page( 'edit.php?post_type=ba_sh', 'ShortCodes UI Import Export', 'Import/Export', 'manage_options', 'sc_ui_ie', array($this,'sc_ui_import_export_page'));
		}
	
		//import export panel
		public function sc_ui_import_export_page(){
			global $wpdb;
			?>
				<div class="wrap">
					<div id="icon-plugins" class="icon32"></div><h2><a href="http://en.bainternet.info" target="_blank">BaInternet</a> ShortCodes UI <?php _e('Import/Export'); ?></h2>
						<div id="tabs">
						<ul>
							<li><a href="#Export">Export ShortCodes</a></li>
							<li><a href="#Import">Import Shortcodes</a></li>
							<li><a href="#stn">Export As Standalone Plugin</a></li>
						</ul>
						<div id="Export">
							<h4><?php _e('Export'); ?></h4>
							<p><?php _e('Select ShortCodes To Export'); ?> <small>(<?php _e('Hold CRTL to select multiple shortcode'); ?>)</small></p>
							<?php 
							$args = array( 'posts_per_page' => -1, 'post_type' => 'ba_sh', 'fields' =>'ids' );
							$myshortcodes = get_posts( $args );
							if (count($myshortcodes) > 0){
								$myshortcodes = implode(',', $myshortcodes);
								$ids_with_titles = $wpdb->get_results( 
									"
										SELECT ID, post_title 
										FROM $wpdb->posts
										WHERE ID IN  ({$myshortcodes})
									"
								);
								if (count($ids_with_titles) > 0){
									echo '<select id="sc_to_ex" name="sc_to_ex[]" multiple="multiple" style="height: 18em;">';
									foreach( $ids_with_titles as $p){
										echo '<option value="'.$p->ID.'">'.$p->post_title.'</option>';
									}
									echo '</select>';
									echo '<p><input class="button-primary" type="button" name="export" value="'.__('Export ShortCodes').'" id="su_ui_export" />';
									echo '<input type="hidden" id="sc_ui_Get_Export_code" name="sc_ui_Get_Export_code" value="'.wp_create_nonce("sc_ui_Get_Export_code").'" />';
									echo '<div class="sc_ex_status" style="display: none;"><img src="http://i.imgur.com/l4pWs.gif" alt="loading..."/></div>';
									echo '<div class="export_code" style="display: none"><label for="export_code">'.__('Export Code').'</label><br/>
									<textarea id="export_code" style="width: 760px; height: 160px;"></textarea><br/>
									<p>'.__('Copy this code to and paste it at this page in the WordPress Install you want to use this shortcodes in at the buttom box under Import Code').'</p>
									</div>';
									
								}else{
									echo '<p>No ShortCodes are avialble!</p>';
								}
							}else{
								echo '<p>No ShortCodes are avialble!</p>';
							}
							?>
						</div>
						<div id="Import">
							<h4><?php _e('Import'); ?></h4>
							<p><?php _e('To Import ShortCodes paste the Export output in to the Import Code box bellow and click Import.'); ?></p>
							<div class="import_code"><label for="import_code"><?php _E('Import Code');?></label><br/>
								<textarea id="import_code" style="width: 760px; height: 160px;"></textarea><br/>
								<input type="hidden" id="sc_ui_Import_sc" name="sc_ui_Import_sc" value="<?php echo wp_create_nonce("sc_ui_Import_sc");?>" />
								<input class="button-primary" type="button" name="import" value="<?php _E('Import ShortCodes');?>" id="su_ui_import" />
								<div class="sc_im_status" style="display: none;"><img src="http://i.imgur.com/l4pWs.gif" alt="loading..."/></div>
								<div class="im-results" style="display: none;"></div>
							
							</div>
						</div>
						<div id="stn">
							<h4><?php _e('Export Shortcode as Standalone Plugin'); ?></h4>
							<p style="color: red;font-size: 28px;><strong><?php _e('Comming soon!')?></strong></p>
							<p><?php _e('You can Use this option to export a shortcode as a plugin and and install it in any site you want, sell it or share it at the WordPress Plugin repository, Anything YOU WANT.')?></p>
						
						</div>	
				</div>
			</div>
			<?php
		}
		
		//load import/export js code
		public function sc_ui_import_export_scripts(){
			wp_enqueue_script('jquery'); 
			wp_enqueue_script('jquery-ui-core');
			wp_enqueue_script('jquery-ui-tabs');
			$src = plugins_url()."/shortcode-ui/";
			wp_enqueue_script('sc_ui_im_ex', $src.'js/im_ex.js', array('jquery'),'', true);
		}
		
		//load import/export css code
		public function sc_ui_import_export_styles(){
			wp_enqueue_style( 'jquery-ui', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/themes/smoothness/jquery-ui.css' );
		}
		
		//get Shortcode to Export function
		public function get_shortcode_export($post_id){
			//shortcode post row
			$po = get_post($post_id,'ARRAY_A');
			$p = array();
			$fs = array('post_content','post_title','post_status','post_excerpt','comment_status','post_password','post_type');
			foreach($fs as $key){
				$p[$key] = $po[$key]; 	
			}
			//shortcode meta
			$meta = get_post_custom($post_id);
			unset($meta['_edit_last']);
			unset($meta['_edit_lock']);
			
			//shortcode tax
			$tax = array();
			$taxs = wp_get_object_terms($post_id, 'bs_sh_cats');
			foreach ((array)$taxs as $t){
				$tax[] = $t->slug;
			}
			$tag = $meta['_bascsh_tag'][0];
			return array('p' => $p, 'meta' => $meta, 'tax' => $tax, 'tag' => $tag);
		}
		
		//ajax Export function
		public function sc_ui_ajax_export(){
			check_ajax_referer( 'sc_ui_Get_Export_code', 'seq' );
			if (!isset($_GET['sc_ids'])){
				echo json_encode($re['errors'] = __('No ShortCode were found!'));
				die();
			}
			
			foreach ((array)$_GET['sc_ids'] as $p){
				$re['code'][] = $this->get_shortcode_export($p);
			}
			$re['code']= "<!*!* START export Code !*!*>\n".base64_encode(serialize($re['code']))."\n<!*!* END export Code !*!*>";
			//update nonce
			$re['nonce'] = wp_create_nonce("sc_ui_Get_Export_code");
			
			echo json_encode($re);
			die();
		}
		
		//import shortcode to database function 
		function import_shortcode($sc){
			if (in_array($sc['tag'],array_keys($this->sc_tags))){
				return array('sc_title' => $sc['p']['post_title'], 'status' => __('ShortCode already Exists with this tag'), 'tag' => $sc['tag']);
			}else{
				//insert shortcode post row
				$sc_id = wp_insert_post($sc['p']);
				if (!is_wp_error($sc_id) && $sc_id > 0){
					//insert meta
					foreach ($sc['meta'] as $k => $v){
						update_post_meta($sc_id,$k,$v[0]);
					}
					//taxonomy
					wp_set_object_terms($sc_id,(array)$sc['tax'],'bs_sh_cats');
					return array('sc_title' => $sc['p']['post_title'], 'status' => __('Imported Successfully'), 'tag' => $sc['tag']);
				}else{
					return array('sc_title' => $sc['p']['post_title'], 'status' =>__('Error in Importting Shortcode'), 'tag' => $sc['tag']);
				}
			} 
			
		}
		
		//ajax Import function
		public function sc_ui_ajax_import(){
			check_ajax_referer( 'sc_ui_Import_sc', 'seq2' );
			if (!isset($_GET['import_code'])){
				echo json_encode($re['errors'] = __('No Import Code Was Found!'));
				die();
			}
			
			//prepare import code
			$import_code = $_GET['import_code'];
			$import_code = str_replace("<!*!* START export Code !*!*>\n","",$import_code);
			$import_code = str_replace("\n<!*!* END export Code !*!*>","",$import_code);
		   	$import_code = base64_decode($import_code);
			$import_code = unserialize($import_code);
			
			if (is_array($import_code)){
			     foreach  ($import_code as $shortcode){
			     	$re['status'][] = $this->import_shortcode($shortcode);
			     	$re['dump'] = $shortcode['tax'];
			     }
			      
				
			}
			$re['nonce'] = wp_create_nonce("sc_ui_Import_sc");
			echo json_encode($re);
			die();
		}
		
		
		
		//avoid duplicates and bad tag names
		public function avoid_duplicate_shortcode_tags($sc_tag,$sc_id){
			$tmp_tag = $sc_tag;
			$shortcode_exists = true;
			$counter_tag = 0;
			$sc_tag = str_replace(' ','_',$sc_tag);
			$sc_tag = str_replace('  ','_',$sc_tag);
			$sc_tag = str_replace('.','_',$sc_tag);
			while ($shortcode_exists){
				if (!array_key_exists($sc_tag,array_keys($this->sc_tags))){
					$shortcode_exists = false;
				}else{
					$sc_tag = $sc_tag.'_'.$counter_tag;
					$counter_tag = $counter_tag + 1; 
				}
			}
			if ($tmp_tag != $sc_tag){
				update_post_meta($sc_id,'_bascsh_tag',$sc_tag);
			}
			return $sc_tag;
		}
	}//end class
}//end if

new BA_ShortCode_Maker();