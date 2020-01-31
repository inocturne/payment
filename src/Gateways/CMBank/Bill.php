<?php

/*
 * The file is part of the payment lib.
 *
 * (c) Leo <dayugog@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Payment\Gateways\CMBank;

use Payment\Contracts\IGatewayRequest;
use Payment\Exceptions\GatewayException;

/**
 * @package Payment\Gateways\CMBank
 * @author  : Leo
 * @email   : dayugog@gmail.com
 * @date    : 2019/11/27 7:03 PM
 * @version : 1.0.0
 * @desc    : 对账文件下载
 **/
class Bill extends CMBaseObject implements IGatewayRequest
{
    const ONLINE_METHOD = 'https://payment.ebank.cmbchina.com/NetPayment/BaseHttp.dll?GetDownloadURL';

    const SANDBOX_METHOD = 'http://121.15.180.66:801/NetPayment_dl/BaseHttp.dll?GetDownloadURL';

    /**
     * 获取第三方返回结果
     * @param array $requestParams
     * @return mixed
     * @throws GatewayException
     */
    public function request(array $requestParams)
    {
        // 初始 网关地址
        $this->setGatewayUrl(self::ONLINE_METHOD);
        if ($this->isSandbox) {
            $this->setGatewayUrl(self::SANDBOX_METHOD);
        }
    }

    /**
     * @param array $requestParams
     * @return mixed
     */
    protected function getRequestParams(array $requestParams)
    {
        $nowTime  = time();
        $billData = $requestParams['bill_date'] ?? strtotime('-1 days');
        $billData = date('Ymd', $billData);

        $params = [
            'dateTime'     => date('YmdHis', $nowTime),
            'branchNo'     => self::$config->get('branch_no', ''),
            'merchantNo'   => self::$config->get('mch_id', ''),
            'date'         => $billData,
            'transactType' => '4001',
            'fileType'     => 'YBL',
            'messageKey'   => $requestParams['message_key'] ?? '', // 交易流水，合作方内部唯一流水
        ];

        return $params;
    }
}
