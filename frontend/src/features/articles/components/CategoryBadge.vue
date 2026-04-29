<script setup lang="ts">
import type {ArticleCategory} from '@/features/articles/types'
import {CATEGORY_META} from "@/features/articles/categories.ts";

defineProps<{
    category: ArticleCategory | null
}>()
</script>

<template>
    <span
        v-if="category"
        class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium"
        :class="CATEGORY_META[category].colorClasses"
    >
      {{ CATEGORY_META[category].label }}
    </span>
    <span
        v-else
        class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium border border-dashed border-muted-foreground/40 text-muted-foreground"
    >
      Bez kategorii
    </span>
</template>