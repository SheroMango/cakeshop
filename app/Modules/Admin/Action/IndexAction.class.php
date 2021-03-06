<?php
/**
 * 首页
 * @version 2013-09-10
 */
class IndexAction extends AdminCommonAction
{

	//框架页
	public function index()
	{
		$this->assign('channel', $this->_getChannel());
		$this->assign('menu',    $this->_getMenu());
		C('SHOW_PAGE_TRACE', false);
		$this->display();
	}

	/**
	 * 首页
	 */
	public function main()
	{
		$this->display();
	}

	/**
	 * 顶部频道
	 */
	protected function _getChannel()
	{
		return array(
			'index'   => '首页',
			'global'  => '全局',
			'content'   => '内容管理',
			'other'   => '其他',
		);
	}

	/**
	 * 左侧菜单
	 */
	protected function _getMenu()
	{
		//初始化
		$menu = array();

		// 后台管理首页
		$menu['index'] = array(
			'首页' => array(
				'首页' => U('admin/index/main'),
			),
		);

		//全局
		$menu['global'] = array(
			'全局配置' => array(
				'基本信息' => U('admin/Global/basic'),
				'SEO设置'  => U('admin/global/seo'),
				//'邮件设置' => U('Admin/global/email'),
				//'附件设置' => U('Admin/global/attach'),
				'单页管理' => U('Admin/global/page'),
			),
		);
		//商品
		$menu['content'] = array(
			'商品管理' => array(
			    '品牌管理' => U('admin/Brand/ls'),
				'蛋糕管理' => U('admin/Cake/ls'),
				'鲜花管理' => U('admin/Flower/ls'),
			),
			'订单管理' => array(
			    '订单列表' => U('admin/Order/ls'),
			),
			'会员管理' => array(
				'会员列表' => U('admin/User/ls'),
			),
            '留言管理' => array(
                '评价列表' => U('admin/Comment/ls'),
                '咨询列表' => U('admin/Qa/ls'),
            ),
            '运费管理' => array(
                '城市列表' => U('admin/Freight/ls'),
            ),

		);

		//其他
		$menu['other'] = array(
			'帮助'=>array(
				'常见问题'=>U('Admin/help/question'),
			),
			'广告' => array(
				'广告管理'   => U('admin/ad/ad'),
				'广告位管理' => U('admin/ad/position'),
			),
		);
		return $menu;
	}
}
