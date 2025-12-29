<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('savings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name'); // ex: "Épargne maison"
            $table->decimal('balance', 15, 2)->default(0); // solde actuel
            $table->decimal('amount_goal', 15, 2)->nullable(); // objectif
            $table->boolean('active')->default(true);
            $table->timestamps();
        });


        Schema::create('tontines', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('reference'); //permet au utilisateur de retrouver la tontine
            $table->decimal('amount', 15, 2);
            $table->enum('frequency', ['daily', 'weekly', 'monthly']);
            $table->unsignedInteger('participants_count');
            $table->foreignId('owner_id')->constrained('users');
            $table->timestamps();
        });
        Schema::create('tontine_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tontine_id')->constrained();
            $table->foreignId('user_id')->constrained();
            $table->integer('position'); // ordre de réception
            $table->unique(['tontine_id', 'user_id']); // un utilisateur ne peut rejoindre qu’une fois
            $table->timestamps();
        });
        Schema::create('tours', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tontine_id')->constrained();
            $table->integer('cycle_number');
            $table->foreignId('beneficiary_id')->nullable()->constrained('users');
            $table->decimal('amount', 15, 2);
            $table->enum('status', ['PENDING', 'PAID']);
            $table->unique(['tontine_id', 'cycle_number']); // un cycle par tontine
            $table->timestamps();
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tour_id')->nullable()->constrained();
            $table->foreignId('saving_id')->nullable()->constrained('savings');
            $table->foreignId('user_id')->constrained();

            $table->decimal('amount', 15, 2);
            $table->string('method_pay'); // OM, MOMO, CARD
            $table->string('reference')->nullable();

            $table->enum('type', ['TONTINE', 'EPARGNE']);
            $table->enum('status', ['PENDING', 'SUCCESS', 'FAILED']);

            $table->timestamps();
        });

        Schema::create('scheduled_savings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('saving_id')->constrained();
            $table->foreignId('user_id')->constrained();

            $table->decimal('amount', 15, 2);
            $table->enum('frequency', ['daily', 'weekly', 'monthly']);

            $table->date('start_date');
            $table->date('next_run_date');

            $table->boolean('active')->default(true);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }

};
