<?php
/**
 * Created by PhpStorm.
 * User: AF
 * Date: 2016/6/17
 * Time: 10:41
 * 部分代码重复率有点高，还需优化
 * 数据库链接异常代码11001，增删改 异常代码11002，查询错误代码11003，sql语句问题和参数有误代码11004
 */

namespace core\ufoahc\db;


abstract class MysqliImp
{
    /**
     * @var \mysqli
     */
    private $link;

    /**
     * 获取表名
     * @return mixed
     */
    abstract protected function _getTableName();

    /**
     * 默认id
     * @return mixed
     */
    abstract protected function _getDefaultId();

    /**
     * 初始化数据库连接
     * @param array $config
     * @throws \Exception
     */
    public function setConnectInit($config){
        if(!$this->link){
            $conn=new \mysqli($config['db_host'],$config['db_user'],$config['db_pwd'],$config['db_name'],$config['port']);
            if($conn->errno){
                throw new \Exception('创建数据连接失败，原因：'.$conn->error,11001);
            }
            $conn->set_charset($config['db_char_set']);
            $this->link=$conn;
        }
    }

    /**
     * 关闭数据连接的
     */
    function __destruct(){
        if($this->link){
            $this->link->close();
        }
    }

    /**
     * 插入数据库
     * @link https://bugs.php.net/bug.php?id=43568
     * @param array $data 数组格式 数据格式为：数据库字段为键值，键值值为需要插入的值的数组，类似 array('id'=>'000','name'=>'test')
     * @return bool
     * @throws \Exception
     */
    public function insert($data){
        if(!is_array($data)||empty($data)){
            throw new \Exception('插入数据格式有误',11002);
        }
        $keyArr=array();
        $tmpArr=array();
        $valueArr=array();
        $bindType='';
        foreach ($data as $key=>$value){
            $keyArr[]=$key;
            $tmpArr[]='?';
            $valueArr[]=&$data[$key];
            $bindType.= $this->_determineType($value);
        }
        $keyValue=implode(',', $keyArr);
        $tempWhy=implode(',',$tmpArr);
        $sql='insert into '.$this->_getTableName().' ('.$keyValue.') values ('.$tempWhy.')';
        $args[]=$bindType;
        $parameter=array_merge($args,$valueArr);
        $stmt=$this->_prepare($sql);
        call_user_func_array(array($stmt,'bind_param'), self::refValues($parameter));
        $stmt->execute();
        $res=$this->link->affected_rows;
        $stmt->close();
        if($res>0){
            return true;
        }
        return false;
    }

    /**
     * 根据id更新
     * @param string $id 需要更新的id
     * @param array $data 需要更新的数据 格式：数据库字段为键值，键值值为需要更新的值的数组，类似 array('sex'=>'1','name'=>'test')
     * @return bool
     * @throws \Exception
     */
    public function updateId($id,$data){
        if(!is_array($data)||empty($data)){
            throw new \Exception('根据id更新数据格式错误或者为空数组',11002);
        }
        $keyArr=array();
        $valueArr=array();
        $bindType='';
        foreach ($data as $key=>$value){
            $keyArr[]=$key.'=? ';
            $valueArr[]=&$data[$key];
            $bindType.=$this->_determineType($value);
        }
        $keyValue=implode(',',$keyArr);
        $sql='update '.$this->_getTableName().' set '.$keyValue.' where '.$this->_getDefaultId().'=? ';
        $bindType.=$this->_determineType($id);
        $args[]=$bindType;
        array_push($valueArr,$id);
        $parameter=array_merge($args,$valueArr);
        $stmt=$this->_prepare($sql);
        call_user_func_array(array($stmt,'bind_param'), self::refValues($parameter));
        $stmt->execute();
        $res=$this->link->affected_rows;
        $stmt->close();
        if($res>0){
            return true;
        }
        return false;
    }

