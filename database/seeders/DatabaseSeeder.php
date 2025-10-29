<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@snbtku.com'],
            [
                'name' => 'Admin SNBTKU',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]
        );

        User::firstOrCreate(
            ['email' => 'budi@student.com'],
            [
                'name' => 'Budi Santoso',
                'password' => Hash::make('password'),
                'role' => 'user',
            ]
        );

        User::firstOrCreate(
            ['email' => 'sari@student.com'],
            [
                'name' => 'Sari Dewi',
                'password' => Hash::make('password'),
                'role' => 'user',
            ]
        );

        $this->call(BankSoalTryoutSeeder::class);

        $adminId = $admin->id;

        Post::firstOrCreate(
            ['slug' => 'tips-sukses-menghadapi-snbt-2024'],
            [
                'user_id' => $adminId,
                'title' => 'Tips Sukses Menghadapi SNBT 2024',
                'body' => 'SNBT merupakan salah satu jalur masuk perguruan tinggi negeri yang paling diminati. Berikut beberapa tips untuk sukses menghadapi SNBT 2024:

1. Pahami format tes SNBT. Tes terdiri dari tujuh subtes dengan durasi total 195 menit.
2. Lakukan latihan soal secara rutin untuk meningkatkan kecepatan dan ketelitian.
3. Jaga manajemen waktu saat mengerjakan soal agar semua bagian terjawab.
4. Perhatikan kondisi fisik dan mental menjelang ujian.
5. Ikuti try out sebagai simulasi suasana ujian sesungguhnya.',
                'status' => 'published',
            ]
        );

        Post::firstOrCreate(
            ['slug' => 'strategi-mengerjakan-7-subtes-snbt'],
            [
                'user_id' => $adminId,
                'title' => 'Strategi Mengerjakan 7 Subtes SNBT',
                'body' => 'SNBT terdiri dari tujuh subtes yang perlu dikerjakan secara berurutan. Berikut strategi ringkas untuk setiap subtes:

1. Penalaran Umum (30 soal): fokus pada logika dan pola, jangan habiskan waktu di satu soal.
2. KMBM (20 soal): baca dengan teliti, pahami konteks bacaan.
3. Pengetahuan dan Pemahaman Umum (20 soal): gunakan pengetahuan umum dan logika.
4. Kemampuan Kuantitatif (20 soal): kuasai operasi dasar dan gunakan eliminasi jawaban.
5. Literasi Indonesia (30 soal): pahami tata bahasa dan makna kata.
6. Literasi Inggris (20 soal): perhatikan grammar dan kosakata.
7. Penalaran Matematika (20 soal): gunakan konsep matematika dasar dan penalaran logis.',
                'status' => 'published',
            ]
        );
    }
}
