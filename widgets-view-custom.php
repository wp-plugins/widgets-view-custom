<?php
/*
Plugin Name: Widgets view custom
Description: Select only the widget you want to use, Customize the widgets list.
Plugin URI: http://gqevu6bsiz.chicappa.jp
Version: 1.0.2
Author: gqevu6bsiz
Author URI: http://gqevu6bsiz.chicappa.jp/author/admin/
Text Domain: widgets_view_custom
Domain Path: /languages
*/

/*  Copyright 2012 gqevu6bsiz (email : gqevu6bsiz@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
	published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

load_plugin_textdomain('widgets_view_custom', false, basename(dirname(__FILE__)).'/languages');

define ('WIDGETS_VIEW_CUSTOM_VER', '1.0.1');
define ('WIDGETS_VIEW_CUSTOM_PLUGIN_NAME', 'Widgets view custom');
define ('WIDGETS_VIEW_CUSTOM_MANAGE_URL', admin_url('options-general.php').'?page=widgets_view_custom');
define ('WIDGETS_VIEW_CUSTOM_RECORD_NAME', 'widget_view_custom');
define ('WIDGETS_VIEW_CUSTOM_PLUGIN_DIR', WP_PLUGIN_URL.'/'.dirname(plugin_basename(__FILE__)).'/');
?>
<?php
function widgets_view_custom_add_menu() {
	// add menu
	add_options_page(__('Widgets view custom\'s setting', 'widgets_view_custom'), WIDGETS_VIEW_CUSTOM_PLUGIN_NAME, 'administrator', 'widgets_view_custom', 'widgets_view_custom_setting');

	// plugin links
	add_filter('plugin_action_links', 'widgets_view_custom_plugin_setting', 10, 2);
}



// plugin setup
function widgets_view_custom_plugin_setting($links, $file) {
	if(plugin_basename(__FILE__) == $file) {
		$settings_link = '<a href="'.WIDGETS_VIEW_CUSTOM_MANAGE_URL.'">'.__('Settings').'</a>'; 
		array_unshift( $links, $settings_link );
	}
	return $links;
}
add_action('admin_menu', 'widgets_view_custom_add_menu');



// setting
function widgets_view_custom_setting() {
	$UPFN = 'sett';
	$Msg = '';

	if(!empty($_POST[$UPFN])) {

		// update
		if($_POST[$UPFN] == 'Y') {
			unset($_POST[$UPFN]);

			$Modes = array("use", "not_use");
			foreach($Modes as $mode) {
				$Update[$mode] = array();
				if(!empty($_POST[$mode])) {
					foreach ($_POST[$mode] as $key => $val) {
						$Update[$mode][strip_tags($key)]["id_base"] = strip_tags($val["id_base"]);
						$Update[$mode][strip_tags($key)]["name"] = strip_tags($val["name"]);
						$Update[$mode][strip_tags($key)]["option_name"] = strip_tags($val["option_name"]);
					}
				}
			}

			if(!empty($Update)) {
				update_option(WIDGETS_VIEW_CUSTOM_RECORD_NAME, $Update);
				$Msg = '<div class="updated"><p><strong>'.__('Settings saved.').'</strong></p></div>';
			}
		}

	}

	// get data
	$Data = widgets_view_custom_get(get_option(WIDGETS_VIEW_CUSTOM_RECORD_NAME));

	// include js css
	$ReadedJs = array('jquery', 'jquery-ui-sortable', 'jquery-ui-widget');
	wp_enqueue_script('widgets-view-custom', WIDGETS_VIEW_CUSTOM_PLUGIN_DIR.dirname(plugin_basename(__FILE__)).'.js', $ReadedJs, WIDGETS_VIEW_CUSTOM_VER);
	wp_enqueue_style('widgets-view-custom', WIDGETS_VIEW_CUSTOM_PLUGIN_DIR.dirname(plugin_basename(__FILE__)).'.css', array(), WIDGETS_VIEW_CUSTOM_VER);
?>
<div class="wrap">
	<div class="icon32" id="icon-themes"></div>
	<h2><?php _e('Widgets view custom\'s setting', 'widgets_view_custom'); ?></h2>
	<?php echo $Msg; ?>
	<p>&nbsp;</p>

	<form id="widget_view_custom_form" method="post" action="">
		<input type="hidden" name="<?php echo $UPFN; ?>" value="Y">
		<?php wp_nonce_field(); ?>

		<table cellspacing="0" class="widefat fixed">
			<thead>
				<tr>
					<th><?php _e('Widgets to show', 'widgets_view_custom'); ?></th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>
						<span class="description"><?php _e('Please side by side by dragging.','widgets_view_custom'); ?></span>
						<div id="use" class="widget-list">
							<?php if(!empty($Data["use"])): ?>
								<?php widgets_view_custom_lists_create('use', $Data["use"]); ?>
							<?php endif; ?>
						</div>
						<div class="clear"></div>
					</td>
				</tr>
			</tbody>
		</table>

		<p>&nbsp;</p>

		<table cellspacing="0" class="widefat fixed">
			<thead>
				<tr>
					<th><?php _e('Widgets to hide', 'widgets_view_custom'); ?></th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>
						<div id="not_use" class="widget-list">
							<?php if(!empty($Data["not_use"])): ?>
								<?php widgets_view_custom_lists_create('not_use', $Data["not_use"]); ?>
							<?php endif; ?>
						</div>
						<div class="clear"></div>
					</td>
				</tr>
			</tbody>
		</table>
		<p class="submit">
			<input type="submit" class="button-primary" value="<?php _e('Save'); ?>" />
		</p>
		<p class="submit reset">
			<span class="description"><?php _e('Would initialize?', 'widgets_view_custom'); ?></span>
			<input type="button" class="button-secondary" value="<?php _e('Reset'); ?>" />
		</p>

	</form>
</div>
<?php
}



// widget lists create
function widgets_view_custom_lists_create($mode, $widget) {

	$Contents = '';
	foreach($widget as $key => $val) {
		$Contents .= '<div id="'.$val["option_name"].'" class="widget">';
		
		$Contents .= '<div class="widget-top">';
		$Contents .= '<div class="widget-title">';
		$Contents .= '<h4>'.$val["name"].'</h4>';
		$Contents .= '</div>';
		$Contents .= '</div>';
		
		$Contents .= '<div class="widget-inside">';
		$Contents .= '<input type="hidden" name="'.$mode.'['.$key.'][id_base]" value="'.$val["id_base"].'" />';
		$Contents .= '<input type="hidden" name="'.$mode.'['.$key.'][name]" value="'.$val["name"].'" />';
		$Contents .= '<input type="hidden" name="'.$mode.'['.$key.'][option_name]" value="'.$val["option_name"].'" />';
		$Contents .= '</div>';
		
		$Contents .= '</div>';
	}

	echo $Contents;

}



// widget list get datas
function widgets_view_custom_get($Data = array()) {

	global $wp_widget_factory;

	$NewData = array();
	foreach($wp_widget_factory->widgets as $key => $widget) {
		$NotFlg = false;
		if(!empty($Data["not_use"])) {
			foreach($Data["not_use"] as $objName => $NotUse) {
				if($key == $objName && $widget->id_base == $NotUse["id_base"] && $widget->name == $NotUse["name"] && $widget->option_name == $NotUse["option_name"]) {
					$NotFlg = true;
				}
			}
		}

		if($NotFlg == false) {
			$NewData['use'][$key] = array("id_base" => $widget->id_base, "name" => $widget->name, "option_name" => $widget->option_name);
		} else {
			$NewData['not_use'][$key] = array("id_base" => $widget->id_base, "name" => $widget->name, "option_name" => $widget->option_name);
		}
	}

	return $NewData;

}



// widgets view filter
function widgets_view_custom_filter() {
	global $wp_widget_factory, $pagenow;
	
	if($pagenow == 'widgets.php') {
		$Data = get_option(WIDGETS_VIEW_CUSTOM_RECORD_NAME);
		if(!empty($Data)) {
			$WidgetFilter = widgets_view_custom_get($Data);

			if(!empty($WidgetFilter["not_use"])) {
				foreach($wp_widget_factory->widgets as $key => $val) {
					if(array_key_exists($key, $WidgetFilter["not_use"])) {
						unregister_widget($key);
					}
				}
			}
		}
	}
}
add_filter('widgets_init', 'widgets_view_custom_filter', 99);

?>