<?php
/**
 * Created by PhpStorm.
 * User: xialei
 * Date: 2018/7/19
 * Time: 上午10:12
 */

namespace Wechat\reply;

/**
 * 响应Builder
 * Class ResponseBuilder
 * @package Wechat\reply
 */
abstract class ResponseBuilder
{
    protected $request = [];

    /**
     * ResponseBuilder constructor.
     * @param array $request
     */
    public function __construct(array $request)
    {
        $this->request = $request;
    }

    abstract public function build(): string;
}