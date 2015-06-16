<?php

/**
 * Configurations code
 *
 * This class defines all code necessary for skins configuration
 * @author     ORION <support@orionorigin.com>
 */
class WVAS_Configuration {
    
        /**
         * Registers the config CPT
         */
        public function register_cpt_config() {

            $labels = array(
                'name' => __('Configurations', 'wvas-config'),
                'singular_name' => __('Template', 'wvas-config'),
                'add_new' => __('New config', 'wvas-config'),
                'add_new_item' => __('New config', 'wvas-config'),
                'edit_item' => __('Edit config', 'wvas-config'),
                'new_item' => __('New config', 'wvas-config'),
                'view_item' => __('View', 'wvas-config'),
                'search_items' => __('Search configs', 'wvas-config'),
                'not_found' => __('No config found', 'wvas-config'),
                'not_found_in_trash' => __('No config in the trash', 'wvas-config'),
                'menu_name' => __('Configurations', 'wvas-config'),
            );

            $args = array(
                'labels' => $labels,
                'hierarchical' => false,
                'description' => 'Configurations for the products customizer.',
                'supports' => array( 'title' ),
                'public' => true,
                'menu_icon' => 'dashicons-media-default',
                'show_ui' => true,
                'show_in_menu' => true,
                'show_in_nav_menus' => false,
                'publicly_queryable' => false,
                'exclude_from_search' => true,
                'has_archive' => false,
                'query_var' => false,
                'can_export' => true
            );

            register_post_type('wvas-config', $args);
        }
        
        function enqueue_scripts() {
            wp_enqueue_script( 'jquery_fontselect', plugin_dir_url( __FILE__ ) . 'js/jquery.fontselect.js', array( 'jquery' ));
        }
        
        function enqueue_styles () {
            wp_enqueue_style("fontselect", plugin_dir_url(__FILE__) . 'css/fontselect.css');
        }
        /**
         * Adds the base product column to the configs list
         * @param array $defaults Default columns
         * @return array
         */
//        public function get_configs_columns($defaults) {
//            $defaults['base_product'] =__('Base product','wvas');
//            return $defaults;
//        }
        
        /**
         * Returns the values for the columns in the configs list page
         * @param string $column_name Column key
         * @param type $id Template ID
         */
//        public function get_configs_columns_values($column_name, $id) {
//            if ($column_name === 'base_product') {
//
//                $base_product=  get_post_meta ($id,"base-product", true);
//                $pdt_name=  get_the_title($base_product);
//                $product=get_product($base_product);
//                if($product->product_type=="variation")
//                    $link=  get_edit_post_link($product->parent->id);
//                else
//                    $link= get_edit_post_link($base_product);
//                echo "<a href='$link'>$pdt_name</a>";
//            }
//        }
        
        /**
         * Adds the config design metabox on the config edition page
         */
        public function get_config_metabox() {

            $screens = array( 'wvas-config' );

            foreach ( $screens as $screen ) {

                    add_meta_box(
                            'wvas-config-box',
                            __( 'Configuration', 'wvas' ),
                            array($this, 'get_config_metabox_content'),
                            $screen
                    );
            }
        }
        
