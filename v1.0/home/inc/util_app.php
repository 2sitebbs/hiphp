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

        $imgUrl = "{$config[THEMEDIR]}/theme/{$theme}/$imgDir/{$fileName}";
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

}
