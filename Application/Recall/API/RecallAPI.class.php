<?php
    /**
     * Created by PhpStorm.
     * User: yumin
     * Date: 2016/7/25
     * Time: 12:05
     */
    namespace Recall\API;
    class RecallAPI
    {
        public static function addRecall($data)
        {
            $model = M('base_recall');
            $rules = self::checkQrcode();
            if (!$model->validate($rules)->create($data))
            {
                json("009", null, $model->getError());

                return false;
            }
            $id = $model->add();

            //$id=1;
            return $id;
        }

        public static function updateRecall($id,$data)
        {
            $companyid = $_SESSION["user_info"]["companyid"];
            $model = M('base_recall');
            $map['id'] = $id;
            $map['company_id'] = $companyid;
            $rules = array(
                //array('id', 'require', 'id不能为空'),
                array('reason', 'require', '召回原因不能为空'),
            );
            if (!$model->validate($rules)->where($map)->create($data))
            {
                json("009", null, $model->getError());

                return false;
            }
            else
            {

                $result = $model->save();

                return $result;
            }
        }

        public static function findRecall($id)
        {
            $model = M('base_recall');
            $companyid = $_SESSION["user_info"]["companyid"];
            $map['id'] = $id;
            $map['company_id'] = $companyid;

            $model->where($map)->find();

            $data = $model->data();
            if ($data)
            {
                return $data;
            }
            else
            {
                return false;
            }
        }

        public static function listRecall()
        {

            $model = M('base_recall');
            $companyid = $_SESSION["user_info"]["companyid"];
            $map['company_id'] = $companyid;
            $data = $model->field('zm_base_recall.`id`,productname,guige,qrcode_range_s,qrcode_range_e,zm_base_recall.create_time,reason')
                ->join('LEFT JOIN __BASE_PRODUCT__ ON __BASE_RECALL__.product_id = __BASE_PRODUCT__.productid')
                ->where($map)
                ->select();
            //echo $model->_sql();

            if ($data)
            {
                return $data;
            }
            else
            {
                return false;
            }
        }

        public static function delRecall($id)
        {
            $model = M('base_recall');
            $companyid = $_SESSION["user_info"]["companyid"];
            $map['id'] = $id;
            $map['company_id'] = $companyid;

            return $model->where($map)->delete();
        }

        public static function checkQrcodeRepeat($start, $end = 0)
        {
            if (empty($end))
            {
                $end = $start;
            }
            $model = M('base_recall');
            $companyid = $_SESSION["user_info"]["companyid"];
            $sql = 'SELECT `id` FROM `zm_base_recall` WHERE ((`qrcode_range_s` <= ' . $start . ' AND `qrcode_range_e` >= ' . $start . ') OR (`qrcode_range_s` <= ' . $end . ' AND `qrcode_range_e` >= ' . $end . ') OR (`qrcode_range_s` >= ' . $start . ' AND `qrcode_range_s` <= ' . $end . ') OR (`qrcode_range_e` >= ' . $start . ' AND `qrcode_range_e` <= ' . $end . ')) AND `company_id` = ' . $companyid;
            $rows = $model->query($sql);

//            $rows->_sql();

            return $rows;
        }

        static function checkQrcode()
        {
            $companyid = $_SESSION["user_info"]["companyid"];
            $rules = array(
                array('company_id', 'require', '企业id不能为空'),
                array('qrcode_range_s', '/^' . $companyid . '[1]\d{9}$/', "起始质量码内容不符合规范", 0),
                array('qrcode_range_e', '/^' . $companyid . '[1]\d{9}$/', "结束质量码内容不符合规范", 0),
                array('reason', 'require', '召回原因不能为空'),
            );

            return $rules;
        }

        /**
         * 通过码段获取到关联的产品内容
         *
         * @param $start
         * @param $end
         *
         * @return int
         */
        public static function fetchProductCorr($start, $end = 0)
        {
            if (empty($end))
            {
                $end = $start;
            }
            $model = M('base_recall');
            $companyid = $_SESSION["user_info"]["companyid"];
            $sql = 'SELECT `product_id` FROM `zm_corr_qrcode_product` WHERE ((`qrcode_range_s` <= ' . $start . ' AND `qrcode_range_e` >= ' . $start . ') OR (`qrcode_range_s` <= ' . $end . ' AND `qrcode_range_e` >= ' . $end . ') OR (`qrcode_range_s` >= ' . $start . ' AND `qrcode_range_s` <= ' . $end . ') OR (`qrcode_range_e` >= ' . $start . ' AND `qrcode_range_e` <= ' . $end . ')) AND `company_id` = ' . $companyid;

            return $model->query($sql);
        }
    }