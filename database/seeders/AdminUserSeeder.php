<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Seeder para criar um usuário administrador padrão
 * 
 * Este seeder cria um usuário de teste para acessar o painel administrativo.
 * É útil para desenvolvimento e testes iniciais.
 * 
 * Para executar: php artisan db:seed --class=AdminUserSeeder
 */
class AdminUserSeeder extends Seeder
{
    /**
     * Executa o seeder.
     * 
     * Cria um usuário administrador com credenciais padrão.
     * Se o usuário já existir (email duplicado), não faz nada.
     */
    public function run(): void
    {
        // Verifica se já existe um usuário com este email
        $existingUser = User::where('email', 'admin@geolayers.com')->first();
        
        if ($existingUser) {
            $this->command->info('⚠️  Usuário admin já existe!');
            return;
        }

        // Cria o usuário administrador
        User::create([
            'name' => 'Administrador',
            'email' => 'admin@geolayers.com',
            'password' => Hash::make('password'), // IMPORTANTE: Mude em produção!
            'email_verified_at' => now(), // Marca email como verificado
        ]);

        $this->command->info('✅ Usuário administrador criado com sucesso!');
        $this->command->info('📧 Email: admin@geolayers.com');
        $this->command->info('🔑 Senha: password');
        $this->command->warn('⚠️  ATENÇÃO: Altere a senha em produção!');
    }
}
