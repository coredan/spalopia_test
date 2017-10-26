// suma N días a la fecha
Date.prototype.addDays = function(days) {
	var dat = new Date(this.valueOf());
  	dat.setDate(dat.getDate() + days);
  	return dat;
}

// Una vez el documento haya sido cargado:
$(document).ready(function(){
	
	var dates;

	// Calendario utilizado en el formulario de búsqueda
	$('#mdp-demo').multiDatesPicker({
		//mode: 'daysRange',
		//autoselectRange: [0,1],
		maxPicks: 2,
		minDate: 0,
		dateFormat: "yy-mm-dd",
		onSelect: function(dateText) {
			dates = $('#mdp-demo').multiDatesPicker('getDates');
			$('#checkin').val(dates[0]);
			if(dates.length > 1)
				$('#checkout').val(dates[1]);
		}
	});

	// Calendario utilizado en el formulario de reservas
	$('.datepicker').multiDatesPicker({minDate: 0, maxPicks:1,dateFormat: "yy-mm-dd"});

	// Cuando se envíe el formulario de búsqueda interceptamos aquí el envío (GET) y 
	// hacemos una llamada a la API utilizando ajax. 
	$('#searchForm').submit(function(event) {				
		var url = "/api/rooms";
		// parámetros de formulario a un array 
		var data = $(this).serializeArray();
			
		//console.dir(data);
		// Callback de la llamada Ajax en caso satisfactorio:
		var success_callback = function(response){
			//console.dir(response);
			// Si la respuesta es correcta se muestra el resultado en la vista
			buildRow(response,$('#checkin').val(), $('#checkout').val());
		};

		// AJAX:
		doAjaxCall(url,data,'GET',success_callback);
		// Previene que el formulario se envíe y la página se recargue.
		event.preventDefault();
	});

	// Cuando se envíe el formulario de reserva interceptamos aquí el envío y 
	// haremos una llamada a la API utilizando ajax.
	$('#reservationForm').submit(function(event) {				
		var url = "/api/rooms";
		var data = $(this).serializeArray();
		//console.dir(data);		
		var success_callback = function(response){
			if(response.status === "Saved"){
				// Mensaje de confirmación
				swal({
				  	title: "Reserva Realizada",
				  	text: "Su reserva <strong>ha sido realidada</strong>! <p>Su localizador:</p><p class='reservation-code'>827839484</p>",
				  	html: true,
				  	icon: "success",
				  	button: "Aww yiss!",
				  	type: "success",
				  	button: {
				    	text: "OK",
				    	closeModal: false,
					}				  
				},function(){
					// Lo que ocurrirá al pulsar el botón OK
				  	window.location.replace("/site/index");
				});				
			}
			//console.dir(response);
			
		};	
		doAjaxCall(url,data,'POST',success_callback);
		event.preventDefault();
	});

	// Clic en el botón de reservas (se usa la forma $.on() que es similar a live()
	// ya que el botón es generado dinámicamente con ajax)
	$('#hotels_list').on('click', "button.reservation", function(){		
		var roomId = $(this).attr('id').replace("roomid_","");
		var form = $(this).closest('form'); // el formulario que contiene al botón
		$(".extras input:checkbox:checked").each(function(index){			
	      form.append($('<input type="hidden" name="room_extras[]">').val($(this).val()));
	    });			    
		form.submit(); // se fuerza el submit (hacia el formulario de reserva)
	});
		
});

// Trozo de código feo que sin duda hay que mejorar.
// Construye el HTML necesario para representar cada hotel en el listado.
function buildRow(hotels, checkin, checkout) {
	$('#hotels_list').empty();
	$.each(hotels, function( index, value ) {
		//console.dir(value);
		var $dir = $('<dir>').addClass('hotel-instance');
		var html = '<form action="/reservations"><div class="panel panel-default">';
		html +=	'		<input type="hidden" name="room_id" value="'+value.room.id+'">';
		html +=	'		<input type="hidden" name="checkin" value="'+checkin+'">';		
		html +=	'		<input type="hidden" name="checkout" value="'+checkout+'">';
		html +=	'		<input type="hidden" name="total" value="'+value.total+'">';		
		html +=	'		<div class="panel-heading">';
		html += '			<div class="col-md-7 col-sm-7 col-xs-7"><h3 class="panel-title"><i class="fa fa-h-square" aria-hidden="true"></i> '+value.room.name+'</h3></div>';
		html += '          	<div class="col-md-5 col-sm-5 col-xs-5 text-right"><i class="fa fa-eur" aria-hidden="true"></i> Precio por persona: '+value.room.price+'</div>';
		html += '   	</div>';
		html += '       <div class="panel-body">';
		html += '         	<div class="col-md-7">'+ value.room.description +'</div>';
		html += '			<div class="col-md-5 text-right prices">';
		html += '          		<h4>Precio ('+ value.nights +' noches)</h4><hr />';
		if(value.extras){			
			html += '<div class="col-md-8 col-sm-8 col-xs-8 tag"> Total Extras </div><div class="col-md-4 col-sm-4 col-xs-4 total"> + ' +value.extras+' €</div>';
		}

		$.each(value.increments_per_day, function(index, increment) {
			//console.dir(value);
			$.each(increment, function(index, val) {
				html += '<div class="col-md-8 col-sm-8 col-xs-8 tag">'+index+' incr.</div><div class="col-md-4 col-sm-4 col-xs-4 total"> + ' +val+' €</div>';
			});	
		});		
		html += '<div class="col-md-8 col-sm-8 col-xs-8">Total </div><div class="col-md-4 col-sm-4 col-xs-4 text-right total-price">' +value.total+' €</div>';
		html += '          	</div>';
		html += '      	</div>';
		html += '		<div class="panel-footer text-right"><button type="button" id="roomid_'+value.room.id+'" class="btn btn-danger reservation">¡Reserve ahora!</button></div>'
		html += '	</div></form>';     
		$('#hotels_list').append($dir.html(html));		
	});	
}      

// función AJAX
function doAjaxCall(url, data, type, success) {
	//console.dir(data);       
    $.ajax({
    	type: type,
        encoding:"UTF-8",
        dataType:"json",
        url: url,    
        data: data,
        success: success,
        "error":function(xhr, error){
            //console.debug(xhr); 
            alert(xhr.responseText);
        }
    });
}