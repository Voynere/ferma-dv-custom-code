<?php

/**
 * Refinements
 *
 * @author Prudnikov Mikhail, MsWoo (©) 2024
 * @date 26-07-2024
 * Start >>
 */
/**
 * Синхронизация ACF "Разбивка веса" с атрибутом "Фасовка".
 * Разбивка только по 0.1: если НЕ "шт" → считаем весовым.
 */
add_action( 'save_post_product', 'fdv_sync_razbivka_from_fasovka', 30, 3 );

function fdv_sync_razbivka_from_fasovka( $post_id, $post, $update ) {

    // тех. фильтры
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }
    if ( wp_is_post_revision( $post_id ) ) {
        return;
    }

    $product = wc_get_product( $post_id );
    if ( ! $product ) {
        return;
    }
    if ( $product->is_type('variation') ) {
        return;
    }
    // ----- 1. Достаем значение "Фасовки" из атрибутов товара -----
    $fasovka = '';
    $attributes = $product->get_attributes();

    foreach ( $attributes as $attr ) {

        $name = $attr->get_name(); // для таксономий будет 'pa_что-то', для кастомных — строка

        // Подстрой под свои варианты названия атрибута
        if ( in_array( $name, array( 'pa_fasovka', 'Фасовка', 'fasovka' ), true ) ) {

            $options = $attr->get_options();
            if ( empty( $options ) ) {
                continue;
            }

            // Если это таксономия (pa_fasovka) – тянем имя термина
            if ( taxonomy_exists( $name ) ) {
                $term = get_term( $options[0] );
                $fasovka = $term && ! is_wp_error( $term ) ? $term->name : '';
            } else {
                // Нестационный атрибут
                $fasovka = (string) $options[0];
            }

            break;
        }
    }

    if ( $fasovka === '' ) {
        // нет фасовки – не трогаем поле
        return;
    }

    // ----- 2. Логика: "шт" -> штучный, иначе весовой -----
    $fasovka_lc = mb_strtolower( trim( $fasovka ) );

    // по умолчанию считаем весовым (будет 'да')
    $is_weight = true;

    // если в фасовке встречается "шт" – считаем штучным
    if ( preg_match( '/шт/u', $fasovka_lc ) ) {
        $is_weight = false;
    }

    $value = $is_weight ? 'да' : 'нет';

    // ----- 3. Обновляем ACF-поле "Разбивка веса" -----
    // ACF-ключ с твоего скрина: field_627cbc0e2d6f3
    if ( function_exists( 'update_field' ) ) {
        update_field( 'field_627cbc0e2d6f3', $value, $post_id );
    } else {
        // fallback, если ACF вдруг выключат
        update_post_meta( $post_id, 'razbivka_vesa', $value );
    }
}





add_filter('wms_order_action', 'fdv_ms_delivery_time', 10, 2);

/**
 * Delivery time.
 *
 * @param $customerOrder
 * @param $Order
 *
 * @return string
 */
function fdv_ms_delivery_time($customerOrder, $Order) {

	$deliveryTime = $Order->get_meta('billing_asdx1');

	if(!$deliveryTime){
		return $customerOrder;
	}

	$dataMapping = [
		'Сегодня, с 10 до 12' =>  'ecc50a96-0836-11ef-0a80-0925004ecd8a',//10:00 - 12:00
		'Завтра, с 10 до 12' =>  'ecc50a96-0836-11ef-0a80-0925004ecd8a',//10:00 - 12:00
		'Сегодня, с 15 до 17' => 'f3aa8e72-0836-11ef-0a80-0e45004ec647',//15:00 - 17:00
		'Завтра, с 15 до 17' => 'f3aa8e72-0836-11ef-0a80-0e45004ec647',//15:00 - 17:00
		'Сегодня, с 19 до 22' => 'fee68744-0836-11ef-0a80-0f13004ed5f6',//19:00 - 22:00
		'Завтра, с 19 до 22' => 'fee68744-0836-11ef-0a80-0f13004ed5f6',//19:00 - 22:00
	];

	if(isset($dataMapping[$deliveryTime])){

		$customerOrder['attributes'][] = array(
			'meta' => [
				"href" =>  "https://api.moysklad.ru/api/remap/1.2/entity/customerorder/metadata/attributes/45abb3ca-0837-11ef-0a80-0bcc004efc3f",
				"type" => "attributemetadata",
				"mediaType" => "application/json"
			],
			'value' => [
				'meta' => [
					"href" => "https://api.moysklad.ru/api/remap/1.2/entity/customentity/d133041a-0836-11ef-0a80-10de004fb76e/{$dataMapping[$deliveryTime]}",
					"metadataHref" => "https://api.moysklad.ru/api/remap/1.2/entity/companysettings/metadata/customEntities/d133041a-0836-11ef-0a80-10de004fb76e",
					"type" => "customentity",
					"mediaType" => "application/json",
					"uuidHref" => "https://api.moysklad.ru/app/#custom_d133041a-0836-11ef-0a80-10de004fb76e/edit?id={$dataMapping[$deliveryTime]}"
				]
			]
		);
	}


	return $customerOrder;
}


add_filter('wms_order_action', 'fdv_ms_delivery_local_time', 10, 2);

/**
 * Delivery time.
 *
 * @param $customerOrder
 * @param $Order
 *
 * @return string
 */
function fdv_ms_delivery_local_time($customerOrder, $Order) {

	$deliveryTime = $Order->get_meta('billing_type_delivery_sam');

	if(!$deliveryTime){
		return $customerOrder;
	}

	$dataMapping = [
		'Сегодня, 10:00-12:00' => '9eeed7aa-0836-11ef-0a80-0f13004ecfd7',//19:00 - 22:00
		'Сегодня, 12:00-14:00' =>  'a7fbfed1-0836-11ef-0a80-0c4c005119a6',//10:00 - 12:00
		'Сегодня, 14:00-16:00' =>  'b031841b-0836-11ef-0a80-0e45004ec319',//10:00 - 12:00
		'Сегодня, 16:00-18:00' => 'b7ba69bd-0836-11ef-0a80-134d004e4aa6',//15:00 - 17:00
		'Сегодня, 18:00-20:00' => 'beb09fae-0836-11ef-0a80-0e45004ec427',//15:00 - 17:00
		'Сегодня, 20:00-21:00' => 'c63fc813-0836-11ef-0a80-0925004ec2b0',//19:00 - 22:00
		'Завтра, 10:00-12:00' => '9eeed7aa-0836-11ef-0a80-0f13004ecfd7',//19:00 - 22:00
		'Завтра, 12:00-14:00' => 'a7fbfed1-0836-11ef-0a80-0c4c005119a6',//19:00 - 22:00
		'Завтра, 14:00-16:00' => 'b031841b-0836-11ef-0a80-0e45004ec319',//19:00 - 22:00
		'Завтра, 16:00-18:00' => 'b7ba69bd-0836-11ef-0a80-134d004e4aa6',//19:00 - 22:00
		'Завтра, 18:00-20:00' => 'beb09fae-0836-11ef-0a80-0e45004ec427',//19:00 - 22:00
		'Завтра, 20:00-21:00' => 'c63fc813-0836-11ef-0a80-0925004ec2b0',//19:00 - 22:00
	];

	if(isset($dataMapping[$deliveryTime])){

		$customerOrder['attributes'][] = array(
			'meta' => [
				"href" =>  "https://api.moysklad.ru/api/remap/1.2/entity/customerorder/metadata/attributes/45abb540-0837-11ef-0a80-0bcc004efc40",
				"type" => "attributemetadata",
				"mediaType" => "application/json"
			],
			'value' => [
				'meta' => [
					"href" => "https://api.moysklad.ru/api/remap/1.2/entity/customentity/52f5aab7-0836-11ef-0a80-0bcc004eec9d/{$dataMapping[$deliveryTime]}",
					"metadataHref" => "https://api.moysklad.ru/api/remap/1.2/entity/companysettings/metadata/customEntities/52f5aab7-0836-11ef-0a80-0bcc004eec9d",
					"type" => "customentity",
					"mediaType" => "application/json",
					"uuidHref" => "https://api.moysklad.ru/app/#custom_52f5aab7-0836-11ef-0a80-0bcc004eec9d/edit?id={$dataMapping[$deliveryTime]}"
				]
			]
		);
	}


	return $customerOrder;
}


add_filter('wms_order_action', 'fdv_ms_delivery_type', 10, 2);

/**
 * Delivery type.
 *
 * @param $customerOrder
 * @param $Order
 *
 * @return string
 */
function fdv_ms_delivery_type($customerOrder, $Order) {

	$isLocal = $Order->get_meta('billing_samoviziv');
	$deliveryTypeUuid = '7d785dc9-9807-11ee-0a80-0cca0036d646';
	if($isLocal){
		$deliveryTypeUuid = '83334031-9807-11ee-0a80-017900363eaf';
	}

	$customerOrder['attributes'][] = array(
		'meta' => [
			"href" =>  "https://api.moysklad.ru/api/remap/1.2/entity/customerorder/metadata/attributes/d07a5167-9807-11ee-0a80-1398003690cf",
			"type" => "attributemetadata",
			"mediaType" => "application/json"
		],
		'value' => [
			'meta' => [
				"href" => "https://api.moysklad.ru/api/remap/1.2/entity/customentity/8864b5ac-9806-11ee-0a80-11fb003800a6/{$deliveryTypeUuid}",
				"metadataHref" => "https://api.moysklad.ru/api/remap/1.2/entity/companysettings/metadata/customEntities/8864b5ac-9806-11ee-0a80-11fb003800a6",
				"type" => "customentity",
				"mediaType" => "application/json",
				"uuidHref" => "https://api.moysklad.ru/app/#custom_8864b5ac-9806-11ee-0a80-11fb003800a6/edit?id={$deliveryTypeUuid}"
			]
		]
	);


	return $customerOrder;
}




add_filter('wms_order_action', 'fdv_ms_address', 10, 2);

/**
 * Delivery address.
 *
 * @param $customerOrder
 * @param $Order
 *
 * @return string
 */
function fdv_ms_address($customerOrder, $Order) {

	$billingDelivery= $Order->get_meta('billing_delivery');


	if($billingDelivery){
		$customerOrder['shipmentAddressFull']['addInfo'] = $billingDelivery;
	}

	$comment = array();

	if($driveway = $Order->get_meta('billing_dev_2')){
		$comment[] = 'Подъезд: ' . $driveway;
	}

	if($floor = $Order->get_meta('billing_dev_3')){
		$comment[] = 'Этаж: ' . $floor;
	}

	if($family_or_office = $Order->get_meta('billing_dev_1')){
		$comment[] = 'Квартира / офис: ' . $family_or_office;
	}

	if($intercom  = $Order->get_meta('billing_dev_4')){
		$comment[] = 'Домофон: ' . $intercom;
	}
	if($comment){
		$customerOrder['shipmentAddressFull']['comment'] = implode(PHP_EOL, array_filter($comment));
	}


	return $customerOrder;
}

add_filter('wms_order_action', 'fdv_ms_phone', 10, 2);

/**
 * Delivery address.
 *
 * @param $customerOrder
 * @param $Order
 *
 * @return string
 */
