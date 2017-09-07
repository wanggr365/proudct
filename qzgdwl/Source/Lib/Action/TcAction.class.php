﻿<?php
class TcAction extends Action {
    protected function _initialize() {
		$model = M("Setting");
		$condition['item'] = "0";
		$setting = $model->where($condition)->getField('item_key,item_value');
		$this->assign(C('AIMEE_PREFIX'),$setting);
		
		$useragent = addslashes($_SERVER['HTTP_USER_AGENT']);
		if(strpos($useragent, 'MicroMessenger') == false && strpos($useragent, 'Windows Phone') == false ){
			header("location:http://wx.968816.com.cn/error.html");
		}		
	}
	
    public function index(){
		Log::write('文件名testsete：' . $tplName);
    	$this->display(C('HOME_DEFAULT_THEME').':index');
    }
	
	function isAcess($unionid){
		$unionidArr = array();
		$unionidArr['unionid'] = $unionid;
	}
    
	//吐槽首页
	function tcShare(){
		$this->display(C('HOME_DEFAULT_THEME').':tcShare');
	}
	
    function tcIndex(){	
		//$this->tcSave();
		$unionid = $_GET['unionid'];
		$unionidArr = array();
		$unionidArr['unionid'] = $unionid;
		$this->assign("unionid",$unionid);
		
		$userInfor = M('Userinfor');
		$user = M('User');
		$userRow = $user->where($unionidArr)->select();
		/*if($userRow){
		}else if($unionid){
			$unionidArr['openid'] = $_GET['openid'];
			$unionidArr['is_binding'] = 0;
			$unionidArr['subscribe_time'] = date('Y-m-d H:i:s',time());
			$user->data($unionidArr)->add();
		}
		$userRow = $user->where($unionidArr)->select();		
		$this->assign("userRow",$userRow[0]);
		
		$userInforRow = $userInfor->where($unionidArr)->select();
		if(!$userInforRow){
			$result = array();
			$data['login_date'] = date('Y-m-d H:i:s',time());
			$data['unionid'] = $unionid;
			$data['is_submit'] = 0;
			$data['is_share'] = 0;
			$data['is_prize'] = 0;
			$data['draw_num'] = 0;
			$data['is_binding'] = $userRow[0]['is_binding'];
			$userInfor->data($data)->add();
			$userInforRow = $userInfor->where($unionidArr)->select();
		}*/
		$this->assign("userInforRow",$userInforRow[0]);
		require_once "jssdk.php";
		$jssdk = new JSSDK("wx3a20e613be177269", "b722fba7bd164688bcfa5b4acb2f1bee");
		$signPackage = $jssdk->GetSignPackage();
		$this->assign("signPackage",$signPackage);
		
		$tc = M('Tc');
		$unionidArr = array();
		$unionidArr['unionid'] = $unionid;	
		$unionidArr['create_date'] = array('like',date('Y-m-d').'%');
		$tcRow = $tc->where($unionidArr)->count();	
		$this->assign("tcRow",$tcRow);
		
		$share = M('Share');
		$unionidArr = array();
		$unionidArr['unionid'] = $unionid;	
		$unionidArr['create_date'] = array('like',date('Y-m-d').'%');
		$shareRow = $share->where($unionidArr)->count();
		$this->assign("shareRow",$shareRow);
		
		$this->display(C('HOME_DEFAULT_THEME').':tcIndex');
	}
		
	//吐槽提交
	function tcSave(){
		$result = array();		
		$tc = M('Tc');
		$data = array();
		$unionid = $_POST['unionid'];
		$tc_pic = $_POST['tc_pic'];
		$unionidArr = array();
		$unionidArr['unionid'] = $unionid;	
		$unionidArr['create_date'] = array('like',date('Y-m-d').'%');
		$tcRow = $tc->where($unionidArr)->select();		
		//print_r($unionidArr);
		if($tcRow){
			$result['code'] = 0;
		}else{
			$unionidArr = array();
			$unionidArr['unionid'] = $unionid;
			$unionidArr['content'] = $_POST['content'];
			$unionidArr['tc_pic'] = $_POST['tc_pic'];
			$unionidArr['create_date'] = date('Y-m-d H:i:s',time());
			$tc->data($unionidArr)->add();
			
			$userInfor = M('Userinfor');
			$userInforRow = $userInfor->where(array('unionid'=>$unionid))->select();
			$userInfor->where(array('unionid'=>$unionid))->save(array('draw_num'=>intval($userInforRow[0]['draw_num']) + 2));
			$result['code'] = 1;
			$result['draw_num'] = intval($userInforRow[0]['draw_num']) + 2;
		}
		echo $this->json($result);	
	}
	
