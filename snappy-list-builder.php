<?php 
/**
 * Plugin Name:Snappy list builder
 * Plugin URI:weblinks.cc
 * Description:plugin for collect email informations
 * Version:1.10.3
 * Author:Wayne
 * Author URI:weblinks.cc
 * License:GPL
 * Text Domain:snappy-list
 */

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

$path = plugins_url();

echo __FILE__;
require plugin_dir_path( __FILE__ ) . 'ajax-helper.php';
require plugin_dir_path( __FILE__ ) . 'slb-metabox.php';
/* !0 hooks */

//0.1 
add_action( 'init', 'slb_register_shortcodes' );

//register custom column header
add_filter('manage_edit-slb_subscriber_columns','slb_subscriber_column_headers');
add_filter('manage_edit-slb_list_columns','slb_list_column_headers');
add_filter('manage_slb_subscriber_posts_custom_column','slb_subscriber_column_data',1,2);
add_filter('manage_slb_list_posts_custom_column','slb_list_column_data',1,2);


//register ajax_actions
add_action('wp_ajax_nopriv_slb_save_subscription','slb_save_subscription'); //for website visitor
add_action('wp_ajax_slb_save_subscription','slb_save_subscription');  //for admin user


add_action('wp_enqueue_scripts','slb_public_scripts');
//0.2 filters
function slb_subscriber_column_headers($columns) {
  //create custom header data
  $columns = array(
    'cb'  => '<input type="checkbox">',
    'title'  => __( 'Subsriber Name'),
    'email'  => __('Email Address'),
    'address'  => __('Addresses')

  );

  return $columns;
}

function slb_list_column_headers($columns) {
  //create custom header data
  $columns = array(
    'cb'  => '<input type="checkbox">',
    'title'  => __( 'List Name'),
    'shortcode' => __('Shortcode'),
    

  );

  return $columns;
}

function slb_subscriber_column_data($column,$post_id) {
  //seup return text
  $output = '';
  switch ($column) {

    case 'email':
    $email = get_post_meta( $post_id, 'slb_email', true );
    $output .= $email;
      // code...
      break;

      case 'address':
        // code...
        echo "Where are you";
        break;
  }

echo $output;

}

function slb_list_column_data($column,$post_id) {
  //seup return text
  $output = '';
  switch ($column) {

    case 'shortcode':
    $output .= '[slb_form id="'. $post_id . '"]';
      // code...
      break;

  }

echo $output;

}



/* !1shortcodes */

//1.1
function slb_register_shortcodes() {
  add_shortcode( 'slb_form', 'slb_form_shortcode' );
}

//1.2
function slb_form_shortcode($args,$content="") {

  //get the list id
  $list_id = 0;
  if (isset($args['id'])) {
    $list_id = (int)$args['id'];  //make $args['id'] integier
    // code...
  }
  //setup form html
  
  $output = '

    <div class="slb">
    
      <form id="slb_form" name="slb_form" class="slb-form" method="post"
      action="/wp-admin/admin-ajax.php?action=slb_save_subscription" method="post">
      
        <input type="hidden" name="slb_list" value="'. $list_id .'">
      
        <p class="slb-input-container">
        
          <label>Your Name</label><br />
          <input type="text" name="slb_fname" placeholder="First Name" />
          <input type="text" name="slb_lname" placeholder="Last Name" />
        
        </p>
        
        <p class="slb-input-container">
        
          <label>Your Email</label><br />
          <input type="email" name="slb_email" placeholder="ex. you@email.com" />
        
        </p>';
        
        // including content in our form html if content is passed into the function
        if( strlen($content) ):
        
          $output .= '<div class="slb-content">'. wpautop($content) .'</div>';
        
        endif;
        
        // completing our form html
        $output .= '<p class="slb-input-container">
        
          <input type="submit" class="slb_submit" name="slb_submit" value="Sign Me Up!" />
        
        </p>
      
      </form>
    
    </div>
   ';

   return $output;
}


/**
 * !2 Metaboxes
 */

//2.1 function metabox


//change the screen admin column title of subscribers

