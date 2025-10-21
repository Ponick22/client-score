<?php

namespace App\Util\Collection;

use ArrayObject;
use Iterator;
use LogicException;

/**
 * @template T
 * @template TKey
 */
abstract class ArrayCollectionAbstract extends ArrayObject
{
    abstract public function getClass(): string;

    public function __construct(array $array = [])
    {
        foreach ($array as $item) {
            $this->checkClass($item);
        }

        parent::__construct($array);
    }

    /**
     * @param TKey $key
     * @return T|null
     */
    public function get(mixed $key): ?object
    {
        return $this->offsetExists($key) ? $this->offsetGet($key) : null;
    }

    /**
     * @return T|null
     */
    public function getFirst(): ?object
    {
        $array = $this->getArrayCopy();

        return reset($array) ?: null;
    }

    /**
     * @return T|null
     */
    public function getLast(): ?object
    {
        $array = $this->getArrayCopy();

        return end($array) ?: null;
    }

    /**
     * @param T $value
     */
    public function add(mixed $value, int|string|null $key = null): static
    {
        $this->offsetSet($key, $value);

        return $this;
    }

    /**
     * @param TKey $key
     */
    public function remove(mixed $key): static
    {
        $this->offsetUnset($key);

        return $this;
    }

    /**
     * @param T[] $array
     */
    public function fromArray(array $array): static
    {
        foreach ($array as $item) {
            $this->checkClass($item);
        }

        $this->exchangeArray($array);

        return $this;
    }

    /**
     * @return T[]
     */
    public function toArray(): array
    {
        return $this->getArrayCopy();
    }

    /**
     * @param TKey $key
     * @param T $value
     */
    public function offsetSet(mixed $key, mixed $value): void
    {
        $this->checkClass($value);

        parent::offsetSet($key, $value);
    }

    /**
     * @param TKey $key
     * @return T
     */
    public function offsetGet(mixed $key): mixed
    {
        return parent::offsetGet($key);
    }

    /**
     * @return Iterator<T>
     */
    public function getIterator(): Iterator
    {
        return parent::getIterator();
    }

    protected function checkClass(object $value): void
    {
        $class = $this->getClass();

        if (!$value instanceof $class) {
            throw new LogicException(sprintf('Expected "%s"', $class));
        }
    }
}
