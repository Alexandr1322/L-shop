<?php

namespace App\Repositories;

use App\Exceptions\InvalidArgumentTypeException;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class BaseRepository
 * Parent repository class for concrete repositories.
 *
 * @author  D3lph1 <d3lph1.contact@gmail.com>
 *
 * @package App\Repositories
 */
abstract class BaseRepository
{
    /**
     * Find record by identifier.
     *
     * @param int   $id      Record identifier.
     * @param array $columns Columns for sampling.
     *
     * @throws InvalidArgumentTypeException
     *
     * @return mixed
     */
    public function find($id, $columns = [])
    {
        $columns = $this->prepareColumns($columns);

        if (!is_int($id)) {
            throw new InvalidArgumentTypeException('integer', $id);
        }

        return $this->query()->find($id, $columns);
    }

    /**
     * Get all rows where id contains in array
     *
     * @param array $ids
     * @param array $columns
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function whereIdIn(array $ids, array $columns = [])
    {
        $columns = $this->prepareColumns($columns);

        return $this->query()->select($columns)->whereIn('id', $ids)->get();
    }

    /**
     * Get all records.
     *
     * @param array $columns
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function all($columns = [])
    {
        $columns = $this->prepareColumns($columns);

        return $this->query()->select($columns)->get();
    }

    /**
     * Create new record.
     *
     * @param array $attributes
     *
     * @return mixed
     */
    public function create(array $attributes)
    {
        return $this->query()->create($attributes);
    }

    /**
     * Update record by identifier.
     *
     * @param int   $id         Record identifier.
     * @param array $attributes Updated record attributes.
     *
     * @throws InvalidArgumentTypeException
     *
     * @return bool
     */
    public function update($id, array $attributes)
    {
        if (!is_int($id)) {
            throw new InvalidArgumentTypeException('integer', $id);
        }

        return $this->query()->where('id', $id)
            ->update($attributes);
    }

    /**
     * Delete record by identifier.
     *
     * @param int|array $id Record identifier(s).
     *
     * @throws InvalidArgumentTypeException
     *
     * @return bool
     */
    public function delete($id)
    {
        if (!(is_int($id) or is_array($id))) {
            throw new InvalidArgumentTypeException('integer', $id);
        }

        if (is_array($id)) {
            return $this->query()->whereIn('id', $id)->delete();
        }

        return $this->query()->where('id', $id)->delete();
    }

    /**
     * Checks record for existence by identifier.
     *
     * @param int $id Record identifier.
     *
     * @throws InvalidArgumentTypeException
     *
     * @return bool
     */
    public function exists($id)
    {
        if (!is_int($id)) {
            throw new InvalidArgumentTypeException('integer', $id);
        }

        return $this->query()->where('id', $id)->exists();
    }

    /**
     * Checking column(s) on a valid type.
     *
     * @param null|string|array $columns
     *
     * @throws InvalidArgumentTypeException
     *
     * @return mixed
     */
    protected function prepareColumns($columns = null)
    {
        if (is_null($columns)) {
            // It is all columns. (SELECT * FROM ...)
            return ['*'];
        }

        if (is_string($columns)) {
            // It is single column. (SELECT `column` FROM ...)
            return $columns;
        }

        if (is_array($columns)) {
            if (count($columns) === 0) {
                // It is all columns. (SELECT * FROM ...)
                return ['*'];
            }

            return $columns;
        }

        throw new InvalidArgumentTypeException(['string', 'array'], $columns);
    }

    /**
     * Call static method query() on model.
     *
     * @return Builder
     */
    protected function query()
    {
        return call_user_func(static::MODEL . '::query');
    }
}
