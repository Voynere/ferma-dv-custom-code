<?
/*
Template Name: Шаблон связи

Template Post Type: post, page, product
*/
?>
<?php get_header(); ?>

<div class="container info_page">
	<div class="row">
    <?
    if(!isset($_GET['work'])) {
      ?>


<?
} else {?>
		<div class="col-12" style="text-align:center"><h1 style="color: #6ba802; font-size: -webkit-xxx-large;font-weight:bold"><?php the_title(); ?></h1></div>
    <?
            $cur_user_id = get_current_user_id();
            $user = get_userdata($cur_user_id);
            $username = $user->user_market;
            ?>
    <div class="col-12">
			<h1 style="margin-top: -2rem !important">В ближайшее время наш менеджер свяжется с вами.</h1>
		</div>
</div>
</div>
<?}?>
<?php get_footer(); ?>