<?PHP
/**
 * 快钱支付
 * @version 2013-01-04
 */
header("Content-type: text/html; charset=utf-8");
require('../../libraries/common.inc.php');
require('../../share.inc.php');
$url = URL;
//$url = 'http://127.0.0.1/miniyi/';

//订单信息
$id = intval($_GET['id']);
$logInfo = $pdb->GetRow("SELECT * FROM {$tb_prefix}paylogs WHERE id='$id'");


////////////////////协议参数////////////////////
//inputCharset：编码方式，1代表 UTF-8; 2 代表 GBK; 3代表 GB2312 默认为1,该参数必填。
//pageUrl：接收支付结果的页面地址，该参数一般置为空即可。
//bgUrl：服务器接收支付结果的后台地址，该参数务必填写，不能为空。
//version：网关版本，固定值：v2.0,该参数必填。
//language：语言种类，1代表中文显示，2代表英文显示。默认为1,该参数必填。
//signType：签名类型,该值为4，代表PKI加密方式,该参数必填。
$inputCharset = "1";
//$pageUrl = "http://127.0.0.1/miniyi/payment/99bill/cz_return_url.php";
//$bgUrl = "http://127.0.0.1/miniyi/payment/99bill/cz_notify_url.php";
$bgUrl = $url."payment/99bill/cz_notify_url.php";
$version =  "v2.0";
$language =  "1";
$signType =  "4";

////////////////////买卖双方信息////////////////////
//merchantAcctId：人民币网关账号，该账号为11位人民币网关商户编号+01,该参数必填。
//payContactType：支付人联系类型，1 代表电子邮件方式；2 代表手机联系方式。可以为空。
//payContact：支付人联系方式，与payerContactType设置对应
//payerContactType为1，则填写邮箱地址；payerContactType为2，则填写手机号码。可以为空。
$merchantAcctId = "1002225284401";
//$payerName= "111"; 
$payerName = $theMemberInfo['nickname'];
//$payerContactType =  "1";
//$payerContact =  "2532987@qq.com";
//$payerIdType = 2;
//$payerId = '';
//$payerIP = '';


////////////////////买卖双方信息////////////////////
//orderId：商户订单号，以下采用时间来定义订单号，商户可以根据自己订单号的定义规则来定义该值，不能为空。
//orderAmount：订单金额，金额以“分”为单位，商户测试以1分测试即可，切勿以大金额测试。该参数必填。
//orderTime：订单提交时间，格式：yyyyMMddHHmmss，如：20071117020101，不能为空。
//productName：商品名称，可以为空。
//productNum：商品数量，可以为空。
//productId：商品代码，可以为空。
//productDesc：商品描述，可以为空。
//ext1：扩展字段1，商户可以传递自己需要的参数，支付完快钱会原值返回，可以为空。
//ext2：扩展自段2，商户可以传递自己需要的参数，支付完快钱会原值返回，可以为空。
//payType：支付方式，一般为00，代表所有的支付方式。如果是银行直连商户，该值为10，必填。
//bankId：银行代码，如果payType为00，该值可以为空；如果payType为10，该值必须填写，具体请参考银行列表。
//redoFlag：同一订单禁止重复提交标志，实物购物车填1，虚拟产品用0。1代表只能提交一次，0代表在支付不成功情况下可以再提交。可为空。
//pid：快钱合作伙伴的帐户号，即商户编号，可为空

$orderId = $logInfo['number'];
$orderAmount = $logInfo['money']*100;
$orderTime = date('YmdHis');
$productName= $logInfo['money'].'个迷你通';
//$productNum = $logInfo['money'];
//$productId = "55558888";
$productDesc = $logInfo['money'].'个迷你通';
$ext1 = "";
$ext2 = "";
$payType = "00";
$bankId = "";
$redoFlag = "";
$pid = "";


