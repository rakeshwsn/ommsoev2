<div class="container-fluid mt-5 mb-3">
	<div class="section-title">
		<h2>Gallery</h2>
		<h3>Our Works</h3>
	</div>
	<?php if (is_array($sliders)) : ?>
		<div class="grid grid-four-col kc_image_gallery" id="kehl-grid" role="grid" aria-label="Slider gallery">
			<div class="grid-sizer">&nbsp;</div>
			<?php foreach ($sliders as $slider) : ?>
				<?php if (isset($slider['image'])) : ?>
					<div class="grid-box fruits">
						<figure>
							<a class="image-popup-vertical-fit kc-image-link" href="<?= htmlspecialchars($slider['image']) ?>">
								<img src="<?= htmlspecialchars($slider['image']) ?>" alt="<?= htmlspecialchars(isset($slider['title']) ? $slider['title'] : 'Slider image') ?>" height="200" width="300" />
								<?php if (isset($slider['title'])) : ?>
									<figcaption><?= htmlspecialchars($slider['title']) ?></figcaption>
					
