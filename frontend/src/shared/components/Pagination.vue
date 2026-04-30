<script setup lang="ts">
import { Button } from '@/shared/components/ui/button'
import type { PaginationMeta } from '@/shared/types/api'

const props = defineProps<{
    meta: PaginationMeta
}>()

const emit = defineEmits<{
    'page-change': [page: number]
}>()

function goTo(page: number) {
    if (page < 1 || page > props.meta.last_page || page === props.meta.current_page) {
        return
    }
    emit('page-change', page)
}
</script>

<template>
    <div class="flex items-center justify-between gap-4 mt-6 flex-wrap">
        <p class="text-sm text-muted-foreground">
            Strona {{ meta.current_page }} z {{ meta.last_page }}
            <span class="text-muted-foreground/70"> {{ meta.total }} wyników</span>
        </p>
        <div class="flex gap-2">
            <Button
                variant="outline"
                size="sm"
                :disabled="meta.current_page === 1"
                @click="goTo(meta.current_page - 1)"
            >
                Poprzednia
            </Button>
            <Button
                variant="outline"
                size="sm"
                :disabled="meta.current_page === meta.last_page"
                @click="goTo(meta.current_page + 1)"
            >
                Następna
            </Button>
        </div>
    </div>
</template>