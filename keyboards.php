<?php	

class keyboard {
		
  		static function start(){
			$keyboard = [
            "keyboard" => [[["text" => "🇷🇺 Ru"],["text" => "🇺🇿 Uz"]]],       
        	"resize_keyboard" => true];
         	return $keyboard;  
        }
  
    	static function register($lan){
    		if($lan == 'ru'){     
          $keyboard = [
    		"keyboard" => [[["text" => "Регистрация","request_contact" => true]]],
    		"resize_keyboard" => true];
          }
        if($lan == 'uz'){
          $keyboard = [
    		"keyboard" => [[["text" => "Ro'yxatdan o'tish","request_contact" => true]]],
    		"resize_keyboard" => true];
        }
              return $keyboard;
    
    } 
  
  		static function main($lan){
        	if($lan == 'ru'){
          $keyboard = [
    			"keyboard" => [
            [["text" => "🍴 Меню"]],
            [["text" => "🛍 Мои заказы"]],
            [["text" => "✍️ Оставить отзыв"],
            ["text" => "⚙️ Настройки"]]],
    			"resize_keyboard" => true];
    	
            }
          if($lan == 'uz'){
          $keyboard = [
            "keyboard" => [
              [["text" => "🍴 Menyu"]],
              [["text" => "🛍 Mening buyurtmalarim"]],
              [["text" => "✍️ Fikr bildirish"],
              ["text" => "⚙️ Sozlamalar"]]],
            "resize_keyboard" => true];
            }
    		return $keyboard;
        }

        static function ask_loc($lan){
        	if($lan == 'ru'){
          $keyboard = [
    			"keyboard" => [
            [["text" => "🗺 Мои адреса"]],
            [["text" => "📍 Отправить геолокацию", "request_location" => true],
            ["text" => "⬅️ Назад"]]],
    			"resize_keyboard" => true];
    	
            }
          if($lan == 'uz'){
          $keyboard = [
            "keyboard" => [
              [["text" => "🗺 Mening manzillarim"]],
              [["text" => "📍 Geolokatsiyani yuboring", "request_location" => true],
              ["text" => "⬅️ Ortga"]]],
            "resize_keyboard" => true];
            }
    		return $keyboard;
        }

        static function payment($lan){
          if($lan == 'ru'){
            $keyboard = [
            "keyboard" => [
              [["text" => "Оплата наличными"]],
              [["text" => "Пластик"]],
              [["text" => "⬅️ Назад"]]
              ],
              "resize_keyboard" => true];
        
              }
            if($lan == 'uz'){
            $keyboard = [
              "keyboard" => [
                [["text" => "🍴 Menyu"]],
                [["text" => "🛍 Mening buyurtmalarim"]],
                [["text" => "✍️ Fikr bildirish"],
                ["text" => "⚙️ Sozlamalar"]]],
              "resize_keyboard" => true];
              }
              return $keyboard;
        }

        static function food_menu($lan, $categories){
                  
                    $lan = strtoupper($lan); 
                    foreach ($categories as $c) {
                      $cat1[] = ["text" => $c["NAME_".$lan]];
                    }  
                    if($lan == 'RU'){
                      $cat1[] = ["text" => "📥 Корзина"];
                      $cat1[] = ["text" => "⬅️ Назад"];                       
                    }
                    if($lan == 'UZ'){
                      $cat1[] = ["text" => "📥 Savat"];
                      $cat1[] = ["text" => "⬅️ Ortga"]; 
                    }
                $f_array = array_chunk($cat1, 2);
            
                $keyboard = [
                "keyboard" => $f_array,
                "resize_keyboard" => true
                ];         
             return $keyboard;
        }
        static function cat_menu($lan, $product){
          $lan = strtoupper($lan); 
          foreach ($product as $pd) {
            $pro[] = ["text" => $pd["NAME_".$lan]];
          }
            if($lan == "RU"){
            $pro[] = ["text" => "⬅️ Назад"];
            }
            if($lan == "UZ"){
            $pro[] = ["text" => "⬅️ Ortga"];
            }
            $f_array = array_chunk($pro, 2);
       
            $keyboard = [
            "keyboard" => $f_array,
            "resize_keyboard" => true
            ];
        
       
          return $keyboard;
        }

        static function cart($lan, $chat_id){
          $order_select = database::array_single("SELECT * FROM `evos_orders` WHERE `CHAT_ID` = '$chat_id' AND `STATUS` = 'draft'");
			    $cart_data = database::array_all("SELECT * FROM `evos_cart` WHERE `ORDER_ID` = '$order_select[ID]' AND `STATUS` = 'open'");
			    

          if($lan == 'ru'){
            if(empty($cart_data)){
              $keyboard = [
                "inline_keyboard" => 
                [[]]
                ];

              } else {
              $products = database::array_by_id("SELECT * FROM `evos_products`");
              //$lan_upper = strtoupper($lan);
              foreach($cart_data as $v){
              $key[] = ["text" => "❌".$products[$v['PRODUCT_ID']]['NAME_RU'], "callback_data" => "delete_".$v['PRODUCT_ID']];
              }
              $keyboard = [
                "inline_keyboard" => [
                [["text" => "⬅️ Назад", "callback_data" => "back_to_menu"],["text"=>"🚖 Оформить заказ", "callback_data" => "make_order"]],
                $key
                ]
                ];
            
              }
          }
          return $keyboard;
        }
        
