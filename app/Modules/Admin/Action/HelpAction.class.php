<?php
/**
 *帮助控制器
 */
class HelpAction extends AdminCommonAction
{
	/**
	 *常见问题列表
	 */
	public function question()
	{
		$questionDao = D('Question');
		//分页
		import('ORG.Util.Page');
		$count      = $questionDao->count();
		$Page       = new Page($count,20);
		$show       = $Page->show();
		//条件
		$arrField = array('id', 'title', 'mtime', 'status');
		$arrMap = array();
		//搜索
		$title = trim($_POST['title']);
		if (!empty($title)) {
			$arrMap['title'] = array('like', "%{$title}%");
		}
		$arrOrder = array('id'=>'desc');
		$offset   = $Page->firstRow;
        $length   = $Page->listRows;
		$pageList = $questionDao->getList($arrField, $arrMap, $arrOrder, $offset, $length);
		foreach ($pageList as $key => $value) {
			$pageList[$key]['mtime'] = date('Y-m-d H:i:s',$value['mtime']);
			$pageList[$key]['status'] = ($value['status'] == 1)? '显示' : '不显示';
		}
        // 输出到模板
        $tplData = array(
            'pageList' => $pageList,
            'pageHtml' => $show,
        );
        $this->assign($tplData);
		$this->display();
	}
	/**
	 *添加问题
	 */
	public function addQuestion()
	{

		$this->display();
	}
	/**
	 *处理：添加问题
	 */
	public function doAddQuestion()
	{
		$questionDao = D('Question');
		$page = $_POST['page'];
		$page['ctime'] = time();
		$page['mtime'] = $page['ctime'] ;
		$res = $questionDao->addData($page);
		if ($res) {
			$this->success('添加成功', U('Admin/Help/question'));
		} else {
			$this->error('添加失败');
		}
	}
	/**
	 *修改问题
	 */
	public function editQuestion()
	{
		$questionDao = D('Question');
		$id = intval($_GET['id']);
		$pageInfo = $questionDao->getInfoById($id);
		$this->assign('pageInfo', $pageInfo);
		$this->display();
	}
	/**
	 *处理：修改问题
	 */
	public function doEditQuestion()
	{
		$questionDao = D('Question');
		$page = $_POST['page'];
		$id = intval($_POST['id']);
		$page['mtime'] = time();
		$res = $questionDao->updateData(array('id'=>$id),$page);
		if ($res) {
			$this->success('修改成功', U('Admin/Help/question'));
		} else {
			$this->error('修改失败');
		}
	}
	/**
	 * 删除问题
	 */
	public function doDelQuestion()
	{
		//模型
		$pageDao = D('Question');
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
		//删除数据
		if (empty($delIds)) {
			$this->error('请选择您要删除的数据');
		}
		$arrMap['id'] = array('in', $delIds);
		$pageDao->where($arrMap)->delete();
		//页面跳转
		$this->success('删除成功');
	}
}
