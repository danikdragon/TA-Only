<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class GenerateToken extends Command
{
    protected $signature = 'user:token {email}';
    protected $description = 'Generate a Sanctum token for a user';

    public function handle()
    {
        $email = $this->argument('email');
        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->error("User with email {$email} not found.");
            return 1;
        }

        $token = $user->createToken('test-token')->plainTextToken;
        $this->info("Token for {$email}:");
        $this->line($token);
        
        return 0;
    }
}