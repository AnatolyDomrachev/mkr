<?php

namespace Application\Db\Table;

class Dictionary extends Table
{

    public function getByAlias($alias)
    {
        return $this->value("SELECT `value` FROM `dictionary` WHERE `alias` = '$alias'");
    }

    public function getListByType($type)
    {
        return $this->values("SELECT `value` FROM `dictionary` WHERE `type` = '$type'");
    }

    public function getBps()
    {
        $bps = array();
        $select = "SELECT `id`, `value` FROM `dictionary` WHERE `type` = 'bps'";
        foreach($this->db()->query($select)->fetchAll(\Pdo::FETCH_ASSOC) as $row) {
            $bps[$row['id']] = $row['value'];
        }
        return $bps;
    }

    public function getByType($type)
    {
        $select = "SELECT `value`, `alias` FROM `dictionary` WHERE `type` = '$type'";
        return $this->db()->query($select)->fetchAll(\Pdo::FETCH_ASSOC);
    }

    protected function value($select)
    {
        return $this->db()->query($select)->fetch(\Pdo::FETCH_COLUMN);
    }

    protected function values($select)
    {
        return $this->db()->query($select)->fetchAll(\Pdo::FETCH_COLUMN);
    }

}