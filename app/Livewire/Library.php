<?php

namespace App\Livewire;

use App\Models\Asset;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

class Library extends Component
{
    use WithFileUploads;

    /**
     * @var TemporaryUploadedFile[] array
     */
    public $files = [];

    public function saveUploadedFiles()
    {
        foreach ($this->files as $file) {
            $uuid = Str::uuid()->toString();
            $name = $uuid . '.' . $file->getClientOriginalExtension();

            Asset::create([
                'name' => $file->getClientOriginalName(),
                'path' => 'library/' . $name,
                'uuid' => $uuid,
                'type' => $file->getMimeType(),
                'size' => $file->getSize(),
                'user_id' => auth()->id(),
            ]);

            $file->storeAs(
                path: 'library',
                name: $name,
                options: ['disk' => 'public'],
            );

            $file->delete();
        }

        $this->dispatch('file-saved', 'Files uploaded successfully!');

        $this->cleanupOldUploads();
        $this->files = [];
    }

    public function render()
    {
        return view('livewire.library', [
            'assets' => auth()->user()->assets,
        ]);
    }
}