	//吐槽分享
	function qzgdwlShare(){
		$unionid = $_POST['unionid'];
		$unionidArr = array();
		$unionidArr['unionid'] = $unionid;
		$unionidArr['create_date'] = array('like',date('Y-m-d').'%');
		$result = array();		
		$share = M('Share');
		$shareRow = $share->where($unionidArr)->select();
		
		
		/*$tc = M('Tc');		
		$tcRow = $tc->where($unionidArr)->select();	
		if($tcRow){
			$result['tc'] = 1;
		}else{			
			$result['tc'] = 0;
		}*/
		if($shareRow){
			$result['code'] = 0;
			$userInfor = M('Userinfor');
			$userInforRow = $userInfor->where(array('unionid'=>$unionid))->select();			
			$result['draw_num'] = $userInforRow[0]['draw_num'];
		}else{
			$unionidArr = array();
			$unionidArr['unionid'] = $unionid;
			$unionidArr['create_date'] = date('Y-m-d H:i:s',time());
			$share->data($unionidArr)->add();
			
			$userInfor = M('Userinfor');
			$userInforRow = $userInfor->where(array('unionid'=>$unionid))->select();
			$userInfor->where(array('unionid'=>$unionid))->save(array('draw_num'=>intval($userInforRow[0]['draw_num']) + 2));
			$result['code'] = 1;
			$result['draw_num'] = intval($userInforRow[0]['draw_num']) + 2;
		}
		echo $this->json($result);		
	}
	
	//吐槽随机抽奖
	public function run(){
		sleep(1.5);
		$arrPirze = array();
		$prize=M('Cltprize');
		$prizeRow = $prize->order('id asc')->select();
		
		
		$userInfor = M('Userinfor');
		$unionid = $_POST['unionid'];
		if(!$unionid){			
			$unionid = $_GET['unionid'];
		}
		$unionidArr = array();
		$unionidArr['unionid'] = $unionid;		
		$unionidArr['is_prize'] = 1;
		$userInforRow = $userInfor->where($unionidArr)->select();	
		
		/*$rowp7=$mp->where(array('openid'=>$openid,'ispraise'=>1,'prizeid'=>8))->select();
		$rowp8=$mp->where(array('openid'=>$openid,'ispraise'=>1,'prizeid'=>9))->select();
		$rowp9=$mp->where(array('openid'=>$openid,'ispraise'=>1,'prizeid'=>10))->select();*/
		if($userInforRow){
			foreach($prizeRow as $key=>$val){
				if($val['ispraise'] == 1){
					//$val['chance'] = 0;
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
			$userInfor = M('Userinfor');
			$userInforRow = $userInfor->where($unionidArr)->select();
			if($userInforRow){
				if($userInforRow[0]['draw_num'] == 0){
					$praiseName = null;
					$numtimes = 0;
					$angle = 0;
				}else{
					$mp=M('Cltuserpraise');//保存奖品
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
					//$result['num']=$num;
					if($res['ispraise'] == 1){
						$userInfor->where(array('unionid'=>$unionid))->save(array('is_prize'=>1));
						
						M()->startTrans();//开启事务
						$cardArr['accountno'] = '';//查询条件
						$cardArr['card_value'] = intval($res['value']);//查询条件
						$card = M('Card');
						$idRow = $card->lock(true)->where($cardArr)->min('id');//加锁查询
						if($idRow){
							//执行你想进行的操作, 最后返回操作结果 result
							$user = M('User');
							$userRow = $user->where(array('unionid'=>$unionid))->select();
							$cardRow = $card->where(array('id'=>$idRow))->save(array('cust_code'=>$userRow[0]['cust_code'],'cust_name'=>$userRow[0]['cust_name'],'accountno'=>$userRow[0]['accountno']));
							if(!$cardRow){
								M()->rollback();//回滚
								//$this->error('错误提示');
							}
						}
						M()->commit();//事务提交
						//$this->success('成功提示');
					}
					
					//用户抽取的那个奖项减1
					
					$prize=M('Cltprize');
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
		$userInfor = M('Userinfor');
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
		$result = array();
		$userInfor = M('Userinfor');
		$userInforRow = $userInfor->where(array('phone'=>$phone))->select();
		if($userInforRow){
			$result['code']=0;
			echo $this->json($result);
		}
		else{
			$userInfor->where(array('unionid'=>$unionid))->save(array('phone'=>$phone,'username'=>$username));
			$result['code']=1;			
			echo $this->json($result);
		}
    }
	
	private function getPraiseAndJf($unionid){
		$cltuserpraise = M('Cltuserpraise');
		$data = array(array('unionid'=>$unionid));				
		$cltuserpraiseRow = $cltuserpraise->where($data)->select();
		$numJf = 0;//积分
		$ydj='';//奖品
		$praiseAndJf = array();
		if($cltuserpraiseRow){
			foreach($cltuserpraiseRow as $key=>$val){
				if($val['ispraise'] == 1 && $val['isexchange'] != 1){
					$ydj = $ydj.$val['praisename'].'；';
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
		//$this->tcSave();
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
		$jssdk = new JSSDK("wx3a20e613be177269", "b722fba7bd164688bcfa5b4acb2f1bee");
		$signPackage = $jssdk->GetSignPackage();
		$this->assign("signPackage",$signPackage);
		
		
		$this->display(C('HOME_DEFAULT_THEME').':qgsIndex');
	}
	
	function sjIndex(){	
		//$this->tcSave();
		$code = $_GET['code'];
		require_once "jssdk.php";
		$jssdk = new JSSDK("wx3a20e613be177269", "b722fba7bd164688bcfa5b4acb2f1bee");
		$signPackage = $jssdk->GetSignPackage();
		$this->assign("signPackage",$signPackage);
		//echo $code.'<br>';
		echo "<h1>请识别下方二维码，关注 泉州广电网络 公众号就可以领奖哦</h1>";
		echo "<img src=\"qzgdwl.png\" />";
		//$this->display(C('HOME_DEFAULT_THEME').':sjIndex');
	}
	
}

?>