<?php

namespace multeback\modules\bulk\controllers;

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
use multebox\models\Tags;
use multebox\models\InventoryTags;
use multebox\models\Inventory;
use multebox\models\Vendor;
use multebox\models\InventoryDetails;
use multebox\models\search\Inventory as InventorySearch;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\NamedRange;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;

function getCategoryIDNumber($input)
{
	if($input)
	{
		$category = ProductCategory::findOne(['name' => $input]);
		if($category)
		{
			return $category->id;
		}
		else
		{
			$start = strpos($input, "(");
			$end = strpos($input, ")");

			$id = substr($input, $start+1, $end-1);

			return $id?$id:NULL;
		}
	}
	else
	{
		return NULL;
	}
}

function getSubCategoryIDNumber($parent_id, $input)
{
	if($input)
	{
		$subcategory = ProductSubCategory::findOne(['parent_id' => $parent_id, 'name' => $input]);
		if($subcategory)
		{
			return $subcategory->id;
		}
		else
		{
			$start = strpos($input, "(");
			$end = strpos($input, ")");

			$id = substr($input, $start+1, $end-1);

			return $id?$id:NULL;
		}
	}
	else
	{
		return NULL;
	}
}

function getSubSubCategoryIDNumber($parent_id, $input)
{
	if($input)
	{
		$sub_subcategory = ProductSubSubCategory::findOne(['parent_id' => $parent_id, 'name' => $input]);
		if($sub_subcategory)
		{
			return $sub_subcategory->id;
		}
		else
		{
			$start = strpos($input, "(");
			$end = strpos($input, ")");

			$id = substr($input, $start+1, $end-1);

			return $id?$id:NULL;
		}
	}
	else
	{
		return NULL;
	}
}

function getBrandIDNumber($input)
{
	if($input)
	{
		$brand = ProductBrand::findOne(['name' => $input]);
		if($brand)
		{
			return $brand->id;
		}
		else
		{
			$start = strpos($input, "(");
			$end = strpos($input, ")");

			$id = substr($input, $start+1, $end-1);

			return $id?$id:NULL;
		}
	}
	else
	{
		return NULL;
	}
}

function getYesNoInd($input)
{
	if($input)
	{
		return $input=="Yes"?1:0;
	}
	else
	{
		return 0;
	}
}

function validateInputProduct($category, $sub_category, $sub_subcategory)
{
	if (ProductSubCategory::find()->where(['id' => $sub_category, 'parent_id' => $category])->one())
	{
		if(ProductSubSubCategory::find()->where(['id' => $sub_subcategory, 'parent_id' => $sub_category])->one())
		{
			return 1;
		}
		else
		{
			return 0;
		}
	}
	else
	{
		return 0;
	}
}

function getPriceType($input)
{
	if($input)
	{
		return $input=="Flat"?'F':'B';
	}
	else
	{
		return NULL;
	}
}

function getDiscountType($input)
{
	if($input)
	{
		return $input=="Flat"?'F':'P';
	}
	else
	{
		return NULL;
	}
}

