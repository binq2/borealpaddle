<?php

/**
 * Name: My Skin 3
 * Description: Replace woocommerce's default attributes dropdowns by pictures or color fields
 * Author: ORION
 * Screenshot: Bing
 * Type: Radio
 */

ob_start();
?>
<table class="wvas_variations skin-3-container {variation-container-class}" cellspacing="0">
	<tbody>
		<tr class="">
			<td class="label wvas_item_label">
				<label>{variation-label}<label>
			</td>
			<td class="value">
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
<div class="skin-3-child"><input type="radio" class="wvas-radio " name="wvas-{attributes-id}" value="{attribute-value}" data-id="{attributes-id}" data-label="{attribute-label}" {attribute-selected}> <label class="skin-3-label">{attribute-label}</label></div>
<?php
$attribute_tpl=  ob_get_contents();
ob_end_clean();