<?php
/**
 * RESTful接口
 * by lane
 * @2015-11-26
 * -----------------
 */
interface RESTAPI {
    //获取资源数据，可在内部实现支持排序、翻页
    function getData();

    //根据主键ID获取资源单个数据
    function getDataById($id);

    //根据关键词搜索资源数据，可在内部实现支持排序、翻页
    function searchDataByKeyword($keyword);

    //添加新的资源数据
    function addData();

    //根据主键ID更新资源单个数据
    function updateDataById($id);

    //根据主键ID删除资源单个数据
    function deleteDataById($id);

}
