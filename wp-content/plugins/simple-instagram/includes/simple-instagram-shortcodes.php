<?php
require_once('instagram.class.php');

  function si_feed( $atts ) {
    extract( shortcode_atts( array(
          'limit' => 10,
          'size' => 'medium',
          'wrapper' => 'div',
          'link' => 'true',
          'width' => 'auto',
          'tag' => '',
          'user' => 'self'
     ), $atts ) );
     
     $instagram = _createInstagram();
     
	 if($tag == ''){
     	$feed = $instagram->getUserMedia($user, $limit);
	 }else{
     	$feed = $instagram->getTagMedia($tag, $limit);
	 }
	 if(($feed) && count($feed->data) > 0){
	 	if(count($feed->data) > $limit){
	 		$total = count($feed->data);
	 		$diff = $total - $limit;
			$start = $total - $diff;
			for($i=$start; $i <= $total; $i++){	
				unset($feed->data[$i]);
			}
	 	}
	 	//$return = '<div class="si_feed">';
	     $return .= '<div class="woocommerce columns-6">';
			$return .= '<ul class="products grid-layout"  >';

		 $width = str_replace('px', '', $width);
		 
	     if($width != 'auto'){
	       if($width > 612){
	         $width = 612;
	       }
	       $w_param = 'width="'.$width.'" height="'.$width.'"';
	     }else{
	       $w_param = '';
	     }
	     
	     if($wrapper == 'li'){
	       $return .= '<ul class="si_feed_list">';
	     }
	     
	     $L_url ='';
	     
	     foreach($feed->data as $image){
	       switch($size){
	         case 'lightbox':
	           $L_url = $image->images->standard_resolution->url;
	           $url = $image->images->thumbnail->url;
	           break;
	         case 'full':
	           $L_url = $url = $image->images->standard_resolution->url;
	           break;
	         case 'medium':
	           $L_url =  $url = $image->images->low_resolution->url;
	           break;
	         case 'small':
	           $L_url =  $url = $image->images->thumbnail->url;
	           break;
	       }

	       // Fix https
	       $url = str_replace('http://', '//', $url);
	       if($wrapper == 'div'){
	         $return .= '<div data-element class="product-category">';
	       }else{
	         $return .= '<li class="si_item">';
	       }
	       
	       if($link == 'true'){
	         $return .= '<a href="'.$L_url.'" rel="lightbox[colorbox]">';
	       }
	       $return .= '<img src="'.$url.'" '.$w_param.' >';
	       if($link == 'true'){
	         $return .= '</a>';
	       }
	       
	       if($wrapper == 'div'){
	         $return .= '</div>';
	       }else{
	         $return .= '</li>';
	       }
	     }
	     if($wrapper == 'li'){
	       $return .= '</ul>';
	     }
	     //$return .= '</div>';
	     	$return .= '</ul>';
		$return .= '</div>';

	 }else{
	 	$return = '';
	 }
     
     return $return;
  }
  
  add_shortcode( 'si_feed', 'si_feed');
  
  function si_popular($atts){
  	extract( shortcode_atts( array(
          'count' => 16,
          'size' => 'full',
          'wrapper' => 'div',
          'link' => 'true',
          'width' => 'auto'
     ), $atts ) );
	 
	 if($count >= 16){
	 	$count = 15;
	 }
	 
  	 $instagram = _createInstagram();
     
     $feed = $instagram->getPopularMedia();
     $return = '<div class="si_feed">';
     
     if($width != 'auto'){
       if($width > 612){
         $width = 612;
       }
       $w_param = 'width="'.$width.'" height="'.$width.'"';
     }else{
       $w_param = '';
     }
     
     if($wrapper == 'li'){
       $return .= '<ul class="si_feed_list">';
     }
     
	 for($i=0; $i <= $count; $i++){
       if($feed->data[$i]->images != NULL){
	       switch($size){
	         case 'full':
	           $url = $feed->data[$i]->images->standard_resolution->url;
	           break;
	         case 'medium':
	           $url = $feed->data[$i]->images->low_resolution->url;
	           break;
	         case 'small':
	           $url = $feed->data[$i]->images->thumbnail->url;
	           break;
	       }

	       // Fix https
	       $url = str_replace('http://', '//', $url);
	       if($wrapper == 'div'){
	         $return .= '<div class="si_item">';
	       }else{
	         $return .= '<li class="si_item">';
	       }
	       
	       if($link == 'true'){
	         $return .= '<a href="'.$feed->data[$i]->link.'" target="_blank">';
	       }
	       $return .= '<img src="'.$url.'" '.$w_param.' >';
	       if($link == 'true'){
	         $return .= '</a>';
	       }
	       
	       if($wrapper == 'div'){
	         $return .= '</div>';
	       }else{
	         $return .= '</li>';
	       }
	   }
     }
	 
     for($i==0; $i <= $count; $i++){
       if($feed->data[$i]->images != NULL){
           $url = $feed->data[$i]->images->standard_resolution->url;

           // Fix https
       	   $url = str_replace('http://', '//', $url);

	       $return .= '<li class="si_item">';
	       
	       $return .= '<a href="'.$feed->data[$i]->link.'" target="_blank">';
	       $return .= '<img src="'.$url.'">';
	       
	       $return .= '</li>';
       }
       
    }

    if($wrapper == 'li'){
       $return .= '</ul>';
     }
     $return .= '</div>';
	
	return $return;
  }

  add_shortcode( 'si_popular', 'si_popular');
  
  function si_profile( $atts ) {
    extract( shortcode_atts( array(
          'username' => 'true',
          'profile_picture' => 'true',
          'bio' => 'true',
          'website' => 'true',
          'full_name' => 'true',
          'themed' => 'false'
     ), $atts ) );
     
     $instagram = _createInstagram();
     
     $user = $instagram->getUser();
     $data = $user->data;
     
     if($themed == 'true'){
       $class = 'si_profile themed';
     }else{
       $class = 'si_profile';
     }
     $return = '<div class="'.$class.'">';
     
     if($profile_picture == 'true' && $data->profile_picture != ''){
       $return .= '<div class="si_profile_picture">';
       $return .= '<img src="'.$data->profile_picture.'">';
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
     
     return $return;
  }
  
  add_shortcode( 'si_profile', 'si_profile');
  
  
  
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
  
?>
