<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSfParcelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        
        Schema::create('sf_parcels', function (Blueprint $table) {
            $table->increments('id');
            $table->text('PropertyIDRecordID')->collate('utf8_bin')->nullable();
            $table->text('PropertyIDCreatedDate')->nullable();
            $table->text('PropertyIDPropertyName')->nullable();
            $table->text('PropertyIDParcelID')->nullable();
            $table->text('PropertyIDPropertyStatus')->nullable();
            $table->text('PropertyIDStatusExplanation')->nullable();
            $table->text('ProgramID')->nullable();
            $table->text('ProgramProgramName')->nullable();
            $table->text('PropertyIDTargetArea')->nullable();
            $table->float('PropertyIDSalesPrice', 15, 2)->nullable();
            $table->text('PropertyIDPropertyDocuments')->nullable();
            $table->text('PropertyIDHowAcquired')->nullable();
            $table->text('PropertyIDHowAcquiredExplanation')->nullable();
            $table->float('PropertyIDLatLonLatitude', 30, 20)->nullable();
            $table->float('PropertyIDLatLonLongitude', 30, 20)->nullable();
            $table->text('PropertyIDLocationMap')->nullable();
            $table->integer('PropertyIDConfidenceCode')->nullable();
            $table->integer('PropertyIDOHHouseDistrict')->nullable();
            $table->integer('PropertyIDOHSenateDistrict')->nullable();
            $table->integer('PropertyIDUSHouseDistrict')->nullable();
            $table->text('PropertyIDGeocodeUpdatedDate')->nullable();
            $table->text('PropertyIDStreetAddress')->nullable();
            $table->text('PropertyIDCity')->nullable();
            $table->text('PropertyIDState')->nullable();
            $table->text('PropertyIDZip')->nullable();
            $table->text('PropertyIDCounty')->nullable();
            $table->tinyInteger('PropertyIDUglyHouse')->nullable();
            $table->tinyInteger('PropertyIDPrettyLot')->nullable();
            $table->tinyInteger('PropertyIDHistoricWaiverApproved')->nullable();
            $table->tinyInteger('PropertyIDHistoricSignificanceDistrict')->nullable();
            $table->text('PropertyIDDispositionType')->nullable();
            $table->text('PropertyIDDisposition')->nullable();
            $table->text('PropertyIDWithdrawnDate')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sf_parcels');
    }
}
