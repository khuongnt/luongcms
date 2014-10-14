<?php
/*
Uploadify
Copyright (c) 2012 Reactive Apps, Ronnie Garcia
Released under the MIT License <http://www.opensource.org/licenses/mit-license.php> 
*/

// Define a destination


if (!empty($_FILES)) {
	$tempFile = $_FILES['Filedata']['tmp_name'];
	$targetPath = $_SERVER['DOCUMENT_ROOT'].$_POST['folder'];
	$text=date("dmy-his");
	$nameFile=explode(".", $_FILES['Filedata']['name']);
	$count=count($nameFile);
	if($count>=3){
		for($i=1;$i<=($count-2);$i++){
			$nameFile[0].=".".$nameFile[$i];
		}
		$nameFile[1]=$nameFile[$count-1];
	}
	$targetFile =  str_replace('//','/',$targetPath) ."/".$nameFile[0].$text.".".$nameFile[1] ;
	
	// Validate the file type
	// Những file cho phép up
	$fileTypes  = str_replace('*.','',$_POST['fileext']);
	$fileTypes  = str_replace(';','|',$fileTypes);
	$fileTypes = explode("|", $fileTypes);
	$fileParts = pathinfo($_FILES['Filedata']['name']);
	if (in_array($fileParts['extension'],$fileTypes)) {
		move_uploaded_file($tempFile,$targetFile);
		echo str_replace($_SERVER['DOCUMENT_ROOT'],'',$targetFile);
	} else {
		echo 0;
	}
}
?>