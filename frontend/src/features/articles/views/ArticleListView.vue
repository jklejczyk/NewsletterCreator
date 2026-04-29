<script setup lang="ts">
import { onMounted } from 'vue'
import { useArticleStore } from '@/features/articles/store'
import ArticleCard from'@/features/articles/components/ArticleCard.vue'

const store = useArticleStore()

onMounted(() => {
    store.fetchList()
})
</script>

<template>
    <div class="space-y-6">
        <h1 class="text-3xl font-bold">Najnowsze artykuły</h1>

        <div
            v-if="store.isLoading"
            class="text-center py-12 text-muted-foreground"
        >
            Ładowanie artykułów...
        </div>

        <div
            v-else-if="store.hasError"
            class="rounded-lg border border-destructive/30 bg-destructive/5 p-6"
        >
            <p class="font-medium text-destructive">Nie udało się pobrać artykułów</p>
            <p class="text-sm mt-1 text-muted-foreground">{{ store.error }}</p>
        </div>

        <div v-else-if="store.isEmpty" class="text-center py-12">
            <p class="text-muted-foreground">Nie ma jeszcze żadnych artykułów.</p>
        </div>

        <template v-else>
            <p v-if="store.meta" class="text-sm text-muted-foreground">
                Pokazuję {{ store.articles.length }} z {{ store.meta.total }} artykułów
            </p>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <ArticleCard
                    v-for="article in store.articles"
                    :key="article.id"
                    :article="article"
                />
            </div>
        </template>
    </div>
</template>