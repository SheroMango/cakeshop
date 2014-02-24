<?php
/**
 * 咨询内容控制器
 */
class QaAction extends AdminCommonAction
{
    /**
     * 咨询列表
     */
    public function ls()
    {
        //模型
        $QaDao = D('Qa');
        $arrField = array('*');
        $arrMap = array();
        $arrOrder = array('ctime');
        //分页
        $count = $QaDao->getCount($arrMap);
        $page = page($count);
        $pageHtml = $page->show();
        //列表
        $qaList = $QaDao->getList($arrField, $arrMap, $arrOrder, $page->firstRow, $page->listRows);
        //格式化
        $arrFormatField = array('ctime_text', 'status_name');
        foreach($qaList as $k=>$v){
            $qaList[$k] = $QaDao->format($v, $arrFormatField);
        }
        //模版赋值
        $tplData = array(
            'qaList' => $qaList,
            'pageHtml' => $pageHtml,
        );
        $this->assign($tplData);
        $this->display();
    }

    /**
     * 查看详情
     */
    public function answer(){
        $QaDao = D('Qa');
        $id = intval($this->_get('id'));
        $qaInfo = $QaDao->getInfoById($id);
        $tplData = array(
            'qaInfo' => $qaInfo,
        );
        $this->assign($tplData);
        $this->display();
    }

    /**
     * 回复
     */
    public function addAnswer(){
        $update = $this->_post();
        $QaDao = D('Qa');
        $update['answer_time'] = time();
        $id = $QaDao->save($update);
        if(!empty($id)){
            $this->success('更新成功');
        }else{
            $this->error('更新失败');
        }
    }

	/**
	 * 删除评论 
	 */
	public function del()
	{
		//模型
		$qaDao = D('Qa');
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
		$qaDao->where($map)->delete();
		$this->success('删除成功');
	}

}
