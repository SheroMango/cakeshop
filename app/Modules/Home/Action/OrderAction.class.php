<?php
/*
 * 订单控制器
 */
class OrderAction extends HomeCommonAction {
	
	public function ls()
	{
        $orderInfoDao = D('OrderInfo');
        $arrField = array('id, user_id, order_status, pay_type, ctime');
        //此处存在安全隐患，如果用户此处SESSION失效，则得到的是全部用户的订单
        $arrMap['user_id'] = array('eq', $_SESSION['uid']);
        $arrOrder = array('ctime desc');

        $count = $orderInfoDao->getCount($arrMap);
        $page = page($count);
        $pageHtml = $page->show();

        $orderList = $orderInfoDao->getList($arrField, $arrMap, $arrOrder, $page->firstRow, $page->listRows);

        foreach($orderList as $k=>$v){
            $goods_id = D('OrderGoods')->where('order_id='.$v['id'])->getField('goods_id');
            $goodsInfo = D('Goods')->where('id='.$goods_id)->getField('id, name, pic, tag, brief, price');
            $orderList[$k] = $orderInfoDao->format($v, array('ctime_text', 'order_status_name', 'pay_type_name'));
            $orderList[$k]['goods'] = D('Goods')->format($goodsInfo[$goods_id], array('pic_name', 'url_cake'));
        }
 
		//输出到模版
		$tplData = array(
            'title' => '我的订单',
			'arrList'=>$orderList,
			'pageHtml'=>$pageHtml,
		);
		$this->assign($tplData);
		$this->display();
	}
	
	/**
     * 获取城市列表
     */
    public function getZoneList()
    {
        $id = intval($this->_get('id'));
		
		//freight list
		$freightList = D('Freight')->select();
		foreach($freightList as $k=>$v){
			$freightList[$k] = array_merge($v, D('Zone')->where('id='.$v['zone_id'])->find());
			if($id == $freightList[$k]['pid']){
				$newList[] = $freightList[$k];
			}
		}
		foreach($newList as $k=>$v){
			$newList[$k]['name'] = urlencode($v['name']);
		}
        echo urldecode(json_encode($newList));
    }
	
	public function writeOrder()
	{
		//模型
		$goodsDao     = D('Goods');
		$goodsAttrDao = D('GoodsAttr');

        //根据历史记录自动填写订单信息
        $preOrderInfo = D('OrderInfo')->where('user_id='.$_SESSION['uid'])->order('ctime desc')->find();
		
		//地区运费
		$freightList = D('Freight')->select();
		foreach($freightList as $k=>$v){
			$cityIds[] = D('Zone')->where('id='.$v['zone_id'])->getField('pid');
		}
		$cityMap['id'] = array('in', $cityIds);
		$cityList = D('Zone')->where($cityMap)->select();

		//接受表单参数
		$goods = $_POST['goods'];
        if(empty($goods)){
            $this->redirect('Public/is_empty', array('title'=>'订单错误', 'content'=>'订单会话已过期，请返回上一步重新填写'));
        }

		$goods_id = intval($goods['id']);
		$price = floatval($goods['price']);
		$sizeid = $goods['sizeid'];
		$num = $goods['num'];
		//订单信息
		$goodsField = array('id', 'name');
		$orderInfo['goods_name'] = $goodsDao->getOneValue(array('id'=>$goods_id), 'name');
		$discount = $goodsDao->getOneValue(array('id'=>$goods_id), 'discount');
		$orderInfo['goods_id'] = $goods_id;
		$orderInfo['price'] = $price;//单价
		$orderInfo['sumprice'] = $price*$num;//总价
		//优惠总额
		$orderInfo['discount'] = number_format($orderInfo['sumprice']-$orderInfo['sumprice']*($discount/100), 2);
		//优惠后价格
		$orderInfo['sumdiscount'] = $orderInfo['sumprice']-$orderInfo['discount'];
		$orderInfo['num'] = $num;
		$orderInfo['size'] = $goodsAttrDao->getOneValue(array('id'=>$sizeid), 'attr_value');
		//输出到模版
		$tplData = array(
			'title' => '填写订单',
			'orderInfo' => $orderInfo,
            'preOrderInfo' => $preOrderInfo,//自动填写的订单信息
			'cityList' => $cityList,
		);
		$this->assign($tplData);
		$this->display();
	}
	//订单写入数据库
	public function doOrderAdd()
	{
		//模型
		$orderGoodsDao= D('OrderGoods'); 
		$orderInfoDao = D('OrderInfo');

		$orderInfo = $_POST['order'];
		$orderGoods = $_POST['goods'];

		$randStr = str_shuffle('1234567890');
		$rand = substr($randStr,0,5);
		$orderInfo['order_sn'] = time().$rand;
		$orderInfo['consignee_district'] = $orderInfo['consignee_city'].$orderInfo['consignee_district'];
		$orderInfo['user_id'] = session('uid');
		$goods_id = $orderGoods['goods_id'];
		$orderInfo['type_id'] = D('Goods')->where('id='.$goods_id)->getField('cat_id');
		$orderInfo['order_status'] = 0;
		$orderInfo['arrive_time'] = strtotime($orderInfo['arrive_time']);
		$orderInfo['ctime'] = time();
		$order_id = $orderInfoDao->addData($orderInfo);
		
		$orderGoods['type_id'] = $orderInfo['type_id'];
		$orderGoods['order_id'] = $order_id ;

		$orderGoodsDao->addData($orderGoods);
		$order_paytype = $orderInfo['pay_type'];
		$data = array(
			'status'  => 'success',
			'url'     => U('Home/Order/orderDetail',array('id'=>$order_id)),
			'content' => '订单提交成功',
		);
		
		/* 短信通知 */
		$msgMobile = '13878857057';
		$msgContent = '您好,最新订单号:'.$orderInfo['order_sn'].'下单,请注意确认<3156蛋糕>';
		$url = 'http://sms.106vip.com/sms.aspx?action=send&userid=16328&account=tanhuayou&password=asdf1234&mobile='.$msgMobile.'&content='.$msgContent.'【316蛋糕商城】&sendTime=&taskName=订单通知&checkcontent=1&mobilenumber=1&countnumber=1';
		file_get_contents($url);
		
		echo json_encode($data);
		
	}
	//在线支付
	public function orderDetail()
	{
		//读取订单数据
		$id = $_GET['id'];
		$orderInfoDao = D('OrderInfo');
		$orderGoodsDao = D('OrderGoods');
		$goodsDao = D('Goods');
		$brandDao = D('Brand');

		//订单信息
		$orderField = array('id', 'order_sn', 'ctime', 'order_total', 'order_status', 'pay_type', 'pay_status', 'freight');
		$orderInfo = $orderInfoDao->getInfoById($id, $orderField);
        $orderInfo = $orderInfoDao->format($orderInfo, array('order_status_name'));
		$orderInfo['ctime'] = date('Y-m-d H:i:s', $orderInfo['ctime']);
		
		//商品信息
		$goodsFiled = array('goods_id', 'goods_price', 'goods_number');
		$godosMap = array('order_id'=>$id);
		$goodsInfo = $orderGoodsDao->getInfo($goodsFiled, $godosMap);
		$goods_id = $goodsInfo['goods_id'];
		$goodsInfo['goods_name'] = $goodsDao->getOneValue(array('id'=>$goods_id), 'name');
		$brand_id = $goodsDao->getOneValue(array('id'=>$goods_id), 'brand_id');
		$goodsInfo['brand_name'] = $brandDao->getOneValue(array('id'=>$brand_id), 'name');

		//收货人信息
		$consigneeField = array('consignee_name', 'consignee_mobile', 'consignee_district', 'consignee_address', 'arrive_time');
		$consigneeInfo = $orderInfoDao->getInfoById($id, $consigneeField);
		$consigneeInfo['arrive_time'] = date('Y-m-d H:i:s', $consigneeInfo['arrive_time']);

		//订购人信息
		$orderOneField = array('order_name', 'order_mobile');
		$orderOneInfo = $orderInfoDao->getInfoById($id, $orderOneField);
		//输出到模版
		$tplData = array(
			'title' => '我的订单',
			'orderInfo' => $orderInfo,
			'goodsInfo' => $goodsInfo,
			'consigneeInfo'=>$consigneeInfo,
			'orderOneInfo'=>$orderOneInfo,
		);
		$this->assign($tplData);
		$this->display();
	}

