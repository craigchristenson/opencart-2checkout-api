<?php
class ControllerExtensionPaymentTwoCheckoutPP extends Controller {
	public function index() {
    	$data['button_confirm'] = $this->language->get('button_confirm');

		$this->load->model('checkout/order');
		
		$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
		
		$data['action'] = 'https://www.2checkout.com/checkout/purchase';

		$data['sid'] = $this->config->get('twocheckout_pp_account');
		$data['currency_code'] = $order_info['currency_code'];
		$data['total'] = $this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], false);
		$data['cart_order_id'] = $this->session->data['order_id'];
		$data['card_holder_name'] = $order_info['payment_firstname'] . ' ' . $order_info['payment_lastname'];
		$data['street_address'] = $order_info['payment_address_1'];
		$data['city'] = $order_info['payment_city'];
		
		if ($order_info['payment_iso_code_2'] == 'US' || $order_info['payment_iso_code_2'] == 'CA') {
			$data['state'] = $order_info['payment_zone'];
		} else {
			$data['state'] = 'XX';
		}
		
		$data['zip'] = $order_info['payment_postcode'];
		$data['country'] = $order_info['payment_country'];
		$data['email'] = $order_info['email'];
		$data['phone'] = $order_info['telephone'];
		
		if ($this->cart->hasShipping()) {
			$data['ship_street_address'] = $order_info['shipping_address_1'];
			$data['ship_city'] = $order_info['shipping_city'];
			$data['ship_state'] = $order_info['shipping_zone'];
			$data['ship_zip'] = $order_info['shipping_postcode'];
			$data['ship_country'] = $order_info['shipping_country'];
		} else {
			$data['ship_street_address'] = $order_info['payment_address_1'];
			$data['ship_city'] = $order_info['payment_city'];
			$data['ship_state'] = $order_info['payment_zone'];
			$data['ship_zip'] = $order_info['payment_postcode'];
			$data['ship_country'] = $order_info['payment_country'];			
		}
		
		$data['products'] = array();
		
		$products = $this->cart->getProducts();

		foreach ($products as $product) {
			$data['products'][] = array(
				'product_id'  => $product['product_id'],
				'name'        => $product['name'],
				'description' => $product['name'],
				'quantity'    => $product['quantity'],
				'price'		  => $this->currency->format($product['price'], $order_info['currency_code'], $order_info['currency_value'], false)
			);
		}
		
		$data['lang'] = $this->session->data['language'];

		$data['x_receipt_link_url'] = $this->url->link('extension/payment/twocheckout_pp/callback', '', 'SSL');
		$data['return_url'] = $this->url->link('checkout/checkout', '', 'SSL');
		
		return $this->load->view('extension/payment/twocheckout_pp', $data);
	}
	
	public function callback() {
		$this->load->model('checkout/order');
		
		$order_info = $this->model_checkout_order->getOrder($this->request->request['cart_order_id']);
		$order_number = $this->request->request['order_number'];
		echo "$this->config->get('twocheckout_pp_secret') \n";
		echo "$this->config->get('twocheckout_pp_account') \n";
		print_r($this->request->request);
		if (strtoupper(md5($this->config->get('twocheckout_pp_secret') . $this->config->get('twocheckout_pp_account') . $order_number . $this->request->request['total'])) == $this->request->request['key']) {
      echo 'MD5 matched';
			if ($this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], false) == $this->request->request['total']) {
				$this->model_checkout_order->addOrderHistory($this->request->request['cart_order_id'], $this->config->get('twocheckout_pp_order_status_id'));
			} else {
				$this->model_checkout_order->addOrderHistory($this->request->request['cart_order_id'], $this->config->get('config_order_status_id'));
			}
			
			echo '<html>' . "\n";
			echo '<head>' . "\n";
			echo '  <meta http-equiv="Refresh" content="0; url=' . $this->url->link('checkout/success') . '">' . "\n";
			echo '</head>'. "\n";
			echo '<body>' . "\n";
			echo '  <p>Please follow <a href="' . $this->url->link('checkout/success') . '">link</a>!</p>' . "\n";
			echo '</body>' . "\n";
			echo '</html>' . "\n";
			exit();
		} else {
      echo "MD5 not matched \n";
			echo 'The response from 2checkout.com can\'t be parsed. Contact site administrator, please!'; 
		}
	}
}
?>
