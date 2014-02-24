<?php
/**
 * 整站通用控制器
 * 网站所有控制器均为CommonAction的子类
 * @version 2013-12-04
 */
class CommonAction extends Action
{
	//全局变量
	public $_G;

	/**
	 * 初始化
	 */
	public  function _initialize()
	{
		//$_G
		global $_G;
		$_G = array();
		$this->_G = &$_G;

		//初始化
		$this->initEnv();
		$this->initUser();
		$this->initConfig();
		$this->initSetting();
		$this->initCache();
		$this->initOption();
        $this->initTpl();
		//$_G
		$this->_G['timestamp'] = time();
		$this->assign('_G', $this->_G);

	}


	/**
	 * 环境设置
	 */
	protected function initEnv()
	{
		//路径
		define('__DATA__',   __ROOT__.'/data/');
		define('__ATTACH__', __ROOT__.'/data/attach/');
		define('__API__',	__ROOT__.'/api/');
		define('__PUBLIC__', __ROOT__.'/public/');
		//定义项目名称和路径
		define('SITE_PATH', getcwd());
		define('SITE_PUBLIC_PATH','./public');
		define('SITE_JS_PATH',SITE_PUBLIC_PATH.'/js');
	}

	/**
	 * 初始化config 
	 */
	protected function initConfig()
	{
		//模板变量替换
		$tmplParseString = array(
			'__PUBLIC__' =>  __ROOT__.'/public',
			'__JS__'	 =>  __ROOT__.'/public/js',
			'__IMG__'	 =>  __ROOT__.'/public/images',
			'__CSS__'	 =>  __ROOT__.'/public/css',
			
			'__DATA__'   =>  __ROOT__.'/data',
			'__ATTACH__' =>  __ROOT__.'/data/attach',

			'__API__'	=>  __ROOT__.'/api',
			'SITE_PUBLIC_PATH'=> SITE_PUBLIC_PATH,
		);
		C('TMPL_PARSE_STRING', $tmplParseString);
		
		//其他配置参数
		$this->_G['config'] = array();
	}


	/**
	 * 初始化设置信息
	 */
	protected function initSetting()
	{
		$this->_G['setting'] = D('Setting')->getCache();
	}
	

	/**
	 * 初始化缓存
	 */
	protected function initCache()
	{
		$this->_G['cache'] = array();
		//创新工作分类
		$this->_G['cache']['innovation_type'] = array();

		//期刊年份
		$this->_G['cache']['magazine_year'] = array();
	}

	/**
	 * 初始化选项
	 */
	protected function initOption()
	{
		
		$this->_G['option'] = $optionList;
	}

  /*
  *  初始化模板
  */ 
   protected function initTpl(){
        $tplHtml = array(
		            'header'=>SITE_PUBLIC_PATH.'/tpl/header.html',
					'footer'=>SITE_PUBLIC_PATH.'/tpl/footer.html',
		);
		$this->assign($tplHtml);
   }
	/**
	 * 初始化用户信息
	 */
	protected function initUser()
	{
		$userId = intval($_SESSION['user_id']);
		/*
		if ($userId) {
			//用户信息
			$this->_G['user_id']   = intval($userId);
			$this->_G['user_info'] = D('User')->getInfoById($userId);
			//用户组信息
			$this->_G['user_id']   = intval($userInfo['group_id']);
			$this->_G['user_info'] = D('UserGroup')->getInfoById($userInfo['group_id']);
		}
		*/
	}
	/*  
	 * 中文截取，支持gb2312,gbk,utf-8,big5  
	 *  
	 * @param string $str 要截取的字串  
	 * @param int $start 截取起始位置  
	 * @param int $length 截取长度  
	 * @param string $charset utf-8|gb2312|gbk|big5 编码  
	 * @param $suffix 是否加尾缀   
	 */
	 
	public function csubstr($str, $start=0, $length, $charset="utf-8", $suffix=true)  
	 
	{  
	 
	   if(function_exists("mb_substr"))  
	 
	   {  
	 
	       if(mb_strlen($str, $charset) <= $length) return $str;  
	 
	       $slice = mb_substr($str, $start, $length, $charset);  
	 
	   }  
	 
	   else
	 
	   {  
	 
	       $re['utf-8']   = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";  
	 
	       $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";  
	 
	       $re['gbk']          = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";  
	 
	       $re['big5']          = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";  
	 
	       preg_match_all($re[$charset], $str, $match);  
	 
	       if(count($match[0]) <= $length) return $str;  
	 
	       $slice = join("",array_slice($match[0], $start, $length));  
	 
	   }  
	 
	   if($suffix) return $slice."…";  
	 
	   return $slice;  
	 
	} 
}
?>