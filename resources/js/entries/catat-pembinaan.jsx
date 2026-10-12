import React from 'react';
import { createRoot } from 'react-dom/client';
import CatatPembinaanManager from '../pages/CatatPembinaanManager';

const el = document.getElementById('app');
if (el) {
    const root = createRoot(el);
    const propsElement = document.getElementById('page-props');
    const props = propsElement ? JSON.parse(propsElement.textContent) : {};

    root.render(
        <React.StrictMode>
            <CatatPembinaanManager {...props} />
        </React.StrictMode>
    );
}
