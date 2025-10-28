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

        // Create 7 Subjects with correct SNBT order
        $subjects = [
            ['name' => 'Penalaran Umum', 'slug' => 'penalaran-umum', 'subtest_order' => 1],
            ['name' => 'Kemampuan Memahami Bacaan dan Menulis', 'slug' => 'kemampuan-memahami-bacaan-menulis', 'subtest_order' => 2],
            ['name' => 'Pengetahuan dan Pemahaman Umum', 'slug' => 'pengetahuan-pemahaman-umum', 'subtest_order' => 3],
            ['name' => 'Kemampuan Kuantitatif', 'slug' => 'kemampuan-kuantitatif', 'subtest_order' => 4],
            ['name' => 'Literasi dalam Bahasa Indonesia', 'slug' => 'literasi-bahasa-indonesia', 'subtest_order' => 5],
            ['name' => 'Literasi dalam Bahasa Inggris', 'slug' => 'literasi-bahasa-inggris', 'subtest_order' => 6],
            ['name' => 'Penalaran Matematika', 'slug' => 'penalaran-matematika', 'subtest_order' => 7],
        ];

        $createdSubjects = [];
        foreach ($subjects as $subjectData) {
            $createdSubjects[] = Subject::create($subjectData);
        }

        // Create Topics for each Subject
        $topics = [];
        
        // Topics for Penalaran Umum
        $topics[] = Topic::create([
            'subject_id' => $createdSubjects[0]->id,
            'name' => 'Logika Deduktif',
            'slug' => 'logika-deduktif',
        ]);
        $topics[] = Topic::create([
            'subject_id' => $createdSubjects[0]->id,
            'name' => 'Analogi',
            'slug' => 'analogi',
        ]);

        // Topics for KMBM
        $topics[] = Topic::create([
            'subject_id' => $createdSubjects[1]->id,
            'name' => 'Pemahaman Bacaan',
            'slug' => 'pemahaman-bacaan',
        ]);
        $topics[] = Topic::create([
            'subject_id' => $createdSubjects[1]->id,
            'name' => 'Menulis Efektif',
            'slug' => 'menulis-efektif',
        ]);

        // Topics for PPU
        $topics[] = Topic::create([
            'subject_id' => $createdSubjects[2]->id,
            'name' => 'Pengetahuan Umum',
            'slug' => 'pengetahuan-umum',
        ]);
        $topics[] = Topic::create([
            'subject_id' => $createdSubjects[2]->id,
            'name' => 'Pemahaman Konteks',
            'slug' => 'pemahaman-konteks',
        ]);

        // Topics for Kemampuan Kuantitatif
        $topics[] = Topic::create([
            'subject_id' => $createdSubjects[3]->id,
            'name' => 'Aritmatika',
            'slug' => 'aritmatika',
        ]);
        $topics[] = Topic::create([
            'subject_id' => $createdSubjects[3]->id,
            'name' => 'Aljabar',
            'slug' => 'aljabar',
        ]);

        // Topics for Literasi Indonesia
        $topics[] = Topic::create([
            'subject_id' => $createdSubjects[4]->id,
            'name' => 'Tata Bahasa',
            'slug' => 'tata-bahasa',
        ]);
        $topics[] = Topic::create([
            'subject_id' => $createdSubjects[4]->id,
            'name' => 'Pemahaman Teks',
            'slug' => 'pemahaman-teks',
        ]);

        // Topics for Literasi Inggris
        $topics[] = Topic::create([
            'subject_id' => $createdSubjects[5]->id,
            'name' => 'Grammar',
            'slug' => 'grammar',
        ]);
        $topics[] = Topic::create([
            'subject_id' => $createdSubjects[5]->id,
            'name' => 'Reading Comprehension',
            'slug' => 'reading-comprehension',
        ]);

        // Topics for Penalaran Matematika
        $topics[] = Topic::create([
            'subject_id' => $createdSubjects[6]->id,
            'name' => 'Geometri',
            'slug' => 'geometri',
        ]);
        $topics[] = Topic::create([
            'subject_id' => $createdSubjects[6]->id,
            'name' => 'Statistika',
            'slug' => 'statistika',
        ]);

        // Create Questions for each Topic
        $allQuestions = [];

        // Penalaran Umum Questions (30 questions total)
        for ($i = 1; $i <= 15; $i++) {
            $allQuestions[] = Question::create([
                'topic_id' => $topics[0]->id, // Logika Deduktif
                'question_text' => "Soal Logika Deduktif $i: Jika semua A adalah B, dan semua B adalah C, maka dapat disimpulkan bahwa...",
                'option_a' => 'Semua A adalah C',
                'option_b' => 'Semua C adalah A',
                'option_c' => 'Beberapa A adalah C',
                'option_d' => 'Tidak ada A yang C',
                'option_e' => 'Tidak dapat disimpulkan',
                'correct_answer' => 'a',
                'explanation' => 'Berdasarkan silogisme, jika A⊆B dan B⊆C, maka A⊆C (semua A adalah C).',
            ]);
        }

        for ($i = 1; $i <= 15; $i++) {
            $allQuestions[] = Question::create([
                'topic_id' => $topics[1]->id, // Analogi
                'question_text' => "Soal Analogi $i: Buku : Perpustakaan = Mobil : ...",
                'option_a' => 'Jalan',
                'option_b' => 'Garasi',
                'option_c' => 'Bensin',
                'option_d' => 'Roda',
                'option_e' => 'Mesin',
                'correct_answer' => 'b',
                'explanation' => 'Buku disimpan di perpustakaan, mobil disimpan di garasi.',
            ]);
        }

        // KMBM Questions (20 questions total)
        for ($i = 1; $i <= 10; $i++) {
            $allQuestions[] = Question::create([
                'topic_id' => $topics[2]->id, // Pemahaman Bacaan
                'question_text' => "Soal Pemahaman Bacaan $i: Berdasarkan teks berikut, ide pokok paragraf pertama adalah...",
                'option_a' => 'Pentingnya pendidikan',
                'option_b' => 'Manfaat teknologi',
                'option_c' => 'Dampak globalisasi',
                'option_d' => 'Perubahan sosial',
                'option_e' => 'Kemajuan zaman',
                'correct_answer' => 'a',
                'explanation' => 'Ide pokok dapat ditemukan di kalimat utama paragraf.',
            ]);
        }

        for ($i = 1; $i <= 10; $i++) {
            $allQuestions[] = Question::create([
                'topic_id' => $topics[3]->id, // Menulis Efektif
                'question_text' => "Soal Menulis Efektif $i: Kalimat yang paling efektif adalah...",
                'option_a' => 'Dia pergi ke sekolah dengan berjalan kaki',
                'option_b' => 'Dia berjalan kaki ke sekolah',
                'option_c' => 'Dia pergi ke sekolah sambil berjalan kaki',
                'option_d' => 'Dia ke sekolah dengan cara berjalan kaki',
                'option_e' => 'Dia menuju sekolah dengan berjalan kaki',
                'correct_answer' => 'b',
                'explanation' => 'Kalimat efektif menggunakan kata-kata yang tepat dan tidak berlebihan.',
            ]);
        }

        // PPU Questions (20 questions total)
        for ($i = 1; $i <= 10; $i++) {
            $allQuestions[] = Question::create([
                'topic_id' => $topics[4]->id, // Pengetahuan Umum
                'question_text' => "Soal Pengetahuan Umum $i: Ibu kota Indonesia adalah...",
                'option_a' => 'Jakarta',
                'option_b' => 'Surabaya',
                'option_c' => 'Bandung',
                'option_d' => 'Medan',
                'option_e' => 'Yogyakarta',
                'correct_answer' => 'a',
                'explanation' => 'Jakarta adalah ibu kota Republik Indonesia.',
            ]);
        }

        for ($i = 1; $i <= 10; $i++) {
            $allQuestions[] = Question::create([
                'topic_id' => $topics[5]->id, // Pemahaman Konteks
                'question_text' => "Soal Pemahaman Konteks $i: Dalam konteks sejarah Indonesia, proklamasi kemerdekaan dibacakan pada tanggal...",
                'option_a' => '17 Agustus 1945',
                'option_b' => '17 Agustus 1944',
                'option_c' => '17 Agustus 1946',
                'option_d' => '18 Agustus 1945',
                'option_e' => '16 Agustus 1945',
                'correct_answer' => 'a',
                'explanation' => 'Proklamasi kemerdekaan Indonesia dibacakan pada 17 Agustus 1945.',
            ]);
        }

        // Kemampuan Kuantitatif Questions (20 questions total)
        for ($i = 1; $i <= 10; $i++) {
            $allQuestions[] = Question::create([
                'topic_id' => $topics[6]->id, // Aritmatika
                'question_text' => "Soal Aritmatika $i: Hasil dari 15 + 25 × 2 adalah...",
                'option_a' => '80',
                'option_b' => '65',
                'option_c' => '55',
                'option_d' => '45',
                'option_e' => '35',
                'correct_answer' => 'b',
                'explanation' => 'Menggunakan aturan operasi: 15 + (25 × 2) = 15 + 50 = 65.',
            ]);
        }

        for ($i = 1; $i <= 10; $i++) {
            $allQuestions[] = Question::create([
                'topic_id' => $topics[7]->id, // Aljabar
                'question_text' => "Soal Aljabar $i: Jika x + 2y = 10 dan x - y = 1, maka nilai x adalah...",
                'option_a' => '3',
                'option_b' => '4',
                'option_c' => '5',
                'option_d' => '6',
                'option_e' => '7',
                'correct_answer' => 'b',
                'explanation' => 'Dari sistem persamaan: x = 1 + y, substitusi ke persamaan pertama: (1 + y) + 2y = 10, sehingga y = 3 dan x = 4.',
            ]);
        }

        // Literasi Indonesia Questions (30 questions total)
        for ($i = 1; $i <= 15; $i++) {
            $allQuestions[] = Question::create([
                'topic_id' => $topics[8]->id, // Tata Bahasa
                'question_text' => "Soal Tata Bahasa $i: Kata yang tepat untuk melengkapi kalimat 'Dia _____ ke sekolah setiap hari' adalah...",
                'option_a' => 'pergi',
                'option_b' => 'berangkat',
                'option_c' => 'berjalan',
                'option_d' => 'menuju',
                'option_e' => 'datang',
                'correct_answer' => 'b',
                'explanation' => 'Kata "berangkat" paling tepat untuk menunjukkan aktivitas rutin pergi ke sekolah.',
            ]);
        }

        for ($i = 1; $i <= 15; $i++) {
            $allQuestions[] = Question::create([
                'topic_id' => $topics[9]->id, // Pemahaman Teks
                'question_text' => "Soal Pemahaman Teks $i: Makna kata 'komprehensif' dalam konteks pendidikan adalah...",
                'option_a' => 'Menyeluruh',
                'option_b' => 'Terbatas',
                'option_c' => 'Khusus',
                'option_d' => 'Sederhana',
                'option_e' => 'Rumit',
                'correct_answer' => 'a',
                'explanation' => 'Komprehensif berarti menyeluruh atau mencakup semua aspek.',
            ]);
        }

        // Literasi Inggris Questions (20 questions total)
        for ($i = 1; $i <= 10; $i++) {
            $allQuestions[] = Question::create([
                'topic_id' => $topics[10]->id, // Grammar
                'question_text' => "Grammar Question $i: Choose the correct form: 'She _____ to school every day.'",
                'option_a' => 'go',
                'option_b' => 'goes',
                'option_c' => 'going',
                'option_d' => 'gone',
                'option_e' => 'went',
                'correct_answer' => 'b',
                'explanation' => 'For third person singular (she), we use "goes" in simple present tense.',
            ]);
        }

        for ($i = 1; $i <= 10; $i++) {
            $allQuestions[] = Question::create([
                'topic_id' => $topics[11]->id, // Reading Comprehension
                'question_text' => "Reading Comprehension $i: Based on the passage, the main idea is...",
                'option_a' => 'Education is important',
                'option_b' => 'Technology helps learning',
                'option_c' => 'Students need guidance',
                'option_d' => 'Schools are essential',
                'option_e' => 'Learning never stops',
                'correct_answer' => 'a',
                'explanation' => 'The main idea is usually stated in the topic sentence.',
            ]);
        }

        // Penalaran Matematika Questions (20 questions total)
        for ($i = 1; $i <= 10; $i++) {
            $allQuestions[] = Question::create([
                'topic_id' => $topics[12]->id, // Geometri
                'question_text' => "Soal Geometri $i: Sebuah segitiga memiliki sisi 3, 4, dan 5. Luas segitiga tersebut adalah...",
                'option_a' => '6',
                'option_b' => '8',
                'option_c' => '10',
                'option_d' => '12',
                'option_e' => '15',
                'correct_answer' => 'a',
                'explanation' => 'Segitiga dengan sisi 3, 4, 5 adalah segitiga siku-siku. Luas = 1/2 × 3 × 4 = 6.',
            ]);
        }

        for ($i = 1; $i <= 10; $i++) {
            $allQuestions[] = Question::create([
                'topic_id' => $topics[13]->id, // Statistika
                'question_text' => "Soal Statistika $i: Rata-rata dari data 2, 4, 6, 8, 10 adalah...",
                'option_a' => '5',
                'option_b' => '6',
                'option_c' => '7',
                'option_d' => '8',
                'option_e' => '9',
                'correct_answer' => 'b',
                'explanation' => 'Rata-rata = (2+4+6+8+10)/5 = 30/5 = 6.',
            ]);
        }

        // Create TryoutPackage with correct duration
        $tryoutPackage = TryoutPackage::create([
            'title' => 'Tryout SNBTKU #1',
            'slug' => 'tryout-snbtku-1',
            'duration_minutes' => 195, // 3 jam 15 menit sesuai SNBT
            'status' => 'published',
        ]);

        // Attach questions to tryout package with correct distribution per subtest
        $penalaranUmumQuestions = Question::whereHas('topic.subject', fn($q) => $q->where('subtest_order', 1))->take(30)->get();
        $kmbmQuestions = Question::whereHas('topic.subject', fn($q) => $q->where('subtest_order', 2))->take(20)->get();
        $ppuQuestions = Question::whereHas('topic.subject', fn($q) => $q->where('subtest_order', 3))->take(20)->get();
        $kuantitatifQuestions = Question::whereHas('topic.subject', fn($q) => $q->where('subtest_order', 4))->take(20)->get();
        $literasiIndoQuestions = Question::whereHas('topic.subject', fn($q) => $q->where('subtest_order', 5))->take(30)->get();
        $literasiIngQuestions = Question::whereHas('topic.subject', fn($q) => $q->where('subtest_order', 6))->take(20)->get();
        $penalaranMatQuestions = Question::whereHas('topic.subject', fn($q) => $q->where('subtest_order', 7))->take(20)->get();

        $allQuestionsForPackage = $penalaranUmumQuestions
            ->merge($kmbmQuestions)
            ->merge($ppuQuestions)
            ->merge($kuantitatifQuestions)
            ->merge($literasiIndoQuestions)
            ->merge($literasiIngQuestions)
            ->merge($penalaranMatQuestions);

        // Attach questions with proper order
        $order = 1;
        foreach ($allQuestionsForPackage as $question) {
            $tryoutPackage->questions()->attach($question->id, ['order' => $order++]);
        }

        // Create Posts
        Post::create([
            'user_id' => $admin->id,
            'title' => 'Tips Sukses Menghadapi SNBT 2024',
            'slug' => 'tips-sukses-menghadapi-snbt-2024',
            'body' => 'SNBT (Seleksi Nasional Berbasis Tes) merupakan salah satu jalur masuk perguruan tinggi negeri yang paling diminati. Berikut adalah beberapa tips untuk sukses menghadapi SNBT 2024:

1. **Pahami Format Tes**: SNBT terdiri dari 7 subtes dengan total durasi 195 menit.

2. **Latihan Rutin**: Kerjakan soal-soal latihan secara rutin untuk meningkatkan kemampuan dan kecepatan mengerjakan.

3. **Manajemen Waktu**: Pelajari cara mengatur waktu dengan baik selama tes berlangsung.

4. **Jaga Kesehatan**: Pastikan kondisi fisik dan mental dalam keadaan prima menjelang tes.

5. **Simulasi Tes**: Ikuti try out atau simulasi tes untuk membiasakan diri dengan suasana tes yang sesungguhnya.',
            'status' => 'published',
        ]);

        Post::create([
            'user_id' => $admin->id,
            'title' => 'Strategi Mengerjakan 7 Subtes SNBT',
            'slug' => 'strategi-mengerjakan-7-subtes-snbt',
            'body' => 'SNBT terdiri dari 7 subtes yang harus dikerjakan secara berurutan. Berikut strategi untuk setiap subtes:

**1. Penalaran Umum (30 soal)**
- Fokus pada logika dan pola
- Jangan terpaku pada satu soal terlalu lama

**2. KMBM (20 soal)**
- Baca dengan teliti
- Pahami konteks bacaan

**3. PPU (20 soal)**
- Gunakan pengetahuan umum
- Analisis konteks soal

**4. Kemampuan Kuantitatif (20 soal)**
- Kuasai operasi dasar
- Gunakan eliminasi jawaban

**5. Literasi Indonesia (30 soal)**
- Pahami tata bahasa
- Perhatikan makna kata

**6. Literasi Inggris (20 soal)**
- Fokus pada grammar dan vocabulary
- Baca dengan pemahaman

**7. Penalaran Matematika (20 soal)**
- Gunakan logika matematika
- Pahami konsep dasar',
            'status' => 'published',
        ]);
    }
}