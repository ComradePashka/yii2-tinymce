<?php

namespace comradepashka\tinymce;

use Yii;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\widgets\InputWidget;

class TinyMce extends InputWidget
{
    /**
     * @var string the language to use. Defaults to null (en).
     */
    public $language;

    /**
     * @var array the options for the TinyMCE JS plugin.
     * Please refer to the TinyMCE JS plugin Web page for possible options.
     * @see http://www.tinymce.com/wiki.php/Configuration
     */
    public $clientOptions = [];
    /**
     * ???
     */
    public $triggerSaveOnBeforeValidateForm = true;

    private static $defaultSettings = [
        'language'                  => 'en',
        'inline'                    => 'true',
        'plugins'                   => [
            "advlist autolink link image imagetools lists charmap print preview hr anchor pagebreak spellchecker",
            "searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
            "save table contextmenu directionality emoticons template paste textcolor"
        ],
        'toolbar'                   => "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify |
         bullist numlist outdent indent | link image imagetools | print preview media fullpage | forecolor backcolor emoticons",
        'toolbar_items_size'        => 'small',
        'image_advtab'              => true,
        'relative_urls'             => false,
        'convert_urls'              => false,
        'remove_script_host'        => true
    ];

    /**
     * @inheritdoc
     */
    public function run()
    {
        if ($this->hasModel()) {
//            echo Html::activeTextarea($this->model, $this->attribute, $this->options);
            $this->options['name'] = isset($this->options['name']) ? $this->options['name'] : Html::getInputName($this->model, $this->attribute);
            if (isset($this->options['value'])) {
                $value = $this->options['value'];
                unset($this->options['value']);
            } else {
                $value = Html::getAttributeValue($this->model, $this->attribute);
            }
            if (!array_key_exists('id', $this->options)) {
                $this->options['id'] = Html::getInputId($this->model, $this->attribute);
            }
        } else {
//            echo Html::textarea($this->name, $this->value, $this->options);
            $options['name'] = $this->name;
            $value = $this->value;
        }
        echo Html::tag('div', $value, $this->options);
        $this->registerClientScript();
    }

    /**
     * Registers tinyMCE js plugin
     */
    protected function registerClientScript()
    {
        $js = [];
        $view = $this->getView();

        TinyMceAsset::register($view);

        $id = $this->options['id'];
        if ($this->language == null) $this->language = yii::$app->language;
        $this->clientOptions['language'] = $this->language;
        $this->clientOptions['document_base_url'] = yii::$app->urlManager->hostInfo . '/';
        /**
         * @2do find out some way to set up additional css via options
         */
        $this->clientOptions['content_css'] = "'/css/site.css," . yii::$app->assetManager->getPublishedUrl(yii::$app->assetManager->getBundle('yii\bootstrap\BootstrapAsset')->sourcePath) . "/css/bootstrap.css'";
        $this->clientOptions['selector'] = "#$id";

        $langFile = "langs/{$this->language}.js";
        $langAssetBundle = TinyMceLangAsset::register($view);
        $langAssetBundle->js[] = $langFile;
        $this->clientOptions['language_url'] = $langAssetBundle->baseUrl . "/{$langFile}";

        $options = Json::encode($this->clientOptions);

        $js[] = "tinymce.init($options);";
        if ($this->triggerSaveOnBeforeValidateForm) {
            $js[] = "$('#{$id}').parents('form').on('beforeValidate', function() { tinymce.triggerSave(); });";
        }
        $view->registerJs(implode("\n", $js));
    }
}
