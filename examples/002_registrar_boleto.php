<?php

use AndrewsChiozo\ApiCobrancaBb\Application\CobrancaManagerFacade;
use AndrewsChiozo\ApiCobrancaBb\Application\DTO\RegistrarBoletoDTO;
use AndrewsChiozo\ApiCobrancaBb\Domain\Exceptions\BBApiException;

require __DIR__ . '/../vendor/autoload.php';

/**
 * O container é um facilitador, ele e entrega os CobrancaManagerFacade pronto para uso.
 * Você deve ajustar as configurações de ambiente(client_id, client_secret) no container
 * para que os exemplos funcionem.
 * 
 * Você não depende do container oferecido, você pode criar o seu próprio container ou
 * configurar o container existente no seu projeto
 */
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
    'nossoNumero' => date('ymdhs'),
    'pagadorNumeroDocumento' => '81676009000119',
    'pagadorCep' => '01035971',
    // 'dataEmissao' => date("Y-m-d", strtotime("-8 days")), // opcional
    // 'valorAbatimento' => '10.50', // opcional
    // 'numeroTituloBeneficiario' => 'ID-001ABC-def', // opcional
];

try {
    /**
     * @var CobrancaManagerFacade
     */
    $cobrancaManager = $container->get(CobrancaManagerFacade::class);
    $dto = RegistrarBoletoDTO::fromArray($params);

    $cobrancaManager = $cobrancaManager->withAuth($token, $appKey);
    $response = $cobrancaManager->registrarCobranca($dto);
    echo 'Sucesso: ' . PHP_EOL . PHP_EOL;
    print_r($response);
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
