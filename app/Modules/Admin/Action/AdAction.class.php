<?php
/**
 * 广告模块
 * @version 2013-08-24
 */
class AdAction extends AdminCommonAction
{
	 ////////////////////////广告管理////////////////////////
	/**
	 * 广告管理
	 */
	public function ad()
	{
		//模型
		$adDao = D('Ad');
		$positionDao = D('AdPosition');
		
		//表单数据
		$name = trim($this->_request('name'));
		$position = intval($this->_request('position'));
		
		//条件
		$arrField = array('*');
		$arrMap = array();
		if (!empty($name)) {
			$arrMap['name'] = array('like', "%{$name}%");
		}
		if (!empty($position)) {
			$arrMap['position_id'] = array('eq', $position);
		}
		$arrOrder = array(
			'position_id'  => 'asc',
			'display_order' => 'asc',
		);
		//分页
		$count = $adDao->getCount($arrMap);
		$page = page($count);
		$pageHtml = $page->show();
		
		//列表
		$adList = $adDao->getList($arrField, $arrMap, $arrOrder, $page->firstRow, $page->listRows);
		if (!empty($adList)) {
			$arrFormatField = array('position_name', 'pic_small', 'status_name', 'url_admin_edit', 'url_admin_del', 'mtime_text');
			foreach ($adList as $k=>$v) {
				$adList[$k] = $adDao->format($v, $arrFormatField);
			}
		}
		
		//广告位
		$positionList = $positionDao->getList('*', array(), array('id'=>'asc'));
		
		//输出到模板
		$tplData = array(
			'pageHtml'	 => $pageHtml,
			'adList'	   => $adList,
			'positionList' => $positionList,
		);
		$this->assign($tplData);
		$this->display();
	}

	/**
	 * 页面：添加广告
	 */
	public function addAd()
	{
		//模型
		$positionDao = D('AdPosition');
		//广告位
		$positionList = $positionDao->getAll();
		//输出到模板
		$tplData = array(
			'positionList' => $positionList,
		);
		$this->assign($tplData);
		$this->display();
	}

	/**
	 * 处理：添加广告
	 */
	public function doAddAd()
	{
		//模型
		$adDao = D('Ad');
		$positionDao = D('AdPosition');
		//数据
		$insert = $this->_post('ad');
		$insert['ctime'] = $insert['mtime'] = time();
		if (!empty($_FILES['pic']['name'])) {
			$picList = uploadPic();
			if ($picList['code'] != 'error') {
				$insert['pic'] = $picList['pic']['savename'];
			}
		}
		$adDao->add($insert);
		//更新广告数
		$positionDao->updateAds($insert['position_id']);
		
		//跳转
		$url = U('Admin/Ad/ad');
		$this->success('添加成功', $url);
	}


	/**
	 * 页面：编辑广告
	 */
	public function editAd()
	{
		//模型
		$adDao = D('Ad');
		$positionDao = D('AdPosition');
		//参数
		$id = intval($this->_get('id'));
		//广告位
		$positionList = $positionDao->getAll();
		//广告信息
		$adInfo = $adDao->getInfoById($id);
		if ($adInfo['pic']) {
			$adInfo['pic_small'] = getPicPath($adInfo['pic'], 's');
		}
		//输出到模板
		$tplData = array(
			'positionList' => $positionList,
			'adInfo'	   => $adInfo,
		);
		$this->assign($tplData);
		$this->display();
	}

	/**
	 * 处理：编辑广告
	 */
	public function doEditAd()
	{
		//模型
		$adDao = D('Ad');
		$positionDao = D('AdPosition');
		//数据
		$id = intval($this->_post('id'));
		$update = $this->_post('ad');
		$update['ctime'] = $update['mtime'] = time();
		if (!empty($_FILES['pic']['name'])) {
			$picList = uploadPic();
			if ($picList['code'] != 'error') {
				$update['pic'] = $picList['pic']['savename'];
			}
		}
		$adDao->where('id='.$id)->save($update);
		//更新广告数
		$positionDao->updateAds($update['position_id']);
		
		//跳转
		$url = $adDao->getUrl($id, 'admin_edit');
		$this->success('编辑成功', $url);
	}
	
	/**
	 * 操作：删除广告
	 */
	public function doDelAd()
	{
		//模型
		$adDao = D('Ad');
		//数据
		$delIds = array();
		$postIds = $this->_post('id');
		if (!empty($postIds)) {
			$delIds = $postIds;
		}
		$getId = intval($this->_get('id'));
		if (!empty($getId)) {
			$delIds[] = $getId;
		}
		//删除
		if (empty($delIds)) {
			$this->error('请选择您要删除的数据');
		}
		$map['id'] = array('in', $delIds);
		$adDao->where($map)->delete();
		$this->success('删除成功');
	}

	////////////////////////广告位管理////////////////////////
	/**
	 * 页面：广告位管理
	 */
	public function position()
	{
		//模型
		$positionDao = D('AdPosition');
		//条件
		$arrField = array('id', 'name', 'desc', 'ads', 'mtime');
		$arrMap = array();
		$arrOrder = array(
			'id' => 'asc',
		);
		//分页
		$count = $positionDao->getCount($arrMap);
		//列表
		$positionList = $positionDao->getList($arrField, $arrMap, $arrOrder);
		if (!empty($positionList)) {
			$arrFormatField = array('url_admin_edit', 'url_admin_del', 'mtime_text');
			foreach ($positionList as $k=>$v) {
				$positionList[$k] = $positionDao->format($v, $arrFormatField);
			}
		}
	 
		//输出到模板
		$tplData = array(
			'pageHtml'	 => $pageHtml,
			'positionList' => $positionList,
		);
		$this->assign($tplData);
		$this->display();
	}

