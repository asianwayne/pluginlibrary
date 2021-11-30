<?php 

class Themename_Add_Shoetcode {

  public function __construct() {
    add_action( 'init', array($this,'_themename_add_shortcodes' ));

    add_filter('shortcode_atts__themename_button',array($this,'func'),10,4);


  }

public function _themename_add_shortcodes() {
  add_shortcode( '_themename_button', array($this,'_themename_button' ));
  add_shortcode( '_themename_icon', array($this,'_themename_icon_genetor' ));

}

/**
 * $atts = [],$content = null,$tag = ''
 * $tags is the shorcode name define in add_shortcode  can pass in the shortcode_atts function
 * 可以通过$tag 来filter shortcode, no $tags parameter can not add the shortcode_atts__themename_button filter
 */


public function func($out,$pairs,$atts,$shortcode) {
  return [
    'color'  => 'yellow',];
}


public function _themename_button($atts = [],$content = null,$tag = '') { //shorcode callback 传递三个参数 $atts,$content,$tag
   
  ob_start();
  extract(shortcode_atts(array(
    'color' => 'blue',
    'text' => 'finebutton'

  ),$atts,$tag));
  
  return '<button style="background:' . $color . '">' . do_shortcode($content) . '</button>';
  ?>

  <?php 
  ob_clean();
  
}

public function _themename_icon_genetor($atts){
  ob_start();
  extract(shortcode_atts( array(
    'icon'  => ''
  ), $atts ));

  return '<i class="'. $icon . '"></i>';

}

}

$themename_shortcode = new Themename_Add_Shoetcode();
