{{-- Toast Notifications --}}
<div x-data="{
    toasts: [],
    addToast(type, message, duration = 5000) {
        const id = Date.now();
        this.toasts.push({ id, type, message, visible: true });
        setTimeout(() => {
            const index = this.toasts.findIndex(t => t.id === id);
            if (index !== -1) {
                this.toasts[index].visible = false;
                setTimeout(() => {
                    this.toasts = this.toasts.filter(t => t.id !== id);
                }, 300);
            }
        }, duration);
    }
}" class="fixed bottom-4 right-4 z-50 space-y-2">
    <template x-for="toast in toasts" :key="toast.id">
        <div x-show="toast.visible" 
             x-transition.duration.300ms
             class="min-w-[280px] max-w-sm rounded-lg shadow-lg overflow-hidden"
             :class="{
                 'bg-green-50 border-l-4 border-green-500': toast.type === 'success',
                 'bg-red-50 border-l-4 border-red-500': toast.type === 'error',
                 'bg-yellow-50 border-l-4 border-yellow-500': toast.type === 'warning',
                 'bg-blue-50 border-l-4 border-blue-500': toast.type === 'info'
             }">
            <div class="p-3">
                <div class="flex items-center gap-3">
                    <div class="flex-shrink-0">
                        <template x-if="toast.type === 'success'">
                            <svg class="size-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </template>
                        <template x-if="toast.type === 'error'">
                            <svg class="size-5 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </template>
                        <template x-if="toast.type === 'warning'">
                            <svg class="size-5 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </template>
                        <template x-if="toast.type === 'info'">
                            <svg class="size-5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </template>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-medium" x-text="toast.message" :class="{
                            'text-green-800': toast.type === 'success',
                            'text-red-800': toast.type === 'error',
                            'text-yellow-800': toast.type === 'warning',
                            'text-blue-800': toast.type === 'info'
                        }"></p>
                    </div>
                    <button @click="toast.visible = false" class="flex-shrink-0">
                        <svg class="size-4 text-gray-400 hover:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </template>
</div>