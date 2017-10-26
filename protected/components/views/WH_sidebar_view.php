<div id="sidebar">
    <h1>Opciones</h1>
    <form name="searchForm" id="searchForm">       
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-calendar-check-o" aria-hidden="true"></i> Fecha de su estancia</h3>
            </div>
            <div class="panel-body">
                <div class="form-group">
                    <label for="fentrada">Fechas de entrada y salida:</label>
                    <div id="mdp-demo"></div>
                </div>            
                <div class="form-group">
                    <label for="checkin">Entrada:</label>
                    <input type="text" class="form-control mini-form" id="checkin" name="checkin" readOnly="readOnly">
                    <label for="checkout">Salida:</label>
                    <input type="text" class="form-control mini-form" id="checkout" name="checkout" readOnly="readOnly">
                </div>                
            </div>  
        </div>

        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-calendar-check-o" aria-hidden="true"></i> Extras</h3>
            </div>
            <div class="panel-body">
                <div class="form-group extras">
                    <?php foreach ($this->extras as $extra) { ?>
                        <p><label><input type="checkbox" name="extras[]" value="<?php echo $extra->extra_key ?>"> <?php echo $extra->name ?></label></p>
                    <?php } ?>
                    <div id="mdp-demo"></div>
                </div>                                         
            </div>  
        </div>

        <div class="form-group">
            <button type="submit" id="check" class="btn btn-default">Buscar disponibles</button>
        </div>
    </form>
</div><!-- sidebar -->