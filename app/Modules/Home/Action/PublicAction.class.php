<?php
header("Content-type: text/html; charset=utf-8"); 
class PublicAction extends CommonAction
{
	/**
	 *注册
	 */
	public function register()
	{
		$tplData = array(
			'title' => '注册',
		);
		$this->assign($tplData);
		$this->display();
	}
	/**
	 *处理：注册
	 */
	public function doRegister()
	{
		//模型
		$userDao = D('User');		
		$mobile = trim($_POST['mobile']);
		$ismobile = $userDao->where('mobile='.$mobile)->find();
		if ($ismobile) {
			$data = array(
				'status' => 'warning',
				'content' => '您的手机号已注册',
			);
			echo json_encode($data);
			exit;
		}
		$data['mobile'] = $mobile;
		$password = trim($_POST['password']);
		$data['password'] = md5($password);
		$data['mtime'] = $data['ctime'] = time();
		$res = $userDao->addData($data);
		if ($res) {
			$data = array(
				'status' => 'success',
				'content' => '注册成功',
			);
			echo json_encode($data);
		} else {
			$data = array(
				'status' => 'warning',
				'content' => '注册失败',
			);
			echo json_encode($data);
		}


        /*
		for ($i=0; $i < 5; $i++) { 
			$yzm .= rand(0, 9);
		}
		session('mobileyzm', $yzm);
		$url = 'http://sms.106vip.com/sms.aspx?action=send&userid=16328&account=tanhuayou&password=asdf1234&mobile='.$mobile.'&content=您好：您的验证码是：'.$yzm.'【316蛋糕商城】&sendTime=&taskName=本次任务描述&checkcontent=1&mobilenumber=1&countnumber=1';

		file_get_contents($url);
		$rurl = U('Home/Public/mobileVerify', array('mobile'=>$mobile));
		redirect($rurl,  2,  '验证码已发到您的手机');
         */
	}

	/**
	 *登录
	 */
	public function login()
	{
		$tplData = array(
			'title' => '登录',
		);
		$this->assign($tplData);
		$this->display();
	}
	/**
	 *处理：登录
	 */
	public function doLogin()
	{
		$mobile = trim($_POST['mobile']);
		$password = md5(trim($_POST['password']));
		
		$userDao = D('User');
		$arrMap = array();
		$arrMap['mobile'] = $mobile;
		$arrMap['password'] = $password;
		$res = $userDao->where($arrMap)->find();
		if ($res) {
			session('uid', $res['id']);
            $data = array(
                'status' => 'success',
                'content' => '登录成功',
            );
            echo json_encode($data);
		} else {
            $data = array(
                'status' => 'warning',
                'content' => '用户名或密码错误',
            );
            echo json_encode($data);
		}
	}
	/**
	 * 找回密码
	 * 用户输入注册时的手机号码，获取短信验证码
	 */
	public function backpasswd()
	{
		$tplData = array(
			'title' => '手机号找回密码',
		);
		$this->assign($tplData);
		$this->display();
	}
	/**
	 *手机号验证找回密码
	 */
	public function doBakcPasswd()
	{
		//模型
		$userDao = D('User');
		
		$mobile = trim($_POST['mobile']);
		
		if (!empty($_GET['mobile'])) {
			$mobile = trim($_GET['mobile']);
		}
		$ismobile = $userDao->where('mobile='.$mobile)->find();
		
		if (empty($ismobile)) {
			$data = array(
				'status' => 'warning',
				'content' => '您的手机号未注册',
			);
			echo json_encode($data);
			exit;
		}

		for ($i=0; $i < 5; $i++) { 
			$yzm .= rand(0, 9);
		}
		session('mobileyzm', $yzm);
		$url = 'http://sms.106vip.com/sms.aspx?action=send&userid=16328&account=tanhuayou&password=asdf1234&mobile='.$mobile.'&content=您好：您的验证码是：'.$yzm.'【316蛋糕商城】&sendTime=&taskName=本次任务描述&checkcontent=1&mobilenumber=1&countnumber=1';
		
		file_get_contents($url);
		
		$data = array(
			'status' => 'success',
			'content' => '验证码已发到您的手机，请注意查收',
		);
		echo json_encode($data);
	}
	
