<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Support\Facades\Storage;
use App\Services\SignatureService;
use App\Models\Procurement\InspectionAcceptanceReport;
use Illuminate\Support\Str;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SignatureServiceTest extends TestCase
{
    // Don't run full migrations; create minimal tables needed for tests
    protected function setUp(): void
    {
        parent::setUp();
        // create minimal inspectionacceptancereport table if not exists
        if (!\Illuminate\Support\Facades\Schema::hasTable('inspectionacceptancereport')) {
            \Illuminate\Support\Facades\Schema::create('inspectionacceptancereport', function (\Illuminate\Database\Schema\Blueprint $table) {
                $table->increments('IARID');
                $table->string('IARNumber')->nullable();
                $table->integer('DRID')->nullable();
                $table->integer('InspectedBy')->nullable();
                $table->integer('AcceptedBy')->nullable();
                $table->date('DateInspected')->nullable();
                $table->timestamps();
            });
        }

        // create minimal iar_signatures table
        if (!\Illuminate\Support\Facades\Schema::hasTable('iar_signatures')) {
            \Illuminate\Support\Facades\Schema::create('iar_signatures', function (\Illuminate\Database\Schema\Blueprint $table) {
                $table->bigIncrements('id');
                $table->integer('iar_id');
                $table->string('role', 20);
                $table->integer('signed_by_user_id');
                $table->string('source', 20);
                $table->string('storage_disk', 32)->default('public');
                $table->string('storage_path', 512);
                $table->string('url', 1024)->nullable();
                $table->string('mime', 50);
                $table->integer('width')->nullable();
                $table->integer('height')->nullable();
                $table->integer('size_bytes')->nullable();
                $table->char('sha256_hash', 64);
                $table->string('ip_address', 45)->nullable();
                $table->string('user_agent', 255)->nullable();
                $table->timestamps();
            });
        }
    }

    public function test_process_and_store_creates_signature_record_and_file()
    {
        Storage::fake('public');
        // ensure the service will use the fake public disk
        config(['filesystems.default' => 'public']);

        // create an IAR record
        $iar = InspectionAcceptanceReport::create([
            'IARNumber' => 'IAR-TEST-1',
            'DRID' => 1,
            'InspectedBy' => 1,
            'AcceptedBy' => null,
            'DateInspected' => now()->format('Y-m-d'),
        ]);

        // disable pixel coverage enforcement for this test to avoid reliance on GD image sampling
        $service = new SignatureService(false);

        // Use a tiny 1x1 black PNG as a base64 data URL (avoids relying on GD extension)
        $dataUrl = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR4nGMAAQAAAgAB9HFkWQAAAABJRU5ErkJggg==';

        $signature = $service->processAndStore($iar->IARID, 'inspected', $dataUrl, 'canvas', 1);

        $this->assertNotNull($signature);
        $this->assertEquals($iar->IARID, $signature->iar_id);
        $this->assertEquals('inspected', $signature->role);
        Storage::disk('public')->assertExists($signature->storage_path);
    }

    public function test_blank_signature_fails_pixel_coverage()
    {
        Storage::fake('public');

        $iar = InspectionAcceptanceReport::create([
            'IARNumber' => 'IAR-TEST-2',
            'DRID' => 1,
            'InspectedBy' => 1,
            'AcceptedBy' => null,
            'DateInspected' => now()->format('Y-m-d'),
        ]);

        $service = new SignatureService();

        // Use a tiny 1x1 transparent PNG (very small -> should fail the size heuristic)
        $dataUrl = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR4nGMAAQAABQABDQottAAAAABJRU5ErkJggg==';

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Signature appears empty');

        $service->processAndStore($iar->IARID, 'inspected', $dataUrl, 'canvas', 1);
    }
}
