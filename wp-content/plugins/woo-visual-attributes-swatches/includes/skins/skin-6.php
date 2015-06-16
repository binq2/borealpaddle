<?php

/**
 * Name: My Skin 6
 * Screenshot: Bing
 * Type: Image
 */

ob_start();
?>
<table class="wvas_variations skin-6-container {variation-container-class}" cellspacing="0">
	<tbody>
		<tr class="">

			<td class="label wvas_item_label">
				<label>{variation-label}</label>
			</td>
			<td class="value"> 
				<div class="variations_data_selected wvas_item_selected_container">
	                <span class="wvas_item_selected"></span>
	                <span class="wvas_item_selected_label">Choose value</span>
	                <span class="wvas_show_items_ico"></span>
	            </div>
				<div class="variations_data wvas_item_container">
                                    {attributes}
				</div>
			</td>
			
			
		</tr>
	</tbody>
</table>
<?php
$global_tpl=  ob_get_contents();
ob_end_clean();

ob_start();
?>
<span data-label="{attribute-label}" data-val="{attribute-value}" data-id="{attributes-id}" class="wvas_item  {attribute-selected} skin-6-child" style="background-image: url('{attribute-image}')"></span>
<?php
$attribute_tpl=  ob_get_contents();
ob_end_clean();