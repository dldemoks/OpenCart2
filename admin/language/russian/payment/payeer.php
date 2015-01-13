<?php
// Heading
$_['heading_title']      	= 'Платежная система Payeer';

// Text 
$_['text_payment']       	= 'Оплата';
$_['text_success']       	= 'Настройки модуля обновлены!';
$_['text_edit']          	= 'Редактирование';
$_['text_payeer']		 	= '<a onclick="window.open(\'https://www.payeer.com\');"><img src="view/image/payment/payeer.png" alt="Payeer" title="Payeer" /></a>';
  
// Entry
$_['entry_url']     		= 'URL мерчанта';
$_['entry_merchant']     	= 'Идентификатор магазина';
$_['entry_security']     	= 'Секретный ключ';
$_['entry_order_status'] 	= 'Статус заказа после оплаты';
$_['entry_geo_zone']     	= 'Географическая зона';
$_['entry_status']      	= 'Статус';
$_['entry_sort_order']   	= 'Порядок сортировки';
$_['entry_log'] 			= 'Путь до файла-журнала';
$_['entry_order_desc'] 		= 'Комментарий к оплате';
$_['entry_list_ip'] 		= 'Список IP';
$_['entry_admin_email'] 	= 'E-mail для оповещения об ошибках';

//Help
$_['entry_url_help']     		= 'URL для оплаты в системе Payeer (по умолчанию //payeer.com/merchant/)';
$_['entry_merchant_help']     	= 'Идентификатор магазина, зарегистрированного в системе PAYEER. Узнать его можно в аккаунте Payeer: Аккаунт -> Мой магазин -> Изменить. Пример: 01234567';
$_['entry_security_help']     	= 'Секретный ключ оповещения о выполнении платежа, который используется для проверки целостности полученной информации и однозначной идентификации отправителя. Должен совпадать с секретным ключем, указанным в аккаунте Payeer: Аккаунт -> Мой магазин -> Изменить. Пример: 1234567';
$_['entry_order_status_help'] 	= 'Статус заказа, после успешной оплаты';
$_['entry_status_help']      	= 'Активировать / Деактивировать модуль Payeer';
$_['entry_sort_order_help']   	= 'Чем меньше цифра, тем выше приоритет среди других способов оплаты';
$_['entry_log_help'] 			= 'Путь до файла для журнала оплат через Payeer (например, /payeer_orders.log). Если путь не указан, то журнал не записывается';
$_['entry_order_desc_help'] 	= 'Пояснение оплаты заказа';
$_['entry_list_ip_help'] 		= 'Перечислить через запятую доверенные IP-адреса обработчика платежей. Можно указать маски.';

// Error
$_['error_permission']   	= 'У Вас нет прав для управления этим модулем!';
$_['error_merchant']     	= 'Необходимо указать идентификатор магазина!';
$_['error_security']     	= 'Необходимо указать секретный код!';
?>