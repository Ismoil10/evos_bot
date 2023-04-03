<?php

  ini_set('display_errors', 1);
  ini_set('display_startup_errors', 1);
  error_reporting(E_ALL);
	
	require 'config.php';
	require 'keyboards.php';
	require 'msg.php';
	
	$webhook = file_get_contents("php://input");

  file_get_contents("https://api.telegram.org/".bot::token()."/setWebhook?url=back.dcamp.uz/ismoil/evos_bot/index.php");
	
	file_put_contents('message.txt', $webhook);
	
	$input_m = json_decode($webhook, true);

  if(isset($input_m['message'])){
    $chat_id = $input_m['message']['from']['id'];	
    $arr = $input_m['message']['text'];
  }
  if(isset($input_m['callback_query'])){
    $chat_id = $input_m['callback_query']['from']['id'];	
    $arr = $input_m['callback_query']['message']['text'];
    
    $callback_data = $input_m['callback_query']['data'];
    
    $message_id = $input_m['callback_query']['message']['message_id'];
  }
	
	$user_array = database::array_single("SELECT * FROM `evos_users` WHERE CHAT_ID ='$chat_id'");
	
	if(isset($user_array['ID'])){
      	$event_code = $user_array['EVENT_CODE'];
      	$lan = $user_array['LAN'];
    }else{
    	$name = $input_m['message']['from']['first_name'];
      	$user_query = database::query("INSERT INTO `evos_users` 
        (`CHAT_ID`, `LAN`, `EVENT_CODE`, `NAME`, `PHONE`) 
        VALUES ('$chat_id', 'NULL', 'start', '$name', 'NULL');");
    $event_code = "start";
    }	
    
	

	if($arr == '/start'){
    $text = message::start();
    $keyboard = keyboard::start();
    
      event_code($chat_id, 'wait_lan');
    bot::sendMessage($chat_id, $text, $keyboard);
    } else { 
		if($event_code == 'wait_lan'){
          if(isset($arr)){
       		if($arr == '🇷🇺 Ru'){ 
          		$lan = 'ru';	
         		database::query("UPDATE `evos_users` SET LAN='ru' WHERE CHAT_ID='$chat_id'");	
            }
          	
            if($arr == '🇺🇿 Uz'){ 
          		$lan = 'uz';	
         		database::query("UPDATE `evos_users` SET LAN='uz' WHERE CHAT_ID='$chat_id'");	
            }
        	
            $text = message::register($lan);
        	$keyboard = keyboard::register($lan);
        	
            bot::sendMessage($chat_id, $text, $keyboard);
          	
            event_code($chat_id, 'wait_contact');
          }
        
        }
      
      if($event_code == 'wait_contact'){
        	if(isset($input_m['message']['contact'])){
            	$name = $input_m['message']['contact']['first_name'];
            	$contact = $input_m['message']['contact']['phone_number'];
            $update_user = database::query("UPDATE `evos_users` SET 
            NAME = '$name', PHONE = '$contact' WHERE CHAT_ID = ".$chat_id);
            $text = message::name($lan);
            event_code($chat_id, 'wait_name');   
    		    bot::sendMessageText($chat_id, $text);	
        	
            } else {
            		
              $text = message::error($lan);
              bot::sendMessageText($chat_id, $text);
            }         
        	
        	} 
      			if($event_code == "wait_name"){
                if(isset($input_m['message']['text'])){
              	$update_name = database::query("UPDATE `evos_users` 
                SET NAME = '$arr' WHERE CHAT_ID =".$chat_id);
            	
                $text = message::success($lan);
				        $keyboard = keyboard::main($lan);		
            	    
            	  event_code($chat_id, 'main_menu'); 
                bot::sendMessage($chat_id, $text, $keyboard);        
             }	
           }
           if($event_code == "main_menu"){
            
            if(in_array($arr, ["🍴 Меню", "🛍 Мои заказы", "✍️ Оставить отзыв"])){
              
              if(in_array($arr,["🍴 Меню", "🍴 Menyu"])){
                
                $order_check = database::array_single("SELECT * FROM `evos_orders` WHERE `CHAT_ID` = '$chat_id' AND `STATUS` = 'draft'");
                
                if(!isset($order_check['ID'])){
                    
                    $order = database::query("INSERT INTO `evos_orders` (`CHAT_ID`,`STATUS`) VALUES ('$chat_id','draft')");    
                }   
              
                  $text = message::ask_loc($lan);
                  $keyboard = keyboard::ask_loc($lan);		
                  event_code($chat_id, 'ask_loc'); 
                  bot::sendMessage($chat_id, $text, $keyboard);
                
              } 
            elseif(in_array($arr,["🛍 Мои заказы", "🛍 Mening buyurtmalarim"])){
              bot::sendMessageText($chat_id, $arr);
            }
            elseif(in_array($arr,["✍️ Оставить отзыв", "✍️ Fikr bildirish"])){
              bot::sendMessageText($chat_id, $arr);
            }
            elseif(in_array($arr,["⚙️ Настройки", "⚙️ Sozlamalar"])){
              bot::sendMessageText($chat_id, $arr);
            } 
           } else {
                $text = message::mistake($lan);
                bot::sendMessageText($chat_id, $text);

            }
              
         }
  
            if($event_code == 'ask_loc'){
              if(isset($input_m['message']['location'])){
                    $lat = $input_m['message']['location']['latitude'];
                    $long = $input_m['message']['location']['longitude'];
                    $update = database::query("UPDATE `evos_orders` SET `LAT` = '$lat', 
                    `LONG` = '$long' WHERE CHAT_ID = '$chat_id' AND STATUS = 'draft'");
                    
                    $categories = database::array_all("SELECT * FROM `evos_categories`");      
                    $text = message::food_menu($lan);	
                    $keyboard = keyboard::food_menu($lan, $categories);
                    bot::sendMessage($chat_id, $text, $keyboard);
                    event_code($chat_id, 'food_menu');

                    $order_check = database::array_single("SELECT * FROM `evos_orders` WHERE `CHAT_ID` = '$chat_id' AND `STATUS` = 'draft'");

                    $ord['ID'] = $order_check['ID'];
                    
                    if($user_array['CURRENT_ORDER'] != $ord['ID']){
                      database::query("UPDATE `evos_users` SET `CURRENT_ORDER` = '$ord[ID]' WHERE `CHAT_ID` = '$chat_id'");
                    }
              } 
              elseif(in_array($arr,["⬅️ Назад", "⬅️ Ortga"])){
                  $text = message::success($lan);
                  event_code($chat_id, 'main_menu');	
                  $keyboard = keyboard::main($lan);
                  bot::sendMessage($chat_id, $text, $keyboard);
              } else {
                $text = message::mistake($lan);
                bot::sendMessageText($chat_id, $text);
              }			
            }    
            if($event_code == 'food_menu'){
                $cat_name = database::array_all("SELECT * FROM `evos_categories`");
                
                $lan_upper = strtoupper($lan);
                foreach($cat_name as $pd){
                  $p[] = $pd['NAME_'.$lan_upper];
                }
                //bot::sendMessageText($chat_id, json_encode($p));
                  if(in_array($arr, $p)){
                    
                    $cat_arr = database::array_single("SELECT * FROM 
                    `evos_categories` WHERE `NAME_$lan_upper` = '$arr'");

                    database::query("UPDATE `evos_users` SET `LAST_CAT` = '$cat_arr[ID]' WHERE `CHAT_ID` = '$chat_id'");

                    $product = database::array_all("SELECT * FROM 
                    `evos_products` WHERE `CAT_ID` = '$cat_arr[ID]'");

                    //$text = message::cat_menu($lan);	
                    $keyboard = keyboard::cat_menu($lan_upper, $product);
                    bot::sendPhoto($chat_id, $cat_arr['PHOTO_'.$lan_upper], $keyboard);
                    event_code($chat_id, 'product_menu');

                  } elseif (in_array($arr, ["📥 Корзина", "📥 Savat"])){

                    $text = message::cart($lan, $chat_id);

                    if($text == "Ваша корзина пусто"){

                      bot::sendMessageText($chat_id, $text);

                    } else {
                      
                      $keyboard = keyboard::cart($lan, $chat_id);                  
                      bot::sendMessage($chat_id, $text, $keyboard);
                      
                    }
              
                  } if(in_array($arr, ["⬅️ Назад", "⬅️ Ortga"])){
                    
                    event_code($chat_id, 'ask_loc');
                    
                    $text = message::ask_loc($lan);	
                    
                    $keyboard = keyboard::ask_loc($lan);
                   
                    bot::sendMessage($chat_id, $text, $keyboard);
                  
                  }
                }
                if($event_code == 'product_menu'){
                  $lan_upper = strtoupper($lan);
                  $eats = database::array_all("SELECT * FROM `evos_products` 
                  WHERE `CAT_ID` = '$user_array[LAST_CAT]'");                  
                    foreach($eats as $fd){
                        $food[] = $fd['NAME_'.$lan_upper];
                    }
                    if(in_array($arr, $food)){

                      $text = message::success($lan);
                      $keyboard = keyboard::cart_back($lan);
                      bot::sendMessage($chat_id, $text, $keyboard);

                      $eat = database::array_single("SELECT * FROM `evos_products` 
                      WHERE `NAME_$lan_upper` = '$arr'");
                      
                      database::query("UPDATE `evos_users` 
                      SET `LAST_PRODUCT` = '$eat[ID]', `COUNTER` = 1 
                      WHERE `CHAT_ID` = '$chat_id'");

                      $text = message::product_info($lan, $eat);
                      
                      $keyboard = keyboard::product_info($lan, '1');
                      
                      bot::sendPhotoText($chat_id, $eat['PHOTO'], $text, $keyboard);
                      
                      event_code($chat_id, 'product_info');

                      $check_cart = database::array_single("SELECT * FROM `evos_cart` 
                      WHERE `ORDER_ID` = '$user_array[CURRENT_ORDER]' AND `STATUS` = 'draft'");
                      
                      if(isset($check_cart['ID'])){

                      database::query("UPDATE `evos_cart` SET 
                      `PRODUCT_ID` = '$eat[ID]', AMOUNT = 1 
                      WHERE `ID` = '$check_cart[ID]'");
                      
                    } else {
                      database::query("INSERT INTO `evos_cart` (`ORDER_ID`, `PRODUCT_ID`, `AMOUNT`, `STATUS`) 
                      VALUES ('$user_array[CURRENT_ORDER]', '$eat[ID]', '1', 'draft')");
                      }
                    } elseif (in_array($arr, ["⬅️ Назад", "⬅️ Ortga"])){
                      $categories = database::array_all("SELECT * FROM `evos_categories`");
                      event_code($chat_id, 'food_menu');
                      $text = message::food_menu($lan);	
                      $keyboard = keyboard::food_menu($lan, $categories);
                      bot::sendMessage($chat_id, $text, $keyboard);

                    } elseif(!isset($callback_data)){
                      $text = message::success($lan);
                      bot::sendMessageText($chat_id, $text);
                  }


              
                  if(isset($callback_data)){
                        if($callback_data == "make_order"){
                            $text = message::payment($lan);
                            $keyboard = keyboard::payment($lan);
                            event_code($chat_id, "choose_payment");
                            bot::sendMessage($chat_id, $text, $keyboard);
                        }
                      
                      $data = explode('_', $callback_data);
                      if($data[0]=='delete'){
                       database::query("DELETE FROM `evos_cart` WHERE `PRODUCT_ID`='$data[1]' AND `ORDER_ID`='$user_array[CURRENT_ORDER]' AND `STATUS`='open'");
                        
                        $text = message::cart($lan, $chat_id);
                        $keyboard = keyboard::cart($lan, $chat_id);
                       
                        bot::editMessageText($chat_id, $message_id, $text, $keyboard);
                         //bot::sendMessage($chat_id, $text, $keyboard);  
                         
                      }
                  }
                }
                if($event_code == 'product_info'){
                  if(in_array($arr, ["⬅️ Назад", "⬅️ Ortga"])){
                    $lan_upper = strtoupper($lan);
                    $cat_arr = database::array_single("SELECT * FROM 
                    `evos_categories` WHERE `ID` = '$user_array[LAST_CAT]'");

                    $product = database::array_all("SELECT * FROM 
                    `evos_products` WHERE `CAT_ID` = '$cat_arr[ID]'");

                    $categories = database::array_all("SELECT * FROM `evos_products` 
                    WHERE `CAT_ID` = '$user_array[LAST_CAT]'");
                    event_code($chat_id, 'product_menu');
                    $text = message::success($lan);	
                    $keyboard = keyboard::cat_menu($lan_upper, $product);
                    bot::sendPhoto($chat_id, $cat_arr['PHOTO_'.$lan_upper], $keyboard);

                  }

                  if($callback_data == 'increase'){

                    $cart_data = database::array_single("SELECT * FROM `evos_cart` 
                    WHERE `ORDER_ID` = '$user_array[CURRENT_ORDER]' AND `STATUS` = 'draft'");
  
                    $cart_data['AMOUNT']++;
  
                    database::query("UPDATE `evos_cart` SET `AMOUNT` = '$cart_data[AMOUNT]' WHERE `ID` = '$cart_data[ID]'");
  
                    $keyboard = keyboard::product_info($lan, $cart_data['AMOUNT']);
                    bot::updateInlineKey($chat_id, $user_array['PROMD_MD'], $keyboard);
                  }
                  if($callback_data == 'decrease'){
  
                    $cart_data = database::array_single("SELECT * FROM `evos_cart` 
                    WHERE `ORDER_ID` = '$user_array[CURRENT_ORDER]' AND `STATUS` = 'draft'");

                    if($cart_data['AMOUNT']>1){
  
                      $cart_data['AMOUNT']--;

                      database::query("UPDATE `evos_cart` SET `AMOUNT` = '$cart_data[AMOUNT]' WHERE `ID` = '$cart_data[ID]'");
                    } else {
                      $cart_data['AMOUNT'] = 1;
                    }
                    $keyboard = keyboard::product_info($lan, $cart_data['AMOUNT']);
                    bot::updateInlineKey($chat_id, $user_array['PROMD_MD'], $keyboard);
                  }
                  if($callback_data == 'add_cart'){
                    $lan_upper = strtoupper($lan);
                    
                    $cart_check = database::array_single("SELECT * FROM `evos_cart` 
                    WHERE `ORDER_ID` = '$user_array[CURRENT_ORDER]' AND `PRODUCT_ID` = '$user_array[LAST_PRODUCT]' AND `STATUS` = 'open'");

                    $cart_data = database::array_single("SELECT * FROM `evos_cart` 
                    WHERE `ORDER_ID` = '$user_array[CURRENT_ORDER]' AND `STATUS` = 'draft'");
                    
                    if(isset($cart_check['ID'])){
                                
                      $amount = $cart_check['AMOUNT'] + $cart_data['AMOUNT'];
                      $update_cart = database::query("UPDATE `evos_cart` SET `AMOUNT` = '$amount' WHERE `ID` = '$cart_check[ID]'");

                    } else {

                      database::query("UPDATE `evos_cart` SET `STATUS` = 'open' WHERE `ID` = '$cart_data[ID]'");
                    }
            
                    $add_eat = database::array_single("SELECT * FROM `evos_products` WHERE `ID` = '$cart_data[PRODUCT_ID]'");

                    $text = message::add_cart($lan, $cart_data, $add_eat);

                    bot::sendMessageText($chat_id, $text);
                    
                    $cat_arr = database::array_single("SELECT * FROM 
                    `evos_categories` WHERE `ID` = '$user_array[LAST_CAT]'");

                    $product = database::array_all("SELECT * FROM 
                    `evos_products` WHERE `CAT_ID` = '$cat_arr[ID]'");

                    $categories = database::array_all("SELECT * FROM `evos_products` 
                    WHERE `CAT_ID` = '$user_array[LAST_CAT]'");
                    event_code($chat_id, 'product_menu');
                    $text = message::success($lan);	
                    $keyboard = keyboard::cat_menu($lan_upper, $product);
                    bot::sendPhoto($chat_id, $cat_arr['PHOTO_'.$lan_upper], $keyboard);

                  }

                }
  
  }			

?>