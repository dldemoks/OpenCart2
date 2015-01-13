<?php
class ControllerPaymentPayeer extends Controller 
{
	private $error = array(); 

	public function index() 
	{
		$this->load->language('payment/payeer');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('setting/setting');
		
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) 
		{
			$this->model_setting_setting->editSetting('payeer', $this->request->post);
			$this->session->data['success'] = $this->language->get('text_success');
			$this->response->redirect($this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'));
		}
		
		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_edit'] = $this->language->get('text_edit');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['text_all_zones'] = $this->language->get('text_all_zones');
		
		$data['entry_url'] = $this->language->get('entry_url');
		$data['entry_merchant'] = $this->language->get('entry_merchant');
		$data['entry_security'] = $this->language->get('entry_security');
		$data['entry_callback'] = $this->language->get('entry_callback');
		$data['entry_total'] = $this->language->get('entry_total');	
		$data['entry_order_status'] = $this->language->get('entry_order_status');		
		$data['entry_geo_zone'] = $this->language->get('entry_geo_zone');
		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_sort_order'] = $this->language->get('entry_sort_order');
		$data['entry_title_pay_settings'] = $this->language->get('entry_title_pay_settings');
		$data['entry_order_desc'] = $this->language->get('entry_order_desc');
		$data['entry_log'] = $this->language->get('entry_log');
		$data['entry_title_url_settings'] = $this->language->get('entry_title_url_settings');
		$data['entry_list_ip'] = $this->language->get('entry_list_ip');
		$data['entry_admin_email'] = $this->language->get('entry_admin_email');

		$data['entry_url_help'] = $this->language->get('entry_url_help');
		$data['entry_merchant_help'] = $this->language->get('entry_merchant_help');
		$data['entry_security_help'] = $this->language->get('entry_security_help');
		$data['entry_order_status_help'] = $this->language->get('entry_order_status_help');
		$data['entry_status_help'] = $this->language->get('entry_status_help');
		$data['entry_sort_order_help'] = $this->language->get('entry_sort_order_help');
		$data['entry_log_help'] = $this->language->get('entry_log_help');
		$data['entry_order_desc_help'] = $this->language->get('entry_order_desc_help');
		$data['entry_list_ip_help'] = $this->language->get('entry_list_ip_help'); 
		
		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');

  		if (isset($this->error['warning'])) 
		{
			$data['error_warning'] = $this->error['warning'];
		}
		else 
		{
			$data['error_warning'] = '';
		}

 		if (isset($this->error['merchant'])) 
		{
			$data['error_merchant'] = $this->error['merchant'];
		} 
		else 
		{
			$data['error_merchant'] = '';
		}

 		if (isset($this->error['security']))
		{
			$data['error_security'] = $this->error['security'];
		} 
		else 
		{
			$data['error_security'] = '';
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
			'href'      => $this->url->link('payment/payeer', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);
				
		$data['action'] = $this->url->link('payment/payeer', 'token=' . $this->session->data['token'], 'SSL');
		
		$data['cancel'] = $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL');
		
		if (isset($this->request->post['payeer_url']))
		{
			if ($this->request->post['payeer_url'] == '')
			{
				$data['payeer_url'] = '//payeer.com/merchant/';
			}
			else
			{
				$data['payeer_url'] = $this->request->post['payeer_url'];
			}
		} 
		else
		{
			if ($this->config->get('payeer_url') == '')
			{
				$data['payeer_url'] = '//payeer.com/merchant/';
			}
			else
			{
				$data['payeer_url'] = $this->config->get('payeer_url');
			}
		}
		
		if (isset($this->request->post['payeer_order_desc']))
		{
			$data['payeer_order_desc'] = $this->request->post['payeer_order_desc'];
		}
		else 
		{
			$data['payeer_order_desc'] = $this->config->get('payeer_order_desc');
		}

		if (isset($this->request->post['payeer_merchant']))
		{
			$data['payeer_merchant'] = $this->request->post['payeer_merchant'];
		}
		else 
		{
			$data['payeer_merchant'] = $this->config->get('payeer_merchant');
		}

		if (isset($this->request->post['payeer_security'])) 
		{
			$data['payeer_security'] = $this->request->post['payeer_security'];
		}
		else 
		{
			$data['payeer_security'] = $this->config->get('payeer_security');
		}

		if (isset($this->request->post['payeer_order_status_id'])) 
		{
			$data['payeer_order_status_id'] = $this->request->post['payeer_order_status_id'];
		} 
		else 
		{
			$data['payeer_order_status_id'] = $this->config->get('payeer_order_status_id'); 
		} 
		
		$this->load->model('localisation/order_status');
		
		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();
		
		if (isset($this->request->post['payeer_geo_zone_id']))
		{
			$data['payeer_geo_zone_id'] = $this->request->post['payeer_geo_zone_id'];
		}
		else 
		{
			$data['payeer_geo_zone_id'] = $this->config->get('payeer_geo_zone_id'); 
		} 

		$this->load->model('localisation/geo_zone');
										
		$data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();
		
		if (isset($this->request->post['payeer_status'])) 
		{
			$data['payeer_status'] = $this->request->post['payeer_status'];
		}
		else 
		{
			$data['payeer_status'] = $this->config->get('payeer_status');
		}
		
		if (isset($this->request->post['payeer_sort_order'])) 
		{
			$data['payeer_sort_order'] = $this->request->post['payeer_sort_order'];
		} 
		else 
		{
			$data['payeer_sort_order'] = $this->config->get('payeer_sort_order');
		}
		
		if (isset($this->request->post['payeer_log_value'])) 
		{
			$data['payeer_log_value'] = $this->request->post['payeer_log_value'];
		} 
		else 
		{
			$data['payeer_log_value'] = $this->config->get('payeer_log_value'); 
		} 
		
		if (isset($this->request->post['payeer_list_ip'])) 
		{
			$data['payeer_list_ip'] = $this->request->post['payeer_list_ip'];
		} 
		else 
		{
			$data['payeer_list_ip'] = $this->config->get('payeer_list_ip');
		} 
		
		if (isset($this->request->post['payeer_admin_email'])) 
		{
			$data['payeer_admin_email'] = $this->request->post['payeer_admin_email'];
		} 
		else
		{
			$data['payeer_admin_email'] = $this->config->get('payeer_admin_email');
		} 

		$this->load->model('localisation/currency');
		$data['currencies'] = $this->model_localisation_currency->getCurrencies();
		
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
		
		$this->response->setOutput($this->load->view('payment/payeer.tpl', $data));
	}

	protected function validate()
	{
		if (!$this->user->hasPermission('modify', 'payment/payeer')) 
		{
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (!$this->request->post['payeer_merchant']) 
		{
			$this->error['merchant'] = $this->language->get('error_merchant');
		}

		if (!$this->request->post['payeer_security']) 
		{
			$this->error['security'] = $this->language->get('error_security');
		}
		
		return !$this->error;
	}
}
?>