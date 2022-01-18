<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterUsersTableAddGithubDataColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {

            $table->dropIndex('users_email_unique');
            $table->string('email')->nullable()->change();
            $table->string('name')->nullable()->change();
            $table->dropColumn('password');
            $table->unsignedBigInteger('github_db_id')->unique();
            $table->unsignedInteger('github_repositories_count')->default(0);
            $table->unsignedInteger('github_subscribers_count')->default(0);
            $table->unsignedInteger('profile_shown_counter')->default(0);
            $table->string('location',255)->nullable();
            $table->string('avatar_url',500 );
            $table->text('bio')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'github_db_id',
                'github_repositories_count',
                'github_subscribers_count',
                'profile_shown_counter',
                'location',
                'avatar_url',
                'bio',
            ]);
            $table->string('email')->nullable(false)->change();
            $table->unique('email');
            $table->string('name')->nullable(false)->change();
            $table->string('password');

        });
    }
}
