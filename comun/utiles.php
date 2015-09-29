<?php
// *************************************
function anade_filtrado ( &$parametros_filtrado, $nombre_campo, $valor, $comparativa, $concatenado="and", $tipo_comparativa="normal" ){
		$fila = array("campo"=>$nombre_campo, "valor"=>$valor, "comparativa"=>$comparativa, "concatenado"=>$concatenado, "tipo_comparativa"=>$tipo_comparativa);
		array_push($parametros_filtrado, $fila);
        return $parametros_filtrado;
}

// *************************************************    
    function componer_filtro ($parametros_filtrado){
    	$sw1 = 0; $sw2 = 0; $filters = "";
    	if($parametros_filtrado!=null){
	        // Primero proceso los ands
	        foreach($parametros_filtrado as $key=>$value ){
	            if(strtolower($value['concatenado'])=="and"){
	                if( $sw1 != 0 ) $filters .= " ".$value['concatenado'];
	                else $filters .= " where ( ";
	                $sw1++;
	                // $filters .= " ".$value['concatenado'];
	                if($value['comparativa'] == "like" || $value['comparativa'] == "not like") {
	                    $filters .= " LOWER(".$value['campo'].") ".$value['comparativa']." LOWER('%".trim($value['valor'])."%') ";
	                } else if($value['comparativa'] == "is" || $value['comparativa'] == "is not") {
	                    $filters .= " ".$value['campo']." ".$value['comparativa']." ".trim($value['valor'])." ";
	                } else {
	                	if(trim($value['valor'])!='now()'){
	                    	$filters .= " ".$value['campo']." ".$value['comparativa']." '".trim($value['valor'])."' ";
	                	}else{
	                		$filters .= " ".$value['campo']." ".$value['comparativa']." ".trim($value['valor'])." ";
	                	}
	                }
	            }
	        }
	        foreach($parametros_filtrado as $key=>$value ){
	            if(strtolower($value['concatenado'])=="or"){
	                if($sw1==0 && $sw2==0) $filters .= " where ( ";
	                // Si solo hay dos elementos el and de concatenacion tiene que ser un or
	                if(sizeof($parametros_filtrado)==2){ 
	                    if($sw1!=0 && $sw2==0) $filters .= " ) or ( ";
	                } else {
	                    if($sw1!=0 && $sw2==0) $filters .= " ) and ( ";
	                }
	                if( $sw2 != 0 ) $filters .= " ".$value['concatenado'];
	                $sw2++;
	                // $filters .= " ".$value['concatenado'];
	                if($value['comparativa'] == "like") {
	                    $filters .= " LOWER(".$value['campo'].") ".$value['comparativa']." LOWER('%".trim($value['valor'])."%') ";
	                } else if($value['comparativa'] == "is" || $value['comparativa'] == "is not") {
	                    $filters .= " ".$value['campo']." ".$value['comparativa']." ".trim($value['valor'])." ";
	                } else {
	               	 	if(trim($value['valor'])!='now()'){
	                    	$filters .= " ".$value['campo']." ".$value['comparativa']." '".trim($value['valor'])."' ";
	                	}else{
	                		$filters .= " ".$value['campo']." ".$value['comparativa']." ".trim($value['valor'])." ";
	                	}
	                }
	            }
	        }
    	}
        // if($sw2!=0 || sizeof($parametros_filtrado)==1) 
        if($sw1!=0 || $sw2!=0) $filters .= " ) ";
        return $filters;
    }

// ***********************************
function convertir_fecha_espanol ($fecha){
        if(trim($fecha)=="" || $fecha=="0000-00-00 00:00:00" || $fecha=="0000-00-00"){
            return "";
        } else {
            $timestamp = strtotime($fecha);
            return date('d/m/Y', $timestamp);
        }
    }
?>