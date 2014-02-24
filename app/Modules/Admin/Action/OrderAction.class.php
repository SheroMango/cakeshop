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
		$arrOrder = array('id'=>'desc');
		$offset   = $Page->firstRow;
        $length   = $Page->listRows;
		$orderList = $OrderInfoDao->getList($arrField, $arrMap, $arrOrder, $offset, $length);
        $arrFormatField = array('order_status_name');
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
}
