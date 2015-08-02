<?php
/**
 * App控制器基类
 * by lane
 * @2015-03-15
 * -----------------
 */
Class AppController extends Controller {

    /*public function __construct() {
        parent::__construct();
    }*/

    public function init() {
        if ($this->config[NEEDDB]) {
            //读取顶部导航栏数据
            $order = 'sort';
            $navigators = $this->dao_read->getSitegroupBySidAndPid($this->config[SITEID], 0, $order);

            //读取底部导航数据
            $order = 'pid,sort';
            $allNavigators = $this->dao_read->getSitegroupBySid($this->config[SITEID], $order);

            //对子分类做归类，只支持两级分类
            $productNavigator = array();        //产品栏目
            $newsNavigator = array();           //新闻栏目
            $noticeNavigator = array();          //公告栏目

            //获取当前分类
            $gid = isset($_GET['gid']) ? (int)$_GET['gid'] : 0;
            $currentGroup = array();

            foreach ($navigators as $key => $nav) {
                $arr = array();
                foreach ($allNavigators as $item) {
                    if (empty($currentGroup) && $item['gid'] == $gid) {
                        $currentGroup = $item;
                    }

                    if ($item['pid'] == $nav['gid']) {  //根据父分类ID归类
                        $arr[] = $item;
                    }
                }
                $navigators[$key]['subnavs'] = $arr;

                //保存当前分类
                if ($nav['gid'] == $gid) {
                    $currentGroup = $navigators[$key];
                }

                if ($nav['cate'] == 'product') {
                    $productNavigator = $navigators[$key];
                }else if ($nav['cate'] == 'news') {
                    $newsNavigator = $navigators[$key];
                }else if ($nav['cate'] == 'notice') {
                    $noticeNavigator = $navigators[$key];
                    unset($navigators[$key]);                       //不在顶部和底部导航栏出现
                }
            }

            //获取最新一条未过期的公告
            $today = date('Y-m-d H:i:s');
            $conditions = array(
                'sid' => $this->config[SITEID],
                'groupcate' => 'notice',
                'expire' => "> {$today}",
            );
            $order = 'addtime desc';
            $limit = '1';
            $arrNotice = $this->dao_read->getSitecontent($conditions, $order, $limit);
            $notice = !empty($arrNotice) ? $arrNotice[0] : array();

            //设置视图访问变量
            Util::setViewVar(compact('navigators', 'allNavigators', 'productNavigator', 'newsNavigator', 'currentGroup', 'notice'));
        }
    }
}