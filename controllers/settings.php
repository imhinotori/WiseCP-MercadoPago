<?php
if (!defined("CORE_FOLDER")) die();

$lang = $module->lang;
$config = $module->config;

Helper::Load(["Money"]);

$live_public_key = Filter::init("POST/live_public_key", "hclear");
$live_access_token = Filter::init("POST/live_access_token", "hclear");
$test_public_key = Filter::init("POST/test_public_key", "hclear");
$test_access_token = Filter::init("POST/test_access_token", "hclear");
$commission_rate = Filter::init("POST/commission_rate", "amount");
$commission_rate = str_replace(",", ".", $commission_rate);


$sets = [];

if($live_public_key != $config["settings"]["live_public_key"])
    $sets["settings"]["live_public_key"] = $live_public_key;

if($live_access_token != $config["settings"]["live_access_token"])
    $sets["settings"]["live_access_token"] = $live_access_token;

if($test_public_key != $config["settings"]["test_public_key"])
    $sets["settings"]["test_public_key"] = $test_public_key;

if($test_access_token != $config["settings"]["test_access_token"])
    $sets["settings"]["test_access_token"] = $test_access_token;

if ($commission_rate != $config["settings"]["commission_rate"])
    $sets["settings"]["commission_rate"] = $commission_rate;


if ($sets) {
    $config_result = array_replace_recursive($config, $sets);
    $array_export = Utility::array_export($config_result, ['pwith' => true]);

    $file = dirname(__DIR__) . DS . "config.php";
    $write = FileManager::file_write($file, $array_export);

    $adata = UserManager::LoginData("admin");
    User::addAction($adata["id"], "alteration", "changed-payment-module-settings", [
        'module' => $config["meta"]["name"],
        'name' => $lang["name"],
    ]);
}

echo Utility::jencode([
    'status' => "successful",
    'message' => $lang["success1"],
]);
