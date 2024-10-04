<?php

namespace Tests\Feature;

use App\Jobs\UploadExpire;
use Bus;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class UploadApiTest extends TestCase
{
    public function test_upload_and_download_file(): void
    {
        Bus::fake();

        $content = UploadedFile::fake()->image('myimage.jpg')->getContent();
        $filename = 'myimage.jpg';

        $response = $this->call('PUT', route('api.upload', ['filename' => $filename]), content: $content);

        $response->assertOk();

        $download_url = $response->getContent();

        $response = $this->get($download_url);
        $response->assertOk();
        $response->assertDownload($filename);

        Bus::assertDispatched(UploadExpire::class, function (UploadExpire $job) {
            /** @var Carbon $delay */
            $delay = $job->delay;

            return $delay->isSameMinute(Carbon::now()->addHour());
        });
    }

    public function test_upload_empty_file(): void
    {
        $response = $this->call('PUT', route('api.upload', ['filename' => 'myimage.jpg']), content: '');
        $response->assertBadRequest();
    }

    public function test_file_deletes(): void
    {
        Bus::fake();

        $filename = 'myimage.jpg';
        $content = UploadedFile::fake()->image($filename)->getContent();

        $response = $this->call('PUT', route('api.upload', ['filename' => $filename]), content: $content);
        $download_url = $response->getContent();

        $this->delete($download_url)->assertOk();

        Bus::assertDispatched(UploadExpire::class);
    }
}
