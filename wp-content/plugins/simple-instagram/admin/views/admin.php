<?php
/**
 * Represents the view for the administration dashboard.
 *
 * This includes the header, options, and other information that should provide
 * The User Interface to the end user.
 *
 * @package   Simple Instagram
 * @author    Aaron Speer <adspeer@gmail.com>
 * @license   GPL-2.0+
 * @copyright 2013 Your Name or Company Name
 */
?>

<?php 
  $options_check = get_option('si_options');
  if(strlen($options_check['instagram_app_id']) > 0 && strlen($options_check['instagram_app_secret']) > 0){
    $set = 1;
  }else{
    $set = 0;
  }
  
  if(isset($options_check['instagram_oauth']) && strlen($options_check['instagram_oauth']) > 0){
    $auth = 1;
  }else{
    $auth = 0;
  }
?>

<?php if(isset($_GET['settings-updated'])){ update_option('si_oauth', ''); } ?>

<div class="wrap">

	<?php screen_icon(); ?>
	<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
	<section id="step01">
	  <div class="section_title">
	    Step 01: Register Your Application with Instagram
	  </div>
	  <div class="section_content <?php if($set == 0){ echo "active"; } ?>">
	    <ol>
        <li>Visit <a href="http://instagram.com/developer/" rel="nofollow">http://instagram.com/developer/</a> and log in (click "login" in the upper-righthand corner)</li>
        <li>Once you've logged in, you'll be taken to your Edit Profile page. Make any changes you'd like, and then click on the API link in the footer of the page. This will return you to the Developer homepage.</li>
        <li>Click the "Register Your Application" button.</li>
        <li>Click "Register a New Client" in the upper-righthand corner.</li>
        <li>
          On this page, enter the following information into the form:
          <ul>
            <li><strong>Application Name</strong>: The name of your application. Choose something that is easy to remember and relates to your website in a meaningful way.</li>
            <li><strong>Description</strong>: Describe your application and its functionality.</li>
            <li><strong>Website</strong>: The URL of this website ( <?php echo get_site_url(); ?> )</li>
            <li><strong>OAuth redirect_uri</strong>: Use the following URL: <br /><input type="text" id="url_text" disabled value="<?php echo  get_site_url(); ?>/wp-admin/admin-ajax.php?action=register_instagram"><button id="d_clip_button" data-clipboard-target="url_text" class="mini-button" value="Copy to Clipboard"><i class="fa fa-clipboard"></i> Copy</button></li>
          </ul>
        </li>
        <li>Click the "Register" button. On the resulting page, take note of the Client ID and Client Secret values for the next step.</li>
      </ol>
	  </div>
	  <script>
	ZeroClipboard.setDefaults( { moviePath: '<?php echo  str_replace('admin/views', 'admin/assets/js/', plugins_url( NULL , __FILE__ )); ?>ZeroClipboard.swf' } );
</script>
	</section>
	
	<section id="step02">
	  <div class="section_title">
      Step 02: Copy Your Client ID and Client Secret
    </div>
    <div class="section_content <?php if($set == 1){ echo "active"; } ?>">
  	  <form action="options.php" method="post">
        <?php settings_fields('simple-instagram_options'); ?>
        <?php do_settings_sections('simple-instagram'); ?>
        <input name="Submit" type="submit" value="<?php esc_attr_e('Save Changes'); ?>" />
      </form>
    </div>
	</section>
	
	<section id="step03">
	  <div class="section_title">
      Step 03: Authorize Your Instagram Application
    </div>
    <div class="section_content <?php if($set == 1){ echo "active"; } ?>">
     <?php if($set == 1){ ?>
	   <iframe width="100%" src="<?php echo  get_site_url(); ?>/wp-admin/admin-ajax.php?action=register_instagram"></iframe>
	   <?php }else{ ?>
	     Please follow steps 1 and 2 first.
	   <?php } ?>
	  </div>
	</section>
	
	<?php if(get_option('si_oauth') && get_option('si_oauth') != ''){ ?>
		<section id="search">
			<div class="section_title">
				Lookup User ID
			</div>
			<div class="section_content active">
			    <p>In order to display the feed of another user, you'll need to know their user ID. Use the form below to search for a user by their username.</p>
			    <form>
			    	<label>Username:</label>
			    	<input type="text" name="user_name">
			    	<button class="search_user" value="Search">Search!</button>
			    	<div id="search_results"></div>
			    </form>
			</div>
		</section>
	<?php } ?>

</div>