    /**
     * 条件更新语句
     * @param array $data 需要更新的数据 格式：数据库字段为键值，键值值为需要更新的值的数组，类似 array('sex'=>'1','name'=>'tt')
     * @param array $where 满足需要更新的条件 格式：数据库字段为键值，键值值为满足更新数据的值，类似 array('id'=>'king','name'=>'test')
     * @return bool
     * @throws \Exception
     */
    public function update($data,$where){
        if(!is_array($data)||empty($data)||!is_array($where)||empty($where)){
            throw new \Exception('条件数据数据格式错误或者为空数组',11002);
        }
        //拼装需要更新的数据
        $keyArr=array();
        $valueArr=array();
        $bindType='';
        foreach ($data as $key=>$value){
            $keyArr[]=$key.'=? ';
            $valueArr[]=&$data[$key];
            $bindType.=$this->_determineType($value);
        }
        $keyValue=implode(',',$keyArr);
        //拼装条件更新的数据
        $whereData=$this->_andWhere($where);
        $bindType.=$whereData['bind_type'];
        $whereStr=$whereData['where_string'];
        $whereValueArr=$whereData['where_value_arr'];
        //拼装sql语句
        $sql='update '.$this->_getTableName().' set '.$keyValue.' where '.$whereStr;
        $args[]=$bindType;
        $bindData=array_merge($valueArr,$whereValueArr);
        $parameter=array_merge($args,$bindData);
        $stmt=$this->_prepare($sql);
        call_user_func_array(array($stmt,'bind_param'), self::refValues($parameter));
        $stmt->execute();
        $res=$this->link->affected_rows;
        $stmt->close();
        if($res>0){
            return true;
        }
        return false;
    }

    /**
     * 根据id删除
     * @param string $id 需要删除数据的id
     * @return bool
     */
    public function deleteId($id){
        $sql='delete from '.$this->_getTableName().' where '.$this->_getDefaultId().'=?';
        $bindType=$this->_determineType($id);
        $stmt=$this->_prepare($sql);
        $stmt->bind_param($bindType,$id);
        $stmt->execute();
        $res=$this->link->affected_rows;
        $stmt->close();
        if($res>0){
            return true;
        }
        return false;
    }

    /**
     * 根据条件删除操作
     * @param array $where 满足删除数据的条件 格式：数据库字段为键值，键值值为满足删除数据的值，类似 array('id'=>'king','name'=>'test')
     * @return bool
     * @throws \Exception
     */
    public function delete($where){
        if(!is_array($where)||empty($where)){
            throw new \Exception('条件删除数据格式错误或者为空数组',11002);
        }
        $bindType='';
        //拼装条件更新的数据
        $whereData=$this->_andWhere($where);
        $bindType.=$whereData['bind_type'];
        $whereStr=$whereData['where_string'];
        $whereValueArr=$whereData['where_value_arr'];
        //sql语句拼装
        $sql='delete from '.$this->_getTableName().' where '.$whereStr;

        $args[]=$bindType;
        $parameter=array_merge($args,$whereValueArr);
        $stmt=$this->_prepare($sql);
        call_user_func_array(array($stmt,'bind_param'), self::refValues($parameter));
        $stmt->execute();
        $res=$this->link->affected_rows;
        $stmt->close();
        if($res>0){
            return true;
        }
        return false;
    }

