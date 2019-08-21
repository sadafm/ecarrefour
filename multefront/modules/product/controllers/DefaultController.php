<?php

namespace multefront\modules\product\controllers;

use Yii;
use multebox\Controller;
use multebox\models\search\MulteModel;
use multebox\models\Product;
use multebox\models\Inventory;
use multebox\models\InventoryDetails;
use multebox\models\ProductAttributes;
use multebox\models\ProductSubCategory;
use multebox\models\ProductSubSubCategory;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use yii\helpers\Json;
use multebox\models\SendEmail;

define("MAX_ITEMS_LOAD", "20");

/**
 * Default controller for the `product` module
 */
class DefaultController extends Controller
{
	public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'search' => ['post'],
					'filter' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

	public function actionSearch()
    {
		$escapedstring = str_replace("'", "", $_REQUEST['searchbox']);
		$stringtokens = str_replace(' ', ',', trim(strtolower($escapedstring)));

		$token_array = explode(',', $stringtokens);
		
		$tagstring = '';
		foreach($token_array as $row)
		{
			$tagstring = $tagstring."'".$row."',";
		}
		$tagstring = substr($tagstring, 0, -1).",'".strtolower($escapedstring)."'";
		
		$connection = \Yii::$app->db;

		$query = "(select id from tbl_inventory where active = 1 and product_id in 
					(select id from tbl_product where lower(name) like '".strtolower($escapedstring)."%')
					order by id desc limit 10)
					union
					(select id from tbl_inventory where active = 1 and id in 
					(select inventory_id from tbl_inventory_tags a, 
					tbl_tags b where b.tag in (".$tagstring.") and b.id = a.tag_id)
					order by id desc limit 10)
					union
					(select id from tbl_inventory where active = 1 and id in 
					(select inventory_id from tbl_inventory_tags a, 
					tbl_tags b where b.tag like ('%".$escapedstring."%') and b.id = a.tag_id)
					order by id desc limit 10)
					order by id desc";
		
		//var_dump($query);exit;

		$mdl = $connection->createCommand($query);

		$result = $mdl->queryAll();
		
		$inventories = "'',";
		foreach($result as $row)
		{
			$inventories = $inventories."'".$row['id']."',";
		}
		$inventories = substr($inventories, 0, -1);

		$itemsList = Inventory::find()->where("id in (".$inventories.")")->distinct()->limit(20)->OrderBy('id desc')->all();

