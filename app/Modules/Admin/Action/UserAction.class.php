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
		$arrField = array('*');
		$arrOrder = array('spend_count desc');
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
		);
		$this->assign($tplData);
		$this->display();
	}
}
