<?php
/**
 * Created by PhpStorm.
 * User: david
 * Date: 16/4/12
 * Time: 下午11:08
 */
namespace  Company\Controller;
use Common\Controller\BaseController;
use Company\API\CompanyAPI;
class BasicController extends BaseController{
    
    protected function _initialize(){

    }
    /*
     * name: 公司名称
     * phone:公司电话
     * logo:公司logo图片
     * fuzeren:公司负责人
     * describe:公司介绍
     * adrs:公司地址
     */
    public function add(){
        $data = [
            'wxid'=>I('request.wxid', null),
            'province'=>I('request.name', null),
            'city'=>I('request.city', null),
            'district'=>I('request.district', null),
            'name' => I('request.name', null),
            'logo' => I('request.logo', null),
            'businesslicense'=>I('request.businesslicense', null),
            'businesslicense_img'=>I('request.businesslicense_img', null),
            'legalperson' => I('request.legalperson', null),
            'property'=>I('request.property',null),
            'address' => I('request.address', null),
            'contact'=>I('request.contact', null),
            'phone' => I('request.phone', null),
            'introduction' => I('request.introduction', null),
            'introimage' => I('request.introimage', null),
            'introimage1' => I('request.introimage1', null),
            'introimage2' => I('request.introimage2', null),
            'introimage3' => I('request.introimage3', null),
            'introimage4' => I('request.introimage4', null),
            'longitude' => I('request.longitude'),
            'latitude' => I('request.latitude'),
            'create_time' => time()

        ];

        //$companyId=$this->get("company_id");
        $response=CompanyAPI::addCompany($data);
        echo json("001",$response);
    }
    /*
     * 更新企业基本信息
     */
    public function update(){
        $data = [
            'id'=>I('request.id'),
            'wxid'=>I('request.wxid', null),
            'province'=>I('request.province', null),
            'city'=>I('request.city', null),
            'district'=>I('request.district', null),
            'name' => I('request.name', null),
            'logo' => I('request.logo', null),
            'businesslicense'=>I('request.businesslicense', null),
            'businesslicense_img'=>I('request.businesslicense_img', null),
            'legalperson' => I('request.legalperson', null),
            'property'=>I('request.property',null),
            'address' => I('request.address', null),
            'contact'=>I('request.contact', null),
            'phone' => I('request.phone', null),
            'introduction' => I('request.introduction', null),
            'introimage' => I('request.introimage', null),
            'introimage1' => I('request.introimage1', null),
            'introimage2' => I('request.introimage2', null),
            'introimage3' => I('request.introimage3', null),
            'introimage4' => I('request.introimage4', null),
            'longitude' => I('request.longitude',0.0),
            'latitude' => I('request.latitude',0.0),
            'email'=>I('request.email', null),
            'update_time' => time()
        ];
        if($data['id'] == null ||
            $data['id'] === ''
        ) {
            json("002");
        }
        $response=CompanyAPI::updateCompany($data);
        if($response!==false){
            json("001");
        }else{
            json("002");
            return;
        }

    }
    /*
     * 编辑企业信息
     * $id:企业id(主键)
     */
    public function index(){
        $id=$_SESSION["user_info"]["companyid"];//获取企业id
        if(($id!=='')&&($id!==null)){

            $data=CompanyAPI::editCompany($id);
            if($data){
               json("001",$data);
            }else{
                json("002");
                return;
            }

        }else{
            json("002");
            return;
        }

    }
    /*
     * 生成二维码对应的url
     * 失败返回null
     */
    public function getCodeUrl($bcode)
    {
        $companyid=$_SESSION["user_info"]["companyid"];//获取企业id
        $pattern = '/^' . $companyid . '1\d{9}$/';
        $res=preg_match($pattern,$bcode);
        if($res)
        {
            $ccode= CompanyAPI::getCCode($bcode);
            if($ccode)
            {
                $qurl="http://zhima.zmade.cn/code/api/verify/c/".$ccode['c']."/b/".$bcode;
                return $qurl;
            }
            else
            {
                return null;
            }
        }
        else
        {
            return;
        }
    }

}
