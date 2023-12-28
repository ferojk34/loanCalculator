<?php

namespace Modules\Core\Repositories;

use Exception;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Modules\Core\Facades\CoreCache;
use Modules\Core\Traits\Filterable;
use Illuminate\Support\Facades\Event;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Modules\Core\Exceptions\DeleteUnauthorized;

class BaseRepository
{
    use Filterable;

    protected object $model;
    protected ?string $model_key;
    protected ?string $model_name;
    protected array $rules = [];
    protected array $relationships;
    protected bool $restrict_default_delete = false;
    public int $pagination_limit = 25;
    public bool $without_pagination = false;

    public function model(): Model
    {
        return $this->model;
    }

    public function validateData(
        object $request,
        array $merge = [],
        ?callable $callback = null
       ): array
       {
        try {
            $data = $request->validate(
                array_merge($this->rules, $merge)
            );
            $append_data = $callback ? $callback($request) : [];
        }catch (Exception $exception){
            throw $exception;
        }
        return array_merge($data, $append_data);
    }

    public function fetchAll(object $request, array $with = [], ?callable $callback = null): object
    {
        try
        {
            $this->validateListFiltering($request);
            $rows = ($callback) ? $callback() : null;

            $fetched = $this->getFilteredList($request, $with, $rows);
        }
        catch( Exception $exception )
        {
            throw $exception;
        }

        return $fetched;
    }

    public function fetch(int $id, array $with = [], ?callable $callback = null): object
    {
        try
        {
            $rows = $this->model;
            if ($callback) {
                $rows = $callback();
            }
            if ($with !== []) {
                $rows = $rows->with($with);
            }

            $fetched = $rows->findOrFail($id);
        }
        catch (Exception $exception)
        {
            throw $exception;
        }

        return $fetched;
    }

    public function queryFetch(array $condition, array $with = [], ?callable $callback = null): ?object
    {
        try
        {
            $rows = $this->model::where($condition);
            $rows = $callback ? $callback($rows) : $rows;

            if ($with !== []) {
                $rows = $rows->with($with);
            }

            $fetched = $rows->first();
        }
        catch (Exception $exception)
        {
            throw $exception;
        }

        return $fetched;
    }

    public function query(callable $callback): mixed
    {
        DB::beginTransaction();

        try
        {
            $query = $this->model::query();
            $query = $callback($query);
        }
        catch (Exception $exception)
        {
            DB::rollBack();
            throw $exception;
        }

        DB::commit();

        return $query;
    }

    public function relationships(int $id, object $request, ?callable $callback = null): object
    {
        Event::dispatch("{$this->model_key}.fetch-single-relationships.before");

        try
        {
            $relationships = $request->relationships ?? $this->relationships;
            $relationships = is_array($relationships) ? $relationships : [];

            $fetched = $this->model->whereId($id)->with($relationships)->firstOrFail();
            if ($callback) {
                $callback($fetched);
            }
        }
        catch (Exception $exception)
        {
            throw $exception;
        }

        Event::dispatch("{$this->model_key}.fetch-single-relationships.after", $fetched);

        return $fetched;
    }

    public function create(array $data, ?callable $callback = null): object
    {
        DB::beginTransaction();

        try
        {
            $created = $this->model->create($data);
            if ($callback) {
                $callback($created);
            }
        }
        catch (Exception $exception)
        {
            DB::rollBack();
            throw $exception;
        }

        DB::commit();

        return $created;
    }

    public function createOrUpdate(array $match, array $data, ?callable $callback = null): object
    {
        DB::beginTransaction();
        Event::dispatch("{$this->model_key}.createOrUpdate.before");

        try
        {
            $createOrUpdate = $this->model->updateOrCreate($match, $data);
            if ($callback) {
                $callback($createOrUpdate);
            }
        }
        catch (Exception $exception)
        {
            DB::rollBack();
            throw $exception;
        }

        Event::dispatch("{$this->model_key}.createOrUpdate.after", $createOrUpdate);
        DB::commit();

        return $createOrUpdate;
    }

