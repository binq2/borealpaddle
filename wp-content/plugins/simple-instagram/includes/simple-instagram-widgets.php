<?php
add_action( 'widgets_init', 'si_feed_widget' );
add_action( 'widgets_init', 'si_tag_widget' );
add_action( 'widgets_init', 'si_popular_widget' );
add_action( 'widgets_init', 'si_profile_widget' );

require_once('instagram.class.php');


function si_feed_widget() {
  register_widget( 'SI_Feed_Widget' );
}

class SI_Feed_Widget extends WP_Widget {

  function SI_Feed_Widget() {
    $widget_ops = array( 'classname' => 'si_feed_widget', 'description' => __('A widget to display your Instagram Feed', 'si_feed') );
    
    $control_ops = array( 'width' => 300, 'height' => 350, 'id_base' => 'si_feed_widget' );
    
    $this->WP_Widget( 'si_feed_widget', __('Simple Instagram Feed Widget', 'si_feed'), $widget_ops, $control_ops );
  }
  
  function widget( $args, $instance ) {
    extract( $args );

    //Our variables from the widget settings.
    $title = apply_filters('widget_title', $instance['title'] );
    $count = $instance['count'];
  $user = isset($instance['user']) ? $instance['user'] : '';
  if($user == ''){
    $user = 'self';
  }
    echo $before_widget;

    // Display the widget title 
    if ( $title )
      echo $before_title . $title . $after_title;

    $instagram = _createInstagram();
     
     $feed = $instagram->getUserMedia($user, $count);
     if($feed && count($feed->data) > 0){
      $return = '<div class="si_feed_widget">';
     
       $return .= '<ul class="si_feed_list">';
       
       foreach($feed->data as $image){
         
         $url = $image->images->standard_resolution->url;

         // Fix https
         $url = str_replace('http://', '//', $url);

         $return .= '<li class="si_item">';
         
         $return .= '<a href="'.$image->link.'" target="_blank">';
         $return .= '<img src="'.$url.'">';
         $return .= '</a>';
         $return .= '</li>';
       }
      $return .= '</ul>';
      
      $return .= '</div>';
     }else{
      $return = '';
     }
     
    
    echo $return;
    
    echo $after_widget;
  }

  //Update the widget 
   
  function update( $new_instance, $old_instance ) {
    $instance = $old_instance;

    $instance['title'] = strip_tags( $new_instance['title'] );
    $instance['count'] = $new_instance['count'];
  $instance['user'] = $new_instance['user'];
    return $instance;
  }

  
  function form( $instance ) {

    //Set up some default widget settings.
    $defaults = array( 'title' => __('From Instagram', 'example'), 'count' => __('0', 'example'), 'user' => __('', 'example'));
    $instance = wp_parse_args( (array) $instance, $defaults ); ?>

    
    <p>
      <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title:', 'example'); ?></label>
      <input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" style="width:100%;" />
    </p>
    
    <p>
      <label for="<?php echo $this->get_field_id( 'user' ); ?>"><?php _e('User ID (leave blank to use your own feed):', 'example'); ?></label>
      <input id="<?php echo $this->get_field_id( 'user' ); ?>" name="<?php echo $this->get_field_name( 'user' ); ?>" value="<?php echo $instance['user']; ?>" style="width:100%;" />
    </p>

    <p>
      <label for="<?php echo $this->get_field_id( 'count' ); ?>"><?php _e('Number of Images (0 for Unmlimited):', 'example'); ?></label>
      <input id="<?php echo $this->get_field_id( 'count' ); ?>" name="<?php echo $this->get_field_name( 'count' ); ?>" value="<?php echo $instance['count']; ?>" style="width:100%;" />
    </p>

    
    

  <?php
  }
  
  function _createInstagram(){
    $options = get_option('si_options');
    $auth = get_option('si_oauth');

    $config = array(
          'apiKey'      => $options['instagram_app_id'],
          'apiSecret'   => $options['instagram_app_secret'],
          'apiCallback' => site_url() . '/wp-admin/admin-ajax.php?action=register_instagram'
        );
   
    $instagram = new Instagram($config);
    $instagram->setAccessToken($auth);
    
    return $instagram;
  }
}