function fdv_ms_phone($customerOrder, $Order) {
	$customerOrder['attributes'][] = array(
		'meta' => [
			"href" =>  "https://api.moysklad.ru/api/remap/1.2/entity/customerorder/metadata/attributes/2bedb738-ed8c-11ef-0a80-04c100021130",
			"type" => "attributemetadata",
			"mediaType" => "application/json"
		],
		'value' => (string)$Order->get_billing_phone()
	);


	return $customerOrder;
}

add_filter('wms_order_product_action', 'fdv_ms_weight_items_fix', 10, 2);

/**
 * Приводим весовые товары к кг в МойСклад:
 * quantity: шаги (0.1) -> кг
 * price: цена за шаг -> цена за кг
 *
 * @param array    $positions
 * @param WC_Order $Order
 *
 * @return array
 */
function fdv_ms_weight_items_fix( $positions, WC_Order $Order ) {

    if ( empty( $positions ) ) {
        return $positions;
    }

    $items = $Order->get_items( 'line_item' );
    if ( empty( $items ) ) {
        return $positions;
    }

    $i = 0;

    foreach ( $items as $item_id => $item ) {

        if ( ! isset( $positions[ $i ] ) ) {
            $i++;
            continue;
        }

        $product = $item->get_product();
        if ( ! $product ) {
            $i++;
            continue;
        }

// ВСЕГДА работаем по parent ID (важно для вариаций)
        $product_id = $product->is_type('variation') ? $product->get_parent_id() : $product->get_id();

// Нормализуем значение ACF (чтобы "Да", "да ", и т.п. не ломали логику)
        $rz = function_exists('get_field')
            ? (string) get_field('razbivka_vesa', $product_id)
            : (string) get_post_meta($product_id, 'razbivka_vesa', true);

        $rz = mb_strtolower(trim($rz));

// Работать только с товарами, у которых включена разбивка веса
        if ( $rz === 'да' ) {

            $ratio = fdv_ms_get_weight_ratio_for_product( $product_id );

            if ( $ratio <= 0 || $ratio == 1 ) {
                $i++;
                continue;
            }

            $stepsQty = isset( $positions[ $i ]['quantity'] ) ? (float) $positions[ $i ]['quantity'] : 0;
            $oldPrice = isset( $positions[ $i ]['price'] ) ? (float) $positions[ $i ]['price'] : 0;

            if ( $stepsQty > 0 ) {
                $qtyBase   = $stepsQty * $ratio;
                $priceBase = $oldPrice / $ratio;

                $positions[ $i ]['quantity'] = $qtyBase;
                $positions[ $i ]['price']    = (int) round( $priceBase );
            }
        }


        $i++;
    }

    return $positions;
}


add_filter('wms_order_product_action', 'fdv_ms_add_delivery_total', 20, 2);

/**
 * Delivery total.
 *
 * @param $positions
 * @param $Order
 *
 * @return string
 */
function fdv_ms_add_delivery_total($positions, WC_Order $Order) {

	$order_fees = $Order->get_items('fee');


	if(!$order_fees){
		return $positions;
	}


	foreach ($order_fees as $order_fee){
		if($order_fee->get_name() === 'Доставка' && $order_fee->get_total() > 0){
			$positions[] = array(
				"quantity" => 1,
				"price" => $order_fee->get_total() * 100,
				"assortment" => array(
					"meta" => array(
						"href" =>  "https://api.moysklad.ru/api/remap/1.2/entity/service/bca82cda-cfaa-11ee-0a80-0d920004a1bb",
						"type" => 'service',
						"mediaType" => "application/json"
					)
				)
			);
		}

	}

	return $positions;
}


add_filter('wms_order_action', 'fdv_ms_store', 10, 2);

/**
 * Delivery address.
 *
 * @param $customerOrder
 * @param $Order
 *
 * @return string
 */
function fdv_ms_store($customerOrder, WC_Order $Order) {

    $store = $Order->get_meta('billing_point');

    if(!$store) {
        $local = $Order->get_meta('billing_samoviziv');
        $delivery = $Order->get_meta('billing_sss');

        $dataMapping = [
            'Эгершельд, Верхнепортовая, 41в' => '7c0dc9ce-ce1e-11ea-0a80-09ca000e5e93',
            'Реми-Сити (ул. Народный пр-т, 20)' => 'b24e4c35-9609-11eb-0a80-0d0d008550c2',
            'ТЦ «Море», Гипермаркет (ул. Некрасовская, 49а)' => 'cab1caa9-da10-11eb-0a80-07410026c356',
            'ул. Тимирязева, 31 строение 1 (район Спутник)' => 'a99d6fdf-0970-11ed-0a80-0ed600075845',
            'ТЦ Москва, 1-й этаж (ул. Суханова, 52)' => '9c9dfcc4-733f-11ec-0a80-0da1013a560d',
            'Находка, Проспект мира, 65/1' => '149a2219-9003-11ef-0a80-14a00002d2a5',
            'Океанский проспект, 108' => '076fd75d-aa46-11f0-0a80-16ae0000467c',

            'Эгершельд' => '7c0dc9ce-ce1e-11ea-0a80-09ca000e5e93',
            'РусскийДальний' => '7c0dc9ce-ce1e-11ea-0a80-09ca000e5e93',
            'РусскийСредний' => '7c0dc9ce-ce1e-11ea-0a80-09ca000e5e93',
            'РусскийБлижний' => '7c0dc9ce-ce1e-11ea-0a80-09ca000e5e93',
            'Реми-Сити' => 'b24e4c35-9609-11eb-0a80-0d0d008550c2',
            'ГринМаркет ТЦ Море' => 'cab1caa9-da10-11eb-0a80-07410026c356',
            'Космос' => 'a99d6fdf-0970-11ed-0a80-0ed600075845',
            'Артём' => 'a99d6fdf-0970-11ed-0a80-0ed600075845',
            'Трудовое' => 'a99d6fdf-0970-11ed-0a80-0ed600075845',
            'ДеФризДальний' => 'a99d6fdf-0970-11ed-0a80-0ed600075845',
            'ДеФризБлижний' => 'a99d6fdf-0970-11ed-0a80-0ed600075845',
            'Шамора' => 'a99d6fdf-0970-11ed-0a80-0ed600075845',
            'Щитовая' => 'a99d6fdf-0970-11ed-0a80-0ed600075845',
            'Уссурийск' => '9c9dfcc4-733f-11ec-0a80-0da1013a560d',
            'Чкалова' => '028e05a7-b4fa-11ee-0a80-1198000442be',
            'Находка' => '149a2219-9003-11ef-0a80-14a00002d2a5',
            'НаходкаЦентр' => '149a2219-9003-11ef-0a80-14a00002d2a5',
            'НаходкаАстафьево' => '149a2219-9003-11ef-0a80-14a00002d2a5',
            'НаходкаВрангель' => '149a2219-9003-11ef-0a80-14a00002d2a5',
            'НаходкаСевер' => '149a2219-9003-11ef-0a80-14a00002d2a5',
            'НаходкаДальняя' => '149a2219-9003-11ef-0a80-14a00002d2a5',
            'Океанский проспект 108' => '076fd75d-aa46-11f0-0a80-16ae0000467c',
        ];

        $store = null;

        foreach ($dataMapping as $storeName => $storeUuid) {
            if ($local === trim($storeName) || $delivery === trim($storeName)) {
                $store = $storeUuid;
                break;
            }
        }

    }

	if(!$store){
		return $customerOrder;
	}

	$customerOrder['store'] = array(
		"meta" => array(
			"href" => 'https://api.moysklad.ru/api/remap/1.2/entity/store/' . $store,
			"type" => "store",
			"mediaType" => "application/json"
		)
	);


	return $customerOrder;
}

/**
 * Определяет шаг веса для товара:
 *  - 0.1 кг для категорий:
 *      "мясные деликатесы"
 *      "колбасы"
 *      "сыры"
 *      "сладости и десерты"
 *      "орехи, сухофрукты, снеки"
 *  - 1 кг для остальных весовых
 *
 * @param int $product_id
 * @return float
 */
function fdv_ms_get_weight_ratio_for_product( $product_id ) {

    // Всегда работаем по parent (если вдруг сюда прилетит variation_id)
    $p = wc_get_product( $product_id );
    if ( $p && $p->is_type('variation') ) {
        $product_id = $p->get_parent_id();
    }

    $rz = function_exists('get_field')
        ? (string) get_field('razbivka_vesa', $product_id)
        : (string) get_post_meta($product_id, 'razbivka_vesa', true);

    $rz = mb_strtolower(trim($rz));

    if ( $rz !== 'да' ) {
        return 1;
    }

    // 2) Категории, для которых шаг = 0.1 кг
    $cats_01 = array(
        'мясные деликатесы',
        'колбасные изделия',
        'молочная продукция',
        'сладости и десерты',
        'орехи, сухофрукты, снеки',
        'домашние и ремесленные сыры',
        'твердые и полутвердые сыры',
        'творожные сыры',
        'сыры марко мельпиньяно',
    );

    // Только весовые товары из этих категорий → 0.1 кг
    if ( fdv_ms_product_in_categories_recursive( $product_id, $cats_01 ) ) {
        return 0.1;
    }

    // Все остальные весовые → шаг 1 кг
    return 1;
}

/**
 * Весовой ли товар (по твоей "razbivka_vesa" = да/нет).
 */
function fdv_ya_is_catch_weight(int $product_id): bool {
    $p = wc_get_product($product_id);
    if ($p && $p->is_type('variation')) $product_id = $p->get_parent_id();

    $rz = function_exists('get_field')
        ? (string)get_field('razbivka_vesa', $product_id)
        : (string)get_post_meta($product_id, 'razbivka_vesa', true);

    return (mb_strtolower(trim($rz)) === 'да');
}

/**
 * Квант (шаг) для ВЕСОВЫХ товаров в кг по твоей таблице.
 * Если НЕ весовой — возвращаем 0 (не используется).
 */
function fdv_ya_quantum_kg_by_table(int $product_id): float {
    // parent для вариаций
    $p = wc_get_product($product_id);
    if ($p && $p->is_type('variation')) $product_id = $p->get_parent_id();

    if (!fdv_ya_is_catch_weight($product_id)) {
        return 0.0;
    }

    // 1) ручной override (если используешь)
    $manual = get_post_meta($product_id, 'yandex_quant', true);
    $manual_unit = get_post_meta($product_id, 'yandex_quant_unit', true); // ожидаем 'g'
    if ($manual !== '' && $manual_unit === 'g' && is_numeric($manual)) {
        $g = (int)$manual;
        return max(0.001, $g / 1000.0);
    }

    // 2) ТВОЯ ТАБЛИЦА (заполни точно по нужным категориям)
    // ключи = названия категорий (как в WP), значения = квант в кг
    $map = [
        'домашние и ремесленные сыры' => 0.3,
        'колбасные изделия'           => 0.5,
        'мясные деликатесы'           => 0.5,
        'орехи, сухофрукты, снеки'    => 0.5,
        'сладости и десерты'          => 0.5,
        // если есть другие категории с 0.3/0.5 — добавь
    ];

    foreach ($map as $cat_name => $qkg) {
        if (fdv_ms_product_in_categories_recursive($product_id, [$cat_name])) {
            return (float)$qkg;
        }
    }

    // 3) дефолт для весовых, если не попали в список
    return 1.0;
}
/**
 * Measure для ЯЕ.
 * Возвращает массив measure в формате, который ты используешь (unit/value/quantum).
 */
