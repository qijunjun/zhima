<?php
/**
 * Created by PhpStorm.
 * User: dichun
 * Date: 2016/8/30
 * Time: 15:32
 */

namespace Government\API;


class MessageAPI
{
    /**
     * 政府给企业发送公告2016830
     * @param $title
     * @param $content
     * @param $governmentid
     * @param $governmentname
     * @param $ids
     * @return array|bool
     */
    public static function sendAnnouncement($title,$content,$governmentid,$governmentname,$ids){
        $data = [
            'governmentid' => $governmentid,
            'governmentname' =>$governmentname,
            'title' => $title,
            'content' => $content,
            'create_time' => time(),
//            'status' => 0,
//            'comment_status' => 0,
//            'classid' => 1
        ];
        $posts = M("BasePosts");
        if(!$posts->create($data)){
            json("009",null,$posts->getError());
            return false;
        }
        $id = $posts->add();
        if($id > 0){
            for($i=0;$i<count($ids);$i++) {
                $model = M("BaseCompany");
                $fields = 'id as companyid,name as companyname';
                $map = [
                    'id' => $ids[$i]
                ];
                $re = $model->where($map)->field($fields)->find();
                $comInfo[] = $re;
            }
            for($k=0;$k<count($comInfo);$k++){
                $comment = M("BaseReceipt");
                $dataInfo = [
                    'postid' => $id,
                    'companyid' => $comInfo[$k]['companyid'],
                    'companyname' => $comInfo[$k]['companyname'],
                    'create_time' => time(),
//                    'hits' => 0
                ];
                if(!$comment->create($dataInfo)){
                    json("009",null,$comment->getError());
                    return false;
                }
                $res = $comment->add();
                if($res !== false){
                    $result[] = $res;
                }
                if($res == false){
                    return array(
                        'companyid' => $comInfo[$i]['companyid'],
                        'companyname' => $comInfo[$i]['companyname']
                    );
                }
            }
            return $result;
        }
    }

    /**
     * 政府已发的过往公告记录列表2016831
     * @param $governmentid
     * @return bool
     */
    public static function listInfo($governmentid){
        $model = M("BasePosts");
        $map = [
            'governmentid' => $governmentid
        ];
        $fields = 'id,title,create_time';
        $res = $model->field($fields)->where($map)->select();

        if($res){
            return $res;
        }else{
            return false;
        }
    }

    /**
     * 政府已发的过往公告的详细信息2016831
     * @param $governmentid
     * @param $id
     * @return bool
     */
    public static function checkGovInfo($id,$governmentid){
        $model = M("BasePosts");
        $map = [
            'id' => $id,
            'governmentid' => $governmentid
        ];
        $res = $model->where($map)->find();

        $re['id'] = $res['id'];
        $str = $res['content'];
        $re['content'] = html_entity_decode($str);
        $re['title'] = $res['title'];
        $re['create_time'] = $res['create_time'];
        $re['status'] = $res['status'];
        $re['comment_status'] = $res['comment_status'];
        $re['governmentid'] = $res['governmentid'];
        $re['governmentname'] = $res['governmentname'];
        $re['classid'] = $res['classid'];

        if($re){
            return $re;
        }else{
            return false;
        }
    }

    /**
     * 政府删除已发的过往公告记录2016831
     * @param $id
     * @return mixed
     */
    public static function delInfo($id){
        $model = M("BasePosts");
        $map = [
            'id' => $id
        ];
        $re = $model->where($map)->delete();
        return $re;
    }

    /**
     * 政府编辑已发的公告记录2016831
     * @param $id
     * @return bool
     */
    public static function editInfo($id){
        $model = M("BasePosts");
        $map = [
            'id' => $id
        ];
        $res = $model->where($map)->find();
        $re['id'] = $res['id'];
        $str = $res['content'];
        $re['content'] = html_entity_decode($str);
        $re['title'] = $res['title'];
        $re['create_time'] = $res['create_time'];
        $re['status'] = $res['status'];
        $re['comment_status'] = $res['comment_status'];
        $re['governmentid'] = $res['governmentid'];
        $re['governmentname'] = $res['governmentname'];
        $re['classid'] = $res['classid'];
        if($re){
            return $re;
        }else{
            return false;
        }
    }

