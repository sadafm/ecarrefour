<?php

namespace multebox\models;

use Yii;
use yii\filters\VerbFilter;
use yii\db\Query;
use multebox\models\search\MulteModel;
use multebox\models\Order;
use multebox\models\SubOrder;

class SalesReport extends \yii\db\ActiveRecord
{

public function salesReportChart($id, $months)
{
	$labels = '';
	$data = '';
	for ($i = 0; $i < $months; $i++)
	{
		$labels ="'".date("F, Y", strtotime("-$i month"))."',".$labels;
		$j = $i+1;

		$oldtimestamp = strtotime("-$j month 00:00:00");
		$newtimestamp = strtotime("-$i month 23:59:59");
		
		//print_r($oldtimestamp."<br>");
		//print_r($newtimestamp."<br>");
		$sale_amount = round(Order::find()->where("added_at > ".$oldtimestamp." and added_at <= ".$newtimestamp)->sum('total_cost'),2);
		//print_r($sale_amount."<br>");
		$data = "'".$sale_amount."',".$data;
	}
	$labels = substr($labels, 0, -1);
	$data = substr($data, 0, -1);
	//var_dump($data);exit;
	?>
	<script type="text/javascript">
	  // Get context with jQuery - using jQuery's .get() method.
	  var salesChartCanvas = $('#<?=$id?>').get(0).getContext('2d');
	  // This will get the first returned node in the jQuery collection.
	  var salesChart = new Chart(salesChartCanvas);

	  var salesChartData = {
		labels  : [<?=$labels?>],
		datasets: [
		  {
			label               : 'Sales',
			fillColor           : 'rgba(60,141,188,0.9)',
			strokeColor         : 'rgba(60,141,188,0.8)',
			pointColor          : '#3b8bba',
			pointStrokeColor    : 'rgba(60,141,188,1)',
			pointHighlightFill  : '#fff',
			pointHighlightStroke: 'rgba(60,141,188,1)',
			data                : [<?=$data?>]
		  }
		]
	  };

	  var salesChartOptions = {
		// Boolean - If we should show the scale at all
		showScale               : true,
		// Boolean - Whether grid lines are shown across the chart
		scaleShowGridLines      : false,
		// String - Colour of the grid lines
		scaleGridLineColor      : 'rgba(0,0,0,.05)',
		// Number - Width of the grid lines
		scaleGridLineWidth      : 1,
		// Boolean - Whether to show horizontal lines (except X axis)
		scaleShowHorizontalLines: true,
		// Boolean - Whether to show vertical lines (except Y axis)
		scaleShowVerticalLines  : true,
		// Boolean - Whether the line is curved between points
		bezierCurve             : true,
		// Number - Tension of the bezier curve between points
		bezierCurveTension      : 0.3,
		// Boolean - Whether to show a dot for each point
		pointDot                : false,
		// Number - Radius of each point dot in pixels
		pointDotRadius          : 4,
		// Number - Pixel width of point dot stroke
		pointDotStrokeWidth     : 1,
		// Number - amount extra to add to the radius to cater for hit detection outside the drawn point
		pointHitDetectionRadius : 20,
		// Boolean - Whether to show a stroke for datasets
		datasetStroke           : true,
		// Number - Pixel width of dataset stroke
		datasetStrokeWidth      : 2,
		// Boolean - Whether to fill the dataset with a color
		datasetFill             : true,
		// String - A legend template
		legendTemplate          : '<ul class=\'<%=name.toLowerCase()%>-legend\'><% for (var i=0; i<datasets.length; i++){%><li><span style=\'background-color:<%=datasets[i].lineColor%>\'></span><%=datasets[i].label%></li><%}%></ul>',
		// Boolean - whether to maintain the starting aspect ratio or not when responsive, if set to false, will take up entire container
		maintainAspectRatio     : true,
		// Boolean - whether to make the chart responsive to window resizing
		responsive              : true
	  };

	  // Create the line chart
	  <?=$id?>.Line(salesChartData, salesChartOptions);
	  </script>
		<?php
	}

