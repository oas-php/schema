<?php declare(strict_types=1);

namespace OAS\Schema\Vocabulary;

use OAS\Schema;
use TypeError;
use function iter\all;
use function iter\func\operator;
use function OAS\Utils\assertTypeValid;

trait Core
{
    private ?string $_id;
    private ?string $_schema;
    private ?string $_anchor;
    private ?string $_ref;
    private ?string $_dynamicRef;
    private ?string $_dynamicAnchor;
    private ?array $_vocabulary = null;
    private ?string $_comment;
    /** @var ?array<string, Schema> $_defs */
    private ?array $_defs = null;

    public function hasId(): bool
    {
        return !is_null($this->schema()->_id);
    }

    public function getId(): ?string
    {
        return $this->schema()->_id;
    }

    public function hasSchema(): bool
    {
        return !is_null($this->schema()->_schema);
    }

    public function getSchema(): ?string
    {
        return $this->schema()->_schema;
    }

    public function hasAnchor(): bool
    {
        return !is_null($this->schema()->_anchor);
    }

    public function getAnchor(): ?string
    {
        return $this->schema()->_anchor;
    }

    public function hasRef(): bool
    {
        return !is_null($this->_ref);
    }

    public function getRef(): ?string
    {
        return $this->_ref;
    }

    public function hasDynamicRef(): bool
    {
        return !is_null($this->_dynamicRef);
    }

    public function getDynamicRef(): ?string
    {
        return $this->_dynamicRef;
    }

    public function hasDynamicAnchor(): bool
    {
        return !is_null($this->_dynamicAnchor);
    }

    public function getDynamicAnchor(): ?string
    {
        return $this->_dynamicAnchor;
    }

    /**
     * @param array<int, string> $_vocabulary
     */
    public function setVocabulary(array $_vocabulary): void
    {
        assertTypeValid('array<string, boolean>', $_vocabulary, '_vocabulary');

        $this->_vocabulary = $_vocabulary;
    }

    public function hasVocabulary(): bool
    {
        return !is_null($this->schema()->_vocabulary);
    }

    /**
     * @return ?array<int, string>
     */
    public function getVocabulary(): ?array
    {
        return $this->schema()->_vocabulary;
    }

    public function hasComment(): bool
    {
        return !is_null($this->schema()->_comment);
    }

    public function getComment(): ?string
    {
        return $this->schema()->_comment;
    }

    /**
     * @param array<string, \OAS\Schema> $_defs
     */
    private function setDefs(array $_defs): void
    {
        assertTypeValid('array<string, \OAS\Schema>', $_defs, '_defs');

        $this->setChildren($_defs);
        $this->_defs = $_defs;
    }

    public function hasDefs(): bool
    {
        return !is_null($this->schema()->_defs);
    }

    /**
     * @return ?array<string, \OAS\Schema>
     */
    public function getDefs(): ?array
    {
        return $this->schema()->_defs;
    }
}
