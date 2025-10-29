<?php

namespace Database\Seeders;

use App\Models\Question;
use App\Models\Subject;
use App\Models\Topic;
use App\Models\TryoutPackage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BankSoalTryoutSeeder extends Seeder
{
    public function run(): void
    {
        if (Question::exists()) {
            return;
        }

        $subjectDefinitions = $this->getSubjectDefinitions();
        $questionIds = [];

        foreach ($subjectDefinitions as $subjectData) {
            $subject = Subject::firstOrCreate(
                ['slug' => $subjectData['slug']],
                [
                    'name' => $subjectData['name'],
                    'subtest_order' => $subjectData['subtest_order'],
                ]
            );

            $subject->update([
                'name' => $subjectData['name'],
                'subtest_order' => $subjectData['subtest_order'],
            ]);

            foreach ($subjectData['topics'] as $topicData) {
                $topic = Topic::firstOrCreate(
                    [
                        'slug' => $topicData['slug'] ?? Str::slug($topicData['name']),
                        'subject_id' => $subject->id,
                    ],
                    ['name' => $topicData['name']]
                );

                $topic->update(['name' => $topicData['name']]);

                foreach ($topicData['questions'] as $questionData) {
                    $question = Question::create([
                        'topic_id' => $topic->id,
                        'question_text' => $questionData['question_text'],
                        'option_a' => $questionData['options']['a'],
                        'option_b' => $questionData['options']['b'],
                        'option_c' => $questionData['options']['c'],
                        'option_d' => $questionData['options']['d'],
                        'option_e' => $questionData['options']['e'],
                        'correct_answer' => $questionData['correct_answer'],
                        'explanation' => $questionData['explanation'],
                    ]);

                    $questionIds[] = $question->id;
                }
            }
        }

        $tryoutPackage = TryoutPackage::firstOrCreate(
            ['slug' => 'tryout-snbtku-demo-1'],
            [
                'title' => 'Tryout SNBTKU Demo #1',
                'duration_minutes' => 195,
                'status' => 'published',
            ]
        );

        $order = 1;
        $syncData = [];
        foreach ($questionIds as $questionId) {
            $syncData[$questionId] = ['order' => $order++];
        }

        $tryoutPackage->questions()->sync($syncData);
    }

    private function getSubjectDefinitions(): array
    {
        return [
            [
                'name' => 'Penalaran Umum',
                'slug' => 'penalaran-umum',
                'subtest_order' => 1,
                'topics' => [
                    [
                        'name' => 'Logika Deduktif',
                        'questions' => [
                            [
                                'question_text' => 'Jika semua siswa rajin belajar dan Andi adalah siswa, kesimpulan paling tepat adalah...',
                                'options' => [
                                    'a' => 'Andi rajin belajar',
                                    'b' => 'Andi malas belajar',
                                    'c' => 'Andi bukan siswa',
                                    'd' => 'Andi tidak pernah belajar',
                                    'e' => 'Kesimpulan tidak dapat dibuat',
                                ],
                                'correct_answer' => 'a',
                                'explanation' => 'Premis universal menjelaskan semua siswa rajin sehingga Andi sebagai siswa juga rajin.',
                            ],
                            [
                                'question_text' => 'Premis: Tidak ada peserta yang terlambat. Dodi adalah peserta. Kesimpulan yang valid adalah...',
                                'options' => [
                                    'a' => 'Dodi terlambat',
                                    'b' => 'Dodi hadir tepat waktu',
                                    'c' => 'Dodi tidak mengikuti kegiatan',
                                    'd' => 'Dodi bukan peserta',
                                    'e' => 'Tidak ada informasi tentang Dodi',
                                ],
                                'correct_answer' => 'b',
                                'explanation' => 'Jika tidak ada peserta terlambat maka seluruh peserta termasuk Dodi hadir tepat waktu.',
                            ],
                            [
                                'question_text' => 'Sebagian besar guru adalah pembaca buku. Sinta adalah guru. Pernyataan yang pasti benar adalah...',
                                'options' => [
                                    'a' => 'Sinta pasti pembaca buku',
                                    'b' => 'Sinta tidak menyukai buku',
                                    'c' => 'Sinta mungkin pembaca buku',
                                    'd' => 'Sinta bukan guru',
                                    'e' => 'Semua guru tidak membaca buku',
                                ],
                                'correct_answer' => 'c',
                                'explanation' => 'Premis menyatakan sebagian besar guru membaca buku sehingga Sinta mungkin termasuk.',
                            ],
                        ],
                    ],
                    [
                        'name' => 'Analogi',
                        'questions' => [
                            [
                                'question_text' => 'Padi berhubungan dengan petani sebagaimana kopi berhubungan dengan...',
                                'options' => [
                                    'a' => 'Nelayan',
                                    'b' => 'Perkebun',
                                    'c' => 'Pelukis',
                                    'd' => 'Sopir',
                                    'e' => 'Penulis',
                                ],
                                'correct_answer' => 'b',
                                'explanation' => 'Padi dibudidayakan petani sedangkan kopi dibudidayakan perkebun.',
                            ],
                            [
                                'question_text' => 'Mata digunakan untuk melihat sebagaimana telinga digunakan untuk...',
                                'options' => [
                                    'a' => 'Berbicara',
                                    'b' => 'Mendengar',
                                    'c' => 'Menulis',
                                    'd' => 'Mencium',
                                    'e' => 'Mengetik',
                                ],
                                'correct_answer' => 'b',
                                'explanation' => 'Fungsi telinga adalah mendengar.',
                            ],
                            [
                                'question_text' => 'Pensil digunakan untuk menulis sebagaimana sapu digunakan untuk...',
                                'options' => [
                                    'a' => 'Membersihkan lantai',
                                    'b' => 'Menjahit pakaian',
                                    'c' => 'Memasak makanan',
                                    'd' => 'Menghias kelas',
                                    'e' => 'Memotong kayu',
                                ],
                                'correct_answer' => 'a',
                                'explanation' => 'Relasi fungsi alat: sapu berfungsi membersihkan lantai.',
                            ],
                        ],
                    ],
                ],
            ],
            [
                'name' => 'Kemampuan Memahami Bacaan dan Menulis',
                'slug' => 'kemampuan-memahami-bacaan-menulis',
                'subtest_order' => 2,
                'topics' => [
                    [
                        'name' => 'Pemahaman Bacaan',
                        'questions' => [
                            [
                                'question_text' => 'Paragraf: Perpustakaan sekolah memperpanjang jam layanan agar siswa dapat belajar lebih lama. Ide pokok paragraf tersebut adalah...',
                                'options' => [
                                    'a' => 'Perpustakaan menambah koleksi buku',
                                    'b' => 'Jam layanan perpustakaan diperpanjang',
                                    'c' => 'Siswa harus belajar lebih lama',
                                    'd' => 'Belajar di rumah lebih efektif',
                                    'e' => 'Perpustakaan tutup lebih awal',
                                ],
                                'correct_answer' => 'b',
                                'explanation' => 'Kalimat utama menegaskan perpanjangan jam layanan perpustakaan.',
                            ],
                            [
                                'question_text' => 'Paragraf: Guna mengurangi sampah plastik, pemerintah mendukung penggunaan tas belanja ramah lingkungan. Pernyataan yang sesuai adalah...',
                                'options' => [
                                    'a' => 'Tas plastik lebih murah digunakan',
                                    'b' => 'Pemerintah membatasi kegiatan belanja',
                                    'c' => 'Pemerintah mendorong tas belanja ramah lingkungan',
                                    'd' => 'Masyarakat dilarang belanja di pasar',
                                    'e' => 'Kantong kertas diwajibkan untuk semua',
                                ],
                                'correct_answer' => 'c',
                                'explanation' => 'Paragraf menekankan dukungan pemerintah pada tas ramah lingkungan.',
                            ],
                            [
                                'question_text' => 'Paragraf: Penelitian membuktikan tidur cukup meningkatkan konsentrasi siswa di kelas. Kesimpulan yang tepat adalah...',
                                'options' => [
                                    'a' => 'Tidur tidak memengaruhi konsentrasi',
                                    'b' => 'Siswa wajib belajar di malam hari',
                                    'c' => 'Tidur yang cukup membantu konsentrasi belajar',
                                    'd' => 'Penelitian tersebut tidak valid',
                                    'e' => 'Konsentrasi tidak penting saat belajar',
                                ],
                                'correct_answer' => 'c',
                                'explanation' => 'Hasil penelitian menunjukkan manfaat tidur cukup bagi konsentrasi.',
                            ],
                        ],
                    ],
                    [
                        'name' => 'Menulis Efektif',
                        'questions' => [
                            [
                                'question_text' => 'Kalimat manakah yang paling efektif?',
                                'options' => [
                                    'a' => 'Para siswa-siswa sedang belajar di perpustakaan.',
                                    'b' => 'Para siswa belajar di perpustakaan sekolah.',
                                    'c' => 'Siswa-siswa sedang pada belajar di perpustakaan.',
                                    'd' => 'Sedang belajar para siswa di perpustakaan.',
                                    'e' => 'Belajar para siswa di perpustakaan sekolahnya.',
                                ],
                                'correct_answer' => 'b',
                                'explanation' => 'Kalimat b ringkas dan tidak menggunakan kata ganda.',
                            ],
                            [
                                'question_text' => 'Pilih kalimat yang menggunakan tanda baca dengan benar.',
                                'options' => [
                                    'a' => 'Raka membawa buku, pulpen dan penggaris.',
                                    'b' => 'Raka membawa buku pulpen, dan penggaris.',
                                    'c' => 'Raka membawa buku, pulpen, dan penggaris.',
                                    'd' => 'Raka membawa, buku, pulpen, dan penggaris.',
                                    'e' => 'Raka membawa buku pulpen dan, penggaris.',
                                ],
                                'correct_answer' => 'c',
                                'explanation' => 'Deret tiga unsur dipisah koma kecuali sebelum kata penghubung terakhir.',
                            ],
                            [
                                'question_text' => 'Kalimat dengan pilihan kata tepat adalah...',
                                'options' => [
                                    'a' => 'Sekolah mengadakan perlombaan guna meningkatkan daya saing siswa.',
                                    'b' => 'Sekolah mengadakan perlombaan supaya jam kosong.',
                                    'c' => 'Sekolah mengadakan perlombaan agar siswa tidak belajar.',
                                    'd' => 'Sekolah mengadakan perlombaan untuk memperbanyak tugas.',
                                    'e' => 'Sekolah mengadakan perlombaan karena kelas kosong.',
                                ],
                                'correct_answer' => 'a',
                                'explanation' => 'Pilihan a menggunakan kata yang efektif dan tujuan positif.',
                            ],
                        ],
                    ],
                ],
            ],
            [
                'name' => 'Pengetahuan dan Pemahaman Umum',
                'slug' => 'pengetahuan-pemahaman-umum',
                'subtest_order' => 3,
                'topics' => [
                    [
                        'name' => 'Pengetahuan Umum',
                        'questions' => [
                            [
                                'question_text' => 'Ibu kota negara Indonesia adalah...',
                                'options' => [
                                    'a' => 'Surabaya',
                                    'b' => 'Jakarta',
                                    'c' => 'Bandung',
                                    'd' => 'Medan',
                                    'e' => 'Makassar',
                                ],
                                'correct_answer' => 'b',
                                'explanation' => 'Secara administratif pusat pemerintahan saat ini berada di Jakarta.',
                            ],
                            [
                                'question_text' => 'Lembaga negara yang bertugas membuat undang-undang adalah...',
                                'options' => [
                                    'a' => 'MA',
                                    'b' => 'MK',
                                    'c' => 'DPR',
                                    'd' => 'KPK',
                                    'e' => 'Polri',
                                ],
                                'correct_answer' => 'c',
                                'explanation' => 'Dewan Perwakilan Rakyat menjalankan fungsi legislasi.',
                            ],
                            [
                                'question_text' => 'Hari Kemerdekaan Indonesia diperingati setiap tanggal...',
                                'options' => [
                                    'a' => '17 Juli',
                                    'b' => '17 Agustus',
                                    'c' => '20 Mei',
                                    'd' => '1 Juni',
                                    'e' => '10 November',
                                ],
                                'correct_answer' => 'b',
                                'explanation' => 'Proklamasi kemerdekaan Indonesia terjadi pada 17 Agustus 1945.',
                            ],
                        ],
                    ],
                    [
                        'name' => 'Pemahaman Konteks',
                        'questions' => [
                            [
                                'question_text' => 'Rapat siswa membahas program bakti sosial. Sikap yang tepat adalah...',
                                'options' => [
                                    'a' => 'Diam saja selama rapat',
                                    'b' => 'Mengkritik tanpa solusi',
                                    'c' => 'Memberikan usulan kegiatan yang realistis',
                                    'd' => 'Menolak semua keputusan rapat',
                                    'e' => 'Meninggalkan rapat sebelum selesai',
                                ],
                                'correct_answer' => 'c',
                                'explanation' => 'Partisipasi aktif ditunjukkan dengan memberi usulan yang bisa dijalankan.',
                            ],
                            [
                                'question_text' => 'Sekolah menggalang dana korban banjir. Kontribusi yang tepat adalah...',
                                'options' => [
                                    'a' => 'Menonton kegiatan dari jauh',
                                    'b' => 'Menyebarkan kabar tidak benar',
                                    'c' => 'Ikut menjadi relawan penggalangan dana',
                                    'd' => 'Melarang teman untuk membantu',
                                    'e' => 'Mengabaikan kegiatan sosial',
                                ],
                                'correct_answer' => 'c',
                                'explanation' => 'Ikut menjadi relawan menunjukkan kepedulian sosial.',
                            ],
                            [
                                'question_text' => 'Jika teman sekelompok tidak hadir saat presentasi, tindakan terbaik adalah...',
                                'options' => [
                                    'a' => 'Membatalkan presentasi',
                                    'b' => 'Mengabaikan tugas kelompok',
                                    'c' => 'Tetap presentasi dan melaporkan kondisi kepada guru',
                                    'd' => 'Meninggalkan kelas saat itu juga',
                                    'e' => 'Memarahi teman di depan kelas',
                                ],
                                'correct_answer' => 'c',
                                'explanation' => 'Tanggung jawab tetap dijalankan sambil menyampaikan kondisi kepada guru.',
                            ],
                        ],
                    ],
                ],
            ],
            [
                'name' => 'Kemampuan Kuantitatif',
                'slug' => 'kemampuan-kuantitatif',
                'subtest_order' => 4,
                'topics' => [
                    [
                        'name' => 'Aritmetika',
                        'questions' => [
                            [
                                'question_text' => 'Hasil dari 48 / 6 + 12 adalah...',
                                'options' => [
                                    'a' => '4',
                                    'b' => '8',
                                    'c' => '12',
                                    'd' => '20',
                                    'e' => '28',
                                ],
                                'correct_answer' => 'd',
                                'explanation' => '48 / 6 = 8 kemudian 8 + 12 = 20.',
                            ],
                            [
                                'question_text' => 'Jika harga buku Rp35.000 mendapat diskon 20%, harga setelah diskon adalah...',
                                'options' => [
                                    'a' => 'Rp7.000',
                                    'b' => 'Rp28.000',
                                    'c' => 'Rp29.000',
                                    'd' => 'Rp30.000',
                                    'e' => 'Rp32.000',
                                ],
                                'correct_answer' => 'b',
                                'explanation' => 'Diskon dua puluh persen dari 35.000 adalah 7.000 sehingga harga menjadi 28.000.',
                            ],
                            [
                                'question_text' => 'Rata-rata nilai empat siswa 78. Jika tiga nilai pertama 80, 75, 74 maka nilai keempat adalah...',
                                'options' => [
                                    'a' => '75',
                                    'b' => '76',
                                    'c' => '79',
                                    'd' => '83',
                                    'e' => '85',
                                ],
                                'correct_answer' => 'd',
                                'explanation' => 'Total nilai 78 x 4 = 312. Nilai keempat 312 - (80 + 75 + 74) = 83.',
                            ],
                        ],
                    ],
                    [
                        'name' => 'Aljabar',
                        'questions' => [
                            [
                                'question_text' => 'Jika 3x + 5 = 20, nilai x adalah...',
                                'options' => [
                                    'a' => '3',
                                    'b' => '4',
                                    'c' => '5',
                                    'd' => '6',
                                    'e' => '8',
                                ],
                                'correct_answer' => 'c',
                                'explanation' => '3x = 20 - 5 = 15 sehingga x = 5.',
                            ],
                            [
                                'question_text' => 'Nilai x yang memenuhi 2x - 4 = 10 adalah...',
                                'options' => [
                                    'a' => '5',
                                    'b' => '6',
                                    'c' => '7',
                                    'd' => '8',
                                    'e' => '9',
                                ],
                                'correct_answer' => 'c',
                                'explanation' => '2x = 14 sehingga x = 7.',
                            ],
                            [
                                'question_text' => 'Jika y = 4x dan x = 6, maka nilai y adalah...',
                                'options' => [
                                    'a' => '12',
                                    'b' => '18',
                                    'c' => '20',
                                    'd' => '24',
                                    'e' => '30',
                                ],
                                'correct_answer' => 'd',
                                'explanation' => 'y = 4 x 6 = 24.',
                            ],
                        ],
                    ],
                ],
            ],
            [
                'name' => 'Literasi dalam Bahasa Indonesia',
                'slug' => 'literasi-bahasa-indonesia',
                'subtest_order' => 5,
                'topics' => [
                    [
                        'name' => 'Tata Bahasa',
                        'questions' => [
                            [
                                'question_text' => 'Kalimat baku terdapat pada...',
                                'options' => [
                                    'a' => 'Mereka pada pergi ke perpustakaan.',
                                    'b' => 'Mereka pergi ke perpustakaan untuk meminjam buku.',
                                    'c' => 'Mereka lagi pergi ke perpustakaan buat baca.',
                                    'd' => 'Mereka pergi perpustakaan meminjam buku.',
                                    'e' => 'Mereka pada meminjam buku di perpustakaan.',
                                ],
                                'correct_answer' => 'b',
                                'explanation' => 'Kalimat b menggunakan struktur baku dan pilihan kata tepat.',
                            ],
                            [
                                'question_text' => 'Penulisan huruf kapital yang benar terdapat pada kalimat...',
                                'options' => [
                                    'a' => 'kegiatan itu dilaksanakan pada bulan juni.',
                                    'b' => 'Kegiatan itu dilaksanakan pada bulan Juni.',
                                    'c' => 'Kegiatan Itu Dilaksanakan pada Bulan Juni.',
                                    'd' => 'Kegiatan itu dilaksanakan pada Bulan juni.',
                                    'e' => 'kegiatan itu Dilaksanakan pada bulan juni.',
                                ],
                                'correct_answer' => 'b',
                                'explanation' => 'Nama bulan ditulis dengan huruf kapital pada awal kata.',
                            ],
                            [
                                'question_text' => 'Kalimat dengan imbuhan yang tepat adalah...',
                                'options' => [
                                    'a' => 'Para siswa diminta mengumpulkan tugas tepat waktu.',
                                    'b' => 'Para siswa diminta kumpulkan tugas tepat waktu.',
                                    'c' => 'Para siswa diminta mengumpulkan tugas tepat waktunya.',
                                    'd' => 'Para siswa diminta kumpulkan tugas tepat waktunya.',
                                    'e' => 'Para siswa diminta tugasnya dikumpulkan tepat waktu.',
                                ],
                                'correct_answer' => 'a',
                                'explanation' => 'Imbuhan dan struktur kalimat pada pilihan a sudah benar.',
                            ],
                        ],
                    ],
                    [
                        'name' => 'Pemahaman Teks',
                        'questions' => [
                            [
                                'question_text' => 'Paragraf: Festival seni diadakan untuk menumbuhkan kreativitas siswa dan mengenalkan budaya lokal. Ide utama paragraf tersebut adalah...',
                                'options' => [
                                    'a' => 'Festival seni menghabiskan banyak dana.',
                                    'b' => 'Festival seni mengenalkan budaya lokal dan kreativitas.',
                                    'c' => 'Siswa tidak tertarik dengan festival seni.',
                                    'd' => 'Festival seni khusus untuk siswa kelas seni.',
                                    'e' => 'Festival seni dilaksanakan setiap minggu.',
                                ],
                                'correct_answer' => 'b',
                                'explanation' => 'Kalimat utama menegaskan tujuan festival seni.',
                            ],
                            [
                                'question_text' => 'Paragraf: Kegiatan membaca pagi meningkatkan minat literasi dan menumbuhkan kebiasaan baik bagi siswa. Kesimpulan terbaik adalah...',
                                'options' => [
                                    'a' => 'Membaca pagi tidak memberi manfaat.',
                                    'b' => 'Membaca pagi meningkatkan kebiasaan baik.',
                                    'c' => 'Siswa tidak wajib mengikuti membaca pagi.',
                                    'd' => 'Membaca pagi mengurangi minat belajar.',
                                    'e' => 'Guru menolak kegiatan membaca pagi.',
                                ],
                                'correct_answer' => 'b',
                                'explanation' => 'Paragraf menyebutkan manfaat membaca pagi.',
                            ],
                            [
                                'question_text' => 'Paragraf: Sekolah menyediakan sudut baca nyaman agar siswa mudah mengakses bacaan. Tujuan utama kebijakan tersebut adalah...',
                                'options' => [
                                    'a' => 'Mengurangi jumlah buku di perpustakaan.',
                                    'b' => 'Menyediakan tempat menyimpan peralatan olahraga.',
                                    'c' => 'Memudahkan siswa membaca sumber belajar.',
                                    'd' => 'Mengurangi waktu belajar di kelas.',
                                    'e' => 'Mengganti perpustakaan dengan sudut baca.',
                                ],
                                'correct_answer' => 'c',
                                'explanation' => 'Sudut baca disediakan agar siswa mudah membaca.',
                            ],
                        ],
                    ],
                ],
            ],
            [
                'name' => 'Literasi dalam Bahasa Inggris',
                'slug' => 'literasi-bahasa-inggris',
                'subtest_order' => 6,
                'topics' => [
                    [
                        'name' => 'Grammar',
                        'questions' => [
                            [
                                'question_text' => 'Choose the correct sentence.',
                                'options' => [
                                    'a' => 'She go to school every day.',
                                    'b' => 'She goes to school every day.',
                                    'c' => 'She going to school every day.',
                                    'd' => 'She gone to school every day.',
                                    'e' => 'She gone going to school every day.',
                                ],
                                'correct_answer' => 'b',
                                'explanation' => 'Subjek she memerlukan verb goes pada simple present.',
                            ],
                            [
                                'question_text' => 'Choose the correct form to complete: We ____ dinner right now.',
                                'options' => [
                                    'a' => 'eat',
                                    'b' => 'eats',
                                    'c' => 'are eating',
                                    'd' => 'is eating',
                                    'e' => 'ate',
                                ],
                                'correct_answer' => 'c',
                                'explanation' => 'Present continuous menggunakan are ditambah verb ing.',
                            ],
                            [
                                'question_text' => 'Choose the correct question: ____ you finish the assignment yesterday?',
                                'options' => [
                                    'a' => 'Do',
                                    'b' => 'Does',
                                    'c' => 'Did',
                                    'd' => 'Are',
                                    'e' => 'Will',
                                ],
                                'correct_answer' => 'c',
                                'explanation' => 'Kalimat tanya past simple memakai did.',
                            ],
                        ],
                    ],
                    [
                        'name' => 'Reading Comprehension',
                        'questions' => [
                            [
                                'question_text' => 'Teks: The school library opens at seven in the morning so students can read before class. Gagasan utama teks tersebut adalah...',
                                'options' => [
                                    'a' => 'Siswa harus datang terlambat.',
                                    'b' => 'Perpustakaan tutup di pagi hari.',
                                    'c' => 'Perpustakaan membuka layanan lebih awal untuk siswa.',
                                    'd' => 'Membaca sebelum kelas dilarang.',
                                    'e' => 'Pelajaran dimulai pukul tujuh tepat.',
                                ],
                                'correct_answer' => 'c',
                                'explanation' => 'Kalimat inti menyebutkan perpustakaan buka lebih awal untuk siswa.',
                            ],
                            [
                                'question_text' => 'Teks: Teachers encourage students to join the English club to practise speaking with friends. Informasi yang tersirat adalah...',
                                'options' => [
                                    'a' => 'Berlatih berbicara tidak penting.',
                                    'b' => 'Siswa sebaiknya menghindari klub.',
                                    'c' => 'English club membantu siswa berlatih berbicara.',
                                    'd' => 'Guru melarang siswa mengikuti klub apa pun.',
                                    'e' => 'English club hanya diadakan setahun sekali.',
                                ],
                                'correct_answer' => 'c',
                                'explanation' => 'Guru mendorong siswa bergabung agar dapat berlatih berbicara.',
                            ],
                            [
                                'question_text' => 'Teks: During the science fair, students present simple experiments and explain the results to visitors. Tujuan teks tersebut adalah...',
                                'options' => [
                                    'a' => 'Mengajak siswa bergabung tim sepak bola.',
                                    'b' => 'Menjelaskan kegiatan dalam science fair.',
                                    'c' => 'Melarang percobaan di sekolah.',
                                    'd' => 'Menjelaskan alasan sains sulit dipahami.',
                                    'e' => 'Mengumumkan pemenang lomba matematika.',
                                ],
                                'correct_answer' => 'b',
                                'explanation' => 'Teks menggambarkan aktivitas selama science fair.',
                            ],
                        ],
                    ],
                ],
            ],
            [
                'name' => 'Penalaran Matematika',
                'slug' => 'penalaran-matematika',
                'subtest_order' => 7,
                'topics' => [
                    [
                        'name' => 'Geometri',
                        'questions' => [
                            [
                                'question_text' => 'Luas persegi dengan sisi 8 cm adalah...',
                                'options' => [
                                    'a' => '16 cm2',
                                    'b' => '32 cm2',
                                    'c' => '48 cm2',
                                    'd' => '64 cm2',
                                    'e' => '80 cm2',
                                ],
                                'correct_answer' => 'd',
                                'explanation' => 'Luas persegi = sisi x sisi = 8 x 8 = 64 cm2.',
                            ],
                            [
                                'question_text' => 'Keliling lingkaran dengan jari-jari 7 cm dan pi = 22/7 adalah...',
                                'options' => [
                                    'a' => '22 cm',
                                    'b' => '38 cm',
                                    'c' => '44 cm',
                                    'd' => '66 cm',
                                    'e' => '88 cm',
                                ],
                                'correct_answer' => 'c',
                                'explanation' => 'Keliling = 2 x pi x r = 2 x 22/7 x 7 = 44 cm.',
                            ],
                            [
                                'question_text' => 'Luas segitiga dengan alas 10 cm dan tinggi 12 cm adalah...',
                                'options' => [
                                    'a' => '30 cm2',
                                    'b' => '48 cm2',
                                    'c' => '50 cm2',
                                    'd' => '60 cm2',
                                    'e' => '72 cm2',
                                ],
                                'correct_answer' => 'd',
                                'explanation' => 'Luas segitiga = 1/2 x alas x tinggi = 1/2 x 10 x 12 = 60 cm2.',
                            ],
                        ],
                    ],
                    [
                        'name' => 'Statistika',
                        'questions' => [
                            [
                                'question_text' => 'Rata-rata dari data 3, 5, 7, 9, 11 adalah...',
                                'options' => [
                                    'a' => '5',
                                    'b' => '7',
                                    'c' => '8',
                                    'd' => '9',
                                    'e' => '11',
                                ],
                                'correct_answer' => 'b',
                                'explanation' => 'Jumlah data 35 dan dibagi 5 menghasilkan rata-rata 7.',
                            ],
                            [
                                'question_text' => 'Median dari data 2, 4, 6, 8, 10, 12, 14 adalah...',
                                'options' => [
                                    'a' => '6',
                                    'b' => '7',
                                    'c' => '8',
                                    'd' => '9',
                                    'e' => '10',
                                ],
                                'correct_answer' => 'c',
                                'explanation' => 'Nilai tengah dari tujuh data terurut tersebut adalah 8.',
                            ],
                            [
                                'question_text' => 'Modus dari data 4, 4, 5, 6, 7, 7, 7, 8 adalah...',
                                'options' => [
                                    'a' => '4',
                                    'b' => '5',
                                    'c' => '6',
                                    'd' => '7',
                                    'e' => '8',
                                ],
                                'correct_answer' => 'd',
                                'explanation' => 'Angka 7 muncul paling banyak sehingga menjadi modus.',
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
}
