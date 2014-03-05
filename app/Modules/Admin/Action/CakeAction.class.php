<?php
/**
 * 商品控制器
 */
class CakeAction extends AdminCommonAction
{
	
	/**********商品相关************/
	/**
	 * 商品列表
	 */
	public function ls()
	{
				
		//模型
		$goodsDao    = D('Goods');
		$categoryDao = D('Category');
		$brandDao    = D('Brand');
		//分页
		import('ORG.Util.Page');
		$count      = $goodsDao->count();
		$Page       = new Page($count,20);
		$show       = $Page->show();
		//条件
		$arrField = array('id', 'name', 'brand_id', 'price', 'is_on_sale', 'is_index', 'number', 'discount', 'mtime', 'sales');
		$arrMap   = array('cat_id'=>1);
		//搜索
		$name = trim($_POST['name']);
		if (!empty($name)) {
			$arrMap['name'] = array('like', "%{$name}%");
		}
        //排序
        $search_sales = $_REQUEST['sales'];
        if(!empty($search_sales)){
            $arrMap['sales'] = array('gt', $search_sales);
        }
		$arrOrder = array('id'=>'desc');
        $display_order = trim($_GET['display_order']);
        $display_order_desc = trim($_GET['desc']);
        if(!empty($display_order)){
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
		$pageList = $goodsDao->getList($arrField, $arrMap, $arrOrder, $offset, $length);
		
		if (!empty($pageList)) {
            $arrFormatField = array('sale_name', 'mtime_text', 'url_admin_edit', 'url_admin_del');
            foreach ($pageList as $k=>$v) {
                $pageList[$k] = $goodsDao->format($v, $arrFormatField);
				$brand_id = $v['brand_id'];
				$brandMap = array('id'=>$brand_id);
				$pageList[$k]['brand_id'] = $brandDao->getOneValue($brandMap, 'name');
            }
        }
        // 输出到模板
        $tplData = array(
            'pageList' => $pageList,
            'pageHtml' => $show,
            'display_order' => $display_order,
            'display_desc' => $display_desc,

        );
        $this->assign($tplData);
		$this->display();
	}
	/**
	 * 添加商品
	 */
	public function addCake()
	{
		//模型
		$goodsDao    = D('Goods');
		$categoryDao = D('Category');
		$brandDao    = D('Brand');
		$attrDao     = D('Attr');
		//条件
		$arrMap     = array('display_status'=>1, 'type_id'=>1);
		$arrOrder   = array('id'=>'desc');
		$brandField = array('id', 'name');
		$brandList  = $brandDao->getList($brandField, $arrMap, $arrOrder);
		
		//属性类型
		$attrMap   = array('type_id'=>1);
		$attrField = array('id', 'attr_name');
		$attrList = $attrDao->getList($attrField, $attrMap);
		 // 输出到模板
        $tplData = array(
            'brandList' => $brandList,
        );
        $this->assign($tplData);
		$this->display();
	}
	/**
	 * 处理：添加商品
	 */
	public function doAddCake()
	{
		$pageInfo = $_POST['page'];
		$pageSize = $_POST['size'];
		$pageAttr = $_POST['attr'];
		$pagePic  = $_POST['pic'];
		//模型
		$goodsDao = D('Goods');
		$goodsPic = D('GoodsPic');
		$goodsAttr= D('GoodsAttr');
		
		
		$pageInfo['cat_id'] = 1;
		$pageInfo['ctime'] = time();
		$pageInfo['mtime'] = time();
		//图片
	
		$uploadList = uploadPic();
		$fileNum = count($uploadList);
		if ($uploadList['image1']['name']) {
			$pageInfo['pic'] = $uploadList['image1']['savename'];
			$goods_id = $goodsDao->addData($pageInfo);
			for($i=2; $i<=$fileNum; $i++){
				$pagePic['goods_id'] = $goods_id;
				$pagePic['pic']      = $uploadList['image'.$i]['savename'];
				$pagePic['desc']     = $pagePic['desc'.$i];
				$goodsPic->addData($pagePic);
			}
		} elseif($uploadList['image2']['name']) {
			$goods_id = $goodsDao->addData($pageInfo);
			for($i=2; $i<=$fileNum+1; $i++){
				$pagePic['goods_id'] = $goods_id;
				$pagePic['pic']      = $uploadList['image'.$i]['savename'];
				$pagePic['desc']     = $pagePic['desc'.$i];
				$goodsPic->addData($pagePic);
			}
		} else {
			$goods_id = $goodsDao->addData($pageInfo);
		}

		
		$attrInfo = array();
		$attrInfo['goods_id'] = $goods_id;
		//属性
		
		$attrInfo['attr_id'] = 1;
		$attrInfo['attr_value'] = $pageAttr['cl'];
		$goodsAttr->addData($attrInfo);
	
	
		$attrInfo['attr_id'] = 2;
		$attrInfo['attr_value'] = $pageAttr['bzq'];
		$goodsAttr->addData($attrInfo);
	
	
		$attrInfo['attr_id'] = 211;
		$attrInfo['attr_value'] = $pageAttr['kw'];
		$goodsAttr->addData($attrInfo);
	
	
		$attrInfo['attr_id'] = 212;
		$attrInfo['attr_value'] = $pageAttr['zctj'];
		$goodsAttr->addData($attrInfo);
	
	
		$attrInfo['attr_id'] = 238;
		$attrInfo['attr_value'] = $pageAttr['syrs'];
		$goodsAttr->addData($attrInfo);
		
		//尺寸
		$sizeInfo = array();
		if (!empty($pageSize)) {
			$sizeCount = count($pageSize)/2;
			for($i=0; $i<$sizeCount; $i++){
				$sizeInfo['goods_id'] = $goods_id;
				$sizeInfo['attr_id']  = 213;
				$sizeInfo['attr_value']= $pageSize['size'.$i];
				$sizeInfo['attr_price']= $pageSize['price'.$i];
				$goodsAttr->addData($sizeInfo);
			}
		}

		$this->success('添加成功', U('Admin/Cake/ls'));
		
		
	}
	/**
	 * 修改商品信息
	 */
	public function editCake()
	{
		//接收id
		$id = intval($_GET['id']);
		//模型
		$goodsDao        = D('Goods');
		$brandDao        = D('Brand');
		$goodsAttrDao    = D('GoodsAttr');
		$goodsPicDao     = D('GoodsPic');
		
		//商品信息
		$goodsInfo = $goodsDao->getInfoById($id);
		
		//商品品牌
		$brandField = array('id', 'name');
		$brandMap = array('type_id'=>1);
		$brandList = $brandDao->getList($brandField, $brandMap);
		
		//属性
		$attrField = array();
		$attrMap = array('goods_id'=>$id);
		$attrMap['attr_id'] = array('neq',213);
		$attrList = $goodsAttrDao->getList($attrField, $attrMap);
		//图片
		$picField = array('id', 'pic', 'desc');
		$picMap = array('goods_id'=>$id);
		$picList = $goodsPicDao->getList($picField, $picMap);
		
		$sizeMap = array('goods_id'=>$id, 'attr_id'=>213);
		$sizeCount = $goodsAttrDao->getCount($sizeMap);
		$sizeList = $goodsAttrDao->getList($attrField, $sizeMap);
		

		//输出到模板
        $tplData = array(
            'goodsInfo'  => $goodsInfo,
            'brandList' => $brandList,
            'attrList'  => $attrList,
            'sizeCount' => $sizeCount,
            'sizeList'  => $sizeList,
            'picList'   => $picList,
        );
        $this->assign($tplData);
		$this->display();
		
	}
	/**
	 *处理：修改商品 
	 */
	public function doEditCake()
	{
		$id = intval($_GET['goods_id']);
		$arrMap = array('id'=>$id);
		
		$pageInfo = $_POST['page'];
		$pageSize = $_POST['size'];
		$pageAttr = $_POST['attr'];
		$pagePic  = $_POST['pic'];
		//模型
		$goodsDao = D('Goods');
		$goodsPic = D('GoodsPic');
		$goodsAttr= D('GoodsAttr');
		
		
		$pageInfo['cat_id'] = 1;
		$pageInfo['mtime'] = time();
		//图片
	
		$uploadList = uploadPic();
		$fileNum = count($uploadList);
		if ($uploadList['image1']['name']) {
			$pageInfo['pic'] = $uploadList['image1']['savename'];
			$fieldMap = array('id'=>$id);
			$imgname = $goodsDao->getOneValue($fieldMap, 'pic');
			delImage($imgname);
			$goodsDao->updateData($arrMap, $pageInfo);
			for($i=2; $i<=$fileNum; $i++){
				$pagePic['goods_id'] = $id;
				$pagePic['pic']      = $uploadList['image'.$i]['savename'];
				$pagePic['desc']     = $pagePic['desc'.$i];
				$goodsPic->addData($pagePic);
			}
		} elseif($uploadList['image2']['name']) {
			for($i=2; $i<=$fileNum+1; $i++){
				$pagePic['goods_id'] = $id;
				$pagePic['pic']      = $uploadList['image'.$i]['savename'];
				$pagePic['desc']     = $pagePic['desc'.$i];
				$goodsPic->addData($pagePic);
			}
		} else {
			$goodsDao->updateData($arrMap, $pageInfo);
		}

		
		$attrInfo = array();
		$attrInfo['goods_id'] = $id;
		//属性
		foreach ($pageAttr as $key => $value) {
			$attrMap = array('id'=>$key);
			$attrInfo['attr_value'] = $value;
			$goodsAttr->updateData($attrMap, $attrInfo);
		}

		//尺寸
		$sizeInfo = array();
		if (!empty($pageSize)) {
			foreach ($pageSize as $key => $value) {
				$sizeMap = array('id'=>$key);
				$sizeInfo['attr_value'] = $value['size'];
				$sizeInfo['attr_price'] = $value['price'];
				$goodsAttr->updateData($sizeMap, $sizeInfo);
			}
		}

		$this->success('修改成功', U('Admin/Cake/ls'));
	}
	
	/**
	 *删除图片 
	 */
	public function delPic()
	{
		$id = intval($_GET['id']);
		$goodsPicDao = D('GoodsPic');
		$arrMap = array('id'=>$id);
		$imgname = $goodsPicDao->getOneValue($arrMap, 'pic');
		delImage($imgname);
		
		$res = $goodsPicDao->delData($arrMap);
		
		
		$this->success('删除成功');

	}
	
	
	/**
	 * 删除
	 */
	public function doDelCake()
	{
		//模型
		$goodsDao = D('Goods');
		$goodsAttrDao  = D('GoodsAttr');
		$goodsPic = D('GoodsPic');
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
		
		//删除图片
		$imgname = $goodsDao->getOneValue($arrMap, 'pic');
		delImage($imgname);
		
		$goodsDao->delData($arrMap);
		$attrMap['goods_id'] = array('in', $delIds);
		
		//删除图片
		$picField = array('pic');
		$picList = $goodsPic->getList($picField, $attrMap);
		foreach($picList as $k=>$v){
			$img = $v['pic'];
			delImage($img);
		}
		$goodsPic->delData($attrMap);
		$goodsAttrDao->delData($attrMap);
		//页面跳转
		$this->success('删除成功');
		
	}
	
	
}
