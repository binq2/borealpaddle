<?php

/**
 * Name: My Skin 1
 * Description: Replace woocommerce's default attributes dropdowns by pictures or color fields
 * Author: ORION
 * Screenshot: Bing
 * Type: Select
 */

ob_start();
?>
<table class="wvas_variations  skin-1-container {variation-container-class}" cellspacing="0">
	<tbody>
		<tr class="">
			<td class="label wvas_item_label">
				<label>{variation-label}</label>
			</td>
			<td class="value">
				<div class="variations_data wvas_item_container">
					<select class=" wvas-variation-select skin-1-child">
		            	{attributes}
		        	</select>
	        	<div>
	        </td>
		</tr>
	</tbody>
</table>
<?php
$global_tpl=  ob_get_contents();
ob_end_clean();

ob_start();
?>
<option value="{attribute-value}" data-id="{attributes-id}" data-label="{attribute-label}" {attribute-selected}>{attribute-label}</option>
<?php
$attribute_tpl=  ob_get_contents();
ob_end_clean();