        return $this->render('search', [
            'itemsList' => $itemsList,
			'searchbox' => $_REQUEST['searchbox'],
			'category_id' => '',
			'sub_category_id' => '',
			'sub_subcategory_id' => '',
			'vendor_id' => '',
			'digital' => '',
			'sortfilter' => '',
			'showfilters' => 'false',
        ]);
    }

	public function actionReSearch()
    {
		$escapedstring = str_replace("'", "", $_REQUEST['searchbox']);
		$stringtokens = str_replace(' ', ',', trim(strtolower($escapedstring)));

		$token_array = explode(',', $stringtokens);
		
		$tagstring = '';
		foreach($token_array as $row)
		{
			$tagstring = $tagstring."'".$row."',";
		}
		$tagstring = substr($tagstring, 0, -1).",'".strtolower($escapedstring)."'";
		
		$connection = \Yii::$app->db;

		$query = "(select id from tbl_inventory where id < ".$_REQUEST['last_id']." and active = 1 and product_id in 
					(select id from tbl_product where lower(name) like '".strtolower($escapedstring)."%')
					order by id desc limit 10)
					union
					(select id from tbl_inventory where id < ".$_REQUEST['last_id']." and active = 1 and id in 
					(select inventory_id from tbl_inventory_tags a, 
					tbl_tags b where b.tag in (".$tagstring.") and b.id = a.tag_id)
					order by id desc limit 10)
					union
					(select id from tbl_inventory where id < ".$_REQUEST['last_id']." and active = 1 and id in 
					(select inventory_id from tbl_inventory_tags a, 
					tbl_tags b where b.tag like ('%".$escapedstring."%') and b.id = a.tag_id)
					order by id desc limit 10)
					order by id desc";
		
		//var_dump($query);exit;

		$mdl = $connection->createCommand($query);

		$result = $mdl->queryAll();
		
		$inventories = "'',";
		foreach($result as $row)
		{
			$inventories = $inventories."'".$row['id']."',";
		}
		$inventories = substr($inventories, 0, -1);

		$itemsList = Inventory::find()->where("id in (".$inventories.") and id < ".$_REQUEST['last_id'])->distinct()->limit(20)->OrderBy('id desc')->all();

        if(!$itemsList)
			return 0;
		else
		return $this->renderPartial('data', [
                'itemsList' => $itemsList,
            ]);
    }


	protected function getItemsList()
	{
		$names_list = "'@$^',";
		$i=0;
		foreach($_REQUEST['attribute_names'] as $row)
		{
			if($_REQUEST['attribute_value'][$i] != '')
				$names_list = "'".$row."',".$names_list;

			$i++;
		}
		$names_list = substr($names_list, 0, -1);

		if($names_list != "'@$^'")
		{
			
			$connection = \Yii::$app->db;

			$parent_id = Product::findOne($_REQUEST['product_id'])->sub_subcategory_id;

			$query = "SELECT a.inventory_id, group_concat(a.attribute_value order by a.attribute_id SEPARATOR ';' ) as result FROM `tbl_inventory_details` a, `tbl_product_attributes` b 
						WHERE b.parent_id=".$parent_id." and b.name in (".$names_list.")
						and a.attribute_id = b.id
						group by a.inventory_id
						order by a.inventory_id";
			
			$model = $connection->createCommand($query);

			$result = $model->queryAll();

			$search_set = '';

			foreach($_REQUEST['attribute_value'] as $set)
			{
				if($set != '')
					$search_set = $search_set.$set.";";
			}
			$search_set = substr($search_set, 0, -1);

			$inventory_ids = '0,';
			foreach($result as $row)
			{
				if($row['result'] == $search_set)
				{
					$inventory_ids = $row['inventory_id'].",".$inventory_ids;
				}
			}
			$inventory_ids = substr($inventory_ids, 0, -1);

			$itemsList = Inventory::find()
									->where("id in (".$inventory_ids.")")
									->andWhere('active = 1')
									->all();
		}
		else
		{
			$itemsList = Inventory::find()
									->where("product_id=".$_REQUEST['product_id'])
									->andWhere('active = 1')
									->all();
		}

		return $itemsList;
	}

	public function actionAjaxDetail()
	{
		$itemsList = $this->getItemsList();

		if(!$itemsList)
		{
			$inventory = Inventory::findOne($_REQUEST['inventory_id']);
		}
		else
		{
			$inventory = Inventory::findOne($itemsList[0]['id']);
		}

		return $this->renderPartial('detail', [
            'inventory' => $inventory
        ]);
	}

	public function actionAjaxGetMatchingItem()
	{
		$inventory = Inventory::findOne(['active' => 1, 'product_id' => $_REQUEST['product_id'], 'vendor_id' => $_REQUEST['vendor_id'], 'attribute_values' => $_REQUEST['attributes_list']]);
		
		if(!$inventory || $inventory->stock == 0)
		{
			return 'NRF'; // No result found
		}
		else
		{
			$inventoryPrice = MulteModel::getInventoryActualPrice ($inventory, 1);
			$inventoryDiscount = MulteModel::getInventoryDiscountPercentage ($inventory, 1);
			$inventoryDiscountedPrice = round($inventoryPrice - $inventoryPrice*$inventoryDiscount/100, 2);
			$product_code = 'INVT'.str_pad($inventory->id, 9, "0", STR_PAD_LEFT);
			
			$ret_arr = [MulteModel::formatAmount($inventoryPrice), $inventoryDiscount, MulteModel::formatAmount($inventoryDiscountedPrice), $product_code, $inventory->id, $inventory->stock];

			return Json::encode($ret_arr);
			//return $inventory->id;
			/*return $this->renderPartial('detail', [
				'inventory' => $inventory
			]);*/
		}
	}

	public function actionResult()
	{
		$itemsList = $this->getItemsList();

		return $this->render('result', [
            'itemsList' => $itemsList,
			'category_id' => '',
			'sub_category_id' => '',
			'sub_subcategory_id' => '',
			'vendor_id' => '',
			'digital' => '',
			'sortfilter' => '',
			'showfilters' => 'false',
			]);
	}

	public function actionDetail()
    {
		if(isset($_REQUEST['sendtofriend']))
		{
			$link = Url::to(['/product/default/detail', 'inventory_id' => $_REQUEST['inventory_id']], true);
			SendEmail::sendItemToFriendEmail($_REQUEST['modal_yourname'], $_REQUEST['modal_friendname'], $_REQUEST['modal_youremail'], $_REQUEST['modal_friendemail'], $link, $_REQUEST['modal_message']);
			Yii::$app->session->setFlash('success', Yii::t('app', 'Email sent successfully!'));
		}

		$inventory = Inventory::findOne($_GET['inventory_id']);

		if(!$inventory || $inventory->active == 0)
		{
			throw new NotFoundHttpException(Yii::t('app', 'You are trying to perform an activity which is not allowed!'));
		}

		return $this->render('detail', [
            'inventory' => $inventory
        ]);
    }

	public function actionListing()
    {
		if($_GET['category_id'] != '' && $_GET['sub_category_id'] != '' && $_GET['sub_subcategory_id'] != '')
		{
			$itemsList = Inventory::find()
								//->select(['product_id', 'vendor_id'])
								->joinWith('inventoryProducts p')
								->where('tbl_inventory.active = 1')
								->andWhere('p.category_id = '.$_GET['category_id'])
								->andWhere('p.sub_category_id = '.$_GET['sub_category_id'])
								->andWhere('p.sub_subcategory_id = '.$_GET['sub_subcategory_id'])
								//->orderBy('stock desc, name')
								->orderBy('tbl_inventory.id desc, name')
								//->distinct()
								->limit(MAX_ITEMS_LOAD)
								->asArray()
								->all();
		}
		else
		if($_GET['category_id'] != '' && $_GET['sub_category_id'] != '')
		{
			$itemsList = Inventory::find()
								//->select(['product_id', 'vendor_id'])
								->joinWith('inventoryProducts p')
								->where('tbl_inventory.active = 1')
								->andWhere('p.category_id = '.$_GET['category_id'])
								->andWhere('p.sub_category_id = '.$_GET['sub_category_id'])
								//->orderBy('stock desc, name')
								->orderBy('tbl_inventory.id desc, name')
								//->distinct()
								->limit(MAX_ITEMS_LOAD)
								->asArray()
								->all();
		}
		else
		if($_GET['category_id'] != '')
		{
			$itemsList = Inventory::find()
								//->select(['product_id', 'vendor_id'])
								->joinWith('inventoryProducts p')
								->where('tbl_inventory.active = 1')
								->andWhere('p.category_id = '.$_GET['category_id'])
								//->orderBy('stock desc, name')
								->orderBy('tbl_inventory.id desc, name')
								//->distinct()
								->limit(MAX_ITEMS_LOAD)
								->all();
		}
		else
		{
			throw new NotFoundHttpException(Yii::t('app', 'You are trying to perform an activity which is not allowed!'));
		}

		//echo "<pre>";
		//print_r($itemsList);
		//exit;

        return $this->render('listing', [
            'itemsList' => $itemsList,
			'category_id' => $_GET['category_id'],
			'sub_category_id' => $_GET['sub_category_id'],
			'sub_subcategory_id' => $_GET['sub_subcategory_id'],
			'vendor_id' => '',
			'digital' => '',
			'sortfilter' => '',
			'showfilters' => 'true',
        ]);
    }

	public function actionReListing()
    {
		if($_REQUEST['category_id'] != '' && $_REQUEST['sub_category_id'] != '' && $_REQUEST['sub_subcategory_id'] != '')
		{
			$itemsList = Inventory::find()
								//->select(['product_id', 'vendor_id'])
								->joinWith('inventoryProducts p')
								->where('tbl_inventory.active = 1 and tbl_inventory.id < '.$_REQUEST['last_id'])
								->andWhere('p.category_id = '.$_REQUEST['category_id'])
								->andWhere('p.sub_category_id = '.$_REQUEST['sub_category_id'])
								->andWhere('p.sub_subcategory_id = '.$_REQUEST['sub_subcategory_id'])
								//->orderBy('stock desc, name')
								->orderBy('tbl_inventory.id desc, name')
								//->distinct()
								->limit(MAX_ITEMS_LOAD)
								->asArray()
								->all();
		}
		else
		if($_REQUEST['category_id'] != '' && $_REQUEST['sub_category_id'] != '')
		{
			$itemsList = Inventory::find()
								//->select(['product_id', 'vendor_id'])
								->joinWith('inventoryProducts p')
								->where('tbl_inventory.active = 1 and tbl_inventory.id < '.$_REQUEST['last_id'])
								->andWhere('p.category_id = '.$_REQUEST['category_id'])
								->andWhere('p.sub_category_id = '.$_REQUEST['sub_category_id'])
								//->orderBy('stock desc, name')
								->orderBy('tbl_inventory.id desc, name')
								//->distinct()
								->limit(MAX_ITEMS_LOAD)
								->asArray()
								->all();
		}
		else
		if($_REQUEST['category_id'] != '')
		{
			$itemsList = Inventory::find()
								//->select(['product_id', 'vendor_id'])
								->joinWith('inventoryProducts p')
								->where('tbl_inventory.active = 1 and tbl_inventory.id < '.$_REQUEST['last_id'])
								->andWhere('p.category_id = '.$_REQUEST['category_id'])
								//->orderBy('stock desc, name')
								->orderBy('tbl_inventory.id desc, name')
								//->distinct()
								->limit(MAX_ITEMS_LOAD)
								->all();
		}
		else
		{
			throw new NotFoundHttpException(Yii::t('app', 'You are trying to perform an activity which is not allowed!'));
		}

		if(!$itemsList)
			return 0;
		else
		return $this->renderPartial('data', [
                'itemsList' => $itemsList,
            ]);
    }

	public function actionFilter()
    {
		//var_dump($_POST);exit;
		$in_products_query = false;
		if((!empty($_REQUEST['category_id']) && $_REQUEST['category_id'] != '') || (!empty($_REQUEST['sub_category_id']) && $_REQUEST['sub_category_id'] != '') || (!empty($_REQUEST['sub_subcategory_id']) && $_REQUEST['sub_subcategory_id'] != '') || (!empty($_REQUEST['digitaltype']) && $_REQUEST['digitaltype'] != ''))
		{
			$in_products_query = true;

			$category = is_null($_REQUEST['category_id'])||empty($_REQUEST['category_id'])?'null':$_REQUEST['category_id'];
			$sub_category = is_null($_REQUEST['sub_category_id'])||empty($_REQUEST['sub_category_id'])?'null':$_REQUEST['sub_category_id'];
			$sub_subcategory = is_null($_REQUEST['sub_subcategory_id'])||empty($_REQUEST['sub_subcategory_id'])?'null':$_REQUEST['sub_subcategory_id'];
			$digital = is_null($_REQUEST['digitaltype'])||empty($_REQUEST['digitaltype'])?'null':$_REQUEST['digitaltype'];

			$connection = \Yii::$app->db;
			$query = "select id from tbl_product where category_id=ifnull(".$category.", category_id) and sub_category_id=ifnull(".$sub_category.", sub_category_id) 
						and sub_subcategory_id=ifnull(".$sub_subcategory.", sub_subcategory_id)
						and digital=ifnull(".$digital.", digital)
						order by id desc";
			//var_dump($query);exit;
				
			$model = $connection->createCommand($query);

			$result = $model->queryAll();

			$product_ids = "0,";
			foreach($result as $row)
			{
				$product_ids = $product_ids.$row['id'].",";
			}
			$product_ids = substr($product_ids, 0, -1);
		}

		$vendor = is_null($_REQUEST['vendor_id'])||empty($_REQUEST['vendor_id'])?'null':$_REQUEST['vendor_id'];
		$orderby = ' order by id desc';

		if(!empty($_REQUEST['sortfilter']) && $_REQUEST['sortfilter'] != '')
		{
			switch ($_REQUEST['sortfilter'])
			{
				case 'name_asc':
					$orderby = ' order by id desc, product_name asc';
					break;

				case 'name_desc':
					$orderby = ' order by id desc, product_name desc';
					break;

				case 'price_asc':
					$orderby = ' order by id desc, price asc';
					break;

				case 'price_desc':
					$orderby = ' order by id desc, price desc';
					break;
			}
		}
		
		$in_string = '';
		if ($in_products_query)
		{
			$in_string = ' and product_id in ('.$product_ids.')';
		}

		$connection = \Yii::$app->db;
		$query = "select * from tbl_inventory where active = 1 and vendor_id=ifnull(".$vendor.", vendor_id)".$in_string.$orderby." limit 20";
		//var_dump($query);exit;
			
		$model = $connection->createCommand($query);

		$itemsList = $model->queryAll();

        return $this->render('filter-result', [
            'itemsList' => $itemsList,
			'vendor_id' => $_REQUEST['vendor_id'],
			'category_id' => $_REQUEST['category_id'],
			'sub_category_id' => $_REQUEST['sub_category_id'],
			'sub_subcategory_id' => $_REQUEST['sub_subcategory_id'],
			'digital' => $_REQUEST['digitaltype'],
			'sortfilter' => $_REQUEST['sortfilter'],
			'showfilters' => 'true',
        ]);
    }

	public function actionReFilter()
    {
		//var_dump($_POST);exit;
		$in_products_query = false;
		if((!empty($_REQUEST['category_id']) && $_REQUEST['category_id'] != '') || (!empty($_REQUEST['sub_category_id']) && $_REQUEST['sub_category_id'] != '') || (!empty($_REQUEST['sub_subcategory_id']) && $_REQUEST['sub_subcategory_id'] != '') || (!empty($_REQUEST['digitaltype']) && $_REQUEST['digitaltype'] != ''))
		{
			$in_products_query = true;

			$category = is_null($_REQUEST['category_id'])||empty($_REQUEST['category_id'])?'null':$_REQUEST['category_id'];
			$sub_category = is_null($_REQUEST['sub_category_id'])||empty($_REQUEST['sub_category_id'])?'null':$_REQUEST['sub_category_id'];
			$sub_subcategory = is_null($_REQUEST['sub_subcategory_id'])||empty($_REQUEST['sub_subcategory_id'])?'null':$_REQUEST['sub_subcategory_id'];
			$digital = is_null($_REQUEST['digitaltype'])||empty($_REQUEST['digitaltype'])?'null':$_REQUEST['digitaltype'];

			$connection = \Yii::$app->db;
			$query = "select id from tbl_product where category_id=ifnull(".$category.", category_id) and sub_category_id=ifnull(".$sub_category.", sub_category_id) 
						and sub_subcategory_id=ifnull(".$sub_subcategory.", sub_subcategory_id)
						and digital=ifnull(".$digital.", digital)
						order by id desc";
			//var_dump($query);exit;
				
			$model = $connection->createCommand($query);

			$result = $model->queryAll();

			$product_ids = "0,";
			foreach($result as $row)
			{
				$product_ids = $product_ids.$row['id'].",";
			}
			$product_ids = substr($product_ids, 0, -1);
		}

		$vendor = is_null($_REQUEST['vendor_id'])||empty($_REQUEST['vendor_id'])?'null':$_REQUEST['vendor_id'];
		$orderby = ' order by id desc';

		if(!empty($_REQUEST['sortfilter']) && $_REQUEST['sortfilter'] != '')
		{
			switch ($_REQUEST['sortfilter'])
			{
				case 'name_asc':
					$orderby = ' order by id desc, product_name asc';
					break;

				case 'name_desc':
					$orderby = ' order by id desc, product_name desc';
					break;

				case 'price_asc':
					$orderby = ' order by id desc, price asc';
					break;

				case 'price_desc':
					$orderby = ' order by id desc, price desc';
					break;
			}
		}
		
		$in_string = '';
		if ($in_products_query)
		{
			$in_string = ' and product_id in ('.$product_ids.')';
		}

		$connection = \Yii::$app->db;
		$query = "select * from tbl_inventory where id < ".$_REQUEST['last_id']." and active = 1 and vendor_id=ifnull(".$vendor.", vendor_id)".$in_string.$orderby." limit 20";
		//var_dump($query);exit;
			
		$model = $connection->createCommand($query);

		$itemsList = $model->queryAll();

       if(!$itemsList)
			return 0;
		else
		return $this->renderPartial('data', [
                'itemsList' => $itemsList,
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
}