    /**
     * 条件查询
     * @param array $where 满足获取数据的条件 格式：数据库字段为键值，键值值为满足获得数据的值，类似 array('id'=>'king','name'=>'test')
     * @param array $order 排序 格式：需要排序的字段为数组键值，降序（desc）或者升序（asc）的为对应键值的值 类似 array('id'=>'desc','name'=>'asc')
     * @param int $offset 跳过的页数
     * @param int $fetchNum 需要查询出来的记录条数 默认全部
     * @param array $getInfo 需要查询出来的字段 无键值的数组 填写需要查询的字段即可 类似 array('id','name')
     * @param array $orWhere 或条件数据  格式与where条件格式一样
     * @return array
     * @throws \Exception
     */
    public function select($where,$order=array(),$offset=0,$fetchNum=0,$getInfo=array('*'),$orWhere=array()){
        if(!is_array($where)||empty($where)||!is_array($order)||!is_array($orWhere)){
            throw new \Exception('条件查询数据格式错误，请检查',11003);
        }
        if(empty($getInfo)||!is_array($getInfo)){
            $getInfo=array('*');
        }
        //拼装where数据
        $bindType='';
        $whereData=$this->_andWhere($where);
        $bindType.=$whereData['bind_type'];
        $whereStr=$whereData['where_string'];
        $whereValueArr=$whereData['where_value_arr'];
        $sql='select '.implode(',',$getInfo).' from '.$this->_getTableName().' where '.$whereStr;
        //拼装orWhere数据
        $whereOrValueArr=array();
        if(!empty($orWhere)){
            $orWhereData=$this->_orWhere($orWhere);
            $bindType.=$orWhereData['bind_type'];
            $whereOrStr=$orWhereData['where_string'];
            $whereOrValueArr=$orWhereData['where_value_arr'];
            $sql.=' or '.$whereOrStr;
        }
        if(!empty($order)){
            $orderArr=array();
            foreach($order as $orderKey=>$rowOrder){
                $orderArr[]=$orderKey.' '.$rowOrder;
            }
            $sql.=' order by '.implode(',',$orderArr);
        }
        if($fetchNum>0&&$offset>=0){
            $sql.=' limit '.$offset.','.$fetchNum;
        }
        $args[]=$bindType;
        $bindData=array_merge($whereValueArr,$whereOrValueArr);
        $parameter=array_merge($args,$bindData);
        $stmt=$this->_prepare($sql);
        call_user_func_array(array($stmt,'bind_param'), self::refValues($parameter));
        $stmt->execute();
        $returnData=$this->_dynamicBindResults($stmt);
        $stmt->free_result();
        $stmt->close();
        return $returnData;
    }

    /**
     * 如果不传任何参数默认查询所有
     * @param array $order 排序 格式：需要排序的字段为数组键值，降序（desc）或者升序（asc）的为对应键值的值 类似 array('id'=>'desc','name'=>'asc')
     * @param int $offset 跳过的页数
     * @param int $fetchNum 需要查询出来的记录条数 默认全部
     * @param array $getInfo 需要查询出来的字段 无键值的数组 填写需要查询的字段即可 类似 array('id','name')
     * @param array $orWhere or条件的数据 格式：数据库字段为键值，键值值为满足获得数据的值，类似 array('id'=>'king','name'=>'test')
     * @return array
     * @throws \Exception
     */
    public function selectAll($order=array(),$offset=0,$fetchNum=0,$getInfo=array('*'),$orWhere=array()){
        if(!is_array($order)||!is_array($orWhere)){
            throw new \Exception('查询所有数据格式错误，请检查',11003);
        }
        if(empty($getInfo)||!is_array($getInfo)){
            $getInfo=array('*');
        }
        $bindType='';
        $sql="select ".implode(',',$getInfo).' from '.$this->_getTableName();
        $whereOrValueArr=array();
        if(!empty($orWhere)){
            $retOrData=$this->_orWhere($orWhere);
            $bindType.=$retOrData['bind_type'];
            $whereOrStr=$retOrData['where_string'];
            $whereOrValueArr=$retOrData['where_value_arr'];
            $sql.=' where '.$whereOrStr;
        }
        if(!empty($order)){
            $orderArr=array();
            foreach($order as $orderKey=>$rowOrder){
                $orderArr[]=$orderKey.' '.$rowOrder;
            }
            $sql.=' order by '.implode(',',$orderArr);
        }
        if($fetchNum>0&&$offset>=0){
            $sql.=' limit '.$offset.','.$fetchNum;
        }
        if(!empty($whereOrValueArr)){
            $args[]=$bindType;
            $parameter=array_merge($args,$whereOrValueArr);
            $stmt=$this->_prepare($sql);
            call_user_func_array(array($stmt,'bind_param'), self::refValues($parameter));
            $stmt->execute();
        }else{
            $stmt=$this->_prepare($sql);
            $stmt->execute();
        }
        $returnData=$this->_dynamicBindResults($stmt);
        $stmt->free_result();
        $stmt->close();
        return $returnData;
    }