    /**
     * 查看进度
     */
    public function orderStatus(){
        $id = intval($_GET['id']);
        $orderInfo = D('OrderInfo')->getInfoById($id);
        $tplData = array(
            'title' => '查看进度',
            'orderInfo' => $orderInfo,
        );
        $this->assign($tplData);
        $this->display();
    }

    /**
     * 确认收货
     */
    public function setStatus(){
        $toStatus = intval($_GET['toStatus']);
        $orderInfoDao = D('OrderInfo');
        $order_sn = $_REQUEST['order_sn'];
        $id = $_REQUEST['id'];
        
        //会员认证
        $map['order_sn'] = array('eq', $order_sn);
        $user_id = $orderInfoDao->where($map)->getField('user_id');
        if($user_id != $_SESSION['uid']){
            $this->error('认证错误', U('Home/Index/index'));
        }
        
        //会员确认收货
        $orderInfoDao->where($map)->setField('order_status', $toStatus);
        redirect(U('Order/orderDetail', array('id'=>$id)));

    }

	//货到付款
	public function orderDetails()
	{
		//读取订单数据
		$id = $_GET['id'];
		$orderInfoDao = D('OrderInfo');
		$orderGoodsDao = D('OrderGoods');
		$goodsDao = D('Goods');
		$brandDao = D('Brand');

		//订单信息
		$orderField = array('order_sn', 'ctime', 'order_total');
		$orderInfo = $orderInfoDao->getInfoById($id, $orderField);
		$orderInfo['ctime'] = date('Y-m-d H:i:s', $orderInfo['ctime']);
		
		//商品信息
		$goodsFiled = array('goods_id', 'goods_price', 'goods_number');
		$godosMap = array('order_id'=>$id);
		$goodsInfo = $orderGoodsDao->getInfo($goodsFiled, $godosMap);
		$goods_id = $goodsInfo['goods_id'];
		$goodsInfo['goods_name'] = $goodsDao->getOneValue(array('id'=>$goods_id), 'name');
		$brand_id = $goodsDao->getOneValue(array('id'=>$goods_id), 'brand_id');
		$goodsInfo['brand_name'] = $brandDao->getOneValue(array('id'=>$brand_id), 'name');

		//收货人信息
		$consigneeField = array('consignee_name', 'consignee_mobile', 'consignee_district', 'consignee_address', 'arrive_time');
		$consigneeInfo = $orderInfoDao->getInfoById($id, $consigneeField);
		$consigneeInfo['arrive_time'] = date('Y-m-d H:i:s', $consigneeInfo['arrive_time']);

		//订购人信息
		$orderOneField = array('order_name', 'order_mobile');
		$orderOneInfo = $orderInfoDao->getInfoById($id, $orderOneField);
		//输出到模版
		$tplData = array(
			'title' => '我的订单',
			'orderInfo' => $orderInfo,
			'goodsInfo' => $goodsInfo,
			'consigneeInfo'=>$consigneeInfo,
			'orderOneInfo'=>$orderOneInfo,
		);
		$this->assign($tplData);
		$this->display();
	}
}

