<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
// Todos os imports ajustados com ../ para subir um nível
import HeaderSection from '../components/LandingPage/headerSection.vue'
import HeroSection from '../components/LandingPage/heroSection.vue'
import ProductsSection from '../components/LandingPage/productsSection.vue'
import LookbookSection from '../components/LandingPage/lookbookSection.vue'
import LuxurySection from '../components/LandingPage/luxurySection.vue'
import AboutSection from '../components/LandingPage/aboutSection.vue'
import FooterSection from '../components/LandingPage/footerSection.vue'
</script>

<template>
    <div>

        <Head title="MÔNICA ARAGÃO" />
        <div>
            <HeaderSection />
            <HeroSection />
            <ProductsSection />
            <LookbookSection />
            <LuxurySection />
            <AboutSection />
            <FooterSection />
        </div>
    </div>
</template>