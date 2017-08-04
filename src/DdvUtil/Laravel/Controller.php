<?php
namespace DdvPhp\DdvUtil\Laravel;
use \DdvPhp\DdvUtil\String\Conversion;

trait Controller
{
    public $verifyObj;   //验证类
    public $verifyData;    //验证成功后的数据
    /**
     * 验证
     * @param array $rule [验证规则]
     * @param array|string $data [验证数据源]
     * @throws ApiException
     */
    public function verify(array $rule, $data = 'GET')
    {
        if (empty($verifyObj)) {
            $this->verifyObj = new \JiaLeo\Laravel\Verify\Verify();
        }

        $result = $this->verifyObj->check($rule, $data);
        $this->verifyData = $this->verifyObj->data;

        return $result;
    }
}