function slb_edit_post_change_title() {
  global $post;
  if (isset($post->post_type) AND $post->post_type == 'slb_subscriber') {
    add_filter('the_title','slb_subscriber_title',100,2);
  }
}
add_action('admin_head-edit.php','slb_edit_post_change_title');

function slb_subscriber_title($title,$post_id) {
  $new_title = get_post_meta($post_id,'slb_first_name',true) . ' ' . get_post_meta($post_id,'slb_last_name',true);
  return $new_title;
}


/*external scripts */
//hint: loads external files into public websites
function slb_public_scripts() {
  wp_enqueue_script('snappy-list-builder-js-public', plugins_url( '/js/public/snappy-list-builder.js', __FILE__ ),array('jquery'),'',true);

  wp_localize_script( 'snappy-list-builder-js-public', 'methodData', array(
    'admin_url'  => admin_url(),
    'ajax_url'  => admin_url('admin-ajax.php')
  ) );
}

function slb_add_subscriber_metabox() {
  add_meta_box( 
    'slb-subscriber-details',   //id 
    'Subscriber Details',  //title
    'slb_subscriber_metabox_callback',   //callback
    'slb_subscriber',  //screen
    'normal',  // context
    'default');
}

add_action('add_meta_boxes','slb_add_subscriber_metabox');


function slb_subscriber_metabox_callback() {
  wp_nonce_field(basename(__FILE__),'slb_subscriber_nonce');
  global $post;
  $post_id = $post->ID;

 ?>

  <div class="slb-field-row">
    <div class="slb-field-container">
      <label for="">First Name <span>*</span></label>
      <input type="text" name="slb_first_name" required class="widefat" value="<?php echo get_post_meta( $post_id, 'slb_first_name', true ); ?>">
    </div>
    <div class="slb-field-container">
      <label for="">Last Name</label>
      <input type="text" name="slb_last_name" required class="widefat" value="<?php echo get_post_meta( $post_id, 'slb_last_name', true ); ?>">
    </div>
  </div>

  <div class="slb-field-row">
    <div class="slb-field-container">
      <label for="#">Email</label>
      <input type="email" name="slb_email" required class="widefat" value="<?php echo get_post_meta( $post_id, 'slb_email', true ); ?>">
    </div>
  </div>

  <div class="slb-field-row">
    <div class="slb-field-container">
      <ul>

        <?php 
        $lists = get_post_meta( $post_id, 'slb_subscription', false );


        $listQuery = new WP_Query(array(
          'post_type'  => 'slb_list',
        ));

        while($listQuery->have_posts()) : $listQuery->the_post();

         ?>
         <?php $checked = (in_array(get_the_ID(), $lists)) ? 'checked="checked"' : ''; ?>
        <li><label for="#"><input type="checkbox" name="slb_subscription[]" value="<?php echo get_the_ID(); ?>"<?php echo $checked; ?> ><?php the_title(); ?></label></li>

      <?php endwhile;wp_reset_postdata(); ?>
        
      </ul>
    </div>
  </div>

  <?php
}

function slb_save_subscriber_meta($post_id,$post) {

  

  // Verify nonce
  if(!isset( $_POST['slb_subscriber_nonce'] ) || !wp_verify_nonce( $_POST['slb_subscriber_nonce'], basename(__FILE__) ) ) {
    return $post_id;
  }

  //get the post type object
  $post_type = get_post_type_object( $post->post_type );

  //check if the current user has permission
  if (!current_user_can( $post_type->cap->edit_post,$post_id )) {
    // code...
    return $post_id;
  }

  //get the posted data and sanitize it
  
  $first_name = (isset($_POST['slb_first_name'])) ? sanitize_text_field( $_POST['slb_first_name'] ) : '';
  $last_name = (isset($_POST['slb_last_name'])) ? sanitize_text_field( $_POST['slb_last_name'] ) : '';
  $email = (isset($_POST['slb_email'])) ? sanitize_text_field( $_POST['slb_email'] ) : '';
  $subscriptions = (isset($_POST['slb_subscription']) && is_array($_POST['slb_subscription'])) ? (array) $_POST['slb_subscription'] : [];


  //update post meta
  update_post_meta( $post_id, 'slb_first_name', $first_name );
  update_post_meta( $post_id, 'slb_last_name', $last_name );
  update_post_meta( $post_id, 'slb_email', $email );

  //update subscription meta
  delete_post_meta( $post_id, 'slb_subscription' );


  //add new subscription meta
  if (!empty($subscriptions)) {
    foreach($subscriptions as $subscription_id) {
      //add subscription relational meta value
      add_post_meta( $post_id, 'slb_subscription', $subscription_id, false ); 
    }
  }



}

