<?php
/**
 * 全局配置
  * @version 2013-12-04
 */

class GlobalAction extends AdminCommonAction
{

	/**
	 * 初始化
	 */
	public function _initialize()
	{
		//初始化
		parent::_initialize();
		
		//获取数据
		D('Setting')->clearCache();
		$settingList = D('Setting')->getCache();
		
		//输出到模板
		$this->assign($settingList);
	}

	
	////////////////////网站配置////////////////////

	/**
	 * 基本设置
	 */
	public function basic()
	{
		$this->display();
	}

	/**
	 * seo设置
	 */
	public function seo()
	{
		$this->display();
	}

	
	/**
	 * 注册配置
	 */
	public function register()
	{
		$this->display();
	}

	/**
	 * 邮件设置
	 */
	public function email()
	{
		$this->display();
	}


	/**
	 * 附件设置
	 */
	public function attach()
	{
		$this->display();
	}

	/**
	 * 处理：编辑配置文件
	 */
	public function doEditSetting()
	{
		//初始化
		$settingDao = D('Setting');
		//表单数据
		$setting = $this->_post('setting');
		//更新数据、清空缓存
		$settingDao->updateBatch($setting);
		$settingDao->clearCache();
		//页面跳转
		$this->success('修改成功');
	}


	////////////////////单页相关////////////////////

	/**
	 * 单页列表
	 */
	public function page()
	{
		//模型
		$pageDao = D('Page');
		
		//条件
		$arrField = array('id', 'title', 'spell', 'display_order', 'status', 'mtime');
		$arrMap = array();
		$arrOrder = array(
			'display_order' => 'asc',
		);

		//分页
		$count = $pageDao->getCount($arrMap);
		$page = page($count);
		$pageHtml = $page->show();

		//列表
		$pageList = $pageDao->getList($arrField, $arrMap, $arrOrder, $page->firstRow, $page->listRows);
		if (!empty($pageList)) {
			$arrFormatField = array('status_name', 'mtime_text', 'url_admin_edit', 'url_admin_del');
			foreach ($pageList as $k=>$v) {
				$pageList[$k] = $pageDao->format($v, $arrFormatField);
			}
		}
		// 输出到模板
		$tplData = array(
			'pageHtml' => $pageHtml,
			'pageList' => $pageList,
		);
		$this->assign($tplData);
		$this->display();
	}

	/**
	 * 添加单页
	 */
	public function addPage()
	{
		$this->display();
	}

	/**
	 * 处理:添加单页
	 */
	public function doAddPage()
	{
		//模型
		$pageDao = D('Page');

		//表单数据
		$insert = $this->_post('page');
		$insert['ctime'] = $insert['mtime'] = $this->_G['timestamp'];
		
		//插入数据
		$id = $pageDao->add($insert);
		
		//页面跳转
		$url = U('admin/global/page');
		$this->success('添加成功', $url);
	}

	/**
	 * 编辑单页
	 */
	public function editPage()
	{
		//模型
		$pageDao = D('Page');

		//表单数据
		$id = $this->_get('id');
		$pageInfo = $pageDao->getInfoById($id);
		
		//输出模板
		$tplData = array(
			'pageInfo' => $pageInfo,
		);
		$this->assign($tplData);
		$this->display();
	}

	/**
	 * 处理:编辑单页
	 */
	public function doEditPage()
	{
		//模型
		$pageDao = D('Page');
		
		//表单数据
		$id = intval($this->_post('id'));
		$update = $this->_post('page');
		$update['mtime'] = $this->_G['timestamp'];
		
		//更新数据
		$pageDao->where('id='.$id)->save($update);
		
		//页面跳转
		$url = U('admin/global/editPage', array('id'=>$id));
		$this->success('修改成功', $url);
	}

	/**
	 * 删除单页
	 */
	public function doDelPage()
	{
		//模型
		$pageDao = D('Page');
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
		//删除数据
		if (empty($delIds)) {
			$this->error('请选择您要删除的数据');
		}
		$arrMap['id'] = array('in', $delIds);
		$pageDao->where($arrMap)->delete();
		//页面跳转
		$this->success('删除成功');
	}

}
