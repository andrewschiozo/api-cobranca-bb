<?php


use AndrewsChiozo\ApiCobrancaBb\Application\CobrancaManagerFacade;
use AndrewsChiozo\ApiCobrancaBb\Application\UseCases\AutenticarUseCase;
use AndrewsChiozo\ApiCobrancaBb\Domain\Ports\HttpClientInterface;
use AndrewsChiozo\ApiCobrancaBb\Domain\Services\Formatters\AutenticarFormatter;
use AndrewsChiozo\ApiCobrancaBb\Domain\Services\Parsers\AutenticarResponseParser;
use AndrewsChiozo\ApiCobrancaBb\Domain\Services\Parsers\ErrorResponseParser;
use AndrewsChiozo\ApiCobrancaBb\Infrastructure\Adapters\BBHttpClientAdapter;
use AndrewsChiozo\ApiCobrancaBb\Infrastructure\Adapters\GuzzleHttpClientAdapter;
use AndrewsChiozo\ApiCobrancaBb\Infrastructure\Console\Commands\BoletoCommand;
use AndrewsChiozo\ApiCobrancaBb\Infrastructure\Http\Controllers\BoletoController;
use AndrewsChiozo\ApiCobrancaBb\Infrastructure\Logging\LoggerFactory;
use DI\ContainerBuilder;
use function DI\create;
use function DI\get;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use Psr\Log\LoggerInterface;

define('APP_ROOT', dirname(__DIR__, 3));

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

    ClientInterface::class => create(Client::class)->constructor(['base_uri' => $_ENV['BB_COBRANCA_URL_BASE'], 'verify' => false]),

    // Logger
    LoggerFactory::class => create()->constructor(APP_ROOT . '/storage/logs/'),
    LoggerInterface::class => new LoggerFactory(APP_ROOT . '/storage/logs/')->createLogger('bb-api'),

    AutenticarUseCase::class => create()
        ->constructor(
            get(GuzzleHttpClientAdapter::class),
            new AutenticarFormatter(),
            new AutenticarResponseParser(),
            get(LoggerInterface::class)
        ),

    BoletoController::class => create()
        ->constructor(
            get(CobrancaManagerFacade::class),
            $_ENV['BB_COBRANCA_CONVENIO']
        ),
    
    BoletoCommand::class => \DI\create()
        ->constructor(
            get(CobrancaManagerFacade::class),
            $_ENV['BB_COBRANCA_CONVENIO']
        ),
]);

return $builder->build();