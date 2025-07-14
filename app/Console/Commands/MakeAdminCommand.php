<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class MakeAdminCommand extends Command
{
    protected $signature = 'user:make-admin {email : El email del usuario}';
    protected $description = 'Convierte un usuario en administrador';

    public function handle()
    {
        $email = $this->argument('email');
        
        $user = User::where('email', $email)->first();
        
        if (!$user) {
            $this->error("No se encontró un usuario con el email: {$email}");
            return 1;
        }
        
        if ($user->isAdmin()) {
            $this->info("El usuario {$email} ya es administrador.");
            return 0;
        }
        
        $user->update(['role' => User::ROLE_ADMIN]);
        
        $this->info("El usuario {$email} ahora es administrador.");
        return 0;
    }
}