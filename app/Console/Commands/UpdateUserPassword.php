<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class UpdateUserPassword extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:update-password';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update the password for the user with id=1';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Log::info('***********EXECUTION*******');

        $newPassword = '234qaz!@#';
        $hashedPassword = Hash::make($newPassword);

        DB::table('afyacallproduction.users')
            ->where('id', 4)
            ->update(['password' => $hashedPassword]);

        $this->info('Password updated successfully for user with id=4');

        return 0;
    }
}
