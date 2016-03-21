<?php

namespace comradepashka\tinymce;

use yii\web\AssetBundle;

class TinyMceAsset extends AssetBundle
{
    public $sourcePath = '@vendor/tinymce/tinymce';

    public function init()
    {
        parent::init();
        $this->js[] = YII_DEBUG ? 'tinymce.js' : 'tinymce.min.js';
    }
}