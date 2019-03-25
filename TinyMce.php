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

    public $extraCss = "";

    /**
     * @var array the options for the TinyMCE JS plugin.
     * Please refer to the TinyMCE JS plugin Web page for possible options.
     * @see http://www.tinymce.com/wiki.php/Configuration
     * Options for container could be set through options attribute inherited
     * from InputWidget
     *
     * what 2 do?
     * 'extended_valid_elements' => ["img[class|src|alt|title|hspace|vspace|width|height|align|onmouseover|onmouseout|name|usemap]"],
     */
    public $clientOptions = [];

    private static $defaultSettings = [
        'language' => 'en',
        'inline' => true,
        'plugins' => [
            "advlist autolink link image imagetools lists charmap print preview hr anchor pagebreak spellchecker",
            "searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
            "save table contextmenu directionality emoticons template paste textcolor "
        ],
        'toolbar' => [
            "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify |" .
            "bullist numlist outdent indent | link image imagetools | print preview media fullpage | forecolor backcolor emoticons"
        ],
        'min-width' => 1200,
        'toolbar_items_size' => "small",
        'relative_urls' => true,
        'convert_urls' => false,
        'image_advtab' => false,
        'image_caption' => true,
        'image_title' => true,
        'image_description' => true,
        'image_dimensions' => true,
        'statusbar' => true,
        'end_container_on_empty_block' => true,
        'extended_valid_elements' => [],
    ];

    /**
     * @inheritdoc
     */
    public function run()
    {
        $this->clientOptions = array_merge_recursive(self::$defaultSettings, $this->clientOptions);
        if (is_array($this->clientOptions['extended_valid_elements'])) {
            if (count($this->clientOptions['extended_valid_elements']) > 0) {
              $this->clientOptions['extended_valid_elements'] = implode(',', $this->clientOptions['extended_valid_elements']);
            } else {
              unset($this->clientOptions['extended_valid_elements']);
            }
        } elseif (!strlen($this->clientOptions['extended_valid_elements'])) {
            unset($this->clientOptions['extended_valid_elements']);
        }

        if ($this->hasModel()) {
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
            $options['name'] = $this->name;
            $value = $this->value;
        }
        if (!isset($this->options['class'])) $this->options['class'] = 'tinymce';
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
        if ($this->language == null) {
            $this->language = yii::$app->language;
            if ($this->language == "en") $this->language = "en_GB";
        }
        $this->clientOptions['language'] = $this->language;
        $this->clientOptions['document_base_url'] = yii::$app->urlManager->hostInfo . '/';

        $this->clientOptions['content_css'] =
            yii::$app->assetManager->getPublishedUrl(yii::$app->assetManager->getBundle('yii\bootstrap\BootstrapAsset')->sourcePath) . "/css/bootstrap.css," .
            $this->extraCss;
        $this->clientOptions['selector'] = "#$id";

        $langFile = "langs/{$this->language}.js";
        $langAssetBundle = TinyMceLangAsset::register($view);
        $langAssetBundle->js[] = $langFile;
        $this->clientOptions['language_url'] = $langAssetBundle->baseUrl . "/{$langFile}";
        $options = Json::encode($this->clientOptions);

        $js[] = "tinymce.init($options);";
        $js[] = "$('#{$id}').parents('form').on('beforeValidate beforeSubmit submit', function() {
            tinymce.triggerSave();
            $('[name={$id}]').attr('name',$('#{$id}').attr('name'));
            return true;
        });";
        $view->registerJs(implode("\n", $js));
    }
}
