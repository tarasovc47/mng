<?php
namespace common\components;

use yii\base\Component;
use yii\helpers\Json;

class SiteHelper extends Component{

    public static function Seconds2HMS($time){
        $sec = $time % 60;
        if($sec<10){$sec='0'.$sec;}
        $time = floor($time / 60);

        $min = $time % 60;
        if($min<10){$min='0'.$min;}
        $time = floor($time / 60);

//        $hours = $time % 24;
//        $time = floor($time / 24);
//
//        $days = $time;
//        if($days!=0){
//            return $days."d ".$time. ":" . $min . ":" . $sec;
//        }

//        return $time. ":" . $min . ":" . $sec;
        return $time. ":" . $min . ":" . $sec;
    }
    public static function debug($arr){
        echo "<pre>";
        print_r($arr);
        echo "</pre>";
    }

    public static function  secondsToTime($seconds) {
        $dtF = new \DateTime('@0');
        $dtT = new \DateTime("@$seconds");
        return $dtF->diff($dtT)->format('%aд, %hч, %iм и %s сек');
    }

    public static function TagStripper($var){
//        $name = strip_tags($_POST['name']);
//        $name = htmlentities($_POST['name'], ENT_QUOTES, "UTF-8");
//        $name = htmlspecialchars($_POST['name'], ENT_QUOTES)
        return htmlentities(htmlspecialchars(strip_tags($var), ENT_QUOTES), ENT_QUOTES, "UTF-8");
    }

    public static function cidr2rangeInLong($cidr) {
        $cidr = explode('/', $cidr);
        $range_start = ip2long($cidr[0]);
        $range_end = $range_start + pow(2, 32-intval($cidr[1])) - 1;
        return [$range_start, $range_end];
    }

	public static function translit($str) 
	{
		$tr = array(
			"А" => "a", "Б" => "b", "В" => "v", "Г" => "g",
			"Д" => "d", "Е" => "e", "Ж" => "j", "З" => "z", "И" => "i",
			"Й" => "y", "К" => "k", "Л" => "l", "М" => "m", "Н" => "n",
			"О" => "o", "П" => "p", "Р" => "r", "С" => "s", "Т" => "t",
			"У" => "u", "Ф" => "f", "Х" => "h", "Ц" => "ts", "Ч" => "ch",
			"Ш" => "sh", "Щ" => "sch", "Ъ" => "", "Ы" => "yi", "Ь" => "",
			"Э" => "e", "Ю" => "yu", "Я" => "ya", "а" => "a", "б" => "b",
			"в" => "v", "г" => "g", "д" => "d", "е" => "e", "ж" => "j",
			"з" => "z", "и" => "i", "й" => "y", "к" => "k", "л" => "l",
			"м" => "m", "н" => "n", "о" => "o", "п" => "p", "р" => "r",
			"с" => "s", "т" => "t", "у" => "u", "ф" => "f", "х" => "h",
			"ц" => "ts", "ч" => "ch", "ш" => "sh", "щ" => "sch", "ъ" => "y",
			"ы" => "yi", "ь" => "", "э" => "e", "ю" => "yu", "я" => "ya",
			" " => "-", "." => "-", "(" => "", ")" => "",
			"—" => '-', "@" => "", '"' => "", "#" => "", "№" => "", ";" => "",
			"$" => "", "%" => "", ":" => "", "^" => "", "&" => "", "?" => "",
			"*" => "", "|" => "", "\\" => "", "<" => "", ">" => "", "=" => "",
			"+" => "", "`" => "", "~" => "", "," => "",
		);

		return strtr($str, $tr);
	}

	public static function genUniqueKey($length = 9, $salt = '') 
	{
		$string = 'abcdefghijlklmnopqrstuvwxyzABCDEFGHIJLKLMNOPQRSTUVWXYZ1234567890';
		$result = '';
		$n = mb_strlen($string) - 1;
		for($i = 0; $i < $length; $i++){
			$result .= $string[rand(0, $n)];
		}
		if($length and $length > 0)
			return mb_substr(md5($result . $salt . time()), 0, $length);
		else
			return mb_substr(md5($result . time()), 0);
	}
    
