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
		if (isset($_POST["m_operation_id"]) && isset($_POST["m_sign"]))
		{
			$m_key = $this->config->get('payeer_security');
			$arHash = array(
				$_POST['m_operation_id'],
				$_POST['m_operation_ps'],
				$_POST['m_operation_date'],
				$_POST['m_operation_pay_date'],
				$_POST['m_shop'],
				$_POST['m_orderid'],
				$_POST['m_amount'],
				$_POST['m_curr'],
				$_POST['m_desc'],
				$_POST['m_status'],
				$m_key);
			$sign_hash = strtoupper(hash('sha256', implode(":", $arHash)));
			
			// проверка принадлежности ip списку доверенных ip
			
			$list_ip_str = str_replace(' ', '', $this->config->get('payeer_list_ip'));
			
			if ($list_ip_str != '') 
			{
				$list_ip = explode(',', $list_ip_str);
				$this_ip = $_SERVER['REMOTE_ADDR'];
				$this_ip_field = explode('.', $this_ip);
				$list_ip_field = array();
				$i = 0;
				$valid_ip = FALSE;
				foreach ($list_ip as $ip)
				{
					$ip_field[$i] = explode('.', $ip);
					if ((($this_ip_field[0] ==  $ip_field[$i][0]) or ($ip_field[$i][0] == '*')) and
						(($this_ip_field[1] ==  $ip_field[$i][1]) or ($ip_field[$i][1] == '*')) and
						(($this_ip_field[2] ==  $ip_field[$i][2]) or ($ip_field[$i][2] == '*')) and
						(($this_ip_field[3] ==  $ip_field[$i][3]) or ($ip_field[$i][3] == '*')))
						{
							$valid_ip = TRUE;
							break;
						}
					$i++;
				}
			}
			else
			{
				$valid_ip = TRUE;
			}
			
			// запись в логи если требуется
			
			$log_text = 
			"--------------------------------------------------------\n" .
			"operation id		" . $_POST['m_operation_id'] . "\n" .
			"operation ps		" . $_POST['m_operation_ps'] . "\n" .
			"operation date		" . $_POST['m_operation_date'] . "\n" .
			"operation pay date	" . $_POST['m_operation_pay_date'] . "\n" .
			"shop				" . $_POST['m_shop'] . "\n" .
			"order id			" . $_POST['m_orderid'] . "\n" .
			"amount				" . $_POST['m_amount'] . "\n" .
			"currency			" . $_POST['m_curr'] . "\n" .
			"description		" . base64_decode($_POST['m_desc']) . "\n" .
			"status				" . $_POST['m_status'] . "\n" .
			"sign				" . $_POST['m_sign'] . "\n\n";
			
			if ($this->config->get('payeer_log_value') !== '')
			{
				file_put_contents($_SERVER['DOCUMENT_ROOT'] . $this->config->get('payeer_log_value'), $log_text, FILE_APPEND);
			}
			
			$this->load->model('checkout/order');
			$order_info = $this->model_checkout_order->getOrder($_POST['m_orderid']);
			if (!$order_info) return;
				
			// проверка цифровой подписи и ip сервера
			
			if ($order_info['order_status_id'] != $this->config->get('payeer_order_success_id')
				&& $_POST['m_status'] == 'success'
				&& $_POST['m_sign'] == $sign_hash  
				&& $valid_ip)
			{
				$this->model_checkout_order->addOrderHistory($_POST['m_orderid'], $this->config->get('payeer_order_success_id'));
				echo $_POST['m_orderid'] . '|success';
			}
			elseif ($order_info['order_status_id'] != $this->config->get('payeer_order_fail_id'))
			{
				$this->load->language('payment/payeer');
				
				$subject = $this->language->get('text_email_subject');
				$message = $this->language->get('text_email_message1') . "\n\n";
				
				if ($_POST["m_sign"] != $sign_hash)
				{
					$message.= $this->language->get('text_email_message2') . "\n";
				}
				
				if ($_POST['m_status'] != "success")
				{
					$message .= $this->language->get('text_email_message3') . "\n";
				}
				
				if (!$valid_ip)
				{
					$message .= $this->language->get('text_email_message4') . "\n";
					$message .= $this->language->get('text_email_message5') . $this->config->get('payeer_list_ip') . "\n";
					$message .= $this->language->get('text_email_message6') . $_SERVER['REMOTE_ADDR'] . "\n";
				}
				
				$message .= "\n" . $log_text;
				$this->model_checkout_order->addOrderHistory($_POST['m_orderid'], $this->config->get('payeer_order_fail_id'));
				
				$mail = new Mail();
				$mail->protocol = $this->config->get('config_mail_protocol');
				$mail->parameter = $this->config->get('config_mail_parameter');
				$mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
				$mail->smtp_username = $this->config->get('config_mail_smtp_username');
				$mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
				$mail->smtp_port = $this->config->get('config_mail_smtp_port');
				$mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');
				$mail->setTo($this->config->get('payeer_admin_email'));
				$mail->setFrom($this->config->get('config_email'));
				$mail->setSender(html_entity_decode($order_info['store_name'], ENT_QUOTES, 'UTF-8'));
				$mail->setSubject(html_entity_decode($subject, ENT_QUOTES, 'UTF-8'));
				$mail->setText($message);
				$mail->send();
				
				echo $_POST['m_orderid'] . '|error';
			}
			exit;
		}
   	}

	public function fail() 
	{
		$this->response->redirect($this->url->link('checkout/checkout'));	
		return TRUE;
	}

	public function success() 
	{
		$this->response->redirect($this->url->link('checkout/success'));
		return TRUE;
	}
}