<x-guest-layout>
    <div class="text-center mb-6">
        <div class="mx-auto w-20 h-20 bg-gradient-to-br from-blue-100 to-purple-100 rounded-full flex items-center justify-center mb-4">
            <svg class="w-10 h-10 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
            </svg>
        </div>
        <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-2">Potwierdź swój adres email</h2>
    </div>

    <div class="mb-6 text-sm text-gray-600 dark:text-gray-400 leading-relaxed">
        <p class="mb-3">
            Dziękujemy za rejestrację! 🎉
        </p>
        <p class="mb-3">
            Aby rozpocząć korzystanie z pełnych funkcji platformy <strong>Oaza dla Autyzmu</strong>, potwierdź swój adres email klikając w link, który właśnie wysłaliśmy na:
        </p>
        <p class="font-medium text-blue-600 dark:text-blue-400 mb-3">
            {{ auth()->user()->email }}
        </p>
        <p class="text-xs text-gray-500">
            Nie otrzymałeś maila? Sprawdź folder SPAM lub użyj przycisku poniżej, aby wysłać link ponownie.
        </p>
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
                <span class="text-sm text-green-700 dark:text-green-300 font-medium">
                    Nowy link weryfikacyjny został wysłany na Twój adres email!
                </span>
            </div>
        </div>
    @endif

    @if (session('mail_error'))
        <div class="mb-4 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-red-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10A8 8 0 112 10a8 8 0 0116 0zm-8-4a1 1 0 00-1 1v4a1 1 0 102 0V7a1 1 0 00-1-1zm0 8a1.25 1.25 0 100-2.5A1.25 1.25 0 0010 14z" clip-rule="evenodd"></path>
                </svg>
                <span class="text-sm text-red-700 dark:text-red-300 font-medium">
                    {{ session('mail_error') }}
                </span>
            </div>
        </div>
    @endif

    <div class="flex flex-col gap-3">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="w-full px-6 py-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-lg font-medium hover:shadow-lg hover:scale-105 transition-all">
                📧 Wyślij link ponownie
            </button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full px-6 py-3 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg font-medium hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                Wyloguj się
            </button>
        </form>
    </div>

    <!-- Help Box -->
    <div class="mt-6 p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
        <div class="flex items-start">
            <svg class="w-5 h-5 text-blue-500 mr-3 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
            </svg>
            <div class="text-xs text-blue-700 dark:text-blue-300">
                <p class="font-medium mb-1">💡 Wskazówka</p>
                <p>Link weryfikacyjny jest ważny przez 60 minut. Po weryfikacji będziesz mógł w pełni korzystać z forum, dodawać recenzje i kontaktować się ze specjalistami.</p>
            </div>
        </div>
    </div>
</x-guest-layout>
