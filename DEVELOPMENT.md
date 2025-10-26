# 🛠 SNBTKU Development Guide

Panduan lengkap untuk developer yang ingin berkontribusi atau mengembangkan platform SNBTKU.

## 📋 Table of Contents

- [Development Setup](#-development-setup)
- [Database Schema](#-database-schema)
- [Livewire Components](#-livewire-components)
- [Testing](#-testing)
- [Code Standards](#-code-standards)
- [Contributing](#-contributing)

## 🚀 Development Setup

### Local Environment

1. **Clone & Install**
   ```bash
   git clone <repository-url>
   cd snbtku
   composer install
   npm install
   ```

2. **Environment Configuration**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

3. **Database Setup**
   ```bash
   # Untuk development menggunakan SQLite
   touch database/database.sqlite
   php artisan migrate:fresh --seed
   ```

4. **Asset Compilation**
   ```bash
   # Development
   npm run dev
   
   # Production
   npm run build
   ```

5. **Start Development Server**
   ```bash
   php artisan serve
   ```

### Development Tools

- **Laravel Debugbar**: Untuk debugging queries dan performance
- **Laravel Telescope**: Untuk monitoring aplikasi
- **PHPUnit**: Untuk testing
- **Laravel Pint**: Untuk code formatting

## 🗄 Database Schema

### Detailed Table Structure

#### Users Table
```sql
CREATE TABLE users (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    email_verified_at TIMESTAMP NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') DEFAULT 'user',
    remember_token VARCHAR(100) NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);
```

#### Subjects Table
```sql
CREATE TABLE subjects (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);
```

#### Topics Table
```sql
CREATE TABLE topics (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    subject_id BIGINT NOT NULL,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE CASCADE
);
```

#### Questions Table
```sql
CREATE TABLE questions (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    topic_id BIGINT NOT NULL,
    question_text TEXT NOT NULL,
    option_a TEXT NOT NULL,
    option_b TEXT NOT NULL,
    option_c TEXT NOT NULL,
    option_d TEXT NOT NULL,
    option_e TEXT NOT NULL,
    correct_answer CHAR(1) NOT NULL,
    explanation TEXT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (topic_id) REFERENCES topics(id) ON DELETE CASCADE
);
```

#### Tryout Packages Table
```sql
CREATE TABLE tryout_packages (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    duration_minutes INT NOT NULL,
    status ENUM('draft', 'published') DEFAULT 'draft',
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);
```

#### Pivot Table: Tryout Package Questions
```sql
CREATE TABLE tryout_package_question (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    tryout_package_id BIGINT NOT NULL,
    question_id BIGINT NOT NULL,
    order INT NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (tryout_package_id) REFERENCES tryout_packages(id) ON DELETE CASCADE,
    FOREIGN KEY (question_id) REFERENCES questions(id) ON DELETE CASCADE
);
```

#### User Tryouts Table
```sql
CREATE TABLE user_tryouts (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT NOT NULL,
    tryout_package_id BIGINT NOT NULL,
    start_time TIMESTAMP NOT NULL,
    end_time TIMESTAMP NULL,
    status ENUM('ongoing', 'completed') DEFAULT 'ongoing',
    score DECIMAL(5,2) NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (tryout_package_id) REFERENCES tryout_packages(id) ON DELETE CASCADE
);
```

#### User Tryout Answers Table
```sql
CREATE TABLE user_tryout_answers (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    user_tryout_id BIGINT NOT NULL,
    question_id BIGINT NOT NULL,
    user_answer CHAR(1) NULL,
    is_correct BOOLEAN NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (user_tryout_id) REFERENCES user_tryouts(id) ON DELETE CASCADE,
    FOREIGN KEY (question_id) REFERENCES questions(id) ON DELETE CASCADE
);
```

#### Posts Table
```sql
CREATE TABLE posts (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT NOT NULL,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    body LONGTEXT NOT NULL,
    status ENUM('draft', 'published') DEFAULT 'draft',
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
```

## ⚡ Livewire Components

### Component Architecture

```
app/Livewire/
├── ShowTryoutPackageList.php    # Daftar paket try out
├── ConductTryout.php            # Pengerjaan try out
└── ShowTryoutResult.php         # Hasil try out

resources/views/livewire/
├── show-tryout-package-list.blade.php
├── conduct-tryout.blade.php
└── show-tryout-result.blade.php
```

### Creating New Livewire Component

```bash
# Generate component
php artisan make:livewire ComponentName

# Generate with inline view
php artisan make:livewire ComponentName --inline
```

### Livewire Best Practices

1. **Property Binding**
   ```php
   public $property = 'default';
   
   // In view
   <input wire:model="property">
   ```

2. **Method Calls**
   ```php
   public function methodName($parameter)
   {
       // Logic here
   }
   
   // In view
   <button wire:click="methodName('value')">Click</button>
   ```

3. **Real-time Updates**
   ```php
   // Polling every 1 second
   <div wire:poll.1s="refreshData">
   
   // Polling when visible
   <div wire:poll.visible="refreshData">
   ```

4. **Loading States**
   ```html
   <div wire:loading>
       Loading...
   </div>
   
   <div wire:loading.remove>
       Content here
   </div>
   ```

## 🧪 Testing

### Running Tests

```bash
# Run all tests
php artisan test

# Run specific test file
php artisan test tests/Feature/TryoutTest.php

# Run with coverage
php artisan test --coverage
```

### Test Structure

```
tests/
├── Feature/
│   ├── Auth/
│   ├── Admin/
│   ├── TryoutTest.php
│   └── PostTest.php
└── Unit/
    ├── Models/
    └── Services/
```

### Example Test

```php
<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\TryoutPackage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TryoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_start_tryout()
    {
        $user = User::factory()->create();
        $package = TryoutPackage::factory()->create(['status' => 'published']);

        $response = $this->actingAs($user)
            ->post("/tryout/start/{$package->id}");

        $response->assertRedirect();
        $this->assertDatabaseHas('user_tryouts', [
            'user_id' => $user->id,
            'tryout_package_id' => $package->id,
            'status' => 'ongoing'
        ]);
    }
}
```

## 📏 Code Standards

### PHP Standards

- Follow **PSR-12** coding standard
- Use **Laravel Pint** for formatting
- Use **PHPStan** for static analysis

```bash
# Format code
./vendor/bin/pint

# Check code quality
./vendor/bin/phpstan analyse
```

### Naming Conventions

1. **Models**: PascalCase, singular
   ```php
   class TryoutPackage extends Model
   ```

2. **Controllers**: PascalCase with Controller suffix
   ```php
   class TryoutPackageController extends Controller
   ```

3. **Livewire Components**: PascalCase
   ```php
   class ShowTryoutPackageList extends Component
   ```

4. **Database Tables**: snake_case, plural
   ```sql
   tryout_packages, user_tryouts, user_tryout_answers
   ```

5. **Routes**: kebab-case
   ```php
   Route::get('/tryout-packages', [TryoutPackageController::class, 'index']);
   ```

### Blade Templates

```blade
{{-- Use semantic HTML --}}
<main class="container mx-auto px-4">
    <section class="hero-section">
        <h1 class="text-3xl font-bold">{{ $title }}</h1>
    </section>
</main>

{{-- Use Tailwind utility classes --}}
<button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
    Submit
</button>

{{-- Use Livewire directives properly --}}
<div wire:loading.class="opacity-50">
    Content here
</div>
```

## 🤝 Contributing

### Git Workflow

1. **Fork** the repository
2. **Create** feature branch: `git checkout -b feature/amazing-feature`
3. **Commit** changes: `git commit -m 'Add amazing feature'`
4. **Push** to branch: `git push origin feature/amazing-feature`
5. **Open** Pull Request

### Commit Message Format

```
type(scope): description

[optional body]

[optional footer]
```

**Types:**
- `feat`: New feature
- `fix`: Bug fix
- `docs`: Documentation
- `style`: Formatting
- `refactor`: Code refactoring
- `test`: Adding tests
- `chore`: Maintenance

**Examples:**
```
feat(tryout): add timer functionality to conduct tryout

fix(auth): resolve login redirect issue

docs(readme): update installation instructions
```

### Pull Request Guidelines

1. **Description**: Clear description of changes
2. **Testing**: Include test cases for new features
3. **Documentation**: Update docs if needed
4. **Code Quality**: Ensure code passes all checks
5. **Screenshots**: Include for UI changes

### Development Checklist

- [ ] Code follows PSR-12 standards
- [ ] All tests pass
- [ ] Documentation updated
- [ ] No breaking changes (or properly documented)
- [ ] Performance impact considered
- [ ] Security implications reviewed

## 🔧 Advanced Development

### Custom Artisan Commands

```bash
# Create command
php artisan make:command ProcessTryoutResults

# Register in app/Console/Kernel.php
protected $commands = [
    Commands\ProcessTryoutResults::class,
];
```

### Event & Listeners

```bash
# Create event
php artisan make:event TryoutCompleted

# Create listener
php artisan make:listener SendTryoutNotification --event=TryoutCompleted
```

### Jobs & Queues

```bash
# Create job
php artisan make:job ProcessTryoutScore

# Run queue worker
php artisan queue:work
```

### Custom Middleware

```bash
# Create middleware
php artisan make:middleware EnsureUserIsAdmin

# Register in app/Http/Kernel.php
protected $routeMiddleware = [
    'admin' => \App\Http\Middleware\EnsureUserIsAdmin::class,
];
```

## 📊 Performance Optimization

### Database Optimization

1. **Eager Loading**
   ```php
   $packages = TryoutPackage::with(['questions', 'userTryouts'])->get();
   ```

2. **Query Optimization**
   ```php
   // Use select to limit columns
   $users = User::select('id', 'name', 'email')->get();
   
   // Use pagination for large datasets
   $questions = Question::paginate(20);
   ```

3. **Database Indexing**
   ```php
   Schema::table('user_tryouts', function (Blueprint $table) {
       $table->index(['user_id', 'status']);
       $table->index('created_at');
   });
   ```

### Caching Strategies

```php
// Cache expensive queries
$subjects = Cache::remember('subjects', 3600, function () {
    return Subject::with('topics')->get();
});

// Cache views
return view('tryouts.index')->with('packages', $packages)->cache(3600);
```

### Frontend Optimization

1. **Livewire Optimization**
   ```php
   // Use wire:key for dynamic lists
   @foreach($questions as $question)
       <div wire:key="question-{{ $question->id }}">
           {{ $question->text }}
       </div>
   @endforeach
   ```

2. **Asset Optimization**
   ```bash
   # Minify and compress assets
   npm run build
   
   # Use CDN for static assets
   ```

---

**Happy Coding! 🚀**

*Dokumentasi ini akan terus diperbarui seiring perkembangan platform SNBTKU.*