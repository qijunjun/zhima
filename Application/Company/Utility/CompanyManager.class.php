<?php
/**
 * Created by PhpStorm.
 * User: david
 * Date: 16/4/12
 * Time: 下午11:46
 */
namespace Company\Utility;

class ComapanyManager{

    public static function addCompany($companyId,$name,$info){

        $company=M("company");
        $data=array(
          "comanpyid"=>$companyId,
           "name"=>$name,
           "info"=>$info
        );
        $result=$company->data($data)->add();
        if($result){
            $data1["id"]=$result;
            $result=$data1;
        }
        return $result;
    }
}