<?php
namespace Admin\Controller;
use Think\Controller;
use Admin\Logic\UpgradeLogic;
class ServerBaseController extends Controller {

    /**
     * 析构函数
     */
    function __construct() 
    {   
   }    
    
    /*
     * 初始化操作
     */
    public function _initialize() 
    {
    }
    

    //去除文件的BOM
    public function dejson($result,$true=''){
        $result = trim($result, "\xEF\xBB\xBF");
        if($true){
            return json_decode($result,ture);
        }else{
            return json_decode($result); 
        }
       
    }
}