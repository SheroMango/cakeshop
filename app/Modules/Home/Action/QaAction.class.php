<?php
/**
 * 问题咨询模块控制器
 * @author blue
 * @version 2014-02-19
 */
class QaAction extends CommonAction
{
    /**
     * 咨询列表
     */
	public function consult()
	{
        $goods_id = intval($_REQUEST['goods_id']);
        $QaDao = D('Qa');
        $arrField = array('*');
        $arrMap['status'] = array('eq', 0);
        $arrOrder = array('ctime desc');
        $qaList = $QaDao->getList($arrField, $arrMap, $arrOrder, 0, 10);
        $tplData = array(
            'title' => '问题咨询',
            'current' => 'qa',
            'goods_id' => $goods_id,
            'qaList' => $qaList,
            'cakeInfo' => $cakeInfo,
            'last_id' => '5',
        );
		$this->assign($tplData);
		$this->display();
	}

    /**
     * 获取更多咨询
     */
    public function getQa(){
        $QaDao = D('Qa');
        $arrField = array('*');
        $arrMap['status'] = array('eq', 0);
        $arrOrder = array('ctime desc');
        $last_id = intval($this->_get('last_id'));
        
        $qaList = $QaDao->getList($arrField, $arrMap, $arrOrder, $last_id, 10);
        if($last_id >= ($QaDao->getCount($arrMap) - 1)){
            $data = array(
                'result' => '已是最后一条',
                'last_id' => '0',
                'status' => 'over',
            );
            echo json_encode($data);
        }else{
            $strResult = '';
            foreach($qaList as $k=>$v){
                $result[$k] = '<li><p><span>咨询内容：</span>'.$v['question'].'</p><p><span class="color-service">316蛋糕商城：</span>'.$v['answer'].'</p></li>';
                $strResult .= $result[$k];
            }
            $data = array(
                'last_id' => ($last_id + count($qaList)),
                'result' => $strResult,
            );
            echo json_encode($data);
        }
    }

    /**
     * 添加咨询
     */
    public function addConsult()
    {
        $insert = $this->_post();
        $QaDao = D('Qa');
        $insert['uid'] = $_SESSION['uid'];
        $insert['ctime'] = time();
        $id = $QaDao->add($insert);
        echo $id;
    }
}
