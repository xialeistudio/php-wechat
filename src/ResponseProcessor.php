<?php
/**
 * User: xialei
 * Date: 2018/7/14
 */

namespace Wechat;

use Psr\Http\Message\ResponseInterface;

/**
 * Wechat Response Processor
 * Trait ResponseProcessor
 * @package wechat
 */
trait ResponseProcessor
{
    /**
     * 处理响应
     * @param ResponseInterface $response
     * @return mixed|string
     * @throws WechatException
     */
    protected function handleResponse(ResponseInterface $response)
    {
        if (!$this->isJsonResponse($response)) {
            return $response->getBody()->getContents();
        }
        $data = json_decode($response->getBody()->getContents(), true);
        if (!empty($data['errcode'])) {
            throw new WechatException($data['errmsg'], $data['errcode']);
        }
        return $data;
    }

    /**
     * 检测是否为JSON响应
     * @param ResponseInterface $response
     * @return bool
     */
    protected function isJsonResponse(ResponseInterface $response)
    {
        if (!$response->hasHeader('Content-Type')) {
            return false;
        }
        $types = $response->getHeader('Content-Type');
        foreach ($types as $type) {
            if (strpos($type, 'application/json') !== false) {
                return true;
            }
        }
        return false;
    }
}