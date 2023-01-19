<?php declare(strict_types=1);

use OAS\Schema;
use OAS\Schema\ConstNull;
use OAS\Schema\Factory;
use PHPUnit\Framework\TestCase;

class SchemaTest extends TestCase
{
    private Factory $factory;

    /**
     * @test
     * @covers \OAS\Schema::setType
     */
    public function emptySchemaIsConstructed(): void
    {
        $schema = new Schema();
        self::assertNull($schema->getSchema());
        self::assertNull($schema->getId());
        self::assertNull($schema->getRef());
        self::assertNull($schema->getDefs());
        self::assertNull($schema->getVocabulary());
        self::assertNull($schema->getAnchor());
        self::assertNull($schema->getAnyOf());
        self::assertJsonStringEqualsJsonString('{}', json_encode($schema));
    }

    /**
     * @test
     * @covers \OAS\Schema::getId
     * @covers \OAS\Schema::hasId
     */
    public function schemaIsConstructedWithIdKeyword(): void
    {
        $id = 'http://example.com/schema.json';
        $schema = new Schema(_id: $id);
        self::assertEquals($id, $schema->getId());
        self::assertTrue($schema->hasId());
        self::assertJsonStringEqualsJsonString("{\"\$id\": \"$id\"}", json_encode($schema));
    }
    
    /**
     * @test
     * @covers \OAS\Schema::getSchema
     * @covers \OAS\Schema::hasSchema
     */
    public function schemaIsConstructedWithSchemaKeyword(): void
    {
        $schemaUri = 'https://json-schema.org/draft/2020-12/schema';
        $schema = new Schema(_schema: $schemaUri);
        self::assertEquals($schemaUri, $schema->getSchema());
        self::assertTrue($schema->hasSchema());
        self::assertJsonStringEqualsJsonString("{\"\$schema\": \"$schemaUri\"}", json_encode($schema));
    }

    /**
     * @test
     * @covers \OAS\Schema::getRef
     * @covers \OAS\Schema::hasRef
     */
    public function schemaIsConstructedWithRefKeyword(): void
    {
        $ref = 'http://example.com/schema.json#/$defs/A';
        $schema = new Schema(_ref: $ref);
        self::assertEquals($ref, $schema->getRef());
        self::assertTrue($schema->hasRef());
        // TODO
        //self::assertJsonStringEqualsJsonString("{\"\$ref\": \"$ref\"}", json_encode($schema));
    }

    /**
     * @test
     * @covers \OAS\Schema::setDefs
     * @covers \OAS\Schema::getDefs
     * @covers \OAS\Schema::hasDefs
     */
    public function schemaIsConstructedWithDefsKeyword(): void
    {
        $defs = [
            'creditCard' => Schema::createObjectType(
                properties: [
                    'number' => Schema::createStringType(pattern: '^[0-9]{16}$'),
                    'expiryMonth' => Schema::createStringType(pattern: '^[0-9]{2}$'),
                    'expiryYear' => Schema::createStringType(pattern: '^[0-9]{2}$')
                ]
            )
        ];
        $schema = new Schema(_defs: $defs);
        self::assertEquals($defs, $schema->getDefs());
        self::assertTrue($schema->hasDefs());
        self::assertJsonStringEqualsJsonString(
            <<<SCHEMA
                {
                    "\$defs": {
                        "creditCard": {
                            "type": "object",
                            "properties": {
                                "number": {
                                    "type": "string",
                                    "pattern": "^[0-9]{16}$" 
                                },
                                "expiryMonth": {
                                    "type": "string",
                                    "pattern": "^[0-9]{2}$" 
                                },
                                "expiryYear": {
                                    "type": "string",
                                    "pattern": "^[0-9]{2}$" 
                                }
                            }
                        }
                    }
                }
            SCHEMA,
            json_encode($schema));
    }

    /**
     * @test
     * @covers \OAS\Schema::setDefs
     * @dataProvider defsParameterInvalidTypeProvider
     */
    public function errorIsRaisedWhenValueProvidedForDefsParameterHasInvalidType(array $value): void
    {
        $this->expectException(TypeError::class);
        $this->expectExceptionMessage('The "_defs" parameter must be of array<string, \OAS\Schema> type');
        new Schema(_defs: $value);
    }

