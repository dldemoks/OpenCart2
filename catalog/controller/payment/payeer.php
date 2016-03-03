<?php
class ControllerPaymentPayeer extends Controller 
{	
	public function index() 
	{
		$data['button_confirm'] = $this->language->get('button_confirm');
		$this->load->model('checkout/order');
		$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);

		$m_key = $this->config->get('payeer_security');
		$data['lang'] = $this->session->data['language'];
		$data['action'] = $this->config->get('payeer_url');
		$data['m_shop'] = $this->config->get('payeer_merchant');
		$data['m_orderid'] = $this->session->data['order_id'];
		$data['m_amount'] = $this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], false);
		$data['m_curr'] = $order_info['currency_code'];
		$data['m_desc'] = base64_encode($order_info['comment']);
		$arHash = array(
			$data['m_shop'],
			$data['m_orderid'],
			$data['m_amount'],
			$data['m_curr'],
			$data['m_desc'],
			$m_key
		);
		
		$data['sign'] = strtoupper(hash('sha256', implode(':', $arHash)));
		$this->model_checkout_order->addOrderHistory($data['m_orderid'], $this->config->get('payeer_order_wait_id'));
		
		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/payeer.tpl'))
		{
			return $this->load->view($this->config->get('config_template') . '/template/payment/payeer.tpl', $data);
		}
		else 
		{
			return $this->load->view('default/template/payment/payeer.tpl', $data);
		}
	}

	public function status()
	{
		$request = $this->request->post;
		$this->load->language('payment/payeer');
		
		if (isset($request["m_operation_id"]) && isset($request["m_sign"]))
		{
			// запись логов
			
			$log_text = 
				"--------------------------------------------------------\n" .
				"operation id		" . $request['m_operation_id'] . "\n" .
				"operation ps		" . $request['m_operation_ps'] . "\n" .
				"operation date		" . $request['m_operation_date'] . "\n" .
				"operation pay date	" . $request['m_operation_pay_date'] . "\n" .
				"shop				" . $request['m_shop'] . "\n" .
				"order id			" . $request['m_orderid'] . "\n" .
				"amount				" . $request['m_amount'] . "\n" .
				"currency			" . $request['m_curr'] . "\n" .
				"description		" . base64_decode($request['m_desc']) . "\n" .
				"status				" . $request['m_status'] . "\n" .
				"sign				" . $request['m_sign'] . "\n\n";
			
			if ($this->config->get('payeer_log_value') !== '')
			{
				file_put_contents($_SERVER['DOCUMENT_ROOT'] . $this->config->get('payeer_log_value'), $log_text, FILE_APPEND);
			}
			
			
			// вычисление цифровой подписи
			
			$sign_hash = strtoupper(hash('sha256', implode(":", array(
				$request['m_operation_id'],
				$request['m_operation_ps'],
				$request['m_operation_date'],
				$request['m_operation_pay_date'],
				$request['m_shop'],
				$request['m_orderid'],
				$request['m_amount'],
				$request['m_curr'],
				$request['m_desc'],
				$request['m_status'],
				$this->config->get('payeer_security')
			))));
			
			
			// подлинность ip адреса
			
			$valid_ip = true;
			$list_ip_str = str_replace(' ', '', $this->config->get('payeer_list_ip'));
			
			if (!empty($list_ip_str)) 
			{
				$i = 0;
				$list_ip = explode(',', $list_ip_str);
				$this_ip_field = explode('.', $_SERVER['REMOTE_ADDR']);
				$list_ip_field = array();
				$valid_ip = false;
				foreach ($list_ip as $ip)
				{
					$ip_field[$i] = explode('.', $ip);
					if ((($this_ip_field[0] ==  $ip_field[$i][0]) || ($ip_field[$i][0] == '*')) &&
						(($this_ip_field[1] ==  $ip_field[$i][1]) || ($ip_field[$i][1] == '*')) &&
						(($this_ip_field[2] ==  $ip_field[$i][2]) || ($ip_field[$i][2] == '*')) &&
						(($this_ip_field[3] ==  $ip_field[$i][3]) || ($ip_field[$i][3] == '*')))
					{
						$valid_ip = true;
						break;
					}
					$i++;
				}
			}
			
			
			// проверка цифровой подписи и ip
		
			if (!($request['m_sign'] == $sign_hash && $valid_ip))
			{
				if ($this->config->get('payeer_admin_email') !== '')
				{
					$message = $this->language->get('text_email_message1') . "\n\n";
					
					if (!$valid_ip)
					{
						$message .= $this->language->get('text_email_message4') . "\n" . 
									$this->language->get('text_email_message5') . $this->config->get('payeer_list_ip') . "\n" . 
									$this->language->get('text_email_message6') . $_SERVER['REMOTE_ADDR'] . "\n";
					}
					
					if ($request["m_sign"] != $sign_hash)
					{
						$message .= $this->language->get('text_email_message2') . "\n";
					}
					
					$message .= "\n" . $log_text;
					$headers = "From: no-reply@" . $_SERVER['HTTP_HOST'] . "\r\n" . 
								"Content-type: text/plain; charset=utf-8 \r\n";
						
					mail($this->config->get('payeer_admin_email'), $this->language->get('text_email_subject'), $message, $headers);
				}

				echo $request['m_orderid'] . '|error';
				return true;
			}
			
			
			// загрузка заказа
			
			$this->load->model('checkout/order');
			$order = $this->model_checkout_order->getOrder($request['m_orderid']);
			
			if (!$order)
			{
				echo $request['m_orderid'] . '|error';
				return true;
			}
			
			$order_curr = ($order['currency_code'] == 'RUR') ? 'RUB' : $order['currency_code'];
			$order_amount = number_format($order['total'], 2, '.', '');
			
			
			// проверка суммы и валюты
			
			if (!($request['m_amount'] == $order_amount && $request['m_curr'] == $order_curr))
			{
				if ($this->config->get('payeer_admin_email') !== '')
				{
					$message = $this->language->get('text_email_message1') . "\n\n";
					
					if ($request['m_amount'] != $order_amount)
					{
						$message .= $this->language->get('text_email_message7') . "\n";
					}
					
					if ($request['m_curr'] != $order_curr)
					{
						$message .= $this->language->get('text_email_message8') . "\n";
					}
					
					$message .= "\n" . $log_text;
					$headers = "From: no-reply@" . $_SERVER['HTTP_HOST'] . "\r\n" . 
								"Content-type: text/plain; charset=utf-8 \r\n";
						
					mail($this->config->get('payeer_admin_email'), $this->language->get('text_email_subject'), $message, $headers);
				}

				echo $request['m_orderid'] . '|error';
				return true;
			}
			
			
			// проверка статуса
			
			switch ($request['m_status'])
			{
				case 'success':
					if ($order['order_status_id'] != $this->config->get('payeer_order_success_id'))
					{
						$this->model_checkout_order->addOrderHistory($request['m_orderid'], $this->config->get('payeer_order_success_id'));
						echo $request['m_orderid'] . '|success';
					}
					return true;
					break;
					
				case 'fail':
					if ($order['order_status_id'] != $this->config->get('payeer_order_fail_id'))
					{
						if ($this->config->get('payeer_admin_email') !== '')
						{
							$message = $this->language->get('text_email_message1') . "\n\n" . 
										$this->language->get('text_email_message3') . "\n\n" . 
										$log_text;

							$headers = "From: no-reply@" . $_SERVER['HTTP_HOST'] . "\r\n" . 
										"Content-type: text/plain; charset=utf-8 \r\n";

							mail($this->config->get('payeer_admin_email'), $this->language->get('text_email_subject'), $message, $headers);
						}
						
						$this->model_checkout_order->addOrderHistory($request['m_orderid'], $this->config->get('payeer_order_fail_id'));
					
						echo $request['m_orderid'] . '|error';
					}
					return true;
					break;
					
				default: 
					echo $request['m_orderid'] . '|error';
					return true;
					break;
			}
		}
   	}

	public function fail() 
	{
		$this->response->redirect($this->url->link('checkout/checkout'));	
		return true;
	}

	public function success() 
	{
		$this->response->redirect($this->url->link('checkout/success'));
		return true;
	}
}