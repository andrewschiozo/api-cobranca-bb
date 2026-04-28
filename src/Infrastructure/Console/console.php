<?php

use AndrewsChiozo\ApiCobrancaBb\Infrastructure\Console\Commands\BoletoCommand;

require realpath(__DIR__ . '/../../../vendor/autoload.php');

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../../');
$dotenv->load();
$container = require __DIR__ . '/../Bootstrap/container.php';

$method = $argv[1] ?? null;

if ($method === 'detalhar') {
    $nossoNumero = $argv[2] ?? null;
    try {
        $command = $container->get(BoletoCommand::class);
        $output = $command->detalhar(['nossoNumero' => $nossoNumero]);
        
        echo "\n--- Detalhes do Boleto ---\n";
        print_r(json_decode($output, true));
        echo "\n";
        exit(0);
    } catch (\Exception $e) {
        echo "Erro: " . $e->getMessage() . "\n";
        exit(1);
    }
}

if ($method === 'autenticar') {
    try {
        $command = $container->get(BoletoCommand::class);

        $output = $command->autenticar([
            'authUrl' => $_ENV['BB_COBRANCA_URL_AUTH'],
            'clientId' => $_ENV['BB_COBRANCA_CLIENT_ID'],
            'clientSecret' => $_ENV['BB_COBRANCA_CLIENT_SECRET'],
            'scope' => $_ENV['BB_COBRANCA_SCOPE']
        ]);

        echo "\n--- Dados de Autenticação ---\n";
        print_r(json_decode($output, true));
        echo "\n";
        exit(0);
    } catch (\Exception $e) {
        echo "Erro: " . $e->getMessage() . "\n";
        exit(1);
    }
}

echo "Uso: php console.php detalhar {nossoNumero}\n";