function si_tag_widget() {
  register_widget( 'SI_Tag_Widget' );
}

class SI_Tag_Widget extends WP_Widget {

  function SI_Tag_Widget() {
    $widget_ops = array( 'classname' => 'si_tag_widget', 'description' => __('A widget to display an Instagram Feed by Tag', 'si_feed') );
    
    $control_ops = array( 'width' => 300, 'height' => 350, 'id_base' => 'si_tag_widget' );
    
    $this->WP_Widget( 'si_tag_widget', __('Simple Instagram Tag Widget', 'si_tag'), $widget_ops, $control_ops );
  }
  
  function widget( $args, $instance ) {
    extract( $args );

    //Our variables from the widget settings.
    $title = apply_filters('widget_title', $instance['title'] );
    $count = $instance['count'];
  if($count > 25){
    $count = 25;
  }
  $tag = $instance['tag'];
    echo $before_widget;

    // Display the widget title 
    if ( $title )
      echo $before_title . $title . $after_title;

    $instagram = _createInstagram();
     
     $feed = $instagram->getTagMedia($tag, $count);
     
     $return = '<div class="si_feed_widget">';
     
     $return .= '<ul class="si_feed_list">';
     
     foreach($feed->data as $image){
       
       $url = $image->images->standard_resolution->url;
       
       // Fix https
       $url = str_replace('http://', '//', $url);

       $return .= '<li class="si_item">';
       
       $return .= '<a href="'.$image->link.'" target="_blank">';
       $return .= '<img src="'.$url.'">';
       $return .= '</a>';
       $return .= '</li>';
     }
    $return .= '</ul>';
    
    $return .= '</div>';
    
    echo $return;
    
    echo $after_widget;
  }

  //Update the widget 
   
  function update( $new_instance, $old_instance ) {
    $instance = $old_instance;

    $instance['title'] = strip_tags( $new_instance['title'] );
    $instance['count'] = $new_instance['count'];
  $instance['tag'] = strip_tags( $new_instance['tag'] );
    return $instance;
  }

  
  function form( $instance ) {

    //Set up some default widget settings.
    $defaults = array( 'title' => __('From Instagram', 'example'), 'count' => __('12', 'example'), 'tag' => __('food', 'example'));
    $instance = wp_parse_args( (array) $instance, $defaults ); ?>

    
    <p>
      <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title:', 'example'); ?></label>
      <input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" style="width:100%;" />
    </p>
    
    <p>
      <label for="<?php echo $this->get_field_id( 'tag' ); ?>"><?php _e('Tag:', 'example'); ?></label>
      <input id="<?php echo $this->get_field_id( 'tag' ); ?>" name="<?php echo $this->get_field_name( 'tag' ); ?>" value="<?php echo $instance['tag']; ?>" style="width:100%;" />
    </p>
  
    <p>
      <label for="<?php echo $this->get_field_id( 'count' ); ?>"><?php _e('Number of Images (25 Maximum):', 'example'); ?></label>
      <input id="<?php echo $this->get_field_id( 'count' ); ?>" name="<?php echo $this->get_field_name( 'count' ); ?>" value="<?php echo $instance['count']; ?>" style="width:100%;" />
    </p>

    
    

  <?php
  }
  
  function _createInstagram(){
    $options = get_option('si_options');
    $auth = get_option('si_oauth');

    $config = array(
          'apiKey'      => $options['instagram_app_id'],
          'apiSecret'   => $options['instagram_app_secret'],
          'apiCallback' => site_url() . '/wp-admin/admin-ajax.php?action=register_instagram'
        );
   
    $instagram = new Instagram($config);
    $instagram->setAccessToken($auth);
    
    return $instagram;
  }
}

function si_popular_widget() {
  register_widget( 'SI_Popular_Widget' );
}

class SI_Popular_Widget extends WP_Widget {

