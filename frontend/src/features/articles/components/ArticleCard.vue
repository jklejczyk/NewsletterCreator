<script setup lang="ts">
import type {Article} from '@/features/articles/types'
import CategoryBadge from './CategoryBadge.vue'

defineProps<{
    article: Article
}>()
</script>

<template>
    <article class="group rounded-lg border bg-card p-5 hover:shadow-md transition-shadow">
        <div class="flex items-start justify-between gap-3 mb-3">
            <CategoryBadge :category="article.category"/>
            <time class="text-xs text-muted-foreground shrink-0">
                {{ article.published_at ?? 'Bez daty' }}
            </time>
        </div>

        <h3 class="text-lg font-semibold mb-2 leading-snug">
            <a
                :href="article.url"
                target="_blank"
                rel="noopener noreferrer"
                class="hover:text-primary transition-colors"
            >
                {{ article.title }}
            </a>
        </h3>

        <p v-if="article.summary" class="text-sm text-muted-foreground line-clamp-3">
            {{ article.summary }}
        </p>
        <p v-else class="text-sm text-muted-foreground italic">
            Brak streszczenia.
        </p>

        <p class="text-xs text-muted-foreground mt-4">
            Źródło:
            <span class="font-medium">{{
                article.source
            }}</span>
        </p>
    </article>
</template>