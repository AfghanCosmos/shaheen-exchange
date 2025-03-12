<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    namespace App\Models;

    use Illuminate\Database\Eloquent\Model;

    class Province extends Model
    {

        public function warehouses()
        {
            return $this->hasMany(Warehouse::class);
        }


        // Relationship: A province has many workshops
        public function workshops()
        {
            return $this->hasMany(Workshop::class, 'province_id');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('provinces');
    }
};
