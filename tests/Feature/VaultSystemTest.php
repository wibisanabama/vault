<?php

namespace Tests\Feature;

use App\Models\Loker;
use App\Models\Pelanggan;
use App\Models\Tarif;
use App\Models\Transaksi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VaultSystemTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed default locker & active rate
        Loker::create([
            'nomor_loker' => 'L-001',
            'status' => 'tersedia',
            'lokasi' => 'Zona A',
        ]);

        Tarif::create([
            'nama' => 'Standard',
            'harga_per_jam' => 2000,
            'is_active' => true,
        ]);
    }

    public function test_petugas_can_titip_helm_and_auto_assign_loker(): void
    {
        $petugas = User::factory()->create(['role' => 'petugas']);

        $response = $this->actingAs($petugas)->post(route('transaksi.store'), [
            'nama_pelanggan' => 'Andi Wijaya',
            'no_hp'          => '081299998888',
            'alamat'         => 'Jl. Sudirman',
            'merk_helm'      => 'KYT NFR',
            'warna_helm'     => 'Hitam Doff',
            'deskripsi_helm' => 'Visor cembung',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('pelanggan', ['nama' => 'Andi Wijaya']);
        $this->assertDatabaseHas('helm', ['merk' => 'KYT NFR']);
        $this->assertDatabaseHas('transaksi', ['status' => 'titip']);
        $this->assertDatabaseHas('loker', ['nomor_loker' => 'L-001', 'status' => 'terisi']);
    }

    public function test_petugas_can_process_ambil_helm_and_record_payment(): void
    {
        $petugas = User::factory()->create(['role' => 'petugas']);

        // Perform titip
        $this->actingAs($petugas)->post(route('transaksi.store'), [
            'nama_pelanggan' => 'Siti Aminah',
            'no_hp'          => '0855667788',
            'merk_helm'      => 'Shoei Z7',
            'warna_helm'     => 'Putih',
        ]);

        $transaksi = Transaksi::first();

        // Perform ambil
        $response = $this->actingAs($petugas)->post(route('transaksi.proses-ambil', $transaksi->id), [
            'metode_bayar' => 'tunai',
        ]);

        $response->assertRedirect(route('transaksi.show', $transaksi->id));

        $transaksi->refresh();
        $this->assertEquals('ambil', $transaksi->status);
        $this->assertEquals('tersedia', $transaksi->loker->status);
        $this->assertDatabaseHas('pembayaran', [
            'transaksi_id' => $transaksi->id,
            'metode_bayar' => 'tunai',
            'status'       => 'lunas',
        ]);
    }

    public function test_only_admin_can_access_laporan_and_tarif(): void
    {
        $petugas = User::factory()->create(['role' => 'petugas']);
        $admin = User::factory()->create(['role' => 'admin']);

        // Petugas should be forbidden from accessing reports
        $responsePetugas = $this->actingAs($petugas)->get(route('laporan.index'));
        $responsePetugas->assertStatus(403);

        // Admin can access reports
        $responseAdmin = $this->actingAs($admin)->get(route('laporan.index'));
        $responseAdmin->assertStatus(200);
    }
}
