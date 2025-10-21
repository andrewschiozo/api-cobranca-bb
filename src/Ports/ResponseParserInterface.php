<?php
declare(strict_types= 1);

namespace AndrewsChiozo\ApiCobrancaBb\Ports;

/**
 * Interface que define o contrato para a formatação dos dados 
 * devolvidos pela API após uma requisição.
 */
interface ResponseParserInterface
{
    /**
     * Formata os dados recebidos da API para o formato da aplicação..
     * 
     * @param string $data Dados a serem formatados
     * @return array Dados formatados
     */
    public function parse(string $data): array;
}