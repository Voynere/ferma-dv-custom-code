<?php


/**
 * Class WmsGroupMetabox
 */
class WmsGroupMetabox
{
    /**
     *
     */
    public static function init()
    {
        add_action('product_cat_edit_form_fields', array('WmsGroupMetabox', 'add'), 10000);
        add_action( 'edit_product_cat', array('WmsGroupMetabox', 'save') );
    }

    /**
     * @param $oProductCat
     */
    public static function add($oProductCat)
    {
        $oGroups = new WmsGroopApi();
        $groups = $oGroups->get_groups_tree();

        $sIdMs = get_term_meta($oProductCat->term_id, 'ms_cat_id', true);

        ?>
        <tr class="form-field">
            <th scope="row" valign="top"><label
                        for="wms_groop_id">Категория Мой Склад</label></th>
            <td>
                <?php wp_nonce_field(basename(__FILE__), 'wms_product_cat_group_nonce'); ?>

                <select name="wms_group_id" id="wms_group_id">
                    <option value="none">Выберите группу</option>
                    <?php echo  WmsHelper::render_select_options($groups, $sIdMs); ?>
                </select>
                <p class="description">Укажите категорию из Мой Склад</p>
            </td>
        </tr>


        <?php

    }

    /**
     * @param $term_id
     */
    public static function save($term_id)
    {
        if (!isset($_POST['wms_product_cat_group_nonce']) || !wp_verify_nonce($_POST['wms_product_cat_group_nonce'], basename(__FILE__))) {
            return;
        }

        $sIdMs = isset($_POST['wms_group_id']) ? $_POST['wms_group_id'] : '';
        update_term_meta($term_id, 'ms_cat_id', $sIdMs);
    }

}