﻿<?php
class ZpfansAction extends Action {
    protected function _initialize() {
		$this->isWx();
		
		$useragent = addslashes($_SERVER['HTTP_USER_AGENT']);
		if(strpos($useragent, 'MicroMessenger') == false && strpos($useragent, 'Windows Phone') == false ){
			header("location:http://wx.968816.com.cn/error.html");
		}		
	}
	
	
	private function isWx(){		
		if(!$_SESSION['unionid'] || !$_SESSION['openid'] || !$_SESSION['state']){
				$url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx3a20e613be177269&redirect_uri=http%3a%2f%2fwx.968816.com.cn%2fqzgdwl%2findex.php%3fm%3dlogin%26a%3dindex&response_type=code&scope=snsapi_base&state=zpfans%26zpfansIndex#wechat_redirect";			
			header("Location: $url");
		}
	}	
	
	public function countNum(){
		
		echo 2000-intval(M('Zpfans_yhj_cltprize')->select()[0]['praisenumber']);
	} 
	
    public function index(){
		Log::write('文件名testsete：' . $tplName);
    	$this->display(C('HOME_DEFAULT_THEME').':index');
    }
	
	function isAcess($unionid){
		$unionid = $_SESSION['unionid'];  //获取QhbIndex 里的UnionID
		$unionidArr = array();
		$unionidArr['unionid'] = $unionid;
	}
    
	//吐槽首页
	function zpShare(){
		$this->display(C('HOME_DEFAULT_THEME').':zpShare');
	}
	
	public function addTimes(){ //无绑定到绑定，增加游戏次数
		$unionid = $_SESSION['unionid'];
		$zpfansUserInfor = M('Zpfans_userinfor');
		$zpfansUserInforRow = $zpfansUserInfor->where(array('unionid'=>$unionid))->select(); //获取当前unionid用户的信息
		
		//print_r($zpfansUserInforRow);
		
		if($zpfansUserInforRow[0]['is_binding'] == 0 && $this->isBinding()==1) {
			//echo PHP_EOL.$zpfansUserInforRow[0]['is_binding'];
			//user中新绑定，第一次进入时qhbUserRow时未绑定
			$zpfansUserInforRow = $zpfansUserInfor->where("unionid = '$unionid'")->setInc('draw_num',2);		
			$zpfansUserInforRow = $zpfansUserInfor->where("unionid = '$unionid'")->save(array('is_binding'=>1));
		
		}
	}
	
	public function isBinding(){ //只在用户第一次进入系统的时候才使用
		$unionid = $_SESSION['unionid'];
		$unionidArr = array();
		$unionidArr['unionid'] = $unionid;
		$user = M('User');
		$userRow = $user->where($unionidArr)->select();
		if($userRow){
		}else if($unionid){
			$unionidArr['unionid'] = $unionid;
			$unionidArr['is_binding'] = 0;
			$unionidArr['subscribe_time'] = date('Y-m-d H:i:s',time());
			$user->data($unionidArr)->add();
		}
		$userRow = $user->where(array('unionid'=>$unionid))->select();	
		$this->assign("is_subscribe",$userRow[0]['is_subscribe']);	
		return 	$userRow[0]['is_binding'];	
	}
		
