<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;

class FileController extends AbstractActionController
{

    public function downloadAction()
    {
        if(!isset($this->getRequest()->getQuery()->fileName)) {
            throw new \ErrorException('Не передана имя файла');
        }

        $fileName = $this->getRequest()->getQuery()->fileName;
        $filePath = $this->getRoot() . '/data/files/' . $fileName;

        if(!file_exists($filePath)) {
            throw new \ErrorException("Файл не найден ($filePath)");
        }

        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . basename($filePath));
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filePath));

        if ($fd = fopen($filePath, 'rb')) {
            while (!feof($fd)) {
                print fread($fd, 1024);
            }
            fclose($fd);
        }
        exit;
    }

    private function getRoot()
    {
        return dirname(dirname(dirname(dirname(dirname(dirname(__FILE__))))));
    }

}