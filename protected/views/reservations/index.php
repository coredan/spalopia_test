<?php
/* @var $this ReservationsController */

$this->breadcrumbs=array(
	'Reservations',
	);
	?>

<form name="reservationForm" id="reservationForm">
	<input type="hidden" name="room_id" value="<?php echo $room_id; ?>">
	<input type="hidden" name="extras" value="<?php echo $selected_extras_cs; ?>">
	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title"><i class="fa fa-handshake-o" aria-hidden="true"></i> Reserva para: <strong><?php echo $room_name; ?></strong></h3>
		</div>
		<div class="panel-body">
			<div class="form-group">
				<label for="client_name"><i class="fa fa-user" aria-hidden="true"></i> Nombre:</label>
				<input type="text" class="form-control" id="client_name" name="client_name" placeholder="Introduzca su nombre y apellidos">
			</div>
			<div class="form-group">
				<label for="comments"><i class="fa fa-commenting" aria-hidden="true"></i> Comentarios:</label>
				<textarea class="form-control" id="comments" name="comments" placeholder="Comente aquí lo que desee"></textarea>
			</div>
			<div class="col-md-6">
				<div class="form-group">		  				  	
					<label for="checkin"><i class="fa fa-calendar-check-o" aria-hidden="true"></i> Fecha de entrada:</label>
					<input class="datepicker form-control" name="checkin" id="checkin" placeholder="El día en que llegará" value="<?php echo isset($checkin) ? $checkin : "" ?>">
				</div>
			</div>
			<div class="col-md-6 last">
				<div class="form-group">
					<label for="checkout"><i class="fa fa-calendar-check-o" aria-hidden="true"></i> Fecha de salida:</label>
					<input class="datepicker form-control" name="checkout" id="checkout" placeholder="El día en que dejará el hotel" value="<?php echo isset($checkout) ? $checkout : "" ?>">
				</div>
			</div>
			<div class="form-group extras">
				<label for="extrass"><i class="fa fa-plus-circle" aria-hidden="true"></i> Extras:</label>
				<hr />
				<?php  
					foreach ($room_extras as $extra) {
						$checked = "";
						if (isset($selected_extras)) { 
							$checked = in_array($extra->extra_key, $selected_extras) ? "checked" : ""; 		    		
						} ?>
						<label><input type="checkbox" name="" value="<?php echo $extra->extra_key ?>" <?php echo $checked ?>>
							<?php echo $extra->name ?>
						</label>		    		
					<?php } ?>        
			</div>
			<div class="form-group text-right">
				<label class="tag">Precio total: 
				<?php if (isset($total)) { ?>
					<span class="prices total-price"><?php echo $total; ?> €</span>
				<?php } ?>
				</label>
				<p><span class="small">(por noche y persona)</span></p>
			</div>
			<div class="form-group text-right"> 	
				<button type="submit" class="btn btn-danger btn-lg">Reservar</button>
			</div>
		</div>
	</div>
</form>

