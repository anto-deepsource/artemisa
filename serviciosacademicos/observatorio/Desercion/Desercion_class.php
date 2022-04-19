<?php

class Desercion{
    public function DesercionSemestral($CodigoPeriodo,$Opcion,$Separador='',$CodigoCarrera=''){
        global $db;

        //$CodigoPeriodo	='20161';
        include_once('../../consulta/estadisticas/matriculasnew/funciones/obtener_datos.php');
       	
        $Periodo_Actual=$this->Periodo('Actual','','');
        
        $C_Periodo  = $this->Periodo('Cadena',$CodigoPeriodo,$Periodo_Actual);
        //echo '<pre>';print_r($C_Periodo); die;
        $Num_P  = (int) count($C_Periodo);
           
                        
         $Datos = array();
         $Total = array();
       
         for($j=0;$j<1;$j++){
               
                 /*****************************************************************************/ 
                $C_Periodo[$j]['codigoperiodo'];
                
                $datos_estadistica=new obtener_datos_matriculas($db,$C_Periodo[$j]['codigoperiodo']);
                /************************************************************************/
                $Lita_C = $this->Carreras();
                if($CodigoCarrera){
                    $Num_Lista = 1;
                }else{
                
                //echo '<pre>';print_r($Lita_C);
                $Num_Lista  = (int) count($Lita_C);
                }
                $Suma_Matriculados=0;
                $Suma_Desercion=0;
                
                                    
                for($i=0;$i<$Num_Lista;$i++){//fin
                    /**********************************/
                                         
                    //if($TotalMatriculados!=0){
                       /********************************************************************************/
                        if($CodigoCarrera){
                            
                        $D_Carrera  = $CodigoCarrera;
                            
                        $DesercionDato=$datos_estadistica->obtener_datos_estudiantes_desercion($D_Carrera,'conteo');
                        
                        $C_Desercion  =$datos_estadistica->obtener_datos_estudiantes_desercion($D_Carrera,'arreglo');
                        
                        
                        }else{  
                       
                        $D_Carrera    = $Lita_C[$i]['codigocarrera'];
                    
                        $DesercionDato=$datos_estadistica->obtener_datos_estudiantes_desercion($D_Carrera,'conteo');
                        
                        $C_Desercion  =$datos_estadistica->obtener_datos_estudiantes_desercion($D_Carrera,'arreglo');
                        
                        }
                        
                        $C_Estudiante = array();
                        
                        for($t=0;$t<count($C_Desercion);$t++){
                            
                            $C_Estudiante[] = $C_Desercion[$t]['codigoestudiante'];
                            
                        }/*for*/
                        //echo '<br>D_Carrera->'.$D_Carrera.'-->'.$Lita_C[$i]['nombrecarrera'];
                        
                                                
                        $TotalMatriculados = $datos_estadistica->obtener_total_matriculados($D_Carrera,'conteo');
                        
                        $C_Matriculados = $datos_estadistica->obtener_total_matriculados($D_Carrera,'arreglo');
                        
                                                
                        $E_Matriculados = array();
                        
                        for($M=0;$M<count($C_Matriculados);$M++){
                            
                            $E_Matriculados[] =  $C_Matriculados[$M]['codigoestudiante'];
                            
                        }/*for*/
                        //echo '<pre>';print_r($TotalMatriculados);
                        
                        $PorcentajeDesercion = (($DesercionDato/$TotalMatriculados)*100);
                        
                        $Suma_Matriculados  = $Suma_Matriculados+$TotalMatriculados;
                        
                        $Suma_Desercion     = $Suma_Desercion+$DesercionDato;
                        
                        
                                      
                        $Datos[$i][$D_Carrera][$j]['Periodo']=$C_Periodo[$j]['codigoperiodo'];
                        $Datos[$i][$D_Carrera][$j]['Carrera']=$Lita_C[$i]['nombrecarrera'];
                        $Datos[$i][$D_Carrera][$j]['TotalMatriculados']=$TotalMatriculados;
                        $Datos[$i][$D_Carrera][$j]['Desercion']=$DesercionDato;
                        if($Separador==1){
                            $Datos[$i][$D_Carrera][$j]['PorcentajeDesercion']=number_format($PorcentajeDesercion,'2',',','.');
                        }else{
                        $Datos[$i][$D_Carrera][$j]['PorcentajeDesercion']=number_format($PorcentajeDesercion,'2','.',',');
                        }
                        $Datos[$i][$D_Carrera][$j]['Estudiantes']=$C_Estudiante;
                        $Datos[$i][$D_Carrera][$j]['E_Matriculados']=$E_Matriculados;
                       /********************************************************************************/ 
                   // }
                   
                    
                }//for
                /************************************************************************/
                
               /*****************************************************************************/ 
               $Total[$j]['Periodo']=$C_Periodo[$j]['codigoperiodo'];
               $Total[$j]['Total_M']=$Suma_Matriculados;
               $Total[$j]['Total_D']=$Suma_Desercion;
               //$Total[$j]['Nom_Carrera']=$Lita_C[$i]['nombrecarrera'];
            }//for
        
        
        //echo '<pre>';print_r($Total);
        
        //$DesercionDato=$datos_estadistica->obtener_datos_estudiantes_desercion('5','conteo');
        
        //echo '<pre>';print_r($DesercionDato); 
        
        //$DesercionArray=$datos_estadistica->obtener_datos_estudiantes_desercion('5','arreglo');
        
        //echo '<pre>';print_r($Datos);
        
        if($Opcion=='Programas'){
            return $Datos;    
        }else if($Opcion=='Institucional'){
            return $Total;
        }
        
    }//public function DesercionSemestral
	
/*
*Ivan quintero 
* 13 de septiembre
* Limpieza de codigo comentado
*/	
 public function DesercionAnual($CodigoPeriodo)
 {    
    global $db;
       
    include_once('../../consulta/estadisticas/matriculasnew/funciones/obtener_datos.php');
    
    $Periodo_Actual = $this->Periodo('Actual','','');
    $C_Periodos     = $this->Periodo('Cadena',$CodigoPeriodo,$Periodo_Actual);
    $Periodos   = array();
    
    for($i=0;$i<count($C_Periodos);$i++)
	{
		$Periodos['Periodo'][]=$C_Periodos[$i]['codigoperiodo'];   
    }//for
        
    $P_Anual    = array();
    
    for($j=0;$j<count($Periodos['Periodo']);$j=$j+2)
	{    
		if(($j+1) <count($Periodos['Periodo']))
		{
			$x  = $j+1;
			$P_Anual['Anual'][]=$Periodos['Periodo'][$j].'-'.$Periodos['Periodo'][$x];
		}        
    }//for

    for($d=0;$d<count($P_Anual['Anual']);$d++)
	{    
		$C_Data  = explode('-',$P_Anual['Anual'][$d]);
        $D_PeriododAnual[]= $C_Data[0];
        
        if($C_Data[1])
		{
			$D_PeriododAnual[]= $C_Data[1];    
        }
    }//for
    
	$C_Datos    = array();
    $R_Datos    = array();
    
    $C_Carrera1  = $this->Carreras();
    $C_Carrera2  = $this->Carreras('',300); 
    $L_Carrera = array_merge ( $C_Carrera1,$C_Carrera2 );
	$contD_PeriododAnual = (int) count($D_PeriododAnual);
	
	$contL_Carrera = (int) count($L_Carrera);	 
	
    for($Q=0;$Q<$contL_Carrera;$Q++)
	{    
        for($l=0;$l<1;$l++)
		{
			$arrayP = str_split($D_PeriododAnual[$l], strlen($D_PeriododAnual[$l])-1);
			$P = $arrayP[0]-1;
			$PeriodoMatriculadoBase = $P.$arrayP[1];                
			$X  = $arrayP[1]-1;                        
                
			$arrayP = str_split($PeriodoMatriculadoBase, strlen($PeriodoMatriculadoBase)-1);
            
			if($arrayP[1]==1)
			{        
				$Year2   = $arrayP[0].'2';    
			}else{  
				$A  = $arrayP[0]+1;    
				$Year2   = $A.'1';
			} 
                
			$arrayP2 = str_split($Year2, strlen($Year2)-1);
                
			if($arrayP2[1]==1)
			{       
				$Year3   = $arrayP2[0].'2';   
			}else{  
				$B  = $arrayP2[0]+1;   
				$Year3   = $B.'1';
			} 
             
			$datos_estadistica_Base  = new obtener_datos_matriculas($db,$PeriodoMatriculadoBase);   							
			$T_Matriculados = $datos_estadistica_Base->obtener_total_matriculados($L_Carrera[$Q]['codigocarrera'],'arreglo');				 
			    
			$datos_estadistica_Desercion_1 = new obtener_datos_matriculas($db,$Year2);
                 
		  	$U_DesercionDato_1        = $datos_estadistica_Desercion_1->obtener_datos_estudiantes_desercion($L_Carrera[$Q]['codigocarrera'],'arreglo');//Desercion INicio
		  	$datos_estadistica = new obtener_datos_matriculas($db,$Year3);
                 
			$X_Matriculados        = $datos_estadistica->obtener_total_matriculados($L_Carrera[$Q]['codigocarrera'],'arreglo');//Matriculados Dos                  
            
			$D_PeriodoUno=count($U_DesercionDato_1);
                  
			$T_DesercionUno =array();
                  
			for($i=0;$i<$D_PeriodoUno;$i++)
			{
				$T_DesercionUno[] = $U_DesercionDato_1[$i]['codigoestudiante'];
			}//for
                        
			$T_DesercionCruse = array_diff($T_DesercionUno,$X_Matriculados);    
			$MatirucladosBase = count($T_Matriculados);      
			$DesercionAnula   = count($T_DesercionCruse);
               
			$E_EstratoMatriculados = array();
			
			$Matriculados_uno = 1;    
                   
			$Valor_final     = (($DesercionAnula)/($Matriculados_uno));
			$R_Datos['codigocarrera']	   = $L_Carrera[$Q]['codigocarrera'];
			$R_Datos['t+2']                = $Year3;
			$R_Datos['Year2']              = $Year2;
			$R_Datos['PeriodoInicio']      = $Year2;
			$R_Datos['Periodobase']        = $PeriodoMatriculadoBase;
			$R_Datos['Periodo_1']          = $D_PeriododAnual[$l];
			$R_Datos['periodos']           = $P_Anual['Anual'][$l];
			$R_Datos['Total_Matriculados'] = $MatirucladosBase;//+$Matriculados_Dos (Poblacion Base)
			$R_Datos['Desercion_Anual']    = $DesercionAnula;//+$D_PeriodoDos
			$R_Datos['Porcentaje_Anual']   = number_format($Valor_final,'2',',','.');
			$R_Datos['Estudiante']         = $T_DesercionCruse;
			$R_Datos['E_Estrato']          = $E_EstratoMatriculados;
			
			$C_Datos[$Q] = $R_Datos; 
		}//for		       
	}//for
    return $C_Datos;
 }//public function DesercionAnual   
	
/*
* END
* Ivan Dario Quintero
* Septiembre 13
*/
	
	
 public function Carreras($codigoCarrera='',$Modalidad=''){
    global $db;
    
    if($codigoCarrera){
        $Condicion=' AND codigocarrera="'.$codigoCarrera.'"';
    }else{
        $Condicion='';
    }
    
    if($Modalidad){
        $CondicionModalida = 'codigomodalidadacademicasic="'.$Modalidad.'"';
    }else{
        $CondicionModalida = 'codigomodalidadacademicasic=200';
    }
    
      $SQL='SELECT 

            codigocarrera,
            nombrecarrera
            
            FROM 
            
            carrera
            
            WHERE
            
            '.$CondicionModalida.'
            AND
            codigocarrera NOT IN (354,6,428,7,120,600,605)
            '.$Condicion.'            
            
            ORDER BY nombrecarrera ASC;';
			
       if($Carreras=&$db->Execute($SQL)===false){
            echo 'Error en el SQL de las Carreras...<br><br>'.$SQL;
            die;
        }     
            
        $C_Carrera  = $Carreras->GetArray();    
        
       return $C_Carrera;
    
 }// Public function Carreras   
 public function Periodo($Opcion,$Periodo_ini='',$Periodo_fin=''){
    global  $db;
    
    if($Opcion=='Actual'){
        $Condicion ='WHERE  codigoestadoperiodo=1';
    }else if($Opcion=='Cadena'){
        
        $Condicion ='WHERE  codigoperiodo BETWEEN "'.$Periodo_ini.'" AND "'.$Periodo_fin.'"';
    }else if($Opcion=='Todos'){
        
        $Condicion ='ORDER BY codigoperiodo DESC';//codigoestadoperiodo, 
    }
    
      $SQL='SELECT 

            codigoperiodo,
            codigoestadoperiodo
            
            FROM 
            
            periodo
            
            '.$Condicion;
            
        if($Periodo=&$db->Execute($SQL)===false){
            echo 'Error en Calcular el Periodo...<br><br>'.$SQL;
            die;
        } 
        
       if($Opcion=='Actual'){
            return $Periodo->fields['codigoperiodo'];
       }else if($Opcion=='Cadena' || $Opcion=='Todos'){
        
            $C_Periodo  = $Periodo->GetArray();
            
            return $C_Periodo;
       }    
 }//public function Periodo
  public function Modalidades(){
    global  $db;
    
    
      $SQL='SELECT 

            codigomodalidadacademica,
            nombremodalidadacademica
            
            FROM 
            
            modalidadacademica
            
            WHERE codigoestado=100 
			ORDER BY nombremodalidadacademica ASC';
            
        if($Modalidad=&$db->Execute($SQL)===false){
            echo 'Error en Calcular la Modalidad...<br><br>'.$SQL;
            die;
        } 
        
        $C_Modalidad  = $Modalidad->GetArray();
            
        return $C_Modalidad;
 }//public function Modalidad
 
public function DisplaySemestral($CodigoPeriodo,$modalidad,$programa=-1){
	global $db;

	require_once("../../consulta/estadisticas/matriculasnew/funciones/obtener_datos.php");
	$datos_matriculas=new obtener_datos_matriculas($db,$CodigoPeriodo);
	
	$queryPrograma = "";
	if($programa!=-1 && $programa!="-1"){
		$queryPrograma=" AND codigocarrera='".$programa."' ";
	}
    
	$arrayP = str_split($CodigoPeriodo, strlen($CodigoPeriodo)-1);
    $P_Periodo=$arrayP[0]."-".$arrayP[1];
	
	$sql = "SELECT codigocarrera,nombrecarrera FROM carrera WHERE codigomodalidadacademica='".$modalidad."' AND fechavencimientocarrera>NOW()
	AND (EsAdministrativa=0 OR EsAdministrativa IS NULL)".$queryPrograma;
	$carreras = $db->GetAll($sql);
	$arregloResultados = array();
	foreach($carreras as $carrera){
        $array_codigosestudiante=$datos_matriculas->obtener_total_matriculados($carrera['codigocarrera'],'arreglo');
		$arregloResultados[$carrera["codigocarrera"]]["Matriculados"]=count($array_codigosestudiante);
		if($arregloResultados[$carrera["codigocarrera"]]["Matriculados"]>0){
		  
			$array_codigosestudiante=$datos_matriculas->obtener_datos_estudiantes_desercion($carrera["codigocarrera"],'arreglo');
           
			$arregloResultados[$carrera["codigocarrera"]]["Desercion"]=count($array_codigosestudiante);
			$arregloResultados[$carrera["codigocarrera"]]["Porcentaje"]=$arregloResultados[$carrera["codigocarrera"]]["Desercion"]*100/$arregloResultados[$carrera["codigocarrera"]]["Matriculados"];
		} else {
			$arregloResultados[$carrera["codigocarrera"]]["Desercion"] = 0;
			$arregloResultados[$carrera["codigocarrera"]]["Porcentaje"] =0;
		}
	} 
	?>
	
	<style type="text/css" title="currentStyle">
                @import "../data/media/css/demo_page.css";
                @import "../data/media/css/demo_table_jui.css";
                @import "../data/media/css/ColVis.css";
                @import "../data/media/css/TableTools.css";
                @import "../data/media/css/ColReorder.css";
                @import "../data/media/css/themes/smoothness/jquery-ui-1.8.4.custom.css";
                @import "../data/media/css/jquery.modal.css";
                
    </style>
   
    <script type="text/javascript" language="javascript" src="../data/media/js/jquery.js"></script>
    <!--<script type="text/javascript" charset="utf-8" src="../jquery/js/jquery-3.6.0.js"></script>-->	
    <script type="text/javascript" language="javascript" src="../js/jquery-ui-1.8.21.custom.min.js"></script>
    <script type="text/javascript" language="javascript" src="../data/media/js/jquery.dataTables.js"></script>
    <script type="text/javascript" charset="utf-8" src="../data/media/js/ColVis.js"></script>
    <script type="text/javascript" charset="utf-8" src="../data/media/js/ZeroClipboard.js"></script>
    <script type="text/javascript" charset="utf-8" src="../data/media/js/TableTools.js"></script>
    <script type="text/javascript" charset="utf-8" src="../data/media/js/FixedColumns.js"></script>
    <script type="text/javascript" charset="utf-8" src="../data/media/js/ColReorder.js"></script>
    <script type="text/javascript" language="javascript">
        
        $(document).ready( function () {//"sDom": '<Cfrltip>',
				var oTable = $('#example_1').dataTable( {
				    
  					"sScrollX": "100%",
					"sScrollXInner": "100,1%",
					"bScrollCollapse": true,
                    "bPaginate": true,
                    "aLengthMenu": [[50], [50,  "All"]],
                     "iDisplayLength": 50,
                    "sPaginationType": "full_numbers",
					"oColReorder": {
						"iFixedColumns": 1
					},
                    "oColVis": {
                            "buttonText": "Ver/Ocultar Columns",
                             "aiExclude": [ 0 ]
                      }
                    
                    
					
				} );
				//new FixedColumns( oTable );
                                
                                new FixedColumns( oTable, {
                                         "iLeftColumns": 2,
                                         "iLeftWidth": 550
				} );
                                
                                 var oTableTools = new TableTools( oTable, {
					"buttons": [
						"copy",
						"csv",
						"xls",
						"pdf",
					]
		         });
                         $('#demo').before( oTableTools.dom.container );
			} ); 
    </script>
    
    <input type="hidden" id="CodigoPeriodo" value="<?PHP echo $CodigoPeriodo?>" />    
    <div id="demo">
         <table cellpadding="0" cellspacing="0" border="1" class="display" id="example_1">
            <thead>
                <tr>
                    <th>N&deg;</th>
                    <th>Carrera</th>
					<th>Población Total <?PHP echo $P_Periodo; ?></th>
					<th>Deserción <?PHP echo $P_Periodo; ?></th>
					<th>Porcentaje Deserción (%) <?PHP echo $P_Periodo; ?></th>
      			</tr>
            </thead>
            <tbody>
            <?PHP 
                        
			$j = 1;
				$totalMatriculados =0;
				$totalDesercion = 0;			
            foreach($carreras as $carrera){
				$totalMatriculados += $arregloResultados[$carrera["codigocarrera"]]["Matriculados"];
				$totalDesercion += $arregloResultados[$carrera["codigocarrera"]]["Desercion"];
			?>
				<tr>
					<td><?PHP echo $j; ?></td>
                    <td><?PHP echo $carrera['nombrecarrera'];?></td>
                    <td><?PHP echo $arregloResultados[$carrera["codigocarrera"]]["Matriculados"]; ?></td>
                    <td><a style="cursor: pointer;" title="Ver Detalle Desercion" 
					onclick="VerDetallePoblacion('<?PHP echo $carrera["codigocarrera"]?>','<?PHP echo $CodigoPeriodo?>')">
					<?PHP echo $arregloResultados[$carrera["codigocarrera"]]["Desercion"]; ?></a></td>
                     <td><a style="cursor: pointer;" title="Ver Grafica de Situaciones Desercion" 
					 onclick="VerSituacion('<?PHP echo $carrera["codigocarrera"]?>','<?PHP echo $CodigoPeriodo?>')">
					 <?PHP echo number_format($arregloResultados[$carrera["codigocarrera"]]['Porcentaje'],'2',',','.')?>%</a></td>
                 </tr>    
			<?php	$j++;
			} //for
			$totalPorcentaje = $totalDesercion*100/$totalMatriculados;
			?>
              <tr>
                <td><?PHP echo $j; ?></td>
                <td>Total</td>
                <td><?PHP echo $totalMatriculados; ?></td>
                <td><?PHP echo $totalDesercion; ?></td>
                <td><?PHP echo number_format($totalPorcentaje,'2',',','.')?>%</td>
			  </tr>
            </tbody>        
         </table>
              <input type="hidden" id="Index" value="<?PHP echo $j; ?>" />
    </div>
<?php }//public function DisplaySemestral
  
