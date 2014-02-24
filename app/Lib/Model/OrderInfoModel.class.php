<?php
/**
 * 订单信息Model
 */
class OrderInfoModel extends CommonModel
{
	protected $tableName = 'order_info'; 
	public function format($info,$fields){
	    if(!$fields) return false;
	    //商品名称
		if(in_array('goods_name',$fields)){
			 $good_id = D('OrderGood')->where("order_id={$info['id']}")->getField('id');
			 $info['goods_name'] = D('Good')->where("id={$good_id}")->getField('name');
		}
		//品牌名称
		if(in_array('bland_name',$fields)){
			 $good_id = D('OrderGood')->where("order_id={$info['id']}")->getField('id');
			 $bland_id = D('Good')->where("id={$good_id}")->getField('id');
			 $info['bland_name'] = D('Bland')->where("id = {$bland_id}")->getField('name');
        }
        //支付方式
        if(in_array('pay_type_name', $fields)){
            $info['pay_type_name'] = ($info['pay_type'] == 1) ? '货到付款' : '在线支付';
        }
        //支付状态
        if(in_array('pay_status_name', $fields)){
            $info['pay_status_name'] = ($info['pay_status'] == 2) ? '已付款' : '未付款';
        }
        //时间
        if(in_array('ctime_text', $fields)){
            $info['ctime_text'] = date('Y-m-d H:i', $info['ctime']);
        }
        //订单状态
        if(in_array('order_status_name', $fields)){
            switch ($info['order_status']){
            case '0':
                $status_name = '未确认';
                if(($info['pay_status'] == 0) AND ($info['pay_type'] == 0)){
                    $action_name = '立即支付';
                    $action_url  = './api/pay/alipay_web/alipayapi.php';
                }else{
                    $action_name = '确认收货';
                    $action_url  = U('Home/Order/checkStatus');
                }
                break;
            case '1':
                $status_name = '已确认';
                if(($info['pay_status'] == 0) AND ($info['pay_type'] == 0)){
                    $action_name = '立即支付';
                    $action_url  = './api/pay/alipay_web/alipayapi.php';
                }else{
                    $action_name = '确认收货';
                    $action_url  = U('Home/Order/setStatus', array('toStatus'=>'5'));
                }
                break;
            case '2':
                $status_name = '已发货';
                $action_name = '确认收货';
                $action_url  = U('Home/Order/setStatus', array('toStatus'=>'5'));
                break;
            case '5':
                $status_name = '用户确认收货';
                $action_name = '商品评价';
                $action_url  = U('Home/Comment/addComment');
                break;
            case '3':
                $status_name = '交易完成';
                $action_name = '商品评价';
                $action_url  = U('Home/Comment/addComment');
                break;
            case '4':
                $status_name = '交易未完成';
                $action_name = '交易失败';
                $action_url  = U('Home/Index/index');
                break;
            case '6':
                $status_name = '评价完成';
                $action_name = '评价完成';
                $action_url = U('Home/Order/ls');
                break;
            case '7':
                $status_name = '订单取消';
                $action_name = '订单已取消';
                $action_url = U('Home/Order/ls');
                break;
            }
            $info['order_status_name'] = $status_name;
            $info['action_name']       = $action_name;
            $info['action_url']        = $action_url;
		}
        //可执行的操作
        if(in_array('order_next', $fields)){

        }
        return $info;
	}	
}
