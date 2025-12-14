<?php
add_action('init', function() {
    if ( ! get_option('mt_flush_rules_once_v1') ) {
        flush_rewrite_rules();
        update_option('mt_flush_rules_once_v1', true);
    }
}, 999);
