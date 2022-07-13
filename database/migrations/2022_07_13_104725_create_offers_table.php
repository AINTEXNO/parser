<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOffersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('offers', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\Mark::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(\App\Models\Year::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(\App\Models\Color::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(\App\Models\BodyType::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(\App\Models\EngineType::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(\App\Models\Transmission::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(\App\Models\GearType::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(\App\Models\AutoModel::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(\App\Models\Generation::class)->constrained()->cascadeOnDelete();
            $table->integer('run')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('offers');
    }
}
