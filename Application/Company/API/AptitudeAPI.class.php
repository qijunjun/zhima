<?php
/**
 * Created by PhpStorm.
 * User: dichun
 * Date: 2016/8/15
 * Time: 15:00
 */

namespace Company\API;


class AptitudeAPI
{
    /**添加企业资质信息2016815
     * @param $data
     * @return mixed
     */
    public static function add($data){
        $model = M("BaseAptitude");
        if(!$model->create($data)){
            json("009",null,$model->getError());
            return false;
        }

        $id = $model->add();
        return $id;
    }

    /**更新企业资质信息2016815
     * @param $data
     * @return bool
     */
    public static function update($id,$data){
        $model = M("BaseAptitude");
        $companyid = $_SESSION['user_info']['companyid'];
        $map = [
            'companyid' => $companyid,
            'aptitudeid' => $id
        ];
        if(!$model->where($map)->create($data)){
            return false;
        }
        $res = $model->save($data);
        return $res;
    }

    /**编辑企业资质信息2016815
     * @param $id
     * @return bool
     */
    public static function edit($id){
        $model = M('BaseAptitude');
        $data=$model->find($id);

        if($data) {
            return $data;
        }else{
            return false;
        }
    }

    /**资质信息显示列表2016817
     * @return mixed
     */
    public static function showAll(){
        $model = M("BaseAptitude");
        $companyid = $_SESSION['user_info']['companyid'];
        $map = [
            'companyid' => $companyid
        ];
        $res = $model->where($map)->select();

        if($res){
            return $res;
        }else{
            return false;
        }
    }

    public static function showAllByCompany($companyid){
            $model = M("BaseAptitude");
            $map = [
                'companyid' => $companyid
            ];
            $res = $model->where($map)->select();

            if($res){
                return $res;
            }else{
                return false;
            }
        }

    /**删除资质信息2016817
     * @param $id
     * @return mixed
     */
    public static function del($id){
        $model = M("BaseAptitude");
        $map = [
            'aptitudeid' => $id
        ];
        $res = $model->where($map)->delete();

        return $res;
    }

    /**检验添加的资质信息是否重复2016817
     * @param $name
     * @return mixed
     */
    public static function searchRepeat($name){
        $model = M("BaseAptitude");
        $companyid = $_SESSION['user_info']['companyid'];
        $map = [
            'companyid' => $companyid,
            'aptitudename' => $name
        ];
        $res = $model->where($map)->find();
        return $res;
    }
}