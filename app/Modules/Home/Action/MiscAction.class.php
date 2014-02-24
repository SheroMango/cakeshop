<?php
/**
 * 杂项控制器
 * @version 2013-09-17
 */
class MiscAction extends CommonAction {
    
    /**
     * 充值
     */
    public function topup()
    {
        $this->display();
    }

    
    /**
     * 第三方登陆
     */
    public function connect()
    {
        
    }
    
    ////////////////////验证码////////////////////
    /**
     * 生成验证码
     */
    public function verify()
    {
        import('ORG.Util.Image');
        $type = trim($_GET['type']);
        $font     = SITE_PUBLIC_PATH.'font/msyh.ttf';
        //zh=中文
        if ($type == 'zh' && file_exists($font)) {
            Image::GBVerify(3, 'png', 140, 50, $font, $name);
        } else {
            //参数：长度、模式（0=字母 1=数字 2=大写字母 3=小写字母 4=中文 5=混合）、图片类型、宽度、高度、session字段名
            Image::buildImageVerify(5, 2, 'png', 50, 25, 'verify');
        }
    }

    /**
     * 验证码是否正确
     */
    public function isVerifyAvailable()
    {
        $verify = trim($_POST['verify']);
        if (md5(strtoupper($verify)) == $_SESSION['verify']) {
            echo 'success';
        } else {
            echo '验证码输入错误';
        }
    }
}
?>