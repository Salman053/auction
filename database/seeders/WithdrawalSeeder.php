<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use App\Models\WithdrawalRequest;
use Illuminate\Database\Seeder;

class WithdrawalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (WithdrawalRequest::query()->count() > 0) {
            return;
        }

        $user = User::query()->where('role', UserRole::User)->first();
        if ($user === null) {
            return;
        }

        WithdrawalRequest::query()->create([
            'user_id' => $user->id,
            'amount_yen' => 10_000,
            'status' => 'pending',
            'destination_type' => 'bank',
            'destination_meta' => ['iban' => 'JP00XXXX0000000000'],
            'memo' => 'Seed withdrawal',
        ]);
    }
}