    /**
     * @test
     * @covers \OAS\Schema::getComment
     * @covers \OAS\Schema::hasComment
     */
    public function schemaIsConstructedWithCommentKeyword(): void
    {
        $comment = '[WIP]';
        $schema = new Schema(_comment: $comment);
        self::assertEquals($comment, $schema->getComment());
        self::assertTrue($schema->hasComment());
        self::assertJsonStringEqualsJsonString("{\"\$comment\": \"$comment\"}", json_encode($schema));
    }

    /**
     * @test
     * @covers \OAS\Schema::setVocabulary
     * @covers \OAS\Schema::getVocabulary
     * @covers \OAS\Schema::hasVocabulary
     */
    public function schemaIsConstructedWithVocabularyKeyword(): void
    {
        $vocabulary = ['https://json-schema.org/draft/2020-12/vocab/core' => true];
        $schema = new Schema(_vocabulary: $vocabulary);
        self::assertEquals($vocabulary, $schema->getVocabulary());
        self::assertTrue($schema->hasVocabulary());
        self::assertJsonStringEqualsJsonString(
            '{"$vocabulary": {"https://json-schema.org/draft/2020-12/vocab/core": true}}',
            json_encode($schema)
        );
    }

    /**
     * @test
     * @covers \OAS\Schema::setVocabulary
     * @dataProvider vocabularyParameterInvalidTypeProvider
     */
    public function errorIsRaisedWhenValueProvidedForVocabularyParameterHasInvalidType(mixed $value): void
    {
        $this->expectException(TypeError::class);
        $this->expectExceptionMessage('The "_vocabulary" parameter must be of array<string, boolean> type');
        new Schema(_vocabulary: $value);
    }

    /**
     * @test
     * @covers \OAS\Schema::getDynamicAnchor
     * @covers \OAS\Schema::hasDynamicAnchor
     */
    public function schemaIsConstructedWithDynamicAnchorKeyword(): void
    {
        $dynamicAnchor = 'node';
        $schema = new Schema(_dynamicAnchor: $dynamicAnchor);
        self::assertEquals($dynamicAnchor, $schema->getDynamicAnchor());
        self::assertTrue($schema->hasDynamicAnchor());
        self::assertJsonStringEqualsJsonString(
            "{\"\$dynamicAnchor\": \"$dynamicAnchor\"}",
            json_encode($schema)
        );
    }

    /**
     * @test
     * @covers \OAS\Schema::getDynamicRef
     * @covers \OAS\Schema::hasDynamicRef
     */
    public function schemaIsConstructedWithDynamicRefKeyword(): void
    {
        $dynamicRef = 'node';
        $schema = new Schema(_dynamicRef: $dynamicRef);
        self::assertEquals($dynamicRef, $schema->getDynamicRef());
        self::assertTrue($schema->hasDynamicRef());
        self::assertJsonStringEqualsJsonString(
            "{\"\$dynamicRef\": \"$dynamicRef\"}",
            json_encode($schema)
        );
    }

    /**
     * @test
     * @covers \OAS\Schema::getAnchor
     * @covers \OAS\Schema::hasAnchor
     */
    public function schemaIsConstructedWithAnchorKeyword(): void
    {
        $anchor = 'node';
        $schema = new Schema(_anchor: $anchor);
        self::assertEquals($anchor, $schema->getAnchor());
        self::assertTrue($schema->hasAnchor());
        self::assertJsonStringEqualsJsonString(
            "{\"\$anchor\": \"$anchor\"}",
            json_encode($schema)
        );
    }

    /**
     * @test
     * @covers \OAS\Schema::setAllOf
     * @covers \OAS\Schema::getAllOf
     * @covers \OAS\Schema::hasAllOf
     */
    public function schemaIsConstructedWithAllOfKeyword(): void
    {
        $allOf = [
            new Schema(type: 'number'),
            new Schema(minimum: 10)
        ];
        $schema = new Schema(allOf: $allOf);
        self::assertEquals($allOf, $schema->getAllOf());
        self::assertTrue($schema->hasAllOf());
        self::assertJsonStringEqualsJsonString(
            <<<SCHEMA
                {
                    "allOf": [
                        {"type": "number"},
                        {"minimum": 10}
                    ]
                }
            SCHEMA,
            json_encode($schema)
        );
    }
    
