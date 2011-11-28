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
		And if you like my work <a href="http://en.bainternet.info/donations" style="color: #FC000D;">make a donation</a></li>
</ul>
');
$options_panel->CloseDiv_Container();
$options_panel->CloseDiv_Container();