    /**
     * 根据id查询 返回一维数组或空
     * @param string $id 获取数据的id
     * @param array $getInfo 需要查询出来的字段 无键值的数组 填写需要查询的字段即可 类似 array('id','name')
     * @return array
     */
    public function selectId($id,$getInfo=array('*')){
        if(empty($getInfo)||!is_array($getInfo)){
            $getInfo=array('*');
        }
        $sql="select ".implode(',',$getInfo).' from '.$this->_getTableName()." where ".$this->_getDefaultId()."=?";
        $bindType=$this->_determineType($id);
        $stmt=$this->_prepare($sql);
        $stmt->bind_param($bindType,$id);
        $stmt->execute();
        $returnData=$this->_dynamicBindResults($stmt);
        $stmt->free_result();
        $stmt->close();
        return isset($returnData[0])?$returnData[0]:array();
    }

    /**
     * 根据sql语句查询
     * @param string $sql
     * @param array $param
     * @return array
     */
    public function selectSql($sql,$param=array()){
        $stmt=$this->_prepare($sql);
        if(!empty($param)&&is_array($param)){
            $paramTmp=array();
            $bindType='';
            foreach($param as $key=>$value){
                $bindType.=$this->_determineType($param[$key]);
                $paramTmp[]=$param[$key];
            }
            $args[]=$bindType;
            $parameter=array_merge($args,$paramTmp);
            call_user_func_array(array($stmt,'bind_param'), self::refValues($parameter));
        }
        $stmt->execute();
        if(stristr($sql,'select')){
            $returnData=$this->_dynamicBindResults($stmt);
            $stmt->free_result();
            $stmt->close();
            return $returnData;
        }else{
            $res=$this->link->affected_rows;
            $stmt->close();
            if($res>0){
                return true;
            }
            return false;
        }
    }

    /**
     * 不等于或者大于说着其他操作 直接是字符串
     * @param string $whereString "id>10 or id<3 and name='test'"
     * @param array $getInfo 需要查询出来的字段 无键值的数组 填写需要查询的字段即可 类似 array('id','name')
     * @return array
     */
    public function selectNotEqualAll($whereString,$getInfo=array('*')){
        if(empty($getInfo)||!is_array($getInfo)){
            $getInfo=array('*');
        }
        $sql='select '.implode(',',$getInfo).' from '.$this->_getTableName().' where '.$whereString;
        $stmt=$this->_prepare($sql);
        $stmt->execute();
        $returnData=$this->_dynamicBindResults($stmt);
        $stmt->free_result();
        $stmt->close();
        return $returnData;
    }

    /**
     * select 和selectAll的综合函数
     * @param array $where
     * @param array $order
     * @param int $offset
     * @param int $fetchNum
     * @param array $getInfo
     * @param array $orWhere
     * @return array
     * @throws \Exception
     */
    public function selects($where=array(),$order=array(),$offset=0,$fetchNum=0,$getInfo=array('*'),$orWhere=array()){
        if(empty($getInfo)||!is_array($getInfo)){
            $getInfo=array('*');
        }
        $sql='select '.implode(',',$getInfo).' from '.$this->_getTableName();
        $bindType='';
        $whereValueArr=array();
        if(!empty($where)){
            $whereData=$this->_andWhere($where);
            $bindType.=$whereData['bind_type'];
            $whereStr=$whereData['where_string'];
            $whereValueArr=$whereData['where_value_arr'];
            $sql.=' where '.$whereStr;
        }
        $whereOrValueArr=array();
        if(!empty($orWhere)){
            $orWhereData=$this->_orWhere($orWhere);
            $bindType.=$orWhereData['bind_type'];
            $whereOrStr=$orWhereData['where_string'];
            $whereOrValueArr=$orWhereData['where_value_arr'];
            if(empty($where)){
                $sql.=' where '.$whereOrStr;
            }else{
                $sql.=' or '.$whereOrStr;
            }
        }
        if(!empty($order)){
            $orderArr=array();
            foreach($order as $orderKey=>$rowOrder){
                $orderArr[]=$orderKey.' '.$rowOrder;
            }
            $sql.=' order by '.implode(',',$orderArr);
        }
        if($fetchNum>0&&$offset>=0){
            $sql.=' limit '.$offset.','.$fetchNum;
        }
        if(empty($whereValueArr)&&empty($whereOrValueArr)){
            $stmt=$this->_prepare($sql);
        }else{
            $args[]=$bindType;
            $bindData=array_merge($whereValueArr,$whereOrValueArr);
            $parameter=array_merge($args,$bindData);
            $stmt=$this->_prepare($sql);
            call_user_func_array(array($stmt,'bind_param'), self::refValues($parameter));
        }
        $stmt->execute();
        $returnData=$this->_dynamicBindResults($stmt);
        $stmt->free_result();
        $stmt->close();
        return $returnData;
    }