    /**
     * @test
     * @covers \OAS\Schema::setAllOf
     * @dataProvider allOfParameterInvalidTypeProvider
     */
    public function errorIsRaisedWhenValueProvidedForAllOfParameterHasInvalidType(mixed $value): void
    {
        $this->expectException(TypeError::class);
        $this->expectExceptionMessage('The "allOf" parameter must be of array<int, \OAS\Schema> type');
        new Schema(allOf: $value);
    }

    /**
     * @test
     * @covers \OAS\Schema::setAnyOf
     * @covers \OAS\Schema::getAnyOf
     * @covers \OAS\Schema::hasAnyOf
     */
    public function schemaIsConstructedWithAnyOfKeyword(): void
    {
        $anyOf = [
            new Schema(type: 'number'),
            new Schema(minimum: 10)
        ];
        $schema = new Schema(anyOf: $anyOf);
        self::assertEquals($anyOf, $schema->getAnyOf());
        self::assertTrue($schema->hasAnyOf());
        self::assertJsonStringEqualsJsonString(
            <<<SCHEMA
                {
                    "anyOf": [
                        {"type": "number"},
                        {"minimum": 10}
                    ]
                }
            SCHEMA,
            json_encode($schema)
        );
    }

    /**
     * @test
     * @covers \OAS\Schema::setAnyOf
     * @dataProvider anyOfParameterInvalidTypeProvider
     */
    public function errorIsRaisedWhenValueProvidedForAnyOfParameterHasInvalidType(mixed $value): void
    {
        $this->expectException(TypeError::class);
        $this->expectExceptionMessage('The "anyOf" parameter must be of array<int, \OAS\Schema> type');
        new Schema(anyOf: $value);
    }

    /**
     * @test
     * @covers \OAS\Schema::setOneOf
     * @covers \OAS\Schema::getOneOf
     * @covers \OAS\Schema::hasOneOf
     */
    public function schemaIsConstructedWithOneOfKeyword(): void
    {
        $oneOf = [
            new Schema(type: 'number'),
            new Schema(minimum: 10)
        ];
        $schema = new Schema(oneOf: $oneOf);
        self::assertEquals($oneOf, $schema->getOneOf());
        self::assertTrue($schema->hasOneOf());
        self::assertJsonStringEqualsJsonString(
            <<<SCHEMA
                {
                    "oneOf": [
                        {"type": "number"},
                        {"minimum": 10}
                    ]
                }
            SCHEMA,
            json_encode($schema)
        );
    }

    /**
     * @test
     * @covers \OAS\Schema::setOneOf
     * @dataProvider oneOfParameterInvalidTypeProvider
     */
    public function errorIsRaisedWhenValueProvidedForOneOfParameterHasInvalidType(mixed $value): void
    {
        $this->expectException(TypeError::class);
        $this->expectExceptionMessage('The "oneOf" parameter must be of array<int, \OAS\Schema> type');
        new Schema(oneOf: $value);
    }
    
    /**
     * @test
     * @covers \OAS\Schema::setType
     * @covers \OAS\Schema::getType
     * @covers \OAS\Schema::hasType
     */
    public function schemaIsConstructedWithTypeKeyword(): void
    {
        foreach (Schema::TYPES as $type) {
            self::assertEquals($type, (new Schema(type: $type))->getType());
        }

        $schema = new Schema(type: ['string', 'null']);
        self::assertEquals(['string', 'null'], $schema->getType());
        self::assertTrue($schema->hasType());
        self::assertJsonStringEqualsJsonString('{"type": ["string","null"]}', json_encode($schema));
    }

    /**
     * @test
     * @covers \OAS\Schema::setType
     */
    public function errorIsRaisedWhenValueProvidedForTypeParameterInvalid(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The "type" parameter must have one of the following values: "null", "string", "number", "integer", "boolean", "array", "object" ("float" provided)');
        new Schema(type: 'float');
    }

    /**
     * @test
     * @covers \OAS\Schema::setType
     * @dataProvider typeParameterInvalidTypeProvider
     */
    public function errorIsRaisedWhenValueProvidedForTypeParameterHasInvalidType(mixed $value): void
    {
        $this->expectException(TypeError::class);
        $this->expectExceptionMessage('The "type" parameter must be of string|array<int, string> type');
        new Schema(type: $value);
    }

