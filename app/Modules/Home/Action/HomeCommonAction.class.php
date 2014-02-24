<?php
/**
 *前台公共控制器
 */
class HomeCommonAction extends CommonAction
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
	 * 登录判断
	 */
	public function isLogin()
	{
		$this->uid = $_SESSION['uid'];
		if (empty($this->uid)) {
			$this->redirect('Public/login');
		}
	}
}
