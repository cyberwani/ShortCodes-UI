<?php
require_once 'adminp.php';

$options_panel = new shui_SubPage('shui', array('page_title' => __('Settings','shui'),'option_group' => 'shui_settings'));
$options_panel->OpenTabs_container('');
$options_panel->TabsListing(array(
	'links' => array(
		'Settings' =>  __('Settings'),
		'options1' =>  __('Help')
		)
	));
//options page
$options_panel->OpenTab('Settings');
$options_panel->addSubtitle(__('Settings:','shui'));
$options_panel->addRoles(array(
				'id' => 'cpt',
				'label' => __('Who can create new shortcodes?','shui'),
				'desc' => __('Only users in the selected role or above will see the Shortcodes UI, be able to create new shortcodes and import/export shortcodes.','shui')
			));
$options_panel->addRoles(array(
				'id' => 'ct',
				'label' => __('Who can create manage shortcodes categories?','shui'),
				'desc' => __('Only users in the selected role or above will see the Shortcodes UI categories and be able to create new categories.','shui')
			));

$options_panel->addDropdown(array(
				'id' => 'autop',
				'label' => __('Fix AutoP filter'),
				'options' => array(
					__('Leave as Is') => 'no',
					__('Prospond Till After Shortcodes') => 'prospond',
					__('Remove AutoP filter') => 'remove'),
				'desc' => __('WordPress filters the content and adds P tags, this is call autoP filter, which can cause problems with your shortcode.')
			));
$options_panel->addDropdown(array(
				'id' => 'code_editor_theme',
				'label' => __('Code Editor Theme'),
				'options' => array(
					__('Default') => 0,
					__('Light') => 1,
					__('Dark') => 2),
				'desc' => __('Select a theme to use in code editor for shortcodes UI code fields ( CSS, Javascript, PHP ).')
			));
$options_panel->CloseDiv_Container();
//help
$options_panel->OpenTab('options1');
$options_panel->addSubtitle(__('Help:','sis'));
$options_panel->addParagraph('
<ul style="list-style: square inside none; width: 300px; font-weight: bolder; padding: 20px; border: 2px solid; background-color: #FFFFE0; border-color: #E6DB55;>
	<li>
		Any feedback or suggestions are welcome at <a href="http://en.bainternet.info/2011/shortcodes-ui">plugin homepage</a></li>
	<li>
		<a href="http://wordpress.org/tags/shortcodes-ui?forum_id=10">Support forum</a> for help and bug submission</li>
	<li>
		Also check out <a href="http://en.bainternet.info/category/plugins">my other plugins</a></li>
	<li>
		And if you like my work <span style="color: #FC000D;">make a donation</span> <br /><a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=EXMZGSLS4JAR8"><img src="http://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif"</a></li>
</ul>
');
$options_panel->CloseDiv_Container();
$options_panel->CloseDiv_Container();