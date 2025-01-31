<?php
    require_once("../../../templates/template.php");
    $db = getBD();
    $utils = new Utils_datos();
    $tipoUsoLab = $db->Execute("SELECT nombre,idsiq_tipoUsoLaboratorio FROM siq_tipoUsoLaboratorio WHERE codigoestado=100 ORDER BY nombre ASC"); 
    $selectTipoUsoLab = $tipoUsoLab->GetMenu2('idsiq_tipoUsoLaboratorio[]',null,true,false,1,'class="grid-11-12 required inputTable" style="float:none;margin:0 auto;"');
?>
<div id="tabs-7">
<form action="save.php" method="post" id="form_laboratorios">
            <input type="hidden" name="entity" id="entity" value="formUnidadesAcademicasLaboratorios" />
            <input type="hidden" name="action" value="save2" id="action" />
            
            <span class="mandatory">* Son campos obligatorios</span>
            <fieldset id="numPersonas">   
                <legend>Laboratorios, Talleres, Museos, etc</legend>
                
                <div class="formModalidad">
                     <?php include("./_elegirProgramaAcademico.php"); ?>
                </div>
                
                <div class="vacio"></div>
                
                <label for="nombre" class="fixedLabel">Semestre: <span class="mandatory">(*)</span></label>
                <?php $utils->getSemestresSelect($db,"codigoperiodo");  
                ?>
				
				<?php $utils->pintarBotonCargar("popup_cargarDocumento(9,17,$('#form_laboratorios #codigoperiodo').val(),$('#form_laboratorios #unidadAcademica').val())","popup_verDocumentos(9,17,$('#form_laboratorios #codigoperiodo').val(),$('#form_laboratorios #unidadAcademica').val())"); ?>
             
                
                <table align="center" class="formData last" width="92%" >
                    <thead>            
                        <tr class="dataColumns">
                            <th class="column" colspan="5"><span>Laboratorios, Talleres, Museos, etc</span></th>                                    
                        </tr>
                        <tr class="dataColumns category">
                            <th class="column" ><span>Nombre del (Laboratorio, taller, Museo, etc)</span></th> 
                            <th class="column" ><span>Cantidad</span></th> 
                            <th class="column" ><span>Capacidad (número de puestos para estudiantes)</span></th> 
                            <th class="column" ><span>Observaciones</span></th> 
                            <th class="column" ><span>Tipo de Utilización</span></th> 
                        </tr>
                     </thead>
                     <tbody>
                        <tr class="dataColumns">
                            <td class="column"> 
                                <input type="text" class="grid-11-12 required inputTable" minlength="1" name="nombre[]" title="Nombre del Laboratorio" maxlength="200" tabindex="1" autocomplete="off" value="" />
                                <input type="hidden" name="idsiq_formUnidadesAcademicasLaboratorios[]" autocomplete="off" value="" />
                            </td>
                            <td class="column"> 
                                <input type="text" class="grid-4-12 required number" minlength="1" name="numLaboratorios[]" title="Total de Laboratorios" maxlength="10" tabindex="1" autocomplete="off" value="" />
                            </td>
                            <td class="column"> 
                                <input type="text" class="grid-4-12 required number" minlength="1" name="capacidad[]" title="Total de Puestos para Estudiantes" maxlength="10" tabindex="1" autocomplete="off" value="" />
                            </td>
                            <td class="column"> 
                                <input type="text" class="grid-11-12 required inputTable" minlength="1" name="observaciones[]" title="Observaciones" maxlength="200" tabindex="1" autocomplete="off" value="" />
                            </td>
                            <td class="column"> 
                                <?php echo $selectTipoUsoLab; ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
            <div class="adicionarVisitantes" onmouseover="adicionarVisitantes(this)" title="">
            <input type="button" class="first small" id="addMoreLaboratorios" value="Agregar otro" style="margin-top:10px">
            </div>
            <div class="removerVisitantes" onmouseover="removerVisitantes(this)" title="">
            <input type="button" class="first small" id="removeLaboratorios" value="Eliminar último" style="margin-top:10px">
            </div>
                <div class="vacio"></div>
                <div id="msg-success" class="msg-success" style="display:none"><p>Los datos han sido guardados de forma correcta.</p></div>
            </fieldset>
            <div class="guardar" onmouseover="guardar(this)" title="">
            <div class="vacio"></div>
            <input type="submit" id="submitLaboratorios" value="Guardar datos" class="first" /> 
            </div>
        </form>
</div>

