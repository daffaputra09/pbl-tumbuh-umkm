import { useEffect, useRef } from 'react';
import { animate, useInView, useReducedMotion } from 'motion/react';

/**
 * Angka yang menghitung naik saat masuk viewport (diadaptasi dari React Bits "CountUp").
 */
export default function CountUp({ to, from = 0, duration = 1.8, delay = 0, decimals = 0, suffix = '', className }) {
    const ref = useRef(null);
    const isInView = useInView(ref, { once: true, amount: 0.6 });
    const prefersReducedMotion = useReducedMotion();

    useEffect(() => {
        const element = ref.current;

        if (!element || !isInView) {
            return;
        }

        const format = (value) => `${value.toLocaleString('id-ID', { minimumFractionDigits: decimals, maximumFractionDigits: decimals })}${suffix}`;

        if (prefersReducedMotion) {
            element.textContent = format(to);

            return;
        }

        const controls = animate(from, to, {
            duration,
            delay,
            ease: [0.16, 1, 0.3, 1],
            onUpdate: (value) => {
                element.textContent = format(value);
            },
        });

        return () => controls.stop();
    }, [isInView, from, to, duration, delay, decimals, suffix, prefersReducedMotion]);

    return (
        <span ref={ref} className={className}>
            {from.toLocaleString('id-ID')}
            {suffix}
        </span>
    );
}