  function SI_Popular_Widget() {
    $widget_ops = array( 'classname' => 'si_popular_widget', 'description' => __('A widget to display popular Instagram Images', 'si_popular') );
    
    $control_ops = array( 'width' => 300, 'height' => 350, 'id_base' => 'si_popular_widget' );
    
    $this->WP_Widget( 'si_popular_widget', __('Simple Instagram Popular Widget', 'si_popular'), $widget_ops, $control_ops );
  }
  
  function widget( $args, $instance ) {
    extract( $args );

    //Our variables from the widget settings.
    $title = apply_filters('widget_title', $instance['title'] );
    $count = $instance['count'];
    
    if($count > 16){
      $count = 16;
    }
    
    echo $before_widget;

    // Display the widget title 
    if ( $title )
      echo $before_title . $title . $after_title;

    $instagram = _createInstagram();
     
     $feed = $instagram->getPopularMedia();
     $return = '<div class="si_feed_widget">';
     
     $return .= '<ul class="si_feed_list">';
     
     for($i=0; $i <= $count; $i++){
       if($feed->data[$i]->images != NULL){
         $url = $feed->data[$i]->images->standard_resolution->url;
         
         // Fix https
         $url = str_replace('http://', '//', $url);

         $return .= '<li class="si_item">';
         
         $return .= '<a href="'.$feed->data[$i]->link.'" target="_blank">';
         $return .= '<img src="'.$url.'">';
         
       $return .= '</a>';
       
         $return .= '</li>';
       }
       
    }

    $return .= '</ul>';
    
    $return .= '</div>';
    
    echo $return;
    
    echo $after_widget;
  }

  //Update the widget 
   
  function update( $new_instance, $old_instance ) {
    $instance = $old_instance;

    $instance['title'] = strip_tags( $new_instance['title'] );
    $instance['count'] = $new_instance['count'];

    return $instance;
  }

  
  function form( $instance ) {

    //Set up some default widget settings.
    $defaults = array( 'title' => __('Popular from Instagram', 'example'), 'count' => __('16', 'example'));
    $instance = wp_parse_args( (array) $instance, $defaults ); ?>

    
    <p>
      <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title:', 'example'); ?></label>
      <input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" style="width:100%;" />
    </p>

    <p>
      <label for="<?php echo $this->get_field_id( 'count' ); ?>"><?php _e('Number of Images (16 Maximum):', 'example'); ?></label>
      <input id="<?php echo $this->get_field_id( 'count' ); ?>" name="<?php echo $this->get_field_name( 'count' ); ?>" value="<?php echo $instance['count']; ?>" style="width:100%;" />
    </p>

    
    

  <?php
  }
  
  
  
  function _createInstagram(){
    $options = get_option('si_options');
    $auth = get_option('si_oauth');

    $config = array(
          'apiKey'      => $options['instagram_app_id'],
          'apiSecret'   => $options['instagram_app_secret'],
          'apiCallback' => site_url() . '/wp-admin/admin-ajax.php?action=register_instagram'
        );
   
    $instagram = new Instagram($config);
    $instagram->setAccessToken($auth);
    
    return $instagram;
  }
}

function si_profile_widget() {
  register_widget( 'SI_Profile_Widget' );
}

class SI_Profile_Widget extends WP_Widget {

  function SI_Profile_Widget() {
    $widget_ops = array( 'classname' => 'si_profile_widget', 'description' => __('A widget to display your Instagram Profile', 'si_profile') );
    
    $control_ops = array( 'width' => 300, 'height' => 350, 'id_base' => 'si_profile_widget' );
    
    $this->WP_Widget( 'si_profile_widget', __('Simple Instagram Profile Widget', 'si_profile'), $widget_ops, $control_ops );
  }
  