<script type="text/javascript">
    getDataLaboratorios("#form_laboratorios");
   
   $('#addMoreLaboratorios').click(function(event) {
           addTableRow("#form_laboratorios");
   });
   
   $('#removeLaboratorios').click(function(event) {
       var formName = "#form_laboratorios";
       var inputName = "input[name='idsiq_formUnidadesAcademicasLaboratorios[]']";
       if($(formName + ' table').children('tbody').children('tr:last').find(inputName).val()!=""){
        var id = $(formName + ' table').children('tbody').children('tr:last').find(inputName).val();
        var entity = $(formName + ' #entity').val();
        $.ajax({
            dataType: 'json',
            type: 'POST',
            url: './formularios/academicos/saveUnidadesAcademicas.php',
            data: { action: "inactivate", idsiq_formUnidadesAcademicasLaboratorios:id, entity:entity },                
                            success:function(data){
                                if (data.success == true){
                                    removeTableRow(formName,inputName);
                                }
                                else{                        
                                    alert("Ocurrio un error");
                                }
                            },
                            error: function(data,error,errorThrown){alert(error + errorThrown);}
            });  
       } else {
            removeTableRow(formName,inputName);
       }
   });
    
                $('#submitLaboratorios').click(function(event) {
                    event.preventDefault();
                    var valido= validateForm("#form_laboratorios");
                    if(valido){
                        sendFormLaboratorios("#form_laboratorios");
                    }
                });
                
                $('#form_laboratorios #codigoperiodo').bind('change', function(event) {
                    getDataLaboratorios("#form_laboratorios");
                });
                
                $(document).on('change', "#form_laboratorios #modalidad", function(){
                    getCarreras("#form_laboratorios");
                    changeFormModalidad("#form_laboratorios");
                });
                
                $(document).on('change', "#form_laboratorios #unidadAcademica", function(){
                    getDataLaboratorios("#form_laboratorios");
                    changeFormModalidad("#form_laboratorios");
                });
                
                function getDataLaboratorios(formName){
                    var periodo = $(formName + ' #codigoperiodo').val();
                    var entity = $(formName + " #entity").val();
                    var codigocarrera = $(formName + " #unidadAcademica").val();
                    if(codigocarrera==""){
                        //no hay datos xq no hay carrera
                        $(formName + ' table input').each(function() {                                     
                             $(this).val("");                                       
                        });
                    } else {
                        $.ajax({
                            dataType: 'json',
                            type: 'POST',
                            url: './formularios/academicos/saveUnidadesAcademicas.php',
                            data: { periodo: periodo, action: "getData2", entity: entity, campoPeriodo: "codigoperiodo",codigocarrera:codigocarrera },     
                            success:function(data){
                                if (data.success == true){
                                    //borro todas las filas
                                    $(formName + ' table').children('tbody').children('tr').remove(); 
                                    for (var i=0;i<data.total;i++)
                                    {                                  
                                        //pinto las nuevas filas
                                        var row = '<tr class="dataColumns">';
                                        row = row + '<td class="column">';
                                        row = row + '<input type="text" class="grid-11-12 required inputTable" minlength="1" name="nombre[]" title="Nombre del Laboratorio" maxlength="200" tabindex="1" autocomplete="off" value="'+data.data[i].nombre+'" />';
                                        row = row + '<input type="hidden" name="idsiq_formUnidadesAcademicasLaboratorios[]" autocomplete="off" value="'+data.data[i].idsiq_formUnidadesAcademicasLaboratorios+'" />';
                                        row = row + '</td><td class="column">';
                                        row = row + '<input type="text" class="grid-4-12 required number" minlength="1" name="numLaboratorios[]" title="Total de Laboratorios" maxlength="10" tabindex="1" autocomplete="off" value="'+data.data[i].numLaboratorios+'" />';
                                        row = row + '</td><td class="column">';
                                        row = row + '<input type="text" class="grid-4-12 required number" minlength="1" name="capacidad[]" title="Total de Puestos para Estudiantes" maxlength="10" tabindex="1" autocomplete="off" value="'+data.data[i].capacidad+'" />';
                                        row = row + '</td><td class="column">';
                                        row = row + '<input type="text" class="grid-11-12 required inputTable" minlength="1" name="observaciones[]" title="Tipo de Uso" maxlength="200" tabindex="1" autocomplete="off" value="'+data.data[i].observaciones+'" />';
                                        row = row + '</td><td class="column" id="tipoUso">';
                                        row = row + '<?php echo str_replace("'",'"',preg_replace( "/\r|\n/", "", $selectTipoUsoLab)); ?>';
                                        row = row + '</td></tr>';
                                        if($(formName + ' table').children('tbody').find('tr:last').length>0){
                                            $(formName + ' table').children('tbody').find('tr:last').after(row);  
                                        } else {
                                            $(formName + ' table').children('tbody').append(row); 
                                        }
                                            $(formName + ' table td#tipoUso select').val(data.data[i].idsiq_tipoUsoLaboratorio); 
                                            $('td#tipoUso').removeAttr('id');
                                    }
                                    $(formName + " #action").val("update2");
                                }
                                else{                        
                                    //no se encontraron datos
                                    $(formName + ' table input').each(function() {                                     
                                        $(this).val("");                                       
                                    });
                                }
                            },
                            error: function(data,error,errorThrown){alert(error + errorThrown);}
                        });  
                    }
                }

                function sendFormLaboratorios(formName){
                activarModalidades(formName);
                    $.ajax({
                        dataType: 'json',
                        type: 'POST',
                        url: './formularios/academicos/saveUnidadesAcademicas.php',
                        data: $(formName).serialize(),                
                        success:function(data){
						<?php if($permisos["rol"][0]!=1) { ?>
                            desactivarModalidades(formName);
							<?php } ?>
                            if (data.success == true){
                                 //window.location.href="./viewData.php?id=row_<?php //echo $formulario["idsiq_formulario"]; ?>";  
                                 var i = 0;
                                 $('input[name="idsiq_formUnidadesAcademicasLaboratorios[]"]').each(function() {
                                        $(this).val(data.data[i]);
                                        i = i + 1;
                                 }); 
                                 $(formName + " #action").val("update2");
                                 $(formName + ' #msg-success').css('display','block');
                                 $(formName + " #msg-success").delay(5500).fadeOut(800);
                            }
                            else{                        
                                $('#msg-error').html('<p>' + data.message + '</p>');
                                $('#msg-error').addClass('msg-error');
                            }
                        },
                        error: function(data,error,errorThrown){alert(error + errorThrown);}
                    });            
                }
                
</script>
