<?php

use AndrewsChiozo\ApiCobrancaBb\Domain\Ports\HttpClientInterface;
use AndrewsChiozo\ApiCobrancaBb\Domain\Services\Parsers\ErrorResponseParser;
use AndrewsChiozo\ApiCobrancaBb\Infrastructure\Adapters\BBHttpClientAdapter;
use AndrewsChiozo\ApiCobrancaBb\Infrastructure\Adapters\GuzzleHttpClientAdapter;
use AndrewsChiozo\ApiCobrancaBb\Infrastructure\Logging\LoggerFactory;
use DI\ContainerBuilder;
use function DI\get;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use Psr\Log\LoggerInterface;

define('APP_ROOT', dirname(__DIR__, 3));

if (file_exists(APP_ROOT . '/.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable(APP_ROOT);
    $dotenv->load();
}

$builder = new ContainerBuilder();

$builder->addDefinitions([
    // dados expostos pela documentação do BB
    'bb.config' => [
        'baseUrl'      => $_ENV['BB_COBRANCA_URL_BASE']           ?? '',
        'authUrl'      => $_ENV['BB_COBRANCA_URL_AUTH']           ?? '',
        'clientId'     => $_ENV['BB_COBRANCA_CLIENT_ID']          ?? '',
        'clientSecret' => $_ENV['BB_COBRANCA_CLIENT_SECRET']      ?? '',
        'scope'        => $_ENV['BB_COBRANCA_SCOPE']              ?? '',
        'appKey'       => $_ENV['BB_COBRANCA_APP_KEY']            ?? '',
        'appKeyLiq'    => $_ENV['BB_COBRANCA_APP_KEY_LIQUIDACAO'] ?? '',
        'token'        => $_ENV['BB_COBRANCA_TOKEN']              ?? '',
        'convenio'     => $_ENV['BB_COBRANCA_CONVENIO']           ?? ''
    ],

    // GuzzleHttpClientAdapter
    GuzzleHttpClientAdapter::class => function ($container) {
        return new GuzzleHttpClientAdapter(
            logger: $container->get(LoggerInterface::class),
            client: $container->get(ClientInterface::class)
        );
    },

    // BBHttpClientAdapter
    BBHttpClientAdapter::class => function ($container) {
        return new BBHttpClientAdapter(
            errorParser: new ErrorResponseParser(),
            client: $container->get(GuzzleHttpClientAdapter::class)
        );
    },
    
    // Mapeamento de interfaces para classes concretas
    HttpClientInterface::class => get(BBHttpClientAdapter::class),

    ClientInterface::class => function ($container) {
        return new Client([
            'base_uri' => $container->get('bb.config')['baseUrl'],
            'verify'   => false
        ]);
    },

    // Logger
    LoggerInterface::class => new LoggerFactory(APP_ROOT . '/storage/logs/')->createLogger('bb-api')
]);

return $builder->build();