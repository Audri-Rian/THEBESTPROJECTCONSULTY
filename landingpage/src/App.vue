<script>
import HeaderSection from './components/headerSection.vue'
import HeroSection from './components/heroSection.vue'
import ProductsSection from './components/productsSection.vue'
import LookbookSection from './components/lookbookSection.vue'
import LuxurySection from './components/luxurySection.vue'
import AboutSection from './components/aboutSection.vue'
import FooterSection from './components/footerSection.vue'

export default {
  components: {
    HeaderSection,
    HeroSection,
    ProductsSection,
    LookbookSection,
    LuxurySection,
    AboutSection,
    FooterSection
  }
}
</script>

<template>
  <div id="app">
    <HeaderSection />
    <HeroSection />
    <ProductsSection />
    <LookbookSection />
    <LuxurySection />
    <AboutSection />
    <FooterSection />
  </div>
</template>

<style>
body {
  margin: 0;
  font-family: 'Arial', sans-serif;
  background-color: #fff;
}
</style>