# pusher.kit
    自己实验过的 ios 消息推送方案

## IOS
## 环境要求
1. iphone 手机一台
2. 安装[gorush](https://github.com/appleboy/gorush)
3. 客户端
   - `appstore` 搜 `bark`
   - `appstore` 搜 `pushdeer 自架版`

## Bark 版
1. 证书准备: [p8](https://github.com/Finb/bark-server/releases/download/v1.0.2/AuthKey_LH4T9V5U4R_5U8LBRXG3A.p8)

2. 推送相关的配置
> 有效期到: 永久
```env
topic  = "me.fin.bark"
keyID  = "LH4T9V5U4R"
teamID = "5U8LBRXG3A"
```

3. deviceToken 获取
> 打开 bark -> 设置 -> 信 -> Device Token

4. 启动 gorush 服务
```bash
gorush -c ios.yml
```

5. 测试
```bash
curl -X POST \
    -H 'Content-Type: application/json' \
    -d '{"notifications":[{"tokens":["e5ca1e229c0cc80d88a7******************33079653c2d8f8ce5b9b2ee4dbf3dd66e5"],"platform":1,"message":"111","topic":"me.fin.bark","sound":{"volume":2}}]}' \
    http://127.0.0.1:8888/api/push
```

>[更多参考](https://day.app/2018/06/bark-server-document/)


## Pushdeer 版
1. 证书准备: [c.p12](https://github.com/easychen/pushdeer/blob/main/push/c.p12)

2. 推送相关的配置
> ⚠️ 自架服务器端需每年2月拉取一次更新推送证书
```bash
topic  = "com.pushdeer.self.ios"
keyID  = "66M7BD2GCV"
teamID = "HUJ6HAE4VU"
```
3. deviceToken 获取
- 在公网/局域网内，部署 [pushdeer 的 api项目](https://github.com/easychen/pushdeer/tree/main/api)
```bash
# 这里推荐使用 [phpbrew](https://github.com/phpbrew/phpbrew)
# 安装 `php-fpm` 命令如下
phpbrew -d install 8.1.2 +default +openssl +iconv +fileinfo +sqlite +xml +zip +mysql +mbstring +gd +curl +fpm
```
- 申请域名，配置 `nginx`
```nginx
server {
    listen 80 default_server;
    root /work/apps/pushdeer/api/public;
    index index.php index.html index.htm;
    server_name pushdeer.mykeep.fun;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        try_files $uri /index.php =404;
        fastcgi_split_path_info ^(.+\.php)(/.+)$;
        fastcgi_pass unix:/home/work/.phpbrew/php/php-8.1.2/var/run/php-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
    access_log  /var/log/nginx/pushdeer.access.log  main;
    error_log  /var/log/nginx/pushdeer.error.log  debug;
}
```
- 将可访问的域名配置到 `pushdeer` 上
- 在 `pushdeer` 客户端发送请求
- 服务端的 `db` 里的 `push_deer_devices` 表的 `device_id` 字段就是 deviceToken 了

4. 测试1
- 启动 gorush 服务
```bash
gorush -c ios.yml
```
- 测试
```bash
curl -X POST \
    -H 'Content-Type: application/json' \
    -d '{"notifications":[{"tokens":["e5ca1e229c0cc80d88a*******83c2d8f8ce5b9b2ee4dbf3dd66e5"],"platform":1,"message":"111","topic":"com.pushdeer.self.ios","sound":{"volume":2}}]}'
    http://127.0.0.1:8888/api/push
```

5. 测试2
```bash
gorush -ios -m "aaa" -i `find c.p12`  -t 'A9C7BA4CD03C319579A07********CCD2633ACBD2C8E9C274D3A' --password '64wtMhU4mULj' --topic 'com.pushdeer.self.ios' --production
```

>[查看更多](https://github.com/easychen/pushdeer)