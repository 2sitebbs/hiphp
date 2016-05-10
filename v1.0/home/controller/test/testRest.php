<?php
/**
 * REDTFull Test控制器
 * by lane
 * @2016-03-20
 * -----------------
 */
$pageStartTime = microtime(true);   //执行计时

//初始化配置
$config[NEEDDB] = true;          //mysql支持
$config[NEEDMMCACHE] = false;    //memcache支持
$config[NEEDMMCACHED] = false;   //使用扩展memcached连memcache
$config[NEEDREDIS] = false;      //redis支持
$config[NEEDSPHINX] = false;     //sphinx全文索引支持
$config[NEEDMONGODB] = false;    //mongodb支持


/**----------------
 * controll logical code here
 * {{{
 */
Class TestRestController extends AppController implements RESTAPI{

    //默认动作，示例action
    public function index() {
        $this->restfulInit();
    }

    //获取资源数据，可在内部实现支持排序、翻页
    function getData(){
        $arr = $this->dao_read->getTest();

        $code = 1;
        $msg = 'OK';
        $this->json($code, $msg, $arr);
    }

    //根据主键ID获取资源单个数据
    function getDataById($id){
        $arr = $this->dao_read->getTestById($id);

        $code = 1;
        $msg = 'OK';
        $this->json($code, $msg, $arr);
    }

    //根据关键词搜索资源数据，可在内部实现支持排序、翻页
    function searchDataByKeyword($keyword){
        $arr = $this->dao_read->getTest();

        $code = 1;
        $msg = 'OK';
        $this->json($code, $msg, $arr);
    }

    //添加新的资源数据
    function addData(){
        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : '1';
        $dd = isset($_REQUEST['dd']) ? $_REQUEST['dd'] : 0;

        $newData = compact('id', 'dd');
        $result = $this->dao_write->addTest($newData);

        $code = 1;
        $msg = 'OK';
        $this->json($code, $msg, $result);
    }

    //根据主键ID更新资源单个数据
    function updateDataById($id){
        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : '1';
        $dd = isset($_REQUEST['dd']) ? $_REQUEST['dd'] : 1;

        $newData = compact('dd');
        $result = $this->dao_write->updateTestById($id, $newData);

        $code = 1;
        $msg = 'OK';
        $this->json($code, $msg, $result);
    }

    //根据主键ID删除资源单个数据
    function deleteDataById($id){
        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : '1';

        $result = $this->dao_write->deleteTestById($id);

        $code = 1;
        $msg = 'OK';
        $this->json($code, $msg, $result);
    }


}
/**
 * }}}
 */
