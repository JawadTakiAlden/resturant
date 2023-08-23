<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Category;
use App\Models\Meal;
use App\Models\Table;
use App\Models\User;
use App\Status\UserType;
use Database\Factories\TableFactory;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        User::create([
           'name' => 'waiter',
            'email' => 'waiter@gmail.com',
            'password' => '123456789',
        ]);

        User::create([
            'name' => 'kitchen',
            'email' => 'kitchen@gmail.com',
            'password' => '123456789',
            'type' => UserType::KITCHEN
        ]);

        User::create([
            'name' => 'casher',
            'email' => 'casher@gmail.com',
            'password' => '123456789',
            'type' => UserType::CASHER
        ]);

        User::create([
            'name' => 'admin',
            'email' => 'admin@gmail.com',
            'password' => '123456789',
            'type' => UserType::ADMIN
        ]);


        Table::factory(100)->create();
        Category::factory(50)->create();
        Meal::factory(250)->create();
    }
}
