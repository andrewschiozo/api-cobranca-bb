<?php

use AndrewsChiozo\ApiCobrancaBb\Application\CobrancaManagerFacade;
use AndrewsChiozo\ApiCobrancaBb\Application\DTO\DetalharBoletoDTO;

require __DIR__ . '/../vendor/autoload.php';

$container = require __DIR__ . '/../src/Infrastructure/Bootstrap/container.php';

/**
 * - O token é obtido na autenticação com client_id e client_secret
 * - O appKey é obtido no BB Developers
 */
$token = $container->get('bb.config')['token'];
$appKey = $container->get('bb.config')['appKey'];

/**
 * Parametros para detalhar um boleto
 * 
 * - O nosso número deve ser o mesmo usado para registrar um boleto
 */
$params = [
    'numeroConvenio' => $container->get('bb.config')['convenio'],
    'nossoNumero' => '2605050545'
];

try {
    $dto = DetalharBoletoDTO::fromArray($params);

    $cobrancaManager = $container->get(CobrancaManagerFacade::class);

    $response = $cobrancaManager
        ->withAuth($token, $appKey)
        ->detalharCobranca($dto);

    echo 'Nosso número: ' . $params['nossoNumero'] . PHP_EOL;
    echo 'Vencimento: ' . $response['dataVencimentoTituloCobranca'] . PHP_EOL;
    echo 'Valor Original: ' . $response['valorOriginalTituloCobranca'] . PHP_EOL;
    echo 'Valor Atual: ' . $response['valorAtualTituloCobranca'] . PHP_EOL;
    echo 'Linha digitável: ' . $response['codigoLinhaDigitavel'] . PHP_EOL;
    exit(0);
} catch (Throwable $th) {
    echo $th->getMessage();
}
