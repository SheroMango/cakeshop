<?php
/**
 * 订单列表
 */
class OrderAction extends AdminCommonAction
{
   /*
 	* 订单列表
 	*/
	public function ls()
	{
		//模型
		$OrderInfoDao = D('OrderInfo');
		$orderList = $OrderInfoDao->order('id desc')->select();
		//分页
		import('ORG.Util.Page');
		
		$count      = $OrderInfoDao->count();
		$Page       = new Page($count,10);
		$show       = $Page->show();
		//搜索
		$order_sn = trim($_POST['order_sn']);
		if (!empty($order_sn)) {
			$arrMap['order_sn'] = array('like', "%{$order_sn}%");
		}
        //时间过滤
        $dateStart = strtotime(trim($_POST['dateStart']));
        $dateOffset = strtotime(trim($_POST['dateOffset']));
        if(!empty($dateStart) && empty($dateOffset)){
            $arrMap['ctime'] = array('between', array($dateStart, time()));
        }elseif(empty($dateStart) && !empty($dateOffset)){
            $arrMap['ctime'] = array('between', array(0, $dateOffset));
        }elseif(!empty($dateStart) && !empty($dateOffset)){
            $arrMap['ctime'] = array('between', array($dateStart, $dateOffset));
        }
		//排序
		$arrOrder = array('id'=>'desc');
		$display_order = trim($_GET['display_order']);
		$display_order_desc = trim($_GET['desc']);
		if (!empty($display_order)) {
			if(!empty($display_order_desc)){
				$arrOrder = array($display_order.' desc');
				$display_desc = '0';
			}else{
				$arrOrder = array($display_order);
				$display_desc = '1';
			}
		}
		
		$offset   = $Page->firstRow;
        $length   = $Page->listRows;
		$orderList = $OrderInfoDao->getList($arrField, $arrMap, $arrOrder, $offset, $length);
        $arrFormatField = array('order_status_name', 'goods_name', 'goods_size');
		foreach ($orderList as $key => $value) {
            $orderList[$key] = $OrderInfoDao->format($value, $arrFormatField);
			$orderList[$key]['pay_time'] = date("Y-m-d", $value['pay_time']);
			$orderList[$key]['pay_type'] = ($value['pay_type'] == 0) ? '在线支付' : '货到付款';
			$orderList[$key]['pay_status'] = ($value['pay_status'] == 0) ? '未付款' : '已付款';
            $orderList[$key]['ctime_text'] = date("Y-m-d", $value['ctime']);
		}
		//数据整合
		$tplData = array(
			'orderList'    => $orderList,
			'pageHtml' => $show,
			'display_order' => $display_order,
			'display_desc' => $display_desc,
		);
		$this->assign($tplData);
		$this->display();
	}
	/**
	 *编辑
	 */
	public function editOrder()
	{
		$id = intval($_GET['id']);

		//模型
		$orderGoodsDao = D('OrderGoods');
		$orderInfoDao = D('OrderInfo');
		$goodsDao = D('Goods');

		//查询订单信息
		$orderInfo = $orderInfoDao->getInfoById($id);
		$order_id = $orderInfo['id'];

		$orderGoods = $orderGoodsDao->getInfo(array('goods_id', 'type_id', 'goods_number', 'goods_price'), array('order_id'=>$order_id));

		
		$orderInfo['goods_number'] = $orderGoods['goods_number'];
		$orderInfo['goods_price'] = $orderGoods['goods_price'];
		$orderInfo['type_id'] = ($orderGoods['type_id'] == 1) ? '蛋糕' : '鲜花';
		$orderInfo['pay_type'] = ($orderGoods['pay_type'] == 0) ? '在线支付' : '货到付款';
		$orderInfo['pay_time'] = date('Y-m-d H:i:s', $orderInfo['pay_time']);
		$orderInfo['ctime'] = date('Y-m-d H:i:s', $orderInfo['ctime']);
		$orderInfo['pay_status'] = ($orderGoods['pay_status'] == 0) ? '未付款' : '已付款';
		switch ($orderInfo['order_status']) {
			case '0':
				$orderInfo['orderstatus'] = '等待确认';
				break;
			case '1':
				$orderInfo['orderstatus'] = '等待发货';
				break;
			case '2':
				$orderInfo['orderstatus'] = '等待完成';
				break;
			case '3':
				$orderInfo['orderstatus'] = '已完成';
				break;
			case '4':
				$orderInfo['orderstatus'] = '已取消';
				break;
			default:
				# code...
				break;
		}

		$goods_id = $orderGoods['goods_id'];
		//查询商品信息
		$goodsInfo = $goodsDao->getInfoById($goods_id);

		$tplData = array(
			'orderInfo' => $orderInfo,
			'goodsInfo' => $goodsInfo,
		);
		$this->assign($tplData);

		$this->display();
	}
	public function orderStatus()
	{
		$order_status = $_GET['order_status'];
		$id = intval($_GET['id']);
		$OrderInfoDao = D('OrderInfo');
		$data['order_status'] = $order_status;
		$res = $OrderInfoDao->updateData(array('id'=>$id), $data);
        if($order_status == 3){
            $orderInfo = D('OrderInfo')->where('id='.$id)->find();
		    //更新订单状态等信息
            D('OrderInfo')->where('order_sn='.$orderInfo['order_sn'])->setField(array('pay_status'=>2, 'pay_time'=>time()));
		    //会员的消费金额增加，消费次数增加
            D('User')->where('id='.$orderInfo['user_id'])->setInc('spend_count', $orderInfo['order_total']);
            D('User')->where('id='.$orderInfo['user_id'])->setInc('spend_times');
            //商品的销售数量增加
            $goodsInfo = D('OrderGoods')->where('order_id='.$orderInfo['id'])->find();
            D('Goods')->where('id='.$goodsInfo['goods_id'])->setInc('sales', $goodsInfo['goods_number']);
        }
		$this->success('修改成功');

	}
   /*
	* 删除
	*/
	public function del(){
	  	//模型
		$OrderInfoDao = D('OrderInfo');
		$orderGoodsDao = D('OrderGoods');
		//数据
		$delIds  = array();
		$postIds = $_POST['id'];
		if (!empty($postIds)) {
			$delIds = $postIds;
		}
		$getId = intval($_GET['id']);
		if (!empty($getId)) {
			$delIds[] = $getId;
		}
		//删除数据
		if (empty($delIds)) {
			$this->error('请选择您要删除的数据');
		}
		$arrMap['id'] = array('in', $delIds);

		$OrderInfoDao->delData($arrMap);

		$orderGoodsMap = array();
		$orderGoodsMap['order_id'] = array('in', $delIds);
		$orderGoodsDao->delData($orderGoodsMap);
		//页面跳转
		$this->success('删除成功');
	}

