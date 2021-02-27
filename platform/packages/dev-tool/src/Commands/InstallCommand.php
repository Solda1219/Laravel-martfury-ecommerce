<?php

namespace Botble\DevTool\Commands;

use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;

class InstallCommand extends Command
{

    use ConfirmableTrait;

    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'cms:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install CMS';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Starting installation...');

        $this->call('migrate:fresh');

        if ($this->confirmToProceed('Do you want to add a new super user?', true)) {
            $this->call('cms:user:create');
        }

        $this->info('Publishing assets...');
        $this->call('vendor:publish', ['--tag' => 'cms-public']);

        $this->info('Installed CMS successfully!');

        return 0;
    }
}
