<?php
/**
* admin pages class and generator
 */
if (!class_exists('shui_AdminPage')){
  class shui_AdminPage {
    /**
     * Contains the menu_slug for the current TopLeve-Menu
     * @var string
     */
    public $top;
    
    /**
     * Contains all arguments needed to build the page itself
     * @var array
     */
    protected $args;
    
    /**
     * Contains all the information needed to build the form structure of the page
     * @var array
     */
    private $boxes;
    
    /**
     * True if the table is opened, false if it is not opened
     * @var boolean
     */
    private $table = false;
    
    /**
     * Holds page slug
     * @var string;
     */
    public $slug;
        
    /**
     * Adds an input field to the current page
     *
     * Possible keys within $args:
     *  > id (string) - This is what you need to get your variable from the database
     *  > label (string) - Describes your field very shortly
     *  > desc (string) (optional) - Further description for this element
     *  > standard (string) (optional) - This is the standard value of your input
     *  > size (string) (optional) - sets the width, can be: small, short, regular and large
     *
     * @param array $args contains everything needed to build the field
     */
    public function addInput($args) {
      $default = array(
        'size' => 'regular',
      );
      $args = array_merge($default, $args);
      $args['type'] = 'input';
      $this->addField($args);
    }
      /**

     * Adds an colorpicker field to the current page
     *
     * Possible keys within $args:
     *  > id (string) - This is what you need to get your variable from the database
     *  > label (string) - Describes your field very shortly
     *  > desc (string) (optional) - Further description for this element
     *  > standard (string) (optional) - This is the standard value of your colorpicker
     *  > size (string) (optional) - sets the width, can be: small, short, regular and large
     *
     * @param array $args contains everything needed to build the field
     */
    public function addColorPicker($args) {
      $default = array(
      );
      $args = array_merge($default, $args);
      $args['type'] = 'colorpicker';
      $args['size'] = 'small';
      $this->addField($args);
    }
    
    /**
     * Adds a textarea to the current page
     *
     * Possible keys within $args:
     *  > id (string) - This is what you need to get your variable from the database
     *  > label (string) - Describes your field very shortly
     *  > desc (string) (optional) - Further description for this element
     *  > standard (string) (optional) - This is the standard value of your field
     *  > rows (integer) (optional) - The number of rows you want to have, standard: 5
     *  > cols (integer) (optional) - The number of cols you want to have, standard: 30
     *  > width (integer) (optional) - How wide should the textarea be?, standard:500
     *
     * @param array $args contains everything needed to build the field
     */
    public function addTextarea($args) {
      $default = array(
        'rows' => 5,
        'cols' => 30,
        'width' => 500,
      );
      $args = array_merge($default, $args);
      $args['type'] = 'textarea';
      $this->addField($args);
    }
    
    /**
     * Adds a TinyMCE editor to the current page
     *
     * Possible keys within $args:
     *  > id (string) - This is what you need to get your variable from the database
     *  > label (string) - Describes your field very shortly
     *  > desc (string) (optional) - Further description for this element
     *  > standard (string) (optional) - This is the standard value of your input
     *
     * @param array $args contains everything needed to build the field
     */
    public function addEditor($args) {
      $args['type'] = 'editor';
      $this->addField($args);
    }
    
    /**
     * Adds a heading to the current page
     *
     * @param string $label simply the text for your heading
     */
    public function addTitle($label) {
      $args['type'] = 'title';
      $args['standard'] = '';
      $args['label'] = $label;
    $args['id'] = 'title'.$label;
      $this->addField($args);
    }
    
    /**
     * Adds a sub-heading to the current page
     *
     * @param string $label simply the text for your heading
     */
    public function addSubtitle($label) {
      $args['type'] = 'subtitle';
      $args['label'] = $label;
    $args['id'] = 'title'.$label;
    $args['standard'] = '';
      $this->addField($args);
    }
    
    /**
     * Adds a paragraph to the current page
     *
     * @param string $text the text you want to display
     */
    public function addParagraph($text) {
      $args['type'] = 'paragraph';
      $args['text'] = $text;
    $args['id'] = 'paragraph';
    $args['standard'] = '';
      $this->addField($args);
    }
    
    /**
     * Adds a checkbox to the current page
     *
     * Possible keys within $args:
     *  > id (string) - This is what you need to get your variable from the database
     *  > label (string) - Describes your field very shortly
     *  > desc (string) - Further description for this element
     *  > standard (bool) - Define wether the checkbox should be checked our unchecked
     *
     * @param array $args contains everything needed to build the field
     */
    public function addCheckbox($args) {
      $args['type'] = 'checkbox';
      $this->addField($args);
    }
    
  
  /**
     * Adds a checkbox array to the current page
     *
     * Possible keys within $args:
     *  > id (string) - This is what you need to get your variable from the database
     *  > label (string) - Describes your field very shortly
     *  > desc (string) - Further description for this element
     *  > standard (bool) - Define wether the checkbox should be checked our unchecked
     *  > options (array) - list of options to add to the array
     * @param array $args contains everything needed to build the field
     */
    public function addCheckboxArray($args) {
      $args['type'] = 'checkboxArray';
      $this->addField($args);
    }
  
  
    /**
     * Adds radiobuttons field to the current page
     *
     * Possible keys within $args:
     *  > id (string) - This is what you need to get your variable from the database
     *  > label (string) - Describes your field very shortly
     *  > standard (string) (optional) - Define which of the options should be checked if there is nothing in the database
     *  > options (array) - An array containing the options to choose from, written in this style: LABEL => VALUE
     *
     * @param array $args contains everything needed to build the field
     */
    public function addRadiobuttons($args) {
      $args['type'] = 'radio';
      $this->addField($args);
    }
    
  /**
     * Adds Imageradiobuttons field to the current page
     *
     * Possible keys within $args:
     *  > id (string) - This is what you need to get your variable from the database
     *  > label (string) - Describes your field very shortly
     *  > standard (string) (optional) - Define which of the options should be checked if there is nothing in the database
     *  > options (array) - An array containing the options to choose from, written in this style: LABEL => VALUE
     *  > directory_url (string) - 
     *
     * @param array $args contains everything needed to build the field
     */
    public function addImageRadiobuttons($args) {
      $args['type'] = 'imageradio';
      $this->addField($args);
    }
    
    /**
     * Adds a dropdown field to the current page
     *
     * Possible keys within $args:
     *  > id (string) - This is what you need to get your variable from the database
     *  > label (string) - Describes your field very shortly
     *  > desc (string) (optional) - Describes your field very shortly
     *  > standard (string) - Define which of the options should be checked if there is nothing in the database
     *  > options (array) - An array containing the options to choose from, written in this style: LABEL => VALUE
     *
     * @param array $args contains everything needed to build the field
     */
    public function addDropdown($args) {
      $args['type'] = 'dropdown';
      $this->addField($args);
    }
  	
  	/**
     * Adds a dropdown field to the current page with a list of wp roles
     *
     * Possible keys within $args:
     *  > id (string) - This is what you need to get your variable from the database
     *  > label (string) - Describes your field very shortly
     *  > desc (string) (optional) - Describes your field very shortly
     *
     * @param array $args contains everything needed to build the field
     */
    public function addRoles($args) {
      $args['type'] = 'wp_roles';
      $this->addField($args);
    }
    
    /**
     * Adds a posts checkboxes list field to the current page
     *
     * Possible keys within $args:
     *  > id (string) - This is what you need to get your variable from the database
     *  > ptype (string) - Post type: ('post,page,custom)
     *  > label (string) - Describes your field very shortly
     *  > desc (string) (optional) - Describes your field very shortly
     *  > standard (string) - Define which of the options should be checked if there is nothing in the database
     *  > options (array) - An array containing the options to choose from, written in this style: LABEL => VALUE
     *
     * @param array $args contains everything needed to build the field
     */
     
     public function addPosts($args) {
      $args['type'] = 'posts';
      $this->addField($args);
    }
    
    
    /**
     * Adds a taxonomy dropdwon list field to the current page
     *
     * Possible keys within $args:
     *  > id (string) - This is what you need to get your variable from the database
     *  > taxonomy (string) - category,post_tags,custom
     *  > label (string) - Describes your field very shortly
     *  > desc (string) (optional) - Describes your field very shortly
     *  > standard (string) - Define which of the options should be checked if there is nothing in the database
     *  > options (array) - An array containing the options to choose from, written in this style: LABEL => VALUE
     *
     * @param array $args contains everything needed to build the field
     */
     
     public function addTaxonomy($args) {
      $args['type'] = 'tax';
      $this->addField($args);
    }
    
    /**
     * Adds a post dropdown list field to the current page
     *
     * Possible keys within $args:
     *  > id (string) - This is what you need to get your variable from the database
     *  > ptype (string) - Post type: ('post,page,custom)
     *  > label (string) - Describes your field very shortly
     *  > desc (string) (optional) - Describes your field very shortly
     *  > standard (string) - Define which of the options should be checked if there is nothing in the database
     *  > options (array) - An array containing the options to choose from, written in this style: LABEL => VALUE
     *
     * @param array $args contains everything needed to build the field
     */
     
     public function addPost($args) {
      $args['type'] = 'post';
      $this->addField($args);
    }
    
    /**
     * Adds an image-upload to the current page
     *
     * Possible keys within $args:
     *  > id (string) - This is what you need to get your variable from the database
     *  > label (string) - Describes your field very shortly
     *  > desc (string) (optional) - Describes your field very shortly
     *  > title (string) (optional) - If set, an input is added to the uploader where you can put additional info
     *
     * @param array $args contains everything needed to build the field
     */
     
    public function addUpload($args) {
      $args['type'] = 'upload';
      $this->addField($args);
    }
    
    /**
     * Adds a slider to the current page
     *
     * Possible keys within $args:
     *  > id (string) - This is what you need to get your variable from the database
     *  > label (string) - Describes your field very shortly
     *  > standard (integer|array) (optional) - The starting position of your slider, if it is an array, a range slider is build
     *  > max (integer) - The maximum value of your slider
     *  > min (integer) - The minimum value of your slider
     *  > step (integer) - The stepsize of your slider
     *
     * @param array $args contains everything needed to build the field
     */
    public function addSlider($args) {
      $default = array(
        'standard' => 0,
        'max' => 100,
        'min' => 0,
        'step' => 1,
      );
      $args = array_merge($default,$args);
      $args['type'] = 'slider';
      $this->addField($args);
    }
    
    /**
     * Adds a datepicker to the current page
     *
     * Possible keys within $args:
     *  > id (string) - This is what you need to get your variable from the database
     *  > label (string) - Describes your field very shortly
     *  > desc (string) (optional) - Describes your field very shortly
     *  > standard (string) (optional) - The standard date in the format: MM/DD/YYYY
     *
     * @param array $args contains everything needed to build the field
     */
    public function addDate($args) {
      $args['type'] = 'date';
      $date = explode('/', $args['standard']);
      if(isset($date[2])) $args['standard'] = mktime(0,0,0,$date[0],$date[1],$date[2]);
      $this->addField($args);
    }
    
    /**
    * Adds tabs current page
    */
    public function OpenTabs_container($text) {
      $args['type'] = 'TABS';
      $args['text'] = $text;
      $args['id'] = 'TABS';
      $args['standard'] = '';
      $this->addField($args);
    }
    
    public function CloseDiv_Container() {
      $args['type'] = 'CloseDiv';
      $args['id'] = 'CloseDiv';
      $args['standard'] = '';
      $this->addField($args);
    }
    
    public function TabsListing($args) {
      $args['type'] = 'TABS_Listing';
      $args['id'] = 'TABS_Listing';
      $args['standard'] = '';
      $this->addField($args);
    }
    
    public function OpenTab($name) {
      $args['type'] = 'OpenTab';
      $args['id'] = $name;
      $args['standard'] = '';
      $this->addField($args);
    }
    
    
    /**
     * Does the repetive tasks of adding a field
     * @access private
     */
    private function addField($args) {
    $default = array(
        'standard' => '',
    'id' => ''
      );
      $args = array_merge($default, $args);
      $this->buildOptions($args);
      $this->boxes[] = $args;
    }
    
    /**
     * Builds all the options with their standard values
     * @access private
     */
    private function buildOptions($args) {
      $default = array(
        'standard' => '',
    'id' => ''
      );
      $args = array_merge($default, $args);
      $saved = get_option($this->args['option_group']);
    if (isset($saved[$args['id']])){
      if($saved[$args['id']] === false) {
      $saved[$args['id']] = $args['standard'];
      update_option($this->args['option_group'],$saved);
      }
    }
    }
    
    /**
     * Outputs all the HTML needed for the new page
     * @access private
     */
    public function outputHTML() {
      
      echo '<div class="wrap">';
      echo '<h2>'.$this->args['page_title'].'</h2>';
      echo '<form method="post" action="" enctype="multipart/form-data">';
    
      if(isset($_POST['action']) && $_POST['action'] == 'save') {
        echo '<div class="updated settings-error"><p><strong>'.__('Settings saved.').'</strong></p></div>';
        $this->save();
      }
      $saved = get_option($this->args['option_group']);
      echo '<style>.editorcontainer { -webkit-border-radius:6px; border:1px solid #DEDEDE;}</style>';
      echo '<link href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/base/jquery-ui.css" rel="stylesheet" />';
      echo '<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.6.1/jquery.min.js"></script>';
      echo '<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/jquery-ui.min.js"></script>';
      //echo '<script type="text/javascript" src="'.WP_PLUGIN_URL . '/wordpress-group-buying/admin/jscolor.js"></script>';
      foreach($this->boxes as $box) {
        if($box['type'] != 'title' && $box['type'] != 'paragraph' && $box['type'] != 'subtitle' && $box['type'] != 'TABS' && $box['type'] != 'CloseDiv' &&  $box['type'] != 'TABS_Listing' &&  $box['type'] != 'OpenTab') {
          if(!$this->table) {
            echo '<table class="form-table">';
            $this->table = true;
          }
          echo '<tr valign="top">';
          echo '<td width="25%"><label for="'.$box['id'].'">'.$box['label'].':</label></td>';
        } else {
          if($this->table) {
            echo '</table>';
            $this->table = false;
          }
        }
    if (isset($saved[$box['id']])){
      $data = $saved[$box['id']];
    }
        if (!isset($data)){
          $data = $box['standard'];
        }
        switch($box['type']) {
          case 'TABS':
                echo '<script>
                  jQuery(function() {
                    jQuery( "#tabs" ).tabs();
                  });
                </script>';
                  echo '<div id="tabs">';
                  break;
          case 'CloseDiv':
                  echo '</div>';
                  break;
          case 'TABS_Listing':
                  echo '<ul>';
    
    //var_dump($box['links']);
              foreach($box['links'] as $id => $name){
                echo '<li><a href="#'.$id.'">'.$name.'</a></li>';
              }
    echo '</ul>';
                  break;
          case 'OpenTab':
                  echo '<div id="'.$box['id'].'">';
                  break;
          case 'title':
            echo '<h2>'.$box['label'].'</h2>';
            break;
          case 'subtitle':
            echo '<h3>'.$box['label'].'</h3>';
            break;
          case 'paragraph':
            echo '<p>'.$box['text'].'</p>';
            break;
          case 'input':
            $data = htmlspecialchars(stripslashes($data));
            echo '<td><input type="text" class="'.$box['size'].'-text" name="'.$this->args['option_group'].'['.$box['id'].']" id="'.$this->args['option_group'].'['.$box['id'].']" value="'.$data.'" /><br /> <span class="description">'.$box['desc'].'</span></td>';
            break;
          case 'colorpicker':
            $data = htmlspecialchars(stripslashes($data));
            echo '<td><input type="text" class="'.$box['size'].'-text color" name="'.$this->args['option_group'].'['.$box['id'].']" id="'.$this->args['option_group'].'['.$box['id'].']" value="'.$data.'" style="width:60px;" /><br /> <span class="description">'.$box['desc'].'</span></td>';
            break;
          case 'textarea':
            $data = stripslashes($data);
            echo '<td><textarea rows="'.$box['rows'].'" cols="'.$box['cols'].'" style="width:'.$box['width'].'px" name="'.$this->args['option_group'].'['.$box['id'].']" id="'.$this->args['option_group'].'['.$box['id'].']">'.$data.'</textarea> <br /><span class="description">'.$box['desc'].'</span></td>';
            break;
            
          case 'editor':
            wp_tiny_mce();
            echo '<td><div class="editorcontainer"><textarea class="theEditor" id="'.$this->args['option_group'].'['.$box['id'].']" name="'.$this->args['option_group'].'['.$box['id'].']">'.$data.'</textarea></div><br /><span class="description">'.$box['desc'].'</span></td>';
            break;
          
          case 'checkbox':
            if($data == 'true') {
              $checked = 'checked="checked"';
            } else {
              $checked = '';
            }
            echo '<td><input type="checkbox" name="'.$this->args['option_group'].'['.$box['id'].']" id="'.$this->args['option_group'].'['.$box['id'].']" value="true" '.$checked.' /><br /> <label for="'.$box['id'].'">'.$box['desc'].'</label></td>';
            break;
          
      case 'checkboxArray':
		  echo '<td>';
		  foreach ((array)$box['options'] as $option) {
			if(in_array($option,(array)$data)) {
			  $checked = 'checked="checked"';
			} else {
			  $checked = '';
			}
			echo '<input type="checkbox" name="'.$this->args['option_group'].'['.$box['id'].'][]" id="'.$this->args['option_group'].'['.$box['id'].'][]" value="'.$option.'" '.$checked.' /> <label for="'.$box['id'].'">'.$option.'</label> <br/> ';
		  }
		  echo $box['desc'];
		  echo '</td>';
            break;
      
          case 'radio':
            echo '<td>';
            foreach((array)$box['options'] as $label=>$value) {
              if($data == $value) {
                $checked = 'checked="checked"';
              } else {
                $checked = '';
              }
              echo '<input type="radio" name="'.$this->args['option_group'].'['.$box['id'].']" id="'.$box['id'].'_'.$value.'" value="'.$value.'" '.$checked.' /> <label for="'.$box['id'].'_'.$value.'">'.$label.'</label><br>';
            }
            echo '</td>';
            break;
            
            
          case 'imageradio':
            echo '<td>';
            foreach((array)$box['options'] as $label=>$value) {
              if($data == $value) {
                $checked = 'checked="checked"';
              } else {
                $checked = '';
              }
              echo '<input type="radio" name="'.$this->args['option_group'].'['.$box['id'].']" id="'.$box['id'].'_'.$value.'" value="'.$value.'" '.$checked.' /><img src="'.$box['directory_url'].$label.'"><br>';
            }
            echo '</td>';
            break;
            
            
          case 'dropdown':
            echo '<td>';
            echo '<select name="'.$this->args['option_group'].'['.$box['id'].']" id="'.$this->args['option_group'].'['.$box['id'].']">';
            foreach((array)$box['options'] as $label=>$value) {
              if($data == $value) {
                $selected = 'selected="selected"';
              } else {
                $selected = '';
              }
              echo '<option value="'.$value.'" '.$selected.'>'.$label.'</option>';
            }
            echo '</select><br /> <span class="description">'.$box['desc'].'</span>';
            echo '</td>';
            break;
          
          case 'post':
            echo '<td>';
            echo '<select name="'.$this->args['option_group'].'['.$box['id'].']" id="'.$this->args['option_group'].'['.$box['id'].']">';
            $args = array( 'numberposts' => -1, 'post_type'=> $box['ptype'],'post_status' => 'publish' );
             

            $posts = get_posts($args);
            foreach ( (array)$posts as $post )
            {
              if($data == $post->ID) {
                $selected = 'selected="selected"';
              } else {
                $selected = '';
              }
              echo '<option value="'.$post->ID.'" '.$selected.'>'.$post->post_title.'</option>';
            }
            
            echo '</select><br /> <span class="description">'.$box['desc'].'</span>';
            echo '</td>';
            break;
          
          case 'tax':
            echo '<td>';
            echo '<select name="'.$this->args['option_group'].'['.$box['id'].']" id="'.$this->args['option_group'].'['.$box['id'].']">';
            $args = array( 'hide_empty' =>0, 'taxonomy'=> $box['taxonomy']);
            $categories=  get_categories($args);
            if ($categories){
              foreach ( $categories as $category )
              {
                if($data == $category->term_id) {
                  $selected = 'selected="selected"';
                } else {
                  $selected = '';
                }
                echo '<option value="'.$category->term_id.'" '.$selected.'>'.$category->name.'</option>';
              }
              
              echo '</select><br /> <span class="description">'.$box['desc'].'</span>';
            }
            else{echo 'Not Found';}
            echo '</td>';
            break;
          
          case 'wp_roles':
            echo '<td>';
            echo '<select name="'.$this->args['option_group'].'['.$box['id'].']" id="'.$this->args['option_group'].'['.$box['id'].']">';
            global $wp_roles;
			if ( ! isset( $wp_roles ) )
    			$wp_roles = new WP_Roles();
			$names = $wp_roles->get_names();
            
            if ($names){
              foreach ( (array)$names as $n )
              {
                if($data == $n) {
                  $selected = 'selected="selected"';
                } else {
                  $selected = '';
                }
                echo '<option value="'.$n.'" '.$selected.'>'.$n.'</option>';
              }
              
              echo '</select><br /> <span class="description">'.$box['desc'].'</span>';
            }
            else{echo 'Not Found';}
            echo '</td>';
            break;
          
          case 'posts':
            echo '<td>';
            
            $args = array( 'numberposts' => -1, 'post_type'=> $box['ptype'],'post_status' => 'publish' );
            $posts = get_posts($args);
            if ($posts){
              //echo '<select name="'.$this->args['option_group'].'['.$box['id'].'][]" id="'.$this->args['option_group'].'['.$box['id'].']">';
              foreach ( $posts as $post )
              {
                $checked = '';
                if (is_array($data)){
                  if ( in_array( $post->ID, $data ) )
                    {
                      $checked = ' checked="checked"';
                    }
                }
                echo '<input value="'.$post->ID.'" '.$checked .' type="checkbox" name="'.$this->args['option_group'].'['.$box['id'].'][]" id="'.$this->args['option_group'].'['.$box['id'].'][]">  '.$post->post_title .'<br />';
              }
              echo '</select> <br /><span class="description">'.$box['desc'].'</span>';
            }else{
             echo 'No Posts';
            }
            echo '</td>';
            break;
    
            case 'taxs':
            echo '<td>';
            $args = array( 'hide_empty' =>0, 'taxonomy'=> $box['taxonomy']);
            $categories =  get_categories($args);
            if ($categories){
              foreach ( $categories as $category )
              {
                $checked = '';
                if (is_array($data)){
                  if ( in_array( $category->term_id, $data ) )
                    {
                      $checked = ' checked="checked"';
                    }
                }
                echo '<input value="'.$category->term_id.'" '.$checked .' type="checkbox" name="'.$this->args['option_group'].'['.$box['id'].'][]" id="'.$this->args['option_group'].'['.$box['id'].'][]">  '.$category->name .'<br />';
              }
              echo '</select><br /> <span class="description">'.$box['desc'].'</span>';
            }
            else{ echo 'Not Found';}
            
            echo '</td>';
            break;
    
    
          case 'upload':
            echo '<td>';
            echo '<div style="-webkit-border-radius:6px; border:1px solid #DEDEDE; padding:10px; position:relative; background:#FFF;">';
            echo '<div style="float:left"><input type="file" name="'.$this->args['option_group'].'['.$box['id'].']" id="'.$this->args['option_group'].'['.$box['id'].']" /> <span class="description">'.$box['desc'].'</span>';
            if(isset($box['title'])) echo '<br><br><input type="text" class="regular-text" name="'.$this->args['option_group'].'['.$box['id'].'_title]" id="'.$this->args['option_group'].'['.$box['id'].'_title]" value="'.$data['title'].'" /> <span class="description">'.$box['title'].'</span>';
            echo '</div>';
            if(strpos($data['type'], 'image') !== false) {
              echo '<img height="75" src="'.$data['url'].'" style="float:right" />';
            } else {
              echo '<p style="float:right"><strong>'.__('Current').':</strong> '.$data['url'].'</p>';
            }
            echo '<div style="clear:both"></div>';
            echo '</div>';
            echo '</td>';
            break;
            
          case 'slider':
            $show = $data;
            if(is_array($show)) $show = implode('-',$show);
            echo '<td>';
            echo '<div style="width:30%" id="'.$box['id'].'-slider" class="ui-slider"></div>';
            echo '<div id="'.$box['id'].'-handle">'.$show.'</div>';
            echo '<input type="hidden" name="'.$this->args['option_group'].'['.$box['id'].']" id="'.$this->args['option_group'].'['.$box['id'].']" value="'.$show.'" />';
            echo '<script type="text/javascript">jQuery("#'.$box['id'].'-slider").slider({';
            if(!is_array($data)) {
              echo 'value: '.$data.',';
            } else {
              echo 'range: true,';
              echo 'values: ['.implode(',',$data).'],';
            }
            echo 'step:' .$box['step'].',';
            echo 'max: '.$box['max'].',';
            echo 'min: '.$box['min'].',';
            if(!is_array($data)) {
              echo 'slide: function(e,ui) { jQuery("#'.$box['id'].'-handle").text(ui.value); jQuery("#'.$box['id'].'").val(ui.value); },';
            } else {
              echo 'slide: function(e,ui) { jQuery("#'.$box['id'].'-handle").text(ui.values[0]+"-"+ui.values[1]); jQuery("#'.$box['id'].'").val(ui.values[0]+"-"+ui.values[1]); },';
            }
            echo '}); </script>';
            echo '</td>';
            break;
            
          case 'date':
            if(strlen($data) > 0) $data = date('m/d/Y',$data);
            echo '<td><input type="text" name="'.$this->args['option_group'].'['.$box['id'].']" id="'.$this->args['option_group'].'['.$box['id'].']" value="'.$data.'" /> <span class="description">'.$box['desc'].'</span></td>';
            echo '<script type="text/javascript">jQuery("#'.$box['id'].'").datepicker();</script>';
            break;
        }
        if($box['type'] != 'title' && $box['type'] != 'paragraph' && $box['type'] != 'subtitle' && $box['type'] != 'TABS' && $box['type'] != 'CloseDiv' &&  $box['type'] != 'TABS_Listing' &&  $box['type'] != 'OpenTab') echo '</tr>';
      }
      if($this->table = true) echo '</table>';
      echo '<p class="submit"><input type="submit" name="Submit" class="button-primary" value="'.esc_attr(__('Save Changes')).'" /></p>';
      echo '<input type="hidden" name="action" value="save" />';
      echo '</form></div>';
      //delete_option($this->args['option_group']);
    }
    
    /**
     * Puts all our data to the database
     * @access private
     */
    private function save() {
    $saved  = get_option($this->args['option_group']);
    $post_data = $_POST[$this->args['option_group']];
    foreach($this->boxes as $box) {
      if($box['type'] != 'title' && $box['type'] != 'paragraph' && $box['type'] != 'subtitle' && $box['type'] != 'TABS' && $box['type'] != 'CloseDiv' &&  $box['type'] != 'TABS_Listing' &&  $box['type'] != 'OpenTab'){
          if (isset($post_data[$box['id']])){
            $data = $post_data[$box['id']];
          }else{
            $data = null;
          }
          if($box['type'] == 'editor') {
            $data = wptexturize(wpautop($data));
          }
          if($box['type'] == 'checkbox') {
            if($data != 'true') {
            $data = 'false';
            }
          }
          if($box['type'] == 'upload') {
            if($_FILES[$this->args['option_group'][$box['id']]]['size'] > 0) {
            $data = wp_handle_upload($_FILES[$this->args['option_group'][$box['id']]], array('test_form' => false));
            } else {
            $data = get_option($this->args['option_group'][$box['id']]);
            }
            $data['title'] = $_POST[$this->args['option_group'][$box['id']].'_title'];
          }
          if($box['type'] == 'slider') {
            if(strpos($data, '-') !== false) {
            $data = explode('-',$data);
            }
          }
          if($box['type'] == 'date') {
            $date = explode('/', $data);
            if(isset($date[2])) $data = mktime(0,0,0,$date[0],$date[1],$date[2]);
          }
          $saved[$box['id']] = $data;
        
        
      }
    }
    update_option($this->args['option_group'], $saved);  
  }
    
    /**
     * Loads all the script and css files needed
     * @access private
     */
    public function loadScripts() {
      wp_enqueue_script('common');
      wp_enqueue_script('jquery-color');
      wp_admin_css('thickbox');
      wp_print_scripts('post');
      wp_print_scripts('media-upload');
      wp_print_scripts('jquery');
      wp_print_scripts('jquery-ui-core');
      wp_print_scripts('jquery-ui-tabs');
      //wp_print_scripts('tiny_mce');
      wp_print_scripts('editor');
      //wp_print_scripts('editor-functions');
      add_thickbox();
      wp_admin_css();
      wp_enqueue_script('utils');
      do_action("admin_print_styles-post-php");
      do_action('admin_print_styles');
      remove_all_filters('mce_external_plugins');
    }
  }
}
if (!class_exists('shui_TopPage')){
  class shui_TopPage extends shui_AdminPage {
    /**
     * Builds a new Top-Level-Menu
     *
     * Possible keys within $args:
     *  > menu_title (string) - The name of the Top-Level-Menu
     *  > page_title (string) - The name of the first page of the menu
     *  > menu_slug (string) - A unique string identifying your new menu
     *  > capability (string) (optional) - The capability needed to view the page
     *  > icon_url (string) (optional) - URL to the icon, decorating the Top-Level-Menu
     *  > position (string) (optional) - The position of the Menu in the ACP
     *
     * @param array $args contains everything needed to build the menu
     */
    public function __construct($args) {
      $this->args = $args;
      $this->top = $this->args['menu_slug'];
      add_action('admin_menu', array($this, 'renderTopPage'));
      add_action('admin_head', array($this, 'loadScripts'));
    }
    
    /**
     * Does all the complicated stuff to build the menu and its first page
     * @access private
     */
    public function renderTopPage() {
      $default = array(
        'capability' => 'edit_themes',
      );
      $this->args = array_merge($default, $this->args);
      add_menu_page($this->args['page_title'], $this->args['menu_title'], $this->args['capability'], $this->args['menu_slug'], array($this, 'outputHTML'), $this->args['icon_url'], $this->args['position']);
      add_submenu_page($this->args['menu_slug'], $this->args['page_title'], $this->args['page_title'], $this->args['capability'], $this->args['menu_slug'], array($this, 'outputHTML'));
    }
  }
}
if (!class_exists('shui_SubPage')){
  class shui_SubPage extends shui_AdminPage{
    /**
     * Builds a new Top-Level-Menu
     *
     * Possible keys within $args:
     *  > page_title (string) - The name of this page
     *  > capability (string) (optional) - The capability needed to view the page
     *
     * @param string|object $top contains the name of the parent Top-Level-Menu or a TopPage object
     * @param array|string $args contains everything needed to build the menu, if just a string it's the name of the page
     */
  	
  	
    public function __construct($top, $args) {
      if(is_object($top)) {
        $this->top = $top->top;
      } else {
        switch($top) {
          case 'posts':
            $this->top = 'edit.php';
            break;
          
          case 'dashboard':
            $this->top = 'index.php';
            break;
          
          case 'media':
            $this->top = 'upload.php';
            break;
          
          case 'links':
            $this->top = 'link-manager.php';
            break;
          
          case 'pages':
            $this->top = 'edit.php?post_type=page';
            break;
          
          case 'comments':
            $this->top = 'edit-comments.php';
            break;
          
          case 'theme':
            $this->top = 'themes.php';
            break;
          
          case 'plugins':
            $this->top = 'plugins.php';
            break;
          
          case 'users':
            $this->top = 'users.php';
            break;
          
          case 'tools':
            $this->top = 'tools.php';
            break;
          
          case 'settings':
            $this->top = 'options-general.php';
            break;
          
          case 'shui':
            $this->top = 'edit.php?post_type=ba_sh';
            break;
          
          default:
            if(post_type_exists($top)) {
              $this->top = 'edit.php?post_type='.$top;
            } else {
              $this->top = $top;
            }
        }
      }
      if(is_array($args)) {
        $this->args = $args;
      } else {
        $array['page_title'] = $args;
        $this->args = $array;
      }
      add_action('admin_menu', array($this, 'renderSubPage'));
      
      
      
    }
    
    public function printslug(){
    	echo 'slug: ' . $this->slug;
    }
    
    /**
     * Does all the complicated stuff to build the page
     * @access private
     */
    public function renderSubPage() {
      $default = array(
        'capability' => 'edit_themes',
      );
      $this->args = array_merge($default, $this->args);
      $this->slug = add_submenu_page($this->top, $this->args['page_title'], $this->args['page_title'], $this->args['capability'], $this->createSlug(), array($this, 'outputHTML'));
      add_action('admin_print_scripts-'.$this->slug, array($this, 'loadScripts'));
    }
    
    
    /**
     * Creates an unique slug out of the page_title and the current menu_slug
     * @access private
     */
    private function createSlug() {
      $slug = $this->args['page_title'];
      $slug = strtolower($slug);
      $slug = str_replace(' ','_',$slug);
      return $this->top.'_'.$slug;
    }
  }
}