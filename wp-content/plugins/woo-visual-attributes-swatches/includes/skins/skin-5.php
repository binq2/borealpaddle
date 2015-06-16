<?php

/**
 * Name: My Skin 5
 * Screenshot: Bing
 * Type: Color
 */

ob_start();
?>
<table class="wvas_variations skin-5-container {variation-container-class}" cellspacing="0">
	<tbody>
		<tr class="">
			<td class="label wvas_item_label">
				<label>{variation-label}</label>
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
<span data-label="{attribute-label}" data-val="{attribute-value}" data-id="{attributes-id}" class="wvas_item {attribute-selected} skin-5-child" style="background-color: {attribute-color}"></span>
<?php
$attribute_tpl=  ob_get_contents();
ob_end_clean();