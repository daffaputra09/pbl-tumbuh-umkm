import { cn } from '@/lib/utils';

/**
 * Teks dengan kilau yang bergerak (diadaptasi dari React Bits "ShinyText").
 */
export default function ShinyText({ children, className, color = '#115e59', shineColor = '#5eead4' }) {
    return (
        <span
            className={cn('animate-shine bg-[length:200%_100%] bg-clip-text text-transparent', className)}
            style={{
                backgroundImage: `linear-gradient(110deg, ${color} 40%, ${shineColor} 50%, ${color} 60%)`,
                WebkitBackgroundClip: 'text',
            }}
        >
            {children}
        </span>
    );
}
