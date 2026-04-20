<?php


/**
 * Class WmsBundlePublic
 */
class WmsBundlePublic
{
    /**
     * WmsBundlePublic constructor.
     */
    public function __construct()
    {
        add_action('woocommerce_product_tabs', array($this, 'product_tabs'), 1);
    }

    public function product_tabs( $tabs = array() ) {

        if(!apply_filters('wms_is_visible_view_component', true)){
            return $tabs;
        }

        $tabs['bundle_components'] = array(
            /* translators: %s: reviews count */
            'title'    => 'Товары в комплекте',
            'priority' => 10000,
            'callback' => [$this, 'view_components'],
        );

        return $tabs;
    }


    /**
     *
     */
    public function view_components(): void
    {
        global $product;

        $components = $product->get_meta( '_components_ms');

        if ($components) {
            $components_table_html = apply_filters('wms_view_components_table_html', null, $components, $product);

            if(!$components_table_html){
                $components_table_html = call_user_func_array(
                    array($this, 'view_components_table'),
                    array($components, $product)
                );
            }

            echo $components_table_html;

        }
    }



    /**
     * @param $components
     * @return mixed
     */
    public function view_components_table($components)
    {
        ob_start();
        ?>
        <div class="wms-view-component wms-view-component-none">
        </div>
        <table class="wms-bundle-public-components">
            <tr>
                <th class="wms-bundle-public-components-title"><?php echo apply_filters('wms_bundle_public_components_title', 'Товар'); ?></th>
                <th class="wms-bundle-public-components-qty"><?php echo apply_filters('wms_bundle_public_components_qty', 'Количество'); ?></th>
            </tr>
            <tbody>
            <?php foreach ($components as $component) { ?>
                <tr>
                    <td class="wms-bundle-public-components-name">
                        <span><?php echo $component['name']; ?></span></td>
                        <td class="wms-bundle-public-components-quantity"><?php echo $component['quantity']; ?></td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    <?php

        return ob_get_clean();

    }


}

new WmsBundlePublic();

