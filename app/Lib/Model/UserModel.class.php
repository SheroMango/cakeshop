<?php
/**
 * 用户模型类
 */
class UserModel extends CommonModel
{
    /**
     * 格式化
     */
    public function format($arrInfo, $arrFormatField)
    {
        //时间
        if(in_array('ctime_text', $arrFormatField)){
            $arrInfo['ctime_text'] = date('Y-m-d H:i', $arrInfo['ctime']);
        }
        return $arrInfo;
    }
}
