<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Outlet profile: where the outlet is (captured on site, like a GPS Stamp
     * field) and the attributes the recommendation engine compares outlets on.
     * business_category already serves as the outlet type.
     */
    public function up()
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->decimal('latitude', 10, 7)->nullable()->after('ife_area_id');
            $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
            $table->decimal('location_accuracy', 8, 1)->nullable()->after('longitude')->comment('Metres, as reported by the device');
            $table->dateTime('location_captured_at')->nullable()->after('location_accuracy');
            $table->string('size_band', 10)->nullable()->after('location_captured_at')->comment('small | medium | large, see Leads::SIZE_BANDS');
            $table->unsignedSmallInteger('seats')->nullable()->after('size_band');
            $table->string('segment', 20)->nullable()->after('seats')->comment('budget | mid_range | premium, see Leads::SEGMENTS');
        });
    }

    public function down()
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropColumn([
                'latitude', 'longitude', 'location_accuracy', 'location_captured_at',
                'size_band', 'seats', 'segment',
            ]);
        });
    }
};
