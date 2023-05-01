<?php

namespace SicrediAPI\Resources\Meta;

use SicrediAPI\Resources\ResourceAbstract;
use GuzzleHttp\Exception\GuzzleException;

class Paginator implements \Iterator
{
    /**
     * Global position of the paginator
     * @var int
     */
    private $position = 0;

    /**
     * Current page of the paginator
     * @var int
     */
    private $page;

    /**
     * Resource to paginate
     * @var ResourceAbstract
     */
    private $resource;

    /**
     * Callable which returns the items of the next page
     * @var callable
     */
    private $nextPage;

    /**
     * Callable which transforms the items of the response
     * @var callable
     */
    private $transformItems;

    /**
     * Arguments to pass to the callable
     * @var array
     */
    private $nextPageArgs;

    /**
     * If we should look for more pages
     * @var bool
     */
    private $hasNextPage;

    /**
     * Agregation of all items
     * @var array
     */
    private $items = [];

    public function __construct(ResourceAbstract $resource, callable $nextPage, callable $transformItems = null)
    {
        $this->position = 0;
        $this->page = 1;

        $this->resource = $resource;
        $this->nextPage = $nextPage;
        $this->transformItems = $transformItems;
    }

    public function rewind(): void
    {
        $this->position = 0;
        $this->page = 1;
        $this->loadItems();
    }

    public function current()
    {
        return $this->items[$this->position];
    }

    public function key()
    {
        return $this->position;
    }

    public function next(): void
    {
        ++$this->position;
    }

    public function valid(): bool
    {
        // If we have items in the current page, return true
        if (isset($this->items[$this->position])) {
            return true;
        }

        // If we don't have items in the current page, but we have more pages to look for, look for them
        if ($this->hasNextPage) {
            $this->page++;
            $this->loadItems();
        }

        return isset($this->items[$this->position]);
    }

    /**
     * Loads the items of the next page
     * @return void
     * @throws GuzzleException
     */
    private function loadItems()
    {

        $response = call_user_func_array($this->nextPage, [$this->page]);

        // If the response is empty, we don't have more pages to look for
        if (empty($response)) {
            $this->hasNextPage = false;
            return;
        }

        // Append the items to the current items
        $this->items = array_merge($this->items, $this->transformItems($response));

        $this->hasNextPage = $response['hasNext'];
    }

    /**
     * Transforms the items of the response into the desired format
     * @param array $response The array obtained from the API call
     * @return array
     */
    private function transformItems(array $response)
    {

        if ($this->transformItems !== null) {
            return call_user_func_array($this->transformItems, [$response['items']]);
        }

        return $response['items'];
    }
}
