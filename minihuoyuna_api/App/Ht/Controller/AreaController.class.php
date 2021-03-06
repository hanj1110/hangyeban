<?php
namespace Ht\Controller;
use Think\Controller;
class AreaController extends PublicController{

	/*
	*
	* 获取、查询栏目表数据
	*/
	public function index(){
		// 获取所有一级分类，进行关系划分
		$list = M('area')->where('tid=0')->field('id,name,sort')->order('sort desc,id asc')->select();
		foreach ($list as $k => $v) {
			$list[$k]['list2'] = M('area')->where('tid='.intval($v['id']))->field('id,name')->order('sort desc,id asc')->select();
		}
		$this->assign('list',$list);// 赋值数据集

		$this->display(); // 输出模板
	}


	/*
	*
	* 跳转添加或修改栏目页面
	*/
	public function add(){
		//如果是修改，则查询对应分类信息
		if (intval($_GET['cid'])) {
			$cate_id = intval($_GET['cid']);
		
			$cate_info = M('area')->where('id='.intval($cate_id))->find();
			if (!$cate_info) {
				$this->error('没有找到相关信息.');
				exit();
			}
			$this->assign('cate_info',$cate_info);
		}
		//获取所有一级分类
		$list = M('area')->where('tid=0')->field('id,name')->order('sort desc,id asc')->select();
		$this->assign('list',$list);// 赋值数据集
		$this->display();
	}


	/*
	*
	* 添加或修改栏目信息
	*/
	public function save(){
		$tid = intval($_POST['tid']);
		//判断是否已经存在该栏目
		if (!intval($_POST['cid'])) {
			$check_id = M('area')->where('name="'.trim($_POST['name']).'" AND tid='.intval($tid))->getField('id');
			if ($check_id) {
				$this->error('该分类已存在.');
				exit();
			}
		}

		//构建数组
		M('area')->create();
		//上传产品分类缩略图
		// if (!empty($_FILES["file2"]["tmp_name"])) {
		// 	//文件上传
		// 	$info2 = $this->upload_images($_FILES["file2"],array('jpg','png','jpeg'),"category/".date(Ymd));
		//     if(!is_array($info2)) {// 上传错误提示错误信息
		//         $this->error($info2);
		//         exit();
		//     }else{// 上传成功 获取上传文件信息
		// 	    $this->pro_cat->img = 'UploadFiles/'.$info2['savepath'].$info2['savename'];
		//     }
		// }
		
		//保存数据
		if (intval($_POST['cid'])) {
			$result = M('area')->where('id='.intval($_POST['cid']))->save();
		}else{
			//保存添加时间
			M('area')->addtime = time();
			$result = M('area')->add();
		}
		//判断数据是否更新成功
		if ($result) {
			$this->success('操作成功.','index');
		}else{
			$this->error('操作失败.');
		}
	}


	/*
	*
	* 栏目删除
	*/
	public function del(){
		//以后删除还要加权限登录判断
		$id = intval($_GET['did']);
		$check_info = M('area')->where('id='.intval($id))->find();
		if (!$check_info) {
			$this->error('操作失败.'.__LINE__);
			exit();
		}

		$res = M('area')->where('id='.intval($id))->delete();
		if ($res) {
			$this->redirect('index');
		}else{
			$this->error('操作失败.');
		}
	}

}