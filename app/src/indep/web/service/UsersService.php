<?php
namespace app\src\indep\web\service;


use app\src\toge\dao\TestDbDao;
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
        $userDao=new UsersDao();
        return $userDao->selectAll();
    }
    /*like 方法测试*/
    public function like(){
        $userDao=new UsersDao();
        return $userDao->like('name','郁',array(),array('sort_order'=>'desc'),1,2,array('name','content','sort_order'));
    }
    /*数量*/
    public function count(){
        $userDao=new UsersDao();
        return $userDao->count();
    }
    public function max(){
        $userDao=new UsersDao();
        return $userDao->max('sort_order');
    }
    public function min(){
        $userDao=new UsersDao();
        return $userDao->min('sort_order');
    }
    public function avg(){
        $userDao=new UsersDao();
        return $userDao->avg('sort_order');
    }
    public function sum(){
        $userDao=new UsersDao();
        return $userDao->sum('sort_order');
    }

    public function tGetAll(){
        $userDao=new TestDbDao();
        return $userDao->selectAll();
    }
}
