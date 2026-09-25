import { MotionConfig } from 'motion/react';
import Navbar from '@/components/landing/Navbar';
import Hero from '@/components/landing/Hero';
import Problem from '@/components/landing/Problem';
import HowItWorks from '@/components/landing/HowItWorks';
import Profiling from '@/components/landing/Profiling';
import Features from '@/components/landing/Features';
import Roles from '@/components/landing/Roles';
import Faq from '@/components/landing/Faq';
import CallToAction from '@/components/landing/CallToAction';
import Footer from '@/components/landing/Footer';

export default function LandingPage() {
    return (
        <MotionConfig reducedMotion="user">
            <Navbar />
            <main className="overflow-x-clip">
                <Hero />
                <Problem />
                <HowItWorks />
                <Profiling />
                <Features />
                <Roles />
                <Faq />
                <CallToAction />
            </main>
            <Footer />
        </MotionConfig>
    );
}
