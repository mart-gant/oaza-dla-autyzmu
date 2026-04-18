<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class ArticleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Tworzenie kategorii artykułów
        $categories = [
            [
                'name' => 'Poradniki',
                'slug' => 'poradniki',
                'description' => 'Praktyczne poradniki i wskazówki dla rodziców i opiekunów',
            ],
            [
                'name' => 'Terapie',
                'slug' => 'terapie',
                'description' => 'Informacje o różnych formach terapii dla osób z autyzmem',
            ],
            [
                'name' => 'Edukacja',
                'slug' => 'edukacja',
                'description' => 'Artykuły związane z edukacją dzieci ze spektrum autyzmu',
            ],
            [
                'name' => 'Prawo i wsparcie',
                'slug' => 'prawo-i-wsparcie',
                'description' => 'Informacje o prawach i dostępnym wsparciu',
            ],
        ];

        foreach ($categories as $category) {
            ArticleCategory::firstOrCreate(
                ['slug' => $category['slug']], // Szukaj po slug
                $category // Jeśli nie istnieje, utwórz z tymi danymi
            );
        }

        // Pobierz użytkowników
        $users = User::all();
        
        if ($users->isEmpty()) {
            $users = collect([
                User::firstOrCreate(
                    ['email' => 'article.user1@oaza.pl'],
                    [
                        'name' => 'Article User 1',
                        'password' => Hash::make('password'),
                        'role' => 'parent',
                        'email_verified_at' => now(),
                    ]
                ),
                User::firstOrCreate(
                    ['email' => 'article.user2@oaza.pl'],
                    [
                        'name' => 'Article User 2',
                        'password' => Hash::make('password'),
                        'role' => 'autistic_person',
                        'email_verified_at' => now(),
                    ]
                ),
                User::firstOrCreate(
                    ['email' => 'article.user3@oaza.pl'],
                    [
                        'name' => 'Article User 3',
                        'password' => Hash::make('password'),
                        'role' => 'therapist',
                        'email_verified_at' => now(),
                    ]
                ),
            ]);
        }

        // Przykładowe artykuły
        $articles = [
            [
                'title' => 'Jak wspierać dziecko z autyzmem w codziennych czynnościach',
                'content' => "Wspieranie dziecka z autyzmem w codziennych czynnościach wymaga cierpliwości, zrozumienia i konsekwencji. Kluczowe jest stworzenie przewidywalnej rutyny, która pomoże dziecku czuć się bezpiecznie.\n\nWażne wskazówki:\n- Ustalaj stały harmonogram dnia\n- Używaj wizualnych harmonogramów\n- Dziel zadania na mniejsze kroki\n- Nagradzaj małe sukcesy\n- Bądź cierpliwy i konsekwentny\n\nPamiętaj, że każde dziecko jest inne i wymaga indywidualnego podejścia.",
                'is_published' => true,
                'published_at' => now()->subDays(10),
            ],
            [
                'title' => 'Rodzaje terapii dla dzieci ze spektrum autyzmu',
                'content' => "Istnieje wiele różnych form terapii, które mogą pomóc dzieciom z autyzmem w rozwoju. Najczęściej stosowane to:\n\n1. Terapia behawioralna (ABA)\n2. Terapia integracji sensorycznej (SI)\n3. Terapia logopedyczna\n4. Terapia zajęciowa\n5. Muzykoterapia\n\nWybór odpowiedniej terapii powinien być konsultowany ze specjalistami i dostosowany do indywidualnych potrzeb dziecka.",
                'is_published' => true,
                'published_at' => now()->subDays(7),
            ],
            [
                'title' => 'Prawa osób z autyzmem w systemie edukacji',
                'content' => "Osoby z autyzmem mają prawo do odpowiedniego wsparcia w systemie edukacji. W Polsce dzieci ze spektrum autyzmu mogą korzystać z:\n\n- Edukacji specjalnej\n- Nauczania indywidualnego\n- Pomocy asystenta edukacji\n- Dostosowania programu nauczania\n- Orzeczenia o potrzebie kształcenia specjalnego\n\nRodzice mają prawo wyboru formy edukacji dla swojego dziecka i powinni być aktywnie włączeni w proces edukacyjny.",
                'is_published' => true,
                'published_at' => now()->subDays(5),
            ],
            [
                'title' => 'Komunikacja alternatywna - PECS i AAC',
                'content' => "Dla dzieci z autyzmem, które mają trudności z mową, dostępne są metody komunikacji alternatywnej. PECS (Picture Exchange Communication System) oraz AAC (Augmentative and Alternative Communication) to skuteczne narzędzia wspierające komunikację.\n\nPECS opiera się na wymianie obrazków, podczas gdy AAC obejmuje szerszy zakres narzędzi, w tym tablice komunikacyjne, aplikacje mobilne i urządzenia generujące mowę.\n\nWprowadzenie tych metod powinno odbywać się pod okiem specjalisty.",
                'is_published' => true,
                'published_at' => now()->subDays(3),
            ],
            [
                'title' => 'Integracja sensoryczna - czym jest i jak pomaga',
                'content' => "Integracja sensoryczna to proces, w którym mózg odbiera, organizuje i interpretuje informacje zmysłowe. Dzieci z autyzmem często mają trudności w tym obszarze.\n\nTerapia integracji sensorycznej pomaga dziecku lepiej radzić sobie z bodźcami z otoczenia. Może obejmować:\n- Ćwiczenia równoważne\n- Stymulację dotykową\n- Zabawy ruchowe\n- Pracę z różnymi teksturami\n\nTerapia SI powinna być prowadzona przez wykwalifikowanego terapeutę.",
                'is_published' => true,
                'published_at' => now()->subDays(1),
            ],
        ];

        foreach ($articles as $index => $articleData) {
            $articleData['user_id'] = $users->random()->id;
            $articleData['category_id'] = ArticleCategory::inRandomOrder()->first()->id;
            $articleData['slug'] = Str::slug($articleData['title']) . '-' . ($index + 1);
            
            Article::create($articleData);
        }

        // Dodaj kilka artykułów generowanych bezpośrednio (bez factory w production)
        $generatedTitles = [
            'Wsparcie psychologiczne dla rodziców dzieci z autyzmem',
            'Jak przygotować dziecko z autyzmem do przedszkola',
            'Diety i suplementy w autyzmie - co mówi nauka',
            'Terapia zajęciowa - jak wygląda w praktyce',
            'Autyzm u dziewczynek - jak rozpoznać',
            'Przejście do szkoły - praktyczne wskazówki',
            'Terapia zwierzętami w autyzmie',
            'Jak budować rutynę dnia dla dziecka z autyzmem',
            'Stymulacja rozwoju mowy u dzieci z autyzmem',
            'Wsparcie w procesie diagnostycznym',
        ];
        
        foreach ($generatedTitles as $index => $title) {
            Article::create([
                'title' => $title,
                'content' => "To jest przykładowa treść artykułu o temacie: {$title}.\n\nArtykuł zawiera praktyczne informacje i wskazówki dla rodziców oraz opiekunów dzieci ze spektrum autyzmu. Każde dziecko jest wyjątkowe i wymaga indywidualnego podejścia.\n\nWięcej szczegółowych informacji znajdziesz po konsultacji ze specjalistami.",
                'user_id' => $users->random()->id,
                'category_id' => ArticleCategory::inRandomOrder()->first()->id,
                'slug' => Str::slug($title) . '-' . (count($articles) + $index + 1),
                'is_published' => rand(0, 100) < 80,
                'published_at' => rand(0, 100) < 80 ? now()->subDays(rand(1, 30)) : null,
            ]);
        }
    }
}
