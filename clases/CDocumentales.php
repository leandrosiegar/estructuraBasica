<?php
class CDocumentales {
	
	var $conn;
	
	function CDocumentales($conn){
		$this->conn = $conn;
	}
	
	// ****************************************
	function crear_ficha($arr) {
		require_once (DOCUMENT_ROOT_EDC."/clases/CYoutube.php");
		$Cyoutube = new CYoutube($this->conn);
		foreach ($arr as $clave => $valor) {
			$roYoutube = $Cyoutube->get_nom_cineastas_por_id($valor->id_director);
			if ($roYoutube->resultado) 
				$arr[$clave]->nom_director = $roYoutube->datos[0]->nom_art_youtube;
			
			$roYoutube = $Cyoutube->get_nom_cineastas_por_id($valor->id_actor1);
			if ($roYoutube->resultado) 
				$arr[$clave]->nom_actor1 = $roYoutube->datos[0]->nom_art_youtube;
			
			$roYoutube = $Cyoutube->get_nom_cineastas_por_id($valor->id_actor2);
			if ($roYoutube->resultado) 
				$arr[$clave]->nom_actor2 = $roYoutube->datos[0]->nom_art_youtube;
			
			$roYoutube = $Cyoutube->get_nom_cineastas_por_id($valor->id_actor3);
			if ($roYoutube->resultado) 
				$arr[$clave]->nom_actor3 = $roYoutube->datos[0]->nom_art_youtube;
		}
		return $arr;
	}
	
	// *****************************************
	function get_busq_documentales($buscar) {
		$ro = new RESPONSE_OBJECT();
        $ro->resultado = true;

		$query = "SELECT nom_documental, foto_documental, nom_pel_youtube, documentales.urlCorta,
nom_art_youtube	FROM documentales 
left join pel_youtube on pel_youtube.id = documentales.id_pelicula  
left join art_youtube on art_youtube.id = documentales.id_cineasta  
where nom_pel_youtube like '%".$buscar."%' or nom_documental like '%".$buscar."%' or 
nom_art_youtube like '%".$buscar."%' order by nom_documental";
		if (($arr_reg = $this->conn->load($query)) != null) {
			$ro->datos = $this->crear_ficha($arr_reg);
		} else {
            $ro->resultado = false;
            $ro->mensaje   = "Error:".$query;
        }
        return $ro;
	}
	
	// *************************************************
	function get_listado_documentales($orderby, $desde) {
		$ro = new RESPONSE_OBJECT();
        $ro->resultado = true;
		
		$query = "SELECT * from documentales";
		
		if (($arr_reg = $this->conn->load($query)) != null) {
			$ro->datos = $arr_reg;
		} else {
            $ro->resultado = false;
            $ro->mensaje   = "Error:".$query;
        }
        return $ro;
	}
	
	// *************************************************
	function get_directores_garci($desde) {
		$ro = new RESPONSE_OBJECT();
        $ro->resultado = true;
		
		$query = "select nom_art_youtube from art_youtube where id in (select distinct(id_director) from videos_garci) 
					order by nom_art_youtube limit ".($desde*12).",12";
		if (($arr_reg = $this->conn->load($query)) != null) {
			$ro->datos = $this->crear_ficha($arr_reg);
		} else {
            $ro->resultado = false;
            $ro->mensaje   = "Error:".$query;
        }
        return $ro;
	}
	
	// ***************************************************
	function get_documental($urlCorta) {
		$ro = new RESPONSE_OBJECT();
        $ro->resultado = true;
		
		$query = "SELECT nom_documental, foto_documental, nom_pel_youtube, documentales.urlCorta,
nom_art_youtube	FROM documentales 
left join pel_youtube on pel_youtube.id = documentales.id_pelicula  
left join art_youtube on art_youtube.id = documentales.id_cineasta  
where documentales.urlCorta = '".$urlCorta."'";
		if (($arr_reg = $this->conn->load($query)) != null) {
			$ro->datos = $this->crear_ficha($arr_reg);
		} else {
            $ro->resultado = false;
            $ro->mensaje   = "Error:".$query;
        }
        return $ro;
	}

}