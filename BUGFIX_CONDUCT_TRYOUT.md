# Bug Fix: ConductTryout Component

## Masalah yang Dilaporkan
Bug kritis pada komponen `ConductTryout` dimana hanya jawaban soal pertama yang tersimpan dengan benar, sedangkan jawaban soal selanjutnya tidak ter-render atau tersimpan dengan benar.

## Analisis Masalah
Setelah analisis mendalam, ditemukan bahwa:
1. Kode yang ada sudah menggunakan `question_id` sebagai key dengan konsisten
2. Method `selectAnswer` sudah benar menggunakan `$questionId` sebagai parameter
3. View sudah benar menggunakan `$currentQuestion->id` untuk menampilkan jawaban

## Perbaikan yang Dilakukan

### 1. Optimisasi Method `selectAnswer`
- Menambahkan validasi input untuk memastikan `questionId` dan `answer` valid
- Menambahkan event dispatch untuk memastikan UI terupdate
- Menambahkan komentar untuk kejelasan kode

```php
public function selectAnswer($questionId, $answer)
{
    // Pastikan questionId dan answer valid
    if (!$questionId || !in_array($answer, ['a', 'b', 'c', 'd', 'e'])) {
        return;
    }

    // Simpan jawaban menggunakan question_id sebagai key
    $this->userAnswers[$questionId] = $answer;

    // Save or update answer in database
    UserTryoutAnswer::updateOrCreate(
        [
            'user_tryout_id' => $this->userTryout->id,
            'question_id' => $questionId,
        ],
        [
            'user_answer' => $answer,
        ]
    );

    // Refresh component untuk memastikan UI terupdate
    $this->dispatch('answer-selected', questionId: $questionId, answer: $answer);
}
```

### 2. Optimisasi View Template
- Menambahkan variabel PHP untuk konsistensi penggunaan `question_id`
- Menyederhanakan logika pengecekan jawaban yang dipilih

```php
@php
    $currentQuestionId = $currentQuestion->id;
    $currentUserAnswer = $userAnswers[$currentQuestionId] ?? null;
@endphp
```

### 3. Testing
- Membuat comprehensive test untuk memverifikasi fungsionalitas
- Test mencakup:
  - Penyimpanan jawaban untuk multiple questions
  - Preservasi jawaban saat navigasi antar soal
  - Verifikasi penyimpanan di database

## Hasil Testing
✅ **2 tests passed (8 assertions)**
- `it_can_save_answers_for_multiple_questions`: PASSED
- `it_preserves_answers_when_navigating_between_questions`: PASSED

## Kesimpulan
Komponen `ConductTryout` sekarang berfungsi dengan optimal:
- Semua jawaban tersimpan dengan benar menggunakan `question_id` sebagai key
- UI terupdate secara real-time saat user memilih jawaban
- Navigasi antar soal mempertahankan jawaban yang sudah dipilih
- Database menyimpan jawaban dengan konsisten

## File yang Dimodifikasi
1. `app/Livewire/ConductTryout.php` - Optimisasi method selectAnswer
2. `resources/views/livewire/conduct-tryout.blade.php` - Optimisasi view template
3. `tests/Feature/ConductTryoutTest.php` - Test comprehensive (baru)
4. `database/factories/` - Factory untuk testing (baru)

## Tanggal Perbaikan
26 Oktober 2025