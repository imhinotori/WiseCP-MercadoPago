<?php

$config         = $module->config;
$lang           = $module->lang;
$checkout_info  = $module->checkout_info();

$preference = new MercadoPago\Preference();

$items = [];

foreach($checkout['items'] as $item) {
    $item = new MercadoPago\Item();
    $item->title = $item['options']['category']+"-"+$item['name'];
    $item->quantity = 1;
    $item->unit_price = $checkout_info['data']['amount'];
    array_push($items, $item);
}

$preference->items = $items;
$preference->save();

?>

<script src="https://sdk.mercadopago.com/js/v2"></script>

<a href="<?php echo $preference->init_point; ?>">Pagar con Mercado Pago</a>

<script>
    const mp = new MercadoPago(<?php echo($module->publicKey); ?>, {
        locale: "es-CL",
    });

    mp.checkout({
        preference: {
            id: "YOUR_PREFERENCE_ID",
        },
        autoOpen: true,
        render: {
            container: ".cho-container",
            label: "Pagar",
        },
    });
</script>
