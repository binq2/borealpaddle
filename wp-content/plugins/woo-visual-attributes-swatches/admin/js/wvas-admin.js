(function( $ ) {
	'use strict';

	/**
	 * All of the code for your admin-specific JavaScript source
	 * should reside in this file.
	 *
	 * Note that this assume you're going to use jQuery, so it prepares
	 * the $ function reference to be used within the scope of this
	 * function.
	 *
	 * From here, you're able to define handlers for when the DOM is
	 * ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * Or when the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and so on.
	 *
	 * Remember that ideally, we should not attach any more than a single DOM-ready or window-load handler
	 * for any particular page. Though other scripts in WordPress core, other plugins, and other themes may
	 * be doing this, we should try to minimize doing that in our own work.
	 */
        $(document).ready(function () {
            
          function load_color_picker(){        
              $('.wvas-color-picker').each(function(index,element)
              {
                  var e=$(this);
                  var initial_color=e.val();       
                   e.css("border-left", "50px solid "+initial_color);
                   $(this).ColorPicker({
                           color: initial_color,
                           onShow: function (colpkr) {
                                   $(colpkr).fadeIn(500);
                                   return false;
                           },            
                           onChange: function (hsb, hex, rgb) {
                               e.css("border-left", "50px solid #"+hex);
                               e.val("#"+hex);
                           }
                       });
               });
          }
          
            $(".TabbedPanels").each(function()
            {
              var id=$(this).attr("id");
              new Spry.Widget.TabbedPanels(id);
            });

          function load_noUiSlider(){
             $(".noUiSlider").each(function()
             {
                 $(this).noUiSlider({
                    start: $(this).data('start'),
                    animate: false,
                    step: $(this).data('step'),
                    range: {
                            min: $(this).data('min'),
                            max: $(this).data('max')
                    }
                 });
                 $(this).on('set', function(){
                     $(this).parent().find('input').val($(this).val());
                 });        
            });
            
            $(".noUiSlider").parent().find('input').change(function(){
                    $(this).parent().find(".noUiSlider").val($(this).val());
            });
            
            $('.noUi-value-container').trigger('change');
            
        }

            function init_color_selector(wvas_color_picker){
                wvas_color_picker.ColorPicker({
                    onChange: function (hsb, hex, rgb) {
                        wvas_color_picker.val('#' + hex);
                        wvas_color_picker.css("border-left", "50px solid #"+hex);
                    }
                });
            }
            load_color_picker();
            load_noUiSlider();
            if($('#wvas_editor_tab').length > 0){
                get_visual_attr();
            }else if ($('.wvas-color-picker').length > 0){
                $(".wvas-color-picker").each(function(key, value){
                    init_color_selector($(this));
                    $(this).css('backgroundColor', this.value);
                });
            }
            
            $(document).on('change', '.wvas-tpl-selector', function(){
                var selected_template=$(this).val().split('|')[0];
                var selectors = $(this).parent().parent().find('.visual_attr_selector');
                console.log(selected_template);
                var type=wvas_templates[selected_template]["Type"];
                if(type==='Color'){
                    selectors.each(function(){
                        $(this).html(table_data.color_selector.replace(/field_name/gi, $(this).parents().data('field_name')));
                        var wvas_color_picker = $(this).find('.wvas-color-picker');
                        wvas_color_picker.each(function(key, value){
                            init_color_selector($(this));
                            $(this).css('backgroundColor', this.value);
                        })
                    });  
                }else if(type==='Image'){
                    selectors.each(function(){
                        $(this).html(table_data.img_selector);
                        $(this).append('<input type="hidden" name= '+$(this).parents().data('field_name')+'>');   
                    });
                }else{
                    selectors.html("");
                }
            });
            //trigger visual attribute type selector 
//            $(document).on('change', '.wvas_type_selector', function(){
//                var selectors = $(this).parent().parent().find('.visual_attr_selector');
//                if($(this).val()==='color'){
//                    selectors.each(function(){
//                        $(this).html(table_data.color_selector.replace(/field_name/gi, $(this).parents().data('field_name')));
//                        var wvas_color_picker = $(this).find('.wvas-color-picker');
//                        wvas_color_picker.each(function(key, value){
//                            init_color_selector($(this));
//                            $(this).css('backgroundColor', this.value);
//                        })
//                    });  
//                }else if($(this).val()==='img'){
//                    selectors.each(function(){
//                        $(this).html(table_data.img_selector);
//                        $(this).append('<input type="hidden" name= '+$(this).parents().data('field_name')+'>');   
//                    });
//                }else{
//                    selectors.html(ajax_object.none_va_txt);
//                }
//            });
    
            $(document).on('click', ".wvas_remove_icon",function() {
                var visual_attr_selector = $(this).parents('.visual_attr_selector')
                visual_attr_selector.find( ".wvas_set_icon" ).css('display','block');
                visual_attr_selector.children( "input[type = 'hidden']" ).attr( 'value', '' );
                $('span', visual_attr_selector).remove();
            });
            
            $(document).on("click",".wvas_set_icon",function(e){
                e.preventDefault();
                var selector = $(this).attr('data-selector');
                var trigger = $(this).parent();
                var uploader=wp.media({
                    title:'Add event icon',
                    button:{
                             text:"Add image"
                             },
                     multiple:false
                })
                .on('select',function(){
                     var selection=uploader.state().get('selection');
                     selection.map(
                            function(attachment){
                                    trigger.append(table_data.img_span.replace(/img_url/gi, attachment.attributes.url));
                                    trigger.children( "input[type = 'hidden']" ).attr( 'value', attachment.id );
                                    trigger.children( ".wvas_set_icon" ).hide();                                
                            }
                     );
                 })
                 .open();
            });
    
            $('#wvas_editor_tab').on('show', function() {
              get_visual_attr();                
            });
            
            function get_visual_attr(){
                var post_id = $("#post_ID").val();
                var attributes_arr = new Object();
                $.each($(".woocommerce_attribute_data"),function(){
                    var attr_name = $(this).find("[name^='attribute_names']").val();
                    if ($(this).find("[name^='attribute_values']").is("textarea"))
                      var attr_values = $(this).find("[name^='attribute_values']").val().split("|"); 
                    else
                       var attr_values = $(this).find("[name^='attribute_values']").val();
                    
                    if (attr_values)
                        attributes_arr[attr_name] = attr_values;
                });

                $.post( 
                        ajaxurl,
                        {
                              action: "get_wvas_editor_content",
                              product_id:post_id,
                              attributes_arr : attributes_arr
                        },
                        function(data) {
                              $("#wvas_editor_tab").html(data);
                              var wvas_color_picker = $("#wvas_editor_tab .wvas-color-picker");
                              wvas_color_picker.each(function(key, value){
                              init_color_selector($(this));
                              $(this).css('backgroundColor', this.value);
                              })
                              load_color_picker();
                              load_noUiSlider();

                              
                              
                        }
                      );
            }
            
            
            //set images on configuration
            $(document).on("click",".set-bg-image-btn",function(e){
                e.preventDefault();
                //var selector = $(this).attr('data-selector');
                var trigger = $(this).parent();
                var uploader=wp.media({
                    title:'Add event icon',
                    button:{
                             text:"Add image"
                             },
                     multiple:false
                })
                .on('select',function(){
                     var selection=uploader.state().get('selection');
                     selection.map(
                            function(attachment){
                                    trigger.find('.thumbnail-container').html('<img src="'+attachment.attributes.url+'" alt="" />');
                                    trigger.children( "input[type = 'hidden']" ).attr( 'value', attachment.id );                               
                            }
                     );
                 })
                 .open();
            });
            
            $(document).on("click",".remove-image-bg-btn",function(e){
                e.preventDefault();
                //var selector = $(this).attr('data-selector');
                var trigger = $(this).parent();
                trigger.find('.thumbnail-container').html('');
                trigger.children( "input[type = 'hidden']" ).removeAttr('value');                               
                            
            })
            .on('change','.configurate-template .enable-fields', function(){
                if($(this).attr('checked') == 'checked'){
                    $(this).parents('.configurate-template').find('input:not(.enable-fields), textarea, button, select').removeClass('disabled') ;
                }else{
                    $(this).parents('.configurate-template').find('input:not(.enable-fields), textarea, button, select').addClass('disabled');
                }
                
            });
            if($('.configurate-template .enable-fields').length > 0){
                $('.configurate-template .enable-fields').trigger('change');
            }
            //Font selector
            if($('.wvas-font-selector').length > 0){
                $('.wvas-font-selector').fontselect();
            }
      });
      $.each(['show', 'hide'], function (i, ev) {
      var el = $.fn[ev];
      $.fn[ev] = function () {
        this.trigger(ev);
        return el.apply(this, arguments);
      };
    });
})( jQuery );
