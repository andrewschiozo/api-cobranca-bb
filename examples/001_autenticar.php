<?php

require __DIR__ . '/../vendor/autoload.php';

use AndrewsChiozo\ApiCobrancaBB\Application\CobrancaManagerFacade;
use AndrewsChiozo\ApiCobrancaBB\Application\DTO\AutenticarDTO;
use AndrewsChiozo\ApiCobrancaBB\Domain\Exceptions\BBApiException;

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
 * @var CobrancaManagerFacade
 */
$cobrancaManager = $container->get(CobrancaManagerFacade::class);

/**
 * Para obter os parâmetros:
 *
 * 1. crie uma conta no BB Developers
 * 2. crie uma aplicação e escolha a API de Cobranças
 * 3. gere suas credenciais
 * 4. armazene as credenciais em um lugar seguro
 */
$params = [
    'authUrl' => $container->get('bb.config')['authUrl'],
    'clientId' => $container->get('bb.config')['clientId'],
    'clientSecret' => $container->get('bb.config')['clientSecret'],
    'scope' => $container->get('bb.config')['scope']
];

try {
    
    $dto = AutenticarDTO::fromArray($params);
    $response = $cobrancaManager->autenticar($dto);
    echo 'Sucesso: ' . PHP_EOL . PHP_EOL;
    echo $response->accessToken . PHP_EOL;
    // echo '- - - - - - - - - - - - - - - - - - - - - - -' . PHP_EOL;
    // echo 'Auditoria: ' . PHP_EOL;
    // print_r($cobrancaManager->ultimaAuditoria());
    exit(0);
} catch (BBApiException $e) { // Erro no http client ou no error parser
    echo "Falha na autenticação: " . PHP_EOL;
    echo '- - - - - - - - - - - - - - - - - - - - - - -' . PHP_EOL;
    echo 'Auditoria: ' . PHP_EOL;
    print_r($e->getAuditoria());
    exit(1);
} catch (JsonException $e) { // Erro no response parser
    echo "Falha no parser do json: " . PHP_EOL;
    echo "Code: " . $e->getCode() . PHP_EOL;
    echo "Message: " . $e->getMessage() . PHP_EOL;
    echo "File " . $e->getFile();
    echo "Line: " . $e->getLine();
    exit(2);
} catch (Throwable $th) { // Erro não previsto
    echo "Falha no processo: " . PHP_EOL;
    echo "Code: " . $th->getCode() . PHP_EOL;
    echo "Message: " . $th->getMessage() . PHP_EOL;
    echo "File " . $th->getFile();
    echo "Line: " . $th->getLine();
    exit(2);
}
