<?php
namespace app\src\indep\admin\service;


use app\src\toge\dao\UsersDao;

class UsersService
{
    //根据id方式
    public function getById($id){
        $userDao=new UsersDao();
        return $userDao->selectId($id);
    }
    //查询所有
    public function getAll(){
        $userDao=new UsersDao(true);
        return $userDao->selectAll();
    }
}