    /**
     * 修改政府已发公告记录2016831
     * @param $data
     * @param $id
     * @return bool
     */
    public static function updateInfo($data,$id){
        $model = M("BasePosts");
        $map = [
            'id' => $id
        ];
        if(!$model->where($map)->create($data)){
            json("009",null,$model->getError());
            return false;
        }else{
            $re = $model->save();
            return $re;
        }
    }

    /**
     * 企业公告信息显示列表2016831
     * @param $companyid
     * @return mixed
     */
    public static function listComInfo($companyid){
        $model = M("BaseReceipt");
        $map = [
            'companyid' => $companyid
        ];
        $fields = 'zm_base_posts.title,zm_base_receipt.id as receiptid,zm_base_receipt.create_time,zm_base_receipt.postid,zm_base_receipt.hits';
        $res = $model->where($map)->field($fields)->join('zm_base_posts ON zm_base_posts.id = zm_base_receipt.postid')->order('create_time desc','hits')->select();
        return $res;
    }

    /**
     * 企业查看公告信息2016831
     * @param $id
     * @param $companyid
     * @return mixed
     */
    public static function checkComInfo($id,$companyid){
        $model = M("BaseReceipt");
        $map = [
            'postid' => $id,
            'companyid' => $companyid
        ];
        $fields = 'zm_base_posts.title,zm_base_posts.content,zm_base_posts.governmentname,zm_base_receipt.id as receiptid,zm_base_receipt.create_time';
        $res = $model->where($map)->field($fields)->join('zm_base_posts ON zm_base_posts.id = zm_base_receipt.postid')->find();
        if($res){
            $hit = $model->where($map)->setInc('hits');
            $re['hits'] = $hit;
        }
        $re['title'] = $res['title'];
        $re['content'] = html_entity_decode($res['content']);
        $re['receiptid'] = $res['receiptid'];
        $re['governmentname'] = $res['governmentname'];
        $re['create_time'] = $res['create_time'];
        return $re;
    }

    /**
     * 企业删除公告信息2016831
     * @param $id
     * @return mixed
     */
    public static function delComInfo($id){
        $model = M("BaseReceipt");
        $map = [
            'id' => $id
        ];
        $re = $model->where($map)->delete();
        return $re;
    }

    /**
     * 企业留言2016831
     * @param $companyid
     * @param $companyname
     * @param $governmentid
     * @param $content
     * @return mixed
     */
    public static function leaveMessage($companyid,$companyname,$governmentid,$content){
        $model = M("BaseComments");
        $data = [
            'governmentid' => $governmentid,
            'companyid' => $companyid,
            'companyname' => $companyname,
            'create_time' => time(),
            'content' => $content
        ];
        if(!$model->create($data)){
            json("009",null,$model->getError());
        }
        $re = $model->add();
        return $re;
    }

    /**
     * 政府查看企业的留言2016831
     * @param $governmentid
     * @return mixed
     */
    public static function listLeaveMessage($governmentid){
        $model = M("BaseComments");
        $map = [
            'governmentid' => $governmentid
        ];
        $res = $model->where($map)->order('create_time desc','hits')->select();
        foreach($res as $key => $value){
            $re['id'] = $value['id'];
            $re['governmentid'] = $value['governmentid'];
            $re['companyid'] = $value['companyid'];
            $re['companyname'] = $value['companyname'];
            $re['create_time'] = $value['create_time'];
            $re['content'] = html_entity_decode($value['content']);
            $re['hits'] = $value['hits'];
            $result[] = $re;
        }
        return $result;
    }

    /**
     * 政府查看留言详情201693
     * @param $id
     * @return mixed
     */
    public static function checkLeaveMessage($id){
        $model = M("BaseComments");
        $map = [
            'id' => $id
        ];
        $res = $model->where($map)->find();
        $result['id'] = $res['id'];
        $result['governmentid'] = $res['governmentid'];
        $result['companyid'] = $res['companyid'];
        $result['companyname'] = $res['companyname'];
        $result['create_time'] = $res['create_time'];
        $result['content'] = html_entity_decode($res['content']);
        if($result){
            $hit = $model->where($map)->setInc('hits');
            $result['hits'] = $hit;
        }
        return $result;
    }

}