    /**
     * like 搜索
     * @param $stringName
     * @param $content
     * @param array $where
     * @param array $order
     * @param int $offset
     * @param int $fetchNum
     * @param array $getInfo
     * @param array $orWhere
     * @return array
     * @throws \Exception
     */
    public function like($stringName,$content,$where=array(),$order=array(),$offset=0,$fetchNum=0,$getInfo=array('*'),$orWhere=array()){
        if(stristr($content,'_')){
            $content=str_replace('_', "\\_", $content);
        }
        if(stristr($content, '%')){
            $content=str_replace('%', '', $content);
        }

        $sql='select '.implode(',',$getInfo).' from '.$this->_getTableName().' where '.$stringName." like '%".$content."%' ";
        $bindType='';
        $whereValueArr=array();
        if(!empty($where)){
            $whereData=$this->_andWhere($where);
            $bindType.=$whereData['bind_type'];
            $whereStr=$whereData['where_string'];
            $whereValueArr=$whereData['where_value_arr'];
            $sql.=' and '.$whereStr;
        }
        $whereOrValueArr=array();
        if(!empty($orWhere)){
            $orWhereData=$this->_orWhere($orWhere);
            $bindType.=$orWhereData['bind_type'];
            $whereOrStr=$orWhereData['where_string'];
            $whereOrValueArr=$orWhereData['where_value_arr'];
            $sql.=' or '.$whereOrStr;
        }
        if(!empty($order)){
            $orderArr=array();
            foreach($order as $orderKey=>$rowOrder){
                $orderArr[]=$orderKey.' '.$rowOrder;
            }
            $sql.=' order by '.implode(',',$orderArr);
        }
        if($fetchNum>0&&$offset>=0){
            $sql.=' limit '.$offset.','.$fetchNum;
        }
        if(empty($whereValueArr)&&empty($whereOrValueArr)){
            $stmt=$this->_prepare($sql);
        }else{
            $args[]=$bindType;
            $bindData=array_merge($whereValueArr,$whereOrValueArr);
            $parameter=array_merge($args,$bindData);
            $stmt=$this->_prepare($sql);
            call_user_func_array(array($stmt,'bind_param'), self::refValues($parameter));
        }
        $stmt->execute();
        $returnData=$this->_dynamicBindResults($stmt);
        $stmt->free_result();
        $stmt->close();
        return $returnData;
    }

