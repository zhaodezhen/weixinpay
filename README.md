## 微信红包、企业付款
**注意：与商户微信支付收款资金并非同一账户，需要单独充值。企业付款需要证书。**

说到代码实现，又不得不吐槽一下 [官方文档](https://pay.weixin.qq.com/wiki/doc/api/tools/mch_pay.php?chapter=14_2) 和SDK  [下载](https://pay.weixin.qq.com/wiki/doc/api/download/cert.zip)了，基本跟没有差不多，全靠程序猿自己摸索然后进行代码实现。

所以我将完整的代码封装成了一个类，可以直接引入项目更改一下配置参数就可以使用的

说一下我在开发是遇到的问题以供大家参考。

1.curl错误码58,原因我用的是docker挂载的环境,需要环境里的绝对路径php可以用realpath()函数获取。
