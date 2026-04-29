<script setup lang="ts">
import {watch} from 'vue'
import {useArticleStore} from '@/features/articles/store'
import {useArticleFilters} from '@/features/articles/composables/useArticleFilters'
import ArticleCard from '@/features/articles/components/ArticleCard.vue'
import ArticleFilters from '@/features/articles/components/ArticleFilters.vue'
import Pagination from '@/shared/components/Pagination.vue'
import {Button} from "@/shared/components/ui/button";

const store = useArticleStore()
const {filters, patchFilters, resetFilters, hasActiveFilters} = useArticleFilters()

watch(filters, (newFilters) => store.fetchList(newFilters), {
    immediate: true,
})

function onPageChange(page: number) {
    patchFilters({page})
}

function retry() {
    store.fetchList(filters.value)
}
</script>

<template>
    <div class="space-y-6">
        <h1 class="text-3xl font-bold">Najnowsze artykuły</h1>

        <ArticleFilters/>

        <div v-if="store.isLoading" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <div v-for="i in 6" :key="i" class="rounded-lg border bg-card p-5 space-y-3 animate-pulse">
                <div class="flex items-start justify-between gap-3">
                    <div class="h-5 w-24 bg-muted rounded-md"/>
                    <div class="h-4 w-16 bg-muted rounded"/>
                </div>
                <div class="h-6 w-3/4 bg-muted rounded"/>
                <div class="space-y-1.5">
                    <div class="h-4 w-full bg-muted rounded"/>
                    <div class="h-4 w-5/6 bg-muted rounded"/>
                    <div class="h-4 w-2/3 bg-muted rounded"/>
                </div>
            </div>
        </div>

        <div v-else-if="store.hasError" class="rounded-lg border border-destructive/30 bg-destructive/5 p-6 space-y-3">
            <div>
                <p class="font-medium text-destructive">Nie udało się pobrać artykułów</p>
                <p class="text-sm mt-1 text-muted-foreground">{{store.error }}</p>
            </div>
            <Button variant="outline" @click="retry">Spróbuj ponownie</Button>
        </div>

        <div v-else-if="store.isEmpty" class="text-center py-12 space-y-3">
            <p class="text-muted-foreground">
                {{ hasActiveFilters ? 'Nie znaleziono artykułów pasujących do filtrów.' : 'Nie ma jeszcze żadnych artykułów.' }}
            </p>
            <Button v-if="hasActiveFilters" variant="outline" @click="resetFilters" >
                Resetuj filtry
            </Button>
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
            <Pagination
                v-if="store.meta && store.meta.last_page > 1"
                :meta="store.meta"
                @page-change="onPageChange"
            />
        </template>
    </div>
</template>