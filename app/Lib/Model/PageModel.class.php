<?php
/**
 * 单页模型类
 * @version 2013-12-04
 */

class PageModel extends CommonModel
{
	/**
	 * 根据spell获取信息
	 */
	public function getInfoBySpell($spell)
	{
		$arrField = array('id', 'title', 'spell', 'content', 'ctime', 'mtime');
		$arrMap = array(
			'spell'	=> array('eq', $spell),
		);
		$pageInfo = $this->getInfo($arrField, $arrMap);
		return $pageInfo;
	}
	
	////////////////////格式化数据////////////////////
	/**
	 * 格式化信息
	 */
	public function format($info, $arrFormatField)
	{
		//显示状态
		if (in_array('status_name', $arrFormatField)) {
			$info['status_name'] = ($info['status'] == 1) ? '是' : '否';
		}
		//时间
		if (in_array('ctime_text', $arrFormatField)) {
			$info['ctime_text'] = date('Y-m-d H:i', $info['ctime']);
		}
		 if (in_array('mtime_text', $arrFormatField)) {
			$info['mtime_text'] = date('Y-m-d H:i', $info['mtime']);
		}
		//url
		if (in_array('url_home_detail', $arrFormatField)) {
			$info['url'] = $this->getUrlBySpell($info['spell']);
		}
		if (in_array('url_admin_edit', $arrFormatField)) {
			$info['url_admin_edit'] = $this->getUrl($info['id'], 'admin_edit');
		}
		if (in_array('url_admin_del', $arrFormatField)) {
			$info['url_admin_del'] = $this->getUrl($info['id'], 'admin_del');
		}
		return $info;
	}
	
	/**
	 * 根据spell获取url
	 */
	public function getUrlBySpell($spell)
	{
		return U('Home/Page/index', array('spell'=>$spell));
	}
	
	/**
	 * 根据spell获取url
	 */
	public function getUrl($id, $type)
	{
		$url = '';
		switch ($type) {
			case 'admin_edit':
				$url = U('admin/global/editPage', array('id'=>$id));
				break;
			case 'admin_del':
				$url = U('Admin/Global/doDelPage', array('id'=>$id));
				break;
			default:
				$url = '';
		}
		return $url;
	}
}
?>