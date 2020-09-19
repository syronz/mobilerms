<?php
function money2word($number) {
    $max_size = pow(10,18);
    $string = '';
    if (!$number) return "zero";
    if (is_int($number) && $number < abs($max_size)) 
    {            
        switch ($number) 
        {
            // set up some rules for converting digits to words
            case $number < 0:
                $prefix = "negative";
                $suffix = money2word(-1*$number);
                $string = $prefix . " " . $suffix;
                break;
            case 1:
                $string = "یەک";
                break;
            case 2:
                $string = "دو";
                break;
            case 3:
                $string = "سێ";
                break;
            case 4: 
                $string = "چوار";
                break;
            case 5:
                $string = "پێنج";
                break;
            case 6:
                $string = "شەش";
                break;
            case 7:
                $string = "حەوت";
                break;
            case 8:
                $string = "هەشت";
                break;
            case 9:
                $string = "نۆ";
                break;                
            case 10:
                $string = "دە";
                break;            
            case 11:
                $string = "یازدە";
                break;            
            case 12:
                $string = "دوازدە";
                break;            
            case 13:
                $string = "سێزدە";
                break;            
            case 14:
                $string = "چوارده";
                break;
            case 15:
                $string = "پانزدە";
                break;  
            case 16:
                $string = "شازدە";
                break; 
            case 17:
                $string = "حەفدە";
                break; 
            case 18:
                $string = "هەژدە";
                break; 
            case 19:
                $string = "نۆزدە";
                break;          
            // case $number < 20:
            //     $string = money2word($number%10);
            //     // eighteen only has one "t"
            //     if ($number == 18)
            //     {
            //     $suffix = "een";
            //     } else 
            //     {
            //     $suffix = "دە";
            //     }
            //     $string .= $suffix;
            //     break;            
            case 20:
                $string = "بیست";
                break;            
            case 30:
                $string = "سی";
                break;            
            case 40:
                $string = "چل";
                break;            
            case 50:
                $string = "پەنجا";
                break;            
            case 60:
                $string = "شەست";
                break;            
            case 70:
                $string = "حەفتا";
                break;            
            case 80:
                $string = "هەشتا";
                break;            
            case 90:
                $string = "نەوەد";
                break;                
            case $number < 100:
                $prefix = money2word($number-$number%10);
                $suffix = money2word($number%10);
                $string = $prefix . " و " . $suffix;
                break;
            // handles all number 100 to 999
            case $number < pow(10,3):                    
                // floor return a float not an integer
                $prefix = money2word(intval(floor($number/pow(10,2)))) . " سەد";
                if ($number%pow(10,2)) $suffix = " و " . money2word($number%pow(10,2));
                // if(@$suffix) @$suffix ='و '.$suffix;
                $string = $prefix . @$suffix;
                break;
            case $number < pow(10,6):
                // floor return a float not an integer
                $prefix = money2word(intval(floor($number/pow(10,3)))) . " هەزار";
                if ($number%pow(10,3)) $suffix = money2word($number%pow(10,3));
                if(@$suffix) @$suffix =' و '.$suffix;
                $string = $prefix . " " . @$suffix;
                break;
            case $number < pow(10,9):
                // floor return a float not an integer
                $prefix = money2word(intval(floor($number/pow(10,6)))) . " میلیۆن";
                if ($number%pow(10,6)) $suffix = money2word($number%pow(10,6));
                if(@$suffix) @$suffix =' و '.$suffix;
                $string = $prefix . " " . @$suffix;
                break;                    
            case $number < pow(10,12):
                // floor return a float not an integer
                $prefix = money2word(intval(floor($number/pow(10,9)))) . " میلیارد";
                if ($number%pow(10,9)) $suffix = money2word($number%pow(10,9));
                if(@$suffix) @$suffix =' و '.$suffix;
                $string = $prefix . " " . @$suffix;    
                break;
            case $number < pow(10,15):
                // floor return a float not an integer
                $prefix = money2word(intval(floor($number/pow(10,12)))) . " ترلیارد";
                if ($number%pow(10,12)) $suffix = money2word($number%pow(10,12));
                if(@$suffix) @$suffix =' و '.$suffix;
                $string = $prefix . " " . @$suffix;    
                break;        
            // Be careful not to pass default formatted numbers in the quadrillions+ into this function
            // Default formatting is float and causes errors
            case $number < pow(10,18):
                // floor return a float not an integer
                $prefix = money2word(intval(floor($number/pow(10,15)))) . " هەزار تیلیارد";
                if ($number%pow(10,15)) $suffix = money2word($number%pow(10,15));
                $string = $prefix . " و " . $suffix;    
                break;                    
        }
    } else
    {
        echo "ERROR with - $number<br/> Number must be an integer between -" . number_format($max_size, 0, ".", ",") . " and " . number_format($max_size, 0, ".", ",") . " exclussive.";
    }
    return $string;    
}



function dsh_money($money,$decimal_check = 2,$symbol = null){
	if($money == 0)
		return 0;
	$negative = false;
	if($money < 0){
		$negative = true;
		$money = abs($money);
	}
	$decimal = $money - intval($money);
	$arr = array();
	if($money !== 0)
		while($money){
			$part = strval($money % 1000);
			$len = strlen($part);
			if($len == 1)
				$part = '00'.$part;
			else if($len == 2)
				$part = '0'.$part;
			$money =intval($money/1000);
			array_push($arr, $part);
		}
	else
		$arr = array(0);
	$arr = array_reverse($arr);
	
	$str = implode(',', $arr);
	if(strlen($str)>1){
		if($str[0]=='0')
			$str = substr($str, 1);
		if($str[0]=='0')
			$str = substr($str, 1);
	}
	
	if($decimal_check)
		if(round($decimal,$decimal_check))
			$str .= substr(strval(round($decimal,2)),1);
	if($symbol)
		$str .= ' '.$symbol;

	if($negative)
		$str = '-'.$str;
	return $str;
}

?>
