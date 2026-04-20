<?php
/*
Template Post Type: page
Template Name: Отзывы
*/
?>


<?php get_header(); ?>
<div class="container info_page">
    <div class="row">
        <div class="col-12">
            <?php the_post(); ?>
            <?php the_content(); ?>
        </div>
    </div>
</div>
<div class="container-fluid footer_info"
    style="background: url(http://localhost/ferma/wp-content/uploads/2021/04/01.jpg); background-size: cover;"></div>
<?php get_footer(); ?>