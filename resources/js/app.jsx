import { StrictMode } from 'react';
import { createRoot } from 'react-dom/client';
import LandingPage from '@/pages/LandingPage';

const pages = {
    landing: LandingPage,
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
