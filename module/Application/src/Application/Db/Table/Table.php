<?php

namespace Application\Db\Table;

use Application\Db\Connect;

class Table
{

    protected function db()
    {
        return Connect::defaultConnection();
    }

}