    /**
     * @test
     * @covers \OAS\Schema::setEnum
     * @covers \OAS\Schema::getEnum
     * @covers \OAS\Schema::hasEnum
     */
    public function schemaIsConstructedWithEnumKeyword(): void
    {
        $enum = ['ON', 'OFF'];
        $schema = new Schema(enum: $enum);
        self::assertEquals($enum, $schema->getEnum());
        self::assertTrue($schema->hasEnum());
        self::assertJsonStringEqualsJsonString('{"enum":["ON", "OFF"]}', json_encode($schema));
    }

    /**
     * @test
     * @covers \OAS\Schema::setEnum
     */
    public function errorIsRaisedWhenValueProvidedForEnumParameterHasInvalidType(): void
    {
        $this->expectException(TypeError::class);
        $this->expectExceptionMessage('The "enum" parameter must be of array<int, mixed> type');
        new Schema(enum: ['on' => 'ON', 'off' => 'OFF']);
    }

    /**
     * @test
     * @covers \OAS\Schema::getConst
     * @covers \OAS\Schema::hasConst
     */
    public function schemaIsConstructedWithConstKeyword(): void
    {
        $const = 3.1415926535;
        $schema = new Schema(const: $const);
        self::assertEquals($const, $schema->getConst());
        self::assertTrue($schema->hasConst());
        // segfault!
        // self::assertJsonStringEqualsJsonString('{"const": 3.1415926535}', json_encode($schema));

        $schema = new Schema();
        self::assertJsonStringEqualsJsonString('{}', json_encode($schema));

        $schema = new Schema(const: new ConstNull());
        self::assertJsonStringEqualsJsonString('{"const": null}', json_encode($schema));
    }

    /**
     * @test
     * @covers \OAS\Schema::getPattern
     * @covers \OAS\Schema::hasPattern
     */
    public function schemaIsConstructedWithPatternKeyword(): void
    {
        $pattern = '^[A-Z]{2}$';
        $schema = new Schema(pattern: $pattern);
        self::assertEquals($pattern, $schema->getPattern());
        self::assertTrue($schema->hasPattern());
        self::assertJsonStringEqualsJsonString('{"pattern": "^[A-Z]{2}$"}', json_encode($schema));
    }

    /**
     * @test
     * @covers \OAS\Schema::getMinLength
     * @covers \OAS\Schema::hasMinLength
     */
    public function schemaIsConstructedWithMinLengthKeyword(): void
    {
        $minLength = 1;
        $schema = new Schema(minLength: $minLength);
        self::assertEquals($minLength, $schema->getMinLength());
        self::assertTrue($schema->hasMinLength());
        self::assertJsonStringEqualsJsonString("{\"minLength\": $minLength}", json_encode($schema));
    }

    /**
     * @test
     * @covers \OAS\Schema::getMaxLength
     * @covers \OAS\Schema::hasMaxLength
     */
    public function schemaIsConstructedWithMaxLengthKeyword(): void
    {
        $maxLength = 1;
        $schema = new Schema(maxLength: $maxLength);
        self::assertEquals($maxLength, $schema->getMaxLength());
        self::assertTrue($schema->hasMaxLength());
        self::assertJsonStringEqualsJsonString("{\"maxLength\": $maxLength}", json_encode($schema));
    }

    /**
     * @test
     * @covers \OAS\Schema::getExclusiveMaximum
     * @covers \OAS\Schema::hasExclusiveMaximum
     */
    public function schemaIsConstructedWithExclusiveMaximumKeyword(): void
    {
        $exclusiveMaximum = 1;
        $schema = new Schema(exclusiveMaximum: $exclusiveMaximum);
        self::assertEquals($exclusiveMaximum, $schema->getExclusiveMaximum());
        self::assertTrue($schema->hasExclusiveMaximum());
        self::assertJsonStringEqualsJsonString("{\"exclusiveMaximum\": $exclusiveMaximum}", json_encode($schema));
    }

    /**
     * @test
     * @covers \OAS\Schema::getMultipleOf
     * @covers \OAS\Schema::hasMultipleOf
     */
    public function schemaIsConstructedWithMultipleOfKeyword(): void
    {
        $multipleOf = 3;
        $schema = new Schema(multipleOf: $multipleOf);
        self::assertEquals($multipleOf, $schema->getMultipleOf());
        self::assertEquals($multipleOf, $schema->hasMultipleOf());
        self::assertJsonStringEqualsJsonString("{\"multipleOf\": $multipleOf}", json_encode($schema));
    }

