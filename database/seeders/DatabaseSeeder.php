<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Subject;
use App\Models\Topic;
use App\Models\Question;
use App\Models\TryoutPackage;
use App\Models\Post;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create Admin User
        $admin = User::create([
            'name' => 'Admin SNBTKU',
            'email' => 'admin@snbtku.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // Create Student Users
        $student1 = User::create([
            'name' => 'Budi Santoso',
            'email' => 'budi@student.com',
            'password' => Hash::make('password'),
            'role' => 'user',
        ]);

        $student2 = User::create([
            'name' => 'Sari Dewi',
            'email' => 'sari@student.com',
            'password' => Hash::make('password'),
            'role' => 'user',
        ]);

        // Create Subjects
        $tps = Subject::create([
            'name' => 'Tes Potensi Skolastik (TPS)',
            'slug' => 'tes-potensi-skolastik-tps',
        ]);

        $literasi = Subject::create([
            'name' => 'Literasi dalam Bahasa Indonesia dan Bahasa Inggris',
            'slug' => 'literasi-dalam-bahasa-indonesia-dan-bahasa-inggris',
        ]);

        // Create Topics for TPS
        $penalaranUmum = Topic::create([
            'subject_id' => $tps->id,
            'name' => 'Penalaran Umum',
            'slug' => 'penalaran-umum',
        ]);

        $pengetahuanKuantitatif = Topic::create([
            'subject_id' => $tps->id,
            'name' => 'Pengetahuan dan Pemahaman Kuantitatif',
            'slug' => 'pengetahuan-dan-pemahaman-kuantitatif',
        ]);

        $penalaranMatematika = Topic::create([
            'subject_id' => $tps->id,
            'name' => 'Penalaran Matematika',
            'slug' => 'penalaran-matematika',
        ]);

        // Create Topics for Literasi
        $literasiIndonesia = Topic::create([
            'subject_id' => $literasi->id,
            'name' => 'Literasi Bahasa Indonesia',
            'slug' => 'literasi-bahasa-indonesia',
        ]);

        $literasiInggris = Topic::create([
            'subject_id' => $literasi->id,
            'name' => 'Literasi Bahasa Inggris',
            'slug' => 'literasi-bahasa-inggris',
        ]);

        // Create Questions for Penalaran Umum
        $questions = [];
        
        // Penalaran Umum Questions (5 questions)
        for ($i = 1; $i <= 5; $i++) {
            $questions[] = Question::create([
                'topic_id' => $penalaranUmum->id,
                'question_text' => "Soal Penalaran Umum $i: Jika semua A adalah B, dan semua B adalah C, maka dapat disimpulkan bahwa...",
                'option_a' => 'Semua A adalah C',
                'option_b' => 'Semua C adalah A',
                'option_c' => 'Beberapa A adalah C',
                'option_d' => 'Tidak ada A yang C',
                'option_e' => 'Tidak dapat disimpulkan',
                'correct_answer' => 'a',
                'explanation' => 'Berdasarkan silogisme, jika A⊆B dan B⊆C, maka A⊆C (semua A adalah C).',
            ]);
        }

        // Pengetahuan Kuantitatif Questions (5 questions)
        for ($i = 1; $i <= 5; $i++) {
            $questions[] = Question::create([
                'topic_id' => $pengetahuanKuantitatif->id,
                'question_text' => "Soal Kuantitatif $i: Jika x + 2y = 10 dan x - y = 1, maka nilai x adalah...",
                'option_a' => '3',
                'option_b' => '4',
                'option_c' => '5',
                'option_d' => '6',
                'option_e' => '7',
                'correct_answer' => 'b',
                'explanation' => 'Dari sistem persamaan: x + 2y = 10 dan x - y = 1. Substitusi: x = 1 + y ke persamaan pertama: (1 + y) + 2y = 10, sehingga 3y = 9, y = 3. Maka x = 1 + 3 = 4.',
            ]);
        }

        // Penalaran Matematika Questions (5 questions)
        for ($i = 1; $i <= 5; $i++) {
            $questions[] = Question::create([
                'topic_id' => $penalaranMatematika->id,
                'question_text' => "Soal Penalaran Matematika $i: Sebuah segitiga memiliki sisi 3, 4, dan 5. Luas segitiga tersebut adalah...",
                'option_a' => '6',
                'option_b' => '8',
                'option_c' => '10',
                'option_d' => '12',
                'option_e' => '15',
                'correct_answer' => 'a',
                'explanation' => 'Segitiga dengan sisi 3, 4, 5 adalah segitiga siku-siku. Luas = 1/2 × alas × tinggi = 1/2 × 3 × 4 = 6.',
            ]);
        }

        // Literasi Indonesia Questions (3 questions)
        for ($i = 1; $i <= 3; $i++) {
            $questions[] = Question::create([
                'topic_id' => $literasiIndonesia->id,
                'question_text' => "Soal Literasi Indonesia $i: Kata yang tepat untuk melengkapi kalimat 'Dia _____ ke sekolah setiap hari' adalah...",
                'option_a' => 'pergi',
                'option_b' => 'berangkat',
                'option_c' => 'berjalan',
                'option_d' => 'menuju',
                'option_e' => 'datang',
                'correct_answer' => 'b',
                'explanation' => 'Kata "berangkat" paling tepat untuk menunjukkan aktivitas rutin pergi ke sekolah.',
            ]);
        }

        // Literasi Inggris Questions (2 questions)
        for ($i = 1; $i <= 2; $i++) {
            $questions[] = Question::create([
                'topic_id' => $literasiInggris->id,
                'question_text' => "English Literacy Question $i: Choose the correct form: 'She _____ to school every day.'",
                'option_a' => 'go',
                'option_b' => 'goes',
                'option_c' => 'going',
                'option_d' => 'gone',
                'option_e' => 'went',
                'correct_answer' => 'b',
                'explanation' => 'For third person singular (she), we use "goes" in simple present tense.',
            ]);
        }

        // Create TryoutPackage
        $tryoutPackage = TryoutPackage::create([
            'title' => 'Try Out SNBT Perdana 2024',
            'slug' => 'try-out-snbt-perdana-2024',
            'duration_minutes' => 120,
            'status' => 'published',
        ]);

        // Attach first 10 questions to the tryout package
        $selectedQuestions = collect($questions)->take(10);
        foreach ($selectedQuestions as $index => $question) {
            $tryoutPackage->questions()->attach($question->id, ['order' => $index + 1]);
        }

        // Create Posts
        Post::create([
            'user_id' => $admin->id,
            'title' => 'Tips Sukses Menghadapi SNBT 2024',
            'slug' => 'tips-sukses-menghadapi-snbt-2024',
            'body' => 'SNBT (Seleksi Nasional Berbasis Tes) merupakan salah satu jalur masuk perguruan tinggi negeri yang paling diminati. Berikut adalah beberapa tips untuk sukses menghadapi SNBT 2024:

1. **Pahami Format Tes**: SNBT terdiri dari Tes Potensi Skolastik (TPS) dan Literasi dalam Bahasa Indonesia dan Bahasa Inggris.

2. **Latihan Rutin**: Kerjakan soal-soal latihan secara rutin untuk meningkatkan kemampuan dan kecepatan mengerjakan.

3. **Manajemen Waktu**: Pelajari cara mengatur waktu dengan baik selama tes berlangsung.

4. **Jaga Kesehatan**: Pastikan kondisi fisik dan mental dalam keadaan prima menjelang tes.

5. **Simulasi Tes**: Ikuti try out atau simulasi tes untuk membiasakan diri dengan suasana tes yang sesungguhnya.',
            'status' => 'published',
        ]);

        Post::create([
            'user_id' => $admin->id,
            'title' => 'Strategi Mengerjakan Soal TPS dengan Efektif',
            'slug' => 'strategi-mengerjakan-soal-tps-dengan-efektif',
            'body' => 'Tes Potensi Skolastik (TPS) merupakan bagian penting dalam SNBT. Berikut strategi untuk mengerjakan soal TPS dengan efektif:

**Penalaran Umum:**
- Baca soal dengan teliti
- Identifikasi pola atau hubungan logis
- Eliminasi jawaban yang jelas salah

**Pengetahuan Kuantitatif:**
- Kuasai konsep dasar matematika
- Gunakan teknik eliminasi untuk mempercepat
- Jangan terpaku pada satu soal terlalu lama

**Penalaran Matematika:**
- Pahami konsep, bukan hanya rumus
- Latih kemampuan analisis soal cerita
- Gunakan logika matematika untuk memecahkan masalah',
            'status' => 'published',
        ]);

        Post::create([
            'user_id' => $admin->id,
            'title' => 'Panduan Literasi Bahasa Indonesia dan Inggris untuk SNBT',
            'slug' => 'panduan-literasi-bahasa-indonesia-dan-inggris-untuk-snbt',
            'body' => 'Bagian Literasi dalam SNBT menguji kemampuan memahami, menganalisis, dan mengevaluasi teks. Berikut panduannya:

**Literasi Bahasa Indonesia:**
- Pahami struktur teks (narasi, deskripsi, argumentasi, eksposisi)
- Latih kemampuan mengidentifikasi ide pokok dan pendukung
- Pelajari kosakata dan tata bahasa Indonesia yang baik

**Literasi Bahasa Inggris:**
- Tingkatkan vocabulary dengan membaca teks bahasa Inggris
- Pahami grammar dasar dan struktur kalimat
- Latih reading comprehension dengan berbagai jenis teks

**Tips Umum:**
- Baca dengan aktif, buat catatan mental
- Perhatikan kata kunci dalam pertanyaan
- Manfaatkan konteks untuk memahami kata yang tidak dikenal',
            'status' => 'draft',
        ]);
    }
}