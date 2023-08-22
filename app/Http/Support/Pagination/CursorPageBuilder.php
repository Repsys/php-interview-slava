<?php

namespace App\Http\Support\Pagination;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\Cursor;
use Illuminate\Pagination\CursorPaginator;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use UnexpectedValueException;

class CursorPageBuilder extends AbstractPageBuilder
{
    public function build(): Page
    {
        $limit = $this->applyMaxLimit((int) $this->request->input('pagination.limit', $this->getDefaultLimit()));

        return $limit > 0
            ? $this->buildWithPositiveLimit($limit)
            : $this->buildWithNotPositiveLimit($limit);
    }

    protected function buildWithPositiveLimit(int $limit): Page
    {
        $cursorHash = $this->request->input('pagination.cursor', null);
        $cursorHash = $cursorHash === '' ? null : $cursorHash;

        $cursor = Cursor::fromEncoded($cursorHash);
        if ($cursorHash !== null && $cursor === null) {
            throw new BadRequestHttpException("Unable to decode pagination cursor");
        }

        try {
            /** @var $paginator CursorPaginator */
            $paginator = $this->query->cursorPaginate($limit, cursor: $cursor);
        } catch (UnexpectedValueException $e) {
            throw new BadRequestHttpException("Invalid pagination cursor: {$e->getMessage()}");
        }

        return new Page($paginator->items(), [
            'cursor' => $cursorHash,
            'limit' => $limit,
            'next_cursor' => $paginator->nextCursor()?->encode(),
            'previous_cursor' => $paginator->previousCursor()?->encode(),
            'type' => PaginationTypeEnum::CURSOR->value,
        ]);
    }

    protected function buildWithNotPositiveLimit(int $limit): Page
    {
        $collection = $limit < 0 && !$this->forbidToBypassPagination ? $this->query->get() : new Collection();

        return new Page($collection, [
            'cursor' => null,
            'limit' => $limit,
            'next_cursor' => null,
            'previous_cursor' => null,
            'type' => PaginationTypeEnum::CURSOR->value,
        ]);
    }
}
