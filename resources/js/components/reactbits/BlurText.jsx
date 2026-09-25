import { motion } from 'motion/react';
import { cn } from '@/lib/utils';

/**
 * Teks yang muncul kata per kata dengan efek blur (diadaptasi dari React Bits "BlurText").
 * `highlight` berisi kata yang diberi kelas `highlightClassName`.
 */
export default function BlurText({
    text,
    as: Tag = 'span',
    className,
    delay = 0,
    stagger = 0.08,
    highlight = [],
    highlightClassName = 'text-brand',
}) {
    const words = text.split(' ');
    const MotionTag = motion[Tag];

    return (
        <MotionTag
            className={cn('inline', className)}
            initial="hidden"
            whileInView="visible"
            viewport={{ once: true, amount: 0.4 }}
            transition={{ staggerChildren: stagger, delayChildren: delay }}
            aria-label={text}
        >
            {words.map((word, index) => (
                <motion.span
                    key={`${word}-${index}`}
                    aria-hidden
                    className={cn('inline-block will-change-[filter,transform]', highlight.includes(word) && highlightClassName)}
                    variants={{
                        hidden: { opacity: 0, filter: 'blur(12px)', y: 24 },
                        visible: {
                            opacity: 1,
                            filter: 'blur(0px)',
                            y: 0,
                            transition: { duration: 0.7, ease: [0.22, 1, 0.36, 1] },
                        },
                    }}
                >
                    {word}
                    {index < words.length - 1 && '\u00A0'}
                </motion.span>
            ))}
        </MotionTag>
    );
}