    public static function russianMonth($monthNumber, $fullFormat = false)
    {
		$n = (int)$monthNumber;
		if(!$fullFormat){
			switch($n){
				case 1:
					return 'янв';
				case 2: 
					return 'фев';
				case 3: 
					return 'мар';
				case 4: 
					return 'апр';
				case 5: 
					return 'май';
				case 6: 
					return 'июн';
				case 7: 
					return 'июл';
				case 8: 
					return 'авг';
				case 9: 
					return 'сен';
				case 10: 
					return 'окт';
				case 11: 
					return 'ноя';
				case 12: 
					return 'дек';
			}
		}
		else{
			switch($n){
				case 1:
					return 'января';
				case 2: 
					return 'февраля';
				case 3: 
					return 'марта';
				case 4: 
					return 'апреля';
				case 5: 
					return 'мая';
				case 6: 
					return 'июня';
				case 7: 
					return 'июля';
				case 8: 
					return 'августа';
				case 9: 
					return 'сентября';
				case 10: 
					return 'октября';
				case 11: 
					return 'ноября';
				case 12: 
					return 'декабря';
			}
		}
	}

	public static function russianDate($datetime = null, $fullFormat = false)
	{
        if(!$datetime || $datetime == 0)
            return '';
            
		if(is_numeric($datetime)){
			$timestamp = $datetime;
		}elseif(is_string($datetime)){
			$timestamp = strtotime($datetime);
        }else{
			$timestamp = time();
		}
		$date = explode(".", date("d.m.Y", $timestamp));
		if($fullFormat){
			$m = self::russianMonth($date[1], true);
		}
		else{
			$m = self::russianMonth($date[1]);
		}
		
		return $date[0] . ' ' . $m . ' ' . $date[2];
	}

	public static function clearPhone($phone)
	{
		$ignoreSym = array('+', ' ', '(', ')', '-');
		$phone = str_replace($ignoreSym, '', $phone);

		if (mb_substr($phone, 0, 1) == 7 && mb_strlen($phone) != 10) {
			$phone = mb_substr($phone, 1);
		}
		
		return $phone;
	}

	public static function handsomePhone($phone)
	{
		$new_phone = '+7 ('.mb_substr($phone, 0, 3).') '.mb_substr($phone, 3, 3).'-'.mb_substr($phone, 6, 4);
		
		return $new_phone;
	}

	public static function dataFromPHPtoJS($name, $data)
	{
		$json = json_encode($data, JSON_FORCE_OBJECT);
	 	return "<script>var ".$name." = ".$json.";</script>";
	}

	public static function textIntro($str, $length = 20){
		if(mb_strlen($str) > $length){
			$temp = mb_substr($str, 0, $length);
			$arr = explode(' ', $temp);
			unset($arr[count($arr) - 1]);
			$str = implode(' ', $arr).'...';
		}
		
		return $str;
	}

	public static function excelCellLetters() {
		return array(
			"A", "B", "C", "D", "E", "F", "G", "H", "I", "J", 
			"K", "L", "M", "N", "O", "P", "Q", "R", "S", "T",
			"U", "V", "W", "X", "Y", "Z", "AA", "AB", "AC", 
			"AD", "AE", "AF", "AG", "AH", "AI", "AJ", 
		);
	}

	public static function getSslPage($url) {
	    $ch = curl_init();
	    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	    curl_setopt($ch, CURLOPT_HEADER, FALSE);
	    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
	    curl_setopt($ch, CURLOPT_UNRESTRICTED_AUTH, TRUE);
	    curl_setopt($ch, CURLOPT_URL, $url);
	    curl_setopt($ch, CURLOPT_REFERER, $url);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	    $result = curl_exec($ch);
	    curl_close($ch);
	    return $result;
	}

