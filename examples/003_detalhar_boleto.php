<?php

use AndrewsChiozo\ApiCobrancaBb\Application\CobrancaManagerFacade;
use AndrewsChiozo\ApiCobrancaBb\Application\DTO\DetalharBoletoDTO;
use AndrewsChiozo\ApiCobrancaBb\Domain\Exceptions\BBApiException;

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

    $cobrancaManager = $cobrancaManager->withAuth($token, $appKey);
    $response = $cobrancaManager->detalharCobranca($dto);

    echo 'Nosso número: ' . $params['nossoNumero'] . PHP_EOL;
    echo 'Vencimento: ' . $response['dataVencimentoTituloCobranca'] . PHP_EOL;
    echo 'Valor Original: ' . $response['valorOriginalTituloCobranca'] . PHP_EOL;
    echo 'Valor Atual: ' . $response['valorAtualTituloCobranca'] . PHP_EOL;
    echo 'Linha digitável: ' . $response['codigoLinhaDigitavel'] . PHP_EOL;
    exit(0);
} catch (BBApiException $e) { // Erro no http client ou no error parser
    echo "Falha no registro: " . PHP_EOL;
    echo '- - - - - - - - - - - - - - - - - - - - - - -' . PHP_EOL;
    echo 'Auditoria: ' . PHP_EOL;
    print_r($e->getAuditoria()->response);
    exit(1);
} catch (JsonException $e) { // Erro no response parser
    echo "Falha no parser do json: " . PHP_EOL;
    echo "Code: " . $e->getCode() . PHP_EOL;
    echo "Message: " . $e->getMessage() . PHP_EOL;
    echo "File " . $e->getFile() . PHP_EOL;
    echo "Line: " . $e->getLine() . PHP_EOL;
    exit(2);
} catch (Throwable $th) { // Erro não previsto
    echo "Falha no processo: " . PHP_EOL;
    echo "Code: " . $th->getCode() . PHP_EOL;
    echo "Message: " . $th->getMessage() . PHP_EOL;
    echo "File " . $th->getFile() . PHP_EOL;
    echo "Line: " . $th->getLine() . PHP_EOL;
    exit(3);
}