function fdv_ya_build_measure(int $product_id): array {
    $product = wc_get_product($product_id);
    if (!$product) {
        // безопасный дефолт
        return ['unit' => 'GRM', 'value' => 1000, 'quantum' => 1.0];
    }

    // parent для вариаций
    if ($product->is_type('variation')) {
        $product_id = $product->get_parent_id();
        $product = wc_get_product($product_id) ?: $product;
    }

    $is_weight = fdv_ya_is_catch_weight($product_id);

    if ($is_weight) {
        $qkg = fdv_ya_quantum_kg_by_table($product_id);   // 0.3 / 0.5 / 1.0
        $qkg = ($qkg > 0 ? $qkg : 1.0);

        return [
            'unit'    => 'GRM',
            'value'   => 1000,          // базовая = 1 кг
            'quantum' => (float)$qkg,   // квант в кг
        ];
    }

    // штучный: "вес одной штуки" в граммах
    $weight_kg = (float)$product->get_weight(); // Woo weight обычно в кг
    $weight_g  = (int)round(($weight_kg > 0 ? $weight_kg : 1.0) * 1000);

    return [
        'unit'    => 'GRM',
        'value'   => max(1, $weight_g),
        'quantum' => 1.0,
    ];
}
/**
 * Вариант B: ЯЕ присылает quantity уже в КГ для весового товара.
 */
function fdv_ya_order_qty_to_wc_already_kg(float $ya_quantity, int $product_id): float {
    if ($ya_quantity <= 0) return 0.0;
    return $ya_quantity;
}

/**
 * Проверка: находится ли товар в одной из категорий (по имени) или её дочерних.
 *
 * @param int   $product_id
 * @param array $category_names
 * @return bool
 */
function fdv_ms_product_in_categories_recursive( $product_id, array $category_names ) {

    $product_terms = get_the_terms( $product_id, 'product_cat' );
    if ( empty( $product_terms ) || is_wp_error( $product_terms ) ) {
        return false;
    }

    // Собираем ID всех категорий из списка (включая их детей)
    $all_ids = array();

    foreach ( $category_names as $cat_name ) {
        $term = get_term_by( 'name', $cat_name, 'product_cat' );
        if ( ! $term || is_wp_error( $term ) ) {
            continue;
        }

        $all_ids[] = (int) $term->term_id;

        // Добавляем всех потомков
        $children = get_terms( array(
            'taxonomy'   => 'product_cat',
            'hide_empty' => false,
            'parent'     => $term->term_id,
        ) );

        if ( ! empty( $children ) && ! is_wp_error( $children ) ) {
            foreach ( $children as $child ) {
                $all_ids[] = (int) $child->term_id;
            }
        }
    }

    if ( empty( $all_ids ) ) {
        return false;
    }

    foreach ( $product_terms as $t ) {
        if ( in_array( (int) $t->term_id, $all_ids, true ) ) {
            return true;
        }
    }

    return false;
}
add_filter( 'wms_order_action', 'fdv_ms_add_qpromo_to_comment', 9999, 2 );

function fdv_ms_add_qpromo_to_comment( $customerOrder, WC_Order $Order ) {

    // 1) основной источник – мета заказа
    $promo = $Order->get_meta( 'q_promocode' );

    // 2) запасной вариант – купоны, начинающиеся с Q
    if ( ! $promo ) {
        $codes = $Order->get_coupon_codes();
        if ( ! empty( $codes ) ) {
            $codes = array_filter( $codes, function( $code ) {
                return preg_match( '/^Q/i', $code );
            } );
            if ( ! empty( $codes ) ) {
                $promo = implode( ', ', $codes );
            }
        }
    }

    if ( ! $promo ) {
        return $customerOrder;
    }

    $line = 'Промокод: ' . $promo;

    if ( ! empty( $customerOrder['description'] ) ) {
        $customerOrder['description'] .= "\n" . $line;
    } else {
        $customerOrder['description'] = $line;
    }

    return $customerOrder;
}
/**
 * ==========================
 *  YANDEX EDA: META + HELPERS + REST
 * ==========================
 */

/**
 * Метабокс на экране товара: "Яндекс Еда".
 */
add_action( 'add_meta_boxes', 'fdv_yandex_add_meta_box' );
function fdv_yandex_add_meta_box() {
    add_meta_box(
        'fdv_yandex_eda_box',
        'Яндекс Еда',
        'fdv_yandex_meta_box_html',
        'product',
        'side',
        'default'
    );
}

/**
 * HTML внутри метабокса.
 *
 * @param WP_Post $post
 */
function fdv_yandex_meta_box_html( $post ) {
    wp_nonce_field( 'fdv_yandex_eda_save', 'fdv_yandex_eda_nonce' );

    $enabled = get_post_meta( $post->ID, 'yandex_eda_enabled', true );
    $price   = get_post_meta( $post->ID, 'yandex_price', true );

    if ($price === '' || $price === null) {
        $p = wc_get_product($post->ID);
        if ($p instanceof WC_Product) {
            $price = fdv_yandex_get_price_for_eda($p);
        }
    }

    ?>
    <p>
        <label>
            <input type="checkbox" name="fdv_yandex_eda_enabled" value="1"
                <?php checked( $enabled, '1' ); ?> />
            Выгружать в Яндекс Еду
        </label>
    </p>

    <p>
        <label for="fdv_yandex_price">
            Цена для Яндекс Еды (руб. за базовую единицу)
        </label>
        <input type="number"
               step="0.01"
               min="0"
               style="width: 100%;"
               id="fdv_yandex_price"
               name="fdv_yandex_price"
               value="<?php echo esc_attr( $price ); ?>" />
        <small>Если пусто — берётся обычная цена WooCommerce.</small>
    </p>
    <?php
}

/**
 * Сохранение меты при сохранении товара.
 */
add_action( 'save_post_product', 'fdv_yandex_save_meta', 10, 2 );
function fdv_yandex_save_meta( $post_id, $post ) {

    // Автосейвы/ревизии не трогаем
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }
    if ( $post->post_type !== 'product' ) {
        return;
    }

    // Проверка nonce
    if ( ! isset( $_POST['fdv_yandex_eda_nonce'] ) ||
        ! wp_verify_nonce( $_POST['fdv_yandex_eda_nonce'], 'fdv_yandex_eda_save' ) ) {
        return;
    }

    // Права
    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }

    // Флаг "Выгружать в Яндекс Еду"
    $enabled = isset( $_POST['fdv_yandex_eda_enabled'] ) ? '1' : '0';
    update_post_meta( $post_id, 'yandex_eda_enabled', $enabled );

    // Цена для Яндекс Еды
    if ( isset( $_POST['fdv_yandex_price'] ) && $_POST['fdv_yandex_price'] !== '' ) {
        $price = floatval( str_replace( ',', '.', $_POST['fdv_yandex_price'] ) );
        if ( $price < 0 ) {
            $price = 0;
        }
        update_post_meta( $post_id, 'yandex_price', $price );
    } else {
        // Можно либо очистить, либо оставить старое значение.
        delete_post_meta( $post_id, 'yandex_price' );
    }
}

/**
 * Проверка: товар включён в выгрузку Яндекс Еды.
 *
 * @param int $product_id
 * @return bool
 */
function fdv_yandex_is_enabled( $product_id ) {
    return get_post_meta( $product_id, 'yandex_eda_enabled', true ) === '1';
}

/**
 * Квант для Яндекс Еды в граммах.
 * Использует fdv_ms_get_weight_ratio_for_product(), которая у тебя уже есть выше.
 *
 * Примеры:
 *  - ratio = 0.1 кг → 100 г
 *  - ratio = 0.5 кг → 500 г
 *  - ratio = 1 кг   → 1000 г
 *
 * @param int $product_id
 * @return int
 */
function fdv_yandex_get_quant($product_id): array {
    // 1) ручной override
    $q = get_post_meta($product_id, 'yandex_quant', true);
    $u = get_post_meta($product_id, 'yandex_quant_unit', true);
    if ($q !== '' && $u) {
        return ['value' => (int)$q, 'unit' => $u]; // g|pcs
    }

    // 2) ЯЕ логика
    if (fdv_ya_is_catch_weight((int)$product_id)) {
        $qkg = fdv_ya_quantum_kg_by_table((int)$product_id); // 0.3/0.5/1.0
        $grams = (int) round(max($qkg, 0.001) * 1000);
        return ['value' => $grams, 'unit' => 'g'];
    }

    return ['value' => 1, 'unit' => 'pcs'];
}


/**
 * Получить цену для Яндекс Еды по товару.
 * Приоритет:
 *  1) post_meta 'yandex_price'
 *  2) стандартная цена товара
 *
 * @param WC_Product $product
 * @return float
 */
/**
 * Цена для Яндекс Еды:
 *  1) ручная meta yandex_price (если задана)
 *  2) МойСклад: тип цены "Яндекс еда" по SKU (article/code)
 *  3) Woo price
 */
function fdv_yandex_get_price_for_eda( WC_Product $product ) : float {

    // всегда работаем по parent для вариаций
    $product_id = (int)$product->get_id();
    if ($product->is_type('variation')) {
        $parent_id = (int)$product->get_parent_id();
        if ($parent_id > 0) {
            $p = wc_get_product($parent_id);
            if ($p instanceof WC_Product) {
                $product = $p;
                $product_id = $parent_id;
            }
        }
    }

    // 1) ручной override
    $y_price = get_post_meta($product_id, 'yandex_price', true);
    if ($y_price !== '' && $y_price !== null) {
        $v = (float)str_replace(',', '.', (string)$y_price);
        return max(0.0, $v);
    }

    // 2) МС цена по типу "Яндекс еда"
    $sku = (string)$product->get_sku();
    if ($sku !== '') {
        try {
            $row = fdv_ms_find_product_row_by_sku($sku);
            if (is_array($row)) {
                $ms_price = fdv_ms_extract_sale_price_rub_by_pricetype_name($row, 'Яндекс еда');
                if ($ms_price !== null) {
                    return max(0.0, (float)$ms_price);
                }
            }
        } catch (Throwable $e) {
            // молча падаем на Woo цену
        }
    }

    // 3) Woo
    return 0.0;
}
/**
 * Собрать одну позицию каталога для Яндекс Еды.
 * Дальше этот массив маппишь на точный формат JSON, который требует Яндекс.
 *
 * @param WC_Product $product
 * @return array
 */