add_action('save_post','slb_save_subscriber_meta',10,2);

//save subscriptio data to and exsting or new subscriber

function slb_save_subscription() {

  //setuo default result data
  $result = array(
    'status'  => 0,
    'message'  => 'Subscription not saved',
    'error' => '',
    'errors'  => array()

  );
  try {
    //get list id
    $list_id = (int)$_POST['slb_list'];

    //prepare subscriber data
    $subscriber_data = array(
      'fname'  => esc_attr( $_POST['slb_fname'] ),
      'lname'  => esc_attr($_POST['slb_lname']),
      'email'  => esc_attr($_POST['slb_email']),
    );

    //setup erros
    $errors = array();

    //form validation
    if(!strlen($subscriber_data['fname'])) $errors['fname'] = "First name is required!";
    if(!strlen($subscriber_data['lname'])) $errors['lname'] = "Last name is required!";
    if(!strlen($subscriber_data['email'])) $errors['email'] = "Email is required!";
    if(strlen($subscriber_data['email']) && !is_email( $subscriber_data['email'] )) $errors['email'] = "Email address must be valid!";

    //IF there are erros
    if(count($errors)) :

      //append errors to result structer for later use
      $result['error'] = 'Some fields are still required!';

      $result['errors'] = $errors;

    else :

      //IF there are no erros proceed .. 

    //attempt to create / save subscriber
    $subscriber_id = slb_save_subscriber($subscriber_data);

    //if subscriber saved suscessfully generate $subscriber_id
    if ($subscriber_id) {

      //If subscriber already has this subscription
      if (slb_subscriber_has_subscription($subscriber_id,$list_id)) {
        // code... get list object
        $list = get_post($list_id);

        //return detailed error
        $result['error'] = esc_attr($subscriber_data['email'] . ' is already subscribsed to  ' . $list->post_title . '.');

      } else {
        //save new subscription
        $subscription_saved = slb_add_subscription($subscriber_id,$list_id);

        //if subscription was saved successfully
        if ($subscription_saved) {
          // code...
          $result['status'] = 1;
          $result['message'] = 'Subscription Saved';
        } else {

          //return detailed error
          $result['error']  = 'Unable to save subscription';
        }
      }
    }

  endif;

  } catch(Exception $e) {
    // a php error occured
    $result['error'] = 'Caught exception: ' . $e->getMessage;
  }

  slb_return_json($result);
}

function slb_save_subscriber($subscriber_data) {

  ////setup default id 0 means the subscriber was not saved
  $subscriber_id = 0;

  try{

    $subscriber_id = slb_get_subscriber_id($subscriber_data['email']);

    //if the subscriber does not already exists
    
    if (!$subscriber_id) {
      // add new subscriber to database
      
      $subscriber_id = wp_insert_post( 
        array(
          'post_type'  => 'slb_subscriber',
          'post_title' => $subscriber_data['fname'] . ' ' . $subscriber_data['lname'],
          'post_status'  => 'publish',

        ),

        true);
    }

    update_post_meta( $subscriber_id, 'slb_first_name', $subscriber_data['fname'] );
    update_post_meta( $subscriber_id, 'slb_last_name', $subscriber_data['lname'] );
    update_post_meta( $subscriber_id, 'slb_email', $subscriber_data['email'] );

  } catch( Exception $e) {

  } return $subscriber_id;

}

