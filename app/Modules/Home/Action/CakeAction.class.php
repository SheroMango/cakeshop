<?php
/**
 * 前台：蛋糕列表控制器
 */

class CakeAction extends CommonAction
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
		$brandDao = D('Brand');

		//条件
		$arrField = array('id', 'name', 'price', 'brief', 'tag', 'pic', 'discount');
		$arrMap = array('status'=>1, 'cat_id'=>1);
		$arrOrder = array();

		//vip
		$is_vip = $_GET['is_vip'];
		if (!empty($is_vip)) {
			$arrMap['is_vip'] = 1;
        }else{
            $arrMap['is_vip'] = 0;
        }

		//筛选
		$shaixuan = $_POST['shaixuan'];
		if (!empty($shaixuan['brand'])) {
			$arrMap['brand_id'] = $shaixuan['brand'];
		}
		if (!empty($shaixuan['syrs'])) {
			$syrs = $shaixuan['syrs'];
			$syrsMap = array('attr_id'=>238);
			switch ($syrs) {
				case '2':
				 	$syrsMap['attr_value'] = array(array('egt', 2), array('elt', 3));
					break;
				case '3':
					$syrsMap['attr_value'] = array(array('egt', 3), array('elt', 5));
					break;
				case '5':
					$syrsMap['attr_value'] = array(array('egt', 5), array('elt', 8));
					break;
				case '8':
					$syrsMap['attr_value'] = array(array('egt', 8), array('elt', 12));
					break;
				case '12':
					$syrsMap['attr_value'] = array(array('egt', 12), array('elt', 20));
					break;
				case '20':
					$syrsMap['attr_value'] = array('egt', 20);
					break;
				default:
					# code...
					break;
			}
			$goods_id = $GoodsAttrDao->getList(array('goods_id'),$syrsMap);
			foreach ($goods_id as $key => $value) {
				$goodsid[] = $value['goods_id'];
			}
			$arrMap['id'] = array('in', $goodsid);

		}
		if (!empty($shaixuan['kw'])) {
			$kw = $shaixuan['kw'];
			$kwMap = array('attr_value'=>$kw, 'attr_id'=>211);
			$goods_id = $GoodsAttrDao->getList(array('goods_id'),$kwMap);
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

		//品牌
		$brandList = $brandDao->getList(array('id', 'name'),array('type_id'=>1));
		
		//输出到模版
		$tplData = array(
            'title' => '蛋糕订购',
			'arrList'=>$arrList,
			'pageHtml'=>$show,
			'orderDesc'=>$orderDesc,
			'brandList'=>$brandList,
			'syrsList'=>$syrsList,
			'kwList'=>$kwList,
            'is_vip' => $is_vip,
		);
		$this->assign($tplData);
		$this->display();
	}
	/**
	 * 详情
	 */
	public function detail()
	{
		$goods_id = intval($_REQUEST['id']);//接受id
		//模型
		$goodsDao = D('Goods');
		$GoodsAttrDao = D('GoodsAttr');
		$attrDao  = D('Attr');
		$goodsDao->where('id='.$goods_id)->setInc('views', 1);
		//条件
		$arrField = array('id', 'name', 'price', 'discount', 'discount_desc', 'desc', 'pic');
		//详情信息
		$cakeInfo = $goodsDao->getInfoById($goods_id, $arrField);
		$cakeInfo['pic']  = getPicPath($cakeInfo['pic']);
		
		//选择价格
		$size_id = intval($_GET['size_id']);
		if (!empty($size_id)) {
			$sizePriceMap['id'] = $size_id;
			$sizePrice = $GoodsAttrDao->getOneValue($sizePriceMap, 'attr_price');
			$cakeInfo['price'] = number_format(($cakeInfo['price']+$sizePrice), 2);
		} else {
			$sizePrice = 0;
		}
		//尺寸
		$sizeMap = array('goods_id'=>$goods_id, 'attr_id'=>213);
		$sizeField = array('id', 'attr_value', 'attr_price');
		$sizeList = $GoodsAttrDao->getList($sizeField, $sizeMap);
		foreach ($sizeList as $key => $value) {
			$sizeList[$key]['attr_price'] = $value['attr_price'];
		}
		
		//属性信息
		$attrMap['attr_id'] = array('neq', 213);
		$attrMap['goods_id'] = $goods_id; 
		$attrField = array('id', 'attr_id', 'attr_value', 'attr_price');
		$attList = $GoodsAttrDao->getList($attrField, $attrMap);
		foreach ($attList as $key => $value) {
			$attList[$key]['attr_id'] = $attrDao->getOneValue(array('id'=>$value['attr_id']), 'attr_name');
		}
		//输出到模版
		$tplData = array(
            'title'    => '蛋糕详情',
            'current'  => 'detail',
            'goods_id' => $cakeInfo['id'],
			'cakeInfo' => $cakeInfo,
			'sizeList' => $sizeList,
			'sizePrice'=> $sizePrice,
			'attList'  => $attList,
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

	//购前续知
	public function notice()
	{
		$goods_id = intval($_REQUEST['goods_id']);
        $tplData = array(
            'title' => '购前须知',
            'current' => 'notice',
            'goods_id' => $goods_id,
        );
		$this->assign($tplData);
		$this->display();
	}

}
