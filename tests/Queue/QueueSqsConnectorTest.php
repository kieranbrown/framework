<?php

namespace Illuminate\Tests\Queue;

use Aws\Credentials\CredentialProvider;
use Aws\Credentials\EcsCredentialProvider;
use Aws\Credentials\InstanceProfileProvider;
use Illuminate\Queue\Connectors\SqsConnector;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

class QueueSqsConnectorTest extends TestCase
{
    public function test_resolve_ecs_credential_provider_from_string()
    {
        $connector = new SqsConnector;
        $method = new ReflectionMethod($connector, 'resolveCredentialProvider');

        $provider = $method->invoke($connector, 'ecs');

        $this->assertIsCallable($provider);
    }

    public function test_resolve_instance_credential_provider_from_string()
    {
        $connector = new SqsConnector;
        $method = new ReflectionMethod($connector, 'resolveCredentialProvider');

        $provider = $method->invoke($connector, 'instance');

        $this->assertIsCallable($provider);
    }

    public function test_resolve_credential_provider_with_config()
    {
        $connector = new SqsConnector;
        $method = new ReflectionMethod($connector, 'resolveCredentialProvider');

        $provider = $method->invoke($connector, 'ecs', ['timeout' => 5, 'retries' => 1]);

        $this->assertIsCallable($provider);
    }

    public function test_resolve_invalid_credential_provider_throws_exception()
    {
        $connector = new SqsConnector;
        $method = new ReflectionMethod($connector, 'resolveCredentialProvider');

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid credential provider [invalid].');

        $method->invoke($connector, 'invalid');
    }
}
