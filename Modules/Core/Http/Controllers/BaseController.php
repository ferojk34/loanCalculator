<?php

namespace Modules\Core\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Modules\Core\Traits\ApiResponseFormat;
use Exception;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Modules\Core\Traits\HasExceptionHandler;

class BaseController extends Controller
{
    use ApiResponseFormat;
    use HasExceptionHandler;

    // public $model;
    // public string $model_name;
    // public array $exception_statuses;

    // public function __construct(
    //     ?object $model,
    //     ?string $model_name,
    //     array $exception_statuses = []
    // ) {

    //     $this->model = $model;
    //     $this->model_name = $model_name ?? str_replace("Controller", "", class_basename($this));
    //     $this->exception_statuses = array_merge($exception_statuses, [
    //         ModelNotFoundException::class => Response::HTTP_NOT_FOUND,
    //     ]);
    // }

    public function storeImage(
        object $request,
        string $file_name,
        ?string $folder = null,
        ?string $delete_url = null
    ): ?string {
        try
        {
            // Check if file is given
            if ($request->file($file_name) !== null) {
                // Store File
                $file = $request->file($file_name);
                $key = Str::random(6);
                $folder = $folder ?? "default";
                $file_path = $file->storeAs("images/{$folder}/{$key}", $this->generateFileName($file));

                // Delete old file if requested
                if ($delete_url !== null) {
                    Storage::delete($delete_url);
                }
            }
        }
        catch (Exception $exception)
        {
            throw $exception;
        }

        return $file_path ?? null;
    }

    public function handleException(object $exception): JsonResponse
    {
        return $this->errorResponse(
            message: $this->getExceptionMessage($exception),
            response_code: $this->getExceptionStatus($exception)
            );
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
