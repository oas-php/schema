<?php declare(strict_types=1);

use OAS\Schema;
use OAS\Schema\Factory;
use PHPUnit\Framework\TestCase;

class FactoryTest extends TestCase
{
    private Factory $factory;

    /**
     * @test
     * @coversNothing
     */
    public function isInstantiableFromPrimitiveTypes(): void
    {
        $schema = $this->factory->createFromPrimitives([
            'anyOf' => [
                [
                    'type' => 'string'
                ],
                [
                    'type' => 'integer'
                ],
                true
            ],
            'not' => [
                'type' => 'string'
            ]
        ]);

        self::assertInstanceOf(Schema::class, $schema);
        self::assertInstanceOf(Schema::class, $schema->getNot());

        foreach ($schema->getAnyOf() as $anyOfSchema) {
            $this->assertInstanceOf(Schema::class, $anyOfSchema);
        }
    }

    public function setUp(): void
    {
        $this->factory = new Factory();
    }
}