function fdv_yandex_build_brand_item_from_product(WC_Product $product): array
{
    // 0) Нормализуем ID: всегда работаем по parent для вариаций
    $product_id = (int) $product->get_id();
    if ($product->is_type('variation')) {
        $parent_id = (int) $product->get_parent_id();
        if ($parent_id > 0) {
            $product_id = $parent_id;
            $parent = wc_get_product($parent_id);
            if ($parent instanceof WC_Product) {
                $product = $parent;
            }
        }
    }

    // 1) categoryId (первый term, как у тебя; при желании можно выбрать "самый глубокий")
    $categoryId = '';
    $terms = get_the_terms($product_id, 'product_cat');
    if (!empty($terms) && !is_wp_error($terms)) {
        $categoryId = (string) $terms[0]->term_id;
    }

    // 2) isCatchWeight + measure: ЕДИНЫЙ источник правды
    // Требует твоих новых функций:
    // - fdv_ya_is_catch_weight($product_id)
    // - fdv_ya_build_measure($product_id)
    $isCatchWeight = function_exists('fdv_ya_is_catch_weight')
        ? (bool) fdv_ya_is_catch_weight($product_id)
        : (function_exists('get_field')
            ? (mb_strtolower(trim((string) get_field('razbivka_vesa', $product_id))) === 'да')
            : (mb_strtolower(trim((string) get_post_meta($product_id, 'razbivka_vesa', true))) === 'да')
        );

    $measure = function_exists('fdv_ya_build_measure')
        ? fdv_ya_build_measure($product_id)
        : [
            'unit'    => 'GRM',
            'value'   => 1000,
            'quantum' => 1.0,
        ];

    // 3) volume для логистики (как у тебя)
    $volume = [
        'unit'  => 'DMQ',
        'value' => 1,
    ];

    $barcode_value = (string) get_post_meta($product_id, 'yandex_barcode', true);
    if ($barcode_value === '') {
        $barcode_value = (string) $product->get_sku();
    }

    $barcode_value = preg_replace('/\D+/', '', $barcode_value);

    if (strlen($barcode_value) !== 13) {
        $barcode_value = fdv_ya_make_unique_13_digits($product_id);
    }

    $barcode = [
        'type'           => 'ean13',
        'value'          => $barcode_value,
        'weightEncoding' => 'none',
        'values'         => [$barcode_value],
    ];

    $images = [];
    $order_counter = 0;

    $main_img_id = (int) $product->get_image_id();
    if ($main_img_id) {
        $img_url = wp_get_attachment_url($main_img_id);
        if ($img_url) {
            $images[] = [
                'url'   => (string) $img_url,
                'order' => $order_counter++,
            ];
        }
    }

    $gallery_ids = $product->get_gallery_image_ids();
    if (is_array($gallery_ids)) {
        foreach ($gallery_ids as $img_id) {
            $img_url = wp_get_attachment_url((int) $img_id);
            if ($img_url) {
                $images[] = [
                    'url'   => (string) $img_url,
                    'order' => $order_counter++,
                ];
            }
        }
    }

    // 6) description
    $description = [
        'vendorCountry' => 'Россия',
        'vendorName'    => (string) get_bloginfo('name'),
    ];

    $short = trim((string) $product->get_short_description());
    if ($short !== '') {
        $description['composition'] = $short;
    }

    $expiresIn = trim((string) get_post_meta($product_id, '_expires_in', true));
    if ($expiresIn !== '') {
        $description['expiresIn'] = $expiresIn;
    }

    $general = trim((string) $product->get_description());
    if ($general !== '') {
        $description['general'] = $general;
    }

    $nutr = trim((string) get_post_meta($product_id, '_nutritional_value', true));
    if ($nutr !== '') {
        $description['nutritionalValue'] = $nutr;
    }

    $pack = trim((string) get_post_meta($product_id, '_package_info', true));
    if ($pack !== '') {
        $description['packageInfo'] = $pack;
    }

    $purpose = trim((string) get_post_meta($product_id, '_purpose', true));
    if ($purpose !== '') {
        $description['purpose'] = $purpose;
    }

    $storage = trim((string) get_post_meta($product_id, '_storage_requirements', true));
    if ($storage !== '') {
        $description['storageRequirements'] = $storage;
    }


    // 7) vendorCode: SKU или ID
    $sku = (string) $product->get_sku();
    $vendorCode = $sku !== '' ? $sku : (string) $product_id;

    // 8) итог
    return [
        'id'                  => (string) $product_id,
        'name'                => (string) $product->get_name(),
        'vendorCode'          => $vendorCode,
        'categoryId'          => $categoryId,
        'barcode'             => $barcode,
        'description'         => $description,
        'images'              => $images,
        'isCatchWeight'       => $isCatchWeight,
        'measure'             => $measure,
        'volume'              => $volume,
        'vat'                 => -1,
        'labels'              => [],
        'markingType'         => 'default',
        'packageQuantity'     => 1,
        'pickupPointsGroupIds'=> [],
    ];
}
/**
 * Вернёт стабильный 13-значный числовой код для товара.
 * Не гарантирует реальную контрольную сумму EAN13, но по спекам ЯЕ допускается "рандомное число".
 */
function fdv_ya_make_unique_13_digits(int $product_id): string {
    // Берём хэш, чтобы получить много цифр, и делаем детерминированно
    $src = home_url('/') . '|' . $product_id;
    $hash = substr(md5($src), 0, 12); // 12 hex-символов

    // Превращаем hex -> число (0..)
    $num = base_convert($hash, 16, 10); // строка
    $num = preg_replace('/\D+/', '', (string)$num);

    // Делаем ровно 12 цифр (паддинг слева нулями, обрезка справа)
    $num12 = str_pad(substr($num, 0, 12), 12, '0', STR_PAD_LEFT);

    // 13-я цифра — просто (product_id % 10) для разнообразия
    $last = (string) ($product_id % 10);

    return $num12 . $last; // 13 цифр
}

function fdv_yandex_get_quant_grams(int $product_id): int {
    // 1) ручной override в граммах
    $manual_quant = get_post_meta($product_id, 'yandex_quant', true);
    $manual_unit  = get_post_meta($product_id, 'yandex_quant_unit', true);
    if ($manual_quant !== '' && $manual_unit === 'g' && is_numeric($manual_quant)) {
        return (int) $manual_quant;
    }

    // 2) весовой? -> граммы по таблице
    if (fdv_ya_is_catch_weight($product_id)) {
        $qkg = fdv_ya_quantum_kg_by_table($product_id);
        return (int) round(max($qkg, 0.001) * 1000);
    }

    // 3) штучный -> вес штуки
    $product = wc_get_product($product_id);
    if (!$product) return 1000;

    $wkg = (float) $product->get_weight();
    return (int) round(($wkg > 0 ? $wkg : 1.0) * 1000);
}
function fdv_ya_order_qty_to_wc(float $ya_quantity, int $product_id): float {
    if ($ya_quantity <= 0) return 0.0;

    if (!fdv_ya_is_catch_weight($product_id)) {
        return $ya_quantity; // штучный
    }

    $qkg = fdv_ya_quantum_kg_by_table($product_id);
    if ($qkg <= 0) $qkg = 1.0;

    // quantity (от ЯЕ) = количество квантов
    return $ya_quantity * $qkg;
}

function fdv_yandex_nomenclature_prices(WP_REST_Request $request) {
    $place_id = $request->get_param('placeId');
    $limit    = (int) $request->get_param('limit') ?: 5000;
    $offset   = (int) $request->get_param('offset') ?: 0;

    $args = [
        'post_type'      => 'product',
        'post_status'    => 'publish',
        'posts_per_page' => $limit,
        'offset'         => $offset,
        'meta_query'     => [
            ['key' => 'yandex_eda_enabled', 'value' => '1'],
            [
                'key'     => 'yandex_price',
                'value'   => '0',
                'compare' => '>',
                'type'    => 'NUMERIC',
            ],
        ],
        'fields'         => 'ids',
        'no_found_rows'  => false,
    ];

    $q = new WP_Query($args);

    $prices = [];
    foreach ($q->posts as $product_id) {
        $price = (float) get_post_meta($product_id, 'yandex_price', true);
        if ($price <= 0) continue;

        $prices[] = [
            'id'    => (string) $product_id,
            'price' => (int) round($price),
        ];
    }

    return new WP_REST_Response([
        'items'      => $prices,
        'totalCount' => (int) $q->found_posts,
    ], 200);
}
/**
 * Каталог для Яндекс Еды:
 * выбираем все опубликованные товары, у которых yandex_eda_enabled = 1.
 *
 * @return array
 */
function fdv_yandex_get_catalog_for_eda() {
    $args = array(
        'post_type'      => 'product',
        'posts_per_page' => -1,
        'post_status'    => 'publish',
        'meta_query'     => array(
            array(
                'key'   => 'yandex_eda_enabled',
                'value' => '1',
            ),
        ),
        'fields' => 'ids',
    );

    $query = new WP_Query( $args );
    if ( ! $query->have_posts() ) {
        return array();
    }

    $items = array();

    foreach ( $query->posts as $product_id ) {
        $product = wc_get_product( $product_id );
        if ( ! $product || ! $product->is_in_stock() ) {
            continue;
        }

        $items[] = fdv_yandex_build_brand_item_from_product( $product );
    }

    wp_reset_postdata();

    return $items;
}

/**
 * REST-эндпоинт:
 *  GET /wp-json/fdv/v1/yandex-catalog
 *
 * Можно указать его как URL фида в интеграции Яндекс Еды
 * (или дергать его своим скриптом интеграции).
 */
add_action( 'rest_api_init', function () {
    register_rest_route(
        'fdv/v1',
        '/yandex-catalog',
        array(
            'methods'             => 'GET',
            'callback'            => 'fdv_yandex_catalog_endpoint',
            'permission_callback' => '__return_true', // если фид должен быть публичным
        )
    );
} );

/**
 * Callback для REST-эндпоинта.
 *
 * @param WP_REST_Request $request
 * @return WP_REST_Response
 */
function fdv_yandex_catalog_endpoint( WP_REST_Request $request ) {

    if ( ! function_exists( 'fdv_yandex_get_catalog_for_eda' ) ) {
        return new WP_REST_Response( array( 'error' => 'helpers_not_loaded' ), 500 );
    }

    $items = fdv_yandex_get_catalog_for_eda();

    // Тут, если нужно, переименуешь ключи/структуру под точный формат Яндекса.
    return new WP_REST_Response( $items, 200 );
}

/**
 * =======================================
 *  YANDEX EDA: OAUTH + NOMEMCLATURE + ORDERS
 * =======================================
 */

/**
 * Настройки OAuth для Яндекс Еды.
 * Клиент и секрет тебе должны выдать / согласовать.
 */

if ( ! defined( 'FDV_YA_CLIENT_ID' ) ) {
    define( 'FDV_YA_CLIENT_ID', 'ferma_yandex_eda' );
}

if ( ! defined( 'FDV_YA_CLIENT_SECRET' ) ) {
    define( 'FDV_YA_CLIENT_SECRET', 'vP9Xc7sN3qL8zD1mK4rT6wY2bF0hG5j' );
}

if ( ! defined( 'FDV_YA_TOKEN_TTL' ) ) {
    define( 'FDV_YA_TOKEN_TTL', 3600 ); // 1 час
}


/**
 * Генерация и сохранение access_token.
 *
 * @return string
 */
function fdv_yandex_generate_token() {
    $token = wp_generate_password( 32, false, false );

    update_option( 'fdv_yandex_token', array(
        'token'   => $token,
        'expires' => time() + FDV_YA_TOKEN_TTL,
    ) );

    return $token;
}