        /**
         * Builds the config design metabox content on the config edition page
         * @return type
         */
        public function get_config_metabox_content()
        {
//            $tmp_id=  get_the_ID();
//            if(isset($_GET["base-product"]))
//                $base_product=$_GET["base-product"];
//            else
//                $base_product=  get_post_meta ($tmp_id,"base-product", true);
//            if(empty($base_product))
//            {
//                echo __("No base product found.","wpd");
//                return;
//            }
            $skin_configuration_id = get_the_ID();
            $skin_configuration = get_post_meta($skin_configuration_id, 'wvas', TRUE);
            if(empty($skin_configuration)){
            	$skin_configuration = array();
            }
            //var_dump($skin_configuration);
            ob_start();
            ?>
            <div class="wrap">
                <div id="wvas-config-container">
                    <div class="configuration-container ">
                        <div>
                            <label>Skin</label>
                            <?php 
                                $skins_obj=new WVAS_Skin(false);
                                $skins=  $skins_obj->get_all();
                                $wvas_templates_list = array_keys($skins);
                                $skins_select_array = array();
                                foreach ($skins as $key => $value) {
                                    $skins_select_array[$key] = $value['Name'];
                                }
                                echo $this->build_html_dropdown('WVAS[skin]',
                                        '',
                                        '',
                                        $skins_select_array,
                                        (isset($skin_configuration['skin'])?$skin_configuration['skin']:'')
                                );
                            ?>

                        </div>
                    <div >
                    <div id="TabbedPanels1" class="TabbedPanels">

                        <ul class="TabbedPanelsTabGroup">
                            <li class="TabbedPanelsTab " tabindex="0"><?php _e('Container','wvas');?></li>
                            <li class="TabbedPanelsTab" tabindex="1"><?php _e('Attribute','wvas');?></li>
                        </ul>
                    <div class="TabbedPanelsContentGroup ">
                        <div class="TabbedPanelsContent">
                            <div id="TabbedPanels2" class="TabbedPanels">

                                <ul class="TabbedPanelsTabGroup">
                                    <li class="TabbedPanelsTab " tabindex="0"><?php _e('Normal','wvas');?></li>
                                    <li class="TabbedPanelsTab" tabindex="1"><?php _e('Hover','wvas');?></li>
                                </ul>
                                <div class="TabbedPanelsContentGroup ">
                                            <?php
                                            $normal_style=array();
                                            if(isset($skin_configuration['styles']['container']['normal']) && !empty($skin_configuration['styles']['container']['normal']))
                                                $normal_style=$skin_configuration['styles']['container']['normal'];
                                            $this->get_skin_configuration_form('container', 'normal', $normal_style);
                                            
                                            $hover_style=array();
                                            if(isset($skin_configuration['styles']['container']['on-hover']) && !empty($skin_configuration['styles']['container']['on-hover']))
                                                $hover_style=$skin_configuration['styles']['container']['on-hover'];
                                            $this->get_skin_configuration_form('container', 'on-hover',$hover_style);
                                                        
                                            ?>
                                </div>
                            </div>
                        </div>

                        <div class="TabbedPanelsContent">
                            <div id="TabbedPanels3" class="TabbedPanels">

                                <ul class="TabbedPanelsTabGroup">
                                    <li class="TabbedPanelsTab " tabindex="2"><?php _e('Normal','wvas');?></li>
                                    <li class="TabbedPanelsTab" tabindex="3"><?php _e('Hover','wvas');?></li>
                                    <li class="TabbedPanelsTab " tabindex="4"><?php _e('Selected','wvas');?></li>
                                </ul>
                                <div class="TabbedPanelsContentGroup ">
                                        <?php
                                            $normal_style2=array();
                                            if(isset($skin_configuration['styles']['attribute']['normal']) && !empty($skin_configuration['styles']['attribute']['normal']))
                                                $normal_style2=$skin_configuration['styles']['attribute']['normal'];
                                            $this->get_skin_configuration_form('attribute', 'normal', $normal_style2);
                                            
                                            $hover_style_2=array();
                                            if(isset($skin_configuration['styles']['attribute']['on-hover']) && !empty($skin_configuration['styles']['attribute']['on-hover']))
                                                $hover_style_2=$skin_configuration['styles']['attribute']['on-hover'];
                                            $this->get_skin_configuration_form('attribute', 'on-hover', $hover_style_2);
                                            
                                            $selected_style=array();
                                            if(isset($skin_configuration['styles']['attribute']['selected']) && !empty($skin_configuration['styles']['attribute']['selected']))
                                                $selected_style=$skin_configuration['styles']['attribute']['selected'];
                                            $this->get_skin_configuration_form('attribute', 'selected', $selected_style);
                                        ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>  
            
            </div>

        </div>
                </div>
            </div>

            <?php
            $output=  ob_get_contents();
            ob_end_clean();
            echo $output;

        }
        
       /**
        * Saves the config data
        * @param in $post_id Post ID
        */
       public function save_wvas_config($post_id) {
            if(isset($_POST["WVAS"])){
                update_post_meta ($post_id, "wvas", $_POST["WVAS"]);
            }
                
        }
        
