<?php
class WXChartsAction extends Action {
    protected function _initialize() {		
		//$this->isWx();
		$useragent = addslashes($_SERVER['HTTP_USER_AGENT']);
		if(strpos($useragent, 'MicroMessenger') == false && strpos($useragent, 'Windows Phone') == false ){
			//header("location:http://wx.968816.com.cn/error.html");
		}	
	}
	
	
		
    function WXChartsIndex(){	
	
		require_once "jssdk.php";
		$jssdk = new JSSDK("wx3a20e613be177269", "b722fba7bd164688bcfa5b4acb2f1bee");
		$signPackage = $jssdk->GetSignPackage();
		$this->assign("signPackage",$signPackage);		
		$this->display(C('HOME_DEFAULT_THEME').':WXChartsIndex');
	}	
	
	
	
}

?>