<?php

namespace Application\Db\Table;

class Config extends Table
{

    public function getByKey($key)
    {
        return $this->value("SELECT `value` FROM `config` WHERE `key` = '$key'");
    }

    public function updateKey($key, $value)
    {
        $this->db()->exec("UPDATE `config` SET `value` = '$value' WHERE `key` = '$key'");
    }
    protected function value($select)
    {
        return $this->db()->query($select)->fetch(\Pdo::FETCH_COLUMN);
    }

}