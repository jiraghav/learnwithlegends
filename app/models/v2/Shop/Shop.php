<?php

namespace v2\Shop;

use Orders;
use v2\Models\Wallet;
use v2\Shop\Payments\Rave;
use v2\Models\Wallet\Journals;
use v2\Shop\Payments\Paystack;
use app\models\ProspectiveUser;
use v2\Shop\Payments\Paypal\PayPal;
use v2\Shop\Payments\Stripe\Stripe;
use v2\Shop\Payments\Website\Website;
use Exception, SiteSettings, Config, Session, MIS;
use v2\Shop\Payments\CoinbaseCommerce\CoinbaseCommerce;

/**
 * 
 */
class Shop
{

	public $available_payment_method;
	public $available_orders;
	private $payment_method;
	private $payment_type;
	private $order;


	function __construct($argument = null)
	{
		$this->setup_available_payment_method();
		$this->setup_available_orders();
	}



	public function __get($property_name)
	{
		return $this->$property_name;
	}

	public function get_available_payment_methods()
	{
		$available = array_filter($this->available_payment_method,  function ($gateway) {

			return $gateway['available'] == true;
		});

		return $available;
	}


	private function setup_available_payment_method()
	{

		$payments_settings = SiteSettings::payment_gateway_settings()->keyBy('criteria');


		$this->available_payment_method = [

			'paypal' => [
				'name' => 'PayPal',
				'class' => PayPal::class,
				'available' => $payments_settings['paypal_keys']->settingsArray['mode']['available']
			],

			'stripe' => [
				'name' => 'Stripe',
				'class' => Stripe::class,
				'available' => $payments_settings['stripe_keys']->settingsArray['mode']['available']
			],

			'wallet' => [
				'name' => 'Wallet',
				'class' => Website::class,
				'available' => 1
			],


			/*
			'coinpay' => [
				'name' => 'CoinwayPay',
				'class' => 'CoinPay',
				'available' => $payments_settings['coinpay_keys']->settingsArray['mode']['available']
			],
			
			
			'paystack' => [
				'name' => 'Paystack',
				'class' => Paystack::class,
				'available' => $payments_settings['paystack_keys']->settingsArray['mode']['available']
			],
			'bank_transfer' => [
				'name' => 'bank transfer',
				'class' => 'banktransfer',
				'available' => $payments_settings['bank_transfer']->settingsarray['mode']['available']
			],

			
			'coinbase_commerce_keys' => [
				'name' => 'Bitcoin - Coinbase',
				'class' => CoinbaseCommerce::class,
				'available' => $payments_settings['coinbase_commerce_keys']->settingsarray['mode']['available']
			],

			*/

			/* 	'rave' => [
				'name' => 'Flutterwave(Rave)',
				'class' => Rave::class,
				'available' => $payments_settings['flutter_wave_keys']->settingsArray['mode']['available']
			], */


		];
	}

	private function setup_available_orders()
	{

		$this->available_type_of_orders = [

			'deposit' => [
				'name' => 'Deposit',
				'class' => Journals::class,
				'namespace' => "",
				'available' => true
			],


			'prospective_user' => [
				'name' => 'Prospective User',
				'class' => ProspectiveUser::class,
				'namespace' => "",
				'available' => true
			],

			'packages' => [
				'name' => 'Packages',
				'class' => 'SubscriptionOrder',
				'namespace' => "",
				'available' => true
			],

			'product' => [
				'name' => 'Packages',
				'class' => Orders::class,
				'namespace' => "",
				'available' => true
			],
		];
	}



	public static function empty_cart_in_session()
	{
		unset($_SESSION['cart']);
		unset($_SESSION['shop_checkout_id']);
	}




	public function verifyPayment()
	{
		$this->setPaymentMethod($this->order->payment_method);
		$verification =  ($this->payment_method->verifyPayment($this->order));


		//payment confirmed
		if ($verification['confirmation']['status'] == 1) {
			$this->order->mark_paid();
			self::empty_cart_in_session();
			//clear session 
		}

		return $this;
	}





	public function reVerifyPayment()
	{

		$this->setPaymentMethod($this->order->payment_method);
		$verification =  ($this->payment_method->reVerifyPayment($this->order));

		//payment confirmed
		if ($verification['confirmation']['status'] == 1) {
			$this->order->mark_paid();
			self::empty_cart_in_session();
			//clear session 
		}

		return $this;
	}





	public function vatBreakdown($amount, $amount_is = 'before_vat')
	{


		$setting = \SiteSettings::find_criteria('site_settings')->settingsArray;
		$vat_percent = $setting['vat_percent'];



		switch ($amount_is) {

			case 'before_vat':

				$vat = $vat_percent * 0.01 * $amount;
				$after_vat = $amount + $vat;

				$breakdown = [
					'value' => $vat,
					'percent' => $vat_percent,
					'before_vat' => $amount,
					'after_vat' => $after_vat,
				];

				break;

			case 'after_vat':

				$before_vat = ($amount * 100) / ($vat_percent + 100);
				$vat = $vat_percent * 0.01 * $before_vat;


				$breakdown = [
					'value' => $vat,
					'percent' => $vat_percent,
					'before_vat' => $before_vat,
					'after_vat' => $amount,
				];

				break;

			default:
				# code...
				break;
		}


		return $breakdown;
	}



