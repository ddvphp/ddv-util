<?php
namespace DdvPhp\DdvUtil\Laravel;

use DdvPhp\DdvUtil\Exception;
use DdvPhp\DdvUtil\Laravel\Proxy\Interfaces\TransactionQuque;
use DdvPhp\DdvUtil\String\Conversion;

/**
 * @mixin \DdvPhp\DdvUtil\Laravel\EloquentBuilder
 * @mixin \DdvPhp\DdvUtil\Laravel\QueryBuilder
 * @mixin \Illuminate\Database\Eloquent\Model
 */
trait Model
{
    protected $ddvSnakeAttributes = null;

    /**
     * Get the model's relationships in array form.
     *
     * @return array
     */
    public function relationsToArray(){
        $snakeAttributes = null;
        if (isset($this->ddvSnakeAttributes)&&is_bool($this->ddvSnakeAttributes)){
            $snakeAttributes = static::$snakeAttributes;
            static::$snakeAttributes = $this->ddvSnakeAttributes;
        }
        $res = parent::relationsToArray();
        is_null($snakeAttributes) || (static::$snakeAttributes = $snakeAttributes);
        return $res;
    }
    /**
     * 小写下滑杠转驼峰
     * @param true|false $isConversionOriginal [是否强制转换源]
     * @return Model $this [请求对象]
     */
    public function toHump($isConversionOriginal = false){
        if ($isConversionOriginal){
            $this->original = Conversion::underlineToHumpByIndexArray($this->original);
        }
        if (!empty($this->relations)){
            if (is_array($this->relations)||is_object($this->relations)){
                foreach ($this->relations as $key => $item){
                    if (empty($item)||!is_object($item)){
                        continue;
                    }
                    if (method_exists($item, 'toHump')){
                        $this->relations[$key] = $item->toHump();
                    }
                }
            }
        }
        $this->ddvSnakeAttributes = false;
        $this->attributes = Conversion::underlineToHumpByIndexArray($this->attributes);
        return $this;
    }
    /**
     * 下滑杠转小写驼峰
     * @param true|false $isConversionOriginal [是否强制转换源]
     * @return Model $this [请求对象]
     */
    public function toUnderline($isConversionOriginal = false){
        if ($isConversionOriginal){
            $this->original = Conversion::humpToUnderlineByIndexArray($this->original);
        }
        if (!empty($this->relations)){
            if (is_array($this->relations)||is_object($this->relations)){
                foreach ($this->relations as $key => $item){
                    if ((!empty($item))&&is_object($item)&&method_exists($item, 'toUnderline')){
                        $this->relations[$key] = $item->toUnderline();
                    }
                }
            }
        }
        $this->ddvSnakeAttributes = true;
        $this->attributes = Conversion::humpToUnderlineByIndexArray($this->attributes);
        return $this;
    }
    /**
     * 返回小写下滑杠转驼峰
     * @param true|false $isConversionOriginal [是否强制转换源]
     * @return array $data [请求数组]
     */
    public function toHumpArray($isConversionOriginal = false){
        return $this->toHump($isConversionOriginal)->toArray();
    }
    /**
     * 设置保存数据 驼峰自动转小写下划线
     * @param array $data [需要保存的数组]
     * @return static|Model|$this [请求对象]
     */
    public function setDataByHumpArray ($data = array()) {
        return $this->setDataByArray(Conversion::humpToUnderlineByArray($data));
    }

    /**
     * 设置保存数据 数组自动遍历保存
     * @param array $data 需要保存的数组
     * @return static|Model|$this
     */
    public function setDataByArray ($data = array()) {
        return $this->setData($data);
    }

    /**
     * @param $model
     * @param null $defaultData
     * @param bool $isInitDefaultData
     * @return static|Model|$this
     * @throws Exception
     */
    public function setDataByModel ($model, $defaultData = null, $isInitDefaultData = true){
        if (is_object($model)&&method_exists($model, 'toArray')){
            $data = $isInitDefaultData ? (empty($defaultData) ? [] : $defaultData) : [];
            return $this->setDataByArray(array_merge($data, $model->toArray()));
        }else{
            if (empty($defaultData)&&!is_array($defaultData)){
                throw new Exception('Model is empty, please set the default data');
            }else{
                return $this->setDataByArray($defaultData);
            }
        }
    }
    /**
     * 设置保存数据 数组自动遍历保存
     * @param array $data [需要保存的数组]
     * @return static|Model|$this [请求对象]
     */
    public function setData ($data = array()) {
        if (method_exists(parent::class, 'setData')){
            call_user_func_array(array(parent::class, 'setData'), func_get_args());
        }elseif (!empty($data)&&(is_array($data)||is_object($data))){
            foreach ($data as $key => $v) {
                $this->setAttribute($key, $v);
            }
        }
        return $this;
    }

    /**
     * 移动资源
     * @param $keyOld
     * @param $keyNew
     * @param null $defaultData
     * @return static|Model|$this [请求对象]
     */
    public function moveRelation($keyOld, $keyNew, $defaultData = null){
        if (empty($this->relations[$keyOld])){
            $this->setAttribute($keyNew, $defaultData);
            @$this->removeAttribute($keyOld);
        }else{
            $this->relations[$keyNew] = $this->relations[$keyOld];
            $this->removeAttribute($keyOld);
        }
        return $this;
    }

    /**
     * 资源复制别名
     * @param $keyOld
     * @param $keyNew
     * @return static|Model|$this [请求对象]
     */
    public function aliasRelation($keyOld, $keyNew){
        if (!empty($this->relations[$keyOld])){
            $this->relations[$keyNew] = &$this->relations[$keyOld];
        }
        return $this;
    }

    /**
     * 插入属性
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return static|Model|$this [请求对象]
     */
    public function pushAttribute($key, $value)
    {
        $array = $this->getAttribute($key);
        $array = is_array($array)?$array:array();
        $array[] = $value;
        $this->setAttribute($key, $array);
        return $this;
    }

    /**
     * 移除一个参数
     * @param $key
     */
    public function removeAttribute($key){
        if(!empty($key)&&is_array($key)){
            array_map([$this, 'removeAttribute'], $key);
            return $this;
        }elseif(strpos($key, ',')!==false){
            $this->removeAttribute(explode(',', $key));
        }
        unset($this->$key);
        return $this;
    }
    /**
     * Create a new Eloquent query builder for the model.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @return EloquentBuilder|static
     */
    public function newEloquentBuilder($query)
    {
        return new EloquentBuilder($query);
    }
    /**
     * Create a new Eloquent Collection instance.
     *
     * @param  array  $models
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function newCollection(array $models = [])
    {
        return new EloquentCollection($models);
    }
    /**
     * Get a new query builder instance for the connection.
     *
     * @return QueryBuilder
     */

    protected function newBaseQueryBuilder()
    {
        $connection = $this->getConnection();

        return new QueryBuilder(
            $connection, $connection->getQueryGrammar(), $connection->getPostProcessor()
        );
    }

    /**
     * 插入事物
     * @param TransactionQuque $quque
     * @return static|Model
     * @throws Exceptions\ProxyExceptions
     */
    public static function pushQuqueTransaction(TransactionQuque $quque){
        $model = new static();
        $quque->pushTransactionModel($model);
        return $model;
    }
}