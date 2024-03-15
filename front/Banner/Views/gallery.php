<div class="container-fluid mt-5 mb-3">
	<div class="section-title">
		<h2>Gallery</h2>
		<h3>Our Works</h3>
	</div>
	<?php if (is_array($sliders)) : ?>
		<div class="grid grid-four-col kc_image_gallery" id="kehl-grid">
			<div class="grid-sizer">&nbsp;</div>
			<?php foreach ($sliders as $slider) : ?>
				<?php if (isset($slider['image'])) : ?>
					<div class="grid-box fruits">
						<a class="image-popup-vertical-fit kc-image-link" href="<?= $slider['image'] ?>">
							<img alt="Slider Image" src="<?= $slider['image'] ?>" height="200" width="300" /> 
						</a>
					</div>
				<?php endif; ?>
			<?php endforeach; ?>
		</div>
	<?php else : ?>
		<p>No sliders found.</p>
	<?php endif; ?>
</div>
