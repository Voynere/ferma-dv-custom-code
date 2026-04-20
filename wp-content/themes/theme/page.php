<?php 
if ( is_product_category() ) {
    get_header('archive');
} else {
    get_header();
}
?>
<div class="container info_page">
	<div class="row">
		<div class="col-12" style="text-align:center">
			<?php 
				if ( is_product_category() ) {
					?>
						<h1 class="page-title" style="text-align: start; margin: 0px !important;"><?php the_title(); ?></h1>
					<?php
				} else {
					?>
						<h1 style="color: #6ba802; font-size: -webkit-xxx-large;font-weight:bold"><?php the_title(); ?></h1>
					<?php
				}
			?>
		</div>
		<?php 
			if ( is_product_category() ) {
				?>
				<div class="col-12 shop-ferma__related shop-ferma__archive">
					<?php the_post(); ?>
					<?php the_content(); ?>
					<?php the_field('tekst'); ?>
				</div>
				<?php
			} else {
				?>
				<div class="col-12">
					<?php the_post(); ?>
					<?php the_content(); ?>
					<?php the_field('tekst'); ?>
				</div>
				<?php
			}
		?>
		
	</div>
</div>
<?php 
if ( is_product_category() ) {
    get_footer('home');
} else {
    get_footer();
}
?>