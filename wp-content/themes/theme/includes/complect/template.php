<div class="product-complect">
	<div class="product-complect__title">Покупай комплект с выгодой</div>
	
	<div class="product-complect__list">
		<div class="product-complect__item">
			<div class="product-complect__discount">
				<?php echo $discount_first; ?>%
			</div>
			<div class="product-complect__image">
				<img src="<?php echo wp_get_attachment_url( $product->get_image_id() ); ?>" />
				<div class="product-complect__name">
					<?php echo $product->get_title(); ?>
				</div>
			</div>
			<div class="product-complect__price">
				<?php echo $product->get_price(); ?> ₽
			</div>
		</div>
		<div class="product-complect__separator">+</div>
		<div class="product-complect__item">
			<div class="product-complect__discount">
				<?php echo $discount_second; ?>%
			</div>
			<div class="product-complect__image">
				<img src="<?php echo wp_get_attachment_url( $complect_second_product->get_image_id() ); ?>" />
				
				<div class="product-complect__name">
					<?php echo $complect_second_product->get_title(); ?>
				</div>
			</div>
			<div class="product-complect__price">
				<?php echo $complect_second_product->get_price() - ($complect_second_product->get_price() / 100 * $discount_second); ?> ₽ <span class="product-complect__old"><?php echo $complect_second_product->get_price(); ?> ₽</span>
				
				<div class="product-complect__action">
					Выгода: <?php echo ($complect_second_product->get_price() / 100 * $discount_second); ?> ₽
				</div>
			</div>

		</div>
		<div class="product-complect__separator">+</div>
		<div class="product-complect__item">
			<div class="product-complect__discount">
				<?php echo $discount_third; ?>%
			</div>
			<div class="product-complect__image">
				<img src="<?php echo wp_get_attachment_url( $complect_third_product->get_image_id() ); ?>" />
				
				<div class="product-complect__name">
					<?php echo $complect_third_product->get_title(); ?>
				</div>
			</div>
			<div class="product-complect__price">
				<?php echo $complect_third_product->get_price() - ($complect_third_product->get_price() / 100 * $discount_second); ?> ₽ <span class="product-complect__old"><?php echo $complect_third_product->get_price(); ?> ₽</span>
				<div class="product-complect__action">
					Выгода: <?php echo ($complect_third_product->get_price() / 100 * $discount_third); ?> ₽
				</div>
			</div>
		</div>
		<div class="product-complect__separator">=</div>
		
		<div class="product-complect__total">
			<div>
				<span><?php echo $total_discount; ?> ₽</span>
				<span class="product-complect__old"><?php echo $total; ?> ₽</span>
				<div class="product-complect__economy">
					Всего вы экономите <?php echo $total-$total_discount; ?> ₽
				</div>
			</div>
		</div>
		<div class="product-complect__total">
			<form class="cart" action="" method="post" enctype="multipart/form-data" style="margin-bottom:0">
				<input type="hidden" name="quantity" value="1">
				<button type="submit" name="add-to-cart" value="<?php echo $product->get_id(); ?>" data-complect="1" class="single_add_to_cart_button button alt">В&nbsp;корзину</button>
			</form>
		</div>
	</div>
</div>