<?php

$config         = $module->config;
$lang           = $module->lang;
$checkout_info  = $module->checkout_info();

$preference = new MercadoPago\Preference();
$payer = new MercadoPago\Payer();

/* Payer Data */
$payer->email = $checkout_info['data']['receipt_email'];

/* Items */
$items = array();
foreach($module->checkout['items'] as $item)
{
    $mercadoPagoItem = new MercadoPago\Item();
    $mercadoPagoItem->title = $item['options']['category']." - ".$item['name'];
    $mercadoPagoItem->quantity = $item['quantity'];
    $mercadoPagoItem->unit_price = $item['amount'];
    array_push($items, $mercadoPagoItem);
}

$preference->items = $items;
$preference->payer = $payer;
$preference->back_urls = array(
    "success" => $links['callback'],
    "pending" => $links['callback'],
    "failure" => $links['failed-page']
);
$preference->auto_return = "approved";
$preference->external_reference = $checkout_info['data']['metadata']['order_id'];
$preference->save();

?>


<script>
    window.location.href = "<?php echo($preference->init_point); ?>";
</script>
