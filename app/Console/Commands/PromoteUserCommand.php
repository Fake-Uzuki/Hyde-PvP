<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class PromoteUserCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:promote {email : The email address of the user} {role=admin : The target role (admin, moderator, member)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Promote or change the RBAC role of a user safely';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $email = $this->argument('email');
        $role = strtolower((string) $this->argument('role'));

        $validRoles = ['admin', 'moderator', 'member'];
        if (! in_array($role, $validRoles, true)) {
            $this->error("Invalid role '{$role}'. Allowed roles are: " . implode(', ', $validRoles));
            return self::FAILURE;
        }

        $user = User::where('email', $email)->first();
        if (! $user) {
            $this->error("User with email '{$email}' not found.");
            return self::FAILURE;
        }

        $oldRole = $user->role;
        $user->role = $role;
        $user->save();

        $this->info("Successfully updated {$user->name} ({$email}) from '{$oldRole}' to '{$role}'.");
        return self::SUCCESS;
    }
}
