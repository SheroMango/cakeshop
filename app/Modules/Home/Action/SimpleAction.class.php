<?php
/**
 * 单页控制器
 */

class SimpleAction extends CommonAction
{
	public  function answer()
	{
		$questionDao = D('Question');
		$arrField = array('id', 'title', 'content');
		$arrMap = array('status'=>1);
		$arrOrder = array('mtime'=>'desc');
		$arrList = $questionDao->getList($arrField, $arrMap, $arrOrder);
		$this->assign('arrList', $arrList);
		$this->display();
	}
}