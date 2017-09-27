<?php
class QzgdwlAction extends Action {
    protected function _initialize() {
		$useragent = addslashes($_SERVER['HTTP_USER_AGENT']);	
		if(!$_SERVER['version']){
			$_SERVER['version'] =$this->getVersion();			
		}
		$this->assign("version",$_SERVER['version']);
		
		 $this->isWx();
		 if(strpos($useragent, 'MicroMessenger') == false && strpos($useragent, 'Windows Phone') == false ){
			 header("location:http://www.968816.com.cn/error.html");
		 }		
	}
	
    public function index(){
		Log::write('文件名testsete：' . $tplName);
    	//$this->display(C('HOME_DEFAULT_THEME').':index');
    }
    
    public function actionClass () {
		$channel = M("Channel");
		$condition['id'] = $_GET['fid'];
		$page = isset($_GET['p'])? $_GET['p'] : '1';  
		$this->assign("page",$page);
		if ($_GET['actype'] == "channel") {
			$this->assign("fid",$_GET['fid']);
			$tplName = $channel->where($condition)->getField('cl_tplclass');
		} else {
			$this->assign("cid",$_GET['cid']);
			$tplName = $channel->where($condition)->getField('cl_tplcontent');
		}
    	$this->display(C('HOME_DEFAULT_THEME').':' . $tplName);
    }
    
    public function actionPage () {
		$class = M("Page");
		$condition['id'] = $_GET['fid'];
		$page = isset($_GET['p'])? $_GET['p'] : '1';  

		$this->assign("fid",$_GET['fid']);
		$tplName = $class->where($condition)->getField('pg_tplpage');
    	$this->display(C('HOME_DEFAULT_THEME').':' . $tplName);
    }
	
    public function taglist () {
		$this->assign("keyword",mb_convert_encoding($_GET['key'],'UTF-8','auto'));
		Log::write('文件名actionClass：' . $_GET['key'] . '2222' . mb_convert_encoding($_GET['key'],'UTF-8','auto'));
		$page   = isset($_GET['p'])? $_GET['p'] : '1';  
		$this->assign("page",$page);
    	$this->display(C('HOME_DEFAULT_THEME').':taglist');
    }

	
	public function GetDiyField($type='',$row='')
	{
		$exFlag = ",";
		$model = M("Class_par");
		$condition['cl_par_attid'] = $type;
		$result = $model->where($condition)->select();
		$tempStr = "";
		foreach($result as $key => $r)
		{
			if (0 != $key) $tempStr .= '<br />';
			
			if(isset($row[$r['cl_par_name']]))
				$fieldvalue = $row[$r['cl_par_name']];
			else
				$fieldvalue = '';
	
	
			$tempStr .= '<tr';
			if($r['cl_par_type'] == 'mediumtext')
			{
				$tempStr .= ' height="304"';
			}
			$tempStr .= '><td height="35" align="right">'.$r['cl_par_key'].'：</td><td>';
	
	
			if($r['cl_par_type']=='varchar' or $r['cl_par_type']=='int' or $r['cl_par_type']=='decimal')
			{
				$tempStr .= '<input type="text" name="'.$r['cl_par_name'].'" id="'.$r['cl_par_name'].'" class="class_input" value="'.$fieldvalue.'" />';
			}
	
	
			else if($r['cl_par_type'] == 'text')
			{
				$tempStr .= '<textarea name="'.$r['cl_par_name'].'" id="'.$r['cl_par_name'].'" class="class_areatext" style="margin:7px 0;">'.$fieldvalue.'</textarea>';
			}
	
	
			else if($r['cl_par_type'] == 'radio')
			{
				if(!empty($r['cl_par_cnf']))
				{
					$cl_par_cnf = explode($exFlag, $r['cl_par_cnf']);
					foreach($cl_par_cnf as $k=>$cl_par_cnf_arr)
					{
						$cl_par_cnf_val = explode('=', $cl_par_cnf_arr);
	
						if($fieldvalue != '')
						{
							if($cl_par_cnf_val[1] == $fieldvalue)
								$checked = 'checked="checked"';
							else
								$checked = '';
						}
						else
						{
							if($k == 0)
								$checked = 'checked="checked"';
							else
								$checked = '';
						}
	
						$tempStr .= '<input type="radio" name="'.$r['cl_par_name'].'" id="'.$r['cl_par_name'].'" value="'.$cl_par_cnf_val[1].'" '.$checked.' />&nbsp;'.$cl_par_cnf_val[0];
						if($k < (count($cl_par_cnf)-1)) $tempStr .= '&nbsp;&nbsp;&nbsp;';
					}
	
				}
				
			}
	
	
			else if($r['cl_par_type'] == 'checkbox')
			{
				if(!empty($r['cl_par_cnf']))
				{
					$cl_par_cnf = explode($exFlag, $r['cl_par_cnf']);
					foreach($cl_par_cnf as $k=>$cl_par_cnf_arr)
					{
						$cl_par_cnf_val = explode('=', $cl_par_cnf_arr);
	
						if($fieldvalue != '')
						{
							$fileall = explode($exFlag,$fieldvalue);
							if(is_array($fileall))
							{
								if(in_array($cl_par_cnf_val[1], $fileall))
									$checked = 'checked="checked"';
								else
									$checked = '';
							}
							else
							{
								if($cl_par_cnf_val[1] == $fieldvalue)
									$checked = 'checked="checked"';
								else
									$checked = '';
							}
						}
						else
						{
							$checked = '';
						}
	
						$tempStr .= '<input type="checkbox" name="'.$r['cl_par_name'].'[]" id="'.$r['cl_par_name'].'[]" value="'.$cl_par_cnf_val[1].'" '.$checked.' />&nbsp;'.$cl_par_cnf_val[0];
						if($k < (count($cl_par_cnf)-1)) $tempStr .= '&nbsp;&nbsp;&nbsp;';
					}
				}
	
			}
	
	
			else if($r['cl_par_type'] == 'select')
			{
				if(!empty($r['cl_par_cnf']))
				{
	
					$tempStr .= '<select name="'.$r['cl_par_name'].'" id="'.$r['cl_par_name'].'">';
					$cl_par_cnf = explode($exFlag, $r['cl_par_cnf']);
					foreach($cl_par_cnf as $k=>$cl_par_cnf_arr)
					{
						$cl_par_cnf_val = explode('=', $cl_par_cnf_arr);
	
						if($fieldvalue != '')
						{
							if($cl_par_cnf_val[1] == $fieldvalue)
								$selected = 'selected="selected"';
							else
								$selected = '';
						}
						else
						{
							$selected = '';
						}
	
						$cl_par_cnf_val = explode('=', $cl_par_cnf_arr);
						$tempStr .= '<option name="'.$r['cl_par_name'].'" id="'.$r['cl_par_name'].'" value="'.$cl_par_cnf_val[1].'"'.$selected.' />'.$cl_par_cnf_val[0].'</option>';
						if($k < (count($cl_par_cnf)-1)) $tempStr .= '&nbsp;&nbsp;&nbsp;';
					}
					$tempStr .= '</select>';
				}
			}
	
	
			else if($r['cl_par_type'] == 'file')
			{
				$tempStr .= '<input type="text" name="'.$r['cl_par_name'].'" id="'.$r['cl_par_name'].'" class="class_input" value="'.$fieldvalue.'" />';
				$tempStr .= '<span class="cnote"><span class="gray_btn" onclick="GetUploadify(\'uploadify\',\''.$r['cl_par_key'].'\',\'all\',\'all\',1,'.$cfg_max_file_size.',\''.$r['cl_par_name'].'\')">上 传</span></span>';
			}
	
	
			else if($r['cl_par_type'] == 'mediumtext')
			{
				$tempStr .= '<textarea name="'.$r['cl_par_name'].'" id="'.$r['cl_par_name'].'" class="kindeditor">'.$fieldvalue.'</textarea>';
				$tempStr .= '<script type="text/javascript">var editor_'.$r['cl_par_name'].';KindEditor.ready(function(K) {editor_'.$r['cl_par_name'].' = K.create(\'textarea[name="'.$r['cl_par_name'].'"]\', {allowFileManager : true,width:\'667px\',height:\'280px\'});});</script>';
			}
		}
	
		$tempStr .= '</td></tr>';
		return $tempStr;
	}

    

    public function message(){

        if ($_POST['submit']) {
        	$message=M('Message');

        	$data=$_POST;

        	$data['clientip']=get_client_ip();
        	$data['postdate']=time();
 
        	if($message->data($data)->add()){
        		 
        		$this->success('提交留言成功!');
        	}else{
        		$this->error('提交留言失败');
        	}

        } else {
        
           $message=M('Message')->order("id desc")->select();
	       $this->assign("message",$message);

	       $this->display(C('HOME_DEFAULT_THEME').':message');
        }
        
    	
    }
	
	public function qzgdwlChange(){
		
		$unionid = $_SESSION['unionid'];
		$openid = $_SESSION['openid'];
		
		$cust_code = $_GET['cust_code'];
		$arrCondition = array();
		$arrCondition['unionid'] = $unionid;
		//$userRow = M('User')->where($arrCondition)->select();
		$userMoreRow = M('Usermore')->where(array('unionid'=>$unionid,'cust_code'=>$cust_code))->select()[0];
		if($userMoreRow){
			$date = array();
			$data['cust_code'] = $userMoreRow['cust_code'];
			$data['cust_name'] = $userMoreRow['cust_name'];
			$data['cust_prop'] = $userMoreRow['cust_prop'];
			$data['address'] = $userMoreRow['address'];
			$data['phone1'] = $userMoreRow['phone1'];
			$data['phone2'] = $userMoreRow['phone2'];
			$data['mobile1'] = $userMoreRow['mobile1'];
			$data['mobile2'] = $userMoreRow['mobile2'];
			$data['accountno'] = $userMoreRow['accountno'];
			$data['createdate'] = $userMoreRow['createdate'];
			$data['createorg'] = $userMoreRow['createorg'];
			$data['ownorgid'] = $userMoreRow['ownorgid'];
			$data['parentMgrAddrId'] = $userMoreRow['parentMgrAddrId'];	
			$data['mgrAddrid'] = $userMoreRow['mgrAddrid'];
			M('User')->where($arrCondition)->save($data);
			$this->qzgdwlSaveLog("切换账号至$cust_code");
		}
		$this->qzgdwlIndex();
	}
	
	private function isWx(){
		
		if(!$_SESSION['unionid'] || !$_SESSION['openid'] || !$_SESSION['state']){
			$url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx3a20e613be177269&redirect_uri=http%3a%2f%2fwx.968816.com.cn%2fqzgdwl%2findex.php%3fm%3dlogin%26a%3dindex&response_type=code&scope=snsapi_base&state=qzgdwl%26qzgdwlIndex#wechat_redirect";
			header("Location: $url");
		}
	}
	
	public function qzgdwlIndex(){
		if(strpos($_SERVER["HTTP_USER_AGENT"],"Chrome"))  {
			$this->assign("browser","chrome");
		}
		
		$this->assign("unionid",$_SESSION['unionid']);
		$this->assign("openid",$_SESSION['openid']);
		
		$unionid = $_SESSION['unionid'];
		$openid = $_SESSION['openid'];
		

		$user = M('User');
		$unionidArr = array();
		$unionidArr['unionid'] = $unionid;
		$userRow = $user->where($unionidArr)->select();
						
		$openidArr = array();
		$openidArr['openid'] = $openid;
		$userRow1 = $user->where($openidArr)->select();
		// if($userRow1[0]['openid'] == $userRow1[0]['unionid']){		
			// $user->where($openidArr)->save(array('unionid'=>$unionid));
			// $userMore = M('Usermore');
			// $userMoreRow = $userMore->where(array('unionid'=>$openid))->select();
			// if($userMoreRow){
				// $userMore->where(array('unionid'=>$openid))->save(array('unionid'=>$unionid));
			// }
		// }
		
		
		
		$unionidArr['unionid'] = $unionid;
		$userRow = $user->where($unionidArr)->select();
		$userMoreRow = M('Usermore')->where($unionidArr)->select();
		
		//echo $this->json($userRow);
		//echo $this->json($userMoreRow);
		//$this->assign("tmp_userRow",json_encode($userRow));
		//$this->assign("tmp_userMoreRow",json_encode($userMoreRow));
		
		//echo count($userMoreRow);
		
		if($userRow){
			if($userRow[0]['is_binding'] == 1 && $userRow[0]['cust_code']){		
				$this->qzgdwlSaveLog("登录");
				$addressArr = explode("|",$userRow[0]['address']);
				$count = count($addressArr);
				$userRow[0]['address1'] = $addressArr[$count-3];
				$userRow[0]['address2'] = $addressArr[$count-2].$addressArr[$count-1];
				
				$userRow[0]['cust_hideName'] = $this->hideName($userRow[0]['cust_name']);
				
				for ($i=0;$i<count($userMoreRow);$i++)
				{
						$userMoreRow[$i]['cust_hideName'] = $this->hideName($userMoreRow[$i]['cust_name']);
				}
			
				
				$this->assign("userRow",$userRow[0]);
				$this->assign("userMoreRow",$userMoreRow);
				
				$card = M('Card');
				$cardArr = array();
				$cardArr['cust_code'] = $userRow[0]['cust_code'];
				$cardArr['card_status'] = 0;
				$cardRow = count($card->where($cardArr)->select());
				$this->assign("cardRow",$cardRow);
				
				//$this->jfs($unionid);
				$this->display(C('HOME_DEFAULT_THEME').':qzgdwlIndex');
			}else{			
				$user->where(array('unionid'=>$unionid))->save(array('is_binding'=>0));	
				M('Usermore')->where(array('unionid'=>$unionid))->delete();
				//$this->display(C('HOME_DEFAULT_THEME').':qzgdwlLogin');
				$this->display(C('HOME_DEFAULT_THEME').':qzgdwlIndex');
			}
		}else if($unionid){
			$unionidArr['openid'] = $openid;
			$unionidArr['is_binding'] = 0;
			$unionidArr['binding_num'] = 0;
			$unionidArr['subscribe_time'] = date('Y-m-d H:i:s',time());
			$user->data($unionidArr)->add();
			//$this->display(C('HOME_DEFAULT_THEME').':qzgdwlLogin');
			$this->display(C('HOME_DEFAULT_THEME').':qzgdwlIndex');
		}
		//$this->display(C('HOME_DEFAULT_THEME').':qzgdwlIndex');
	}
	
