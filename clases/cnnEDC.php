<?php
class cnnEDC {
	var $entorno = "dev"; // dev | prod

	var $site = ""; 
	var $user = "";
	var $passwd = "";
	var $database = "";
	var $id = null;
	var $conn = null;
	
	
	function cnnEDC(){
		// LOCAL

		$this->site = "localhost"; 
		$this->user = "root";
		$this->passwd = ""; 
		$this->database = "lsgthedn_eldespotricador2";

		
		// INTERNET
		/*
		$this->site = "localhost"; 
		$this->user = "xxx";
		$this->passwd = "xxx";
		$this->database = "lsgthedn_eldespotricador";
		*/
		
	}
	
	function connect (){
		$this->conn = mysqli_connect($this->site,$this->user,$this->passwd, $this->database) or die("Connection error");
        mysqli_query($this->conn,"SET NAMES utf8");
	}
	function disconnect (){
			mysqli_close($this->conn);
	}

	function load ($query){
		$arr_registros = array ();
		$result = mysqli_query($this->conn,$query);
		// print $query."<br>\n";
		if( $result ){ 
		    while( $r = mysqli_fetch_array($result) ){
				$row = new stdClass ();
				foreach ($r as $key => $value){
					if(!is_numeric($key)){
						$row->{$key} = $value;
					}
				}
		    	array_push ($arr_registros, $row);
		    }
		}
		return $arr_registros;
	}
	
	function insert ($query){
		$result = mysqli_query($this->conn,$query);
		if( $result ){ 
		    $id = mysqli_insert_id($this->conn);
		    return $id;
		} else {
			return false;
		}
	}


	function update ($query){
		$result = mysqli_query($this->conn,$query);
		if( $result ){ 
		    return true;
		} else {
			return false;
		}
	}
	function remove ($query){
		$result = mysqli_query($this->conn,$query);
		if( $result ){ 
		    return true;
		} else {
			return false;
		}
	}
	function get_table_info($tabla){
	    $query= "desc `".$tabla."`;";
	    // print $query;
	    $datos = $this->load($query);
	    $cont=0;
	    foreach($datos as $row){
	    	$campos[$cont] = new stdClass();
	        $campos[$cont]->COLUMN_NAME = $row->Field;
	        $campos[$cont]->COLUMN_KEY  = $row->Key;
	        $campos[$cont]->IS_NULLABLE = $row->Null;
	        $campos[$cont]->COLUMN_TYPE = $row->Type;
	        $cont++;
	    }
	    // print_r($campos);
	    return $campos;
	}

