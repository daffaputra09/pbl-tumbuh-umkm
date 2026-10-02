import { StrictMode } from 'react';
import { createRoot } from 'react-dom/client';
import LandingPage from '@/pages/LandingPage';

const rootElement = document.getElementById('app');

if (rootElement) {
    createRoot(rootElement).render(
        <StrictMode>
            <LandingPage />
        </StrictMode>,
    );
}