	### Перевод в float. Учитывает и "." и ",";
	public static function tofloat($num) {
		$dotPos = strrpos($num, '.');
		$commaPos = strrpos($num, ',');
		$sep = (($dotPos > $commaPos) && $dotPos) ? $dotPos : 
			((($commaPos > $dotPos) && $commaPos) ? $commaPos : false);

		if (!$sep) {
			return floatval(preg_replace("/[^0-9]/", "", $num));
		} 

		return floatval(
			preg_replace("/[^0-9]/", "", substr($num, 0, $sep)) . '.' .
			preg_replace("/[^0-9]/", "", substr($num, $sep+1, strlen($num)))
		);
	}

	public static function to_postgre_array($data) {
	    settype($data, 'array');
	    $result = array();
	    foreach($data as $t){
	        if(is_array($t)){
	            $result[] = self::to_postgre_array($t);
	        }
	        else{
	            $t = str_replace('"', '\\"', $t);

	            if(!is_numeric($t)){
	                $t = '"' . $t . '"';
	            }

	            $result[] = $t;
	        }
	    }

	    return '{' . implode(",", $result) . '}';
	}

	public static function to_php_array($data){
		$data = trim($data,"{}");
		$data = explode(",", $data);

		foreach($data as $key => $elem){
			if(mb_substr($elem, 0, 1) == "{"){
				$data[$key] = self::to_php_array($elem);
			}
		}

		return $data;
	}

	public static function FBytes($bytes, $precision = 2) {
	    $units = array('B', 'KB', 'MB', 'GB', 'TB');
	    $bytes = max($bytes, 0);
	    $pow = floor(($bytes?log($bytes):0)/log(1024));
	    $pow = min($pow, count($units)-1);
	    $bytes /= pow(1024, $pow);
	    return round($bytes, $precision).' '.$units[$pow];
	}

	public static function getAddressNameByUuid($uuid){
		$address_name = self::getSslPage('https://api.t72.ru/fias/place/select.json?uuid='.$uuid);
		$address_name = json_decode($address_name, true);
		$address_name = array_shift($address_name['result']);
		return $address_name['text'];
	}

    public static function Seconds2Times($seconds)
    {
        $times = array();

        // считать нули в значениях
        $count_zero = false;

        // количество секунд в году не учитывает високосный год
        // поэтому функция считает что в году 365 дней
        // секунд в минуте|часе|сутках|году
        $periods = array(60, 3600, 86400, 31536000);

        for ($i = 3; $i >= 0; $i--)
        {
            $period = floor($seconds/$periods[$i]);
            if (($period > 0) || ($period == 0 && $count_zero))
            {
                $times[$i+1] = $period;
                $seconds -= $period * $periods[$i];

                $count_zero = true;
            }
        }

        $times[0] = $seconds;
        return $times;
    }

	public static function loadAllAddressInfoByUuid($uuid){
		$address = self::getSslPage('https://api.t72.ru/fias/place/select.json?uuid='.$uuid);
		$address = Json::decode($address, true);
		return $address;
	}

	public static function loadAllAddressInfoByFias($fias){
		$address = self::getSslPage('https://api.t72.ru/fias/place/select.json?fias='.$fias);
		$address = Json::decode($address, true);
		return $address;
	}

	public static function plural($number, $one, $two, $five) {
		if(($number - $number % 10) % 100 != 10){
			if($number % 10 == 1){
				$result = $one;
			}
			elseif($number % 10 >= 2 && $number % 10 <= 4){
				$result = $two;
			}
			else{
				$result = $five;
			}
		} 
		else{
			$result = $five;
		}
		
		return $result;
	}

	public static function dateAgo($timestamp){
		$now = time();
		$date = $now - $timestamp;

		if($date < 0){
			return $timestamp;
		}

		if($date < 86400){
			if(strtotime("midnight", $timestamp) < strtotime("midnight", $now)){
				return "вчера";
			}
			return "сегодня";
		}

		$date = ceil($date / 86400);

		return $date . " " . self::plural($date, "день", "дня", "дней") . " назад";
	}

    public static function generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}
