<?php
class YytAction extends Action {
    protected function _initialize() {
		if(!$_SERVER['version']){
			$_SERVER['version'] =$this->getVersion();			
		}
		$this->assign("version",$_SERVER['version']);
		
		$this->isWx();
		$useragent = addslashes($_SERVER['HTTP_USER_AGENT']);
		if(strpos($useragent, 'MicroMessenger') == false && strpos($useragent, 'Windows Phone') == false ){
			//header("location:http://www.968816.com.cn/error.html");
		}	
	}
	
	private function isWx(){		
		if(!$_SESSION['unionid'] || !$_SESSION['openid'] || !$_SESSION['state']){
			$url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx3a20e613be177269&redirect_uri=http%3a%2f%2fwx.968816.com.cn%2fqzgdwl%2findex.php%3fm%3dlogin%26a%3dindex&response_type=code&scope=snsapi_base&state=qzgdwl%26qzgdwlIndex#wechat_redirect";
			header("Location: $url");
		}
	}
		
	public function sjSaveLog($logStr){
		$log = M('Log_sj');
		$data = array();
		$data['unionid'] = $_SESSION['unionid'];
		$data['create_date'] = date('Y-m-d H:i:s',time());
		$data['log'] = $logStr;
		$log->data($data)->add();
		
	}
		
	public function get_device_type(){
		 //全部变成小写字母
		$agent = strtolower($_SERVER['HTTP_USER_AGENT']);
		$type = 'other';
		if(strpos($agent, 'iphone') || strpos($agent, 'ipad')){
			$type = 'ios';
		} 
		  
		if(strpos($agent, 'android')){
			$type = 'android';
		}
		return $type;
	}
	
    function yytIndex(){			
		$yyt = M('Yyt'); // 实例化User对象
		$pageNo = 1;
		$limit = 10;		
		$this->assign("type",$this->get_device_type());
		$yytRow = $yyt->where("longlai <> '' ")->order('id')->page($pageNo.",$limit")->select();
		$this->assign('yytRow',$yytRow);// 赋值数据集
		$count = $yyt->where("longlai <> '' ")->count();// 查询满足要求的总记录数
		$this->assign('pageNo',$pageNo);// 赋值数据集
		$this->assign('pageTotal',$count/$limit+1);// 赋值数据集
		$this->display(C('HOME_DEFAULT_THEME').':yytIndex');
	}	
	
	
    function yytMore(){
		$yyt = M('Yyt'); // 实例化User对象
		$pageNo = $_POST['pageNo'] + 1;
		$corp = $_POST['corp'];
		$name = $_POST['name'];
		$condition = "longlai <> '' ";
		if($corp == '选择地区') {			
			if($name){
				$condition = $condition."and name like '%$name%' ";
			}
		}else{
			$condition = $condition."and corp = '$corp' ";
			if($name){
				$condition = $condition."and name like '%$name%' ";
			}
		}
		
		$limit = 10;		
		$yytRow = $yyt->where($condition)->order('id')->page($pageNo.",$limit")->select();
		
		$result = array();
		if($yytRow){
			$i = 0;
			foreach($yytRow as $yr){
				$result["$i"]['name'] = $yr['name'];	
				$result["$i"]['address'] = $yr['address'];	
				$result["$i"]['phone'] = $yr['phone'];	
				$result["$i"]['id'] = $yr['id'];	
				$result["$i"]['longlai'] = $yr['longlai'];			
				$i++;			
			}
			if($i == 0){
				$result = array();
			}
			echo $this->json($result);
		}else{
			echo $this->json($result);
		}
	}	
	
	public function yytDetail(){
		$id = $_GET['id'];
		$yyt = M('Yyt'); 
		$yytRow = $yyt->where("id = $id")->select();
		$this->assign('yytRow',$yytRow[0]);
		$this->display(C('HOME_DEFAULT_THEME').':yytDetail');
	}
	
	
	public function yytMap(){
		$this->display(C('HOME_DEFAULT_THEME').':yytMap');
	}
		
	private function json($array){
    	$this->arrayRecursive($array, 'urlencode', true);
		$json = json_encode($array);
		return urldecode($json);
    }
	
	//对数组中所有元素做处理
	private function arrayRecursive(&$array, $function, $apply_to_keys_also = false){
		foreach ($array as $key => $value) {
			if (is_array($value)) {
				$this->arrayRecursive($array[$key], $function, $apply_to_keys_also);
			}else{
				$array[$key] = $function($value);
			}
			if ($apply_to_keys_also && is_string($key)){
				$new_key = $function($key);
				if ($new_key != $key){
					$array[$new_key] = $array[$key];
					unset($array[$key]);
				}
			}
		}
	}
	public function getVersion(){
		$file = "version.txt";
		$data = json_decode(file_get_contents($file));
		return $data->version;          	 	
		if (!$data->version) {
			 return $data->version;
		}else {
		 return '10';
		}
		
	}
	
	
	
}

?>