  function widget( $args, $instance ) {
    extract( $args );

    //Our variables from the widget settings.
    $title = apply_filters('widget_title', $instance['title'] );
    $profile_picture = $instance['profile_picture'];

    

    $username = $instance['username'];
    $full_name = $instance['full_name'];
    $bio = $instance['bio'];
    $website = $instance['website'];
    
    
    echo $before_widget;

    // Display the widget title 
    if ( $title )
      echo $before_title . $title . $after_title;

    $instagram = _createInstagram();
     
     $user = $instagram->getUser();
     $data = $user->data;
        
     $return = '<div class="si_profile_widget">';
     
     if($profile_picture == 'true' && $data->profile_picture != ''){
       // Fix https
       $url = str_replace('http://', '//', $data->profile_picture);
       $return .= '<div class="si_profile_picture">';
       $return .= '<img src="'.$url.'">';
       $return .= '</div>';
     }
     
     if($username == 'true' && $data->username != ''){
       $return .= '<div class="si_username">'.$data->username.'</div>';
     }
     
     if($full_name == 'true' && $data->full_name != ''){
       $return .= '<div class="si_full_name">'.$data->full_name.'</div>';
     }
     
     if($bio == 'true' && $data->bio != ''){
       $return .= '<div class="si_bio">'.$data->bio.'</div>';
     }
     
     if($website == 'true' && $data->website != ''){
       $return .= '<div class="si_website"><a href="'.$data->website.'">View Website</a></div>';
     }
     
     $return .= '</div>';
    
    echo $return;
    
    echo $after_widget;
  }

  //Update the widget 
   
  function update( $new_instance, $old_instance ) {
    $instance = $old_instance;

    $instance['title'] = strip_tags( $new_instance['title'] );
  $instance['profile_picture'] = isset($new_instance['profile_picture']) ? 'true' : 'false';
  $instance['username'] = isset($new_instance['username']) ? 'true' : 'false';
  $instance['full_name'] = isset($new_instance['full_name']) ? 'true' : 'false';
  $instance['bio'] = isset($new_instance['bio']) ? 'true' : 'false';
  $instance['website'] = isset($new_instance['website']) ? 'true' : 'false';
  
    return $instance;
  }

  
  function form( $instance ) {

    //Set up some default widget settings.
    $defaults = array( 'title' => __('My Instagram Profile', 'example'));
    $instance = wp_parse_args( (array) $instance, $defaults ); ?>

    
    <p>
      <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title:', 'example'); ?></label>
      <input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" style="width:100%;" />
    </p>

    <p>
      <label>Include the Following:</label><br />
      <input type="checkbox" name="<?php echo $this->get_field_name( 'profile_picture' ); ?>" <?php if(isset($instance['profile_picture']) && $instance['profile_picture'] == 'true'){ echo 'checked="checked"'; }?> /> Profile Picture<br />
      <input type="checkbox" name="<?php echo $this->get_field_name( 'username' ); ?>" <?php if(isset($instance['username']) && $instance['username'] == 'true'){ echo 'checked="checked"'; }?> /> Username<br />
      <input type="checkbox" name="<?php echo $this->get_field_name( 'full_name' ); ?>" <?php if(isset($instance['full_name']) && $instance['full_name'] == 'true'){ echo 'checked="checked"'; }?> /> Full Name<br />
      <input type="checkbox" name="<?php echo $this->get_field_name( 'bio' ); ?>" <?php if(isset($instance['bio']) && $instance['bio'] == 'true'){ echo 'checked="checked"'; }?> /> Bio<br />
      <input type="checkbox" name="<?php echo $this->get_field_name( 'website' ); ?>" <?php if(isset($instance['website']) && $instance['website'] == 'true'){ echo 'checked="checked"'; }?> /> Website<br />
    </p>

    
    

  <?php
  }
  
  
  
  function _createInstagram(){
    $options = get_option('si_options');
    $auth = get_option('si_oauth');

    $config = array(
          'apiKey'      => $options['instagram_app_id'],
          'apiSecret'   => $options['instagram_app_secret'],
          'apiCallback' => site_url() . '/wp-admin/admin-ajax.php?action=register_instagram'
        );
   
    $instagram = new Instagram($config);
    $instagram->setAccessToken($auth);
    
    return $instagram;
  }
}


?>