    /**
     * @test
     * @covers \OAS\Schema::hasExclusiveMinimum
     * @covers \OAS\Schema::getExclusiveMinimum
     */
    public function schemaIsConstructedWithExclusiveMinimumKeyword(): void
    {
        $exclusiveMinimum = 1;
        $schema = new Schema(exclusiveMinimum: $exclusiveMinimum);
        self::assertEquals($exclusiveMinimum, $schema->getExclusiveMinimum());
        self::assertTrue($schema->hasExclusiveMinimum());
        self::assertJsonStringEqualsJsonString("{\"exclusiveMinimum\": $exclusiveMinimum}", json_encode($schema));
    }

    /**
     * @test
     * @covers \OAS\Schema::getMinimum
     * @covers \OAS\Schema::hasMinimum
     */
    public function schemaIsConstructedWithMinimumKeyword(): void
    {
        $minimum = 1;
        $schema = new Schema(minimum: $minimum);
        self::assertEquals($minimum, $schema->getMinimum());
        self::assertTrue($schema->hasMinimum());
        self::assertJsonStringEqualsJsonString("{\"minimum\": $minimum}", json_encode($schema));
    }

    /**
     * @test
     * @covers \OAS\Schema::hasMaximum
     * @covers \OAS\Schema::getMaximum
     */
    public function schemaIsConstructedWithMaximumKeyword(): void
    {
        $maximum = 1;
        $schema = new Schema(maximum: $maximum);
        self::assertEquals($maximum, $schema->getMaximum());
        self::assertTrue($schema->hasMaximum());
        self::assertJsonStringEqualsJsonString("{\"maximum\": $maximum}", json_encode($schema));
    }

    /**
     * @test
     * @covers \OAS\Schema::setDependentRequired
     * @covers \OAS\Schema::hasDependentRequired
     * @covers \OAS\Schema::getDependentRequired
     */
    public function schemaIsConstructedWithDependentRequiredKeyword(): void
    {
        $dependentRequired = ['creditCard' => ['billingAddress']];
        $schema = new Schema(dependentRequired: $dependentRequired);
        self::assertEquals($dependentRequired, $schema->getDependentRequired());
        self::assertTrue($schema->hasDependentRequired());
        self::assertJsonStringEqualsJsonString(
            <<<SCHEMA
                {
                    "dependentRequired": {
                        "creditCard": ["billingAddress"]
                    }
               }
            SCHEMA,
            json_encode($schema)
        );
    }

    /**
     * @test
     * @covers \OAS\Schema::setDependentRequired
     * @dataProvider dependentRequiredParameterInvalidTypeProvider
     */
    public function errorIsRaisedWhenValueProvidedForDependentRequiredParameterHasInvalidType(mixed $value): void
    {
        $this->expectException(TypeError::class);
        $this->expectExceptionMessage('The "dependentRequired" parameter must be of array<string, array<int, string>> type');
        new Schema(dependentRequired: $value);
    }

    /**
     * @test
     * @covers \OAS\Schema::getMaxProperties
     * @covers \OAS\Schema::hasMaxProperties
     */
    public function schemaIsConstructedWithMaxPropertiesKeyword(): void
    {
        $maxProperties = 5;
        $schema = new Schema(maxProperties: $maxProperties);
        self::assertEquals($maxProperties, $schema->getMaxProperties());
        self::assertTrue($schema->hasMaxProperties());
        self::assertJsonStringEqualsJsonString("{\"maxProperties\": $maxProperties}", json_encode($schema));
    }

    /**
     * @test
     * @covers \OAS\Schema::getMinProperties
     * @covers \OAS\Schema::hasMinProperties
     */
    public function schemaIsConstructedWithMinPropertiesKeyword(): void
    {
        $minProperties = 5;
        $schema = new Schema(minProperties: $minProperties);
        self::assertEquals($minProperties, $schema->getMinProperties());
        self::assertTrue($schema->hasMinProperties());
        self::assertJsonStringEqualsJsonString("{\"minProperties\": $minProperties}", json_encode($schema));
    }