 public function Display($CodigoPeriodo,$Op=''){ 
    global $db;
    
    //$CodigoPeriodo	='20081';
    
    $Periodo_Actual=$this->Periodo('Actual','','');
        
    $C_Periodo  = $this->Periodo('Cadena',$CodigoPeriodo,$Periodo_Actual);
    
    $P_num= count($C_Periodo);
    
    $P_num  = $P_num-1;
    
    
    
    //echo '<pre>';print_r($C_Periodo);echo 'num->'.$P_num;die;
    ?>
    <style type="text/css" title="currentStyle">
                @import "../data/media/css/demo_page.css";
                @import "../data/media/css/demo_table_jui.css";
                @import "../data/media/css/ColVis.css";
                @import "../data/media/css/TableTools.css";
                @import "../data/media/css/ColReorder.css";
                @import "../data/media/css/themes/smoothness/jquery-ui-1.8.4.custom.css";
                @import "../data/media/css/jquery.modal.css";
                
    </style>
   
    <script type="text/javascript" language="javascript" src="../data/media/js/jquery.js"></script>
    <!--<script type="text/javascript" charset="utf-8" src="../jquery/js/jquery-3.6.0.js"></script>-->	
    <script type="text/javascript" language="javascript" src="../js/jquery-ui-1.8.21.custom.min.js"></script>
    <script type="text/javascript" language="javascript" src="../data/media/js/jquery.dataTables.js"></script>
    <script type="text/javascript" charset="utf-8" src="../data/media/js/ColVis.js"></script>
    <script type="text/javascript" charset="utf-8" src="../data/media/js/ZeroClipboard.js"></script>
    <script type="text/javascript" charset="utf-8" src="../data/media/js/TableTools.js"></script>
    <script type="text/javascript" charset="utf-8" src="../data/media/js/FixedColumns.js"></script>
    <script type="text/javascript" charset="utf-8" src="../data/media/js/ColReorder.js"></script>
    <script type="text/javascript" language="javascript">
        
        $(document).ready( function () {//"sDom": '<Cfrltip>',
				var oTable = $('#example_1').dataTable( {
				    
  					"sScrollX": "100%",
					"sScrollXInner": "100,1%",
					"bScrollCollapse": true,
                    "bPaginate": true,
                    "aLengthMenu": [[50], [50,  "All"]],
                     "iDisplayLength": 50,
                    "sPaginationType": "full_numbers",
					"oColReorder": {
						"iFixedColumns": 1
					},
                    "oColVis": {
                            "buttonText": "Ver/Ocultar Columns",
                             "aiExclude": [ 0 ]
                      }
                    
                    
					
				} );
				//new FixedColumns( oTable );
                                
                                new FixedColumns( oTable, {
                                         "iLeftColumns": 2,
                                         "iLeftWidth": 550
				} );
                                
                                 var oTableTools = new TableTools( oTable, {
					"buttons": [
						"copy",
						"csv",
						"xls",
						"pdf",
					]
		         });
                         $('#demo').before( oTableTools.dom.container );
			} ); 
    </script>
    
    <input type="hidden" id="CodigoPeriodo" value="<?PHP echo $CodigoPeriodo?>" />    
    <div id="demo">
         <table cellpadding="0" cellspacing="0" border="1" class="display" id="example_1">
            <thead>
                <tr>
                    <th>N&deg;</th>
                    <th>Carrera</th>
                    <?PHP 
                    for($i=0;$i<$P_num;$i++){
                        
                            /***************************************/
                            $arrayP = str_split($C_Periodo[$i]['codigoperiodo'], strlen($C_Periodo[$i]['codigoperiodo'])-1);
                            
                            $P_Periodo=$arrayP[0]."-".$arrayP[1];
                            
                            /***************************************/
  
                        ?>
                        <th>Poblaci&oacute;n total <?PHP echo $P_Periodo?></th>
                        <th>Poblaci&oacute;n Deserci&oacute;n <?PHP echo $P_Periodo?></th>
                        <th>Porcentaje Deserci&oacute;n (%) <?PHP echo $P_Periodo?></th>
                        <?PHP
                    }//for
                    ?>
                </tr>
            </thead>
            <tbody>
            <?PHP 
            
            $C_Carrera = $this->Carreras();
            
            $C_Matricula    = array();
            $C_Desertores   = array();
            
            for($j=0;$j<count($C_Carrera);$j++){
                /************************************************************/
                ?>
                <tr>
                <?PHP
                for($i=0;$i<$P_num;$i++){
                    /********************************************************/
                    $R_Consulta  = $this->ConsultaDesercionSemestral($C_Carrera[$j]['codigocarrera'],$CodigoPeriodo);
                    
                    if($i==0){
                        ?>
                        <td><?PHP echo $j+1?></td>
                        <td>
                            <input type="hidden" id="CodigoCarrera_<?PHP echo $j?>" value="<?PHP echo $C_Carrera[$j]['codigocarrera']?>" />
                        <a onclick="Graficar('<?PHP echo $C_Carrera[$j]['codigocarrera']?>');" title="Click para ver Grafica" style="cursor: pointer;">    
                        <?PHP echo $C_Carrera[$j]['nombrecarrera'];?>
                        </a>
                        </td>
                        <?PHP
                    }
                    $C_Matricula [$C_Periodo[$i]['codigoperiodo']][]    = $R_Consulta[$C_Carrera[$j]['codigocarrera']]['Matriculados'][$i];
                    
                    $C_Desertores [$C_Periodo[$i]['codigoperiodo']][]    = $R_Consulta[$C_Carrera[$j]['codigocarrera']]['Desercion'][$i];
                    
                    //echo '<br><br>'.$C_Periodo[$i]['codigoperiodo'].'=='.$R_Consulta[$C_Carrera[$j]['codigocarrera']]['Periodo'][$i];
                    
                    //if($CodigoPeriodo==$R_Consulta[$C_Carrera[$j]['codigocarrera']]['Periodo'][$i]){
                    ?>
                    <td><?PHP echo $R_Consulta[$C_Carrera[$j]['codigocarrera']]['Matriculados'][$i]?></td>
                    <td><a style="cursor: pointer;" title="Ver Detalle Desercion" onclick="VerDetallePoblacion('<?PHP echo $C_Carrera[$j]['codigocarrera']?>','<?PHP echo $C_Periodo[$i]['codigoperiodo']?>','<?PHP echo $Op?>')"><?PHP echo $R_Consulta[$C_Carrera[$j]['codigocarrera']]['Desercion'][$i]?></a></td>
                    <td><a style="cursor: pointer;" title="Ver Grafica de Situaciones Desercion" onclick="VerSituacion('<?PHP echo $C_Carrera[$j]['codigocarrera']?>','<?PHP echo $C_Periodo[$i]['codigoperiodo']?>')"><?PHP echo number_format($R_Consulta[$C_Carrera[$j]['codigocarrera']]['Porcentaje'][$i],'2',',','.')?>%</a></td>
                    <?PHP                   
                    /********************************************************/
                    //}
                }//for
                ?>
                </tr>
                <?PHP
                /************************************************************/
            }//for
  
              ?>
              <tr>
                <td><?PHP echo $j+1?></td>
                <td><a onclick="VerBarras('GraficaProgramaTotal','<?PHP echo $CodigoPeriodo?>')" title="Ver Grafica Total Programas">Total</a></td>
                <?PHP 
                
                
                for($i=0;$i<$P_num;$i++){
                    $S_matriculado = 0;
                    $S_Desercion   = 0;
                    /********************************************************/
                    for($Q=0;$Q<count($C_Matricula[$C_Periodo[$i]['codigoperiodo']]);$Q++){
                        /****************************************************/
                        
                        $S_matriculado = $S_matriculado+$C_Matricula[$C_Periodo[$i]['codigoperiodo']][$Q];
                        $S_Desercion   = $S_Desercion+$C_Desertores[$C_Periodo[$i]['codigoperiodo']][$Q];
                        
                        /****************************************************/
                    }//for
                    ?>
                    <td><?PHP echo $S_matriculado?></td>
                    <td><a onclick="SituacionTotal('<?PHP echo $S_Desercion?>','<?PHP echo $C_Periodo[$i]['codigoperiodo']?>')" title="grafica Situacion Total" style="cursor: pointer;"><?PHP echo $S_Desercion?></a></td>
                    <?PHP 
                    $S_Porcentaje   = (($S_Desercion/$S_matriculado)*100);
                    ?>
                    <td>
                    <a  onclick="VerBarras('GraficaPrograma','<?PHP echo $C_Periodo[$i]['codigoperiodo']?>')" title="Ver Grafica Programas periodo <?PHP echo $C_Periodo[$i]['codigoperiodo']?>" style="cursor: pointer;"><?PHP echo number_format($S_Porcentaje,'2',',','.')?>%</a>
                    </td>
                    <?PHP
                    /********************************************************/
                }//for    
                ?>
              </tr>
              <input type="hidden" id="Index" value="<?PHP echo $j?>" />
            </tbody>        
         </table>
         <input type="hidden" id="Index" value="<?PHP echo $j?>" />
    </div>
    <?PHP
   // echo '<pre>';print_r($C_Matricula);
 }//public function Display
public function DisplayAnual($CodigoPeriodo,$tipo=1){
    /*************************************************/
    global $db;
    
	if($tipo==1){
		//desercion
		$title="Deserci&oacute;n";
		$title2 = "Desercion";
		$grafica = "GenerarGraficAnual";
	} else if($tipo==2){
		//retencion
		$title="Permanencia";
		$title2="Permanencia";
		$grafica = "GenerarGraficAnualRetencion";
	}
    $PeriodoAnual   = $this->PeriodosAnuales($CodigoPeriodo);
    
    //echo '<pre>';print_r($PeriodoAnual);
    
    $D_PeriododAnual = array();
    
    for($d=0;$d<count($PeriodoAnual['Anual']);$d++){
        
        $C_Data  = explode('-',$PeriodoAnual['Anual'][$d]);
        
        $D_PeriododAnual[]= $C_Data[0];
        
        if($C_Data[1]){
            
             $D_PeriododAnual[]= $C_Data[1];
            
        }
       
        
    }//for
    
    //echo '<pre>';print_r($D_PeriododAnual);
    ?>
    <style type="text/css" title="currentStyle">
                @import "../data/media/css/demo_page.css";
                @import "../data/media/css/demo_table_jui.css";
                @import "../data/media/css/ColVis.css";
                @import "../data/media/css/TableTools.css";
                @import "../data/media/css/ColReorder.css";
                @import "../data/media/css/themes/smoothness/jquery-ui-1.8.4.custom.css";
                @import "../data/media/css/jquery.modal.css";
                
    </style>
    <!--<style type="text/css" title="currentStyle">
                @import "../../../observatorio/data/media/css/demo_page.css";
                @import "../../../observatorio/data/media/css/demo_table_jui.css";
                @import "../../../observatorio/data/media/css/ColVis.css";
                @import "../../../observatorio/data/media/css/TableTools.css";
                @import "../../../observatorio/data/media/css/ColReorder.css";
                @import "../../../observatorio/data/media/css/themes/smoothness/jquery-ui-1.8.4.custom.css";
    </style>-->
    <script type="text/javascript" language="javascript" src="../data/media/js/jquery.js"></script>
    <!--<script type="text/javascript" charset="utf-8" src="../jquery/js/jquery-3.6.0.js"></script>-->	
    <script type="text/javascript" language="javascript" src="../js/jquery-ui-1.8.21.custom.min.js"></script>
    <script type="text/javascript" language="javascript" src="../data/media/js/jquery.dataTables.js"></script>
    <script type="text/javascript" charset="utf-8" src="../data/media/js/ColVis.js"></script>
    <script type="text/javascript" charset="utf-8" src="../data/media/js/ZeroClipboard.js"></script>
    <script type="text/javascript" charset="utf-8" src="../data/media/js/TableTools.js"></script>
    <script type="text/javascript" charset="utf-8" src="../data/media/js/FixedColumns.js"></script>
    <script type="text/javascript" charset="utf-8" src="../data/media/js/ColReorder.js"></script>
    <script type="text/javascript" language="javascript">
        
        $(document).ready( function () {//"sDom": '<Cfrltip>',
				var oTable = $('#example').dataTable( {
				    
  					 
  					"sScrollX": "100%",
					"sScrollXInner": "100,1%",
					"bScrollCollapse": true,
                    "bPaginate": true,
                    "aLengthMenu": [[50], [50,  "All"]],
                     "iDisplayLength": 50,
                    "sPaginationType": "full_numbers",
					"oColReorder": {
						"iFixedColumns": 1
					},
                    "oColVis": {
                            "buttonText": "Ver/Ocultar Columns",
                             "aiExclude": [ 0 ]
                      }
                    
                    
					
				} );
				//new FixedColumns( oTable );
                                
                                new FixedColumns( oTable, {
                                         "iLeftColumns": 2,
                                         "iLeftWidth": 350
				} );  
                                
                                 var oTableTools = new TableTools( oTable, {
					"buttons": [
						"copy",
						"csv",
						"xls",
						"pdf",
					]
		         });
                         $('#demo_1').before( oTableTools.dom.container );
			} ); 
        
    </script>
    
    <input type="hidden" id="CodigoPeriodo" value="<?PHP echo $CodigoPeriodo?>" />    
    <div id="demo_1">
         <table cellpadding="0" cellspacing="0" border="1" class="display" id="example">
            <thead>
                <tr>
                    <th>N&deg;</th>
                    <th>Carrera</th>
                    <?PHP 
                    $P_num = count($D_PeriododAnual);//$PeriodoAnual['Anual']
                   					
                    //echo '<pre>';print_r($PeriodoAnual['Anual']);
                   
                    $Index  = '';
                    
                    for($i=0;$i<$P_num;$i++){
                        
                            /***************************************/
                            //$C_Periodo  = explode('-',$D_PeriododAnual[$i]);
                            /***************************************/
                            if($D_PeriododAnual[$i]){
                                /************************************/
                                
                                $arrayP     = str_split($D_PeriododAnual[$i], strlen($D_PeriododAnual[$i])-1);
                                
                                
                
                                $P = $arrayP[0]-1;
                                      
                                                        
                                $PeriodoDesercionInicio = $P.$arrayP[1];
                                
                                
                                $X  = $arrayP[1]-1;                        
                                
                                if($X==0){
                                    
                                    $O = $arrayP[0]-1;
                                    
                                    $Periodobase = $O.'1';
                                    
                                }else{
                                    
                                   $O = $arrayP[0]-1; 
                                   $Periodobase = $O.'2'; 
                                }
                                
                                
                                $arrayP = str_split($PeriodoDesercionInicio, strlen($PeriodoDesercionInicio)-1);
                        
                                
                                
                                if($arrayP[1]==1){
                                    
                                    $Year2   = $arrayP[0].'2';
                                    
                                }else{
                                    
                                    $A  = $arrayP[0]+1;
                                    
                                    $Year2   = $A.'1';
                                } 
                                
                                $arrayP2 = str_split($Year2, strlen($Year2)-1);
                
                                 if($arrayP2[1]==1){
                                    
                                    $Year3   = $arrayP2[0].'2';
                                    
                                }else{
                                    
                                    $B  = $arrayP2[0]+1;
                                    
                                    $Year3   = $B.'1';
                                } 
                                
                                $Index  = $i;
                                /************************************/
                                
                                 $arrayB = str_split($Periodobase, strlen($Periodobase)-1);
                                 
                                 $Periodo_B = $arrayB[0].'-'.$arrayB[1];
                                 
                                 $arrayD = str_split($Year3, strlen($Year3)-1);
                                 
                                 $Periodo_D = $arrayD[0].'-'.$arrayD[1];
                             ?>
                            <th>Poblaci&oacute;n total <?PHP echo $Periodo_B?></th>
                            <th>Poblaci&oacute;n <?PHP echo $title." ".$Periodo_D?></th>
                            <th>Porcentaje <?PHP echo $title." ".$Periodo_D?> (%) </th>
                            <?PHP    
                            }//if
                         
                    }//for
                    ?>
                </tr>
            </thead>
            <tbody>
            <?PHP 
            //$D_Anual    = $this->DesercionAnual($CodigoPeriodo);
            
            $C_Carrera  = $this->Carreras();	
			                        
            for($j=0;$j<count($C_Carrera);$j++)
			{//for
              
                /********************************************************************/
                ?>
                <tr>
                    <td align="center"><?PHP echo $j+1?></td>
                    <td ><a onclick="<?php echo $grafica; ?>('<?PHP echo $C_Carrera[$j]['codigocarrera']?>')" title="Ver Grafica"><?PHP echo $C_Carrera[$j]['nombrecarrera']?></a></td>
                    <?PHP 
                     for($i=0;$i<$P_num;$i++)
					 {//for						 
                        /*******************************************************************/
                        
                            $Periodo = $D_PeriododAnual[$i];//$PeriodoAnual['Anual'][$i];
                            
                             $D_Anual = $this->ConsultaDesercionAnual($C_Carrera[$j]['codigocarrera'],$Periodo);
                                                          
							 if($tipo==1){
								 $poblacion = $D_Anual[$C_Carrera[$j]['codigocarrera']]['Desercion'][0];
								 $porcentaje = $D_Anual[$C_Carrera[$j]['codigocarrera']]['Porcentaje'][0];
							 } else if ($tipo==2){
								 $poblacion = $D_Anual[$C_Carrera[$j]['codigocarrera']]['Matriculados'][0]-$D_Anual[$C_Carrera[$j]['codigocarrera']]['Desercion'][0];
								 $porcentaje = 100-$D_Anual[$C_Carrera[$j]['codigocarrera']]['Porcentaje'][0];
							 }
                            /****************************************************************************/
                            ?>
                            <td><center><?PHP echo $D_Anual[$C_Carrera[$j]['codigocarrera']]['Matriculados'][0]?></center></td>
                            <td><center><?php if($tipo==1){ ?><a style="cursor: pointer;" title="Ver Poblacion <?php echo $title2; ?>" onclick="VerDetallePoblacionAnual('<?PHP echo $C_Carrera[$j]['codigocarrera']?>','<?PHP echo $Periodo?>')" ><?php } ?>
							<?PHP echo $poblacion?><?php if($tipo==1){ ?></a><?php } ?></center></td>
                            <td><center><a style="cursor: pointer;" title="Ver Grafica de Situaciones" 
					 onclick="VerSituacionAnual('<?PHP echo $C_Carrera[$j]['codigocarrera']?>','<?PHP echo $Periodo?>')"><?PHP echo number_format($porcentaje,'2',',','.')?>%</a></center></td>
                            <?PHP
                            /****************************************************************************/
                           
                        /*******************************************************************/
                    }//for
                    ?>
                </tr>
                <?PHP
                /********************************************************************/
            }//for
           
            ?>
             <tr>
                <td align="center"><?PHP echo $j+1?></td>
                <td>Total</td>
                <?PHP 
                for($i=0;$i<count($D_PeriododAnual);$i++)
				{    
                    $Periodo = $D_PeriododAnual[$i];
					
					$Modalidad = "200";
                    
                    $R_Total  = $this->SumaTotalAnual($Periodo, $Modalidad);
					if($tipo==1)
					{
						$poblacion = $R_Total['TotalDesercion'];
						$porcentaje = $R_Total['TotalPorcentaje'];
					} else if ($tipo==2)
					{
						$poblacion = $R_Total['TotalMatriculados']-$R_Total['TotalDesercion'];
						$porcentaje = 100-$R_Total['TotalPorcentaje'];
					}
                    
                    ?>
                    <td><center><?PHP echo $R_Total['TotalMatriculados']?></center></td>
                    <td><center><?PHP echo $poblacion?></center></td>
                    <td><center><?PHP echo $porcentaje?>%</center></td>
                    <?PHP
                }//for
                ?>
             </tr> 
            </tbody>        
         </table>
         <input type="hidden" id="Index" value="<?PHP echo $j?>" />
    </div>
    <?PHP
   
    
    /*************************************************/
}//public function DisplayAnual
public function Consola(){
    global $db;
    
    ?>
	<style>	
	#definicionesDesercion{
		margin-left:60px;
	}
	#definicionesDesercion h5{
        font-family:Georgia,serif;
		color:#4E443C;
		font-size: 16px;
		font-variant: small-caps; text-transform: none; font-weight: 100; margin-bottom: 0;
	}
	#definicionesDesercion p{
			font-family: "Helvetica Neue", "Lucida Grande", Helvetica, Arial, Verdana, sans-serif;
			font-size: 14px;
			margin-top: .5em; color: #666;
	}

	#definicionesDesercion h5::first-letter{
        font-family:Georgia,serif;
		font-size: .9em;
			font-weight: bold;
		text-transform:uppercase;
		letter-spacing:2px; 
	}
	</style>
    <script type="text/javascript">
		$(document).on('change', "#TypeDesercion", function(){
			//console.log(idTab);
			var idTab = $("#TypeDesercion").val();
			if(idTab==3 || idTab==1){
				//pintar modalidad y programas academicos
				$("#filaModalidad").show();
			} else{
				$("#filaModalidad").hide();
			}
		});
		
		$(document).on('change', "#ModalidadDesercion", function(){
			var mod = $("#ModalidadDesercion").val();
			if(mod==-1 || mod=="-1"){
				$("#ProgramaDesercion").html("<option value='-1'></option>");
			} else {
				//console.log(idTab);
				$.ajax({//Ajax
				  type: 'POST',
				  url: '../../utilidades/modalidades_programas/lookForCareersByModalidad.php',
				  async: false,
				  dataType: 'html',
				  data:({modalidad:mod}),
				  error:function(objeto, quepaso, otroobj){alert('Error de Conexión , Favor Vuelva a Intentar');},
				  success: function(data){
						var html = '<option value="-1" selected></option>';
                                     var i = 0;
									 var array = JSON.parse(data);
                                        while(array.length>i){
                                            html = html + '<option value="'+array[i]["value"]+'" >'+array[i]["label"]+'</option>';
                                            i = i + 1;
                                        }                                    
						$('#ProgramaDesercion').html(html);
					}//data 
				}); //AJAX
			}
		});
	
	</script>
    <div id="container">
            <fieldset >
                <legend>Deserci&oacute;n</legend>
                <table border="0" cellpadding="0" cellspacing="0" class="display" align="center">
                    <thead>
                        <tr>
                            <th class="titulo_label">Periodo</th>
                            <th>
                                <select id="Periodo" name="Periodo" style="width: 50%;text-align: center;">
                                    <option value="-1"></option>
                                    <?PHP 
                                    $C_Periodo=$this->Periodo('Todos','','');
                                    
                                    for($i=0;$i<count($C_Periodo);$i++){
                                        ?>
                                        <option value="<?PHP echo $C_Periodo[$i]['codigoperiodo']?>"><?PHP echo $C_Periodo[$i]['codigoperiodo']?></option>
                                        <?PHP
                                    }//for
                                    ?>
                                </select>
                            </th>
                            <th>&nbsp;</th>
                            <th class="titulo_label">Tipo Deserci&oacute;n</th>
                            <th>
                                <select id="TypeDesercion" name="TypeDesercion" style="width: auto;">
                                    <option value="-1"></option>
                                    <option value="1">Anual</option>
                                    <option value="2">Cohorte</option>
                                    <option value="3">Semestral</option>
                                    <option value="0">Semestral Histórica</option>
                                </select>
                            </th>
                        </tr>
						<tr style="display:none" id="filaModalidad">
							<th  class="titulo_label">Modalidad académica</th>
							<th>
								<select id="ModalidadDesercion" name="modalidad" style="width: 50%;">
									<option value="-1"></option>
									<?php $C_Modalidad=$this->Modalidades();
									for($i=0;$i<count($C_Modalidad);$i++){
                                        ?>
                                        <option value="<?PHP echo $C_Modalidad[$i]['codigomodalidadacademica']?>"><?PHP echo $C_Modalidad[$i]['nombremodalidadacademica']?></option>
                                        <?PHP
                                    }//for
                                    ?>
                                </select>
							</th>
							<th>&nbsp;</th>
							<th  class="titulo_label">Programa académico</th>
							<th>
								<select id="ProgramaDesercion" name="codigocarrera" style="width: auto;">
									<option value="-1"></option>
								</select>
							</th>
						</tr>
                        <tr>
                            <th colspan="5" align="center">&nbsp;</th>
                        </tr>
                        <tr>
                            <th colspan="5" align="center"><button class="submit" type="button" tabindex="3" onclick="CargarInfo()">Buscar</button></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="5" align="center">
                                <hr style="width:100%" align="center" />
                            </td>
                        </tr>
                        <tr>
                            <td colspan="5" align="center">
                                <div id="Rerporte" style="width: 95%;height:auto;" align="center">
                                    <?PHP 
                                   //$this->Display();
                                    ?>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <input type="hidden" id="Cadena" />
            </fieldset>
			<div id="definicionesDesercion">
				<h5>1. Deserción Semestral:</h5> 
				<p>Hace referencia al estudiante que estando matriculado en un periodo académico no se matricula en el siguiente, sin haberse graduado.</p>

 

			<h5>2. Deserción Semestral Histórica:</h5>
			<p>Hace referencia a todos los datos históricos de la deserción con los resultados a corte de las fechas institucionales.</p>

 

			<h5>3. Deserción Anual:</h5>
			<p>Hace referencia a aquellos estudiantes que estaban matriculados en “t-2” y debían seguir matriculados en “t” (dado que no se han graduado) y 
				no se encontraron matriculados en “t” ni en “t-1”. Por tanto la deserción por periodo es la relación entre los desertores por periodo en “t” sobre el 
				total de matriculados de “t-2”.</p>

			<h5>4. Deserción por Cohorte:</h5>
			<p>La deserción por cohorte contabiliza, de forma acumulada, aquellos estudiantes pertenecientes a una cohorte que han 
				tomado el estatus de desertor (Aquel estudiante que abandona la institución durante dos o más periodos consecutivos al momento del estudio). </p> 
			</div>
    </div> 
    <?PHP
  }//public function Consola
  public function CadenaInstitucional($CodigoPeriodo){
    global $db;
    
    /******************************************************************/
    $Periodo_Actual     = $this->Periodo('Actual','','');
        
    $C_Periodo          = $this->Periodo('Cadena',$CodigoPeriodo,$Periodo_Actual);
    
    $P_num= count($C_Periodo);
    
    $P_num = $P_num-1;
    
    $C_Total=$this->DesercionSemestral($CodigoPeriodo,'Institucional');
    
    $D_Total    = array();
    
    for($Q=0;$Q<$P_num;$Q++){//for
        /**************************************************************/
        if($C_Periodo[$Q]['codigoperiodo']==$C_Total[$Q]['Periodo']){
            /**********************************************************/
            $T = (($C_Total[$Q]['Total_D']/$C_Total[$Q]['Total_M'])*100);
            $D_Total['Total_D'][]=number_format($T,'2','.','');
            $D_Total['Periodo'][]=$C_Periodo[$Q]['codigoperiodo'];
            /**********************************************************/
        }//if
        /**************************************************************/
    }//for
    /******************************************************************/
    
    return $D_Total;
  }//public function CadenaInstitucional
  public function CadenaPrograma($CodigoPeriodo,$codigoCarrera,$P=''){
    global $db;
    /*******************************************************************/
        $Periodo_Actual = $this->Periodo('Actual','','');
        
        $C_Periodo      = $this->Periodo('Cadena',$CodigoPeriodo,$Periodo_Actual);
        
        $P_num= count($C_Periodo);
        
        $P_num = $P_num-1;
        
        $P_Datos=$this->DesercionSemestral($CodigoPeriodo,'Programas','1',$codigoCarrera);
        
       /************************************************************/ 
        for($i=0;$i<$P_num;$i++){
           /*******************************************/
            $arrayP = str_split($C_Periodo[$i]['codigoperiodo'], strlen($C_Periodo[$i]['codigoperiodo'])-1);
            $C_Datos['Periodo'][] = $arrayP[0]."-".$arrayP[1];
            //$C_Datos['Periodo'][]=$C_Periodo[$i]['codigoperiodo'];
           /*******************************************/ 
        }//for
        /**********************************************************/
        for($j=0;$j<1;$j++){//for count($C_Carrera)
        /********************************************************/
            for($x=0;$x<1;$x++){//for count($P_Datos)
            /*****************************************************/
                for($Q=0;$Q<$P_num;$Q++){//for
                    /*********************************************/
                    if($P_Datos['0'][$codigoCarrera][$Q]['Periodo']==$C_Periodo[$Q]['codigoperiodo']){//if
                       /******************************************/
                            $C_Datos['Desercion'][]=str_replace (',','.',$P_Datos[$x][$codigoCarrera][$Q]['PorcentajeDesercion']);
                            
                       /******************************************/ 
                    }//if
                    /*********************************************/
                }//for
            /*****************************************************/
            }//for
        /********************************************************/
        }//for
      if($P==1){
        return $C_Datos['Periodo'];
      }else{
      return $C_Datos;
      }  
    /*******************************************************************/
  }//public function CadenaPrograma
