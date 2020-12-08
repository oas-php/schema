<?php declare(strict_types=1);

namespace OAS\Schema\Vocabulary;

use OAS\Schema;
use function iter\all;
use function iter\func\operator;

trait Core
{
    private ?string $_id;

    private ?string $_schema;

    private ?string $_anchor;

    private ?string $_ref;

    private ?string $_recursiveRef;

    private ?bool $_recursiveAnchor;

    private ?array $_vocabulary;

    private ?string $_comment;

    /** @var ?Schema[] */
    private ?array $_defs;

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

    public function hasRecursiveRef(): bool
    {
        return !is_null($this->_recursiveRef);
    }

    public function getRecursiveRef(): ?string
    {
        return $this->_recursiveRef;
    }

    public function hasRecursiveAnchor(): bool
    {
        return !is_null($this->_recursiveAnchor);
    }

    public function getRecursiveAnchor(): ?bool
    {
        return $this->_recursiveAnchor;
    }

    public function isRecursiveAnchor(): ?bool
    {
        return (bool) $this->_recursiveAnchor;
    }

    public function hasVocabulary(): bool
    {
        return !is_null($this->schema()->_vocabulary);
    }

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

    private function setDefs(?array $_defs): void
    {
        if (!is_null($_defs)) {
            if (!all(operator('instanceof', Schema::class), $_defs)) {
                throw new \TypeError(
                    'Parameter "_defs" must be of ?\OAS\Schema[] type'
                );
            }

            $this->setChildren($_defs);
        }

        $this->_defs = $_defs;
    }

    public function hasDefs(): bool
    {
        return !is_null($this->schema()->_defs);
    }

    /**
     * @return Schema[]|null
     */
    public function getDefs(): ?array
    {
        return $this->schema()->_defs;
    }
}
