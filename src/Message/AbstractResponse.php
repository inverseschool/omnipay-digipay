<?php

/**
 * @package Omnipay\Digipay
 * @author Amirreza Salari <amirrezasalari1997@gmail.com>
 */

namespace Omnipay\Digipay\Message;

/**
 * Class AbstractResponse
 */
abstract class AbstractResponse extends \Omnipay\Common\Message\AbstractResponse
{
    /**
     * @var array
     */
    protected $errorCodes = [
        0 => 'عملیات با موفیت انجام شد',
        1 => 'عملیات ناموفق بود',
        2 => 'عدم وضعیت مشخص',
        15805 => 'سرویسی برای این شماره همراه یافت نشد',
        1054 => 'اطلاعات ورودی اشتباه میباشد',
        9000 => 'اطلاعات خرید یافت نشد',
        9001 => 'توکن پرداخت معتبر نمیباشد',
        9003 => 'خرید مورد نظر منقضی شده است',
        9004 => 'خرید مورد نظر در حال انجام است',
        9005 => 'خرید قابل پرداخت نمیباشد',
        9006 => 'خطا در برقراری ارتباط با درگاه پرداخت',
        9007 => 'خرید با موفقیت انجام نشده است',
        9008 => 'این خرید با داده های متفاوتی قبلا ثبت شده است',
        9009 => 'محدوده زمانی تایید تراکنش گذشته است',
        9010 => 'تایید خرید ناموفق بود',
        9011 => 'نتیجه تایید خرید نامشخص است',
        9012 => 'وضعیت خرید برای این درخواست صحیح نمیباشد',
        9030 => 'ورود شماره همراه برای کاربران ثبت نام شده الزامی است',
        9031 => 'اعطای تیکت برای کاربر مورد نظرامکان پذیر نمیباشد',
    ];

    /**
     * @var array
     */
    protected array $paymentGatewayTypesCodes = [
        0 => 'IPG',
        3 => 'WALLET',
        4 => 'CPG'
    ];

    /**
     * Response Message
     *
     * @return null|string A response message from the payment gateway
     */
    public function getMessage()
    {
        return isset($this->errorCodes[$this->getCode()]) ? $this->errorCodes[$this->getCode()] : $this->data['result']['message'];
    }

    /**
     * Response code
     *
     * @return null|string A response code from the payment gateway
     */
    public function getCode()
    {
        return (int)$this->data['result']['status'] ?? parent::getCode();
    }

    /**
     * Http status code
     *
     * @return int A response code from the payment gateway
     */
    public function getHttpStatus(): int
    {
        return (int)($this->data['httpStatus'] ?? null);
    }
}
