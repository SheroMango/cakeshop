<?php
/**
 * 商品评论模块后台控制器
 * @auther blue
 * @version 2014-02-19
 */
class CommentAction extends AdminCommonAction
{
    /**
     * 评论列表
     */
    public function ls()
    {
        $commentDao = D('Comment');
        $arrField = array('*');
        $arrMap = array();
        $arrOrder = array('ctime desc');

        $count = $commentDao->getCount($arrMap);
        $page = page($count);
        $pageHtml = $page->show();

        $commentList = $commentDao->getList($arrField, $arrMap, $arrOrder, $page->firstRow, $page->listRows);
        $arrFormatField = array('ctime_text', 'user_name', 'goods_name', 'status_name');
        foreach($commentList as $k=>$v){
            $commentList[$k] = $commentDao->format($v, $arrFormatField);
        }

        $tplData = array(
            'commentList' => $commentList,
            'pageHtml' => $pageHtml,
        );
        $this->assign($tplData);
        $this->display();
    }

    /**
     * 查看评论详情
     */
    public function view()
    {
        $commentDao = D('Comment');
        $id = intval($this->_get('id'));
        $commentInfo = $commentDao->getInfoById($id);
        $arrFormatField = array('user_name', 'goods_name', 'status_name', 'ctime_text');
        $commentInfo = $commentDao->format($commentInfo, $arrFormatField);
        $tplData = array(
            'commentInfo' => $commentInfo,
        );
        $this->assign($tplData);
        $this->display();
    }

    /**
     * 状态更新
     */
    public function doUpdate()
    {
        $commentDao = D('Comment');
        $update = $this->_post();
        if($commentDao->save($update)){
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
		$commentDao = D('Comment');
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
		$commentDao->where($map)->delete();
		$this->success('删除成功');
	}

}
