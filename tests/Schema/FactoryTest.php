<?php declare(strict_types=1);

use OAS\Resolver\Resolver;
use OAS\Schema;
use OAS\Schema\Factory;
use PHPUnit\Framework\TestCase;

class FactoryTest extends TestCase
{
    /**
     * @test
     * @covers \OAS\Schema\Factory
     * @dataProvider provider
     */
    public function schemaIsCreatedFromPrimitives(bool|array|stdClass $primitives, bool|Schema $expectedSchema): void
    {
        $factory = new Factory();

        self::assertEquals($factory->createFromPrimitives($primitives), $expectedSchema);
    }

    /**
     * @test
     * @covers \OAS\Schema\Factory
     * @dataProvider provider
     */
    public function schemaIsCreatedFromPrimitivesResolvingReferences(): void
    {
        $factory = new Factory(new Resolver());

        //self::assertEquals($factory->createFromUri(), $expectedSchema);
    }

    /**
     * @return iterable<array{bool|array|stdClass, bool|Schema}>
     */
    public static function provider(): iterable
    {
        yield [
            true,
            true
        ];

        yield [
            new stdClass(),
            new Schema()]
        ;

        yield [
            [],
            new Schema()
        ];

        yield [
            ['type' => 'integer'],
            new Schema(type: Schema\Type::INTEGER)
        ];

        yield [
            [
                'type' => 'array',
                'items' => [
                    'anyOf' => [
                        ['type' => 'integer'],
                        ['$ref' => '#/'],
                    ]
                ]
            ],
            new Schema(
                items: new Schema(
                    anyOf: [
                        new Schema(type: Schema\Type::INTEGER),
                        new Schema(_ref: '#/')
                    ]
                ),
                type: Schema\Type::ARRAY
            )
        ];
    }
}