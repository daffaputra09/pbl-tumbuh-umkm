import { Component, StrictMode } from 'react';
import { createRoot } from 'react-dom/client';

class ErrorBoundary extends Component {
    constructor(props) {
        super(props);
        this.state = { hasError: false, error: null };
    }

    static getDerivedStateFromError(error) {
        return { hasError: true, error };
    }

    componentDidCatch(error, errorInfo) {
        console.error('ErrorBoundary caught an error:', error, errorInfo);
    }

    render() {
        if (this.state.hasError) {
            return (
                <div className="m-6 rounded-2xl border border-rose-200 bg-rose-50 p-6 text-rose-800">
                    <h2 className="text-base font-bold">Terjadi kendala saat memuat tampilan</h2>
                    <p className="mt-1 text-sm font-medium">{this.state.error?.message || String(this.state.error)}</p>
                    <details className="mt-3">
                        <summary className="cursor-pointer text-xs font-semibold text-rose-600 hover:underline">
                            Lihat detail error (stack trace)
                        </summary>
                        <pre className="mt-2 overflow-auto rounded bg-white p-3 text-xs text-slate-700">
                            {this.state.error?.stack}
                        </pre>
                    </details>
                </div>
            );
        }

        return this.props.children;
    }
}

export function mount(Page) {
    const rootElement = document.getElementById('app');

    if (!rootElement) {
        return;
    }

    const propsElement = document.getElementById('page-props');
    let props = {};
    if (propsElement) {
        try {
            props = JSON.parse(propsElement.textContent || '{}');
        } catch (error) {
            console.error('Failed to parse #page-props JSON:', error);
        }
    }

    createRoot(rootElement).render(
        <StrictMode>
            <ErrorBoundary>
                <Page {...props} />
            </ErrorBoundary>
        </StrictMode>,
    );
}