    /**
     * @test
     * @covers \OAS\Schema::setRequired
     * @covers \OAS\Schema::getRequired
     * @covers \OAS\Schema::hasRequired
     */
    public function schemaIsConstructedWithRequiredKeyword(): void
    {
        $required = ['firstName', 'lastName'];
        $schema = new Schema(required: $required);
        self::assertEquals($required, $schema->getRequired());
        self::assertTrue($schema->hasRequired());
        self::assertJsonStringEqualsJsonString("{\"required\": [\"firstName\", \"lastName\"]}", json_encode($schema));
    }

    /**
     * @test
     * @covers \OAS\Schema::setRequired
     * @dataProvider requiredParameterInvalidTypeProvider
     */
    public function errorIsRaisedWhenValueProvidedForRequiredParameterHasInvalidType(mixed $value): void
    {
        $this->expectException(TypeError::class);
        $this->expectExceptionMessage('The "required" parameter must be of array<int, string> type');
        new Schema(required: $value);
    }

    /**
     * @test
     * @covers \OAS\Schema::getMaxItems
     * @covers \OAS\Schema::hasMaxItems
     */
    public function schemaIsConstructedWithMaxItemsKeyword(): void
    {
        $maxItems = 5;
        $schema = new Schema(maxItems: $maxItems);
        self::assertEquals($maxItems, $schema->getMaxItems());
        self::assertTrue($schema->hasMaxItems());
        self::assertJsonStringEqualsJsonString("{\"maxItems\": $maxItems}", json_encode($schema));
    }
    
    /**
     * @test
     * @covers \OAS\Schema::getMinItems
     * @covers \OAS\Schema::hasMinItems
     */
    public function schemaIsConstructedWithMinItemsKeyword(): void
    {
        $minItems = 5;
        $schema = new Schema(minItems: $minItems);
        self::assertEquals($minItems, $schema->getMinItems());
        self::assertTrue($schema->hasMinItems());
        self::assertJsonStringEqualsJsonString("{\"minItems\": $minItems}", json_encode($schema));
    }

    /**
     * @test
     * @covers \OAS\Schema::getMaxContains
     * @covers \OAS\Schema::hasMaxContains
     */
    public function schemaIsConstructedWithMaxContainsKeyword(): void
    {
        $maxContains = 5;
        $schema = new Schema(maxContains: $maxContains);
        self::assertEquals($maxContains, $schema->getMaxContains());
        self::assertTrue($schema->hasMaxContains());
        self::assertJsonStringEqualsJsonString("{\"maxContains\": $maxContains}", json_encode($schema));
    }

    /**
     * @test
     * @covers \OAS\Schema::getMinContains
     * @covers \OAS\Schema::hasMinContains
     */
    public function schemaIsConstructedWithMinContainsKeyword(): void
    {
        $minContains = 5;
        $schema = new Schema(minContains: $minContains);
        self::assertEquals($minContains, $schema->getMinContains());
        self::assertTrue($schema->hasMinContains());
        self::assertJsonStringEqualsJsonString("{\"minContains\": $minContains}", json_encode($schema));
    }

    /**
     * @test
     * @covers \OAS\Schema::getUniqueItems
     * @covers \OAS\Schema::hasUniqueItems
     */
    public function schemaIsConstructedWithUniqueItemsKeyword(): void
    {
        $uniqueItems = true;
        $schema = new Schema(uniqueItems: $uniqueItems);
        self::assertTrue($schema->getUniqueItems());
        self::assertTrue($schema->hasUniqueItems());
        self::assertJsonStringEqualsJsonString("{\"uniqueItems\": true}", json_encode($schema));
    }

    /**
     * @test
     * @covers \OAS\Schema::createArrayType
     */
    public function arrayTypeIsConstructedByFactoryMethod(): void
    {
        // TODO: provide values for method properties
        $schema = Schema::createArrayType();
        self::assertInstanceOf(Schema::class, $schema);
        self::assertEquals(Schema::TYPE_ARRAY, $schema->getType());
        self::assertNull($schema->getAdditionalItems());
        self::assertFalse($schema->hasMinItems());
        self::assertNull($schema->getMinItems());
        self::assertFalse($schema->hasMinItems());
        self::assertNull($schema->getMaxItems());
        self::assertFalse($schema->hasItems());
        self::assertNull($schema->getItems());
        self::assertFalse($schema->hasAdditionalItems());
        self::assertNull($schema->getAdditionalItems());
    }

