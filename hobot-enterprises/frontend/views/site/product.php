<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
$this->title = $product->name;
$this->params['breadcrumbs'][] = ['label' => 'Товари', 'url' => ['products']];
$this->params['breadcrumbs'][] = $this->title;
$this->registerCssFile('@web/css/product.css');

?>
<div class="row">
    <div class="col-md-5">
        <div class="image-container">
            <?= Html::img(\Yii::$app->urlManagerBackEnd->baseUrl .'/uploads/images/'.$product->img, ['alt' => $product->name,'class'=>'product-img']) ?>
        </div>
    </div>
    <div class="col-md-7">
        <h1 class="product-title"><?= $product->name ?></h1>
        <p class="product-price"><span class="product-price-tag"><?= $product->price ?></span> грн.</p>
        <div class="product-description">
        <b>Опис:</b>
        <p><?= $product->description ?></p>
        </div>
        <?php $form = ActiveForm::begin([  ]); ?>
        <?= $product->isInStock() ? $form->field($model, 'quantity')->label("Кількість") : "" ?>
        <?=  $product->isInStock() ? $form->field($model, 'size')->label("Розмір")
        ->dropDownList($product->getRelatedSizesArray()) : "" ?>
        <div class="form-group">
            <?= Html::submitButton($product->isInStock() ? 'Додати!' : 'Товару нема в наявності', 
            ['class' =>$product->isInStock() ? "btn btn-primary active" : "btn btn-primary disabled"]) ?>
        </div> 
    <?php ActiveForm::end(); ?>
    </div>
</div>
