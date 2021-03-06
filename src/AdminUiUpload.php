<?php

namespace epii\ui\upload;

use epii\admin\ui\EpiiAdminUi;
use epii\server\Tools;
use epii\ui\upload\driver\IUploader;
use epii\ui\upload\driver\LocalFileUploader;
use epii\ui\upload\driver\UploaderResult;

/**
 * Created by PhpStorm.
 * User: mrren
 * Date: 2019/1/8
 * Time: 1:09 PM
 */
class AdminUiUpload
{

    private static $handler_class;

    public static function init(string $upload_url,string $class = LocalFileUploader::class)
    {
        EpiiAdminUi::addPluginData("upload_url", $upload_url);
        self::$handler_class = $class;

    }

    public static function enablePhone($ws)
    {

        EpiiAdminUi::addPluginData("epii_upload_phone", json_encode([
            
            "ws" => $ws,
            "client_url" => Tools::get_web_root() . "/?app=epii_phone_upload@index&",
            "server_name" => "epii_upload_server_"
        ]));
        EpiiAdminUi::addPluginData("epii_upload_phone_enable", "1");
    }
    public static function setUploadHandler(string $class)
    {
        self::$handler_class = $class;
    }


    private static function getUploadHandler(): IUploader
    {
        if (!self::$handler_class) {
            self::$handler_class = new LocalFileUploader();
        } else {
            if (is_string(self::$handler_class)) {
                self::$handler_class = new self::$handler_class();
            }
        }

        if (!(self::$handler_class instanceof IUploader)) {
            echo "Uploader Must instanceof IUploader ";
            exit;
        }
        return self::$handler_class;

    }

    public static function doUpload(array $allowedExts = ["gif", "jpeg", "jpg", "png"], $file_size = 204800, $dir = null, $url_pre = null): string
    {
        // die('{"jsonrpc" : "2.0", "result" : null, "id" : "id"}');
        return json_encode(self::getUploadHandler()->handlePostFiles( $allowedExts , $file_size , $dir , $url_pre )->getResult(), true);


    }


    public static function delFile(array $data): string
    {
 
        $ret = new UploaderResult();
        $out = self::getUploadHandler()->del($data);
        if ($out) {
            $ret->success("","");
        } else {
            $ret->error("");
        }
        return json_encode($ret->getResult(), true);
    }
    public static function do_upload(array $allowedExts = ["gif", "jpeg", "jpg", "png"], $file_size = 204800, $dir = null, $url_pre = null)
    {
        return self::getUploadHandler()->handlePostFiles( $allowedExts , $file_size , $dir , $url_pre )->getResult();
    }
    public static function del_file(array $data): UploaderResult
    {
 
        $ret = new UploaderResult();
        $out = self::getUploadHandler()->del($data);
        if ($out) {
            $ret->success("","");
        } else {
            $ret->error("");
        }
        return $ret;
    }
    

}
