<?php
namespace frontend\controllers;

use Yii;
use common\models\LoginForm;
use frontend\models\PasswordResetRequestForm;
use frontend\models\ResetPasswordForm;
use frontend\models\SignupForm;
use frontend\models\ContactForm;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\Product;
use yii\data\Pagination;
use frontend\models\ProductForm;
use yii\base\Model;
use yii\helpers\Url;
/**
 * Site controller
 */
class SiteController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout', 'signup'],
                'rules' => [
                    [
                        'actions' => ['signup'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionLogin()
    {
        if (!\Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->actionIndex();
        } else {
            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    public function actionLogout()
    {
        Yii::$app->user->logout(false);

        return $this->goHome();
    }

    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail(Yii::$app->params['adminEmail'])) {
                Yii::$app->session->setFlash('success', 'Thank you for contacting us. We will respond to you as soon as possible.');
            } else {
                Yii::$app->session->setFlash('error', 'There was an error sending email.');
            }

            return $this->refresh();
        } else {
            return $this->render('contact', [
                'model' => $model,
            ]);
        }
    }

    public function actionAbout()
    {
        return $this->render('about');
    }

    public function actionSignup()
    {
        $model = new SignupForm();
        if ($model->load(Yii::$app->request->post())) {
            if ($user = $model->signup()) {
                if (Yii::$app->getUser()->login($user)) {
                    return $this->goHome();
                }
            }
        }

        return $this->render('signup', [
            'model' => $model,
        ]);
    }

    public function actionRequestPasswordReset()
    {
        $model = new PasswordResetRequestForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->getSession()->setFlash('success', 'Check your email for further instructions.');

                return $this->goHome();
            } else {
                Yii::$app->getSession()->setFlash('error', 'Sorry, we are unable to reset password for email provided.');
            }
        }

        return $this->render('requestPasswordResetToken', [
            'model' => $model,
        ]);
    }

    public function actionResetPassword($token)
    {
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidParamException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->getSession()->setFlash('success', 'New password was saved.');
            return $this->goHome();
        }

        return $this->render('resetPassword', [
            'model' => $model,
        ]);
    }
    
    public function actionProducts(){
        $query = Product::find();
        $model=new \frontend\models\ProductSearch;
        $searched_val = -1;
        //if product is searched, query its name from SQL, 
        if($search_request = Yii::$app->request->get('search') ){
            $query = $model->searchProduct($search_request);
            $searched_val = $search_request;
        } 
        //if category if selected, query products from selected category
        else if($category_request = Yii::$app->request->get('category')){
            $query = $model->searchCategory($category_request);   
            $searched_val = $category_request;
        }
        //display 9 products per page
        $pagination = new Pagination([
        'defaultPageSize' =>9,
        'totalCount' => $query->count(),
        ]);
        //query products by name or category,if any is seleted,or all if no name or category is searched
        $products = $query
        ->offset($pagination->offset)
        ->limit($pagination->limit)      
        ->all();

        return $this->render('products', [
        'model' => $model,
        'products' => $products,
        'pagination' => $pagination,
        'searched_val'=> $searched_val,
        ]);
    }
    
    public function actionProduct($id){
        $product = Product::find()->where(['id'=> $id])->one();
        $model = new ProductForm();
        $model->max = $product['quantity'];
        //if product if added to cart, insert product info to cart session
        if ($model->load(Yii::$app->request->post()) && $model->validate() ) {        
            $column = [];
            $column[] = $product->name;
            $column[] = intval($model->quantity);
            $column[] = $model->size;
            $cart = Yii::$app->CartComponent;
            $cart->addColumn($column); 
            //redirect to products page
            return $this->redirect('../site/products',302);
        }
        //display product data otherwise
        return $this->render('product', [
            'product'=>$product,
            'model'=>$model,
        ]);
    }
    
       
    public function actionOrderSuccess(){
        $items = [];
        $cart = Yii::$app->CartComponent;
        $names = $cart->getNamesColumn();
        //if order is successful, set product info to be added to a table,that summarizes purchases
        foreach($names as $i=> $name){
             $product = Product::find()
                        ->where(['name' => $name])
                        ->one();
             $data = (object)[
                 'name'=>$product->name,
                 'img'=>$product->img,
                 'quantity'=>$cart->getQuantityColumn()[$i],
                 'size'=>$cart->getSizeColumn()[$i]
             ];
             $items[] = $data;
             $product->quantity -= intval($data->quantity);
             $product->save();
        }
        $cart->clearCart();
        return $this->render('order-success',['items'=>$items]);
    }
    
    public function actionOrder(){
        $data = Yii::$app->request->post();
        $data_removed = $data['remove'];
        //if data is removed, remove item row from cart session
        if($data_removed!== null){
            $id = intval($data_removed);
            $cart = Yii::$app->CartComponent;
            foreach ($cart->column_names as $val){
                $cart->removeByName($id,$val);
            }
            $arr = $data['OrderForm'];
            unset($arr[$data_removed]);
            $arr =  array_values($arr);
            $data['OrderForm'] = $arr;
        }
        
        $items = [];
        $cart = Yii::$app->CartComponent;
        $names = $cart->getNamesColumn();
        if(count($names)!==0){
            //query products by name from cart,load their models filled with product data to items array
            foreach($names as $i=> $name){
                $product = Product::find()
                        ->where(['name' => $name])
                        ->one(); 
                $form = new \frontend\models\OrderForm();    
                $items[] = $form->getLoadedModel($product, $cart, $i);
            }

            if(Model::loadMultiple($items, $data)) {
                //update items in cart to new values from loaded model
                foreach($items as $i => $item){
                    $cart->setColumnByIndex(1,$i,$item['quantity']);
                    $cart->setColumnByIndex(2,$i,$item['size']);
                }
                //if item was removed from cart,renew order page with items updated
                if($data_removed !== null){
                    return $this->render('order', [ 'items' => $items]);    
                }
                //if all products are validates successfuly,redirect to page,that summarizes purchases
                if(Model::validateMultiple($items)) { 
                   return $this->redirect('order-success');    
                }
            }
        }
        return $this->render('order', [
             'items' => $items,
        ]);
    }

   //remove product from cart 
    public function actionRemove($id){
        $cart = Yii::$app->CartComponent;
        foreach ($cart->column_names as $val){
            $cart->removeByName($id,$val);
        }   
        return $this->actionShow();  
    }
    //render renewed cart popup via ajax
    public function actionShow(){
           $cart = Yii::$app->CartComponent;
           $len = count($cart->getNamesColumn());
           $table_heads = $cart->column_names;
           
        return $this->renderAjax('_cart-popup',[
            'cart'=>$cart,
            'table_heads'=>$table_heads,
            'len'=>$len
        ]);
    }
    
}
