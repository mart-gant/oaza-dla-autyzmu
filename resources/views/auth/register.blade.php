<x-guest-layout>
    <form method="POST" action="{{ route('register') }}" class="space-y-6">
        @csrf

        <!-- Role Selection -->
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                Kim jesteś? <span class="text-red-500">*</span>
            </label>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Autistic Person Card -->
                <label class="role-card cursor-pointer">
                    <input type="radio" name="role" value="autistic_person" class="peer hidden" {{ old('role') == 'autistic_person' ? 'checked' : '' }} required>
                    <div class="border-2 border-gray-300 dark:border-gray-600 rounded-lg p-4 transition-all hover:border-teal-500 peer-checked:border-teal-600 peer-checked:bg-teal-50 dark:peer-checked:bg-teal-900/20 peer-checked:ring-2 peer-checked:ring-teal-500">
                        <div class="flex flex-col items-center text-center">
                            <svg class="w-12 h-12 mb-3 text-gray-600 dark:text-gray-400 peer-checked:text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            <h3 class="font-bold text-gray-900 dark:text-white mb-1">Osoba autystyczna</h3>
                            <p class="text-xs text-gray-600 dark:text-gray-400">Jestem w spektrum autyzmu</p>
                        </div>
                    </div>
                </label>

                <!-- Parent/Guardian Card -->
                <label class="role-card cursor-pointer">
                    <input type="radio" name="role" value="parent" class="peer hidden" {{ old('role', 'parent') == 'parent' ? 'checked' : '' }}>
                    <div class="border-2 border-gray-300 dark:border-gray-600 rounded-lg p-4 transition-all hover:border-blue-500 peer-checked:border-blue-600 peer-checked:bg-blue-50 dark:peer-checked:bg-blue-900/20 peer-checked:ring-2 peer-checked:ring-blue-500">
                        <div class="flex flex-col items-center text-center">
                            <svg class="w-12 h-12 mb-3 text-gray-600 dark:text-gray-400 peer-checked:text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            <h3 class="font-bold text-gray-900 dark:text-white mb-1">Rodzic / Opiekun</h3>
                            <p class="text-xs text-gray-600 dark:text-gray-400">Szukam wsparcia dla bliskiej osoby</p>
                        </div>
                    </div>
                </label>

                <!-- Therapist Card -->
                <label class="role-card cursor-pointer">
                    <input type="radio" name="role" value="therapist" class="peer hidden" {{ old('role') == 'therapist' ? 'checked' : '' }}>
                    <div class="border-2 border-gray-300 dark:border-gray-600 rounded-lg p-4 transition-all hover:border-purple-500 peer-checked:border-purple-600 peer-checked:bg-purple-50 dark:peer-checked:bg-purple-900/20 peer-checked:ring-2 peer-checked:ring-purple-500">
                        <div class="flex flex-col items-center text-center">
                            <svg class="w-12 h-12 mb-3 text-gray-600 dark:text-gray-400 peer-checked:text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <h3 class="font-bold text-gray-900 dark:text-white mb-1">Terapeuta</h3>
                            <p class="text-xs text-gray-600 dark:text-gray-400">Pracuję z osobami ze spektrum autyzmu</p>
                        </div>
                    </div>
                </label>

                <!-- Educator Card -->
                <label class="role-card cursor-pointer">
                    <input type="radio" name="role" value="educator" class="peer hidden" {{ old('role') == 'educator' ? 'checked' : '' }}>
                    <div class="border-2 border-gray-300 dark:border-gray-600 rounded-lg p-4 transition-all hover:border-green-500 peer-checked:border-green-600 peer-checked:bg-green-50 dark:peer-checked:bg-green-900/20 peer-checked:ring-2 peer-checked:ring-green-500">
                        <div class="flex flex-col items-center text-center">
                            <svg class="w-12 h-12 mb-3 text-gray-600 dark:text-gray-400 peer-checked:text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                            <h3 class="font-bold text-gray-900 dark:text-white mb-1">Edukator</h3>
                            <p class="text-xs text-gray-600 dark:text-gray-400">Prowadzę edukację lub wspieram rozwój</p>
                        </div>
                    </div>
                </label>
            </div>
            <x-input-error :messages="$errors->get('role')" class="mt-2" />
        </div>

        <!-- Role Notification Box -->
        <div id="role-notice-box" class="p-4 rounded-lg hidden transition-all duration-300">
            <div class="flex items-start">
                <svg class="w-5 h-5 mr-3 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                </svg>
                <div class="text-sm">
                    <p class="font-medium mb-1" id="role-notice-title"></p>
                    <p id="role-notice-desc"></p>
                </div>
            </div>
        </div>

        <!-- Name -->
        <div>
            <x-input-label for="name" value="Imię i nazwisko" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div>
            <x-input-label for="email" value="Adres e-mail" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div>
            <x-input-label for="password" value="Hasło" />
            <div class="relative mt-1">
                <x-text-input id="password" class="block w-full pr-10"
                                type="password"
                                name="password"
                                required autocomplete="new-password" />
                <button type="button" onclick="togglePasswordVisibility('password')" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300">
                    <svg id="eye-open-password" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    <svg id="eye-closed-password" class="h-5 w-5 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                    </svg>
                </button>
            </div>
            
            <!-- Wskaźnik siły hasła -->
            <div class="mt-3 space-y-2">
                <div class="flex space-x-1 h-1.5 w-full bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                    <div id="strength-bar-1" class="h-full w-1/5 bg-gray-300 dark:bg-gray-600 transition-colors duration-200"></div>
                    <div id="strength-bar-2" class="h-full w-1/5 bg-gray-300 dark:bg-gray-600 transition-colors duration-200"></div>
                    <div id="strength-bar-3" class="h-full w-1/5 bg-gray-300 dark:bg-gray-600 transition-colors duration-200"></div>
                    <div id="strength-bar-4" class="h-full w-1/5 bg-gray-300 dark:bg-gray-600 transition-colors duration-200"></div>
                    <div id="strength-bar-5" class="h-full w-1/5 bg-gray-300 dark:bg-gray-600 transition-colors duration-200"></div>
                </div>
                <ul class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-1 text-xs text-gray-500 dark:text-gray-400">
                    <li id="rule-length" class="flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        Min. 8 znaków
                    </li>
                    <li id="rule-upper" class="flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        Wielka litera
                    </li>
                    <li id="rule-lower" class="flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        Mała litera
                    </li>
                    <li id="rule-number" class="flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        Cyfra
                    </li>
                    <li id="rule-special" class="flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        Znak specjalny
                    </li>
                </ul>
            </div>

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div>
            <x-input-label for="password_confirmation" value="Potwierdź hasło" />
            <div class="relative mt-1">
                <x-text-input id="password_confirmation" class="block w-full pr-10"
                                type="password"
                                name="password_confirmation" required autocomplete="new-password" />
                <button type="button" onclick="togglePasswordVisibility('password_confirmation')" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300">
                    <svg id="eye-open-password_confirmation" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    <svg id="eye-closed-password_confirmation" class="h-5 w-5 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                    </svg>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <!-- Terms Acceptance -->
        <div class="flex items-start">
            <input type="checkbox" id="terms" name="terms" required class="mt-1 rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500">
            <label for="terms" class="ml-2 text-sm text-gray-600 dark:text-gray-400">
                Akceptuję <a href="/terms" target="_blank" class="text-blue-600 hover:underline">Regulamin</a> oraz <a href="/privacy" target="_blank" class="text-blue-600 hover:underline">Politykę prywatności</a>
            </label>
        </div>

        <div class="flex flex-col sm:flex-row items-center justify-between gap-4 pt-4">
            <a class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 underline" href="{{ route('login') }}">
                Masz już konto? Zaloguj się
            </a>

            <x-primary-button class="w-full sm:w-auto justify-center">
                Utwórz konto
            </x-primary-button>
        </div>
    </form>

    <!-- Divider -->
    <div class="relative my-6">
        <div class="absolute inset-0 flex items-center">
            <div class="w-full border-t border-gray-300 dark:border-gray-700"></div>
        </div>
        <div class="relative flex justify-center text-sm">
            <span class="px-2 bg-white dark:bg-gray-800 text-gray-500 dark:text-gray-400">{{ __('Lub zarejestruj się przez') }}</span>
        </div>
    </div>

    <!-- Social Login Buttons -->
    <div class="grid grid-cols-2 gap-3 mt-4">
        <a href="{{ route('social.redirect', 'google') }}" class="flex items-center justify-center gap-2 px-4 py-2.5 border border-gray-300 dark:border-gray-700 rounded-md shadow-sm bg-white dark:bg-gray-800 text-sm font-semibold text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition ease-in-out duration-150">
            <svg class="h-5 w-5" viewBox="0 0 24 24">
                <path fill="#EA4335" d="M12.24 10.285V14.4h6.887c-.648 2.41-2.519 4.115-5.174 4.115-3.411 0-6.19-2.779-6.19-6.19s2.779-6.19 6.19-6.19c1.488 0 2.851.529 3.916 1.398l3.053-3.053C18.824 2.222 15.71 1 12.24 1 6.033 1 12.24 12.24s5.033 11.24 11.24 11.24c5.897 0 10.793-4.225 11.216-9.756l.024-.316h-11.24z"/>
            </svg>
            <span>Google</span>
        </a>

        <a href="{{ route('social.redirect', 'facebook') }}" class="flex items-center justify-center gap-2 px-4 py-2.5 border border-gray-300 dark:border-gray-700 rounded-md shadow-sm bg-white dark:bg-gray-800 text-sm font-semibold text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition ease-in-out duration-150">
            <svg class="h-5 w-5 text-[#1877F2]" fill="currentColor" viewBox="0 0 24 24">
                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
            </svg>
            <span>Facebook</span>
        </a>
    </div>

    <style>
        .role-card input:checked ~ div svg {
            transform: scale(1.1);
            transition: transform 0.2s;
        }
    </style>

    <script>
        // Pokazywanie / Ukrywanie hasła
        function togglePasswordVisibility(id) {
            const input = document.getElementById(id);
            const eyeOpen = document.getElementById('eye-open-' + id);
            const eyeClosed = document.getElementById('eye-closed-' + id);
            
            if (input.type === 'password') {
                input.type = 'text';
                eyeOpen.classList.add('hidden');
                eyeClosed.classList.remove('hidden');
            } else {
                input.type = 'password';
                eyeOpen.classList.remove('hidden');
                eyeClosed.classList.add('hidden');
            }
        }

        // Powiadomienia w zależności od wybranej roli
        const roleNoticeBox = document.getElementById('role-notice-box');
        const roleNoticeTitle = document.getElementById('role-notice-title');
        const roleNoticeDesc = document.getElementById('role-notice-desc');

        document.querySelectorAll('input[name="role"]').forEach(radio => {
            radio.addEventListener('change', function() {
                const val = this.value;
                if (val === 'therapist' || val === 'educator') {
                    roleNoticeBox.classList.remove('hidden');
                    roleNoticeBox.className = "p-4 rounded-lg bg-purple-50 dark:bg-purple-900/20 border border-purple-200 dark:border-purple-800 text-purple-700 dark:text-purple-300 transition-all duration-300";
                    roleNoticeTitle.innerText = val === 'therapist' ? "Konto Terapeuty" : "Konto Edukatora";
                    roleNoticeDesc.innerText = "Konta specjalistów wymagają weryfikacji. Po zalogowaniu będziesz mógł uzupełnić dane swojej specjalizacji i opis w profilu, aby być widocznym dla innych użytkowników.";
                } else if (val === 'autistic_person') {
                    roleNoticeBox.classList.remove('hidden');
                    roleNoticeBox.className = "p-4 rounded-lg bg-teal-50 dark:bg-teal-900/20 border border-teal-200 dark:border-teal-800 text-teal-700 dark:text-teal-300 transition-all duration-300";
                    roleNoticeTitle.innerText = "Konto Osoby Autystycznej";
                    roleNoticeDesc.innerText = "Twoje konto zostanie dostosowane pod kątem przyjaznej prezentacji i ułatwień sensorycznych. Witamy w naszej społeczności!";
                } else {
                    roleNoticeBox.classList.add('hidden');
                }
            });
        });

        // Trigger change na start jeśli rola jest już zaznaczona
        const checkedRole = document.querySelector('input[name="role"]:checked');
        if (checkedRole) {
            checkedRole.dispatchEvent(new Event('change'));
        }

        // Walidacja siły hasła w czasie rzeczywistym
        document.getElementById('password').addEventListener('input', function (e) {
            const val = e.target.value;
            const rules = {
                length: val.length >= 8,
                upper: /[A-Z]/.test(val),
                lower: /[a-z]/.test(val),
                number: /[0-9]/.test(val),
                special: /[!@#$%^&*(),.?":{}|<>]/.test(val)
            };

            let score = 0;
            for (const rule in rules) {
                const el = document.getElementById('rule-' + rule);
                const icon = el.querySelector('svg');
                if (rules[rule]) {
                    score++;
                    el.classList.remove('text-gray-500', 'dark:text-gray-400');
                    el.classList.add('text-green-600', 'dark:text-green-400');
                    icon.classList.remove('text-gray-400');
                    icon.classList.add('text-green-600', 'dark:text-green-400');
                } else {
                    el.classList.remove('text-green-600', 'dark:text-green-400');
                    el.classList.add('text-gray-500', 'dark:text-gray-400');
                    icon.classList.remove('text-green-600', 'dark:text-green-400');
                    icon.classList.add('text-gray-400');
                }
            }

            // Kolory paska
            const colors = ['bg-red-500', 'bg-orange-500', 'bg-yellow-500', 'bg-blue-500', 'bg-green-500'];
            const activeColor = colors[score - 1] || 'bg-gray-300 dark:bg-gray-600';

            for (let i = 1; i <= 5; i++) {
                const bar = document.getElementById('strength-bar-' + i);
                bar.className = 'h-full w-1/5 transition-colors duration-200';
                if (i <= score) {
                    bar.classList.add(activeColor);
                } else {
                    bar.classList.add('bg-gray-300', 'dark:bg-gray-600');
                }
            }
        });
    </script>
</x-guest-layout>