	function stor($datos,$tabla,$indices=null){
		
		if($datos==null || sizeof($datos)==0){
			die ("Los parametros enviados a stor para la tabla ".$tabla." est�n vacios");
		}
	    $campos = $this->get_table_info($tabla);
	    if($campos==null || sizeof($campos)==0){
			die ("La tabla enviada ".$tabla." no existe en el modelo o no tiene campos");
		}
	    
	    $get->null=false;
	    $get->insert=false;
	    $cont_primary=0;
	    //COMPRUEBO SI $INDICES ES UN ARRAY
	    if(is_array($indices)){
	        //COMPRUEBO INDICES EN LOS DATOS
	        foreach($indices as $indice){
	            $ind=false;
	            foreach($datos as $row2=>$valor){
	                if($indice == $row2){
	                    if(!isset($valor) || $valor == ""){
	                        $ind=false;
	                    }else{
	                        $ind=true;
	                    }
	                    $primary[$cont_primary]->COLUMN=$row2;
	                    $primary[$cont_primary]->VALOR=$valor;
	                    break;
	                }
	                else{
	                    $primary[$cont_primary]->COLUMN=$indice;
	                    $primary[$cont_primary]->VALOR=null;
	                }
	            }
	            $cont_primary++;
	            if($ind == false){
	                $get->insert= true;
	            }
	        }
	    //COMPRUEBO SI VIENE UN STRING
	    }else if(strlen($indices)>0){
	        $ind=false;
	        //COMPRUEBO INDICES EN LOS DATOS
	        foreach($datos as $row2=>$valor){
	            if($indices == $row2){
	                if(!isset($valor) || $valor == ""){
	                    $ind=false;
	                }else{
	                    $ind=true;
	                }
	                $primary[$cont_primary]->COLUMN=$row2;
	                $primary[$cont_primary]->VALOR=$valor;
	                break;
	            }
	            else{
	                $primary[$cont_primary]->COLUMN=$indices;
	                $primary[$cont_primary]->VALOR=null;
	            }
	        }
	        $cont_primary++;
	        if($ind == false){
	            $get->insert= true;
	        }
	    //SI NO VIENE COMO ARRAY O COMO STRING TIENE QUE VENIR COMO NULL
	    } else if ($indices == null){
	    	$indices = array();
	    	if(sizeof($campos)>0){
	    		foreach($campos as $row){
		            $ind=false;
		            if($row->COLUMN_KEY == "PRI"){
		            	foreach($datos as $row2=>$valor){
		                    if($row->COLUMN_NAME == $row2){
		                        if(!isset($valor) || $valor == ""){
		                            $ind=false;
		                        }
		                        else{
		                            $ind=true;
		                        }
		                        $primary[$cont_primary]->COLUMN=$row2;
		                        $primary[$cont_primary]->VALOR=$valor;
		                        break;
		                    }
		                    else{
		                        $primary[$cont_primary]->COLUMN=$row->COLUMN_NAME;
		                        $primary[$cont_primary]->VALOR=null;
		                    }
		                }
		                $indices[$row->COLUMN_NAME] = $primary[$cont_primary]->VALOR;
		                $cont_primary++;
		                if($ind == false){
		                    $get->insert= true;
		                }
		            }
		        }
		    } else {
		    	die("Error al comprobar la tabla");
		    }
	    }
	  
	    //COMPRUEBO SI LOS CAMPOS QUE NO PUEDEN SER NULOS VIENEN CON DATOS
	    $null=false;

	    foreach($campos as $row){
	        if($row->COLUMN_KEY != "PRI"){
	            if($row->IS_NULLABLE == "NO"){
	                foreach($datos as $row2=>$valor){
	                    if($row->COLUMN_NAME == $row2){
	                        if(!isset($valor) || $valor == ""){
	                            $null=false;
	                        }else{
	                            $null=true;
	                        }
	                    }
	                }
	                if($null == false){	  
	                	//print_r($row);exit;
	                    $get->null= true;
	                }
	            }
	        }
	        if($row->COLUMN_TYPE == "smallint(1)"){
	        	if(!$datos[$row->COLUMN_NAME]){
	        		$datos[$row->COLUMN_NAME]="0";
	        	}
	        }
	    }
	    
	    
	    //PREPARO EL WHERE PARA SELECT QUE COMPRUEBA SI EXISTE Y UPDATE
	    if($get->insert == false){
	        $cadena_primary="";
	        foreach($primary as $row){
	            if($cadena_primary==""){
	                $cadena_primary .= "`".$row->COLUMN."`='".$row->VALOR."'";
	            }else {
	                $cadena_primary .= " AND `".$row->COLUMN."`='".$row->VALOR."'";
	            }
	        } 
	    }
	      
	    
	    $cadena_insert_def="";
	    $cadena_update="";
	    $cont=0;
	    //CREAMOS LOS PRIMARY EN LA CADENA PARA LOS INSERT
	    foreach($primary as $row){
	        if($cadena_insert_def==""){
	            if($primary_insert == false){
	                $cadena_insert_def .= "".$row->COLUMN;
	                if(!isset($row->VALOR)){
	                    $cadena_insert_val .= strtolower("null");
	                }else{
	                    $cadena_insert_val .= "'".$row->VALOR."'";  
	                }  
	            }
	        } else {
	            if($primary_insert == false){
	                $cadena_insert_def .= ", ".$row->COLUMN;
	                if(!isset($row->VALOR)){
	                    $cadena_insert_val .= ", ".strtolower("null")."";
	                }else{
	                    $cadena_insert_val .= ", '".$row->VALOR."'";
	                }
	            }
	        }
	    }
	    foreach($campos as $camp){
	    	foreach($datos as $row=>$valor){
	    		if($row == $camp->COLUMN_NAME){
		        	$primary_column=false;
			        //COMPRUEBA SI EL DATO QUE LE VAMOS A PASAR ES UNA PRIMARY
			        foreach($primary as $row2){
			            if($row2->COLUMN == $row){
			                $primary_column=true;
			            }
			        }
			        //CREAMOS CADENA PARA UPDATE
			        
			        if($cadena_update==""){
			            if($primary_column==false){
			                $cadena_update .="`" .$row."`='".$valor."'";
			            }
			        } else {
			            if($primary_column==false){
			                $cadena_update .= ", `".$row."`='".$valor."'";
			            }
			        }
			    
			        //CREAMOS CADENA PARA INSERT
			       
			        if($cadena_insert_def==""){
			            if($primary_column == false){
			                $cadena_insert_def .= "`".$row."`";
			                if(!isset($valor)){
			                    $cadena_insert_val .= strtolower("null");
			                }else{
			                    $cadena_insert_val .= "'".$valor."'";  
			                }  
			            }
			        } else {
			            if($primary_column == false){
			                $cadena_insert_def .= ", `".$row."`";
			                if(!isset($valor)){
			                    $cadena_insert_val .= ", ".strtolower("null")."";
			                }else{
			                    $cadena_insert_val .= ", '".$valor."'";
			                }
			            }
			        }
	    		}
	    	}
	    }

	    
	    $es_update = false;
	    //COMPROBAMOS QUE SE UN INSERT Y QUE NO TENGA NULOS
	    if($get->insert == true && $get->null == false){
	        $query="INSERT INTO ".$tabla."(".$cadena_insert_def.") values(".$cadena_insert_val.")";
	         //print $query."<br>\n";
	    }
	    //SINO EN PRINCIPIO ES UN UPDATE
	    else if($get->insert == false){
	    	//COMPROBAMOS SI LAS PRIMARY EXISTEN EN LA TABLA SI NO EXISTEN HACEMOS UN INSERT
	        $query="SELECT * FROM ".$tabla." WHERE ".$cadena_primary;
	        $existe=$this->load($query);
	        if(sizeof($existe)>0){
	        	$es_update = true;
	            $query="UPDATE ".$tabla." SET ".$cadena_update." WHERE ".$cadena_primary;
	             //print $query."<br>\n";
	        }else{
	            if($get->null == false){
	                $query="INSERT INTO ".$tabla."(".$cadena_insert_def.") values(".$cadena_insert_val.")";
	                //print($query);
	            }
	        }
	    }else{
	    	//print("hay alg�n campo requerido que est� a null");
	    }
	   	
	   // print("SQL LSG: ".$query."<br>");exit;
	    
	    $result = mysqli_query($this->conn,$query);
		if( $result ){ 
			if(!$es_update){
				$this->id = mysqli_insert_id ($this->conn);
			} else {
				// Si es un update devuelvo de los indices el primero que tiene un valor numerico
				if(is_array($indices) && sizeof($indices)>0){
					foreach($indices as $c=>$v){
						if(is_numeric($v)){
							$this->id = $v;
							break;
						}
					}
				}
			}
		    return true;
		} else {
			return false;
		}
	}
	
	
}
?>