function getCsrfToken() {
    return document.querySelector('meta[name="csrf-token"]')?.content ?? '';
}

export async function apiFetch(url, options = {}) {
    const response = await fetch(url, {
        ...options,
        headers: {
            'Content-Type': 'application/json',
            Accept: 'application/json',
            'X-CSRF-TOKEN': getCsrfToken(),
            ...options.headers,
        },
    });

    if (!response.ok) {
        const body = await response.json().catch(() => null);
        throw new Error(body?.message ?? 'Terjadi kesalahan, coba lagi.');
    }

    if (response.status === 204) {
        return null;
    }

    return response.json();
}
