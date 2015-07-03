<?php 

// load settings file
require_once('settings.php');



// to remove the hoook in the future call this:
// wp_clear_scheduled_hook('autoupdater');

/**
 * Adding an hourly cron job
 */
if (!wp_next_scheduled('autoupdater_refresh_theme')) {
  wp_schedule_event(time(), 'hourly', 'autoupdater_refresh_theme' );
}

/**
 * Adding a hook for the actual function
 */
add_action('autoupdater_refresh_theme', 'autoupdater_refresh_theme');

add_filter('pre_set_site_transient_update_themes', 'check_for_update');

function check_for_update($checked_data) {
  global $wp_version, $theme_version, $theme_to_update, $api_url;

  $request = array(
    'slug' => $theme_to_update,
    'version' => $theme_version 
  );
  // Start checking for an update
  $send_for_check = array(
    'body' => array(
      'action' => 'theme_update', 
      'request' => serialize($request),
      'api-key' => md5(get_bloginfo('url'))
    ),
    'user-agent' => 'WordPress/' . $wp_version . '; ' . get_bloginfo('url')
  );
  $raw_response = wp_remote_post($api_url, $send_for_check);

  if (!is_wp_error($raw_response) && ($raw_response['response']['code'] == 200))
    $response = unserialize($raw_response['body']);

  // Feed the update data into WP updater
  if (!empty($response)) 
    $checked_data->response[$theme_to_update] = $response;

  return $checked_data;
}

/**
 * Refresh families from meteor function
 */

function autoupdater_refresh_theme(){

  // refresh WordPress information of new theme versions
  get_transient('update_themes');

  global $wp_version, $theme_version, $theme_to_update, $api_url;
  log_me('checking the update and stuff...');


  if(function_exists('wp_get_theme')){
      $theme_data = wp_get_theme($theme_to_update);
      $theme_version = $theme_data->Version;  
  } else {
      $theme_data = get_theme_data( ABSPATH . '/wp-content/themes/'.$theme_to_update.'/style.css');
      $theme_version = $theme_data['Version'];
  }    



  $request = array(
    'slug' => $theme_to_update,
    'version' => $theme_version 
  );
  // Start checking for an update
  $send_for_check = array(
    'body' => array(
      'action' => 'theme_update', 
      'request' => serialize($request),
      'api-key' => md5(get_bloginfo('url'))
    ),
    'user-agent' => 'WordPress/' . $wp_version . '; ' . get_bloginfo('url')
  );
  $raw_response = wp_remote_post($api_url, $send_for_check);

  if (!is_wp_error($raw_response) && ($raw_response['response']['code'] == 200))
    $response = unserialize($raw_response['body']);



  // check if the new version is higher
  // if (version_compare($theme_version, $response['new_version']))
  if (version_compare($theme_version, $response['new_version'], '<')){
      


    require ABSPATH . 'wp-admin/includes/screen.php';
    require ABSPATH . 'wp-admin/includes/plugin.php';
    require ABSPATH . 'wp-admin/includes/template.php';
    require ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
    require ABSPATH . 'wp-admin/includes/file.php';
    require ABSPATH . 'wp-admin/includes/misc.php';




    $title = __('Update Theme');
    $parent_file = 'themes.php';
    $submenu_file = 'themes.php';
    // require_once(ABSPATH . 'wp-admin/admin-header.php');

    $nonce = 'upgrade-theme_' . $theme_to_update;
    $url = 'update.php?action=upgrade-theme&theme=' . urlencode( $theme_to_update );


    $upgrader = new Theme_Upgrader( new Theme_Upgrader_Skin( compact('title', 'nonce', 'url', 'theme') ) );
    $upgrader->upgrade($theme_to_update);


  }

}

// for tests
if ($run_every_time_in_admin && is_admin())
  autoupdater_refresh_theme();


?>