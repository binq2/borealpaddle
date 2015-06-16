/* 
 * Front-end js
 */

jQuery(document).ready(function ($) {

    $(".variations_data_selected.wvas_item_selected_container").hover(function () {
        $(this).parents('.value').find(".wvas_item_container").show(function () {
            $(this).parents('.value').mouseleave(
                function () {
                    $(this).parent().find(".wvas_item_container").hide();
                }
            );
        });
    });
    
    $(document).on('click', '.skin-6-container .variations_data > span', function () {
        $(this).parents('.value').find('.wvas_item_selected').css('background-image', $(this).css('background-image'));
    });
});
