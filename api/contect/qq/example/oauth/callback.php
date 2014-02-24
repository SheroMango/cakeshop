<?php
require_once("../../API/qqConnectAPI.php");
$qc = new QC();
echo $qc->qq_callback();
echo '<hr />';
echo $qc->get_openid();
