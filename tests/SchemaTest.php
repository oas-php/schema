<?php declare(strict_types=1);

use OAS\Schema;
use OAS\Schema\NullValue;
use OAS\Schema\Type;
use PHPUnit\Framework\TestCase;

class SchemaTest extends TestCase
{
    /**
     * @test
     * @covers \OAS\Schema::__construct
     * @covers \OAS\Schema::jsonSerialize
     */
    public function schemaIsConstructedWithoutParameters(): void
    {
        $schema = new Schema();
        self::assertNull($schema->getSchema());
        self::assertNull($schema->getId());
        self::assertNull($schema->getRef());
        self::assertNull($schema->getDefs());
        self::assertNull($schema->getVocabulary());
        self::assertNull($schema->getDynamicRef());
        self::assertNull($schema->getDynamicAnchor());
        self::assertNull($schema->getAllOf());
        self::assertNull($schema->getAnyOf());
        self::assertNull($schema->getOneOf());
        self::assertNull($schema->getNot());
        self::assertNull($schema->getIf());
        self::assertNull($schema->getThen());
        self::assertNull($schema->getElse());
        self::assertNull($schema->getDependentSchemas());
        self::assertNull($schema->getPrefixItems());
        self::assertNull($schema->getItems());
        self::assertNull($schema->getContains());
        self::assertNull($schema->getProperties());
        self::assertNull($schema->getPatternProperties());
        self::assertNull($schema->getPropertyNames());
        self::assertNull($schema->getType());
        self::assertNull($schema->getEnum());
        self::assertNull($schema->getConst());
        self::assertNull($schema->getMultipleOf());
        self::assertNull($schema->getMaximum());
        self::assertNull($schema->getExclusiveMaximum());
        self::assertNull($schema->getMinimum());
        self::assertNull($schema->getExclusiveMinimum());
        self::assertNull($schema->getMaxLength());
        self::assertNull($schema->getMinLength());
        self::assertNull($schema->getPattern());
        self::assertNull($schema->getMaxItems());
        self::assertNull($schema->getMinItems());
        self::assertNull($schema->getUniqueItems());
        self::assertNull($schema->getMaxContains());
        self::assertNull($schema->getMinContains());
        self::assertNull($schema->getMaxProperties());
        self::assertNull($schema->getMinProperties());
        self::assertNull($schema->getRequired());
        self::assertNull($schema->getDependentRequired());
        self::assertNull($schema->getTitle());
        self::assertNull($schema->getDescription());
        self::assertNull($schema->getDefault());
        self::assertNull($schema->getDeprecated());
        self::assertNull($schema->getReadOnly());
        self::assertNull($schema->getWriteOnly());
        self::assertNull($schema->getExamples());
        self::assertNull($schema->getFormat());
        self::assertNull($schema->getUnevaluatedProperties());
        self::assertNull($schema->getUnevaluatedItems());
        self::assertJsonStringEqualsJsonString('{}', json_encode($schema));
    }

    /**
     * @test
     * @covers \OAS\Schema::getSchema
     * @covers \OAS\Schema::hasSchema
     * @covers \OAS\Schema::jsonSerialize
     * @covers \OAS\Schema::denormalizePropertyNames
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
     * @covers \OAS\Schema::getId
     * @covers \OAS\Schema::hasId
     * @covers \OAS\Schema::jsonSerialize
     * @covers \OAS\Schema::denormalizePropertyNames
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
     * @covers \OAS\Schema::getRef
     * @covers \OAS\Schema::hasRef
     * @covers \OAS\Schema::jsonSerialize
     * @covers \OAS\Schema::denormalizePropertyNames
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
     * @covers \OAS\Schema::jsonSerialize
     * @covers \OAS\Schema::denormalizePropertyNames
     */
    public function schemaIsConstructedWithDefsKeyword(): void
    {
        $defs = [
            'creditCard' => new Schema(
                properties: [
                    'number' => new Schema(
                        type: Type::STRING,
                        pattern: '^[0-9]{16}$'
                    ),
                    'expiryMonth' => new Schema(
                        type:  Type::STRING,
                        pattern: '^[0-9]{2}$'
                    ),
                    'expiryYear' => new Schema(
                        type:  Type::STRING,
                        pattern: '^[0-9]{2}$'
                    )
                ],
                type:  Type::OBJECT
            )
        ];
        $schema = new Schema(_defs: $defs);
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
        $this->expectExceptionMessage('The "_defs" parameter must be of array<string, \OAS\Schema|bool> type');
        new Schema(_defs: $value);
    }

