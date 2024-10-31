<?php
/*
Plugin Name: Post Archival in the Internet Archive
Plugin URI:  https://www.ctrl.blog/entry/wordpress-internet-archive-plugin
Description: Automatically save new blog posts to the Internet Archive.
Version:     1.3.1
Author:      Geeky Software
Author URI:  https://www.ctrl.blog/topic/wordpress
License:     GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html
*/

if ( !defined('ABSPATH') ) {
    header( 'HTTP/1.1 403 Forbidden' );
    exit(   'HTTP/1.1 403 Forbidden' );
}

/* wppaiapi_file_get_contents() and wppaiapi_file_get_intbound_contents()
   are documented at https://www.ctrl.blog/entry/php-file-contents-dual-stack */
function wppaiapi_file_get_contents( $url ) {
    $hostname = parse_url( $url, PHP_URL_HOST );
    if ( $hostname == FALSE ) {
        return FALSE;
    }

    $host_has_ipv6 = FALSE;
    $host_has_ipv4 = FALSE;
    $file_response = FALSE;

    $dns_records = dns_get_record( $hostname, DNS_AAAA + DNS_A );

    foreach ($dns_records as $dns_record ) {
      if ( isset( $dns_record['type'] ) ) {
        switch ( $dns_record['type'] ) {
            case 'AAAA':
                $host_has_ipv6 = TRUE;
                break;
            case 'A':
                $host_has_ipv4 = TRUE;
                break;
    }   }   }

    if ( $host_has_ipv6 === TRUE ) {
        $file_response = wppaiapi_file_get_intbound_contents( $url, '[0]:0' );
    }
    if ( $host_has_ipv4 === TRUE && $file_response == FALSE ) {
        $file_response = wppaiapi_file_get_intbound_contents( $url, '0:0' );
}   }

function wppaiapi_file_get_intbound_contents( $url, $bindto_addr_family ) {
    global $wp_version;

    $stream_context = stream_context_create(
        array(
            'socket' => array(
                'bindto' => $bindto_addr_family
            ),
            'http' => array(
                'header' => 'Connection: close',
                'method' => 'GET',
                'timeout' => 35,
                'user_agent' => 'Post-Archival-Plugin/1.3.0 (https://wordpress.org/plugins/post-archival/) ' .
                                'WordPress/' . $wp_version . ' (' . get_site_url() .')'
    )   )   );

    return file_get_contents( $url, FALSE, $stream_context );
}

function wppaiapi_archive_link( $url ) {
    $request_url = "https://web.archive.org/save/${url}";
    wppaiapi_file_get_contents( $request_url );
}
add_action( 'wppaiapi_handle_link_archival', 'wppaiapi_archive_link', 10, 1 );

function wppaiapi_archive_post( $ID, $url, $on_publish ) {
    // verify that the post is still public and a link.
    if ( get_post_status( $ID ) != 'publish' || strpos( $url, 'http' ) === FALSE )
        return FALSE;

    // Archive the post link
    wppaiapi_archive_link( $url );

    // Archive any linked resources in the post (only on publish)
    if ( $on_publish ) {
        $post = get_post( $ID );
        $post_content = $post->post_content;
        $post_content = apply_filters( 'the_content', $post_content );
        $links = wp_extract_urls( $post_content );
        if ( $links != FALSE && count( $links ) >= 1 ) {
            $site_url = get_site_url();
            $delay = 10;
            foreach ( $links as $num => $link ) {
                wp_schedule_single_event( time() + $delay, 'wppaiapi_handle_link_archival', array($link) );
                $delay = $delay + 8;  // +8 seconds
}   }   }   }
add_action( 'wppaiapi_handle_post_archival', 'wppaiapi_archive_post', 10, 3 );

function wppaiapi_schedule_archival_request( $post_id, $on_publish = FALSE ) {
    wp_schedule_single_event( time() + 43200, 'wppaiapi_handle_post_archival', array( $post_id, get_permalink( $post_id ), $on_publish ));
}

// Schedule after post status transitioned to 'publish'.
function wppaiapi_schedule_archival_request_on_publish( $new_status, $old_status, $post ) {
    if ( $old_status !== 'publish' && $new_status == 'publish' ) {
        wppaiapi_schedule_archival_request( $post->ID, TRUE );
}   }
add_action( 'transition_post_status', 'wppaiapi_schedule_archival_request_on_publish', 10, 3 );

// Schedule after significant post updates only, requires https://wordpress.org/plugins/minor-edits/
function wppaiapi_schedule_archival_request_on_significant_update( $new_post, $old_post ) {
    if ( isset( $new_post['post_type'] ) && $new_post['post_type'] == 'post' && isset( $old_post['ID'] ) ) {
        wppaiapi_schedule_archival_request( $old_post['ID'] );
}   }
add_action( 'minor_edits_post_status_significant_update', 'wppaiapi_schedule_archival_request_on_significant_update', 10, 2 );

if ( is_admin() ) {
    // archive all published posts the first time the plugin is activated.
    if ( get_option( 'wppaiapi_firstrun', '0' ) == '0' ) {
        update_option( 'wppaiapi_firstrun', '1' );

        $published_posts = get_posts( array(
            'offset' => 0,
            'orderby' => 'rand',
            'posts_per_page' => -1,
            'post_status' => 'publish',
            'post_type' => 'post' ));

        $delay = 180;
        // schedule archiving of all published permalinks.
        foreach ( $published_posts as $postkey => $post ) {
            wp_schedule_single_event( time() + $delay, 'wppaiapi_handle_post_archival', array( $post->ID, get_permalink( $post->ID ), FALSE ));
            $delay = $delay + 600;  // +10 minutes
        }

        // archive the home page right away (only once)
        wppaiapi_file_get_contents( get_home_url() );
    }

    function wppaiapi_deactivate() {
        // unschedule every possibly scheduled task.
        $published_posts = get_posts( array(
            'offset' => 0,
            'orderby' => 'rand',
            'posts_per_page' => -1,
            'post_type' => 'post' ));
        foreach ( $published_posts as $postkey => $post ) {
            wp_clear_scheduled_hook( 'wppaiapi_handle_post_archival', array( $post->ID, get_permalink( $post->ID ), FALSE ));
    }   }
    register_deactivation_hook( __FILE__, 'wppaiapi_deactivate' );
}

