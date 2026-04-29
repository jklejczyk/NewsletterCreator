<script setup lang="ts">
import {Button} from '@/shared/components/ui/button'
import {CATEGORY_META, CATEGORY_VALUES} from '@/features/articles/categories'
import {useArticleFilters} from '@/features/articles/composables/useArticleFilters'
import type {ArticleCategory} from '@/features/articles/types'
import {useDebouncedRef} from "@/shared/composables/useDebouncedRef.ts";
import {ref, watch} from "vue";

const {filters, hasActiveFilters, patchFilters, resetFilters} = useArticleFilters()

const dateFromInput = ref<string>(filters.value.date_from ?? '')
const dateToInput = ref<string>(filters.value.date_to ?? '')

watch(filters, (newFilters) => {
    if (dateFromInput.value !== (newFilters.date_from ?? ''))
        dateFromInput.value = newFilters.date_from ?? ''

    if (dateToInput.value !== (newFilters.date_to ?? ''))
        dateToInput.value = newFilters.date_to ?? ''
})

const debouncedFrom = useDebouncedRef(dateFromInput, 400)
const debouncedTo = useDebouncedRef(dateToInput, 400)

watch([debouncedFrom, debouncedTo], ([from, to]) => {
    patchFilters(
        { date_from: from || undefined, date_to: to || undefined },
        { resetPage: true },
    )
})

function onCategoryChange(event: Event) {
    const value = (event.target as HTMLSelectElement).value
    patchFilters(
        { category: value === '' ? undefined : (value as ArticleCategory) },
        { resetPage: true },
    )
}
</script>

<template>
    <div class="flex flex-wrap gap-3 items-end p-4 border rounded-lg bg-muted/30">
        <div class="flex flex-col gap-1.5">
            <label for="filter-category" class="text-sm font-medium">Kategoria</label>
            <select
                id="filter-category"
                :value="filters.category ?? ''"
                @change="onCategoryChange"
                class="h-9 px-3 rounded-md border bg-background text-sm"
            >
                <option value="">Wszystkie</option>
                <option v-for="key in CATEGORY_VALUES" :key="key"
                        :value="key">
                    {{ CATEGORY_META[key].label }}
                </option>
            </select>
        </div>

        <div class="flex flex-col gap-1.5">
            <label for="filter-from" class="text-sm font-medium">Od</label>
            <input
                id="filter-from"
                type="date"
                v-model="dateFromInput"
                class="h-9 px-3 rounded-md border bg-background text-sm"
            />
        </div>

        <div class="flex flex-col gap-1.5">
            <label for="filter-to" class="text-sm font-medium">Od</label>
            <input
                id="filter-to"
                type="date"
                v-model="dateToInput"
                class="h-9 px-3 rounded-md border bg-background text-sm"
            />
        </div>

        <Button
            v-if="hasActiveFilters"
            variant="outline"
            @click="resetFilters"
        >
            Resetuj filtry
        </Button>
    </div>
</template>