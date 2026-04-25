<?php
/**
* A Simple Category Template
*/
 
get_header(); ?> 
 <?
 $the_query = new WP_Query('cat=288&showposts=40');
 $thename = "Новости";
 
 $url = "https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
 if ($url == "https://ferma-dv.ru/novosti/") { 
    $the_query = new WP_Query('cat=288&showposts=40');
    $thename = "Новости";
 } 
 if ($url == "https://ferma-dv.ru/category/novosti/") { 
    $the_query = new WP_Query('cat=288&showposts=40');
    $thename = "Новости";
 } 
 if ($url == "https://ferma-dv.ru/category/akcii/") {
    $the_query = new WP_Query('cat=1&showposts=40');
    $thename = "Акции";
  }
  if ($url == "https://ferma-dv.ru/category/fermerskij-blog/") {
    $the_query = new WP_Query('cat=200&showposts=40');
    $thename = "Фермерский блог";
  }
  
 ?>
<h2 style="text-align:center"><?php echo $thename?></h2>
 <style>
    .cat-post {
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
    margin: 0 10px 20px 0;
    padding: 15px;
}

 .cat-post li{
    background: #fff;
    display: inline-block;
    vertical-align: top;
        width: 300px;
        padding: 20px;
        margin-right: 16px;
        margin-bottom:10px;
        box-shadow: 0px 0px 1px rgba(0,0,0,0.2);
}
.cat-post img{
    height: auto;
    max-width: 100%;
        margin-bottom:10px;
}
#secondary {
        display: none !important;
    }
 </style>
<section id="primary" class="site-content">
<div id="content" role="main">

<div class="container">
<ul class="cat-post">
<?php while ($the_query -> have_posts()) : $the_query -> the_post(); ?>
<li>
<!-- миниатюра записи -->
<a href="<?php the_permalink() ?>"><?php echo get_the_post_thumbnail( $post->ID,'full', 'thumbnail'); ?></a>
                        
<!-- заголовок записи -->
<h2><a style="font-size: 18px;" href='<?php the_permalink() ?>'><?php the_title(); ?></a></h2>
                        
<!-- количество слов в анонсе (необязательно) -->                
</li>
<?php endwhile; ?>
            
<!-- функция для правильной работы условных тегов -->
<?php wp_reset_query(); ?>
</ul>
</div>
</div>
<!-- функция вывода сайдбара -->
<?php get_sidebar(); ?>
<!-- функция вывода футера -->
<?php get_footer(); ?>
</section>
 
 
<?php get_sidebar(); ?>
<?php get_footer(); ?>