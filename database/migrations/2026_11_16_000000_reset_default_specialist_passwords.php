<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

return new class extends Migration
{
    // Profile specjalistów tworzone przez admina dostawały hasło "password" i publiczny e-mail.
    public function up(): void
    {
        User::where('is_specialist', true)
            ->where('email', 'like', '%@example.com')
            ->each(function (User $user) {
                if ($user->password && Hash::check('password', $user->password)) {
                    $user->forceFill(['password' => Hash::make(Str::random(40))])->save();
                }
            });
    }

    public function down(): void
    {
        // Celowo bez cofania: nie przywracamy znanego hasła.
    }
};
