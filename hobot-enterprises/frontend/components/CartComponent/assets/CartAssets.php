<?php
namespace frontend\components\CartComponent\assets;

use yii\web\AssetBundle;

class CartAssets extends AssetBundle
{
    public $basePath = '@webroot/../../frontend/components/CartComponent/';
    public $baseUrl = '@web/../../frontend/components/CartComponent/';

    public $css = [
        'assets/css/cart.css',
    ];
    public $js = [
        'assets/js/cart.js',
        
    ];
    public $jsOptions = ['position' => \yii\web\View::POS_HEAD];
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}