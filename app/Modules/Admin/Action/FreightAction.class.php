<?php
/**
 * 运费管理
 * @author blue
 * @version
 */
class FreightAction extends AdminCommonAction
{
    /**
     * 市区列表
     */
    public function ls()
    {
        $freightObj = D('Freight');
        $arrField = array('*');
        $arrMap = array();
        $arrOrder = array();
        $count = $freightObj->getCount($arrMap);
        $page = page($count);
        $pageHtml = $page->show();
        $freightList = $freightObj->getList($arrField, $arrMap, $arrOrder, $page->firstRow, $page->listRows);
        $arrFormatField = array('zone_name');
        foreach($freightList as $k=>$v){
            $freightList[$k] = $freightObj->format($v, $arrFormatField);
        }
        $tplData = array(
            'list' => $freightList,
            'pageHtml' => $pageHtml,
        );
        $this->assign($tplData);
        $this->display();
    }

    /**
     * 添加运费
     */
    public function addFreight()
    {
        $tplData = array(
            'list' => $this->getProvinceList(),
        );
        $this->assign($tplData);
        $this->display();
    }

    /**
     * 添加运费操作
     */
    public function doAddFreight()
    {
        $data = $this->_post();
        D('Freight')->add($data);
        $this->success('添加成功');
    }

    /**
     * 获取省份列表
     */
    public function getProvinceList()
    {
        $arrField = array();
        $arrMap['pid'] = 0;
        $arrOrder = array('pid desc', 'code');
        $list = D('Zone')->getList($arrField, $arrMap, $arrOrder);
        return $list;
    }

    /**
     * 获取城市列表
     */
    public function getZoneList()
    {
        $id = intval($this->_get('id'));
        $arrField = array();
        $arrMap['pid'] = $id;
        $arrOrder = array('pid desc', 'code');
        $list = D('Zone')->getList($arrField, $arrMap, $arrOrder);
        if(!empty($list)){
            $data = array(
                'status' => 'success',
                'content' => $list,
            );
            echo json_encode($data);
        }else{
            $data = array(
                'status' => 'error',
                'content' => 'error',
            );
            echo json_encode($data);
        }
    }

}
