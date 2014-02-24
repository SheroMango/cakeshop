<?php
require('../../libraries/common.inc.php');
require('../../share.inc.php');

$msg = trim($_GET['msg']);
if ($msg = 'success') {
	flash('充值成功', 'virtual-office/coin.php?do=recharge', 1);
}

?>