/**
 * Default controller for the `bulk` module
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

	public function actionBulkUploadProducts()
    {
		if(!Yii::$app->user->can('Product.Create')){
			throw new \yii\web\ForbiddenHttpException(Yii::t('app', 'You dont have permissions to view this page.'));
		}

		try
		{
			if(isset($_REQUEST['bulk_download']))
			{
				$alphalist = range("A", "Z");

				$max_col_count = count($alphalist);
				$max_row_count = 1000000;

				$alpha_index = 0;
				$row_index = 1;

				$spreadsheet = new Spreadsheet();

				$myWorkSheet = new Worksheet($spreadsheet, 'Data');
				$spreadsheet->addSheet($myWorkSheet, 1);

				$sheet = $spreadsheet->setActiveSheetIndex(1);

				$categories = ProductCategory::find()->where("active=1")->all();

				if($categories)
				{
					if(count($categories) > $max_row_count)
					{
						throw new \yii\web\ForbiddenHttpException(Yii::t('app', 'Too many rows!')); 
					}
				}
				
				$start_range = $alphalist[$alpha_index].$row_index;
				foreach ($categories as $category)
				{
					$sheet->setCellValue ($alphalist[$alpha_index].$row_index, '('.$category->id.') '.$category->name);

					$row_index++;
				}
				$end_range = $alphalist[$alpha_index].($row_index-1);
				
				$spreadsheet->addNamedRange( new NamedRange('CATEGORY', $spreadsheet->getActiveSheet(), $start_range.':'.$end_range) );

				foreach ($categories as $category)
				{
					$sub_categories = ProductSubCategory::find()->where("parent_id = ".$category->id." and active=1")->all();
					
					if(count($sub_categories) > $max_row_count)
					{
						throw new \yii\web\ForbiddenHttpException(Yii::t('app', 'Too many rows!')); 
					}

					if(count($sub_categories) > ($max_row_count - $row_index))
					{
						$alpha_index++;
						if($alpha_index > $max_col_count)
						{
							throw new \yii\web\ForbiddenHttpException(Yii::t('app', 'Too many columns!')); 
						}
						$row_index = 1;
					}

					$start_range = $alphalist[$alpha_index].$row_index;
					foreach ($sub_categories as $sub_category)
					{
						$sheet->setCellValue ($alphalist[$alpha_index].$row_index, '('.$sub_category->id.') '.$sub_category->name);

						$row_index++;
					}
					$end_range = $alphalist[$alpha_index].($row_index-1);

					$spreadsheet->addNamedRange( new NamedRange('SUBCATEGORY'.$category->id, $spreadsheet->getActiveSheet(), $start_range.':'.$end_range) );

					foreach ($sub_categories as $sub_category)
					{
						$sub_subcategories = ProductSubSubCategory::find()->where("parent_id = ".$sub_category->id." and active=1")->all();
						
						if(count($sub_subcategories) > $max_row_count)
						{
							throw new \yii\web\ForbiddenHttpException(Yii::t('app', 'Too many rows!')); 
						}

						if(count($sub_subcategories) > ($max_row_count - $row_index))
						{
							$alpha_index++;
							if($alpha_index > $max_col_count)
							{
								throw new \yii\web\ForbiddenHttpException(Yii::t('app', 'Too many columns!')); 
							}
							$row_index = 1;
						}

						$start_range = $alphalist[$alpha_index].$row_index;
						foreach ($sub_subcategories as $sub_subcategory)
						{
							$sheet->setCellValue ($alphalist[$alpha_index].$row_index, '('.$sub_subcategory->id.') '.$sub_subcategory->name);

							$row_index++;
						}
						$end_range = $alphalist[$alpha_index].($row_index-1);

						$spreadsheet->addNamedRange( new NamedRange('SUBSUBCATEGORY'.$sub_category->id, $spreadsheet->getActiveSheet(), $start_range.':'.$end_range) );
					}
				}

				$brands = ProductBrand::find()->where("active=1")->all();

				if(count($brands) > $max_row_count)
				{
					throw new \yii\web\ForbiddenHttpException(Yii::t('app', 'Too many rows!')); 
				}

				if(count($brands) > ($max_row_count - $row_index))
				{
					$alpha_index++;
					if($alpha_index > $max_col_count)
					{
						throw new \yii\web\ForbiddenHttpException(Yii::t('app', 'Too many columns!')); 
					}
					$row_index = 1;
				}

				$start_range = $alphalist[$alpha_index].$row_index;
				foreach ($brands as $brand)
				{
					$sheet->setCellValue ($alphalist[$alpha_index].$row_index, '('.$brand->id.') '.$brand->name);

					$row_index++;
				}
				$end_range = $alphalist[$alpha_index].($row_index-1);

				$spreadsheet->addNamedRange( new NamedRange('BRAND', $spreadsheet->getActiveSheet(), $start_range.':'.$end_range) );

				$yesnolist = ["Yes", "No"];

				if(count($yesnolist) > $max_row_count)
				{
					throw new \yii\web\ForbiddenHttpException(Yii::t('app', 'Too many rows!')); 
				}

				if(count($yesnolist) > ($max_row_count - $row_index))
				{
					$alpha_index++;
					if($alpha_index > $max_col_count)
					{
						throw new \yii\web\ForbiddenHttpException(Yii::t('app', 'Too many columns!')); 
					}
					$row_index = 1;
				}

				$start_range = $alphalist[$alpha_index].$row_index;
				foreach ($yesnolist as $row)
				{
					$sheet->setCellValue ($alphalist[$alpha_index].$row_index, $row);

					$row_index++;
				}
				$end_range = $alphalist[$alpha_index].($row_index-1);

				$spreadsheet->addNamedRange( new NamedRange('YESNOLIST', $spreadsheet->getActiveSheet(), $start_range.':'.$end_range) );
				
				$spreadsheet->getActiveSheet()->setSheetState(Worksheet::SHEETSTATE_HIDDEN);

				$sheet = $spreadsheet->setActiveSheetIndex(0);

				$sheet->setCellValue ('A1', 'CATEGORY');
				$sheet->setCellValue ('B1', 'SUB-CATEGORY');
				$sheet->setCellValue ('C1', 'SUB-SUB-CATEGORY');
				$sheet->setCellValue ('D1', 'PRODUCT-NAME');
				$sheet->setCellValue ('E1', 'DESCRIPTION');
				$sheet->setCellValue ('F1', 'PRODUCT-BRAND');
				$sheet->setCellValue ('G1', 'IS-DIGITAL');
				$sheet->setCellValue ('H1', 'IS-LICENSE-KEY-CODE');
				$sheet->setCellValue ('I1', 'ACTIVE');
				$sheet->setCellValue ('J1', 'IMAGE-URL-1');
				$sheet->setCellValue ('K1', 'IMAGE-URL-2');
				$sheet->setCellValue ('L1', 'IMAGE-URL-3');
				$sheet->setCellValue ('M1', 'IMAGE-URL-4');
				$sheet->setCellValue ('N1', 'UPC-CODE');

				for ($i = 2; $i <= 1001; $i++)
				{
					$validation = $spreadsheet->getActiveSheet()->getCell('A'.$i)->getDataValidation();
					$validation->setType(DataValidation::TYPE_LIST );
					$validation->setErrorStyle(DataValidation::STYLE_INFORMATION );
					$validation->setAllowBlank(false);
					$validation->setShowDropDown(true);
					$validation->setFormula1('=CATEGORY');

					$validation = $spreadsheet->getActiveSheet()->getCell('B'.$i)->getDataValidation();
					$validation->setType(DataValidation::TYPE_LIST );
					$validation->setErrorStyle(DataValidation::STYLE_INFORMATION );
					$validation->setAllowBlank(false);
					$validation->setShowDropDown(true);
					$validation->setFormula1('=INDIRECT(CONCATENATE("SUBCATEGORY", MID(A'.$i.', FIND("(", A'.$i.')+1, FIND(")", A'.$i.')-2)))');

					$validation = $spreadsheet->getActiveSheet()->getCell('C'.$i)->getDataValidation();;
					$validation->setType(DataValidation::TYPE_LIST );
					$validation->setErrorStyle(DataValidation::STYLE_INFORMATION );
					$validation->setAllowBlank(false);
					$validation->setShowDropDown(true);
					$validation->setFormula1('=INDIRECT(CONCATENATE("SUBSUBCATEGORY", MID(B'.$i.', FIND("(", B'.$i.')+1, FIND(")", B'.$i.')-2)))');

					$validation = $spreadsheet->getActiveSheet()->getCell('F'.$i)->getDataValidation();
					$validation->setType(DataValidation::TYPE_LIST );
					$validation->setErrorStyle(DataValidation::STYLE_INFORMATION );
					$validation->setAllowBlank(false);
					$validation->setShowDropDown(true);
					$validation->setFormula1('=BRAND');

					$validation = $spreadsheet->getActiveSheet()->getCell('G'.$i)->getDataValidation();
					$validation->setType(DataValidation::TYPE_LIST );
					$validation->setErrorStyle(DataValidation::STYLE_INFORMATION );
					$validation->setAllowBlank(false);
					$validation->setShowDropDown(true);
					$validation->setFormula1('=YESNOLIST');

					$validation = $spreadsheet->getActiveSheet()->getCell('H'.$i)->getDataValidation();
					$validation->setType(DataValidation::TYPE_LIST );
					$validation->setErrorStyle(DataValidation::STYLE_INFORMATION );
					$validation->setAllowBlank(false);
					$validation->setShowDropDown(true);
					$validation->setFormula1('=YESNOLIST');

					$validation = $spreadsheet->getActiveSheet()->getCell('I'.$i)->getDataValidation();
					$validation->setType(DataValidation::TYPE_LIST );
					$validation->setErrorStyle(DataValidation::STYLE_INFORMATION );
					$validation->setAllowBlank(false);
					$validation->setShowDropDown(true);
					$validation->setFormula1('=YESNOLIST');
				}

				$writer = new Xlsx($spreadsheet);
				$file_name = 'temp/'.uniqid(Yii::$app->user->identity->id);
				$writer->save($file_name);

				return Yii::$app->response->sendFile($file_name, 'product_create_template.xlsx');
			}
		}
		catch (\Exception $e)
		{
			Yii::$app->session->setFlash('error', Yii::t('app', 'Failed to get template!'));
		}

		try
		{
			if(isset($_REQUEST['bulk_upload']))
			{

				$connection = Yii::$app->db;
				$transaction = $connection->beginTransaction();
				
				$spreadsheet_inventory = MulteModel::initiateInventoryCreateTemplate();
				$inventory_row_index = 2;	// As header takes first row

				$spreadsheet_out = new Spreadsheet();

				$reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
				$spreadsheet_in = $reader->load($_FILES['product_file']['tmp_name']);

				$clonedWorksheet = clone $spreadsheet_in->getSheet(1);
				$spreadsheet_out->addExternalSheet($clonedWorksheet);

				$sheet = $spreadsheet_out->setActiveSheetIndex(0);

				$sheetData = $spreadsheet_in->getSheet(0)->toArray();
				
				$count = 0;
				$row_index = 1;
				$rejects = false;

				foreach ($sheetData as $row)
				{
					$count++;
					
					if($count == 1)
					{
						$spreadsheet_out->getActiveSheet()->fromArray($row, NULL, 'A'.$row_index++);
						continue;
					}

					$product = new Product();

					$product->category_id = getCategoryIDNumber($row[0]);
					$product->sub_category_id = getSubCategoryIDNumber($product->category_id, $row[1]);
					$product->sub_subcategory_id = getSubSubCategoryIDNumber($product->sub_category_id, $row[2]);
					$product->name = $row[3];
					$product->description = $row[4];
					$product->brand_id = getBrandIDNumber($row[5]);
					$product->digital = getYesNoInd($row[6]);
					$product->license_key_code = getYesNoInd($row[7]);
					$product->active = getYesNoInd($row[8]);
					$product->upc_code = floatval($row[13]);
					$product->added_by_id = Yii::$app->user->identity->id;
					$product->added_at = time();

					if(!validateInputProduct($product->category_id, $product->sub_category_id, $product->sub_subcategory_id) || !$product->save())
					{
						//print_r($product->getErrors());exit;
						$rejects = true;

						// Write to reject file
						$spreadsheet_out->getActiveSheet()->fromArray($row, NULL, 'A'.$row_index);
						$i = $row_index;
						$validation = $spreadsheet_out->getActiveSheet()->getCell('A'.$i)->getDataValidation();
						$validation->setType(DataValidation::TYPE_LIST );
						$validation->setErrorStyle(DataValidation::STYLE_INFORMATION );
						$validation->setAllowBlank(false);
						$validation->setShowDropDown(true);
						$validation->setFormula1('=CATEGORY');

						$validation = $spreadsheet_out->getActiveSheet()->getCell('B'.$i)->getDataValidation();
						$validation->setType(DataValidation::TYPE_LIST );
						$validation->setErrorStyle(DataValidation::STYLE_INFORMATION );
						$validation->setAllowBlank(false);
						$validation->setShowDropDown(true);
						$validation->setFormula1('=INDIRECT(CONCATENATE("SUBCATEGORY", MID(A'.$i.', FIND("(", A'.$i.')+1, FIND(")", A'.$i.')-2)))');

						$validation = $spreadsheet_out->getActiveSheet()->getCell('C'.$i)->getDataValidation();;
						$validation->setType(DataValidation::TYPE_LIST );
						$validation->setErrorStyle(DataValidation::STYLE_INFORMATION );
						$validation->setAllowBlank(false);
						$validation->setShowDropDown(true);
						$validation->setFormula1('=INDIRECT(CONCATENATE("SUBSUBCATEGORY", MID(B'.$i.', FIND("(", B'.$i.')+1, FIND(")", B'.$i.')-2)))');

						$validation = $spreadsheet_out->getActiveSheet()->getCell('F'.$i)->getDataValidation();
						$validation->setType(DataValidation::TYPE_LIST );
						$validation->setErrorStyle(DataValidation::STYLE_INFORMATION );
						$validation->setAllowBlank(false);
						$validation->setShowDropDown(true);
						$validation->setFormula1('=BRAND');

						$validation = $spreadsheet_out->getActiveSheet()->getCell('G'.$i)->getDataValidation();
						$validation->setType(DataValidation::TYPE_LIST );
						$validation->setErrorStyle(DataValidation::STYLE_INFORMATION );
						$validation->setAllowBlank(false);
						$validation->setShowDropDown(true);
						$validation->setFormula1('=YESNOLIST');

						$validation = $spreadsheet_out->getActiveSheet()->getCell('H'.$i)->getDataValidation();
						$validation->setType(DataValidation::TYPE_LIST );
						$validation->setErrorStyle(DataValidation::STYLE_INFORMATION );
						$validation->setAllowBlank(false);
						$validation->setShowDropDown(true);
						$validation->setFormula1('=YESNOLIST');

						$validation = $spreadsheet_out->getActiveSheet()->getCell('I'.$i)->getDataValidation();
						$validation->setType(DataValidation::TYPE_LIST );
						$validation->setErrorStyle(DataValidation::STYLE_INFORMATION );
						$validation->setAllowBlank(false);
						$validation->setShowDropDown(true);
						$validation->setFormula1('=YESNOLIST');

						$row_index++;
					}
					else
					{
						/* Add Images */
						if ($row[9] && filter_var($row[9], FILTER_VALIDATE_URL))
						{
							MulteModel::getAndAddFileToProduct($row[9], $product->id);
						}

						if ($row[10] && filter_var($row[10], FILTER_VALIDATE_URL))
						{
							MulteModel::getAndAddFileToProduct($row[10], $product->id);
						}

						if ($row[11] && filter_var($row[11], FILTER_VALIDATE_URL))
						{
							MulteModel::getAndAddFileToProduct($row[11], $product->id);
						}

						if ($row[12] && filter_var($row[12], FILTER_VALIDATE_URL))
						{
							MulteModel::getAndAddFileToProduct($row[12], $product->id);
						}

						/* End Add Images */

						if($product->active)
						{
							$spreadsheet_inventory = MulteModel::writeInventoryCreateTemplateRecord($spreadsheet_inventory, $product, 0, $inventory_row_index);			
							
							$inventory_row_index++;
						}
					}
				}

				$writer = new Xlsx($spreadsheet_inventory);
				$inventory_file_name = 'public/'.uniqid(Yii::$app->user->identity->id).".xlsx";
				$writer->save($inventory_file_name);
				
				if ($rejects)
				{
					$writer = new Xlsx($spreadsheet_out);
					$file_name = 'public/'.uniqid(Yii::$app->user->identity->id).".xlsx";
					$writer->save($file_name);

					Yii::$app->session->setFlash('warning', Yii::t('app', 'Few records are rejected - Please download rejected records').' <a href="'.Url::base()."/".$file_name.'" target="_blank">'.Yii::t('app', 'here').'</a>');
					Yii::$app->session->setFlash('info', Yii::t('app', 'Please download inventory template').' <a href="'.Url::base()."/".$inventory_file_name.'" target="_blank">'.Yii::t('app', 'here').'</a>');
				}
				else
				{
					Yii::$app->session->setFlash('success', Yii::t('app', 'Records uploaded successfully!'));
					Yii::$app->session->setFlash('info', Yii::t('app', 'Please download inventory template').' <a href="'.Url::base()."/".$inventory_file_name.'" target="_blank">'.Yii::t('app', 'here').'</a>');
				}

				$transaction->commit();
			}
		}
		catch (\Exception $e)
		{
			$transaction->rollback();
			Yii::$app->session->setFlash('error', $e->getMessage());
		}

        return $this->render('bulk-upload-products');
    }

	public function actionBulkUploadInventories()
    {
		if(!Yii::$app->user->can('Inventory.Create')){
			throw new \yii\web\ForbiddenHttpException(Yii::t('app', 'You dont have permissions to view this page.'));
		}
		
		try
		{
			$connection = Yii::$app->db;

			if(isset($_REQUEST['bulk_create_inventory']))
			{		
				$transaction = $connection->beginTransaction();
				
				$spreadsheet_out = new Spreadsheet();

				$reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
				$spreadsheet_in = $reader->load($_FILES['inventory_file']['tmp_name']);

				$clonedWorksheet = clone $spreadsheet_in->getSheet(1);
				$spreadsheet_out->addExternalSheet($clonedWorksheet);

				$sheet = $spreadsheet_out->setActiveSheetIndex(0);

				$sheetData = $spreadsheet_in->getSheet(0)->toArray();
				
				$count = 0;
				$row_index = 1;
				$rejects = false;

				foreach ($sheetData as $row)
				{
					$rejected_rec = false;
					$count++;
					
					if($count == 1)
					{
						$spreadsheet_out->getActiveSheet()->fromArray($row, NULL, 'A'.$row_index++);
						continue;
					}

					$inventory = new Inventory();

					$inventory->product_id = $row[0];
					$inventory->product_name = Product::findOne($row[0])->name;
					$inventory->vendor_id = Yii::$app->user->identity->entity_id;
					$inventory->stock = $row[5];
					$inventory->price_type = getPriceType($row[6]);
					$inventory->price = $row[7];
					$inventory->discount_type = getDiscountType($row[8]);
					$inventory->discount = $row[9];
					$inventory->shipping_cost = $row[10];
					$inventory->slab_discount_ind = getYesNoInd($row[11]);
					$inventory->slab_discount_type = getDiscountType($row[12]);
					$inventory->slab_1_range = $row[13];
					$inventory->slab_1_discount = $row[14];
					$inventory->slab_2_range = $row[15];
					$inventory->slab_2_discount = $row[16];
					$inventory->slab_3_range = $row[17];
					$inventory->slab_3_discount =$row[18];
					$inventory->slab_4_range = $row[19];
					$inventory->slab_4_discount = $row[20];

					$inventory->length = $row[22];
					$inventory->width = $row[23];
					$inventory->height = $row[24];
					$inventory->weight = $row[25];
					$inventory->warranty = $row[26];

					$inventory->active = 1; //Active

					$inventory->added_by_id = Yii::$app->user->identity->id;
					$inventory->added_at = time();
					
					//search_tags = $row[21];
					
					$col_index = 27;

					$product_attributes = ProductAttributes::find()->where(['parent_id' => $inventory->product->sub_subcategory_id])->all();
					
					$attributes_array = [];
					$attributes_price_array = [];

					if($product_attributes)
					{
						for ($i = 0; $i < count($product_attributes); $i++)
						{
							if($product_attributes[$i]['fixed'] == 1)
							{
								$attribute_values = ProductAttributeValues::findOne($product_attributes[$i]['fixed_id']);

								if(!in_array($row[$col_index+$i], Json::decode($attribute_values->values)))
								{
									$rejected_rec = true;
									break;
								}
							}
							else
							{
								if($row[$col_index+$i] != $product_attributes[$i]['name'])
								{
									$rejected_rec = true;
									break;
								}
							}

							if(!is_numeric($row[$col_index+$i+1]))
							{
								$rejected_rec = true;
								break;
							}

							array_push($attributes_array, $row[$col_index+$i]);
							array_push($attributes_price_array, (string)$row[$col_index+$i+1]);

							$col_index++;
						}

						$inventory->attribute_values = Json::encode($attributes_array);
						$inventory->attribute_price = Json::encode($attributes_price_array);
					}
					else
					{
						$inventory->attribute_values = '';
						$inventory->attribute_price = '';
					}

					if(!$rejected_rec)
					{
						if(!$inventory->save())
						{
							$rejected_rec = true;
						}
						else if($row[27])
						{
							/* Create Inventory Details */
							if($product_attributes)
							{
								for ($i = 0; $i < count($product_attributes); $i++)
								{
									$inventory_details = new InventoryDetails;

									$inventory_details->inventory_id = $inventory->id;
									$inventory_details->attribute_id = $product_attributes[$i]['id'];
									$inventory_details->attribute_value = $attributes_array[$i];
									$inventory_details->attribute_price = $attributes_price_array[$i];

									if(!$inventory_details->save())
									{
										throw new \Exception (Yii::t('app', 'Failed to save attribute details!'));
									}
								}
							}
						}
						
						if($row[21] && !$rejected_rec)
						{
							/* Begin creating Search Tags */
							$tags_array = explode(',', $row[21]);

							foreach($tags_array as $tagrow)
							{
								$inventorytags = new InventoryTags;
								$inventorytags->inventory_id = $inventory->id;

								$tag = Tags::find()->where(['tag'=> strtolower(trim($tagrow))])->one();

								if($tag)
								{
									$inventorytags->tag_id = $tag->id;
								}
								else
								{
									$newtag = new Tags;
									$newtag->tag = strtolower(trim($tagrow));
									if(!$newtag->save())
									{
										throw new \Exception(Yii::t('app', 'Error trying to save tags!'));
									}

									$inventorytags->tag_id = $newtag->id;
								}

								if(!$inventorytags->save())
								{
									throw new \Exception(Yii::t('app', 'Error trying to save tags!'));
								}
							}
						}
					}
					

					if ($rejected_rec)
					{
						//print_r($product->getErrors());exit;
						$rejects = true;

						// Write to reject file
						$spreadsheet_out->getActiveSheet()->fromArray($row, NULL, 'A'.$row_index);

						$validation = $spreadsheet_out->getActiveSheet()->getCell('A'.$row_index)->getDataValidation();
						$validation->setErrorStyle(DataValidation::STYLE_WARNING );
						$validation->setAllowBlank(false);
						$validation->setShowInputMessage(true);
						$validation->setPrompt(Yii::t('app', 'Please Do not change this value!'));

						$validation = $spreadsheet_out->getActiveSheet()->getCell('G'.$row_index)->getDataValidation();
						$validation->setType(DataValidation::TYPE_LIST );
						$validation->setErrorStyle(DataValidation::STYLE_INFORMATION );
						$validation->setAllowBlank(false);
						$validation->setShowDropDown(true);
						$validation->setFormula1('=PRICETYPELIST');

						$validation = $spreadsheet_out->getActiveSheet()->getCell('I'.$row_index)->getDataValidation();
						$validation->setType(DataValidation::TYPE_LIST );
						$validation->setErrorStyle(DataValidation::STYLE_INFORMATION );
						$validation->setAllowBlank(false);
						$validation->setShowDropDown(true);
						$validation->setFormula1('=DISCOUNTTYPELIST');

						$validation = $spreadsheet_out->getActiveSheet()->getCell('L'.$row_index)->getDataValidation();
						$validation->setType(DataValidation::TYPE_LIST );
						$validation->setErrorStyle(DataValidation::STYLE_INFORMATION );
						$validation->setAllowBlank(false);
						$validation->setShowDropDown(true);
						$validation->setFormula1('=YESNOLIST');

						$validation = $spreadsheet_out->getActiveSheet()->getCell('M'.$row_index)->getDataValidation();
						$validation->setType(DataValidation::TYPE_LIST );
						$validation->setErrorStyle(DataValidation::STYLE_INFORMATION );
						$validation->setAllowBlank(false);
						$validation->setShowDropDown(true);
						$validation->setFormula1('=DISCOUNTTYPELIST');

						$validation = $spreadsheet_out->getActiveSheet()->getCell('V'.$row_index)->getDataValidation();
						$validation->setErrorStyle(DataValidation::STYLE_INFORMATION );
						$validation->setAllowBlank(false);
						$validation->setShowInputMessage(true);
						$validation->setPrompt(Yii::t('app', 'Enter comma separated tags'));

						$invalphalist = ['AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM', 'AN', 'AO', 'AP', 'AQ','AR', 'AS', 'AT', 'AU', 'AV', 'AW', 'AX', 'AY', 'AZ', 'BA', 'BB', 'BC', 'BD', 'BE'];
						$invalpha_index = 0;

						if($product_attributes)
						{
							$start_alpha = $invalpha_index;
							foreach ($product_attributes as $attribute_row)
							{
								if($attribute_row->fixed == 1)
								{
									$validation = $spreadsheet_out->getActiveSheet()->getCell($invalphalist[$invalpha_index].$row_index)->getDataValidation();
									$validation->setType(DataValidation::TYPE_LIST );
									$validation->setErrorStyle(DataValidation::STYLE_INFORMATION );
									$validation->setAllowBlank(false);
									$validation->setShowDropDown(true);
									$validation->setShowInputMessage(true);
									$validation->setPromptTitle(Yii::t('app', 'Select Attribute Name'));
									$validation->setPrompt($attribute_row->name);
									$validation->setFormula1('=ATTRIBUTELIST'.$attribute_row->fixed_id);
								}
								else
								{
									$validation = $spreadsheet_out->getActiveSheet()->getCell($invalphalist[$invalpha_index].$row_index)->getDataValidation();
									$validation->setErrorStyle(DataValidation::STYLE_INFORMATION );
									$validation->setAllowBlank(false);
									$validation->setShowInputMessage(true);
									$validation->setPromptTitle(Yii::t('app', 'Fill Attribute Name'));
									$validation->setPrompt($attribute_row->name);
								}

								$invalpha_index++;

								$validation = $spreadsheet_out->getActiveSheet()->getCell($invalphalist[$invalpha_index].$row_index)->getDataValidation();
								$validation->setErrorStyle(DataValidation::STYLE_INFORMATION );
								$validation->setAllowBlank(false);
								$validation->setShowInputMessage(true);
								$validation->setPromptTitle(Yii::t('app', 'Fill Attribute Price'));
								$validation->setPrompt($attribute_row->name);

								$invalpha_index++;
							}
							$end_alpha = $invalpha_index-1;

							$spreadsheet_out->getActiveSheet()
											->getStyle($invalphalist[$start_alpha].$row_index.':'.$invalphalist[$end_alpha].$row_index)
											->getFill()
											->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
											->getStartColor()->setARGB('FFFF0000');
						}

						$row_index++;
					}
				}

				if ($rejects)
				{
					$writer = new Xlsx($spreadsheet_out);
					$file_name = 'public/'.uniqid(Yii::$app->user->identity->id).".xlsx";
					$writer->save($file_name);

					Yii::$app->session->setFlash('warning', Yii::t('app', 'Few records are rejected - Please download rejected records').' <a href="'.Url::base()."/".$file_name.'" target="_blank">'.Yii::t('app', 'here').'</a>');
				}
				else
				{
					Yii::$app->session->setFlash('success', Yii::t('app', 'Records uploaded successfully!'));
				}

				$transaction->commit();
			}

			if(isset($_REQUEST['bulk_update_inventory']))
			{
				$transaction = $connection->beginTransaction();
				
				$spreadsheet_out = new Spreadsheet();

				$reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
				$spreadsheet_in = $reader->load($_FILES['inventory_file']['tmp_name']);

				$clonedWorksheet = clone $spreadsheet_in->getSheet(1);
				$spreadsheet_out->addExternalSheet($clonedWorksheet);

				$sheet = $spreadsheet_out->setActiveSheetIndex(0);

				$sheetData = $spreadsheet_in->getSheet(0)->toArray();
				
				$count = 0;
				$row_index = 1;
				$rejects = false;

				foreach ($sheetData as $row)
				{
					$rejected_rec = false;
					$count++;
					
					if($count == 1)
					{
						$spreadsheet_out->getActiveSheet()->fromArray($row, NULL, 'A'.$row_index++);
						continue;
					}

					$inventory = Inventory::findOne(['id' => $row[0], 'vendor_id' => Yii::$app->user->identity->entity_id]);

					if($inventory)
					{
						$inventory->stock = $row[6];
						$inventory->price_type = getPriceType($row[7]);
						$inventory->price = $row[8];
						$inventory->discount_type = getDiscountType($row[9]);
						$inventory->discount = $row[10];
						$inventory->shipping_cost = $row[11];
						$inventory->slab_discount_ind = getYesNoInd($row[12]);
						$inventory->slab_discount_type = getDiscountType($row[13]);
						$inventory->slab_1_range = $row[14];
						$inventory->slab_1_discount = $row[15];
						$inventory->slab_2_range = $row[16];
						$inventory->slab_2_discount = $row[17];
						$inventory->slab_3_range = $row[18];
						$inventory->slab_3_discount =$row[19];
						$inventory->slab_4_range = $row[20];
						$inventory->slab_4_discount = $row[21];

						$inventory->length = $row[23];
						$inventory->width = $row[24];
						$inventory->height = $row[25];
						$inventory->weight = $row[26];
						$inventory->warranty = $row[27];

						$inventory->updated_at = time();
					
						//search_tags = $row[22];
						
						$col_index = 28;

						$product_attributes = ProductAttributes::find()->where(['parent_id' => $inventory->product->sub_subcategory_id])->all();
						
						$attributes_array = [];
						$attributes_price_array = [];

						if($product_attributes)
						{
							for ($i = 0; $i < count($product_attributes); $i++)
							{
								if($product_attributes[$i]['fixed'] == 1)
								{
									$attribute_values = ProductAttributeValues::findOne($product_attributes[$i]['fixed_id']);

									if(!in_array($row[$col_index+$i], Json::decode($attribute_values->values)))
									{
										$rejected_rec = true;
										break;
									}
								}
								else
								{
									if($row[$col_index+$i] != $product_attributes[$i]['name'])
									{
										$rejected_rec = true;
										break;
									}
								}

								if(!is_numeric($row[$col_index+$i+1]))
								{
									$rejected_rec = true;
									break;
								}

								array_push($attributes_array, $row[$col_index+$i]);
								array_push($attributes_price_array, (string)$row[$col_index+$i+1]);

								$col_index++;
							}

							$inventory->attribute_values = Json::encode($attributes_array);
							$inventory->attribute_price = Json::encode($attributes_price_array);
						}
						else
						{
							$inventory->attribute_values = '';
							$inventory->attribute_price = '';
						}

						if(!$rejected_rec)
						{
							if(!$inventory->save())
							{
								$rejected_rec = true;
							}
							else if($row[28])
							{
								/* Update Inventory Details */
								$col_index = 28;
								$inventory_details = InventoryDetails::find()->where(['inventory_id' => $inventory->id])->all();
								if ($inventory_details)
								{
									$i=0;
									foreach($inventory_details as $inv_row)
									{
										$invDet = InventoryDetails::findOne($inv_row->id);
										$invDet->attribute_price = $row[$col_index + 1];
										if($invDet->attribute_value != $row[$col_index] || !$invDet->save())
										{
											throw new \Exception(Yii::t('app', 'Error trying to update attributes!'));
										}
										$col_index = $col_index + 2;
									}
								}
							}
							
							if($row[22] && !$rejected_rec)
							{
								/* Update Inventory Tags */
								InventoryTags::deleteAll(['=', 'inventory_id', $inventory->id]);

								$tags_array = explode(',', $row[22]);

								foreach($tags_array as $tag_row)
								{
									$inventorytags = new InventoryTags;
									$inventorytags->inventory_id = $inventory->id;

									$tag = Tags::find()->where(['tag'=> strtolower(trim($tagrow))])->one();

									if($tag)
									{
										$inventorytags->tag_id = $tag->id;
									}
									else
									{
										$newtag = new Tags;
										$newtag->tag = strtolower(trim($tag_row));
										if(!$newtag->save())
										{
											throw new \Exception(Yii::t('app', 'Error trying to save tags!'));
										}

										$inventorytags->tag_id = $newtag->id;
									}

									if(!$inventorytags->save())
									{
										throw new \Exception(Yii::t('app', 'Error trying to save tags!'));
									}
								}
							}
						}
					}
					else
					{
						$rejected_rec = true;
					}

					if ($rejected_rec)
					{
						//print_r($product->getErrors());exit;
						$rejects = true;

						// Write to reject file
						$spreadsheet_out->getActiveSheet()->fromArray($row, NULL, 'A'.$row_index);

						$validation = $spreadsheet_out->getActiveSheet()->getCell('A'.$row_index)->getDataValidation();
						$validation->setErrorStyle(DataValidation::STYLE_WARNING );
						$validation->setAllowBlank(false);
						$validation->setShowInputMessage(true);
						$validation->setPrompt(Yii::t('app', 'Please Do not change this value!'));

						$validation = $spreadsheet_out->getActiveSheet()->getCell('H'.$row_index)->getDataValidation();
						$validation->setType(DataValidation::TYPE_LIST );
						$validation->setErrorStyle(DataValidation::STYLE_INFORMATION );
						$validation->setAllowBlank(false);
						$validation->setShowDropDown(true);
						$validation->setFormula1('=PRICETYPELIST');

						$validation = $spreadsheet_out->getActiveSheet()->getCell('J'.$row_index)->getDataValidation();
						$validation->setType(DataValidation::TYPE_LIST );
						$validation->setErrorStyle(DataValidation::STYLE_INFORMATION );
						$validation->setAllowBlank(false);
						$validation->setShowDropDown(true);
						$validation->setFormula1('=DISCOUNTTYPELIST');

						$validation = $spreadsheet_out->getActiveSheet()->getCell('M'.$row_index)->getDataValidation();
						$validation->setType(DataValidation::TYPE_LIST );
						$validation->setErrorStyle(DataValidation::STYLE_INFORMATION );
						$validation->setAllowBlank(false);
						$validation->setShowDropDown(true);
						$validation->setFormula1('=YESNOLIST');

						$validation = $spreadsheet_out->getActiveSheet()->getCell('N'.$row_index)->getDataValidation();
						$validation->setType(DataValidation::TYPE_LIST );
						$validation->setErrorStyle(DataValidation::STYLE_INFORMATION );
						$validation->setAllowBlank(false);
						$validation->setShowDropDown(true);
						$validation->setFormula1('=DISCOUNTTYPELIST');

						$validation = $spreadsheet_out->getActiveSheet()->getCell('W'.$row_index)->getDataValidation();
						$validation->setErrorStyle(DataValidation::STYLE_INFORMATION );
						$validation->setAllowBlank(false);
						$validation->setShowInputMessage(true);
						$validation->setPrompt(Yii::t('app', 'Enter comma separated tags'));

						$invalphalist = ['AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM', 'AN', 'AO', 'AP', 'AQ','AR', 'AS', 'AT', 'AU', 'AV', 'AW', 'AX', 'AY', 'AZ', 'BA', 'BB', 'BC', 'BD', 'BE', 'BF'];
						$invalpha_index = 0;

						if($product_attributes)
						{
							$start_alpha = $invalpha_index;
							foreach ($product_attributes as $attribute_row)
							{
								$validation = $spreadsheet_out->getActiveSheet()->getCell($invalphalist[$invalpha_index].$row_index)->getDataValidation();
								$validation->setErrorStyle(DataValidation::STYLE_INFORMATION );
								$validation->setAllowBlank(false);
								$validation->setShowInputMessage(true);
								$validation->setPrompt(Yii::t('app', 'Attribute Name'));

								$invalpha_index++;

								$validation = $spreadsheet_out->getActiveSheet()->getCell($invalphalist[$invalpha_index].$row_index)->getDataValidation();
								$validation->setErrorStyle(DataValidation::STYLE_INFORMATION );
								$validation->setAllowBlank(false);
								$validation->setShowInputMessage(true);
								$validation->setPrompt(Yii::t('app', 'Attribute Price'));

								$invalpha_index++;
							}
							$end_alpha = $invalpha_index-1;

							$spreadsheet_out->getActiveSheet()
											->getStyle($invalphalist[$start_alpha].$row_index.':'.$invalphalist[$end_alpha].$row_index)
											->getFill()
											->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
											->getStartColor()->setARGB('FFFF0000');
						}

						$row_index++;
					}
				}

				if ($rejects)
				{
					$writer = new Xlsx($spreadsheet_out);
					$file_name = 'public/'.uniqid(Yii::$app->user->identity->id).".xlsx";
					$writer->save($file_name);

					Yii::$app->session->setFlash('warning', Yii::t('app', 'Few records are rejected - Please download rejected records').' <a href="'.Url::base()."/".$file_name.'" target="_blank">'.Yii::t('app', 'here').'</a>');
				}
				else
				{
					Yii::$app->session->setFlash('success', Yii::t('app', 'Records uploaded successfully!'));
				}

				$transaction->commit();
			}
		}
		catch (\Exception $e)
		{
			$transaction->rollback();
			Yii::$app->session->setFlash('error', $e->getMessage());
		}

		return $this->render('bulk-upload-inventories');
	}

	public function actionBulkUploadCategories()
    {
		if(!Yii::$app->user->can('ProductCategory.Create')){
			throw new \yii\web\ForbiddenHttpException(Yii::t('app', 'You dont have permissions to view this page.'));
		}
		
		if(isset($_REQUEST['bulk_upload']))
		{
			try
			{
				$connection = Yii::$app->db;

				$spreadsheet_out = new Spreadsheet();

				$reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
				$spreadsheet_in = $reader->load($_FILES['product_file']['tmp_name']);

				$sheet = $spreadsheet_out->setActiveSheetIndex(0);

				$sheetData = $spreadsheet_in->getSheet(0)->toArray();
				
				$count = 0;
				$row_index = 1;
				$rejects = false;

				foreach ($sheetData as $row)
				{
					$count++;
					
					if($count == 1)
					{
						$spreadsheet_out->getActiveSheet()->fromArray($row, NULL, 'A'.$row_index++);
						continue;
					}
					
					if(trim($row[0]) == '' || trim($row[2]) == '' || trim($row[4]) == '')
					{
						$rejects = true;
						// Write to reject file
						$spreadsheet_out->getActiveSheet()->fromArray($row, NULL, 'A'.$row_index++);
						continue;
					}

					$transaction = $connection->beginTransaction();

					$category_id = ProductCategory::findOne(['name' => $row[0]])->id;
					if(!$category_id)
					{
						$product_category = new ProductCategory();
						$product_category->name = $row[0];
						$product_category->active = 1;
						$product_category->description = $row[1];
						$product_category->added_by_id = Yii::$app->user->identity->id;
						
						if(!$product_category->save())
						{
							$rejects = true;
							$transaction->rollback();
							// Write to reject file
							$spreadsheet_out->getActiveSheet()->fromArray($row, NULL, 'A'.$row_index++);
							continue;
						}

						$category_id = $product_category->id;
					}
				
					$sub_category_id = ProductSubCategory::findOne(['name' => $row[2], 'parent_id' => $category_id])->id;
					if(!$sub_category_id)
					{
						$product_sub_category = new ProductSubCategory();
						$product_sub_category->parent_id = $category_id;
						$product_sub_category->name = $row[2];
						$product_sub_category->active = 1;
						$product_sub_category->description = $row[3];
						$product_sub_category->added_by_id = Yii::$app->user->identity->id;
						
						if(!$product_sub_category->save())
						{
							$rejects = true;
							$transaction->rollback();
							// Write to reject file
							$spreadsheet_out->getActiveSheet()->fromArray($row, NULL, 'A'.$row_index++);
							continue;
						}

						$sub_category_id = $product_sub_category->id;
					}

					$sub_subcategory_id = ProductSubSubCategory::findOne(['name' => $row[4], 'parent_id' => $sub_category_id])->id;
					if(!$sub_subcategory_id)
					{
						$product_sub_subcategory = new ProductSubSubCategory();
						$product_sub_subcategory->parent_id = $sub_category_id;
						$product_sub_subcategory->name = $row[4];
						$product_sub_subcategory->active = 1;
						$product_sub_subcategory->description = $row[5];
						$product_sub_subcategory->tax_ind = 0;
						$product_sub_subcategory->return_window = $row[6];
						$product_sub_subcategory->added_by_id = Yii::$app->user->identity->id;
						
						if(!$product_sub_subcategory->save())
						{
							$rejects = true;
							$transaction->rollback();
							// Write to reject file
							$spreadsheet_out->getActiveSheet()->fromArray($row, NULL, 'A'.$row_index++);
							continue;
						}
					}

					$transaction->commit();
				}
				
				if ($rejects)
				{
					$writer = new Xlsx($spreadsheet_out);
					$file_name = 'public/'.uniqid(Yii::$app->user->identity->id).".xlsx";
					$writer->save($file_name);

					Yii::$app->session->setFlash('warning', Yii::t('app', 'Few records are rejected - Please download rejected records').' <a href="'.Url::base()."/".$file_name.'" target="_blank">'.Yii::t('app', 'here').'</a>');
				}
				else
				{
					Yii::$app->session->setFlash('success', Yii::t('app', 'Records uploaded successfully!'));
				}			
			}
			catch (\Exception $e)
			{
				Yii::$app->session->setFlash('error', $e->getMessage());
			}
		}

        return $this->render('bulk-upload-categories');
    }

	public function actionBulkUploadCombined()
    {
		if(!Yii::$app->user->can('Inventory.Create')){
			throw new \yii\web\ForbiddenHttpException(Yii::t('app', 'You dont have permissions to view this page.'));
		}
		if(!Yii::$app->user->can('Product.Create')){
			throw new \yii\web\ForbiddenHttpException(Yii::t('app', 'You dont have permissions to view this page.'));
		}

		if(isset($_REQUEST['bulk_upload_prd_inv']))
		{
			try
			{
				$connection = Yii::$app->db;

				$spreadsheet_out = new Spreadsheet();

				$reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
				$spreadsheet_in = $reader->load($_FILES['prd_inv_file']['tmp_name']);

				$sheet = $spreadsheet_out->setActiveSheetIndex(0);

				$sheetData = $spreadsheet_in->getSheet(0)->toArray();
				
				$count = 0;
				$row_index = 1;
				$rejects = false;

				foreach ($sheetData as $row)
				{
					$count++;
					
					if($count == 1)
					{
						array_unshift($row, Yii::t('app', 'REJECTION-REASON'));
						$spreadsheet_out->getActiveSheet()->fromArray($row, NULL, 'A'.$row_index++);
						continue;
					}

					$transaction = $connection->beginTransaction();

					try
					{
						// Product Create/Update Start
						if(trim($row[0]) == '' || trim($row[1]) == '' || trim($row[2]) == '' || trim($row[4]) == '')
						{
							$transaction->rollback();
							$rejects = true;
							// Write to reject file
							array_unshift($row, Yii::t('app', 'One of the mandatory fields is blank'));
							$spreadsheet_out->getActiveSheet()->fromArray($row, NULL, 'A'.$row_index++);
							continue;
						}

						$category_id = ProductCategory::findOne(['name' => $row[0]])->id;
						if(!$category_id)
						{
							$transaction->rollback();
							$rejects = true;
							// Write to reject file
							array_unshift($row, Yii::t('app', 'Unable to get Category ID'));
							$spreadsheet_out->getActiveSheet()->fromArray($row, NULL, 'A'.$row_index++);
							continue;
						}
					
						$sub_category_id = ProductSubCategory::findOne(['name' => $row[1], 'parent_id' => $category_id])->id;
						if(!$sub_category_id)
						{
							$transaction->rollback();
							$rejects = true;
							// Write to reject file
							array_unshift($row, Yii::t('app', 'Unable to get Sub Category ID'));
							$spreadsheet_out->getActiveSheet()->fromArray($row, NULL, 'A'.$row_index++);
							continue;
						}

						$sub_subcategory_id = ProductSubSubCategory::findOne(['name' => $row[2], 'parent_id' => $sub_category_id])->id;
						if(!$sub_subcategory_id)
						{
							$transaction->rollback();
							$rejects = true;
							// Write to reject file
							array_unshift($row, Yii::t('app', 'Unable to get Sub SubCategory ID'));
							$spreadsheet_out->getActiveSheet()->fromArray($row, NULL, 'A'.$row_index++);
							continue;
						}

						$upc_code = floatval($row[3]);

						if($row[3] && $upc_code == 0)
						{
							$transaction->rollback();
							$rejects = true;
							// Write to reject file
							array_unshift($row, Yii::t('app', 'UPC Code invalid'));
							$spreadsheet_out->getActiveSheet()->fromArray($row, NULL, 'A'.$row_index++);
							continue;
						}

						/* Check for Product Brand Existance - if not present then create it */
						if($row[6])
						{
							$brand = ProductBrand::findOne(['name' => $row[6]]);

							if(!$brand)
							{
								// Create new brand
								$brand = new ProductBrand();
								$brand->name = $row[6];
								$brand->active = 1;
								$brand->added_by_id = Yii::$app->user->identity->id;

								if($brand->save())
								{
									// Add Brand Image
									if($row[7] && filter_var($row[7], FILTER_VALIDATE_URL))
									{
										MulteModel::getAndAddFileToBrand($row[7], $brand->id);
									}
								}
								else
								{
									$transaction->rollback();
									$rejects = true;
									// Write to reject file
									array_unshift($row, Yii::t('app', 'Unable to save brand'));
									$spreadsheet_out->getActiveSheet()->fromArray($row, NULL, 'A'.$row_index++);
									continue;
								}
							}
							else
							{
								// Update Brand Image - Not doing as of now!
							}
						}

						$create_product = false;

						if($row[3]) // UPC Code
						{
							$product = Product::findOne(['upc_code' => $upc_code, 'added_by_id' => Yii::$app->user->identity->id]);

							if(!$product)
							{
								$create_product = true;
							}
						}
						else
						{
							$product = Product::findOne(['sub_subcategory_id' => $sub_subcategory_id, 'name' => $row[4], 'added_by_id' => Yii::$app->user->identity->id]);

							if(!$product)
							{
								$create_product = true;
							}
						}

						if($create_product)
						{
							// Create New Product Here
							$product = new Product();

							$product->category_id = $category_id;
							$product->sub_category_id = $sub_category_id;
							$product->sub_subcategory_id = $sub_subcategory_id;
							$product->name = $row[4];
							$product->description = $row[5];
							$product->brand_id = $brand->id;
							$product->digital = 0; // No digital products allowed from bulk upload combined menu
							$product->license_key_code = 0; // No digital products allowed from bulk upload combined menu
							$product->active = 1; // By Default Active
							$product->upc_code = $upc_code;
							$product->added_by_id = Yii::$app->user->identity->id;
							$product->added_at = time();

							if($product->save())
							{
								/* Add Images */
								if ($row[8] && filter_var($row[8], FILTER_VALIDATE_URL))
								{
									MulteModel::getAndAddFileToProduct($row[8], $product->id);
								}

								if ($row[9] && filter_var($row[9], FILTER_VALIDATE_URL))
								{
									MulteModel::getAndAddFileToProduct($row[9], $product->id);
								}

								if ($row[10] && filter_var($row[10], FILTER_VALIDATE_URL))
								{
									MulteModel::getAndAddFileToProduct($row[10], $product->id);
								}

								if ($row[11] && filter_var($row[11], FILTER_VALIDATE_URL))
								{
									MulteModel::getAndAddFileToProduct($row[11], $product->id);
								}
								/* End Add Images */
							}
							else
							{
								$transaction->rollback();
								$rejects = true;
								// Write to reject file
								array_unshift($row, Yii::t('app', 'Unable to create product'));
								$spreadsheet_out->getActiveSheet()->fromArray($row, NULL, 'A'.$row_index++);
								continue;
							}
						}
						else
						{
							// Update Existing Product
							$product->description = $row[5];
							$product->brand_id = $brand->id;
							$product->digital = 0; // No digital products allowed from bulk upload combined menu
							$product->license_key_code = 0; // No digital products allowed from bulk upload combined menu
							$product->active = 1; // By Default Active
							$product->upc_code = $upc_code;
							$product->updated_at = time();

							if(!$product->save())
							{
								$transaction->rollback();
								$rejects = true;
								// Write to reject file
								array_unshift($row, Yii::t('app', 'Unable to update product'));
								$spreadsheet_out->getActiveSheet()->fromArray($row, NULL, 'A'.$row_index++);
								continue;
							}
						}
						// Product Create/Update End

						// Inventory Create/Update Start
						$create_inventory = false;

						if($create_product)
						{
							$create_inventory = true;
						}
						else
						{
							// Check if inventory already exists
							$product_attributes = ProductAttributes::find()->where(['parent_id' => $sub_subcategory_id])->all();

							if($product_attributes)
							{
								$attributes_count = count($product_attributes);
							}
							else
							{
								$attributes_count = 0;
							}
						}

						$attributes_array = [];
						$attributes_price_array = [];
						$error = false;

						if($attributes_count > 0)
						{
							for ($i = 0, $j = 0; $i < $attributes_count; $i++)
							{
								if($row[34+$j] && ($row[34+$j+1] == '0' || $row[34+$j+1]))
								{
									if($product_attributes[$i]['fixed'] == 1)
									{
										$product_attribute_values = ProductAttributeValues::findOne($product_attributes[$i]['fixed_id']);
										$chk_arr = Json::decode($product_attribute_values->values);

										if(!in_array($row[34+$j], $chk_arr))
										{
											$error = true;
											break;
										}
									}
									array_push($attributes_array, (string)$row[34+$j]);
									array_push($attributes_price_array, (string)floatval($row[34+$j+1]));
								}
								else
								{
									$error = true;
									break;
								}
								
								$j = $j + 2;
							}

							if($error)
							{
								$transaction->rollback();
								$rejects = true;
								// Write to reject file
								array_unshift($row, Yii::t('app', 'Error processing product attributes'));
								$spreadsheet_out->getActiveSheet()->fromArray($row, NULL, 'A'.$row_index++);
								continue;
							}

							$json_attributes_array = Json::encode($attributes_array);
							
							$inventory = Inventory::findOne(['product_id' => $product->id, 'vendor_id' => Yii::$app->user->identity->entity_id, 'attribute_values' => $json_attributes_array]);

							if(!$inventory)
							{
								$create_inventory = true;
							}
						}
						else
						{
							$inventory = Inventory::findOne(['product_id' => $product->id, 'vendor_id' => Yii::$app->user->identity->entity_id, 'attribute_values' => NULL]);

							if(!$inventory)
							{
								$create_inventory = true;
							}
						}

						if($create_inventory)
						{
							$inventory = new Inventory();

							$inventory->product_id = $product->id;
							$inventory->product_name = $product->name;
							$inventory->vendor_id = Yii::$app->user->identity->entity_id;
							$inventory->stock = $row[12];
							$inventory->price_type = getPriceType($row[13]);
							$inventory->price = $row[14];
							$inventory->discount_type = getDiscountType($row[15]);
							$inventory->discount = $row[16];
							$inventory->shipping_cost = $row[17];
							$inventory->slab_discount_ind = getYesNoInd($row[18]);
							$inventory->slab_discount_type = getDiscountType($row[19]);
							$inventory->slab_1_range = $row[20];
							$inventory->slab_1_discount = $row[21];
							$inventory->slab_2_range = $row[22];
							$inventory->slab_2_discount = $row[23];
							$inventory->slab_3_range = $row[24];
							$inventory->slab_3_discount =$row[25];
							$inventory->slab_4_range = $row[26];
							$inventory->slab_4_discount = $row[27];

							if($attributes_count > 0)
							{
								$inventory->attribute_values = Json::encode($attributes_array);
								$inventory->attribute_price = Json::encode($attributes_price_array);
							}

							$inventory->length = $row[29];
							$inventory->width = $row[30];
							$inventory->height = $row[31];
							$inventory->weight = $row[32];
							$inventory->warranty = $row[33];

							$inventory->active = 1; //active

							$inventory->added_by_id = Yii::$app->user->identity->id;
							$inventory->added_at = time();

							if($inventory->save())
							{
								if($row[28])
								{
									/* Begin creating Search Tags */
									$tags_array = explode(',', $row[28]);
									$error = false;

									foreach($tags_array as $tagrow)
									{
										$inventorytags = new InventoryTags;
										$inventorytags->inventory_id = $inventory->id;

										$tag = Tags::find()->where(['tag'=> strtolower(trim($tagrow))])->one();

										if($tag)
										{
											$inventorytags->tag_id = $tag->id;
										}
										else
										{
											$newtag = new Tags;
											$newtag->tag = strtolower(trim($tagrow));
											if(!$newtag->save())
											{
												$error = true;
												break;
											}

											$inventorytags->tag_id = $newtag->id;
										}

										if(!$inventorytags->save())
										{
											$error = true;
											break;
										}
									}

									if($error)
									{
										$transaction->rollback();
										$rejects = true;
										// Write to reject file
										array_unshift($row, Yii::t('app', 'Error processing search tags'));
										$spreadsheet_out->getActiveSheet()->fromArray($row, NULL, 'A'.$row_index++);
										continue;
									}
								}
							}
							else
							{
								$transaction->rollback();
								$rejects = true;
								// Write to reject file
								array_unshift($row, Yii::t('app', 'Error creating inventory'));
								$spreadsheet_out->getActiveSheet()->fromArray($row, NULL, 'A'.$row_index++);
								continue;
							}
						}
						else
						{
							// Update Inventory
							$inventory->stock = $row[12];
							$inventory->price_type = getPriceType($row[13]);
							$inventory->price = $row[14];
							$inventory->discount_type = getDiscountType($row[15]);
							$inventory->discount = $row[16];
							$inventory->shipping_cost = $row[17];

							if($attributes_count > 0)
							{
								$inventory->attribute_values = Json::encode($attributes_array);
								$inventory->attribute_price = Json::encode($attributes_price_array);
							}

							$inventory->length = $row[29];
							$inventory->width = $row[30];
							$inventory->height = $row[31];
							$inventory->weight = $row[32];
							$inventory->warranty = $row[33];

							$inventory->slab_discount_ind = getYesNoInd($row[18]);
							$inventory->slab_discount_type = getDiscountType($row[19]);
							$inventory->slab_1_range = $row[20];
							$inventory->slab_1_discount = $row[21];
							$inventory->slab_2_range = $row[22];
							$inventory->slab_2_discount = $row[23];
							$inventory->slab_3_range = $row[24];
							$inventory->slab_3_discount =$row[25];
							$inventory->slab_4_range = $row[26];
							$inventory->slab_4_discount = $row[27];
							$inventory->updated_at = time();

							if(!$inventory->save())
							{
								$transaction->rollback();
								$rejects = true;
								// Write to reject file
								array_unshift($row, Yii::t('app', 'Error updating inventory'));
								$spreadsheet_out->getActiveSheet()->fromArray($row, NULL, 'A'.$row_index++);
								continue;
							}

							if($row[28])
							{
								/* Delete existing InventoryTags */
								InventoryTags::deleteAll(['inventory_id' => $inventory->id]);

								/* Begin creating Search Tags */
								$tags_array = explode(',', $row[28]);
								$error = false;

								foreach($tags_array as $tagrow)
								{
									$inventorytags = new InventoryTags;
									$inventorytags->inventory_id = $inventory->id;

									$tag = Tags::find()->where(['tag'=> strtolower(trim($tagrow))])->one();

									if($tag)
									{
										$inventorytags->tag_id = $tag->id;
									}
									else
									{
										$newtag = new Tags;
										$newtag->tag = strtolower(trim($tagrow));
										if(!$newtag->save())
										{
											$error = true;
											break;
										}

										$inventorytags->tag_id = $newtag->id;
									}

									if(!$inventorytags->save())
									{
										$error = true;
										break;
									}
								}

								if($error)
								{
									$transaction->rollback();
									$rejects = true;
									// Write to reject file
									array_unshift($row, Yii::t('app', 'Error processing search tags'));
									$spreadsheet_out->getActiveSheet()->fromArray($row, NULL, 'A'.$row_index++);
									continue;
								}
							}
							else
							{
								InventoryTags::deleteAll(['inventory_id' => $inventory->id]);
							}
						}
						// Inventory Create/Update End
					}
					catch (\Exception $e)
					{
						$transaction->rollback();
						$rejects = true;
						// Write to reject file
						array_unshift($row, Yii::t('app', 'Unexpected error: '.$e->getMessage()));
						$spreadsheet_out->getActiveSheet()->fromArray($row, NULL, 'A'.$row_index++);
						continue;
					}

					$transaction->commit();
				}
				
				if ($rejects)
				{
					$writer = new Xlsx($spreadsheet_out);
					$file_name = 'public/'.uniqid(Yii::$app->user->identity->id).".xlsx";
					$writer->save($file_name);

					Yii::$app->session->setFlash('warning', Yii::t('app', 'Few records are rejected - Please download rejected records').' <a href="'.Url::base()."/".$file_name.'" target="_blank">'.Yii::t('app', 'here').'</a>');
				}
				else
				{
					Yii::$app->session->setFlash('success', Yii::t('app', 'Records uploaded successfully!'));
				}			
			}
			catch (\Exception $e)
			{
				Yii::$app->session->setFlash('error', $e->getMessage());
			}
		}

		return $this->render('bulk-upload-combined');
	}
}