	/**
	 * 页面：添加广告位
	 */
	public function addPosition()
	{
		$this->display();
	}

	/**
	 * 处理：添加广告
	 */
	public function doAddPosition()
	{
		//模型
		$positionDao = D('AdPosition');
		//数据
		$insert = $this->_post('position');
		$insert['ctime'] = $insert['mtime'] = time();
		//插入数据
		$positionId = $positionDao->add($insert);
		//页面跳转
		$url = U('Admin/Ad/position');
		$this->success('添加成功', $url);
	}

	/**
	 * 页面：编辑广告位
	 */
	public function editPosition()
	{

		//模型
		$positionDao = D('AdPosition');
		//数据
		$id = intval($this->_get('id'));
		$positionInfo = $positionDao->getInfoById($id);
		//输出到模板
		$tplData = array(
			'positionInfo' => $positionInfo,
		);
		$this->assign($tplData);
		$this->display();
	}

	/**
	 * 处理：编辑广告
	 */
	public function doEditPosition()
	{
		//模型
		$positionDao = D('AdPosition');
		//数据
		$id = intval($this->_post('id'));
		$update = $this->_post('position');
		$update['mtime'] = time();
		//更新数据
		$arrMap = array(
			'id' => array('eq', $id),
		);
		$positionDao->where($arrMap)->save($update);
		//跳转
		$url = $positionDao->getUrl($id, 'admin_url_edit');
		$this->success('修改成功', $url);
		
	}
	
	/**
	 * 删除广告位
	 */
	public function doDelPosition()
	{
		//模型
		$positionDao = D('AdPosition');
		//数据
		$delIds = array();
		$postIds = $this->_post('id');
		if (!empty($postIds)) {
			$delIds = $postIds;
		}
		$getId = intval($this->_get('id'));
		if (!empty($getId)) {
			$delIds[] = $getId;
		}
		//删除
		if (empty($delIds)) {
			$this->error('请选择您要删除的数据');
		}
		$map['id'] = array('in', $delIds);
		$positionDao->where($map)->delete();
		$this->success('删除成功');
	}

	/************友情链接********/

	/**
	 *列表
	 */
	public function friendlink() 
	{
		//模型
		$FriendLink = D('FriendLink');
		import('ORG.Util.Page');// 导入分页类
		$count      = $FriendLink->count();// 查询满足要求的总记录数
		$Page       = new Page($count,25);// 实例化分页类
		$show       = $Page->show();// 分页显示输出
		$title = trim($this->_post('title'));
		if (!empty($title)) {
			$arrMap['title'] = array('like', "%{$title}%");
		}
		$arrOrder = array('display_order'  => 'asc',);
        $offset   = $Page->firstRow;
        $length   = $Page->listRows;
		// 进行分页数据查询 注意limit方法的参数要使用Page类的属性
		$friendList = $FriendLink->getList($arrField, $arrMap, $arrOrder, $offset, $length);
		$friendList  = $FriendLink->format($friendList);
		$this->assign('friendList', $friendList);// 赋值数据集
		$this->assign('page',$show);// 赋值分页输出
		$this->display(); // 输出模板
	}
	/**
	 *添加友情链接
	 */
	public function addLink() 
	{
		$this->display();
	}
	/**
	 *处理添加友情链接
	 */
	public function doAddLink() 
	{
		//模型
		$FriendLink = D('FriendLink');
		//接收表单参数
		$insert = $this->_post('page');
		$insert['ctime'] = time();
		$insert['mtime'] = $insert['ctime'];
		$res = $FriendLink->add($insert);
		if($res){
			$this->success('添加成功', U('Admin/Ad/friendlink'));
		}
	}
	/**
	 *修改友情链接
	 */
	public function editLink() 
	{
		//接收参数
		$id = intval($this->_get('id'));
		//模型
		$FriendLink = D('FriendLink');
		$adInfo = $FriendLink->where('id='.$id)->find();
		$this->assign('adInfo', $adInfo);
		$this->display();
	}
	/**
	 *处理：修改友情链接
	 */
	public function doEditLink() 
	{
		//模型
		$FriendLink = D('FriendLink');
		//接受参数
		$update = $this->_post('ad');
		$update['mtime'] = time();
		$res = $FriendLink->save($update);
		if($res){
			$this->success('修改成功', U('Admin/Ad/friendlink'));
		}
	}
	/**
	 * 删除友情链接
	 */
	public function doDelLink()
	{
		//模型
		$FriendLink = D('FriendLink');
		//数据
		$delIds = array();
		$postIds = $this->_post('id');
		if (!empty($postIds)) {
			$delIds = $postIds;
		}
		$getId = intval($this->_get('id'));
		if (!empty($getId)) {
			$delIds[] = $getId;
		}
		//删除
		if (empty($delIds)) {
			$this->error('请选择您要删除的数据');
		}
		$map['id'] = array('in', $delIds);
		$FriendLink->where($map)->delete();
		$this->success('删除成功');
	}
}
