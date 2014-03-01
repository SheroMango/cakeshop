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
        //搜索
        $search = trim($_POST['name']);
        $idList = $this->searchName($search);
        if(!empty($idList)){
            $arrMap['id'] = array('in', $idList);
        }
        $arrOrder = array();
        $page = page($freightObj->getCount($arrMap));
        $freightList = $freightObj->getList($arrField, $arrMap, $arrOrder, $page->firstRow, $page->listRows);
        $arrFormatField = array('zone_name');
        foreach($freightList as $k=>$v){
            $freightList[$k] = $freightObj->format($v, $arrFormatField);
        }
        $tplData = array(
            'list' => $freightList,
            'pageHtml' => $page->show(),
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

    /**
     * 更新运费页面
     */
    public function editFreight()
    {
        $id = intval($_GET['id']);
        $freightInfo = D('Freight')->getInfoById($id);
        $freightInfo = D('Freight')->format($freightInfo, array('zone_name'));
        $tplData = array(
            'freightInfo' => $freightInfo,
        );
        $this->assign($tplData);
        $this->display();
    }

    /**
     * 更新运费操作
     */
    public function doEditFreight()
    {
        $data = $_POST;
        if(D('Freight')->save($data)){
            $this->success('更新成功');
        }else{
            $this->error('更新失败');
        }
    }

	/**
	 * 删除运费 
	 */
	public function del()
	{
		//模型
		$freightDao = D('Freight');
		//数据
		$delIds = array();
		$postIds = $this->_post('id');
		if (!empty($postIds)) {
			$delIds = $postIds;
		}
		$getId = intval($this->_get('id'));
		if (!empty($getId)) {
			$delIds[] = $getId;
		}
		//删除
		if (empty($delIds)) {
			$this->error('请选择您要删除的数据');
		}
		$map['id'] = array('in', $delIds);
		$freightDao->where($map)->delete();
		$this->success('删除成功');
	}

    /**
     * 获取搜索匹配的ID列表
     */
    public function searchName($search)
    {
        $freightObj = D('Freight');
        $resultList = $freightObj->select();
        $i = 0;
        $idList = array();
        foreach($resultList as $k=>$v){
            $resultList[$k] = $freightObj->format($v, array('zone_name'));
            preg_match('/'.$search.'/i', $resultList[$k]['province_name'], $province_result);
            preg_match('/'.$search.'/i', $resultList[$k]['city_name'], $city_result);
            preg_match('/'.$search.'/i', $resultList[$k]['zone_name'], $zone_result);
            if($province_result OR $city_result OR $zone_result){
                $idList[$i] = $v['id'];
                $i++;
            }
        }
        return $idList;
    }

}
