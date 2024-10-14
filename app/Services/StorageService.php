<?php

namespace App\Services;

use App\Exceptions\EmptyFileException;
use App\Jobs\UploadExpire;
use App\Models\Upload;
use ByteUnits\Binary;
use ByteUnits\System;
use GuzzleHttp\Psr7\Stream;
use GuzzleHttp\Psr7\Utils;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Psr\Http\Message\StreamInterface;

class StorageService
{
    /**
     * @return System Size in bytes
     */
    public function calculateStorageUsedPerIp(string $ip): System
    {
        $bytes = DB::table('uploads')
            ->where('ip_address', $ip)
            ->where('deleted', false)
            ->sum('size');

        return Binary::bytes($bytes);
    }

    public function calculateStorageUsed(): System
    {
        $bytes = DB::table('uploads')
            ->where('deleted', false)
            ->sum('size');

        return Binary::bytes($bytes);
    }

    public function getMaxFileSize(): System
    {
        $maxFileSize = ini_get('upload_max_filesize');
        $maxFileSize = Binary::bytes($this->parseIniSize($maxFileSize));

        $maxPostSize = ini_get('post_max_size');
        $maxPostSize = Binary::bytes($this->parseIniSize($maxPostSize));

        if ($maxPostSize->isLessThan($maxFileSize)) {
            return $maxPostSize;
        }

        return $maxFileSize;
    }

    /**
     * @throws EmptyFileException
     */
    public function upload(UploadedFile|Request $file, string $filename, string $ip, ?string $related_session_id): Upload
    {
        $stream = $file instanceof UploadedFile ? new Stream(fopen($file->getPath(), 'r')) : $this->requestToStream($file);

        if ($stream->getSize() === 0) {
            throw new EmptyFileException;
        }

        $upload = new Upload;
        $upload->filename = $filename;
        $upload->size = $stream->getSize();
        $upload->ip_address = $ip;
        $upload->session_id = $related_session_id;
        $upload->save();

        UploadExpire::dispatch($upload)->delay(now()->addHour());

        Storage::put($upload->getFilePath(), $stream);

        return $upload;
    }

    private function requestToStream(Request $request): StreamInterface
    {
        $content = Utils::streamFor(
            $request->getContent(asResource: true)
        );

        $stream = Utils::streamFor(tmpfile());

        Utils::copyToStream($content, $stream);

        return $stream;
    }

    /**
     * From https://github.com/sbolch/max-upload-file-size/blob/main/MaxUploadFileSizeGetter.php
     */
    private function parseIniSize($size): int
    {
        $unit = preg_replace('/[^bkmgtpezy]/i', '', $size);
        $size = preg_replace('/\D/', '', $size);

        if($unit) {
            return (int) floor($size * pow(1024, stripos('bkmgtpezy', $unit[0])));
        }

        return (int) floor($size);
    }
}