    function zpfansIndex(){		
//echo 	123;
		$unionid = $_SESSION['unionid'];
		$openid = $_SESSION['openid'];
		
		$unionidArr = array();
		$unionidArr['unionid'] = $unionid;
		
		$zpfansUserInfor = M('Zpfans_userinfor');
		$zpfansUserInforRow = $zpfansUserInfor->where($unionidArr)->select(); //获取当前unionid用户的信息
				
		//print_r($qhbUserRow);
		if($zpfansUserInforRow){ //用户的UnionID已经存到抢红包的表中  需判断是否绑定，若未绑定，但又重新绑定后需重新增加用户次数
			//判断user表中的unionID是否重新绑定  根据日期重新确定用户次数
			$this->addTimes();
		}else{
			$unionidArr['openid'] = $openid;
			$unionidArr['unionid'] = $unionid;
			$unionidArr['score'] = 0;
			$unionidArr['is_binding'] = $this->isBinding() == 1?1:0;
			$unionidArr['draw_num'] = $this->isBinding() == 1?3:1;
			$unionidArr['is_submit'] = 0;
			$unionidArr['is_share'] = 0;
			$unionidArr['is_prize'] = 0;
			$unionidArr['create_date'] = date('Y-m-d H:i:s',time()); //存储第一次登陆时间		
			$zpfansUserInfor->data($unionidArr)->add();	
		}	
		
		$zpfansUserInforRow = $zpfansUserInfor->where($unionidArr)->select(); //获取当前unionid用户的信息
		$this->assign("is_binding",$this->isBinding());				
		$this->assign("zpfansUserInforRow",$zpfansUserInforRow[0]);		
		$this->display(C('HOME_DEFAULT_THEME').':zpfansIndex');
	}	
	
	
    function zpfansTurnplate(){	
		$unionid = $_SESSION['unionid'];  //获取QhbIndex 里的UnionID
		$unionidArr = array();
		$unionidArr['unionid'] = $unionid;
		$this->assign("unionid",$unionid);
		
		$zpfansUserInfor = M('Zpfans_userinfor');
		$zpfansUserInforRow = $zpfansUserInfor->where($unionidArr)->select(); //获取当前unionid用户的信息
		$this->assign("is_binding",$this->isBinding());				
		$this->assign("zpfansUserInforRow",$zpfansUserInforRow[0]);		
			
		$getPraiseAndJf = $this->getPraiseAndJf($unionid);				
		$praiseName = $getPraiseAndJf[0];
		$this->assign("praiseName",$praiseName);
		$this->display(C('HOME_DEFAULT_THEME').':zpfansTurnplate');
	}	
	
	
	function zpfansRecord(){	
		$unionid = $_SESSION['unionid'];
		$openid = $_SESSION['openid'];
		$this->assign("unionid",$unionid);
		
		$rank = M('Zpfans_cltuserpraise');
		$zpfansCltuserpraiseRow = $rank->where("unionid = '$unionid' and prizeid <> 4")->select(); //获取当前unionid用户的信息
		
		//$this->assign("cltuserpraiseRowArray",$cltuserpraiseRow[0]);
		
		$this->assign("zpfansCltuserpraiseRow",$zpfansCltuserpraiseRow);
	
		$this->display(C('HOME_DEFAULT_THEME').':zpfansRecord');
	}		
		
	//吐槽随机抽奖
	public function run(){
		//sleep(1.5);
		$arrPirze = array();
		$prize=M('Zpfans_cltprize');
		$prizeRow = $prize->order('id asc')->select();
		
		
		$userInfor = M('Zpfans_userinfor');
		$unionid = $_POST['unionid'];
		if(!$unionid){			
			$unionid = $_GET['unionid'];
		}
		$unionidArr = array();
		$unionidArr['unionid'] = $unionid;	
		
		// $user = M('User');
		// $userRow = $user->where($unionidArr)->select();
		// if($userRow[0]['cust_code'] == '850600034142'){
			// foreach($prizeRow as $key=>$val ){
				// if($val['ispraise'] == 1 ){
					// $val['chance'] = 0;
				// }			
				
				// $arrPirze[$key]=$val;
			// }
		// }
		$unionidArr['is_prize'] = 1;
		$userInforRow = $userInfor->where($unionidArr)->select();	
		//|| $userInforRow[0][cust_code] == '850600034142'
		/*$rowp7=$mp->where(array('openid'=>$openid,'ispraise'=>1,'prizeid'=>8))->select();
		$rowp8=$mp->where(array('openid'=>$openid,'ispraise'=>1,'prizeid'=>9))->select();
		$rowp9=$mp->where(array('openid'=>$openid,'ispraise'=>1,'prizeid'=>10))->select();*/
		if($userInforRow){
			foreach($prizeRow as $key=>$val ){
				if($val['ispraise'] == 1 ){
					$val['chance'] = 0;
				}			
				
				$arrPirze[$key]=$val;
			}
		}else{
			foreach($prizeRow as $key=>$val){
				$arrPirze[$key]=$val;
			}
		}
		//print_r($arrPirze);
		//echo $this->json($arrPirze);
		echo $this->getResult($arrPirze,$unionid);
    }
	
