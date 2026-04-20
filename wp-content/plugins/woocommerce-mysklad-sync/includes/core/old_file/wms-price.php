<?php
if (!defined('ABSPATH')) exit;

/**
 *
 */
class WmsPrice
{


    static public function init()
    {
        add_action('wp_ajax_wms_create_price', array('WmsPrice', 'create_price'));
        add_action('wp_ajax_nopriv_wms_create_price', array('WmsPrice', 'create_price'));
        add_action('wp_ajax_wms_get_price', array('WmsPrice', 'get_dop_price_table'));
        add_action('wp_ajax_nopriv_wms_get_price', array('WmsPrice', 'get_dop_price_table'));
        add_action('wp_ajax_wms_delete_price', array('WmsPrice', 'delete_price'));
        add_action('wp_ajax_nopriv_wms_delete_price', array('WmsPrice', 'delete_price'));

        $option = get_option('wms_settings_counterparty');
        if (!empty($option) or $option !== false) {
            if (isset($option['wms_load_price_counterparty']) and $option['wms_load_price_counterparty'] == 'on') {
                add_action('woocommerce_product_options_pricing', array('WmsPrice', 'wms_dop_product_price_field'));
                add_action('woocommerce_variation_options_pricing', array('WmsPrice', 'wms_dop_product_price_field_variation'), 10, 3);
                //add_action( 'save_post', array('WmsPrice',  'wms_dop_price_save_product' ) );
            }
        }
    }


    static function wms_dop_product_price_field()
    {
        $option = get_option('wms_price_product_dop');
        if (is_array($option) and !empty($option)) {
            foreach ($option as $key => $value) {
                woocommerce_wp_text_input(array('id' => $value['id'], 'class' => 'wc_input_price short', 'label' => __($key, 'woocommerce') . ' (' . get_woocommerce_currency_symbol() . ')'));
            }
        }
    }


    static function wms_dop_product_price_field_variation($loop, $variation_data, $variation)
    {
        $option = get_option('wms_price_product_dop');
        if (is_array($option) and !empty($option)) {
            foreach ($option as $key => $value) {
                $unit_price = get_post_meta($variation->ID, $value['id'], true);
                print_r('<p>' . $value['type'] . ' (' . get_woocommerce_currency_symbol() . ')' . ': ' . $unit_price . '</p>');

            }
        }
    }


    function wms_dop_price_save_product($product_id)
    {
        // Если это автосохранение, то ничего не делаем, сохраняем данные только при нажатии на кнопку Обновить
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
        $option = get_option('wms_price_product_dop');
        if (is_array($option) and !empty($option)) {
            foreach ($option as $key => $value) {
                $id = $value['id'];
                if (isset($_POST[$id])) {
                    if (is_numeric($_POST[$id])) update_post_meta($product_id, $id, $_POST[$id]);
                } else delete_post_meta($product_id, $id);
            }
        }
    }

    static public function create_price()
    {

        if (empty($_POST['name'])) {
            printf('<div class="alert alert-danger">%s</div>', 'Заполните поле Название цены');
            exit;
        }
        $name = $_POST['name'];
        $type = $_POST['type'];
        $option = get_option('wms_price_product_dop');
        if (is_array($option) and !empty($option)) {
            if (isset($option[$name])) {
                printf('<div class="alert alert-danger">%s</div>', 'Цена с таким названием уже существует');
                exit;
            }
        }

        $id = WmsHelper::translit($name);
        if (empty($option)) {
            $option = array($name => array('id' => $id, 'type' => $type));
        } else {
            $option = array_merge($option, array($name => array('id' => $id, 'type' => $type)));
        }
        update_option('wms_price_product_dop', $option);
        printf('<div class="alert alert-success">%s</div>', "Дополнительный тип цены <strong>$name</strong>  создана");
        exit;
    }

