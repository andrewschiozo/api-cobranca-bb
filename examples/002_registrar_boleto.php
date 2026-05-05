<?php

use AndrewsChiozo\ApiCobrancaBb\Application\CobrancaManagerFacade;
use AndrewsChiozo\ApiCobrancaBb\Application\DTO\RegistrarBoletoDTO;

require __DIR__ . '/../vendor/autoload.php';

$container = require __DIR__ . '/../src/Infrastructure/Bootstrap/container.php';

/**
 * - O token é obtido na autenticação com client_id e client_secret
 * - O appKey é obtido no BB Developers
 */
$token = $container->get('bb.config')['token'];
$appKey = $container->get('bb.config')['appKey'];

/**
 * Parametros mínimos para registrar um boleto
 * 
 * - A data de vencimento foi preenchida com a data atual + 30 dias para evitar erro de negócio (registrar um boleto vencido)
 * - O nosso número deve ser preenchido com 10 caracteres e não pode ter sido usado antes
 * - O número do documento do pagador em sandbox/homologação deve ser um entre os que o BB disponibiliza
 */
$params = [
    'numeroConvenio' => $container->get('bb.config')['convenio'],
    'dataVencimento' => date("Y-m-d", strtotime("+30 days")),
    'valorTitulo' => '100.56',
    'nossoNumero' => date('ymdhi'),
    'pagadorNumeroDocumento' => '81676009000119',
    'pagadorCep' => '01035971'
];

try {
    $dto = RegistrarBoletoDTO::fromArray($params);

    $cobrancaManager = $container->get(CobrancaManagerFacade::class);

    $response = $cobrancaManager
        ->withAuth($token, $appKey)
        ->registrarCobranca($dto);

    /**
     * É pelo "número" que o boleto poderá ser consultado ou alterado.
     */
    echo 'Nosso número: ' . $params['nossoNumero'] . PHP_EOL;
    echo 'Linha digitável: ' . $response['linhaDigitavel'] . PHP_EOL;
    exit(0);
} catch (Throwable $th) {
    echo $th->getMessage();
}
