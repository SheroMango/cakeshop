<?php
/**
 * 会员管理类
 * @author blue
 * @version 2014-02-15
 */
class UserAction extends AdminCommonAction
{
	/**
	 * 会员列表
	 */
	public function ls()
	{
		$userDao = D('User');
        //搜索
		$arrMap = array();
        $search_name = $_REQUEST['name'];
        if(!empty($search_name)){
            $arrMap['name'] = array('like', "%".$search_name."%");
        }
        //排序
        $search_filter = $_REQUEST['spend_count'];
        if(!empty($search_filter)){
            $arrMap['spend_count'] = array('gt', $search_filter);
        }
		$arrOrder = array('spend_count desc');
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
		$arrField = array('*');
		$count = $userDao->getCount($arrMap);
		$page = page($count);
		$pageHtml = $page->show();
		$userList = $userDao->getList($arrField, $arrMap, $arrOrder, $page->firstRow, $page->listRows);
        $arrFormatField = array('ctime_text');
		foreach($userList as $k=>$v){
            $userList[$k] = $userDao->format($v, $arrFormatField);
		}
		$tplData = array(
			'userList' => $userList,
			'pageHtml' => $pageHtml,
            'display_order' => $display_order,
            'display_desc' => $display_desc,
		);
		$this->assign($tplData);
		$this->display();
	}

    /**
     * 查看详情
     */
    public function view()
    {
        $id = intval($this->_get('id'));
        $userInfo = D('User')->getInfoById($id);
        $userInfo = D('User')->format($userInfo, array('sex_name'));
        $this->assign('userInfo', $userInfo);
        $this->display();
    }

	/**
	 * 删除用户 
	 */
	public function del()
	{
		//模型
		$userDao = D('User');
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
		$userDao->where($map)->delete();
		$this->success('删除成功');
	}


}
