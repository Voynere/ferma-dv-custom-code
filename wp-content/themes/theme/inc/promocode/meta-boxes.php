<?php
/**
 * Q promocode admin meta boxes.
 *
 * @package Theme
 */

// Метабоксы.
add_action(
	'add_meta_boxes',
	function () {
		add_meta_box(
			'q_promocode_meta',
			'Настройки промокода',
			'q_promocode_meta_box_cb',
			'q_promocode',
			'normal',
			'high'
		);
	}
);

function q_promocode_meta_box_cb( $post ) {
	$code          = get_post_meta( $post->ID, '_q_code', true );
	$gift_sku      = get_post_meta( $post->ID, '_q_gift_sku', true );
	$discount_type = get_post_meta( $post->ID, '_q_discount_type', true ); // percent|absolute
	$discount_val  = get_post_meta( $post->ID, '_q_discount_val', true );
	$lifetime      = get_post_meta( $post->ID, '_q_lifetime_hours', true );
	$usage_limit   = get_post_meta( $post->ID, '_q_usage_limit', true );
	?>
		<style>
			.product-card__cart {
				display: flex;
				align-items: center;
				gap: 10px;
			}

			.product-card__cart .add_to_cart_button {
				margin-left: auto;
				display: inline-flex;
				justify-content: center;
				align-items: center;
				white-space: nowrap;
			}
			.product-card__cart {
				display: flex;
				align-items: center;
				gap: 10px;
			}
			.product-card {
				padding-right: 10px; /* или больше/меньше */
			}
			.add_to_cart_button.shop-ferma__rel-add {
				margin-left: 28px; /* или сколько тебе нужно */
			}
			.product-card__cart .cart__qty {
				margin-right: 32px; /* подбери число под макет */
			}

			/* или, если такого контейнера нет, просто так: */
			.cart__qty {
				margin-right: 20px;
			}
			.product-card__cart .add_to_cart_button {
				margin-left: auto;
				margin-right: 10px; /* сколько нужно – подбери */
				display: inline-flex;
				justify-content: center;
				align-items: center;
				white-space: nowrap;
			}

			/* количество как и раньше */
			.product-card__cart .cart__qty {
				display: inline-flex;
				align-items: center;
				gap: 8px;
			}

		</style>
	<p>
		<label>Код (формат Q123):</label><br>
		<input type="text" name="q_code" value="<?php echo esc_attr( $code ); ?>" style="width:100%;">
	</p>

	<p>
		<label>Артикул товара (SKU подарка):</label><br>
		<input type="text" name="q_gift_sku" value="<?php echo esc_attr( $gift_sku ); ?>" style="width:100%;">
	</p>
	<p>
		<label>Тип скидки:</label><br>
		<select name="q_discount_type">
			<option value="percent"  <?php selected( $discount_type, 'percent' ); ?>>Процент, %</option>
			<option value="absolute" <?php selected( $discount_type, 'absolute' ); ?>>Абсолютная цена (руб.)</option>
		</select>
	</p>
	<p>
		<label>Значение скидки (процент или итоговая цена):</label><br>
		<input type="number" step="0.01" name="q_discount_val" value="<?php echo esc_attr( $discount_val ); ?>">
	</p>
	<p>
		<label>Срок действия, часов:</label><br>
		<input type="number" name="q_lifetime_hours" value="<?php echo esc_attr( $lifetime ); ?>">
	</p>
	<p>
		<label>Макс. применений на 1 пользователя (по телефону):</label><br>
		<input type="number" name="q_usage_limit" value="<?php echo esc_attr( $usage_limit ); ?>">
	</p>
	<?php
}
