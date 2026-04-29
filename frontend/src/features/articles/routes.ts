import type {RouteRecordRaw} from 'vue-router'

export const articleRoutes: RouteRecordRaw[] = [
    {
        path: '',
        name: 'home',
        component: () =>
            import('./views/ArticleListView.vue'),
    },
]