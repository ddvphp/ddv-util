<?php
/**
 * Created by PhpStorm.
 * User: hua
 * Date: 2017/8/4
 * Time: 下午6:09
 */

namespace DdvPhp\DdvUtil\Laravel;
use DdvPhp\DdvUtil\Exception;
use \DdvPhp\DdvUtil\String\Conversion;
use \Illuminate\Database\Eloquent\Model as EloquentModel;
use Closure;

/**
 * @mixin EloquentBuilder
 * @mixin \Illuminate\Database\Query\Builder
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class EloquentBuilder extends \Illuminate\Database\Eloquent\Builder
{
    /**
     * 直接获取第一条数据
     * @param  array  $columns
     * @return Model|object|static|null
     */
    public function firstHump($columns = array('*'), $isColumnsAutoHumpToUnderline = true){
        if ($isColumnsAutoHumpToUnderline){
            foreach ($columns as $index => $key){
                $columns[$index] = Conversion::humpToUnderline($key);
            }
        }
        $res = $this->first($columns);
        if ($res instanceof EloquentModel){
            $res->toHump();
        }
        return $res;
    }
    /**
     * Convert the model instance to an array.
     *
     * @return array
     */
    public function firstHumpArray(...$parameters){
        $res = $this->firstHump(...$parameters);
        if ($res instanceof EloquentModel){
            $res = $res->toArray();
        }
        return $res;
    }
    /**
     * Execute the query as a "select" statement.
     *
     * @param  array  $columns
     * @return EloquentCollection|static[]
     */
    public function getHump($columns = array('*'), $isColumnsAutoHumpToUnderline = true){
        if ($isColumnsAutoHumpToUnderline){
            foreach ($columns as $index => $key){
                $columns[$index] = Conversion::humpToUnderline($key);
            }
        }
        $res = $this->get($columns);
        if ($res instanceof EloquentCollection){
            foreach ($res as $item){
                if ($item instanceof EloquentModel){
                    $item->toHump();
                }
            }
        }
        return $res;
    }
    /**
     * Convert the model instance to an array.
     *
     * @return array
     */
    public function getHumpArray(...$parameters){
        return $this->getHump(...$parameters)->toArray();
    }
    /**
     * 返回DdvPage对象
     * @return \DdvPhp\DdvPage $page [分页对象]
     */
    public function getDdvPage (...$parameters) {
        array_unshift($parameters, $this);
        return \DdvPhp\DdvPage::create(...$parameters);
    }
    /**
     * 返回DdvPage数组
     * @return array $data [数据数组]
     */
    public function getDdvPageModel () {
        return $this->getDdvPage(...func_get_args())->getRes();
    }
    /**
     * 返回DdvPage 驼峰数组
     * @return array $data [驼峰数组]
     */
    public function getDdvPageHumpModel () {
        $res = $this->getDdvPageModel();
        if (isset($res)&&isset($res['lists'])&&is_array($res['lists'])){
            foreach ($res['lists'] as $index => $value){
                if ((!empty($value))&&is_object($value)&&method_exists($value, 'toHump')){
                    $res['lists'][$index] = $value->toHump();
                }
            }
        }
        return $res;
    }
    /**
     * 返回DdvPage数组
     * @return array $data [数据数组]
     */
    public function getDdvPageArray () {
        return $this->getDdvPage(...func_get_args())->toArray();
    }
    /**
     * 返回DdvPage 驼峰数组
     * @return array $data [驼峰数组]
     */
    public function getDdvPageHumpArray () {
        return $this->getDdvPage(...func_get_args())->toHumpArray();
    }

    /**
     * @param $ddvIf
     * @param null $whereCall
     * @return $this
     * @throws Exception
     */
    public function whereDdvIf($ddvIf, $whereCall = null){
        if (empty($whereCall)) {
            if ($ddvIf instanceof Closure) {
                $whereCall = $ddvIf;
                $ddvIf = true;
            }else{
                throw new Exception('Must have a closure parameter', 'MUST_HAVE_A_CLOSURE_PARAMETER');
            }
        } elseif ($whereCall instanceof Closure) {
            if ($ddvIf instanceof Closure) {
                $ddvIf = $ddvIf($this);
            }
        }
        if ($ddvIf){
            $res = $whereCall($this);
            if (!empty($res)){
                return $res;
            }
        }
        return $this;
    }
    public function __construct(\Illuminate\Database\Query\Builder $query)
    {
        parent::__construct($query);
        $this->passthru[] = 'getDdvPage';
        $this->passthru[] = 'getDdvPageArray';
    }

}