/**
 * Получение текущего токена из опций.
 *
 * @return array|null
 */
function fdv_yandex_get_stored_token() {
    $data = get_option( 'fdv_yandex_token' );
    if ( ! is_array( $data ) ) {
        return null;
    }
    if ( empty( $data['token'] ) || empty( $data['expires'] ) ) {
        return null;
    }
    if ( time() >= (int) $data['expires'] ) {
        return null;
    }
    return $data;
}

/**
 * Достаём access_token из запроса (Authorization: Bearer XXX или ?access_token=XXX).
 *
 * @param WP_REST_Request $request
 * @return string|null
 */
function fdv_yandex_get_token_from_request( WP_REST_Request $request ) {
    $auth = $request->get_header( 'authorization' );
    if ( $auth && stripos( $auth, 'bearer ' ) === 0 ) {
        return trim( substr( $auth, 7 ) );
    }

    $token = $request->get_param( 'access_token' );
    if ( $token ) {
        return (string) $token;
    }

    return null;
}

/**
 * Проверка авторизации Яндекс Еды.
 *
 * @param WP_REST_Request $request
 * @return true|WP_Error
 */
function fdv_yandex_check_auth( WP_REST_Request $request ) {
    $stored = fdv_yandex_get_stored_token();
    if ( ! $stored ) {
        return new WP_Error( 'unauthorized', 'No valid token stored', array( 'status' => 401 ) );
    }

    $token = fdv_yandex_get_token_from_request( $request );
    if ( ! $token || $token !== $stored['token'] ) {
        return new WP_Error( 'unauthorized', 'Invalid access token', array( 'status' => 401 ) );
    }

    return true;
}

/**
 * Обёртка для permission_callback.
 *
 * @param WP_REST_Request $request
 * @return bool|WP_Error
 */
function fdv_yandex_permission( WP_REST_Request $request ) {
    $check = fdv_yandex_check_auth( $request );
    if ( is_wp_error( $check ) ) {
        return $check;
    }
    return true;
}

/**
 * Регистрация REST-эндпоинтов под Яндекс Еду.
 *
 * URL-и будут:
 *  - /wp-json/security/oauth/token
 *  - /wp-json/nomenclature/composition
 *  - /wp-json/nomenclature/{placeId}/composition
 *  - /wp-json/nomenclature/{placeId}/availability
 *  - /wp-json/order        (POST)
 *  - /wp-json/order/{orderId}
 *  - /wp-json/order/{orderId}/status
 */
add_action( 'rest_api_init', function () {

    // OAuth
    register_rest_route(
        'security',
        '/oauth/token',
        array(
            'methods'             => 'POST',
            'callback'            => 'fdv_yandex_oauth_token_endpoint',
            'permission_callback' => '__return_true',
        )
    );

    // Номенклатура (общая)
    register_rest_route(
        'nomenclature',
        '/composition',
        array(
            'methods'             => 'GET',
            'callback'            => 'fdv_yandex_nomenclature_composition',
            'permission_callback' => 'fdv_yandex_permission',
        )
    );

    // Номенклатура по placeId
    register_rest_route(
        'nomenclature',
        '/(?P<placeId>[\w\-]+)/composition',
        array(
            'methods'             => 'GET',
            'callback'            => 'fdv_yandex_nomenclature_composition',
            'permission_callback' => 'fdv_yandex_permission',
        )
    );
    register_rest_route(
        'nomenclature',
        '/(?P<placeId>[\w\-]+)/prices',
        array(
            'methods'             => 'GET',
            'callback'            => 'fdv_yandex_nomenclature_prices',
            'permission_callback' => 'fdv_yandex_permission',
        )
    );
    // Остатки по placeId
    register_rest_route(
        'nomenclature',
        '/(?P<placeId>[\w\-]+)/availability',
        array(
            'methods'             => 'GET',
            'callback'            => 'fdv_yandex_nomenclature_availability',
            'permission_callback' => 'fdv_yandex_permission',
        )
    );

    register_rest_route(
        'order',
        '/create',
        [
            'methods' => 'POST',
            'callback' => 'fdv_yandex_order_create',
            'permission_callback' => 'fdv_yandex_permission',
        ]
    );

    // Получение заказа по ID
    register_rest_route(
        'order',
        '/(?P<orderId>[\w\-]+)',
        array(
            'methods'             => 'GET',
            'callback'            => 'fdv_yandex_order_get',
            'permission_callback' => 'fdv_yandex_permission',
        )
    );

    // Статус заказа
    register_rest_route(
        'order',
        '/(?P<orderId>[\w\-]+)/status',
        array(
            array(
                'methods'             => 'GET',
                'callback'            => 'fdv_yandex_order_get_status',
                'permission_callback' => 'fdv_yandex_permission',
            ),
            array(
                'methods'             => 'PUT',
                'callback'            => 'fdv_yandex_order_set_status',
                'permission_callback' => 'fdv_yandex_permission',
            ),
        )
    );
} );

function fdv_yandex_oauth_token_endpoint( WP_REST_Request $request ) {

    $body = $request->get_body_params();
    $json = $request->get_json_params();

    // Маскируем секреты в логах
    $mask_secret = function($arr) {
        if (!is_array($arr)) return $arr;
        if (isset($arr['client_secret'])) $arr['client_secret'] = '***';
        if (isset($arr['password']))      $arr['password']      = '***';
        return $arr;
    };

    yandex_log([
        'ct'        => $request->get_header('content-type'),
        'has_auth'  => $request->get_header('authorization') ? 1 : 0,
        'body'      => $mask_secret($body),
        'json'      => $mask_secret($json),
        'query'     => $request->get_query_params(),
        'method'    => $request->get_method(),
        'route'     => $request->get_route(),
    ], 'TOKEN_REQ');

    $params = $body;
    if (is_array($json) && !empty($json)) {
        $params = array_merge($params, $json);
    }

    $client_id     = $params['client_id'] ?? null;
    $client_secret = $params['client_secret'] ?? null;

    $auth = $request->get_header('authorization');
    if ($auth && stripos($auth, 'basic ') === 0) {
        $decoded = base64_decode(trim(substr($auth, 6)));
        if ($decoded && strpos($decoded, ':') !== false) {
            [$basic_id, $basic_secret] = explode(':', $decoded, 2);
            if (!$client_id)     $client_id = $basic_id;
            if (!$client_secret) $client_secret = $basic_secret;
        }
    }

    if ( $client_id !== FDV_YA_CLIENT_ID || $client_secret !== FDV_YA_CLIENT_SECRET ) {
        yandex_log([
            'got_client_id'     => $client_id,
            'got_secret'        => $client_secret,
            'expected_secret'   => FDV_YA_CLIENT_SECRET,
        ], 'TOKEN_401');
        return new WP_REST_Response([
            'error' => 'invalid_client',
            'error_description' => 'Invalid client_id or client_secret',
        ], 401);
    }

    $token_data = fdv_yandex_get_stored_token();
    if ( ! $token_data ) {
        fdv_yandex_generate_token();
        $token_data = fdv_yandex_get_stored_token();
    }

    yandex_log([
        'expires_in' => (int) max(0, $token_data['expires'] - time()),
    ], 'TOKEN_OK');

    return new WP_REST_Response([
        'access_token' => $token_data['token'],
        'expires_in'   => (int) max(0, $token_data['expires'] - time()),
    ], 200);
}


function fdv_yandex_nomenclature_composition( WP_REST_Request $request ) {

    // placeId в этом методе по спеке может быть, но в ответ НЕ возвращается
    $place_id = (string) $request->get_param('placeId');

    // пагинация
    $limit  = (int) $request->get_param('limit');
    $offset = (int) $request->get_param('offset');
    if ($limit <= 0)  $limit = 5000;
    if ($limit > 5000) $limit = 5000;
    if ($offset < 0) $offset = 0;

    // 1) categories: список категорий (id/name/parentId)
    $categories = function_exists('fdv_yandex_get_categories_for_composition')
        ? fdv_yandex_get_categories_for_composition($place_id)
        : [];

    // 2) items: BrandItems (id/name/categoryId/barcode/images/isCatchWeight/measure/vat...)
    $res = function_exists('fdv_yandex_get_items_for_composition')
        ? fdv_yandex_get_items_for_composition($place_id, $limit, $offset)
        : null;

    if (!is_array($res) || !isset($res['items'], $res['totalCount'])) {
        return new WP_REST_Response([
            'error' => 'items_builder_not_found_or_invalid',
            'hint'  => 'Implement fdv_yandex_get_items_for_composition($placeId, $limit, $offset) => [items=>[], totalCount=>int]',
        ], 500);
    }

    $items      = is_array($res['items']) ? $res['items'] : [];
    $totalCount = (int) $res['totalCount'];

    // ВАЖНО: в ответе должны быть categories/items (+ totalCount при пагинации)
    $response = [
        'categories' => $categories,
        'items'      => $items,
        'totalCount' => $totalCount,
    ];

    return new WP_REST_Response($response, 200);
}
function fdv_yandex_get_categories_for_composition(string $place_id = ''): array {

    $terms = get_terms([
        'taxonomy'   => 'product_cat',
        'hide_empty' => false,      // важно: чтобы родители не пропадали
        'pad_counts' => true,
    ]);

    if (is_wp_error($terms) || empty($terms)) {
        return [];
    }

    $categories = [];
    foreach ($terms as $t) {
        $row = [
            'id'   => (string) $t->term_id,
            'name' => (string) $t->name,
        ];

        $parent = (int) ($t->parent ?? 0);
        if ($parent > 0) {
            $row['parentId'] = (string) $parent;
        }

        $categories[] = $row;
    }

    return $categories;
}

function fdv_yandex_get_items_for_composition(string $place_id, int $limit, int $offset): array {

    $args = [
        'post_type'      => 'product',
        'post_status'    => 'publish',
        'posts_per_page' => $limit,
        'offset'         => $offset,
        'meta_query'     => [
            [
                'key'   => 'yandex_eda_enabled',
                'value' => '1',
            ],
        ],
        'fields' => 'ids',
        'no_found_rows' => false,
    ];

    $q = new WP_Query($args);

    $items = [];
    foreach ($q->posts as $product_id) {

        $product = wc_get_product($product_id);
        if (!$product) continue;

        // если надо — можно фильтровать по наличию
        // if (!$product->is_in_stock()) continue;

        $items[] = fdv_yandex_build_brand_item_from_product($product);
    }

    return [
        'items'      => $items,
        'totalCount' => (int)$q->found_posts,
    ];
}

function fdv_yandex_get_order_by_external_id( $external_id ) {
    $orders = wc_get_orders( array(
        'limit'      => 1,
        'meta_key'   => 'yandex_eda_order_id',
        'meta_value' => (string) $external_id,
        'orderby'    => 'date',
        'order'      => 'DESC',
    ) );

    if ( empty( $orders ) ) {
        return false;
    }

    return $orders[0];
}

