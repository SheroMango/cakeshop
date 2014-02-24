<?php
/**
 * 前台：鲜花列表控制器
 */

class FlowerAction extends CommonAction
{
/**
	 * 列表
	 */
	public function ls()
	{
		//模型
		$goodsDao = D('Goods');
		$GoodsAttrDao = D('GoodsAttr');
		$attrDao  = D('Attr');
		//条件
		$arrField = array('id', 'name', 'price', 'brief', 'tag', 'pic', 'discount');
		$arrMap = array('status'=>1, 'cat_id'=>2);
		$arrOrder = array();

		//筛选
		$shaixuan = $_POST['shaixuan'];
		if (!empty($shaixuan['zsdx'])) {
			$zsdx = $shaixuan['zsdx'];
			$zsdxMap = array('attr_value'=>$zsdx, 'attr_id'=>243);
			$goods_id = $GoodsAttrDao->getList(array('goods_id'),$zsdxMap);
			foreach ($goods_id as $key => $value) {
				$goodsid[] = $value['goods_id'];
			}
			$arrMap['id'] = array('in', $goodsid);
		}
		if (!empty($shaixuan['hc'])) {
			$hc = $shaixuan['hc'];
			$hcMap = array('attr_value'=>$hc, 'attr_id'=>241);
			$goods_id = $GoodsAttrDao->getList(array('goods_id'),$hcMap);
			foreach ($goods_id as $key => $value) {
				$goodsid[] = $value['goods_id'];
			}
			$arrMap['id'] = array('in', $goodsid);
		}
		if (!empty($shaixuan['bzlb'])) {
			$bzlb = $shaixuan['bzlb'];
			$bzlbMap = array('attr_value'=>$bzlb, 'attr_id'=>245);
			$goods_id = $GoodsAttrDao->getList(array('goods_id'),$bzlbMap);
			foreach ($goods_id as $key => $value) {
				$goodsid[] = $value['goods_id'];
			}
			$arrMap['id'] = array('in', $goodsid);
		}
		if (!empty($shaixuan['shjr'])) {
			$shjr = $shaixuan['shjr'];
			$shjrMap = array('attr_value'=>$shjr, 'attr_id'=>244);
			$goods_id = $GoodsAttrDao->getList(array('goods_id'),$shjrMap);
			foreach ($goods_id as $key => $value) {
				$goodsid[] = $value['goods_id'];
			}
			$arrMap['id'] = array('in', $goodsid);
		}
		if (!empty($shaixuan['jg'])) {
			$jg = $shaixuan['jg'];
			switch ($jg) {
				case '100':
					$arrMap['price'] = array('lt', 100);
					break;
				case '200':
					$arrMap['price'] = array(array('egt', 100), array('elt', 200));
					break;
				case '300':
					$arrMap['price'] = array(array('egt', 200), array('elt', 300));
					break;
				case '400':
					$arrMap['price'] = array(array('egt', 300), array('elt', 500));
					break;
				case '500':
					$arrMap['price'] = array('egt', 500);
					break;
				default:
					# code...
					break;
			}
		}


		//排序条件
		$orderDesc = $_GET['order'];
		if ($orderDesc == 'price') {
			$arrOrder = array('price'=>'desc');
		}elseif ($orderDesc == 'sales') {
			$arrOrder = array('sales'=>'desc');
		}else{
			$arrOrder = array('views'=>'desc');
		}


		//分页
		import('ORG.Util.Page');
		$count      = $goodsDao->where($arrMap)->count();
		$Page       = new Page($count,20);	
		$show       = $Page->show();
		$offset   = $Page->firstRow;
        $length   = $Page->listRows;
        //列表
		$arrList = $goodsDao->getList($arrField, $arrMap, $arrOrder, $offset, $length);
		foreach ($arrList as $key => $value) {
			$arrList[$key]['pic']  = getPicPath($value['pic'], 'b');
		}

		//输出到模版
		$tplData = array(
			'arrList'=>$arrList,
			'pageHtml'=>$show,
			'orderDesc'=>$orderDesc,
		);
		$this->assign($tplData);
		$this->display();
	}
	/**
	 * 详情
	 */
	public function detail()
	{
		$goods_id = intval($_GET['id']);//接受id
		//模型
		$goodsDao = D('Goods');
		$GoodsAttrDao = D('GoodsAttr');
		$attrDao  = D('Attr');
		$goodsDao->where('id='.$goods_id)->setInc('views', 1);
		//条件
		$arrField = array('id', 'name', 'price', 'discount_desc', 'desc', 'pic');
		//详情信息
		$flowerInfo = $goodsDao->getInfoById($goods_id, $arrField);
		$flowerInfo['pic']  = getPicPath($flowerInfo['pic']);
		
		//选择价格
		$size_id = intval($_GET['size_id']);
		if (!empty($size_id)) {
			$sizePriceMap['id'] = $size_id;
			$sizePrice = $GoodsAttrDao->getOneValue($sizePriceMap, 'attr_price');
			$flowerInfo['price'] = number_format(($flowerInfo['price']+$sizePrice), 2);
		} else {
			$sizePrice = 0;
		}
		//尺寸
		$sizeMap = array('goods_id'=>$goods_id, 'attr_id'=>246);
		$sizeField = array('id', 'attr_value', 'attr_price');
		$sizeList = $GoodsAttrDao->getList($sizeField, $sizeMap);
		foreach ($sizeList as $key => $value) {
			$sizeList[$key]['attr_price'] = $value['attr_price'];
		}
		//属性信息
		$attrMap['attr_id'] = array('neq', 246);
		$attrMap['goods_id'] = $goods_id; 
		$attrField = array('id', 'attr_id', 'attr_value', 'attr_price');
		$attList = $GoodsAttrDao->getList($attrField, $attrMap);
		foreach ($attList as $key => $value) {
			$attList[$key]['attr_id'] = $attrDao->getOneValue(array('id'=>$value['attr_id']), 'attr_name');
		}

		//输出到模版
		$tplData = array(
			'flowerInfo' => $flowerInfo,
			'sizeList'   => $sizeList,
			'sizePrice'  => $sizePrice,
			'attList'    => $attList,
		);
		$this->assign($tplData);
		$this->display();
	}
	//价格选择
	public function selprice()
	{
		$data = $_POST;
		$size_id = intval($data['size_id']);
		$id = intval($data['id']);
		//模型
		$goodsDao = D('Goods');
		$GoodsAttrDao = D('GoodsAttr');
		$attrDao  = D('Attr');

		$arrField = array('id', 'price');
		//详情信息
		$cakeInfo = $goodsDao->getInfoById($id, $arrField);
		//选择价格
		$sizePriceMap['id'] = $size_id;
		$sizePrice = $GoodsAttrDao->getOneValue($sizePriceMap, 'attr_price');
		$price = number_format(($cakeInfo['price']+$sizePrice), 2);
		$sizeid = $size_id;
		echo '{"price":'.$price.',"sizeid":'.$sizeid.'}';
	}
	//咨询
	public function consult()
	{
		$flowerInfo['id'] = intval($_GET['id']);
		$this->assign('flowerInfo', $flowerInfo);
		$this->display();
	}
	//评价
	public function evaluate()
	{
		$flowerInfo['id'] = intval($_GET['id']);
		$this->assign('flowerInfo', $flowerInfo);
		$this->display();
	}
	//购前续知
	public function notice()
	{
		$flowerInfo['id'] = intval($_GET['id']);
		$this->assign('flowerInfo', $flowerInfo);
		$this->display();
	}
}