public function PeriodosAnuales($CodigoPeriodo){
    /*******************************/
    global $db;
    
        
        $Periodo_Actual = $this->Periodo('Actual','','');
        
        $C_Periodos     = $this->Periodo('Cadena',$CodigoPeriodo,$Periodo_Actual);
        
        
        
        $Periodos   = array();
        $count_C_Periodos = (int) count($C_Periodos);
        for($i=0;$i<$count_C_Periodos;$i++){
            /**********************************************/
                $Periodos['Periodo'][]=$C_Periodos[$i]['codigoperiodo'];
            /**********************************************/
        }//for
        
        $P_Anual    = array();
        $count_Periodos = (int) count($Periodos['Periodo']);
        for($j=0;$j<$count_Periodos;$j=$j+2){
            /****************************************************/
				if(($j+1) < $count_Periodos){
					$x  = $j+1;
					//echo $x." -- ".$j; //die;
					$P_Anual['Anual'][]=$Periodos['Periodo'][$j].'-'.$Periodos['Periodo'][$x];
				}
                
            /****************************************************/
        }//for
        //echo '<pre>';print_r($P_Anual); die;
     return $P_Anual;   
    /*******************************/
}//public function PeriodosAnuales 
public function CadenaAnualPrograma($CodigoPeriodo,$CodigoCarrera,$opcion){
    global $db;
    
    $PeriodoAnual   = $this->PeriodosAnuales($CodigoPeriodo);
    
    $D_Grafica  = array();
    $T_Grafica  = array();
    
    for($x=0;$x<count($PeriodoAnual['Anual']);$x++){
        
        $Periodos   = $PeriodoAnual['Anual'][$x];
        
        $C_Anual  = $this->ConsultaDesercionAnual($CodigoCarrera,$Periodos);
        
        //echo '<pre>';print_r($C_Anual);
        
        $D_Grafica['Periodo'][]          = $C_Anual[$CodigoCarrera]['Periodo'][0];
        $D_Grafica['desercionperiodo'][] = $C_Anual[$CodigoCarrera]['desercionperiodo'][0];
        $D_Grafica['Matriculados'][]     = $C_Anual[$CodigoCarrera]['Matriculados'][0];
        $D_Grafica['Desercion'][]        = $C_Anual[$CodigoCarrera]['Desercion'][0];
        $D_Grafica['Porcentaje'][]       = number_format($C_Anual[$CodigoCarrera]['Porcentaje'][0],'2','.','.');
        
		$Modalidad = "200";
        
        $C_Total    = $this->SumaTotalAnual($Periodos, $Modalidad);
        
        $T_Grafica['Periodo'][]         = $Periodos;
        $T_Grafica['Matriculados'][]    = $C_Total['TotalMatriculados'];
        $T_Grafica['Desercion'][]       = $C_Total['TotalPorcentaje'];
        $T_Grafica['Porcentaje'][]      = number_format($C_Total['TotalPorcentaje'],'2','.','.');
        
    }//for Anual
    
   
     
     if($opcion=='programa'){
        return $D_Grafica;
     }else if($opcion=='Total'){
        return $T_Grafica;
     }   
}//public function CadenaAnualPrograma 
public function ConsultaDesercionSemestral($CodigoCarrera,$Periodo){
    
   global $db;
   
   /*$CodigoCarrera  = 5;
   $Periodo        = 20092;*/
   
    $SQL_ConsultaDesercion='SELECT 

                            d.id_desercion,
                            d.codigocarrera,
                            dt.codigoperiodo,
                            dt.matriculados,
                            dt.desercion,
                            ((dt.desercion/dt.matriculados)*100) AS Porcentaje ,
                            dt.id_detalledesercion 
                            
                            FROM 
                            
                            desercion d INNER JOIN deserciondetalle dt ON d.id_desercion=dt.id_desercion
                            
                            WHERE
                            
                            d.codigocarrera="'.$CodigoCarrera.'"
                            AND
                            dt.codigoperiodo>="'.$Periodo.'"
                            AND
                            d.codigoestado=100
                            AND
                            dt.codigoestado=100
                            AND
                            d.tipodesercion=0';
                            
                   if($R_Consulta=&$db->Execute($SQL_ConsultaDesercion)===false){
                     echo 'Error en el SQL de la Desercion Semestral ....<br><br>'.$SQL_ConsultaDesercion;
                     die;
                   }         
           
                    
           $C_Resultado = array();
           
           while(!$R_Consulta->EOF){
            /**********************************************************/
                $SQL='SELECT codigoestudiante FROM desercionEstudiante WHERE id_detalledesercion="'.$R_Consulta->fields['id_detalledesercion'].'"';
                
                if($Estudiantes=&$db->Execute($SQL)===false){
                    echo 'Error en el SQL de los Estudiantes Desercion...<br><br>'.$SQL;
                    die;
                }
                
                $C_Estudiantes = $Estudiantes->GetArray();
                
                $E_Estudiantes = array();
                
                for($i=0;$i<count($C_Estudiantes);$i++){
                    
                    $E_Estudiantes[] = $C_Estudiantes[$i]['codigoestudiante'];
                    
                }/*for*/
                
                $SQL_Estrato='SELECT cantidad, tipo FROM desercionestrato WHERE iddetalledesercion="'.$R_Consulta->fields['id_detalledesercion'].'"';
                
                if($EstratoMatriculados=&$db->Execute($SQL_Estrato)===false){
                    echo 'Error en el SQl ...<br><br>'.$SQL_Estrato;
                    die;
                }
                
                $C_Estratomatriculados = $EstratoMatriculados->GetArray();
                
                $M_Estratos = array();
                
                for($j=0;$j<count($C_Estratomatriculados);$j++){
                    
                    $M_Estratos['Cantidad'][] = $C_Estratomatriculados[$j]['cantidad'];
                    $M_Estratos['tipo'][]     = $C_Estratomatriculados[$j]['tipo'];
                    
                }/*for*/
                
            /**********************************************************/
              $C_Resultado[$CodigoCarrera]['Periodo'][]         = $R_Consulta->fields['codigoperiodo'];
              $C_Resultado[$CodigoCarrera]['Matriculados'][]    = $R_Consulta->fields['matriculados'];
              $C_Resultado[$CodigoCarrera]['Desercion'][]       = $R_Consulta->fields['desercion'];
              $C_Resultado[$CodigoCarrera]['Porcentaje'][]      = $R_Consulta->fields['Porcentaje'];
              $C_Resultado[$CodigoCarrera]['Estudiantes'][]     = $E_Estudiantes;
              $C_Resultado[$CodigoCarrera]['E_Matriculados'][]  = $M_Estratos;
            /**********************************************************/
            $R_Consulta->MoveNext();
           }         
        
        return $C_Resultado;           
   }//public function ConsultaDesercionSemestral
  public function ConsultaDesercionAnual($CodigoCarrera,$Periodo){
    global $db;
    
	$SQL_ConsultaDesercion='SELECT
								d.id_desercion,
								d.codigocarrera,
								dt.periodos,
								dt.matriculados,
								dt.desercion,
								(
									(
										dt.desercion / dt.matriculados
									) * 100
								) AS Porcentaje,
								dt.desercionperiodo
							FROM
								desercion d
							INNER JOIN deserciondetalle dt ON d.id_desercion = dt.id_desercion
							WHERE
								d.codigocarrera = "'.$CodigoCarrera.'"
							AND dt.desercionperiodo = "'.$Periodo.'"
							AND d.codigoestado = 100
							AND dt.codigoestado = 100
							AND d.tipodesercion = 1
							GROUP BY
								d.id_desercion';
	  
                            
                   if($R_Consulta=&$db->Execute($SQL_ConsultaDesercion)===false){
                     echo 'Error en el SQL de la Desercion Semestral ....<br><br>'.$SQL_ConsultaDesercion;
                     die;
                   }         
                    
           $C_Resultado = array();
           while(!$R_Consulta->EOF){
            /**********************************************************/
              $C_Resultado[$CodigoCarrera]['Periodo'][]                  = $R_Consulta->fields['periodos'];
              $C_Resultado[$CodigoCarrera]['desercionperiodo'][]         = $R_Consulta->fields['desercionperiodo'];
              $C_Resultado[$CodigoCarrera]['Matriculados'][]             = $R_Consulta->fields['matriculados'];
              $C_Resultado[$CodigoCarrera]['Desercion'][]                = $R_Consulta->fields['desercion'];
              $C_Resultado[$CodigoCarrera]['Porcentaje'][]               = $R_Consulta->fields['Porcentaje'];
            /**********************************************************/
            $R_Consulta->MoveNext();
           }         
        
        return $C_Resultado;       
    
  }//public function ConsultaDesercionAnual 
   public function RetencionSemestral($CodigoPeriodo){
    global $db;
    
    $Periodo_Actual=$this->Periodo('Actual','','');
        
    $C_Periodo  = $this->Periodo('Cadena',$CodigoPeriodo,$Periodo_Actual);
    
    $P_num= count($C_Periodo);
    
    $P_num  = $P_num-1;
    
    
    
    //echo '<pre>';print_r($C_Periodo);echo 'num->'.$P_num;die;
    ?>
    <style type="text/css" title="currentStyle">
                @import "../data/media/css/demo_page.css";
                @import "../data/media/css/demo_table_jui.css";
                @import "../data/media/css/ColVis.css";
                @import "../data/media/css/TableTools.css";
                @import "../data/media/css/ColReorder.css";
                @import "../data/media/css/themes/smoothness/jquery-ui-1.8.4.custom.css";
                @import "../data/media/css/jquery.modal.css";
                
    </style>
    <!--<style type="text/css" title="currentStyle">
                @import "../../../observatorio/data/media/css/demo_page.css";
                @import "../../../observatorio/data/media/css/demo_table_jui.css";
                @import "../../../observatorio/data/media/css/ColVis.css";
                @import "../../../observatorio/data/media/css/TableTools.css";
                @import "../../../observatorio/data/media/css/ColReorder.css";
                @import "../../../observatorio/data/media/css/themes/smoothness/jquery-ui-1.8.4.custom.css";
    </style>-->
    <script type="text/javascript" language="javascript" src="../data/media/js/jquery.js"></script>
    <script type="text/javascript" charset="utf-8" src="../jquery/js/jquery-3.6.0.js"></script>
    <script type="text/javascript" language="javascript" src="../data/media/js/jquery.dataTables.js"></script>
    <script type="text/javascript" charset="utf-8" src="../data/media/js/ColVis.js"></script>
    <script type="text/javascript" charset="utf-8" src="../data/media/js/ZeroClipboard.js"></script>
    <script type="text/javascript" charset="utf-8" src="../data/media/js/TableTools.js"></script>
    <script type="text/javascript" charset="utf-8" src="../data/media/js/FixedColumns.js"></script>
    <script type="text/javascript" charset="utf-8" src="../data/media/js/ColReorder.js"></script>
    <script type="text/javascript" language="javascript">
        
        $(document).ready( function () {//"sDom": '<Cfrltip>',
				var oTable = $('#example_1').dataTable( {
				    
  					"sScrollX": "100%",
					"sScrollXInner": "100,1%",
					"bScrollCollapse": true,
                    "bPaginate": true,
                    "aLengthMenu": [[50], [50,  "All"]],
                     "iDisplayLength": 50,
                    "sPaginationType": "full_numbers",
					"oColReorder": {
						"iFixedColumns": 1
					},
                    "oColVis": {
                            "buttonText": "Ver/Ocultar Columns",
                             "aiExclude": [ 0 ]
                      }
                    
                    
					
				} );
				//new FixedColumns( oTable );
                                
                                new FixedColumns( oTable, {
                                         "iLeftColumns": 2,
                                         "iLeftWidth": 550
				} );
                                
                                 var oTableTools = new TableTools( oTable, {
					"buttons": [
						"copy",
						"csv",
						"xls",
						"pdf",
					]
		         });
                         $('#demo').before( oTableTools.dom.container );
			} ); 
    </script>
    
    <input type="hidden" id="CodigoPeriodo" value="<?PHP echo $CodigoPeriodo?>" />    
    <div id="demo">
         <table cellpadding="0" cellspacing="0" border="1" class="display" id="example_1">
            <thead>
                <tr>
                    <th>N&deg;</th>
                    <th>Carrera</th>
                    <?PHP 
                    for($i=0;$i<$P_num;$i++){
                        
                            /***************************************/
                            $arrayP = str_split($C_Periodo[$i]['codigoperiodo'], strlen($C_Periodo[$i]['codigoperiodo'])-1);
                            
                            $P_Periodo=$arrayP[0]."-".$arrayP[1];
                            
                            /***************************************/
  
                        ?>
                        <th>Poblaci&oacute;n total <?PHP echo $P_Periodo?></th>
                        <th>Poblaci&oacute;n Permanencia <?PHP echo $P_Periodo?></th>
                        <th>Porcentaje Permanencia (%) <?PHP echo $P_Periodo?></th>
                        <?PHP
                    }//for
                    ?>
                </tr>
            </thead>
            <tbody>
            <?PHP 
            
            $C_Carrera = $this->Carreras();
            
            $C_Matricula    = array();
            $C_Retencion   = array();
            
            for($j=0;$j<count($C_Carrera);$j++){
                /************************************************************/
                ?>
                <tr>
                <?PHP
                for($i=0;$i<$P_num;$i++){
                    /********************************************************/
                    $R_Consulta  = $this->ConsultaDesercionSemestral($C_Carrera[$j]['codigocarrera'],$CodigoPeriodo);
                    if($i==0){
                        ?>
                        <td><?PHP echo $j+1?></td>
                        <td>
                            <input type="hidden" id="CodigoCarrera_<?PHP echo $j?>" value="<?PHP echo $C_Carrera[$j]['codigocarrera']?>" />
                        <a onclick="GraficarRetencion('<?PHP echo $C_Carrera[$j]['codigocarrera']?>');" title="Click para ver Grafica" style="cursor: pointer;">    
                        <?PHP echo $C_Carrera[$j]['nombrecarrera'];?>
                        </a>
                        </td>
                        <?PHP
                    }
                    $C_Matricula [$C_Periodo[$i]['codigoperiodo']][]    = $R_Consulta[$C_Carrera[$j]['codigocarrera']]['Matriculados'][$i];
                    
                    /******************************************************/
                    $Retencion = $R_Consulta[$C_Carrera[$j]['codigocarrera']]['Matriculados'][$i]-$R_Consulta[$C_Carrera[$j]['codigocarrera']]['Desercion'][$i];
                    /******************************************************/
                    
                    $C_Retencion [$C_Periodo[$i]['codigoperiodo']][]    = $Retencion;
                    ?>
                    <td><?PHP echo $R_Consulta[$C_Carrera[$j]['codigocarrera']]['Matriculados'][$i]?></td>
                    <td><?PHP echo $Retencion?></td>
                    <?PHP 
                    $Por_Retencion = (($Retencion/$R_Consulta[$C_Carrera[$j]['codigocarrera']]['Matriculados'][$i])*100);
                    ?>
                    <td><?PHP echo number_format($Por_Retencion,'2',',','.')?>%</td>
                    <?PHP                   
                    /********************************************************/
                }//for
                ?>
                </tr>
                <?PHP
                /************************************************************/
            }//for
  
              ?>
              <tr>
                <td><?PHP echo $j+1?></td>
                <td><a onclick="VerBarras('GraficaProgramaTotalRetencion','<?PHP echo $CodigoPeriodo?>')" title="Ver Grafica Total Programas">Total</a></td>
                <?PHP 
                
                
                for($i=0;$i<$P_num;$i++){
                    $S_matriculado = 0;
                    $S_Retencion   = 0;
                    /********************************************************/
                    for($Q=0;$Q<count($C_Matricula[$C_Periodo[$i]['codigoperiodo']]);$Q++){
                        /****************************************************/
                        
                        $S_matriculado = $S_matriculado+$C_Matricula[$C_Periodo[$i]['codigoperiodo']][$Q];
                        $S_Retencion   = $S_Retencion+$C_Retencion[$C_Periodo[$i]['codigoperiodo']][$Q];
                        
                        /****************************************************/
                    }//for
                    ?>
                    <td><?PHP echo $S_matriculado?></td>
                    <td><?PHP echo $S_Retencion?></td>
                    <?PHP 
                    $S_Porcentaje   = (($S_Retencion/$S_matriculado)*100);
                    ?>
                    <td>
                    <a  onclick="VerBarras('GraficaProgramaRetecion','<?PHP echo $C_Periodo[$i]['codigoperiodo']?>')" title="Ver Grafica Programas periodo <?PHP echo $C_Periodo[$i]['codigoperiodo']?>"><?PHP echo number_format($S_Porcentaje,'2',',','.')?>%</a>
                    </td>
                    <?PHP
                    /********************************************************/
                }//for    
                ?>
              </tr>
              <input type="hidden" id="Index" value="<?PHP echo $j?>" />
            </tbody>        
         </table>
         <input type="hidden" id="Index" value="<?PHP echo $j?>" />
    </div>
    <?PHP
    
   }//public function RetencionSemestral
   public function Retencion(){
        global $db;
    
    ?>
    <style>
	#definicionesDesercion{
		margin-left:60px;
	}
	#definicionesDesercion h5{
        font-family:Georgia,serif;
		color:#4E443C;
		font-size: 16px;
		font-variant: small-caps; text-transform: none; font-weight: 100; margin-bottom: 0;
	}
	#definicionesDesercion p{
			font-family: "Helvetica Neue", "Lucida Grande", Helvetica, Arial, Verdana, sans-serif;
			font-size: 14px;
			margin-top: .5em; color: #666;
	}

	#definicionesDesercion h5::first-letter{
        font-family:Georgia,serif;
		font-size: .9em;
			font-weight: bold;
		text-transform:uppercase;
		letter-spacing:2px; 
	}
	</style>
    <div id="container">
        <div class="titulo">Permanencia</div>
            <fieldset >
                <legend>Permanencia</legend>
                <table border="0" cellpadding="0" cellspacing="0" class="display" aling="center">
                    <thead>
                        <tr>
                            <th class="titulo_label">Periodo</th>
                            <th>
                                <select id="Periodo" name="Periodo" style="width: 50%;text-align: center;">
                                    <option value="-1"></option>
                                    <?PHP 
                                    $C_Periodo=$this->Periodo('Todos','','');
                                    
                                    for($i=0;$i<count($C_Periodo);$i++){
                                        ?>
                                        <option value="<?PHP echo $C_Periodo[$i]['codigoperiodo']?>"><?PHP echo $C_Periodo[$i]['codigoperiodo']?></option>
                                        <?PHP
                                    }//for
                                    ?>
                                </select>
                            </th>
                            <th>&nbsp;</th>
                            <th class="titulo_label">Tipo Permanencia</th>
                            <th>
                                <select id="TypeDesercion" name="TypeDesercion" style="width: auto;">
                                    <option value="-1"></option>
                                    <option value="1">Anual</option>
                                    <option value="2">Cohorte</option>
                                    <option value="3">Semestral</option>
                                    <option value="0">Semestral Histórica</option>
                                </select>
                            </th>
                        </tr>
                        <tr>
                            <th colspan="5" aling="center">&nbsp;</th>
                        </tr>
                        <tr>
                            <th colspan="5" aling="center"><button class="submit" type="button" tabindex="3" onclick="CargarInfo2()">Buscar</button></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="5" aling="center">
                                <hr style="width:100%;" aling="center" />
                            </td>
                        </tr>
                        <tr>
                            <td colspan="5" aling="center">
                                <div id="Rerporte" style="width: 95%;height:auto;" aling="center">
                                    <?PHP 
                                   //$this->Display();
                                    ?>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <input type="hidden" id="Cadena" />
            </fieldset>
			<div id="definicionesDesercion">
			<h5>1. Permanencia Semestral:</h5> 
			<p>Hace referencia al estudiante que estando matriculado en un periodo académico continua siendo estudiante (se matricula) en el siguiente periodo. </p>

			<h5>2. Permanencia Semestral Histórica:</h5>
			<p>Hace referencia a todos los datos históricos de permanencia con los resultados a corte de las fechas institucionales.</p>
 
			<h5>3. Permanencia Anual:</h5>
			<p>Hace referencia a aquellos estudiantes que estaban matriculados en “t-2” y siguieron matriculados en “t”. 
			Por tanto la permanencia por periodo es la relación entre los estudiantes matriculados en “t” sobre el total de matriculados de “t-2”.</p>

			<h5>4. Permanencia por Cohorte:</h5>
			<p>La permanencia por cohorte contabiliza, de forma acumulada, aquellos estudiantes pertenecientes a una cohorte que siguieron siendo estudiantes regulares de 
			dicha cohorte y no toman el estado de desertor (Aquel estudiante que abandona la institución durante dos o más periodos consecutivos al momento del estudio).</p> 
			</div>
    </div> 
    <?PHP
   }//public function Retencion
   public function CadenaProRetencion($CodigoPeriodo,$codigoCarrera,$P=''){
    global $db;
    /*******************************************************************/
        $Periodo_Actual = $this->Periodo('Actual','','');
        
        $C_Periodo      = $this->Periodo('Cadena',$CodigoPeriodo,$Periodo_Actual);
        
        $P_num= count($C_Periodo);
        
        $P_num = $P_num-1;
        
        $P_Datos=$this->DesercionSemestral($CodigoPeriodo,'Programas','1',$codigoCarrera);
        
        //echo '<pre>';print_r($P_Datos);die;
        
       /************************************************************/ 
        for($i=0;$i<$P_num;$i++){
           /*******************************************/
            $arrayP = str_split($C_Periodo[$i]['codigoperiodo'], strlen($C_Periodo[$i]['codigoperiodo'])-1);
            $C_Datos['Periodo'][] = $arrayP[0]."-".$arrayP[1];
            //$C_Datos['Periodo'][]=$C_Periodo[$i]['codigoperiodo'];
           /*******************************************/ 
        }//for
        /**********************************************************/
        for($j=0;$j<1;$j++){//for count($C_Carrera)
        /********************************************************/
            for($x=0;$x<1;$x++){//for count($P_Datos)
            /*****************************************************/
                for($Q=0;$Q<$P_num;$Q++){//for
                    /*********************************************/
                    if($P_Datos['0'][$codigoCarrera][$Q]['Periodo']==$C_Periodo[$Q]['codigoperiodo']){//if
                       /******************************************/
                       $Retencion  = $P_Datos[$x][$codigoCarrera][$Q]['TotalMatriculados']-$P_Datos[$x][$codigoCarrera][$Q]['Desercion'];
                       
                       $Porcentaje   = (($Retencion/$P_Datos[$x][$codigoCarrera][$Q]['TotalMatriculados'])*100);
                       
                            $C_Datos['Desercion'][]=number_format($Porcentaje,'2','.','');
                            
                       /******************************************/ 
                    }//if
                    /*********************************************/
                }//for
            /*****************************************************/
            }//for
        /********************************************************/
        }//for
      if($P==1){
        return $C_Datos['Periodo'];
      }else{
      return $C_Datos;
      }  
    /*******************************************************************/
  }//public function CadenaProRetencion
  public function CadenaRetenInstitucional($CodigoPeriodo){
    global $db;
    
    /******************************************************************/
    $Periodo_Actual     = $this->Periodo('Actual','','');
        
    $C_Periodo          = $this->Periodo('Cadena',$CodigoPeriodo,$Periodo_Actual);
    
    $P_num= count($C_Periodo);
    
    $P_num = $P_num-1;
    
    $C_Total=$this->DesercionSemestral($CodigoPeriodo,'Institucional');
    
    //echo '<pre>';print_r($C_Total);die;
    
    $D_Total    = array();
    
    for($Q=0;$Q<$P_num;$Q++){//for
        /**************************************************************/
        if($C_Periodo[$Q]['codigoperiodo']==$C_Total[$Q]['Periodo']){
            /**********************************************************/
            
            
            $R = ($C_Total[$Q]['Total_M']-$C_Total[$Q]['Total_D']);
            
            $T  = (($R/$C_Total[$Q]['Total_M'])*100);
            
            
            $D_Total['Total_R'][]=number_format($T,'2','.','');
            /**********************************************************/
        }//if
        /**************************************************************/
    }//for
    /******************************************************************/
    
    return $D_Total;
  }//public function CadenaRetenInstitucional
   public function Formularios(){
        global $db;
    
    ?>
    
    <div id="container">
        <div class="titulo"></div>
            <fieldset >
                <legend>Gr&aacute;ficas UEB vs Nacional</legend>
                <table border="0" cellpadding="0" cellspacing="0" class="display" aling="center">
                    <thead>
                        <tr>
                            <th class="titulo_label">Periodo</th>
                            <th>
                                <select id="Periodo" name="Periodo" style="width: 50%;text-align: center;">
                                    <option value="-1"></option>
                                    <?PHP 
                                    $C_Periodo=$this->Periodo('Todos','','');
                                    
                                    for($i=0;$i<count($C_Periodo);$i++){
                                        ?>
                                        <option value="<?PHP echo $C_Periodo[$i]['codigoperiodo']?>"><?PHP echo $C_Periodo[$i]['codigoperiodo']?></option>
                                        <?PHP
                                    }//for
                                    ?>
                                </select>
                            </th>
                            <th>&nbsp;</th>
                            <th class="titulo_label">Tipo Formulario</th>
                            <th>
                                <select id="TypeFormulario" name="TypeFormulario" style="width: auto;">
                                    <option value="-1"></option>
                                    <option value="0">Deserci&oacute;n</option>
                                    <option value="1">Permanencia</option>
                                </select>
                            </th>
                        </tr>
                        <tr>
                            <th colspan="5">&nbsp;</th>
                        </tr>
                        <tr>
                            <th colspan="5" aling="center">&nbsp;</th>
                        </tr>
                        <tr>
                            <th colspan="5" aling="center"><button class="submit" type="button" tabindex="3" onclick="return Validar();">Buscar</button></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="5" aling="center">
                                <hr style="width:95%;margin-left: 2.5%;" aling="center" />
                            </td>
                        </tr>
                        <tr>
                            <td colspan="5" aling="center">
                                <div id="Rerporte" style="width: 95%;margin-left: 2.5%;height:auto;" aling="center">
                                    <?PHP 
                                   //$this->Display();
                                    ?>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
                
                <input type="hidden" id="Cadena" />
            </fieldset>
    </div> 
    <?PHP
   }//public function Formularios
  public function CausasDesercion($Periodo,$Carrera,$Op){
    global $db;
    
    include_once('../../consulta/estadisticas/matriculasnew/funciones/obtener_datos.php');
    
    //echo 'Periodo-->'.$Periodo.'<br>'.$Carrera;die;
   // $Periodo = '20142';
    $datos_estadistica=new obtener_datos_matriculas($db,$Periodo);
    
   // $DesercionDato=$datos_estadistica->obtener_datos_estudiantes_desercion($Carrera,'arreglo');
    //$Carrera = 5;
    $DesercionDato2=$datos_estadistica->obtener_datos_estudiantes_desercion($Carrera,'arreglo');
   // obtener_datos_estudiantes_desercion
    
   // echo '<pre>';print_r($DesercionDato2);die;
    
    /*
    100	Perdida de la Calidad de Estudiante Academica
    101	Perdida de la Calidad de Estudiante Disciplinaria
    102	Perdida de la Calidad de Estudiante Administrativa
    103	Perdida de la Calidad de Estudiante Voluntaria
    104	Egresado
    105	Admitido que no Ingreso
    106	Preinscrito
    107	Inscrito
    108	Reserva de cupo
    109	Registro Anulado
    110	Pendiente Decision Consejo Facultad
    111	Inscrito sin pago
    112	Terminacion de curso o educacion no formal
    113	No Admitido
    114	En proceso de financiación
    115	Lista en Espera
    200	Prueba Academica
    300	Admitido
    301	Normal
    302	Solicitud reserva de cupo
    400	Graduado
    500	En proceso de Grado
    */
    
    $C_Situacion  = array();
    
    for($i=0;$i<count($DesercionDato2);$i++){
        /*********************************************************/
        switch($DesercionDato2[$i]['CodigoSalidad']){
            case '100':{
                $C_Situacion['P_Academica']['Codigo'][]=$DesercionDato2[$i]['CodigoSalidad'];
                $C_Situacion['P_Academica']['CodigoEstudiante'][]=$DesercionDato2[$i]['codigoestudiante'];
                $C_Situacion['P_Academica']['Count'] =  count($C_Situacion['P_Academica']['Codigo']);
                
            }break;
            case '101':{
                $C_Situacion['P_Disciplinaria']['Codigo'][]=$DesercionDato2[$i]['CodigoSalidad'];
                $C_Situacion['P_Disciplinaria']['CodigoEstudiante'][]=$DesercionDato2[$i]['codigoestudiante'];
                $C_Situacion['P_Disciplinaria']['Count'] =  count($C_Situacion['P_Disciplinaria']['Codigo']);
                
                
            }break;
            case '102':{
                $C_Situacion['P_Administrativa']['Codigo'][]=$DesercionDato2[$i]['CodigoSalidad'];
                $C_Situacion['P_Administrativa']['CodigoEstudiante'][]=$DesercionDato2[$i]['codigoestudiante'];
                $C_Situacion['P_Administrativa']['Count'] =  count($C_Situacion['P_Administrativa']['Codigo']);
                
                
            }break;
            case '103':{
                $C_Situacion['P_Voluntaria']['Codigo'][]=$DesercionDato2[$i]['CodigoSalidad'];
                $C_Situacion['P_Voluntaria']['CodigoEstudiante'][]=$DesercionDato2[$i]['codigoestudiante'];
                $C_Situacion['P_Voluntaria']['Count'] =  count($C_Situacion['P_Voluntaria']['Codigo']);
                
            }break;
               
            case '104':{
                $C_Situacion['Egresado']['Codigo'][]=$DesercionDato2[$i]['CodigoSalidad'];
                $C_Situacion['Egresado']['CodigoEstudiante'][]=$DesercionDato2[$i]['codigoestudiante'];
                $C_Situacion['Egresado']['Count'] =  count($C_Situacion['Egresado']['Codigo']);
                
            }break;
            case '105':{
                $C_Situacion['Admitido_no']['Codigo'][]=$DesercionDato2[$i]['CodigoSalidad'];
                $C_Situacion['Admitido_no']['CodigoEstudiante'][]=$DesercionDato2[$i]['codigoestudiante'];
                $C_Situacion['Admitido_no']['Count'] =  count($C_Situacion['Admitido_no']['Codigo']);
                
            }break;
            case '106':{
                $C_Situacion['Preinscrito']['Codigo'][]=$DesercionDato2[$i]['CodigoSalidad'];
                $C_Situacion['Preinscrito']['CodigoEstudiante'][]=$DesercionDato2[$i]['codigoestudiante'];
                $C_Situacion['Preinscrito']['Count'] =  count($C_Situacion['Preinscrito']['Codigo']);
                
            }break;
            case '107':{
                $C_Situacion['Inscrito']['Codigo'][]=$DesercionDato2[$i]['CodigoSalidad'];
                $C_Situacion['Inscrito']['CodigoEstudiante'][]=$DesercionDato2[$i]['codigoestudiante'];
                $C_Situacion['Inscrito']['Count'] =  count($C_Situacion['Inscrito']['Codigo']);
                
            }break;
            case '108':{
                $C_Situacion['Reserva_Cupo']['Codigo'][]=$DesercionDato2[$i]['CodigoSalidad'];
                $C_Situacion['Reserva_Cupo']['CodigoEstudiante'][]=$DesercionDato2[$i]['codigoestudiante'];
                $C_Situacion['Reserva_Cupo']['Count'] =  count($C_Situacion['Reserva_Cupo']['Codigo']);
                
            }break;
            case '109':{
                $C_Situacion['Registro_Anulado']['Codigo'][]=$DesercionDato2[$i]['CodigoSalidad'];
                $C_Situacion['Registro_Anulado']['CodigoEstudiante'][]=$DesercionDato2[$i]['codigoestudiante'];
                $C_Situacion['Registro_Anulado']['Count'] =  count($C_Situacion['Registro_Anulado']['Codigo']);
                
            }break;
            case '110':{
                $C_Situacion['PendienteConsejo']['Codigo'][]=$DesercionDato2[$i]['CodigoSalidad'];
                $C_Situacion['PendienteConsejo']['CodigoEstudiante'][]=$DesercionDato2[$i]['codigoestudiante'];
                $C_Situacion['PendienteConsejo']['Count'] =  count($C_Situacion['PendienteConsejo']['Codigo']);
                
            }break;
            case '111':{
                $C_Situacion['InsSinPago']['Codigo'][]=$DesercionDato2[$i]['CodigoSalidad'];
                $C_Situacion['InsSinPago']['CodigoEstudiante'][]=$DesercionDato2[$i]['codigoestudiante'];
                $C_Situacion['InsSinPago']['Count'] =  count($C_Situacion['InsSinPago']['Codigo']);
                
            }break;
            case '112':{
                $C_Situacion['CursoEduNoFormal']['Codigo'][]=$DesercionDato2[$i]['CodigoSalidad'];
                $C_Situacion['CursoEduNoFormal']['CodigoEstudiante'][]=$DesercionDato2[$i]['codigoestudiante'];
                $C_Situacion['CursoEduNoFormal']['Count'] =  count($C_Situacion['CursoEduNoFormal']['Codigo']);
                
            }break;
            case '113':{
                $C_Situacion['No_Admitido']['Codigo'][]=$DesercionDato2[$i]['CodigoSalidad'];
                $C_Situacion['No_Admitido']['CodigoEstudiante'][]=$DesercionDato2[$i]['codigoestudiante'];
                $C_Situacion['No_Admitido']['Count'] =  count($C_Situacion['No_Admitido']['Codigo']);
                
            }break;
            case '114':{
                $C_Situacion['EnProcesoFinanciacion']['Codigo'][]=$DesercionDato2[$i]['CodigoSalidad'];
                $C_Situacion['EnProcesoFinanciacion']['CodigoEstudiante'][]=$DesercionDato2[$i]['codigoestudiante'];
                $C_Situacion['EnProcesoFinanciacion']['Count'] =  count($C_Situacion['EnProcesoFinanciacion']['Codigo']);
                
            }break;
            case '115':{
                $C_Situacion['Lista_Espera']['Codigo'][]=$DesercionDato2[$i]['CodigoSalidad'];
                $C_Situacion['Lista_Espera']['CodigoEstudiante'][]=$DesercionDato2[$i]['codigoestudiante'];
                $C_Situacion['Lista_Espera']['Count'] =  count($C_Situacion['Lista_Espera']['Codigo']);
                
            }break;
            case '200':{
                $C_Situacion['Prueba_Academica']['Codigo'][]=$DesercionDato2[$i]['CodigoSalidad'];
                $C_Situacion['Prueba_Academica']['CodigoEstudiante'][]=$DesercionDato2[$i]['codigoestudiante'];
                $C_Situacion['Prueba_Academica']['Count'] =  count($C_Situacion['Prueba_Academica']['Codigo']);
                
            }break;
            case '300':{
                $C_Situacion['Admitido']['Codigo'][]=$DesercionDato2[$i]['CodigoSalidad'];
                $C_Situacion['Admitido']['CodigoEstudiante'][]=$DesercionDato2[$i]['codigoestudiante'];
                $C_Situacion['Admitido']['Count'] =  count($C_Situacion['Admitido']['Codigo']);
            }break;
            case '301':{
                $C_Situacion['Normal']['Codigo'][]=$DesercionDato2[$i]['CodigoSalidad'];
                $C_Situacion['Normal']['CodigoEstudiante'][]=$DesercionDato2[$i]['codigoestudiante'];
                $C_Situacion['Normal']['Count'] =  count($C_Situacion['Normal']['Codigo']);
                
            }break;
            case '302':{
                $C_Situacion['SolicitudReservaCupo']['Codigo'][]=$DesercionDato2[$i]['CodigoSalidad'];
                $C_Situacion['SolicitudReservaCupo']['CodigoEstudiante'][]=$DesercionDato2[$i]['codigoestudiante'];
                $C_Situacion['SolicitudReservaCupo']['Count'] =  count($C_Situacion['SolicitudReservaCupo']['Codigo']);
            }break;
            case '400':{
                $C_Situacion['Graduado']['Codigo'][]=$DesercionDato2[$i]['CodigoSalidad'];
                $C_Situacion['Graduado']['CodigoEstudiante'][]=$DesercionDato2[$i]['codigoestudiante'];
                $C_Situacion['Graduado']['Count'] =  count($C_Situacion['Graduado']['Codigo']);
            }break;
            case '500':{
                $C_Situacion['EnProceso_Grado']['Codigo'][]=$DesercionDato2[$i]['CodigoSalidad'];
                $C_Situacion['EnProceso_Grado']['CodigoEstudiante'][]=$DesercionDato2[$i]['codigoestudiante'];
                $C_Situacion['EnProceso_Grado']['Count'] =  count($C_Situacion['EnProceso_Grado']['Codigo']);
            }break;
        }
        /*********************************************************/
        $C_Situacion['CodigoEstudiante'][]=$DesercionDato2[$i]['codigoestudiante'];
        $C_Situacion['P_Salida'][]=$DesercionDato2[$i]['periodo_salida'];
        $C_Situacion['Semestre'][]=$DesercionDato2[$i]['semestre'];
    }//for
         
    
    $Num_P_Academica  = number_format(((count($C_Situacion['P_Academica']['Codigo'])*100)/count($DesercionDato2)),'2','.','.');
    if($Op==1){
        
        if($Num_P_Academica>0){
        
            $C_Situacion['Porcentaje'][] = $Num_P_Academica;
            $C_Situacion['Nombre'][] = 'Perdida Academica';
            $C_Situacion['Codigo'][] = '100';
            $C_Situacion['Num'][] =  count($C_Situacion['P_Academica']['Codigo']);            
        }
        
    }else{
        
            $C_Situacion['Porcentaje'][] = $Num_P_Academica;
            $C_Situacion['Nombre'][] = 'Perdida Academica';
            $C_Situacion['Codigo'][] = '100';
        
    }
    
    
    $Num_P_Disciplinaria = number_format(((count($C_Situacion['P_Disciplinaria']['Codigo'])*100)/count($DesercionDato2)),'2','.','.');
     
    if($Op==1){    
        if($Num_P_Disciplinaria>0){
            
            $C_Situacion['Porcentaje'][] = $Num_P_Disciplinaria;
            $C_Situacion['Nombre'][] = 'Perdida Disiplinaria';
            $C_Situacion['Codigo'][] = '101';
            $C_Situacion['Num'][] =  count($C_Situacion['P_Disciplinaria']['Codigo']);
        }
    }else{
        
            $C_Situacion['Porcentaje'][] = $Num_P_Disciplinaria;
            $C_Situacion['Nombre'][] = 'Perdida Disiplinaria';
            $C_Situacion['Codigo'][] = '101';
    } 
    
    $Num_P_Administrativa = number_format(((count($C_Situacion['P_Administrativa']['Codigo'])*100)/count($DesercionDato2)),'2','.','.');
    
    if($Op==1){
        if($Num_P_Administrativa>0){
            
            $C_Situacion['Porcentaje'][] = $Num_P_Administrativa;
            $C_Situacion['Nombre'][] = 'Perdida Administrativa';
            $C_Situacion['Codigo'][] = '102';
            $C_Situacion['Num'][] =  count($C_Situacion['P_Administrativa']['Codigo']);
            
        }
    }else{
            $C_Situacion['Porcentaje'][] = $Num_P_Administrativa;
            $C_Situacion['Nombre'][] = 'Perdida Administrativa';
            $C_Situacion['Codigo'][] = '102';
        
    }
    
    $Num_P_Voluntaria = number_format(((count($C_Situacion['P_Voluntaria']['Codigo'])*100)/count($DesercionDato2)),'2','.','.');
    
    if($Op==1){
        if($Num_P_Voluntaria>0){
            
            $C_Situacion['Porcentaje'][] = $Num_P_Voluntaria;
            $C_Situacion['Nombre'][] = 'Perdida Voluntaria';
            $C_Situacion['Codigo'][] = '103';
            $C_Situacion['Num'][] =  count($C_Situacion['P_Voluntaria']['Codigo']);
        }
    }else{
        
            $C_Situacion['Porcentaje'][] = $Num_P_Voluntaria;
            $C_Situacion['Nombre'][] = 'Perdida Voluntaria';
            $C_Situacion['Codigo'][] = '103';
    }
    
    $Num_Egresado = number_format(((count($C_Situacion['Egresado']['Codigo'])*100)/count($DesercionDato2)),'2','.','.');
    
    if($Op==1){
        if($Num_Egresado>0){
            
            $C_Situacion['Porcentaje'][] = $Num_Egresado;
            $C_Situacion['Nombre'][] = 'Egresado';
            $C_Situacion['Codigo'][] = '104';
            $C_Situacion['Num'][] =  count($C_Situacion['Egresado']['Codigo']);
        }
    }else{
            $C_Situacion['Porcentaje'][] = $Num_Egresado;
            $C_Situacion['Nombre'][] = 'Egresado';
            $C_Situacion['Codigo'][] = '104';
    }
    
    $Num_Admitido_no = number_format(((count($C_Situacion['Admitido_no']['Codigo'])*100)/count($DesercionDato2)),'2','.','.');
    
    
    if($Op==1){
        if($Num_Admitido_no>0){
            
            $C_Situacion['Porcentaje'][] = $Num_Admitido_no;
            $C_Situacion['Nombre'][] = 'Admitido que no Ingreso';
            $C_Situacion['Codigo'][] = '105';
            $C_Situacion['Num'][] =  count($C_Situacion['Admitido_no']['Codigo']);
        }
    }
    
    $Num_Preinscrito = number_format(((count($C_Situacion['Preinscrito']['Codigo'])*100)/count($DesercionDato2)),'2','.','.');
    
    if($Op==1){
        if($Num_Preinscrito>0){
            
            $C_Situacion['Porcentaje'][] = $Num_Preinscrito;
            $C_Situacion['Nombre'][] = 'Preinscrito';
            $C_Situacion['Codigo'][] = '106';
            $C_Situacion['Num'][] =  count($C_Situacion['Preinscrito']['Codigo']);
        }
    }else{
            $C_Situacion['Porcentaje'][] = $Num_Preinscrito;
            $C_Situacion['Nombre'][] = 'Preinscrito';
            $C_Situacion['Codigo'][] = '106';
    }
    
    $Num_Inscrito = number_format(((count($C_Situacion['Inscrito']['Codigo'])*100)/count($DesercionDato2)),'2','.','.');
    
    if($Op==1){
        if($Num_Inscrito>0){
            
            $C_Situacion['Porcentaje'][] = $Num_Inscrito;
            $C_Situacion['Nombre'][] = 'Inscrito';
            $C_Situacion['Codigo'][] = '107';
            $C_Situacion['Num'][] =  count($C_Situacion['Inscrito']['Codigo']);
        }
    }else{
            $C_Situacion['Porcentaje'][] = $Num_Inscrito;
            $C_Situacion['Nombre'][] = 'Inscrito';
            $C_Situacion['Codigo'][] = '107';
    }
   
    $Num_Reserva_Cupo = number_format(((count($C_Situacion['Reserva_Cupo']['Codigo'])*100)/count($DesercionDato2)),'2','.','.');
    
    if($Op==1){
        if($Num_Reserva_Cupo>0){
            
            $C_Situacion['Porcentaje'][] = $Num_Reserva_Cupo;
            $C_Situacion['Nombre'][] = 'Reserva de cupo';
            $C_Situacion['Codigo'][] = '108';
            $C_Situacion['Num'][] =  count($C_Situacion['Reserva_Cupo']['Codigo']);
        }
    }else{
            $C_Situacion['Porcentaje'][] = $Num_Reserva_Cupo;
            $C_Situacion['Nombre'][] = 'Reserva de cupo';
            $C_Situacion['Codigo'][] = '108';
    }
    
    $Num_Registro_Anulado = number_format(((count($C_Situacion['Registro_Anulado']['Codigo'])*100)/count($DesercionDato2)),'2','.','.');
    
    if($Op==1){
        if($Num_Registro_Anulado>0){
            
            $C_Situacion['Porcentaje'][] = $Num_Registro_Anulado;
            $C_Situacion['Nombre'][] = 'Registro Anulado';
            $C_Situacion['Codigo'][] = '109';
            $C_Situacion['Num'][] =  count($C_Situacion['Registro_Anulado']['Codigo']);
        }
    }else{
            $C_Situacion['Porcentaje'][] = $Num_Registro_Anulado;
            $C_Situacion['Nombre'][] = 'Registro Anulado';
            $C_Situacion['Codigo'][] = '109';
    }
    
    $Num_PendienteConsejo = number_format(((count($C_Situacion['PendienteConsejo']['Codigo'])*100)/count($DesercionDato2)),'2','.','.');
    
    if($Op==1){
        if($Num_PendienteConsejo>0){
            
            $C_Situacion['Porcentaje'][] = $Num_PendienteConsejo;
            $C_Situacion['Nombre'][] = 'Pendiente Decision Consejo Facultad';
            $C_Situacion['Codigo'][] = '110';
            $C_Situacion['Num'][] =  count($C_Situacion['PendienteConsejo']['Codigo']);
        }
    }else{
            $C_Situacion['Porcentaje'][] = $Num_PendienteConsejo;
            $C_Situacion['Nombre'][] = 'Pendiente Decision Consejo Facultad';
            $C_Situacion['Codigo'][] = '110'; 
        
    }
    $Num_InsSinPago = number_format(((count($C_Situacion['InsSinPago']['Codigo'])*100)/count($DesercionDato2)),'2','.','.');
    
    if($Op==1){
        if($Num_InsSinPago>0){
            
            $C_Situacion['Porcentaje'][] = $Num_InsSinPago;
            $C_Situacion['Nombre'][] = 'Inscrito sin pago';
            $C_Situacion['Codigo'][] = '111';
            $C_Situacion['Num'][] =  count($C_Situacion['InsSinPago']['Codigo']);
        }
    }else{
            $C_Situacion['Porcentaje'][] = $Num_InsSinPago;
            $C_Situacion['Nombre'][] = 'Inscrito sin pago';
            $C_Situacion['Codigo'][] = '111';
    }
   
    $Num_CursoEduNoFormal = number_format(((count($C_Situacion['CursoEduNoFormal']['Codigo'])*100)/count($DesercionDato2)),'2','.','.');
    
    if($Op==1){
        if($Num_CursoEduNoFormal>0){
            
            $C_Situacion['Porcentaje'][] = $Num_CursoEduNoFormal;
            $C_Situacion['Nombre'][] = 'Terminacion de curso o educacion no formal';
            $C_Situacion['Codigo'][] = '112';
            $C_Situacion['Num'][] =  count($C_Situacion['CursoEduNoFormal']['Codigo']);
        }
    }else{
            $C_Situacion['Porcentaje'][] = $Num_CursoEduNoFormal;
            $C_Situacion['Nombre'][] = 'Terminacion de curso o educacion no formal';
            $C_Situacion['Codigo'][] = '112';
    }
    
    $Num_No_Admitido = number_format(((count($C_Situacion['No_Admitido']['Codigo'])*100)/count($DesercionDato2)),'2','.','.');
    
    if($Op==1){
        if($Num_No_Admitido>0){
            
            $C_Situacion['Porcentaje'][] = $Num_No_Admitido;
            $C_Situacion['Nombre'][] = 'No Admitido';
            $C_Situacion['Codigo'][] = '113';
            $C_Situacion['Num'][] =  count($C_Situacion['No_Admitido']['Codigo']);
        }
    }else{
            $C_Situacion['Porcentaje'][] = $Num_No_Admitido;
            $C_Situacion['Nombre'][] = 'No Admitido';
            $C_Situacion['Codigo'][] = '113';
    }
     
    $Num_EnProcesoFinanciacion = number_format(((count($C_Situacion['EnProcesoFinanciacion']['Codigo'])*100)/count($DesercionDato2)),'2','.','.');
    
    if($Op==1){
        if($Num_EnProcesoFinanciacion>0){
            
            $C_Situacion['Porcentaje'][] = $Num_EnProcesoFinanciacion;
            $C_Situacion['Nombre'][] = 'En proceso de financiacion';
            $C_Situacion['Codigo'][] = '114';
            $C_Situacion['Num'][] =  count($C_Situacion['EnProcesoFinanciacion']['Codigo']);
        }
    }else{
            $C_Situacion['Porcentaje'][] = $Num_EnProcesoFinanciacion;
            $C_Situacion['Nombre'][] = 'En proceso de financiacion';
            $C_Situacion['Codigo'][] = '114';
    }
    
    $Num_Lista_Espera = number_format(((count($C_Situacion['Lista_Espera']['Codigo'])*100)/count($DesercionDato2)),'2','.','.');
    
    if($Op==1){
        if($Num_Lista_Espera>0){
            
            $C_Situacion['Porcentaje'][] = $Num_Lista_Espera;
            $C_Situacion['Nombre'][] = 'Lista en Espera';
            $C_Situacion['Codigo'][] = '115';
            $C_Situacion['Num'][] =  count($C_Situacion['Lista_Espera']['Codigo']);
        }
    }else{
            $C_Situacion['Porcentaje'][] = $Num_Lista_Espera;
            $C_Situacion['Nombre'][] = 'Lista en Espera';
            $C_Situacion['Codigo'][] = '115';
    }
    
    $Num_Prueba_Academica = number_format(((count($C_Situacion['Prueba_Academica']['Codigo'])*100)/count($DesercionDato2)),'2','.','.');
    
    if($Op==1){
        if($Num_Prueba_Academica>0){
            
            $C_Situacion['Porcentaje'][] = $Num_Prueba_Academica;
            $C_Situacion['Nombre'][] = 'Prueba Academica';
            $C_Situacion['Codigo'][] = '200';
            $C_Situacion['Num'][] =  count($C_Situacion['Prueba_Academica']['Codigo']);
        }
    }else{
            $C_Situacion['Porcentaje'][] = $Num_Prueba_Academica;
            $C_Situacion['Nombre'][] = 'Prueba Academica';
            $C_Situacion['Codigo'][] = '200';
    }
    
    $Num_Admitido = number_format(((count($C_Situacion['Admitido']['Codigo'])*100)/count($DesercionDato2)),'2','.','.');
    
    if($Op==1){
        if($Num_Admitido>0){
            
            $C_Situacion['Porcentaje'][] = $Num_Admitido;
            $C_Situacion['Nombre'][] = 'Admitido';
            $C_Situacion['Codigo'][] = '300';
            $C_Situacion['Num'][] =  count($C_Situacion['Admitido']['Codigo']);
        }
    }else{
            $C_Situacion['Porcentaje'][] = $Num_Admitido;
            $C_Situacion['Nombre'][] = 'Admitido';
            $C_Situacion['Codigo'][] = '300';
    }
     
    $Num_Normal = number_format(((count($C_Situacion['Normal']['Codigo'])*100)/count($DesercionDato2)),'2','.','.');
    
    if($Op==1){
        if($Num_Normal>0){
            
            $C_Situacion['Porcentaje'][] = $Num_Normal;
            $C_Situacion['Nombre'][] = 'Normal';
            $C_Situacion['Codigo'][] = '301';
            $C_Situacion['Num'][] =  count($C_Situacion['Normal']['Codigo']);
        }
    }else{
            $C_Situacion['Porcentaje'][] = $Num_Normal;
            $C_Situacion['Nombre'][] = 'Normal';
            $C_Situacion['Codigo'][] = '301';
    }
    
    $Num_SolicitudReservaCupo = number_format(((count($C_Situacion['SolicitudReservaCupo']['Codigo'])*100)/count($DesercionDato2)),'2','.','.');
    
    if($Op==1){
        if($Num_SolicitudReservaCupo>0){
            
            $C_Situacion['Porcentaje'][] = $Num_SolicitudReservaCupo;
            $C_Situacion['Nombre'][] = 'Solicitud reserva de cupo';
            $C_Situacion['Codigo'][] = '302';
            $C_Situacion['Num'][] =  count($C_Situacion['SolicitudReservaCupo']['Codigo']);
        }
    }else{
            $C_Situacion['Porcentaje'][] = $Num_SolicitudReservaCupo;
            $C_Situacion['Nombre'][] = 'Solicitud reserva de cupo';
            $C_Situacion['Codigo'][] = '302';
    }
   
    $Num_Graduado = number_format(((count($C_Situacion['Graduado']['Codigo'])*100)/count($DesercionDato2)),'2','.','.');
    
    if($Op==1){
        if($Num_Graduado>0){
            
            $C_Situacion['Porcentaje'][] = $Num_Graduado;
            $C_Situacion['Nombre'][] = 'Graduado';
            $C_Situacion['Codigo'][] = '400';
            $C_Situacion['Num'][] =  count($C_Situacion['Graduado']['Codigo']);
        }
    }else{
            $C_Situacion['Porcentaje'][] = $Num_Graduado;
            $C_Situacion['Nombre'][] = 'Graduado';
            $C_Situacion['Codigo'][] = '400';
    }
   
    $Num_EnProceso_Grado = number_format(((count($C_Situacion['EnProceso_Grado']['Codigo'])*100)/count($DesercionDato2)),'2','.','.');
    
    if($Op==1){
        if($Num_EnProceso_Grado>0){
            
            $C_Situacion['Porcentaje'][] = $Num_EnProceso_Grado;
            $C_Situacion['Nombre'][] = 'En proceso de Grado';
            $C_Situacion['Codigo'][] = '500';
            $C_Situacion['Num'][] =  count($C_Situacion['EnProceso_Grado']['Codigo']);
        }
    }else{
        
            $C_Situacion['Porcentaje'][] = $Num_EnProceso_Grado;
            $C_Situacion['Nombre'][] = 'En proceso de Grado';
            $C_Situacion['Codigo'][] = '500';
    }
    /**********************************************************************************************************************************************/  
   
      //echo '<pre>';print_r($C_Situacion);
      
      return $C_Situacion;
                       
  } 
  public function CausasDesercionTotalSemestral($Periodo,$T_Desercion){
    
    global $db;
    
    $C_Carrera  = $this->Carreras();
        
        $D_Porcentaje  = array();
           
        for($i=0;$i<count($C_Carrera);$i++){
            
            $CodigoCarrera  = $C_Carrera[$i]['codigocarrera'];
            
            $C_Datos        = $this->CausasDesercion($Periodo,$CodigoCarrera,'0');
            
            //echo '<pre>';print_r($C_Datos);
            
            /**************************************************************************/
            for($j=0;$j<count($C_Datos['Codigo']);$j++){
                /************************************************************************************/
                switch($C_Datos['Codigo'][$j]){
                        case '100':{
                                    $D_Porcentaje ['P_Academica']['Porcentajeee'][] = $C_Datos['Porcentaje'][$j];
                                    $D_Porcentaje ['P_Academica']['Porcentaje'] += $C_Datos['Porcentaje'][$j];
                                    $D_Porcentaje ['P_Academica']['Nombre'][] = $C_Datos['Nombre'][$j];
                                    $D_Porcentaje ['P_Academica']['Count'][] = $C_Datos['P_Academica']['Count'];
                                    $D_Porcentaje ['P_Academica']['SumaCount'] += $C_Datos['P_Academica']['Count'];
                        }break;
                        case '101':{
                                    
                                    $D_Porcentaje ['P_Disciplinaria']['Porcentaje'] += $C_Datos['Porcentaje'][$j];
                                    $D_Porcentaje ['P_Disciplinaria']['Nombre'][] = $C_Datos['Nombre'][$j];
                                    $D_Porcentaje ['P_Disciplinaria']['Count'][] = $C_Datos['P_Disciplinaria']['Count'];
                                    $D_Porcentaje ['P_Disciplinaria']['SumaCount'] += $C_Datos['P_Disciplinaria']['Count'];
                        }break;
                        case '102':{
                                    $D_Porcentaje ['P_Administrativa']['Porcentaje'] += $C_Datos['Porcentaje'][$j];
                                    $D_Porcentaje ['P_Administrativa']['Nombre'][] = $C_Datos['Nombre'][$j];
                                    $D_Porcentaje ['P_Administrativa']['Count'][] = $C_Datos['P_Administrativa']['Count'];
                                    $D_Porcentaje ['P_Administrativa']['SumaCount'] += $C_Datos['P_Administrativa']['Count'];
                        }break;
                        case '103':{
                                    $D_Porcentaje ['P_Voluntaria']['Porcentajeee'][] = $C_Datos['Porcentaje'][$j];
                                    $D_Porcentaje ['P_Voluntaria']['Porcentaje'] += $C_Datos['Porcentaje'][$j];
                                    $D_Porcentaje ['P_Voluntaria']['Nombre'][] = $C_Datos['Nombre'][$j];
                                    $D_Porcentaje ['P_Voluntaria']['Count'][] = $C_Datos['P_Voluntaria']['Count'];
                                    $D_Porcentaje ['P_Voluntaria']['SumaCount'] += $C_Datos['P_Voluntaria']['Count'];
                        }break;
                         case '104':{
                                    $D_Porcentaje ['Egresado']['Porcentaje'] += $C_Datos['Porcentaje'][$j];
                                    $D_Porcentaje ['Egresado']['Nombre'][] = $C_Datos['Nombre'][$j];
                                    $D_Porcentaje ['Egresado']['Count'][] = $C_Datos['Egresado']['Count'];
                                    $D_Porcentaje ['Egresado']['SumaCount'] += $C_Datos['Egresado']['Count'];
                        }break;
                        case '105':{
                                    $D_Porcentaje ['Admitido_no']['Porcentaje'] += $C_Datos['Porcentaje'][$j];
                                    $D_Porcentaje ['Admitido_no']['Nombre'][] = $C_Datos['Nombre'][$j];
                                    $D_Porcentaje ['Admitido_no']['Count'][] = $C_Datos['Admitido_no']['Count'];
                                    $D_Porcentaje ['Admitido_no']['SumaCount'] += $C_Datos['Admitido_no']['Count'];
                        }break;
                        case '106':{
                                    $D_Porcentaje ['Preinscrito']['Porcentaje'] += $C_Datos['Porcentaje'][$j];
                                    $D_Porcentaje ['Preinscrito']['Nombre'][] = $C_Datos['Nombre'][$j];
                                    $D_Porcentaje ['Preinscrito']['Count'][] = $C_Datos['Preinscrito']['Count'];
                                    $D_Porcentaje ['Preinscrito']['SumaCount'] += $C_Datos['Preinscrito']['Count'];
                        }break;
                        case '107':{
                                    $D_Porcentaje ['Inscrito']['Porcentaje'] += $C_Datos['Porcentaje'][$j];
                                    $D_Porcentaje ['Inscrito']['Nombre'][] = $C_Datos['Nombre'][$j];
                                    $D_Porcentaje ['Inscrito']['Count'][] = $C_Datos['Inscrito']['Count'];
                                    $D_Porcentaje ['Inscrito']['SumaCount'] += $C_Datos['Inscrito']['Count'];
                        }break;
                        case '108':{
                                    $D_Porcentaje ['Reserva_Cupo']['Porcentajeee'][] = $C_Datos['Porcentaje'][$j];
                                    $D_Porcentaje ['Reserva_Cupo']['Porcentaje'] += $C_Datos['Porcentaje'][$j];
                                    $D_Porcentaje ['Reserva_Cupo']['Nombre'][] = $C_Datos['Nombre'][$j];
                                    $D_Porcentaje ['Reserva_Cupo']['Count'][] = $C_Datos['Reserva_Cupo']['Count'];
                                    $D_Porcentaje ['Reserva_Cupo']['SumaCount'] += $C_Datos['Reserva_Cupo']['Count'];
                        }break;
                        case '109':{
                                    $D_Porcentaje ['Registro_Anulado']['Porcentaje'] += $C_Datos['Porcentaje'][$j];
                                    $D_Porcentaje ['Registro_Anulado']['Nombre'][] = $C_Datos['Nombre'][$j];
                                    $D_Porcentaje ['Registro_Anulado']['Count'][] = $C_Datos['Registro_Anulado']['Count'];
                                    $D_Porcentaje ['Registro_Anulado']['SumaCount'] += $C_Datos['Registro_Anulado']['Count'];
                        }break;
                        case '110':{
                                    $D_Porcentaje ['PendienteConsejo']['Porcentaje'] += $C_Datos['Porcentaje'][$j];
                                    $D_Porcentaje ['PendienteConsejo']['Nombre'][] = $C_Datos['Nombre'][$j];
                                    $D_Porcentaje ['PendienteConsejo']['Count'][] = $C_Datos['PendienteConsejo']['Count'];
                                    $D_Porcentaje ['PendienteConsejo']['SumaCount'] += $C_Datos['PendienteConsejo']['Count'];
                        }break;
                        case '111':{
                                    $D_Porcentaje ['InsSinPago']['Porcentaje'] += $C_Datos['Porcentaje'][$j];
                                    $D_Porcentaje ['InsSinPago']['Nombre'][] = $C_Datos['Nombre'][$j];
                                    $D_Porcentaje ['InsSinPago']['Count'][] = $C_Datos['InsSinPago']['Count'];
                                    $D_Porcentaje ['InsSinPago']['SumaCount'] += $C_Datos['InsSinPago']['Count'];
                        }break;
                        case '112':{
                                    $D_Porcentaje ['CursoEduNoFormal']['Porcentaje'] += $C_Datos['Porcentaje'][$j];
                                    $D_Porcentaje ['CursoEduNoFormal']['Nombre'][] = $C_Datos['Nombre'][$j];
                                    $D_Porcentaje ['CursoEduNoFormal']['Count'][] = $C_Datos['CursoEduNoFormal']['Count'];
                                    $D_Porcentaje ['CursoEduNoFormal']['SumaCount'] += $C_Datos['CursoEduNoFormal']['Count'];
                        }break;
                        case '113':{
                                    $D_Porcentaje ['No_Admitido']['Porcentaje'] += $C_Datos['Porcentaje'][$j];
                                    $D_Porcentaje ['No_Admitido']['Nombre'][] = $C_Datos['Nombre'][$j];
                                    $D_Porcentaje ['No_Admitido']['Count'][] = $C_Datos['No_Admitido']['Count'];
                                    $D_Porcentaje ['No_Admitido']['SumaCount'] += $C_Datos['No_Admitido']['Count'];
                        }break;
                        case '114':{
                                    $D_Porcentaje ['EnProcesoFinanciacion']['Porcentaje'] += $C_Datos['Porcentaje'][$j];
                                    $D_Porcentaje ['EnProcesoFinanciacion']['Nombre'][] = $C_Datos['Nombre'][$j];
                                    $D_Porcentaje ['EnProcesoFinanciacion']['Count'][] = $C_Datos['EnProcesoFinanciacion']['Count'];
                                    $D_Porcentaje ['EnProcesoFinanciacion']['SumaCount'] += $C_Datos['EnProcesoFinanciacion']['Count'];
                        }break;
                        case '115':{
                                    $D_Porcentaje ['Lista_Espera']['Porcentaje'] += $C_Datos['Porcentaje'][$j];
                                    $D_Porcentaje ['Lista_Espera']['Nombre'][] = $C_Datos['Nombre'][$j];
                                    $D_Porcentaje ['Lista_Espera']['Count'][] = $C_Datos['Lista_Espera']['Count'];
                                    $D_Porcentaje ['Lista_Espera']['SumaCount'] += $C_Datos['Lista_Espera']['Count'];
                        }break;
                        case '200':{
                                    $D_Porcentaje ['Prueba_Academica']['Porcentajeee'][] = $C_Datos['Porcentaje'][$j];
                                    $D_Porcentaje ['Prueba_Academica']['Porcentaje'] += $C_Datos['Porcentaje'][$j];
                                    $D_Porcentaje ['Prueba_Academica']['Nombre'][] = $C_Datos['Nombre'][$j];
                                    $D_Porcentaje ['Prueba_Academica']['Count'][] = $C_Datos['Prueba_Academica']['Count'];
                                    $D_Porcentaje ['Prueba_Academica']['SumaCount'] += $C_Datos['Prueba_Academica']['Count'];
                        }break;
                        case '300':{
                                    $D_Porcentaje ['Admitido']['Porcentaje'] += $C_Datos['Porcentaje'][$j];
                                    $D_Porcentaje ['Admitido']['Nombre'][] = $C_Datos['Nombre'][$j];
                                    $D_Porcentaje ['Admitido']['Count'][] = $C_Datos['Admitido']['Count'];
                                    $D_Porcentaje ['Admitido']['SumaCount'] += $C_Datos['Admitido']['Count'];
                        }break;
                        case '301':{
                                    $D_Porcentaje ['Normal']['Porcentaje'] += $C_Datos['Porcentaje'][$j];
                                    $D_Porcentaje ['Normal']['Nombre'][] = $C_Datos['Nombre'][$j];
                                    $D_Porcentaje ['Normal']['Count'][] = $C_Datos['Normal']['Count'];
                                    $D_Porcentaje ['Normal']['SumaCount'] += $C_Datos['Normal']['Count'];
                        }break;
                        case '302':{
                                    $D_Porcentaje ['SolicitudReservaCupo']['Porcentaje'] += $C_Datos['Porcentaje'][$j];
                                    $D_Porcentaje ['SolicitudReservaCupo']['Nombre'][] = $C_Datos['Nombre'][$j];
                                    $D_Porcentaje ['SolicitudReservaCupo']['Count'][] = $C_Datos['SolicitudReservaCupo']['Count'];
                                    $D_Porcentaje ['SolicitudReservaCupo']['SumaCount'] += $C_Datos['SolicitudReservaCupo']['Count'];
                        }break;
                        case '400':{
                                    $D_Porcentaje ['Graduado']['Porcentaje'] += $C_Datos['Porcentaje'][$j];
                                    $D_Porcentaje ['Graduado']['Nombre'][] = $C_Datos['Nombre'][$j];
                                    $D_Porcentaje ['Graduado']['Count'][] = $C_Datos['Graduado']['Count'];
                                    $D_Porcentaje ['Graduado']['SumaCount'] += $C_Datos['Graduado']['Count'];
                        }break;
                        case '500':{
                                    $D_Porcentaje ['EnProceso_Grado']['Porcentaje'] += $C_Datos['Porcentaje'][$j];
                                    $D_Porcentaje ['EnProceso_Grado']['Nombre'][] = $C_Datos['Nombre'][$j];
                                    $D_Porcentaje ['EnProceso_Grado']['Count'][] = $C_Datos['EnProceso_Grado']['Count'];
                                    $D_Porcentaje ['EnProceso_Grado']['SumaCount'] += $C_Datos['EnProceso_Grado']['Count'];
                        }break;
                    }//switch
                /************************************************************************************/
            }//for
            /**************************************************************************/

        }//for
        
    //echo '<pre>';print_r($D_Porcentaje);   
        
    $C_Situacion  = array();    
    
    //echo '<br>'.$D_Porcentaje['P_Academica']['Porcentaje'].'/'.count($D_Porcentaje['P_Academica']['Nombre']);
        
    $Num_P_Academica  = number_format((($D_Porcentaje['P_Academica']['SumaCount']*100)/$T_Desercion),'2','.','.');
    
    if($Num_P_Academica>0){
        
        $C_Situacion['Porcentaje'][] = $Num_P_Academica;
        $C_Situacion['Nombre'][] = 'Perdida Academica '.$Num_P_Academica.'%';
        $C_Situacion['Codigo'][] = '100';
        
    }
    
    $Num_P_Disciplinaria = number_format((($D_Porcentaje['P_Disciplinaria']['SumaCount']*100)/$T_Desercion),'2','.','.');
        
    if($Num_P_Disciplinaria>0){
        
        $C_Situacion['Porcentaje'][] = $Num_P_Disciplinaria;
        $C_Situacion['Nombre'][] = 'Perdida Disiplinaria '.$Num_P_Disciplinaria.'%';
        $C_Situacion['Codigo'][] = '101';
        
    }
    
    $Num_P_Administrativa = number_format((($D_Porcentaje['P_Administrativa']['SumaCount']*100)/$T_Desercion),'2','.','.');
    
    if($Num_P_Administrativa>0){
        
        $C_Situacion['Porcentaje'][] = $Num_P_Administrativa;
        $C_Situacion['Nombre'][] = 'Perdida Administrativa '.$Num_P_Administrativa.'%';
        $C_Situacion['Codigo'][] = '102';
        
    }
    
    $Num_P_Voluntaria = number_format((($D_Porcentaje['P_Voluntaria']['SumaCount']*100)/$T_Desercion),'2','.','.');
    
    if($Num_P_Voluntaria>0){
        
        $C_Situacion['Porcentaje'][] = $Num_P_Voluntaria;
        $C_Situacion['Nombre'][] = 'Perdida Voluntaria '.$Num_P_Voluntaria.'%';
        $C_Situacion['Codigo'][] = '103';
    }
    
    $Num_Egresado = number_format((($D_Porcentaje['Egresado']['SumaCount']*100)/$T_Desercion),'2','.','.');
    
    if($Num_Egresado>0){
        
        $C_Situacion['Porcentaje'][] = $Num_Egresado;
        $C_Situacion['Nombre'][] = 'Egresado '.$Num_Egresado.'%';
        $C_Situacion['Codigo'][] = '104';
    }
    
    $Num_Admitido_no = number_format((($D_Porcentaje['Admitido_no']['SumaCount']*100)/$T_Desercion),'2','.','.');
    
    if($Num_Admitido_no>0){
        
        $C_Situacion['Porcentaje'][] = $Num_Admitido_no;
        $C_Situacion['Nombre'][] = 'Admitido que no Ingreso '.$Num_Admitido_no.'%';
        $C_Situacion['Codigo'][] = '105';
    }
    
    $Num_Preinscrito = number_format((($D_Porcentaje['Preinscrito']['SumaCount']*100)/$T_Desercion),'2','.','.');
    
    if($Num_Preinscrito>0){
        
        $C_Situacion['Porcentaje'][] = $Num_Preinscrito;
        $C_Situacion['Nombre'][] = 'Preinscrito '.$Num_Preinscrito.'%';
        $C_Situacion['Codigo'][] = '106';
    }
    $Num_Inscrito = number_format((($D_Porcentaje['Inscrito']['SumaCount']*100)/$T_Desercion),'2','.','.');
    
    if($Num_Inscrito>0){
        
        $C_Situacion['Porcentaje'][] = $Num_Inscrito;
        $C_Situacion['Nombre'][] = 'Inscrito '.$Num_Inscrito.'%';
        $C_Situacion['Codigo'][] = '107';
    }
    
    $Num_Reserva_Cupo = number_format((($D_Porcentaje['Reserva_Cupo']['SumaCount']*100)/$T_Desercion),'2','.','.');
    
    if($Num_Reserva_Cupo>0){
        
        $C_Situacion['Porcentaje'][] = $Num_Reserva_Cupo;
        $C_Situacion['Nombre'][] = 'Reserva de cupo '.$Num_Reserva_Cupo.'%';
        $C_Situacion['Codigo'][] = '108';
    }
    
    $Num_Registro_Anulado = number_format((($D_Porcentaje['Registro_Anulado']['SumaCount']*100)/$T_Desercion),'2','.','.');
    
    if($Num_Registro_Anulado>0){
        
        $C_Situacion['Porcentaje'][] = $Num_Registro_Anulado;
        $C_Situacion['Nombre'][] = 'Registro Anulado '.$Num_Registro_Anulado.'%';
        $C_Situacion['Codigo'][] = '109';
    }
    
    $Num_PendienteConsejo = number_format((($D_Porcentaje['PendienteConsejo']['SumaCount']*100)/$T_Desercion),'2','.','.');
    
    if($Num_PendienteConsejo>0){
        
        $C_Situacion['Porcentaje'][] = $Num_PendienteConsejo;
        $C_Situacion['Nombre'][] = 'Pendiente Decision Consejo Facultad '.$Num_PendienteConsejo.'%';
        $C_Situacion['Codigo'][] = '110';
    }
    
    $Num_InsSinPago = number_format((($D_Porcentaje['InsSinPago']['SumaCount']*100)/$T_Desercion),'2','.','.');
    
    if($Num_InsSinPago>0){
        
        $C_Situacion['Porcentaje'][] = $Num_InsSinPago;
        $C_Situacion['Nombre'][] = 'Inscrito sin pago '.$Num_InsSinPago.'%';
        $C_Situacion['Codigo'][] = '111';
    }
   
    $Num_CursoEduNoFormal = number_format((($D_Porcentaje['CursoEduNoFormal']['SumaCount']*100)/$T_Desercion),'2','.','.');
    
    if($Num_CursoEduNoFormal>0){
        
        $C_Situacion['Porcentaje'][] = $Num_CursoEduNoFormal;
        $C_Situacion['Nombre'][] = 'Terminacion de curso o educacion no formal '.$Num_CursoEduNoFormal.'%';
        $C_Situacion['Codigo'][] = '112';
    }
    
    $Num_No_Admitido = number_format((($D_Porcentaje['No_Admitido']['SumaCount']*100)/$T_Desercion),'2','.','.');
    
    if($Num_No_Admitido>0){
        
        $C_Situacion['Porcentaje'][] = $Num_No_Admitido;
        $C_Situacion['Nombre'][] = 'No Admitido '.$Num_No_Admitido.'%';
        $C_Situacion['Codigo'][] = '113';
    }
     
    $Num_EnProcesoFinanciacion = number_format((($D_Porcentaje['EnProcesoFinanciacion']['SumaCount']*100)/$T_Desercion),'2','.','.');
    
    if($Num_EnProcesoFinanciacion>0){
        
        $C_Situacion['Porcentaje'][] = $Num_EnProcesoFinanciacion;
        $C_Situacion['Nombre'][] = 'En proceso de financiacion '.$Num_EnProcesoFinanciacion.'%';
        $C_Situacion['Codigo'][] = '114';
    }
    
    $Num_Lista_Espera = number_format((($D_Porcentaje['Lista_Espera']['SumaCount']*100)/$T_Desercion),'2','.','.');
    
    if($Num_Lista_Espera>0){
        
        $C_Situacion['Porcentaje'][] = $Num_Lista_Espera;
        $C_Situacion['Nombre'][] = 'Lista en Espera '.$Num_Lista_Espera.'%';
        $C_Situacion['Codigo'][] = '115';
    }
    
    $Num_Prueba_Academica = number_format((($D_Porcentaje['Prueba_Academica']['SumaCount']*100)/$T_Desercion),'2','.','.');
    
    if($Num_Prueba_Academica>0){
        
        $C_Situacion['Porcentaje'][] = $Num_Prueba_Academica;
        $C_Situacion['Nombre'][] = 'Prueba Academica '.$Num_Prueba_Academica.'%';
        $C_Situacion['Codigo'][] = '200';
    }
    
    $Num_Admitido = number_format((($D_Porcentaje['Admitido']['SumaCount']*100)/$T_Desercion),'2','.','.');
    
    if($Num_Admitido>0){
        
        $C_Situacion['Porcentaje'][] = $Num_Admitido;
        $C_Situacion['Nombre'][] = 'Admitido '.$Num_Admitido.'%';
        $C_Situacion['Codigo'][] = '300';
    }
     
    $Num_Normal = number_format((($D_Porcentaje['Normal']['SumaCount']*100)/$T_Desercion),'2','.','.');
    
    if($Num_Normal>0){
        
        $C_Situacion['Porcentaje'][] = $Num_Normal;
        $C_Situacion['Nombre'][] = 'Normal '.$Num_Normal.'%';
        $C_Situacion['Codigo'][] = '301';
    }
    
    $Num_SolicitudReservaCupo = number_format((($D_Porcentaje['SolicitudReservaCupo']['SumaCount']*100)/$T_Desercion),'2','.','.');
    
    if($Num_SolicitudReservaCupo>0){
        
        $C_Situacion['Porcentaje'][] = $Num_SolicitudReservaCupo;
        $C_Situacion['Nombre'][] = 'Solicitud reserva de cupo '.$Num_SolicitudReservaCupo.'%';
        $C_Situacion['Codigo'][] = '302';
    }
   
    $Num_Graduado = number_format((($D_Porcentaje['Graduado']['SumaCount']*100)/$T_Desercion),'2','.','.');
    
    if($Num_Graduado>0){
        
        $C_Situacion['Porcentaje'][] = $Num_Graduado;
        $C_Situacion['Nombre'][] = 'Graduado '.$Num_Graduado.'%';
        $C_Situacion['Codigo'][] = '400';
    }
   
    $Num_EnProceso_Grado = number_format((($D_Porcentaje['EnProceso_Grado']['SumaCount']*100)/$T_Desercion),'2','.','.');
    
    if($Num_EnProceso_Grado>0){
        
        $C_Situacion['Porcentaje'][] = $Num_EnProceso_Grado;
        $C_Situacion['Nombre'][] = 'En proceso de Grado '.$Num_EnProceso_Grado.'%';
        $C_Situacion['Codigo'][] = '500';
    }
        
       // echo '<pre>';print_r($C_Situacion);die;
       
       return $C_Situacion;
    
  }//public function CausasDesercionTotalSemestral
 public function SumaTotalAnual($Periodos, $Modalidad){
    global $db;
    
     $SQL='SELECT
				SUM(x.matriculados) AS T_Matriculados,
				SUM(x.desercion) AS T_Desercion
			FROM
				(
					SELECT
						dd.matriculados,
						dd.desercion
					FROM
						desercion d
					INNER JOIN deserciondetalle dd ON d.id_desercion = dd.id_desercion
					INNER JOIN carrera c on d.codigocarrera = c.codigocarrera
					WHERE
						d.tipodesercion = 1
					AND d.codigoestado = 100
					AND dd.codigoestado = 100
					AND dd.desercionperiodo = "'.$Periodos.'"
					AND  c.codigomodalidadacademica = "'.$Modalidad.'"
					GROUP BY
						d.id_desercion
				)x';
            
        if($TotalesAnual=&$db->Execute($SQL)===false){
            echo 'error en el SQl de los Totales anuales...<br><br>'.$SQL;
            die;
        }
        
        
      $R_TotalAnual = array();
      
      $R_TotalAnual['TotalMatriculados']  = $TotalesAnual->fields['T_Matriculados'];      
      $R_TotalAnual['TotalDesercion']  = $TotalesAnual->fields['T_Desercion'];
      
      $PocentajeAnual  = (($TotalesAnual->fields['T_Desercion']/$TotalesAnual->fields['T_Matriculados'])*100);
      
      $R_TotalAnual['TotalPorcentaje']  = number_format($PocentajeAnual,'2','.','.');
      
      return $R_TotalAnual;

 }//public function SumaTotalAnual  
 public function Cohorte($Periodo,$Carrera_id,$Periodo_2){
    global $db;
    
    
    $D_Cohorte  = array();
    
    /*********************************************************************************************/
    
    
        /*****************************************************************************************/
        if($Periodo_2==$Periodo){
            /*************************************************************************************/
            $SQL_InicioCohorte='SELECT

                                COUNT(dc.id_DesercionChohorte)  AS Num,
                                d.id_desercion,
                                dc.id_desercion
                                
                                FROM
                                
                                desercion d INNER JOIN DesercionChohorte dc ON d.id_desercion=dc.id_desercion
                                
                                
                                WHERE
                                
                                d.tipodesercion=3
                                AND
                                d.codigocarrera="'.$Carrera_id.'"
                                AND
                                dc.codigoperiodo="'.$Periodo.'"
                                AND
                                dc.nuevo=1';
                                
                if($InicioCohorte=&$db->Execute($SQL_InicioCohorte)===false){
                    echo 'error en el SQl de Incio de Cohorte ...<br><br>'.$SQL_InicioCohorte;
                    die;
                }       
                
                $D_Cohorte[$Periodo][$Carrera_id][$Periodo_2]['Matriculados'] =  $InicioCohorte->fields['Num']; 
                $D_Cohorte[$Periodo][$Carrera_id][$Periodo_2]['Desercion']    =  0; 
                $D_Cohorte[$Periodo][$Carrera_id][$Periodo_2]['Porcentaje_Matriculados']   =  100;                      
                $D_Cohorte[$Periodo][$Carrera_id][$Periodo_2]['Porcentaje_Desercion']   =  0;          
            /*************************************************************************************/
        }else if($Periodo_2!=$Periodo){
            /*************************************************************************************/
            $SQL_InicioCohorte='SELECT

                                COUNT(dc.id_DesercionChohorte)  AS Num,
                                d.id_desercion,
                                dc.id_desercion
                                
                                
                                
                                FROM
                                
                                desercion d INNER JOIN DesercionChohorte dc ON d.id_desercion=dc.id_desercion
                                
                                
                                WHERE
                                
                                d.tipodesercion=3
                                AND
                                d.codigocarrera="'.$Carrera_id.'"
                                AND
                                dc.codigoperiodo="'.$Periodo.'"
                                AND
                                dc.nuevo=1';
                                
                if($InicioCohorte=&$db->Execute($SQL_InicioCohorte)===false){
                    echo 'error en el SQl de Incio de Cohorte ...<br><br>'.$SQL_InicioCohorte;
                    die;
                }
                
                $MatriculadosInicio   = $InicioCohorte->fields['Num'];
            /*************************************************************************************/
               $SQL_SigCohorte='SELECT

                                COUNT(dc.id_DesercionChohorte) AS Num
                                
                                FROM
                                
                                DesercionChohorte dc,
                                
                                (
                                
                                SELECT
                                
                                dc.codigoestudiante
                                
                                 
                                
                                FROM
                                
                                desercion d,
                                
                                DesercionChohorte dc
                                
                                 
                                
                                WHERE
                                
                                d.id_desercion=dc.id_desercion and
                                
                                d.tipodesercion=3 AND
                                
                                d.codigocarrera="'.$Carrera_id.'" AND
                                
                                dc.codigoperiodo="'.$Periodo.'" AND
                                
                                dc.nuevo=1
                                
                                ) ini
                                
                                
                                WHERE
                                
                                ini.codigoestudiante = dc.codigoestudiante and
                                
                                dc.codigoperiodo="'.$Periodo_2.'" AND
                                
                                dc.nuevo=0';
              
              
              if($SigCohorte=&$db->Execute($SQL_SigCohorte)===false){
                    echo 'error en el SQl de Sig de Cohorte ...<br><br>'.$SQL_SigCohorte;
                    die;
                }
                
                
              $SQL_DesCohorte='SELECT

                                COUNT(dc.id_DesercionChohorte) AS Num
                                
                                FROM
                                
                                DesercionChohorte dc,
                                
                                (
                                
                                SELECT
                                
                                dc.codigoestudiante
                                
                                 
                                
                                FROM
                                
                                desercion d,
                                
                                DesercionChohorte dc
                                
                                 
                                
                                WHERE
                                
                                d.id_desercion=dc.id_desercion and
                                
                                d.tipodesercion=3 AND
                                
                                d.codigocarrera="'.$Carrera_id.'" AND
                                
                                dc.codigoperiodo="'.$Periodo.'" AND
                                
                                dc.nuevo=1
                                
                                ) ini
                                
                                
                                WHERE
                                
                                ini.codigoestudiante = dc.codigoestudiante and
                                
                                dc.codigoperiododesercion="'.$Periodo_2.'" AND
                                
                                dc.nuevo=0'; 
                                   
                    if($DesercionCohorte=&$db->Execute($SQL_DesCohorte)===false){
                        echo 'Error en el SQl de Desercion Cohorte...<br><br>'.$SQL_DesCohorte;
                        die;
                    }             
                
             $D_Cohorte[$Periodo][$Carrera_id][$Periodo_2]['Matriculados'] =  $SigCohorte->fields['Num']; 
             $D_Cohorte[$Periodo][$Carrera_id][$Periodo_2]['Desercion']    =  $DesercionCohorte->fields['Num']; 
             /**************************************************************************************************************/
             
             if($SigCohorte->fields['Num']==0 && $DesercionCohorte->fields['Num']==0){
                $Porcentaje  = 0;
                $Porcentaje_D  = 0;
             }else{
                
                $Porcentaje  = (($SigCohorte->fields['Num']*100)/$MatriculadosInicio);
                $Porcentaje_D = (100-$Porcentaje);
                
             }
             
             /**************************************************************************************************************/
             $D_Cohorte[$Periodo][$Carrera_id][$Periodo_2]['Porcentaje_Matriculados']   =  number_format($Porcentaje,'2','.','.');                      
             $D_Cohorte[$Periodo][$Carrera_id][$Periodo_2]['Porcentaje_Desercion']   =  number_format($Porcentaje_D,'2','.','.');  
            /*************************************************************************************/
        }
        /*****************************************************************************************/
     
    /*********************************************************************************************/
  
  //echo '<pre>';print_r($D_Cohorte); die; 
  
  return $D_Cohorte;
}//public function Cohorte
public function DisplayCohorte($CodigoPeriodo,$tipo=1){
    global $db;
    
    //$CodigoPeriodo  = '20082';
  
	if($tipo==1){
		//desercion
		$title="Deserci&oacute;n";
		$title2 = "Desercion";
		$grafica = "GenerarGraficAnual";
	} else if($tipo==2){
		//retencion
		$title="Permanencia";
		$title2="Permanencia";
		$grafica = "GenerarGraficAnualRetencion";
	}
    $_D_Periodos   = $this->CortesDinamicas($CodigoPeriodo);
    
    $C_Carrera   = $this->Carreras();
   
    ?>
    <style type="text/css" title="currentStyle">
                @import "../data/media/css/demo_page.css";
                @import "../data/media/css/demo_table_jui.css";
                @import "../data/media/css/ColVis.css";
                @import "../data/media/css/TableTools.css";
                @import "../data/media/css/ColReorder.css";
                @import "../data/media/css/themes/smoothness/jquery-ui-1.8.4.custom.css";
                @import "../data/media/css/jquery.modal.css";
                
    </style>
    
    <script type="text/javascript" language="javascript" src="../data/media/js/jquery.js"></script>
    <!--<script type="text/javascript" charset="utf-8" src="../jquery/js/jquery-3.6.0.js"></script>-->
    <script type="text/javascript" language="javascript" src="../js/jquery-ui-1.8.21.custom.min.js"></script>
    <script type="text/javascript" language="javascript" src="../data/media/js/jquery.dataTables.js"></script>
    <script type="text/javascript" charset="utf-8" src="../data/media/js/ColVis.js"></script>
    <script type="text/javascript" charset="utf-8" src="../data/media/js/ZeroClipboard.js"></script>
    <script type="text/javascript" charset="utf-8" src="../data/media/js/TableTools.js"></script>
    <script type="text/javascript" charset="utf-8" src="../data/media/js/FixedColumns.js"></script>
    <script type="text/javascript" charset="utf-8" src="../data/media/js/ColReorder.js"></script>
    <script type="text/javascript" language="javascript">
        
        $(document).ready( function () {//"sDom": '<Cfrltip>',
				var oTable = $('#example').dataTable( {
				    
  					 
  					"sScrollX": "100%",
					"sScrollXInner": "100,1%",
					"bScrollCollapse": true,
                    "bPaginate": true,
                    "aLengthMenu": [[50], [50,  "All"]],
                     "iDisplayLength": 50,
                    "sPaginationType": "full_numbers",
					"oColReorder": {
						"iFixedColumns": 1
					},
                    "oColVis": {
                            "buttonText": "Ver/Ocultar Columns",
                             "aiExclude": [ 0 ]
                      }
                    
                    
					
				} );
				//new FixedColumns( oTable );
                                
                                new FixedColumns( oTable, {
                                         "iLeftColumns": 2,
                                         "iLeftWidth": 550
				} );
                                
                                 var oTableTools = new TableTools( oTable, {
					"buttons": [
						"copy",
						"csv",
						"xls",
						"pdf",
					]
		         });
                         $('#demo_1').before( oTableTools.dom.container );
			} ); 
        
    </script>
    <div id="demo_1">
    <table cellpadding="0" cellspacing="0" border="1" class="display" id="example">
        <thead>
            <tr>
                <th rowspan="2" class="bl bt">N&deg;</th>
                <th rowspan="2" class="bl bt">Carrera</th>
            <?PHP 
            for($P=0;$P<count($_D_Periodos['ViewPeriodo']);$P++){
            ?>
            <th colspan="4" class="bl br bt"><?PHP echo $_D_Periodos['ViewPeriodo'][$P]?></th>
            <?PHP
            }//For Periodos
            ?>
            </tr>
            <tr>
            <?PHP 
            for($P=0;$P<count($_D_Periodos['ViewPeriodo']);$P++){
            ?>
                <th>Matriculados</th>
                <th><?php echo $title; ?></th>
                <th>Porcentaje Matriculados (%)</th>
                <th>Porcentaje <?php echo $title; ?> (%)</th>
            <?PHP
            }//For Periodos
            ?>    
            </tr>
        </thead>
        <tbody>
        <?PHP
        for($C=0;$C<count($C_Carrera);$C++){
           /**********************************************************/
           $Carrera_id    = $C_Carrera[$C]['codigocarrera'];
            //
           ?>
           <tr>
               <td><?PHP echo $C+1?></td> 
               <td><?PHP echo $C_Carrera[$C]['nombrecarrera']?></td>
           <?PHP 
            for($J=0;$J<count($_D_Periodos['CodigoPeriodo']);$J++){
            
                  $C_Cohorte  = $this->Cohorte($CodigoPeriodo,$Carrera_id,$_D_Periodos['CodigoPeriodo'][$J]);  
           
		   if($tipo==1){
				$poblacion = $C_Cohorte[$CodigoPeriodo][$Carrera_id][$_D_Periodos['CodigoPeriodo'][$J]]['Desercion'];
				$porcentaje = $C_Cohorte[$CodigoPeriodo][$Carrera_id][$_D_Periodos['CodigoPeriodo'][$J]]['Porcentaje_Desercion'];
			} else if ($tipo==2){
				$poblacion = $C_Cohorte[$CodigoPeriodo][$Carrera_id][$_D_Periodos['CodigoPeriodo'][$J]]['Matriculados']-$C_Cohorte[$CodigoPeriodo][$Carrera_id][$_D_Periodos['CodigoPeriodo'][$J]]['Desercion'];
				$porcentaje = 100-$C_Cohorte[$CodigoPeriodo][$Carrera_id][$_D_Periodos['CodigoPeriodo'][$J]]['Porcentaje_Desercion'];
			}
                 ?>
                <td><?PHP echo $C_Cohorte[$CodigoPeriodo][$Carrera_id][$_D_Periodos['CodigoPeriodo'][$J]]['Matriculados']?></td>
                <td><?PHP echo $poblacion?></td>
                <td><?PHP echo $C_Cohorte[$CodigoPeriodo][$Carrera_id][$_D_Periodos['CodigoPeriodo'][$J]]['Porcentaje_Matriculados']?>%</td>
                <td><?PHP echo $porcentaje?>%</td>
                 <?PHP
             }    
           ?>
           </tr>
           <?PHP 
           /**********************************************************/ 
        }//for
        ?>
        </tbody>
    </table>
    </div>
    <?PHP
    
}//public function DisplayCohorte
public function CortesDinamicas($Periodo){
    
   
    
    $arrayP = str_split($Periodo, strlen($Periodo)-1);

    $year = $arrayP[0];
    $P_Ini = $arrayP[1];
    
    $C_Periodos   = array();
    
    for($i=1;$i<=20;$i++){
                /***********************************************************************/
                if($i==1){//if..1
                    $PeriodoView  = $year.'-'.$P_Ini;
                    $CodigoPeriodo = $Periodo;
                }else{
                    if($P_Ini==2){////if..2
                        $year  = $year+1;
                        $P_Ini = 1;
                        $PeriodoView  = $year.'-'.$P_Ini;
                        $CodigoPeriodo = $year.$P_Ini;
                    }else{
                        if($P_Ini==1){
                           $P_Ini = 2; 
                        }
                        $PeriodoView  = $year.'-'.$P_Ini;
                        $CodigoPeriodo = $year.$P_Ini;
                    }//if..2
                }//if..1
                
         $C_Periodos['CodigoPeriodo'][]=   $CodigoPeriodo;
         $C_Periodos['ViewPeriodo'][]=   $PeriodoView;    
     }     
    
    //echo '<pre>';print_r($C_Periodos);
    return $C_Periodos;
    
}
public function DesercionCosto($Carrera_id,$Periodo){
    global $db;
    
    //include_once('../../consulta/estadisticas/matriculasnew/funciones/obtener_datos.php');
    
    //$datos_estadistica=new obtener_datos_matriculas($db,$Periodo);
    
    //$DesercionDato=$datos_estadistica->obtener_datos_estudiantes_desercion($Carrera_id,'arreglo');
    
    $SQL='SELECT 
                    de.codigoestudiante

          FROM 
                    desercionEstudiante de INNER JOIN deserciondetalle dd ON dd.id_detalledesercion=de.id_detalledesercion
										   INNER JOIN desercion d ON d.id_desercion=dd.id_desercion 
                                           AND 
                                           d.codigocarrera="'.$Carrera_id.'" 
                                           AND 
                                           dd.desercionperiodo="'.$Periodo.'" 
                                           AND 
                                           d.tipodesercion=1';
                                           
         if($DataEstudiante=&$db->Execute($SQL)===false){
            echo 'Error en el SQL del Data Estudiante....<br><br>'.$SQL;
            die;
         }                                  
                                           
      $DesercionDato = $DataEstudiante->GetArray();                                     
    
    //echo '<pre>';print_r($DesercionDato);die;
    
    $D_Costo  = array();
    
    for($i=0;$i<count($DesercionDato);$i++){
        
        $SQL_P ='SELECT 
        
                       MIN(codigoperiodo) AS Min
                         
                 FROM 
                        prematricula 
                        
                 WHERE  
                 
                        codigoestudiante="'.$DesercionDato[$i]['codigoestudiante'].'" 
                        AND 
                        codigoestadoprematricula IN (40,41)';
                        
                  if($P_ini=&$db->Execute($SQL_P)===false){
                    echo 'Error en el SQL Periodo inicial....<br><br>'.$SQL_P;
                    die;
                  } 
                  
         $PeriodoInicial  = $P_ini->fields['Min'];              
        
        $SQL_S='SELECT 
                        MAX(semestreprematricula)  AS Max
                
                FROM 
                        prematricula 
                        
                WHERE  
                
                        codigoestudiante="'.$DesercionDato[$i]['codigoestudiante'].'" 
                        AND 
                        codigoestadoprematricula IN (40,41)';
                        
                 
              if($S_Fin=&$db->Execute($SQL_S)===false){
                echo 'Error en SQL Semestre Final....<br><br>'.$SQL_S;
                die;
              }          
        
        $SemestreFinal = $S_Fin->fields['Max'];
        
          
        $C_Costo  = $this->Costos($Carrera_id,$Periodo,$PeriodoInicial,$SemestreFinal,$DesercionDato[$i]['codigoestudiante']);
        
        $D_Costo[$i]   = $C_Costo;
        
    }//for
        
    //echo '<pre>';print_r($D_Costo);die;
    
    return $D_Costo;
    
}/*public function DesercionCosto*/
public function Costos($Carrera_id,$Periodo,$inicio,$semestre,$CodigoEstudiante){
    global $db;
    
     $SQL='SELECT 

            idcohorte,
            codigoperiodoinicial, 
            codigoperiodofinal 
            
            FROM 
            
            cohorte 
            
            WHERE 
            
            codigocarrera="'.$Carrera_id.'"
            AND 
            codigoperiodo="'.$Periodo.'"';
            
            if($Cohorte=&$db->Execute($SQL)===false){
                echo 'Error en el SQL de la Cohorte...<br><br>'.$SQL;
                die;
            }
           
        $arrayP = str_split($Periodo, strlen($Periodo)-1);
        
        $P_Periodo=$arrayP[0]."-".$arrayP[1]; 
        
        
        
        if($arrayP[1]==2){
            
            $YearNew     = $arrayP[0]+1;  
            $PeriodoNew  = 1;
            
        }else{
            
            $YearNew     = $arrayP[0];  
            $PeriodoNew  = 2;
            
        }
        
        $C_Periodo = $Periodo.'-'.$YearNew.$PeriodoNew;
        $Periodo_Dos = $YearNew.$PeriodoNew;
            
     /*echo '<br><br>'.$SQL_1='SELECT 

            de.id_desercionestudiante,
            de.codigoestudiante
            
            
            FROM deserciondetalle  dd INNER JOIN desercion  d  ON d.id_desercion=dd.id_desercion
                                      INNER JOIN desercionEstudiante de ON de.id_detalledesercion=dd.id_detalledesercion
            
            
            WHERE  
            
            d.codigocarrera="'.$Carrera_id.'"
            AND
            d.tipodesercion=1
            AND
            dd.desercionperiodo IN ("'.$Periodo.'","'.$Periodo_Dos.'")
            AND
            d.codigoestado=100
            AND
            de.codigoestudiante="'.$CodigoEstudiante.'"';die;
            
            
 
            
            if($Estudiante=&$db->Execute($SQL_1)===false){
                echo 'Error en el SQL de LOs Estudiantes....<br><br>'.$SQL_1;
                die;
            }*/
            
            $D_Costos = array();
            
            while(!$Cohorte->EOF){
                  // echo '<br><br>'.$inicio.'>='.$Cohorte->fields['codigoperiodoinicial'].' && '.$inicio.'<='.$Cohorte->fields['codigoperiodofinal'];
                    if($inicio>=$Cohorte->fields['codigoperiodoinicial'] && $inicio<=$Cohorte->fields['codigoperiodofinal']){
                        
                        $D_Costos['inicio']  = $inicio;
                        $D_Costos['codigoperiodoinicial']  = $Cohorte->fields['codigoperiodoinicial'];
                        $D_Costos['codigoperiodofinal']  = $Cohorte->fields['codigoperiodofinal'];
                        
                        $Cohorte_id   = $Cohorte->fields['idcohorte'];
                        
                        $SQL_Detalle='SELECT 
                                        
                                        iddetallecohorte,
                                        semestredetallecohorte,
                                        valordetallecohorte 
                                        
                                        FROM 
                                        
                                        detallecohorte 
                                        
                                        WHERE 
                                        
                                        idcohorte="'.$Cohorte_id.'"';
                                        
                                        if($DetalleCohorte=&$db->Execute($SQL_Detalle)===false){
                                            echo 'Error en el SQl del detalle de la Cohorte...<br><br>'.$SQL_Detalle;
                                            die;
                                        }
                                        
                                        while(!$DetalleCohorte->EOF){
                                            
                                            $F_Semestre = $semestre;
                                            
                                            $D_Costos['Semestre']  = $F_Semestre;
                                           
                                            if($F_Semestre==$DetalleCohorte->fields['semestredetallecohorte']){
                                                
                                                $Valor  = $DetalleCohorte->fields['valordetallecohorte'];
                                                
                                                $D_Costos['Valor']  = $Valor;
                                                $D_Costos['CodigoEstudiante'] = $CodigoEstudiante;
                                            }//if
                                            
                                            $DetalleCohorte->MoveNext();
                                        }
                        
                    }//if
                  $Cohorte->MoveNext();  
              }  
                //echo '<pre>';print_r($D_Costos);die;
            
    return $D_Costos;        
    
}/*public function Costos*/
public function TablaData(){
    global $db;
    
    ?>
    <div id="container">
        <div class="titulo"></div>
            <fieldset >
                <legend>Estad&iacute;sticas Nacionales</legend>
                <table border="1">
                    <thead>
                        <tr>
                            <th>Tipo Dato</th>
                            <th>Porcentaje (%)</th>
                            <th>Periodo</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <select id="Typo" name="Typo">
                                    <option value="-1"></option>
                                    <option value="0">Deserci&oacute;n Nacional</option>
                                    <option value="1">Permanencia Nacional</option>
                                </select>
                            </td>
                            <td>
                                <input type="text" id="Valor" style="text-align: center;" onkeypress="return NumberKey(event);" />
                            </td>
                            <td>
                                <select id="Periodo" name="Periodo" style="width: 100%;text-align: center;">
                                    <option value="-1"></option>
                                    <?PHP 
                                    $C_Periodo=$this->Periodo('Todos','','');
                                    
                                    for($i=0;$i<count($C_Periodo);$i++){
                                        ?>
                                        <option value="<?PHP echo $C_Periodo[$i]['codigoperiodo']?>"><?PHP echo $C_Periodo[$i]['codigoperiodo']?></option>
                                        <?PHP
                                    }//for
                                    ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3"></td>
                        </tr>
                        <tr>
                            <td colspan="3"><center><input type="button" id="Savedata" value="Guardar." onclick="SaveData()" /></center></td>
                        </tr>
                    </tbody>
                </table>
                <br />
                <br />
                <hr >
                <div id="DivTablaInfo">
                <?PHP $this->InfoTabla();?>
                </div>
             </fieldset>
         </div>
                
    <?PHP
}//public function TablaData
public function InfoTabla(){
    global $db;
    
      $SQL='SELECT 
            
            id_datosNacionales as id,
            tipo,
            valor,
            periodo
            
            FROM 
            
            DatosNacionales
            
            WHERE
            
            codigoestado=100
            AND
            tipo=0
            
            ORDER BY periodo';
            
          if($Data=&$db->Execute($SQL)===false){
            echo 'Error en el SQL de la Data...<br><br>'.$SQL;
            die;
          }  
            
        if(!$Data->EOF){
            ?>
             <table border="1" align="left" style="width: 40%;">
                <thead>
                    <tr>
                        <th colspan="2">Deserci&oacute;n</th>
                    </tr>
                    <tr>
                         <th>Porcentaje (%)</th>
                        <th>Periodo</th>
                    </tr>
                </thead>
                <tbody>
                <?PHP 
                while(!$Data->EOF){
                    /*
                    0->Desercion
                    1->Retencion
                    */
                    
                    if($Data->fields['tipo']==1){
                        $Tipo = 'Permanencia';
                    }else{
                         $Tipo = 'Deserci&oacute;n';
                    }
                    ?>
                    <tr>
                        <td><center><?PHP echo $Data->fields['valor']?>%</center></td>
                        <td><center><?PHP echo $Data->fields['periodo']?></center></td>
                    </tr>
                    <?PHP
                    $Data->MoveNext();
                }
                ?>
                </tbody>
            </table>
            <?PHP
        }    
        
      $SQL_2='SELECT 
            
            id_datosNacionales as id,
            tipo,
            valor,
            periodo
            
            FROM 
            
            DatosNacionales
            
            WHERE
            
            codigoestado=100
            AND
            tipo=1
            
            ORDER BY periodo';
            
          if($Data_2=&$db->Execute($SQL_2)===false){
            echo 'Error en el SQL de la Data...<br><br>'.$SQL_2;
            die;
          }  
            
        if(!$Data_2->EOF){
            ?>
            <table border="1" align="right" style="width: 40%;">
                <thead>
                    <tr>
                        <th colspan="2">Permanencia</th>
                    </tr>
                    <tr>
                        <th>Porcentaje (%)</th>
                        <th>Periodo</th>
                    </tr>
                </thead>
                <tbody>
                <?PHP 
                while(!$Data_2->EOF){
                    /*
                    0->Desercion
                    1->Retencion
                    */
                    
                    if($Data_2->fields['tipo']==1){
                        $Tipo = 'Permanencia';
                    }else{
                         $Tipo = 'Deserci&oacute;n';
                    }
                    ?>
                    <tr>
                        <td><center><?PHP echo $Data_2->fields['valor']?>%</center></td>
                        <td><center><?PHP echo $Data_2->fields['periodo']?></center></td>
                    </tr>
                    <?PHP
                    $Data_2->MoveNext();
                }
                ?>
                </tbody>
            </table>
            <?PHP
        }    
            
}/*public function InfoTabla*/
public function DisplaySocioEconomicoSemestral(){
    global $db;
    
    /***********************************************************************/
    
    $CodigoPeriodo = '20081';
    
    $Periodo_Actual = $this->Periodo('Actual','','');
        
    $C_Periodo      = $this->Periodo('Cadena',$CodigoPeriodo,$Periodo_Actual);
    
    $P_num= count($C_Periodo);
    
    $P_num  = $P_num-1;
    
    $C_Estrato  = $this->Estrato();
    
    //echo '<pre>';print_r($C_Estrato);
    //echo '<pre>';print_r($C_Periodo);echo 'num->'.$P_num;die;
    ?>
    <style type="text/css" title="currentStyle">
                @import "../data/media/css/demo_page.css";
                @import "../data/media/css/demo_table_jui.css";
                @import "../data/media/css/ColVis.css";
                @import "../data/media/css/TableTools.css";
                @import "../data/media/css/ColReorder.css";
                @import "../data/media/css/themes/smoothness/jquery-ui-1.8.4.custom.css";
                @import "../data/media/css/jquery.modal.css";
                
    </style>
    
    <script type="text/javascript" language="javascript" src="../data/media/js/jquery.js"></script>
    <script type="text/javascript" charset="utf-8" src="../jquery/js/jquery-3.6.0.js"></script>
    <script type="text/javascript" language="javascript" src="../data/media/js/jquery.dataTables.js"></script>
    <script type="text/javascript" charset="utf-8" src="../data/media/js/ColVis.js"></script>
    <script type="text/javascript" charset="utf-8" src="../data/media/js/ZeroClipboard.js"></script>
    <script type="text/javascript" charset="utf-8" src="../data/media/js/TableTools.js"></script>
    <script type="text/javascript" charset="utf-8" src="../data/media/js/FixedColumns.js"></script>
    <script type="text/javascript" charset="utf-8" src="../data/media/js/ColReorder.js"></script>
    <script type="text/javascript" language="javascript">
        
        $(document).ready( function () {//"sDom": '<Cfrltip>',
				var oTable = $('#example_1').dataTable( {
				    
  					"sScrollX": "100%",
					"sScrollXInner": "100,1%",
					"bScrollCollapse": true,
                    "bPaginate": true,
                    "aLengthMenu": [[50], [50,  "All"]],
                     "iDisplayLength": 50,
                    "sPaginationType": "full_numbers",
					"oColReorder": {
						"iFixedColumns": 1
					},
                    "oColVis": {
                            "buttonText": "Ver/Ocultar Columns",
                             "aiExclude": [ 0 ]
                      }
                    
                    
					
				} );
				//new FixedColumns( oTable );
                                
                                new FixedColumns( oTable, {
                                         "iLeftColumns": 2,
                                         "iLeftWidth": 350
				} );
                                
                                 var oTableTools = new TableTools( oTable, {
					"buttons": [
						"copy",
						"csv",
						"xls",
						"pdf",
					]
		         });
                         $('#demo').before( oTableTools.dom.container );
			} ); 
        /* $(document).ready(function() {
            	$('#Ejemplo').dataTable();
            } );  */ 
    </script>
    
    <input type="hidden" id="CodigoPeriodo" value="<?PHP echo $CodigoPeriodo?>" />    
    <div id="demo">
         <table cellpadding="0" cellspacing="0" border="1" class="display" id="example_1">
            <thead>
                <tr>
                    <th rowspan="3" class="bl bt">N&deg;</th>
                    <th rowspan="3" class="bl bt">Carrera</th>
                    <?PHP 
                    
                    $Num      = (count($C_Estrato['Nombre'])*2)+1;
                    $Num_2    = (count($C_Estrato['Nombre'])*2)+2;
                    
                    for($i=0;$i<$P_num;$i++){
                        
                            /***************************************/
                            $arrayP = str_split($C_Periodo[$i]['codigoperiodo'], strlen($C_Periodo[$i]['codigoperiodo'])-1);
                            
                            $P_Periodo=$arrayP[0]."-".$arrayP[1];
                            
                            /***************************************/
  
                        ?>
                        <th colspan="<?PHP echo $Num+$Num_2?>" class="bl br bt" ><?PHP echo $P_Periodo?></th>
                        
                   <?PHP
                    }//for
                   ?>
                   </tr>
                   <tr>
                   <?PHP
                   
                    
                    for($i=0;$i<$P_num;$i++){
                        ?>
                        <th colspan="<?PHP echo $Num?>" class="bl br bt" >Distribuci&oacute;n por Estratos de la Poblaci&oacute;n Total</th>
                        <th colspan="<?PHP echo $Num_2?>" class="bl br bt" >Distribuci&oacute;n de Deserci&oacute;n Por Estratos</th>
                        <?PHP
                        }
                   ?>
                        
                   </tr>
                   <tr>
                        <?PHP 
                        for($i=0;$i<$P_num;$i++){
                            /***************************************/
                            $arrayP = str_split($C_Periodo[$i]['codigoperiodo'], strlen($C_Periodo[$i]['codigoperiodo'])-1);
                            
                            $P_Periodo=$arrayP[0]."-".$arrayP[1];
                            
                            /***************************************/
                            ?>
                            <th class="bl">Poblaci&oacute;n total <?PHP echo $P_Periodo?></th>   
                            <?PHP
                            for($E=0;$E<count($C_Estrato['Nombre']);$E++){
                                /*******************************/
                                ?>
                                <th class="br">Estrato <?PHP echo $C_Estrato['Nombre'][$E]?></th>
                                <th class="br">Estrato <?PHP echo $C_Estrato['Nombre'][$E]?> <strong>%</strong></th>
                                <?PHP
                                /*******************************/
                            }//for estrato
                            ?>
                            <th class="bl">Poblaci&oacute;n Deserci&oacute;n </th>
                            <th class="bl">Porcentaje Deserci&oacute;n (%) </th>
                            <?PHP 
                           
                            for($E=0;$E<count($C_Estrato['Nombre']);$E++){
                                /*******************************/
                                ?>
                                <th class="br">Estrato <?PHP echo $C_Estrato['Nombre'][$E]?></th>
                                <th class="br">Estrato <?PHP echo $C_Estrato['Nombre'][$E]?> <strong>%</strong></th>
                                <?PHP
                                /*******************************/
                            }//for estrato
                        }
                        ?>
                    </tr>
            </thead>
            <tbody>
            <?PHP 
            
           
            
            //$C_Datos=$this->DesercionSemestral($CodigoPeriodo,'Programas');

            //$C_Total=$this->DesercionSemestral($CodigoPeriodo,'Institucional');
            /*echo '<pre>';print_r($C_Datos);
            echo 'C->'.count($C_Datos[0][5][0]['Estrato']['Uno']['id_estrato']);
            die;*/
            
            $C_Carrera = $this->Carreras();
            
            $C_Matricula    = array();
            $C_Desertores   = array();
            
            $Suma_TotalMatriculados=0;
            $Suma_ToralDesercion=0;
            
           // $C_Array= array();
            $S_Estrato  = array();
            $M_Estrato  = array();
            $T_Matriculados = array();
            $T_NoAplica = array();
            $T_Uno      = array();
            $T_Dos      = array();
            $T_Tres     = array();
            $T_Cuatro   = array();
            $T_Cinco    = array();
            $T_Seis     = array();
            
            $T_Desercion = array();
            $DT_NoAplica = array();
            $DT_Uno      = array();
            $DT_Dos      = array();
            $DT_Tres     = array();
            $DT_Cuatro   = array();
            $DT_Cinco    = array();
            $DT_Seis     = array();
            
            for($j=0;$j<count($C_Carrera);$j++){//for
                /*************************************************/
                
                $CodigoCarrera  = $C_Carrera[$j]['codigocarrera'];
                $NombreCarrera  = $C_Carrera[$j]['nombrecarrera'];
                
                $C_Datos  = $this->ConsultaDesercionSemestral($CodigoCarrera,$CodigoPeriodo);
              
                //echo '<pre>';print_r($C_Datos);die;  
                  ?>
                <tr>
                    <td align="center"><?PHP echo $j+1?></td>
                    <td><?PHP echo $NombreCarrera?></td>
                    <?PHP
                     
                    $NumData = count($C_Datos[$CodigoCarrera]['Desercion'])-1;
                    
                    
                     
                    for($x=0;$x<$NumData;$x++){
                       
                        $Estudiantes = $C_Datos[$CodigoCarrera]['Estudiantes'][$x];
                       
                        $D_Estrato = $this->EstratoEstudiantes($Estudiantes,1);
                        
                        //echo '<pre>';print_r($D_Estrato);die;
                        
                        $No_Aplica_Num  = count($D_Estrato['No_Aplica']['id_estrato']);
                        $Uno_Num        = count($D_Estrato['Uno']['id_estrato']);
                        $Dos_Num        = count($D_Estrato['Dos']['id_estrato']);
                        $Tres_Num       = count($D_Estrato['Tres']['id_estrato']);
                        $Cuatro_Num     = count($D_Estrato['Cuatro']['id_estrato']);
                        $Cinco_Num      = count($D_Estrato['Cinco']['id_estrato']);
                        $Seis_Num       = count($D_Estrato['Seis']['id_estrato']);
                        
                        $Por_No         = number_format((($No_Aplica_Num*100)/$C_Datos[$CodigoCarrera]['Desercion'][$x]),'2','.',',');
                        $Por_Uno        = number_format((($Uno_Num*100)/$C_Datos[$CodigoCarrera]['Desercion'][$x]),'2','.',',');
                        $Por_Dos        = number_format((($Dos_Num*100)/$C_Datos[$CodigoCarrera]['Desercion'][$x]),'2','.',',');
                        $Por_Tres       = number_format((($Tres_Num*100)/$C_Datos[$CodigoCarrera]['Desercion'][$x]),'2','.',',');
                        $Por_Cuatro     = number_format((($Cuatro_Num*100)/$C_Datos[$CodigoCarrera]['Desercion'][$x]),'2','.',',');
                        $Por_Cinco      = number_format((($Cinco_Num*100)/$C_Datos[$CodigoCarrera]['Desercion'][$x]),'2','.',',');
                        $Por_Seis       = number_format((($Seis_Num*100)/$C_Datos[$CodigoCarrera]['Desercion'][$x]),'2','.',',');
                        
                        /*********************************************************************/
                        
                        $E_Matriculados = $C_Datos[$CodigoCarrera]['E_Matriculados'][$x];
                        
                        //echo '<pre>';print_r($E_Matriculados);die;  
                        
                        $E_NoAplica  = $E_Matriculados['Cantidad']['0'];
                        $E_Uno       = $E_Matriculados['Cantidad']['1'];
                        $E_Dos       = $E_Matriculados['Cantidad']['2'];
                        $E_Tres      = $E_Matriculados['Cantidad']['3'];
                        $E_Cuatro    = $E_Matriculados['Cantidad']['4'];
                        $E_Cinco     = $E_Matriculados['Cantidad']['5'];
                        $E_Seis      = $E_Matriculados['Cantidad']['6'];
                        
                        $E_Por_NoAplica  = number_format((($E_NoAplica*100)/$C_Datos[$CodigoCarrera]['Matriculados'][$x]),'2','.',',');
                        $E_Por_Uno       = number_format((($E_Uno*100)/$C_Datos[$CodigoCarrera]['Matriculados'][$x]),'2','.',',');
                        $E_Por_Dos       = number_format((($E_Dos*100)/$C_Datos[$CodigoCarrera]['Matriculados'][$x]),'2','.',',');
                        $E_Por_Tres      = number_format((($E_Tres*100)/$C_Datos[$CodigoCarrera]['Matriculados'][$x]),'2','.',',');
                        $E_Por_Cuatro    = number_format((($E_Cuatro*100)/$C_Datos[$CodigoCarrera]['Matriculados'][$x]),'2','.',',');
                        $E_Por_Cinco     = number_format((($E_Cinco*100)/$C_Datos[$CodigoCarrera]['Matriculados'][$x]),'2','.',',');
                        $E_Por_Seis      = number_format((($E_Seis*100)/$C_Datos[$CodigoCarrera]['Matriculados'][$x]),'2','.',',');
                        
                        /*************************************************************************************/
                        
                        $T_Matriculados[$j][$x] = $C_Datos[$CodigoCarrera]['Matriculados'][$x];
                        
                        $T_NoAplica[$j][$x]     =  $E_NoAplica;
                        $T_Uno[$j][$x]          =  $E_Uno;
                        $T_Dos[$j][$x]          =  $E_Dos;
                        $T_Tres[$j][$x]         =  $E_Tres;
                        $T_Cuatro[$j][$x]       =  $E_Cuatro;
                        $T_Cinco[$j][$x]        =  $E_Cinco;
                        $T_Seis[$j][$x]         =  $E_Seis;
                        
                        $T_Desercion[$j][$x] = $C_Datos[$CodigoCarrera]['Desercion'][$x];
                        $DT_NoAplica[$j][$x] = $No_Aplica_Num;
                        $DT_Uno[$j][$x]      = $Uno_Num;
                        $DT_Dos[$j][$x]      = $Dos_Num;
                        $DT_Tres[$j][$x]     = $Tres_Num;
                        $DT_Cuatro[$j][$x]   = $Cuatro_Num;
                        $DT_Cinco[$j][$x]    = $Cinco_Num;
                        $DT_Seis[$j][$x]     = $Seis_Num;
                        /*************************************************************************************/
                        
                        ?>
                        <td style="text-align: center;"><?PHP echo $C_Datos[$CodigoCarrera]['Matriculados'][$x]?></td>
                        <td style="text-align: center;"><?PHP echo $E_NoAplica?></td>
                        <td style="text-align: center;"><?PHP echo $E_Por_NoAplica?>%</td>
                        <td style="text-align: center;"><?PHP echo $E_Uno?></td>
                        <td style="text-align: center;"><?PHP echo $E_Por_Uno?>%</td>
                        <td style="text-align: center;"><?PHP echo $E_Dos?></td>
                        <td style="text-align: center;"><?PHP echo $E_Por_Dos?>%</td>
                        <td style="text-align: center;"><?PHP echo $E_Tres?></td>
                        <td style="text-align: center;"><?PHP echo $E_Por_Tres?>%</td>
                        <td style="text-align: center;"><?PHP echo $E_Cuatro?></td>
                        <td style="text-align: center;"><?PHP echo $E_Por_Cuatro?>%</td>
                        <td style="text-align: center;"><?PHP echo $E_Cinco?></td>
                        <td style="text-align: center;"><?PHP echo $E_Por_Cinco?>%</td>
                        <td style="text-align: center;"><?PHP echo $E_Seis?></td>
                        <td style="text-align: center;"><?PHP echo $E_Por_Seis?>%</td>
                        <td style="text-align: center;"><?PHP echo $C_Datos[$CodigoCarrera]['Desercion'][$x]?></td>
                        <td style="text-align: center;"><?PHP echo number_format($C_Datos[$CodigoCarrera]['Porcentaje'][$x],'2','.',',')?>%</td>
                        <td style="text-align: center;"><?PHP echo $No_Aplica_Num?></td>
                        <td style="text-align: center;"><?PHP echo $Por_No?>%</td>
                        <td style="text-align: center;"><?PHP echo $Uno_Num?></td>
                        <td style="text-align: center;"><?PHP echo $Por_Uno?>%</td>
                        <td style="text-align: center;"><?PHP echo $Dos_Num?></td>
                        <td style="text-align: center;"><?PHP echo $Por_Dos?>%</td>
                        <td style="text-align: center;"><?PHP echo $Tres_Num?></td>
                        <td style="text-align: center;"><?PHP echo $Por_Tres?>%</td>
                        <td style="text-align: center;"><?PHP echo $Cuatro_Num?></td>
                        <td style="text-align: center;"><?PHP echo $Por_Cuatro?>%</td>
                        <td style="text-align: center;"><?PHP echo $Cinco_Num?></td>
                        <td style="text-align: center;"><?PHP echo $Por_Cinco?>%</td>
                        <td style="text-align: center;"><?PHP echo $Seis_Num?></td>
                        <td style="text-align: center;"><?PHP echo $Por_Seis?>%</td>    
                        <?PHP
                        
                      /************************************************/ 
                      
                    }//for
                    
                    ?>
                </tr>    
                <?PHP
                /*************************************************/
                //echo '<pre>';print_r($S_Estrato);
              }//for
               
              
              $S_matriculados = array();
              $S_NoAplica     = array();
              $S_Uno          = array();
              $S_Dos          = array();
              $S_Tres         = array();
              $S_Cuatro       = array();
              $S_Cinco        = array();
              $S_Seis         = array();
              
              
              $D_matriculados = array();
              $D_NoAplica     = array();
              $D_Uno          = array();
              $D_Dos          = array();
              $D_Tres         = array();
              $D_Cuatro       = array();
              $D_Cinco        = array();
              $D_Seis         = array();
               
              for($M=0;$M<count($T_Matriculados);$M++){
                
                for($N=0;$N<count($T_Matriculados[$M]);$N++){
                    
                    $S_matriculados[$N] = $S_matriculados[$N]+$T_Matriculados[$M][$N];
                    $S_NoAplica[$N]     = $S_NoAplica[$N]+$T_NoAplica[$M][$N];
                    $S_Uno[$N]          = $S_Uno[$N]+$T_Uno[$M][$N];
                    $S_Dos[$N]          = $S_Dos[$N]+$T_Dos[$M][$N];
                    $S_Tres[$N]         = $S_Tres[$N]+$T_Tres[$M][$N];
                    $S_Cuatro[$N]       = $S_Cuatro[$N]+$T_Cuatro[$M][$N];
                    $S_Cinco[$N]        = $S_Cinco[$N]+$T_Cinco[$M][$N];
                    $S_Seis[$N]         = $S_Seis[$N]+$T_Seis[$M][$N];
                    
                    $D_matriculados[$N] = $D_matriculados[$N]+$T_Desercion[$M][$N];
                    $D_NoAplica[$N]     = $D_NoAplica[$N]+$DT_NoAplica[$M][$N];
                    $D_Uno[$N]          = $D_Uno[$N]+$DT_Uno[$M][$N];
                    $D_Dos[$N]          = $D_Dos[$N]+$DT_Dos[$M][$N];
                    $D_Tres[$N]         = $D_Tres[$N]+$DT_Tres[$M][$N];
                    $D_Cuatro[$N]       = $D_Cuatro[$N]+$DT_Cuatro[$M][$N];
                    $D_Cinco[$N]        = $D_Cinco[$N]+$DT_Cinco[$M][$N];
                    $D_Seis[$N]         = $D_Seis[$N]+$DT_Seis[$M][$N];
                    
                }//for
                
              }/*for*/ 
               
              //echo '<pre>';print_r($S_NoAplica);die;
             ?>
             <tr>
                <td style="text-align: center;"><?PHP echo $j+1?></td>
                <td style="text-align: center;"><strong>Total</strong></td>
                <?PHP 
                for($T=0;$T<count($S_matriculados);$T++){
                    ?>
                    <td style="text-align: center;"><?PHP echo $S_matriculados[$T]?></td>
                    <td style="text-align: center;"><?PHP echo $S_NoAplica[$T]?></td>
                    <td style="text-align: center;"><?PHP echo number_format((($S_NoAplica[$T]*100)/$S_matriculados[$T]),'2','.',',')?>%</td>
                    <td style="text-align: center;"><?PHP echo $S_Uno[$T]?></td>
                    <td style="text-align: center;"><?PHP echo number_format((($S_Uno[$T]*100)/$S_matriculados[$T]),'2','.',',')?>%</td>
                    <td style="text-align: center;"><?PHP echo $S_Dos[$T]?></td>
                    <td style="text-align: center;"><?PHP echo number_format((($S_Dos[$T]*100)/$S_matriculados[$T]),'2','.',',')?>%</td>
                    <td style="text-align: center;"><?PHP echo $S_Tres[$T]?></td>
                    <td style="text-align: center;"><?PHP echo number_format((($S_Tres[$T]*100)/$S_matriculados[$T]),'2','.',',')?>%</td>
                    <td style="text-align: center;"><?PHP echo $S_Cuatro[$T]?></td>
                    <td style="text-align: center;"><?PHP echo number_format((($S_Cuatro[$T]*100)/$S_matriculados[$T]),'2','.',',')?>%</td>
                    <td style="text-align: center;"><?PHP echo $S_Cinco[$T]?></td>
                    <td style="text-align: center;"><?PHP echo number_format((($S_Cinco[$T]*100)/$S_matriculados[$T]),'2','.',',')?>%</td>
                    <td style="text-align: center;"><?PHP echo $S_Seis[$T]?></td>
                    <td style="text-align: center;"><?PHP echo number_format((($S_Seis[$T]*100)/$S_matriculados[$T]),'2','.',',')?>%</td>
                    <td style="text-align: center;"><?PHP echo $D_matriculados[$T]?></td>
                    <td style="text-align: center;"><?PHP echo number_format((($D_matriculados[$T]*100)/$S_matriculados[$T]),'2','.',',')?>%</td>
                    <td style="text-align: center;"><?PHP echo $D_NoAplica[$T]?></td>
                    <td style="text-align: center;"><?PHP echo number_format((($D_NoAplica[$T]*100)/$D_matriculados[$T]),'2','.',',')?>%</td>
                    <td style="text-align: center;"><?PHP echo $D_Uno[$T]?></td>
                    <td style="text-align: center;"><?PHP echo number_format((($D_Uno[$T]*100)/$D_matriculados[$T]),'2','.',',')?>%</td>
                    <td style="text-align: center;"><?PHP echo $D_Dos[$T]?></td>
                    <td style="text-align: center;"><?PHP echo number_format((($D_Dos[$T]*100)/$D_matriculados[$T]),'2','.',',')?>%</td>
                    <td style="text-align: center;"><?PHP echo $D_Tres[$T]?></td>
                    <td style="text-align: center;"><?PHP echo number_format((($D_Tres[$T]*100)/$D_matriculados[$T]),'2','.',',')?>%</td>
                    <td style="text-align: center;"><?PHP echo $D_Cuatro[$T]?></td>
                    <td style="text-align: center;"><?PHP echo number_format((($D_Cuatro[$T]*100)/$D_matriculados[$T]),'2','.',',')?>%</td>
                    <td style="text-align: center;"><?PHP echo $D_Cinco[$T]?></td>
                    <td style="text-align: center;"><?PHP echo number_format((($D_Cinco[$T]*100)/$D_matriculados[$T]),'2','.',',')?>%</td>
                    <td style="text-align: center;"><?PHP echo $D_Seis[$T]?></td>
                    <td style="text-align: center;"><?PHP echo number_format((($D_Seis[$T]*100)/$D_matriculados[$T]),'2','.',',')?>%</td>
                    <?PHP
                }//for
                ?>
             </tr>
            </tbody>         
         </table>
         <input type="hidden" id="Index" value="<?PHP echo $j?>" />
    </div>
<!-------->
    
    <?PHP
    /***********************************************************************/
}/*public function DisplaySocioEconomicoSemestral*/
public function Estrato(){
    global $db;
    
    $SQL_Estrato='SELECT idestrato, nombreestrato FROM estrato WHERE codigoestado=100';
    
    if($Estato=&$db->Execute($SQL_Estrato)===false){
        echo 'Error en el SQL de los Estratos...<br><br>'.$SQL_Estrato;
        die;
    }
    
    $C_Estrato  = array();
    
    while(!$Estato->EOF){
        
            $C_Estrato['id_Estrato'][]    = $Estato->fields['idestrato'];
            $C_Estrato['Nombre'][]        = $Estato->fields['nombreestrato'];
        
        $Estato->MoveNext();
    }
    
   return $C_Estrato; 
    
}//public function Estrato 
public function EstratoEstudiantes($Arreglo,$Op){
    global $db;
    
    //echo '<pre>';print_r($Arreglo);die;
    
    $E_Estrato  = array();
    for($s=0;$s<count($Arreglo['Estudiante']);$s++){
        /*********************************************/
          if($Op==1 || $Op=='1'){
            $CodigoEstudiante  = $Arreglo['Estudiante'][$s];
          }else{
            $CodigoEstudiante  =  $Arreglo['Estudiante'][$s]['codigoestudiante'];
          }
            
          
          
                
          $SQL='SELECT 

                t.idestrato,
                t.nombreestrato
                
                FROM  estudiante e  INNER JOIN  estudiantegeneral eg ON e.idestudiantegeneral=eg.idestudiantegeneral 
                
                INNER JOIN estratohistorico h ON eg.idestudiantegeneral=h.idestudiantegeneral 
                INNER JOIN estrato t ON h.idestrato=t.idestrato 
                
                AND 
                e.codigoestudiante="'.$CodigoEstudiante.'"
                AND
                h.codigoestado=100
                AND
                t.codigoestado=100';
                
          if($EstratoEstudiante=&$db->Execute($SQL)===false){
            echo 'Error en el SQL del Estrato Estudiante...<br><br>'.$SQL;
            die;
          }
               
        
        if($EstratoEstudiante->EOF || $EstratoEstudiante->fields['idestrato']==0 || $EstratoEstudiante->fields['idestrato']>6){
            $E_Estrato['No_Aplica']['id_estrato'][]=0;  
            $E_Estrato['No_Aplica']['Estrato'][]='No Aplica';
            $E_Estrato['No_Aplica']['CodigoEstudiante'][]=$CodigoEstudiante; 
        }else if($EstratoEstudiante->fields['idestrato']==1){
            $E_Estrato['Uno']['id_estrato'][]=$EstratoEstudiante->fields['idestrato'];  
            $E_Estrato['Uno']['Estrato'][]=$EstratoEstudiante->fields['nombreestrato']; 
            $E_Estrato['Uno']['CodigoEstudiante'][]=$CodigoEstudiante; 
        }else if($EstratoEstudiante->fields['idestrato']==2){
            $E_Estrato['Dos']['id_estrato'][]=$EstratoEstudiante->fields['idestrato'];  
            $E_Estrato['Dos']['Estrato'][]=$EstratoEstudiante->fields['nombreestrato']; 
            $E_Estrato['Dos']['CodigoEstudiante'][]=$CodigoEstudiante; 
        }else if($EstratoEstudiante->fields['idestrato']==3){
            $E_Estrato['Tres']['id_estrato'][]=$EstratoEstudiante->fields['idestrato'];  
            $E_Estrato['Tres']['Estrato'][]=$EstratoEstudiante->fields['nombreestrato']; 
            $E_Estrato['Tres']['CodigoEstudiante'][]=$CodigoEstudiante;
        }else if($EstratoEstudiante->fields['idestrato']==4){
            $E_Estrato['Cuatro']['id_estrato'][]=$EstratoEstudiante->fields['idestrato'];  
            $E_Estrato['Cuatro']['Estrato'][]=$EstratoEstudiante->fields['nombreestrato']; 
            $E_Estrato['Cuatro']['CodigoEstudiante'][]=$CodigoEstudiante;
        }else if($EstratoEstudiante->fields['idestrato']==5){
            $E_Estrato['Cinco']['id_estrato'][]=$EstratoEstudiante->fields['idestrato'];  
            $E_Estrato['Cinco']['Estrato'][]=$EstratoEstudiante->fields['nombreestrato']; 
            $E_Estrato['Cinco']['CodigoEstudiante'][]=$CodigoEstudiante;
        }else if($EstratoEstudiante->fields['idestrato']==6){
            $E_Estrato['Seis']['id_estrato'][]=$EstratoEstudiante->fields['idestrato'];  
            $E_Estrato['Seis']['Estrato'][]=$EstratoEstudiante->fields['nombreestrato']; 
            $E_Estrato['Seis']['CodigoEstudiante'][]=$CodigoEstudiante;
        }   
        //$Arreglo[$s]['codigoestudiante'];
        /*********************************************/
    }//for
    
    /*echo '<pre>';print_r($E_Estrato);
    die;*/
    return $E_Estrato;
}//public function EstratoEstudiantes
}/*Class*/

?>