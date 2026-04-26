<?php
/**
 * Banner shortcode registration.
 *
 * @package Theme
 */

function my_shortcode() {
	$img1  = get_field( '1_izobrazhenie' );
	$img2  = get_field( '2_izobrazhenie' );
	$img3  = get_field( '3_izobrazhenie' );
	$link1 = get_field( '1_ssylka' );
	$link2 = get_field( '2_ssylka' );
	$link3 = get_field( '3_ssylka' );
	echo '
	<div class="short_bnr">
	<div style="display:flex">
	<img style="width: 633px;;margin-bottom: -5px;" id="photo_pc_1" onclick="location.href = ' . "'" . $link1 . "'" . ';" src="' . $img1 . '" height="250" alt="" /> 
  <div class="class" style="width:50%;margin-left:5px"> 
	  <img  onclick="location.href = ' . "'" . $link2 . "'" . ';" src="' . $img2 . '" style="width:100%;height: 50%;"  alt="" /> 
	<img  onclick="location.href = ' . "'" . $link3 . "'" . ';" src="' . $img3 . '" id="photo_pc_2" style="margin-top:5px;width:100%;height: 50%;" alt="" /> 
  
  </div>
   
  </div>
	<img style="width: 633px;display:none" id="photo_0"  onclick="location.href = ' . "'" . $link1 . "'" . ';" src="' . $img1 . '" height="250" alt="" /> 
   <img  onclick="location.href = ' . "'" . $link2 . "'" . ';" style="width: 100%;display:none;margin-top:1em" id="photo_1" src="' . $img2 . '" height="250" alt="" /> 
   <img style="width: 100%;display:none;margin-top:1em;" onclick="location.href = ' . "'" . $link3 . "'" . ';"  id="photo_2" src="' . $img3 . '" height="250" alt="" />
   </div>';
}
add_shortcode( 'say_banner', 'my_shortcode' );
