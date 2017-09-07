<?php
class LoginAction extends Action{
	private $unionid;
	private $openid;
	private $state;
	protected function _initialize() {
		error_reporting(E_ALL^E_NOTICE);
		$code = $_GET["code"];
		$this->state = $_GET["state"];
		$this->getUserInfo($code);
	}
	
	private function getUserInfo($code){
		$appid = "wx3a20e613be177269";
		$appsecret = "b722fba7bd164688bcfa5b4acb2f1bee";

		//oauth2的方式获得openid
		$access_token_url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=$appid&secret=$appsecret&code=$code&grant_type=authorization_code";
		$access_token_json = $this->https_request($access_token_url);
		$access_token_array = json_decode($access_token_json, true);
		$this->openid = $access_token_array['openid'];
		
		$access_token = $this->getAccessToken();
		//echo $access_token;
		//全局access token获得用户基本信息
		$userinfo_url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=$access_token&openid=".$this->openid;
		$userinfo_json = $this->https_request($userinfo_url);
		$userinfo_array = json_decode($userinfo_json, true);
		$this->unionid =  $userinfo_array['unionid'];
	}
	
	private function https_request($url){
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
	
	private function getAccessToken() {
		$db = M("Access_token_wxkfpt");
		$data = array();
		$atRow = $db->select();
		$access_token = '';
		if($atRow[0]['expire_time'] < time()){		
			$appid = "wx3a20e613be177269";
			$appsecret = "b722fba7bd164688bcfa5b4acb2f1bee";
			$url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$appid&secret=$appsecret";		
			$res = $this->getJson($url);
			$access_token = $res['access_token'];
			$data['expire_time'] = time() + 7000;
			$data['access_token'] = $access_token;
			$db->where("1=1")->save($data);	
		}else{			
			$access_token = $atRow[0]['access_token'];
		}
		return $access_token;
	}
  
	private function getJson($url){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); 
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE); 
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$output = curl_exec($ch);
		curl_close($ch);
		return json_decode($output, true);
	}
	private function get_php_file($filename) {
		return trim(substr(file_get_contents($filename), 15));
	}
	
	private function set_php_file($filename, $content) {
		$fp = fopen($filename, "w");
		fwrite($fp, $content);
		fclose($fp);
	}
	
	private function httpGet($url) {
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_TIMEOUT, 500);
		// 为保证第三方服务器与微信服务器之间数据传输的安全性，所有微信接口采用https方式调用，必须使用下面2行代码打开ssl安全校验。
		// 如果在部署过程中代码在此处验证失败，请到 http://curl.haxx.se/ca/cacert.pem 下载新的证书判别文件。
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($curl, CURLOPT_URL, $url);

		$res = curl_exec($curl);
		curl_close($curl);

		return $res;
	}

	public function index(){
		
		$_SESSION['unionid'] = $this->unionid;
		$_SESSION['openid'] = $this->openid;
		$_SESSION['state'] = $this->state;
		
		if(!$_SESSION['unionid'] || !$_SESSION['openid'] || !$_SESSION['state']){
			$url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx3a20e613be177269&redirect_uri=http%3a%2f%2fwx.968816.com.cn%2fqzgdwl%2findex.php%3fm%3dlogin%26a%3dindex&response_type=code&scope=snsapi_base&state=qzgdwl%26qzgdwlIndex#wechat_redirect";
			header("Location: $url");
		}else{
			//echo  $this->state;
			$arrState = explode("&",$this->state);
			//print_r($arrState);
			if(count($arrState) == 3 && $arrState[2]){
				$url = "index.php?m=".$arrState[0]."&a=".$arrState[1]."&tv=".$arrState[2];
			}else{
				$url = "index.php?m=".$arrState[0]."&a=".$arrState[1];
			}
			header("Location: $url");
		}
		
	}
}
?>