<?php
/**
 * 后台公共控制器
 */
class AdminCommonAction extends CommonAction
{
	/**
	 * 初始化
	 */
	public function _initialize()
	{
		parent::_initialize();
		$this->isLogin();
	}
	
	/**
	 * 邓普判断
	 */
	public function isLogin()
	{
		$this->uid = $_SESSION['aid'];
		if (empty($this->uid)) {
			$this->error('请先登录', U('admin/public/login'));
		}
		$this->userInfo = $_SESSION['user_info'];
	}


}
