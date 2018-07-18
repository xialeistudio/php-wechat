<?php
/**
 * User: xialei
 * Date: 2018/7/15
 */

namespace Wechat\Tests;


use Dotenv\Dotenv;
use PHPUnit\Framework\TestCase;
use Wechat\OfficialAccount\OfficialAccount;

/**
 * 公众平台单元测试
 * Class OfficialAccountTest
 *
 * @package Wechat\Tests
 */
class OfficialAccountTest extends TestCase
{
    /**
     * @var OfficialAccount
     */
    protected $officialAccount;

    protected function setUp()
    {
        $dotenv = new Dotenv(__DIR__ . '/../');
        $dotenv->load();

        $this->officialAccount = new OfficialAccount(
            getenv('OFFICIAL_ACCOUNT_APPID'),
            getenv('OFFICIAL_ACCOUNT_SECRET'),
            ['token' => getenv('OFFICIAL_ACCOUNT_TOKEN')]
        );
    }

    public function testToken()
    {
        $data = $this->officialAccount->token();
        $this->assertTrue(is_string($data));
    }
}