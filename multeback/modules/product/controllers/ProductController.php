<?php

namespace multeback\modules\product\controllers;

use Yii;
use yii\helpers\Url;
use yii\helpers\Json;
use multebox\models\Product;
use multebox\models\ProductBrand;
use multebox\models\search\Product as ProductSearch;
use multebox\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use multebox\models\ProductAttributes;
use multebox\models\ProductAttributeValues;
use multebox\models\ProductCategory;
use multebox\models\ProductSubCategory;
use multebox\models\ProductSubSubCategory;
use multebox\models\FileModel;
use multebox\models\File;
use multebox\models\SendEmail;
use multebox\models\search\MulteModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\NamedRange;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;

/**
 * ProductController implements the CRUD actions for Product model.
 */

class ProductController extends Controller
{
	public $entity_type='product';

    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all Product models.
     * @return mixed
     */
    public function actionIndex()
    {
		if(!Yii::$app->user->can('Product.Index')){
			throw new \yii\web\ForbiddenHttpException(Yii::t('app', 'You dont have permissions to view this page.'));
		}
        $searchModel = new ProductSearch;
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());

		if(isset($_REQUEST['bulk_download']))
		{
			$spreadsheet_inventory = MulteModel::initiateInventoryCreateTemplate();
			$inventory_row_index = 2;	// As header takes first row
			
			if($_REQUEST['selection'])
			{
				foreach ($_REQUEST['selection'] as $product_id)
				{
					$product = Product::findOne($product_id);

					if($product && $product->active)
					{
						$spreadsheet_inventory = MulteModel::writeInventoryCreateTemplateRecord($spreadsheet_inventory, $product, 0, $inventory_row_index);			
						
						$inventory_row_index++;
					}
				}

				$writer = new Xlsx($spreadsheet_inventory);
				$file_name = 'temp/'.uniqid(Yii::$app->user->identity->id);
				$writer->save($file_name);

				return Yii::$app->response->sendFile($file_name, 'inventory_create_template.xlsx');
			}
			else
			{
				Yii::$app->session->setFlash('error', Yii::t('app', 'No records selected!'));
			}
		}

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    /**
     * Displays a single Product model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
		if(!Yii::$app->user->can('Product.View')){
			throw new \yii\web\ForbiddenHttpException(Yii::t('app', 'You dont have permissions to view this page.'));
		}
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('view', ['model' => $model]);
        }
    }

    /**
     * Creates a new Product model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
		if(!Yii::$app->user->can('Product.Create')){
			throw new \yii\web\ForbiddenHttpException(Yii::t('app', 'You dont have permissions to view this page.'));
		}
        $model = new Product;

        if ($model->load(Yii::$app->request->post()))
		{
			if ($model->digital == 0)
			{
				$model->license_key_code = 0;
			}

			if($model->save()) {
				return $this->redirect(['update', 'id' => $model->id]);
			} else {
				return $this->render('create', [
					'model' => $model,
				]);
			}
		}
		else 
		{
			return $this->render('create', [
				'model' => $model,
			]);
		}
    }

    /**
     * Updates an existing Product model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
		if(!Yii::$app->user->can('Product.Update')){
			throw new \yii\web\ForbiddenHttpException(Yii::t('app', 'You dont have permissions to view this page.'));
		}

        $model = $this->findModel($id);

		if(Yii::$app->user->identity->entity_type == 'vendor' && Yii::$app->user->identity->id != $model->added_by_id)
		{
			throw new \yii\web\ForbiddenHttpException(Yii::t('app', 'You dont have permissions to view this page.'));
		}

		$emailObj = new SendEmail;

		// Add Attachment for Product
		if(!empty($_REQUEST['add_attach']))
		{
			$aid=FileModel::fileInsert($_REQUEST['entity_id'],$this->entity_type, true); // true for image so that small image can be stored
			
			if($aid > 0)
			{
				return $this->redirect(['update', 'id' => $_REQUEST['id']]);
			}
			else
			{
				if($aid == 0) // Invalid extension
				{
					$msg = Yii::t('app', 'File type not allowed to be uploaded!');
				}
				else // File size exceeded maximum limit
				{
					$msg = Yii::t('app', 'File size exceeded maximum allowed size')." (".Yii::$app->params['FILE_SIZE'].")";
				}

				return $this->redirect(['update', 'id' => $_REQUEST['id'], 'err_msg' => $msg]);
			}
		}

		if(!empty($_REQUEST['send_attachment_file']))
		{
			//Send an Email
			SendEmail::sendMultEmail($_REQUEST['uemail'],$_REQUEST['email_body'], $_REQUEST['cc'], $_REQUEST['subject']);

			return $this->redirect(['update', 'id' => $_REQUEST['id']]);
		}

		// Delete  Attachment
		if(!empty($_REQUEST['attachment_del_id']))
		{
			$fileResult = File::find()->where("id = '".$_REQUEST['attachment_del_id']."' and entity_type='product' and entity_id='".$model->id."'")->one();

			//$Attachmodel = File::findOne($_REQUEST['attachment_del_id']);
			if (!is_null($fileResult)) 
			{
				$fileResult->delete();
			}

			return $this->redirect(['update', 'id' => $_REQUEST['id']]);
		}

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            //return $this->redirect(['update', 'id' => $model->id]);

			return $this->redirect(['index']);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Product model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
		if(!Yii::$app->user->can('Product.Delete')){
			throw new \yii\web\ForbiddenHttpException(Yii::t('app', 'You dont have permissions to view this page.'));
		}
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Product model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Product the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Product::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
        }
    }

	public function actionAjaxLoadSubCategory(){
		$category_id=!empty($_REQUEST['category_id'])?$_REQUEST['category_id']:'';
		$sub_category_id=!empty($_REQUEST['sub_category_id'])?$_REQUEST['sub_category_id']:'';
		$subcategories = ProductSubCategory::find()->orderBy('name')->where("parent_id=$category_id and active=1")->asArray()->all();
		 return $this->renderPartial('ajax-load-sub-category', [
                'subcategories' => $subcategories,
				'sub_category_id' => $sub_category_id
            ]);
	}

	public function actionAjaxLoadSubSubCategory(){
		$sub_category_id=!empty($_REQUEST['sub_category_id'])?$_REQUEST['sub_category_id']:'';
		$sub_subcategory_id=!empty($_REQUEST['sub_subcategory_id'])?$_REQUEST['sub_subcategory_id']:'';
		$subsubcategories = ProductSubSubCategory::find()->orderBy('name')->where("parent_id=$sub_category_id and active=1")->asArray()->all();
		 return $this->renderPartial('ajax-load-sub-sub-category', [
                'subsubcategories' => $subsubcategories,
				'sub_category_id' => $sub_category_id,
				'sub_subcategory_id' => $sub_subcategory_id
            ]);
	}

	public function actionActivate($id)
    {
		if(!Yii::$app->user->can('Product.Update')){
			throw new \yii\web\ForbiddenHttpException(Yii::t('app', 'You dont have permissions to view this page.'));
		}
        $result = $this->findModel($id);
		$result->active = 1;
		$result->updated_at = time();
		$result->save();
        return $this->redirect(['index']);
    }

	public function actionDeactivate($id)
    {
		if(!Yii::$app->user->can('Product.Update')){
			throw new \yii\web\ForbiddenHttpException(Yii::t('app', 'You dont have permissions to view this page.'));
		}
        $result = $this->findModel($id);
		$result->active = 0;
		$result->updated_at = time();
		$result->save();
        return $this->redirect(['index']);
    }

	public function actionReduceSize()
	{
		$file = File::find()->where(['entity_type' => 'product'])->all();

		foreach ($file as $rec)
		{
			$img = new \multebox\models\ImageUpload();
			
			$original = Yii::$app->params['web_folder'].'/'.$rec->new_file_name;
			$file_extension = pathinfo($original, PATHINFO_EXTENSION);
			$new = Yii::$app->params['web_folder'].'/'.$rec->id.'_small.'.$file_extension;

			list($width, $height, $type, $attr) = getimagesize(Yii::$app->params['web_folder'].'/'.$rec->new_file_name); 

			$ratio = min(290 / $height, 250 / $width); 
			$newHeight = ceil($height * $ratio); 
			$newWidth = ceil($width * $ratio);

			$img->loadImage($original)->resize($newWidth, $newHeight)->saveImage($new);

			$rec->new_file_name = $rec->id.'_small.'.$file_extension;
			$rec->update();
		}
	}
}
