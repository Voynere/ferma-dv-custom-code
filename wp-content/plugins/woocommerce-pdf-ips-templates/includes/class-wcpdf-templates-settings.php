<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'WooCommerce_PDF_IPS_Templates_Settings' ) ) {

	class WooCommerce_PDF_IPS_Templates_Settings {		
		public function __construct() {
			// Hook into main pdf plugin settings
			add_filter( 'wpo_wcpdf_settings_tabs', array( $this, 'settings_tab' ) );
			add_action( 'admin_init', array( $this, 'init_settings' ) );
			add_action( 'wpo_wcpdf_before_settings', array( $this, 'column_editor' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'load_scripts_styles' ), 99 );

			// Fix compatibility issues with YIT themes and other plugins loading jquery-ui styles everywhere
			add_action( 'admin_enqueue_scripts', array( $this, 'dequeue_jquery_ui_styles' ), 999 );

			// Footer height settings (also initiated in the template functions but registered here too for backwards compatibility)
			add_filter( 'wpo_wcpdf_settings_fields_general', array( $this, 'add_footer_height_setting' ), 10, 4 );

			// Add field to columns or totals
			add_action( 'wp_ajax_wcpdf_templates_add_totals_columns_field', array( $this, 'add_totals_columns_field' ) );

			// Add custom block
			add_action( 'wp_ajax_wcpdf_templates_add_custom_block', array( $this, 'add_custom_block' ) );

			// remove single use query arg for restoring defaults
			add_action( 'updated_option', array( $this, 'remove_load_defaults_after_updating_option' ), 10, 3 );
		}


		/**
		 * Styles for settings page
		 */
		public function load_scripts_styles ( $hook ) {
			// only load on our own settings page
			// maybe find a way to refer directly to WPO\WC\PDF_Invoices\Settings::$options_page_hook ?
			if ( !( $hook == 'woocommerce_page_wpo_wcpdf_options_page' || $hook == 'settings_page_wpo_wcpdf_options_page' || ( isset($_GET['page']) && $_GET['page'] == 'wpo_wcpdf_options_page' ) ) ) {
				return;
			}

			wp_enqueue_script(
				'wcpdf-editor',
				WPO_WCPDF_Templates()->plugin_url() . '/assets/js/editor.js',
				array( 'jquery-ui-accordion', 'jquery-ui-sortable', 'jquery-ui-draggable', 'jquery-ui-tabs' ),
				WPO_WCPDF_TEMPLATES_VERSION
			);

			wp_enqueue_style(
				'wcpdf-editor',
				WPO_WCPDF_Templates()->plugin_url() . '/assets/css/editor.css',
				array(),
				WPO_WCPDF_TEMPLATES_VERSION
			);

			wp_enqueue_style(
				'woocommerce-pdf-ips-templates-jquery-ui-style',
				'https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css'
			);

			wp_localize_script(
				'wcpdf-editor',
				'wpo_wcpdf_templates',
				array(  
					'ajaxurl'        => admin_url( 'admin-ajax.php' ), // URL to WordPress ajax handling page
					'nonce'          => wp_create_nonce('wpo_wcpdf_templates'),
				)
			);
		}

		/**
		 * Dequeue YIT styles (they're all over the place man!)
		 */
		public function dequeue_jquery_ui_styles ( $hook ) {
			// only load on our own settings page
			// maybe find a way to refer directly to WPO\WC\PDF_Invoices\Settings::$options_page_hook ?
			if ( !( $hook == 'woocommerce_page_wpo_wcpdf_options_page' || $hook == 'settings_page_wpo_wcpdf_options_page' ) ) {
				return;
			}

			$offending_styles = array (
				'jquery-ui-overcast',
				'yit-plugin-metaboxes',
				'jquery-ui-style',
				'jquery-ui',
				'jquery-style',
				'yit-jquery-ui-style',
				'jquery-ui-style-css',
				'yith-wcaf',
				'yith_ywdpd_admin',
				'ig-pb-jquery-ui',
				'jquery_smoothness_ui',
				'fblb_jquery-ui',
				'wp-review-admin-ui-css',
				'tribe-jquery-ui-theme',
				'jquery-style-css',
			);

			foreach ($offending_styles as $handle) {
				wp_dequeue_style( $handle );
				wp_deregister_style( $handle );
			}
		}

		public function get_settings ( $template_type, $settings_name, $document = null ) {
			$editor_settings = get_option('wpo_wcpdf_editor_settings');

			$settings_key = 'fields_'.$template_type.'_'.$settings_name;
			if (isset($editor_settings[$settings_key])) {
				$settings = $editor_settings[$settings_key];
			} else {
				$settings = array();
			}

			// use defaults if settings not defined
			if ( empty($settings) && !isset($editor_settings['settings_saved'])) {
				// only packing slip is different
				if ( $template_type == 'packing-slip' ) {
					switch ($settings_name) {
						case 'columns':
							$settings = array (
								1 => array (
									'type'			=> 'sku',
								),
								2 => array (
									'type'			=> 'description',
									'show_meta'		=> 1,
								),
								3 => array (
									'type'			=> 'quantity',
								),
							);
							break;
						case 'totals':
							$settings = array();
							break;
					}
				} else {
					switch ($settings_name) {
						case 'columns':
							$settings = array (
								1 => array (
									'type'			=> 'sku',
								),
								2 => array (
									'type'			=> 'description',
									'show_meta'		=> 1,
								),
								3 => array (
									'type'			=> 'quantity',
								),
								4 => array (
									'type'			=> 'price',
									'price_type'	=> 'single',
									'tax'			=> 'excl',
									'discount'		=> 'before',
								),
								5 => array (
									'type'			=> 'tax_rate',
								),
								6 => array (
									'type'			=> 'price',
									'price_type'	=> 'total',
									'tax'			=> 'excl',
									'discount'		=> 'before',
								),
							);
							break;
						case 'totals':
							$settings = array(
								1 => array (
									'type'			=> 'subtotal',
									'tax'			=> 'excl',
									'discount'		=> 'before',
								),
								2 => array (
									'type'			=> 'discount',
									'tax'			=> 'excl',
								),
								3 => array (
									'type'			=> 'shipping',
									'tax'			=> 'excl',
								),
								4 => array (
									'type'			=> 'fees',
									'tax'			=> 'excl',
								),
								5 => array (
									'type'			=> 'vat',
								),
								6 => array (
									'type'			=> 'total',
									'tax'			=> 'incl',
								),
							);
							break;
					}
				}
			}

			return apply_filters( 'wpo_wcpdf_template_editor_settings', $settings, $template_type, $settings_name, $document );
		}

		/**
		 * add Editor settings tab to the PDF Invoice settings page
		 * @param  array $tabs slug => Title
		 * @return array $tabs with Editor
		 */
		public function settings_tab( $tabs ) {
			$tabs['editor'] = __('Customizer','wpo_wcpdf_templates');
			return $tabs;
		}

		public function column_editor ( $settings_tab ) {
			if ( $settings_tab != 'editor') {
				return;
			}

			$option = 'wpo_wcpdf_editor_settings';

			// hidden option to check if user has saved/modified the settings (to know whether to load defaults or not!)
			printf('<input type="hidden" data-key="type" name="%s[settings_saved]" value="1">', $option);

			// show drag & drop editor
			$editor_args = array(
				'menu'			=> $option,
				'id'			=> 'fields',
				'documents'		=> array(),
				'description'	=> __( 'Drag & drop any of these fields to the documents below', 'wpo_wcpdf_templates' ),
			);

			$documents = WPO_WCPDF()->documents->get_documents('all');
			foreach ($documents as $document) {
				$document_type = $document->get_type();
				$editor_args['documents'][$document_type] = $document->get_title();
			}

			$this->columns_editor_callback( $editor_args );

			?>
			<style>
			.form-table td, .form-table th {
				display: block;
				padding: 0;
			}
			</style>
			<?php
		}

		/**
		 * User settings.
		 */
		public function init_settings() {
			$page = $option_group = $option_name = 'wpo_wcpdf_editor_settings';

			$settings_fields = array(
				array(
					'type'		=> 'section',
					'id'		=> 'custom_styles',
					'title'		=> '',
					'callback'	=> 'section',
				),
				array(
					'type'			=> 'setting',
					'id'			=> 'custom_styles',
					'title'			=> sprintf('<h3>%s</h3>', __( 'Custom Styles', 'wpo_wcpdf_templates' )),
					'callback'		=> 'textarea',
					'section'		=> 'custom_styles',
					'args'			=> array(
						'option_name'	=> $option_name,
						'id'			=> 'custom_styles',
						'description'	=> __( 'Enter any custom styles here to modify/override the template styles', 'wpo_wcpdf_templates' ),
						'width'			=> '72',
						'height'		=> '8',
					)
				),
			);

			// allow plugins to alter settings fields
			$settings_fields = apply_filters( 'wpo_wcpdf_settings_fields_customizer', $settings_fields, $page, $option_group, $option_name );
			WPO_WCPDF()->settings->add_settings_fields( $settings_fields, $page, $option_group, $option_name );
			return;	
		}

		/**
		 * Section null callback.
		 *
		 * @return void.
		 */
		public function section_options_callback() {
		}

		public function get_columns_field_options() {
			return array (
				'position'		=> array (
					'title'		=> __( 'Position', 'wpo_wcpdf_templates' ),
					'options'	=> array (
						'label'		=> array(
							'type'			=> 'text',
							'description'	=> __( 'Label', 'wpo_wcpdf_templates' ),
						),
					),
				),
				'sku'			=> array (
					'title'		=> __( 'SKU', 'wpo_wcpdf_templates' ),
					'options'	=> array (
						'label'		=> array(
							'type'			=> 'text',
							'description'	=> __( 'Label', 'wpo_wcpdf_templates' ),
							'placeholder'	=> __( 'Use default', 'wpo_wcpdf_templates' ),
						),
					),
				),
				'thumbnail'		=> array (
					'title'		=> __( 'Thumbnail', 'wpo_wcpdf_templates' ),
					'options'	=> array (
						'label'		=> array(
							'type'			=> 'text',
							'description'	=> __( 'Label', 'wpo_wcpdf_templates' ),
							'placeholder'	=> __( 'Use default', 'wpo_wcpdf_templates' ),
						),
					),
				),
				'description'	=> array (
					'title'		=> __( 'Product', 'wpo_wcpdf_templates' ),
					'options'	=> array (
						'label'			=> array(
							'type'			=> 'text',
							'description'	=> __( 'Label', 'wpo_wcpdf_templates' ),
							'placeholder'	=> __( 'Use default', 'wpo_wcpdf_templates' ),
						),
						'show_sku'		=> array(
							'type'			=> 'checkbox',
							'description'	=> __( 'Show SKU', 'wpo_wcpdf_templates' ),
						),
						'show_weight'	=> array(
							'type'			=> 'checkbox',
							'description'	=> __( 'Show weight', 'wpo_wcpdf_templates' ),
						),
						'show_meta'		=> array(
							'type'			=> 'checkbox',
							'description'	=> __( 'Show meta data', 'wpo_wcpdf_templates' ),
						),
						'show_external_plugin_meta'	=> array(
							'type'			=> 'checkbox',
							'description'	=> __( 'Show external plugin data', 'wpo_wcpdf_templates' ),
						),
						'custom_text'	=> array(
							'type'			=> 'textarea',
							'rows'			=> 4,
							'description'	=> __( 'Text', 'wpo_wcpdf_templates' ),
						),
					),
				),
				'quantity'		=> array (
					'title'		=> __( 'Quantity', 'wpo_wcpdf_templates' ),
					'options'	=> array (
						'label'		=> array(
							'type'			=> 'text',
							'description'	=> __( 'Label', 'wpo_wcpdf_templates' ),
							'placeholder'	=> __( 'Use default', 'wpo_wcpdf_templates' ),
						),
					),
				),
				'all_meta'		=> array (
					'title'		=> __( 'Variation / item meta', 'wpo_wcpdf_templates' ),
					'options'	=> array (
						'label'		=> array(
							'type'			=> 'text',
							'description'	=> __( 'Label', 'wpo_wcpdf_templates' ),
							'placeholder'	=> __( 'Use default', 'wpo_wcpdf_templates' ),
						),
						'product_fallback'	=> array(
							'type'			=> 'checkbox',
							'description'	=> __( 'Fallback to product variation data', 'wpo_wcpdf_templates' ),
						),
					),
				),
				'item_meta'	=> array (
					'title'		=> __( 'Item meta (single)', 'wpo_wcpdf_templates' ),
					'options'	=> array (
						'field_name' => array(
							'type'			=> 'text',
							'description'	=> __( 'Meta key / name', 'wpo_wcpdf_templates' ),
						),
						'label'		=> array(
							'type'			=> 'text',
							'description'	=> __( 'Label', 'wpo_wcpdf_templates' ),
						),
					),
				),
				'price'	=> array (
					'title'		=> __( 'Price', 'wpo_wcpdf_templates' ),
					'options'	=> array (
						'label'		=> array(
							'type'			=> 'text',
							'description'	=> __( 'Label', 'wpo_wcpdf_templates' ),
							'placeholder'	=> __( 'Use default', 'wpo_wcpdf_templates' ),
						),
						'price_type'	=> array(
							'type'			=> 'select',
							'options'		=> array(
								'single'	=> __( 'Single price', 'wpo_wcpdf_templates' ),
								'total'		=> __( 'Total price', 'wpo_wcpdf_templates' ),
							),
						),
						'tax'	=> array(
							'type'			=> 'select',
							'options'		=> array(
								'incl'		=> __( 'Including tax', 'wpo_wcpdf_templates' ),
								'excl'		=> __( 'Excluding tax', 'wpo_wcpdf_templates' ),
							),
						),
						'discount'	=> array(
							'type'			=> 'select',
							'options'		=> array(
								'before'	=> __( 'Before discount', 'wpo_wcpdf_templates' ),
								'after'		=> __( 'After discount', 'wpo_wcpdf_templates' ),
							),
						),
						'only_discounted'	=> array(
							'type'			=> 'checkbox',
							'description'	=> __( 'Show column only for discounted orders', 'wpo_wcpdf_templates' ),
						),
					),
				),
				'regular_price'	=> array (
					'title'		=> __( 'Regular price', 'wpo_wcpdf_templates' ),
					'options'	=> array (
						'label'		=> array(
							'type'			=> 'text',
							'description'	=> __( 'Label', 'wpo_wcpdf_templates' ),
							'placeholder'	=> __( 'Use default', 'wpo_wcpdf_templates' ),
						),
						'price_type'	=> array(
							'type'			=> 'select',
							'options'		=> array(
								'single'	=> __( 'Single price', 'wpo_wcpdf_templates' ),
								'total'		=> __( 'Total price', 'wpo_wcpdf_templates' ),
							),
						),
						'tax'	=> array(
							'type'			=> 'select',
							'options'		=> array(
								'incl'		=> __( 'Including tax', 'wpo_wcpdf_templates' ),
								'excl'		=> __( 'Excluding tax', 'wpo_wcpdf_templates' ),
							),
						),
						'only_sale'	=> array(
							'type'			=> 'checkbox',
							'description'	=> __( 'Only show for items that sold for a sale price', 'wpo_wcpdf_templates' ),
						),
					),
				),
				'discount'	=> array (
					'title'		=> __( 'Discount', 'wpo_wcpdf_templates' ),
					'options'	=> array (
						'label'		=> array(
							'type'			=> 'text',
							'description'	=> __( 'Label', 'wpo_wcpdf_templates' ),
							'placeholder'	=> __( 'Use default', 'wpo_wcpdf_templates' ),
						),
						'price_type'	=> array(
							'type'			=> 'select',
							'options'		=> array(
								'single'	=> __( 'Single price', 'wpo_wcpdf_templates' ),
								'total'		=> __( 'Total price', 'wpo_wcpdf_templates' ),
								'percent'	=> __( 'Percent', 'wpo_wcpdf_templates' ),
							),
						),
						'tax'	=> array(
							'type'			=> 'select',
							'options'		=> array(
								'incl'	=> __( 'Including tax', 'wpo_wcpdf_templates' ),
								'excl'		=> __( 'Excluding tax', 'wpo_wcpdf_templates' ),
							),
						),
						'only_discounted'	=> array(
							'type'			=> 'checkbox',
							'description'	=> __( 'Show column only for discounted orders', 'wpo_wcpdf_templates' ),
						),
					),
				),
				'vat'	=> array (
					'title'		=> __( 'VAT', 'wpo_wcpdf_templates' ),
					'options'	=> array (
						'label'		=> array(
							'type'			=> 'text',
							'description'	=> __( 'Label', 'wpo_wcpdf_templates' ),
							'placeholder'	=> __( 'Use default', 'wpo_wcpdf_templates' ),
						),
						'price_type'	=> array(
							'type'			=> 'select',
							'options'		=> array(
								'single'	=> __( 'Single price', 'wpo_wcpdf_templates' ),
								'total'		=> __( 'Total price', 'wpo_wcpdf_templates' ),
							),
						),
						'discount'	=> array(
							'type'			=> 'select',
							'options'		=> array(
								'before'	=> __( 'Before discount', 'wpo_wcpdf_templates' ),
								'after'		=> __( 'After discount', 'wpo_wcpdf_templates' ),
							),
						),
						'only_discounted'	=> array(
							'type'			=> 'checkbox',
							'description'	=> __( 'Show column only for discounted orders', 'wpo_wcpdf_templates' ),
						),
					),
				),
				'tax_rate'	=> array (
					'title'		=> __( 'Tax rate', 'wpo_wcpdf_templates' ),
					'options'	=> array (
						'label'		=> array(
							'type'			=> 'text',
							'description'	=> __( 'Label', 'wpo_wcpdf_templates' ),
							'placeholder'	=> __( 'Use default', 'wpo_wcpdf_templates' ),
						),
					),
				),
				'weight'			=> array (
					'title'		=> __( 'Weight', 'wpo_wcpdf_templates' ),
					'options'	=> array (
						'label'		=> array(
							'type'			=> 'text',
							'description'	=> __( 'Label', 'wpo_wcpdf_templates' ),
							'placeholder'	=> __( 'Use default', 'wpo_wcpdf_templates' ),
						),
						'qty'	=> array(
							'type'			=> 'select',
							'options'		=> array(
								'single'	=> __( 'Single weight', 'wpo_wcpdf_templates' ),
								'total'		=> __( 'Total weight', 'wpo_wcpdf_templates' ),
							),
						),
						'show_unit'		=> array(
							'type'			=> 'checkbox',
							'description'	=> __( 'Append weight unit', 'wpo_wcpdf_templates' ),
						),
					),
				),
				'product_attribute'	=> array (
					'title'		=> __( 'Attribute', 'wpo_wcpdf_templates' ),
					'options'	=> array (
						'attribute_name' => array(
							'type'			=> 'text',
							'description'	=> __( 'Attribute name', 'wpo_wcpdf_templates' ),
						),
						'label'		=> array(
							'type'			=> 'text',
							'description'	=> __( 'Label', 'wpo_wcpdf_templates' ),
						),
					),
				),
				'product_custom'	=> array (
					'title'		=> __( 'Custom field (Product)', 'wpo_wcpdf_templates' ),
					'options'	=> array (
						'field_name' => array(
							'type'			=> 'text',
							'description'	=> __( 'Field name', 'wpo_wcpdf_templates' ),
						),
						'label'		=> array(
							'type'			=> 'text',
							'description'	=> __( 'Label', 'wpo_wcpdf_templates' ),
						),
					),
				),
				'product_description'	=> array (
					'title'		=> __( 'Product description', 'wpo_wcpdf_templates' ),
					'options'	=> array (
						'label'		=> array(
							'type'			=> 'text',
							'description'	=> __( 'Label', 'wpo_wcpdf_templates' ),
							'placeholder'	=> __( 'Use default', 'wpo_wcpdf_templates' ),
						),
						'description_type'	=> array(
							'type'			=> 'select',
							'options'		=> array(
								'short'		=> __( 'Short description', 'wpo_wcpdf_templates' ),
								'long'		=> __( 'Long description', 'wpo_wcpdf_templates' ),
							),
						),
						'use_variation_description' => array(
							'type'			=> 'checkbox',
							'description'	=> __( 'Use variation description when available', 'wpo_wcpdf_templates' ),
						),
					),
				),
				'product_categories'	=> array (
					'title'		=> __( 'Product categories', 'wpo_wcpdf_templates' ),
					'options'	=> array (
						'label'		=> array(
							'type'			=> 'text',
							'description'	=> __( 'Label', 'wpo_wcpdf_templates' ),
							'placeholder'	=> __( 'Use default', 'wpo_wcpdf_templates' ),
						),
					),
				),
				'cb'	=> array (
					'title'		=> __( 'Checkbox', 'wpo_wcpdf_templates' ),
					'options'		=> array (
						'label'		=> array(
							'type'			=> 'text',
							'description'	=> __( 'Label', 'wpo_wcpdf_templates' ),
						),
					),
				),
				'static_text'	=> array (
					'title'			=> __( 'Static text', 'wpo_wcpdf_templates' ),
					'options'		=> array (
						'label'		=> array(
							'type'			=> 'text',
							'description'	=> __( 'Label', 'wpo_wcpdf_templates' ),
						),
						'text'		=> array(
							'type'			=> 'text',
							'description'	=> __( 'Text', 'wpo_wcpdf_templates' ),
						),
					),
				),
			);
		}

		public function get_totals_field_options() {
			return array (
				'subtotal'	=> array (
					'title'		=> __( 'Subtotal', 'wpo_wcpdf_templates' ),
					'options'	=> array (
						'label'		=> array(
							'type'			=> 'text',
							'description'	=> __( 'Label', 'wpo_wcpdf_templates' ),
							'placeholder'	=> __( 'Use default', 'wpo_wcpdf_templates' ),
						),
						'tax'	=> array(
							'type'			=> 'select',
							'options'		=> array(
								'incl'	=> __( 'Including tax', 'wpo_wcpdf_templates' ),
								'excl'		=> __( 'Excluding tax', 'wpo_wcpdf_templates' ),
							),
						),
						'discount'	=> array(
							'type'			=> 'select',
							'options'		=> array(
								'before'	=> __( 'Before discount', 'wpo_wcpdf_templates' ),
								'after'		=> __( 'After discount', 'wpo_wcpdf_templates' ),
							),
						),
						'only_discounted'	=> array(
							'type'			=> 'checkbox',
							'description'	=> __( 'Show only for discounted orders', 'wpo_wcpdf_templates' ),
						),
					),
				),
				'discount'	=> array (
					'title'		=> __( 'Discount', 'wpo_wcpdf_templates' ),
					'options'	=> array (
						'label'		=> array(
							'type'			=> 'text',
							'description'	=> __( 'Label', 'wpo_wcpdf_templates' ),
							'placeholder'	=> __( 'Use default', 'wpo_wcpdf_templates' ),
						),
						'tax'	=> array(
							'type'			=> 'select',
							'options'		=> array(
								'incl'	=> __( 'Including tax', 'wpo_wcpdf_templates' ),
								'excl'		=> __( 'Excluding tax', 'wpo_wcpdf_templates' ),
							),
						),
						'show_percentage'	=> array(
							'type'			=> 'checkbox',
							'description'	=> __( 'Add discount percentage to label', 'wpo_wcpdf_templates' ),
						),
						'show_codes'	=> array(
							'type'			=> 'checkbox',
							'description'	=> __( 'Add coupon codes to label', 'wpo_wcpdf_templates' ),
						),
					),
				),
				'shipping'	=> array (
					'title'		=> __( 'Shipping', 'wpo_wcpdf_templates' ),
					'options'	=> array (
						'label'		=> array(
							'type'			=> 'text',
							'description'	=> __( 'Label', 'wpo_wcpdf_templates' ),
							'placeholder'	=> __( 'Use default', 'wpo_wcpdf_templates' ),
						),
						'hide_free'		=> array(
							'type'			=> 'checkbox',
							'description'	=> __( 'Hide when free', 'wpo_wcpdf_templates' ),
						),
						'method'		=> array(
							'type'			=> 'checkbox',
							'description'	=> __( 'Show method instead of cost', 'wpo_wcpdf_templates' ),
						),
						'tax'	=> array(
							'type'			=> 'select',
							'options'		=> array(
								'incl'	=> __( 'Including tax', 'wpo_wcpdf_templates' ),
								'excl'	=> __( 'Excluding tax', 'wpo_wcpdf_templates' ),
							),
						),
					),
				),
				'fees'	=> array (
					'title'		=> __( 'Fees', 'wpo_wcpdf_templates' ),
					'options'	=> array (
						'tax'	=> array(
							'type'			=> 'select',
							'options'		=> array(
								'incl'	=> __( 'Including tax', 'wpo_wcpdf_templates' ),
								'excl'		=> __( 'Excluding tax', 'wpo_wcpdf_templates' ),
							),
						),
					),
				),
				'vat'	=> array (
					'title'		=> __( 'VAT', 'wpo_wcpdf_templates' ),
					'options'	=> array (
						'label'		=> array(
							'type'			=> 'text',
							'description'	=> __( 'Label', 'wpo_wcpdf_templates' ),
							'placeholder'	=> __( 'Use default', 'wpo_wcpdf_templates' ),
						),
						'percent'		=> array(
							'type'			=> 'checkbox',
							'description'	=> __( 'Include %', 'wpo_wcpdf_templates' ),
						),
						'base'		=> array(
							'type'			=> 'checkbox',
							'description'	=> __( 'Include tax base/subtotal', 'wpo_wcpdf_templates' ),
						),
						'single_total'		=> array(
							'type'			=> 'checkbox',
							'description'	=> __( 'Single total', 'wpo_wcpdf_templates' ),
						),
						'tax_type'	=> array(
							'type'			=> 'select',
							'options'		=> array(
								'combined'	=> __( 'Combined tax', 'wpo_wcpdf_templates' ),
								'shipping'	=> __( 'Shipping tax', 'wpo_wcpdf_templates' ),
								'product'	=> __( 'Product tax', 'wpo_wcpdf_templates' ),
							),
						),
					),
				),
				'vat_base'	=> array (
					'title'		=> __( 'VAT base/subtotal', 'wpo_wcpdf_templates' ),
					'options'	=> array (
						'label'		=> array(
							'type'			=> 'text',
							'description'	=> __( 'Label', 'wpo_wcpdf_templates' ),
							'placeholder'	=> __( 'Use default', 'wpo_wcpdf_templates' ),
						),
						'percent'		=> array(
							'type'			=> 'checkbox',
							'description'	=> __( 'Include %', 'wpo_wcpdf_templates' ),
						),
					),
				),
				'total'	=> array (
					'title'		=> __( 'Grand total', 'wpo_wcpdf_templates' ),
					'options'	=> array (
						'label'		=> array(
							'type'			=> 'text',
							'description'	=> __( 'Label', 'wpo_wcpdf_templates' ),
							'placeholder'	=> __( 'Use default', 'wpo_wcpdf_templates' ),
						),
						'tax'	=> array(
							'type'			=> 'select',
							'options'		=> array(
								'incl'	=> __( 'Including tax', 'wpo_wcpdf_templates' ),
								'excl'	=> __( 'Excluding tax', 'wpo_wcpdf_templates' ),
							),
						),
					),
				),
				'order_weight'	=> array (
					'title'		=> __( 'Total weight', 'wpo_wcpdf_templates' ),
					'options'	=> array (
						'label'		=> array(
							'type'			=> 'text',
							'description'	=> __( 'Label', 'wpo_wcpdf_templates' ),
							'placeholder'	=> __( 'Use default', 'wpo_wcpdf_templates' ),
						),
						'show_unit'		=> array(
							'type'			=> 'checkbox',
							'description'	=> __( 'Append weight unit', 'wpo_wcpdf_templates' ),
						),
					),
				),
				'total_qty'		=> array (
					'title'		=> __( 'Total quantity', 'wpo_wcpdf_templates' ),
					'options'	=> array (
						'label'		=> array(
							'type'			=> 'text',
							'description'	=> __( 'Label', 'wpo_wcpdf_templates' ),
							'placeholder'	=> __( 'Use default', 'wpo_wcpdf_templates' ),
						),
					),
				),
			);	
		}

		/**
		 * Editor callback.
		 */
		public function columns_editor_callback( $args ) {
			$menu = $args['menu'];
			$id = $args['id'];
		
			$options = get_option( $menu );

			$available_columns = $this->get_columns_field_options();
			$available_totals = $this->get_totals_field_options();
		
			?>
			<div id="documents" style="display:none;">
				<ul class="document-tabs">
					<?php foreach ($args['documents'] as $document => $title) {
						$document_id = $id.'_'.$document;
						printf('<li><a href="#%s">%s</a></li>', $document_id, $title);
					}
					?>
				</ul>

				<?php foreach ($args['documents'] as $document => $title) {
					$document_id = $id.'_'.$document;
					$sections = array(
						'columns'	=> __( 'Columns', 'wpo_wcpdf_templates'),
						'totals'	=> __( 'Totals', 'wpo_wcpdf_templates'),
					);
					printf('<div id="%1$s" class="document-content fields %2$s" data-document-type="%2$s">', $document_id, $document);
						if ( has_filter('wpo_wcpdf_template_editor_defaults') ) {
							printf('<a class="button load-defaults" href="%s">%s</a>', add_query_arg( 'load-defaults', 'true' ), __( 'Load defaults (all documents!)', 'wpo_wcpdf_templates') );
						}
						foreach ($sections as $section_key => $section_title) {
							$document_section = $document_id.'_'.$section_key
							?>
							<h4 class="columns-header">
								<?php echo $section_title; 
								if ( $section_key == 'columns' ) { ?>
									<span>Need help? <a href="https://docs.wpovernight.com/woocommerce-pdf-invoices-packing-slips/using-the-customizer/" target="_blank"><?php _e( 'Using the Customizer', 'wpo_wcpdf_templates'); ?></a></span>
								<?php } ?>
							</h4>

							<?php
							printf( '<div class="document field-list %1$s" data-option="%2$s[%3$s]" data-section_key="%1$s">', $section_key, $menu, $document_section );
							$current = isset( $options[$document_section] ) ? $options[$document_section] : '';
							if (!isset($options['settings_saved']) || isset($_GET['load-defaults'])) {
								$current = apply_filters( 'wpo_wcpdf_template_editor_defaults', $current, $document, $section_key );
							}

							if (!empty($current)) {
								foreach ($current as $key => $field) {
									$available = 'available_'.$section_key;
									if ( isset($field['type']) && in_array( $field['type'], array_keys(${$available}) ) ) {
										$name = sprintf( '%s[%s][%s]', $menu, $document_section, $key);
										$this->display_table_field( $field['type'], ${$available}[$field['type']], $args, $name, $field ); 
									}
								}
							} ?>

							<div class="document field add-field">
								<span class="dashicons dashicons-plus add-field-plus"></span>
								<select class='dropdown-add-field'>
									<?php
									if ($section_key == 'columns') {
										printf( '<option value="default">%s</option>', __( 'Add a column', 'wpo_wcpdf_templates' ) );
										foreach ($available_columns as $column_key => $column) {
											printf( '<option value="%1$s">%2$s</option>', $column_key, $column['title'] );
										}
									} elseif ($section_key == 'totals') {
										printf( '<option value="default">%s</option>', __( 'Add a row', 'wpo_wcpdf_templates' ) );
										foreach ($available_totals as $total_key => $total) {
											printf( '<option value="%1$s">%2$s</option>', $total_key, $total['title'] );
										}
									}
									?>
								</select>
							</div>

							<?php
							echo '</div>'; // document field-list
						}
						?>
						<!-- Custom Blocks -->
						<h4 class="columns-header"><?php echo __( 'Custom blocks', 'wpo_wcpdf_templates') ?><span>Need help? <a href="https://docs.wpovernight.com/woocommerce-pdf-invoices-packing-slips/using-custom-blocks/" target="_blank"><?php _e( 'Using Custom Blocks', 'wpo_wcpdf_templates'); ?></a></span></h4>
						<?php
						$section_key = 'custom';
						$document_section = $document_id.'_'.$section_key;
						printf( '<div class="document field-list custom-blocks" data-option="%1$s[%2$s]" data-section="%2$s">', $menu, $document_section );

						$current = isset( $options[$document_section] ) ? $options[$document_section] : '';
						if (!empty($current)) {
							foreach ($current as $key => $field) {
								$name = sprintf( '%s[%s][%s]', $menu, $document_section, $key);
								$this->display_custom_block( $key, $args, $name, $field );
							}
						}
						?>
						</div>
						<br/><div class="button add-custom-block"><?php echo __( 'Add a block', 'wpo_wcpdf_templates') ?></div>
					</div> <!-- document-content -->
				<?php } ?>
			</div>

			<?php

		}

		public function add_totals_columns_field () {
			$available_columns = $this->get_columns_field_options();
			$available_totals = $this->get_totals_field_options();
			$section = $_POST['section'];
			$field = $_POST['field_value'];
			$column_key = time();

			if ($field == 'default') { die(); }

			if ( $section == 'columns') {
				$column = $available_columns[ $_POST['field_value'] ];
			} elseif ( $section == 'totals') {
				$column = $available_totals[ $_POST['field_value'] ];
			}

			$args = array(
				'menu'			=> 'wpo_wcpdf_editor_settings',
				'id'			=> 'fields',
			);
			$name = sprintf( '%s[fields_%s_%s][%s]', $args['menu'], $_POST['document_type'], $section, $column_key);
			$this->display_table_field( $_POST['field_value'], $column, $args, $name );

			die();
		}

		public function display_table_field ( $field_key, $field, $args, $name = '', $current = '' ) {
			$menu = $args['menu'];
			$id = $args['id'];

			$options_class = isset($field['options']) ? 'options' : '';
			printf( '<div class="field %1$s %2$s" data-name="%2$s" data-option="%3$s[%4$s]">', $options_class, $field_key, $menu, $id);
			?>
			<span class="dashicons dashicons-dismiss delete-field"></span>
			<div class="field-title"><?php echo $field['title']; ?></div>
			<?php
			if (isset($field['options'])) {
				echo '<div class="field-options">';
				foreach ($field['options'] as $option_key => $field_option) {
					$this->display_table_field_options( $option_key, $field_option, $current, $name ); 
				}
				echo '</div>';
			}
			printf('<input type="hidden" data-key="type" name="%s[type]" value="%s">', $name, $field_key);
			?>
			</div>
			<?php
		}

		public function display_table_field_options ($option_key, $field_option, $current, $name = '' ) {
			$name = sprintf('%s[%s]', $name, $option_key);
			$current = !empty($current[$option_key]) ? $current[$option_key] : '';
			echo '<div class="field-option">';
			switch ($field_option['type']) {
				case 'checkbox':
					printf( '<input type="checkbox" data-key="%s" name="%s" value="1" %s>', $option_key, $name, checked( 1, $current, false ) );
					printf( '<span class="option-description">%s</span>', $field_option['description'] );
					break;
				case 'select':
					printf( '<select data-key="%s" name="%s">', $option_key, $name );
					foreach ($field_option['options'] as $select_option_value => $select_option_title) {
						printf( '<option value="%s" %s>%s</option>', $select_option_value, selected( $current, $select_option_value, false ), $select_option_title );
					}
					echo '</select>';
					break;

				case 'text':
					printf( '<span class="option-description">%s: </span>', $field_option['description'] );
					$placeholder = isset($field_option['placeholder']) ? $field_option['placeholder'] : '';
					printf( '<input type="text" data-key="%s" name="%s" value="%s" placeholder="%s">', $option_key, $name, $current, $placeholder );
					break;
				case 'textarea':
					printf( '<div class="option-description">%s: </div>', $field_option['description'] );
					$placeholder = isset($field_option['placeholder']) ? $field_option['placeholder'] : '';
					$cols = isset($field_option['cols']) ? $field_option['cols'] : '';
					$rows = isset($field_option['rows']) ? $field_option['rows'] : '';
					printf( '<textarea data-key="%s" name="%s" placeholder="%s" cols="%s" rows="%s">%s</textarea>', $option_key, $name, $placeholder, $cols, $rows, $current );
					break;
			}
			echo '</div>';
		}

		public function add_custom_block() {
			check_ajax_referer( 'wpo_wcpdf_templates', 'security' );

			$menu = 'wpo_wcpdf_editor_settings';
			$id = 'fields';
			$args = array(
				'menu' 	=> $menu,
				'id'	=> $id
			);
			$key = uniqid();
			$document = $_POST['document_type'];
			$document_section = "{$id}_{$document}_custom";

			$name = sprintf( '%s[%s][%s]', $menu, $document_section, $key);
			$this->display_custom_block( $key , $args, $name );
			die();
		}

		public function display_custom_block ( $field_key, $args, $name = '', $current = '' ) {
			$menu = $args['menu'];
			$id = $args['id'];

			printf( '<div class="custom-block" data-name="%s" data-option="%s[%s]">', $field_key, $menu, $id);

			?>
			<span class="dashicons dashicons-dismiss delete-field"></span>
			<table>
			<?php
			// TYPE
			$types = array(
				'text'			=> __('Text', 'wpo_wcpdf_templates'),
				'custom_field'	=> __('Custom Field', 'wpo_wcpdf_templates'),
				'user_meta'	=> __('User Meta', 'wpo_wcpdf_templates'),
			);
			$option_key = 'type';

			printf( '<tr><td>%1$s:</td><td><select data-key="%2$s" name="%3$s[%2$s]" class="custom-block-type">', __('Type', 'wpo_wcpdf_templates'), $option_key, $name );
			foreach ($types as $value => $title) {
				printf( '<option value="%1$s" %2$s>%3$s</option>', $value, selected( !empty($current[$option_key]) ? $current[$option_key] : '', $value, false ), $title );
			}
			echo '</select></td></tr>';

			// POSITION
			$positions = array(
				'wpo_wcpdf_before_document'			=> __('Before document', 'wpo_wcpdf_templates'),
				'wpo_wcpdf_after_document_label'	=> __('After the document label', 'wpo_wcpdf_templates'),
				'wpo_wcpdf_before_billing_address'	=> __('Before the billing address', 'wpo_wcpdf_templates'),
				'wpo_wcpdf_after_billing_address'	=> __('After the billing address', 'wpo_wcpdf_templates'),
				'wpo_wcpdf_before_shipping_address'	=> __('Before the shipping address', 'wpo_wcpdf_templates'),
				'wpo_wcpdf_after_shipping_address'	=> __('After the shipping address', 'wpo_wcpdf_templates'),
				'wpo_wcpdf_before_order_data'		=> __('Before the order data (invoice number, order date, etc.)', 'wpo_wcpdf_templates'),
				'wpo_wcpdf_after_order_data'		=> __('After the order data', 'wpo_wcpdf_templates'),
				'wpo_wcpdf_before_customer_notes'	=> __('Before the customer notes', 'wpo_wcpdf_templates'),
				'wpo_wcpdf_after_customer_notes'	=> __('After the customer notes', 'wpo_wcpdf_templates'),
				'wpo_wcpdf_before_order_details'	=> __('Before the order details table with all items', 'wpo_wcpdf_templates'),
				'wpo_wcpdf_after_order_details'		=> __('After the order details table', 'wpo_wcpdf_templates'),
				'wpo_wcpdf_after_document'			=> __('After document', 'wpo_wcpdf_templates'),
			);
			$option_key = 'position';

			printf( '<tr><td>%1$s:</td><td><select data-key="%2$s" name="%3$s[%2$s]">', __('Position', 'wpo_wcpdf_templates'), $option_key, $name );
			foreach ($positions as $value => $title) {
				printf( '<option value="%1$s" %2$s>%3$s</option>', $value, selected( !empty($current[$option_key]) ? $current[$option_key] : '', $value, false ), $title );
			}
			echo '</select></td></tr>';
			
			// LABEL / HEADER
			$option_key = 'label';
			printf( '<tr><td>%1$s:</td><td><input type="text" data-key="%2$s" name="%3$s[%2$s]" value="%4$s"></td></tr>', __('Label / header', 'wpo_wcpdf_templates'), $option_key, $name, !empty($current[$option_key]) ? $current[$option_key] : '' );

			// FIELD NAME
			$option_key = 'meta_key';
			printf( '<tr class="meta_key"><td>%1$s:</td><td><input type="text" data-key="%2$s" name="%3$s[%2$s]" value="%4$s"></td></tr>', __('Field name / meta key', 'wpo_wcpdf_templates'), $option_key, $name, !empty($current[$option_key]) ? $current[$option_key] : '' );

			// TEXT
			$option_key = 'text';
			printf( '<tr class="custom_text"><td colspan="2">%1$s:<br/><textarea data-key="%2$s" name="%3$s[%2$s]" rows="8">%4$s</textarea></td></tr>', __('Text', 'wpo_wcpdf_templates'), $option_key, $name, !empty($current[$option_key]) ? $current[$option_key] : '' );

			// HIDE IF EMPTY
			$option_key = 'hide_if_empty';
			$current_hide_if_empty = !empty($current[$option_key]) ? $current[$option_key] : '';
			printf( '<tr class="hide_if_empty"><td>%1$s:</td><td><input type="checkbox" data-key="%2$s" name="%3$s[%2$s]" value="1" %4$s></td></tr>', __("Don't show if empty", 'wpo_wcpdf_templates'), $option_key, $name, checked( 1, $current_hide_if_empty, false ) );
			?>
			</table>
			</div>
			<?php
		}

		public function get_footer_height() {
			$footer_height = isset( WPO_WCPDF()->settings->general_settings['footer_height'] ) ? WPO_WCPDF()->settings->general_settings['footer_height'] : '';
			return $footer_height;
		}

		/**
		 * Add extra setting for the footer height to the template settings
		 */
		public function add_footer_height_setting( $settings_fields, $page, $option_group, $option_name ) {

			$footer_height_setting = array(
				'type'		=> 'setting',
				'id'		=> 'footer_height',
				'title'		=> __( 'Footer height', 'wpo_wcpdf_templates' ),
				'callback'	=> 'text_input',
				'section'	=> 'general_settings',
				'args'		=> array(
					'option_name'	=> $option_name,
					'id'			=> 'footer_height',
					'size'			=> '5',
					'description'	=> __( 'Enter the total height of the footer in mm, cm or in and use a dot for decimals.<br/>For example: 1.25in or 82mm', 'wpo_wcpdf_templates' )
				)
			);

			$settings_fields = $this->insert_after_setting( $settings_fields, $footer_height_setting, 'footer');
			return $settings_fields;
		}

		public function insert_after_setting( $settings, $new_setting, $insert_after_id ) {
			// search setting with $insert_after_id
			foreach ($settings as $key => $setting) {
				if ($setting['type'] == 'setting' && $setting['id'] == $insert_after_id) {
					$insert_pos = array_search($key, array_keys($settings)) + 1;
				}
			}

			// simply append if position not found
			if (empty($insert_pos)) {
				return array_merge( $settings, array( $new_setting ) );
			}

			// splicemup!
			array_splice( $settings, $insert_pos, 0, array( $new_setting ) );

			return $settings;
		}

		/**
		 * Validate options.
		 *
		 * @param  array $input options to valid.
		 *
		 * @return array		validated options.
		 */
		public function validate_options( $input ) {
			// no validation required at this point!
			$output = $input;
					
			// Return the array processing any additional functions filtered by this action.
			return apply_filters( 'wpo_wcpdf_validate_input', $output, $input );
		}

		/**
		 * Remove load-defaults query variable after option is updated (to prevent loading the defaults again)
		 */
		public function remove_load_defaults_after_updating_option( $option, $old_value, $value ) {
			if ($option == 'wpo_wcpdf_editor_settings') {
				add_filter( 'wp_redirect', function( $location, $status ) {
					return remove_query_arg( 'load-defaults', $location );
				}, 10, 2 );
			}
		}

	} // end class
} // end class_exists

return new WooCommerce_PDF_IPS_Templates_Settings();