    /**
     * 打印订单
     */
    public function doPrint()
    {
		$id = intval($this->_get('id'));
		$orderInfo = D('OrderInfo')->getInfoById($id);
		$orderInfo = D('OrderInfo')->format($orderInfo, array('pay_type_name'));
		$orderGoodsInfo = D('OrderGoods')->where('order_id='.$id)->find();
		$goodsInfo = D('Goods')->getInfoById($orderGoodsInfo['goods_id']);
		$tplData = array(
			'orderInfo' => $orderInfo,
			'goodsInfo' => $goodsInfo,
			'orderGoodsInfo' => $orderGoodsInfo,
		);
		$this->assign($tplData);
		$this->display();
	}

    /**
     * 导出EXCEL表格
     */
    public function exportExcel()
    {
        /** Include PHPExcel */
        require_once './core/PHPExcel/PHPExcel.php';
        // Create new PHPExcel object
        $objPHPExcel = new PHPExcel();
        // Set document properties
        $objPHPExcel->getProperties()->setCreator("深蓝解码")
							 ->setLastModifiedBy("深蓝解码")
							 ->setTitle("蛋糕商城")
							 ->setSubject("蛋糕商城")
							 ->setDescription("蛋糕商城订单数据");
        // Add some data
        $orderInfoObj = D('OrderInfo');
        $arrField = array();
        $arrMap = array();
        $arrOrder = array('ctime desc');
        $orderList = $orderInfoObj->getList($arrField, $arrMap, $arrOrder);
        $arrFormatField = array('order_status_name', 'goods_name', 'goods_size');
		foreach ($orderList as $key => $value) {
            $orderList[$key] = $orderInfoObj->format($value, $arrFormatField);
			$orderList[$key]['pay_time'] = date("Y-m-d", $value['pay_time']);
			$orderList[$key]['pay_type'] = ($value['pay_type'] == 0) ? '在线支付' : '货到付款';
			$orderList[$key]['pay_status'] = ($value['pay_status'] == 0) ? '未付款' : '已付款';
            $orderList[$key]['ctime_text'] = date("Y-m-d", $value['ctime']);
		}

        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', '订单ID')
            ->setCellValue('B1', '订单编号')
            ->setCellValue('C1', '用户姓名')
            ->setCellValue('D1', '商品名称')
            ->setCellValue('E1', '订单总额')
            ->setCellValue('F1', '订购人姓名')
            ->setCellValue('G1', '订购人手机号码')
            ->setCellValue('H1', '订购人地址')
            ->setCellValue('I1', '收货人姓名')
            ->setCellValue('J1', '收货人手机号码')
            ->setCellValue('K1', '收货人地区')
            ->setCellValue('L1', '收货人详细地址')
            ->setCellValue('M1', '运费')
            ->setCellValue('N1', '送达时间')
            ->setCellValue('O1', '订制语')
            ->setCellValue('P1', '备注')
            ->setCellValue('Q1', '支付方式')
            ->setCellValue('R1', '支付状态')
            ->setCellValue('S1', '订单创建时间');
        foreach($orderList as $k=>$v){
            $objPHPExcel->getActiveSheet()
                ->setCellValue('A'.($k+2), $v['id'])
                ->setCellValue('B'.($k+2), $v['order_sn'])
                ->setCellValue('C'.($k+2), $v['user_name'])
                ->setCellValue('D'.($k+2), $v['goods_name'])
                ->setCellValue('E'.($k+2), $v['order_total'])
                ->setCellValue('F'.($k+2), $v['order_name'])
                ->setCellValue('G'.($k+2), $v['order_mobile'])
                ->setCellValue('H'.($k+2), $v['order_address'])
                ->setCellValue('I'.($k+2), $v['consignee_name'])
                ->setCellValue('J'.($k+2), $v['consignee_mobile'])
                ->setCellValue('K'.($k+2), $v['consignee_district'])
                ->setCellValue('L'.($k+2), $v['consignee_address'])
                ->setCellValue('M'.($k+2), $v['freight'])
                ->setCellValue('N'.($k+2), $v['arrive_time'])
                ->setCellValue('O'.($k+2), $v['custom_lang'])
                ->setCellValue('P'.($k+2), $v['postscript'])
                ->setCellValue('Q'.($k+2), $v['pay_type'])
                ->setCellValue('R'.($k+2), $v['pay_status'])
                ->setCellValue('S'.($k+2), $v['ctime_text']);
        }
        // Rename worksheet
        $objPHPExcel->getActiveSheet()->setTitle('订单数据');
        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);
        // Redirect output to a client’s web browser (Excel5)
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="订单数据.xls"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

        // If you're serving to IE over SSL, then the following may be needed
        header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header ('Pragma: public'); // HTTP/1.0

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
    }
}