    /**
     * 获取单条数据
     * @param array $where
     * @param array $order
     * @param array $getInfo
     * @param array $orWhere
     * @return array
     * @throws \Exception
     */
    public function get($where=array(),$order=array(),$getInfo=array('*'),$orWhere=array()){
        if(empty($getInfo)||!is_array($getInfo)){
            $getInfo=array('*');
        }
        $sql='select '.implode(',',$getInfo).' from '.$this->_getTableName();
        $bindType='';
        $whereValueArr=array();
        if(!empty($where)){
            $whereData=$this->_andWhere($where);
            $bindType.=$whereData['bind_type'];
            $whereStr=$whereData['where_string'];
            $whereValueArr=$whereData['where_value_arr'];
            $sql.=' where '.$whereStr;
        }
        $whereOrValueArr=array();
        if(!empty($orWhere)){
            $orWhereData=$this->_orWhere($orWhere);
            $bindType.=$orWhereData['bind_type'];
            $whereOrStr=$orWhereData['where_string'];
            $whereOrValueArr=$orWhereData['where_value_arr'];
            if(empty($where)){
                $sql.=' where '.$whereOrStr;
            }else{
                $sql.=' or '.$whereOrStr;
            }
        }
        if(!empty($order)){
            $orderArr=array();
            foreach($order as $orderKey=>$rowOrder){
                $orderArr[]=$orderKey.' '.$rowOrder;
            }
            $sql.=' order by '.implode(',',$orderArr);
        }
        $sql.=' limit 1';
        if(empty($whereValueArr)&&empty($whereOrValueArr)){
            $stmt=$this->_prepare($sql);
        }else{
            $args[]=$bindType;
            $bindData=array_merge($whereValueArr,$whereOrValueArr);
            $parameter=array_merge($args,$bindData);
            $stmt=$this->_prepare($sql);
            call_user_func_array(array($stmt,'bind_param'), self::refValues($parameter));
        }
        $stmt->execute();
        $returnData=$this->_dynamicBindResults($stmt);
        $stmt->free_result();
        $stmt->close();
        return isset($returnData[0])?$returnData[0]:array();
    }

    /**
     * 获取行的数量
     * @param array $where 获取行数的条件 数据格式 数组键值为数据库字段，键值对应的值为满足获的条件 类似 array('id'=>'king','name'=>'test')
     * @param string $columnName 列名
     * @return string|int
     */
    public function count($where=array(),$columnName='*'){
        $sql="select count(".$columnName.") as count from ".$this->_getTableName();
        $returnData=$this->_group($sql,$where);
        return $returnData[0]['count'];
    }

    /**
     * 取最小值
     * @param $columnName
     * @param array $where
     * @return mixed
     * @throws \Exception
     */
    public function min($columnName,$where=array()){
        if(empty($columnName)){
            throw new \Exception('查询最小值参数错误',1003);
        }
        $sql="select min(".$columnName.") as min from ".$this->_getTableName();
        $returnData=$this->_group($sql,$where);
        return $returnData[0]['min'];
    }

    /**
     * 取最大值
     * @param $columnName
     * @param array $where
     * @return mixed
     * @throws \Exception
     */
    public function max($columnName,$where=array()){
        if(empty($columnName)){
            throw new \Exception('查询最大值参数错误',1003);
        }
        $sql="select max(".$columnName.") as max from ".$this->_getTableName();
        $returnData=$this->_group($sql,$where);
        return $returnData[0]['max'];
    }

    /**
     * 平均值
     * @param $columnName
     * @param array $where
     * @return mixed
     * @throws \Exception
     */
    public function avg($columnName,$where=array()){
        if(empty($columnName)){
            throw new \Exception('查询平均值参数错误',1003);
        }
        $sql="select avg(".$columnName.") as avg from ".$this->_getTableName();
        $returnData=$this->_group($sql,$where);
        return $returnData[0]['avg'];
    }

    /**
     * 求和
     * @param $columnName
     * @param array $where
     * @return mixed
     * @throws \Exception
     */
    public function sum($columnName,$where=array()){
        if(empty($columnName)){
            throw new \Exception('查询总和参数错误',1003);
        }
        $sql="select sum(".$columnName.") as sum from ".$this->_getTableName();
        $returnData=$this->_group($sql,$where);
        return $returnData[0]['sum'];
    }

    /**
     * 分组函数的执行
     * @param $sql
     * @param $where
     * @return array
     * @throws \Exception
     */
    private function _group($sql,$where){
        if(!empty($where)&&is_array($where)){
            //拼装where数据
            $bindType='';
            $whereData=$this->_andWhere($where);
            $bindType.=$whereData['bind_type'];
            $whereStr=$whereData['where_string'];
            $whereValueArr=$whereData['where_value_arr'];

            $sql.=" where ".$whereStr;
            $args[]=$bindType;
            $parameter=array_merge($args,$whereValueArr);
            $stmt=$this->_prepare($sql);
            call_user_func_array(array($stmt,'bind_param'), self::refValues($parameter));
            $stmt->execute();
        }else{
            $stmt=$this->_prepare($sql);
            $stmt->execute();
        }
        $returnData=$this->_dynamicBindResults($stmt);
        $stmt->free_result();
        $stmt->close();
        return $returnData;
    }

