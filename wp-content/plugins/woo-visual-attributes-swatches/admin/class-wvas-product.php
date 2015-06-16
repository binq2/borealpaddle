<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class Wvas_Product {
     
        private function update_global_attr_options(){
            global $wpdb;
            if ( isset( $_POST['visual_attr'] ) && !empty($_POST['visual_attr']) ){
                $visual_attr = $_POST['visual_attr'];
                foreach ($visual_attr as $key=>$new_attribute_meta)
                {
                    if(isset($_POST["wvas-selections"][$key]))
                    {
                        $products_with_these_attributes = $wpdb->get_col("select distinct post_id from $wpdb->postmeta where meta_key='_product_attributes' and meta_value like '%$key%'");
                        foreach ($products_with_these_attributes as $i=>$product_id)
                        {
                            $old_metas=  get_post_meta($product_id, "visual_attr", true);
                            $new_metas=$old_metas;
                            $new_metas[$key]=$new_attribute_meta;
                            update_post_meta($product_id, 'visual_attr',$new_metas);
                        }
                        
//                        $args=array(
//                            "post_type"=>"product",
//                            "meta_key"=>"_product_attributes",
//                            "meta_value" => $key,
//                            "meta_compare"=> "LIKE"
//                        );
//                        $products_with_these_attributes=  get_posts($args);
//                        var_dump($products_with_these_attributes);
                    }
                }
                ?>
                <div id="message" class="updated below-h2"><p><?php echo __("Saved","wvas");?></p></div>
                <?php
            }
    }
    
     private function get_variations_attr(){
        global $wpdb;
        $serialized_shop_attributes = $wpdb->get_results("select  distinct meta_value from $wpdb->postmeta where meta_key='_product_attributes'");
        $shop_attributes = array();
        foreach ($serialized_shop_attributes as $value) {
            $meta_value = unserialize($value->meta_value);
            foreach ($meta_value as $key => $value) {
                $attr_values = array();
                if($value['is_taxonomy']==1){
                    $terms = get_terms($key);
                    foreach ( $terms as $term ) {
                        $attr_values[] = array(
                            'val_slug' => $term->slug,
                            'val_label' => $term->name,
                        );
                    }
                }else{
                    $attr_values_extracted = explode('|', $value['value']);
                    foreach ( $attr_values_extracted as $attr_value ) {
                        $attr_values[] = array(
                            'val_slug' => sanitize_title($attr_value),
                            'val_label' => trim($attr_value),
                        );
                    }
                }
                foreach ($attr_values as $attr_value) {
                    if(!isset($shop_attributes[$key]))
                        $shop_attributes[$key] = array();
                    if(!in_array($attr_value, $shop_attributes[$key])){
                        $shop_attributes[$key][] = $attr_value;
                    }
                }
            }
        }    
        return $shop_attributes;
    }
            
            
     function get_wvas_editor_tab_label()
     {
        ?>
            <li class="wvas_editor_tab"><a href="#wvas_editor_tab"><?php _e( 'Visual attributes', 'wvas' ); ?></a></li>
        <?php
    }
    
     function get_wvas_editor_tab()
     {
        ?>
            <div id="wvas_editor_tab" class="panel woocommerce_options_panel"></div>
        <?php     
    }
    
    function get_skins_configurations() {
        $skins_configurations = array();
        $args = array(
            'post_type'        => 'wvas-config',
            'post_status'      => 'publish',
        );
        $wvas_configs = get_posts( $args );
        
        foreach ($wvas_configs as $wvas_config) {
            $wvas_config_data = get_post_meta($wvas_config->ID, 'wvas', true);
            if(isset($wvas_config_data['skin'])){
                if(!isset($skins_configurations[$wvas_config_data['skin']])){ 
                    $skins_configurations[$wvas_config_data['skin']]=array();
                }
                $skins_configurations[$wvas_config_data['skin']][$wvas_config->ID] = $wvas_config->post_title;
            }
        }
        
        return $skins_configurations;
    }
     private function get_wvas_editor_tab_content($product_id, $attributes) {
        if (is_array($attributes) && !empty($attributes)){
            if($product_id){
                $product=get_product($product_id);
                $visual_attr = ($product)? get_post_meta($product->id, 'visual_attr', true): array();    
            }else{
                $visual_attr = get_option('visual_attr');
            }
            $skins_obj=new WVAS_Skin(false);
            $skins=  $skins_obj->get_all();
            $wvas_templates_list = array_keys($skins);
            $skins_configurations = $this->get_skins_configurations();
            
            ?>
            <table class="wp-list-table widefat fixed wvas_table">
                <thead>
                    <tr>
                        <?php
                        if(!$product_id)
                        {
                            ?>
                        <th scope="col" id="cb" class="manage-column column-cb check-column" style=""><label class="screen-reader-text" for="cb-select-all-1">Select All</label><input id="cb-select-all-1" type="checkbox"></th>
                            <?php
                        }
                        ?>
                        <th><?php _e( 'Names', 'wvas' ); ?></th>
                        <th style="width: 50%;"><?php _e( 'Attributes', 'wvas' ); ?></th>
                        <th><?php _e( 'Skin', 'wvas' ); ?></th>
                    </tr>
                </thead>
            <?php
            foreach ($attributes as $attribute_name => $attribute_values){
                if(!empty($attribute_values)){
                    $visual_attr_type = '';
                    $attribute_name_sanitized = sanitize_title($attribute_name);
                    if($visual_attr && isset($visual_attr[$attribute_name_sanitized]['visual_attr_tpl'])){
                        $skin_file = explode("|", $visual_attr[$attribute_name_sanitized]['visual_attr_tpl']);
                        if(!empty($skin_file[0]) && file_exists(WVA_SKINS_DIR ."/".$skin_file[0])){
                            $skin = new WVAS_Skin($visual_attr[$attribute_name_sanitized]['visual_attr_tpl']);
                            $visual_attr_type=$skin->data["Type"];
                        }
                        
                    }
            ?>
                    <tr>
                        <?php
                        if(!$product_id)
                        {
                            ?>
                            <th scope="row" class="check-column">
                                <input type="checkbox" name="<?php echo 'wvas-selections['.$attribute_name_sanitized.'][active]';?>" value="1">
                            </th>
                            <?php
                        }
                        ?>
                        
                        <td> <strong><?php echo $key = wc_attribute_label( $attribute_name );//ucfirst(str_replace('pa_', '', $attribute_name)); ?></strong></td>
                        <td class="wvas_editor column-format">
                            <table>
                                <?php 
                                if (is_array($attribute_values) && !empty($attribute_values[0])){
                                    foreach ($attribute_values as $value) {
                                        $sanitize_value = $value['val_slug'];//esc_attr( sanitize_title( $value ) );
                                        echo '<tr data-field_name = "visual_attr['.$attribute_name_sanitized.']['.$sanitize_value.']" > <td> <strong>'.$value['val_label'].': </strong></td> <td class = "visual_attr_selector">';
                                        if ($visual_attr_type == "Color" ){
                                            if(isset($visual_attr[$attribute_name_sanitized][$sanitize_value])){
                                                echo '<input type="text" class="wvas-color-picker" name = "visual_attr['.$attribute_name_sanitized.']['.$sanitize_value.']" value ="'.$visual_attr[$attribute_name_sanitized][$sanitize_value].'" />';
                                            }  else {
                                                echo '<input type="text" class="wvas-color-picker" name = "visual_attr['.$attribute_name_sanitized.']['.$sanitize_value.']" value ="#ffffff" />';
                                            }
                                        }
                                        elseif ($visual_attr_type == "Image") {
                                            if (isset($visual_attr[$attribute_name_sanitized][$sanitize_value]) && wp_get_attachment_url( $visual_attr[$attribute_name_sanitized][$sanitize_value])){
                                                echo '<input type="hidden" name="visual_attr['.$attribute_name_sanitized.']['.$sanitize_value.']" value="'.$visual_attr[$attribute_name_sanitized][$sanitize_value].'"><input type="button" class="button wvas_set_icon" value="'.__('Set','wvas').'" style="display: none;"><span><img class = "wvas_icon" width = "90px" src="'.wp_get_attachment_url( $visual_attr[$attribute_name_sanitized][$sanitize_value]).'" alt="icon" /><input type="button" class="button wvas_remove_icon" value="'.__('Remove','wvas').'"></span>';
                                            }  else {
                                                 echo '<input type="hidden" name="visual_attr['.$attribute_name_sanitized.']['.$sanitize_value.']" ><input type="button" class="button wvas_set_icon" value="'.__('Set','wvas').'"><br />' ;
                                            }
                                        }else{
//                                            echo'none';
//                                            echo '<input type="hidden" name="visual_attr['.$attribute_name_sanitized.']['.$sanitize_value.']" value = "default">';
                                        }
                                        echo '</td></tr>';
                                    }
                                }else{
                                    echo'<tr><td>'.__('No attribute values found.','wvas').' </td></tr>';
                                }
                                ?>
                            </table>
                        </td>
                        <td>
                            <select name="visual_attr[<?php echo $attribute_name_sanitized;?>][visual_attr_tpl]" class="wvas-tpl-selector" >
                                <?php
                                    foreach ($wvas_templates_list as $tpl) {
                                        $value=$tpl;
                                        $label=$skins[$value]["Name"];
                                        $selected_config = (isset($visual_attr[$attribute_name_sanitized]['visual_attr_tpl']) && !empty($visual_attr[$attribute_name_sanitized]['visual_attr_tpl']))? $visual_attr[$attribute_name_sanitized]['visual_attr_tpl'] :'';
                                        $default_selected = ($selected_config == $value.'|default')?'selected = "selected"':'';
                                        echo '<optgroup label = "'.$label.'">';
                                        echo '<option value="'.$value.'|default" '.$default_selected.'>Default</option>';
                                        if(isset($skins_configurations[$value]) && !empty($skins_configurations[$value])){
                                            foreach ($skins_configurations[$value] as $config_id => $config_label) {
                                                $selected = ($selected_config == ($value.'|'.$config_id))?'selected = "selected"':'';
                                                echo '<option value="'.$value.'|'.$config_id.'" '.$selected.' .>'.$config_label.'</option>';
                                            }
                                        }
                                        echo '</optgroup>';
                                    }
                                ?>
                            </select>
                        </td>
                    </tr>                
                <?php 
                }
            }
            ?>
            </table>
            <?php
            $img_span='<span><img class = "wvas_icon" src="img_url" alt="photo" /><input type="button" class="button wvas_remove_icon" value="'.__('Remove','wvas').'"></span>';
            $img_selector = '<input type="button" class="button wvas_set_icon" value="'.__('Set','wvas').'">';
            $table_data = array(
                'color_selector' => "<input type='text' class='wvas-color-picker' name ='field_name' value='#ffffff' />",
                "img_span" => $img_span,
                "img_selector" => $img_selector
                );
            ?>
            <script type="text/javascript">
            var table_data = <?php echo json_encode($table_data) ; ?>;
            var wvas_templates=<?php echo json_encode($skins);?>;
            </script> 
            <?php
        }
    }
    
     function get_wvas_editor_tab_content_ajx() {
        $product_id = (isset($_POST['product_id'])) ? $_POST['product_id'] : '';
        $attributes_extracted = (isset($_POST['attributes_arr'])) ? $_POST['attributes_arr'] : array();
        $attributes_arr = array();
        foreach ($attributes_extracted as $attributes_name => $attributes_values) {
            $terms = get_terms($attributes_name);
            if (!is_wp_error($terms)){
                foreach ( $terms as $term ) {
                    $attributes_arr[$attributes_name][] = array(
                        'val_slug' => $term->slug,
                        'val_label' => $term->name,
                    );
                }
            }else{
                foreach ($attributes_values as $attr_value) {
                    $attributes_arr[$attributes_name][]= array(
                        'val_slug' => sanitize_title($attr_value),
                        'val_label' => trim($attr_value),
                    );
                }
            }

        }
        
        $checked="";
        $is_active=  get_post_meta($product_id, "wvas-activate", true);
        if($is_active)
            $checked="checked='checked'";
        ?>
            <label style="margin: 10px; width: auto;">
                <input type="checkbox" name='wvas-activate' style="margin-right: 10px;" <?php echo $checked;?> value="1"> <?php _e("Enable visual attributes", "wvas");?>
            </label>
        <?php
         $this->get_wvas_editor_tab_content($product_id, $attributes_arr);
        die();
    }
    
    function wvas_menu() {
    //add_menu_page('', 'VA bulk definitions', 'manage_product_terms', 'wvas-bulk-attributes-definition', 'wvas_get_global_attr_page');
        add_submenu_page( 'woocommerce', __( 'VA bulk definitions', 'woocommerce' ),  __( 'VA bulk definitions', 'woocommerce' ) , 'manage_woocommerce', 'wvas-bulk-attributes-definition', array($this, 'wvas_get_global_attr_page') );
    }
    
    function wvas_get_global_attr_page() {
        $product_id = '';
        $attributes = $this->get_variations_attr();
         if(isset($_POST))
                $this->update_global_attr_options();
        ?>
            <div class="wrap">
                <?php _e('<h2>Visual attributes bulk definition</h2>','wvas');?>
                <form method="POST">
                        <?php 
                         $this->get_wvas_editor_tab_content(FALSE, $attributes); 
                        ?>
                    <input type="submit" value="<?php _e("Apply","wvas");?>" class="button button-primary button-large mg-top-10">
                </form>
            </div>
        <?php                     
    }
    
    function save_visual_variation( $product_id ) {
        // because save_post can be triggered at other times
        if ( isset( $_POST['visual_attr'] ) && !empty($_POST['visual_attr']) ){
            $visual_attr = $_POST['visual_attr'];
            update_post_meta($product_id, 'visual_attr', $visual_attr);
        }
        
        if(isset($_POST["wvas-activate"]))
            update_post_meta($product_id, 'wvas-activate', $_POST["wvas-activate"]);
        else
            delete_post_meta ($product_id, 'wvas-activate');
    }
    

}
?>
