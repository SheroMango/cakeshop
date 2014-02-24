<?php
/**
 * 商品控制器
 */
class BrandAction extends AdminCommonAction
{
	/*************品牌相关************/
	/**
	 * 品牌列表
	 */
	public function ls()
	{
		//模型
		$brandDao    = D('Brand');
		$goodsType   = D('GoodsType');
		//分页
		import('ORG.Util.Page');
		$count      = $brandDao->count();
		$Page       = new Page($count,20);
		$show       = $Page->show();
		//条件
		$arrField = array('id', 'name', 'type_id', 'pic', 'display_status', 'display_order');
		$arrMap   = array();
		//搜索
		$name = trim($_POST['name']);
		if (!empty($name)) {
			$arrMap['name'] = array('like',"%{$name}%");
		}
		$arrOrder = array('id'=>'desc');
		$offset   = $Page->firstRow;
        $length   = $Page->listRows;
		$pageList = $brandDao->getList($arrField, $arrMap, $arrOrder, $offset, $length);
		//格式化
		if (!empty($pageList)) {
            $arrFormatField = array('status_name', 'mtime_text');
            foreach ($pageList as $k=>$v) {
                $pageList[$k] = $brandDao->format($v, $arrFormatField);
				$type_id = $v['type_id'];
				$typeMap = array('id'=>$type_id);
				$pageList[$k]['type_id'] = $goodsType->getOneValue($typeMap, 'ty_name');
            }
        }
        // 输出到模板
        $tplData = array(
            'pageList' => $pageList,
            'pageHtml' => $show,
        );
        $this->assign($tplData);
		$this->display();
	}
	/**
	 * 添加品牌
	 */
	public function addBrand()
	{
		$goodsTypeDao = D('GoodsType');
		$filed = array('id','ty_name');
		$typeList = $goodsTypeDao->getList($filed);
		$this->assign('typeList', $typeList);
		$this->display();
	}
	/**
	 * 处理：添加品牌
	 */
	public function doAddBrand()
	{
		//模型
		$brandDao = D('Brand');	
		//接收表单参数
		$insert = $_POST['page'];
		
		//图片上传
		import('ORG.Net.UploadFile');
		$upload = new UploadFile();// 实例化上传类
		$upload->maxSize = 3145728; // 设置附件上传大小
		$upload->allowExts = array ('jpg','gif','png','jpeg' ); // 设置附件上传类型
		$upload->savePath =  './data/attach/';// 设置附件上传目录
		$upload->autoSub = true;
	    $upload->subType = 'custom';
	    $upload->subDir = date('Ym').'/'.date('d').'/';
		//$upload->thumbRemoveOrigin = true;
		if(!$upload->upload()) {// 上传错误提示错误信息
			//$this->error($upload->getErrorMsg());
		}else{// 上传成功 获取上传文件信息
			$info =  $upload->getUploadFileInfo();
		}
		if ($info[0]['savename']) {
			$insert['pic'] = date ( 'Ym' ) . date ( 'd' ) .'/'.$info[0]['savename'];
		}
		$res = $brandDao->addData($insert);
		if ($res) {
			$this->success('添加成功', U('Admin/Brand/ls'));
		}
	}
	/**
	 * 编辑品牌
	 */
	public function editBrand()
	{
		//接收id
		$id = intval($_GET['id']);
		//模型
		$brandDao = D('Brand');
		$goodsTypeDao = D('GoodsType');
		
		//信息
		$brandInfo = $brandDao->getInfoById($id);
		$filed = array('id','ty_name');
		$typeList = $goodsTypeDao->getList($filed);
		$tplData = array('brandInfo'=>$brandInfo,'typeList'=>$typeList);
		$this->assign($tplData);
		$this->display();
	}
	/**
	 * 处理：编辑品牌
	 */
	public function doEditBrand()
	{
		//模型
		$brandDao = D('Brand');	
		//接收表单参数
		$update = $_POST['page'];
		//图片上传
		import('ORG.Net.UploadFile');
		$upload = new UploadFile();// 实例化上传类
		$upload->maxSize = 3145728; // 设置附件上传大小
		$upload->allowExts = array ('jpg','gif','png','jpeg' ); // 设置附件上传类型
		$upload->savePath =  './data/attach/';// 设置附件上传目录
		$upload->autoSub = true;
	    $upload->subType = 'custom';
	    $upload->subDir = date('Ym').'/'.date('d').'/';
		if(!$upload->upload()) {// 上传错误提示错误信息
			//$this->error($upload->getErrorMsg());
		}else{// 上传成功 获取上传文件信息
			$info =  $upload->getUploadFileInfo();
		}
		if ($info[0]['savename']) {
			$update['pic'] = $info[0]['savename'];
			$id = $update['id'];
			$fieldMap = array('id'=>$id);
			$imgname = $brandDao->getOneValue($fieldMap, 'pic');
			$imgpath = APP_PATH.'../data/attach/';
			$filename = $imgpath.$imgname;
			unlink($filename);
		}
		$res = $brandDao->updateData('',$update);
		if ($res) {
			$this->success('修改成功', U('Admin/Brand/ls'));
		}
	}
	/**
	 * 删除品牌
	 */
	public function doDelBrand()
	{
		
		//模型
		$brandDao = D('Brand');
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
		foreach ($delIds as $key => $value) {
			$id = $value;
			$fileMap = array('id'=>$id);
			$imgname = $brandDao->getOneValue($fileMap, 'pic');
			$imgpath = APP_PATH.'../data/attach/';
			$filename = $imgpath.$imgname;
			unlink($filename);
		}
		$brandDao->delData($arrMap);
		//页面跳转
		$this->success('删除成功');
	}
}
