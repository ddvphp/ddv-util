<?php
/**
 * Created by PhpStorm.
 * User: hua
 * Date: 2017/8/5
 * Time: 上午11:22
 */

namespace DdvPhp\DdvUtil\Laravel;
use \DdvPhp\DdvUtil\String\Conversion;


class QueryBuilder extends \Illuminate\Database\Query\Builder
{
    /**
     * Update the model in the database.
     *
     * @param  array  $attributes
     * @return int
     */
    public function updateByHump(array $attributes = array())
    {
        $attributes = Conversion::humpToUnderlineByIndexArray($attributes);
        return $this->update($attributes);
    }
}