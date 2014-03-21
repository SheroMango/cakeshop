<?php
/**
 * 会员管理类
 * @author blue
 * @version 2014-02-15
 */
class UserAction extends AdminCommonAction
{
	/**
	 * 会员列表
	 */
	public function ls()
	{
		$userDao = D('User');
        //搜索
		$arrMap = array();
        $search_name = $_REQUEST['name'];
        if(!empty($search_name)){
            $arrMap['name'] = array('like', "%".$search_name."%");
        }
        //时间过滤
        $dateStart = strtotime(trim($_POST['dateStart']));
        $dateOffset = strtotime(trim($_POST['dateOffset']));
        if(!empty($dateStart) && empty($dateOffset)){
            $arrMap['ctime'] = array('between', array($dateStart, time()));
        }elseif(empty($dateStart) && !empty($dateOffset)){
            $arrMap['ctime'] = array('between', array(0, $dateOffset));
        }elseif(!empty($dateStart) && !empty($dateOffset)){
            $arrMap['ctime'] = array('between', array($dateStart, $dateOffset));
        }

        //排序
        $search_filter = $_REQUEST['spend_count'];
        if(!empty($search_filter)){
            $arrMap['spend_count'] = array('gt', $search_filter);
        }
		$arrOrder = array('spend_count desc');
        $display_order = trim($_GET['display_order']);
        $display_order_desc = trim($_GET['desc']);
        if(!empty($display_order)){
            if(!empty($display_order_desc)){
                $arrOrder = array($display_order.' desc');
                $display_desc = '0';
            }else{
                $arrOrder = array($display_order);
                $display_desc = '1';
            }
        }
		$arrField = array('*');
		$count = $userDao->getCount($arrMap);
		$page = page($count);
		$pageHtml = $page->show();
		$userList = $userDao->getList($arrField, $arrMap, $arrOrder, $page->firstRow, $page->listRows);
        $arrFormatField = array('ctime_text', 'sex_name');
		foreach($userList as $k=>$v){
            $userList[$k] = $userDao->format($v, $arrFormatField);
		}
		$tplData = array(
			'userList' => $userList,
			'pageHtml' => $pageHtml,
            'display_order' => $display_order,
            'display_desc' => $display_desc,
		);
		$this->assign($tplData);
		$this->display();
	}

    /**
     * 查看详情
     */
    public function view()
    {
        $id = intval($this->_get('id'));
        $userInfo = D('User')->getInfoById($id);
        $userInfo = D('User')->format($userInfo, array('sex_name'));
        $this->assign('userInfo', $userInfo);
        $this->display();
    }

	/**
	 * 删除用户 
	 */
	public function del()
	{
		//模型
		$userDao = D('User');
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
		$userDao->where($map)->delete();
		$this->success('删除成功');
	}

    /**
     * 导出EXCEL表格
     */
    public function exportExcel()
    {
        /** Include PHPExcel */
        require_once './core/PHPExcel/PHPExcel.php';
        // Create new PHPExcel object
        $objPHPExcel = new PHPExcel();
        // Set document properties
        $objPHPExcel->getProperties()->setCreator("深蓝解码")
							 ->setLastModifiedBy("深蓝解码")
							 ->setTitle("蛋糕商城")
							 ->setSubject("蛋糕商城")
							 ->setDescription("蛋糕商城会员数据");
        // Add some data
        $userObj = D('User');
        $arrField = array();
        $arrMap = array();
        $arrOrder = array('ctime desc');
        $userList = $userObj->getList($arrField, $arrMap, $arrOrder);
        $arrFormatField = array('sex_name', 'ctime_text');
		foreach ($userList as $key => $value) {
            $userList[$key] = $userObj->format($value, $arrFormatField);
		}

        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', '会员ID')
            ->setCellValue('B1', '姓名')
            ->setCellValue('C1', '手机号码')
            ->setCellValue('D1', '性别')
            ->setCellValue('E1', '生日')
            ->setCellValue('F1', '累计消费次数')
            ->setCellValue('G1', '累计消费金额')
            ->setCellValue('H1', '备注')
            ->setCellValue('I1', '注册时间');
        foreach($userList as $k=>$v){
            $objPHPExcel->getActiveSheet()
                ->setCellValue('A'.($k+2), $v['id'])
                ->setCellValue('B'.($k+2), $v['name'])
                ->setCellValue('C'.($k+2), $v['mobile'])
                ->setCellValue('D'.($k+2), $v['sex_name'])
                ->setCellValue('E'.($k+2), $v['birth'])
                ->setCellValue('F'.($k+2), $v['spend_times'])
                ->setCellValue('G'.($k+2), $v['spend_count'])
                ->setCellValue('H'.($k+2), $v['remark'])
                ->setCellValue('I'.($k+2), $v['ctime_text']);
        }
        // Rename worksheet
        $objPHPExcel->getActiveSheet()->setTitle('会员数据');
        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);
        // Redirect output to a client’s web browser (Excel5)
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="会员数据.xls"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

        // If you're serving to IE over SSL, then the following may be needed
        header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header ('Pragma: public'); // HTTP/1.0

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
    }

}
