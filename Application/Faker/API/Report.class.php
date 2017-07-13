<?php
/**
 * Created by PhpStorm.
 * User: Liming
 * Date: 2016/4/15
 * Time: 13:50
 */

namespace Faker\API;

/** 举报
 * Class Report
 * @package Faker\API
 */
class Report {
    /** 添加举报信息
     *
     * @param array $data 举报数据[
     *              'content' => '内容',
     *              'tel' => '电话',
     *              'pic' => '图片路径',
     *              'productId' => '产品id',
     *              'b' => '质量码',
     *              'c' => '二维码校验',
     *              'ip' => 'IP地址',
     *              'ipaddr' => 'IP所在地',
     *              'address' => 'GPS地址'
     *              ]
     *
     * @return bool 添加结果
     */
    public static function add($data) {
        $model = M('base_scanreport');
        $data['create_time'] = time();
        if(!$model->create($data)) {
            return false;
        }
        $id = $model->add();
        return !($id === false);
    }

    /** 查询举报信息
     * @param array $filter 过滤器
     * @param int   $start 起始记录
     * @param int   $limit 条数（-1代表取出全部数据）
     *
     * @return mixed 举报信息记录
     */
    public static function lists($filter = [], $start = 0, $limit = 100) {
        $model = M('base_scanreport');
        if($limit == -1) {
            return $model->where($filter)->select();
        } else {
            return $model->where($filter)->limit($start, $limit)->select();
        }
    }
    /** 删除举报信息
     * @param array $filter 过滤器
     */
    public static function delete($filter) {
        $model = M('base_scanreport');
        return $model->where($filter)->delete();
    }
}
