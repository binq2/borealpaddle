<?php global $post, $product, $woocommerce, $prodImgs, $pid; ?>

<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>

<div id="<?php echo $this->slug.'_images_wrap'; ?>">
	<div id="<?php echo $this->slug.'_images'; ?>" class="royalSlider rsMinW">
	
		<?php
			
			//if(!empty($prodImgs)):
				$i = 0; foreach($prodImgs as $imgId => $imgData):
					if($i == 0):
						echo '<img src="'.$imgData['imgSrc'][0].'" data-'.$this->slug.'="'.implode(' ', $imgData['slideId']).'" class="'.$this->slug.'_image" data-rsTmb="'.$imgData['imgThumbSrc'][0].'">';
					else:
						echo '<a href="'.$imgData['imgSrc'][0].'" data-'.$this->slug.'="'.implode(' ', $imgData['slideId']).'" class="'.$this->slug.'_image rsImg" data-rsTmb="'.$imgData['imgThumbSrc'][0].'"></a>';
					endif;
				$i++; endforeach;
			//endif;
			
		?>
	
	</div>
</div>