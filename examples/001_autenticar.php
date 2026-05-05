<?php

use AndrewsChiozo\ApiCobrancaBb\Application\CobrancaManagerFacade;
use AndrewsChiozo\ApiCobrancaBb\Application\DTO\AutenticarDTO;

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
    $cobrancaManager = $container->get(CobrancaManagerFacade::class);    
    $dto = AutenticarDTO::fromArray($params);
    $response = $cobrancaManager->autenticar($dto);
    
    echo $response->accessToken . PHP_EOL;
    exit(0);
} catch (Throwable $th) {
    echo $th->getMessage();
}