function fdv_yandex_order_create(WP_REST_Request $request)
{
    $data = $request->get_json_params();

    yandex_log([
        'order_create_data' => $data,
        'ct' => $request->get_header('content-type'),
        'route' => $request->get_route(),
    ], 'ORDER_CREATE');

    // 1) Валидация входа
    $yandex_order_id = (string) ($data['id'] ?? '');
    $place_id        = (string) ($data['placeId'] ?? '');

    if ($yandex_order_id === '') {
        return new WP_REST_Response([
            'error' => [
                'code' => 400,
                'description' => 'Missing order ID',
            ],
        ], 400);
    }

    // 2) Дедупликация
    $existing_order = fdv_yandex_get_order_by_external_id($yandex_order_id);
    if ($existing_order instanceof WC_Order) {
        return new WP_REST_Response([
            'orderId' => $yandex_order_id,
            'status'  => fdv_yandex_map_status_to_yandex($existing_order->get_status()),
        ], 200);
    }

    // 3) Создаём заказ
    try {
        $order = wc_create_order();
        if (!($order instanceof WC_Order)) {
            throw new RuntimeException('Failed to create WooCommerce order');
        }

        // 3.1) Клиент
        $customer = is_array($data['customer'] ?? null) ? $data['customer'] : [];
        $name  = isset($customer['name'])  ? trim((string)$customer['name'])  : '';
        $phone = isset($customer['phone']) ? trim((string)$customer['phone']) : '';

        if ($name !== '')  $order->set_billing_first_name($name);
        if ($phone !== '') $order->set_billing_phone($phone);

        // 3.2) Товары
        $items = is_array($data['items'] ?? null) ? $data['items'] : [];
        if (empty($items)) {
            // ЯЕ иногда может прислать пусто — это лучше валить сразу
            return new WP_REST_Response([
                'error' => [
                    'code' => 400,
                    'description' => 'Missing items',
                ],
            ], 400);
        }

        foreach ($items as $row) {
            if (!is_array($row)) continue;

            $pid = (int) ($row['id'] ?? 0);
            $ya_qty = (float) ($row['quantity'] ?? 0);

            if ($pid <= 0 || $ya_qty <= 0) continue;

            $product = wc_get_product($pid);
            if (!($product instanceof WC_Product)) continue;

            // всегда parent для вариаций
            $base_pid = $product->is_type('variation') ? (int)$product->get_parent_id() : (int)$product->get_id();

            /**
             * ВАЖНО: выбрать стратегию:
             * - fdv_ya_order_qty_to_wc(): quantity от ЯЕ = число квантов (шагов)
             * - fdv_ya_order_qty_to_wc_already_kg(): quantity от ЯЕ уже в кг
             *
             * По умолчанию ставлю вариант "ШАГИ", потому что у тебя квант задаётся в measure.quantum.
             */
            if (function_exists('fdv_ya_order_qty_to_wc')) {
                $wc_qty = (float) fdv_ya_order_qty_to_wc($ya_qty, $base_pid);
            } else {
                // fallback: если функции ещё нет — хотя бы не использовать старый ratio 0.1/1
                $wc_qty = $ya_qty;
            }

            if ($wc_qty <= 0) continue;

            $order->add_product($product, $wc_qty);
        }

        // 3.3) Метаданные
        $order->update_meta_data('yandex_eda_order_id', $yandex_order_id);
        if ($place_id !== '') {
            $order->update_meta_data('yandex_eda_place_id', $place_id);
        }

        // 3.4) Итоги + статус
        $order->calculate_totals();
        $order->save();
        $order->update_status('processing', 'Заказ создан из Яндекс Еды');

        return new WP_REST_Response([
            'orderId' => $yandex_order_id,
            'status'  => 'accepted',
        ], 201);

    } catch (Throwable $e) {
        yandex_log([
            'err' => $e->getMessage(),
        ], 'ORDER_CREATE_ERR');

        return new WP_REST_Response([
            'error' => [
                'code' => 500,
                'description' => 'Failed to create order',
            ],
        ], 500);
    }
}

function fdv_yandex_order_get( WP_REST_Request $request ) {

    $external_id = (string) $request['orderId'];

    $order = fdv_yandex_get_order_by_external_id( $external_id );
    if ( ! $order ) {
        return new WP_REST_Response( array( 'error' => 'order_not_found' ), 404 );
    }

    $items = array();

    foreach ( $order->get_items() as $item_id => $item ) {
        $product = $item->get_product();
        if ( ! $product ) {
            continue;
        }

        $items[] = array(
            'id'       => (string) $product->get_id(),
            'name'     => $item->get_name(),
            'quantity' => (float) $item->get_quantity(),
            'price'    => (float) $order->get_item_total( $item, false ),
        );
    }

    $response = array(
        'external_id' => $external_id,
        'order_id'    => (int) $order->get_id(),
        'status'      => $order->get_status(),
        'status_mapped' => fdv_yandex_map_status_to_yandex( $order->get_status() ),
        'total'       => (float) $order->get_total(),
        'items'       => $items,
        'created_at'  => $order->get_date_created() ? $order->get_date_created()->date( DATE_ATOM ) : null,
    );

    return new WP_REST_Response( $response, 200 );
}

function fdv_yandex_order_get_status( WP_REST_Request $request ) {

    $external_id = (string) $request['orderId'];

    $order = fdv_yandex_get_order_by_external_id( $external_id );
    if ( ! $order ) {
        return new WP_REST_Response( array( 'error' => 'order_not_found' ), 404 );
    }

    $status_wc   = $order->get_status();
    $status_yand = fdv_yandex_map_status_to_yandex( $status_wc );

    return new WP_REST_Response( array(
        'external_id' => $external_id,
        'order_id'    => (int) $order->get_id(),
        'status'      => $status_yand,
        'status_raw'  => $status_wc,
    ), 200 );
}


function fdv_yandex_order_set_status( WP_REST_Request $request ) {

    $external_id = (string) $request['orderId'];
    $data        = $request->get_json_params();

    if ( empty( $data['status'] ) ) {
        return new WP_REST_Response( array( 'error' => 'missing_status' ), 400 );
    }

    $status_yandex = (string) $data['status'];

    $order = fdv_yandex_get_order_by_external_id( $external_id );
    if ( ! $order ) {
        return new WP_REST_Response( array( 'error' => 'order_not_found' ), 404 );
    }

    $status_wc = fdv_yandex_map_status_from_yandex( $status_yandex );
    if ( ! $status_wc ) {
        return new WP_REST_Response( array( 'error' => 'unknown_status' ), 400 );
    }

    $order->update_status( $status_wc, 'Статус изменён по запросу Яндекс Еды' );

    return new WP_REST_Response( array(
        'external_id' => $external_id,
        'order_id'    => (int) $order->get_id(),
        'status'      => $status_yandex,
        'status_raw'  => $status_wc,
    ), 200 );
}

function fdv_yandex_map_status_to_yandex( $wc_status ) {
    $map = array(
        'pending'    => 'new',
        'on-hold'    => 'new',
        'processing' => 'accepted',
        'completed'  => 'delivered',
        'cancelled'  => 'cancelled',
        'refunded'   => 'cancelled',
        'failed'     => 'failed',
    );

    return isset( $map[ $wc_status ] ) ? $map[ $wc_status ] : 'unknown';
}

function fdv_yandex_map_status_from_yandex( $y_status ) {
    $map = array(
        'new'       => 'pending',
        'accepted'  => 'processing',
        'cooking'   => 'processing',
        'ready'     => 'processing',
        'delivered' => 'completed',
        'cancelled' => 'cancelled',
        'failed'    => 'failed',
    );

    return isset( $map[ $y_status ] ) ? $map[ $y_status ] : null;
}
add_action( 'save_post_product', function( $post_id, $post, $update ) {

    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
    if ( wp_is_post_revision( $post_id ) ) return;

    $product = wc_get_product( $post_id );
    if ( ! $product ) return;

    $attributes = $product->get_attributes();

    // ЛОГИ: смотрим, что за атрибуты вообще есть
    error_log( '=== ATTRIBUTES for product ' . $post_id . ' ===' );
    foreach ( $attributes as $attr ) {
        /** @var WC_Product_Attribute $attr */
        error_log(
            'name=' . $attr->get_name() .
            ' | is_tax=' . (int) $attr->is_taxonomy() .
            ' | options=' . print_r( $attr->get_options(), true )
        );
    }
}, 5, 3 );
function yandex_log($data, string $tag = 'YA'): void
{
    $log_file = WP_CONTENT_DIR . '/fdv-yandex-eda.log';

    if ($data instanceof WP_REST_Request) {
        $payload = [
            'method'       => $data->get_method(),
            'route'        => $data->get_route(),
            'content_type' => $data->get_header('content-type'),
            'has_auth'     => $data->get_header('authorization') ? 1 : 0,
            'body_params'  => $data->get_body_params(),
            'json_params'  => $data->get_json_params(),
            'query_params' => $data->get_query_params(),
        ];
    } elseif (is_array($data) || is_object($data)) {
        $payload = $data;
    } else {
        $payload = (string)$data;
    }

    $line = sprintf(
        "[%s] [%s] %s\n",
        gmdate('c'),
        $tag,
        is_string($payload)
            ? $payload
            : wp_json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
    );

    // гарантируем файл
    if (!file_exists($log_file)) {
        @file_put_contents($log_file, '');
    }

    @file_put_contents($log_file, $line, FILE_APPEND | LOCK_EX);
}
/**
 * ==========================
 *  IMPORT yandexeda (MS attribute) -> yandex_eda_enabled (Woo) via URL
 *  GET /wp-json/fdv/v1/ms-import-yandexeda?key=YOUR_KEY&limit=500&offset=0&dry_run=0
 * ==========================
 */
define('MS_LOGIN', 'buh@fermadv');
define('MS_PASSWORD', '!#!Wsdcphsdk1947');
if (!defined('MS_BASE')) {
    define('MS_BASE', 'https://api.moysklad.ru/api/remap/1.2');
}

if (!defined('MS_TOKEN')) {
    define('MS_TOKEN', '7fe5aa1b7dcaa2f072ee8ced4ef18d32bd269fc0'); // токен без двоеточия
}

if (!defined('FDV_IMPORT_KEY')) {
    define('FDV_IMPORT_KEY', '1234'); // секрет для запуска урла
}

if (!defined('FDV_MS_YANDEXEDA_ATTR_HREF')) {
    define(
        'FDV_MS_YANDEXEDA_ATTR_HREF',
        'https://api.moysklad.ru/api/remap/1.2/entity/product/metadata/attributes/e8845a64-f04f-11f0-0a80-06b40005b710'
    );
}
function fdv_ms_api_get(string $path, array $query = []): array {
    $url = rtrim(MS_BASE, '/') . '/' . ltrim($path, '/');
    if (!empty($query)) {
        $url .= '?' . http_build_query($query);
    }

    // Используем токен вместо логина/пароля
    $headers = [
        'Accept' => 'application/json;charset=utf-8',
    ];

    if (defined('MS_TOKEN') && MS_TOKEN !== '') {
        // Авторизация по токену
        $headers['Authorization'] = 'Bearer ' . MS_TOKEN;
    } else {
        // Fallback на Basic Auth
        $auth = base64_encode(MS_LOGIN . ':' . MS_PASSWORD);
        $headers['Authorization'] = 'Basic ' . $auth;
    }

    $resp = wp_remote_get($url, [
        'timeout' => 60,
        'headers' => $headers,
    ]);

    if (is_wp_error($resp)) {
        throw new RuntimeException($resp->get_error_message());
    }

    $code = (int) wp_remote_retrieve_response_code($resp);
    $body = (string) wp_remote_retrieve_body($resp);

    if ($code < 200 || $code >= 300) {
        throw new RuntimeException("MS HTTP {$code}: {$body}");
    }

    $json = json_decode($body, true);
    if (!is_array($json)) {
        throw new RuntimeException("MS invalid json");
    }

    return $json;
}
/**
 * Найти товар в МС по SKU (article, если нет — code).
 * Возвращает строку товара (row) или null.
 */
