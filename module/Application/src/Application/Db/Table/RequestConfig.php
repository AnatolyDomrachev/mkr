<?php

namespace Application\Db\Table;

class RequestConfig extends Table
{

    public function getAll()
    {
        return $this->db()->query("SELECT * FROM `request_config`")->fetchAll(\Pdo::FETCH_ASSOC);
    }

    public function getByAlias($alias)
    {
        $select = "SELECT * FROM `request_config` WHERE `alias` = '$alias'";
        return $this->db()->query($select)->fetch(\Pdo::FETCH_ASSOC);
    }

    public function getNumberByAlias($alias)
    {
        return $this->value("SELECT `number` FROM `request_config` WHERE `alias` = '$alias'");
    }

    public function getByValue($value)
    {
        return $this->value("SELECT `alias` FROM `request_config` WHERE `value` = '$value'");
    }

    public function updateAlias($alias, $number, $requestFile, $responseFile)
    {
        $this->db()->exec("UPDATE `request_config` SET
                `number` = '$number',
                `response_file` = '$responseFile',
                `request_file` = '$requestFile'
            WHERE `alias` = '$alias'");
    }
    protected function value($select)
    {
        return $this->db()->query($select)->fetch(\Pdo::FETCH_COLUMN);
    }

}