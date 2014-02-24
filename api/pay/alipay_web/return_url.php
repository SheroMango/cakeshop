<?php
/* * 
 * 功能：支付宝页面跳转同步通知页面
 * 版本：3.3
 * 日期：2012-07-23
 * 说明：
 * 以下代码只是为了方便商户测试而提供的样例代码，商户可以根据自己网站的需要，按照技术文档编写,并非一定要使用该代码。
 * 该代码仅供学习和研究支付宝接口使用，只是提供一个参考。

 *************************页面功能说明*************************
 * 该页面可在本机电脑测试
 * 可放入HTML等美化页面的代码、商户业务逻辑程序代码
 * 该页面可以使用PHP开发工具调试，也可以使用写文本函数logResult，该函数已被默认关闭，见alipay_notify_class.php中的函数verifyReturn
 */

require_once("alipay.config.php");
require_once("lib/alipay_notify.class.php");
?>
<!DOCTYPE HTML>
<html>
    <head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<?php
//计算得出通知验证结果
$alipayNotify = new AlipayNotify($alipay_config);
$verify_result = $alipayNotify->verifyReturn();
if($verify_result) {//验证成功
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//请在这里加上商户的业务逻辑程序代码
	
	//——请根据您的业务逻辑来编写程序（以下代码仅作参考）——
    //获取支付宝的通知返回参数，可参考技术文档中页面跳转同步通知参数列表

	//商户订单号

	$out_trade_no = $_GET['out_trade_no'];

	//支付宝交易号

	$trade_no = $_GET['trade_no'];

	//交易状态
	$trade_status = $_GET['trade_status'];


    if($_GET['trade_status'] == 'TRADE_FINISHED' || $_GET['trade_status'] == 'TRADE_SUCCESS') {
		//判断该笔订单是否在商户网站中已经做过处理
			//如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
			//如果有做过处理，不执行商户的业务程序
    	$con = mysql_connect("localhost","316ddg","316ddg");
		if (!$con)
		  {
		  	die('Could not connect: ' . mysql_error());
		  }

		mysql_select_db("316ddg_db", $con);
		$time = time();
		//更新订单状态等信息
		mysql_query("UPDATE cs_order_info SET pay_status = '2', pay_time = '". $time ."' WHERE order_sn = '" . $out_trade_no . "'");
		
		//会员的消费金额增加
        $order_info = mysql_query("select id, user_id, order_total from cs_order_info where order_sn = ".$out_trade_no);
        $order_info = mysql_fetch_assoc($order_info);
		mysql_query("UPDATE cs_user SET spend_count = spend_count+".$order_info['order_total']." WHERE id = ".$order_info['user_id']);
		
        //商品的销售数量增加
        $goods_info = mysql_query("select goods_id, goods_number from cs_order_goods where order_id = ".$order_info['id']);
        $goods_info = mysql_fetch_assoc($goods_info);
		mysql_query("UPDATE cs_goods SET sales = sales+".$goods_info['goods_number']." WHERE id = ".$goods_info['goods_id']);

		mysql_close($con);
    }
    else {
      echo "trade_status=".$_GET['trade_status'];
    }
		
	echo "支付成功<br />";
	//——请根据您的业务逻辑来编写程序（以上代码仅作参考）——
	
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
}
else {
    //验证失败
    //如要调试，请看alipay_notify.php页面的verifyReturn函数
    echo "支付失败";
}
$url="http://www.316ddg.com/index.php?g=home&m=order&a=ls";
echo "<SCRIPT LANGUAGE='javascript'>";
echo "location.href='$url'";
echo "</SCRIPT>";
?>
        <title>支付宝即时到账交易接口</title>
	</head>
    <body>
    </body>
</html>
