<script setup lang="ts">
import { computed } from 'vue'
const props = defineProps<{ open: boolean; title?: string; message?: string; busy?: boolean }>()
const emit = defineEmits<{ (e: 'close'): void; (e: 'confirm'): void }>()

const titleText = computed(() => props.title ?? 'Confirm')
const messageText = computed(() => props.message ?? 'Are you sure?')
</script>

<template>
  <div v-if="open" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
    <div class="card w-full max-w-md p-6">
      <h3 class="mb-2 text-lg font-semibold">{{ titleText }}</h3>
      <p class="mb-6 text-sm text-gray-600">{{ messageText }}</p>
      <div class="flex justify-end gap-2">
        <button class="btn-secondary" @click="$emit('close')" :disabled="busy">Cancel</button>
        <button class="btn-primary" @click="$emit('confirm')" :disabled="busy">{{ busy ? 'Processing…' : 'Confirm' }}</button>
      </div>
    </div>
  </div>
  
</template>

<style scoped></style>
