<?php
/**
 * 广告位模型类
 * @version 2013-08-13
 */
class AdPositionModel extends CommonModel {
	
	/**
	 * 获取所有广告位
	 */
	public function getAll()
	{
		$arrField = array('id', 'name');
		$arrMap = array();
		$arrOrder = array('id'=>'asc');
		$positionList = $this->getList($arrField, $arrMap, $arrOrder);
		return $positionList;
	}
	
	/**
	 * 更新广告数量
	 */
	public function updateAds($id)
	{
		//获取总数
		$arrAdMap = array(
			'position_id' => $id,
		);
		$ads = D('Ad')->getCount($arrAdMap);
		//更新数量
		$update = array(
			'ads' => $ads
		);
		$arrMap = array(
			'id' => $id,
		);
		$this->where($arrMap)->save($update);
	}
	
	
	/**
	 * 格式化
	 */
	public function format($info, $arrField)
	{
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
				$url = U('Admin/Ad/editPosition', array('id'=>$id));
				break;
			case 'admin_del':
				$url = U('Admin/Ad/doDelPosition', array('id'=>$id));
				break;
			default:
				$url = '';
		}
		return $url;
	}
}