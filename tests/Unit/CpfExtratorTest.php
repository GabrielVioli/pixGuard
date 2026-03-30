<?php

namespace Tests\Unit;

use App\Services\Api\CpfHub\CpfExtrator;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class CpfExtratorTest extends TestCase
{
    public function test_returns_fixture_for_test_cpf(): void
    {
        $service = new CpfExtrator();

        $result = $service->extract('000.000.000-00');

        $this->assertSame('00000000000', $result['cpf']);
        $this->assertSame('USUARIO TESTE (SEM CUSTO)', $result['nome']);
        $this->assertSame('REGULAR', $result['situacao']);
        $this->assertSame('2000-01-01', $result['nascimento']);
    }

    public function test_converts_birthdate_format_from_api(): void
    {
        Http::fake([
            'https://api.cpfhub.io/cpf/*' => Http::response([
                'data' => [
                    'name' => 'Maria Teste',
                    'situation' => 'regular',
                    'birthDate' => '31/12/1999',
                ],
            ], 200),
        ]);

        $service = new CpfExtrator();
        $result = $service->extract('529.982.247-25');

        $this->assertSame('52998224725', $result['cpf']);
        $this->assertSame('Maria Teste', $result['nome']);
        $this->assertSame('REGULAR', $result['situacao']);
        $this->assertSame('1999-12-31', $result['nascimento']);
    }

    public function test_returns_null_on_failed_response(): void
    {
        Http::fake([
            'https://api.cpfhub.io/cpf/*' => Http::response([], 500),
        ]);

        $service = new CpfExtrator();
        $result = $service->extract('529.982.247-25');

        $this->assertNull($result);
    }
}
