<?php
class QaModel extends CommonModel{
    /**
     * 格式化
     */
    public function format($arrInfo, $arrFormatField){
        //时间
        if(in_array('ctime_text', $arrFormatField)){
            $arrInfo['ctime_text'] = date('Y-m-d H:i', $arrInfo['ctime']);
        }
        //显示状态
        if(in_array('status_name', $arrFormatField)){
            $arrInfo['status_name'] = ($arrInfo['status'] == 0) ? '显示' : '不显示';
        }
        return $arrInfo;
    }
	
}