	public function qzgdwlPersonal(){
		$unionid = $_SESSION['unionid'];
		$openid = $_SESSION['openid'];
		
		
		
		$this->assign("unionid",$unionid);
		$user = M('User');
		$unionidArr = array();
		$unionidArr['unionid'] = $unionid;
		$userRow = $user->where($unionidArr)->select();
		if($userRow && $userRow[0]['is_binding'] == 1){
			$this->assign("userRow",$userRow[0]);
			$this->display(C('HOME_DEFAULT_THEME').':qzgdwlPersonal');
		}else{			
			$this->display(C('HOME_DEFAULT_THEME').':qzgdwlLogin');
		}
	}
	
	public function qzgdwlChargeList(){
		$unionid = $_SESSION['unionid'];
		$openid = $_SESSION['openid'];
		
		$user = M('User');
		$unionidArr = array();
		$unionidArr['unionid'] = $unionid;
		$userRow = $user->where($unionidArr)->select();
		$this->assign("userRow",$userRow[0]);
		$this->assign("unionid",$unionid);
		$this->display(C('HOME_DEFAULT_THEME').':qzgdwlChargeList');
	}
	
	public function qzgdwlTicketList(){
		$unionid = $_SESSION['unionid'];
		$openid = $_SESSION['openid'];
		
		$this->assign("unionid",$unionid);
		$user = M('User');
		$unionidArr = array();
		$unionidArr['unionid'] = $unionid;
		$userRow = $user->where($unionidArr)->select();
		if($userRow && $userRow[0]['is_binding'] == 1 && $userRow[0]['cust_code']){
			$card = M('Card');
			$cardArr = array();
			$cardArr['cust_code'] = $userRow[0]['cust_code'];
			$cardRow = $card->where($cardArr)->order('id desc')->limit(20)->select();
			$this->assign("userRow",$userRow[0]);
			$this->assign("cardRow",$cardRow);
			//$this->jfs($unionid);
			$this->display(C('HOME_DEFAULT_THEME').':qzgdwlTicketList');
		}else{			
			$this->display(C('HOME_DEFAULT_THEME').':qzgdwlLogin');
		}
		
		
	}
	
	public function jfs($unionid){
		$userRow = M('User')->where(array('unionid'=>$unionid,'is_binding'=>1))->select();
		$wxTime = '';
		$APPPAYNO = '';
		$log_sjRow = '';//获取有效的缴费进入记录
		$logName = '用户节跳转集团微信支付';
		$arrJfs = array();
		$arrResult = array();
		
		$_SESSION['APPPAYNO'] = '';
		
		$log_sjRow = M('Yhj_log')->where(array('unionid'=>$unionid,'log'=>$logName))->max('create_date');
		//echo substr($log_sjRow,0,10);
		if($log_sjRow && substr($log_sjRow,0,10) == '2017-05-06'){
			//$log_sjRow = M('Yhj_log')->where(array('id'=>$log_sjRow))->select();
			//$wxTime = substr($log_sjRow[0]['create_date'],0,10);
			$wxTime = '2017-05-06';
			if(time()-strtotime('2017-05-07 00:00:00') >= 0){
				$wxTime = '2999-05-06';
			}
			//echo $wxTime;
		}else{
			$arrResult['code'] = 0;
			return $arrResult;
		}
		if($userRow){	
			$jfsRow = M('Yhj_jfs')->where(array('unionid'=>$unionid))->select();
			//print_r($jfsRow);
			if($jfsRow){				
				$arrResult['code'] = 0;
				return $arrResult;
			}else{
				$accountNo = $userRow[0]['accountno'];
				//echo $accountNo;
				$TOKEN_URL="http://36.250.88.58:8085/ppjj/tvPpjj.action?method=getCharge&accountNo=$accountNo";
				$values = $this->getBoss($TOKEN_URL);
				if($values[0]['attributes']['RETURN-CODE'] == 0) {					
					foreach($values as $value1){
						if(isset($value1['attributes']['MERCHANTID']) && $value1['attributes']['MERCHANTID'] == '910002' && floatval($value1['attributes']['AMOUNT']) >= 300 && $value1['attributes']['PAYTIME'] >= $wxTime) {
							$log_sjRow1 = M('Yhj_jfs')->where(array('payid'=>$value1['attributes']['APPPAYNO']))->select();
							if($log_sjRow1){
								$arrResult['code'] = 0;
								return $arrResult;
							}
							$_SESSION['APPPAYNO'] = $value1['attributes']['APPPAYNO'];	
							$arrResult['APPPAYNO'] = $value1['attributes']['APPPAYNO'];	
							$arrResult['code'] = 1;
							$arrReturn = array();
							$arrReturn = $this->runByPraise('用户节赠送');	
							return $arrResult;
							break;
							//update `ai_card` t set t.unionid = null,t.cust_code = null,t.cust_name = null,t.accountno=null  where t.card_no = 35060010501;
						}
					}				
				}
				$arrResult['code'] = 0;
				return $arrResult;
			}			
		}		
		$arrResult['code'] = 0;
		return $arrResult;
		
	}
	
