<?php
header("Content-Type: text/html;charset=utf-8");
require ("../config/conn.php");//引入链接数据库
require_once ("include-power.php");//引入权限判断
    if(empty($_REQUEST['mm']))
    {  
	    echo DELETE_FAIL_MORE;
	    exit;  
    }
    else
    {
        $level=$_REQUEST['level'];
        $mm = $_REQUEST["mm"];
        $id =implode(",",$mm);
        $product_rows=$db->query_lists("select * from product where product_id in (".$id.")");
        $product_images=array();
        foreach($product_rows as $k=>$product_row)
        {
            if(!empty($product_row["product_image"]))
            {
                $product_images[$k]=$product_row["product_image"];
            }
        }
        $sql = "delete from product where product_id in(".$id.")";
        $rows = $db->edit_list($sql);
        // 返回影响行数  如果影响行数>=1,则判断添加成功,否则失败
        if($rows >= 1)
        {
            foreach($product_images as $product_image)
            {
                if(file_exists("../../".$product_image))
                {
                    unlink("../../".$product_image);
                }
            }
	        ///////////
	        $Log_name=$_COOKIE['login'];
	        $Log_event=WORD_BATCH_DELETE.WORD_PRODUCT.IMPLEMENT_SUCCESS;
	        $db->edit_list("insert into log (Log_name,Log_event)values('$Log_name','$Log_event')");
	        ////////////
	        echo "<script>alert('".DELETE_SUCCESS."');location.href=\"select.php?level=$level\";</script>";
        }
        else
        {
            ///////////
            $Log_name=$_COOKIE['login'];
            $Log_event=WORD_BATCH_DELETE.WORD_PRODUCT.IMPLEMENT_FAIL;
            $db->edit_list("insert into log (Log_name,Log_event)values('$Log_name','$Log_event')");
            ////////////
            echo "<script>alert('".DELETE_FAIL."');location.href=\"select.php?level=$level\";</script>";
        }
    }