function fdv_ms_find_product_row_by_sku(string $sku): ?array {
    $sku = trim($sku);
    if ($sku === '') return null;

    // 1) article
    $data = fdv_ms_api_get('entity/product', [
        'limit'  => 1,
        'offset' => 0,
        'filter' => 'article=' . $sku,
        // salePrices обычно и так приходят, но expand не мешает
        'expand' => 'salePrices',
    ]);
    $rows = $data['rows'] ?? [];
    if (!empty($rows) && is_array($rows[0])) return $rows[0];

    // 2) code
    $data = fdv_ms_api_get('entity/product', [
        'limit'  => 1,
        'offset' => 0,
        'filter' => 'code=' . $sku,
        'expand' => 'salePrices',
    ]);
    $rows = $data['rows'] ?? [];
    if (!empty($rows) && is_array($rows[0])) return $rows[0];

    return null;
}

/**
 * Найти meta.href типа цены по названию (например "Яндекс еда").
 * Кешируем в option.
 */
function fdv_ms_get_pricetype_href_by_name(string $name): ?string {
    $name_lc = mb_strtolower(trim($name));
    if ($name_lc === '') return null;

    $cache_key = 'fdv_ms_pricetype_href_' . md5($name_lc);
    $cached = get_option($cache_key);
    if (is_string($cached) && $cached !== '') return $cached;

    $data = fdv_ms_api_get('context/companysettings/pricetype', [
        'limit'  => 1000,
        'offset' => 0,
    ]);

    $rows = isset($data['rows']) ? $data['rows'] : $data;
    if (!is_array($rows)) return null;

    foreach ($rows as $row) {
        $row_name = isset($row['name']) ? mb_strtolower(trim((string)$row['name'])) : '';
        if ($row_name === $name_lc) {
            $href = $row['meta']['href'] ?? '';
            if (is_string($href) && $href !== '') {
                update_option($cache_key, $href, false);
                return $href;
            }
        }
    }

    return null;
}

/**
 * Достать цену (в рублях) из salePrices по НАЗВАНИЮ типа цены.
 * В МС value обычно в "копейках" (int), поэтому делим на 100.
 */
function fdv_ms_extract_sale_price_rub_by_pricetype_name(array $ms_product_row, string $price_type_name): ?float {
    $href = fdv_ms_get_pricetype_href_by_name($price_type_name);
    if (!$href) return null;

    $salePrices = $ms_product_row['salePrices'] ?? null;
    if (!is_array($salePrices)) return null;

    foreach ($salePrices as $p) {
        $pt_href = $p['priceType']['meta']['href'] ?? '';
        if ($pt_href !== $href) continue;

        if (!isset($p['value'])) return null;

        $kopeks = (int)$p['value'];
        return $kopeks / 100.0;
    }

    return null;
}

function fdv_ms_get_bool_attr_by_href(array $attributes, string $attr_href): ?bool {
    foreach ($attributes as $a) {
        $href = $a['meta']['href'] ?? '';
        if ($href === $attr_href) {
            if (!array_key_exists('value', $a)) return null;
            return (bool) $a['value'];
        }
    }
    return null;
}

function fdv_wc_find_product_id_by_sku(string $sku): int {
    $sku = trim($sku);
    if ($sku === '') return 0;
    $id = wc_get_product_id_by_sku($sku);
    return $id ? (int) $id : 0;
}

function fdv_import_ms_yandexeda_to_woo_products(int $limit = 1000, int $offset = 0, bool $dry_run = false): array {
    $stats = [
        'ms_total'      => 0,
        'updated_on'    => 0,
        'updated_off'   => 0,
        'no_attr'       => 0,
        'sku_missing'   => 0,
        'sku_not_found' => 0,
        'dry_run'       => $dry_run ? 1 : 0,
        'limit'         => $limit,
        'offset_start'  => $offset,
        'offset_end'    => null,
    ];

    while (true) {
        $data = fdv_ms_api_get('entity/product', [
            'limit'  => $limit,
            'offset' => $offset,
            'expand' => 'attributes',
        ]);

        $rows = $data['rows'] ?? [];
        if (empty($rows)) {
            $stats['offset_end'] = $offset;
            break;
        }

        foreach ($rows as $row) {
            $stats['ms_total']++;

            $sku = (string) ($row['article'] ?? $row['code'] ?? '');
            if (trim($sku) === '') {
                $stats['sku_missing']++;
                continue;
            }

            $attrs = $row['attributes'] ?? [];
            if (!is_array($attrs)) $attrs = [];

            $flag = fdv_ms_get_bool_attr_by_href($attrs, FDV_MS_YANDEXEDA_ATTR_HREF);
            if ($flag === null) {
                $stats['no_attr']++;
                continue;
            }

            $wc_id = fdv_wc_find_product_id_by_sku($sku);
            if (!$wc_id) {
                $stats['sku_not_found']++;
                continue;
            }

            if (!$dry_run) {
                update_post_meta($wc_id, 'yandex_eda_enabled', $flag ? '1' : '0');
            }

            if ($flag) $stats['updated_on']++;
            else       $stats['updated_off']++;
        }

        $offset += $limit;
        if (count($rows) < $limit) {
            $stats['offset_end'] = $offset;
            break;
        }
    }

    return $stats;
}

/**
 * REST endpoint:
 * GET /wp-json/fdv/v1/ms-import-yandexeda?key=...&limit=1000&offset=0&dry_run=1
 */
add_action('rest_api_init', function () {
    register_rest_route('fdv/v1', '/ms-import-yandexeda', [
        'methods'             => 'GET',
        'callback'            => 'fdv_ms_import_yandexeda_endpoint',
        'permission_callback' => '__return_true',
    ]);
});

function fdv_ms_import_yandexeda_endpoint(WP_REST_Request $request) {
    try {
        $key = (string) $request->get_param('key');
        if ($key === '' || !hash_equals(FDV_IMPORT_KEY, $key)) {
            return new WP_REST_Response(['error' => 'forbidden'], 403);
        }
        if ((int)$request->get_param('debug') === 1) {
            $tok = defined('MS_TOKEN') ? (string) MS_TOKEN : '';
            return new WP_REST_Response([
                'ok' => true,
                'debug' => [
                    'ms_base'        => MS_BASE,
                    'ms_token_len'   => strlen($tok),
                    'ms_token_head'  => substr($tok, 0, 6),
                    'ms_token_tail'  => substr($tok, -6),
                    'accept'         => 'application/json;charset=utf-8',
                    'auth_header'    => 'Basic base64(token:)',
                ],
            ], 200);
        }

        // ограничения чтобы случайно не положить сайт
        $limit = (int) ($request->get_param('limit') ?? 1000);
        if ($limit < 1) $limit = 1000;
        if ($limit > 1000) $limit = 1000; // у МС стандартный верхний лимит

        $offset = (int) ($request->get_param('offset') ?? 0);
        if ($offset < 0) $offset = 0;

        $dry_run = (int) ($request->get_param('dry_run') ?? 0) === 1;

        $stats = fdv_import_ms_yandexeda_to_woo_products($limit, $offset, $dry_run);

        return new WP_REST_Response([
            'ok' => true,
            'stats' => $stats,
        ], 200);

    } catch (Throwable $e) {
        return new WP_REST_Response([
            'ok'    => false,
            'error' => $e->getMessage(),
        ], 500);
    }
}
add_filter('rest_json_encode_options', function ($options) {
    return $options | JSON_UNESCAPED_SLASHES;
});
/**
 * Импорт цен "Яндекс еда" из МойСклад в WooCommerce
 * GET /wp-json/fdv/v1/ms-import-yandex-prices?key=YOUR_KEY&limit=500&offset=0&dry_run=1
 */
add_action('rest_api_init', function () {
    register_rest_route('fdv/v1', '/ms-import-yandex-prices', [
        'methods'  => 'GET',
        'callback' => 'fdv_ms_import_yandex_prices_endpoint',
        'permission_callback' => '__return_true',
    ]);
});

function fdv_ms_import_yandex_prices_endpoint(WP_REST_Request $request) {
    try {
        $key = (string) $request->get_param('key');
        if ($key === '' || !hash_equals(FDV_IMPORT_KEY, $key)) {
            return new WP_REST_Response(['error' => 'forbidden'], 403);
        }

        $limit   = min(max((int) ($request->get_param('limit') ?: 500), 1), 1000);
        $offset  = max((int) ($request->get_param('offset') ?: 0), 0);
        $dry_run = (int) $request->get_param('dry_run') === 1;
        if ((int)$request->get_param('debug') === 1) {
            try {
                // Проверяем типы цен
                $pt = fdv_ms_api_get('context/companysettings/pricetype', ['limit' => 100]);

                // Проверяем хотя бы один товар
                $prod = fdv_ms_api_get('entity/product', ['limit' => 1]);

                $sample_prices = [];
                if (!empty($prod['rows'][0]['salePrices'])) {
                    foreach ($prod['rows'][0]['salePrices'] as $sp) {
                        $sample_prices[] = [
                            'name'  => $sp['priceType']['name'] ?? '???',
                            'value' => $sp['value'] ?? 0,
                            'href'  => $sp['priceType']['meta']['href'] ?? '',
                        ];
                    }
                }

                return new WP_REST_Response([
                    'ok' => true,
                    'pricetype_rows_count' => count($pt['rows'] ?? []),
                    'pricetype_raw'        => $pt,
                    'product_name'         => $prod['rows'][0]['name'] ?? 'no products',
                    'product_salePrices'   => $sample_prices,
                ], 200);
            } catch (Throwable $e) {
                return new WP_REST_Response(['ok' => false, 'error' => $e->getMessage()], 500);
            }
        }
        $stats = fdv_import_ms_yandex_prices_to_woo($limit, $offset, $dry_run);

        return new WP_REST_Response([
            'ok'    => true,
            'stats' => $stats,
        ], 200);

    } catch (Throwable $e) {
        return new WP_REST_Response([
            'ok'    => false,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ], 500);
    }
}