////////////////////买卖双方信息////////////////////
// signMsg 签名字符串 不可空，生成加密签名串
$kq_all_para = kq_ck_null($inputCharset,'inputCharset');
$kq_all_para .= kq_ck_null($pageUrl,"pageUrl");
$kq_all_para .= kq_ck_null($bgUrl,'bgUrl');
$kq_all_para .= kq_ck_null($version,'version');
$kq_all_para .= kq_ck_null($language,'language');
$kq_all_para .= kq_ck_null($signType,'signType');
$kq_all_para .= kq_ck_null($merchantAcctId,'merchantAcctId');
$kq_all_para .= kq_ck_null($payerName,'payerName');
$kq_all_para .= kq_ck_null($payerContactType,'payerContactType');
$kq_all_para .= kq_ck_null($payerContact,'payerContact');
$kq_all_para .= kq_ck_null($orderId,'orderId');
$kq_all_para .= kq_ck_null($orderAmount,'orderAmount');
$kq_all_para .= kq_ck_null($orderTime,'orderTime');
$kq_all_para .= kq_ck_null($productName,'productName');
$kq_all_para .= kq_ck_null($productNum,'productNum');
$kq_all_para .= kq_ck_null($productId,'productId');
$kq_all_para .= kq_ck_null($productDesc,'productDesc');
$kq_all_para .= kq_ck_null($ext1,'ext1');
$kq_all_para .= kq_ck_null($ext2,'ext2');
$kq_all_para .= kq_ck_null($payType,'payType');
$kq_all_para .= kq_ck_null($bankId,'bankId');
$kq_all_para .= kq_ck_null($redoFlag,'redoFlag');
$kq_all_para .= kq_ck_null($pid,'pid');
$kq_all_para = substr($kq_all_para,0,strlen($kq_all_para)-1);
function kq_ck_null($kq_va,$kq_na){
	if($kq_va == ""){
		$kq_va="";
	}else{
		return $kq_va=$kq_na.'='.$kq_va.'&';
	}
}

////////////////////RSA 签名计算////////////////////
$fp = fopen("./99bill-rsa.pem", "r");
$priv_key = fread($fp, 8192);
fclose($fp);
$pkeyid = openssl_get_privatekey($priv_key);
// compute signature
openssl_sign($kq_all_para, $signMsg, $pkeyid, OPENSSL_ALGO_SHA1);
// free the key from memory
openssl_free_key($pkeyid);
 $signMsg = base64_encode($signMsg);


////////////////////表单、跳转////////////////////

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>快钱充值 - 迷你医</title>
</head>
<body>
<div align="center" style="display:none; font-weight: bold;">
  <form id="kqPay" name="kqPay" action="https://www.99bill.com/gateway/recvMerchantInfoAction.htm" method="post">
    <input type="hidden" name="inputCharset" value="<?PHP echo $inputCharset; ?>" />
	<input type="hidden" name="pageUrl" value="<?PHP echo $pageUrl; ?>" />
	<input type="hidden" name="bgUrl" value="<?PHP echo $bgUrl; ?>" />
	<input type="hidden" name="version" value="<?PHP echo $version; ?>" />
	<input type="hidden" name="language" value="<?PHP echo $language; ?>" />
	<input type="hidden" name="signType" value="<?PHP echo $signType; ?>" />
	<input type="hidden" name="signMsg" value="<?PHP echo $signMsg; ?>" />
	<input type="hidden" name="merchantAcctId" value="<?PHP echo $merchantAcctId; ?>" />
	<input type="hidden" name="payerName" value="<?PHP echo $payerName; ?>" />
	<input type="hidden" name="payerContactType" value="<?PHP echo $payerContactType; ?>" />
	<input type="hidden" name="payerContact" value="<?PHP echo $payerContact; ?>" />
	<input type="hidden" name="orderId" value="<?PHP echo $orderId; ?>" />
	<input type="hidden" name="orderAmount" value="<?PHP echo $orderAmount; ?>" />
	<input type="hidden" name="orderTime" value="<?PHP echo $orderTime; ?>" />
	<input type="hidden" name="productName" value="<?PHP echo $productName; ?>" />
	<input type="hidden" name="productNum" value="<?PHP echo $productNum; ?>" />
	<input type="hidden" name="productId" value="<?PHP echo $productId; ?>" />
	<input type="hidden" name="productDesc" value="<?PHP echo $productDesc; ?>" />
	<input type="hidden" name="ext1" value="<?PHP echo $ext1; ?>" />
	<input type="hidden" name="ext2" value="<?PHP echo $ext2; ?>" />
	<input type="hidden" name="payType" value="<?PHP echo $payType; ?>" />
	<input type="hidden" name="bankId" value="<?PHP echo $bankId; ?>" />
	<input type="hidden" name="redoFlag" value="<?PHP echo $redoFlag; ?>" />
	<input type="hidden" name="pid" value="<?PHP echo $pid; ?>" />
	<input type="submit" id="kqSubmit" name="submit" value="提交到快钱">
  </form>
  <script type="text/javascript">
      document.getElementById("kqSubmit").click();
  </script>
</div>
</body>
</html>
