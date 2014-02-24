<?php
/**
 * 前台首页控制器
 */
class IndexAction extends CommonAction
{
	/**
	 * 首页
	 */
	public function index()
	{
		//模型
		$goodsDao     = D('Goods');
		$GoodsAttrDao = D('GoodsAttr');
		$orderInfoDao = D('OrderInfo');
		
		/*********蛋糕************/
		//条件 
		$cakeField = array('id', 'name', 'price', 'pic', 'discount');
		$cakeMap   = array('is_index'=>1, 'status'=>1, 'cat_id'=>1);
		$cakeOrder = array();
		//列表
		$cakeList = $goodsDao->getList($cakeField, $cakeMap, $cakeOrder, 0, 6);
		foreach ($cakeList as $key => $value) {
			$goods_id = intval($value['id']);
			$sizeMap = array('goods_id'=>$goods_id, 'attr_id'=>213, 'attr_price'=>0);
			$cakeList[$key]['size'] = $GoodsAttrDao->getOneValue($sizeMap, 'attr_value');
			$cakeList[$key]['pic']  = getPicPath($value['pic'], 's');
		}
		
		/*********鲜花************/
		//条件 
		$flowerField = array('id', 'name', 'price', 'pic');
		$flowerMap   = array('is_index'=>1, 'status'=>1, 'cat_id'=>2);
		$flowerOrder = array();
		//列表
		$flowerList = $goodsDao->getList($flowerField, $flowerMap, $flowerOrder, 0, 3);
		foreach ($flowerList as $key => $value) {
			$flowerList[$key]['pic']  = getPicPath($value['pic'], 's');
		}
		
		//最新订单
        $orderField = array('consignee_district');
        $orderMap = array();
        $orderOrder = array('ctime desc');
		$orderList = $orderInfoDao->getList($orderField, $orderMap, $orderOrder);
		
		//输出到模版
		$tplData = array(
			'cakeList' => $cakeList,
			'flowerList'=>$flowerList,
			'orderList'=>$orderList,
            'city' => ($_SESSION['current_city']) ? $_SESSION['current_city'] : '未知',
		);
		$this->assign($tplData);
		$this->display();
	}

    /**
     * 获取地址位置
     */
    public function getLocation(){
        echo 'haha';
        $latitude = $this->_post('latitude');
        $longitude = $this->_post('longitude');
        if(!empty($latitude)){
            $_SESSION['latitude'] = $latitude;
            $_SESSION['longitude'] = $longitude;
            $this->getAddress();
        }
        echo '1';

    }

    /**
     * 百度API：根据经纬度获取城市名称
     */
    public function getAddress(){
        $url = 'http://api.map.baidu.com/geocoder?location='.$_SESSION['latitude'].','.$_SESSION['longitude'].'&output=json&key=5c1da412cb98cbde54f87d45d8feda56';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($ch);
        curl_close($ch);
        $result = json_decode($result, true);
        $city = $result['result']['addressComponent']['city'];
        $_SESSION['current_city'] = $city;
    }

}
