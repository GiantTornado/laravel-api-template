<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Storage;

class ArchiveBackupFiles extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'files:archive-backup-files';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Move files from storage/app/Book Store to storage/app/archived/Book Store';

    /**
     * Execute the console command.
     */
    public function handle() {
        $appName = env('APP_NAME');
        $sourcePath = "$appName";
        $destinationPath = "archived/$appName";

        if (!Storage::disk('local')->exists($sourcePath)) {
            $this->error("The directory '$sourcePath' does not exist.");
            return Command::FAILURE;
        }

        Storage::disk('local')->makeDirectory($destinationPath);

        $files = Storage::disk('local')->allFiles($sourcePath);
        foreach ($files as $file) {
            $destination = str_replace($sourcePath, $destinationPath, $file);
            Storage::disk('local')->move($file, $destination);
        }

        $this->info("All files have been moved from '$sourcePath' to '$destinationPath'.");

        return Command::SUCCESS;
    }
}
