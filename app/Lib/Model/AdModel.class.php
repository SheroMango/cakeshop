<?php
/**
 * 广告模型类
 * @version 2013-08-13
 */
class AdModel extends CommonModel {

	/**
	 * 格式化
	 */
	public function format($info, $arrField)
	{
		//广告位
		if (in_array('position_name', $arrField)) {
			$positionInfo = D('AdPosition')->getInfoById($info['position_id']);
			$info['position_name'] = $positionInfo['name'];
		}
		//图片
		if (in_array('pic_small', $arrField)) {
			$info['pic_small'] = getPicPath($info['pic'], 's');
		}
		if (in_array('pic_original', $arrField)) {
			$info['pic_original'] = getPicPath($info['pic'], 'o');
		}
		//状态名称
		if (in_array('status_name', $arrField)) {
			$info['status_name'] = ($info['status'] == 1) ? '是' : '否';
		}
		
		//时间
		if (in_array('ctime_text', $arrField)) {
			$info['ctime_text'] = date('Y-m-d H:i', $info['ctime']);
		}
		if (in_array('mtime_text', $arrField)) {
			$info['mtime_text'] = date('Y-m-d H:i', $info['ctime']);
		}
		//链接
		if (in_array('url_admin_edit', $arrField)) {
			$info['url_admin_edit'] = $this->getUrl($info['id'], 'admin_edit');
		}
		if (in_array('url_admin_del', $arrField)) {
			$info['url_admin_del'] = $this->getUrl($info['id'], 'admin_del');
		}
		return $info;
	}
	
	/**
	 * url
	 */
	public function getUrl($id, $type)
	{
		$url = '';
		switch ($type) {
			case 'admin_edit':
				$url = U('Admin/Ad/editAd', array('id'=>$id));
				break;
			case 'admin_del':
				$url = U('Admin/Ad/doDelAd', array('id'=>$id));
				break;
			default:
				$url = '';
		}
		return $url;
	}
}