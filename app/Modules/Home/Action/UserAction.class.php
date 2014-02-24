<?php
/**
 * 个人中心
 */

class UserAction extends HomeCommonAction
{
	/**
	 *个人中心
	 */
	public function center()
	{
		$tplData = array(
			'title' => '个人中心',
		);
		$this->assign($tplData);
		$this->display();
	}
	/**
	 *修改资料信息
	 */
	public function editInfo()
	{
		$uid = session('uid');
		//模型
		$userDao = D('User');

		$userInfo = $userDao->getInfoById($uid);
		$userInfo['birth'] = date('Y年m月d日', $userInfo['birth']);
		//输出到模版
		$tplData = array(
			'title' => '基本资料',
			'userInfo' => $userInfo,
		);
		$this->assign($tplData);
		$this->display();
	}
	
	/**
	 * 修改资料操作
	 */
	public function doEditInfo()
	{
		$userDao = D('User');
		$update = $this->_post('page');
		$result = $userDao->save($update);
		if($result){
			$data = array(
				'status' => 'success',
				'content' => '资料修改成功',
			);
			echo json_encode($data);
		}else{
			$data = array(
				'status' => 'warning',
				'content' => '资料修改失败',
			);
			echo json_encode($data);
		}
	}
	

	/**
	 *修改密码
	 */
	public function editPassword()
	{
		$tplData = array(
			'title' => '修改密码',
		);
		$this->assign($tplData);
		$this->display();
	}
	/**
	 *处理：修改密码
	 */
	public function doEditPassword()
	{
		$pass = md5(trim($_POST['pass']));
		$newpass = $_POST['newpass'];
		$renewpass = $_POST['renewpass'];
		
		$userDao = D('User');
		$uid = session('uid');
		
		$arrMap = array();
		$arrMap['id'] = array('eq', $uid);
		$arrMap['password'] = array('eq', $pass);

		$res = $userDao->getInfo(array(), $arrMap);
		if (empty($res)) {
            $data = array(
                'status' => 'warning',
                'content' => '原密码输入不正确',
            );
            echo json_encode($data);
            exit;
		}
		$data['password'] = md5($newpass);
		$ret = $userDao->updateData(array('id'=>$uid), $data);
		if ($ret) {
            $data = array(
                'status' => 'success',
                'content' => '修改密码成功',
            );
            echo json_encode($data);
		} else {
            $data = array(
                'status' => 'warning',
                'content' => '修改密码失败',
            );
            echo json_encode($data);
		}
	}

    /**
     * 退出登录
     */
    public function logout(){
        $userDao = D('User');
        session_destroy();
        $data = array(
			'status' => 'success',
			'content' => '注销成功',
		);
		echo json_encode($data);
    }
}
