<?php

namespace App\Jobs;

use App\Models\Upload;
use App\Services\StorageService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class UploadExpire implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly Upload $upload
    ) {}

    public function handle(StorageService $storageService): void
    {
        $storageService->delete($this->upload);
    }
}
