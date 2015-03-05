<?php

namespace Application\Db\Table;

class ResponseStatus extends Table
{

    public function getAll()
    {
        return $this->db()->query("SELECT * FROM `response_status`")->fetchAll(\Pdo::FETCH_ASSOC);
    }

    public function getByAlias($alias)
    {
        return $this->value("SELECT `value` FROM `response_status` WHERE `alias` = '$alias'");
    }

    public function getByValue($value)
    {
        return $this->value("SELECT `alias` FROM `response_status` WHERE `value` = '$value'");
    }

    public function updateAlias($alias, $value)
    {
        $this->db()->exec("UPDATE `response_status` SET `value` = '$value' WHERE `alias` = '$alias'");
    }
    protected function value($select)
    {
        return $this->db()->query($select)->fetch(\Pdo::FETCH_COLUMN);
    }

}