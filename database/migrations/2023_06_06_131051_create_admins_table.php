<?php

use App\Enums\Status;
use App\Models\Admin;
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
        Schema::create('admins', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('firstname');
            $table->string('lastname');
            $table->string('email')->unique();
            $table->string('phone')->unique()->nullable();
            $table->string('password');
            $table->string('status')->default(Status::ACTIVE->value);
            $table->string('otp')->nullable();
            $table->dateTime('otp_expiry')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        $data = [
            'firstname' => 'Super',
            'lastname' => 'Admin',
            'email' => 'admin@naira4dollar.com',
            'password' => bcrypt('password'),
        ];
        (new Admin($data))->save();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admins');
    }
};
