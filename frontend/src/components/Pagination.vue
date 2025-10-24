<script setup lang="ts">
const props = withDefaults(defineProps<{
  current: number
  totalPages: number
  disabled?: boolean
}>(), { disabled: false })

const emit = defineEmits<{ (e: 'change', page: number): void }>()

function go(page: number) {
  if (page < 1 || page > props.totalPages || page === props.current) return
  emit('change', page)
}
</script>

<template>
  <div class="flex items-center justify-between gap-2">
    <button class="btn-secondary" @click="go(current - 1)" :disabled="current <= 1 || disabled">Prev</button>
    <div class="text-sm text-gray-600">Page {{ current }} of {{ totalPages }}</div>
    <button class="btn-secondary" @click="go(current + 1)" :disabled="current >= totalPages || disabled">Next</button>
  </div>
</template>

<style scoped></style>
