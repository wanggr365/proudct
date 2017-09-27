<?php

$url = "http://36.250.88.2:22280/xmlDeal/menuGet.php";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); 
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE); 
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$output = curl_exec($ch);
curl_close($ch);
/*echo $output;
echo '<meta http-equiv="content-type" content="text/html; charset=utf-8">';
echo '<pre>';
print_r(json_decode(json_encode($output),true));
echo '</pre>';
//print_r(json_decode(json_encode($output),true));
//echo var_dump($abc);
//$access_token = $abc['tag'];
//echo $access_token;
echo json_decode(json_encode($output),true)['tag'];*/
echo '<meta http-equiv="content-type" content="text/html; charset=utf-8">';
$result = json_decode(trim($output,chr(239).chr(187).chr(191)),true);
print_r($result);
echo $result['attributes']['NAME'];
?>