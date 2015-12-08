<?php
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use frontend\assets\AppAsset;
use frontend\widgets\Alert;
use frontend\components\CartComponent\CartWidget;
use frontend\components\CartComponent\assets\CartAssets;
use yii\helpers\Url;
AppAsset::register($this);
CartAssets::register($this);

$company_name = 'Хобот ентерпрайзес (Клієнстька частина)';
$this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
    <?php $this->beginBody() ?>
    
    <div class="wrap">
        <?php
            NavBar::begin([
                'brandLabel' => $company_name,
                'brandUrl' => Yii::$app->homeUrl,
                'options' => [
                    'class' => 'navbar-inverse navbar-fixed-top',
                ],
            ]);
            $menuItems = [
                ['label' => 'Товари', 'url' => ['/site/products']],
            ];
            if (Yii::$app->user->isGuest) {
                $menuItems[] = ['label' => 'Реєстрація', 'url' => ['/site/signup']];
                $menuItems[] = ['label' => 'Вхід', 'url' => ['/site/login']];
            } else {
                $menuItems[] = [
                    'label' => 'Вийти (' . Yii::$app->user->identity->username . ')',
                    'url' => ['/site/logout'],
                    'linkOptions' => ['data-method' => 'post']
                ];
            }
            // add CartWidget to menu
            $menuItems[] = ['label'=>CartWidget::widget([
                    'view'=>$this,
                    'div'=>'cart-container',
                    'popup'=>'cart-popup',            
                    'counted_col'=> ".Кількість",
                    'counter_id'=> 'cart-items-counter',
                    'actionUrl'=>'http://melnichuk-yii-advanced.hol.es/advanced/frontend/web/site/show'
                ]), 'url'=>['/site/order'],
                'options'=>['id'=>'cart-dropdown-li']];
            $menuItems[] = ['label' => 'Адміністративна частина','url' =>Url::toRoute('../../backend/web')];
            echo Nav::widget([
                'encodeLabels'=>false,
                'options' => ['class' => 'navbar-nav navbar-right',],
                'items' => $menuItems,
            ]);
            
            NavBar::end();        
        ?>
        <!-- container for ajax data refreshing of cart dropdown -->
        <div class="container">
        <?= Breadcrumbs::widget([
            'homeLink' => ['label' => 'Головна', 'url'=>Yii::$app->getHomeUrl()],
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?= Alert::widget() ?>
            
            
       <?= $content ?>  

        </div>
    </div>

    <footer class="footer">
        <div class="container">
        <p class="pull-left"><?= $company_name ?> &copy; <?= date('Y') ?></p>
        <p class="pull-right"><?= Yii::powered() ?></p>
        </div>
    </footer>
    
        <div id="cart-popup" class="container">              
        </div>
    
<?php $this->endBody() ?>
 

</body>
</html>
<?php $this->endPage() ?>
