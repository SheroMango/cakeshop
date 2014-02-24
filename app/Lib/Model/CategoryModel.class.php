<?php
/**
 * 商品分类模型
 * 
 */
class CategoryModel extends CommonModel
{
	////////////////////格式化数据////////////////////
    /**
     * 格式化信息
     */
    public function format($info, $arrFormatField)
    {
    	//状态
        if (in_array('status_name', $arrFormatField)) {
            $info['mtime_text'] = ($info['status']==1)? '显示' : '不显示';
        }
        //时间
        if (in_array('mtime_text', $arrFormatField)) {
            $info['mtime_text'] = date('Y-m-d H:i', $info['mtime']);
        }
        return $info;
    }
}
	