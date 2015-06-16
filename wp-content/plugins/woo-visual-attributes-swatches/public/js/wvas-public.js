(function( $ ) {
	'use strict';

	/**
	 * All of the code for your public-facing JavaScript source
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
        jQuery(document).ready(function ($) {
//            $(".single-product table.variations").hide();
            
            //Select
            $(document).on('change', '.wvas-variation-select', function () {
                var select_value = $(this).val();
                var selected_element=$(this).find("option[value='"+select_value+"']");
                var dropdown_id = selected_element.data("id");
                var select_value_label=selected_element.data("label");
                wvas_set_selected_value(dropdown_id, select_value_label, select_value);
            });
            
            //Radio
            $(document).on('change', '.wvas-radio', function () {
                var select_value = $(this).val();
                var dropdown_id = this.dataset.id;
                var select_value_label=this.dataset.label;
                wvas_set_selected_value(dropdown_id, select_value_label, select_value);
            });
            
            //Span
            $(document).on('click', '.variations_data span', function () {
                var dropdown_id = $(this).data('id');
                var select_value = $(this).data('val');
                var select_value_label = $(this).attr('title');
                wvas_set_selected_value(dropdown_id, select_value_label, select_value);

                $(this).parent().find(".selected_wvas").removeClass("selected_wvas");
                $(this).addClass("selected_wvas");

            });
            
            function wvas_set_selected_value(dropdown_id, select_value_label, select_value)
            {
//                attr_dropdown = $('select[name="' + select_name + '"]');
                var attr_dropdown = $('#' + dropdown_id);
                $variation_form = attr_dropdown.closest('.variations_form');
                if(attr_dropdown.find("option[value='"+select_value+"']").length === 0)
                {
                    var to_append='<option value="'+select_value+'" class="attached enabled">'+select_value_label+'</option>';
                    attr_dropdown.append(to_append);
                    attr_dropdown.val(select_value);
                }
                else
                    attr_dropdown.val(select_value).change();
            }
//          $('.variations').hide();
//        $(document).on('click', '.variations_data span', function () {
//            var select_name = $(this).data('select_name'),
//                select_value = $(this).data('attr_val'),
//                select_value_label = $(this).attr('title'),
//                attr_dropdown = $('select[name="' + select_name + '"]'),
//                $variation_form = attr_dropdown.closest('.variations_form');
//
//            select_value = select_value.toLowerCase();
//            $(this).parents('.value').find('span').each(function () {
//                if ($(this).hasClass('selected_wvas')) {
//                    $(this).removeClass('selected_wvas');
//                }
//            });
//            $(this).addClass("selected_wvas");
//            if ($(this).parents('.value').find('.wvas_item_selected').length > 0) {
//                $(this).parents('.value').find('.wvas_item_selected').html(
//                    $(this).html()
//                ).css('backgroundColor', $(this).css('backgroundColor'));
//                $(this).parents('.value').find('.wvas_item_selected_label').html(
//                    select_value_label
//                );
//            }
//
//            if ($(':selected', attr_dropdown).length > 0) {
//                $(':selected', attr_dropdown).prop('selected', false);
//            }
//            
//            $('[value=' + select_value + ']', attr_dropdown).prop('selected', true);
//            //attr_dropdown.trigger('change');
//            
//            $variation_form.find('input[name=variation_id]').val('').change();
//
//            $variation_form
//                    .trigger('woocommerce_variation_select_change')
//                    .trigger('check_variations', [ '', false ]);
//
//            attr_dropdown.blur();
//
//            if ($().uniform && $.isFunction($.uniform.update)) {
//                $.uniform.update();
//            }
//        });
//    
//        //Custom dropdown selector handler 
//        $(document).on('change', '.wvas_default_style', function () {
//            var select_name = $(this).data('select_name'),
//                select_value = $(this).val(),
//                attr_dropdown = $('select[name="' + select_name + '"]'),
//                $variation_form = attr_dropdown.closest('.variations_form');
//
//            select_value = select_value.toLowerCase();
//            if ($(':selected', attr_dropdown).length > 0) {
//                $(':selected', attr_dropdown).prop('selected', false);
//            }
//            if (select_value !== '')
//                $('[value=' + select_value + ']', attr_dropdown).prop('selected', true);
//            else
//                attr_dropdown.selectedIndex = 0;
//
//            //attr_dropdown.trigger('change');
//
//            //Do "attr_dropdown.trigger('change')" actions 
//            $variation_form.find('input[name=variation_id]').val('').change();
//
//            $variation_form
//                    .trigger('woocommerce_variation_select_change')
//                    .trigger('check_variations', [ '', false ]);
//
//            attr_dropdown.blur();
//
//            if ($().uniform && $.isFunction($.uniform.update)) {
//                $.uniform.update();
//            }
//        });
//    
//        $(".variations_data_selected.wvas_item_selected_container").hover(function () {
//            $(this).parents('.value').find(".wvas_item_container").show(function () {
//                $(this).parents('.value').mouseleave(
//                    function () {
//                        $(this).parent().find(".wvas_item_container").hide();
//                    }
//                );
//            });
//        });
    });
})( jQuery );
