<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://orionorigin.com
 * @since      1.0.0
 *
 * @package    Wvas
 * @subpackage Wvas/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Wvas
 * @subpackage Wvas/public
 * @author     ORION <freelance@orionorigin.com>
 */
class Wvas_Public {

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string    $plugin_name       The name of the plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct($plugin_name, $version) {

        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    /**
     * Register the stylesheets for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_styles() {

        
        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/wvas-public.css', array(), $this->version, 'all');
    }

    /**
     * Register the stylesheets for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts() {

        /**
         * 
         */
        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/wvas-public.js', array('jquery'), $this->version, false);
    }

//    private function get_visual_attr($option, $visual_attr_type, $visual_attr_name, $visual_attr_val) {
//        $output = '';
//        if ($visual_attr_type == "color") {
//            $output .= '<span title= "' . $visual_attr_name[0] . '" data-select_name="attribute_' . sanitize_title($option) . '" data-attr_val = "' . $visual_attr_name[1] . '" class = "wvas_item wvas_color" style="background-color: ' . $visual_attr_val . '"></span>';
//        } elseif ($visual_attr_type == "img") {
//            if (wp_get_attachment_url($visual_attr_val)) {
//                $output .= '<span title= "' . $visual_attr_name[0] . '" data-select_name="attribute_' . sanitize_title($option) . '" data-attr_val = "' . $visual_attr_name[1] . '" class = "wvas_item wvas_img" ><img src="' . wp_get_attachment_url($visual_attr_val) . '" alt="icon" /></span>';
//            } else {
//                $output .= '<span title= "' . $visual_attr_name[0] . '" data-select_name="attribute_' . sanitize_title($option) . '" data-attr_val = "' . $visual_attr_name[1] . '" class = "wvas_item wvas_default">' . $visual_attr_name . '</span>';
//            }
//        } else {
//            $output .= '<span title= "' . $visual_attr_name[0] . '" data-select_name="attribute_' . sanitize_title($option) . '" data-attr_val = "' . $visual_attr_name[1] . '" class = "wvas_item wvas_default">' . $visual_attr_name . '</span>';
//        }
//        return $output;
//    }
    
    function extract_usable_attributes($product)
    {
        $attributes = $product->get_attributes();
        $usable_attributes = array();
        foreach ($attributes as $attribute) {
            if ($attribute["is_visible"] && $attribute["is_variation"]) {
                if ($attribute["is_taxonomy"]) {
                    $values = wc_get_product_terms($product->id, $attribute['name'], array('fields' => 'names'));
                    $taxonomy = get_taxonomy($attribute["name"]);
                    $usable_attributes[$attribute["name"]] = array("label" => $taxonomy->labels->name, "values" => $values); //$values;
//                        var_dump($values);
                } else {
                    // Convert pipes to commas and display values
                    $values = array_map('trim', explode(WC_DELIMITER, $attribute['value']));
                    $usable_attributes[$attribute["name"]] = array("label" => $attribute["name"], "values" => $values);
                }
            }
        }
        
        return $usable_attributes;
    }

    function display_visual_attr() {
        $product_id = get_the_ID();
        $is_active=  get_post_meta($product_id, "wvas-activate", true);
        if(!$is_active)
            return;
        wp_enqueue_script('wc-add-to-cart-variation');
        
        $product = wc_get_product($product_id);
//        $default_attributes = $product->get_variation_default_attributes();
//            var_dump($default_attributes);
//        $available_variations = $product->get_available_variations();
//            var_dump($attributes);
        

        
        $usable_attributes=  $this->extract_usable_attributes($product);
        $visual_attributes_meta = get_post_meta($product->id, "visual_attr", true);
        foreach ($usable_attributes as $attribute_name => $attribute_data) {
            $visual_attributes_meta = get_post_meta($product->id, "visual_attr", true);
            $serialized_attribute_name=  sanitize_title(strtolower($attribute_name));
            $attribute_metas = $visual_attributes_meta[$serialized_attribute_name];
            $skin = $attribute_metas["visual_attr_tpl"];
            $skin_file = explode("|", $skin);
            if(!empty($skin_file[0]) && file_exists(WVA_SKINS_DIR ."/".$skin_file[0])){
                $skin_object=new WVAS_Skin($skin);
                //var_dump($skin_object);
                $html_code=$skin_object->get_skinned_attributes($product, $attribute_name, $attribute_data);
                $html_code .= ' <style type="text/css"> .single-product table.variations{  display: none !important; } </style>';
                $skin_configuration_styles = $skin_object->get_skin_configuration_styles();
                echo $skin_configuration_styles;

                echo $html_code;
            }
            
        }

        return;
    }

}