    /**
     * 开始事务
     * @return bool
     */
    public function beginTransaction(){
        return $this->link->autocommit(false);
    }

    /**
     * 提交事务
     * @return bool
     */
    public function commitTransaction(){
        return $this->link->commit();
    }

    /**
     * 事务回滚
     * @return bool
     */
    public function rollbackTransaction(){
        return $this->link->rollback();
    }


    /**
     * 参数化查询初始化参数
     * @link http://php.net/manual/zh/mysqli-stmt.bind-param.php
     * @param  array $data
     * @return array
     */
    static private function refValues($data){
        $refs=array();
        foreach($data as $key=>$value){
            $refs[]=&$data[$key];
        }
        return $refs;
    }

    /**
     * 获取参数传递类型
     * @link   https://github.com/joshcam/PHP-MySQLi-Database-Class/tree/v1.1
     * @version   1.1
     * @param $dataType
     * @return string
     * @throws \Exception
     */
    protected function _determineType($dataType)
    {
        switch (gettype($dataType)) {
            case 'string':
                return 's';
                break;
            case 'integer':
                return 'i';
                break;
            case 'blob':
                return 'b';
                break;
            case 'double':
                return 'd';
                break;
        }
        throw new \Exception('参数化查询数据类型有误',11004);
    }

    /**
     * 取得结果集
     * @link   https://github.com/joshcam/PHP-MySQLi-Database-Class/tree/v1.1
     * @version   1.1
     * @param \mysqli_stmt $stmt
     * @return array
     */
    protected function _dynamicBindResults($stmt)
    {
        $parameters = array();
        $results = array();
        $meta = $stmt->result_metadata();
        while ($field = $meta->fetch_field()) {
            $parameters[] = &$row[$field->name];
        }
        call_user_func_array(array($stmt, 'bind_result'), $parameters);
        while ($stmt->fetch()) {
            $x = array();
            foreach ($row as $key => $val) {
                $x[$key] = $val;
            }
            $results[] = $x;
        }
        return $results;
    }

    /**
     * and 条件数据拼装
     * @param array $where
     * @return array
     */
    protected function _andWhere($where){
        $bindType='';
        $whereKeyArr=array();
        $whereValueArr=array();
        foreach($where as $keys=>$values){
            $bindType.=$this->_determineType($values);
            $whereKeyArr[]=$keys.'=? ';
            $whereValueArr[]=&$where[$keys];
        }
        $whereStrTmp='';
        foreach($whereKeyArr as $row){
            $whereStrTmp.=$row.' and ';
        }
        $whereStr=substr($whereStrTmp,0,-4);
        $returnData=array(
            'bind_type'=>$bindType,
            'where_value_arr'=>$whereValueArr,
            'where_string'=>$whereStr
        );
        return $returnData;
    }

    /**
     * or 条件数据拼装
     * @param array $where
     * @return array
     */
    protected function _orWhere($where){
        $bindType='';
        $whereKeyArr=array();
        $whereValueArr=array();
        foreach($where as $keys=>$values){
            $bindType.=$this->_determineType($values);
            $whereKeyArr[]=$keys.'=? ';
            $whereValueArr[]=&$where[$keys];
        }
        $whereStrTmp='';
        foreach($whereKeyArr as $row){
            $whereStrTmp.=$row.' or ';
        }
        $whereStr=substr($whereStrTmp,0,-3);
        $returnData=array(
            'bind_type'=>$bindType,
            'where_value_arr'=>$whereValueArr,
            'where_string'=>$whereStr
        );
        return $returnData;
    }

    /**
     * 验证sql与表的正确性
     * @param $sql
     * @return \mysqli_stmt
     * @throws \Exception
     */
    protected function _prepare($sql){
        $stmt = $this->link->prepare($sql);
        if (!$stmt) {
            $msg = $this->link->error . " --SQL语句: " . $sql;
            throw new \Exception($msg,11004);
        }
        return $stmt;
    }
}