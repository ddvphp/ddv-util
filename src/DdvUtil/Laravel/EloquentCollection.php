<?php
/**
 * Created by PhpStorm.
 * User: hua
 * Date: 2017/8/4
 * Time: 下午6:09
 */

namespace DdvPhp\DdvUtil\Laravel;

use Closure;
use DdvPhp\DdvUtil\String\Conversion;
use Illuminate\Database\Eloquent\Model as EloquentModel;


class EloquentCollection extends \Illuminate\Database\Eloquent\Collection
{
    /**
     * 小写下滑杠转驼峰
     * @param true|false $isConversionOriginal [是否强制转换源]
     * @return Model|static|$this [请求对象]
     */
    public function toHump($isConversionOriginal = false){
        $this->map(function ($model){
            if (empty($model)){
                return;
            }
            is_object($model) && method_exists($model, 'toHump') && $model->toHump();
        });
        return $this;
    }

    /**
     * @param $fn
     * @return Model|static|$this [请求对象]
     */
    public function mapLists($fn){
        if ($fn instanceof Closure){
            if(is_array($this) || is_object($this)){
                foreach ($this as $key => $item){
                    $fn($item, $key);
                    $this[$key] = $item;
                }
            }
        }
        return $this;
    }

}