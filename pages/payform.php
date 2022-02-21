<?php

$config         = $module->config;
$lang           = $module->lang;
$checkout_info  = $module->checkout_info();


/*$item = new MercadoPago\Item();
$item->title = 'Mi producto';
$item->quantity = 1;
$item->unit_price = 75;
$preference->items = array($item);
$preference->save();*/

print_r($config);
print_r($lang);
print_r($checkout_info);
print_r($checkout);
?>

Here you will include the payment form.