<?php
/* @var $this SiteController */

$this->pageTitle=Yii::app()->name;
$this->breadcrumbs=array(
	'Buscar Hotel',
);

?>
<div id="hotels_list">
	<?php foreach ($rooms as $room) { ?>
		<form action="/reservations">
			<div class="panel panel-default">					
				<div class="panel-heading">
					<div class="col-md-7 col-sm-7 col-xs-7"><h3 class="panel-title"><i class="fa fa-h-square" aria-hidden="true"></i> <?php echo $room->name; ?></h3></div>
					<div class="col-md-5 col-sm-5 col-xs-5 text-right"><i class="fa fa-eur" aria-hidden="true"></i> Precio por persona: <?php echo $room->price; ?></div>
				</div>
				<div class="panel-body">
					<div class="col-md-7"><?php echo $room->description; ?></div>
					<div class="col-md-5 text-right prices">						
					</div>
				</div>				
			</div>
		</form>
	<?php } ?>
</div>