<?php

namespace App\Jobs;

use App\Models\Upload;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;

class UploadExpire implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly Upload $upload
    ) {}

    public function handle(): void
    {
        if ($this->upload->deleted) {
            return;
        }

        if (Storage::exists($this->upload->getFilePath())) {
            Storage::delete($this->upload->getFilePath());
        }

        $this->upload->deleted = true;
        $this->upload->save();
    }
}
