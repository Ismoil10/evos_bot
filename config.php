
<?php

class bot {
		
  	static function token(){
    	$token = 'bot5901566647:AAE67fXqGPwtDiy0_RM-zjmRrsBIJucp31g';
         return $token;
    }  
  
  	static function sendMessageText($chat_id, $text){
    file_get_contents("https://api.telegram.org/".bot::token()."/sendMessage?chat_id=".$chat_id."&parse_mode=HTML&text=".$text);
    }
	static function sendMessage($chat_id, $text, $keyboard){
  	$output = file_get_contents("https://api.telegram.org/".bot::token()."/sendMessage?chat_id=".$chat_id."&text=".$text."&reply_markup=".json_encode($keyboard));
    
    return $output;
    }
    static function sendPhoto($chat_id, $photo, $keyboard){
        file_get_contents("https://api.telegram.org/".bot::token()."/sendPhoto?chat_id=".$chat_id."&photo=".$photo."&reply_markup=".json_encode($keyboard));
      }
    static function sendPhotoText($chat_id, $photo, $text, $keyboard){
        $output = file_get_contents("https://api.telegram.org/".bot::token()."/sendPhoto?chat_id=".$chat_id."&photo=".$photo."&caption=".$text."&reply_markup=".json_encode($keyboard));
        file_put_contents('output.txt', $output);
        $product_data =  json_decode($output, true);
        $mid = $product_data['result']['message_id'];
        database::query("UPDATE `evos_users` SET `PROMD_MD` = '$mid' WHERE `CHAT_ID` = '$chat_id'");
    }
    static function updateInlineKey($chat_id, $message_id, $keyboard){
        file_get_contents("https://api.telegram.org/".bot::token()."/editMessageReplyMarkup?chat_id=".$chat_id."&message_id=".$message_id."&reply_markup=".json_encode($keyboard));
    }

    static function editMessageText($chat_id, $message_id, $text, $keyboard){
        file_get_contents("https://api.telegram.org/".bot::token()."/editMessageText?chat_id=".$chat_id."&message_id=".$message_id."&text=".$text."&reply_markup=".json_encode($keyboard));
    }
}

class database {
    static function connection(){
        $mysqli = mysqli_connect('localhost', 'u912555qno_ismoil', 'dcamp1234', 'u912555qno_ismoil');
        return $mysqli;
    }
    
    static function array_single($sql) {
        $single = mysqli_query(database::connection(), $sql);
        $array = mysqli_fetch_assoc($single);
        return $array;
    }
    
    static function array_all($sql) {
        $query = mysqli_query(database::connection(), $sql);
        $array = mysqli_fetch_all($query, MYSQLI_ASSOC);
        return $array;
    }
    
    static function query($sql) {
        $query = mysqli_query(database::connection(), $sql);
        return $query;
    }

    static function array_by_id($sql) {
        $query = mysqli_query(database::connection(), $sql);
        while($row = mysqli_fetch_assoc($query)){
            $array[$row['ID']] = $row;
        }
        return $array;
    }

    static function count($sql) {
        $con = database::connection();
        $query = mysqli_query($con, $sql);
        $rs['ID'] = mysqli_insert_id($con);
        return $rs;
    }
}

	function event_code($chat_id, $name){
      		database::query("UPDATE evos_users SET EVENT_CODE = '$name' WHERE CHAT_ID =".$chat_id);
  
    }	

?>