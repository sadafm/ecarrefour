<?php

namespace multefront\modules\review\controllers;

use Yii;
use multebox\Controller;
use multebox\models\Order;
use multebox\models\SubOrder;
use multebox\models\OrderStatus;
use multebox\models\ProductReview;
use multebox\models\VendorReview;
use multebox\models\Inventory;
use multebox\models\Product;
use multebox\models\Vendor;
use yii\web\NotFoundHttpException;
use \Exception;

/**
 * Default controller for the `review` module
 */
class DefaultController extends Controller
{
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

	public function actionProduct()
    {
		if(Order::findOne($_REQUEST['order_id'])->customer_id != Yii::$app->user->identity->entity_id)
		{
			throw new NotFoundHttpException(Yii::t('app', 'You are trying to perform an activity which is not allowed!'));
		}

		//var_dump($_REQUEST['productrating']);exit;
		if(isset($_REQUEST['productrating']))
		{
			$connection = Yii::$app->db;

			try
			{
				$transaction = $connection->beginTransaction(); 
				$i = 0;
				foreach($_REQUEST['productid'] as $id)
				{
					if($_REQUEST['productrating'][$i] == '')
					{
						$i++;
						continue;
					}

					$prod_review = ProductReview::find()->where("product_id=".$id." and customer_id=".Yii::$app->user->identity->entity_id)->one();

					if(!$prod_review)
					{
						$prod_review = new ProductReview;
						$prod_review->product_id = $id;
						$prod_review->customer_id = Yii::$app->user->identity->entity_id;
						$prod_review->added_at = time();
					}

					$prod_review->rating = $_REQUEST['productrating'][$i];
					$prod_review->review = $_REQUEST['productreview'][$i];
					
					$prod_review->save();

					$avg = ProductReview::find()->where("product_id=".$id)->average('rating');

					// Update average rating in tbl_inventory
					Inventory::updateAll(['product_rating' => $avg], ['=', 'product_id', $id]);

					// Update average rating in tbl_product
					Product::updateAll(['rating' => $avg], ['=', 'id', $id]);

					$i++;
				}

				$transaction->commit();

				Yii::$app->session->setFlash('success', Yii::t('app', 'Review saved successfully!'));
			}
			catch (Exception $e)
			{
				Yii::$app->session->setFlash('error', $e->getMessage());
				$transaction->rollback();
				return $this->goHome();
			}
		}

		$connection = \Yii::$app->db;

		/* SubOrder::find()->select('a.product_id')->joinWith('inventory a')->where("order_id=".$_REQUEST['order_id']." and sub_order_status in ('".OrderStatus::_DELIVERED."', '".OrderStatus::_COMPLETED."', '".OrderStatus::_RETURNED."')")->distinct();*/
		
		$query = "select distinct a.product_id from tbl_inventory a, tbl_sub_order b where b.order_id = ".$_REQUEST['order_id']." and sub_order_status in ('".OrderStatus::_DELIVERED."', '".OrderStatus::_COMPLETED."', '".OrderStatus::_RETURNED."', '".OrderStatus::_REFUNDED."') and a.id = b.inventory_id";

		$model = $connection->createCommand($query);

		$products = $model->queryAll();

		return $this->render('product', ['products' => $products]);
    }

	public function actionVendor()
    {
		if(Order::findOne($_REQUEST['order_id'])->customer_id != Yii::$app->user->identity->entity_id)
		{
			throw new NotFoundHttpException(Yii::t('app', 'You are trying to perform an activity which is not allowed!'));
		}

		if(isset($_REQUEST['vendorrating']))
		{
			$connection = Yii::$app->db;

			try
			{
				$transaction = $connection->beginTransaction(); 
				$i = 0;
				foreach($_REQUEST['vendorid'] as $id)
				{
					if($_REQUEST['vendorrating'][$i] == '')
					{
						$i++;
						continue;
					}

					$vendor_review = VendorReview::find()->where("vendor_id=".$id." and customer_id=".Yii::$app->user->identity->entity_id)->one();

					if(!$vendor_review)
					{
						$vendor_review = new VendorReview;
						$vendor_review->vendor_id = $id;
						$vendor_review->customer_id = Yii::$app->user->identity->entity_id;
						$vendor_review->added_at = time();
					}

					$vendor_review->rating = $_REQUEST['vendorrating'][$i];
					$vendor_review->review = $_REQUEST['vendorreview'][$i];
					
					$vendor_review->save();

					$avg = VendorReview::find()->where("vendor_id=".$id)->average('rating');

					// Update average rating in tbl_inventory
					Inventory::updateAll(['vendor_rating' => $avg], ['=', 'vendor_id', $id]);

					// Update average rating in tbl_vendor
					Vendor::updateAll(['rating' => $avg], ['=', 'id', $id]);

					$i++;
				}

				$transaction->commit();

				Yii::$app->session->setFlash('success', Yii::t('app', 'Review saved successfully!'));
			}
			catch (Exception $e)
			{
				Yii::$app->session->setFlash('error', $e->getMessage());
				$transaction->rollback();
				return $this->goHome();
			}
		}

		$vendors = SubOrder::find()->select('vendor_id')->where("order_id=".$_REQUEST['order_id']." and sub_order_status in ('".OrderStatus::_DELIVERED."', '".OrderStatus::_COMPLETED."', '".OrderStatus::_RETURNED."')")->distinct()->all();

        return $this->render('vendor', ['vendors' => $vendors]);
    }
}
