<?php
namespace app\src\toge\dao;

use core\ufoahc\db\MysqliConnect;

class UsersDao extends MysqliConnect
{
    protected function _getTableName(){
        return 'users';
    }
    protected function _getDefaultId(){
        return 'id';
    }
}