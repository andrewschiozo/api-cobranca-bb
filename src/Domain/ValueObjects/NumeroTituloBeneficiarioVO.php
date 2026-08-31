<?php

declare(strict_types=1);

namespace AndrewsChiozo\ApiCobrancaBB\Domain\ValueObjects;

use AndrewsChiozo\ApiCobrancaBB\Domain\Exceptions\NumeroTituloBeneficiarioInvalidoException;
use InvalidArgumentException;

readonly class NumeroTituloBeneficiarioVO
{
    public string $numeroTitulo;

    /**
     * Campo utilizado para identificar o título do beneficiário.
     * Converte automaticamente caracteres minúsculos para maiúsculos.
     * 
     * Critérios:
     * - caracteres alfanuméricos: A a Z, 0 a 9;
     * - caracteres especiais de conjunção: hifen (-),apostrofo (');
     * - separador de palavras: branco ( );
     * - tamanho máximo: 15 caracteres.
     * 
     * Exemplos: D'EL-REI, D'ALCORTIVO, SANT'ANA, O001-ABC-DEF-01
     * @param string $numeroTitulo
     * @throws InvalidArgumentException
     */
    public function __construct(string $numeroTitulo)
    {
        $numeroTituloFormatado = mb_strtoupper($numeroTitulo);

        if (!preg_match('/^[A-Z0-9\'\- ]{1,15}$/', $numeroTituloFormatado)) {
            $msg = <<<EOT
                Número do título do beneficiário inválido.
                Deve conter apenas caracteres alfanuméricos, hifen (-), apostrofo (') e espaço.
                Tamanho máximo de 15 caracteres.
            EOT;
            throw new NumeroTituloBeneficiarioInvalidoException($msg);
        }

        $this->numeroTitulo = $numeroTituloFormatado;
    }
}
