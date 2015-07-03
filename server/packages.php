<?php
// Theme with update info
$packages['simpletest'] = array( //Replace theme with theme stylesheet slug that the update is for
  'versions' => array(
    '1.0' => array( //Array name should be set to current version of update
    'version' => '1.0', //Current version available
    'date' => '2015-07-03', //Date version was released
    //theme.zip is the same as file_name
    'package' => 'http://API_URL/download.php?key=' . md5('THEME_SLUG.zip' . mktime(0,0,0,date("m"),date("d"),date("Y"))),
    //file_name is the name of the file in the update folder.
    'file_name' => 'THEME_SLUG.zip', //File name of theme zip file
    'author' => '', //Author of theme
    'name' => '', //Name of theme
    'requires' => '3.3', //Wordpress version required
    'tested' => '3.3', //WordPress version tested up to
    // 'screenshot_url' => 'http://url_to_your_theme_site/screenshot.png' //url of screenshot of theme
    )

  ),
  'info' => array(
    'url' => ''  // Website devoted to theme if available
  )
);
