﻿<?php
class YwyAction extends Action {
	
    protected function _initialize() {
	/*echo "<br>"."网站的根目录地址".__ROOT__." ";  
echo "<br>"."入口文件地址".__APP__." "; 
echo "<br>"."当前模块地址".__URL__." "; 
echo "<br>"."当前url地址".__SELF__." ";
echo "<br>"."当前操作地址".__ACTION__." ";
echo "<br>"."当前模块的模板目录".__CURRENT__." ";
echo "<br>"."当前操作名称".ACTION_NAME." ";
echo "<br>"."当前项目目录".APP_PATH." ";
echo "<br>"."当前项目名称".APP_NAME." ";
echo "<br>"."当前项目的模板目录".APP_TMPL_PATH." ";
echo "<br>"."项目的公共文件目录".APP_PUBLIC_PATH." ";
echo "<br>"."项目的配置文件目录".CONFIG_PATH." ";
echo "<br>"."项目的公共文件目录".COMMON_PATH." ";
//自动缓存与表相关的全部信息
echo "<br>"."项目的数据文件目录".DATA_PATH." runtime下的data目录";
echo "<br>"." ".GROUP_NAME."";
echo "<br>"." ".IS_CGI."";
echo "<br>"." ".IS_WIN."";
echo "<br>"." ".LANG_SET."";
echo "<br>"." ".LOG_PATH."";
echo "<br>"." ".LANG_PATH."";
echo "<br>"." ".TMPL_PATH."";
//js放入的位置，供多个应用的公共资源
echo "<br>"." ".WEB_PUBLIC_PATH."";*/
		$model = M("Setting");
		$condition['item'] = "0";
		$setting = $model->where($condition)->getField('item_key,item_value');
		$this->assign(C('AIMEE_PREFIX'),$setting);
		
		$this->is_session();
		$useragent = addslashes($_SERVER['HTTP_USER_AGENT']);
		if(strpos($useragent, 'MicroMessenger') == false && strpos($useragent, 'Windows Phone') == false ){
			//header("location:http://www.968816.com.cn/error.html");
		}
		//$this->ywyBuildQR();
	}
	
    public function index(){
		Log::write('文件名testsete：' . $tplName);
    	//$this->display(C('HOME_DEFAULT_THEME').':index');
    }
    
