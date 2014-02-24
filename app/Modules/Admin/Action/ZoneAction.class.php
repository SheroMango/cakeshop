<?php
/**
 * 运费管理
 * @author blue
 * @version
 */
class ZoneAction extends AdminCommonAction
{
    /**
     * 市区列表
     */
    public function ls()
    {
        $zoneObj = D('Zone');
        $arrField = array('*');
        $arrMap = array();
        $arrOrder = array('display_order');
        $count = $zoneObj->getCount($arrMap);
        $page = page($count);
        $pageHtml = $page->show();
        $zoneList = $zoneObj->getList($arrField, $arrMap, $arrOrder, $page->firstRow, $page->listRows);
        foreach($zoneList as $k=>$v){
            $zoneList[$k] = $zoneObj->format($v, $arrFormatField);
        }
        $tplData = array(
            'zoneList' => $zoneList,
            'pageHtml' => $pageHtml,
        );
        $this->assign($tplData);
        $this->display();
    }

    /**
     * 添加运费
     */
    public function addZone()
    {
        $tplData = array(
            'list' => $this->getZoneList('0'),
        );
        $this->assign($tplData);
        $this->display();
    }

    /**
     * 获取城市列表
     */
    public function getZoneList($id = 0)
    {
        $arrField = array();
        $arrMap['pid'] = $id;
        $arrOrder = array('pid desc', 'code');
        $list = D('Zone')->getList($arrField, $arrMap, $arrOrder);
        return $list;
    }

}
