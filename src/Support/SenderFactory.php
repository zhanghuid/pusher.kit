<?php

declare(strict_types=1);

namespace Huid\Pusher\Support;

use Apple\ApnPush\Certificate\Certificate;
use Apple\ApnPush\Jwt\Jwt;
use Apple\ApnPush\Model\DeviceToken;
use Apple\ApnPush\Model\Notification;
use Apple\ApnPush\Model\Receiver;
use Apple\ApnPush\Protocol\Http\Authenticator\AuthenticatorInterface;
use Apple\ApnPush\Protocol\Http\Authenticator\CertificateAuthenticator;
use Apple\ApnPush\Protocol\Http\Authenticator\JwtAuthenticator;
use Apple\ApnPush\Sender\Builder\Http20Builder;
use Apple\ApnPush\Sender\SenderInterface;

class SenderFactory
{
    protected AuthenticatorInterface $authenticator;
    protected SenderInterface $builder;
    protected Receiver $receiver;
    protected Notification $notification;
    protected string $message;
    protected string $deviceToken;
    protected string $keyType;
    protected bool $sandbox;

    public function __construct(string $message, string $deviceToken, string $keyType, bool $sandbox)
    {
        $method = "create".ucfirst($keyType)."Authenticator";
        if (!method_exists($this, $method)) {
            throw new \RuntimeException("not implement method: {$keyType}");
        }

        $this->authenticator = $this->{$method}();
        $this->message = $message;
        $this->deviceToken = $deviceToken;
        $this->keyType = $keyType;
        $this->sandbox = $sandbox;
    }

    protected function createP8Authenticator(): JwtAuthenticator
    {
        $jwt = new Jwt(config('apns.team_id'), config('apns.key_id'), config('apns.key_path'));
        return new JwtAuthenticator($jwt);
    }

    protected function createP12Authenticator(): CertificateAuthenticator
    {
        $certificate = new Certificate(storage_path('c.p12.pem'), config('apns.password'));
        return new CertificateAuthenticator($certificate);
    }


    public static function create($args): SenderFactory
    {
        return new self(...$args);
    }

    public function withReceiver(): SenderFactory
    {
        $this->receiver = new Receiver(
            new DeviceToken($this->deviceToken),
            config('apns.topic')
        );

        return $this;
    }

    public function withNotification(): SenderFactory
    {
        $this->notification = Notification::createWithBody($this->message);
        return $this;
    }

    public function withBuilder(): SenderFactory
    {
        $this->builder = (new Http20Builder($this->authenticator))->build();
        return $this;
    }

    public function execute(): void
    {
        $this->builder->send($this->receiver, $this->notification, $this->sandbox);
    }

}
