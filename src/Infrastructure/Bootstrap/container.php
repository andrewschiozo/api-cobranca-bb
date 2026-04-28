<?php

use DI\ContainerBuilder;
use AndrewsChiozo\ApiCobrancaBb\Infrastructure\Adapters\GuzzleHttpClientAdapter;
use AndrewsChiozo\ApiCobrancaBb\Infrastructure\Adapters\FileTokenStorageAdapter;
use AndrewsChiozo\ApiCobrancaBb\Domain\Services\ErrorResponseParser;
use AndrewsChiozo\ApiCobrancaBb\Infrastructure\Logging\LoggerFactory;
use AndrewsChiozo\ApiCobrancaBb\Ports\HttpClientInterface;
use AndrewsChiozo\ApiCobrancaBb\Ports\TokenStorageInterface;
use Psr\Log\LoggerInterface;

use function DI\create;
use function DI\get;

$builder = new ContainerBuilder();

$builder->addDefinitions([
    // Configurações extraídas do ENV
    'bb.config' => [
        'baseUrl'      => $_ENV['BB_COBRANCA_URL_BASE'],
        'authUrl'      => $_ENV['BB_COBRANCA_URL_AUTH'],
        'clientId'     => $_ENV['BB_COBRANCA_CLIENT_ID'],
        'clientSecret' => $_ENV['BB_COBRANCA_CLIENT_SECRET'],
        'appKey'       => $_ENV['BB_COBRANCA_APP_KEY'],
    ],

    // GuzzleHttp Adapter
    GuzzleHttpClientAdapter::class => function ($container) {
        $config = $container->get('bb.config');
        return new GuzzleHttpClientAdapter(
            options: $config,
            errorParser: new ErrorResponseParser(),
            tokenStorage: $container->get(TokenStorageInterface::class),
            logger: $container->get(LoggerInterface::class)
        );
    },
    
    // Mapeamento de interfaces para classes concretas
    HttpClientInterface::class => get(GuzzleHttpClientAdapter::class),

    // TokenStorage
    TokenStorageInterface::class => create(FileTokenStorageAdapter::class)
        ->constructor('/storage/cache/bb_api_token.json'),

    // Logger
    LoggerFactory::class => create()->constructor('/storage/logs/'),
    LoggerInterface::class => new LoggerFactory('/storage/logs/')->createLogger('bb-api'),

    \AndrewsChiozo\ApiCobrancaBb\Infrastructure\Http\Controllers\BoletoController::class => create()
        ->constructor(
            get(\AndrewsChiozo\ApiCobrancaBb\Application\CobrancaManagerFacade::class),
            $_ENV['BB_COBRANCA_CONVENIO'] // Aqui a mágica acontece
        ),
    
    \AndrewsChiozo\ApiCobrancaBb\Infrastructure\Console\Commands\BoletoCommand::class => \DI\create()
        ->constructor(
            get(\AndrewsChiozo\ApiCobrancaBb\Application\CobrancaManagerFacade::class),
            $_ENV['BB_COBRANCA_CONVENIO']
        ),
]);

return $builder->build();