	public function stampDutyBreakdown($amount, $amount_is = 'before_stampduty')
	{

		$setting = \SiteSettings::find_criteria('site_settings')->settingsArray;

		$charge_stamp_duty_from = $setting['charge_stamp_duty_from'];
		$stamp_duty = $setting['stamp_duty'];

		switch ($amount_is) {
			case 'before_stampduty':

				if ($amount >= $charge_stamp_duty_from) {

					$charge = $stamp_duty;
				} else {

					$charge = 0;
				}

				$after_stamp_duty = $stamp_duty + $amount;

				$breakdown = [
					'before_stampduty' => $amount,
					'stamp_duty' => $charge,
					'after_stamp_duty' => $after_stamp_duty
				];

				break;

			case 'after_stampduty':

				$before_stampduty = $amount - $stamp_duty;


				if ($before_stampduty >= $charge_stamp_duty_from) {

					$charge = $stamp_duty;
				} else {

					$charge = 0;
				}

				$before_stampduty =  $amount  - $charge;

				$breakdown = [
					'before_stampduty' => $before_stampduty,
					'stamp_duty' => $charge,
					'after_stamp_duty' => $amount
				];

				break;

			default:
				# code...
				break;
		}


		return $breakdown;
	}




	public function paymentBreakdown()
	{
		//subtotal
		//service_fee
		//stamp_duty
		//vat
		//gateway fee

		$subtotal = $this->order->total_price();

		// $stamp_duty = $this->stampDutyBreakdown($subtotal)['stamp_duty'];
		$stamp_duty = 0;



		$service_fee = 0;
		$vat = $this->order->calculate_vat();

		$subtotal_payable = $subtotal + $service_fee + $stamp_duty + $vat['value'];

		// $gateway_fee = $this->payment_method->gatewayChargeOn($subtotal_payable);
		$gateway_fee = 0;
		$total_payable = $subtotal_payable + $gateway_fee;
		$gateway_name = $this->payment_method->name;


		$breakdown = [
			/*'actual_order' => [
								'value'=> $this->order->amount,
								'name' => 'Order',
							],*/

			/* 	'subtotal' => [
				'value' => $subtotal,
				'name' => 'Sub Total',
			],
 			*/

			'service_fee' => [
				'value' => $service_fee,
				'name' => "Service Charge({$service_fee}%)",
			],

			'vat' => [
				'value' => $vat['value'],
				'name' => "VAT({$vat['percent']}%)",
			],

			'stamp_duty' => [
				'value' => 0,
				'name' => 'Stamp Duty',
			],
			/* 'subtotal_payable' => [
				'value' => $subtotal_payable,
				'name' => 'Grand Total',
			], */

			'gateway_fee' => [
				'value' => $gateway_fee,
				'name' => ucfirst($gateway_name) . " Fee",
			],

			'total_payable' => [
				'value' => $total_payable,
				'name' => 'Total Payable',
			],

		];
		return $breakdown;
	}



	public function fetchPaymentBreakdown()
	{
		$breakdown = $this->paymentBreakdown();
		$currency = '$';
		$line = '';
		foreach ($breakdown as $key => $value) {
			if ($value['value'] == 0) {
				continue;
			}
			$amt =  MIS::money_format($value['value']);
			$size = '';
			if ($value == end($breakdown)) {
				$size = 'font-size:20px;font-weight:700;';
			}

			$line .= "                                   
							<tr>
							<th style='padding: 5px;'>{$value['name']}</th>
							<td class='text-right' style='padding: 5px;$size'>$currency$amt

							</td>  
							</tr>
							";
		}


		$breakdown['line'] = $line;

		return $breakdown;
	}






	public function setPaymentType($payment_type = 'one_time')
	{
		$this->payment_type = $payment_type;
		$this->payment_method->setPaymentType($this->payment_type);
		return $this;
	}


	public function setOrder($order)
	{
		$this->order = $order;
		return $this;
	}



	public function initializePayment()
	{

		$this->payment_method->initializePayment($this->order);

		return $this;
	}

	public function setPaymentMethod($payment_method)
	{

		$method = $this->available_payment_method[$payment_method];

		if ($method['available'] != true) {
			throw new Exception("{$method['name']} is not available", 1);
		}


		$full_class_name = $method['class'];


		$this->payment_method = new  $full_class_name;
		$this->payment_method/* ->setShop($this) */->setOrder($this->order);


		return $this;
	}

	public function setOrderType($order_type)
	{

		$method = $this->available_type_of_orders[$order_type];

		$full_class_name = $method['namespace'] . '\\' . $method['class'];

		if ($method['available'] != true) {
			throw new Exception("{$method['name']} is not available", 1);
		}

		$this->order = new  $full_class_name;
		return $this;
	}


	public function receiveOrder(array $cart)
	{

		$this->order = 	$this->order->create_order($cart);

		return $this;
	}


	public function cancelAgreement()
	{
		$this->setPaymentMethod($this->order->payment_method);
		$execution =  $this->payment_method->cancelAgreement();
	}

	public function fetchAgreement()
	{
		$this->setPaymentMethod($this->order->payment_method);
		$agreement =  $this->payment_method->fetchAgreement();

		return $agreement;
	}

	public function executeAgreement()
	{

		$this->setPaymentMethod($this->order->payment_method);
		$execution =  $this->payment_method->executeAgreement();



		$previous_sub = $this->order->user->subscription;
		if ($previous_sub->payment_state == 'automatic') {
			$previous_sub->cancelAgreement();
		}


		//payment confirmed
		if ($execution['confirmation']['status'] == 1) {
			// print_r($execution);

			$this->order->update_agreement_id($execution['result']->getId());
			$this->order->mark_paid();
			self::empty_cart_in_session();
			//clear session 
		}

		return $this;
	}


	public function goToGateway()
	{
		$this->payment_method->goToGateway();
	}




	public function attemptPayment()
	{


		$payment_breakdown =  $this->paymentBreakdown();
		$this->order->setPaymentBreakdown($payment_breakdown); // important


		$this->payment_attempt_details = $this->payment_method->attemptPayment($this->order);

		// $this->payment_attempt_details['api_keys'] = [];

		return $this->payment_attempt_details;
	}
}
