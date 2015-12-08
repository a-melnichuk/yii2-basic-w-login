<?php
use yii\helpers\Html;
use yii\widgets\LinkPager;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
$this->title = 'Товари';
$thumbnails_url = Yii::$app->urlManagerBackEnd->createUrl('uploads/thumbnails');
if($searched_val == -1){
     $this->params['breadcrumbs'][] = $this->title;
} else {
   $this->params['breadcrumbs'][]= ['label' => $this->title, 'url' => ['site/products']];
   $this->params['breadcrumbs'][]= $searched_val;
}
$this->registerCssFile('@web/css/products.css');
?>
<div class="row">
    <div class="col-md-4"><?= Html::a('Звичайні', ['products', 'category' => 'default'], ['class' => 'btn btn-primary btn-block']) ?></div>
    <div class="col-md-4"><?= Html::a('Рожеві', ['products', 'category' => 'pink'], ['class' => 'btn btn-primary btn-block']) ?></div>
    <div class="col-md-4"><?= Html::a('Різнокольорові', ['products', 'category' => 'multy-coloured'], ['class' => 'btn btn-primary btn-block']) ?></div>
</div>

<div class="form-category">
    <?php
    /* $form_category = ActiveForm::begin(['id' => 'product-categories', 
                                    'method'=> 'get', 
                                    'action' => ['site/products'],
                                    ]); ?>
    <?= Html::submitButton('Default', ['class' => 'btn btn-primary','name'=>'category','value'=>'default']) ?>
    <?= Html::submitButton('Pink', ['class' => 'btn btn-primary','name'=>'category','value'=>'pink']) ?>
    <?= Html::submitButton('Multy-coloured', ['class' => 'btn btn-primary','name'=>'category','value'=>'multy-coloured']) ?>
    <?php /*ActiveForm::end(); */?>
</div>

<div class="row" style="padding-top: 20px">
    <?php $form_search = ActiveForm::begin(['id' => 'product-search', 
                                    'method'=> 'get',
                                    'action' => Url::to(['site/products']),
                                    ]); ?>
    <div class="col-md-2" style="text-align:right; font-size: 1.5em;"><b>Шукати товар:</b></div>
    <div class="col-md-9"><?= $form_search->field($model, 'search_val')->label(false)->textInput(['name' => 'search'])?></div>
    <div class="col-md-1"><?= Html::submitButton('Шукати', ['class' => 'btn btn-primary']) ?></div>
    <?php ActiveForm::end(); ?>
</div>

<div class="products-container">  
    <?php if(count($products)==0):?>
    <h1>Вибачте, жодного товару не знайдено...</h1>
    <?php else: ?>
    
    <ul>
    <?php foreach ($products as $product): ?> 
    <li class ="product-item">
    <div class="img-container">
        
            <?=Html::a(Html::img($thumbnails_url .'/'. $product->img , ['alt' => $product->name,'class'=>'product-img']), ['product','id'=> $product->id]) ?>
        </a
    </div>
    <p class="product-name"><?= $product->name ?></p>
    <p class="price">
        <span class="price-tag"><?= $product->price ?> грн.</span></p>
    <div class="products-btn">
    <?= $product->quantity > 0 ? Html::a(Детальніше , ['product','id'=> $product->id], ['class' => 'btn btn-primary btn-block']) : "<button class='btn btn-primary btn-block disabled'>Нема в наявності</button>" ?>
    </div>
    </li>
    <?php endforeach; ?>
    </ul>
    <?php endif; ?>
   <?= LinkPager::widget(['pagination' => $pagination]) ?>
</div>
