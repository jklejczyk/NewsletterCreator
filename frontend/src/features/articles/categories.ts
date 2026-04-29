import type {ArticleCategory} from './types'

interface CategoryMeta {
    label: string
    colorClasses: string
}

export const CATEGORY_META: Record<ArticleCategory, CategoryMeta> = {
    technology: {
        label: 'Technologia', colorClasses: 'bg-blue-100 text-blue-800'
    },
    business: {
        label: 'Biznes', colorClasses: 'bg-amber-100 text-amber-800'
    },
    science: {
        label: 'Nauka', colorClasses: 'bg-emerald-100 text-emerald-800'
    },
    general: {
        label: 'Ogólne', colorClasses: 'bg-slate-100 text-slate-800'
    },
}

export const CATEGORY_VALUES = Object.keys(CATEGORY_META) as ArticleCategory[]