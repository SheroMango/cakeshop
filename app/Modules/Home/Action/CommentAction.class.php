<?php
/**
 * 会员评价表
 * @author blue
 * @version 2014-02-18
 */
class CommentAction extends HomeCommonAction
{
    /**
     * 评价列表页
     */
    public function ls()
    {
		$goods_id = intval($_REQUEST['goods_id']);
        $commentDao = D('Comment');
        $arrField = array('*');
        $arrMap['goods_id'] = array('eq', $goods_id);
        $arrOrder = array('ctime desc');

        $commentList = $commentDao->getList($arrField, $arrMap, $arrOrder, 0, 10);
        $arrFormatField = array('ctime_text', 'user_name');
        foreach($commentList as $k=>$v){
            $commentList[$k] = $commentDao->format($v, $arrFormatField);
        }
        $tplData = array(
            'title' => '购买评价',
            'current' => 'comment',
            'goods_id' => $goods_id,
            'commentList' => $commentList,
        );
		$this->assign($tplData);
		$this->display();

    }

    /**
     * 会员添加评价页
     */
    public function addComment()
    {
        $goods_id = $_POST['goods_id'];
        $goods_name = $_POST['goods_name'];
        $order_sn = $_POST['order_sn'];
        $user_name = D('User')->where('id='.$_SESSION['uid'])->getField('name');
        $tplData = array(
            'title'     => '添加评价',
            'goods_id'  => $goods_id,
            'goods_name'=> $goods_name,
            'user_name' => $user_name,
            'order_sn'  => $order_sn,
        );
        $this->assign($tplData);
        $this->display();
    }

    /**
     * 添加评价操作
     */
    public function doAddComment()
    {
        $insert = $this->_post();

        $isComment = D('OrderInfo')->where("order_sn=".$insert['order_sn'])->getField('order_status');
        if($isComment == 6){
            $data = array(
                'status' => 'danger',
                'content' => '对不起，您已经评价过该商品，不能重复评价',
            );
            echo json_encode($data);
            exit;
        }
        $insert['user_id'] = $_SESSION['uid'];
        $insert['status'] = '1';
        $insert['ctime'] = time();
        $result = D('Comment')->add($insert);
        if($result){
            D('OrderInfo')->where("order_sn=".$insert['order_sn'])->setField('order_status', 6);
            $data = array(
                'status' => 'success',
                'content' => '评论成功',
                'url' => U('Home/Order/ls'),
            );
            echo json_encode($data);
        }else{
            $data = array(
                'status' => 'danger',
                'content' => '评论失败',
            );
            echo json_encode($data);
        }
    }
}
