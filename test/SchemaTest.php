<?php declare(strict_types=1);

use OAS\Schema;
use OAS\Schema\Factory\Factory;
use PHPUnit\Framework\TestCase;

class SchemaTest extends TestCase
{
    private Factory $factory;

    /** @test */
    public function isInstantiable(): void
    {
        $this->assertInstanceOf(Schema::class, new Schema());
    }

    /** @test */
    public function isInstantiableFromArray(): void
    {
        $this->assertInstanceOf(Schema::class, Schema::createFromArray([]));
    }

    /** @test */
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

        $this->assertInstanceOf(Schema::class, $schema);
        $this->assertInstanceOf(Schema::class, $schema->getNot());

        foreach ($schema->getAnyOf() as $anyOfSchema) {
            $this->assertInstanceOf(Schema::class, $anyOfSchema);
        }
    }

    /** @test */
    public function arrayTypeIsConstructed(): void
    {
        // instantiate using dedicated constructor
        $schema = Schema::createArrayType();
        $this->assertInstanceOf(Schema::class, $schema);
        $this->assertEquals(Schema::TYPE_ARRAY, $schema->getType());
        // by the fault additional items are allowed
        $this->assertNull($schema->getAdditionalItems());
        $this->assertFalse($schema->hasMinItems());
        $this->assertNull($schema->getMinItems());
        $this->assertFalse($schema->hasMinItems());
        $this->assertNull($schema->getMaxItems());
        $this->assertFalse($schema->hasItems());
        $this->assertNull($schema->getItems());
        $this->assertFalse($schema->hasAdditionalItems());
        $this->assertNull($schema->getAdditionalItems());

        // items keyword
        $schema = $this->factory->createFromPrimitives(
            [
                'items' => [
                    'type' => 'string'
                ]
            ]
        );
        $this->assertInstanceOf(Schema::class, $schema->getItems());
        $this->assertFalse($schema->isTuple());

        // items keyword: tuple
        $schema = $this->factory->createFromPrimitives(
            [
                'items' => [
                    [
                        'type' => 'string'
                    ],
                    [
                        'type' => 'integer'
                    ]
                ]
            ]
        );
        $this->assertIsArray($schema->getItems());
        $this->assertTrue($schema->isTuple());
        $this->assertCount(2, $schema->getItems());
        $this->assertInstanceOf(Schema::class, $schema->getItems()[0]);
        $this->assertEquals(Schema::TYPE_STRING, $schema->getItems()[0]->getType());
        $this->assertInstanceOf(Schema::class, $schema->getItems()[1]);
        $this->assertEquals(Schema::TYPE_INTEGER, $schema->getItems()[1]->getType());

        // items & additionalItems keywords: additionalItems is boolean (createFromPrimitives)
        $schema = $this->factory->createFromPrimitives(
            [
                'items' => [
                    [
                        'type' => 'string'
                    ]
                ],
                'additionalItems' => true
            ]
        );
        $this->assertIsArray($schema->getItems());
        $this->assertTrue($schema->getAdditionalItems()->isAlwaysValid());

        // items & additionalItems keywords: additionalItems is always valid schema
        $schema = Schema::createFromArray(
            [
                'items' => [
                    Schema::createNumberType()
                ],
                'additionalItems' => Schema::createBooleanSchema(true)
            ]
        );
        $this->assertIsArray($schema->getItems());
        $this->assertInstanceOf(Schema::class, $schema->getAdditionalItems());

        // items & additionalItems keywords: additionalItems is always invalid schema
        $schema = Schema::createFromArray(
            [
                'items' => [
                    Schema::createNumberType()
                ],
                'additionalItems' => Schema::createBooleanSchema(false)
            ]
        );
        $this->assertIsArray($schema->getItems());
        $this->assertInstanceOf(Schema::class, $schema->getAdditionalItems());
    }

    /** @test */
    public function stringTypeIsConstructedByFactoryMethod(): void
    {
        $stringTypeDefault = Schema::createStringType();
        $this->assertInstanceOf(Schema::class, $stringTypeDefault);
        $this->assertTrue($stringTypeDefault->hasType());
        $this->assertEquals(Schema::TYPE_STRING, $stringTypeDefault->getType());
        $this->assertFalse($stringTypeDefault->hasMinLength());
        $this->assertNull($stringTypeDefault->getMinLength());
        $this->assertFalse($stringTypeDefault->hasMaxLength());
        $this->assertNull($stringTypeDefault->getMaxLength());
        $this->assertFalse($stringTypeDefault->hasFormat());
        $this->assertNull($stringTypeDefault->getFormat());
        $this->assertFalse($stringTypeDefault->hasPattern());
        $this->assertNull($stringTypeDefault->getPattern());

        $stringType = Schema::createStringType(
            $minLength = 8,
            $maxLength = 64,
            $format ='email',
            $pattern = '^\S+@mail.com'
        );
        $this->assertInstanceOf(Schema::class, $stringType);
        $this->assertTrue($stringType->hasType());
        $this->assertEquals(Schema::TYPE_STRING, $stringType->getType());
        $this->assertTrue($stringType->hasMinLength());
        $this->assertEquals($minLength, $stringType->getMinLength());
        $this->assertTrue($stringType->hasMaxLength());
        $this->assertEquals($maxLength, $stringType->getMaxLength());
        $this->assertTrue($stringType->hasFormat());
        $this->assertEquals($format, $stringType->getFormat());
        $this->assertTrue($stringType->hasPattern());
        $this->assertEquals($pattern, $stringType->getPattern());
    }

    /** @test */
    public function integerTypeIsConstructedByFactoryMethod(): void
    {
        $integerTypeDefault = Schema::createIntegerType();
        $this->assertInstanceOf(Schema::class, $integerTypeDefault);
        $this->assertTrue($integerTypeDefault->hasType());
        $this->assertEquals(Schema::TYPE_INTEGER, $integerTypeDefault->getType());
        $this->assertFalse($integerTypeDefault->hasMultipleOf());
        $this->assertNull($integerTypeDefault->getMultipleOf());
        $this->assertFalse($integerTypeDefault->hasMinimum());
        $this->assertNull($integerTypeDefault->getMinimum());
        $this->assertFalse($integerTypeDefault->hasExclusiveMinimum());
        $this->assertNull($integerTypeDefault->getExclusiveMinimum());
        $this->assertFalse($integerTypeDefault->hasMaximum());
        $this->assertNull($integerTypeDefault->getMaximum());
        $this->assertFalse($integerTypeDefault->hasExclusiveMaximum());
        $this->assertNull($integerTypeDefault->getExclusiveMaximum());

        $integerType = Schema::createIntegerType(
            $multipleOf = 10,
            $minimum = 11,
            $exclusiveMinimum = 10,
            $maximum = 21,
            $exclusiveMaximum = 20
        );
        $this->assertInstanceOf(Schema::class, $integerType);
        $this->assertTrue($integerType->hasType());
        $this->assertEquals(Schema::TYPE_INTEGER, $integerType->getType());
        $this->assertTrue($integerType->hasMultipleOf());
        $this->assertEquals($multipleOf, $integerType->getMultipleOf());
        $this->assertTrue($integerType->hasMinimum());
        $this->assertEquals($minimum, $integerType->getMinimum());
        $this->assertTrue($integerType->hasExclusiveMinimum());
        $this->assertEquals($exclusiveMinimum, $integerType->getExclusiveMinimum());
        $this->assertTrue($integerType->hasMaximum());
        $this->assertEquals($maximum, $integerType->getMaximum());
        $this->assertTrue($integerType->hasExclusiveMaximum());
        $this->assertEquals($exclusiveMaximum, $integerType->getExclusiveMaximum());
    }

    /** @test */
    public function numberTypeIsConstructedByFactoryMethod(): void
    {
        $numberTypeDefault = Schema::createNumberType();
        $this->assertInstanceOf(Schema::class, $numberTypeDefault);
        $this->assertTrue($numberTypeDefault->hasType());
        $this->assertEquals(Schema::TYPE_NUMBER, $numberTypeDefault->getType());
        $this->assertFalse($numberTypeDefault->hasMultipleOf());
        $this->assertNull($numberTypeDefault->getMultipleOf());
        $this->assertFalse($numberTypeDefault->hasMinimum());
        $this->assertNull($numberTypeDefault->getMinimum());
        $this->assertFalse($numberTypeDefault->hasExclusiveMinimum());
        $this->assertNull($numberTypeDefault->getExclusiveMinimum());
        $this->assertFalse($numberTypeDefault->hasMaximum());
        $this->assertNull($numberTypeDefault->getMaximum());
        $this->assertFalse($numberTypeDefault->hasExclusiveMaximum());
        $this->assertNull($numberTypeDefault->getExclusiveMaximum());

        $numberType = Schema::createNumberType(
            $multipleOf = 10,
            $minimum = 11,
            $exclusiveMinimum = 10,
            $maximum = 21,
            $exclusiveMaximum = 20
        );
        $this->assertInstanceOf(Schema::class, $numberType);
        $this->assertTrue($numberType->hasType());
        $this->assertEquals(Schema::TYPE_NUMBER, $numberType->getType());
        $this->assertTrue($numberType->hasMultipleOf());
        $this->assertEquals($multipleOf, $numberType->getMultipleOf());
        $this->assertTrue($numberType->hasMinimum());
        $this->assertEquals($minimum, $numberType->getMinimum());
        $this->assertTrue($numberType->hasExclusiveMinimum());
        $this->assertEquals($exclusiveMinimum, $numberType->getExclusiveMinimum());
        $this->assertTrue($numberType->hasMaximum());
        $this->assertEquals($maximum, $numberType->getMaximum());
        $this->assertTrue($numberType->hasExclusiveMaximum());
        $this->assertEquals($exclusiveMaximum, $numberType->getExclusiveMaximum());
    }

    /** @test */
    public function objectTypeIsConstructedByFactoryMethod(): void
    {
        $objectType = Schema::createObjectType(
            [
                'firstName' =>
                    Schema::createStringType(),
                'lastName' =>
                    Schema::createStringType(),
                'email' =>
                    Schema::createStringType(null, null, 'email'),
                'address' =>
                    Schema::createObjectType(
                        [
                            'street' =>
                                Schema::createStringType(),
                            'city' =>
                                Schema::createStringType(),
                            'postcode' =>
                                Schema::createStringType(
                                    null,
                                    null,
                                    null,
                                    '[0-9]{2}-[0-9]{3}'
                                ),
                            'phone' =>
                                Schema::createStringType(
                                    null,
                                    null,
                                    null,
                                    '([0-9]{3}-){2}[0-9]{3}'
                                )
                        ],
                        null,
                        null,
                        null,
                        [
                            'street', 'city', 'postcode'
                        ]
                    )
            ],
            null,
            null,
            null,
            [
                'firstName', 'lastName', 'email'
            ]
        );

        $this->assertInstanceOf(Schema::class, $objectType);
        $this->assertEquals(Schema::TYPE_OBJECT, $objectType->getType());

        $personProperties = $objectType->getProperties();

        foreach ($personProperties as $property) {
            $this->assertInstanceOf(Schema::class, $property);
        }

        $this->assertEquals(Schema::TYPE_STRING, $personProperties['firstName']->getType());
        $this->assertEquals(Schema::TYPE_STRING, $personProperties['lastName']->getType());
        $this->assertEquals(Schema::TYPE_STRING, $personProperties['email']->getType());
        $this->assertEquals(Schema::TYPE_OBJECT, $personProperties['address']->getType());
        $this->assertEquals('email', $personProperties['email']->getFormat());

        $addressProperties = $personProperties['address']->getProperties();
        $this->assertEquals(Schema::TYPE_STRING, $addressProperties['street']->getType());
        $this->assertEquals(Schema::TYPE_STRING, $addressProperties['city']->getType());
        $this->assertEquals(Schema::TYPE_STRING, $addressProperties['postcode']->getType());
        $this->assertEquals(Schema::TYPE_STRING, $addressProperties['phone']->getType());
        $this->assertNotNull($addressProperties['postcode']->getPattern());
        $this->assertNotNull($addressProperties['phone']->getPattern());
    }

    /** @test */
    public function itImplementsArrayAccessInterface(): void
    {
        $schema = $this->factory->createFromPrimitives(
            [
                'type' => 'object',
                'properties' => [
                    'name' => [
                        'type' => 'string'
                    ],
                    'age' => [
                        'type' => 'number'
                    ]
                ]
            ]
        );

        $this->assertEquals('object', $schema['type']);
        $this->assertEquals('string', $schema['properties']['name']['type']);
        $this->assertTrue(isset($schema['properties']['name']['type']));
        $this->assertFalse(isset($schema['properties']['address']['type']));
        $this->assertInstanceOf(Schema::class, $schema['properties']['name']);
    }

    /** @test */
    public function itProvidesAccessToGraphNodesUsingPath(): void
    {
        $schema = $this->factory->createFromPrimitives(
            [
                'type' => 'object',
                'properties' => [
                    'name' => [
                        'type' => 'string'
                    ],
                    'age' => [
                        'type' => 'number'
                    ]
                ]
            ]
        );

        $this->assertEquals('object', $schema->find('/type'));
        $this->assertEquals('string', $schema->find('/properties/name/type'));
        $this->assertInstanceOf(Schema::class, $schema->find('/properties/name'));
    }

    /** @test */
    public function itHandlesRecursion(): void
    {
        $recursiveSchema = $this->factory->createFromPrimitives(
            [
                'properties' => [
                    'name' => [
                        'type' => 'string'
                    ],
                    'friends' => [
                        'items' => [
                            '$ref' => '#/'
                        ]
                    ]
                ]
            ]
        );

        /** @var Schema $items */
        $items = $recursiveSchema['properties']['friends']['items'] ?? null;
        $this->assertInstanceOf(Schema::class, $items);
        $this->assertTrue($items->hasRef());
        $this->assertEquals('#/', $items->getRef());
        $this->assertSame($recursiveSchema, $items->getReference());
    }

    /** @test */
    public function itSerializesToJSON(): void
    {
        $this->assertEquals("true", \json_encode(Schema::createBooleanSchema(true)));
        $this->assertEquals("false", \json_encode(Schema::createBooleanSchema(false)));
        $this->assertEquals("{}", \json_encode(new Schema()));
        $this->assertEquals('{"const":null}', \json_encode(Schema::createFromArray(['const' => null])));

        $schema = "{}";
        $this->assertJsonStringEqualsJsonString(
            $schema,
            \json_encode(
                $this->factory->createFromPrimitives(
                    \json_decode($schema)
                )
            )
        );

        $schema = <<<JSON
            {
                "\$id": "http://example.com/schema",
                "\$defs": {
                    "text": {
                        "type": "string"
                    }
                },
                "oneOf": [
                    {
                        "type": "string",
                        "format": "email"
                    },
                    {
                        "type": "number"                                
                    },
                    {
                        "type": "boolean"                                 
                    }
                ],
                "not": {
                    "type": ["object", "array"]
                }
            }        
        JSON;
        $this->assertJsonStringEqualsJsonString(
            $schema,
            \json_encode(
                $this->factory->createFromPrimitives(
                    \json_decode($schema)
                )
            )
        );
    }

    public function setUp(): void
    {
        $this->factory = new Factory();
    }
}
