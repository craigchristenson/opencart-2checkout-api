<?php 
class ControllerPaymentTwoCheckoutPP extends Controller {
    private $error = array(); 

    public function index() {
        $this->load->language('payment/twocheckout_pp');

        $this->document->setTitle($this->language->get('heading_title'));
        
        $this->load->model('setting/setting');
            
        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->model_setting_setting->editSetting('twocheckout_pp', $this->request->post);				
            
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
        $data['entry_secret'] = $this->language->get('entry_secret');
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
        
        if (isset($this->error['secret'])) {
            $data['error_secret'] = $this->error['secret'];
        } else {
            $data['error_secret'] = '';
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
            'href'      => $this->url->link('payment/twocheckout_pp', 'token=' . $this->session->data['token'], 'SSL'),
              'separator' => ' :: '
           );
                
        $data['action'] = $this->url->link('payment/twocheckout_pp', 'token=' . $this->session->data['token'], 'SSL');
        
        $data['cancel'] = $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL');
        
        if (isset($this->request->post['twocheckout_pp_account'])) {
            $data['twocheckout_pp_account'] = $this->request->post['twocheckout_pp_account'];
        } else {
            $data['twocheckout_pp_account'] = $this->config->get('twocheckout_pp_account');
        }

        if (isset($this->request->post['twocheckout_pp_secret'])) {
            $data['twocheckout_pp_secret'] = $this->request->post['twocheckout_pp_secret'];
        } else {
            $data['twocheckout_pp_secret'] = $this->config->get('twocheckout_pp_secret');
        }
        
        if (isset($this->request->post['twocheckout_pp_total'])) {
            $data['twocheckout_pp_total'] = $this->request->post['twocheckout_pp_total'];
        } else {
            $data['twocheckout_pp_total'] = $this->config->get('twocheckout_pp_total'); 
        } 
                
        if (isset($this->request->post['twocheckout_pp_order_status_id'])) {
            $data['twocheckout_pp_order_status_id'] = $this->request->post['twocheckout_pp_order_status_id'];
        } else {
            $data['twocheckout_pp_order_status_id'] = $this->config->get('twocheckout_pp_order_status_id'); 
        }
        
        $this->load->model('localisation/order_status');
        
        $data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();
        
        if (isset($this->request->post['twocheckout_pp_geo_zone_id'])) {
            $data['twocheckout_pp_geo_zone_id'] = $this->request->post['twocheckout_pp_geo_zone_id'];
        } else {
            $data['twocheckout_pp_geo_zone_id'] = $this->config->get('twocheckout_pp_geo_zone_id'); 
        }
        
        $this->load->model('localisation/geo_zone');
                                        
        $data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();
        
        if (isset($this->request->post['twocheckout_pp_status'])) {
            $data['twocheckout_pp_status'] = $this->request->post['twocheckout_pp_status'];
        } else {
            $data['twocheckout_pp_status'] = $this->config->get('twocheckout_pp_status');
        }
        
        if (isset($this->request->post['twocheckout_pp_sort_order'])) {
            $data['twocheckout_pp_sort_order'] = $this->request->post['twocheckout_pp_sort_order'];
        } else {
            $data['twocheckout_pp_sort_order'] = $this->config->get('twocheckout_pp_sort_order');
        }

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('payment/twocheckout_pp', $data));
    }

    protected function validate() {
        if (!$this->user->hasPermission('modify', 'payment/twocheckout_pp')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }
        
        if (!$this->request->post['twocheckout_pp_account']) {
            $this->error['account'] = $this->language->get('error_account');
        }

        if (!$this->request->post['twocheckout_pp_secret']) {
            $this->error['secret'] = $this->language->get('error_secret');
        }
        
        if (!$this->error) {
            return true;
        } else {
            return false;
        }	
    }
}
?>