<?php
namespace Helper;

class ImageBase64Helper
{
    public static function downloadImageAsBase64($url)
    {
        $content = file_get_contents($url);
        return base64_encode($content);
    }

    public static function createImageResourceFromBase64($base64data)
    {
        return imagecreatefromstring(base64_decode($base64data));
    }

    public static function encodeImageResourceToPngBase64($im)
    {
        ob_start();
        imagepng($im);
        $contents = ob_get_contents();
        ob_end_clean();
        return base64_encode($contents);
    }
    
    public static function encodeImageResourceToGifBase64($im)
    {
        ob_start();
        imagegif($im);
        $contents = ob_get_contents();
        ob_end_clean();
        return base64_encode($contents);
    }
}