<script setup lang="ts">
import { CATEGORY_META, CATEGORY_VALUES } from '@/features/articles/categories'
import type { ArticleCategory } from '@/features/articles/types'

const model = defineModel<ArticleCategory[]>({ required: true})

defineProps<{
    errors?: string[]
}>()

function toggle(category: ArticleCategory) {
    model.value = model.value.includes(category) ? model.value.filter((c) => c !== category) : [...model.value, category]
}

function isChecked(category: ArticleCategory): boolean {
    return model.value.includes(category)
}
</script>

<template>
    <div class="space-y-2">
        <span class="text-sm font-medium">Kategorie</span>

        <div class="flex flex-col gap-2">
            <label
                v-for="key in CATEGORY_VALUES"
                :key="key"
                class="flex items-center gap-2 text-sm cursor-pointer"
            >
                <input
                    type="checkbox"
                    :checked="isChecked(key)"
                    @change="toggle(key)"
                    class="h-4 w-4 rounded border-input"
                />
                <span>{{ CATEGORY_META[key].label }}</span>
            </label>
        </div>

        <p v-if="errors && errors.length > 0" class="text-sm text-destructive">
            {{ errors[0] }}
        </p>
    </div>
</template>