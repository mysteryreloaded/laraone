<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Artisan;
use Config;

class Install extends Releases
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'laraone:install {--artisan-output}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install Laraone CMS';

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
     * @return mixed
     */
    public function handle()
    {
        $themesPath = 'themes';
        $adminThemeFileName = 'admin_one.zip';
        $defaultThemeFileName = $this->getDefaultTheme();
        $release = $this->getLastVersion();

        $this->info('last version: ' . $release);

        Artisan::call('config:clear');
        Artisan::call('config:cache');

        $downloadsUrl = config('laraone.release_attachments_url');
        $adminThemeUrl = $downloadsUrl . '/' . $release . '/' . $adminThemeFileName;
        $defaultThemeUrl = $downloadsUrl . '/' . $release . '/' . $defaultThemeFileName;

        $this->info('admin theme: ' . $adminThemeUrl);
        $this->info('default theme: ' . $defaultThemeUrl);


        $this->info('About to download admin and default frontend theme.');

        $context = stream_context_create([], ['notification' => [$this, 'downloadProgress']]);

        if($this->urlExists($adminThemeUrl)) {
            $this->info('Downloading admin theme. This may take few moments.');
            $adminThemeDownload = fopen($adminThemeUrl, 'r', null, $context);
            $adminThemePath = storage_path($themesPath . DIRECTORY_SEPARATOR . $adminThemeFileName);
            file_put_contents($adminThemePath, $adminThemeDownload);
            fclose($adminThemeDownload);
            $this->progressBar->finish();
            $this->output->newLine(1);
        } else {
            $this->info('Downloading admin theme failed. Theme url is either not correct or file is no longer there.');
            exit();
        }

        if($this->urlExists($defaultThemeUrl)) {
            $this->info('Downloading default frontend theme. This may take few moments.');
            $defaultThemeDownload = fopen($defaultThemeUrl, 'r', null, $context);
            $defaultThemePath = storage_path($themesPath . DIRECTORY_SEPARATOR . $defaultThemeFileName);
            file_put_contents($defaultThemePath, $defaultThemeDownload);
            fclose($defaultThemeDownload);
            $this->progressBar->finish();
            $this->output->newLine(1);
        } else {
            $this->info('Downloading default theme failed. Theme url is either not correct or file is no longer there.');
            exit();
        }

        $this->info('Both themes downloaded.');

        // // $envType = $this->choice('Where are we?', ['local', 'production'], 'local');
        // // $this->info($envType);
        $this->info('Running migrations.');
        Artisan::call('migrate:fresh', [
            '--force' => true,
        ], null, null);

        if($this->option('artisan-output'))
            $this->info(Artisan::output());

        $this->info('Running seeds.');

        Artisan::call('db:seed', [
            '--class' => 'DatabaseSeeder',
            '--force' => true,
        ]);

        if($this->option('artisan-output'))
            $this->info(Artisan::output());

        $this->info('Laraone v' . $release . ' installed successfully!');
    }
}
