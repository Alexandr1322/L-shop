<?php

namespace App\Repositories;

use App\Models\News;

/**
 * Class NewsRepository
 *
 * @author  D3lph1 <d3lph1.contact@gmail.com>
 *
 * @package App\Repositories
 */
class NewsRepository extends BaseRepository
{
    const MODEL = News::class;

    /**
     * Get first portion of the news.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getFirstPortion()
    {
        return \Cache::get('news', function () {
            $result = News::select(['id', 'title', 'content'])
                ->orderBy('created_at', 'DESC')
                ->limit(s_get('news.first_portion', 15))
                ->get();

            \Cache::add('news', $result, 1);

            return $result;
        });
    }

    /**
     * Get all news count.
     *
     * @return int
     */
    public function count()
    {
        return \Cache::get('news.count', function () {
            $result = News::count();

            \Cache::add('news.count', $result, 1);

            return $result;
        });
    }

    /**
     * Load more news.
     *
     * @param int $count Count of news for load.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function load($count)
    {
        return News::select(['id', 'title', 'content'])
            ->orderBy('created_at', 'DESC')
            ->offset($count)
            ->limit(s_get('news.per_page', 15))
            ->get();
    }

    /**
     * Retrieve and paginate news.
     *
     * @param int   $perPage Count of news in one pagination page.
     * @param array $columns Columns for sampling.
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function paginate($perPage, $columns = [])
    {
        $columns = $this->prepareColumns($columns);

        return News::select($columns)->orderBy('created_at', 'DESC')->paginate($perPage);
    }
}