    /**
     * @test
     * @covers \OAS\Schema::setItems
     * @covers \OAS\Schema::getItems
     * @covers \OAS\Schema::isTuple
     */
    public function schemaIsConstructedWithItemsKeyword(): void
    {
        $schema = new Schema(items: new Schema(type: 'string'));
        self::assertInstanceOf(Schema::class, $schema->getItems());
        self::assertFalse($schema->isTuple());
        self::assertJsonStringEqualsJsonString('{"items": {"type": "string"}}', json_encode($schema));

        $schema = new Schema(items: [new Schema(type: 'string'), new Schema(type: 'null')]);
        self::assertIsArray($schema->getItems());
        self::assertTrue($schema->isTuple());
        self::assertJsonStringEqualsJsonString('{"items": [{"type": "string"},{"type": "null"}]}', json_encode($schema));
    }

    /**
     * @test
     * @covers \OAS\Schema::setItems
     * @dataProvider itemsParameterInvalidTypeProvider
     */
    public function errorIsRaisedWhenValueProvidedForItemsParameterHasInvalidType(mixed $value): void
    {
        $this->expectException(TypeError::class);
        $this->expectExceptionMessage('The "items" parameter must be of \OAS\Schema|array<int, \OAS\Schema> type');
        new Schema(items: $value);
    }

    /**
     * @test
     * @coversNothing
     */
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

    /**
     * @test
     * @coversNothing
     */
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

    /**
     * @test
     * @coversNothing
     */
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

    /**
     * @tes
     * @coversNothing
     */
    public function objectTypeIsConstructedByFactoryMethod(): void
    {
        $objectType = Schema::createObjectType(
            [
                'firstName' => Schema::createStringType(),
                'lastName' => Schema::createStringType(),
                'email' => Schema::createStringType(null, null, 'email'),
                'address' => Schema::createObjectType(
                        [
                            'street' => Schema::createStringType(),
                            'city' => Schema::createStringType(),
                            'postcode' => Schema::createStringType(
                                null,
                                null,
                                null,
                                '[0-9]{2}-[0-9]{3}'
                            ),
                            'phone' => Schema::createStringType(
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

    /**
     * @test
     * @coversNothing
     */
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

    /**
     * @test
     * @coversNothing
     */
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

    /**
     * @test
     * @coversNothing
     */
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

    public function setUp(): void
    {
        $this->factory = new Factory();
    }

    public static function typeParameterInvalidTypeProvider(): iterable
    {
        yield ['value' => ['string', true]];
        yield ['value' => ['type1' => 'string', 'type2' => 'boolean']];
    }

    public static function itemsParameterInvalidTypeProvider(): iterable
    {
        yield ['value' => [new Schema(), null]];
        yield ['value' => ['a' => new Schema(), 'b' => new Schema()]];
    }

    public static function dependentRequiredParameterInvalidTypeProvider(): iterable
    {
        yield ['value' => ['parameter']];
        yield ['value' => [['parameter']]];
        yield ['value' => ['parameter' => ['key' => 'dependentParameter']]];
    }

    public static function requiredParameterInvalidTypeProvider(): iterable
    {
        yield ['value' => [1, 2, 3]];
        yield ['value' => ['a' => 'one', 'b' => 'two']];
        yield ['value' => ['one', 'two', false]];
    }

    public static function defsParameterInvalidTypeProvider(): iterable
    {
        yield ['value' => [new Schema()]];
        yield ['value' => ['prop' => [new Schema()]]];
        yield ['value' => ['prop' => new stdClass()]];
    }

    public static function vocabularyParameterInvalidTypeProvider(): iterable
    {
        yield ['value' => ['https://json-schema.org/draft/2020-12/vocab/core' => 1]];
        yield ['value' => [true]];
    }

    public static function allOfParameterInvalidTypeProvider(): iterable
    {
        yield ['value' => ['schema' => new Schema()]];
        yield ['value' => [new stdClass()]];
    }

    public static function anyOfParameterInvalidTypeProvider(): iterable
    {
        yield ['value' => ['schema' => new Schema()]];
        yield ['value' => [new stdClass()]];
    }

    public static function oneOfParameterInvalidTypeProvider(): iterable
    {
        yield ['value' => ['schema' => new Schema()]];
        yield ['value' => [new stdClass()]];
    }
}
