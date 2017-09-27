<?php

$template = array('touser' => "odrEdt4BSKcs-VvOXocU3joswWDU",
                 'template_id' => "bzSCOsNZxqq9nIE8d_wlWESLVbhbIGsAfzauXit1cuU",
                 'url' => "http://www.qq.com",
                 'topcolor' => "#7B68EE",
                 'data' => array('first'    => array('value' => urlencode("你好，有新零星工程单！"),
                                                    'color' => "#743A3A",
                                                     ),
								'keyword1'    => array('value' => urlencode("已断网2小时，互动可用，无网络！"),
                                                    'color' => "#743A3A",
                                                     ),
                                 'keyword2' => array('value' => date('Y-m-d H:i:s',time()),
                                                    'color' => "#FF0000",
                                                     ),
                                 'keyword3'     => array('value' => urlencode("张三"),
                                                    'color' => "#C4C400",
                                                     ),
                                 'keyword4'     => array('value' => urlencode("13811111111"),
                                                    'color' => "#0000FF",
                                                     ),
                                 'remark'     => array('value' => urlencode("\\n请尽快接单并处理。"),
                                                    'color' => "#008000",
                                                     )

                                 )
                 );

$url = "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token="."h3RIkoXE-Ix6wGSvz37bQt2k_m8KTRNlAP6h-SxdM2I4JNSy-znHSG8_SbS6lU4yPL0MEQlqps6RJXc945z4BJg4xIdDbJRt-eNrETw82-d4VWR_cjChoOY6LOJBLE_-RDIiCDAYKP";
$result = https_request($url, urldecode(json_encode($template)));
var_dump($result);
    
function https_request($url,$data = null){
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
    if (!empty($data)){
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    }
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $output = curl_exec($curl);
    curl_close($curl);
    return $output;
}
?>