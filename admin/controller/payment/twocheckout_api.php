<?php 
class ControllerPaymentTwoCheckoutApi extends Controller {
	private $error = array(); 

	public function index() {
		$this->load->language('payment/twocheckout_api');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('setting/setting');
			
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('twocheckout_api', $this->request->post);				
			
			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('extension/payment', 'token=' . $this->session->data['token'], true));
		}

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['text_all_zones'] = $this->language->get('text_all_zones');
		$data['text_yes'] = $this->language->get('text_yes');
		$data['text_no'] = $this->language->get('text_no');
		
		$data['entry_account'] = $this->language->get('entry_account');
		$data['entry_public_key'] = $this->language->get('entry_public_key');
        $data['entry_private_key'] = $this->language->get('entry_private_key');
		$data['entry_test'] = $this->language->get('entry_test');
		$data['entry_total'] = $this->language->get('entry_total');	
		$data['entry_order_status'] = $this->language->get('entry_order_status');		
		$data['entry_geo_zone'] = $this->language->get('entry_geo_zone');
		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_sort_order'] = $this->language->get('entry_sort_order');
		
		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');
		 
		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}
		
		if (isset($this->error['account'])) {
			$data['error_account'] = $this->error['account'];
		} else {
			$data['error_account'] = '';
		}
		
  		$data['breadcrumbs'] = array();

   		$data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),       		
      		'separator' => false
   		);

   		$data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_payment'),
			'href'      => $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);

   		$data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('payment/twocheckout_api', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);
				
		$data['action'] = $this->url->link('payment/twocheckout_api', 'token=' . $this->session->data['token'], 'SSL');
		
		$data['cancel'] = $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL');
		
		if (isset($this->request->post['twocheckout_api_account'])) {
			$data['twocheckout_api_account'] = $this->request->post['twocheckout_api_account'];
		} else {
			$data['twocheckout_api_account'] = $this->config->get('twocheckout_api_account');
		}

        if (isset($this->request->post['twocheckout_api_public_key'])) {
            $data['twocheckout_api_public_key'] = $this->request->post['twocheckout_api_public_key'];
        } else {
            $data['twocheckout_api_public_key'] = $this->config->get('twocheckout_api_public_key');
        }

        if (isset($this->request->post['twocheckout_api_private_key'])) {
            $data['twocheckout_api_private_key'] = $this->request->post['twocheckout_api_private_key'];
        } else {
            $data['twocheckout_api_private_key'] = $this->config->get('twocheckout_api_private_key');
        }
		
		if (isset($this->request->post['twocheckout_api_test'])) {
			$data['twocheckout_api_test'] = $this->request->post['twocheckout_api_test'];
		} else {
			$data['twocheckout_api_test'] = $this->config->get('twocheckout_api_test');
		}
		
		if (isset($this->request->post['twocheckout_api_total'])) {
			$data['twocheckout_api_total'] = $this->request->post['twocheckout_api_total'];
		} else {
			$data['twocheckout_api_total'] = $this->config->get('twocheckout_api_total'); 
		} 
				
		if (isset($this->request->post['twocheckout_api_order_status_id'])) {
			$data['twocheckout_api_order_status_id'] = $this->request->post['twocheckout_api_order_status_id'];
		} else {
			$data['twocheckout_api_order_status_id'] = $this->config->get('twocheckout_api_order_status_id'); 
		}
		
		$this->load->model('localisation/order_status');
		
		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();
		
		if (isset($this->request->post['twocheckout_api_geo_zone_id'])) {
			$data['twocheckout_api_geo_zone_id'] = $this->request->post['twocheckout_api_geo_zone_id'];
		} else {
			$data['twocheckout_api_geo_zone_id'] = $this->config->get('twocheckout_api_geo_zone_id'); 
		}
		
		$this->load->model('localisation/geo_zone');
										
		$data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();
		
		if (isset($this->request->post['twocheckout_api_status'])) {
			$data['twocheckout_api_status'] = $this->request->post['twocheckout_api_status'];
		} else {
			$data['twocheckout_api_status'] = $this->config->get('twocheckout_api_status');
		}
		
		if (isset($this->request->post['twocheckout_api_sort_order'])) {
			$data['twocheckout_api_sort_order'] = $this->request->post['twocheckout_api_sort_order'];
		} else {
			$data['twocheckout_api_sort_order'] = $this->config->get('twocheckout_api_sort_order');
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('payment/twocheckout_api', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'payment/twocheckout_api')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		
		if (!$this->request->post['twocheckout_api_account']) {
			$this->error['account'] = $this->language->get('error_account');
		}
		
		return !$this->error;	
	}
}
?>