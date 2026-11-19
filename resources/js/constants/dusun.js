export const DEFAULT_DUSUN_LIST = ['Jeding', 'Junwatu', 'Rejoso'];
export const DUSUN_LAINNYA = 'Lainnya';

export function normalizeHamlet(hamlet) {
    if (!hamlet) {
        return DUSUN_LAINNYA;
    }

    const trimmed = String(hamlet).trim();
    const found = DEFAULT_DUSUN_LIST.find((dusun) => dusun.toLowerCase() === trimmed.toLowerCase());

    return found ?? DUSUN_LAINNYA;
}
