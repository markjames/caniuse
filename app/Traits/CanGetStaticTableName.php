<?php namespace App\Traits;

trait CanGetStaticTableName
{
    public static function tableName()
    {
        return with(new static)->getTable();
    }
}
