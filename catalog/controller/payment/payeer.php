<?php
class ControllerPaymentPayeer extends Controller 
{
	public function index() 
	{
		$data['button_confirm'] = $this->language->get('button_confirm');

		$this->load->model('checkout/order');
		
		$order_id = $this->session->data['order_id'];
		
		$this->model_checkout_order->addOrderHistory($order_id, 1);
		
		$order_info = $this->model_checkout_order->getOrder($order_id);
		
		$data['action'] = $this->config->get('payeer_url');

		$data['m_shop'] = $this->config->get('payeer_merchant');

		$m_key = $this->config->get('payeer_security');
		$data['m_orderid'] = $order_id;
		$data['m_amount'] = $this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], false);
		$data['m_amount'] = number_format($data['m_amount'], 2, '.', '');
		
		$data['m_curr'] = strtoupper($order_info['currency_code']);
		
		if ($data['m_curr'] == 'RUR')
		{
			$data['m_curr'] = 'RUB';
		}
		
		$data['m_desc'] = base64_encode($this->config->get('payeer_order_desc'));
		$arHash = array(
			$data['m_shop'],
			$data['m_orderid'],
			$data['m_amount'],
			$data['m_curr'],
			$data['m_desc'],
			$m_key
		);
		
		$sign = strtoupper(hash('sha256', implode(":", $arHash)));
		$data['sign'] = $sign;

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
				$m_key
			);
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
				"--------------------------------------------------------\n".
				"operation id		" . $_POST["m_operation_id"] . "\n".
				"operation ps		" . $_POST["m_operation_ps"] . "\n".
				"operation date		" . $_POST["m_operation_date"] . "\n".
				"operation pay date	" . $_POST["m_operation_pay_date"] . "\n".
				"shop				" . $_POST["m_shop"] . "\n".
				"order id			" . $_POST["m_orderid"] . "\n".
				"amount				" . $_POST["m_amount"] . "\n".
				"currency			" . $_POST["m_curr"] . "\n".
				"description		" . base64_decode($_POST["m_desc"]) . "\n".
				"status				" . $_POST["m_status"] . "\n".
				"sign				" . $_POST["m_sign"] . "\n\n";
			
			$log_file = $this->config->get('payeer_log_value');
			
			if (!empty($log_file))
			{
				file_put_contents($_SERVER['DOCUMENT_ROOT'] . $log_file, $log_text, FILE_APPEND);
			}
			
			$order_id = $_POST['m_orderid'];
			$this->load->model('checkout/order');
			$order_info = $this->model_checkout_order->getOrder($order_id);
			
			// проверка цифровой подписи и ip сервера
			if ($_POST["m_sign"] == $sign_hash && $_POST['m_status'] == "success" && $valid_ip)
			{
				if( $order_info['order_status_id'] != $this->config->get('payeer_order_status_id')) 
				{
					$this->model_checkout_order->addOrderHistory($order_id, $this->config->get('payeer_order_status_id'));
				}
				
				exit ($order_id . '|success');
			}
			else
			{	
				$this->model_checkout_order->addOrderHistory($order_id, 7);
				$to = $this->config->get('payeer_admin_email');
				$subject = "Payment error";
				$message = "Failed to make the payment through the system Payeer for the following reasons:\n\n";
				
				if ($_POST["m_sign"] != $sign_hash)
				{
					$message .= " - Do not match the digital signature\n";
				}
				
				if ($_POST['m_status'] != "success")
				{
					$message .= " - The payment status is not success\n";
				}
				
				if (!$valid_ip)
				{
					$message .= " - the ip address of the server is not trusted\n";
					$message .= "   trusted ip: " . $this->config->get('list_ip') . "\n";
					$message .= "   ip of the current server: " . $_SERVER['REMOTE_ADDR'] . "\n";
				}
				
				$message .= "\n" . $log_text;
				$headers = "From: no-reply@" . $_SERVER['HTTP_SERVER']."\r\nContent-type: text/plain; charset=utf-8 \r\n";
				mail($to, $subject, $message, $headers);
				
				exit ($order_id . '|error');
			}
		}
	}
	
	public function fail()
	{
		$this->response->redirect($this->url->link('checkout/checkout', '', 'SSL'));
		
		return TRUE;
	}
	
	public function success()
	{
		$this->response->redirect($this->url->link('checkout/success', '', 'SSL'));
		
		return TRUE;
	}
}
?>