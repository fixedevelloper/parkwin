<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Payment;
use App\Models\Tontine;
use App\Models\TontineMember;
use App\Models\Tour;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'phone' => '675066919',
            'role' => 'admin',
            'email' => 'test@example.com',
        ]);
        // ----------- USERS -----------
        $users = [
            ['name'=>'Alice', 'email'=>'alice@test.com', 'phone'=>'237600000001', 'password'=>Hash::make('password')],
            ['name'=>'Bob', 'email'=>'bob@test.com', 'phone'=>'237600000002', 'password'=>Hash::make('password')],
            ['name'=>'Charlie', 'email'=>'charlie@test.com', 'phone'=>'237600000003', 'password'=>Hash::make('password')],
            ['name'=>'David', 'email'=>'david@test.com', 'phone'=>'237600000004', 'password'=>Hash::make('password')],
            ['name'=>'Eve', 'email'=>'eve@test.com', 'phone'=>'237600000005', 'password'=>Hash::make('password')],
        ];

        foreach($users as $user){
            User::create($user);
        }

        $users = User::all();

        // ----------- TONTINES -----------
        $tontine1 = Tontine::create([
            'admin_id'=>$users[0]->id,
            'name'=>'Tontine Mensuelle Alpha',
            'amount'=>10000,
            'frequency'=>'monthly',
            'status'=>'active',
            'participants_count'=>3
        ]);

        $tontine2 = Tontine::create([
            'admin_id'=>$users[1]->id,
            'name'=>'Tontine Hebdo Beta',
            'amount'=>5000,
            'frequency'=>'weekly',
            'status'=>'active',
            'participants_count'=>2
        ]);

        // ----------- TONTINE MEMBERS -----------
        TontineMember::create(['tontine_id'=>$tontine1->id,'user_id'=>$users[0]->id,'order_position'=>1,'status'=>'active']);
        TontineMember::create(['tontine_id'=>$tontine1->id,'user_id'=>$users[2]->id,'order_position'=>2,'status'=>'active']);
        TontineMember::create(['tontine_id'=>$tontine1->id,'user_id'=>$users[3]->id,'order_position'=>3,'status'=>'active']);

        TontineMember::create(['tontine_id'=>$tontine2->id,'user_id'=>$users[1]->id,'order_position'=>1,'status'=>'active']);
        TontineMember::create(['tontine_id'=>$tontine2->id,'user_id'=>$users[4]->id,'order_position'=>2,'status'=>'active']);

        // ----------- SESSIONS -----------
        $sessions_tontine1 = [];
        for($i=1;$i<=3;$i++){
            $sessions_tontine1[] = Tour::create([
                'tontine_id'=>$tontine1->id,
                'cycle_number'=>$i,
                'beneficiary_id'=>$users[$i-1]->id,
                'amount'=>10000,
                'status'=>'pending'
            ]);
        }

        $sessions_tontine2 = [];
        for($i=1;$i<=3;$i++){
            $sessions_tontine2[] = Tour::create([
                'tontine_id'=>$tontine2->id,
                'cycle_number'=>$i,
                'beneficiary_id'=>$users[$i+1]->id ?? $users[4]->id,
                'amount'=>5000,
                'status'=>'pending'
            ]);
        }

        // ----------- PAYMENTS -----------
        foreach($sessions_tontine1 as $session){
            foreach($tontine1->members as $member){
                Payment::create([
                    'user_id'=>$member->user_id,
                    'tour_id'=>$session->id,
                    'amount'=>$session->amount,
                    'status'=>'success',
                    'transaction_ref'=>'TXN'.rand(100000,999999)
                ]);
            }
        }

        foreach($sessions_tontine2 as $session){
            foreach($tontine2->members as $member){
                Payment::create([
                    'user_id'=>$member->user_id,
                    'tour_id'=>$session->id,
                    'amount'=>$session->amount,
                    'status'=>'success',
                    'transaction_ref'=>'TXN'.rand(100000,999999)
                ]);
            }
        }
    }
}
