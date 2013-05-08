<?php
/*
      ___         ___                   
     /\__\       /\  \         _____    
    /:/ _/_     /::\  \       /::\  \   
   /:/ /\__\   /:/\:\  \     /:/\:\  \  
  /:/ /:/  /  /:/ /::\  \   /:/ /::\__\ 
 /:/_/:/  /  /:/_/:/\:\__\ /:/_/:/\:|__|
 \:\/:/  /   \:\/:/  \/__/ \:\/:/ /:/  /
  \::/__/     \::/__/       \::/_/:/  / 
   \:\  \      \:\  \        \:\/:/  /  
    \:\__\      \:\__\        \::/  /   
     \/__/       \/__/         \/__/
     

Plugin Name: Custom Code
Plugin URI: http://owlwatch.com
Description: Add custom JS and CSS to any post type
Version: 1.0
Author: Mark Fabrizio
Author URI: http://owlwatch.com
*/

add_action('init', function(){
  
  /********************************************************
  * After plugins and theme loads, check for Snap library
  *********************************************************/
  if( !class_exists('Snap') ){
    add_action('admin_notices', function(){
      ?>
    <div class="error">
      <p>Please install the <a href="https://github.com/fabrizim/Snap">Snap library plugin</a> to use the Custom Code plugin.</p>
    </div>
      <?
    });
    return;
  }
  
  Snap_Loader::register('CustomCode', dirname(__FILE__).'/lib');
  define( 'CUSTOM_CODE_BASE_URI', plugins_url('/', __FILE__) );
  
  // dispatch the plugin
  Snap::inst('CustomCode_Plugin');
  
  
  
});