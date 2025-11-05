<?php
declare(strict_types= 1);

namespace AndrewsChiozo\ApiCobrancaBb\Tests\Unit;

use AndrewsChiozo\ApiCobrancaBb\Domain\Enums\DocumentoTipoEnum;
use AndrewsChiozo\ApiCobrancaBb\Domain\Exceptions\DocumentoInvalidoException;
use AndrewsChiozo\ApiCobrancaBb\Domain\ValueObjects\DocumentoVO;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class DocumentoVOTest extends TestCase
{
    private const CPF_VALIDO = "713.118.490-09";
    private const CNPJ_VALIDO = "96.122.438/0001-11";

    #[Test]
    public function cpfValido(): void
    {
        $documento = new DocumentoVO(self::CPF_VALIDO);
        $this->assertEquals(self::CPF_VALIDO, $documento->valor);
        $this->assertEquals(DocumentoTipoEnum::CPF, $documento->tipo);
    }

    #[Test]
    public function cnpjValido(): void
    {
        $documento = new DocumentoVO(self::CNPJ_VALIDO);
        $this->assertEquals(self::CNPJ_VALIDO, $documento->valor);
        $this->assertEquals(DocumentoTipoEnum::CNPJ, $documento->tipo);
    }

    #[Test]
    #[\PHPUnit\Framework\Attributes\DataProvider('documentosInvalidosProvider')]
    public function documentosInvalidos(string $documentoInvalido): void
    {
        $this->expectException(DocumentoInvalidoException::class);
        new DocumentoVO($documentoInvalido);
    }

    public static function documentosInvalidosProvider(): array
    {
        return [
            'CPF com digito errado' => ['11111111111'],
            'CNPJ com digito errado' => ['99999999999999'],
            'String Vazia' => [''],
            'Apenas Espaços' => ['   '],
            'Curto demais' => ['12345'],
            'Formato inválido/misto' => ['ABC1-234']
        ];
    }

}