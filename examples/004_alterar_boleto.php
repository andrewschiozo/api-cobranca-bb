<?php

use AndrewsChiozo\ApiCobrancaBb\Application\CobrancaManagerFacade;
use AndrewsChiozo\ApiCobrancaBb\Application\DTO\AlterarBoletoDTO;
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
 * Parametros para alterar um boleto
 *
 * O BB não permite que você tente alterar um valor se o novo valor for igual ao valor atual
 * Ex: novo valor do título 155.22 / valor atual 155.22
 * Isso deve retornar um erro genérico: "Aconteceu um problema técnico"
 * 
 * Também não é permitido alterar um boleto assim que ele for registrado, você deve esperar
 * 30 minutos
 */
$params = [
    'numeroConvenio' => $container->get('bb.config')['convenio'],
    'nossoNumero' => '2605050545',
    // 'dataVencimento' => date("Y-m-d", strtotime("+15 days")),
    'valorTitulo' => '341.22',
];

try {
    $dto = AlterarBoletoDTO::fromArray($params);

    /**
     * @var CobrancaManagerFacade
     */
    $cobrancaManager = $container->get(CobrancaManagerFacade::class);

    $cobrancaManager = $cobrancaManager->withAuth($token, $appKey);
    $response = $cobrancaManager->alterarCobranca($dto);

    /**
     * É pelo "número" que o boleto poderá ser consultado ou alterado.
     * Na alteração, o BB só devolve a data/hora de atualização e número do contrato de cobrança
     */
    echo 'Nosso número: '     . $params['nossoNumero'] . PHP_EOL;
    echo 'Número contrato: '  . $response['numeroContratoCobranca'] . PHP_EOL;
    echo 'Data Atualização: ' . $response['dataAtualizacao'] . PHP_EOL;
    echo 'Hora Atualização: ' . $response['horarioAtualizacao'] . PHP_EOL;
    exit(0);

} catch (BBApiException $e) { // Erro no http client ou no error parser
    echo "Falha na alteração: " . PHP_EOL;
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