<?php namespace N1_Durable_Goods;

if(Module_Offline::has_download()){?>
<section class="offline">
	<div class="floatwrapper">
		<h3 class="offline category"><span class="module-hed"><?php echo get_field('offline_header', 'options')?></span></h3>
		<p class="offline dek"><?php echo get_field('offline_dek', 'options')?></p>
		<?php echo get_field('offline_copy', 'options');?>
		<?php Module_Offline::print_download_button();?>
	</div>
</section>
<?php }?>