	public function runByPraise($name){
		//sleep(1.5);
		$arrPirze = array();
		$prize=M('Yhj_cltprize');
		$prizeRow = $prize->order('id asc')->select();		
				
		foreach($prizeRow as $key=>$val){
			if($val['praisecontent'] != $name ){
				$val['chance'] = 0;
			}
			$arrPirze[$key]=$val;
		}
		
		$arr = array();
		$count = array();
		$praiseName = null; //奖品
		$numtimes = 0; //还剩次数
		$angle = 0;		
		$praiseNumber = 0;//所有奖项		
		$unionidArr = array();
		$unionidArr['unionid'] = $_SESSION['unionid'];	
		$unionid = $_SESSION['unionid'];
		$praise = 0;
		$numstore = 0;
		
		foreach ($arrPirze as $key => $val) {
    		$arr[$val['id']] = $val['chance'];
    		$count[$val['id']] = $val['praisenumber'];
		}
		
		$rid = $this->getRand($arr,$count); //根据概率获取奖项id
		$res = $arrPirze[$rid-1]; //中奖项
					
		$mp=M('Yhj_cltuserpraise');//保存奖品
		$praise = $res['id'];
		$praiseName = $res['praisename'];
		
		$mpRow = $mp->add(array('unionid'=>$unionid,'prizeid'=>$praise,'praisename'=>$praiseName,'date'=>date("Y-m-d H:i:s"),'isjf'=>$res['isjf'],'ispraise'=>$res['ispraise'],'isexchange'=>0));
		
		M('Yhj_jfs')->add(array('unionid'=>$unionid,'payid'=>$_SESSION['APPPAYNO'],'prizeid'=>$praise,'praisename'=>$praiseName,'date'=>date("Y-m-d H:i:s"),'isjf'=>$res['isjf'],'ispraise'=>$res['ispraise'],'isexchange'=>0));
		
		$angle = 1;
		
		if($mpRow){
			if($res['ischarge'] == 1){
				M()->startTrans();//开启事务
				$cardArr['unionid']=array('EXP','IS NULL');
				$cardArr['card_value'] = intval($res['value']);//查询条件
				$cardArr['card_remark'] = '用户节红包';//查询条件
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
			}
			
		}
		
		
		
		//用户抽取的那个奖项减1
		
		$prize = M('Yhj_cltprize');
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
    	//概率数组循环
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
	
	public function qzgdwlCzkCharge(){
		$unionid = $_SESSION['unionid'];
		$openid = $_SESSION['openid'];
		
		$this->assign("unionid",$unionid);
		$user = M('User');
		$unionidArr = array();
		$unionidArr['unionid'] = $unionid;
		$userRow = $user->where($unionidArr)->select();
		if($userRow && $userRow[0]['is_binding'] == 1){
			$this->assign("userRow",$userRow[0]);
			$this->display(C('HOME_DEFAULT_THEME').':qzgdwlCzkCharge');
		}else{			
			$this->display(C('HOME_DEFAULT_THEME').':qzgdwlLogin');
		}
	}
	public function trimall($str){
		$qian=array(" ","　","\t","\n","\r");
		$hou=array("","","","","");
		return str_replace($qian,$hou,$str);    
	}
	
	public function qzgdwlTicketCzkSure(){
		$result = array();
		$unionid = $_SESSION['unionid'];
		$openid = $_SESSION['openid'];
		
		$id = $_POST['id'];
		$card = M('Card');
		$card = M('Card');
		$cardConditonArr = array();
		$cardConditonArr['id'] = $id;
		$cardRow = $card->where($cardConditonArr)->select();
		$card_no = $cardRow[0]['card_no'];
		$card_password = $cardRow[0]['card_password'];
		$accountno = $cardRow[0]['accountno'];
		
		/*$user = M('User');
		$unionidArr = array();
		$unionidArr['unionid'] = $unionid;
		$userRow = $user->where($unionidArr)->select();
		$accountno = $userRow[0]['accountno'];*/
		$phone = "";
		
		if(!$unionid){
			$this->display(C('HOME_DEFAULT_THEME').':errorMessage');
		}else{
				$TOKEN_URL="http://36.250.88.58:8085/ppjj/tvPpjj.action?method=chargeSure&card_no=$card_no&card_password=$card_password&accountno=$accountno&phone=$phone";
				$values = $this->getBoss($TOKEN_URL);
				if($values[0]['attributes']['RETURN-CODE'] != "0") {	
					$result['code'] = 0;
					$result['msg'] = $values[0]['attributes']['RETURN-MESSAGE'];	
					if($values[0]['attributes']['RETURN-MESSAGE'] == '卡已使用'){
						$cardStatusArr = array();
						$cardStatusArr['card_status'] = 1;
						$cardStatusArr['card_useddate'] = date('Y-m-d H:i:s',time());
						$card->where($cardConditonArr)->save($cardStatusArr);
						$result['msg'] = $values[0]['attributes']['RETURN-MESSAGE'];	
					}
				}else{					
					$result['code'] = 1;
					$cardStatusArr = array();
					$cardStatusArr['card_status'] = 1;
					$cardStatusArr['card_useddate'] = date('Y-m-d H:i:s',time());
					$card->where($cardConditonArr)->save($cardStatusArr);
					$result['msg'] = $values[0]['attributes']['RETURN-MESSAGE'];
				}
				
				$this->qzgdwlSaveCardLog($unionid,$result['msg'],$card_no,$card_password,$cardRow[0]['cust_code'],$cardRow[0]['cust_name'],$cardRow[0]['accountno'],$result['code']);
				
				echo $this->json($result);
			
		}
	}
	
	public function qzgdwlCzkSure(){
		$result = array();
		$unionid = $_SESSION['unionid'];
		$openid = $_SESSION['openid'];
		
		$card_no = $this->trimall($_POST['card_no']);
		$card_password = $this->trimall($_POST['card_password']);
		
		$user = M('User');
		$unionidArr = array();
		$unionidArr['unionid'] = $unionid;
		$userRow = $user->where($unionidArr)->select();
		$accountno = $userRow[0]['accountno'];
		$phone = "";
		
		if(!$unionid){
			$this->display(C('HOME_DEFAULT_THEME').':errorMessage');
		}else{
				$TOKEN_URL="http://36.250.88.58:8085/ppjj/tvPpjj.action?method=chargeSure&card_no=$card_no&card_password=$card_password&accountno=$accountno&phone=$phone";
				$values = $this->getBoss($TOKEN_URL);
				if(isset($values[0]['attributes']['RETURN-CODE'])){
					$result['code'] = 0;
					$result['msg'] = $values[0]['attributes']['RETURN-MESSAGE'];	
				}else{
					$result['code'] = 2;
					$result['msg'] = "系统错误，请联系客服96311";
				}
				
				$this->qzgdwlSaveCardLog($unionid,$result['msg'],$card_no,$card_password,$userRow[0]['cust_code'],$userRow[0]['cust_name'],$userRow[0]['accountno'],$result['code']);
				echo $this->json($result);
			
		}
	}
	
	public function qzgdwlBusiness(){
		$unionid = $_SESSION['unionid'];
		$openid = $_SESSION['openid'];
		$tc_name = $_GET['tc_name'];
		$this->assign("unionid",$unionid);
		$this->assign("tc_name",$tc_name);
		$user = M('User');
		$unionidArr = array();
		$unionidArr['unionid'] = $unionid;
		$userRow = $user->where($unionidArr)->select();
		if($userRow && $userRow[0]['is_binding'] == 1){
			$this->assign("userRow",$userRow[0]);
			$this->display(C('HOME_DEFAULT_THEME').':qzgdwlBusiness');
		}else{			
			$this->display(C('HOME_DEFAULT_THEME').':qzgdwlBusiness');
		}
	}
	
	public function qzgdwlGoWx(){
		$unionid = $_SESSION['unionid'];
		$openid = $_SESSION['openid'];
		
		$user = M('User');
		$unionidArr = array();
		$unionidArr['unionid'] = $unionid;
		$userRow = $user->where($unionidArr)->select();
		$this->qzgdwlSaveLog("双节跳转集团微信支付");
		//$this->sjSaveLog("用户节跳转集团微信支付");
		$url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx7a6b2b757241f8f2&redirect_uri=http://wsc.fjgdwl.com/cgi-bin/auth?from=dotcore&cityid=350500&response_type=code&scope=snsapi_base&state=http%3a%2f%2fwx.fjgdwl.com%2fbroadcast%2fwx%2fWxToken.action%3ftype%3dauthSuccess%26custCode%3d".$userRow[0]["cust_code"]."%26corpOrgId%3d".$userRow[0]["ownorgid"]."&connect_redirect=1#wechat_redirect";
		//header("location:$url"); 
		header("Location: $url");
	}
	
	
	public function qzgdwlGoWxSj(){
		$unionid = $_SESSION['unionid'];
		$openid = $_SESSION['openid'];
		
		$user = M('User');
		$unionidArr = array();
		$unionidArr['unionid'] = $unionid;
		$userRow = $user->where($unionidArr)->select();
		if($userRow[0]['is_binding'] == 1){
			$this->qzgdwlSaveLog("双节跳转集团微信支付");
			//$this->sjSaveLog("用户节跳转集团微信支付");
			$url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx7a6b2b757241f8f2&redirect_uri=http://wsc.fjgdwl.com/cgi-bin/auth?from=dotcore&cityid=350500&response_type=code&scope=snsapi_base&state=http%3a%2f%2fwx.fjgdwl.com%2fbroadcast%2fwx%2fWxToken.action%3ftype%3dauthSuccess%26custCode%3d".$userRow[0]["cust_code"]."%26corpOrgId%3d".$userRow[0]["ownorgid"]."&connect_redirect=1#wechat_redirect";
			//header("location:$url"); 
			header("Location: $url");
		}else{
			$this->display(C('HOME_DEFAULT_THEME').':qzgdwlLogin');
		}
	}
	
	public function qzgdwlBusi(){
		$unionid = $_SESSION['unionid'];
		$openid = $_SESSION['openid'];
		
		$this->assign("unionid",$unionid);
		$taocan = M('Taocan');
		$taocanRow = $taocan->order('tc_id asc')->select();
		$this->assign("taocanRow",$taocanRow);
		$this->display(C('HOME_DEFAULT_THEME').':qzgdwlBusi');		
	}
	
	public function qzgdwlFaultList(){
		$unionid = $_SESSION['unionid'];
		$openid = $_SESSION['openid'];
		
		$fault = M('Fault');
		$unionidArr = array();
		$faultRow = array();
		$unionidArr['unionid'] = $unionid;
		if($unionid == 'o1-4qxEHFazQ2f79N2OJ3rhK600w' || $unionid == 'odrEdt4BSKcs-VvOXocU3joswWDU'){
			$faultRow = $fault->order('fault_id desc')->select();
		}else{
			$faultRow = $fault->where($unionidArr)->order('fault_id desc')->select();
		}
		$this->assign("faultRow",$faultRow);
		$this->assign("unionid",$unionid);
		$this->display(C('HOME_DEFAULT_THEME').':qzgdwlFaultList');		
	}
	
	public function qzgdwlBusinessList(){
		$unionid = $_SESSION['unionid'];
		$openid = $_SESSION['openid'];
		
		$business = M('Business');
		$unionidArr = array();
		$unionidArr['unionid'] = $unionid;
		$busiRow = array();
		if($unionid == 'o1-4qxEHFazQ2f79N2OJ3rhK600w' || $unionid == 'odrEdt4BSKcs-VvOXocU3joswWDU'){
			$busiRow = $business->order('busi_id desc')->select();
		}else{
			$busiRow = $business->where($unionidArr)->order('busi_id desc')->select();
		}
		$this->assign("busiRow",$busiRow);
		$this->assign("unionid",$unionid);
		$this->display(C('HOME_DEFAULT_THEME').':qzgdwlBusinessList');		
	}
	
	public function qzgdwlSaveBusiness(){
		$business = M('Business');
		$data = array();
		$data['unionid'] = $_SESSION['unionid'];
		$data['create_date'] = date('Y-m-d H:i:s',time());
		$data['busi_type'] = $_POST['business'];
		$data['busi_name'] = $_POST['name'];
		$data['busi_phone'] = $_POST['phone'];
		$data['busi_address'] = $_POST['address'];
		$data['busi_code'] = $_POST['cust_code'];
		$data['busi_status'] = '待处理';
		$data['busi_status1'] = '待处理';
		$business->data($data)->add();
		$result = array();
		$result['code'] = 0;
		$result['msg'] = "提交成功，客服人员将稍后联系您。";
		echo $this->json($result);
		
	}
	
	public function qzgdwlDphd(){
		$this->display(C('HOME_DEFAULT_THEME').':qzgdwlDphd');	
	}
	
	public function qzgdwlActivity(){
		$this->display(C('HOME_DEFAULT_THEME').':qzgdwlActivity');	
	}
	
	public function qzgdwlMenuActivity(){
		//echo 123;
		$this->display(C('HOME_DEFAULT_THEME').':qzgdwlMenuActivity');	
	}
	
	public function qzgdwlSelf(){
		$unionid = $_SESSION['unionid'];
		$openid = $_SESSION['openid'];
		
		$self = M('Self');
		$selfRow = $self->order('self_id asc')->select();
		$selfType = $self->Distinct(true)->field('self_type')->order('self_id asc')->select();
		
		$this->assign("selfRow",json_encode($selfRow));
		$this->assign("selfType",json_encode($selfType));
		$this->assign("unionid",$unionid);
		
		$this->assign("test","HeiLOOd");
		
		$this->display(C('HOME_DEFAULT_THEME').':qzgdwlSelf');		
	}
	
	public function qzgdwlPdcx(){		
		$unionid = $_SESSION['unionid'];
		$openid = $_SESSION['openid'];
		
		$channelList = M('Channel_list');
		$condition = array();
		$channelListRow = $channelList->order('channel_id,channel_no')->select();	
		$this->assign("channelListRow",$channelListRow);	
		
		
		
		$channelListBDRow = $channelList->where(array('channel_type'=>'本地'))->order('channel_id,channel_no')->select();
		$this->assign("channelListBDRow",$channelListBDRow);	
		$channelListBDRow = $channelList->where(array('channel_type'=>'央视'))->order('channel_id,channel_no')->select();
		$this->assign("channelListYSRow",$channelListBDRow);	
		$channelListBDRow = $channelList->where(array('channel_type'=>'专业'))->order('channel_id,channel_no')->select();
		$this->assign("channelListZYRow",$channelListBDRow);	
		$channelListBDRow = $channelList->where(array('channel_type'=>'卫视'))->order('channel_id,channel_no')->select();
		$this->assign("channelListWSRow",$channelListBDRow);	
		$channelListBDRow = $channelList->where(array('channel_type'=>'高清'))->order('channel_id,channel_no')->select();
		$this->assign("channelListGQRow",$channelListBDRow);
		
		/*$channelListBDRow = $channelList->where(array('channel_type'=>'本地','channel_area'=>array('in','市区','全区域')))->order('channel_id,channel_no')->select();
		$this->assign("channelListBDSQRow",$channelListBDRow);	$channelListBDRow = $channelList->where(array('channel_type'=>'本地','channel_area'=>array('in','市区','全区域')))->order('channel_id,channel_no')->select();
		$this->assign("channelListBDSQRow",$channelListBDRow);	*/
		
		$this->assign("unionid",$unionid);
		$this->display(C('HOME_DEFAULT_THEME').':qzgdwlPdcx');
		
	}
	
	public function qzgdwlLogin(){
		$unionid = $_SESSION['unionid'];
		$openid = $_SESSION['openid'];
		$this->assign("unionid",$unionid);	
		
		$custCode = $_GET['custCode'];
		$this->assign("custCode",$custCode);
		$this->display(C('HOME_DEFAULT_THEME').':qzgdwlLogin');
		
	}
	
	public function qzgdwlFault(){
		$unionid = $_SESSION['unionid'];
		$openid = $_SESSION['openid'];
		
		$user = M('User');
		$unionidArr = array();
		$unionidArr['unionid'] = $unionid;
		$userRow = $user->where($unionidArr)->select();
		if($userRow && $userRow[0]['is_binding'] == 1){
			require_once "jssdk.php";
			$jssdk = new JSSDK("wx3a20e613be177269", "b722fba7bd164688bcfa5b4acb2f1bee");
			$signPackage = $jssdk->GetSignPackage();
			$this->assign("signPackage",$signPackage);
			$this->assign("unionid",$unionid);
			$this->assign("userRow",$userRow[0]);
			$this->display(C('HOME_DEFAULT_THEME').':qzgdwlFault');
		}else{
			$this->display(C('HOME_DEFAULT_THEME').':qzgdwlLogin');
			//$this->display(C('HOME_DEFAULT_THEME').':qzgdwlFault');
		}
		
	}
	
	public function qzgdwlScanCardno(){
		$unionid = $_SESSION['unionid'];
		$openid = $_SESSION['openid'];
		
		require_once "jssdk.php";
		$jssdk = new JSSDK("wx3a20e613be177269", "b722fba7bd164688bcfa5b4acb2f1bee");
		$signPackage = $jssdk->GetSignPackage();
		//print_r($signPackage);
		$this->assign("signPackage",$signPackage);
		$this->assign("unionid",$unionid);
		$this->display(C('HOME_DEFAULT_THEME').':qzgdwlScanCardno');		
	}
	
	public function qzgdwlScanQRCardno(){
		$unionid = $_SESSION['unionid'];
		$openid = $_SESSION['openid'];
		
		$user = M('User');
		$unionidArr = array();
		$unionidArr['unionid'] = $unionid;
		$userRow = $user->where($unionidArr)->select();
		$this->assign("userRow",$userRow[0]['is_binding']);
		
		require_once "jssdk.php";
		$jssdk = new JSSDK("wx3a20e613be177269", "b722fba7bd164688bcfa5b4acb2f1bee");
		$signPackage = $jssdk->GetSignPackage();
		$this->assign("signPackage",$signPackage);
		$this->assign("unionid",$unionid);
		$this->display(C('HOME_DEFAULT_THEME').':qzgdwlScanQRCardno');		
	}
	
	public function qzgdwlUploadImg(){
		$result = array();
		$result['code'] = 0;
		$result['msg'] = 1;
		$serverId = $_GET['serverId'];
		$accessToken = $this->accessToken();
		$filename = 'wx-'.($this->getMillisecond()).'.jpg';
		$url = "http://file.api.weixin.qq.com/cgi-bin/media/get?access_token=$accessToken&media_id=$serverId";
		$this->getFile($url,$save_dir,$filename,1);		
		
		$result['msg'] = $filename;
		
		echo $this->json($result);
	}
	
	
    public function getMillisecond() {
		list($t1, $t2) = explode(' ', microtime());
		return (float)sprintf('%.0f',(floatval($t1)+floatval($t2))*1000);
	}
	
	public function qzgdwlSaveFault(){
		$fault = M('Fault');
		$data = array();
		$data['unionid'] = $_SESSION['unionid'];
		$user = M('User');
		$userRow = $user->where(array('unionid'=>$data['unionid']))->select();
		
		$data['create_date'] = date('Y-m-d H:i:s',time());
		$data['fault_type'] = $_POST['fault_type'];
		$data['fault_detail'] = $_POST['fault_detail'];
		$data['fault_pic'] = $_POST['fault_pic'];
		$data['fault_phone'] = $_POST['fault_phone'];
		$data['fault_name'] = $_POST['fault_name'];
		$data['fault_code'] = $userRow[0]['cust_code'];
		$data['fault_address'] = $_POST['fault_address'];
		$data['fault_status'] = '待处理';
		$fault->data($data)->add();
		$result = array();
		$result['code'] = 0;
		$result['msg'] = "提交成功，客服人员将稍后联系您。";
		echo $this->json($result);
		
	}
	
	function getFile($url,$save_dir='',$filename='',$type=0){
		if(trim($url)==''){
			return false;
		}
		if(trim($save_dir)==''){
			$save_dir='Upload/';
		}
		if(0!==strrpos($save_dir,'/')){
			$save_dir.='/';
		}
	//创建保存目录
		if(!file_exists($save_dir)&&!mkdir($save_dir,0777,true)){
			return false;
		}
	//获取远程文件所采用的方法
		if($type){
			$ch=curl_init();
			$timeout=5;
			curl_setopt($ch,CURLOPT_URL,$url);
			curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
			curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);
			$content=curl_exec($ch);
			curl_close($ch);
		}else{
			
		}
		$size=strlen($content);
		//文件大小
		$fp2=@fopen($save_dir.$filename,'a');
		fwrite($fp2,$content);
		fclose($fp2);
		unset($content,$url);
		return array('file_name'=>$filename,'save_path'=>$save_dir.$filename);
	}

	
	private function accessToken() {
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
	
	private function https_request($url)
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
	
	public function qzgdwlUploadFile(){
		include "fileupload.class.php";  
		$up = new fileupload;
		//设置属性(上传的位置， 大小， 类型， 名是是否要随机生成)
		$up -> set("path", "./images/");
		$up -> set("maxsize", 2000000);
		$up -> set("allowtype", array("gif", "png", "jpg","jpeg"));
		$up -> set("israndname", false);
	  
		//使用对象中的upload方法， 就可以上传文件， 方法需要传一个上传表单的名子 pic, 如果成功返回true, 失败返回false
		if($up -> upload("feedBack")) {
			echo '<pre>';
			//获取上传后文件名子
			var_dump($up->getFileName());
			echo '</pre>';
	  
		} else {
			echo '<pre>';
			//获取上传失败以后的错误提示
			var_dump($up->getErrorMsg());
			echo '</pre>';
		}
	}
	
	public function test2(){
		require_once "jssdk.php";
		$jssdk = new JSSDK("wx3a20e613be177269", "b722fba7bd164688bcfa5b4acb2f1bee");
		$signPackage = $jssdk->GetSignPackage();
		//print_r($signPackage);
		$this->assign("signPackage",$signPackage);
		$this->display(C('HOME_DEFAULT_THEME').':test2');
	}
	
	public function login(){
		$unionid = $_SESSION['unionid'];
		$openid = $_SESSION['openid'];
		
		$this->display(C('HOME_DEFAULT_THEME').':login');
    }
	
	public function bangding(){
		
		$this->display(C('HOME_DEFAULT_THEME').':bangding');
    }
	
	public function ywyInsertCardno(){
		
		$this->display(C('HOME_DEFAULT_THEME').':ywyInsertCardno');
    }
	
	public function ywyInsertMac(){
		
		$this->display(C('HOME_DEFAULT_THEME').':ywyInsertMac');
    }
	
	public function ywyInsertStbno(){
		
		$this->display(C('HOME_DEFAULT_THEME').':ywyInsertStbno');
    }
	
	public function ywyInsertPhone(){
		
		$this->display(C('HOME_DEFAULT_THEME').':ywyInsertPhone');
    }
	
	public function ywyInsertCertno(){
		
		$this->display(C('HOME_DEFAULT_THEME').':ywyInsertCertno');
    }
	
	public function ywyInsertAddress(){
		
		$this->display(C('HOME_DEFAULT_THEME').':ywyInsertAddress');
    }
		
	public function ywyAcct(){
		$this->is_session();
		//echo $_SESSION['own_org_id'];
		
		$this->display(C('HOME_DEFAULT_THEME').':ywyAcct');
    }
	
	public function ywyUser(){
		$this->is_session();
		$this->display(C('HOME_DEFAULT_THEME').':ywyUser');
    }
	
	public function ywwOrdering(){
		$this->is_session();
		$this->display(C('HOME_DEFAULT_THEME').':ywwOrdering');
    }
	
	public function ywyBusiness(){
		$this->is_session();
		$this->display(C('HOME_DEFAULT_THEME').':ywyBusiness');
    }
	
	public function ywyBill(){
		//$this->is_session();
		$unionid = $_SESSION['unionid'];
		$openid = $_SESSION['openid'];
		
		$user = M("User");
		$unionidArr = array();
		$unionidArr['unionid'] = $unionid;
		$userRow = $user->where($unionidArr)->select();
		if($userRow[0]['is_binding']==1){
			
			$userRow[0]['cust_name'] = $this->hideName($userRow[0]['cust_name']);	
			
			$this->assign("accountNo",$userRow[0]['accountno']);
			$this->assign("cust_name",$userRow[0]['cust_name']);				
			$this->assign("unionid",$unionid);		
			$this->display(C('HOME_DEFAULT_THEME').':ywyBill');
		}else{
			$this->display(C('HOME_DEFAULT_THEME').':qzgdwlLogin');
		}
    }
	
	public function ywyCharge(){
		$unionid = $_SESSION['unionid'];
		$openid = $_SESSION['openid'];
		
		$this->assign("unionid",$unionid);
		$this->display(C('HOME_DEFAULT_THEME').':ywyCharge');
    }
	
	public function ywyIndex(){
		$this->is_session();
		$this->display(C('HOME_DEFAULT_THEME').':ywyIndex');
    }
	
	public function is_session(){
		/*if(!$_SESSION['own_org_id']){
			$url = " https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx3a20e613be177269&redirect_uri=http://www.968816.com.cn/oauth2_openid_ywy.php&response_type=code&scope=snsapi_base&state=1#wechat_redirect";
			echo "<script>";  
			echo "window.location.href=\"".$url."\"";  
			echo "</script>"; 
		}*/
	}
	
	public function ywyCustCodeSearch(){
		
		$this->display(C('HOME_DEFAULT_THEME').':ywyCustCodeSearch');
    }
	
	public function ywyInsertName(){
		
		$this->display(C('HOME_DEFAULT_THEME').':ywyInsertName');
    }
	
	public function qzgdwlSaveLog($logStr){
		$log = M('Log');
		$data = array();
		$data['unionid'] = $_SESSION['unionid'];
		$data['create_date'] = date('Y-m-d H:i:s',time());
		$data['log'] = $logStr;
		$log->data($data)->add();
		
	}
	
	public function sjSaveLog($logStr){
		$log = M('Yhj_log');
		$data = array();
		$data['unionid'] = $_SESSION['unionid'];
		$data['create_date'] = date('Y-m-d H:i:s',time());
		$data['log'] = $logStr;
		$log->data($data)->add();
		
	}
	
	public function qzgdwlSaveCardLog($unionid,$log1,$card_no,$card_password,$cust_code,$cust_name,$accountno,$is_suscess){
	
		$log = M('Card_log');
		$data = array();
		$data['unionid'] = $unionid;
		$data['create_date'] = date('Y-m-d H:i:s',time());
		$data['card_no'] = $card_no;
		$data['log'] = $log1;
		$data['card_password'] = $card_password;
		$data['cust_code'] = $cust_code;
		$data['cust_name'] = $cust_name;
		$data['accountno'] = $accountno;
		$data['is_suscess'] = $is_suscess;
		$log->data($data)->add();
		
	}
	
	public function ywyGetCust(){
		$cust_no = "";
		$resultStr = "";
		$result = array();
		$result['CODE'] = 99;
		if(isset($_POST['custCode']) && $_SESSION['unionid']){
			$cust_no = $_POST['custCode'];
			$TOKEN_URL="http://36.250.88.58:8085/ppjj/tvPpjj.action?method=loginBossByName&custcode=$cust_no";
			$this->qzgdwlSaveLog("客户信息查询：".$_POST['custCode']);
			$values = $this->getBoss($TOKEN_URL);
			/*$unionidArr = array();
			//$unionidArr['unionid'] =$_SESSION['unionid'];
			//$ywy = M('Ywy');
			$ywyRow = $ywy->where($unionidArr)->select();
			if($ywyRow]){
				if($ywyRow[0][is_confirm] != "1"){
					$ywy->where($unionidArr)->save(array('is_confirm'=>1,'unionid'=>$_SESSION['unionid'],'openid'=>$_SESSION['openid']));
					$result['CODE'] = 1;		
				}else{
					$result['CODE'] = 2;
				}
				echo $this->json($result);
			}*/
			if($values[0]['attributes']['RETURN-CODE'] == 0 ) {	
				
				if($_SESSION['own_org_id'] == $values[1]['attributes']['OWNORGID'] || $_SESSION['own_org_id'] == "1201"){
					if($_SESSION['own_org_id'] == "2201" && (strpos($values[1]['attributes']['ADDRESS'],"鲤城")== 0 || strpos($values[1]['attributes']['ADDRESS'],"丰泽") == 0 || strpos($values[1]['attributes']['ADDRESS'],"洛江") == 0 || strpos($values[1]['attributes']['ADDRESS'],"台商") == 0 )){
						$result['CUSTOMERCODE']  = $values[1]['attributes']['CUSTOMERCODE'];
						$result['CUSTOMERNAME']  = $values[1]['attributes']['CUSTOMERNAME'];
						$result['CUSTPROP']  = $values[1]['attributes']['CUSTPROP'];
						$result['ADDRESS']  = $values[1]['attributes']['ADDRESS'];
						$result['ACCOUNTID'] = $values[1]['attributes']['ACCOUNTID'];
						$result['OWNORGID'] = $values[1]['attributes']['OWNORGID'];
						$result['OWNORGID2'] = $_SESSION['own_org_id'];
						$result['PHONE']  = $values[1]['attributes']['PHONE1']."  ".$values[1]['attributes']['PHONE2']."  ".$values[1]['attributes']['MOBILE1']."  ".$values[1]['attributes']['MOBILE2'];				
						$result['CODE'] = 0;						
						echo $this->json($result);
					}elseif($_SESSION['own_org_id'] != "2201"){
						$result['CUSTOMERCODE']  = $values[1]['attributes']['CUSTOMERCODE'];
						$result['CUSTOMERNAME']  = $values[1]['attributes']['CUSTOMERNAME'];
						$result['CUSTPROP']  = $values[1]['attributes']['CUSTPROP'];
						$result['ADDRESS']  = $values[1]['attributes']['ADDRESS'];
						$result['ACCOUNTID'] = $values[1]['attributes']['ACCOUNTID'];
						$result['OWNORGID'] = $values[1]['attributes']['OWNORGID'];
						$result['OWNORGID2'] = $_SESSION['own_org_id'];
						$result['PHONE']  = $values[1]['attributes']['PHONE1']."  ".$values[1]['attributes']['PHONE2']."  ".$values[1]['attributes']['MOBILE1']."  ".$values[1]['attributes']['MOBILE2'];				
						$result['CODE'] = 0;						
						echo $this->json($result);
					}else{
						$result['CODE'] = 43;	
						//$result['MSG'] = strpos($values[1]['attributes']['ADDRESS'],"丰泽") ;
						echo $this->json($result);
					}
				}else{
					$result['CODE'] = 43;					
					echo $this->json($result);
				}
			}else{
				echo $this->json($result);
			}
			
		}else if(!$_SESSION['unionid']){
			$result['CODE'] = 44;
			echo $this->json($result);
		}else{
			echo $this->json($result);
		}		
		
	}
	
	public function getAcctNo(){
		$cust_no = "";
		$resultStr = "";
		$result = array();
		$result['CODE'] = 99;
		if(isset($_POST['custCode'])){
			$cust_no = $_POST['custCode'];
			$TOKEN_URL_ALL="http://36.250.88.58:8085/ppjj/tvPpjj.action?method=getAllInfro&custcode=$cust_no";
			$accoutNoArr = $this->getBoss($TOKEN_URL_ALL);
			$result['ACCOUNTID'] = $accoutNoArr[count($accoutNoArr)-3]['attributes']['ACCOUNTID'];
			$result['CODE'] = 0;
			echo $this->json($result);
		}else{
			echo $this->json($result);
		}
	}
	
	public function getAcctBalance(){
		$result = array();
		$result['CODE'] = 99;
		if(isset($_POST['acctNo'])){
			$acctNo = $_POST['acctNo'];
			$TOKEN_URL="http://36.250.88.58:8085/ppjj/tvPpjj.action?method=getAcct&accountNo=$acctNo";			
			$this->qzgdwlSaveLog("账户信息查询：".$_POST['acctNo']);
			$values = $this->getBoss($TOKEN_URL);
			$result['NAME']  = $values[1]['attributes']['NAME'];
			$result['ACCOUNTNO']  = $values[1]['attributes']['ACCOUNTNO'];
			$result['BALANCE']  = $values[1]['attributes']['BALANCE']."元";	
			$result['PAYTYPE']  = $values[1]['attributes']['PAYTYPE'];
			$result['JOINDATE']  = $values[1]['attributes']['JOINDATE'];
			$result['OFFICE']  = $values[1]['attributes']['OFFICE'];	
			$result['STATUS']  = $values[1]['attributes']['STATUS'];		
			$result['CODE'] = 0;
			echo $this->json($result);
		}else{
			echo $this->json($result);
		}
	}
	
	public function queryBillYwy(){
		$accountNo = $_POST['accountNo'];
		$result = array();
		//echo $phone!=null ;
		$TOKEN_URL="http://36.250.88.58:8085/ppjj/tvPpjj.action?method=getBill&accountNo=$accountNo";
		$this->qzgdwlSaveLog("客户账单查询：".$_POST['accountNo']);
		$values = $this->getBoss($TOKEN_URL);
		$TOKEN_URL1 ="http://36.250.88.58:8085/ppjj/tvPpjj.action?method=getAcct&accountNo=$accountNo";
		$values1 = $this->getBoss($TOKEN_URL1);
		if($values[0]['attributes']['RETURN-CODE'] == 0) {			
			$i = 0;
			$j = 0;
			$isSubBill = 0;
			foreach($values as $value1){
				if($value1['tag'] == 'BILLINFO' && isset($value1['attributes']) ){//&& $value1['attributes']['BILLCYCLE'] != date("Ym")
					$result["$i"]['BILLCYCLE'] = $value1['attributes']['BILLCYCLE'];
					$result["$i"]['AMOUNT'] = (floatval($value1['attributes']['ORIGINALAMOUNT']) + floatval($value1['attributes']['DISCOUNTAMOUNT']) + floatval($value1['attributes']['ADJUSTAMOUN']))/100;
					$result["$i"]['PPYAMOUNT'] = (floatval($value1['attributes']['PPYAMOUNT']))/100;
					$result["$i"]['UNPAYMENTCHARGE'] = $result["$i"]['AMOUNT'] - $result["$i"]['PPYAMOUNT'];
					$result["$i"]['BALANCE'] = $values1[1]['attributes']['BALANCE'];
					//
				}elseif($value1['tag'] == 'SUBBILL' && $value1['type'] == 'open'){
					$isSubBill = 1;
				}elseif($value1['tag'] == 'SUBBILL' && $value1['type'] == 'close'){
					$isSubBill = 0;
				}elseif($value1['tag'] == 'BILLDETAILINFO' && $value1['attributes']['BILLCYCLE'] == $result["$i"]['BILLCYCLE'] && $isSubBill == 1){
					$result["$i"]["$j"]['BILLCYCLE'] = $value1['attributes']['BILLCYCLE'];
					$result["$i"]["$j"]['ACCTITEMTYPENAME'] = $value1['attributes']['ACCTITEMTYPENAME'];
					$result["$i"]["$j"]['AMOUNT'] = (floatval($value1['attributes']['ORIGINALAMOUNT']) + floatval($value1['attributes']['DISCOUNTAMOUNT']) + floatval($value1['attributes']['ADJUSTAMOUN']))/100;
					$result["$i"]["$j"]['PPYAMOUNT'] = (floatval($value1['attributes']['PPYAMOUNT']))/100;
					$result["$i"]["$j"]['UNPAYMENTCHARGE'] =  $result["$i"]["$j"]['AMOUNT'] - $result["$i"]["$j"]['PPYAMOUNT'];
					$j++;
				}elseif($value1['tag'] == 'BILLINFO' && !isset($value1['attributes']) ){
					$i++;
					$j = 0;
				}
			}
			//$result['msy'] = 123;
			//echo $this->json($result);
			echo $this->json($result);
		}else{
			echo $this->json($result);
		}
	}
	
	public function queryCharge(){
		$accountNo = $_POST['accountNo'];
		$result = array();
		//echo $phone!=null ;
		$TOKEN_URL="http://36.250.88.58:8085/ppjj/tvPpjj.action?method=getCharge&accountNo=$accountNo";
		$this->qzgdwlSaveLog("缴费记录查询：".$_POST['accountNo']);
		$values = $this->getBoss($TOKEN_URL);
		$TOKEN_URL1 ="http://36.250.88.58:8085/ppjj/tvPpjj.action?method=getAcct&accountNo=$accountNo";
		$values1 = $this->getBoss($TOKEN_URL1);
		if($values[0]['attributes']['RETURN-CODE'] == 0) {			
			$i = 0;
			//OFFICE PAYTIME AMOUNT PAYMENTTYPE APPPAYSTATUS APPPAYTYPE
			foreach($values as $value1){
				if($value1['tag'] == 'PAYMENTINFO') {
					$result["$i"]['OFFICE'] = $value1['attributes']['OFFICE'];
					$result["$i"]['PAYTIME'] = $value1['attributes']['PAYTIME'];
					$result["$i"]['AMOUNT'] = $value1['attributes']['AMOUNT'];
					$result["$i"]['PAYMENTTYPE'] = $value1['attributes']['PAYMENTTYPE'];
					$result["$i"]['APPPAYSTATUS'] = $value1['attributes']['APPPAYSTATUS'];
					$result["$i"]['APPPAYTYPE'] = $value1['attributes']['APPPAYTYPE'];
					$result["$i"]['BALANCE'] = $values1[1]['attributes']['BALANCE'];					
					$i++;
				}
			}				
			echo $this->json($result);
		}else{
			echo $this->json($result);
		}
	}
	
	public function queryBusiness(){
		$custCode = $_POST['custCode'];
		$startDate = $_POST['startDate'];
		$endDate = $_POST['endDate'];
		$result = array();
		//echo $phone!=null ;
		$TOKEN_URL="http://36.250.88.58:8085/ppjj/tvPpjj.action?method=getBusiness&custcode=$custCode&beginDate=$startDate&endDate=$endDate";
		$this->qzgdwlSaveLog("业务办理查询：".$_POST['custCode']."  ".$startDate."  ".$endDate);
		$values = $this->getBoss($TOKEN_URL);
		if($values[0]['attributes']['RETURN-CODE'] == 0) {			
			$i = 0;
			//OFFICE PAYTIME AMOUNT PAYMENTTYPE APPPAYSTATUS APPPAYTYPE
			//SODATE BUSITYPE SOSTATUS OUTFLAG OPERNAME ORGNAME
			foreach($values as $value1){
				if($value1['tag'] == 'ORDERINFO') {
					$result["$i"]['SODATE'] = $value1['attributes']['SODATE'];
					$result["$i"]['BUSITYPE'] = $value1['attributes']['BUSITYPE'];
					$result["$i"]['SOSTATUS'] = $value1['attributes']['SOSTATUS'];
					$result["$i"]['OUTFLAG'] = $value1['attributes']['OUTFLAG'];
					$result["$i"]['OPERNAME'] = $value1['attributes']['OPERNAME'];
					$result["$i"]['ORGNAME'] = $value1['attributes']['ORGNAME'];				
					$i++;
				}
			}				
			echo $this->json($result);
		}else{
			echo $this->json($result);
		}
	}
	
	public function qzgdwlRefreshOrder(){
		$card_no = $_POST['card_no'];
		$prodInstId = $_POST['prodInstId'];
		$opType = $_POST['opType'];
		$result = array();
		//echo $phone!=null ;
		$TOKEN_URL="http://36.250.88.58:8085/ppjj/tvPpjj.action?method=refreshOrder&prodInstId=$prodInstId&opType=$opType";
		$this->qzgdwlSaveLog("刷新授权：".$card_no."  ".$opType);
		$values = $this->getBoss($TOKEN_URL);
		if($values[0]['attributes']['RETURN-CODE'] == 0) {			
			$i = 0;
			//OFFICE PAYTIME AMOUNT PAYMENTTYPE APPPAYSTATUS APPPAYTYPE
			//SODATE BUSITYPE SOSTATUS OUTFLAG OPERNAME ORGNAME
			$result['code'] = 1;
			$result['msg'] = $values[0]['attributes']['RETURN-MESSAGE'];				
			echo $this->json($result);
		}else{
			$result['code'] = 0;
			$result['msg'] = $values[0]['attributes']['RETURN-MESSAGE'];
			echo $this->json($result);
		}
	}
	
	
	public function queryUser(){
		$custcode = $_POST['custcode'];
		$result = array();
		//echo $phone!=null ;
		$TOKEN_URL="http://36.250.88.58:8085/ppjj/tvPpjj.action?method=getUser&custcode=$custcode";
		$this->qzgdwlSaveLog("用户信息查询：".$_POST['custcode']);
		$values = $this->getBoss($TOKEN_URL);
		if($values[0]['attributes']['RETURN-CODE'] == 0) {			
			$i = 0;
			$j = 0;
			foreach($values as $value1){
				if(isset($value1['attributes']['SUBSCRIBERID']) && $value1['attributes']['PRODSPECID'] == '800200000001' ){	   
					$result["$i"]['SUBSCRIBERID'] = $value1['attributes']['SUBSCRIBERID'];			
					$result["$i"]['SUBRELATIONTYPETITLE'] = $value1['attributes']['SUBRELATIONTYPETITLE'];
					$result["$i"]['SUBSCRIBERSTATUSSTR'] = $value1['attributes']['SUBSCRIBERSTATUSSTR'];
					$result["$i"]['LOGINNAME'] = $value1['attributes']['LOGINNAME'];
					$result["$i"]['PASSWORD'] = $value1['attributes']['PASSWORD'];
					$result["$i"]['STBNO'] = $value1['attributes']['STBNO'];
					$result["$i"]['CARDNO'] = $value1['attributes']['CARDNO'];		
					$result["$i"]['RESCODE'] = "";	
					//$i++;
				}elseif(isset($value1['attributes']['SUBSCRIBERID']) && $value1['attributes']['PRODSPECID'] == '800200000003'){
					$result["$i"]['SUBSCRIBERID'] = $value1['attributes']['SUBSCRIBERID'];			
					$result["$i"]['SUBRELATIONTYPETITLE'] = "宽带";
					$result["$i"]['SUBSCRIBERSTATUSSTR'] = $value1['attributes']['SUBSCRIBERSTATUSSTR'];
					$result["$i"]['LOGINNAME'] = $value1['attributes']['LOGINNAME'];
					$result["$i"]['PASSWORD'] = $value1['attributes']['PASSWORD'];
					$result["$i"]['STBNO'] = "";
					$result["$i"]['CARDNO'] = "";		
					$result["$i"]['RESCODE'] = "";	
					//$i++;
				}elseif(isset($value1['attributes']['RESTYPE']) && $value1['attributes']['RESTYPE'] == '机顶盒'){	
					if((isset($values[$j+1]['attributes']['RESTYPE']) && $values[$j+1]['attributes']['RESTYPE'] != "智能卡") || (isset($values[$j+2]['attributes']['RESTYPE']) && $values[$j+2]['attributes']['RESTYPE'] != "智能卡"))
						$result["$i"]['RESCODE'] = $value1['attributes']['RESCODE'];
					else{
						$result["$i"]['RESCODE'] = $value1['attributes']['RESCODE'];
						$i++;
					}
				}elseif(isset($values[$j-1]['attributes']['SUBSCRIBERID']) && isset($value1['attributes']['RESTYPE']) && ($value1['attributes']['RESTYPE'] == 'Cable Modem' || $value1['attributes']['RESTYPE'] == 'EOC')){			
					$result["$i"]['RESCODE'] = $value1['attributes']['RESCODE'];
					$result["$i"]['STBNO'] = $value1['attributes']['RESEQUNO'];
					$i++;
				}elseif(!isset($values[$j-1]['attributes']['SUBSCRIBERID']) && isset($value1['attributes']['RESTYPE']) && ($value1['attributes']['RESTYPE'] == 'Cable Modem' || $value1['attributes']['RESTYPE'] == 'EOC')){			
					if(isset($values[$j-1]['attributes']['RESTYPE'])){
						$result["$i"]['STBNO'] = $result["$i"]['STBNO'].' / '.$value1['attributes']['RESEQUNO'];	
						$result["$i"]['RESCODE'] = $result["$i"]['RESCODE'].' / '.$value1['attributes']['RESCODE'];
					}else{
						$result["$i"]['STBNO'] = $value1['attributes']['RESEQUNO'];			
						$result["$i"]['RESCODE'] = $value1['attributes']['RESCODE'];
					}
					$i++;
				}
				$j++;
			}
			//$result['msy'] = 123;
			//echo $this->json($result);
			echo $this->json($result);
		}else{
			echo $this->json($result);
		}
	}
	
	public function ywyLogin(){		
		$unionid = $_GET['unionid'];
		$openid = $_GET['openid'];
		if($unionid){
			$_SESSION['unionid'] = $unionid;
		}
		if($openid){
			$_SESSION['openid'] = $openid;
		}
		$ywy = M('Ywy');
		$unionidArr = array();
		$unionidArr['unionid'] = $unionid;
		$ywyRow = $ywy->where($unionidArr)->select();
		//echo $unionid;
		if($ywyRow && $unionid){
			if($ywyRow[0]['is_confirm'] == 1){
				$_SESSION['own_org_id'] = $ywyRow[0]['own_org_id'];				
				$this->qzgdwlSaveLog("登录");
				$this->display(C('HOME_DEFAULT_THEME').':ywyIndex');
			}else{
				$this->display(C('HOME_DEFAULT_THEME').':ywyLogin');
			}
			//echo 123;
		}else{
			$this->display(C('HOME_DEFAULT_THEME').':ywyLogin');
		}
	}
	
	public function qzgdwlUnBinding(){
		$result = array();
		$unionidArr = array();
		$unionid = $_SESSION['unionid'];
		$openid = $_SESSION['openid'];
		$unionidArr['unionid'] = $unionid;
		$user = M('User');
		$userRow = $user->where($unionidArr)->select();
		
		if($userRow && $userRow[0]['binding_num'] == 1){
			$user->where($unionidArr)->save(array('is_binding'=>0,'binding_num'=> intval($userRow[0]['binding_num']) - 1));			
			$this->qzgdwlSaveLog("解绑成功");
			M('Usermore')->where(array('unionid'=>$unionid,'cust_code'=>$userRow[0]['cust_code']))->delete();
			$result['CODE'] = 1;
			$share_bindingRow = M('Share_binding_ywy')->where(array('unionidnext'=>$unionid))->select();
			$shareBindingYwyRow = M('Share_binding_ywy')->where(array('unionidnext'=>$unionid))->save(array('cust_code'=>array('exp','null'),'cust_name'=>array('exp','null'),'create_date'=>date('Y-m-d H:i:s',time())));
			M('Share_code_ywy')->where(array('sharecode'=>$share_bindingRow[0]['sharecode']))->setInc('score',-1);
		}elseif($userRow && $userRow[0]['binding_num'] > 1){
					
			M('Usermore')->where(array('unionid'=>$unionid,'cust_code'=>$userRow[0]['cust_code']))->delete();
			$arrCondition = array();
			$arrCondition['unionid'] = $unionid;
			//$userRow = M('User')->where($arrCondition)->select();
			$userMoreRow = M('Usermore')->where(array('unionid'=>$unionid))->select()[0];
			if($userMoreRow){
				$date = array();
				$data['cust_code'] = $userMoreRow['cust_code'];
				$data['cust_name'] = $userMoreRow['cust_name'];
				$data['cust_prop'] = $userMoreRow['cust_prop'];
				$data['address'] = $userMoreRow['address'];
				$data['phone1'] = $userMoreRow['phone1'];
				$data['phone2'] = $userMoreRow['phone2'];
				$data['mobile1'] = $userMoreRow['mobile1'];
				$data['mobile2'] = $userMoreRow['mobile2'];
				$data['accountno'] = $userMoreRow['accountno'];
				$data['createdate'] = $userMoreRow['createdate'];
				$data['createorg'] = $userMoreRow['createorg'];
				$data['ownorgid'] = $userMoreRow['ownorgid'];
				$data['parentMgrAddrId'] = $userMoreRow['parentMgrAddrId'];	
				$data['mgrAddrid'] = $userMoreRow['mgrAddrid'];
				$data['binding_num'] = intval($userRow[0]['binding_num']) - 1;
				M('User')->where($arrCondition)->save($data);
				$cust_code = $data['cust_code'];
				$this->qzgdwlSaveLog("解绑，切换账号至$cust_code");
			}
			$result['CODE'] = 2;
		}else{
			$result['CODE'] = 44;
		}
		echo $this->json($result);
	}
	
	public function ywyLoginConfirm(){	
		$result = array();
		//$boss_no = $_POST['boss_no'];
		$boss_name = $_POST['boss_name'];
		$phone = $_POST['phone'];
		$unionidArr = array();
		//$unionidArr['boss_no'] = $boss_no;
		$unionidArr['boss_name'] = $boss_name;
		$unionidArr['phone'] = $phone;
		$ywy = M('Ywy');
		$ywyRow = $ywy->where($unionidArr)->select();
		if($ywyRow && $_SESSION['unionid']){
			if($ywyRow[0]['is_confirm'] != "1"){
				$ywy->where($unionidArr)->save(array('is_confirm'=>1,'unionid'=>$_SESSION['unionid'],'openid'=>$_SESSION['openid']));
				$result['CODE'] = 1;
				$_SESSION['own_org_id'] = $ywyRow[0]['own_org_id'];				
				$this->qzgdwlSaveLog("登录成功");
			}else{
				$result['CODE'] = 2;
			}
			echo $this->json($result);
		}elseif(!$_SESSION['unionid']){
			$result['CODE'] = 0;	
			echo $this->json($result);
		}else{
			$result['CODE'] = 99;	
			echo $this->json($result);
		}
	}
	
	function qzgdwlSearchInforByCardno(){
		//echo strlen('陈龙腾');
		$result = array();
		$unionid = $_SESSION['unionid'];
		$openid = $_SESSION['openid'];
		$this->assign("unionid",$unionid);
		$cardno = $_POST['cardno'];
		
		$TOKEN_URL="http://36.250.88.58:8085/ppjj/tvPpjj.action?method=getCustCode&cardno=$cardno&address=$address&mac=$mac&stbno=$stbno&cust_name=$cust_name&cert_no=$cert_no&phone=$phone";
		$values = $this->getBoss($TOKEN_URL);
		if($values[0]['attributes']['RETURN-CODE'] == 0) {
			$cust_no = $values[1]['attributes']['CUSTCODE'];
			$TOKEN_URL="http://36.250.88.58:8085/ppjj/tvPpjj.action?method=loginBossByName&custcode=$cust_no";
			$values = $this->getBoss($TOKEN_URL);
			$result['cust_code'] = $values[1]['attributes']['CUSTOMERCODE'];
			if(mb_strlen($values[1]['attributes']['CUSTOMERNAME'],"utf-8") >= 3){
				$result['cust_name'] = mb_substr($values[1]['attributes']['CUSTOMERNAME'],0,2,"utf-8") . "*";
			}else{
				$result['cust_name'] = mb_substr($values[1]['attributes']['CUSTOMERNAME'],0,1,"utf-8") . "*";				
			}
			//$result['cust_name'] = mb_strlen('陈龙腾',"utf-8");//mb_substr($values[1]['attributes']['CUSTOMERNAME'],0,2,"utf-8") . "*";
			//$result['address'] = $values[1]['attributes']['ADDRESS'];
			
			$addressArr = explode("|",$values[1]['attributes']['ADDRESS']);
			$count = count($addressArr);
			$result['address'] =  '****'.$addressArr[$count-2].$addressArr[$count-1];
			
			if($values[1]['attributes']['MOBILE1']){
				$result['mobile1'] = substr($values[1]['attributes']['MOBILE1'],0,strlen($values[1]['attributes']['MOBILE1'])-4).'****';
			}else{
				$result['mobile1'] = $values[1]['attributes']['MOBILE1'];
			}
			$result['accountno'] = $values[1]['attributes']['ACCOUNTID'];	
			$result['cardno'] = $cardno;
			$acctNo = $result['accountno'];
			//$TOKEN_URL="http://36.250.88.58:8085/ppjj/tvPpjj.action?method=getAcct&accountNo=$acctNo";		
			//$values = $this->getBoss($TOKEN_URL);
			//$result['balance']  = $values[1]['attributes']['BALANCE'];	
			$result['code'] = 0;
		}else{
			$result['code'] = 1;
		}
		echo $this->json($result);
	}
	
	function qzgdwlScanSure(){
		$haveBind = 0;
		$result = array();
		$unionid = $_SESSION['unionid'];
		$openid = $_SESSION['openid'];
		$source = $_POST['source'];
		$cust_no = $_POST['cust_no'];
		
			
		$isUser = $this->isUser(array('unionid'=>$unionid,'is_binding'=>1));
		$isMore = $this->isMore($cust_no);
		if(count($isMore) > 3){
			$result['code'] = 2;
			$result['msg'] = "抱歉，该账号已被绑定！";
		}elseif(count($isUser)>=3){
			$result['code'] = 2;
			$result['msg'] = "抱歉，最多只能绑定三个账号！";
		}elseif($isUser){
			foreach($isUser as $isU){
				if($isU['cust_code'] == $cust_no){
					$haveBind = 1;
					break;
				}
			}
			if($haveBind == 1){
				$result['code'] = 2;
				$result['msg'] = "您已绑定当前账户，请勿重复！";
			}else{								
							
				$userMore = M('Usermore');				
				$data = array();			
				$unionidArr = array();
				$unionidArr['unionid'] = $unionid;	
				$TOKEN_URL="http://36.250.88.58:8085/ppjj/tvPpjj.action?method=loginBossByName&custcode=$cust_no";
				$values = $this->getBoss($TOKEN_URL);
				if(strlen($values[1]['attributes']['CUSTOMERCODE']) == 12){
					$data['cust_code'] = $values[1]['attributes']['CUSTOMERCODE'];
					$data['cust_name'] = $values[1]['attributes']['CUSTOMERNAME'];
					$data['cust_prop'] = $values[1]['attributes']['CUSTPROP'];
					$data['address'] = $values[1]['attributes']['ADDRESS'];
					$data['phone1'] = $values[1]['attributes']['PHONE1'];
					$data['phone2'] = $values[1]['attributes']['PHONE2'];
					$data['mobile1'] = $values[1]['attributes']['MOBILE1'];
					$data['mobile2'] = $values[1]['attributes']['MOBILE2'];
					$data['accountno'] = $values[1]['attributes']['ACCOUNTID'];	
					$data['createdate'] = $values[1]['attributes']['CREATEDATE'];
					$data['createorg'] = $values[1]['attributes']['CREATEORG'];	
					$data['ownorgid'] = $values[1]['attributes']['OWNORGID'];
					$data['parentMgrAddrId'] = $values[1]['attributes']['PARENTMGRADDRID'];	
					$data['mgrAddrid'] = $values[1]['attributes']['MGRADDRID'];
					$data['bind_time'] = date('Y-m-d H:i:s',time());			
					$data['is_binding'] = 1;
					$data['source'] = $source;
					$data['unionid'] = $unionid;
					$data['subscribe_time'] = date('Y-m-d H:i:s',time());
					$userMore->data($data)->add();
					
					$user = M('User');
					$userRow = $user->where($unionidArr)->select();
					$user->where($unionidArr)->setInc('binding_num',1);
					//$user->where($unionidArr)->save(array('binding_num'=>intval($userRow[0]['binding_num']) + 1));
					
					$result['code'] = 0;
					$result['msg'] = "新客户编号绑定成功！";
				}else{
					$this->qzgdwlSaveLog("系统无法获取该账号！".$cust_no);
					$result['code'] = 2;
					$result['msg'] = "系统无法获取该账号，请重试一下！";
				}
			}			
		}else{
				
			//echo "登录成功";
			$user = M('User');				
			$data = array();			
			$unionidArr = array();
			$unionidArr['unionid'] = $unionid;
			$userRow = $user->where($unionidArr)->select();
			//if($userRow){
			$TOKEN_URL="http://36.250.88.58:8085/ppjj/tvPpjj.action?method=loginBossByName&custcode=$cust_no";
			$values = $this->getBoss($TOKEN_URL);
			if(strlen($values[1]['attributes']['CUSTOMERCODE']) == 12){
				$data['cust_code'] = $values[1]['attributes']['CUSTOMERCODE'];
				$data['binding_num'] = 1;
				$data['cust_name'] = $values[1]['attributes']['CUSTOMERNAME'];
				$data['cust_prop'] = $values[1]['attributes']['CUSTPROP'];
				$data['address'] = $values[1]['attributes']['ADDRESS'];
				$data['phone1'] = $values[1]['attributes']['PHONE1'];
				$data['phone2'] = $values[1]['attributes']['PHONE2'];
				$data['mobile1'] = $values[1]['attributes']['MOBILE1'];
				$data['mobile2'] = $values[1]['attributes']['MOBILE2'];
				$data['accountno'] = $values[1]['attributes']['ACCOUNTID'];	
				$data['createdate'] = $values[1]['attributes']['CREATEDATE'];
				$data['createorg'] = $values[1]['attributes']['CREATEORG'];	
				$data['ownorgid'] = $values[1]['attributes']['OWNORGID'];
				$data['parentMgrAddrId'] = $values[1]['attributes']['PARENTMGRADDRID'];	
				$data['mgrAddrid'] = $values[1]['attributes']['MGRADDRID'];
				$data['bind_time'] = date('Y-m-d H:i:s',time());			
				$data['is_binding'] = 1;
				$user->where($unionidArr)->save($data);
				//$this->display(C('HOME_DEFAULT_THEME').':bindSuc');
							
				$data['unionid'] = $unionid;
				M('Usermore')->data($data)->add();
				
				$arrShare = array();
				$arrShare['unionidnext'] = $unionid;
				$arrShare['cust_code']=array('EXP','IS NULL');
				$share_binding = M('Share_binding_ywy');
				$share_bindingRow = $share_binding->where($arrShare)->order('id desc')->select();
				if($share_bindingRow){
					$arrShare['sharecode'] = $share_bindingRow[0]['sharecode'];
					$share_bindingRow1 = $share_binding->where($arrShare)->save(array('cust_code'=>$values[1]['attributes']['CUSTOMERCODE'],'cust_name'=>$values[1]['attributes']['CUSTOMERNAME'],'create_date'=>date('Y-m-d H:i:s',time())));
					if($share_bindingRow1){
						M('Share_code_ywy')->where(array('sharecode'=>$share_bindingRow[0]['sharecode']))->setInc('score',1);
						//$this->bindingShare($share_bindingRow[0]['unionid'],$share_bindingRow[0]['unionidnext']);
					}
				}else{
					//$this->bindingShare('',$unionid);
				}
				
				$result['code'] = 0;
				$result['msg'] = "绑定成功！";	
			}else{
				$this->qzgdwlSaveLog("系统无法获取该账号！".$cust_no);
				$result['code'] = 2;
				$result['msg'] = "系统无法获取该账号，请重试一下！";
			}
		}
		
		echo $this->json($result);
			
	}
	
	public function ajjIndex(){		
		$this->display(C('HOME_DEFAULT_THEME').':ajjIndex');
	}
	
	public function isUser($arrCondition){
		$user = M('Usermore');
		$userRow = $user->where($arrCondition)->select();//array('unionid'=>$_GET['unionid'],'is_binding'=>1)
		return $userRow;
	}
	
	private function isMore($cust_code){
		$user = M('Usermore');
		$userRow = $user->where(array('cust_code'=>$cust_code))->select();//array('unionid'=>$_GET['unionid'],'is_binding'=>1)
		return $userRow;
		/*$binding = M('Binding_num');
		$bindingRow = $binding->where(array('cust_code'=>$cust_code))->select();
		if($bindingRow){
			if($bindingRow[0]['num'] > 1){
				return 2;
			}else{
				return 1;
			}
		}else{
			return 0;
		}*/
	}
	public function post_check($post){     
		if (!get_magic_quotes_gpc()) // 判断magic_quotes_gpc是否为打开     
		{     
			$post = addslashes($post); // 进行magic_quotes_gpc没有打开的情况对提交数据的过滤     
		}     
		$post = str_replace("_", "\_", $post); // 把 '_'过滤掉     
		$post = str_replace("select", "\_", $post); // 把 '_'过滤掉     
		$post = str_replace("insert", "\_", $post); // 把 '_'过滤掉     
		$post = str_replace("update", "\_", $post); // 把 '_'过滤掉     
		$post = str_replace("delete", "\_", $post); // 把 '_'过滤掉     
		$post = str_replace("%", "\%", $post); // 把' % '过滤掉     
		$post = nl2br($post); // 回车转换     
		$post= htmlspecialchars($post); // html标记转换        
		return $post;     
    }   
	
	public function loginSubmit(){
		$result = array();
		$haveBind = 0;//判断是否有绑定记录
		$unionid = $_SESSION['unionid'];
		$openid = $_SESSION['openid'];
		
		$cust_no = $this->post_check($_POST['cust_no']);
		$pwd = $this->post_check($_POST['pwd']);
		$isUser = $this->isUser(array('unionid'=>$unionid,'is_binding'=>1));
		
		$isMore = $this->isMore($cust_no);
		if(count($isMore) > 3){
			$result['code'] = 2;
			$result['msg'] = "抱歉，该账号已被绑定！";
		}elseif(count($isUser)>=3){
			$result['code'] = 2;
			$result['msg'] = "抱歉，最多只能绑定三个账号！";
		}elseif($isUser){
			foreach($isUser as $isU){
				if($isU['cust_code'] == $cust_no){
					$haveBind = 1;
					break;
				}
			}
			if($haveBind == 1){
				$result['code'] = 2;
				$result['msg'] = "您已绑定当前账户，请勿重复！";
			}else{
				$TOKEN_URL="http://36.250.88.58:8085/ppjj/tvPpjj.action?method=loginBossByPwd&custcode=$cust_no&pwd=$pwd";
				$values = $this->getBoss($TOKEN_URL);
				if($values[0]['attributes']['RETURN-CODE'] == 0) {	
	
				
					$userMore = M('Usermore');				
					$data = array();			
					
					$TOKEN_URL="http://36.250.88.58:8085/ppjj/tvPpjj.action?method=loginBossByName&custcode=$cust_no";
					$values = $this->getBoss($TOKEN_URL);
					if(strlen($values[1]['attributes']['CUSTOMERCODE']) == 12){
						
						$data['cust_code'] = $values[1]['attributes']['CUSTOMERCODE'];
						$data['cust_name'] = $values[1]['attributes']['CUSTOMERNAME'];
						$data['cust_prop'] = $values[1]['attributes']['CUSTPROP'];
						$data['address'] = $values[1]['attributes']['ADDRESS'];
						$data['phone1'] = $values[1]['attributes']['PHONE1'];
						$data['phone2'] = $values[1]['attributes']['PHONE2'];
						$data['mobile1'] = $values[1]['attributes']['MOBILE1'];
						$data['mobile2'] = $values[1]['attributes']['MOBILE2'];
						$data['accountno'] = $values[1]['attributes']['ACCOUNTID'];	
						$data['createdate'] = $values[1]['attributes']['CREATEDATE'];
						$data['createorg'] = $values[1]['attributes']['CREATEORG'];	
						$data['ownorgid'] = $values[1]['attributes']['OWNORGID'];
						$data['parentMgrAddrId'] = $values[1]['attributes']['PARENTMGRADDRID'];	
						$data['mgrAddrid'] = $values[1]['attributes']['MGRADDRID'];
						$data['bind_time'] = date('Y-m-d H:i:s',time());			
						$data['is_binding'] = 1;					
						$data['unionid'] = $unionid;
						$data['subscribe_time'] = date('Y-m-d H:i:s',time());
						$userMore->data($data)->add();
											
						$unionidArr = array();
						$unionidArr['unionid'] = $unionid;
						$user = M('User');
						//$userRow = $user->where($unionidArr)->select();
						$user->where($unionidArr)->setInc('binding_num',1);//->save(array('binding_num'=>intval($userRow[0]['binding_num'] )+ 1));	
						
						$result['code'] = 0;
						$result['msg'] = "新客户编号绑定成功！";
					}else{
						$this->qzgdwlSaveLog("系统无法获取该账号！".$cust_no);
						$result['code'] = 2;
						$result['msg'] = "系统无法获取该账号，请重试一下！";
					}
				}else{
					$result['code'] = 1;
					$result['msg'] = "客户编号或服务密码错误！";
				}			
			}			
		}else{
			$TOKEN_URL="http://36.250.88.58:8085/ppjj/tvPpjj.action?method=loginBossByPwd&custcode=$cust_no&pwd=$pwd";
			$values = $this->getBoss($TOKEN_URL);
			if($values[0]['attributes']['RETURN-CODE'] == 0) {		
				//echo "登录成功";
				$user = M('User');				
				$data = array();			
				$unionidArr = array();
				$unionidArr['unionid'] = $unionid;
				$userRow = $user->where($unionidArr)->select();
				//if($userRow){
				$TOKEN_URL="http://36.250.88.58:8085/ppjj/tvPpjj.action?method=loginBossByName&custcode=$cust_no";
				$values = $this->getBoss($TOKEN_URL);
				if(strlen($values[1]['attributes']['CUSTOMERCODE']) == 12){
					$data['cust_code'] = $values[1]['attributes']['CUSTOMERCODE'];
					$data['binding_num'] = 1;
					$data['cust_name'] = $values[1]['attributes']['CUSTOMERNAME'];
					$data['cust_prop'] = $values[1]['attributes']['CUSTPROP'];
					$data['address'] = $values[1]['attributes']['ADDRESS'];
					$data['phone1'] = $values[1]['attributes']['PHONE1'];
					$data['phone2'] = $values[1]['attributes']['PHONE2'];
					$data['mobile1'] = $values[1]['attributes']['MOBILE1'];
					$data['mobile2'] = $values[1]['attributes']['MOBILE2'];
					$data['accountno'] = $values[1]['attributes']['ACCOUNTID'];	
					$data['createdate'] = $values[1]['attributes']['CREATEDATE'];
					$data['createorg'] = $values[1]['attributes']['CREATEORG'];	
					$data['ownorgid'] = $values[1]['attributes']['OWNORGID'];
					$data['parentMgrAddrId'] = $values[1]['attributes']['PARENTMGRADDRID'];	
					$data['mgrAddrid'] = $values[1]['attributes']['MGRADDRID'];
					$data['bind_time'] = date('Y-m-d H:i:s',time());			
					$data['is_binding'] = 1;
					$user->where($unionidArr)->save($data);
					//$this->display(C('HOME_DEFAULT_THEME').':bindSuc');
								
					$data['unionid'] = $unionid;
					M('Usermore')->data($data)->add();
					
					$arrShare = array();
					$arrShare['unionidnext'] = $unionid;
					$arrShare['cust_code']=array('EXP','IS NULL');
					$share_binding = M('Share_binding_ywy');
					$share_bindingRow = $share_binding->where($arrShare)->order('id desc')->select();
					//$arrShare['unionid'] = $share_bindingRow[0]['unionid'];
					if($share_bindingRow){
						$share_bindingRow1 = $share_binding->where($arrShare)->save(array('cust_code'=>$values[1]['attributes']['CUSTOMERCODE'],'cust_name'=>$values[1]['attributes']['CUSTOMERNAME'],'create_date'=>date('Y-m-d H:i:s',time())));
						if($share_bindingRow1){
							M('Share_code_ywy')->where(array('sharecode'=>$share_bindingRow[0]['sharecode']))->setInc('score',1);
							//$this->bindingShare($share_bindingRow[0]['unionid'],$share_bindingRow[0]['unionidnext']);
						}
					}else{
						//$this->bindingShare('',$unionid);
					}
					$result['code'] = 0;
					$result['msg'] = "绑定成功！";
				}else{
					$this->qzgdwlSaveLog("系统无法获取该账号！".$cust_no);
					$result['code'] = 2;
					$result['msg'] = "系统无法获取该账号，请重试一下！";
				}
			}else{
				$result['code'] = 1;
				$result['msg'] = "客户编号或服务密码错误！";
			}	
		}
		echo $this->json($result);
    }
	
	public function loginSubmitNew(){
		$result = array();
		$haveBind = 0;//判断是否有绑定记录
		$unionid = $_SESSION['unionid'];
		$openid = $_SESSION['openid'];
		
		$cust_code = $this->post_check($_POST['cust_code']);
		$cust_name = $this->post_check($_POST['cust_name']);
		$phone = $this->post_check($_POST['phone']);
		$verCode = $this->post_check($_POST['verCode']);
		
		
			
		$isUser = $this->isUser(array('unionid'=>$unionid,'is_binding'=>1));
		
		//判断验证码是否正确
        
		$result['verCode'] = "验证码正确！";
		$isMore = $this->isMore($cust_code);
		if(count($isMore) > 3){
			$result['code'] = 2;
			$result['msg'] = "抱歉，该账号已被绑定！";
		}elseif(count($isUser)>=3){
			$result['code'] = 2;
			$result['msg'] = "抱歉，最多只能绑定三个账号！";
		}elseif($isUser){
			foreach($isUser as $isU){
				if($isU['cust_code'] == $cust_code){
					$haveBind = 1;
					break;
				}
			}
			if($haveBind == 1){
				$result['code'] = 2;
				$result['msg'] = "您已绑定当前账户，请勿重复！";
			}else{
				//$TOKEN_URL="http://36.250.88.58:8085/ppjj/tvPpjj.action?method=loginBossByPwd&custcode=$cust_no&pwd=$pwd";
				//$values = $this->getBoss($TOKEN_URL);
				//if($values[0]['attributes']['RETURN-CODE'] == 0) {	
				
				
				$userMore = M('Usermore');				
				$data = array();			
				
				$TOKEN_URL="http://36.250.88.58:8085/ppjj/tvPpjj.action?method=loginBossByName&custcode=$cust_code";
				$values = $this->getBoss($TOKEN_URL);
				
				if(strlen($values[1]['attributes']['CUSTOMERCODE']) == 12){
					
					$data['cust_code'] = $values[1]['attributes']['CUSTOMERCODE'];
					$data['cust_name'] = $values[1]['attributes']['CUSTOMERNAME'];
					if($cust_name != $data['cust_name']){
						$this->qzgdwlSaveLog("客户姓名输入错误！".$cust_no);
						$result['code'] = 4;
						$result['msg'] = "客户姓名输入错误，请重新输入！";
					}elseif(!$this->checkVerifyNo($phone,$verCode)){
						$result['code'] = 4;
						$result['msg'] = "抱歉，验证码错误！";
						$result['verCode'] = "验证码错误！";
					}else{
						$data['cust_prop'] = $values[1]['attributes']['CUSTPROP'];
						$data['address'] = $values[1]['attributes']['ADDRESS'];
						$data['phone1'] = $values[1]['attributes']['PHONE1'];
						$data['phone2'] = $values[1]['attributes']['PHONE2'];
						$data['mobile1'] = $values[1]['attributes']['MOBILE1'];
						$data['mobile2'] = $values[1]['attributes']['MOBILE2'];
						$data['accountno'] = $values[1]['attributes']['ACCOUNTID'];	
						$data['createdate'] = $values[1]['attributes']['CREATEDATE'];
						$data['createorg'] = $values[1]['attributes']['CREATEORG'];	
						$data['ownorgid'] = $values[1]['attributes']['OWNORGID'];
						$data['parentMgrAddrId'] = $values[1]['attributes']['PARENTMGRADDRID'];	
						$data['mgrAddrid'] = $values[1]['attributes']['MGRADDRID'];
						$data['bind_time'] = date('Y-m-d H:i:s',time());			
						$data['is_binding'] = 1;					
						$data['unionid'] = $unionid;
						$data['subscribe_time'] = date('Y-m-d H:i:s',time());
						$data['bind_msisdn'] = $phone;
						$userMore->data($data)->add();
											
						$unionidArr = array();
						$unionidArr['unionid'] = $unionid;
						$user = M('User');
						//$userRow = $user->where($unionidArr)->select();
						$user->where($unionidArr)->setInc('binding_num',1);//->save(array('binding_num'=>intval($userRow[0]['binding_num'] )+ 1));	
						
						$result['code'] = 0;
						$result['msg'] = "新客户编号绑定成功！";
						$result['verCode'] = "验证码正确！";
					}
					
				}else{
					$this->qzgdwlSaveLog("系统无法获取该账号！".$cust_no);
					$result['code'] = 2;
					$result['msg'] = "系统无法获取该账号，请重试一下！";
				}		
			}			
		}else{
			// $TOKEN_URL="http://36.250.88.58:8085/ppjj/tvPpjj.action?method=loginBossByPwd&custcode=$cust_no&pwd=$pwd";
			// $values = $this->getBoss($TOKEN_URL);
			// if($values[0]['attributes']['RETURN-CODE'] == 0) {		
				//echo "登录成功";
			   if(true){
				$user = M('User');				
				$data = array();			
				$unionidArr = array();
				$unionidArr['unionid'] = $unionid;
				$userRow = $user->where($unionidArr)->select();
				//if($userRow){
				$TOKEN_URL="http://36.250.88.58:8085/ppjj/tvPpjj.action?method=loginBossByName&custcode=$cust_code";
				$values = $this->getBoss($TOKEN_URL);
				if(strlen($values[1]['attributes']['CUSTOMERCODE']) == 12){
					$data['cust_code'] = $values[1]['attributes']['CUSTOMERCODE'];
					$data['binding_num'] = 1;
					$data['cust_name'] = $values[1]['attributes']['CUSTOMERNAME'];
					if($cust_name != $data['cust_name']){
						$this->qzgdwlSaveLog("客户姓名输入错误！".$cust_no);
						$result['code'] = 4;
						$result['msg'] = "客户姓名输入错误，请重新输入！";
					}elseif(!$this->checkVerifyNo($phone,$verCode)){
						$result['code'] = 4;
						$result['msg'] = "抱歉，验证码错误！";
						$result['verCode'] = "验证码错误！";
					}else{
						$data['cust_prop'] = $values[1]['attributes']['CUSTPROP'];
						$data['address'] = $values[1]['attributes']['ADDRESS'];
						$data['phone1'] = $values[1]['attributes']['PHONE1'];
						$data['phone2'] = $values[1]['attributes']['PHONE2'];
						$data['mobile1'] = $values[1]['attributes']['MOBILE1'];
						$data['mobile2'] = $values[1]['attributes']['MOBILE2'];
						$data['accountno'] = $values[1]['attributes']['ACCOUNTID'];	
						$data['createdate'] = $values[1]['attributes']['CREATEDATE'];
						$data['createorg'] = $values[1]['attributes']['CREATEORG'];	
						$data['ownorgid'] = $values[1]['attributes']['OWNORGID'];
						$data['parentMgrAddrId'] = $values[1]['attributes']['PARENTMGRADDRID'];	
						$data['mgrAddrid'] = $values[1]['attributes']['MGRADDRID'];
						$data['bind_time'] = date('Y-m-d H:i:s',time());			
						$data['is_binding'] = 1;
						$data['bind_msisdn'] = $phone;
						$user->where($unionidArr)->save($data);
						//$this->display(C('HOME_DEFAULT_THEME').':bindSuc');
									
						$data['unionid'] = $unionid;
						M('Usermore')->data($data)->add();
						
						$arrShare = array();
						$arrShare['unionidnext'] = $unionid;
						$arrShare['cust_code']=array('EXP','IS NULL');
						$share_binding = M('Share_binding_ywy');
						$share_bindingRow = $share_binding->where($arrShare)->order('id desc')->select();
						//$arrShare['unionid'] = $share_bindingRow[0]['unionid'];
						if($share_bindingRow){
							$share_bindingRow1 = $share_binding->where($arrShare)->save(array('cust_code'=>$values[1]['attributes']['CUSTOMERCODE'],'cust_name'=>$values[1]['attributes']['CUSTOMERNAME'],'create_date'=>date('Y-m-d H:i:s',time())));
							if($share_bindingRow1){
								M('Share_code_ywy')->where(array('sharecode'=>$share_bindingRow[0]['sharecode']))->setInc('score',1);
								//$this->bindingShare($share_bindingRow[0]['unionid'],$share_bindingRow[0]['unionidnext']);
							}
						}else{
							//$this->bindingShare('',$unionid);
						}
						$result['code'] = 0;
						$result['msg'] = "绑定成功！";
						$result['verCode'] = "验证码正确！";
					}
				}else{
					$this->qzgdwlSaveLog("系统无法获取该账号！".$cust_no);
					$result['code'] = 2;
					$result['msg'] = "系统无法获取该账号，请重试一下！";
				}
			}else{
				$result['code'] = 1;
				$result['msg'] = "客户编号或服务密码错误！";
			}	
		}		
		echo $this->json($result);
    }
	
	
	
	public function bindingShare($unionid,$unionidNext){
		
		
		$prize = M('Cltprize_sj');
		$resRow = $prize->where(array('id'=>11))->select();
		$res = $resRow[0];
		
		
		$user = M('User');
		$card = M('Card');
		
		if($unionid){
			$mpRow = M('Cltuserpraise_sj')->add(array('unionid'=>$unionid,'prizeid'=>$res['id'],'praisename'=>$res['praisename'],'date'=>date("Y-m-d H:i:s"),'isjf'=>$res['isjf'],'ispraise'=>$res['ispraise'],'isexchange'=>0));
			if($mpRow){
				if($res['ischarge'] == 1){
					M()->startTrans();//开启事务
					$cardArr['unionid']=array('EXP','IS NULL');
					$cardArr['card_value'] = intval($res['value']);//查询条件
					$cardArr['card_remark'] = '好友绑定红包';//查询条件				
					$idRow = $card->lock(true)->where($cardArr)->min('id');//加锁查询
					if($idRow){
						//执行你想进行的操作, 最后返回操作结果 result
						$userRow = $user->where(array('unionid'=>$unionid))->select();
						$cardRow = $card->where(array('id'=>$idRow))->save(array('unionid'=>$unionid,'cust_code'=>$userRow[0]['cust_code'],'cust_name'=>$userRow[0]['cust_name'],'accountno'=>$userRow[0]['accountno']));
						if(!$cardRow){
							M()->rollback();//回滚
							//$this->error('错误提示');
						}
					}
					M()->commit();//事务提交
				}
				
			}
			
			$prizeRow = $prize->where(array('id'=>$res['id']))->select();
			if($prizeRow[0]['praisenumber'] == -1){
				$numstore = -1;
			}else if($prizeRow[0]['praisenumber'] == 0){
				$numstore = 0;
			}else{
				$numstore = $prizeRow[0]['praisenumber']-1;
			}
			$prize->where(array('id'=>$res['id']))->save(array('praisenumber'=>$numstore));
		}
		
		$mpNextRow = M('Cltuserpraise_sj')->add(array('unionid'=>$unionidNext,'prizeid'=>$res['id'],'praisename'=>$res['praisename'],'date'=>date("Y-m-d H:i:s"),'isjf'=>$res['isjf'],'ispraise'=>$res['ispraise'],'isexchange'=>0));		
		if($mpNextRow){
			if($res['ischarge'] == 1){
				M()->startTrans();//开启事务
				$cardArr['unionid']=array('EXP','IS NULL');
				$cardArr['card_value'] = intval($res['value']);//查询条件
				if($unionid){
					$cardArr['card_remark'] = '好友绑定红包';//查询条件
				}else{
					$cardArr['card_remark'] = '个人绑定红包';//查询条件
				}
				$idRow = $card->lock(true)->where($cardArr)->min('id');//加锁查询
				if($idRow){
					//执行你想进行的操作, 最后返回操作结果 result
					$userRow = $user->where(array('unionid'=>$unionidNext))->select();
					$cardRow = $card->where(array('id'=>$idRow))->save(array('unionid'=>$unionidNext,'cust_code'=>$userRow[0]['cust_code'],'cust_name'=>$userRow[0]['cust_name'],'accountno'=>$userRow[0]['accountno']));
					if(!$cardRow){
						M()->rollback();//回滚
						//$this->error('错误提示');
					}
				}
				M()->commit();//事务提交
			}
			
		}
		
		$prizeRow = $prize->where(array('id'=>$res['id']))->select();
		if($prizeRow[0]['praisenumber'] == -1){
			$numstore = -1;
		}else if($prizeRow[0]['praisenumber'] == 0){
			$numstore = 0;
		}else{
			$numstore = $prizeRow[0]['praisenumber']-1;
		}
		$prize->where(array('id'=>$res['id']))->save(array('praisenumber'=>$numstore));
		
	}
	
	public function ywyCustConfirm(){
		if(isset($_POST['custCode'])){
			session('custCode',$_POST['custCode']);
		}
	}
	
	public function query(){
		$cust_name = $_POST['cust_name'];
		$address = $_POST['address'];
		$mac = $_POST['mac'];
		$stbno = $_POST['stbno'];
		$cardno = $_POST['cardno'];
		$cert_no = $_POST['cert_no'];
		$phone = $_POST['phone'];
		//echo $phone!=null ;
		
		$TOKEN_URL="http://36.250.88.58:8085/ppjj/tvPpjj.action?method=getCustCode&cardno=$cardno&address=$address&mac=$mac&stbno=$stbno&cust_name=$cust_name&cert_no=$cert_no&phone=$phone";
		$this->qzgdwlSaveLog("获取客户编号：cardno=$cardno&address=$address&mac=$mac&stbno=$stbno&cust_name=$cust_name&cert_no=$cert_no&phone=$phone");
		$values = $this->getBoss($TOKEN_URL);
		if($values[0]['attributes']['RETURN-CODE'] == 0) {
			$returnStr = "";
			$arrLen = count($values);
			for ($i = 1;$i < $arrLen-1; $i++){ 
				$returnStr .= "<div class=\"queryCust\">".$i.". 客户编号：<span id=\"custCode".$i."\" style=\"color:#CC0033\">".$values[$i]['attributes']['CUSTCODE']."</span><br>"."地址：".$values[$i]['attributes']['ADDRNAME']."<br>".
				"<div class=\"col-50\"><a href=\"#\" onclick=\"ywyCustConfirm(".$i.");\" class=\"weui_btn_sure\">确定</a></div>".
				"----------------------------------------------<br></div>";
			} 
			echo $returnStr;
		}else{
			echo "该客户不存在！";
		}
    }
	
	public function queryBill(){
		$accountNo = $_POST['accountNo'];
		$result = array();
		//echo $phone!=null ;
		$TOKEN_URL="http://36.250.88.58:8085/ppjj/tvPpjj.action?method=getBill&accountNo=$accountNo";
		$values = $this->getBoss($TOKEN_URL);
		if($values[0]['attributes']['RETURN-CODE'] == 0) {			
			$i = 0;
			foreach($values as $value1){
				if($value1['tag'] == 'BILLINFO' && isset($value1['attributes']) && $value1['attributes']['BILLCYCLE'] != date("Ym")){
					$result["$i"]['BILLCYCLE'] = $value1['attributes']['BILLCYCLE'];
					$result["$i"]['AMOUNT'] = (floatval($value1['attributes']['ORIGINALAMOUNT']) + floatval($value1['attributes']['DISCOUNTAMOUNT']) + floatval($value1['attributes']['ADJUSTAMOUN']))/100;
					$result["$i"]['PPYAMOUNT'] = (floatval($value1['attributes']['PPYAMOUNT']))/100;
					$result["$i"]['UNPAYMENTCHARGE'] = $result["$i"]['AMOUNT'] - $result["$i"]['PPYAMOUNT'];
					$i++;
				}
			}
			//$result['msy'] = 123;
			//echo $this->json($result);
			echo $this->json($result);
		}else{
			echo $this->json($result);
		}
	}
	
	public function queryAllInfor(){
		$custcode = $_POST['custcode'];
		$result = array();
		//echo $phone!=null ;
		$TOKEN_URL="http://36.250.88.58:8085/ppjj/tvPpjj.action?method=getAllInfro&custcode=$custcode";
		$values = $this->getBoss($TOKEN_URL);
		if($values[0]['attributes']['RETURN-CODE'] == 0) {			
			$i = 0;
			foreach($values as $value1){
				if(isset($value1['attributes']['SUBSCRIBERID']) ){
					$result["$i"]['SUBSCRIBERID'] = $value1['attributes']['SUBSCRIBERID'];
					$result["$i"]['STBNO'] = $value1['attributes']['STBNO'] == "" ? '宽带' : $value1['attributes']['STBNO'];
					$result["$i"]['SUBRELATIONTYPETITLE'] = $value1['attributes']['SUBRELATIONTYPETITLE'];
					$i++;
				}
			}
			//$result['msy'] = 123;
			//echo $this->json($result);
			echo $this->json($result);
		}else{
			echo $this->json($result);
		}
	}
	
	public function queryAllOrdering(){
		$SUBSCRIBERID = $_POST['SUBSCRIBERID'];
		$result = array();
		//echo $phone!=null ;
		$TOKEN_URL="http://36.250.88.58:8085/ppjj/tvPpjj.action?method=getOrding&subscriberNo=$SUBSCRIBERID";
		$this->qzgdwlSaveLog("用户订购查询：".$_POST['SUBSCRIBERID']);
		$values = $this->getBoss($TOKEN_URL);
		if($values[0]['attributes']['RETURN-CODE'] == 0) {			
			$i = 0;
			foreach($values as $value1){
				if(isset($value1['attributes']['OFFERID']) ){
					$result["$i"]['OFFERNAME'] = $value1['attributes']['OFFERNAME'];				
				}elseif(isset($value1['attributes']['PRODUCTID']) ){
					if(!isset($result["$i"]['OFFERNAME'])){
						$l = $i - 1;
						$result["$i"]['OFFERNAME'] = $result["$l"]['OFFERNAME'];
					}
					$result["$i"]['PRODUCTNAME'] = $value1['attributes']['PRODUCTNAME'];	
					$result["$i"]['SUBSTATUSSTR'] = $value1['attributes']['SUBSTATUSSTR'];	
					$result["$i"]['JOINDATE'] = substr($value1['attributes']['JOINDATE'],0,10);	
					$result["$i"]['VALIDDAYS'] = substr($value1['attributes']['VALIDDAYS'],0,10);	
					$result["$i"]['EXPIREDAYS'] = substr($value1['attributes']['EXPIREDAYS'],0,10);					
					$i++;
				}				
			}
			if($i == 0){
				$result = array();
			}
			//$result['msy'] = 123;
			//echo $this->json($result);
			echo $this->json($result);
		}else{
			echo $this->json($result);
		}
	}
	
	public function ywyOrdering(){
		
		$this->display(C('HOME_DEFAULT_THEME').':ywyOrdering');
    }
	
	public function test(){
		define("TOKEN", "ywkfzx");
        //echo $_GET["echostr"];        
		$wechatObj = new wechatCallbackapiTest();
    	if (!isset($_GET['echostr'])) {	
			//$access_token = $wechatObj->accessToken();
			//echo $access_token;
			//echo 123;
			$wechatObj->responseMsg();
		}else{
			//echo 333;
			$wechatObj->valid();
		}
    }
	
	public function getBoss($param){
		//$json=file_get_contents($param);
		//$result = urldecode($json);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $param);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); 
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE); 
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$output = curl_exec($ch);
		curl_close($ch);
		$result = urldecode($output);
		
		$data = str_replace("gb2312","utf-8",$result);
		$parser = xml_parser_create();//创建解析器
		xml_parse_into_struct($parser, $data, $values, $index);//解析到数组
		xml_parser_free($parser);//释放资源
		
		
		// print_r($values);
		// echo $values[0]['tag'];
		// echo json_encode($values[1],JSON_UNESCAPED_UNICODE);
		// echo json_decode($values);
		// echo "\n索引数组\n";
		// print_r($index);
		// echo "\n数据数组\n";
		// print_r($values);
		
		
		return $values;
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
	
	public function sendSMS(){
		
		// $result['code'] = 1;
		// $result['msg'] = "短信已发送！";
		// echo $this->json($result);
						
			
		$unionid = $_SESSION['unionid'];
		$openid = $_SESSION['openid'];
		$result = array();
		
		$phone = $_POST['phone'];
		
		$hasBind = $this->checkMsisdnHasBind($phone,$unionid,$openid);
		if($hasBind){
			
			$result['code'] = 5;
			$result['msg'] = "该手机号码已经被绑定！";
			echo $this->json($result);
			
		}else{
			
			$data = array();
			$data['unionid'] = $_SESSION['unionid'];
			
			if($_SESSION['unionid']){
				
												
						require_once "ServerAPI.php";
						$test = new ServerAPI('b71820caab4115163fccea2ebe6a1f6f','f21c592d1317','curl');
						$resultSMS = $test->sendSmsCode($phone,'',3064106,6);
						if($resultSMS['code'] == 200){
							$result['code'] = 1;
							$result['msg'] = "短信已发送！";
							//$this->agentSaveLog($phone."  修改密码短信已发送");
						}else{
							$result['code'] = 3;
							$result['msg'] = "短信发送失败！";
							$result['phone'] = $phone;
							//$this->agentSaveLog($phone."  修改密码短信发送失败！");
						}
						
						echo $this->json($result);
				
			}else{
				$result['code'] = 0;
				$result['msg'] = "操作超时！";
				echo $this->json($result);
			}
			
		}
		
				
		
	}
	
	public function checkMsisdnHasBind($phone,$unionid,$openid){
		$user = M('User');
		$unionidArr = array();
		$unionidArr['unionid'] = $unionid;
		$unionidArr['bind_msisdn'] = $phone;
		//$userRow = $user->where($unionidArr)->select();
		$userRow = $user->where("bind_msisdn='$phone' and unionid <> '$unionid' ")->select();
		if($userRow){
			return true;
		}else{
			
			return false;
		}
			
	}
	public function checkVerifyNo($phone,$verify_no){
		
		require_once "ServerAPI.php";
		$test = new ServerAPI('b71820caab4115163fccea2ebe6a1f6f','f21c592d1317','curl');
		$resultSMS = $test->verifycode($phone,$verify_no);
		if($resultSMS['code'] == 200){
			return true;//成功
		}else{
			return false;
		}
		
	}
	
	public function hideName($name){
		$length = mb_strlen($name);
		return $length > 2 ? mb_substr($name,0,1, 'utf-8').'*'.mb_substr($name,2,1, 'utf-8'):mb_substr($name,0,1, 'utf-8').'*';
	}
	
	
	public function productOrder(){
		$this->isLogin();
		$unionid = $_SESSION['unionid'];
		$openid = $_SESSION['openid'];
		
		$this->assign("cust_name",$_REQUEST['cust_name']);
		$this->assign("mobile1",$_REQUEST['mobile1']);
		
		$this->display(C('HOME_DEFAULT_THEME').':productOrder');
	}
	
	private function isLogin(){
		if(!$_SESSION['unionid']){			
			$this->display(C('HOME_DEFAULT_THEME').':qzgdwlLogin');
			$url = "index.php?m=qzgdwl&a=qzgdwlLogin";
			header("Location: $url");
		}
	}
	
	public function productOrderAdd(){
		$business = M('business');
		$data = array();
		$data['unionid'] = $_SESSION['unionid'];
		$result = array();
		if($_SESSION['unionid']){
			$data['org'] = $_SESSION['org'];
			$data['org_type'] = $_SESSION['org_type'];
			$data['district'] = $_POST['district'];
			$data['town'] = $_POST['town'];
			$data['create_date'] = date('Y-m-d H:i:s',time());
			$data['busi_type'] = mb_substr($_POST['business'],0,11);
			$data['busi_name'] = $_POST['name'];
			$data['busi_phone'] = $_POST['phone'];
			$data['phone'] = $_SESSION['phone'];
			$data['busi_address'] = $data['district'].$data['town'].$_POST['address'];
			$data['busi_status1'] = '待处理';
			$business->data($data)->add();
			$result['code'] = 1;
			$result['msg'] = "提交成功，客服人员将与客户联系。";
			echo $this->json($result);
		}else{
			$result['code'] = 0;
			$result['msg'] = "操作超时！";
			echo $this->json($result);
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
	
	public function qzgdwlProductDetail(){
		$this->display(C('HOME_DEFAULT_THEME').':qzgdwlProductDetail');	
	}



}


?>
