import { StrictMode } from 'react';
import { createRoot } from 'react-dom/client';
import LandingPage from '@/pages/LandingPage';
import UmkmProfileForm from '@/pages/UmkmProfileForm';
import UmkmNeedsForm from '@/pages/UmkmNeedsForm';
import UmkmDashboard from '@/pages/UmkmDashboard';

const pages = {
    landing: LandingPage,
    umkmProfile: UmkmProfileForm,
    umkmKebutuhan: UmkmNeedsForm,
    umkmDashboard: UmkmDashboard,
};

const rootElement = document.getElementById('app');

if (rootElement) {
    const Page = pages[rootElement.dataset.page] ?? LandingPage;

    createRoot(rootElement).render(
        <StrictMode>
            <Page />
        </StrictMode>,
    );
}