// hint: adds list to subscribers subscriptions
// 
function slb_add_subscription( $subscriber_id, $list_id ) {

  // setup default return value
  $subscription_saved = false;
  
  // IF the subscriber does NOT have the current list subscription
  if( !slb_subscriber_has_subscription( $subscriber_id, $list_id ) ):

    // get subscriptions and append new $list_id
    $subscriptions = slb_get_subscriptions( $subscriber_id );

    array_push($subscriptions, $list_id);
    
    // update slb_subscription
     //add new subscription meta
     //
     $subscriptions = (isset($_POST['slb_list']) && is_array($_POST['slb_list'])) ? (array) $_POST['slb_list'] : [];

  if (!empty($subscriptions)) {
    foreach($subscriptions as $subscription_id) {
      //add subscription relational meta value
      add_post_meta( $post_id, 'slb_subscription', $subscription_id, false ); 
    }
  }
    // subscriptions updated!
    $subscription_saved = true;
  
  endif;
  
  // return result
  return $subscription_saved;
  
}

function slb_subscriber_has_subscription($subscriber_id,$list_id) {
  //setup default return value
  $has_subscription = false;

  //get subscriber
  $subscriber = get_post($subscriber_id);

  $subscriptions = slb_get_subscriptions( $subscriber_id );

  //check subscriptions fo $listd_id
  if (in_array($list_id,$subscriptions)) {
    // code...
    $has_subscription = true;

  }
  return $has_subscription;
}

function slb_get_subscriber_id($email) {
  $subscriber_id = 0;

  try {
    //check if subscriber already exist
    $subscriber_query = new WP_Query(array(
      'post_type'  => 'slb_subscriber',
      'posts_per_page'  => 1,
      'meta_key'  => 'slb_email',
      'meta_query' => array(
        array(
          'key'  => 'slb_email',
          'value'  => $email,
          'compare'  => '='

        ))));

    if ($subscriber_query->have_posts()) {

      //get the subscriber id
      $subscriber_query->the_post();
      $subscriber_id = get_the_ID();
      // code...
    }
  } catch(Exception $e) {

  }
  wp_reset_query();
  return (int)$subscriber_id;


}

function slb_get_subscriptions( $subscriber_id ) {

  $subscriptions = array();

  // get subscriptions (returns array of list objects)
  $lists = get_post_meta($subscriber_id,'slb_subscription',false);


  // IF $lists returns something
  if( $lists ):
  
    // IF $lists is an array and there is one or more items
    if( is_array($lists) && count($lists) ):
      // build subscriptions: array of list id's
      foreach( $lists as &$list):
        $subscriptions[]= (int)$list->ID;
      endforeach;
    elseif( is_numeric($lists) ):
      // single result returned
      $subscriptions[]= $lists;
    endif;
  
  endif;
  
  return (array)$subscriptions;
  
}

//////////////////////////////////////////
//add list to subscriber's subscription //
//////////////////////////////////////////

//returns an array of list_id's

// hint: returns an array of list_id's


function slb_return_json($php_array) {
  //encode result as json string
  $json_result = json_encode( $php_array );

  // return result
  die($json_result);

  //stop all other processing
  exit;

}



//returns an array of subscriber data including the subscriptions 
function slb_get_subscriber_data($subscriber_id) {
  //setup subscriber data
  $subscriber_data = array();

  //get subscriber object
  $subscriber = get_post($subscriber_id);


  //if subscriber object is valid
  if (isset($subscriber->post_type) && $subscriber->post_type == 'slb_subscriber') {
    // code...build subsriber data for return
    $subscriber_data = array(
      'name'  => get_post_meta($subscriber->ID,'slb_first_name',true) . ' ' .get_post_meta($subscriber->ID,'slb_last_name',true) ,
      'fname'  => get_post_meta($subscriber->ID,'slb_first_name',true),
      'lname'  => get_post_meta($subscriber->ID,'slb_last_name',true),
      'email'  => get_post_meta($subscriber->ID,'slb_email',true),
      'subscriptions'  => slb_get_subscriptions($subscriber_id)

    );
  }
  return $subscriber_data;
}

