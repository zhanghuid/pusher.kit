# pusher.kit
    自己实验过的 ios 消息推送方案

## 前言
   关于设备环境要求的，请[查阅](https://github.com/zhanghuid/pusher.kit/wiki/Requirement)

## 运行环境
1. php 要求
```bash
❯ composer check-platform-reqs
Checking platform requirements for packages in the vendor dir
ext-curl       8.0.18    success
ext-json       8.0.18    success
ext-mbstring   *         success provided by symfony/polyfill-mbstring
ext-openssl    8.0.18    success
ext-pcre       8.0.18    success
ext-swoole     4.8.8     success
ext-tokenizer  8.0.18    success
php            8.0.18    success
```

## 命令行
```bash
php cli help apns

#eg:
# bark
php cli apns 'Hello ;)' 'xxxxxx229c0cc80d88a77ec599ab1433079653c2d8f8ce5b9b2ee4dbf3dd66e5'
# pushdeer
php cli apns 'Hello ;)' 'xxxxxx4CD03C319579A071A3BFB2249E8FD291CA3CCD2633ACBD2C8E9C274D3A'
```
