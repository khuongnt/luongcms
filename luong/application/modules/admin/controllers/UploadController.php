<?php

class Admin_UploadController extends Luong_Controller_Admin_Action {

    public function thumbAction() {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
        if (!empty($_FILES)) {
            $tempFile = $_FILES['Filedata']['tmp_name'];
// 			$size = getimagesize($tempFile);
            $targetPath = $_SERVER['DOCUMENT_ROOT'] . $_POST['folder'];
            $this->checkPathDirectory($targetPath);
            $text = date("-dmyhis");
            $nameFile = explode(".", $_FILES['Filedata']['name']);
            $count = count($nameFile);
            if ($count >= 3) {
                for ($i = 1; $i <= ($count - 2); $i++) {
                    $nameFile[0].="." . $nameFile[$i];
                }
                $nameFile[1] = $nameFile[$count - 1];
            }
            $targetFile = str_replace('//', '/', $targetPath) . "/" . $nameFile[0] . $text . "." . $nameFile[1];
            $fileTypes = explode("|", $_POST['fileext']);
            $fileParts = pathinfo($_FILES['Filedata']['name']);
            if (in_array($fileParts['extension'], $fileTypes)) {
                move_uploaded_file($tempFile, $targetFile);
                $forder_thumb = $_SERVER['DOCUMENT_ROOT'] . $_POST['folder'] . '/thumb/';
                $this->checkPathDirectory($forder_thumb);
                $thumb = new Luong_Helper_Thumb();
                $thumb->Quality = 100;
                $thumb->Thumbsaveas = $nameFile[1];
                $thumb->Thumbfilename = $nameFile[0] . $text . "." . $nameFile[1];
                $thumb->Thumblocation = $forder_thumb;
                $thumb->Thumbwidth = 140;
                $thumb->Thumbheight = 80;
                $thumb->Createthumb($targetFile, 'file');
                $feedback = '1|' . $nameFile[0] . $text . "." . $nameFile[1];
            } else {
                $feedback = "0|Kiểu file không đúng";
            }
            echo $feedback;
        }
    }

    public function noThumbAction() {
        try {
            $this->_helper->layout->disableLayout();
            $this->_helper->viewRenderer->setNoRender();
            if (!empty($_FILES)) {
                $tempFile = $_FILES['Filedata']['tmp_name'];
                $size = getimagesize($tempFile);
                $targetPath = $_SERVER['DOCUMENT_ROOT'] . $_POST['folder'];
                $this->checkPathDirectory($targetPath);
                $text = date("-dmyhis");
                $nameFile = explode(".", $_FILES['Filedata']['name']);
                $count = count($nameFile);
                if ($count >= 3) {
                    for ($i = 1; $i <= ($count - 2); $i++) {
                        $nameFile[0].="." . $nameFile[$i];
                    }
                    $nameFile[1] = $nameFile[$count - 1];
                }
                $targetFile = str_replace('//', '/', $targetPath) . "/" . $nameFile[0] . $text . "." . $nameFile[1];
                $fileTypes = explode("|", $_POST['fileext']);
                $fileParts = pathinfo($_FILES['Filedata']['name']);
                if (in_array($fileParts['extension'], $fileTypes)) {
                    move_uploaded_file($tempFile, $targetFile);
                    $feedback = '1|' . $nameFile[0] . $text . "." . $nameFile[1];
                } else {
                    $feedback = "0|Kiểu file không đúng";
                }
                echo $feedback;
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    public function checkPathDirectory($paths) {
        @mkdir($paths);
        @chmod($paths, 0777);
        return $paths;
    }

}
