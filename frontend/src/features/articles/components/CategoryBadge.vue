<script setup lang="ts">
import type {ArticleCategory} from '@/features/articles/types'

defineProps<{
    category: ArticleCategory | null
}>()

const labels: Record<ArticleCategory, string> = {
    technology: 'Technologia',
    business: 'Biznes',
    science: 'Nauka',
    general: 'Ogólne',
}

const colors: Record<ArticleCategory, string> = {
    technology: 'bg-blue-100 text-blue-800',
    business: 'bg-amber-100 text-amber-800',
    science: 'bg-emerald-100 text-emerald-800',
    general: 'bg-slate-100 text-slate-800',
}
</script>

<template>
    <span
        v-if="category"
        class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium"
        :class="colors[category]"
    >
      {{ labels[category] }}
    </span>
    <span
        v-else
        class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium border border-dashed border-muted-foreground/40 text-muted-foreground"
    >
      Bez kategorii
    </span>
</template>