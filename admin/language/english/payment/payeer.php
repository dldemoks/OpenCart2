<?php
// Heading
$_['heading_title'] 		= 'Payment system Payeer';

// Text 
$_['text_payment'] 			= 'Payment';
$_['text_success'] 			= 'Settings module updated!';
$_['text_edit'] 			= 'Edit';
$_['text_payeer'] 			= '<a onclick="window.open(\'https://www.payeer.com\');"><img src="view/image/payment/payeer.png" alt="Payeer" title="Payeer" /></a>';

// Entry
$_['entry_url'] 			= 'URL of the merchant';
$_['entry_merchant'] 		= 'store ID';
$_['entry_security'] 		= 'Secret key';
$_['entry_order_status']	= 'the status of the order after payment';
$_['entry_geo_zone'] 		= 'Geographical area';
$_['entry_status']	 		= 'Status';
$_['entry_sort_order'] 		= 'sort Order';
$_['entry_log'] 			= 'Path to file-log';
$_['entry_order_desc'] 		= 'Comment for payment';
$_['entry_list_ip'] 		= 'IP';
$_['entry_admin_email'] 	= 'E-mail to alert error';

//Help
$_['entry_url_help'] 		= 'the URL for payment in the system Payeer (the default //payeer.com/merchant/)';
$_['entry_merchant_help'] 	= 'store ID registered in the system PAYEER. It can be found in Payeer account: Account -> My store -> Edit. Example: 01234567';
$_['entry_security_help'] 	= 'Secret key notification of payment that is used to verify the integrity of the received information and the unique identification of the sender. Must be the same secret key specified in the Payeer account: Account -> My store -> Edit. Example: 1234567';
$_['entry_order_status_help'] = 'order Status after successful payment';
$_['entry_status_help'] 	= 'Activate / Deactivate the module Payeer';
$_['entry_sort_order_help'] = 'the smaller the number the higher the priority among other payment methods';
$_['entry_log_help'] 		= 'Path to file to log payments through Payeer (for example, /payeer_orders.log). If a path is not specified, the log is not written';
$_['entry_order_desc_help'] = 'an explanation of the payment order';
$_['entry_list_ip_help'] 	= 'a comma-separated List of trusted IP addresses of the payment processor. You can specify the mask.';

// Error
$_['error_permission'] 		= 'You Have no rights to control this module!';
$_['error_merchant'] 		= 'you Must specify the ID of the store!';
$_['error_security'] 		= 'you Must specify the secret code!';
?>