   	public function login(){
		/*$channel = M("Channel");
		$condition['id'] = $_GET['fid'];
		$page = isset($_GET['p'])? $_GET['p'] : '1';  
		$this->assign("page",$page);
		if ($_GET['actype'] == "channel") {
			$this->assign("fid",$_GET['fid']);
			$tplName = $channel->where($condition)->getField('cl_tplclass');
		} else {
			$this->assign("cid",$_GET['cid']);
			$tplName = $channel->where($condition)->getField('cl_tplcontent');
		}*/		
		$unionid = $_GET['unionid'];
		session('unionid',$unionid);
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
		
	public function ywyMoney(){
		$this->is_session();
		if(!$_SESSION['cust_code']){
			$_SESSION['cust_code'] = $_GET['cust_code'];
		}
		if($_SESSION['own_org_id']){
			$_SESSION['own_org_id'] = $_GET['own_org_id'];
		}
		
		$this->assign("cust_code",$_SESSION['cust_code']);	
		$this->assign("own_org_id",$_SESSION['own_org_id']);	
		$this->display(C('HOME_DEFAULT_THEME').':ywyMoney');
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
		$this->is_session();
		$this->display(C('HOME_DEFAULT_THEME').':ywyBill');
    }
	
	public function ywyCharge(){
		$this->is_session();
		$this->display(C('HOME_DEFAULT_THEME').':ywyCharge');
    }
	
	public function ywyRank(){
		//$this->is_session();
		$ywy = M('Ywy');
		//->order('id desc')->limit(20)->select();
		$ywyRowMonth = $ywy->order('month_times desc')->limit('10')->select();
		$ywyRowYear = $ywy->order('year_times desc')->limit('10')->select();
		$this->assign("ywyRowMonth",$ywyRowMonth);
		$this->assign("ywyRowYear",$ywyRowYear);
		$this->display(C('HOME_DEFAULT_THEME').':ywyRank');
    }
	
	public function ywyIndex(){
		$this->is_session();
		if(isset($_GET['cust_code']) && $_SESSION['unionid']){
			$cust_code = $_GET['cust_code'];
			$TOKEN_URL="http://36.250.88.18:8085/ppjj/tvPpjj.action?method=loginBossByName&custcode=$cust_code";
			$this->ywySaveLog("客户信息查询：".$_POST['cust_code']);
			$values = $this->getBoss($TOKEN_URL);
			if($values[0]['attributes']['RETURN-CODE'] == 0 ) {	
				if($_SESSION['own_org_id'] == $values[1]['attributes']['OWNORGID'] || $_SESSION['own_org_id'] == "1201"){
					if($_SESSION['own_org_id'] == "2201" && (strpos($values[1]['attributes']['ADDRESS'],"鲤城")== 0 || strpos($values[1]['attributes']['ADDRESS'],"丰泽") == 0 || strpos($values[1]['attributes']['ADDRESS'],"洛江") == 0 || strpos($values[1]['attributes']['ADDRESS'],"台商") == 0 )){
						$values[1]['attributes']['PHONE'] = $values[1]['attributes']['PHONE1']."  ".$values[1]['attributes']['PHONE2']."  ".$values[1]['attributes']['MOBILE1']."  ".$values[1]['attributes']['MOBILE2'];
						$acctNo = $values[1]['attributes']['ACCOUNTID'];
						$TOKEN_URL="http://36.250.88.18:8085/ppjj/tvPpjj.action?method=getAcct&accountNo=$acctNo";			
						//$this->ywySaveLog("账户信息查询：".$acctNo);
						$values1 = $this->getBoss($TOKEN_URL);
						//echo $values1[1]['attributes']['BALANCE']."元";
						if($acctNo != "null"){
							$values[1]['attributes']['BALANCE']  = $values1[1]['attributes']['BALANCE']."元";	
						}else{
							$values[1]['attributes']['BALANCE']  = "--元";	
						}
						M('Recent')->data(array('cust_code'=>$cust_code,'phone'=>$values[1]['attributes']['MOBILE1'],'cust_name'=>$values[1]['attributes']['CUSTOMERNAME'],'unionid'=>$_SESSION['unionid'],'create_date'=>date('Y-m-d H:i:s',time())))->add();
						$this->assign("cust",$values[1]['attributes']);		
						$this->assign("code",100);		
						$this->assign("cust_code",$cust_code);	
						$this->assign("own_org_id",$values[1]['attributes']['OWNORGID']);	
						$_SESSION['cust_code'] = $cust_code;
						$_SESSION['own_org_id'] = $values[1]['attributes']['OWNORGID'];
					}elseif($_SESSION['own_org_id'] != "2201"){
						$values[1]['attributes']['PHONE'] = $values[1]['attributes']['PHONE1']."  ".$values[1]['attributes']['PHONE2']."  ".$values[1]['attributes']['MOBILE1']."  ".$values[1]['attributes']['MOBILE2'];
						$acctNo = $values[1]['attributes']['ACCOUNTID'];
						$TOKEN_URL="http://36.250.88.18:8085/ppjj/tvPpjj.action?method=getAcct&accountNo=$acctNo";	
						//print_r();
						//echo $values1[1]['attributes']['BALANCE']."元";
						$values1 = $this->getBoss($TOKEN_URL);
						if($acctNo != "null"){
							$values[1]['attributes']['BALANCE']  = $values1[1]['attributes']['BALANCE']."元";	
						}else{
							$values[1]['attributes']['BALANCE']  = "--元";	
						}						
						//$this->ywySaveLog("账户信息查询：".$acctNo);
						//$values[1]['attributes']['BALANCE']  = $values1[1]['attributes']['BALANCE']."元";	
						M('Recent')->data(array('cust_code'=>$cust_code,'phone'=>$values[1]['attributes']['MOBILE1'],'cust_name'=>$values[1]['attributes']['CUSTOMERNAME'],'unionid'=>$_SESSION['unionid'],'create_date'=>date('Y-m-d H:i:s',time())))->add();
						$this->assign("cust",$values[1]['attributes']);		
						$this->assign("code",100);	
						$this->assign("cust_code",$cust_code);	
						$this->assign("own_org_id",$values[1]['attributes']['OWNORGID']);	
						$_SESSION['cust_code'] = $cust_code;
						$_SESSION['own_org_id'] = $values[1]['attributes']['OWNORGID'];						
					}else{
						$this->assign("code",400);	
					}
				}else{
					$this->assign("code",400);	
				}
			}else{
				$this->assign("code",404);	
			}
			
		}else{
			$this->assign("code",404);	
		}
		$this->display(C('HOME_DEFAULT_THEME').':ywyIndex');
		
    }
	
	public function ywyBuildQR(){
				
		$own_org_id = $_POST['own_org_id'];
		$cust_code = $_POST['cust_code'];
		$money = $_POST['money'];
		$result = array();
		
		$TOKEN_URL="http://36.250.88.18:8085/ppjj/tvPpjj.action?method=loginBossByName&custcode=$cust_code";
		$this->ywySaveLog("账户充值".$cust_code."_".$money);
		$values = $this->getBoss($TOKEN_URL);
		if($values[0]['attributes']['RETURN-CODE'] == 0 ) {	
		
			include 'phpqrcode.php'; 
			$value = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx7a6b2b757241f8f2&redirect_uri=http://wsc.fjgdwl.com/cgi-bin/auth&response_type=code&scope=snsapi_base&state=http%3a%2f%2fwxyyt.fjgdwl.com%2fwtbh_web%2fhtml%2fweb%2fservFunc%2frecharge%2frechargeSkip.jsp%3fcustCode%3d$cust_code%26corpOrgId%3d$own_org_id%26money%3d$money&connect_redirect=1#wechat_redirect";//二维码内容 
			$errorCorrectionLevel = 'L';//容错级别 
			$matrixPointSize = 6;//生成图片大小 
			//生成二维码图片 
			QRcode::png($value, 'qrcode.png', $errorCorrectionLevel, $matrixPointSize, 2); 
			$logo = 'fjgdwl1.jpg';//准备好的logo图片 
			$QR = 'qrcode.png';//已经生成的原始二维码图 
			 
			if ($logo !== FALSE) { 
				$QR = imagecreatefromstring(file_get_contents($QR)); 
				$logo = imagecreatefromstring(file_get_contents($logo)); 
				$QR_width = imagesx($QR);//二维码图片宽度 
				$QR_height = imagesy($QR);//二维码图片高度 
				$logo_width = imagesx($logo);//logo图片宽度 
				$logo_height = imagesy($logo);//logo图片高度 
				$logo_qr_width = $QR_width / 5; 
				$scale = $logo_width/$logo_qr_width; 
				$logo_qr_height = $logo_height/$scale; 
				$from_width = ($QR_width - $logo_qr_width) / 2; 
				//重新组合图片并调整大小 
				imagecopyresampled($QR, $logo, $from_width, $from_width, 0, 0, $logo_qr_width, 
				$logo_qr_height, $logo_width, $logo_height); 
			} 
			//输出图片 		
			Header("Content-type: image/png");
			ImagePng($QR, "QRCZ/$cust_code"."_"."$money.png");
			
			$result['code'] = 100;
			$result['msg'] = "订单名称:".$values[1]['attributes']['CUSTOMERNAME']."[$cust_code]_账户充值".$money."元";
			echo $this->json($result);	
		}else{
			$result['code'] = 404;
			$result['msg'] = "订单无法生成，请重新尝试！";
			echo $this->json($result);
		}
	}
	
	public function is_session(){
		
		$unionid = 'oj8HfvquP3oHLCavPTo5bCROjMmc';
		$openid = 'odrEdt4KBaxcWlnEB4YCkkyWe0wgr';
		if($unionid){
			$_SESSION['unionid'] = $unionid;
		}
		if($openid){
			$_SESSION['openid'] = $openid;
		}
		$_SESSION['own_org_id'] = 1201;
		// $unionid = $_GET['unionid'];
		// $openid = $_GET['openid'];
		// if($unionid){
			// $_SESSION['unionid'] = $unionid;
		// }
		// if($openid){
			// $_SESSION['openid'] = $openid;
		// }
		// if(!$_SESSION['own_org_id']){
			// //$url = " https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx1ae00a2623048bf1&redirect_uri=http://www.968816.com.cn/oauth2_openid_ywy.php&response_type=code&scope=snsapi_base&state=1#wechat_redirect";
			// //echo "<script>";  
			// //echo "window.location.href=\"".$url."\"";  
			// //echo "</script>"; 
			// $ywy = M('Ywy');
			// $unionidArr = array();
			// $unionidArr['unionid'] = $unionid;
			// $ywyRow = $ywy->where($unionidArr)->select();
			// //echo $ywyRow[0]['is_confirm'];
			// //print_r($ywyRow);
			// if($ywyRow && $unionid){
				// if($ywyRow[0]['is_confirm'] == 1){
					// $_SESSION['own_org_id'] = $ywyRow[0]['own_org_id'];		
				// }else{
					// $this->display(C('HOME_DEFAULT_THEME').':ywyLogin');
				// }
				// //echo 123;
			// }else{
				// $this->display(C('HOME_DEFAULT_THEME').':ywyLogin');
			// }
		// }
	}
	
	public function ywyCustCodeSearch(){
		
		$this->display(C('HOME_DEFAULT_THEME').':ywyCustCodeSearch');
    }
	
	public function ywyInsertName(){
		
		$this->display(C('HOME_DEFAULT_THEME').':ywyInsertName');
    }
	
	public function ywySaveLog($logStr){
		$log = M('Log');
		$data = array();
		$data['unionid'] = $_SESSION['unionid'];
		$data['create_date'] = date('Y-m-d H:i:s',time());
		$data['log'] = $logStr;
		$log->data($data)->add();
		
	}
	
	public function ywyGetCust(){
		$cust_no = "";
		$resultStr = "";
		$result = array();
		$result['code'] = 99;
		if(isset($_POST['cust_code']) && $_SESSION['unionid']){
			$cust_no = $_POST['cust_code'];
			$TOKEN_URL="http://36.250.88.18:8085/ppjj/tvPpjj.action?method=loginBossByName&custcode=$cust_no";
			$this->ywySaveLog("客户信息查询：".$_POST['cust_code']);
			$values = $this->getBoss($TOKEN_URL);
			/*$unionidArr = array();
			//$unionidArr['unionid'] =$_SESSION['unionid'];
			//$ywy = M('Ywy');
			$ywyRow = $ywy->where($unionidArr)->select();
			if($ywyRow]){
				if($ywyRow[0][is_confirm] != "1"){
					$ywy->where($unionidArr)->save(array('is_confirm'=>1,'unionid'=>$_SESSION['unionid'],'openid'=>$_SESSION['openid']));
					$result['code'] = 1;		
				}else{
					$result['code'] = 2;
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
						$result['code'] = 0;						
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
						$result['code'] = 0;						
						echo $this->json($result);
					}else{
						$result['code'] = 43;	
						//$result['MSG'] = strpos($values[1]['attributes']['ADDRESS'],"丰泽") ;
						echo $this->json($result);
					}
				}else{
					$result['code'] = 43;					
					echo $this->json($result);
				}
			}else{
				echo $this->json($result);
			}
			
		}else if(!$_SESSION['unionid']){
			$result['code'] = 44;
			echo $this->json($result);
		}else{
			echo $this->json($result);
		}		
		
	}
	
	public function getAcctNo(){
		$cust_no = "";
		$resultStr = "";
		$result = array();
		$result['code'] = 99;
		if(isset($_POST['cust_code'])){
			$cust_no = $_POST['cust_code'];
			$TOKEN_URL_ALL="http://36.250.88.18:8085/ppjj/tvPpjj.action?method=getAllInfro&custcode=$cust_no";
			$accoutNoArr = $this->getBoss($TOKEN_URL_ALL);
			$result['ACCOUNTID'] = $accoutNoArr[count($accoutNoArr)-3]['attributes']['ACCOUNTID'];
			$result['code'] = 0;
			echo $this->json($result);
		}else{
			echo $this->json($result);
		}
	}
	
	public function getAcctBalance(){
		$result = array();
		$result['code'] = 99;
		if(isset($_POST['acctNo'])){
			$acctNo = $_POST['acctNo'];
			$TOKEN_URL="http://36.250.88.18:8085/ppjj/tvPpjj.action?method=getAcct&accountNo=$acctNo";			
			$this->ywySaveLog("账户信息查询：".$_POST['acctNo']);
			$values = $this->getBoss($TOKEN_URL);
			$result['NAME']  = $values[1]['attributes']['NAME'];
			$result['ACCOUNTNO']  = $values[1]['attributes']['ACCOUNTNO'];
			if($acctNo != "null"){
				$result['BALANCE']  = $values[1]['attributes']['BALANCE']."元";	
			}else{
				$result['BALANCE']  = "--元";					
			}
			$result['PAYTYPE']  = $values[1]['attributes']['PAYTYPE'];
			$result['JOINDATE']  = $values[1]['attributes']['JOINDATE'];
			$result['OFFICE']  = $values[1]['attributes']['OFFICE'];	
			$result['STATUS']  = $values[1]['attributes']['STATUS'];		
			$result['code'] = 0;
			echo $this->json($result);
		}else{
			echo $this->json($result);
		}
	}
	
	public function queryBillYwy(){
		$accountNo = $_POST['accountNo'];
		$result = array();
		//echo $phone!=null ;
		$TOKEN_URL="http://36.250.88.18:8085/ppjj/tvPpjj.action?method=getBill&accountNo=$accountNo";
		$this->ywySaveLog("客户账单查询：".$_POST['accountNo']);
		$values = $this->getBoss($TOKEN_URL);
		$TOKEN_URL1 ="http://36.250.88.18:8085/ppjj/tvPpjj.action?method=getAcct&accountNo=$accountNo";
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
					//$result["$i"]['BALANCE'] = $values1[1]['attributes']['BALANCE'];
					if($accountNo != "null"){
						$result["$i"]['BALANCE'] = $values1[1]['attributes']['BALANCE'];
					}else{
						$result["$i"]['BALANCE'] = "--";	
					}
					
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
		$TOKEN_URL="http://36.250.88.18:8085/ppjj/tvPpjj.action?method=getCharge&accountNo=$accountNo";
		$this->ywySaveLog("缴费记录查询：".$_POST['accountNo']);
		$values = $this->getBoss($TOKEN_URL);
		$TOKEN_URL1 ="http://36.250.88.18:8085/ppjj/tvPpjj.action?method=getAcct&accountNo=$accountNo";
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
					//$result["$i"]['BALANCE'] = $values1[1]['attributes']['BALANCE'];
					if($accountNo != "null"){
						$result["$i"]['BALANCE'] = $values1[1]['attributes']['BALANCE'];
					}else{
						$result["$i"]['BALANCE'] = "--";	
					}					
					$i++;
				}
			}				
			echo $this->json($result);
		}else{
			echo $this->json($result);
		}
	}
	
	public function queryBusiness(){
		$custCode = $_POST['cust_code'];
		$startDate = $_POST['startDate'];
		$endDate = $_POST['endDate'];
		$result = array();
		//echo $phone!=null ;
		$TOKEN_URL="http://36.250.88.18:8085/ppjj/tvPpjj.action?method=getBusiness&custcode=$custCode&beginDate=$startDate&endDate=$endDate";
		$this->ywySaveLog("业务办理查询：".$_POST['cust_code']."  ".$startDate."  ".$endDate);
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
		$TOKEN_URL="http://36.250.88.18:8085/ppjj/tvPpjj.action?method=refreshOrder&prodInstId=$prodInstId&opType=$opType";
		$this->ywySaveLog("刷新授权：".$card_no."  ".$opType);
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
		$custcode = $_POST['cust_code'];
		$result = array();
		//echo $phone!=null ;
		$TOKEN_URL="http://36.250.88.18:8085/ppjj/tvPpjj.action?method=getUser&custcode=$custcode";
		$this->ywySaveLog("用户信息查询：".$_POST['cust_code']);
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
		$unionid = 'oj8HfvquP3oHLCavPTo5bCROjMmc';
		$openid = 'odrEdt4BSKcs-VvOXocU3joswWDU';
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
		//echo $ywyRow[0]['is_confirm'];
		//print_r($ywyRow);
		if($ywyRow && $unionid){
			if($ywyRow[0]['is_confirm'] == 1){
				$_SESSION['own_org_id'] = $ywyRow[0]['own_org_id'];				
				$this->ywySaveLog("登录");
				$this->display(C('HOME_DEFAULT_THEME').':ywyCustCodeSearch');
			}else{
				$this->display(C('HOME_DEFAULT_THEME').':ywyCustCodeSearch');
			}
			//echo 123;
		}else{
			$this->display(C('HOME_DEFAULT_THEME').':ywyCustCodeSearch');
		}
	}
	
	public function ywyUnBunding(){
		$result = array();
		$unionidArr = array();
		$unionid = $_GET['unionid'];
		$unionidArr['unionid'] = $_SESSION['unionid'];
		$ywy = M('Ywy');
		$ywyRow = $ywy->where($unionidArr)->select();
		if($ywyRow ){
			$ywy->where($unionidArr)->save(array('is_confirm'=>0,'unionid'=>'','openid'=>''));			
			$this->ywySaveLog("解绑成功");
			$result['code'] = 1;
		}else{
			$result['code'] = 44;
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
				$result['code'] = 1;
				$_SESSION['own_org_id'] = $ywyRow[0]['own_org_id'];				
				$this->ywySaveLog("登录成功");
			}elseif($ywyRow[0]['is_confirm'] == "1"){
				$result['code'] = 2;
				//$_SESSION['own_org_id'] = $ywyRow[0]['own_org_id'];
				//$this->ywySaveLog("登录成功");
			}else{
				$result['code'] = 2;
			}
			echo $this->json($result);
		}elseif(!$_SESSION['unionid']){
			$result['code'] = 0;	
			echo $this->json($result);
		}else{
			$result['code'] = 99;	
			echo $this->json($result);
		}
	}
	
	public function loginSubmit(){
		$unionid = session('unionid');
		if(!$unionid){
			$unionid = $_POST['unionid'];
		}
		$cust_no = $_POST['cust_no'];
		$pwd = $_POST['pwd'];
		$cust_name = $_POST['cust_name'];
		//echo $cust_no.$pwd."<br>";
		//echo $cust_name;
		if(!$unionid){
			$this->display(C('HOME_DEFAULT_THEME').':errorMessage');
		}else{
			if($pwd != "" || $cust_name == "%"){
				$TOKEN_URL="http://36.250.88.18:8085/ppjj/tvPpjj.action?method=loginBossByPwd&custcode=$cust_no&pwd=$pwd";
				$values = $this->getBoss($TOKEN_URL);
				if($values[0]['attributes']['RETURN-CODE'] == 0) {		
					//echo "登录成功";
					$user = M('User');				
					$data = array();			
					$unionidArr = array();
					$unionidArr['unionid'] = $unionid;
					$userRow = $user->where($unionidArr)->select();
					//if($userRow){
					$TOKEN_URL="http://36.250.88.18:8085/ppjj/tvPpjj.action?method=loginBossByName&custcode=$cust_no";
					$values = $this->getBoss($TOKEN_URL);
					$data['cust_code'] = $values[1]['attributes']['CUSTOMERCODE'];
					$data['cust_name'] = $values[1]['attributes']['CUSTOMERNAME'];
					$data['cust_prop'] = $values[1]['attributes']['CUSTPROP'];
					$data['address'] = $values[1]['attributes']['ADDRESS'];
					$data['phone1'] = $values[1]['attributes']['PHONE1'];
					$data['phone2'] = $values[1]['attributes']['PHONE2'];
					$data['mobile1'] = $values[1]['attributes']['MOBILE1'];
					$data['mobile2'] = $values[1]['attributes']['MOBILE2'];
					$TOKEN_URL_ALL="http://36.250.88.18:8085/ppjj/tvPpjj.action?method=getAllInfro&custcode=$cust_no";
					$accoutNoArr = $this->getBoss($TOKEN_URL_ALL);
					$accoutNo = $accoutNoArr[count($accoutNoArr)-3]['attributes']['ACCOUNTID'];						
					$data['accountno'] = $accoutNo;				
					$data['is_binding'] = 1;
					$result = $user->where($unionidArr)->save($data);
					$this->display(C('HOME_DEFAULT_THEME').':bindSuc');
				}else{
					echo "客户编号或服务密码错误！";
				}
			}else{
				$TOKEN_URL="http://36.250.88.18:8085/ppjj/tvPpjj.action?method=loginBossByName&custcode=$cust_no";
				$values = $this->getBoss($TOKEN_URL);
				if($values[0]['attributes']['RETURN-CODE'] == 0) {					
					if($values[1]['attributes']['CUSTOMERNAME'] == $cust_name){					
						//echo "登录成功";
						$user = M('User');				
						$data = array();			
						$unionidArr = array();
						$unionidArr['unionid'] = $unionid;
						$userRow = $user->where($unionidArr)->select();
						//if($userRow){
						$TOKEN_URL="http://36.250.88.18:8085/ppjj/tvPpjj.action?method=loginBossByName&custcode=$cust_no";
						$values = $this->getBoss($TOKEN_URL);
						$data['cust_code'] = $values[1]['attributes']['CUSTOMERCODE'];
						$data['cust_name'] = $values[1]['attributes']['CUSTOMERNAME'];
						$data['cust_prop'] = $values[1]['attributes']['CUSTPROP'];
						$data['address'] = $values[1]['attributes']['ADDRESS'];
						$data['phone1'] = $values[1]['attributes']['PHONE1'];
						$data['phone2'] = $values[1]['attributes']['PHONE2'];
						$data['mobile1'] = $values[1]['attributes']['MOBILE1'];
						$data['mobile2'] = $values[1]['attributes']['MOBILE2'];
						//$result = $user->where($unionidArr)->save($data);		
						//}
						
						$TOKEN_URL_ALL="http://36.250.88.18:8085/ppjj/tvPpjj.action?method=getAllInfro&custcode=$cust_no";
						$accoutNoArr = $this->getBoss($TOKEN_URL_ALL);
						$accoutNo = $accoutNoArr[count($accoutNoArr)-3]['attributes']['ACCOUNTID'];						
						$data['accountno'] = $accoutNo;				
						$data['is_binding'] = 1;
						$result = $user->where($unionidArr)->save($data);	
						$this->display(C('HOME_DEFAULT_THEME').':bindSuc');						
					}else{
						echo "客户编号或姓名错误！";
					}
				}else{
					echo "该客户不存在！";
				}
			}
			
		}
		
		//echo $value.$_POST['cust_no'].$_POST['cust_name'].$_POST['pwd'];
    }
	
	public function ywyCustConfirm(){
		if(isset($_POST['cust_code'])){
			session('custCode',$_POST['cust_code']);
		}
	}
	
	public function query(){
		$cust_code = $_POST['cust_code'];
		$cust_name = $_POST['cust_name'];
		$address = $_POST['address'];
		$mac = $_POST['mac'];
		$stbno = $_POST['stbno'];
		$cardno = $_POST['cardno'];
		$cert_no = $_POST['cert_no'];
		$phone = $_POST['phone'];
		if(!$address){
			if($_SESSION['own_org_id'] == "2202"){
				$address = "泉港";
			}elseif($_SESSION['own_org_id'] == "2203"){
				$address = "石狮";
			}elseif($_SESSION['own_org_id'] == "2204"){
				$address = "晋江";
			}elseif($_SESSION['own_org_id'] == "2205"){
				$address = "惠安";
			}elseif($_SESSION['own_org_id'] == "2206"){
				$address = "南安";
			}elseif($_SESSION['own_org_id'] == "2207"){
				$address = "安溪";
			}elseif($_SESSION['own_org_id'] == "2208"){
				$address = "永春";
			}elseif($_SESSION['own_org_id'] == "2209"){
				$address = "德化";
			}
		}
		if($cust_code){
			$TOKEN_URL="http://36.250.88.18:8085/ppjj/tvPpjj.action?method=loginBossByName&custcode=$cust_code";
			$this->ywySaveLog("客户信息查询：".$cust_code);
			$values = $this->getBoss($TOKEN_URL);
			if($values[0]['attributes']['RETURN-CODE'] == 0 ) {	
				
				if($_SESSION['own_org_id'] == $values[1]['attributes']['OWNORGID'] || $_SESSION['own_org_id'] == "1201"){
					if($_SESSION['own_org_id'] == "2201" && (strpos($values[1]['attributes']['ADDRESS'],"鲤城")== 0 || strpos($values[1]['attributes']['ADDRESS'],"丰泽") == 0 || strpos($values[1]['attributes']['ADDRESS'],"洛江") == 0 || strpos($values[1]['attributes']['ADDRESS'],"台商") == 0 )){						
						echo 100;
					}elseif($_SESSION['own_org_id'] != "2201"){
						echo 100;
					}else{
						$result['code'] = 400;	
						echo "无法查询到客户信息";
					}
				}else{
					$result['code'] = 400;		
					echo "无法查询到客户信息";
				}
			}else{
				$result['code'] = 404;
				echo "无法查询到客户信息";
			}
		}else{
			$TOKEN_URL="http://36.250.88.18:8085/ppjj/tvPpjj.action?method=getCustCode&cardno=$cardno&address=$address&mac=$mac&stbno=$stbno&cust_name=$cust_name&cert_no=$cert_no&phone=$phone";
			$this->ywySaveLog("获取客户编号：cardno=$cardno&address=$address&mac=$mac&stbno=$stbno&cust_name=$cust_name&cert_no=$cert_no&phone=$phone");
			$values = $this->getBoss($TOKEN_URL);
			if($values[0]['attributes']['RETURN-CODE'] == 0) {
				$returnStr = "";
				$arrLen = count($values);
				for ($i = 1;$i < $arrLen-1; $i++){ 
					$returnStr .= "<div class=\"queryCust\">".$i.". 客户编号：<span id=\"custCode".$i."\" style=\"color:#CC0033\">".$values[$i]['attributes']['CUSTCODE']."</span><br>"."地址：".$values[$i]['attributes']['ADDRNAME']."<br>".
					"<div class=\"col-50\"><a href=\"#\" onclick=\"ywyCustConfirm(".$i.");\" class=\"weui_btn_sure\">确定</a></div>".
					"----------------------------------------------<br></div>";
				} 
				
				$result['code'] = 100;
				$result['returnStr'] = $returnStr;
				echo $returnStr;
			}else{
				$result['code'] = 404;
				echo "无法查询到客户信息";
			}
		
		}
    }
	
	public function queryBill(){
		$accountNo = $_POST['accountNo'];
		$result = array();
		//echo $phone!=null ;
		$TOKEN_URL="http://36.250.88.18:8085/ppjj/tvPpjj.action?method=getBill&accountNo=$accountNo";
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
		$custcode = $_POST['cust_code'];
		$result = array();
		//echo $phone!=null ;
		$TOKEN_URL="http://36.250.88.18:8085/ppjj/tvPpjj.action?method=getAllInfro&custcode=$custcode";
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
		$TOKEN_URL="http://36.250.88.18:8085/ppjj/tvPpjj.action?method=getOrding&subscriberNo=$SUBSCRIBERID";
		$this->ywySaveLog("用户订购查询：".$_POST['SUBSCRIBERID']);
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
	
	public function ywyCollect(){
		$cust_code = $_POST['cust_code'];
		$result = array();
		
		if(M('Recent')->where(array('cust_code'=>$cust_code))->save(array('collect'=>1))){
			$result['code'] = 1;
		}else{
		
		}
		echo $this->json($result);
	}
	
	public function ywyUnCollect(){
		$cust_code = $_POST['cust_code'];
		$result = array();
		
		if(M('Recent')->where(array('cust_code'=>$cust_code))->save(array('collect'=>0))){
			$result['code'] = 1;
		}else{
		
		}
		echo $this->json($result);
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
		return $values;
		//print_r($values);
		//echo $values[0]['tag'];
		//echo json_encode($values[1],JSON_UNESCAPED_UNICODE);
		//echo json_decode($values);
		//echo "\n索引数组\n";
		//print_r($index);
		//echo "\n数据数组\n";
		//print_r($values);
		
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


}


?>