function fdv_import_ms_yandex_prices_to_woo(int $limit, int $offset, bool $dry_run): array {
    $stats = [
        'ms_total'       => 0,
        'price_found'    => 0,
        'price_zero'     => 0,
        'sku_missing'    => 0,
        'wc_not_found'   => 0,
        'updated'        => 0,
        'skipped_same'   => 0,
        'dry_run'        => $dry_run ? 1 : 0,
        'limit'          => $limit,
        'offset_start'   => $offset,
        'offset_end'     => null,
        'errors'         => [],
        'samples'        => [], // первые 10 для отладки
    ];

    // Получаем href типа цены "Яндекс еда"
    $price_type_href = fdv_ms_get_pricetype_href_by_name('Яндекс еда');
    if (!$price_type_href) {
        $stats['errors'][] = 'Тип цены "Яндекс еда" не найден в МойСклад';
        return $stats;
    }

    while (true) {
        $data = fdv_ms_api_get('entity/product', [
            'limit'  => $limit,
            'offset' => $offset,
        ]);

        $rows = $data['rows'] ?? [];
        if (empty($rows)) {
            $stats['offset_end'] = $offset;
            break;
        }

        foreach ($rows as $row) {
            $stats['ms_total']++;

            // SKU: сначала article, потом code
            $sku = trim((string) ($row['article'] ?? ''));
            if ($sku === '') {
                $sku = trim((string) ($row['code'] ?? ''));
            }
            if ($sku === '') {
                $stats['sku_missing']++;
                continue;
            }

            // Ищем цену "Яндекс еда"
            $ya_price = fdv_extract_price_by_href($row['salePrices'] ?? [], $price_type_href);

            if ($ya_price === null) {
                $stats['price_zero']++;
                continue;
            }

            $stats['price_found']++;

            // Ищем товар в WooCommerce
            $wc_id = fdv_wc_find_product_id_by_sku($sku);
            if (!$wc_id) {
                $stats['wc_not_found']++;
                continue;
            }

            // Текущее значение
            $current = get_post_meta($wc_id, 'yandex_price', true);
            $current_float = $current !== '' ? (float) $current : null;

            // Сравниваем (с точностью до копейки)
            if ($current_float !== null && abs($current_float - $ya_price) < 0.01) {
                $stats['skipped_same']++;
                continue;
            }

            // Сохраняем sample для отладки
            if (count($stats['samples']) < 10) {
                $stats['samples'][] = [
                    'sku'       => $sku,
                    'wc_id'     => $wc_id,
                    'old_price' => $current_float,
                    'new_price' => $ya_price,
                ];
            }

            if (!$dry_run) {
                update_post_meta($wc_id, 'yandex_price', $ya_price);
            }

            $stats['updated']++;
        }

        $offset += $limit;

        // Если получили меньше лимита — значит это последняя страница
        if (count($rows) < $limit) {
            $stats['offset_end'] = $offset;
            break;
        }
    }

    return $stats;
}

/**
 * Извлечь цену из salePrices по href типа цены
 */
function fdv_extract_price_by_href(array $salePrices, string $price_type_href): ?float {
    foreach ($salePrices as $p) {
        $href = $p['priceType']['meta']['href'] ?? '';
        if ($href !== $price_type_href) {
            continue;
        }

        if (!isset($p['value'])) {
            return null;
        }

        // value в МойСклад — копейки (целое число)
        $kopeks = (int) $p['value'];
        if ($kopeks <= 0) {
            return null;
        }

        return $kopeks / 100.0;
    }

    return null;
}
/**
 * Маппинг placeId (Яндекс Еда) → UUID склада в МойСклад
 */
function fdv_ya_placeid_to_ms_store_uuid( string $place_id ): ?string {
    $map = [
        '7c0dc9ce-ce1e-11ea-0a80-09ca000e5e93' => '7c0dc9ce-ce1e-11ea-0a80-09ca000e5e93',
        'b24e4c35-9609-11eb-0a80-0d0d008550c2' => 'b24e4c35-9609-11eb-0a80-0d0d008550c2',
        'cab1caa9-da10-11eb-0a80-07410026c356' => 'cab1caa9-da10-11eb-0a80-07410026c356',
        'a99d6fdf-0970-11ed-0a80-0ed600075845' => 'a99d6fdf-0970-11ed-0a80-0ed600075845',
        '9c9dfcc4-733f-11ec-0a80-0da1013a560d' => '9c9dfcc4-733f-11ec-0a80-0da1013a560d',
        '028e05a7-b4fa-11ee-0a80-1198000442be' => '028e05a7-b4fa-11ee-0a80-1198000442be',
        '149a2219-9003-11ef-0a80-14a00002d2a5' => '149a2219-9003-11ef-0a80-14a00002d2a5',
        '076fd75d-aa46-11f0-0a80-16ae0000467c' => '076fd75d-aa46-11f0-0a80-16ae0000467c',
    ];

    return $map[$place_id] ?? null;
}

/**
 * Получить остатки из МойСклад по конкретному складу.
 * Возвращает массив [sku => quantity].
 */
function fdv_ms_get_stock_by_store( string $store_uuid ): array {
    $cache_key = 'fdv_ms_stock_' . md5( $store_uuid );
    $cached    = get_transient( $cache_key );
    if ( is_array( $cached ) ) {
        return $cached;
    }

    try {
        $store_href  = 'https://api.moysklad.ru/api/remap/1.2/entity/store/' . $store_uuid;
        $page_limit  = 1000;
        $offset      = 0;
        $result      = [];

        do {
            $data = fdv_ms_api_get( 'report/stock/all', [
                'stockType' => 'quantity',
                'filter'    => 'store=' . $store_href,
                'limit'     => $page_limit,
                'offset'    => $offset,
            ] );

            $rows = $data['rows'] ?? [];
            if ( ! is_array( $rows ) ) break;

            foreach ( $rows as $row ) {
                $sku = trim( (string)( $row['article'] ?? $row['code'] ?? '' ) );
                if ( $sku === '' ) continue;
                $result[ $sku ] = max( 0, (float)( $row['stock'] ?? 0 ) );
            }

            $offset += $page_limit;

        } while ( count( $rows ) === $page_limit );

        set_transient( $cache_key, $result, 5 * MINUTE_IN_SECONDS );
        return $result;

    } catch ( Throwable $e ) {
        return [];
    }
}
function fdv_yandex_nomenclature_availability( WP_REST_Request $request ) {
    $place_id = (string) ( $request->get_param( 'placeId' ) ?? '' );

    // Маппинг placeId → UUID склада МС
    $store_uuid = fdv_ya_placeid_to_ms_store_uuid( $place_id );

    // Если есть UUID склада — берём остатки из МС, иначе — из Woo (fallback)
    $ms_stock = $store_uuid ? fdv_ms_get_stock_by_store( $store_uuid ) : null;

    $args = [
        'post_type'      => 'product',
        'post_status'    => 'publish',
        'posts_per_page' => 5000,
        'meta_query'     => [
            [ 'key' => 'yandex_eda_enabled', 'value' => '1' ],
        ],
        'fields' => 'ids',
    ];

    $q = new WP_Query( $args );

    $availability = [];

    foreach ( $q->posts as $product_id ) {
        $product = wc_get_product( $product_id );
        if ( ! $product ) {
            continue;
        }

        if ( $ms_stock !== null ) {
            // Берём остатки из МС по SKU
            $sku   = (string) $product->get_sku();
            $stock = isset( $ms_stock[ $sku ] ) ? (int) floor( $ms_stock[ $sku ] ) : 0;
        } else {
            // Fallback: остатки из WooCommerce (как было раньше)
            if ( $product->is_in_stock() ) {
                $stock = $product->get_manage_stock()
                    ? max( 0, (int) $product->get_stock_quantity() )
                    : 9999;
            } else {
                $stock = 0;
            }
        }

        $availability[] = [
            'id'    => (string) $product_id,
            'stock' => $stock,
        ];
    }

    return new WP_REST_Response( [
        'items' => $availability,
    ], 200 );
}
add_action('rest_api_init', function() {
    register_rest_route('fdv/v1', '/debug-stock-by-place', [
        'methods' => 'GET',
        'callback' => 'fdv_debug_stock_by_place_endpoint',
        'permission_callback' => '__return_true',
    ]);
});

function fdv_debug_stock_by_place_endpoint(WP_REST_Request $request) {
    $key = (string) $request->get_param('key');
    if (!hash_equals(FDV_IMPORT_KEY, $key)) {
        return new WP_REST_Response(['error' => 'forbidden'], 403);
    }

    $place_id   = (string) ($request->get_param('place_id') ?? '');
    $store_uuid = fdv_ya_placeid_to_ms_store_uuid($place_id);

    if (!$store_uuid) {
        return new WP_REST_Response([
            'ok'       => false,
            'place_id' => $place_id,
            'error'    => 'placeId не найден в маппинге. Доступные ключи: egershelg, remi, more, sputnik, svetlan, chkalova, nakhodka, okeansky',
        ], 200);
    }

    // Сбрасываем кеш если передан параметр nocache=1
    if ((int) $request->get_param('nocache') === 1) {
        delete_transient('fdv_ms_stock_' . md5($store_uuid));
    }

    $ms_stock = fdv_ms_get_stock_by_store($store_uuid);

    // Берём первые N товаров из WC с yandex_eda_enabled=1 и сравниваем
    $limit = min((int) ($request->get_param('limit') ?: 20), 100);

    $q = new WP_Query([
        'post_type'      => 'product',
        'post_status'    => 'publish',
        'posts_per_page' => $limit,
        'meta_query'     => [['key' => 'yandex_eda_enabled', 'value' => '1']],
        'fields'         => 'ids',
    ]);

    $rows = [];
    foreach ($q->posts as $product_id) {
        $product = wc_get_product($product_id);
        if (!$product) continue;

        $sku        = (string) $product->get_sku();
        $ms_qty     = isset($ms_stock[$sku]) ? (int) floor($ms_stock[$sku]) : null;
        $wc_qty     = $product->get_manage_stock()
            ? (int) $product->get_stock_quantity()
            : ($product->is_in_stock() ? 9999 : 0);

        $rows[] = [
            'product_id' => $product_id,
            'name'       => $product->get_name(),
            'sku'        => $sku,
            'stock_ms'   => $ms_qty,   // остаток из МС для этого склада
            'stock_wc'   => $wc_qty,   // остаток в WooCommerce
            'in_ms'      => $ms_qty !== null ? 'да' : 'нет', // нашёлся ли SKU в МС
        ];
    }

    return new WP_REST_Response([
        'ok'              => true,
        'place_id'        => $place_id,
        'store_uuid'      => $store_uuid,
        'ms_total_skus'   => count($ms_stock),   // сколько всего SKU вернул МС для склада
        'wc_sample_count' => count($rows),
        'rows'            => $rows,
    ], 200);
}


//
//## Использование
//
//**Тестовый запуск (без сохранения):**
//```
///wp-json/fdv/v1/ms-import-yandex-prices?key=1234&dry_run=1
//    ```
//
//**Боевой запуск:**
//```
///wp-json/fdv/v1/ms-import-yandex-prices?key=1234&limit=500
//    ```
//
//**С пагинацией (если товаров много):**
//```
///wp-json/fdv/v1/ms-import-yandex-prices?key=1234&offset=0&limit=1000
///wp-json/fdv/v1/ms-import-yandex-prices?key=1234&offset=1000&limit=1000

/* << End
 * @author Prudnikov Mikhail, MsWoo (©) 2024
 * @date 26-07-2024
*/