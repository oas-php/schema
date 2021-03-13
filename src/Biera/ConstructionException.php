<?php declare(strict_types=1);

namespace OAS\Biera;

class ConstructionException extends \RuntimeException
{
    private string $path;
    private array $errors;

    public function __construct(string $path, array $errors = [], \Throwable $previous = null)
    {
        if (empty($errors) && !$previous instanceof ConstructionException) {
            throw new \LogicException(
                sprintf(
                    'Parameter "errors" must not be empty when "previous" is not of %s type',
                    __CLASS__
                )
            );
        }

        $this->path = $path;
        $this->errors = $errors;

        parent::__construct('', 0, $previous);
    }

    public function getPath(): string
    {
        $path = [$this->path];
        $previous = $this->getPrevious();

        while ($previous instanceof ConstructionException) {
            $path[] = $previous->getPath();
            $previous = $previous->getPrevious();
        }

        return join(' -> ', $path);
    }

    public function getErrors(): array
    {
        $deepest = $this;

        while (($previous = $deepest->getPrevious()) instanceof ConstructionException) {
            $deepest = $previous;
        }

        return $deepest->errors;
    }
}

}
