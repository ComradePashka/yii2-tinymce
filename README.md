TinyMCE widget extension with inline mode support for Yii2 framework
====================================================================
There are about 20 extensions at [GitHub](http://github.com) which usually
wrap TinyMCE in Html::activeTextarea() or Html::textarea() so we got
htmlencoded content sent to the end user and moreover - TinyMCE wrapped in
textarea tag which is completely inacceptable with for extremly usefull 
inline mode behavior.
 
This extension wrap TinyMCE inside div tag and made it inline by default.


Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist comradepashka/yii2-tinymce "dev-master"
```

or add

```
"comradepashka/yii2-tinymce": "dev-master"
```

to the require section of your `composer.json` file.


Usage
-----

```
$form->field($model, "body")->widget(TinyMce::className(), $optionsArray)
```