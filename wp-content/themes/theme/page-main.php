<?php
/*
Template Post Type: post, page, product
Template Name: Главная
*/
?>


<?php get_header(); ?>
<style>
    .breadcums {
        display: none;
    }
</style>
	
	<?php if( have_rows('dslider') ): ?>
	<div class="container">
		<div class="dslider">
			<div class="dslider__big">
				<div class="dslider__list">
					<?php while( have_rows('dslider') ) : the_row(); ?>
						<div class="dslider__item">
							<a href="<?php echo get_sub_field('dslider_link'); ?>">
								<img src="<?php echo get_sub_field('dslider_image'); ?>" />
							</a>
						</div>
					<?php endwhile; ?>
				</div>
				
				<a href="javascript:;" class="dslider__arrow dslider__arrow-prev">Назад</a>
				<a href="javascript:;" class="dslider__arrow dslider__arrow-next">Вперед</a>
			</div>
			<div class="dslider__small">
				<div class="dslider__small-item">
					<a href="<?php echo get_field('mini_banner_second_link'); ?>">
						<img src="<?php echo get_field('mini_banner_second_image'); ?>" />
					</a>
				</div>
				<div class="dslider__small-item">
					<a href="<?php echo get_field('mini_banner_third_link'); ?>">
						<img src="<?php echo get_field('mini_banner_third_image'); ?>" />
					</a>
				</div>
			</div>
		</div>
	</div>
	<?php endif; ?>
	
	<div class="container slider_home d-none d-lg-block mrg-0">
        <?php the_field('slajder'); ?>
    </div>
	
	
    <div class="container">
    <?php the_field('novinki'); ?>
    </div>
	
	
    <div class="container">
        <div class="row" style="text-align: center">
            <div class="col-12 pd_lr-20" style="padding: 0px;margin-top:20px">
            <?php the_content(); ?>
            </div>
        </div>
    </div>
    <?php get_footer(); ?>
