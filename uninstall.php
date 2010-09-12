<?php
if ( !defined('WP_UNINSTALL_PLUGIN') ) {
    exit();
}

delete_option('easy_excerpt_length');
delete_option('easy_excerpt_more');
delete_option('easy_excerpt_more_link');

?>