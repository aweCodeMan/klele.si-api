<?php

namespace App\Http\Paginators;

use Illuminate\Pagination\LengthAwarePaginator;

class CustomLengthAwarePaginator extends LengthAwarePaginator
{
    public function toArray()
    {
        return [
            'currentPage' => $this->currentPage(),
            'data' => $this->items->toArray(),
            'from' => $this->firstItem(),
            'perPage' => $this->perPage(),
            'to' => $this->lastItem(),
            'total' => $this->total(),

            //'first_page_url' => $this->url(1),
            //'last_page' => $this->lastPage(),
            //'last_page_url' => $this->url($this->lastPage()),
            //'links' => $this->linkCollection()->toArray(),
            'nextPageUrl' => $this->nextPageUrl(),
            //'path' => $this->path(),
            'prevPageUrl' => $this->previousPageUrl(),
        ];
    }
}
