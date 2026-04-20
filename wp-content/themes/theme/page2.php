<?
/*
Template Name: Мой шаблон страницы12
Template Post Type: post, page, product
*/
?>
<?php get_header(); ?>

<div class="container info_page">
	<div class="row">
    <?
    if( isset($_POST['po'])) {
    ?>
    <style>
      #vtoro {
        display: none !important;
      }
    </style>
    <?}?>
		<div class="col-12" style="text-align:center"><h1 style="color: #6ba802; font-size: -webkit-xxx-large;font-weight:bold"><?php the_title(); ?></h1></div>
    <?
            $cur_user_id = get_current_user_id();
            $user = get_userdata($cur_user_id);
            $username = $user->user_market;
                        if(!$username || isset($_POST['po'])){
            ?>
    <div class="col-12">
			<h1>Выберите ближайший к вам магазин</h1>
		</div>
		<div class="col-12">
		<?
            if (!empty($_POST["asd"])) {
              unset($_COOKIE['vibor']);
              setcookie('vibor', null, -1, '/');
              setcookie('market', null, -1, '/');
                require_once( $_SERVER['DOCUMENT_ROOT'] . '/wp-config.php' );
                require_once( $_SERVER['DOCUMENT_ROOT'] . '/wp-includes/wp-db.php' );
                $wpdb = new wpdb( DB_USER, DB_PASSWORD, DB_NAME, DB_HOST);
                $market = $_POST['asd'];
                SetCookie("market", $_POST["asd"], time()+60*60*24*1, '/');
                header("Location: https://ferma-dv.ru/user-market1");
            } else {
            }
            ?>
            <?
            $cur_user_id = get_current_user_id();
            $user = get_userdata($cur_user_id);
            $username = $user->user_market;
            ?>
                <ul style="list-style: none;
    display: block !important;
    padding: 0;
    margin: 0;" class="menu1 pull-right hidden-xs hidden-sm dblock22">
        <li>
          <style>
            .single { display: none; }
.single.active { display: flex; }
          </style>
          <script>
            function showSingleDiv(selector) {
  const prevBlockEl = document.querySelector('.single.active'),
        currBlockEl = document.querySelector(selector);
  if (!currBlockEl || prevBlockEl === currBlockEl) return;
  prevBlockEl && prevBlockEl.classList.remove('active');
  currBlockEl.classList.add('active');
}
          </script>
    <select onchange="showSingleDiv(this.value)">
    <option disabled selected>Выбор города...</option>
    <option value="#some-id">Владивосток</option>
    <option value=".some-class">Уссурийск</option>
  </select>
  <div id="some-id" class="single">
    <form method="post">
      <input type="hidden" name="asd" value="ГринМаркет ТЦ Море">
    <button type="submit">ТЦ Море, Некрасовская, 49а</button>
    </form>
    <form method="post">
      <input type="hidden" name="asd" value="Реми-Сити">
    <button type="submit">Реми-Сити, Народный проспект, 20</button>
    </form>
    <form method="post">
      <input type="hidden" name="asd" value="Эгершельд">
    <button type="submit">Эгершельд, Верхнепортовая, 41в</button>
    </form>
    <form method="post">
      <input type="hidden" name="asd" value="Космос">
    <button type="submit">Космос, Тимирязева, 31 строение 1</button>
    </form>
  </div>
<div class="single some-class">    <form method="post">
      <input type="hidden" name="asd" value="Уссурийск">
    <button type="submit">Уссурийск, ТЦ Москва, 1-й этаж (ул. Суханова, 52)</button>
    </form></div>
    </li>
      </ul>


		</div>
		<?}?>
</div>
</div>
<?php get_footer(); ?>