<?php
/**
 * Plumtree Promo Box Widget
 *
 * Configurable promo box widget.
 *
 * @author TransparentIdeas
 * @package Plum Tree
 * @subpackage Widgets
 * @since 0.01
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

add_action( 'widgets_init', create_function( '', 'register_widget( "pt_promo_widget" );' ) );

class pt_promo_widget extends WP_Widget {
	
	public function __construct() {
		parent::__construct(
	 		'pt_promo_widget', // Base ID
			__('PT Promo Box', 'plumtree'), // Name
			array( 'description' => __( 'Plum Tree special widget. Add promotion box to widget areas', 'plumtree' ), ) 
		);
		add_action('admin_enqueue_scripts', array($this, 'upload_scripts'));
        add_action('admin_enqueue_styles', array($this, 'upload_styles'));
	}

	public function upload_scripts() {
        if(function_exists( 'wp_enqueue_media' )){
            wp_enqueue_media();
        }else{
            wp_enqueue_style('thickbox');
            wp_enqueue_script('media-upload');
            wp_enqueue_script('thickbox');
        }
    }


    public function upload_styles() {
        wp_enqueue_style('thickbox');
    }

	public function form( $instance ) {

		$defaults = array( 
			'title' 			=> 'Promo Box #',
			'background_image'  => '',
			'promo_image'		=> '',
			'promo_text'		=> '',
			'promo_url'			=> '#',
			'position'			=> 'image-text',
		);

		$instance = wp_parse_args( (array) $instance, $defaults ); 
	?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'plumtree' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $instance['title']; ?>" />
		</p>

		<p>
            <label for="<?php echo $this->get_field_name( 'background_image' ); ?>"><?php _e( 'Background Image:', 'plumtree' ); ?></label>
            <input name="<?php echo $this->get_field_name( 'background_image' ); ?>" id="<?php echo $this->get_field_id( 'background_image' ); ?>" class="widefat" type="text" value="<?php echo $instance['background_image']; ?>" />
            <input id="<?php echo $this->get_field_id( 'background_image' ); ?>_button" style="display:inline-block; margin-top:5px;" class="upload_image_button button" type="button" value="Upload Image" />
            <script>
                jQuery(document).ready(function($){
                    var _custom_media = true, _orig_send_attachment = wp.media.editor.send.attachment;

                    $("#<?php echo $this->get_field_id( 'background_image' ); ?>_button").click(function(e){
                        e.preventDefault();
                        var send_attachment_bkp = wp.media.editor.send.attachment;
                        var button = $(this);
                        var id = button.attr("id").replace("_button", "");

                        _custom_media = true;

                        wp.media.editor.send.attachment = function(props, attachment){
                            if (_custom_media) {
                                $("#"+id).val(attachment.url);
                            } else {
                                return _orig_send_attachment.apply(this, [props, attachment]);
                            }
                        }

                        wp.media.editor.open(button);
                        return false;

                    });

                    $(".add_media").on("click", function(){
                        _custom_media = false;
                    });

                });
            </script>
        </p>

		<p>
            <label for="<?php echo $this->get_field_name( 'promo_image' ); ?>"><?php _e( 'Promo Image:', 'plumtree' ); ?></label>
            <input name="<?php echo $this->get_field_name( 'promo_image' ); ?>" id="<?php echo $this->get_field_id( 'promo_image' ); ?>" class="widefat" type="text" value="<?php echo esc_url( $instance['promo_image'] ); ?>" />
            <input id="<?php echo $this->get_field_id( 'promo_image' ); ?>_button" style="display:inline-block; margin-top:5px;" class="upload_image_button button" type="button" value="Upload Image" />

            <script>
                jQuery(document).ready(function($){
                    var _custom_media = true, _orig_send_attachment = wp.media.editor.send.attachment;

                    $("#<?php echo $this->get_field_id( 'promo_image' ); ?>_button").click(function(e){
                        e.preventDefault();
                        var send_attachment_bkp = wp.media.editor.send.attachment;
                        var button = $(this);
                        var id = button.attr("id").replace("_button", "");

                        _custom_media = true;

                        wp.media.editor.send.attachment = function(props, attachment){
                            if (_custom_media) {
                                $("#"+id).val(attachment.url);
                            } else {
                                return _orig_send_attachment.apply(this, [props, attachment]);
                            }
                        }

                        wp.media.editor.open(button);
                        return false;

                    });

                    $(".add_media").on("click", function(){
                        _custom_media = false;
                    });

                });
            </script>
        </p>

		<p>
			<label for="<?php echo $this->get_field_id ('promo_text'); ?>"><?php _e('Promo Text', 'plumtree'); ?></label>
			<textarea class="widefat" id="<?php echo $this->get_field_id('promo_text'); ?>" name="<?php echo $this->get_field_name('promo_text'); ?>" rows="2" cols="25"><?php echo $instance['promo_text']; ?></textarea>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'promo_url' ); ?>"><?php _e( 'Promo Button url:', 'plumtree' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'promo_btn_url' ); ?>" name="<?php echo $this->get_field_name( 'promo_btn_url' ); ?>" type="text" value="<?php echo $instance['promo_btn_url']; ?>" />
		</p>

		<p><?php _e('Select Promo elements position','plumtree'); ?></p><p>
		<?php
		$typeoptions = array (
							"image-text" => __("Image/Text",'plumtree'),
							"text-image" => __("Text/Image",'plumtree'),
		);
		foreach ($typeoptions as $val => $html) {
			$checked = '';
			$output = '<input type="radio" value="'.$val.'" id="'.$this->get_field_id('position').'-'.$val.'" name="'.$this->get_field_name('position').'" ';
			if($instance['position']==$val) { $checked = 'checked="checked"'; } 
			$output .= $checked.' class="radio" /><label for="'.$this->get_field_id('position').'-'.$val.'">'.$html.'</label><br />';
			echo $output;
		};
		?></p>

		<?php 
	}

	public function update( $new_instance, $old_instance ) {
		
		$instance = $old_instance;

		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['promo_text'] = stripslashes( $new_instance['promo_text'] );
		$instance['background_image'] = strip_tags( $new_instance['background_image'] );
		$instance['promo_image'] = strip_tags( $new_instance['promo_image'] );
		$instance['promo_url'] = strip_tags( $new_instance['promo_url'] );
		$instance['position'] = ( $new_instance['position'] );

		return $instance;
	}

	public function widget( $args, $instance ) {

		global $wpdb;

		extract( $args );

		$title = apply_filters( 'widget_title', $instance['title'] );
		$background_image = (isset($instance['background_image']) ? $instance['background_image'] : '' );
		$promo_image = (isset($instance['promo_image']) ? $instance['promo_image'] : '' );
		$promo_url = (isset($instance['promo_url']) ? $instance['promo_url'] : '' );
		$promo_text = (isset($instance['promo_text']) ? $instance['promo_text'] : '' );
		$position = (isset($instance['position']) ? $instance['position'] : '' );

		$output = '<div class="promo-container '.$position.'">';
		$output .= '<a href="'.$promo_url.'" target="_blank" title="'.__('Click to Learn More', 'plumtree').'">';
		
		if ( ! empty( $background_image ) ) 
			$output .= '<img class="promo-bg" alt="'.$title.'" src="'.$background_image.'">';
		if ( ! empty( $promo_image ) ) 
			$output .= '<div class="img-wrap"><img src="'.$promo_image.'" alt="'.$title.'" /></div>';
		if ( ! empty( $promo_text ) ) 
			$output .= '<div class="text-wrap">'.$promo_text.'</div>';

		$output .= '</a></div>';

		echo $before_widget;
		echo $output;
		echo $after_widget;
	}
}
