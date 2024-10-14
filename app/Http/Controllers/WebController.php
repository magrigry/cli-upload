<?php

namespace App\Http\Controllers;

use App\Jobs\UploadExpire;
use App\Models\Upload;
use App\Services\StorageService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Webmozart\Assert\Assert;

class WebController
{
    public function home(Request $request, StorageService $storageService): View|Factory|Application
    {
        $uploads = Upload::query()
            ->where('session_id', '=', $request->session()->getId())
            ->where('deleted', '=', false)
            ->orderByDesc('id')
            ->paginate(10);

        return view('index', [
            'host' => $request->getHost(),
            'uploads' => $uploads,
            'maxSize' => $storageService->getMaxFileSize()->format()
        ]);
    }

    public function script(string $script): View|Factory|Application
    {
        return view("commands.$script");
    }

    public function upload(Request $request, StorageService $storageService): RedirectResponse
    {
        $files = $request->file('files');

        $request->validate([
            'files' => 'required|array',
            'files.*' => 'required|file|min:0',
        ]);

        Assert::allIsInstanceOf($files, UploadedFile::class);

        foreach ($files as $file) {

            $storageService->upload(
                file: $file,
                filename: $file->getClientOriginalName(),
                ip: $request->ip(),
                related_session_id: $request->session()->getId()
            );

        }

        return redirect()->route('home');
    }

    public function delete(Upload $upload): RedirectResponse
    {
        UploadExpire::dispatchSync($upload);

        return redirect()->route('home');
    }
}
