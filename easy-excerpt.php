<?php
/*
Plugin Name: Easy Excerpt
Plugin URI: http://fredrikmalmgren.com/wordpress/plugins/easy-excerpt/
Description: Control your excerpt style from admin. Choose excerpt length, ending and if you want a "read more"-link
Version: 0.3.0
Author: Fredrik Malmgren	
Author URI: http://fredrikmalmgren.com/
*/

/*  Copyright 2011 Fredrik Malmgren (email : plugins@fredrikmalmgren.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

load_plugin_textdomain( 'easy-excerpt', false, 'easy-excerpt/languages');

$easy_excerpt = new easy_excerpt;

class easy_excerpt {

	function __construct() {
		add_action( 'init', array( $this, 'init' ) );
	}

	function init() {
		add_action( 'admin_init', array( $this, 'admin_init' ) );
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );

		if(get_option( 'easy_excerpt_length' ) > 0){
			add_filter('excerpt_length', array( $this, 'custom_excerpt_length' ) );
		}		
		if(get_option( 'easy_excerpt_more' ) != ''){
			add_filter('excerpt_more', array( $this, 'custom_excerpt_more' ) );
		}	
		if(get_option( 'easy_excerpt_more_link' ) != ''){
			add_filter('excerpt_more', array( $this, 'custom_auto_excerpt_more_link' ) );
			add_filter('get_the_excerpt', array( $this, 'custom_manual_excerpt_more_link' ) );
		}		
	}

	function admin_init() {
		wp_register_style('easy_excerpt_css', plugins_url('easy-excerpt.css', __FILE__));
		
		register_setting( 'easy_excerpt_settings_page', 'easy_excerpt' );
		add_settings_section( 'default', __('Modify your excerpt style', 'easy-excerpt'), array( $this, 'option_content' ), 'easy_excerpt_settings_page' );
		
		register_setting( 'easy_excerpt_settings_page', 'easy_excerpt_length' );
		add_settings_field( 'easy_excerpt_length', __('Excerpt Length', 'easy-excerpt'), array( $this, 'excerpt_length' ), 'easy_excerpt_settings_page', 'default' );
		
		register_setting( 'easy_excerpt_settings_page', 'easy_excerpt_more' );
		add_settings_field( 'easy_excerpt_more', __('Excerpt More', 'easy-excerpt'), array( $this, 'excerpt_more' ), 'easy_excerpt_settings_page', 'default' );
		
		register_setting( 'easy_excerpt_settings_page', 'easy_excerpt_more_link' );
		add_settings_field( 'easy_excerpt_more_link', __('Excerpt Link', 'easy-excerpt'), array( $this, 'excerpt_more_link' ), 'easy_excerpt_settings_page', 'default' );	
	}	
	
	function admin_menu() {
		$page = add_options_page( __('Excerpt Options', 'easy-excerpt'), __('Easy Excerpt', 'easy-excerpt'), 'manage_options', 'easy_excerpt', array( $this, 'options' ) );
        add_action('admin_print_styles-' . $page, array( $this, 'admin_register_css' ) );
	}

	function options() {
	?>
		<div class="wrap">
			<?php screen_icon(); ?>
			<h2><?php _e('Easy Excerpt', 'easy-excerpt'); ?></h2>
			<form action="options.php" method="post">
				<?php settings_fields('easy_excerpt_settings_page'); ?>
				<?php do_settings_sections('easy_excerpt_settings_page'); ?>
				<p class="submit"><input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e('Save Changes', 'easy-excerpt'); ?>" /></p>
			</form>
		</div>
	<?php
	}

	function admin_register_css()
    {
        wp_enqueue_style('easy_excerpt_css');
    }
	
	/*
	* Functions for adding form fields for excerpt style
	*/
	
	function excerpt_length() {
?>
	<input type="text" name="easy_excerpt_length" id="easy_excerpt_length" value="<?php esc_attr(form_option( 'easy_excerpt_length' )); ?>" />
	<label for="easy_excerpt_length" class="small"><?php _e('Default 55 words', 'easy-excerpt'); ?></label>
<?php
	}

	function excerpt_more() {
?>
	<input type="text" name="easy_excerpt_more" id="easy_excerpt_more" value="<?php esc_attr(form_option( 'easy_excerpt_more' )); ?>" />
	<label for="easy_excerpt_more" class="small"><?php _e('Default [...]', 'easy-excerpt'); ?></label>
<?php
	}	
	
	function excerpt_more_link() {
?>
	<input type="text" name="easy_excerpt_more_link" id="easy_excerpt_more_link" value="<?php esc_attr(form_option( 'easy_excerpt_more_link' )); ?>" />
	<label for="easy_excerpt_more" class="small"><?php _e('Not used as default', 'easy-excerpt'); ?></label>
<?php
	}	
	
	function option_content() {
	?>
		<p>
			<?php _e( 'With Easy Excerpt you can control the length of the excerpt, how the ending looks like and choose to give the user a link to the full post.', 'easy-excerpt'); ?>
		</p>
		<p class="small">
			<?php _e( 'Leave the fields blank for WordPress default values.', 'easy-excerpt'); ?>
		</p>
	<?php
	}

	/*
	* Functions for modifying excerpt style
	*/
	
	function custom_excerpt_length($length) {
		return esc_html__(get_option( 'easy_excerpt_length' ));
	}

	function custom_excerpt_more($more) {
		return esc_html__(get_option( 'easy_excerpt_more' ));
	}

	function custom_auto_excerpt_more_link($more) {
		$link = esc_attr__(get_option( 'easy_excerpt_more_link' ));
		return $more. ' <a href="'. get_permalink() . '" rel="nofollow">' . $link . '</a>';
	}
	
	function custom_manual_excerpt_more_link($more) {
		$link = esc_attr__(get_option( 'easy_excerpt_more_link' ));
		$excerpt_more_link = '';
		if( has_excerpt() ) {
			$excerpt_more_link = ' <a href="'. get_permalink() . '" rel="nofollow">' . $link . '</a>';
		}
		return $more. $excerpt_more_link;
	}	

}
?>