	/**
	 * 输入验证码界面
	 */
	 public function regbackpasswd()
	 {
		$mobile = $_GET['mobile'];
		$tplData = array(
			'title' => '手机验证',
			'mobile' => $mobile,
		);
		$this->assign($tplData);
		$this->display();
	}
	
	/**
	 *处理手机验证码
	 */
	public function doBakcPasswdVerity()
	{

		$mobileyzm = trim($_POST['mobileyzm']);
		$mobile = trim($_POST['mobile']);
		if ($mobileyzm == session('mobileyzm')) {
			$data = array(
				'status' => 'success',
				'content' => '手机验证通过，请重新设置密码',
			);
			echo json_encode($data);
			//$this->success('手机验证通过', U('Home/Public/resetPasswd', array('mobile'=>$mobile)));
		} else {
			$data = array(
				'status' => 'warning',
				'content' => '验证码错误',
			);
			echo json_encode($data);
		}
	}
	//重置密码
	public function resetPasswd()
	{
		$mobile = $_GET['mobile'];
		$tplData = array(
			'title' => '重置密码',
			'mobile' => $mobile,
		);
		$this->assign($tplData);
		$this->display();
	}
	//处理重置密码
	public function doResetPasswd()
	{
		$mobile = $_POST['mobile'];
		$newpass = $_POST['newpass'];
		$renewpass = $_POST['renewpass'];

		$userDao = D('User');

		$res = $userDao->where('mobile='.$mobile)->find();
		if ($res) {
			$uid = $res['id'];
			if ($newpass == $renewpass) {
				session('uid', $uid);
				$data['password'] = md5($newpass);
				$userDao->updateData(array('id'=>$uid), $data);
				$tplData = array(
					'status' => 'success',
					'content' => '密码重置成功',
				);
				echo json_encode($tplData);
				//$this->success('密码重置成功', U('Home/Index/index'));
			} else {
				$tplData = array(
					'status' => 'warning',
					'content' => '未知错误',
				);
				echo json_encode($tplData);
				//$this->error('两次输入密码不一致');
			}
		} else {
			$this->error('修改密码失败');
		}
	}

    /**
     * 栏目尚未开通提示
     */
    public function is_empty()
    {
        $tplData = array(
            'title' => $_GET['title'],
            'content' => $_GET['content'],
        );
        $this->assign($tplData);
        $this->display();
    }
	
	/**
	 * 图片显示
	 */
	public function image()
	{
		$image = $_GET['image'];
		$this->assign('image', $image);
		$this->display();
	}
	
	public function imageSlide()
	{
        $images = $_GET['result'];
        $images = unserialize($images);
        $this->assign('images', $images);
		$this->display();
	}
	
	public function do_image()
	{
		$images = $_POST['image'];
		echo serialize($images);
	}

    /**
     * 获取城市列表
     */
    public function setCity()
    {
        $provinceList = D('Zone')->where('pid=0')->select();
        foreach($provinceList as $k=>$v){
            $cityList[$k] = D('Zone')->where('pid='.$v['id'])->select();
        }
        $newCityList = array();
        for($i=0; $i<count($cityList); $i++){
            $newCityList += array_merge($newCityList, $cityList[$i]);
        }
        foreach($newCityList as $k=>$v){
            $cityTags[$k] = urlencode($v['name']);
        }
        $data = array(
            'cityList' => $newCityList,
            'cityTags' => urldecode(json_encode($cityTags)),
        );
        $this->assign($data);
        $this->display();
    }

    /**
     * 获取城市列表
     */
    public function getCityList()
    {
        $city_name = $_REQUEST['city_name'];
        if(!empty($city_name)){
            $map['name'] = array('like', '%'.$city_name.'%');
        }
        $provinceList = D('Zone')->where('pid=0')->select();
        foreach($provinceList as $k=>$v){
            $map['pid'] = array('eq', $v['id']);
            $cityList[$k] = D('Zone')->where($map)->select();
        }
        $newCityList = array();
        for($i=0; $i<count($cityList); $i++){
            if(!empty($cityList[$i])){
            $newCityList += array_merge($newCityList, $cityList[$i]);
            }
        }
        foreach($newCityList as $k=>$v){
            $cityTags[$k]['label'] = urlencode($v['name']);
            $cityTags[$k]['value'] = urlencode(U('Home/Index/index', array('city_id'=>$v['id'], 'city'=>$v['name'])));
        }
        echo urldecode(json_encode($cityTags));
    }
	
}
