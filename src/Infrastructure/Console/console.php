<?php

require realpath(__DIR__ . '/../../../vendor/autoload.php');

// Carrega ENV e Container (mesma lógica do index.php)
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../../');
$dotenv->load();
$container = require __DIR__ . '/../Bootstrap/container.php';

// Simulação de roteamento CLI simples: php console.php detalhar 123456
$method = $argv[1] ?? null;
$nossoNumero = $argv[2] ?? null;

if ($method === 'detalhar' && $nossoNumero) {
    try {
        $command = $container->get(\AndrewsChiozo\ApiCobrancaBb\Infrastructure\Console\Commands\BoletoCommand::class);
        $output = $command->detalhar(['nossoNumero' => $nossoNumero]);
        
        echo "\n--- Detalhes do Boleto ---\n";
        print_r(json_decode($output, true));
        echo "\n";
    } catch (\Exception $e) {
        echo "Erro: " . $e->getMessage() . "\n";
    }
} else {
    echo "Uso: php console.php detalhar {nossoNumero}\n";
}