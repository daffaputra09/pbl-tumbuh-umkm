import { StrictMode, Suspense, lazy } from 'react';
import { createRoot } from 'react-dom/client';
import DashboardSkeleton from '@/components/dashboard/DashboardSkeleton';

const pages = {
    landing: lazy(() => import('@/pages/LandingPage')),
    umkmProfile: lazy(() => import('@/pages/UmkmProfileForm')),
    umkmKebutuhan: lazy(() => import('@/pages/UmkmNeedsForm')),
    dashboard: lazy(() => import('@/pages/Dashboard')),
};

const pageFallbacks = {
    dashboard: <DashboardSkeleton />,
};

function readPageProps() {
    const propsElement = document.getElementById('page-props');

    return propsElement ? JSON.parse(propsElement.textContent) : {};
}

const rootElement = document.getElementById('app');

if (rootElement) {
    const pageName = pages[rootElement.dataset.page] ? rootElement.dataset.page : 'landing';
    const Page = pages[pageName];

    createRoot(rootElement).render(
        <StrictMode>
            <Suspense fallback={pageFallbacks[pageName] ?? null}>
                <Page {...readPageProps()} />
            </Suspense>
        </StrictMode>,
    );
}
