<?php
namespace Admin\Controller;

use Think\Controller;
use Admin\Org\QServer;
use Admin\Controller\ServerBaseController;
use Think\Log;

class ServiceController extends ServerBaseController
{
	public function _empty() {
		$error=array(
				'status'    =>0,
				'errorCode' =>'20000',
				'msg'   =>'数据错误！',
			);
        $this->ajaxreturn($error);
     }

	public function Merchant(){
		$QServer =new QServer();
		$error=array(
				'status'    =>'error',
				'errorCode' =>'10001',
				'data'=>array(
					),
				'msg'   =>'数据错误！',
			);
		//验证数据
	
		$result= $this->dejson($_POST['mdata'],1);
		$req=$result['req'];

		Log::write("logServer".$_POST['mdata'],'logServer',C('LOG_TYPE'),C('LOG_PATH').'logServer/'.date('y_m_d').'logServer_log');

		if(!$result['sysid']||!$result['sign']||!$result['timestamp']||empty($req)){
				$error['errorCode'] ='200001';
				$error['msg']   ='参数为空';
				$error['data']      =$result;
				$error['sign']      =$QServer->sign(json_encode($error['data']));
				$this->ajaxreturn($error);
		}

		if(!$req['service']||!$req['mothod']||!$req['param']){
				$error['errorCode'] ='200002';
				$error['msg']   ='参数为空';
				$error['sign']=$QServer->sign(json_encode($error['data']));
				$this->ajaxreturn($error);
		}

		//验证签名
		$params_str = $result['sysid'].json_encode($req,JSON_UNESCAPED_UNICODE).$result['timestamp'];
		$newsign=$QServer->sign($params_str);
		if($newsign != $result['sign']){
			$error['errorCode'] ='20008';
			$error['msg']   ='签名错误';
			$error['sign']=$QServer->sign(json_encode($error['data']));
			$this->ajaxreturn($error);
		}

		//判断服务名是否合法
		$act=array('JSProduct','PAccountSvr','PCheckSvr','PConfirmSvr');
		if(!in_array($req['service'], $act)){
			$error['msg']   ='服务应用不存在';
			$error['errorCode'] ='20000';
			$error['sign']=$QServer->sign(json_encode($error['data']));
			$this->ajaxreturn($error);
		}
		$service=A('Admin/'.$req['service']);
		$NewRsult=$service->$req['mothod']($result);
		$NewRsult['sign']=$QServer->sign(json_encode($NewRsult['data']));

		$this->ajaxReturn($NewRsult);
	}



}




?>