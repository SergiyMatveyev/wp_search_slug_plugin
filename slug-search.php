<?php
/*
Plugin Name: Slug Search
Description: A plugin that allows users to search for pages and posts by slug.
Version: 1.0
Author: Serhii Matveiev
*/

add_filter('posts_search', 'admin_slug_search', PHP_INT_MAX, 2);
function admin_slug_search($search, \WP_Query $q)
{
    global $wpdb;
    if (
        !did_action('load-edit.php')
        || !is_admin()
        || !$q->is_search()
        || !$q->is_main_query()
    ) {
        return $search;
    }

    $s = $q->get('s');
    if ('slug:' === mb_substr(trim($s), 0, 5)) {
        $search_slug = mb_strtolower(
            $wpdb->esc_like(
                trim(mb_substr($s, 5))
            )
        );
        $search = $wpdb->prepare(
            " AND {$wpdb->posts}.post_name LIKE %s ",
            "%$search_slug%"
        );
        $q->set('orderby', 'post_name');
        $q->set('order', 'ASC');
    }
    return $search;
}
