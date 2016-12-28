<?php
namespace Model;

class ImageWithInfoModel extends ImageModel {
    public $width;
    public $height;
    public $mime;
    public $exif;
    public $imagecolorstotal;

    public function getImgSrcBase64()
    {
        return 'data:'.$this->mime.';base64,'.$this->base64;
    }
}