<?php
/**
 * ��Ա������
 * @author blue
 * @version 2014-02-15
 */
class UserAction extends AdminCommonAction
{
	/**
	 * ��Ա�б�
	 */
	public function ls()
	{
		$userDao = D('User');
        //����
		$arrMap = array();
        $search_name = $_REQUEST['name'];
        if(!empty($search_name)){
            $arrMap['name'] = array('like', "%".$search_name."%");
        }
        //����
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
     * �鿴����
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
	 * ɾ���û� 
	 */
	public function del()
	{
		//ģ��
		$userDao = D('User');
		//����
		$delIds = array();
		$postIds = $this->_post('id');
		if (!empty($postIds)) {
			$delIds = $postIds;
		}
		$getId = intval($this->_get('id'));
		if (!empty($getId)) {
			$delIds[] = $getId;
		}
		//ɾ��
		if (empty($delIds)) {
			$this->error('��ѡ����Ҫɾ��������');
		}
		$map['id'] = array('in', $delIds);
		$userDao->where($map)->delete();
		$this->success('ɾ���ɹ�');
	}


}