        static function product_info($lan, $amount){
          if($lan == 'ru'){
            $keyboard = [
              "inline_keyboard" => [
                [["text" => "➖", "callback_data" => "decrease"],["text" => $amount, "callback_data" => "none"],["text" => "➕", "callback_data" => "increase"]],
                [["text" => "📥 Добавить в корзину", "callback_data" => "add_cart"]]
              ]
              ];
            }
            if($lan == 'uz'){
              $keyboard = [
                "inline_keyboard" => [
                  [["text" => "➖", "callback_data" => "decrease"],["text" => $amount, "callback_data" => "none"],["text" => "➕", "callback_data" => "increase"]],
                  [["text" => "📥 Savatga qo'shish", "callback_data" => "add_cart"]]
                ]
                ];
              }
       
          return $keyboard;
        }

        static function cart_back($lan){
        	if($lan == 'ru'){
          $keyboard = [
    			"keyboard" => [
                        [["text" => "📥 Корзина"]],
                        [["text" => "⬅️ Назад"]]
                      ],
    			"resize_keyboard" => true];
    	
            }
          if($lan == 'uz'){
          $keyboard = [
            "keyboard" => [
                        [["text" => "📥 Savat"]],
                        [["text" => "⬅️ Ortga"]]
                          ],
            "resize_keyboard" => true];
            }
    		return $keyboard;
        }
       

  		static function option($lan){
        	if($lan == 'ru'){
          	$keyboard = [
    			"keyboard" => [[["text" => "✏️ Сменить имя"],["text" => "📱 Сменить номер"]],
                              [["text" => "🇷🇺 Изменить язык"]],
                              [["text" => "⬅️ Назад"]]],
    			 "resize_keyboard" => true];
            }
          if($lan == 'uz'){
          $keyboard = [
    			"keyboard" => [[["text" => "✏️ Ismni o'zgartirish"],["text" => "📱 Raqamni o'zgartirish"]],
                              [["text" => "🇺🇿 Tilni o'zgartirish"]],
                              [["text" => "⬅️ Ortga"]]],
    			 "resize_keyboard" => true];
            }
        	return $keyboard;
        }
  
  		static function back($lan){
        	if($lan == 'ru'){
          $keyboard = [
    			"keyboard" => [[["text" => "⬅️ Назад"]]],                      
    			"resize_keyboard" => true];
            }
          if($lan == 'uz'){
          $keyboard = [
    			"keyboard" => [[["text" => "⬅️ Ortga"]]],                  
    			"resize_keyboard" => true];
            }
        	return $keyboard;
        }
  
  		static function phone($lan){
        	if($lan == 'ru'){
          	$keyboard = [
    			"keyboard" => [[["text" => "📱 Мой номер","request_contact" => true]],[["text" => "⬅️ Назад"]]],                      
    			"resize_keyboard" => true];
            }
          if($lan == 'uz'){
          $keyboard = [
    			"keyboard" => [[["text" => "📱 Mening raqamim","request_contact" => true]],[["text" => "⬅️ Ortga"]]],                  
    			"resize_keyboard" => true];
            }
        	return $keyboard;
        }
  
  
  	static function menu_keyboard($lan){
        	if($lan == 'ru'){
            	$keyboard = [
    			"keyboard" => [[
                  ["text" => "🍔 Бургеры"],["text" => "🌮 Фаст-фуд"],["text" => "🍕 Пицца"]],             
                  [["text" => "🍟 Закуски"],["text" => "🥫 Соусы"],["text" => "🥤 Напитки"]],
                  [["text" => "☕️ Кофе"],["text" => "🍔🍟 Комбо"],["text" => "🍩 Десерты"]],
                  [["text" => "⬅️ Назад"]]],
    			"resize_keyboard" => true];
            }
      		if($lan == 'uz'){
            	$keyboard = [
    			"keyboard" => [[
                  ["text" => "🍔 Burgers"],["text" => "🌮 Fast-food"],["text" => "🍕 Pizza"]],             
                  [["text" => "🍟 Snacks"],["text" => "🥫 Souslar"],["text" => "🥤 Ichimlik"]],
                  [["text" => "☕️ Kofe"],["text" => "🍔🍟 Combo"],["text" => "🍩 Desertlar"]],
                  [["text" => "⬅️ Ortga"]]],
    			"resize_keyboard" => true];
            }
      		   
    		return $keyboard;
    }
  
  	static function burger($lan){
    		if($lan == 'ru'){
              $keyboard = [
    			"keyboard" => [[["text" => "Gamburger 25 000 сум"],["text" => "Cheese Burger 28 000 сум"]],[["text" => "Double Burger 33 000 сум"],             
          ["text" => "Double Cheese 36 000 сум"]],[["text" => "Chicken-Cheese 24 000 сум"],["text" => "BBQ Burger 26 000 сум"]],
          [["text" => "⬅️ Назад"]]],
    			"resize_keyboard" => true];
            }
      		if($lan == 'uz'){
            
            $keyboard = [
          "keyboard" => [[["text" => "Gamburger 25 000 сум"],["text" => "Cheese Burger 28 000 сум"]],[["text" => "Double Burger 33 000 сум"],             
          ["text" => "Double Cheese 36 000 сум"]],[["text" => "Chicken-Cheese 24 000 сум"],["text" => "BBQ Burger 26 000 сум"]],
          [["text" => "⬅️ Ortga"]]],
          "resize_keyboard" => true];		
    }
    return $keyboard;  
  }
  
}

?>