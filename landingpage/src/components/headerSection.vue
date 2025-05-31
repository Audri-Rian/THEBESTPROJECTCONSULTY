<template>
  <header class="header" :class="{ 'scrolled': isScrolled }">
    <nav class="nav">
      <div class="logo">RIRI</div>
      <ul class="nav-links">
        <li v-for="(link, index) in links" :key="index">
          <a :href="link.url" class="nav-link hover-gold">{{ link.name }}</a>
        </li>
      </ul>
    </nav>
  </header>
</template>

<script>
export default {
  name: 'HeaderSection',
  data() {
    return {
      isScrolled: false,
      links: [
        { name: 'Home', url: '#home' },
        { name: 'Produtos', url: '#produtos' },
        { name: 'Lookbook', url: '#lookbook' },
        { name: 'Sobre', url: '#sobre' },
        { name: 'Contato', url: '#contato' }
      ]
    }
  },
  mounted() {
    window.addEventListener('scroll', this.handleScroll)
    this.animateElements()
  },
  methods: {
    handleScroll() {
      this.isScrolled = window.scrollY > 50
    },
    animateElements() {
      const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
          if (entry.isIntersecting) {
            entry.target.classList.add('visible')
          }
        })
      }, { threshold: 0.1 })

      document.querySelectorAll('.nav-link').forEach(el => observer.observe(el))
    }
  }
}
</script>

<style scoped>
.header {
  position: fixed;
  top: 0;
  width: 100%;
  background: rgba(16, 16, 16, 0.4);
  backdrop-filter: blur(20px);
  z-index: 1000;
  transition: all 0.4s cubic-bezier(0.23, 1, 0.32, 1);
  border-bottom: 1px solid rgba(255, 255, 255, 0.05);
}

.header.scrolled {
  background: rgba(16, 16, 16, 0.9);
  box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
}

.nav {
  display: flex;
  justify-content: space-between;
  align-items: center;
  max-width: 1400px;
  margin: 0 auto;
  padding: 1.5rem 2rem;
}

.logo {
  font-family: 'Playfair Display', serif;
  font-size: 2.2rem;
  font-weight: 500;
  background: linear-gradient(135deg, #d4af37 0%, #f9e795 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
  letter-spacing: 0.1em;
}

.nav-links {
  display: flex;
  gap: 3rem;
  list-style: none;
}

.nav-link {
  font-family: 'Inter', sans-serif;
  font-size: 1.1rem;
  color: rgba(255, 255, 255, 0.8);
  text-decoration: none;
  position: relative;
  padding: 0.5rem 0;
  transition: all 0.3s ease;
  opacity: 0;
  transform: translateY(10px);
}

.nav-link.visible {
  opacity: 1;
  transform: translateY(0);
}

.hover-gold::after {
  content: '';
  position: absolute;
  width: 0;
  height: 1px;
  bottom: 0;
  left: 0;
  background: #d4af37;
  transition: width 0.3s ease;
}

.hover-gold:hover {
  color: #d4af37;
}

.hover-gold:hover::after {
  width: 100%;
}

@media (max-width: 1024px) {
  .nav {
    padding: 1rem 1.5rem;
  }
  
  .nav-links {
    gap: 2rem;
  }
  
  .nav-link {
    font-size: 1rem;
  }
  
  .logo {
    font-size: 2rem;
  }
}

@media (max-width: 768px) {
  .nav-links {
    display: none;
  }
  
  .nav {
    justify-content: center;
  }
}
</style>