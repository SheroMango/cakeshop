<?php
/**
 * 网站配置
 * @version 2013-11-04
 */

class SettingModel extends Model
{
	/**
	 * 缓存id
	 */
	public $cacheId = '_setting_';

	/**
	 * 缓存列表
	 */
	public $cacheList = array();

	/**
	 * 构造函数
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * 批量更新数据库中的配置
	 */
	public function updateBatch($data)
	{
		if (empty($data)) {
			return false;
		}
		$updateList = array();
		foreach ($data as $k=>$v) {
			$updateList[] = array('skey'=>$k, 'svalue'=>$v);
		}
		$this->addAll($updateList, array(), true);
		return true;
	}


	////////////////////缓存相关////////////////////

	/**
	 * 获取缓存的setting数据
	 */
	public function getCache()
	{
		$settingList = $cacheList =  array();
		$cacheList = F($this->cacheId);
		//缓存不存在，创建缓存
		if (empty($cacheList)) {
			$cacheList = $this->setCache();
		}
		return $cacheList;
	}
	
	/**
	 * 设置缓存
	 */
	public function setCache()
	{
		//初始化
		$settingList = $cacheList = array();
		//获取数据
		$arrField = array('skey', 'svalue');
		$settingList = $this->field($arrField)->select();
		//写入缓存文件
		if (!empty($settingList)) {
			foreach ($settingList as $k=>$v) {
				$cacheList[$v['skey']] = $v['svalue'];
			}
			F($this->cacheId, $cacheList);
		}
		return $cacheList;
	}
	
	/**
	 * 清空缓存
	 */
	public function clearCache()
	{
		$cacheList = array();
		F($this->cacheId, $cacheList);
	}
}
?>