	private function getResult($arrPirze,$unionid){
		//echo $unionid;
		$arr = array();
		$count = array();
		$praiseName = null; //奖品
		$numtimes = 0; //还剩次数
		$angle = 0;		
		$praiseNumber = 0;//所有奖项		
		$unionidArr = array();
		$unionidArr['unionid'] = $unionid;		
		$praise = 0;
		
		foreach ($arrPirze as $key => $val) {
    		$arr[$val['id']] = $val['chance'];
    		$count[$val['id']] = $val['praisenumber'];
			$praiseNumber = $praiseNumber + intval($val['praisenumber']);
		}
		$rid = $this->getRand($arr,$count); //根据概率获取奖项id
		$res = $arrPirze[$rid-1]; //中奖项
						
		//echo $praiseNumber;
		if($praiseNumber == 0) {
			$result['praisename'] = '抱歉，今天奖品已全部抽出，请明天再来尝试';
			$result['num']=0;
			$result['angle'] = 0;
			$result['show'] = $this->checkPhoneAndAddr($unionid);
			$getPraiseAndJf = $this->getPraiseAndJf($unionid);				
			$result['jiangpin1'] = $getPraiseAndJf[0];
			//$result['numraise1'] = $numPraise1;
			$result['numjf'] = $getPraiseAndJf[1];
			return $this->json($result);
		}else{
			$userInfor = M('Zpfans_userinfor');
			$userInforRow = $userInfor->where($unionidArr)->select();
			if($userInforRow){
				if($userInforRow[0]['draw_num'] == 0){
					$praiseName = null;
					$numtimes = 0;
					$angle = 0;
					$praise = 4;
				}else{
					$mp=M('Zpfans_cltuserpraise');//保存奖品
					$praise = $res['id'];
					$ranJf = $res['praisename'];
					
					$praiseName = $ranJf;
					if($res['isjf'] == 1){
						$ranJf = strval(mt_rand($res['min'], $res['max']));
						$praiseName = $ranJf.$praiseName;
					}
					$mp->add(array('unionid'=>$unionid,'prizeid'=>$praise,'praisename'=>$ranJf,'date'=>date("Y-m-d H:i:s"),'isjf'=>$res['isjf'],'ispraise'=>$res['ispraise'],'isexchange'=>0));
					$angle = 1;
					
					$numtimes = $userInforRow[0]['draw_num'] - 1;
					$userInfor->where($unionidArr)->save(array('draw_num'=>$numtimes));
					
					if($res['ispraise'] == 1){  //最多只中奖一次，上线时候需要关闭注释
						$userInfor->where(array('unionid'=>$unionid))->save(array('is_prize'=>1));
					}
					
					if($res['ischarge'] == 1){
						M()->startTrans();//开启事务
						$cardArr['unionid']=array('EXP','IS NULL');
						$cardArr['card_value'] = intval($res['value']);//查询条件
						$cardArr['card_remark'] = '三十万粉丝感恩回馈';//查询条件
						$card = M('Card');
						$idRow = $card->lock(true)->where($cardArr)->min('id');//加锁查询
						if($idRow){
							//执行你想进行的操作, 最后返回操作结果 result
							$user = M('User');
							$userRow = $user->where(array('unionid'=>$unionid))->select();
							$cardRow = $card->where(array('id'=>$idRow))->save(array('unionid'=>$unionid,'cust_code'=>$userRow[0]['cust_code'],'cust_name'=>$userRow[0]['cust_name'],'accountno'=>$userRow[0]['accountno']));
							if(!$cardRow){
								M()->rollback();//回滚
								//$this->error('错误提示');
							}
						}
						M()->commit();//事务提交
						//$this->success('成功提示');
					}
					
					//用户抽取的那个奖项减1
					
					$prize=M('Zpfans_cltprize');
					$prizeRow = $prize->where(array('id'=>$praise))->select();
					if($prizeRow[0]['praisenumber'] == -1){
						$numstore = -1;
					}else if($prizeRow[0]['praisenumber'] == 0){
						$numstore = 0;
					}else{
						$numstore = $prizeRow[0]['praisenumber']-1;
					}
					$prize->where(array('id'=>$praise))->save(array('praisenumber'=>$numstore));	
					
				}
				$result['praisename'] = $praiseName;
				$result['praise'] = $praise;
				$result['num']=$numtimes;
				$result['angle'] = $angle;
				$result['show'] = $this->checkPhoneAndAddr($unionid);
				$getPraiseAndJf = $this->getPraiseAndJf($unionid);					
				$result['jiangpin1'] = $getPraiseAndJf[0];
				$result['numjf'] = $getPraiseAndJf[1];
				
				echo $this->json($result);
				//echo print_r($res);
			}
		}
		
		
    }
	
