<?php
/**
 * Created by PhpStorm.
 * User: AF
 * Date: 2016/11/8
 * Time: 15:37
 */
include './libs/include.list.php';
////循环删除目录和文件函数
function del_dir($dirName){
    if ($handle=opendir($dirName)){
        while (false!==($item=readdir($handle))){
            if ($item!="."&&$item!=".."){
                if (is_dir($dirName.'/'.$item)){
                    del_dir($dirName.'/'.$item);
                } else {
                    //unlink($dirName.'/'.$item);
                    echo $dirName.'/'.$item."\r\n\r\n";
                }
            }
        }
        closedir($handle);
        //rmdir($dirName);//删除文件夹
        echo "删除文件".$dirName."\r\n\r\n";
    }
}

$cameraId=UuidUtils::uuid();
$url=$cameraId;
$publicDir=__DIR__.'/public/qrcode/100';
if(!is_dir($publicDir)){
    mkdir($publicDir,0777,true);
}
$qrCode=new QrCodeUtils();
$codeInfo=$qrCode->getQr($url,$publicDir,$cameraId);
p(strstr($codeInfo,'/qrcode'));
