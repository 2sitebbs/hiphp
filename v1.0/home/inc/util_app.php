<?php
/*
 * app util
 * by lane @2014-05-09
*/

Class AppUtil extends Util{

    //------------------自定义函数-----------------
    /**
     * 获取cdn图片域名
     */
    public static function getCDNImageDomain($path = 'weikanjia') {
        $domain = "http://img.2sitebbs.com/{$path}/";
        return $domain;
    }

    //获取图片路径
    public static function getImageUrl($fileName, $theme = '', $imgDir = 'images') {
        global $config;

        if (empty($theme)) {
            $theme = $config[THEME];
        }

        $imgUrl = "{$config[THEMEDIR]}/theme/{$theme}/{$imgDir}/{$fileName}";
        if (isset($_SERVER['APP_ENV']) && $_SERVER['APP_ENV'] == 'production') {    //如果是线上，则启用cdn
            $cdnUrl = self::getCDNImageDomain($config['cdnDir']);
            $imgUrl = "{$cdnUrl}{$imgUrl}";
        }

        return $imgUrl;
    }

    //获取JS文件路径
    public static function getJSUrl($fileName, $theme = '', $imgDir = 'js') {
        return self::getImageUrl($fileName, $theme, $imgDir);
    }

    //获取CSSS文件路径
    public static function getCSSUrl($fileName, $theme = '', $imgDir = 'css') {
        return self::getImageUrl($fileName, $theme, $imgDir);
    }

    //获取上传文件片路径
    public static function getUploadedFileUrl($fileName, $preDir = '') {
        global $config;

        $fileUrl = "{$config[THEMEDIR]}/upload/{$preDir}{$fileName}";
        if (isset($_SERVER['APP_ENV']) && $_SERVER['APP_ENV'] == 'production') {    //如果是线上，则启用cdn
            $cdnUrl = self::getCDNImageDomain($config['cdnDir']);
            $fileUrl = "{$cdnUrl}{$fileUrl}";
        }

        return $fileUrl;
    }

    public static function addLang($url, $userLang = '') {
        $supportedLang = array(
            LANG_ZHCN,
            LANG_ZHTW,
            LANG_EN,
        );
        $lang = isset($_GET['lang']) && in_array($_GET['lang'], $supportedLang) ? strtolower($_GET['lang']) : '';
        if (!empty($userLang)) {
            $lang = $userLang;
        }
        if (!empty($lang)) {
            $url .= strpos($url, '?') !== false ? "&lang={$lang}" : "?lang={$lang}";
        }

        return $url;
    }

    //获取分类页Url
    public static function getCateUrl($cate) {
        $controller = $cate['cate'] != 'info' ? 'cate' : 'detail';
        $cateUrl = "/?controller={$controller}&action={$cate['cate']}&nav={$cate['enname']}&gid={$cate['gid']}&cate=" . urlencode($cate['cnname']);
        return self::addLang($cateUrl);
    }

    //获取详细页Url
    public static function getDetailUrl($article) {
        $controller = 'detail';
        $articleUrl = "/?controller={$controller}&action={$article['groupcate']}&cid={$article['cid']}&title=" . urlencode($article['title']);
        return self::addLang($articleUrl);
    }

    //utf-8格式化字符串以便限制输出字符长度
    public static function substr($str, $len) {
        $strlen = mb_strlen($str, 'utf-8');
        $substr = mb_substr($str, 0, $len, 'utf-8');
        return $strlen > $len ? "{$substr}..." : $substr;
    }

    //获取语言包内容
    public static function lang($key) {
        global $langData;
        return isset($langData[$key]) ? $langData[$key] : $key;
    }


    //生成翻页代码
    public static function getPagination($total, $page = 1, $pageSize = 10) {
        if ($total == 0) {
            return '';
        }

        $maxPage = ceil($total / $pageSize);
        if ($page < 1) {
            $page = 1;
        }else if ($page > $maxPage) {
            $page = $maxPage;
        }

        $currentUrl = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '/';

        $urlArr = parse_url($currentUrl);

        $queryString = '';
        if (!empty($urlArr) && isset($urlArr['query'])) {
            $tarr = explode('&', $urlArr['query']);
            
            foreach ($tarr as $qitem) {
                $qarr = explode('=', $qitem);
                if ($qarr[0] == 'page' || $qarr[0] == 'tip') {
                    continue;
                }
                $queryString .= $qitem . '&';
            }
        }
        $linkUrl = $urlArr['path'] . '?' . (!empty($queryString) ? "{$queryString}page=" : 'page=');

        if ($maxPage <= 1) {
            return '';
        }

        $text1 = self::lang('共');
        $text2 = self::lang('页');
        $html = <<<eof
<span class="pull-left" style="margin-top:27px">
{$text1} <strong>{$maxPage}</strong> {$text2}
</span>
<div class="text-right">
    <ul class="pagination">
eof;

        if ($page > 1) {
            $prePage = $page - 1;
            $text = self::lang('第一页');
            $html .= <<<eof
            <li><a href="{$linkUrl}1">{$text}</a></li>
            <li><a href="{$linkUrl}{$prePage}">&laquo;</a></li>
eof;
        }

        if ($page - 2 >= 1) {
            $showPage = $page - 2;
            $html .= <<<eof
            <li><a href="{$linkUrl}{$showPage}">{$showPage}</a></li>
eof;
        }

        if ($page - 1 > 0) {
            $showPage = $page - 1;
            $html .= <<<eof
            <li><a href="{$linkUrl}{$showPage}">{$showPage}</a></li>
eof;
        }

        if ($page >= 1) {
            $html .= <<<eof
            <li class="active"><a href="#">{$page}</a></li>
eof;
        }

        if ($page + 1 <= $maxPage) {
            $showPage = $page + 1;
            $html .= <<<eof
            <li><a href="{$linkUrl}{$showPage}">{$showPage}</a></li>
eof;
        }

        if ($page + 2 <= $maxPage) {
            $showPage = $page + 2;
            $html .= <<<eof
            <li><a href="{$linkUrl}{$showPage}">{$showPage}</a></li>
eof;
        }
        
        if ($page < $maxPage) {
            $nextPage = $page + 1;
            $text = self::lang('末页');
            $html .= <<<eof
            <li><a href="{$linkUrl}{$nextPage}">&raquo;</a></li>
            <li><a href="{$linkUrl}{$maxPage}">{$text }</a></li>
eof;
        }

        $html .= <<<eof
    </ul>
</div>
eof;

        return $html;
    }

}