    static public function delete_price()
    {

        if (empty($_POST['type1'])) {
            return;
        }
        $name = $_POST['type1'];
        $option = get_option('wms_price_product_dop');
        if (isset($option[$name])) {
            //delete_metadata( 'product', 5, $option[$name]['id'], '', true);
            unset($option[$name]);
            update_option('wms_price_product_dop', $option);
            printf('<div class="alert alert-success">%s</div>', "Дополнительный тип цены <strong>$name</strong>  удален");
            exit;
        }
        return;
    }


    static public function get_dop_price($iProductId, $aAssortmentPrices)
    {
        if (empty($iProductId)) return;
        if (empty($aAssortmentPrices)) return;

        $aDopPrices = get_option('wms_price_product_dop');
        if (empty($aDopPrices) or $aDopPrices == false) {
            return;
        }

        $aPricesMs = array();

        foreach ($aAssortmentPrices as $aPrice) {
            if(is_array($aPrice) and isset($aPrice['priceType'])){
                $aPricesMs[$aPrice['priceType']] = $aPrice;
            }
        }

        foreach ($aDopPrices as $aDopPrice) {
            if (isset($aPricesMs[$aDopPrice['type']])) {
                update_post_meta($iProductId, $aDopPrice['id'], $aPricesMs[$aDopPrice['type']]['value'] / 100);
            }
        }

    }


    static public function get_dop_price_table()
    {
        $option = get_option('wms_price_product_dop');
        ?>
        <table class="table" id="table1">
            <thead>
            <tr>
                <th>№</th>
                <th>Название цены</th>
                <th>Тип цены</th>

            </tr>
            </thead>
            <tbody>
            <?php
            $i = 1;
            if (is_array($option) and !empty($option)) {
                foreach ($option as $key => $value) {
                    ?>
                    <tr>
                        <th scope="row"><?php echo $i; ?></th>
                        <td class="wms-price-<?php echo $i; ?>"><? echo $key; ?></td>
                        <td><? echo $value['type']; ?></td>
                        <td>
                            <button type="button" id="wms-price-<?php echo $i; ?>" class="close closeprice">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </td>
                    </tr>
                    <?php
                    $i++;
                }
            } ?>
            </tbody>
        </table>
        <script>
            jQuery(".closeprice").on("click", function (event) {
                var name1 = jQuery(this).attr('id');
                var type1 = jQuery('.' + name1).html();

                jQuery.ajax({
                    url: "/wp-admin/admin-ajax.php",
                    method: 'post',
                    data: {
                        action: 'wms_delete_price',
                        type1: type1

                    },
                    success: function (response) {
                        jQuery('#modal-body1').html(response);
                        jQuery.ajax({
                            url: "/wp-admin/admin-ajax.php",
                            method: 'post',
                            data: {
                                action: 'wms_get_price',


                            },
                            success: function (response) {
                                jQuery('#modal-body').html(response);
                            }
                        });
                    }
                });
            });
        </script>
        <?php exit;
    }

    static public function get_prices()
    {
        $connect = WmsConnect::get_connect('entity/product/metadata');
        $connect = WmsHelper::decode($connect);
        if (isset($connect['errors'])) {
            return array('priceTypes' => array(0 => array('Отсутствуют доступные цены')));
        } else {
            return $connect;
        }
    }

    static function price($price)
    {
        $price_data = $price / 100;
        return $price_data;
    }

    /**
     * @return int
     */
    static function get_price_type($wms_price_type)
    {
        //если тип установлен возращяем тип
        if (!empty($wms_price_type)) {
            return $wms_price_type;
        } else {
            //если нет то устанавливаем первый полученый тип цены
            $wms_price_type = 0;
            return $wms_price_type;
        }
    }

    static public function get_price($wms_post_id, $product_price)
    {
        $sale_price = get_post_meta($wms_post_id, '_sale_price', true);
        $price = get_post_meta($wms_post_id, '_price', true);
        if ($price != $sale_price) {
            return $product_price;
        } else {
            return $sale_price;
        }
    }


}
