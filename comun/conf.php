<?php
	define('DOCUMENT_ROOT_EDC', $_SERVER['DOCUMENT_ROOT'].'/');
	define('ROOT_EDC', '/');
	require_once DOCUMENT_ROOT_EDC.'/comun/comun.php';
	require_once DOCUMENT_ROOT_EDC.'/comun/utiles.php';
	require_once (DOCUMENT_ROOT_EDC."/clases/RESPONSE_OBJECT.php");
	require_once (DOCUMENT_ROOT_EDC."/clases/cnnEDC.php");
	
	$conn = new cnnEDC();
	$conn->connect();
	
	if(!isset($_SESSION)){
		session_start();
	}

	date_default_timezone_set("Europe/Madrid");
	
	error_reporting(E_ERROR); // para que no muestre los warnings

	header('Content-Type: text/html; charset=utf-8');
?>