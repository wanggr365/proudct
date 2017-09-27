<?php
/*
    方倍工作室 http://www.cnblogs.com/txw1958/
    CopyRight 2013 www.doucube.com  All Rights Reserved
*/

define("TOKEN", "weixin");
$wechatObj = new wechatCallbackapiTest();
if (isset($_GET['echostr'])) {
    $wechatObj->valid();
}else{
    $wechatObj->responseMsg();
}

class wechatCallbackapiTest
{
    public function valid()
    {
        $echoStr = $_GET["echostr"];
        if($this->checkSignature()){
            echo $echoStr;
            exit;
        }
    }

    public function checkSignature()
    {
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];

        $token = TOKEN;
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode( $tmpArr );
        $tmpStr = sha1( $tmpStr );

        if( $tmpStr == $signature ){
            return true;
        }else{
            return false;
        }
    }

     public function responseMsg()
    {
        $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
        if (!empty($postStr)){
            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            $RX_TYPE = trim($postObj->MsgType);

            switch ($RX_TYPE)
            {
                case "text":
                    $resultStr = $this->receiveText($postObj);
                    break;
                case "event":
                    $resultStr = $this->receiveEvent($postObj);
                    break;
                default:
                    $resultStr = "";
                    break;
            }
            echo $resultStr;
        }else {
            echo "";
            exit;
        }
    }
	
	public function receiveText($object)
    {
        /*$funcFlag = 0;
        $contentStr = "中国国旗：".$this->utf8_bytes(0x1F60A)."\n";//"<a href = \"http://www.qq.com\">欢迎关注！</a>".$object->Content;
        $resultStr = $this->transmitText($object, $contentStr, $funcFlag);
        return $resultStr;*/
		$funcFlag = 0;
        $keyword = trim($object->Content);
        $resultStr = "";
        $contentStr = "";
		
		if (is_array($contentStr)){
            $resultStr = $this->transmitNews($object, $contentStr);
        }else{
            $resultStr = $this->transmitText($object, $contentStr);
        }
        return $resultStr;
    }
	
	public function getUnionIdByWeinxin($object){
		$access_token = $this->accessToken();
		$openid = $object->FromUserName;
		$url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=$access_token&openid=$openid&lang=zh_CN";
		$output = $this->https_request($url);
		$jsoninfo = json_decode($output, true);
		return $jsoninfo["unionid"];
	}
	
	public function accessToken() {
		$tokenFile = "access_token_ywy.txt";//缓存文件名
		$data = json_decode(file_get_contents($tokenFile));
		//print_r($data);
		if ($data->expire_time < time() or !$data->expire_time) {
		$appid = "wx3a20e613be177269";
		$appsecret = "b722fba7bd164688bcfa5b4acb2f1bee";
		$url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$appid&secret=$appsecret";
		  $res = $this->getJson($url);
		  $access_token = $res['access_token'];
		  if($access_token) {
				$data1['expire_time'] = time() + 7000;
				$data1['access_token'] = $access_token;
				$fp = fopen($tokenFile, "w");
				fwrite($fp, json_encode($data1));
				fclose($fp);
			}
		} else {
			$access_token = $data->access_token;
		}
		return $access_token;
	  }
	  
	  public function transmitText($object, $content, $funcFlag = 0)
    {
        $textTpl = "<xml>
<ToUserName><![CDATA[%s]]></ToUserName>
<FromUserName><![CDATA[%s]]></FromUserName>
<CreateTime>%s</CreateTime>
<MsgType><![CDATA[text]]></MsgType>
<Content><![CDATA[%s]]></Content>
<FuncFlag>%d</FuncFlag>
</xml>";
        $resultStr = sprintf($textTpl, $object->FromUserName, $object->ToUserName, time(), $content, $funcFlag);
        return $resultStr;
    }

    public function transmitNews($object, $arr_item, $funcFlag = 0)
    {
        //首条标题28字，其他标题39字
        if(!is_array($arr_item))
            return;

        $itemTpl = "    <item>
					<Title><![CDATA[%s]]></Title>
					<Description><![CDATA[%s]]></Description>
					<PicUrl><![CDATA[%s]]></PicUrl>
					<Url><![CDATA[%s]]></Url>
				</item>
			";
        $item_str = "";
        foreach ($arr_item as $item)
            $item_str .= sprintf($itemTpl, $item['Title'], $item['Description'], $item['PicUrl'], $item['Url']);

        $newsTpl = "<xml>
			<ToUserName><![CDATA[%s]]></ToUserName>
			<FromUserName><![CDATA[%s]]></FromUserName>
			<CreateTime>%s</CreateTime>
			<MsgType><![CDATA[news]]></MsgType>
			<Content><![CDATA[]]></Content>
			<ArticleCount>%s</ArticleCount>
			<Articles>
			$item_str</Articles>
			<FuncFlag>%s</FuncFlag>
			</xml>";

        $resultStr = sprintf($newsTpl, $object->FromUserName, $object->ToUserName, time(), count($arr_item), $funcFlag);
        return $resultStr;
    }
	
	public function getJson($url){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); 
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE); 
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$output = curl_exec($ch);
		curl_close($ch);
		return json_decode($output, true);
	}
	
	
	public function https_request($url)
	{
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		$data = curl_exec($curl);
		if (curl_errno($curl)) {return 'ERROR '.curl_error($curl);}
		curl_close($curl);
		return $data;
	}
	
	public function utf8_bytes($cp)
	{
		if ($cp > 0x10000){
			# 4 bytes
			return	chr(0xF0 | (($cp & 0x1C0000) >> 18)).
					chr(0x80 | (($cp & 0x3F000) >> 12)).
					chr(0x80 | (($cp & 0xFC0) >> 6)).
					chr(0x80 | ($cp & 0x3F));
		}else if ($cp > 0x800){
			# 3 bytes
			return	chr(0xE0 | (($cp & 0xF000) >> 12)).
					chr(0x80 | (($cp & 0xFC0) >> 6)).
					chr(0x80 | ($cp & 0x3F));
		}else if ($cp > 0x80){
			# 2 bytes
			return	chr(0xC0 | (($cp & 0x7C0) >> 6)).
					chr(0x80 | ($cp & 0x3F));
		}else{
			# 1 byte
			return chr($cp);
		}
	}
	
	public function receiveEvent($object)
    {
        $contentStr = "";
		$openid = "";
        switch ($object->Event)
        {
            case "subscribe":
				date_default_timezone_set("Asia/Shanghai");
				$access_token = $this->accessToken();
				$openid = $object->FromUserName;
				$url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=$access_token&openid=$openid&lang=zh_CN";
				$output = $this->https_request($url);
				$jsoninfo = json_decode($output, true);			
				$data = array();		
				$unionid = $jsoninfo["unionid"];
				require_once "mysql.php";
				$db = new M("ai_sign");
				$userRow = $db->select("unionid = '$unionid'");
				
				if($userRow){
					$data['unionid'] = $jsoninfo["unionid"];
					$data['openid'] = $jsoninfo["openid"];
					$data['nickname'] = $jsoninfo["nickname"];
					$data['sex'] = ($jsoninfo["sex"] == 1)?"男":(($jsoninfo["sex"] == 2)?"女":"未知");
					$data['country'] = $jsoninfo["country"];
					$data['province'] = $jsoninfo["province"];
					$data['city'] = $jsoninfo["city"];
					$data['headimgurl'] = $jsoninfo["headimgurl"];
					$data['subscribe_time'] = date('Y-m-d H:i:s',$jsoninfo["subscribe_time"]);
					$data['is_subscribe'] = 1;
					$db = new M("ai_sign");
					$db->update("unionid = '$unionid'",$data);
					$db->close();
				}else{
					$data['unionid'] = $jsoninfo["unionid"];
					$data['openid'] = $jsoninfo["openid"];
					$data['nickname'] = $jsoninfo["nickname"];
					$data['sex'] = ($jsoninfo["sex"] == 1)?"男":(($jsoninfo["sex"] == 2)?"女":"未知");
					$data['country'] = $jsoninfo["country"];
					$data['province'] = $jsoninfo["province"];
					$data['city'] = $jsoninfo["city"];
					$data['headimgurl'] = $jsoninfo["headimgurl"];
					$data['subscribe_time'] = date('Y-m-d H:i:s',$jsoninfo["subscribe_time"]);					
					$data['is_subscribe'] = 1;
					$db->insert($data);
					$db->close();
				}
				$contentStr = "欢迎关注！请点击--><a href = \"www.968816.com.cn/qzgdwl/index.php?m=qhb&a=qhbIndex \">"."签到"."</a>，签到后才能抢红包哦";		
				$unionid = $jsoninfo["unionid"];
				break;   
            case "unsubscribe":
				date_default_timezone_set("Asia/Shanghai");
				$access_token = $this->accessToken();
				$openid = $object->FromUserName;
				$url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=$access_token&openid=$openid&lang=zh_CN";
				$output = $this->https_request($url);
				$jsoninfo = json_decode($output, true);						
				$data = array();		
				$unionid = $jsoninfo["unionid"];	
				require_once "mysql.php";
				$db = new M("ai_sign");
				$userRow = $db->select("unionid = '$unionid'");
				if($userRow){
					$data['is_subscribe'] = 0;
					$data['un_subscribe_time'] = date('Y-m-d H:i:s',time());
					$data['headimgurl'] = $jsoninfo["headimgurl"];
					$db->update("unionid = '$unionid'",$data);
					$db->close();
				}else{
					$data['unionid'] = $jsoninfo["unionid"];
					$data['openid'] = $jsoninfo["openid"];
					$data['nickname'] = $jsoninfo["nickname"];
					$data['sex'] = ($jsoninfo["sex"] == 1)?"男":(($jsoninfo["sex"] == 2)?"女":"未知");
					$data['country'] = $jsoninfo["country"];
					$data['province'] = $jsoninfo["province"];
					$data['city'] = $jsoninfo["city"];
					$data['headimgurl'] = $jsoninfo["headimgurl"];
					$data['un_subscribe_time'] = date('Y-m-d H:i:s',time());
					$data['is_subscribe'] = 0;
					$db->add($data);
					$db->close();
				}
                break;
            /*case "CLICK":
                switch ($object->EventKey)
                {
                    
					
					case "YWY":
                        $unionid = "123";//$this->getUnionIdByWeinxin($object);
						$ywy = M('Ywy');				
						$data = array();			
						$unionidArr = array();
						$unionidArr['unionid'] = $unionid;
						$ywyRow = $ywy->where($unionidArr)->select();
						if($userRow){
							$this->display(C('HOME_DEFAULT_THEME').':ywyIndex');					
							
						}else{
							$this->display(C('HOME_DEFAULT_THEME').':ywyLogin');
						}		
                        break;							
                    default:
                        $contentStr[] = array("Title" =>"默认菜单回复", 
                        "Description" =>"测试", 
                        "PicUrl" =>"http://discuz.comli.com/weixin/weather/icon/cartoon.jpg", 
                        "Url" =>"weixin://addfriend/pondbaystudio");
                        break;
                }
			break;*/
			case "scancode_waitmsg":
				date_default_timezone_set("Asia/Shanghai");
				if($object->ScanCodeInfo->ScanResult == "业务开发中心"){
					$access_token = $this->accessToken();
					$openid = $object->FromUserName;
					$url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=$access_token&openid=$openid&lang=zh_CN";
					//$output = $this->https_request($url);
					//$jsoninfo = json_decode($output, true);
					$output = $this->https_request($url);
					$jsoninfo = json_decode($output, true);				
					$data = array();	
					$data['unionid'] = $jsoninfo["unionid"];
					$data['openid'] = $jsoninfo["openid"];
					$data['nickname'] = $jsoninfo["nickname"];
					$data['sex'] = ($jsoninfo["sex"] == 1)?"男":(($jsoninfo["sex"] == 2)?"女":"未知");
					$data['country'] = $jsoninfo["country"];
					$data['province'] = $jsoninfo["province"];
					$data['city'] = $jsoninfo["city"];
					$data['sign_time'] =date("Y-m-d H:i:s");
					
					require_once "mysql.php";
					$db = new M("ai_sign");
					$db->insert($data);
					$db->close();
					$contentStr = $jsoninfo["nickname"]."  ".$data['sign_time']."互动平台巡检记录上传成功！";
				}else{
					$contentStr = "无效二维码，请完成巡检后，扫描办公室电视旁边的二维码";
				}
			break;
            default:
                break;      

        }
        if (is_array($contentStr)){
            $resultStr = $this->transmitNews($object, $contentStr);
        }else{
            $resultStr = $this->transmitText($object, $contentStr);
        }
        return $resultStr;
    }
}
?>