        /**
         * 
         * @param type $element
         * @param type $element_state
         * @param type $values
         */
        private function get_skin_configuration_form($element, $element_state, $values) {
            $this->enqueue_scripts();
            $this->enqueue_styles();
            //var_dump($values['disable_background']);
            ?>
                <div class="TabbedPanelsContent">
                    <div class="configurate-template">
                        <div>
                            <?php _e('Background','wvas');?>
                            <div>
                                <input type="checkbox" class="enable-fields" name="WVAS[styles][<?php echo $element;?>][<?php echo $element_state;?>][enable_background]" <?php echo ((isset($values['enable_background']) && $values['enable_background']=='on')?' checked="checked" ':' '); ?> />
                                <label><?php _e('Enable','wvas');?></label>
                            </div>
                            
                        </div>
                        <div>
                            <div>
                                <span><?php _e('Color','wvas');?></span> 
                                <span>
                                    <input type="text" class="wvas-color-picker" name="WVAS[styles][<?php echo $element;?>][<?php echo $element_state;?>][background-color]" value="<?php echo (isset($values['background-color'])?$values['background-color']:'#ffffff') ?>"  >
                                </span>
                            </div>
                            <div>
                                <span><?php _e('Image','wvas');?></span> 
                                <span class="bg-image-set-container">
                                    <span class="thumbnail-container"><?php echo (isset($values['background-image'])?'<img src="'.wp_get_attachment_url($values['background-image']).'" alt="" />':'') ?></span>
                                    <input type="hidden" name="WVAS[styles][<?php echo $element;?>][<?php echo $element_state;?>][background-image]" value="<?php echo (isset($values['background-image'])?$values['background-image']:'') ?>"/>
                                    <button class="button set-bg-image-btn"  ><?php _e('Set','wvas');?></button>      
                                    <button class="button remove-image-bg-btn" ><?php _e('Remove','wvas');?></button>
                                </span>
                                </span>
                            </div>
                            <div>
                                <span><?php _e('Repeat','wvas');?></span> 
                                <span>
                                    <input type="checkbox" name="WVAS[styles][<?php echo $element;?>][<?php echo $element_state;?>][background-repeat]" value="repeat" <?php echo ((isset($values['background-repeat']) && $values['background-repeat']=='repeat')?'checked = checked':' ')?>  />
                                </span>
                            </div>
                            <div>
                                <span><?php _e('Position','wvas');?></span> 
                                <span>
                                        <?php 
                                            echo $this->build_html_dropdown('WVAS[styles]['.$element.']['.$element_state.'][background-position]',
                                                    '',
                                                    'wvas-select',
                                                    array(
                                                        "top left"=>__('Top Left','wvas'),
                                                        "top right"=>__('Top Right','wvas'),
                                                        "center"=>__('center','wvas'),
                                                        "bottom left"=>__('Bottom Left','wvas'),
                                                        "bottom right"=>__('Bottom Right','wvas'),
                                                    ),
                                                    (isset($values['background-position'])?$values['background-position']:'')
                                            );
                                        ?>
                                </span>
                            </div>
                            <div>
                                <span><?php _e('Size','wvas');?></span>
                                <span>
                                            <?php 
                                                echo $this->build_html_dropdown('WVAS[styles]['.$element.']['.$element_state.'][background-size]',
                                                                '',
                                                                'wvas-select',
                                                                array(
                                                                    "initial"=>__('Initial','wvas'),
                                                                    "contain"=>__('Contain','wvas'),
                                                                    "cover"=>__('Cover','wvas'),
                                                                ),
                                                                (isset($values['background-size'])?$values['background-size']:'')
                                                        );
                                            
                                            ?>
                                </span>
                            </div>
                        </div>

                    </div>
                    <div class="configurate-template">
                        <div>
                            <?php _e('Text','wvas');?>
                            <div>
                                <input type="checkbox" class="enable-fields" name="WVAS[styles][<?php echo $element;?>][<?php echo $element_state;?>][enable_text]" <?php echo ((isset($values['enable_text']) && $values['enable_text']=='on')?' checked="checked" ':' '); ?>/>
                                <label><?php _e('Enable','wvas');?></label>
                            </div>
                        </div>
                        <div>
                            <div>
                                <span><?php _e('Color','wvas');?></span>
                                <span>
                                <input type="text" name="WVAS[styles][<?php echo $element;?>][<?php echo $element_state;?>][color]" class="wvas-color-picker" value="<?php echo (isset($values['color'])?$values['color']:'#ffffff') ?>"/>
                                </span>
                            </div>
                            <div>
                                <span><?php _e('Alignment','wvas');?></span>
                                <span>
                                    <?php 
                                        echo $this->build_html_dropdown('WVAS[styles]['.$element.']['.$element_state.'][text-align]',
                                                '',
                                                'wvas-select',
                                                array(
                                                    "left"=>__('Left','wvas'),
                                                    "center"=>__('Center','wvas'),
                                                    "right"=>__('Right','wvas'),
                                                ),
                                                (isset($values['text-align'])?$values['text-align']:'')
                                        );
                                    ?>
                                </span>
                            </div>
                            <div>
                                <span><?php _e('Decoration','wvas');?></span>
                                <span>
                                    <?php 
                                        echo $this->build_html_dropdown('WVAS[styles]['.$element.']['.$element_state.'][text-decoration]',
                                                '',
                                                'wvas-select',
                                                array(
                                                    "none"=>__("None",'wvas'),
                                                    "underline"=>__('Underline','wvas'),
                                                    "line-through"=>__('Barred','wvas'),
                                                    "overline"=>__('Strikethrough','wvas'),
                                                ),
                                                (isset($values['text-decoration'])?$values['text-decoration']:'')
                                        );
                                    ?>
                                </span>
                            </div>
                            <div>
                                <span><?php _e('Transormation','wvas');?></span>
                                <span>
                                    <?php 
                                        echo $this->build_html_dropdown('WVAS[styles]['.$element.']['.$element_state.'][text-transform]',
                                                '',
                                                'wvas-select',
                                                array(
                                                    "none"=>__('None', 'wvas'),
                                                    "capitalize"=>__('Capitalize', 'wvas'),
                                                    "uppercase"=>__('Uppercase', 'wvas'),
                                                    "lowercase"=>__('Lowercase','wvas'),
                                                ),
                                                (isset($values['text-transform'])?$values['text-transform']:'')
                                        );
                                    ?>
                                </span>
                            </div>

                        </div>

                    </div>
                    <div class="configurate-template">
                        <div>
                            <?php _e('Font','wvas');?>
                            <div>
                                <input type="checkbox" class="enable-fields" name="WVAS[styles][<?php echo $element;?>][<?php echo $element_state;?>][enable_font]" <?php echo ((isset($values['enable_font']) && $values['enable_font']=='on')?' checked="checked" ':' '); ?> />
                                <label><?php _e('Enable','wvas');?></label>
                            </div>
                        </div>
                        <div>
                            <div>
                                <span><?php _e('Family','wvas');?></span> 
                                <span>
                                    <input id="" class="wvas-font-selector" type="text" name="<?php echo 'WVAS[styles]['.$element.']['.$element_state.'][font-family]';?>" value="<?php echo (isset($values['font-family'])?$values['font-family']:'');?>"/>
                                    
                                </span>
                            </div>
                            <div>
                                <span><?php _e('Style','wvas');?></span>
                                <span>
                                     <?php 
                                        echo $this->build_html_dropdown('WVAS[styles]['.$element.']['.$element_state.'][font-style]',
                                                '',
                                                'wvas-select',
                                                array(
                                                    "normal"=>__('Normal', 'wvas'),
                                                    "bold"=>__('Bold', 'wvas'),
                                                    "italic"=>__('Italic', 'wvas'),
                                                ),
                                                (isset($values['font-style'])?$values['font-style']:'')
                                        );
                                    ?>
                                </span>
                            </div>
                            <div>
                                <span><?php _e('Size (px)','wvas');?></span>
                                <span>
                                    <div class="noUiSlider horizontal" data-min="10" data-max="80" data-step="1" data-start="12"></div>
                                    <input type="text" name="WVAS[styles][<?php echo $element;?>][<?php echo $element_state;?>][font-size]" class="noUi-value-container"  value="<?php echo (isset($values['font-size'])?$values['font-size']:'') ?>" />
                                </span> 
                            </div>
                            <div>
                                <span><?php _e('Weight','wvas');?></span>
                                <span>
                                    <div class="noUiSlider horizontal" data-min="100" data-max="900" data-step="100" data-start="100"></div>
                                    <input type="text" name="WVAS[styles][<?php echo $element;?>][<?php echo $element_state;?>][font-weight]" class="noUi-value-container" value="<?php echo (isset($values['font-weight'])?$values['font-weight']:'') ?>" />
                                </span>
                            </div>
                        </div>

                    </div>
                    <div class="configurate-template">
                        <div>
                            <?php _e('Dimensions','wvas');?>
                            <div>
                                <input type="checkbox" class="enable-fields" name="WVAS[styles][<?php echo $element;?>][<?php echo $element_state;?>][enable_dimension]" <?php echo ((isset($values['enable_dimension']) && $values['enable_dimension']=='on')?' checked="checked" ':' '); ?>/>
                                <label><?php _e('Enable','wvas');?></label>
                            </div>
                        </div>
                        <div>
                            <div>
                                <span><?php _e('Width (px)','wvas');?></span> 
                                <span>
                                    <div class="noUiSlider horizontal" data-min="0" data-max="1000" data-step="100" data-start="0"></div>
                                    <input type="text" name="WVAS[styles][<?php echo $element;?>][<?php echo $element_state;?>][width]" value="<?php echo (isset($values['width'])?$values['width']:'') ?>" class="noUi-value-container" />
                                </span>
                            </div>
                            <div>
                                <span><?php _e('Height (px)','wvas');?></span>
                                <span>
                                    <div class="noUiSlider horizontal" data-min="0" data-max="100" data-step="10" data-start="0"></div>
                                    <input type="text" name="WVAS[styles][<?php echo $element;?>][<?php echo $element_state;?>][height]" value="<?php echo (isset($values['height'])?$values['height']:'') ?>" class="noUi-value-container" />
                                </span> 
                            </div>
                        </div>
                    </div>

                    <div class="configurate-template">
                        <div>
                            <?php _e('Spacing','wvas');?>
                            <div>
                                <input type="checkbox" class="enable-fields" name="WVAS[styles][<?php echo $element;?>][<?php echo $element_state;?>][enable_spacing]" <?php echo ((isset($values['enable_spacing']) && $values['enable_spacing']=='on')?' checked="checked" ':' '); ?>/>
                                <label><?php _e('Enable','wvas');?></label>
                </div>
                        </div>
                        <div>
							<div class="spacing-padding">
                                <span><?php _e('Padding (px)','wvas');?></span> 
                                <span>
                                    <input type="text" placeholder="Top" name="WVAS[styles][<?php echo $element;?>][<?php echo $element_state;?>][padding-top]" value="<?php echo (isset($values['padding-top'])?$values['padding-top']:'') ?>" class="spacing-top" />
                                    <input type="text" placeholder="Right" name="WVAS[styles][<?php echo $element;?>][<?php echo $element_state;?>][padding-right]" value="<?php echo (isset($values['padding-right'])?$values['padding-right']:'') ?>" class="spacing-right" />
                                    <input type="text" placeholder="Bottom" name="WVAS[styles][<?php echo $element;?>][<?php echo $element_state;?>][padding-bottom]" value="<?php echo (isset($values['padding-bottom'])?$values['padding-bottom']:'') ?>" class="spacing-bottom" />
                                    <input type="text" placeholder="Left" name="WVAS[styles][<?php echo $element;?>][<?php echo $element_state;?>][padding-left]" value="<?php echo (isset($values['padding-left'])?$values['padding-left']:'') ?>" class="spacing-left"/>
                                </span>
                            </div>
                                <div class="spacing-margin">
                                <span><?php _e('Margin (px)','wvas');?></span>
                                    <span>
                                    <input type="text" placeholder="Top" name="WVAS[styles][<?php echo $element;?>][<?php echo $element_state;?>][margin-top]" value="<?php echo (isset($values['margin-top'])?$values['margin-top']:'') ?>" class="spacing-top" />
                                    <input type="text" placeholder="Right" name="WVAS[styles][<?php echo $element;?>][<?php echo $element_state;?>][margin-right]" value="<?php echo (isset($values['margin-right'])?$values['margin-right']:'') ?>" class="spacing-right" />
                                    <input type="text" placeholder="Bottom" name="WVAS[styles][<?php echo $element;?>][<?php echo $element_state;?>][margin-bottom]" value="<?php echo (isset($values['margin-bottom'])?$values['margin-bottom']:'') ?>" class="spacing-bottom" />
                                    <input type="text" placeholder="Left" name="WVAS[styles][<?php echo $element;?>][<?php echo $element_state;?>][margin-left]" value="<?php echo (isset($values['margin-left'])?$values['margin-left']:'') ?>" class="spacing-left"/>
                                    </span> 
                            </div>
                                    <div class="spacing-border">
                                <span><?php _e('Border (px)','wvas');?></span>
                                        <span>
                                    <input type="text" placeholder="Top" name="WVAS[styles][<?php echo $element;?>][<?php echo $element_state;?>][border-top-width]" value="<?php echo (isset($values['border-top-width'])?$values['border-top-width']:'') ?>" class="spacing-top" />
                                    <input type="text" placeholder="Right" name="WVAS[styles][<?php echo $element;?>][<?php echo $element_state;?>][border-right-width]" value="<?php echo (isset($values['border-right-width'])?$values['border-right-width']:'') ?>" class="spacing-right" />
                                    <input type="text" placeholder="Bottom" name="WVAS[styles][<?php echo $element;?>][<?php echo $element_state;?>][border-bottom-width]" value="<?php echo (isset($values['border-bottom-width'])?$values['border-bottom-width']:'') ?>" class="spacing-bottom" />
                                    <input type="text" placeholder="Left" name="WVAS[styles][<?php echo $element;?>][<?php echo $element_state;?>][border-left-width]" value="<?php echo (isset($values['border-left-width'])?$values['border-left-width']:'') ?>" class="spacing-left"/>
                                        </span> 

                                            </div>

							<div>
                                    <label><?php _e('Border color','wvas');?></label>
                                <span>
                                    <input type="text" name="WVAS[styles][<?php echo $element;?>][<?php echo $element_state;?>][border-color]" class="wvas-color-picker" value="<?php echo (isset($values['border-color'])?$values['border-color']:'#000000') ?>"/>
								</span>
							</div>
							<div>
                                    <label><?php _e('Border style','wvas');?></label> 
                                    <span>
            <?php
                                        echo $this->build_html_dropdown('WVAS[styles]['.$element.']['.$element_state.'][border-style]',
                                                '',
                                                'wvas-select',
                                                array(
                                                    "solid"=>__('Solid', 'wvas'),
                                                    "dotted"=>__('Dotted', 'wvas'),
                                                    "dashed"=>__('Dashed', 'wvas'),
                                                    "none"=>__('None', 'wvas'),
                                                    "hidden"=>__('Hidden', 'wvas'),
                                                    "groove"=>__('Groove', 'wvas'),
                                                    "ridge"=>__('Ridge', 'wvas'),
                                                    "inset"=>__('Inset', 'wvas'),
                                                    "outset"=>__('Outset', 'wvas'),
                                                    "initial"=>__('Initial', 'wvas'),
                                                    "inherit"=>__('Inherit', 'wvas'),
                                                ),
                                                (isset($values['border-style'])?$values['border-style']:'')
                                        );
                                    ?>
                                    </span>
                            </div>
                            <div>
                                <label><?php _e('Border radius (px)','wvas');?></label>
                                    <span>
                                        <div class="noUiSlider horizontal" data-min="0" data-max="100" data-step="1" data-start="0"></div>
                                        <input type="text" name="WVAS[styles][<?php echo $element;?>][<?php echo $element_state;?>][border-radius]" value="<?php echo (isset($values['border-radius'])?$values['border-radius']:'') ?>" class="noUi-value-container" />
                                    </span>
                            </div>
                        </div>
                    </div>

                </div>

            <?php
        }
        
        private function build_html_dropdown($select_name, $select_id,  $select_class, $opt_list = array(), $selected_opt = '', $disabled='' ){
            ob_start();
            ?>
                <select name="<?php echo $select_name;?>" id="<?php echo $select_id;?>" class="<?php echo $select_class;?>" <?php echo $disabled; ?> >
                        <?php if (is_array($opt_list) && !empty($opt_list)){
                            foreach ($opt_list as $name => $label) {
                                if ($name == $selected_opt){
                                    ?> <option value="<?php echo $name?>"  selected="selected" > <?php echo $label; ?></option> <?php 
                                }else{
                                    ?> <option value="<?php echo $name?>"> <?php echo $label;?></option> <?php 
                                }
                            }

                        }?>
                </select>
            <?php 
            $html_select = ob_get_contents();
            ob_end_clean();
            return $html_select;
        }
}
