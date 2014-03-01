<?php
/**
 * 单页控制器
 */

class SimpleAction extends CommonAction
{
    /**
     * 常见问题
     */
	public  function answer()
	{
		$questionDao = D('Question');
		$arrField = array('id', 'title', 'content');
		$arrMap = array('status'=>1);
		$arrOrder = array('mtime'=>'desc');
		$arrList = $questionDao->getList($arrField, $arrMap, $arrOrder);
        $tplData = array(
            'title' => '常见问题',
            'arrList' => $arrList,
        );
		$this->assign($tplData);
		$this->display();
	}

    /**
     * 单页 
     */
    public function page()
    {
        $spell = trim($_GET['spell']);
        $pageInfo = D('Page')->where("spell='".$spell."'")->find();
        $pageInfo['content'] = htmlspecialchars_decode($pageInfo['content']);
        $tplData = array(
            'title' => $pageInfo['title'],
            'pageInfo' => $pageInfo,
        );
        $this->assign($tplData);
        $this->display();
    }
}
