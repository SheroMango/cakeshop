<?php
/**
 * 配置文件
 * @version 2013-09-17
 */
return array(
    //URL
    'URL_MODEL'            => 0,
    'URL_CASE_INSENSITIVE' => true,
    //数据库
    'DB_TYPE'   => 'mysql',
    'DB_HOST'   => '192.168.1.188',
    'DB_NAME'   => 'cakeshop',
    'DB_USER'   => 'root',
    'DB_PWD'    => '',
    'DB_PORT'   => '3306',
    'DB_PREFIX' => 'cs_',
    //模板
    'TMPL_L_DELIM' => '{',
    'TMPL_R_DELIM' => '}',
    //分组
    'APP_GROUP_MODE'    =>1,
    'APP_GROUP_LIST'    =>'home, admin',
    'DEFAULT_GROUP'     =>'home',
    //其他
    'APP_AUTOLOAD_PATH' =>'@.TagLib',
    //'SHOW_PAGE_TRACE'   => true,
	//'DEFAULT_THEME'     => 'Default',
);
