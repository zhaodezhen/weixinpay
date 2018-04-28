<?php

use Wxpay\Core\Response;

class Wxpay {

    //绑定商户的APPID
    const APPID = '';
    //商户号
    const MCHID = '';
    // 商户订单号，需保持唯一性
    const ORDER_SN = 2018050142314;
    //openid 需绑定对应商户号
    const OPENID = 'ozTDh0xbZuRXBvG6FpDSQkJeR';
    //金额单位为分
    const AMOUNT ='100';
    //描述 必填项
    const DESC = '提现';
    //商户秘钥
    const KEY = '';
    public function puts(){

        $body=[
            'mch_appid'=>static::APPID,
            'mchid'=>static::MCHID,
//            'device_info'=>'',      //设备号
            'nonce_str'=>static::ORDER_SN,        //随机字符串
            'partner_trade_no'=>static::ORDER_SN,
            'openid'=>static::OPENID,
            'check_name'=>'NO_CHECK',   //不验证真实姓名
            'amount'=> static::AMOUNT,
            'desc'=>static::DESC      ,
            'spbill_create_ip'=>http_client_ip()
        ];
        $arr=$body;
        $sign = $this->makeSign($body);
        $arr['sign']=$sign;
        $xml = $this->arrayToXml($arr);
//        var_dump($xml);exit;

        $url = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/promotion/transfers';
        $re = $this->curl_post_ssl($url,$xml);
        $response = $this->xmlToArray($re);
        return response(static::SUCCESS, $response);

    }
    public function makeSign($data){
        //获取微信支付秘钥
        $key = static::KEY;
        // 去空
        $data=array_filter($data);
        //签名步骤一：按字典序排序参数
        ksort($data);
        $string_a=http_build_query($data);
        $string_a=urldecode($string_a);
        //签名步骤二：在string后加入KEY
        //$config=$this->config;
        $string_sign_temp=$string_a."&key=".$key;
        //签名步骤三：MD5加密
        $sign = md5($string_sign_temp);
        // 签名步骤四：所有字符转为大写
        $result=strtoupper($sign);
        return $result;
    }
    function arrayToXml($arr)
    {
        $xml = "<xml>";
        foreach ($arr as $key=>$val)
        {
            if (is_numeric($val)){
                $xml.="<".$key.">".$val."</".$key.">";
            }else{
                $xml.="<".$key."><![CDATA[".$val."]]></".$key.">";
            }
        }
        $xml.="</xml>";
        return $xml;
    }
    function xmlToArray($xml)
    {
        //禁止引用外部xml实体
        libxml_disable_entity_loader(true);
        $values = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        return $values;
    }
    public function curl_post_ssl($url, $xmldata, $second=30,$aHeader=array()){
        $ch = curl_init();
        //超时时间
        curl_setopt($ch,CURLOPT_TIMEOUT,$second);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);
        //这里设置代理，如果有的话
        //curl_setopt($ch,CURLOPT_PROXY, '10.206.30.98');
        //curl_setopt($ch,CURLOPT_PROXYPORT, 8080);
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,false);

        //以下两种方式需选择一种

        //第一种方法，cert 与 key 分别属于两个.pem文件
        //默认格式为PEM，可以注释
        curl_setopt($ch,CURLOPT_SSLCERTTYPE,'PEM');
        curl_setopt($ch,CURLOPT_SSLCERT,'apiclient_cert.pem');
        //默认格式为PEM，可以注释
        curl_setopt($ch,CURLOPT_SSLKEYTYPE,'PEM');
        curl_setopt($ch,CURLOPT_SSLKEY,'apiclient_key.pem');

        //第二种方式，两个文件合成一个.pem文件
        //curl_setopt($ch,CURLOPT_SSLCERT,getcwd().'/all.pem');

        if( count($aHeader) >= 1 ){
            curl_setopt($ch, CURLOPT_HTTPHEADER, $aHeader);
        }

        curl_setopt($ch,CURLOPT_POST, 1);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$xmldata);
        $data = curl_exec($ch);
        if($data){
            curl_close($ch);
            return $data;
        }
        else {
            $error = curl_errno($ch);
            echo "call faild, errorCode:$error\n";
            curl_close($ch);
            return false;
        }
    }

}
