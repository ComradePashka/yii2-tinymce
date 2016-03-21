<?php

namespace comradepashka\tinymce;

use yii\web\AssetBundle;

class TinyMceLangAsset extends AssetBundle
{
    public $sourcePath = '@vendor/comradepashka/tinymce/assets';

    public $depends = [
        'comradepashka\tinymce\TinyMceAsset'
    ];
}
