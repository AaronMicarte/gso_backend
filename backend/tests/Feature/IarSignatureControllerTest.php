<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Auth\User;
use App\Models\Procurement\InspectionAcceptanceReport;
use App\Models\Procurement\IarSignature;
use Illuminate\Support\Facades\Storage;

class IarSignatureControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_store_signature_endpoint_creates_signature()
    {
        Storage::fake('public');
        // ensure service uses fake public disk
        config(['filesystems.default' => 'public']);

        // ensure minimal related tables exist for test (user role, department)
        if (!\Illuminate\Support\Facades\Schema::hasTable('userrole')) {
            \Illuminate\Support\Facades\Schema::create('userrole', function (\Illuminate\Database\Schema\Blueprint $table) {
                $table->increments('UserRoleID');
                $table->string('Name')->nullable();
            });
        } elseif (!\Illuminate\Support\Facades\Schema::hasColumn('userrole', 'Name')) {
            \Illuminate\Support\Facades\Schema::table('userrole', function (\Illuminate\Database\Schema\Blueprint $table) {
                $table->string('Name')->nullable();
            });
        }

        if (!\Illuminate\Support\Facades\Schema::hasTable('department')) {
            \Illuminate\Support\Facades\Schema::create('department', function (\Illuminate\Database\Schema\Blueprint $table) {
                $table->increments('DepartmentID');
                $table->string('Name')->nullable();
            });
        } elseif (!\Illuminate\Support\Facades\Schema::hasColumn('department', 'Name')) {
            \Illuminate\Support\Facades\Schema::table('department', function (\Illuminate\Database\Schema\Blueprint $table) {
                $table->string('Name')->nullable();
            });
        }

        \Illuminate\Support\Facades\DB::table('userrole')->insertIfNotExists ?? null; // guard for older PHP
        if (!\Illuminate\Support\Facades\DB::table('userrole')->where('UserRoleID', 2)->exists()) {
            if (\Illuminate\Support\Facades\Schema::hasColumn('userrole', 'RoleName')) {
                \Illuminate\Support\Facades\DB::table('userrole')->insert(['UserRoleID' => 2, 'RoleName' => 'Staff']);
            } else {
                \Illuminate\Support\Facades\DB::table('userrole')->insert(['UserRoleID' => 2, 'Name' => 'Staff']);
            }
        }
        if (!\Illuminate\Support\Facades\DB::table('department')->where('DepartmentID', 1)->exists()) {
            if (\Illuminate\Support\Facades\Schema::hasColumn('department', 'DepartmentName')) {
                \Illuminate\Support\Facades\DB::table('department')->insert(['DepartmentID' => 1, 'DepartmentName' => 'Procurement']);
            } else {
                \Illuminate\Support\Facades\DB::table('department')->insert(['DepartmentID' => 1, 'Name' => 'Procurement']);
            }
        }

        // create a user
        $user = User::create([
            'UserRoleID' => 2,
            'DepartmentID' => 1,
            'Username' => 'test.user',
            'Email' => 'test.user@example.com',
            'Password' => bcrypt('password'),
            'FullName' => 'Test User',
            'IsActive' => 1,
        ]);

        // ensure minimal inspectionacceptancereport and iar_signatures tables exist for test
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

        // create DR and IAR
        $iar = InspectionAcceptanceReport::create([
            'IARNumber' => 'IAR-TST-STORE',
            'DRID' => 1,
            'InspectedBy' => $user->UserID,
            'AcceptedBy' => null,
            'DateInspected' => now()->format('Y-m-d'),
        ]);

        // Use a small PNG data URL (black pixel) without relying on GD
        $dataUrl = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR4nGNgYAAAAAMAASsJTYQAAAAASUVORK5CYII=';
        // Use a service instance that doesn't enforce pixel coverage (avoids GD dependency in CI)
        $this->app->instance(\App\Services\SignatureService::class, new \App\Services\SignatureService(false));

        $response = $this->actingAs($user)
            ->postJson("/api/admin/procurement/inspection-acceptance-reports/{$iar->IARID}/signatures", [
                'role' => 'inspected',
                'signature' => $dataUrl,
                'source' => 'canvas',
            ]);

        // dump validation errors for debugging
        file_put_contents('php://stderr', print_r($response->json(), true));

        $response->assertStatus(201);
        $this->assertDatabaseHas('iar_signatures', [
            'iar_id' => $iar->IARID,
            'role' => 'inspected',
            'signed_by_user_id' => $user->UserID,
        ]);

        $sig = IarSignature::where('iar_id', $iar->IARID)->where('role', 'inspected')->first();
        $this->assertNotNull($sig);
        Storage::disk('public')->assertExists($sig->storage_path);
    }

    public function test_store_accepted_signature_creates_signature_when_acceptor_matches()
    {
        Storage::fake('public');
        config(['filesystems.default' => 'public']);

        // create user who will be acceptor
        $user = User::create([
            'UserRoleID' => 2,
            'DepartmentID' => 1,
            'Username' => 'acceptor.user',
            'Email' => 'acceptor@example.com',
            'Password' => bcrypt('password'),
            'FullName' => 'Acceptor User',
            'IsActive' => 1,
        ]);

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

        $iar = InspectionAcceptanceReport::create([
            'IARNumber' => 'IAR-TST-ACCEPTED',
            'DRID' => 1,
            'InspectedBy' => $user->UserID,
            'AcceptedBy' => $user->UserID,
            'DateInspected' => now()->format('Y-m-d'),
        ]);

        $dataUrl = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR4nGNgYAAAAAMAASsJTYQAAAAASUVORK5CYII=';
        $this->app->instance(\App\Services\SignatureService::class, new \App\Services\SignatureService(false));

        $response = $this->actingAs($user)
            ->postJson("/api/admin/procurement/inspection-acceptance-reports/{$iar->IARID}/signatures", [
                'role' => 'accepted',
                'signature' => $dataUrl,
                'source' => 'canvas',
            ]);

        file_put_contents('php://stderr', print_r($response->json(), true));

        $response->assertStatus(201);
        $this->assertDatabaseHas('iar_signatures', [
            'iar_id' => $iar->IARID,
            'role' => 'accepted',
            'signed_by_user_id' => $user->UserID,
        ]);

        $sig = IarSignature::where('iar_id', $iar->IARID)->where('role', 'accepted')->first();
        $this->assertNotNull($sig);
        Storage::disk('public')->assertExists($sig->storage_path);
    }
}
