<?php
/**
 * util functions
 * created @2013-09-02
 * by lane
 */
if (!defined('LANG_ZHCN')) {define('LANG_ZHCN', 'zh-cn');}
if (!defined('LANG_ZHTW')) {define('LANG_ZHTW', 'zh-tw');}
if (!defined('LANG_EN')) {define('LANG_EN', 'en');}

Class Util{
    public static function generatePassword($password) {
        global $config;

        return md5($config[PREKEY4PASSWORD] . md5($config[PREKEY4PASSWORD].$password) . $config[PREKEY4PASSWORD]);
    }

    public static function curlGet($url, $post = false, $postFields = array(), $timeout = 2) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);

        if ($post && !empty($postFields)) {
            curl_setopt($ch, CURLOPT_POST, 1);

            //build query string
            if (is_array($postFields)) {
                $postFields = http_build_query($postFields);
            }
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
        }

        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }

    //获取自定义的rest请求方法
    public static function getRESTMethod() {
        //默认设置为GET
        $restMethod = isset($_SERVER['HTTP_RESTMETHOD']) ? strtoupper($_SERVER['HTTP_RESTMETHOD']) : 'GET';
        $allowedMethods = array(
            'GET',
            'POST',
            'PUT',
            'DELETE',
        );

        //如果没指定自定义的请求方法，或者自定义方法不在允许范围内，则取浏览器的request_method
        if (!in_array($restMethod, $allowedMethods)) {
            $restMethod = isset($_SERVER['REQUEST_METHOD']) ? strtoupper($_SERVER['REQUEST_METHOD']) : 'GET';
        }

        return $restMethod;
    }

    //支持自定义REST请求方法
    public static function restCurl($url, $restMethod = 'GET', $postFields = array(), $timeout = 2) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);

        $postMethods = array(
            'POST',
            'PUT',
            'DELETE',
        );
        if (in_array($restMethod, $postMethods)) {
            //设置request_method = POST
            curl_setopt($ch, CURLOPT_POST, 1);

            //设置自定义请求方法
            $headers = array(
                "RESTMETHOD: {$restMethod}",
            );
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }

        if (!empty($postFields)) {
            if (is_array($postFields)) {
                $postFields = http_build_query($postFields);
            }
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
        }

        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }

    public static function getDomainFromUrl($url) {
        $urlArr = parse_url($url);
        $domain = isset($urlArr['host']) ? $urlArr['host'] : '';
        return $domain;
    }

    public static function isRobot() {
        $agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';

        $reg_googlebot = '/Googlebot/i';
        $reg_googlebot_adwords = '/AdsBot-Google/i';
        $reg_yahoo = '/Yahoo! Slurp/i';
        $reg_yahoo_cn = '/Yahoo! Slurp China/i';
        $reg_bing = '/bingbot/i';
        $reg_msn = '/msnbot/i';

        $reg_soso = '/Sosospider/i';
        $reg_baidu = '/Baiduspider/i';
        $reg_360so = '/360Spider/i';
        $reg_sogou = '/Sogou web spider/i';
        $reg_youdao = '/YoudaoBot/i';

        if (  //baidu, sogou, soso, youdao, yahoo_cn
            preg_match($reg_soso, $agent) ||
            preg_match($reg_baidu, $agent) ||
            preg_match($reg_360so, $agent) ||
            preg_match($reg_sogou, $agent) ||
            preg_match($reg_youdao, $agent) ||

            preg_match($reg_googlebot, $agent) ||
            preg_match($reg_googlebot_adwords, $agent) ||
            preg_match($reg_yahoo, $agent) ||
            preg_match($reg_yahoo_cn, $agent) ||
            preg_match($reg_bing, $agent) ||
            preg_match($reg_msn, $agent)
        ) {
            return true;
        }else {
            return false;
        }
    }

    //parameter filter
    public static function cleanParameters(&$arr) {
        $filters = array(
            '\'', '"', '<', '>', '&',
            '(', ')', '~', '!', '#', '$', '%', '^',
            '*','|', '\\',
            '[', ']',
            '{', '}',
            '=', '?', 
        );
        foreach ($arr as $key => $val) {
            if (is_array($val)) {
                Util::cleanParameters($val);
                $arr[$key] = $val;
                continue;
            }

            $temp = str_replace($filters, '', $val);
            $temp = str_replace(array('+','　','　',), ' ', $temp);
            $arr[$key] = strip_tags($temp);
        }
    }

    //htmlspecialchars
    public static function htmlspecialchars(&$arr) {
        foreach ($arr as $key => $val) {
            if (is_array($val)) {
                Util::htmlspecialchars($val);
                $arr[$key] = $val;
                continue;
            }

            //转义之前先判断下是否有转义
            $tmp = htmlspecialchars_decode($val, ENT_QUOTES);
            $arr[$key] = $tmp == $val ? htmlspecialchars($val, ENT_QUOTES) : $val;
        }
    }

    //compress html
    public static function compressHtml($html) {
        $regs = array(
            '/[ ]{2,}/',
            '/<!--[^>]+-->/',
            '/\/\*[^\*]+\*\//',
            '/\{[ ]*\/\/[\w ]+/',
            '/[\r\n\t]+/',
        );

        $replace = array(
            '',
            '',
            '',
            '{',
            '',
        );
        return preg_replace($regs, $replace, $html);
    }

    public static function isMobile() {
        $userAgent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
        $host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '';
        if (empty($userAgent) || empty($host)) {return false;}

        $reg_mobile = '/Linux; U;/i';
        $reg_android = '/Android/i';
        $reg_iphone = '/iphone os/i';
        $reg_windowCe = '/windows ce/i';
        $reg_windowMobile = '/windows mobile/i';

        //for search engines
        $reg_google_mobile = '/Googlebot-Mobile/i';
        $reg_google = '/Googlebot/i';
        $reg_yahoo = '/Yahoo! Slurp/i';
        $reg_bing = '/bingbot/i';
        $reg_msn = '/msnbot/i';

        $reg_baidu = '/Baiduspider/i';
        $reg_sogou = '/Sogou web spider/i';
        $reg_youdao = '/YoudaoBot/i';

        //mobile check
        $isMobile = preg_match($reg_mobile, $userAgent);

        $isIPhone = preg_match($reg_iphone, $userAgent);
        $isAndroid = preg_match($reg_android, $userAgent);
        $isWindowMobile = preg_match($reg_windowCe, $userAgent) && preg_match($reg_windowMobile, $userAgent);

        //search engine check
        $isGoogleMobileBot = preg_match($reg_google_mobile, $userAgent);
        $isGoogleBot = preg_match($reg_google, $userAgent);
        $isYahooBot = preg_match($reg_yahoo, $userAgent);
        $isBingBot = preg_match($reg_bing, $userAgent);
        $isMSNBot = preg_match($reg_msn, $userAgent);

        $isBaidu = preg_match($reg_baidu, $userAgent);
        $isSogou = preg_match($reg_sogou, $userAgent);
        $isYoudao = preg_match($reg_youdao, $userAgent);

        return $isIPhone || $isAndroid || $isWindowMobile || $isMobile || $isGoogleMobileBot;
    }

    //判断是否为微信浏览器
    public static function isWechatBrowser() {
        $isWechat = isset($_COOKIE['isWechat']) && $_COOKIE['isWechat'] ? true : false;

        if (!$isWechat) {
            $userAgent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
            $reg_wechat = '/MicroMessenger\//i';
            $isWechat = preg_match($reg_wechat, $userAgent);
        }

        return $isWechat;
    }

    public static function getKeywordFromSrcUrl($url, $reg) {
        preg_match($reg, $url, $out);
        return isset($out[1]) ? $out[1] : '';
    }

    public static function getSearchKeyword($search_url) {
        $config = array(
            "s1" => array(
                "domain" => "google.com",
                "kw" => "q",
                "charset" => "utf-8"
            ),
            "s3" => array(
                "domain" => "google.cn",
                "kw" => "q",
                "charset" => "utf-8"
            ),
            "s4" => array(
                "domain" => "baidu.com",
                "kw" => "wd",
                "charset" => "gbk"
            ),
            "s5" => array(
                "domain" => "soso.com",
                "kw" => "q",
                "charset" => "utf-8"
            ),
            "s6" => array(
                "domain" => "yahoo.com",
                "kw" => "q",
                "charset" => "utf-8"
            ),
            "s7" => array(
                "domain" => "bing.com", 
                "kw" => "q",
                "charset" => "utf-8"
            ),
            "s8" => array(
                "domain" => "sogou.com",
                "kw" => "query", 
                "charset" => "gbk"
            ),
            "s9" => array(
                "domain" => "youdao.com",
                "kw" => "q",
                "charset" => "utf-8"
            ),
        );

        $arr_key = array();
        foreach ($config as $item) {
            $sh = preg_match("/\b{$item['domain']}\b/", $search_url);
            if ($sh) {
                $query = "/{$item['kw']}=([^&]+)&?/i";
                $s_s_keyword = self::getKeywordFromSrcUrl($search_url, $query);
                $F_Skey = urldecode($s_s_keyword);
                if ($item['charset'] != "utf-8") {
                    $F_Skey = iconv($item['charset'], "UTF-8", $F_Skey); //最终提取的关键词
                }
                $arr_key = array('q' => $F_Skey, 'from' => $item['domain']);
                break;
            }
        }

        return $arr_key;
    }

    public static function isFromSearchEngine() {
        $srcUrl = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
        if (empty($srcUrl)) {return false;}

        $arr = self::getSearchKeyword($srcUrl);
        if (!empty($arr['q'])) {    //如果是从搜索引擎过来的
            return true;
        }else {
            return false;
        }
    }

    /** 
     * google api 二维码生成【QRcode可以存储最多4296个字母数字类型的任意文本，具体可以查看二维码数据格式】
     * @param string $chl 二维码包含的信息，可以是数字、字符、二进制信息、汉字。不能混合数据类型，数据必须经过UTF-8 URL-encoded.如果需要传递的信息超过2K个字节，请使用POST方式
     * @param int $widhtHeight 生成二维码的尺寸设置
     * @param string $EC_level 可选纠错级别，QR码支持四个等级纠错，用来恢复丢失的、读错的、模糊的、数据。
     *                         L-默认：可以识别已损失的7%的数据
     *                         M-可以识别已损失15%的数据
     *                         Q-可以识别已损失25%的数据
     *                         H-可以识别已损失30%的数据
     * @param int $margin 生成的二维码离图片边框的距离
     */  
    public static function QRfromGoogle($str, $width='120', $height='120', $EC_level='L', $margin='0') {
        $chl = urlencode($str);
        echo '<img src="http://chart.apis.google.com/chart?chs='.$width.'x'.$height.
            '&cht=qr&chld='.$EC_level.'|'.$margin.'&chl='.$chl.'" alt="QR code" width="'.$width.
            '" height="'.$height.'">';
    }

    public static function getHttpLang() {
        $agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
        $lang = isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? $_SERVER['HTTP_ACCEPT_LANGUAGE'] : '';
        $reg_cn = '/' . LANG_ZHCN . '/i';
        $reg_tw = '/' . LANG_ZHTW . '/i';

        $reg_googlebot = '/Googlebot/i';
        $reg_yahoo = '/Yahoo! Slurp/i';
        $reg_yahoo_cn = '/Yahoo! Slurp China/i';
        $reg_bing = '/bingbot/i';
        $reg_msn = '/msnbot/i';

        $reg_baidu = '/Baiduspider/i';
        $reg_sogou = '/Sogou web spider/i';
        $reg_soso = '/Sosospider/i';
        $reg_youdao = '/YoudaoBot/i';

        if (preg_match($reg_tw, $lang)) {
            return LANG_ZHTW;
        }else if (preg_match($reg_cn, $lang)) {
            return LANG_ZHCN;
        }else if (  //baidu, sogou, soso, youdao, yahoo_cn
            preg_match($reg_baidu, $agent) ||
            preg_match($reg_sogou, $agent) ||
            preg_match($reg_soso, $agent) ||
            preg_match($reg_yahoo_cn, $agent) ||
            preg_match($reg_youdao, $agent)
        ) {
            return LANG_ZHCN;
        }else {
            return LANG_ZHTW;
        }
    }

    public static function getImgDir($fileName) {
        $dir1 = substr($fileName, 0, 2);
        $dir2 = substr($fileName, 2, 2);

        return "{$dir1}/{$dir2}/";
    }

    //文件缓存数据
    public static function fileCacheSet($fileName, $arrData) {
		$f = fopen($fileName, 'w');
		if (!$f) {return false;}
        
        $cnt = serialize($arrData);
        fwrite($f, $cnt);
        
		fclose($f);

		return true;
	}
    
    //文件缓存取数据
    public static function fileCacheGet($fileName) {
		$f = fopen($fileName, 'r');
		if (!$f) {return false;}

        $cnt = fgets($f);
        fclose($f);

        $arr = unserialize($cnt);
		return $arr;
	}
    
    
	public static function flushTextFile($fileName, $arrData) {
		$f = fopen($fileName, 'w');
		if (!$f) {return false;}
		if (!is_array($arrData) || empty($arrData)) {
			fwrite($f, '');
		}else {
			foreach ($arrData as $str) {
				fwrite($f, $str);
			}
		}
		fclose($f);

		return true;
	}

	public static function append2File($fileName, $str) {
		$f = fopen($fileName, 'a');
		if (!$f) {return false;}
		fwrite($f, $str);
		fclose($f);

		return true;
	}

    public static function getFilesModifyTime($filename) {
        $time = @filemtime($filename);
        if (!$time) {
            return '';
        }
        return $time;
    }

    /**
    * 保存变量供视图使用
    * 调用方法：Util::setViewVar($key, $val);
    * 调用方法：Util::setViewVar(array('a'=>'x'));
    */
    public static function setViewVar($arr, $val = null) {
        global $pageData;

        if (!is_array($arr) && !empty($arr)) {  //支持key，value形式传参
            $temp = array();
            $temp[$arr] = $val;
            $arr = $temp;
        }

        $pageData = array_merge($pageData, $arr);
        return $pageData;
    }

    public static function render($viewGroup, $viewName, $layoutName = 'main', $themeName = 'default') {
        global $config, $appPath, $pageData;    //必须变量
        global $pageName, $pageStartTime, $pageTitle, $pageDescription, $pageKeywords;  //可选

        $isIE6 = false;
        if (
            isset($_SERVER['HTTP_USER_AGENT']) &&
            preg_match('/MSIE 6/iU', $_SERVER['HTTP_USER_AGENT'])
        ) {
            $isIE6 = true;
        }
        
        //IE 6- check
        if ( $isIE6 ) {
            header("Content-type: text/html; charset=utf-8");
            echo "Sorry, 我们暂时不支持IE 6及以下版本的浏览器，推荐使用Google浏览器Chrome。";
            exit;
        }

        //APP目录
        if (empty($appPath)) {
            $appPath = dirname(__FILE__) . '/..';
        }
        $layoutPath = "{$appPath}/theme/{$themeName}/views/layout";

        if (isset($config[DEBUG]) && $config[DEBUG]) {
            include_once "{$layoutPath}/{$layoutName}.php";
        }else {
            ob_start();
            include_once "{$layoutPath}/{$layoutName}.php";

            $htmlCode = ob_get_contents();
            ob_end_clean();

            ob_start('ob_gzhandler');
            echo Util::compressHtml($htmlCode);
            ob_end_flush();
        }

        //debug information
        if (isset($config[DEBUG]) && $config[DEBUG]) {
            $pageEndTime = microtime(true);
            $pageTimeCost = $pageEndTime - $pageStartTime;
            $serverIp = isset($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : '127.0.0.1';
            $ips = explode('.', $serverIp);
            echo "<!--{$pageTimeCost} {$ips[3]} {$config[APPVERSION]}-->";
        }

        exit;
    }

    public static function echoMsg($key) {
        global $pageData;
        if (!isset($pageData) || empty($pageData) || !isset($pageData[$key])) {
            return false;
        }

        echo $pageData[$key];
        return true;
    }

    public static function echoErrorMsg($key, $htmlTemplate = '') {
        global $pageData;
        if (!isset($pageData['error']) || empty($pageData['error']) || !isset($pageData['error'][$key])) {
            return false;
        }

        $error = $pageData['error'];
        $msg = $error[$key];
        if (!empty($htmlTemplate) && !empty($msg)) {
            $msg = str_replace('{msg}', $msg, $htmlTemplate);
        }

        echo $msg;
        return true;
    }

    public static function verifyData($arr, $rules) {
        $out = array();

        foreach ($arr as $key => $val) {
            $out[$key] = isset($rules[$key]) ? self::verifyVariable($val, $rules[$key]) : true;
        }

        return $out;
    }

    public static function verifyVariable($val, $ruleKey) {
        $rules = array(
            'email' => '/^\w+@(\w+\.)+\w{2,3}$/U',
            'cellphone' => '/^1[34578]\d{9}$/U',
            'number' => '/^\d+$/U',
            'string' => '/^[\w\-]+$/U',
            'noempty' => '/^[\s\S]+$/U',
            'chinese' => '/^[^\x4e00-\x9fa5]+$/u',
            'birthday' => '/^\d{4}\-\d{2}\-\d{2}$/U',
            'date' => '/^\d{4}\-\d{2}\-\d{2}$/U',
        );

        if (!isset($rules[$ruleKey])) {
            return false;
        }

        return empty($val) || preg_match($rules[$ruleKey], $val);
    }

    public static function assignErrorMsg($errorMsg, $verifyResult) {
        $out = array();

        foreach ($verifyResult as $key => $val) {
            if (!$val && isset($errorMsg[$key])) {
                $out[$key] = $errorMsg[$key];
            }
        }

        return $out;
    }
    
    //UUID固定格式
	public static function generateUUID() {
        if (function_exists('com_create_guid')){
            return com_create_guid();
        }else{
            mt_srand((double)microtime()*10000);    //optional for php 4.2.0 and up.
            $charid = strtoupper(md5(uniqid(rand(), true)));
            $hyphen = chr(45);  // "-"
            $uuid = substr($charid, 0, 8).$hyphen
                    .substr($charid, 8, 4).$hyphen
                    .substr($charid,12, 4).$hyphen
                    .substr($charid,16, 4).$hyphen
                    .substr($charid,20,12);
            return $uuid;
        }
	}

    //获取用户的真实ip
    public static function getRealIp() {
        $ip = "Unknown";

        if (isset($_SERVER["HTTP_X_REAL_IP"]) && !empty($_SERVER["HTTP_X_REAL_IP"])) {
            $ip = $_SERVER["HTTP_X_REAL_IP"];
        }
        elseif (isset($HTTP_SERVER_VARS["HTTP_X_FORWARDED_FOR"]) && !empty($HTTP_SERVER_VARS["HTTP_X_FORWARDED_FOR"])) {
            $ip = $HTTP_SERVER_VARS["HTTP_X_FORWARDED_FOR"];
        }
        elseif (isset($HTTP_SERVER_VARS["HTTP_CLIENT_IP"]) && !empty($HTTP_SERVER_VARS["HTTP_CLIENT_IP"])) {
            $ip = $HTTP_SERVER_VARS["HTTP_CLIENT_IP"];
        }
        elseif (isset($HTTP_SERVER_VARS["REMOTE_ADDR"]) && !empty($HTTP_SERVER_VARS["REMOTE_ADDR"])) {
            $ip = $HTTP_SERVER_VARS["REMOTE_ADDR"];
        }
        elseif (getenv("HTTP_X_FORWARDED_FOR")) {
            $ip = getenv("HTTP_X_FORWARDED_FOR");
        }
        elseif (getenv("HTTP_CLIENT_IP")) {
            $ip = getenv("HTTP_CLIENT_IP");
        }
        elseif (getenv("REMOTE_ADDR")) {
            $ip = getenv("REMOTE_ADDR");
        }

        return $ip;
    }

    public static function getCookiePre() {
        global $config;
        return isset($config[COOKIEPRE]) ? $config[COOKIEPRE] : 'hiphp_';
    }

    public static function setcookie($key, $val, $expire = 86400, $path = '/') {
        $pre = self::getCookiePre();
        setcookie("{$pre}_{$key}", $val, time() + $expire, $path);
    }

    public static function getcookie($key) {
        $pre = self::getCookiePre();
        return isset($_COOKIE["{$pre}_{$key}"]) ? $_COOKIE["{$pre}_{$key}"] : null;
    }

    //删除cookie
    public static function delcookie($key, $path = '/') {
        $pre = self::getCookiePre();
        setcookie("{$pre}_{$key}", '', time() - 3600, $path);
        unset($_COOKIE["{$pre}_{$key}"]);
    }

    //将数值字符串转换为整形或浮点型
    public static function strtonumber($strnum) {
        if (is_numeric($strnum)) {
            return $strnum + 0;
        }else {
            return $strnum;
        }
    }

    //将数组中的数值字符串转换为整形或浮点型
    public static function arraytonumber($arr) {
        if (!is_array($arr)) {return false;}

        $newArr = array();
        foreach ($arr as $key => $val) {
            if (!is_array($val)) {
                $newArr[strtolower($key)] = self::strtonumber($val);
            }else {
                $newArr[strtolower($key)] = Util::arraytonumber($val);
            }
        }

        return $newArr;
    }

    //签名检查
    public static function checkSignature($privateKey = '') {
        if (!isset($_REQUEST["signature"])) {return false;}

        $signature = $_REQUEST["signature"];
        $timestamp = $_REQUEST["timestamp"];
        $nonce = $_REQUEST["nonce"];

        $time = time();
        if ($time - $timestamp > 5) {
            return false;
        }

        $token = empty($privateKey) ? YUNAPPSECRET : $privateKey;
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode( $tmpArr );
        $tmpStr = sha1( $tmpStr );
        if( $tmpStr == $signature ){
            return true;
        }else{
            return false;
        }
    }

    //获取数组参数的特征码
    public static function getNonce($arrParas = array()) {
        sort($arrParas, SORT_STRING);
        $str = "";

        for ($arrParas as $key => $value) {
            $str .= "{$key}={$value}&";
        }

        return md5($str);
    }

    //生成签名
    public static function generateSignature($timestamp, $nonce, $privateKey = '') {
        $token = empty($privateKey) ? YUNAPPSECRET : $privateKey;
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode( $tmpArr );
        $signature = sha1( $tmpStr );

        return $signature;
    }

}
