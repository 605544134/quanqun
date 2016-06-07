<?php
namespace Admin\Controller;

use Think\Controller;
use Admin\Controller\ServerBaseController;
use Home\Org\Iclod;
use Admin\Logic\GoodsLogic;
class JSProductController extends ServerBaseController
{

    public function AddEditProduct($result){
    	//定义返回数组
    	$Rarr=array(
    			'status'=>'ok',
    			'signedValue'=>array(
    					'returnFlag'=>'1',
    				),
    		);
    	$message='';
    	//获取参数
		$req        =$result['req'];
		$param      =$req['param'];
		$proid      =(int)$req['proid'];
		$goods_type =$req['goods_type'];
		$mallid     =$req['mallid'];
		$item_img   =$param['item_img'];
		$item       =$param['item'];
		$goods      =$param['goods'];

		$GoodsLogic = new GoodsLogic();                         
	    $Goods = D('Goods'); //
	    $type = $proid > 0 ? 2 : 1; // 标识自动验证时的 场景 1 表示插入 2 表示更新  

		//如果是添加查看所需参数是否存在
	    C('TOKEN_ON',false);
		if(!$Goods->create($goods,$type)){// 根据表单提交的POST数据创建数据对象             
	        //  编辑
	        $return_arr = array(
	            'status' => 0,
	            'msg'   => '操作失败',
	            'data'  => $Goods->getError(),
	        );
	    }else {
	        //  form表单提交
	       // C('TOKEN_ON',true);                                                            
	        $Goods->on_time = time(); // 上架时间
	        //$Goods->cat_id = $_POST['cat_id_1'];
	        $goods['cat_id_2'] && ($Goods->cat_id = $goods['cat_id_2']);
	        $goods['cat_id_3'] && ($Goods->cat_id = $goods['cat_id_3']);
	        $actionstatus='';
	        $status=0;
	        //判断修改还是添加
	        if ($type == 2)
	        {
	            $goods_id = $proid;                                    
	            $goodsSaveR=$Goods->where('goods_id = '.$goods_id.' AND mallid = \''.$mallid."'")->save(); // 写入数据到数据库     
	            //修改成功才执行属性的操作
	            if($goodsSaveR||$goodsSaveR=='0'){
	            	$Goods->afterJsonSave($goods_id,$goods['goods_images'],$$goods['original_img'],$item,$item_img);
	            	$actionstatus=1;
	            }               
	          
	        }
	        else
	        {            
	        	$Goods->mallid=$mallid;                 
	            $goods_id = $insert_id = $Goods->add(); // 写入数据到数据库
	            //插入成功才执行属性的操作
	            if($goods_id){
	            	$Goods->afterJsonSave($goods_id,$goods['goods_images'],$$goods['original_img'],$item,$item_img);
	            	$actionstatus=1;
	            }
	        }                                        
	        
	        $goodsAttrR=$GoodsLogic->saveGoodsAttr($goods_id, $goods_type); // 处理商品 属性
	        
	        if($actionstatus){
				$msg    ='操作成功！';
				$status =1;
	        }else{
	        	$msg='M:操作失败';
	        }

	        $return_arr = array(
				'status'  => $status,
				'msg'     => $msg, 
				'data'=>array(
						'goodsid' =>$goods_id, 
					),                    
	        );
	    } 
	    //$Rarr['signedValue']=$return_arr;
	    return $return_arr;

    }

    public function DelProduct($result){
    	//获取数据
		$req    =$result['req'];
		$param  =$req['param'];
		$mallid =$req['mallid'];
		$proid  =(int)$req['proid'];

		$return_arr = array(
				'status'  => 0,
				'msg'     => '参数为空！', 
				'data'=>array(
						'goodsid' =>$goods_id, 
					),                    
	        );
		if (!$proid||!mallid) {
			return  $return_arr;
		}else{
			// 判断此商品是否有订单
	        $goods_count = M('OrderGoods')->where("goods_id = $proid")->count('1');        
	        if($goods_count)
	        {
	            $return_arr = array('status' => 0,'msg' => '此商品有订单,不得删除!','data'  =>'',);   //$return_arr = array('status' => -1,'msg' => '删除失败','data'  =>'',);                 
	        }else{
	        	$CheckGoods=M("Goods")->where('goods_id ='.$proid.' AND mallid='.$mallid)->find(); 
	        	if($CheckGoods){
	        			// 删除此商品        
			        $del=M("Goods")->where('goods_id ='.$proid.' AND mallid='.$mallid)->delete(); 
			        $return_arr = array('status' => 1,'msg' => '操作成功','data'  =>'',);   //$return_arr = array('status' => -1,'msg' => '删除失败','data'  =>'',);        
		        }else{
		        	$return_arr = array('status' => 0,'msg' => '数据不存在！','data'  =>'',);   //$return_arr = array('status' => -1,'msg' => '删除失败','data'  =>'',);  
		        }
	        
	        }

	        return $return_arr;
		}

		
    }

}




?>