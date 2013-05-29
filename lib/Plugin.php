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
     
  
  WP hooks all up in heer
  
*/
class CustomCode_Plugin extends Snap_Wordpress_Plugin
{
  
  protected $meta_box_id = 'custom-code-meta-box';
  protected $meta_box_title = 'Custom Code';
  protected $js_meta_key = '_customcode_js';
  protected $css_meta_key = '_customcode_css';
  
  /**
   * @wp.action
   */
  public function add_meta_boxes()
  {
    $post_types = get_post_types(array('public' => true));
    
    // lets just add to all post types for now
    foreach( $post_types as $post_type ){
      add_meta_box(
        $this->meta_box_id,
        $this->meta_box_title,
        array(&$this, 'meta_box'),
        $post_type,
        'advanced'
      );
    }
  }
  
  public function meta_box( $post )
  {
    
    $js_files = array();
    $css_files = array();
    $js_deps = array();
    $css_deps = array();
    if( $post && $post->ID ){
      $js_files = get_post_meta( $post->ID, $this->js_meta_key.'_files', true );
      $css_files = get_post_meta( $post->ID, $this->css_meta_key.'_files', true );
      $js_deps = get_post_meta( $post->ID, $this->js_meta_key.'_inline_deps', true);
      $css_deps = get_post_meta( $post->ID, $this->css_meta_key.'_inline_deps', true);
    }
    
    $js_enabled = get_post_meta( $post->ID, $this->js_meta_key.'_enabled', true);
    $css_enabled = get_post_meta( $post->ID, $this->css_meta_key.'_enabled', true);
    
    if( !is_array( $js_files ) )    $js_files = array();
    if( !is_array( $css_files ) )   $css_files = array();
    if( !is_array( $js_deps ) )     $js_deps = array();
    if( !is_array( $css_deps ) )    $css_deps = array();
    
    
    wp_enqueue_style( 'custom-code-codemirror', CUSTOM_CODE_BASE_URI.'/vendor/CodeMirror/lib/codemirror.css');
    wp_enqueue_style( 'custom-code-codemirror-theme', CUSTOM_CODE_BASE_URI.'/vendor/CodeMirror/theme/blackboard.css');
    wp_enqueue_script( 'custom-code-codemirror', CUSTOM_CODE_BASE_URI.'/vendor/CodeMirror/lib/codemirror.js');
    wp_enqueue_script( 'custom-code-codemirror-js', CUSTOM_CODE_BASE_URI.'/vendor/CodeMirror/mode/javascript/javascript.js');
    wp_enqueue_script( 'custom-code-codemirror-css', CUSTOM_CODE_BASE_URI.'/vendor/CodeMirror/mode/css/css.js');
    
    wp_enqueue_style('custom-code-metabox', CUSTOM_CODE_BASE_URI.'/assets/stylesheets/metabox.css');
    wp_enqueue_script('custom-code-metabox', CUSTOM_CODE_BASE_URI.'/assets/javascripts/metabox.js');
    
    ?>
    <input type="hidden" name="_save_custom_code_options" value="1" />
    <div class="custom-code<?= $js_enabled ? ' js-enabled' :'' ?><?= $css_enabled ? ' css-enabled' :'' ?>">
      <table class="form-table">
        <tbody>
          <tr>
            <th scope="row">
              <label for="<?= $this->js_meta_key ?>_enabled">
                Enable Javascript
              </label>
            </th>
            <td>
              <input
                type="checkbox"
                name="<?= $this->js_meta_key ?>_enabled"
                id="<?= $this->js_meta_key ?>_enabled"
                <?= $js_enabled ? 'checked' : '' ?>
              />
            </td>
          </tr>
          <tr class="js-row">
            <th scope="row">
              <label for="<?= $this->js_meta_key ?>_add">
                Javascript Files
              </label>
            </th>
            <td>
              <table class="repeatable enqueue scripts" cellspacing="0">
                <thead>
                  <tr>
                    <th class="handle">Handle</th>
                    <th>Source</th>
                    <th>Dependencies</th>
                    <th>Version</th>
                    <th>&nbsp;</th>
                  </tr>
                </thead>
                <tbody>
                  <tr class="customcode-file clone">
                    <td class="handle"><input type="text" class="input" data-name="<?= $this->js_meta_key ?>_handle[]" /></td>
                    <td class="src"><input type="text" class="input" data-name="<?= $this->js_meta_key ?>_src[]" /></td>
                    <td class="deps"><input type="text" class="input" data-name="<?= $this->js_meta_key ?>_deps[]" /></td>
                    <td class="version"><input type="text" class="input" data-name="<?= $this->js_meta_key ?>_version[]" /></td>
                    <td class="remove"><button class="button button-remove" type="button">Remove</button></td>
                  </tr>
                  <? foreach( $js_files as $file ) { ?>
                  <tr>
                    <td class="handle"><input type="text" class="input" name="<?= $this->js_meta_key ?>_handle[]" value="<?= esc_attr( $file['handle'] ) ?>" /></td>
                    <td class="src"><input type="text" class="input" name="<?= $this->js_meta_key ?>_src[]" value="<?= esc_attr( $file['src'] ) ?>" /></td>
                    <td class="deps"><input type="text" class="input" name="<?= $this->js_meta_key ?>_deps[]" value="<?= esc_attr( implode(', ',$file['deps']) ) ?>"/></td>
                    <td class="version"><input type="text" class="input" name="<?= $this->js_meta_key ?>_version[]" value="<?= esc_attr( $file['version'] ) ?>"/></td>
                    <td class="remove"><button class="button button-remove" type="button">Remove</button></td>
                  </tr>
                  <? } ?>
                </tbody>
                <tfoot>
                  <tr>
                    <td colspan="10" class="add-button-container">
                      <button id="<?= $this->js_meta_key ?>_add" class="button button-primary button-add" type="button">Add Javascript File</button>
                    </td>
                  </tr>
                </tfoot>
              </table>
            </td>
          </tr>
          <tr class="js-row">
            <th scope="row">
              <label for="<?= $this->js_meta_key ?>_inline_deps">JS Dependencies</label>
            </th>
            <td>
              <input type="text"
                     name="<?= $this->js_meta_key ?>_inline_deps"
                     id="<?= $this->js_meta_key ?>_inline_deps"
                     value="<?= esc_attr(implode(', ',$js_deps)) ?>" />
              <p>A comma separated list of wordpress javascript files to enqueue by handle (example: jquery)</p>
            </td>
          </tr>
          <tr class="js-row">
            <th scope="row">
              <label for="<?= $this->js_meta_key ?>">Inline Javascript</label>
            </th>
            <td><div class="codemirror-container">
              <textarea name="<?= $this->js_meta_key ?>" id="<?= $this->js_meta_key ?>" class="codemirror js"><?
                if( $post->ID ) {
                  echo get_post_meta( $post->ID, $this->js_meta_key, true );
                }
              ?></textarea></div>
            </td>
          </tr>
          
          <tr>
            <th scope="row">
              <label for="<?= $this->css_meta_key ?>_enabled">
                Enable CSS
              </label>
            </th>
            <td>
              <input
                type="checkbox"
                name="<?= $this->css_meta_key ?>_enabled"
                id="<?= $this->css_meta_key ?>_enabled"
                <?= $css_enabled ? 'checked' : '' ?>
              />
            </td>
          </tr>
          <tr class="css-row">
            <th scope="row">
              <label for="<?= $this->css_meta_key ?>_add">
                Stylesheets
              </label>
            </th>
            <td>
              <table class="repeatable enqueue styles" cellspacing="0">
                <thead>
                  <tr>
                    <th class="handle">Handle</th>
                    <th>Source</th>
                    <th>Dependencies</th>
                    <th>Version</th>
                    <th>Media</th>
                    <th>&nbsp;</th>
                  </tr>
                </thead>
                <tbody>
                  <tr class="customcode-file clone">
                    <td class="handle"><input type="text" class="input" data-name="<?= $this->css_meta_key ?>_handle[]" /></td>
                    <td class="src"><input type="text" class="input" data-name="<?= $this->css_meta_key ?>_src[]" /></td>
                    <td class="deps"><input type="text" class="input" data-name="<?= $this->css_meta_key ?>_deps[]" /></td>
                    <td class="version"><input type="text" class="input" data-name="<?= $this->css_meta_key ?>_version[]" /></td>
                    <td class="media"><input type="text" class="input" data-name="<?= $this->css_meta_key ?>_media[]" /></td>
                    <td class="remove"><button class="button button-remove" type="button">Remove</button></td>
                  </tr>
                  <? foreach( $css_files as $file ) { ?>
                  <tr>
                    <td class="handle"><input type="text" class="input" name="<?= $this->css_meta_key ?>_handle[]" value="<?= esc_attr( $file['handle'] ) ?>" /></td>
                    <td class="src"><input type="text" class="input" name="<?= $this->css_meta_key ?>_src[]" value="<?= esc_attr( $file['src'] ) ?>" /></td>
                    <td class="deps"><input type="text" class="input" name="<?= $this->css_meta_key ?>_deps[]" value="<?= esc_attr( implode(', ', $file['deps']) ) ?>"/></td>
                    <td class="version"><input type="text" class="input" name="<?= $this->css_meta_key ?>_version[]" value="<?= esc_attr( $file['version'] ) ?>"/></td>
                    <td class="media"><input type="text" class="input" name="<?= $this->css_meta_key ?>_media[]" value="<?= esc_attr( $file['media'] ) ?>" /></td>
                    <td class="remove"><button class="button button-remove" type="button">Remove</button></td>
                  </tr>
                  <? } ?>
                </tbody>
                <tfoot>
                  <tr>
                    <td colspan="10" class="add-button-container">
                      <button id="<?= $this->css_meta_key ?>_add" class="button button-primary button-add" type="button">Add Stylesheet</button>
                    </td>
                  </tr>
                </tfoot>
              </table>
            </td>
          </tr>
          <tr class="css-row">
            <th scope="row">
              <label for="<?= $this->css_meta_key ?>_inline_deps">CSS Dependencies</label>
            </th>
            <td>
              <input type="text"
                     name="<?= $this->css_meta_key ?>_inline_deps"
                     id="<?= $this->css_meta_key ?>_inline_deps"
                     value="<?= esc_attr(implode(', ',$css_deps)) ?>" />
              <p>A comma separated list of wordpress CSS files to enqueue by handle.</p>
            </td>
          </tr>
          <tr class="css-row">
            <th scope="row">
              <label for="<?= $this->css_meta_key ?>">Inline Style</label>
            </th>
            <td><div class="codemirror-container">
              <textarea name="<?= $this->css_meta_key ?>" id="<?= $this->css_meta_key ?>" class="codemirror css"><?
                if( $post->ID ) {
                  echo get_post_meta( $post->ID, $this->css_meta_key, true );
                }
              ?></textarea></div>
            </td>
          </tr>
          
        </tbody>
      </table>
    </div>
    <?
  }
  
