<?php

namespace multeback\modules\inventory\controllers;

use Yii;
use yii\helpers\Json;
use yii\helpers\Url;
use multebox\models\Tags;
use multebox\models\InventoryTags;
use multebox\models\Inventory;
use multebox\models\Vendor;
use multebox\models\InventoryDetails;
use multebox\models\search\Inventory as InventorySearch;
use multebox\models\search\MulteModel;
use multebox\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use multebox\models\Product;
use multebox\models\ProductAttributes;
use multebox\models\ProductAttributeValues;
use multebox\models\ProductSubCategory;
use multebox\models\ProductSubSubCategory;
use multebox\models\LicenseKeyCode;
use yii\web\UploadedFile;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\NamedRange;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;

/**
 * InventoryController implements the CRUD actions for Inventory model.
 */
class InventoryController extends Controller
{
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
     * Lists all Inventory models.
     * @return mixed
     */
    public function actionIndex()
    {
		if(!Yii::$app->user->can('Inventory.Index')){
			throw new \yii\web\ForbiddenHttpException(Yii::t('app', 'You dont have permissions to view this page.'));
		}
        $searchModel = new InventorySearch;
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());
		
		if(isset($_REQUEST['bulk_download']))
		{
			$spreadsheet_inventory = MulteModel::initiateInventoryUpdateTemplate();
			$inventory_row_index = 2;	// As header takes first row
			
			if($_REQUEST['selection'])
			{
				foreach ($_REQUEST['selection'] as $inventory_id)
				{
					$inventory = Inventory::findOne($inventory_id);

					if($inventory)
					{
						$spreadsheet_inventory = MulteModel::writeInventoryUpdateTemplateRecord($spreadsheet_inventory, $inventory, 0, $inventory_row_index);			
						
						$inventory_row_index++;
					}
				}

				$writer = new Xlsx($spreadsheet_inventory);
				$file_name = 'temp/'.uniqid(Yii::$app->user->identity->id);
				$writer->save($file_name);

				return Yii::$app->response->sendFile($file_name, 'inventory_update_template.xlsx');
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
     * Displays a single Inventory model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
		if(!Yii::$app->user->can('Inventory.View')){
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
     * Creates a new Inventory model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
		if(!Yii::$app->user->can('Inventory.Create')){
			throw new \yii\web\ForbiddenHttpException(Yii::t('app', 'You dont have permissions to view this page.'));
		}
        $model = new Inventory;

		$connection = Yii::$app->db;
		
		
		if($model->load(Yii::$app->request->post()))
		{
			$transaction = $connection->beginTransaction();
			try
			{
				$product = Product::findOne($model->product_id);
				$digital_ind = $product->digital;
				$license_key_code = $product->license_key_code;

				if($digital_ind)
				{
					$model->scenario = 'digital';
					$model->digital_file = UploadedFile::getInstance($model, 'digital_file');
				}

				if(isset($_REQUEST['attribute_ids']) && isset($_REQUEST['Inventory']['attribute_values']) && isset($_REQUEST['Inventory']['attribute_price']))
				{
					$model->attribute_values = Json::encode($_REQUEST['Inventory']['attribute_values']);
					$model->attribute_price = Json::encode($_REQUEST['Inventory']['attribute_price']);

					if($model->attribute_values == 'null')
						$model->attribute_values == NULL;

					if($model->attribute_price == 'null')
						$model->attribute_price == NULL;
				}
				
				$model->product_name = $product->name;
				$model->product_rating = $product->rating;
				$model->vendor_rating = Vendor::findOne($model->vendor_id)->rating;

				if($model->save())
				{
					if($digital_ind)
					{
						if(!$license_key_code)
						{
							$name = uniqid($model->id);
							$model->digital_file_name = $name;
							$model->upload($name);
						}
						else
						{
							// Read input excel and populate code table
							$count = MulteModel::readInputSheet($model->id, $model->digital_file->tempName);
							$model->stock = $count;
						}

						$model->update();					
					}
					
					$i=0;
					if(isset($_REQUEST['attribute_ids']))
					{
						foreach(Json::decode($_REQUEST['attribute_ids']) as $row)
						{
							$inventoryDetails = new InventoryDetails;

							$inventoryDetails->inventory_id = $model->id;
							$inventoryDetails->attribute_id = intval($row);
							$inventoryDetails->attribute_value = $_REQUEST['Inventory']['attribute_values'][$i];
							$inventoryDetails->attribute_price = floatval($_REQUEST['Inventory']['attribute_price'][$i]);
							
							$inventoryDetails->save();
							$i++;
						}
					}

					$tags_array = explode(',', $_REQUEST['inventory_tags']);

					foreach($tags_array as $row)
					{
						$inventorytags = new InventoryTags;
						$inventorytags->inventory_id = $model->id;

						$tag = Tags::find()->where(['tag' => strtolower(trim($row))])->one();

						if($tag)
						{
							$inventorytags->tag_id = $tag->id;
						}
						else
						{
							$newtag = new Tags;
							$newtag->tag = strtolower(trim($row));
							$newtag->save();

							$inventorytags->tag_id = $newtag->id;
						}

						$inventorytags->save();
					}

					//print_r($model->getErrors());
					//var_dump($_REQUEST);
					//exit;
					$transaction->commit();
					return $this->redirect(['update', 'id' => $model->id]);
				}
				else 
				{
					//print_r($model->attribute_values);
					if($model->getErrors()['digital_file'][0])
					{
						Yii::$app->session->setFlash('error', $model->getErrors()['digital_file'][0]);
					}
					$transaction->rollback();
					$model = new Inventory;
					return $this->render('create', [
					  'model' => $model,
					]);
				}
			}
			catch (\Exception $e)
			{
				Yii::$app->session->setFlash('error', $e->getMessage());
				$transaction->rollback();
				$model = new Inventory;
				return $this->render('create', [
										'model' => $model,
									]);
			}
		}
		else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Inventory model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
		if(!Yii::$app->user->can('Inventory.Update')){
			throw new \yii\web\ForbiddenHttpException(Yii::t('app', 'You dont have permissions to view this page.'));
		}
        $model = $this->findModel($id);
		$old_file = $model->digital_file_name;
		
		// Delete Existing Records
		if(!empty($_REQUEST['delete_multiple_recs']))
		{
			$rows=$_REQUEST['selection'];
			if ($rows)
				$cnt = count($rows);
			else
				$cnt = 0;

			for($i=0; $i<$cnt;$i++)
			{
				LicenseKeyCode::findOne($rows[$i])->delete();
			}

			$model->stock -= $cnt;
			$model->update();

			return $this->redirect(['update', 'id' => $_REQUEST['id']]);
		}

		if($model->load(Yii::$app->request->post()))
		{
			$product = Product::findOne($model->product_id);
			$digital_ind = $product->digital;
			$license_key_code = $product->license_key_code;

			if($digital_ind)
			{
				$digital_file = UploadedFile::getInstance($model, 'digital_file');
				if(!empty($digital_file) && $digital_file->size !== 0) 
				{
					if(!$license_key_code)
					{
						//unlink('uploads/'.$old_file);
						MulteModel::deleteFileFromServer($old_file, 'digital_uploads');
					}
					$model->digital_file = $digital_file;
				}
			}

			$model->attribute_values = Json::encode($_REQUEST['Inventory']['attribute_values']);
			if($model->attribute_values == 'null')
				$model->attribute_values = NULL;
			$model->attribute_price = Json::encode($_REQUEST['Inventory']['attribute_price']);
			if($model->attribute_price == 'null')
				$model->attribute_price = NULL;

			if($model->save())
			{
				if($digital_ind && !empty($digital_file) && $digital_file->size !== 0)
				{
					if(!$license_key_code)
					{
						//$name = uniqid($model->id);
						$name = $model->digital_file_name;
						$model->digital_file_name = $name;
						$model->update();

						$model->upload($name);
					}
					else
					{
						// Read input excel and populate code table
						$count = MulteModel::readInputSheet($model->id, $model->digital_file->tempName);
						$model->stock += $count;
						$model->update();
					}
				}

				//return $this->redirect(['view', 'id' => $model->id]);

				if (isset($_REQUEST['inventory_detail_ids']))
				{
					$i=0;
					foreach($_REQUEST['inventory_detail_ids'] as $row)
					{
						$invDet = InventoryDetails::findOne($row);
						$invDet->attribute_price = $_REQUEST['Inventory']['attribute_price'][$i];
						$invDet->save();
						$i++;
					}
				}

				InventoryTags::deleteAll(['=', 'inventory_id', $id]);

				$tags_array = explode(',', $_REQUEST['inventory_tags']);

				foreach($tags_array as $row)
				{
					$inventorytags = new InventoryTags;
					$inventorytags->inventory_id = $model->id;

					$tag = Tags::find()->where(['tag' => strtolower(trim($row))])->one();

					if($tag)
					{
						$inventorytags->tag_id = $tag->id;
					}
					else
					{
						$newtag = new Tags;
						$newtag->tag = strtolower(trim($row));
						$newtag->save();

						$inventorytags->tag_id = $newtag->id;
					}

					$inventorytags->save();
				}

				$inventoryDetails = InventoryDetails::find()->where("inventory_id = $id")->all();

				return $this->render('update', [
					'model' => $model,
					'inventoryDetails' => $inventoryDetails,
					'tags' => $_REQUEST['inventory_tags']
				]);
			}
			else 
			{
				//print_r($model->getErrors());
				//var_dump($_REQUEST);
				//exit;
				$inventoryDetails = InventoryDetails::find()->where("inventory_id = $id")->all();

				return $this->render('update', [
					'model' => $model,
					'inventoryDetails' => $inventoryDetails
				]);
			}
		}
		else 
		{
			$inventoryDetails = InventoryDetails::find()->where("inventory_id = $id")->all();
			$connection = \Yii::$app->db;
			$query = "select a.tag from tbl_tags a, tbl_inventory_tags b
						where b.inventory_id = ".$id."
						and a.id = b.tag_id;";
			$mdl = $connection->createCommand($query);

			$result = $mdl->queryAll();
			$tags = '';

			foreach ($result as $row)
			{
				$tags = $tags.$row['tag'].",";
			}
			$tags = substr($tags, 0, -1);

			return $this->render('update', [
				'model' => $model,
				'inventoryDetails' => $inventoryDetails,
				'tags' => $tags
			]);
		}

    }

    /**
     * Deletes an existing Inventory model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
		if(!Yii::$app->user->can('Inventory.Delete')){
			throw new \yii\web\ForbiddenHttpException(Yii::t('app', 'You dont have permissions to view this page.'));
		}
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Inventory model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Inventory the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
		if(Yii::$app->params['user_role'] == 'admin')
		{
			$model = Inventory::find()->where("id=".$id)->one();
			return $model;
		}
		else
        if (($model = Inventory::find()->where("id=".$id." and vendor_id=".Yii::$app->user->identity->entity_id)->one()) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
        }
    }

	public function actionAjaxLoadAttributes()
	{
		$product_id=!empty($_REQUEST['product_id'])?$_REQUEST['product_id']:'';
		$sub_subcategory_id = Product::findOne($product_id)->sub_subcategory_id;
		$attributes = ProductAttributes::find()->orderBy('id')->where("parent_id=$sub_subcategory_id and active=1")->asArray()->all();
		$i = 0;
		$attributeValues = [];
		foreach($attributes as $row)
		{
			if($row['fixed'] == '1')
			{
				$attributeValues[$i] = ProductAttributeValues::findOne($row['fixed_id']);
				$i++;
			}
		}

		return $this->renderPartial('ajax-load-attributes', [
                'attributes' => $attributes,
				'attributeValues' => $attributeValues,
				'stock_ind' => $_REQUEST['stock_ind'],
            ]);
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

	public function actionAjaxLoadProducts(){
		$sub_subcategory_id=!empty($_REQUEST['sub_subcategory_id'])?$_REQUEST['sub_subcategory_id']:'0';
		$product_id = !empty($_REQUEST['product_id'])?$_REQUEST['product_id']:'0';
		$products = Product::find()->orderBy('name')->where("sub_subcategory_id=$sub_subcategory_id and active=1")->asArray()->all();
		 return $this->renderPartial('ajax-load-products', [
                'products' => $products,
				'sub_subcategory_id' => $sub_subcategory_id,
				'product_id' => $product_id
            ]);
	}

	public function actionAjaxLoadInventory(){
		$inventory_id=!empty($_REQUEST['inventory_id'])?$_REQUEST['inventory_id']:'0';
		$product_id = !empty($_REQUEST['product_id'])?$_REQUEST['product_id']:'0';
		$inventories = Inventory::find()->orderBy('product_name')->where("product_id=$product_id")->asArray()->all();
		 return $this->renderPartial('ajax-load-inventory', [
                'inventories' => $inventories,
				'inventory_id' => $inventory_id,
				'product_id' => $product_id
            ]);
	}

	public function actionAjaxGetProductType(){
		//return !empty($_REQUEST['product_id'])?Product::findOne($_REQUEST['product_id'])->digital:0;

		if(empty($_REQUEST['product_id']))
			return 0;

		$product = Product::findOne($_REQUEST['product_id']);

		if($product->digital == 1)
		{
			if($product->license_key_code == 1)
			{
				return 2;
			}
			else
			{
				return 1;
			}
		}
		else
		{
			return 0;
		}
	}

	public function actionSetFeatured()
	{
		if(Yii::$app->params['user_role'] != 'admin')
		{
			throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
		}

		$model = Inventory::findOne($_REQUEST['id']);
		$model->featured = 1;
		$model->save();
		
		if($_REQUEST['return'] == 'index')
			return $this->redirect(['index']);
		else
			return $this->redirect(['update', 'id' => $_REQUEST['id']]);
	}

	public function actionUnsetFeatured()
	{
		if(Yii::$app->params['user_role'] != 'admin')
		{
			throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
		}

		$model = Inventory::findOne($_REQUEST['id']);
		$model->featured = 0;
		$model->save();

		if($_REQUEST['return'] == 'index')
			return $this->redirect(['index']);
		else
			return $this->redirect(['update', 'id' => $_REQUEST['id']]);
	}

	public function actionSetSpecial()
	{
		if(Yii::$app->params['user_role'] != 'admin')
		{
			throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
		}

		$model = Inventory::findOne($_REQUEST['id']);
		$model->special = 1;
		$model->save();

		if($_REQUEST['return'] == 'index')
			return $this->redirect(['index']);
		else
			return $this->redirect(['update', 'id' => $_REQUEST['id']]);
	}

	public function actionUnsetSpecial()
	{
		if(Yii::$app->params['user_role'] != 'admin')
		{
			throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
		}

		$model = Inventory::findOne($_REQUEST['id']);
		$model->special = 0;
		$model->save();

		if($_REQUEST['return'] == 'index')
			return $this->redirect(['index']);
		else
			return $this->redirect(['update', 'id' => $_REQUEST['id']]);
	}

	public function actionSetHot()
	{
		if(Yii::$app->params['user_role'] != 'admin')
		{
			throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
		}
		
		$hotcount = Inventory::find()->where(['hot' => 1])->count();
		if($hotcount == 0)
		{
			$model = Inventory::findOne($_REQUEST['id']);
			$model->hot = 1;
			$model->save();
		}
		else
		{
			Yii::$app->session->setFlash('error', Yii::t('app', 'One Hot Deal already exists - can not have more than one hot deal at a time!'));
		}

		if($_REQUEST['return'] == 'index')
			return $this->redirect(['index']);
		else
			return $this->redirect(['update', 'id' => $_REQUEST['id']]);
	}

	public function actionUnsetHot()
	{
		if(Yii::$app->params['user_role'] != 'admin')
		{
			throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
		}

		$model = Inventory::findOne($_REQUEST['id']);
		$model->hot = 0;
		$model->save();

		if($_REQUEST['return'] == 'index')
			return $this->redirect(['index']);
		else
			return $this->redirect(['update', 'id' => $_REQUEST['id']]);
	}
}
