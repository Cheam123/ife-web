<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ClearZipStorage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'zip:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear temporary generated zip files.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $file_system = Storage::disk('local');
        $files = collect($file_system->allFiles('public/zip'));

        $files->each(function ($file) use ($file_system) {
            if (!Str::endsWith($file, '.gitignore')) {
                $file_system->delete($file);
            }
        });

        storage_path('framework/laravel-excel');
        $files2 = collect($file_system->allFiles('framework/laravel-excel'));

        $files2->each(function ($f) use ($file_system) {
            if (!Str::endsWith($f, '.gitignore')) {
                $file_system->delete($f);
            }
        });

        $scan = glob(rtrim(storage_path('framework/laravel-excel'), '/').'/*');
        foreach ($scan as $index=>$path) {
            unlink($path);
        }

        $this->info("Cleared zip files!");
    }
}