  /**
   * @wp.action
   */
  public function save_post( $post_id )
  {
    
    if( !@$_REQUEST['_save_custom_code_options'] ) return;
    
    // enabled
    update_post_meta( $post_id, $this->js_meta_key.'_enabled', (bool)@$_REQUEST[$this->js_meta_key.'_enabled'] );
    update_post_meta( $post_id, $this->css_meta_key.'_enabled', (bool)@$_REQUEST[$this->css_meta_key.'_enabled'] );
    
    // inline
    if( isset( $_REQUEST[$this->js_meta_key] ) ){
      update_post_meta( $post_id, $this->js_meta_key, $_REQUEST[$this->js_meta_key] );
    }
    if( isset( $_REQUEST[$this->css_meta_key] ) ){
      update_post_meta( $post_id, $this->css_meta_key, $_REQUEST[$this->css_meta_key] );
    }
    
    // check for files
    if( isset( $_REQUEST[$this->js_meta_key.'_src']) && is_array($_REQUEST[$this->js_meta_key.'_src']) ){
      $files = array();
      foreach(  $_REQUEST[$this->js_meta_key.'_src'] as $i => $src ){
        if( $src ) $files[] = array(
          'handle'  => $_REQUEST[$this->js_meta_key.'_handle'][$i],
          'src'     => $src,
          'deps'    => array_map('trim', explode(',', $_REQUEST[$this->js_meta_key.'_deps'][$i])),
          'version' => $_REQUEST[$this->js_meta_key.'_version'][$i]
        );
      }
      update_post_meta( $post_id, $this->js_meta_key.'_files', $files );
    }
    
    if( isset( $_REQUEST[$this->css_meta_key.'_src']) && is_array($_REQUEST[$this->css_meta_key.'_src']) ){
      $files = array();
      foreach(  $_REQUEST[$this->css_meta_key.'_src'] as $i => $src ){
        $files[] = array(
          'handle'  => $_REQUEST[$this->css_meta_key.'_handle'][$i],
          'src'     => $src,
          'deps'    => array_map('trim', explode(',', $_REQUEST[$this->css_meta_key.'_deps'][$i])),
          'version' => $_REQUEST[$this->css_meta_key.'_version'][$i],
          'media'   => $_REQUEST[$this->css_meta_key.'_media'][$i]
        );
      }
      update_post_meta( $post_id, $this->css_meta_key.'_files', $files );
    }
    
    // check for dependencies
    if( isset( $_REQUEST[$this->js_meta_key.'_inline_deps']) ){
      update_post_meta( $post_id, $this->js_meta_key.'_inline_deps', array_map('trim', explode(',', $_REQUEST[$this->js_meta_key.'_inline_deps'])) );
    }
    
    if( isset( $_REQUEST[$this->css_meta_key.'_inline_deps']) ){
      update_post_meta( $post_id, $this->css_meta_key.'_inline_deps', array_map('trim', explode(',', $_REQUEST[$this->css_meta_key.'_inline_deps'])) );
    }
    
    
  }
  
