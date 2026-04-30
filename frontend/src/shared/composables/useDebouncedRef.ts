import { ref, watch, type Ref } from 'vue'

export function useDebouncedRef<T>(source: Ref<T>, delay = 300): Readonly<Ref<T>> {
    const debounced = ref<T>(source.value) as Ref<T>
    let timeoutId: ReturnType<typeof setTimeout> | null = null

    watch(source, (newValue) => {
        if (timeoutId !== null)
            clearTimeout(timeoutId)

        timeoutId = setTimeout(() => {
            debounced.value = newValue
            timeoutId = null
        }, delay)
    })

    return debounced
}