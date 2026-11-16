<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('messages.index') }}" class="text-gray-600 hover:text-gray-900">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                New Message
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('messages.store') }}" x-data="{ searching: false, users: [], selectedUser: {{ $recipient ? $recipient->id : 'null' }} }">
                        @csrf

                        <!-- Recipient Selection -->
                        <div class="mb-6">
                            <label for="receiver_id" class="block text-sm font-medium text-gray-700 mb-2">To:</label>
                            
                            @if($recipient)
                                <!-- Pre-selected recipient -->
                                <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg border border-gray-200">
                                    <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white font-semibold">
                                        {{ strtoupper(substr($recipient->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-900">{{ $recipient->name }}</p>
                                        <p class="text-sm text-gray-500">{{ $recipient->email }}</p>
                                    </div>
                                </div>
                                <input type="hidden" name="receiver_id" value="{{ $recipient->id }}">
                            @else
                                <!-- User search input -->
                                <div class="relative">
                                    <input 
                                        type="text" 
                                        id="user-search"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 pr-10"
                                        placeholder="Search for a user by name..."
                                        @input.debounce.300ms="
                                            searching = true;
                                            fetch('/api/users/search?q=' + $event.target.value)
                                                .then(r => r.json())
                                                .then(data => {
                                                    users = data.users || [];
                                                    searching = false;
                                                })
                                                .catch(() => searching = false);
                                        "
                                    >
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                        </svg>
                                    </div>
                                    
                                    <!-- Search results dropdown -->
                                    <div 
                                        x-show="users.length > 0" 
                                        x-cloak
                                        class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-md shadow-lg max-h-60 overflow-auto"
                                    >
                                        <template x-for="user in users" :key="user.id">
                                            <button 
                                                type="button"
                                                @click="selectedUser = user.id; users = []; document.getElementById('user-search').value = user.name"
                                                class="w-full text-left px-4 py-3 hover:bg-gray-50 border-b last:border-b-0 flex items-center gap-3"
                                            >
                                                <div class="w-8 h-8 bg-gradient-to-br from-green-500 to-teal-600 rounded-full flex items-center justify-center text-white text-sm font-semibold">
                                                    <span x-text="user.name.charAt(0).toUpperCase()"></span>
                                                </div>
                                                <div>
                                                    <p class="font-medium text-gray-900" x-text="user.name"></p>
                                                </div>
                                            </button>
                                        </template>
                                    </div>
                                    
                                    <!-- Loading indicator -->
                                    <div x-show="searching" x-cloak class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-md shadow-lg p-4 text-center text-gray-500">
                                        Searching...
                                    </div>
                                </div>
                                
                                <input type="hidden" name="receiver_id" :value="selectedUser">
                                
                                <p class="mt-2 text-sm text-gray-500">
                                    Start typing to search for users
                                </p>
                            @endif

                            @error('receiver_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Message Content -->
                        <div class="mb-6">
                            <label for="content" class="block text-sm font-medium text-gray-700 mb-2">Message</label>
                            <textarea 
                                id="content" 
                                name="content" 
                                rows="8" 
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                placeholder="Type your message..."
                                required
                            >{{ old('content') }}</textarea>
                            @error('content')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-sm text-gray-500">
                                <span id="char-count">0</span> / 5000 characters
                            </p>
                        </div>

                        <!-- Actions -->
                        <div class="flex justify-end gap-3">
                            <a href="{{ route('messages.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                                Cancel
                            </a>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                                </svg>
                                Send Message
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Character counter
        document.addEventListener('DOMContentLoaded', function() {
            const textarea = document.getElementById('content');
            const charCount = document.getElementById('char-count');
            
            if (textarea && charCount) {
                textarea.addEventListener('input', function() {
                    charCount.textContent = this.value.length;
                    if (this.value.length > 5000) {
                        charCount.classList.add('text-red-600');
                    } else {
                        charCount.classList.remove('text-red-600');
                    }
                });
                
                // Initial count
                charCount.textContent = textarea.value.length;
            }
        });
    </script>
    @endpush
</x-app-layout>
