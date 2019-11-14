<?php
class ControllerExtensionPaymentTwoCheckoutApi extends Controller {
    public function index() {
        $this->load->language('extension/payment/twocheckout_api');

	$this->load->library('twocheckout');

        $data['months'] = array();

        for ($i = 1; $i <= 12; $i++) {
            $data['months'][] = array(
                'text'  => strftime('%B', mktime(0, 0, 0, $i, 1, 2000)),
                'value' => sprintf('%02d', $i)
            );
        }

        $today = getdate();

        $data['year_expire'] = array();

        for ($i = $today['year']; $i < $today['year'] + 11; $i++) {
            $data['year_expire'][] = array(
                'text'  => strftime('%Y', mktime(0, 0, 0, 1, 1, $i)),
                'value' => strftime('%Y', mktime(0, 0, 0, 1, 1, $i))
            );
        }

        $data['twocheckout_api_sid'] = $this->config->get('payment_twocheckout_api_account');
        $data['twocheckout_api_public_key'] = $this->config->get('payment_twocheckout_api_public_key');
        $data['twocheckout_api_test'] = $this->config->get('payment_twocheckout_api_test');

        return $this->load->view('extension/payment/twocheckout_api', $data);
    }

    public function send() {
        $this->load->model('checkout/order');
        $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);

        $params = array(
            "sellerId" => $this->config->get('payment_twocheckout_api_account'),
            "merchantOrderId" => $this->session->data['order_id'],
            "token"      => $this->request->get['token'],
            "currency"   => $order_info['currency_code'],
            "total"      => $this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], false),
            "billingAddr" => array(
            "name" => $order_info['payment_firstname'] . ' ' . $order_info['payment_lastname'],
            "addrLine1" => $order_info['payment_address_1'],
            "addrLine2" => $order_info['payment_address_2'],
            "city" => $order_info['payment_city'],
            "state" => ($order_info['payment_iso_code_2'] == 'US' || $order_info['payment_iso_code_2'] == 'CA') ? $order_info['payment_zone'] : 'XX',
            "zipCode" => $order_info['payment_postcode'],
            "country" => $order_info['payment_country'],
            "email" => $order_info['email'],
            "phoneNumber" => $order_info['telephone']
            )
        );
        if ($this->cart->hasShipping()) {
            $shipping = array(
                "shippingAddr" => array(
                    "name" => $order_info['shipping_firstname'] . ' ' . $order_info['shipping_lastname'],
                    "addrLine1" => $order_info['shipping_address_1'],
                    "addrLine2" => $order_info['shipping_address_2'],
                    "city" => $order_info['shipping_city'],
                    "state" => $order_info['shipping_zone'],
                    "zipCode" => $order_info['shipping_postcode'],
                    "country" => $order_info['shipping_country'],
                    "email" => $order_info['email'],
                    "phoneNumber" => $order_info['telephone']
                )
            );
            $params = array_merge($params,$shipping);
        }

        $sandbox = $this->config->get('payment_twocheckout_api_test');
        $charge = Twocheckout::auth(
	    $this->config->get('payment_twocheckout_api_account'),
	    $this->config->get('payment_twocheckout_api_private_key'),
	    $sandbox,
	    $params
        );
        if (isset($charge['error'])) {
	    $this->response->setOutput(json_encode($charge));
        } else {
	    $message = '2Checkout Order: ' . $charge['response']['orderNumber'];
	    $this->model_checkout_order->addOrderHistory($this->session->data['order_id'], $this->config->get('payment_twocheckout_api_order_status_id'), '', true);
	    $charge['oc_redirect'] = $this->url->link('checkout/success', '', true);
	    $this->response->setOutput(json_encode($charge));
        }
    }
}