    /**
     * @test
     * @covers \OAS\Schema::getComment
     * @covers \OAS\Schema::hasComment
     * @covers \OAS\Schema::jsonSerialize
     * @covers \OAS\Schema::denormalizePropertyNames
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
     * @covers \OAS\Schema::jsonSerialize
     * @covers \OAS\Schema::denormalizePropertyNames
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
     * @covers \OAS\Schema::getDynamicRef
     * @covers \OAS\Schema::hasDynamicRef
     * @covers \OAS\Schema::jsonSerialize
     * @covers \OAS\Schema::denormalizePropertyNames
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
     * @covers \OAS\Schema::getDynamicAnchor
     * @covers \OAS\Schema::hasDynamicAnchor
     * @covers \OAS\Schema::jsonSerialize
     * @covers \OAS\Schema::denormalizePropertyNames
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
     * @covers \OAS\Schema::getAnchor
     * @covers \OAS\Schema::hasAnchor
     * @covers \OAS\Schema::jsonSerialize
     * @covers \OAS\Schema::denormalizePropertyNames
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

    // applicator

    /**
     * @test
     * @covers \OAS\Schema::setAllOf
     * @covers \OAS\Schema::getAllOf
     * @covers \OAS\Schema::hasAllOf
     * @covers \OAS\Schema::jsonSerialize
     */
    public function schemaIsConstructedWithAllOfParameter(): void
    {
        $allOf = [
            new Schema(type: Type::NUMBER),
            new Schema(minimum: 10),
            true
        ];
        $schema = new Schema(allOf: $allOf);
        self::assertEquals($allOf, $schema->getAllOf());
        self::assertTrue($schema->hasAllOf());
        self::assertJsonStringEqualsJsonString(
            <<<SCHEMA
                {
                    "allOf": [
                        {"type": "number"},
                        {"minimum": 10},
                        true
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
        $this->expectExceptionMessage('The "allOf" parameter must be of array<int, \OAS\Schema|bool> type');
        new Schema(allOf: $value);
    }

    /**
     * @test
     * @covers \OAS\Schema::setAnyOf
     * @covers \OAS\Schema::getAnyOf
     * @covers \OAS\Schema::hasAnyOf
     * @covers \OAS\Schema::jsonSerialize
     */
    public function schemaIsConstructedWithAnyOfParameter(): void
    {
        $anyOf = [
            new Schema(type:  Type::NUMBER),
            new Schema(minimum: 10),
            true
        ];
        $schema = new Schema(anyOf: $anyOf);
        self::assertEquals($anyOf, $schema->getAnyOf());
        self::assertTrue($schema->hasAnyOf());
        self::assertJsonStringEqualsJsonString(
            <<<SCHEMA
                {
                    "anyOf": [
                        {"type": "number"},
                        {"minimum": 10},
                        true
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
        $this->expectExceptionMessage('The "anyOf" parameter must be of array<int, \OAS\Schema|bool> type');
        new Schema(anyOf: $value);
    }

    /**
     * @test
     * @covers \OAS\Schema::setOneOf
     * @covers \OAS\Schema::getOneOf
     * @covers \OAS\Schema::hasOneOf
     * @covers \OAS\Schema::jsonSerialize
     */
    public function schemaIsConstructedWithOneOfParameter(): void
    {
        $oneOf = [
            new Schema(type:  Type::NUMBER),
            new Schema(minimum: 10),
            true
        ];
        $schema = new Schema(oneOf: $oneOf);
        self::assertEquals($oneOf, $schema->getOneOf());
        self::assertTrue($schema->hasOneOf());
        self::assertJsonStringEqualsJsonString(
            <<<SCHEMA
                {
                    "oneOf": [
                        {"type": "number"},
                        {"minimum": 10},
                        true
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
        $this->expectExceptionMessage('The "oneOf" parameter must be of array<int, \OAS\Schema|bool> type');
        new Schema(oneOf: $value);
    }

    /**
     * @test
     * @covers \OAS\Schema::setNot
     * @covers \OAS\Schema::getNot
     * @covers \OAS\Schema::hasNot
     * @covers \OAS\Schema::jsonSerialize
     */
    public function schemaIsConstructedWithNotParameter(): void
    {
        $not = new Schema(type: Type::NUMBER);
        $schema = new Schema(not: $not);
        self::assertEquals($not, $schema->getNot());
        self::assertTrue($schema->hasNot());
        self::assertJsonStringEqualsJsonString('{"not": {"type": "number"}}', json_encode($schema));

        $not = true;
        $schema = new Schema(not: $not);
        self::assertEquals($not, $schema->getNot());
        self::assertTrue($schema->hasNot());
        self::assertJsonStringEqualsJsonString('{"not": true}', json_encode($schema));
    }

    /**
     * @test
     * @covers \OAS\Schema::setIf
     * @covers \OAS\Schema::getIf
     * @covers \OAS\Schema::hasIf
     * @covers \OAS\Schema::jsonSerialize
     */
    public function schemaIsConstructedWithIfParameter(): void
    {
        $if = new Schema(type: Type::NUMBER);
        $schema = new Schema(if: $if);
        self::assertEquals($if, $schema->getIf());
        self::assertTrue($schema->hasIf());
        self::assertJsonStringEqualsJsonString('{"if": {"type": "number"}}', json_encode($schema));

        $if = true;
        $schema = new Schema(if: $if);
        self::assertEquals($if, $schema->getIf());
        self::assertTrue($schema->hasIf());
        self::assertJsonStringEqualsJsonString('{"if": true}', json_encode($schema));
    }

    /**
     * @test
     * @covers \OAS\Schema::setThen
     * @covers \OAS\Schema::getThen
     * @covers \OAS\Schema::hasThen
     * @covers \OAS\Schema::jsonSerialize
     */
    public function schemaIsConstructedWithThenParameter(): void
    {
        $then = new Schema(type: Type::NUMBER);
        $schema = new Schema(then: $then);
        self::assertEquals($then, $schema->getThen());
        self::assertTrue($schema->hasThen());
        self::assertJsonStringEqualsJsonString('{"then": {"type": "number"}}', json_encode($schema));

        $then = true;
        $schema = new Schema(then: $then);
        self::assertEquals($then, $schema->getThen());
        self::assertTrue($schema->hasThen());
        self::assertJsonStringEqualsJsonString('{"then": true}', json_encode($schema));
    }

    /**
     * @test
     * @covers \OAS\Schema::setElse
     * @covers \OAS\Schema::getElse
     * @covers \OAS\Schema::hasElse
     * @covers \OAS\Schema::jsonSerialize
     */
    public function schemaIsConstructedWithElseParameter(): void
    {
        $else = new Schema(type: Type::NUMBER);
        $schema = new Schema(else: $else);
        self::assertEquals($else, $schema->getElse());
        self::assertTrue($schema->hasElse());
        self::assertJsonStringEqualsJsonString('{"else": {"type": "number"}}', json_encode($schema));

        $else = true;
        $schema = new Schema(else: $else);
        self::assertEquals($else, $schema->getElse());
        self::assertTrue($schema->hasElse());
        self::assertJsonStringEqualsJsonString('{"else": true}', json_encode($schema));
    }

    /**
     * @test
     * @covers \OAS\Schema::setDependentSchemas
     * @covers \OAS\Schema::getDependentSchemas
     * @covers \OAS\Schema::hasDependentSchemas
     * @covers \OAS\Schema::jsonSerialize
     */
    public function schemaIsConstructedWithDependentSchemasParameter(): void
    {
        $dependentSchemas = [
            'expiryYear' => new Schema(
                properties: [
                    'expiryMonth' => new Schema(
                        type: Type::STRING,
                        pattern: '^[0-9]{2}$'
                    )
                ]
            )
        ];
        $schema = new Schema(dependentSchemas: $dependentSchemas);
        self::assertEquals($dependentSchemas, $schema->getDependentSchemas());
        self::assertTrue($schema->hasDependentSchemas());
        self::assertJsonStringEqualsJsonString(
            <<<SCHEMA
                {
                    "dependentSchemas": {
                       "expiryYear": {                            
                            "properties": {     
                                "expiryMonth": {     
                                    "pattern": "^[0-9]{2}$",  
                                    "type": "string"
                                }
                            }
                       }
                    }
                }
            SCHEMA,
            json_encode($schema)
        );
    }

    /**
     * @test
     * @covers \OAS\Schema::setDependentSchemas
     * @dataProvider dependentSchemasParameterInvalidTypeProvider
     */
    public function errorIsRaisedWhenValueProvidedForDependentSchemasParameterHasInvalidType(array $value): void
    {
        $this->expectException(TypeError::class);
        $this->expectExceptionMessage('The "dependentSchemas" parameter must be of ?array<string, \OAS\Schema|bool> type');
        new Schema(dependentSchemas: $value);
    }

    /**
     * @test
     * @covers \OAS\Schema::setPrefixItems
     * @covers \OAS\Schema::getPrefixItems
     * @covers \OAS\Schema::hasPrefixItems
     * @covers \OAS\Schema::isTuple
     * @covers \OAS\Schema::jsonSerialize
     */
    public function schemaIsConstructedWithPrefixItemsParameter(): void
    {
        $prefixItems = [new Schema(type:  Type::STRING), new Schema(type:  Type::NULL), true];
        $schema = new Schema(prefixItems: $prefixItems);
        self::assertEquals($prefixItems, $schema->getPrefixItems());
        self::assertTrue($schema->hasPrefixItems());
        self::assertFalse($schema->isTuple());
        self::assertJsonStringEqualsJsonString(
            '{"prefixItems": [{"type": "string"},{"type": "null"}, true]}',
            json_encode($schema)
        );

        $tuple = new Schema(prefixItems: $prefixItems, items: false);
        self::assertTrue($tuple->isTuple());
    }

    /**
     * @test
     * @covers \OAS\Schema::setPrefixItems
     * @dataProvider prefixItemsParameterInvalidTypeProvider
     */
    public function errorIsRaisedWhenValueProvidedForPrefixItemsParameterHasInvalidType(mixed $value): void
    {
        $this->expectException(TypeError::class);
        $this->expectExceptionMessage('The "prefixItems" parameter must be of ?array<int, \OAS\Schema|bool> type');
        new Schema(prefixItems: $value);
    }

    /**
     * @test
     * @covers \OAS\Schema::setItems
     * @covers \OAS\Schema::getItems
     * @covers \OAS\Schema::hasItems
     * @covers \OAS\Schema::jsonSerialize
     */
    public function schemaIsConstructedWithItemsParameter(): void
    {
        $items = new Schema(type: Type::STRING);
        $schema = new Schema(items: $items);
        self::assertEquals($items, $schema->getItems());
        self::assertTrue($schema->hasItems());
        self::assertJsonStringEqualsJsonString('{"items": {"type": "string"}}', json_encode($schema));

        $items = true;
        $schema = new Schema(items: $items);
        self::assertEquals($items, $schema->getItems());
        self::assertTrue($schema->hasItems());
        self::assertJsonStringEqualsJsonString('{"items": true}', json_encode($schema));
    }

    /**
     * @test
     * @covers \OAS\Schema::setContains
     * @covers \OAS\Schema::getContains
     * @covers \OAS\Schema::hasContains
     * @covers \OAS\Schema::jsonSerialize
     */
    public function schemaIsConstructedWithContainsParameter(): void
    {
        $contains = new Schema(type: Type::NUMBER);
        $schema = new Schema(contains: $contains);
        self::assertEquals($contains, $schema->getContains());
        self::assertTrue($schema->hasContains());
        self::assertJsonStringEqualsJsonString('{"contains": {"type": "number"}}', json_encode($schema));

        $contains = true;
        $schema = new Schema(contains: $contains);
        self::assertEquals($contains, $schema->getContains());
        self::assertTrue($schema->hasContains());
        self::assertJsonStringEqualsJsonString('{"contains": true}', json_encode($schema));
    }

    /**
     * @test
     * @covers \OAS\Schema::setProperties
     * @covers \OAS\Schema::getProperties
     * @covers \OAS\Schema::hasProperties
     * @covers \OAS\Schema::jsonSerialize
     */
    public function schemaIsConstructedWithPropertiesParameter(): void
    {
        $properties = [
            'creditCard' => new Schema(
                properties: [
                    'number' => new Schema(
                        type: Type::STRING,
                        pattern: '^[0-9]{16}$'
                    ),
                    'expiryMonth' => new Schema(
                        type: Type::STRING,
                        pattern: '^[0-9]{2}$'
                    ),
                    'expiryYear' => new Schema(
                        type: Type::STRING,
                        pattern: '^[0-9]{2}$'
                    )
                ],
                type: Type::OBJECT
            ),
            'truthy' => true
        ];
        $schema = new Schema(properties: $properties);
        self::assertEquals($properties, $schema->getProperties());
        self::assertTrue($schema->hasProperties());
        self::assertJsonStringEqualsJsonString(
            <<<SCHEMA
                {
                    "properties": {
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
                        },
                        "truthy": true 
                    }
                }
            SCHEMA,
            json_encode($schema));
    }

    /**
     * @test
     * @covers \OAS\Schema::setProperties
     * @dataProvider propertiesParameterInvalidTypeProvider
     */
    public function errorIsRaisedWhenValueProvidedForPropertiesParameterHasInvalidType(array $value): void
    {
        $this->expectException(TypeError::class);
        $this->expectExceptionMessage('The "properties" parameter must be of ?array<string, \OAS\Schema|bool> type');
        new Schema(properties: $value);
    }

    /**
     * @test
     * @covers \OAS\Schema::setPatternProperties
     * @covers \OAS\Schema::getPatternProperties
     * @covers \OAS\Schema::hasPatternProperties
     * @covers \OAS\Schema::jsonSerialize
     */
    public function schemaIsConstructedWithPatternPropertiesParameter(): void
    {
        $patternProperties = ['^[A-Z]' => new Schema(type: Type::NUMBER), '^[a-z]' => false];
        $schema = new Schema(patternProperties: $patternProperties);
        self::assertEquals($patternProperties, $schema->getPatternProperties());
        self::assertTrue($schema->hasPatternProperties());
        self::assertJsonStringEqualsJsonString(
            <<<SCHEMA
                {
                    "patternProperties": {
                       "^[A-Z]": {
                            "type": "number"
                       },
                       "^[a-z]": false
                    }
                }
            SCHEMA,
            json_encode($schema)
        );
    }

    /**
     * @test
     * @covers \OAS\Schema::setPatternProperties
     * @dataProvider patternPropertiesParameterInvalidTypeProvider
     */
    public function errorIsRaisedWhenValueProvidedForPatternPropertiesParameterHasInvalidType(array $value): void
    {
        $this->expectException(TypeError::class);
        $this->expectExceptionMessage('The "patternProperties" parameter must be of ?array<string, \OAS\Schema|bool> type');
        new Schema(patternProperties: $value);
    }

    /**
     * @test
     * @covers \OAS\Schema::setAdditionalProperties
     * @covers \OAS\Schema::getAdditionalProperties
     * @covers \OAS\Schema::hasAdditionalProperties
     * @covers \OAS\Schema::jsonSerialize
     */
    public function schemaIsConstructedWithAdditionalPropertiesParameter(): void
    {
        $additionalProperties = new Schema(type: Type::NUMBER);
        $schema = new Schema(additionalProperties: $additionalProperties);
        self::assertEquals($additionalProperties, $schema->getAdditionalProperties());
        self::assertTrue($schema->hasAdditionalProperties());
        self::assertJsonStringEqualsJsonString('{"additionalProperties": {"type": "number"}}', json_encode($schema));

        $additionalProperties = true;
        $schema = new Schema(additionalProperties: $additionalProperties);
        self::assertEquals($additionalProperties, $schema->getAdditionalProperties());
        self::assertTrue($schema->hasAdditionalProperties());
        self::assertJsonStringEqualsJsonString('{"additionalProperties": true}', json_encode($schema));
    }

    /**
     * @test
     * @covers \OAS\Schema::setPropertyNames
     * @covers \OAS\Schema::getPropertyNames
     * @covers \OAS\Schema::hasPropertyNames
     * @covers \OAS\Schema::jsonSerialize
     */
    public function schemaIsConstructedWithPropertyNamesParameter(): void
    {
        $propertyNames = new Schema(maxLength: 3);
        $schema = new Schema(propertyNames: $propertyNames);
        self::assertEquals($propertyNames, $schema->getPropertyNames());
        self::assertTrue($schema->hasPropertyNames());
        self::assertJsonStringEqualsJsonString('{"propertyNames": {"maxLength": 3}}', json_encode($schema));

        $propertyNames = true;
        $schema = new Schema(propertyNames: $propertyNames);
        self::assertEquals($propertyNames, $schema->getPropertyNames());
        self::assertTrue($schema->hasPropertyNames());
        self::assertJsonStringEqualsJsonString('{"propertyNames": true}', json_encode($schema));
    }

    // validation

    /**
     * @test
     * @covers \OAS\Schema::setType
     * @covers \OAS\Schema::getType
     * @covers \OAS\Schema::hasType
     * @covers \OAS\Schema::jsonSerialize
     */
    public function schemaIsConstructedWithTypeParameter(): void
    {
        foreach (Type::cases() as $type) {
            self::assertEquals($type, (new Schema(type: $type))->getType());
        }

        $schema = new Schema(type: [Type::STRING, Type::NULL]);
        self::assertEquals([Type::STRING, Type::NULL], $schema->getType());
        self::assertTrue($schema->hasType());
        self::assertJsonStringEqualsJsonString('{"type": ["string","null"]}', json_encode($schema));
    }

    /**
     * @test
     * @covers \OAS\Schema::setType
     * @dataProvider typeParameterInvalidTypeProvider
     */
    public function errorIsRaisedWhenValueProvidedForTypeParameterHasInvalidType(mixed $value): void
    {
        $this->expectException(TypeError::class);
        $this->expectExceptionMessage('The "type" parameter must be of \OAS\Schema\Type|array<int, \OAS\Schema\Type>|null type');
        new Schema(type: $value);
    }

    /**
     * @test
     * @covers \OAS\Schema::setEnum
     * @covers \OAS\Schema::getEnum
     * @covers \OAS\Schema::hasEnum
     * @covers \OAS\Schema::jsonSerialize
     */
    public function schemaIsConstructedWithEnumParameter(): void
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
     * @covers \OAS\Schema::jsonSerialize
     */
    public function schemaIsConstructedWithConstParameter(): void
    {
        $const = 3.1415926535;
        $schema = new Schema(const: $const);
        self::assertEquals($const, $schema->getConst());
        self::assertTrue($schema->hasConst());
        self::assertJsonStringEqualsJsonString('{"const": 3.1415926535}', json_encode($schema));

        $schema = new Schema();
        self::assertJsonStringEqualsJsonString('{}', json_encode($schema));

        $schema = new Schema(const: new NullValue());
        self::assertJsonStringEqualsJsonString('{"const": null}', json_encode($schema));
    }

    /**
     * @test
     * @covers \OAS\Schema::setMultipleOf
     * @covers \OAS\Schema::getMultipleOf
     * @covers \OAS\Schema::hasMultipleOf
     * @covers \OAS\Schema::jsonSerialize
     */
    public function schemaIsConstructedWithMultipleOfParameter(): void
    {
        $multipleOf = 3;
        $schema = new Schema(multipleOf: $multipleOf);
        self::assertEquals($multipleOf, $schema->getMultipleOf());
        self::assertEquals($multipleOf, $schema->hasMultipleOf());
        self::assertJsonStringEqualsJsonString("{\"multipleOf\": $multipleOf}", json_encode($schema));
    }

    /**
     * @test
     * @covers \OAS\Schema::setMultipleOf
     */
    public function errorIsRaisedWhenValueProvidedForMultipleOfParameterInvalid(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The value of "multipleOf" parameter must be positive');
        new Schema(multipleOf: 0);
    }

    /**
     * @test
     * @covers \OAS\Schema::hasMaximum
     * @covers \OAS\Schema::getMaximum
     * @covers \OAS\Schema::jsonSerialize
     */
    public function schemaIsConstructedWithMaximumParameter(): void
    {
        $maximum = 1;
        $schema = new Schema(maximum: $maximum);
        self::assertEquals($maximum, $schema->getMaximum());
        self::assertTrue($schema->hasMaximum());
        self::assertJsonStringEqualsJsonString("{\"maximum\": $maximum}", json_encode($schema));
    }

    /**
     * @test
     * @covers \OAS\Schema::getExclusiveMaximum
     * @covers \OAS\Schema::hasExclusiveMaximum
     * @covers \OAS\Schema::jsonSerialize
     */
    public function schemaIsConstructedWithExclusiveMaximumParameter(): void
    {
        $exclusiveMaximum = 1;
        $schema = new Schema(exclusiveMaximum: $exclusiveMaximum);
        self::assertEquals($exclusiveMaximum, $schema->getExclusiveMaximum());
        self::assertTrue($schema->hasExclusiveMaximum());
        self::assertJsonStringEqualsJsonString("{\"exclusiveMaximum\": $exclusiveMaximum}", json_encode($schema));
    }

    /**
     * @test
     * @covers \OAS\Schema::getMinimum
     * @covers \OAS\Schema::hasMinimum
     * @covers \OAS\Schema::jsonSerialize
     */
    public function schemaIsConstructedWithMinimumParameter(): void
    {
        $minimum = 1;
        $schema = new Schema(minimum: $minimum);
        self::assertEquals($minimum, $schema->getMinimum());
        self::assertTrue($schema->hasMinimum());
        self::assertJsonStringEqualsJsonString("{\"minimum\": $minimum}", json_encode($schema));
    }

    /**
     * @test
     * @covers \OAS\Schema::hasExclusiveMinimum
     * @covers \OAS\Schema::getExclusiveMinimum
     * @covers \OAS\Schema::jsonSerialize
     */
    public function schemaIsConstructedWithExclusiveMinimumParameter(): void
    {
        $exclusiveMinimum = 1;
        $schema = new Schema(exclusiveMinimum: $exclusiveMinimum);
        self::assertEquals($exclusiveMinimum, $schema->getExclusiveMinimum());
        self::assertTrue($schema->hasExclusiveMinimum());
        self::assertJsonStringEqualsJsonString("{\"exclusiveMinimum\": $exclusiveMinimum}", json_encode($schema));
    }

    /**
     * @test
     * @covers \OAS\Schema::setMaxLength
     * @covers \OAS\Schema::getMaxLength
     * @covers \OAS\Schema::hasMaxLength
     * @covers \OAS\Schema::jsonSerialize
     */
    public function schemaIsConstructedWithMaxLengthParameter(): void
    {
        $maxLength = 1;
        $schema = new Schema(maxLength: $maxLength);
        self::assertEquals($maxLength, $schema->getMaxLength());
        self::assertTrue($schema->hasMaxLength());
        self::assertJsonStringEqualsJsonString("{\"maxLength\": $maxLength}", json_encode($schema));
    }

    /**
     * @test
     * @covers \OAS\Schema::setMaxLength
     */
    public function errorIsRaisedWhenValueProvidedForMaxLengthParameterInvalid(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage( 'The value of "maxLength" parameter must be a non negative integer');
        new Schema(maxLength: -1);
    }

    /**
     * @test
     * @covers \OAS\Schema::setMinLength
     * @covers \OAS\Schema::getMinLength
     * @covers \OAS\Schema::hasMinLength
     * @covers \OAS\Schema::jsonSerialize
     */
    public function schemaIsConstructedWithMinLengthParameter(): void
    {
        $minLength = 1;
        $schema = new Schema(minLength: $minLength);
        self::assertEquals($minLength, $schema->getMinLength());
        self::assertTrue($schema->hasMinLength());
        self::assertJsonStringEqualsJsonString("{\"minLength\": $minLength}", json_encode($schema));
    }

    /**
     * @test
     * @covers \OAS\Schema::setMinLength
     */
    public function errorIsRaisedWhenValueProvidedForMinLengthParameterInvalid(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage( 'The value of "minLength" parameter must be a non negative integer');
        new Schema(minLength: -1);
    }

    /**
     * @test
     * @covers \OAS\Schema::getPattern
     * @covers \OAS\Schema::hasPattern
     * @covers \OAS\Schema::jsonSerialize
     */
    public function schemaIsConstructedWithPatternParameter(): void
    {
        $pattern = '^[A-Z]{2}$';
        $schema = new Schema(pattern: $pattern);
        self::assertEquals($pattern, $schema->getPattern());
        self::assertTrue($schema->hasPattern());
        self::assertJsonStringEqualsJsonString('{"pattern": "^[A-Z]{2}$"}', json_encode($schema));
    }

    /**
     * @test
     * @covers \OAS\Schema::setMaxItems
     * @covers \OAS\Schema::getMaxItems
     * @covers \OAS\Schema::hasMaxItems
     * @covers \OAS\Schema::jsonSerialize
     */
    public function schemaIsConstructedWithMaxItemsParameter(): void
    {
        $maxItems = 5;
        $schema = new Schema(maxItems: $maxItems);
        self::assertEquals($maxItems, $schema->getMaxItems());
        self::assertTrue($schema->hasMaxItems());
        self::assertJsonStringEqualsJsonString("{\"maxItems\": $maxItems}", json_encode($schema));
    }

    /**
     * @test
     * @covers \OAS\Schema::setMaxItems
     */
    public function errorIsRaisedWhenValueProvidedForMaxItemsParameterInvalid(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage( 'The value of "maxItems" parameter must be a non negative integer');
        new Schema(maxItems: -1);
    }

    /**
     * @test
     * @covers \OAS\Schema::setMinItems
     * @covers \OAS\Schema::getMinItems
     * @covers \OAS\Schema::hasMinItems
     * @covers \OAS\Schema::jsonSerialize
     */
    public function schemaIsConstructedWithMinItemsParameter(): void
    {
        $minItems = 5;
        $schema = new Schema(minItems: $minItems);
        self::assertEquals($minItems, $schema->getMinItems());
        self::assertTrue($schema->hasMinItems());
        self::assertJsonStringEqualsJsonString("{\"minItems\": $minItems}", json_encode($schema));
    }

    /**
     * @test
     * @covers \OAS\Schema::setMinItems
     */
    public function errorIsRaisedWhenValueProvidedForMinItemsParameterInvalid(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage( 'The value of "minItems" parameter must be a non negative integer');
        new Schema(minItems: -1);
    }

    /**
     * @test
     * @covers \OAS\Schema::getUniqueItems
     * @covers \OAS\Schema::hasUniqueItems
     * @covers \OAS\Schema::jsonSerialize
     */
    public function schemaIsConstructedWithUniqueItemsParameter(): void
    {
        $uniqueItems = true;
        $schema = new Schema(uniqueItems: $uniqueItems);
        self::assertTrue($schema->getUniqueItems());
        self::assertTrue($schema->hasUniqueItems());
        self::assertJsonStringEqualsJsonString("{\"uniqueItems\": true}", json_encode($schema));
    }

    /**
     * @test
     * @covers \OAS\Schema::setMaxContains
     * @covers \OAS\Schema::getMaxContains
     * @covers \OAS\Schema::hasMaxContains
     * @covers \OAS\Schema::jsonSerialize
     */
    public function schemaIsConstructedWithMaxContainsParameter(): void
    {
        $maxContains = 5;
        $schema = new Schema(maxContains: $maxContains);
        self::assertEquals($maxContains, $schema->getMaxContains());
        self::assertTrue($schema->hasMaxContains());
        self::assertJsonStringEqualsJsonString("{\"maxContains\": $maxContains}", json_encode($schema));
    }

    /**
     * @test
     * @covers \OAS\Schema::setMaxContains
     */
    public function errorIsRaisedWhenValueProvidedForMaxContainsParameterInvalid(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage( 'The value of "maxContains" parameter must be a non negative integer');
        new Schema(maxContains: -1);
    }

    /**
     * @test
     * @covers \OAS\Schema::setMinContains
     * @covers \OAS\Schema::getMinContains
     * @covers \OAS\Schema::hasMinContains
     * @covers \OAS\Schema::jsonSerialize
     */
    public function schemaIsConstructedWithMinContainsParameter(): void
    {
        $minContains = 5;
        $schema = new Schema(minContains: $minContains);
        self::assertEquals($minContains, $schema->getMinContains());
        self::assertTrue($schema->hasMinContains());
        self::assertJsonStringEqualsJsonString("{\"minContains\": $minContains}", json_encode($schema));
    }

    /**
     * @test
     * @covers \OAS\Schema::setMinContains
     */
    public function errorIsRaisedWhenValueProvidedForMinContainsParameterInvalid(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage( 'The value of "minContains" parameter must be a non negative integer');
        new Schema(minContains: -1);
    }

    /**
     * @test
     * @covers \OAS\Schema::setMaxProperties
     * @covers \OAS\Schema::getMaxProperties
     * @covers \OAS\Schema::hasMaxProperties
     * @covers \OAS\Schema::jsonSerialize
     */
    public function schemaIsConstructedWithMaxPropertiesParameter(): void
    {
        $maxProperties = 5;
        $schema = new Schema(maxProperties: $maxProperties);
        self::assertEquals($maxProperties, $schema->getMaxProperties());
        self::assertTrue($schema->hasMaxProperties());
        self::assertJsonStringEqualsJsonString("{\"maxProperties\": $maxProperties}", json_encode($schema));
    }

    /**
     * @test
     * @covers \OAS\Schema::setMaxProperties
     */
    public function errorIsRaisedWhenValueProvidedForMaxPropertiesParameterInvalid(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage( 'The value of "maxProperties" parameter must be a non negative integer');
        new Schema(maxProperties: -1);
    }

    /**
     * @test
     * @covers \OAS\Schema::setMinProperties
     * @covers \OAS\Schema::getMinProperties
     * @covers \OAS\Schema::hasMinProperties
     * @covers \OAS\Schema::jsonSerialize
     */
    public function schemaIsConstructedWithMinPropertiesParameter(): void
    {
        $minProperties = 5;
        $schema = new Schema(minProperties: $minProperties);
        self::assertEquals($minProperties, $schema->getMinProperties());
        self::assertTrue($schema->hasMinProperties());
        self::assertJsonStringEqualsJsonString("{\"minProperties\": $minProperties}", json_encode($schema));
    }

    /**
     * @test
     * @covers \OAS\Schema::setMinProperties
     */
    public function errorIsRaisedWhenValueProvidedForMinPropertiesParameterInvalid(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage( 'The value of "minProperties" parameter must be a non negative integer');
        new Schema(minProperties: -1);
    }

    /**
     * @test
     * @covers \OAS\Schema::setRequired
     * @covers \OAS\Schema::getRequired
     * @covers \OAS\Schema::hasRequired
     * @covers \OAS\Schema::jsonSerialize
     */
    public function schemaIsConstructedWithRequiredParameter(): void
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
     * @covers \OAS\Schema::setDependentRequired
     * @covers \OAS\Schema::hasDependentRequired
     * @covers \OAS\Schema::getDependentRequired
     * @covers \OAS\Schema::jsonSerialize
     */
    public function schemaIsConstructedWithDependentRequiredParameter(): void
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
     * @covers \OAS\Schema::getTitle
     * @covers \OAS\Schema::hasTitle
     * @covers \OAS\Schema::jsonSerialize
     */
    public function schemaIsConstructedWithTitleParameter(): void
    {
        $title = 'Some title';
        $schema = new Schema(title: $title);
        self::assertEquals($title, $schema->getTitle());
        self::assertTrue($schema->hasTitle());
        self::assertJsonStringEqualsJsonString(
            "{\"title\": \"$title\"}",
            json_encode($schema)
        );
    }

    /**
     * @test
     * @covers \OAS\Schema::getDescription
     * @covers \OAS\Schema::hasDescription
     * @covers \OAS\Schema::jsonSerialize
     */
    public function schemaIsConstructedWithDescriptionParameter(): void
    {
        $description = 'Some description';
        $schema = new Schema(description: $description);
        self::assertEquals($description, $schema->getDescription());
        self::assertTrue($schema->hasDescription());
        self::assertJsonStringEqualsJsonString(
            "{\"description\": \"$description\"}",
            json_encode($schema)
        );
    }

    /**
     * @test
     * @covers \OAS\Schema::getDefault
     * @covers \OAS\Schema::hasDefault
     * @covers \OAS\Schema::jsonSerialize
     */
    public function schemaIsConstructedWithDefaultParameter(): void
    {
        $default = true;
        $schema = new Schema(default: $default);
        self::assertEquals($default, $schema->getDefault());
        self::assertTrue($schema->hasDefault());
        self::assertJsonStringEqualsJsonString('{"default": true}', json_encode($schema));

        $schema = new Schema();
        self::assertJsonStringEqualsJsonString('{}', json_encode($schema));

        $schema = new Schema(default: new NullValue());
        self::assertJsonStringEqualsJsonString('{"default": null}', json_encode($schema));
    }

    /**
     * @test
     * @covers \OAS\Schema::getDeprecated
     * @covers \OAS\Schema::hasDeprecated
     * @covers \OAS\Schema::isDeprecated
     * @covers \OAS\Schema::jsonSerialize
     */
    public function schemaIsConstructedWithDeprecatedParameter(): void
    {
        $deprecated = true;
        $schema = new Schema(deprecated: $deprecated);
        self::assertEquals($deprecated, $schema->getDeprecated());
        self::assertTrue($schema->hasDeprecated());
        self::assertTrue($schema->isDeprecated());
        self::assertJsonStringEqualsJsonString('{"deprecated": true}', json_encode($schema));

        $schema = new Schema();
        self::assertFalse($schema->isDeprecated());
        self::assertJsonStringEqualsJsonString('{}', json_encode($schema));
    }

    /**
     * @test
     * @covers \OAS\Schema::getReadOnly
     * @covers \OAS\Schema::hasReadOnly
     * @covers \OAS\Schema::isReadOnly
     * @covers \OAS\Schema::jsonSerialize
     */
    public function schemaIsConstructedWithReadOnlyParameter(): void
    {
        $readOnly = true;
        $schema = new Schema(readOnly: $readOnly);
        self::assertEquals($readOnly, $schema->getReadOnly());
        self::assertTrue($schema->hasReadOnly());
        self::assertTrue($schema->isReadOnly());
        self::assertJsonStringEqualsJsonString('{"readOnly": true}', json_encode($schema));

        $schema = new Schema();
        self::assertFalse($schema->isReadOnly());
        self::assertJsonStringEqualsJsonString('{}', json_encode($schema));
    }

    /**
     * @test
     * @covers \OAS\Schema::getWriteOnly
     * @covers \OAS\Schema::hasWriteOnly
     * @covers \OAS\Schema::isWriteOnly
     * @covers \OAS\Schema::jsonSerialize
     */
    public function schemaIsConstructedWithWriteOnlyParameter(): void
    {
        $writeOnly = true;
        $schema = new Schema(writeOnly: $writeOnly);
        self::assertEquals($writeOnly, $schema->getWriteOnly());
        self::assertTrue($schema->hasWriteOnly());
        self::assertTrue($schema->isWriteOnly());
        self::assertJsonStringEqualsJsonString('{"writeOnly": true}', json_encode($schema));

        $schema = new Schema();
        self::assertFalse($schema->isWriteOnly());
        self::assertJsonStringEqualsJsonString('{}', json_encode($schema));
    }

    /**
     * @test
     * @covers \OAS\Schema::getExamples
     * @covers \OAS\Schema::hasExamples
     * @covers \OAS\Schema::jsonSerialize
     */
    public function schemaIsConstructedWithExamplesParameter(): void
    {
        $examples = ['a', 'b', 'c'];
        $schema = new Schema(examples: $examples);
        self::assertEquals($examples, $schema->getExamples());
        self::assertTrue($schema->hasExamples());
        self::assertJsonStringEqualsJsonString('{"examples": ["a", "b", "c"]}', json_encode($schema));
    }

    /**
     * @test
     * @covers \OAS\Schema::setUnevaluatedProperties
     * @covers \OAS\Schema::getUnevaluatedProperties
     * @covers \OAS\Schema::hasUnevaluatedProperties
     * @covers \OAS\Schema::jsonSerialize
     */
    public function schemaIsConstructedWithUnevaluatedPropertiesParameter(): void
    {
        $unevaluatedProperties = new Schema(type: Type::NUMBER);
        $schema = new Schema(unevaluatedProperties: $unevaluatedProperties);
        self::assertEquals($unevaluatedProperties, $schema->getUnevaluatedProperties());
        self::assertTrue($schema->hasUnevaluatedProperties());
        self::assertJsonStringEqualsJsonString('{"unevaluatedProperties": {"type": "number"}}', json_encode($schema));

        $unevaluatedProperties = true;
        $schema = new Schema(unevaluatedProperties: $unevaluatedProperties);
        self::assertEquals($unevaluatedProperties, $schema->getUnevaluatedProperties());
        self::assertTrue($schema->hasUnevaluatedProperties());
        self::assertJsonStringEqualsJsonString('{"unevaluatedProperties": true}', json_encode($schema));
    }

    /**
     * @test
     * @covers \OAS\Schema::setUnevaluatedItems
     * @covers \OAS\Schema::getUnevaluatedItems
     * @covers \OAS\Schema::hasUnevaluatedItems
     * @covers \OAS\Schema::jsonSerialize
     */
    public function schemaIsConstructedWithUnevaluatedItemsParameter(): void
    {
        $unevaluatedItems = new Schema(type: Type::NUMBER);
        $schema = new Schema(unevaluatedItems: $unevaluatedItems);
        self::assertEquals($unevaluatedItems, $schema->getUnevaluatedItems());
        self::assertTrue($schema->hasUnevaluatedItems());
        self::assertJsonStringEqualsJsonString('{"unevaluatedItems": {"type": "number"}}', json_encode($schema));

        $unevaluatedItems = true;
        $schema = new Schema(unevaluatedItems: $unevaluatedItems);
        self::assertEquals($unevaluatedItems, $schema->getUnevaluatedItems());
        self::assertTrue($schema->hasUnevaluatedItems());
        self::assertJsonStringEqualsJsonString('{"unevaluatedItems": true}', json_encode($schema));
    }

    /**
     * @test
     * @coversNothing
     */
    public function arrayAccessInterfaceIsImplemented(): void
    {
        $schema = new Schema(
            properties: [
                'name' => new Schema(
                    type: Type::STRING
                ),
                'age' => new Schema(
                    type: Type::STRING
                )
            ],
            type: Type::OBJECT
        );

        $this->assertEquals(Type::OBJECT, $schema['type']);
        $this->assertEquals(Type::STRING, $schema['properties']['name']['type']);
        $this->assertTrue(isset($schema['properties']['name']['type']));
        $this->assertFalse(isset($schema['properties']['address']['type']));
        $this->assertInstanceOf(Schema::class, $schema['properties']['name']);
    }

    /**
     * @test
     * @covers \OAS\Schema::__invoke
     */
    public function itProvidesAccessToNodesUsingJSONPointer(): void
    {
        $schema = new Schema(
            properties: [
                'name' => new Schema(
                    type: Type::STRING
                ),
                'age' => new Schema(
                    type: Type::NUMBER
                )
            ],
            type: Type::OBJECT
        );

        $this->assertEquals(Type::OBJECT, $schema('/type'));
        $this->assertEquals(Type::STRING, $schema('/properties/name/type'));
        $this->assertInstanceOf(Schema::class, $schema('/properties/name'));
    }

    /**
     * @test
     * @covers \OAS\Schema::getResolvedReference
     * @dataProvider schemaProvider
     */
    public function localReferencesAreResolved(Schema $schema): void
    {
        /** @var Schema $referenceToRoot */
        $referenceToLeaf = $schema->getItems()->getAnyOf()[0];
        $this->assertInstanceOf(Schema::class, $referenceToLeaf);
        $this->assertTrue($referenceToLeaf->hasRef());
        $this->assertEquals('#leaf', $referenceToLeaf->getRef());
        $this->assertEquals($schema->getDefs()['leaf'], $referenceToLeaf->getResolvedReference());

        /** @var Schema $referenceToRoot */
        $referenceToRoot = $schema->getItems()->getAnyOf()[1];
        $this->assertInstanceOf(Schema::class, $referenceToRoot);
        $this->assertTrue($referenceToRoot->hasRef());
        $this->assertEquals('#/', $referenceToRoot->getRef());
        $this->assertSame($schema, $referenceToRoot->getResolvedReference());
    }

    public static function propertiesParameterInvalidTypeProvider(): iterable
    {
        yield ['value' => [new Schema()]];
        yield ['value' => ['prop' => [new Schema()]]];
        yield ['value' => ['prop' => new stdClass()]];
    }

    public static function patternPropertiesParameterInvalidTypeProvider(): iterable
    {
        yield ['value' => [new Schema()]];
        yield ['value' => ['prop' => [new Schema()]]];
        yield ['value' => ['prop' => new stdClass()]];
    }

    public static function dependentSchemasParameterInvalidTypeProvider(): iterable
    {
        yield ['value' => [new Schema()]];
        yield ['value' => ['prop' => [new Schema()]]];
        yield ['value' => ['prop' => new stdClass()]];
    }

    public static function prefixItemsParameterInvalidTypeProvider(): iterable
    {
        yield ['value' => [new Schema(), null]];
        yield ['value' => ['a' => new Schema(), 'b' => new Schema()]];
    }

    public static function typeParameterInvalidTypeProvider(): iterable
    {
        yield ['value' => [Type::STRING, true]];
        yield ['value' => ['type1' => Type::STRING, 'type2' => Type::BOOLEAN]];
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

    public static function schemaProvider(): iterable
    {
        yield [
            new Schema(
                _defs: [
                  'leaf' => new Schema(
                      _anchor: 'leaf',
                      type: Type::INTEGER
                  ),
                ],
                items: new Schema(
                    anyOf: [
                        new Schema(_ref: '#leaf'),
                        new Schema(_ref: '#/')
                    ]
                ),
                type: Schema\Type::ARRAY
            )
        ];
    }
}
