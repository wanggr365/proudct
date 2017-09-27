<?php
/**
 * 微信公众平台-自定义菜单功能源代码
 * ================================
 * Copyright 2013-2014 David Tang
 * http://www.cnblogs.com/mchina/
 * 乐思乐享微信论坛
 * http://www.joythink.net/
 * ================================
 * Author:David|唐超
 * 个人微信：mchina_tang
 * 公众微信：zhuojinsz
 * Date:2013-10-12
 */

//header('Content-Type: text/html; charset=UTF-8');



$cust_name = "陈龙";
		$address = "107";
		$mac =  "";
		$stbno =  "";
		$cardno =  "";
		$cert_no =  "";
		$phone =  "";
		//echo $phone!=null ;
		$TOKEN_URL="http://36.250.88.2:8085/ppjj/tvPpjj.action?method=loginBossByName&custcode=850000232256";

//$json=file_get_contents($TOKEN_URL);

$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $TOKEN_URL);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); 
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE); 
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$output = curl_exec($ch);
		curl_close($ch);
		//return json_decode($output, true);

$result = urldecode($output);
$data = str_replace("gb2312","utf-8",$result);
$parser = xml_parser_create();//创建解析器
xml_parse_into_struct($parser, $data, $values, $index);//解析到数组
xml_parser_free($parser);//释放资源
//print_r($values);
//echo $values[0]['tag'];
//echo json_encode($values[1],JSON_UNESCAPED_UNICODE);
//echo json_decode($values);
//echo "\n索引数组\n";
//print_r($index);
//echo "\n数据数组\n";
print_r($values);
if($values[0]['attributes']['RETURN-CODE'] == 0) {
			$returnStr = "";
			$arrLen = count($values);
			for ($i = 1;$i < $arrLen-1; $i++){ 
				$returnStr .= "客户编号：".$values[$i]['attributes']['CUSTCODE']."（长按复制）<br>"."地址：".$values[$i]['attributes']['ADDRNAME'];; 
			} 
			echo $returnStr;
		}else{
			echo "该客户不存在！";
		}
//echo $values[0]['attributes']['RETURN-CODE'];
/*$xml = simplexml_load_string($data);
$json = json_encode($xml); 
echo $json;*/
?>

