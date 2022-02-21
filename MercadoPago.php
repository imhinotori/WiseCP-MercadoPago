<?php

include __DIR__.DS."vendor/autoload.php";

    class MercadoPago {
        public $checkout_id,$checkout;
        public $name,$commission=true;
        public $config=[],$lang=[],$page_type = "in-page",$callback_type="server-sided";
        public $payform=false;
        private $publicKey, $accessToken;
        /**
         * @var mixed
         */

        function __construct(){
            $this->config     = Modules::Config("Payment",__CLASS__);
            $this->lang       = Modules::Lang("Payment",__CLASS__);
            $this->name       = __CLASS__;
            $this->payform   = __DIR__.DS."pages".DS."payform";
        }

        public function get_auth_token(){
            $syskey = Config::get("crypt/system");
            $token  = md5(Crypt::encode("MercadoPago-Auth-Token=".$syskey,$syskey));
            return $token;
        }

        public function set_checkout($checkout){
            $this->checkout_id = $checkout["id"];
            $this->checkout    = $checkout;
        }

        public function commission_fee_calculator($amount){
            $rate = $this->get_commission_rate();
            if(!$rate) return 0;
            $calculate = Money::get_discount_amount($amount,$rate);
            return $calculate;
        }


        public function get_commission_rate(){
            return $this->config["settings"]["commission_rate"];
        }

        public function cid_convert_code($id=0){
            Helper::Load("Money");
            $currency   = Money::Currency($id);
            if($currency) return $currency["code"];
            return false;
        }

        public function get_ip(){
            return UserManager::GetIP();
        }

        public function checkout_info(){
            $this->SetAccessToken();

            $checkout_items         = $this->checkout["items"];
            $checkout_data          = $this->checkout["data"];
            $user_data              = $checkout_data["user_data"];

            $email                  = $user_data["email"];
            $user_name              = $user_data["full_name"];
            if($user_data["company_name"]) $user_name .= " ".$user_data["company_name"];
            $payable_total          = number_format($checkout_data["total"], 2, '.', '');
            $payable_total          = $payable_total * 100;
            $currency               = $this->cid_convert_code($checkout_data["currency"]);
            $phone                  = NULL;
            $address_line           = NULL;
            $address_city           = NULL;
            $address_state          = NULL;
            $address_country        = NULL;
            $address_p_code         = NULL;
            $description            = "Basket Payment";


            if($this->checkout["type"] == "bill" || $this->checkout["type"] == "invoice-bulk-payment")
                $description = "Invoice Payment";

            if(isset($user_data["address"]["address"])) $address_line = $user_data["address"]["address"];
            if(isset($user_data["phone"]) && $user_data["phone"]) $phone = "+".$user_data["phone"];
            if(isset($user_data["address"]["country_code"])) $address_country = $user_data["address"]["country_code"];
            if(isset($user_data["address"]["city"])) $address_city = $user_data["address"]["city"];
            if(isset($user_data["address"]["counti"])) $address_state = $user_data["address"]["counti"];
            if(isset($user_data["address"]["zipcode"])) $address_p_code = $user_data["address"]["zipcode"];


            $data = [
                "description"          => $description,
                "amount"               => $payable_total,
                "currency"             => $currency,
                "payment_method_types" => ["card"],
                "receipt_email"        => $email,
                "metadata"             => [
                    "order_id"         => $this->checkout["id"],
                ]
            ];

            return [
                'key'           => $publishable_key,
                'payable_total' => $payable_total,
                'currency'      => $currency,
            ];
        }

        public function payment_result(){

            $checkout_id    = 12345678;
            $error          = false;
            $txn_id         = 0;
            

            if($error)
                return [
                    'status' => "ERROR",
                    'status_msg' => "",
                ];


            $checkout           = Basket::get_checkout($checkout_id);

            if(!$checkout)
                return [
                    'status' => "ERROR",
                    'status_msg' => Bootstrap::$lang->get("errors/error6",Config::get("general/local")),
                ];


            $this->set_checkout($checkout);

            Basket::set_checkout($this->checkout_id,['status' => "paid"]);

            return [
                'status' => "SUCCESS",
                'checkout'    => $checkout,
                'status_msg' => '',
            ];

        }

        private function SetAccessToken(){
            if($this->config['settings']["test_mode"]){
                $this->publicKey    = $this->config['settings']["test_public_key"];
                $this->accessToken  = $this->config['settings']["test_access_token"];
            } else {
                $this->publicKey    = $this->config['settings']["live_public_key"];
                $this->accessToken  = $this->config['settings']["live_access_token"];
            }
            MercadoPago\SDK::setAccessToken($this->accessToken);
        }

    }
