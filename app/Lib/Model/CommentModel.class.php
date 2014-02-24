<?php
/**
 * 商品评价模型类
 * @author blue
 * @version 2014-02-19
 */
class CommentModel extends CommonModel
{
    /**
     * 格式化
     * @return array $arrInfo
     * @param  array $arrInfo
     * @parma  array $arrFormatField
     */
    public function format($arrInfo, $arrFormatField)
    {
        //时间
        if(in_array('ctime_text', $arrFormatField)){
            $arrInfo['ctime_text'] = date('Y-m-d H:i', $arrInfo['ctime']);
        }

        //用户名
        if(in_array('user_name', $arrFormatField)){
            $arrInfo['user_name'] = D('User')->where('id='.$arrInfo['user_id'])->getField('name');
        }

        //商品名称
        if(in_array('goods_name', $arrFormatField)){
            $arrInfo['goods_name'] = D('Goods')->where('id='.$arrInfo['goods_id'])->getField('name');
        }

        //显示状态
        if(in_array('status_name', $arrFormatField)){
            $arrInfo['status_name'] = ($arrInfo['status']) ? '显示' : '不显示';
        }

        return $arrInfo;
    }
}
