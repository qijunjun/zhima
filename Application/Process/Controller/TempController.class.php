<?php


namespace Process\Controller;

use Think\Controller;

class TempController extends Controller{
    public function addFunctionfc($pid)
    {
        if($pid==""||$pid==null)
        {
            return;
        }
        $functions=array(
            
            "包装入库"=>"/resources/Agriculture/images/operates/act15.png"
        );
        $model=M('base_product_function');

        foreach($functions as $key=>$value){
            $data['function_name']=$key;
            $data['function_image']=$value;
            $data['productid']=$pid;
            $data['companyid']=0;
            $model->add($data);
        }
        json('001','添加成功!');
        return;
    }
}
