import { usePage } from '@inertiajs/vue3';

export function useBreadcrumbs() {
    const page = usePage();

    return {
        breadcrumbs: page.props.breadcrumbs || []
    };
}
