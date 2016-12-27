<?php
namespace Model;

class ImageWithInfoModel extends ImageModel {
	public $width;
	public $height;
	public $mime;
	public $exif;
	public $comments;
}