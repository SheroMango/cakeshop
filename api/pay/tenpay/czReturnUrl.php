<?php
/**
 * 财付通return页面
 * @version 2013-01-05
 */
header("Content-type: text/html; charset=utf-8");
require('../../libraries/common.inc.php');
require('../../share.inc.php');


//---------------------------------------------------------
//财付通即时到帐支付页面回调示例，商户按照此文档进行开发即可
//---------------------------------------------------------
require_once ("./classes/ResponseHandler.class.php");
require_once ("./classes/function.php");
require_once ("./tenpay_config.php");

log_result("进入前台回调页面");


/* 创建支付应答对象 */
$resHandler = new ResponseHandler();
$resHandler->setKey($key);

//判断签名
if($resHandler->isTenpaySign()) {
	//通知id
	$notify_id = $resHandler->getParameter("notify_id");
	//商户订单号
	$out_trade_no = $resHandler->getParameter("out_trade_no");
	//财付通订单号
	$transaction_id = $resHandler->getParameter("transaction_id");
	//金额,以分为单位
	$total_fee = $resHandler->getParameter("total_fee");
	//如果有使用折扣券，discount有值，total_fee+discount=原请求的total_fee
	$discount = $resHandler->getParameter("discount");
	//支付结果
	$trade_state = $resHandler->getParameter("trade_state");
	//交易模式,1即时到账
	$trade_mode = $resHandler->getParameter("trade_mode");
	
	if("1" == $trade_mode ) {
		//即时到帐
		if($trade_state == "0"){ 
			$paylogInfo = $pdb->GetRow("SELECT * FROM {$tb_prefix}paylogs WHERE number='$out_trade_no'");
			if (!$paylogInfo['status']) {
				uses('paylog');
				$paylog = new Paylogs();
				$paylog->updateStatus($paylogInfo['id'], 1);
				flash('充值成功', 'virtual-office/coin.php?do=recharge', 1);
			} else {
				flash('充值成功', 'virtual-office/coin.php?do=recharge', 1);
			}
		} else {
			echo '充值失败';
		}
	} else if( "2" == $trade_mode  ) {
		//担保交易
		if( "0" == $trade_state) {
			echo "<br/>" . "中介担保支付成功" . "<br/>";
		} else {
			echo "<br/>" . "中介担保支付失败" . "<br/>";
		}
	}
	
} else {
	echo "<br/>" . "认证签名失败" . "<br/>";
	echo $resHandler->getDebugInfo() . "<br>";
}

?>