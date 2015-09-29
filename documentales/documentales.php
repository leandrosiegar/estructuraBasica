<?php 
require_once($_SERVER["DOCUMENT_ROOT"]."/comun/cabecera.php");
require_once (DOCUMENT_ROOT_EDC."/clases/CDocumentales.php");

$CDocumentales = new CDocumentales($conn);
$filtros = array();
$es_pelicula = false;

switch ($_REQUEST["accion"]) {
	case "ultimas-entradas.html":
		$orderby = "documentales.id_documental desc";
		$titular = "Documentales: Últimas entradas";
		$linkUrl = "documentales-ultimas-entradas.html&pag=";
		break;
	default:
		$es_pelicula = true;
		break;
}

if (!$es_pelicula) {
	$roDocumentales = $CDocumentales->get_listado_documentales($orderby, $_REQUEST["pag"]);
	require("vista_documentales.php");
}
else {
	$roDocumentales = $CDocumentales->get_documental("documentales-".$_REQUEST["accion"]);
	if ($roDocumentales->resultado) {
		require("vista_show_documentales.php");
	}
}
?>