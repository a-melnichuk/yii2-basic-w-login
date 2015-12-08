<?phpnamespace frontend\models;

use yii\base\Model;
use common\models\Product;

class ProductSearch extends Model {
    public $search_val;
    
     public function rules()
    {
        return [
            [['search_val'] , 'string'],
        ];
    }

    public function searchProduct($name){
        return Product::find()
        ->where('name LIKE :query')
        ->addParams([':query'=>'%'. htmlspecialchars($name).'%']);
    }
    
    public function searchCategory($category){
       return Product::find()->where(['category' =>  htmlspecialchars($category)]);
    }
}