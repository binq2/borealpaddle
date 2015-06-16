<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class WVAS_Skin {
    public $name;
    public $file;
    public $data;
    public $default_headers=array(
                "Name"=>"Name",
                "Description"=>"Description",
                "Author"=>"Author",
                "Screenshot"=>"Screenshot",
                "Type"=>"Type"
            );
    
     function __construct($skin) {
         $skin = explode("|", $skin);
         if(!empty($skin[0]) && file_exists(WVA_SKINS_DIR ."/$skin[0]"))
         {
             //var_dump($skin);
            //"|" need to be escaped
            $this->name=$skin[0];
            $this->skin_config = (isset($skin[1]) && !empty($skin[1]))?$skin[1]:'' ;
            $configuration_post = get_post($this->skin_config);
            if($configuration_post){
                $this->skin_config_name = $configuration_post->post_name;
            }  else {
                $this->skin_config_name = '';
            }
            
            
            $this->file=WVA_SKINS_DIR."/$this->name";
            $this->data=get_file_data($this->file, $this->default_headers);         
         }
     }
     
     public function get_all()
     {
        $skins_files_pattern=WVA_SKINS_DIR."/*.php";
        $skins_arr=array();
        foreach (glob($skins_files_pattern) as $skin_file) {
            $skin=  get_file_data($skin_file, $this->default_headers);
            $tpl_key=basename($skin_file);
            $skins_arr[$tpl_key]= $skin;
        }

        return $skins_arr;
     }
     
     public function get_skinned_attributes($product, $attribute_name, $attribute_data)
     {
         $visual_attributes_meta = get_post_meta($product->id, "visual_attr", true);
         $serialized_attribute_name=  sanitize_title(strtolower($attribute_name));
         $attribute_metas = $visual_attributes_meta[$serialized_attribute_name];
         $variation_label = ucfirst($attribute_data["label"]);
         $default_attributes = $product->get_variation_default_attributes();
        require WVA_SKINS_DIR . "/".$this->name;
        $raw_tpl = $attribute_tpl;
        $skin_name = str_replace(".php", "", $this->name);
        $css = $skin_name . ".css";
        $js = $skin_name . ".js";
        if (file_exists(WVA_SKINS_DIR . "/css/$css"))
            wp_enqueue_style($skin_name . "-css", WVA_SKINS_URL . "/css/$css");

        if (file_exists(WVA_SKINS_DIR . "/js/$js"))
            wp_enqueue_script($skin_name . "-js", WVA_SKINS_URL . "/js/$js", array("jquery"));
        ?>
        <!--<select data-id="<?php // echo $attribute_name; ?>" class="wvas-variation-select">-->
        <?php
        $html_attributes_code = "";
        foreach ($attribute_data["values"] as $attribute_value) {
            //var_dump($attribute_metas[$attribute_value]);
            $sanitized_value = sanitize_title($attribute_value);
            $selected = "";
            if (isset($default_attributes[$attribute_name]) && $default_attributes[$attribute_name] == $sanitized_value)
            {
                if($this->data["Type"]=="Select")
                    $selected = "selected='selected'";
                else if($this->data["Type"]=="Radio")
                    $selected = "checked";
                else
                    $selected = "selected_wvas";
            }
//                var_dump($attribute_metas[$sanitized_value]);
            $attribute_tpl = str_replace("{attributes-id}", $serialized_attribute_name, $raw_tpl);
            $attribute_tpl = str_replace("{attribute-value}", $sanitized_value, $attribute_tpl);
            if(isset($attribute_metas[$sanitized_value]))
            {
                $attribute_tpl = str_replace("{attribute-color}", $attribute_metas[$sanitized_value], $attribute_tpl);
                if($this->data["Type"]=="Image")
                {
                    $image=  wp_get_attachment_url($attribute_metas[$sanitized_value]);
                    $attribute_tpl = str_replace("{attribute-image}", $image, $attribute_tpl);
                    
                }
            }
            $attribute_tpl = str_replace("{attribute-selected}", $selected, $attribute_tpl);
            $attribute_tpl = str_replace("{attribute-label}", $attribute_value, $attribute_tpl);

            $html_attributes_code.=$attribute_tpl;
        }
        $html_code = str_replace("{attributes}", $html_attributes_code, $global_tpl);
        $html_code = str_replace("{variation-label}", $variation_label, $html_code);
        $html_code = str_replace("{variation-container-class}", $this->skin_config_name, $html_code);
        
        //var_dump($html_attributes_code);
        
        return $html_code;
     }
     
    /**
     * get a skin configuration styles for single product page
     * @return type
     */
    public function get_skin_configuration_styles() {
        $skin_configuration_styles = "";
        if ($this->skin_config != 'default'){
            $confi_data = get_post_meta($this->skin_config, 'wvas', true);

            if(isset($confi_data['skin']) && $confi_data['skin'] == $this->name){
                $skin_name = str_replace(".php", "", $this->name);
                $skin_configuration_styles = '<style type="text/css">';
                
                //Container styles
                if (isset($confi_data['styles']['container'] )){
                    //normal
                    $skin_configuration_styles .= $this->extract_css_styles_from_array('.'.$skin_name.'-container.'.$this->skin_config_name , $confi_data['styles']['container']['normal'] );
                    //Hover
                    $skin_configuration_styles .= $this->extract_css_styles_from_array('.'.$skin_name.'-container.'.$this->skin_config_name.':hover' , $confi_data['styles']['container']['on-hover'] );
                }
                
                if (isset($confi_data['styles']['attribute'] )){
                    //normal
                    $skin_configuration_styles .= $this->extract_css_styles_from_array('.'.$this->skin_config_name.' .'.$skin_name.'-child'.'' , $confi_data['styles']['attribute']['normal'] );
                    //Hover
                    $skin_configuration_styles .= $this->extract_css_styles_from_array('.'.$this->skin_config_name.' .'.$skin_name.'-child'.':hover' , $confi_data['styles']['attribute']['on-hover'] );
                    //selected
                    $skin_configuration_styles .= $this->extract_css_styles_from_array('.'.$this->skin_config_name.' .'.$skin_name.'-child'.'.selected_wvas' , $confi_data['styles']['attribute']['selected'] );
                }
                
                $skin_configuration_styles .= '</style>';
            }
        }
        return $skin_configuration_styles;
    }
    
    /**
     * Turn an array of css property into css styles
     * @param str $selector
     * @param array $styles_array
     * @return string
     */
    function extract_css_styles_from_array($selector, $styles_array) {
        $property_in_px = array(
            'font-size','width','height', 
            "margin-top", "margin-right", "margin-bottom", "margin-left", 
            "border-top-width", "border-right-width", "border-bottom-width", "border-left-width", 
            "padding-top", "padding-right", "padding-bottom", "padding-left",
            "border-radius"
            );
        $background_property = array("background-color", "background-image", "background-position", "background-size", "background-repeat");
        $text_property = array("color", "text-align", "text-decoration", "text-transform");
        $font_property = array("font-family", "font-style", "font-size", "font-weight");
        $dimension_property = array("width","height");
        $spacing_property = array( 
            "margin-top", "margin-right", "margin-bottom", "margin-left", 
            "border-top-width", "border-right-width", "border-bottom-width", "border-left-width", 
            "padding-top", "padding-right", "padding-bottom", "padding-left",
            "border-radius", "border-color", "border-style"
        );
        $enabled_property = array();
        if(isset($styles_array["enable_background"]) && $styles_array["enable_background"]=='on'){
            if(!isset($styles_array['background-repeat'])){
                $styles_array['background-repeat'] = 'no-repeat';
            }
            $enabled_property = array_merge($enabled_property, $background_property);
        }
        if(isset($styles_array["enable_text"]) && $styles_array["enable_text"]=='on'){
            $enabled_property = array_merge($enabled_property, $text_property);
        }
        if(isset($styles_array["enable_font"]) && $styles_array["enable_font"]=='on'){
            //Enqueue font
            if (isset($styles_array["font-family"]) && !empty($styles_array["font-family"])){
                wp_enqueue_style($styles_array["font-family"] , "http://fonts.googleapis.com/css?family=".$styles_array["font-family"]);
            }
            $enabled_property = array_merge($enabled_property, $font_property);
        }
        if(isset($styles_array["enable_dimension"]) && $styles_array["enable_dimension"]=='on'){
            $enabled_property = array_merge($enabled_property, $dimension_property);
        }
        if(isset($styles_array["enable_spacing"]) && $styles_array["enable_spacing"]=='on'){
            $enabled_property = array_merge($enabled_property, $spacing_property);
        }
        
        
        
        
        $str_styles = $selector.'{';
        foreach ($styles_array as $styles_property => $styles_values) {
            if(!empty($styles_property) 
                    && !empty($styles_values) 
                    && in_array($styles_property, $enabled_property)){
                if($styles_property == 'font-family'){
                    $styles_values = str_replace('+', ' ', $styles_values);
                }
                
                if($styles_property == 'background-image'){
                    $str_styles .= $styles_property.':url('.wp_get_attachment_url($styles_values).')';
                }  else {
                    $str_styles .= $styles_property.':'.$styles_values;
                }
                
                
                if(in_array($styles_property, $property_in_px)){
                    $str_styles .= 'px';
                }
                $str_styles .= ';';
            }
        }
        $str_styles .= '}';
        
        return $str_styles;
    }

}
?>
