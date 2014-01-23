<?php

require_once dirname(dirname(dirname(__FILE__))) . '/lib/Twocheckout/TwocheckoutApi.php';

class ControllerPaymentTwoCheckout extends Controller {
    protected function index() {
        $this->language->load('payment/twocheckout');

        $this->data['text_credit_card'] = $this->language->get('text_credit_card');
        $this->data['text_wait'] = $this->language->get('text_wait');

        $this->data['entry_cc_owner'] = $this->language->get('entry_cc_owner');
        $this->data['entry_cc_number'] = $this->language->get('entry_cc_number');
        $this->data['entry_cc_expire_date'] = $this->language->get('entry_cc_expire_date');
        $this->data['entry_cc_cvv2'] = $this->language->get('entry_cc_cvv2');

        $this->data['button_confirm'] = $this->language->get('button_confirm');

        $this->data['months'] = array();

        for ($i = 1; $i <= 12; $i++) {
            $this->data['months'][] = array(
                'text'  => strftime('%B', mktime(0, 0, 0, $i, 1, 2000)),
                'value' => sprintf('%02d', $i)
            );
        }

        $today = getdate();

        $this->data['year_expire'] = array();

        for ($i = $today['year']; $i < $today['year'] + 11; $i++) {
            $this->data['year_expire'][] = array(
                'text'  => strftime('%Y', mktime(0, 0, 0, 1, 1, $i)),
                'value' => strftime('%Y', mktime(0, 0, 0, 1, 1, $i))
            );
        }

        $this->data['twocheckout_sid'] = $this->config->get('twocheckout_account');
        $this->data['twocheckout_public_key'] = $this->config->get('twocheckout_public_key');
        $this->data['twocheckout_test'] = $this->config->get('twocheckout_test');

        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/twocheckout.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/payment/twocheckout.tpl';
        } else {
            $this->template = 'default/template/payment/twocheckout.tpl';
        }

        $this->render();
    }


    public function send() {
        $this->load->model('checkout/order');
        $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);

        $params = array(
            "sellerId" => $this->config->get('twocheckout_account'),
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

        try {
            if ($this->config->get('twocheckout_test')) {
                TwocheckoutApi::setCredentials($this->config->get('twocheckout_account'), $this->config->get('twocheckout_private_key'), 'sandbox');
            } else {
                TwocheckoutApi::setCredentials($this->config->get('twocheckout_account'), $this->config->get('twocheckout_private_key'));
            }
            $charge = Twocheckout_Charge::auth($params);

            $this->model_checkout_order->confirm($this->session->data['order_id'], $this->config->get('config_order_status_id'));
            $message = '2Checkout Order: ' . $charge['response']['orderNumber'];
            $this->model_checkout_order->update($this->session->data['order_id'], $this->config->get('twocheckout_order_status_id'), $message, false);
            $charge['oc_redirect'] = $this->url->link('checkout/success', '', 'SSL');
            $this->response->setOutput(json_encode($charge));

        } catch (Twocheckout_Error $e) {
            $error = array(
              "error" => $e->getMessage()
            );
            $this->response->setOutput(json_encode($error));
        }

    }
}
?>
