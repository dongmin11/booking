<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class PasswordLockReset extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'password:lock-reset';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        User::where('passLock', 6)->update([
            'passLock' => 5,
        ]);
    }
}