	public function salesReportChartVendor($id, $months)
	{
	$labels = '';
	$data = '';
	for ($i = 0; $i < $months; $i++)
	{
		$labels ="'".date("F, Y", strtotime("-$i month"))."',".$labels;
		$j = $i+1;

		$oldtimestamp = strtotime("-$j month 00:00:00");
		$newtimestamp = strtotime("-$i month 23:59:59");
		
		//print_r($oldtimestamp."<br>");
		//print_r($newtimestamp."<br>");
		$sale_amount = round(SubOrder::find()->where("vendor_id = ".Yii::$app->user->identity->entity_id." and sub_order_status not in ('".OrderStatus::_CANCELED."', '".OrderStatus::_REFUNDED."', '".OrderStatus::_RETURNED."') and added_at > ".$oldtimestamp." and added_at <= ".$newtimestamp)->sum('total_cost'),2);
		//print_r($sale_amount."<br>");
		$data = "'".$sale_amount."',".$data;
	}
	$labels = substr($labels, 0, -1);
	$data = substr($data, 0, -1);
	//var_dump($data);exit;
	?>
	<script type="text/javascript">
	  // Get context with jQuery - using jQuery's .get() method.
	  var salesChartCanvas = $('#<?=$id?>').get(0).getContext('2d');
	  // This will get the first returned node in the jQuery collection.
	  var salesChart = new Chart(salesChartCanvas);

	  var salesChartData = {
		labels  : [<?=$labels?>],
		datasets: [
		  {
			label               : 'Sales',
			fillColor           : 'rgba(60,141,188,0.9)',
			strokeColor         : 'rgba(60,141,188,0.8)',
			pointColor          : '#3b8bba',
			pointStrokeColor    : 'rgba(60,141,188,1)',
			pointHighlightFill  : '#fff',
			pointHighlightStroke: 'rgba(60,141,188,1)',
			data                : [<?=$data?>]
		  }
		]
	  };

	  var salesChartOptions = {
		// Boolean - If we should show the scale at all
		showScale               : true,
		// Boolean - Whether grid lines are shown across the chart
		scaleShowGridLines      : false,
		// String - Colour of the grid lines
		scaleGridLineColor      : 'rgba(0,0,0,.05)',
		// Number - Width of the grid lines
		scaleGridLineWidth      : 1,
		// Boolean - Whether to show horizontal lines (except X axis)
		scaleShowHorizontalLines: true,
		// Boolean - Whether to show vertical lines (except Y axis)
		scaleShowVerticalLines  : true,
		// Boolean - Whether the line is curved between points
		bezierCurve             : true,
		// Number - Tension of the bezier curve between points
		bezierCurveTension      : 0.3,
		// Boolean - Whether to show a dot for each point
		pointDot                : false,
		// Number - Radius of each point dot in pixels
		pointDotRadius          : 4,
		// Number - Pixel width of point dot stroke
		pointDotStrokeWidth     : 1,
		// Number - amount extra to add to the radius to cater for hit detection outside the drawn point
		pointHitDetectionRadius : 20,
		// Boolean - Whether to show a stroke for datasets
		datasetStroke           : true,
		// Number - Pixel width of dataset stroke
		datasetStrokeWidth      : 2,
		// Boolean - Whether to fill the dataset with a color
		datasetFill             : true,
		// String - A legend template
		legendTemplate          : '<ul class=\'<%=name.toLowerCase()%>-legend\'><% for (var i=0; i<datasets.length; i++){%><li><span style=\'background-color:<%=datasets[i].lineColor%>\'></span><%=datasets[i].label%></li><%}%></ul>',
		// Boolean - whether to maintain the starting aspect ratio or not when responsive, if set to false, will take up entire container
		maintainAspectRatio     : true,
		// Boolean - whether to make the chart responsive to window resizing
		responsive              : true
	  };

	  // Create the line chart
	  <?=$id?>.Line(salesChartData, salesChartOptions);
	  </script>
		<?php
	}
}

?>