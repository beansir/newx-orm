<?php
/**
 * 数据库查询类
 * @author: bean
 * @version: 1.0
 */
namespace newx\orm\base;

class Query extends BaseObject
{
    /**
     * 查询字段
     */
    public $field;

    /**
     * 查询条件
     */
    public $where;

    /**
     * 排序规则
     */
    public $orderBy;

    /**
     * 查询条数
     */
    public $limit;

    /**
     * 是否返回数组结果集，默认false
     */
    public $asArray;

    /**
     * 查询表
     */
    public $tableName;

    /**
     * 类名
     */
    public $className;

    /**
     * 表连接
     */
    public $join;
}