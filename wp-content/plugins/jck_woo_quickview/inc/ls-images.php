<?php global $post, $product, $woocommerce, $items, $pid; ?>

<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<div id="<?php echo $this->slug.'_images_wrap'; ?>">
	<div id="<?php echo $this->slug.'_images'; ?>" class="royalSlider rsMinW">
	
		<?php
		
			$prodImgs = array();

			//$nodes = $items->xpath('//ItemMatrix/itemMatrixID[.="'.$_REQUEST['pid'].'"]/parent::*');
			//$result = $nodes[0];
			
			foreach($items->children() as $item){
				
				if ($item->itemMatrixID == $pid){
					
					print_r($item->Images);
					$imgId = $item->Images->Image[0]->imageID;
					$has_image = (!empty($item->Images))? true : false;
					$image_base = $item->Images->Image[0]->baseImageURL;
					$image_id = $item->Images->Image[0]->publicID;
					$image_url = $image_base .'c_pad,h_400,q_75,w_400/'. $image_id;
					$thumb_url = $image_base .'c_fill,h_220,w_220/'. $image_id;

					if($has_image){
						$prodImgs[$imgId]['slideId'][] = '-0-';
						$prodImgs[$imgId]['imgSrc'] = $image_url;
						$prodImgs[$imgId]['imgThumbSrc'] = $thumb_url;
					}
					//echo '<img src="http://borealpaddle/wp-content/uploads/2014/11/fhl1qli2fzpnhufhfcap-300x300.jpg" data-jckqv="-0-" class="jckqv_image rsImg rsMainSlideImage" data-rstmb="http://borealpaddle/wp-content/uploads/2014/11/fhl1qli2fzpnhufhfcap-150x150.jpg" style="visibility: visible; opacity: 1; -webkit-transition: opacity 400ms ease-in-out; transition: opacity 400ms ease-in-out; width: 360px; height: 360px; margin-left: 0px; margin-top: 0px;">';
					// Additional Images
			
					/*$attachment_count = count( $item->Images );
			
					if(!empty($attachment_count) && $attachment_count > 1):
						foreach($item->Images as $image):
					
							m = $items->xpath('//ItemMatrix/itemMatrixID[.="'.$_REQUEST['pid'].'"]/Images/Image/imageID[.="'.$attachId.'"]/parent::*');
							$image_id = $image->publicID;
							$image_url = $image_base .'c_pad,h_400,q_75,w_400/'. $image_id;
							$thumb_url = $image_base .'c_fill,h_220,w_220/'. $image_id;
					
							$prodImgs[$attachId]['slideId'][] = '-0-';
							$prodImgs[$attachId]['imgSrc'] = $image_url;
							$prodImgs[$attachId]['imgThumbSrc'] = $thumb_url;
					
						endforeach;
					endif;*/
				}
			}	
			
			
			if(!empty($prodImgs)):
				$i = 0; foreach($prodImgs as $imgId => $imgData):
					if($i == 0):
						echo '<img src="'.$imgData['imgSrc'][0].'" data-'.$this->slug.'="'.implode(' ', $imgData['slideId']).'" class="'.$this->slug.'_image" data-rsTmb="'.$imgData['imgThumbSrc'][0].'">';
					else:
						echo '<a href="'.$imgData['imgSrc'][0].'" data-'.$this->slug.'="'.implode(' ', $imgData['slideId']).'" class="'.$this->slug.'_image rsImg" data-rsTmb="'.$imgData['imgThumbSrc'][0].'"></a>';
					endif;
				$i++; endforeach;
			endif;
			
		?>
	
	</div>
</div>