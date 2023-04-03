<?php

class message {

		static function payment($lan){
			if($lan == 'ru') {
				$text = "Выберите тип оплаты";
			  }	
			  if($lan == 'uz') {
				$text = "To'lov turini tanlang";
			  }
			  return $text;
		}

		static function cart($lan, $chat_id){
			$order_select = database::array_single("SELECT * FROM `evos_orders` WHERE `CHAT_ID` = '$chat_id' AND `STATUS` = 'draft'");
			$cart_data = database::array_all("SELECT * FROM `evos_cart` WHERE `ORDER_ID` = '$order_select[ID]' AND `STATUS` = 'open'");
			$products = database::array_by_id("SELECT * FROM `evos_products`");

			$sum = 0;

			if(empty($cart_data)){
				$text = 'Ваша корзина пусто';
			} else {
			foreach($cart_data as $v){
				$lan_upper = strtoupper($lan);
					$cart = $cart.$v['AMOUNT']." ✖️ ".$products[$v['PRODUCT_ID']]['NAME_'.$lan_upper]." ";
					
					$sum = $sum + $products[$v['PRODUCT_ID']]['PRICE'] * $v['AMOUNT'];
			}
			$total = $sum + 10000;
			$text = "В корзине: 
			$cart
			Товары: $sum сум
			Доставка: 10000 сум
			Итого: $total сум";
		}
			return $text;
		}

  		static function start(){
        	$text = "Tilni tanlang | Выберите язык";
        	return $text;
        }
  		
		static function register($lan){
        	if($lan == 'ru') {
              $text = "Привет, добро пожаловать в first_bot";
            }	
          	if($lan == 'uz') {
             	$text = "Salom, first_bot ga xush kelibsiz";
            } 
          	
        	return $text; 
        }	
  		
  		static function new_name($lan){
        	if($lan == 'ru') {
              $text = "✅ Готов";
            }	
          	if($lan == 'uz') {
             	$text = "✅ Tayyor";
            } 	
        	return $text; 
        }
  
  		static function option($lan){
        	if($lan == 'ru') {
              $text = "Настройки";
            }	
          	if($lan == 'uz') {
             	$text = "Sozlamalar";
            } 	
        	return $text; 
        }
  
  		static function name($lan){
        	if ($lan == 'ru') {
              $text = "Введите ваше имя";
            }	
          	if ($lan == 'uz') {
              	$text = "Ismingizni kiriting";
            }
        
        	return $text;
        }
  
  		static function phone($lan){
        	if ($lan == 'ru') {
              $text = "Отправьте или введите ваш номер телефона в виде: +998 ** *** ****";
            }	
          	if ($lan == 'uz') {
              	$text = "Raqamni +998 ** *** **** shaklida kiriting yoki yuboring.";
            }
        
        	return $text;
        }
  		
  		static function error($lan){
        	if ($lan == 'ru') {
              $text = "Пожалуйста, введите ваш контакт!";
            }	
          	if ($lan == 'uz') {
              $text = "Iltimos, telefon nomeringizni kiriting!";
            }
        
        	return $text;
        }
  
  		static function success($lan){
        	if ($lan == 'ru') {
              $text = "Выберите одну из ниже";
            }	
          	if ($lan == 'uz') {
              $text = "Quyidagilardan birini tanlang";
            }
        
        	return $text;
        }

		static function mistake($lan){
        	if ($lan == 'ru') {
              $text = "Пожалуйста, нажмите одну из кнопок ниже";
            }	
          	if ($lan == 'uz') {
              $text = "Iltimos, quyidagi tugmalardan birini bosing";
            }
        
        	return $text;
        }
		static function ask_loc($lan){
        	if ($lan == 'ru') {
              $text = "Отправьте 📍 геолокацию или выберите адрес доставки";
            }	
          	if ($lan == 'uz') {
              $text = "📍 Geolokatsiyani yuboring yoki yetkazib berish manzilini tanlang";
            }
        
        	return $text;
        }

		static function food_menu($lan){
        	if ($lan == 'ru') {
              $text = "Выберите категорию.";
            }	
          	if ($lan == 'uz') {
              $text = "Bo'limni tanlang.";
            }
        
        	return $text;
        }

		static function add_cart($lan, $cart_data, $add_eat){
        	
			if ($lan == 'ru') {
				$lan_upper = strtoupper($lan);
              $text = "<b>Savatga ".$cart_data['AMOUNT']." ta (".$add_eat['NAME_'.$lan_upper].") qo'shildi!</b>";
            }	
          	if ($lan == 'uz') {
				$lan_upper = strtoupper($lan);
              $text = "<b>Savatga ".$cart_data['AMOUNT']." ta (".$add_eat['NAME_'.$lan_upper].") qo'shildi!</b>";
            }
        
        	return $text;
        }

		static function product_info($lan, $eat){
        	
			if ($lan == 'ru') {
				$text = "$eat[DESCRIPTION_RU]   Цена: $eat[PRICE] сум";
			}	
				if ($lan == 'uz') {
				$text = "$eat[DESCRIPTION_UZ]   Narxi: $eat[PRICE] so'm";
			}
			return $text;
        }

		/*static function product_info($lan, $eat){

			if ($lan == 'ru') {
				$lan = strtoupper($lan);
					$text = "$eat[DESCRIPTION_RU] Цена:  $eat[PRICE] сум";
										
			}	
			if ($lan == 'uz') {
					$text = "$eat[DESCRIPTION_UZ] Цена:  $eat[PRICE] so'm";				
				
			}
			return $text;
        }*/



}

?>