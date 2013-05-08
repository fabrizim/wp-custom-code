<?php

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
    ?>
    <div class="custom-code">
      <script src="<?= CUSTOM_CODE_BASE_URI ?>/vendor/CodeMirror/lib/codemirror.js"></script>
      <link rel="stylesheet" href="<?= CUSTOM_CODE_BASE_URI ?>/vendor/CodeMirror/lib/codemirror.css">
      <link rel="stylesheet" href="<?= CUSTOM_CODE_BASE_URI ?>/vendor/CodeMirror/theme/blackboard.css">
      <script src="<?= CUSTOM_CODE_BASE_URI ?>/vendor/CodeMirror/mode/javascript/javascript.js"></script>
      <script src="<?= CUSTOM_CODE_BASE_URI ?>/vendor/CodeMirror/mode/css/css.js"></script>
      
      <label for="<?= $this->js_meta_key ?>">Javascript</label>
      <textarea name="<?= $this->js_meta_key ?>" id="<?= $this->js_meta_key ?>"><?
        if( $post->ID ) {
          echo get_post_meta( $post->ID, $this->js_meta_key, true );
        }
      ?></textarea>
      <label for="<?= $this->js_meta_key ?>">CSS</label>
      <textarea name="<?= $this->css_meta_key ?>" id="<?= $this->css_meta_key ?>"><?
        if( $post->ID ) {
          echo get_post_meta( $post->ID, $this->css_meta_key, true );
        }
      ?></textarea>
      <script type="text/javascript">
          CodeMirror.fromTextArea(document.getElementById("<?= $this->js_meta_key ?>"), {
            mode: 'javascript',
            theme: 'blackboard',
            lineNumbers: true
          });
          CodeMirror.fromTextArea(document.getElementById("<?= $this->css_meta_key ?>"), {
            mode: 'css',
            theme: 'blackboard',
            lineNumbers: true
          });
      </script>
      <style type="text/css">
      .custom-code .CodeMirror{
        width: 100%;
        height: 300px;
        border: 1px solid #ccc;
        border-radius: 4px;
        margin-bottom: 10px;
      }
      </style>
    </div>
    <?
  }
  
  /**
   * @wp.action
   */
  public function save_post( $post_id )
  {
    if( isset( $_REQUEST[$this->js_meta_key] ) ){
      update_post_meta( $post_id, $this->js_meta_key, $_REQUEST[$this->js_meta_key] );
    }
    if( isset( $_REQUEST[$this->css_meta_key] ) ){
      update_post_meta( $post_id, $this->css_meta_key, $_REQUEST[$this->css_meta_key] );
    }
  }
  
  /**
   * @wp.action
   */
  public function wp_footer()
  {
    if( is_admin() || !is_singular() ) return;
    $js = get_post_meta( get_the_ID(), $this->js_meta_key, true );
    if( !$js ) return;
    ?>
    <script type="text/javascript"><?= $js ?></script>
    <?
    
    $css = get_post_meta( get_the_ID(), $this->css_meta_key, true );
    if( !$css ) return;
    ?>
    <style type="text/css"><?= $css ?></style>
    <?
  }
  
}