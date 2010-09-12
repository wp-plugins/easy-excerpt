<?php
/*
Plugin Name: Easy Excerpt
Plugin URI: http://fredrikmalmgren.com/wordpress/plugins/easy-excerpt/
Description: Control your excerpt style from admin. Choose excerpt length, ending and if you want a "read more"-link
Version: 0.1
Author: Fredrik Malmgren	
Author URI: http://fredrikmalmgren.com/
*/

$easy_excerpt = new easy_excerpt;

class easy_excerpt {

	function __construct() {
		add_action( 'init', array( $this, 'init' ) );		
	}

	function init() {
		add_action( 'admin_init', array( $this, 'admin_init' ) );
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );

		if(get_option( 'easy_excerpt_length' ) > 0){
			add_filter('excerpt_length', array( $this, 'new_excerpt_length' ) );
		}		
		if(get_option( 'easy_excerpt_more' ) != ''){
			add_filter('excerpt_more', array( $this, 'new_excerpt_more' ) );
		}	
		if(get_option( 'easy_excerpt_more_link' ) != ''){
			add_filter('excerpt_more', array( $this, 'new_excerpt_more_link' ) );
		}		
	}

	function admin_init() {
		wp_register_style('easy_excerpt_css', plugins_url('easy-excerpt.css', __FILE__));
		
		register_setting( 'easy_excerpt_settings_page', 'easy_excerpt' );
		add_settings_section( 'default', 'Modify your excerpt style', array( $this, 'option_content' ), 'easy_excerpt_settings_page' );
		
		register_setting( 'easy_excerpt_settings_page', 'easy_excerpt_length' );
		add_settings_field( 'easy_excerpt_length', 'Excerpt Length', array( $this, 'excerpt_length' ), 'easy_excerpt_settings_page', 'default' );
		
		register_setting( 'easy_excerpt_settings_page', 'easy_excerpt_more' );
		add_settings_field( 'easy_excerpt_more', 'Excerpt More', array( $this, 'excerpt_more' ), 'easy_excerpt_settings_page', 'default' );
		
		register_setting( 'easy_excerpt_settings_page', 'easy_excerpt_more_link' );
		add_settings_field( 'easy_excerpt_more_link', 'Excerpt Link', array( $this, 'excerpt_more_link' ), 'easy_excerpt_settings_page', 'default' );	
	}	
	
	function admin_menu() {
		$page = add_options_page( 'Excerpt Options', 'Easy Excerpt', 'manage_options', 'easy_excerpt', array( $this, 'options' ) );
        add_action('admin_print_styles-' . $page, array( $this, 'admin_register_css' ) );
	}

	function options() {
		echo '<div class="wrap">';
		screen_icon();
		echo '<h2>Easy Excerpt</h2>';
		echo '<form action="options.php" method="post">';
		settings_fields('easy_excerpt_settings_page');
		do_settings_sections('easy_excerpt_settings_page');
		echo '<p class="submit"><input type="submit" name="Submit" class="button-primary" value="Save Changes" /></p>';
		echo '</form></div>';
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
	<input type="text" name="easy_excerpt_length" id="easy_excerpt_length" value="<?php form_option( 'easy_excerpt_length' ); ?>" />
	<label for="easy_excerpt_length" class="small">Default 55 words</label>
<?php
	}

	function excerpt_more() {
?>
	<input type="text" name="easy_excerpt_more" id="easy_excerpt_more" value="<?php form_option( 'easy_excerpt_more' ); ?>" />
	<label for="easy_excerpt_more" class="small">Default [...]</label>
<?php
	}	
	
	function excerpt_more_link() {
?>
	<input type="text" name="easy_excerpt_more_link" id="easy_excerpt_more_link" value="<?php form_option( 'easy_excerpt_more_link' ); ?>" />
	<label for="easy_excerpt_more" class="small">Not used as default</label>
<?php
	}	
	
	function option_content() {
		echo "<p>With Easy Excerpt you can control the length of the excerpt, how the ending looks like and choose to give the user a link to the full post.</p><p class=\"small\">Leave the fields blank for WordPress default values.</p>";
	}

	/*
	* Functions for modifying excerpt style
	*/
	
	function new_excerpt_length($length) {
		return get_option( 'easy_excerpt_length' );
	}

	function new_excerpt_more($more) {
		return get_option( 'easy_excerpt_more' );
	}

	function new_excerpt_more_link($more) {
		   global $post;
		   $link = get_option( 'easy_excerpt_more_link' );
			if($more == ''){
				$more = '[...]'; 
			}	   
		return $more. ' <a href="'. get_permalink($post->ID) . '">' . $link . '</a>';
	}

}
