import { StrictMode } from 'react';
import { createRoot } from 'react-dom/client';

export function mount(Page) {
    const rootElement = document.getElementById('app');

    if (!rootElement) {
        return;
    }

    const propsElement = document.getElementById('page-props');
    const props = propsElement ? JSON.parse(propsElement.textContent) : {};

    createRoot(rootElement).render(
        <StrictMode>
            <Page {...props} />
        </StrictMode>,
    );
}
