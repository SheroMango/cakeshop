<?php
/**
 * 商品Model
 */
class GoodsModel extends CommonModel
{
	////////////////////格式化数据////////////////////
    /**
     * 格式化信息
     */
    public function format($info, $arrFormatField)
    {
		//上架
        if (in_array('sale_name', $arrFormatField)) {
            $info['sale_name'] = ($info['is_on_sale'] == 1) ? '上架' : '下架';
        }
		//时间
        if (in_array('mtime_text', $arrFormatField)) {
            $info['mtime_text'] = date('Y-m-d H:i', $info['mtime']);
        }
        //url
        if (in_array('url_admin_edit', $arrFormatField)) {
            $info['url_admin_edit'] = $this->getUrl($info['id'], 'admin_edit');
        }
        if (in_array('url_admin_del', $arrFormatField)) {
            $info['url_admin_del'] = $this->getUrl($info['id'], 'admin_del');
        }
        if (in_array('url_cake', $arrFormatField)) {
            $info['url_cake'] = $this->getUrl($info['id'], 'home_cake');
        }
        //pic
        if (in_array('pic_name', $arrFormatField)) {
            $info['pic_name'] = getPicPath($info['pic'], 'b');
        }
        return $info;
    }
    /**
     * 根据spell获取url
     */
    public function getUrl($id, $type)
    {
        $url = '';
        switch ($type) {
            case 'admin_edit':
                $url = U('Admin/Goods/editGoods', array('id'=>$id));
                break;
            case 'admin_del':
                $url = U('Admin/Goods/doDelGoods', array('id'=>$id));
                break;
            case 'home_cake':
                $url = U('Home/Cake/detail', array('id'=>$id));
                break;
            default:
                $url = '';
        }
        return $url;
    }
}