    public function firstOrCreate(array $data, ?callable $callback = null): object
    {
        Event::dispatch("{$this->model_key}.query-firstOrCreate.before");
        DB::beginTransaction();

        try
        {
            $created = $this->model->firstOrCreate($data);
            if ($callback) {
                $callback($created);
            }
        }
        catch (Exception $exception)
        {
            DB::rollBack();
            throw $exception;
        }

        Event::dispatch("{$this->model_key}.query-firstOrCreate.after", $created);
        DB::commit();

        return $created;
    }

    public function update(array $data, int|string $id, ?callable $callback = null): object
    {
        DB::beginTransaction();

        try
        {
            $updated = $this->model->findOrFail($id);
            $updated->fill($data);
            $updated->save();

            if ($callback) {
                $callback($updated);
            }
        }
        catch (Exception $exception)
        {
            DB::rollBack();
            throw $exception;
        }

        DB::commit();

        return $updated;
    }

    public function delete(int|string $id, ?callable $callback = null): object
    {
        DB::beginTransaction();

        try
        {
            $deleted = $this->model->findOrFail($id);
            if ($callback) {
                $callback($deleted);
            }
            $deleted->delete();
        }
        catch (Exception $exception)
        {
            DB::rollBack();
            throw $exception;
        }

        DB::commit();

        return $deleted;
    }

    public function bulkDelete(object $request, ?callable $callback = null): object
    {
        DB::beginTransaction();
        Event::dispatch("{$this->model_key}.delete.before");

        try
        {
            $request->validate([
                "ids" => "array|required",
                "ids.*" => "required|exists:{$this->model->getTable()},id",
            ]);

            $deleted = $this->model->whereIn("id", $request->ids);
            if ($callback) {
                $callback($deleted);
            }
            $deleted->delete();
        }
        catch (Exception $exception)
        {
            DB::rollBack();
            throw $exception;
        }

        Event::dispatch("{$this->model_key}.delete.after", $deleted);
        DB::commit();

        return $deleted;
    }

    public function updateStatus(object $request, int $id, ?callable $callback = null): object
    {
        DB::beginTransaction();
        Event::dispatch("{$this->model_key}.update-status.before");

        try
        {
            $data = $request->validate([
                "status" => "sometimes|boolean"
            ]);

            $updated = $this->model->findOrFail($id);
            $data["status"] = $data["status"] ?? !$updated->status;
            $data["status"] = (bool) $data["status"];

            $updated->fill($data);
            $updated->save();

            if ($callback) {
                $callback($updated);
            }
        }
        catch (Exception $exception)
        {
            DB::rollBack();
            throw $exception;
        }

        Event::dispatch("{$this->model_key}.update-status.after", $updated);
        DB::commit();

        return $updated;
    }

    public function storeScopeImage(object $request, ?string $folder = null, ?string $delete_url = null): string
    {
        try
        {
            // Store File
            $key = (string) Str::uuid();
            $folder = $folder ?? "default";
            $file_path = $request->storeAs("images/{$folder}/{$key}", $this->generateFileName($request));

            // Delete old file if requested
            if ( $delete_url !== null ) {
                Storage::delete($delete_url);
            }
        }
        catch (Exception $exception)
        {
            throw $exception;
        }

        return $file_path;
    }

    public function generateFileName(object $file): string
    {
        try
        {
            $original_filename = $file->getClientOriginalName();
            $name = pathinfo($original_filename, PATHINFO_FILENAME);
            $extension = pathinfo($original_filename, PATHINFO_EXTENSION);

            $filename_slug = Str::slug($name);
            $filename = "{$filename_slug}.{$extension}";
        }
        catch (Exception $exception)
        {
            throw $exception;
        }

        return (string) $filename;
    }
}
