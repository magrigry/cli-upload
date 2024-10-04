<?php

namespace Tests\Feature;

use App\Jobs\UploadExpire;
use App\Models\Upload;
use Bus;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class UploadWebTest extends TestCase
{
    public function test_upload_and_download_file(): void
    {
        Bus::fake();

        $filename = 'myimage.jpg';
        $content = UploadedFile::fake()->image($filename);

        $response = $this->post(route('session.upload', ['filename' => $filename]), [
            'files' => [$content],
        ]);

        $response->assertRedirectToRoute('home');

        $upload = Upload::latest()->first();

        $download_url = route('api.download', ['upload' => $upload]);

        $response = $this->get($download_url);
        $response->assertOk();
        $response->assertDownload($filename);

        Bus::assertDispatched(UploadExpire::class, function (UploadExpire $job) {
            /** @var Carbon $delay */
            $delay = $job->delay;

            return $delay->isSameMinute(Carbon::now()->addHour());
        });
    }

    public function test_delete_file(): void
    {
        Bus::fake();

        $filename = 'myimage.jpg';
        $content = UploadedFile::fake()->image($filename);

        $response = $this->post(route('session.upload', ['filename' => $filename]), [
            'files' => [$content],
        ]);

        $response->assertRedirect(route('home'));

        $upload = Upload::latest()->first();

        $this->delete(route('session.delete', ['upload' => $upload]))
            ->assertRedirectToRoute('home');

        Bus::assertDispatched(UploadExpire::class);
    }
}