    private function getRand($proArr,$proCount){
    	$result = '';
    	$proSum=0;
    	//概率数组的总概率精度  获取库存不为0的
    	foreach($proCount as $key=>$val){
    		if($val==0){
    			continue;
    		}else{
    			$proSum=$proSum+$proArr[$key];
    		}
    	}
    	//概率数组循环 �
    	foreach ($proArr as $key => $proCur) {
    		if($proCount[$key]==0){
    			continue;
    		}else{
    			$randNum = mt_rand(1, $proSum);//关键
        		if ($randNum <= $proCur) {
        			$result = $key;
           	 		break;
        		}else{
            		$proSum -= $proCur;
        		}
    		}

    	}
    	unset ($proArr);
    	return $result;
    }
	
	public function checkPhoneAndAddr($unionid){
		//
		//$openid= 'odrEdt4BSKcs-VvOXocU3joswWDU';
		$userInfor = M('Zpfans_userinfor');
		$userInforRow = $userInfor->where(array('unionid'=>$unionid))->select();
		if(!trim($userInforRow[0]['phone'])||!trim($userInforRow[0]['username'])){
			//return 0;
			return 1;
		}else{
			return 1;
		}
    }
	
	public function inforSubmit(){
		$unionid = $_POST['unionid'];		
		$phone = trim($_POST['phone']);
		$username = $_POST['username'];
		$address = $_POST['address'];
		$result = array();
		$userInfor = M('Zpfans_userinfor');
		$userInforRow = $userInfor->where(array('phone'=>$phone))->select();
		if($userInforRow){
			$result['code']=0;
			echo $this->json($result);
		}
		else{
			$userInfor->where(array('unionid'=>$unionid))->save(array('phone'=>$phone,'username'=>$username,'address'=>$address));
			$result['code']=1;			
			echo $this->json($result);
		}
    }
	
	private function getPraiseAndJf($unionid){
		$cltuserpraise = M('Zpfans_cltuserpraise');
		$data = array('unionid'=>$unionid);				
		$cltuserpraiseRow = $cltuserpraise->where($data)->select();
		$numJf = 0;//积分
		$ydj='';//奖品
		$praiseAndJf = array();
		if($cltuserpraiseRow){
			foreach($cltuserpraiseRow as $key=>$val){
				if($val['prizeid'] != 4 && $val['isexchange'] != 1){
					if(mb_strpos($val['praisename'],'充值卡')){
						$val['praisename'] = "<a  style='text-decoration:underline;' href='index.php?m=qzgdwl&a=qzgdwlTicketList&unionid=$unionid'>".$val['praisename']."</a>";
					}
					$ydj = $ydj.$val['praisename'].'<br>';
				}elseif($val['isjf'] == 1){
					$numJf = $numJf + intval($val['praisename']);
				}						
			}
		}
		$praiseAndJf[0]=$ydj;
		$praiseAndJf[1]=$numJf;
		return $praiseAndJf;
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
	
	function qgsIndex(){	
		//$this->zpSave();
		$voteno = $_GET['voteno'];
		$type = $_GET['type'];
		$card_no = $_GET['card_no'];
		$unionidArr = array();
		$unionidArr['voteno'] = $voteno;	
		if($type == 1){
			$condidate = M('Condidate');
		}else{
			$condidate = M('Condidate_tel');
		}
		$condidateRow = $condidate->where($unionidArr)->select();
			
		$this->assign("condidateRow",$condidateRow[0]);
		$this->assign("type",$type);
		$this->assign("voteno",$voteno);
		require_once "jssdk.php";
		$jssdk = new JSSDK("wx3a20e613be177269", "adb07a65fdaf8b6d572713aa2d764d71");
		$signPackage = $jssdk->GetSignPackage();
		$this->assign("signPackage",$signPackage);
		
		
		$this->display(C('HOME_DEFAULT_THEME').':qgsIndex');
	}
	
	
	
	
	
	
	
	
	
	
	

	
}

?>