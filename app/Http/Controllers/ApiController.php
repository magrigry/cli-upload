<?php

namespace App\Http\Controllers;

use App\Jobs\UploadExpire;
use App\Models\Upload;
use App\Services\StorageService;
use ByteUnits\Metric;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

use function route;

class ApiController
{
    public function upload(Request $request, string $filename, StorageService $storageService): string
    {
        $upload = $storageService->upload(
            file: $request,
            filename: $filename,
            ip: $request->ip(),
            related_session_id: null
        );

        return route('api.download', [
            'filename' => $filename,
            'upload' => $upload,
        ]);
    }

    public function download(Upload $upload, ?string $filename = null): StreamedResponse
    {
        $filename = $filename ?? $upload->filename;
        $file = $upload->getFilePath();

        if (! $upload->deleted && Storage::exists($file)) {
            return Storage::download($file, $filename);
        }

        abort(404);
    }

    public function delete(Upload $upload): void
    {
        if (Metric::bytes($upload->size)->isGreaterThan(Metric::parse('300MB'))) {
            UploadExpire::dispatch($upload)->delay(now());

            return;
        }

        UploadExpire::dispatchAfterResponse($upload);
    }
}
