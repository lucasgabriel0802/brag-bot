<script setup>
defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    title: {
        type: String,
        default: 'Ocorreu um erro',
    },
    message: {
        type: String,
        default: '',
    },
});

defineEmits(['close']);
</script>

<template>
    <Teleport to="body">
        <Transition
            enter-active-class="transition duration-200 ease-out"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition duration-150 ease-in"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div 
                v-if="show" 
                class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70 backdrop-blur-sm"
                @click.self="$emit('close')"
            >
                <div 
                    class="bg-brag-base border border-red-500/30 rounded-2xl p-6 max-w-lg w-full shadow-2xl space-y-4 transform transition-all text-white font-sans"
                >
                    <div class="flex items-center space-x-3 text-red-400">
                        <svg class="w-6 h-6 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                        <h3 class="text-xl font-bold text-white font-agdasima tracking-wide">{{ title }}</h3>
                    </div>

                    <p class="text-gray-300 text-sm md:text-base leading-relaxed bg-black/30 p-4 rounded-xl border border-white/5 whitespace-pre-line">
                        {{ message }}
                    </p>

                    <div class="flex justify-end pt-2">
                        <button
                            type="button"
                            class="px-5 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 text-white font-medium transition-all focus:outline-none focus:ring-2 focus:ring-red-500/50"
                            @click="$emit('close')"
                        >
                            Fechar
                        </button>
                    </div>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>
