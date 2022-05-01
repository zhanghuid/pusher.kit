<?php

declare(strict_types=1);

namespace Huid\Pusher\Support;

use Hyperf\Utils\Context;
use Psr\Http\Message\ResponseInterface;
use Hyperf\Contract\StdoutLoggerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Hyperf\HttpServer\Contract\RequestInterface;

/**
 * get Logger instance
 *
 * @return StdoutLoggerInterface
 */
function logger()
{
    return make(StdoutLoggerInterface::class);
}

/**
 * get Request instance
 *
 * @return \Hyperf\HttpServer\Request|mixed
 * @throws \Psr\Container\ContainerExceptionInterface
 * @throws \Psr\Container\NotFoundExceptionInterface
 */
function request()
{
    return make(RequestInterface::class);
}

/**
 * get Server Request instance
 *
 * @return \Hyperf\HttpMessage\Server\Request
 */
function serverRequest()
{
    return make(ServerRequestInterface::class);
}

/**
 * get or set Response header
 *
 * @param array $headers
 * @return ResponseInterface
 */
function response($headers = [])
{
    /** @var ResponseInterface  $response */
    $response = Context::get(ResponseInterface::class);

    if (!$headers) {
        return $response;
    }

    foreach ($headers as $key => $value) {
        $response = $response->withHeader($key, $value);
    }

    Context::set(ResponseInterface::class, $response);
    return $response;
}

/**
 * debug tool
 *
 * @param mixed $msg
 * @return void
 */
function record($msg)
{
    file_put_contents('./debug.log', date('Y-m-d H:i:s') . ': ' . var_export($msg, true) . PHP_EOL, FILE_APPEND);
}


/**
 * get config files
 *
 * @return array
 */
function get_config_files()
{
    $path = __DIR__ . '/../config';
    $iterator = new \FilesystemIterator($path);

    $ret = [];
    /** @var \SplFileInfo $filepath */
    foreach ($iterator as $splFileInfo) {
        $ret[$splFileInfo->getBasename('.php')] = $splFileInfo->getRealPath();
    }

    return $ret;
}

/**
 * load config content
 *
 * @param null $keyName
 * @return array|mixed|null
 */
function load_config($keyName = null)
{
    $files = get_config_files();
    $repository = new Config();
    foreach ($files as $key => $path) {
        $repository->offsetSet($key, require $path);
    }

    if ($keyName) {
        return $repository->offsetGet($keyName);
    }

    return $repository->all();
}


function storage_path($path = '')
{
    return __DIR__ . "/../Storage/{$path}";
}

