<?php
/**
 * 运费模型
 * @author blue
 * @version 2014-02-05
 */
class FreightModel extends CommonModel
{
    /**
     * 格式化
     */
    public function format($arrInfo, $arrFormatField)
    {
        if(in_array('zone_name', $arrFormatField)){
            $zoneInfo = D('Zone')->where('id='.$arrInfo['zone_id'])->find();
            $cityInfo = D('Zone')->where('id='.$zoneInfo['pid'])->find();
            $provinceInfo = D('Zone')->where('id='.$cityInfo['pid'])->find();
            $arrInfo['zone_name'] = $zoneInfo['name'];
            $arrInfo['city_name'] = $cityInfo['name'];
            $arrInfo['province_name'] = $provinceInfo['name'];
        }
        return $arrInfo;
    }
}
