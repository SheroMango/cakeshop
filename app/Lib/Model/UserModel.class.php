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
        //性别
        if(in_array('sex_name', $arrFormatField)){
            $arrInfo['sex_name'] = ($arrInfo['sex'] == 1) ? '男' : '女';
        }
        //时间
        if(in_array('ctime_text', $arrFormatField)){
            $arrInfo['ctime_text'] = date('Y-m-d H:i', $arrInfo['ctime']);
        }
        return $arrInfo;
    }
}
