<?php
    namespace Recall\Controller;

    use Common\Controller\BaseController;
    use Recall\API\RecallAPI;
//    use CheckIO\API\CheckioAPI;
    use \Think\Controller;

    class RecallController extends BaseController
    {
        protected function _initialize()
        {

        }

        /**
         * 新建召回信息
         */
        public function add()
        {
            $companyid = $_SESSION["user_info"]["companyid"];//获取企业id
            $qrcodestart = I('bStart', 0, 'htmlspecialchars');
            $qrcodeend = I('bEnd', 0, 'htmlspecialchars');
            $reason = I('promptText', '', 'htmlspecialchars');
            if ($qrcodestart > $qrcodeend)
            {
                json("002", null, "起始码不可大于结束码");

                return;
            }
            if(preg_match('/^'.$companyid.'[1]\d{9}$/',$qrcodestart) == false){
                echo json("002",null,"输入的码段有问题");
                return;
            }
            if(preg_match('/^'.$companyid.'[1]\d{9}$/',$qrcodeend) == false){
                echo json("002",null,"输入的码段有问题");
                return;
            }
            $idcollection = RecallAPI::checkQrcodeRepeat($qrcodestart, $qrcodeend);
            if (sizeof($idcollection) > 0)
            {
                json("009", $idcollection, "新增的码段与现有的召回码段重复");

                return;
            }

            $rows = RecallAPI::fetchProductCorr($qrcodestart, $qrcodeend);
            if ($rows == false)
            {
                json("002", null, "该召回码段没有对应任何产品");

                return;
            }
            $productid = $rows[0]["product_id"];

            $data = [
                'company_id' => $companyid,
                'product_id' => $productid,
                'qrcode_range_s' => $qrcodestart,
                'qrcode_range_e' => $qrcodeend,
                'reason' => trim($reason),
                'create_time' => time()
            ];

//            var_dump($data);
//            return;

            $response = RecallAPI::addRecall($data);
            if ($response != false)
            {
                echo json("001", $response);
            }
        }

        /*
         * 更新召回信息
         */
        public function update()
        {
            //$companyid = $_SESSION["user_info"]["companyid"];//获取企业id
            $id = I('id', 0, 'htmlspecialchars');
            $data = [

                'reason' => trim(I('reason', '', 'htmlspecialchars')),
                'update_time' => time()
            ];

            $response = RecallAPI::updateRecall($id,$data);
            if ($response !== false)
            {
                json("001");
            }
        }

        public function index()
        {
            $data = RecallAPI::listRecall();
            if ($data)
            {
                json("001", $data);
            }


        }

        public function find()
        {
            $id = I('request.id');

            $data = RecallAPI::findRecall($id);

            if ($data)
            {
                json("001", $data);
            }
            else
            {
                json("002");

            }
        }

        public function del()
        {
            $id = I('request.id');

            $data = RecallAPI::delRecall($id);
            if ($data)
            {
                json("001", $data);
            }
            elseif ($data == 0)
            {
                json("009", null, "没有删除任何数据");
            }
            else
            {
                json("002");

            }
        }

        /**
         * 获取码段对应的产品
         */
        public function fetchProduct()
        {
            $qrcodestart = I('bStart', 0, 'htmlspecialchars');
            $qrcodeend = I('bEnd', 0, 'htmlspecialchars');

            $rows = RecallAPI::fetchProductCorr($qrcodestart, $qrcodeend);
            if ($rows == false)
            {
                json("002", null, "该召回码段没有对应任何产品");

                return;
            }
            else
            {
                json("001", $rows);
            }
        }
    }
