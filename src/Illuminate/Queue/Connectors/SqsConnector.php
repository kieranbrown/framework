<?php

namespace Illuminate\Queue\Connectors;

use Aws\Credentials\CredentialProvider;
use Aws\Sqs\SqsClient;
use Illuminate\Queue\SqsQueue;
use Illuminate\Support\Arr;
use InvalidArgumentException;

class SqsConnector implements ConnectorInterface
{
    /**
     * Establish a queue connection.
     *
     * @param  array  $config
     * @return \Illuminate\Contracts\Queue\Queue
     */
    public function connect(array $config)
    {
        $config = $this->getDefaultConfiguration($config);
        $config = $this->resolveCredentials($config);

        return new SqsQueue(
            new SqsClient(
                Arr::except($config, ['token'])
            ),
            $config['queue'],
            $config['prefix'] ?? '',
            $config['suffix'] ?? '',
            $config['after_commit'] ?? null
        );
    }

    /**
     * Resolve the credentials for the given config.
     *
     * @param  array  $config
     * @return array
     */
    protected function resolveCredentials(array $config)
    {
        $credentials = $config['credentials'] ?? null;

        if (is_string($credentials)) {
            $config['credentials'] = $this->resolveCredentialProvider($credentials);
        } elseif (is_array($credentials) && isset($credentials['provider'])) {
            $config['credentials'] = $this->resolveCredentialProvider(
                $credentials['provider'],
                Arr::except($credentials, ['provider'])
            );
        } elseif (! empty($config['key']) && ! empty($config['secret'])) {
            $config['credentials'] = Arr::only($config, ['key', 'secret']);

            if (! empty($config['token'])) {
                $config['credentials']['token'] = $config['token'];
            }
        }

        return $config;
    }

    /**
     * Resolve a credential provider by name.
     *
     * @param  string  $provider
     * @param  array  $config
     * @return callable
     *
     * @throws \InvalidArgumentException
     */
    protected function resolveCredentialProvider(string $provider, array $config = [])
    {
        return match ($provider) {
            'ecs' => CredentialProvider::ecsCredentials($config),
            'instance' => CredentialProvider::instanceProfile($config),
            default => throw new InvalidArgumentException(
                "Invalid credential provider [{$provider}]."
            ),
        };
    }

    /**
     * Get the default configuration for SQS.
     *
     * @param  array  $config
     * @return array
     */
    protected function getDefaultConfiguration(array $config)
    {
        return array_merge([
            'version' => 'latest',
            'http' => [
                'timeout' => 60,
                'connect_timeout' => 60,
            ],
        ], $config);
    }
}
