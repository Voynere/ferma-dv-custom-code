<?php


namespace WCSTORES\WC\MS\Controller\Sync;


use WCSTORES\WC\MS\Queues\Queues;
use WmsOrderController;
use Exception;
use WmsOrderApi;

/**
 * Class OrderController
 * @package WCSTORES\WC\MS\Controller\Sync
 */
class OrderController extends SyncController {
	/**
	 * @var false|mixed|void
	 */
	protected $settings;

	/**
	 * @var bool
	 */
	protected bool $isActivate;

	/**
	 * OrderController constructor.
	 */
	public function __construct() {
		$this->settings = get_option( 'wms_settings_order', array() );

		$this->isActivate = ( isset( $this->settings['wms_active_order'] ) && $this->settings['wms_active_order'] == 'on' );
	}


	/**
	 * @param $type
	 * @param $action
	 * @param $href
	 *
	 * @return mixed
	 * @throws Exception
	 */
	public function webhook( $type, $action, $href ) {
		$customerorder = new WmsOrderController();
		$customerorder->update_order_wc( $href, $action );

		return $type;
	}

    /**
     * @throws Exception
     */
    public function queueUpTasksByCreateOrderStoreApi(\WC_Order $order ) {
		return $this->queueUpTasksByCreateOrder( $order->get_id() ) ;
	}


	/**
	 * @param $orderId
	 *
	 * @return int|void
	 * @throws \Exception
	 */
	public function queueUpTasksByCreateOrder( $orderId ) {

		if ( ! $this->isActivate ) {
			return 0;
		}

		if ( isset( $this->settings['wms_order_type_send'] ) && $this->settings['wms_order_type_send'] == 'autotime' ) {

			$time = ( isset( $this->settings['wms_order_auto_time'] ) ) ? $this->settings['wms_order_auto_time'] : 5;

			return Queues::addSingle(
				( time() + ( $time * 60 ) ),
				'create_an_order_in_moysklad',
				[
					'orderId' => $orderId
				],
				'moysklad',
				true );

		} elseif ( isset( $this->settings['wms_order_type_send'] ) && $this->settings['wms_order_type_send'] == 'auto' ) {

			return Queues::addAsync(
				'create_an_order_in_moysklad',
				[
					'orderId' => $orderId
				],
				'moysklad',
				true,
				0
			);

		}

	}

	/**
	 * @param $orderId
	 *
	 * @return int
	 * @throws Exception
	 */
	public function queueUpTasksByPaymentComplete( $orderId ) {
		if ( ! $this->isActivate ) {
			return 0;
		}

		if ( isset( $this->settings['wms_states_ms_successful_payment'] ) and $this->settings['wms_states_ms_successful_payment'] != false ) {
			return Queues::addAsync(
				'payment_complete_an_order_in_moysklad',
				[
					'orderId' => $orderId
				],
				'moysklad',
				false,
				0
			);
		}
	}


	/**
	 * @param $orderId
	 *
	 * @return false|int|void
	 * @throws Exception
	 */
	public function queueUpTasksByStatusChanged( $orderId ) {
		if ( ! $this->isActivate ) {
			return 0;
		}

		if ( isset( $this->settings['wms_update_order_ms'] ) && $this->settings['wms_update_order_ms'] == 'on' ) {
			return Queues::addAsync(
				'status_changed_an_order_in_moysklad',
				[
					'orderId' => $orderId
				],
				'moysklad',
				false,
				0
			);
		}
	}


	/**
	 * @param $orderId
	 *
	 * @return mixed
	 * @throws Exception
	 */
	public function createAnOrderInMoysklad( $orderId ) {
		$order = wc_get_order( $orderId );
		if ( $order && $order->get_meta( '_ms_order_id' ) ) {
			return 'Заказ уже выгружен в Мой склад';
		}

		$OrderApi = new WmsOrderApi( $orderId );

		return $OrderApi->create_order_ms( $orderId );
	}

	/**
	 * @param $orderId
	 *
	 * @return string|void
	 * @throws Exception
	 */
	public function paymentCompleteAnOrderInMoysklad( $orderId ) {
		$settings = get_option( 'wms_settings_order' );

		$order = wc_get_order( $orderId );

		if ( $order && $order->is_paid() && $order->get_date_paid() ) {
			$order_wc = new WmsOrderApi( $orderId );

			return $order_wc->update_order_ms(
				array(
					'state'   => 'yes',
					'stateId' => $settings['wms_states_ms_successful_payment']
				),
				false );
		}

		return false;


	}

	/**
	 * @param $orderId
	 *
	 * @return string|void
	 * @throws Exception
	 */
	public function statusChangedAnOrderInMoysklad( $orderId ) {
		$order_wc = new WmsOrderApi( $orderId );

		return $order_wc->update_order_ms( array( 'state' => 'yes' ) );
	}


	/**
	 * @param $query
	 * @param $query_vars
	 *
	 * @return mixed
	 */
	public function handleCustomQueryVarOrderUuid( $query, $query_vars = array() ) {
		if ( ! empty( $query_vars['wcs_ms_order_uuid'] ) ) {
			$query['meta_query'][] = array(
				'key'   => '_ms_order_id',
				'value' => esc_attr( $query_vars['wcs_ms_order_uuid'] ),
			);
		}

		return $query;
	}

}