  /**
   * @wp.action
   */
  public function wp_footer()
  {
    if( is_admin() || !is_singular() ) return;
    $js_enabled = get_post_meta( get_the_ID(), $this->js_meta_key.'_enabled', true );
    $js = get_post_meta( get_the_ID(), $this->js_meta_key, true );
    if( $js_enabled && $js ){
      ?>
      <script type="text/javascript"><?= $js ?></script>
      <?
    }
    
    $css_enabled = get_post_meta( get_the_ID(), $this->css_meta_key.'_enabled', true );
    $css = get_post_meta( get_the_ID(), $this->css_meta_key, true );
    if( $css_enabled && $css ){
      ?>
      <style type="text/css"><?= $css ?></style>
      <?
    }
  }
  
  /**
   * @wp.action
   */
  public function wp_enqueue_scripts()
  {
    if( is_admin() || !is_singular() ) return;
    
    // enabled
    $js_enabled = get_post_meta( get_the_ID(), $this->js_meta_key.'_enabled', true );
    $css_enabled = get_post_meta( get_the_ID(), $this->css_meta_key.'_enabled', true );
    
    // dependencies
    $js_deps = get_post_meta( get_the_ID(), $this->js_meta_key.'_inline_deps', true);
    if( $js_enabled && $js_deps && is_array($js_deps) ) foreach($js_deps as $dep) wp_enqueue_script($dep);
    
    $css_deps = get_post_meta( get_the_ID(), $this->css_meta_key.'_inline_deps', true);
    if( $css_enabled && $css_deps && is_array($css_deps) ) foreach($css_deps as $dep) wp_enqueue_style($dep);
    
    
    // add our scripts if we have any
    $js_files = get_post_meta( get_the_ID(), $this->js_meta_key.'_files', true );
    
    if( $js_enabled && $js_files && is_array($js_files) ) foreach($js_files as $file) {
      $src = preg_replace_callback('/\{\$([^\}]+)\}/', array(&$this,'replacer'), $file['src']);
      $handle = $file['handle']?$file['handle']:$src;
      $deps = $file['deps'];
      $version = $file['version'] ? $file['version'] : null;
      wp_enqueue_script($handle, $src, array_filter($deps), $version );
    }
    
    $css_files = get_post_meta( get_the_ID(), $this->css_meta_key.'_files', true );
    if( $css_enabled && $css_files && is_array($css_files) ) foreach($css_files as $file) {
      $src = preg_replace_callback('/\{\$([^\}]+)\}/', array(&$this,'replacer'), $file['src']);
      $handle = $file['handle']?$file['handle']:$src;
      $deps = $file['deps'];
      $version = $file['version'] ? $file['version'] : null;
      $media = $file['media'] ? $file['media'] : null;
      wp_enqueue_style( $handle, $src, array_filter($deps), $version, $media );
    }
    
  }
  
  protected function replacer($matches)
  {
    switch( $matches[1] ){
      case 'stylesheet_url':
        return get_stylesheet_directory_uri();
      case 'template_url':
        return get_template_directory_uri();
      default:
        return '';
    }
  }
}