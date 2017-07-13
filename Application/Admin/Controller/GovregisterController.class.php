<?php
/**
 * Created by PhpStorm.
 * User: dichun
 * Date: 2016/8/13
 * Time: 16:56
 */

namespace Admin\Controller;

use Common\Controller\BaseController;
use Common\API\EntranceAPI;
class GovregisterController extends BaseController
{
    public function registerGov(){
        $province = I('province');
        $city = I('city');
        $district = I('district');
        $regioncode = I('regioncode');
        $government = I('government');
        $username = I('username');
        $password = I('password');
        $phone = I('phone');
        $contact = I('contact');
        $type = 2;

        $result = EntranceAPI::registerGov($username, $password, $phone, $government, $type, $province, $city, $district, $regioncode,$contact);
        echo  json_encode($result);
    }
}