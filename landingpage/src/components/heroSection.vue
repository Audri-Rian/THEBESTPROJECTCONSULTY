<template>
  <section class="hero" id="home">
    <div class="hero-container">
      <div class="hero-content">
        <div class="title-wrapper">
          <h1 class="main-title">MÔNICA ARAGÃO</h1>
          <div class="title-decoration"></div>
        </div>
        <p class="hero-text">Joias com brilho, luxo e exclusividade para realçar sua beleza única. Cada peça é criada com amor e dedicação
          para mulheres que valorizam a sofisticação.</p>
        <button class="cta-button" @click="irParaContato">
          <i class="fas fa-gem"></i> 
          <span>Descobrir Coleção</span>
          <div class="button-overlay"></div>
        </button>
      </div>
      <div class="hero-image">
        <div class="image-wrapper">
          <img
            src="https://images.unsplash.com/photo-1515562141207-7a88fb7ce338?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2940&q=80"
            alt="Mulher elegante usando joias"
            class="parallax-image">
          <div class="image-overlay"></div>
          <div class="gradient-overlay"></div>
        </div>
      </div>
    </div>
    <div class="hero-background"></div>
  </section>
</template>

<script>
export default {
  name: 'HeroSection',
  methods: {
    irParaContato() {
      document.getElementById('contato')?.scrollIntoView({ behavior: 'smooth' });
    }
  },
  mounted() {
    this.initParallax();
  },
  methods: {
    initParallax() {
      window.addEventListener('scroll', () => {
        const scrolled = window.pageYOffset;
        const parallax = document.querySelector('.parallax-image');
        if(parallax) {
          parallax.style.transform = `translateY(${scrolled * 0.4}px)`;
        }
      });
    }
  }
}
</script>

<style scoped>
.hero {
  min-height: 100vh;
  display: flex;
  align-items: center;
  padding: 160px 2rem 60px;
  position: relative;
  overflow: hidden;
  background: #0a0a0a;
}

.hero-container {
  max-width: 1400px;
  margin: 0 auto;
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 5rem;
  align-items: center;
  position: relative;
  z-index: 2;
}

.title-wrapper {
  margin-bottom: 3rem;
}

.main-title {
  font-family: 'Playfair Display', serif;
  font-size: clamp(3.5rem, 8vw, 6rem);
  font-weight: 400;
  color: #d4af37;
  line-height: 1.05;
  letter-spacing: 0.05em;
  margin-bottom: 1.5rem;
  opacity: 0;
  transform: translateY(30px);
  animation: fadeInUp 1s ease forwards;
}

.title-decoration {
  width: 120px;
  height: 2px;
  background: linear-gradient(90deg, #d4af37 0%, transparent 100%);
  margin-top: 1.5rem;
}

.hero-text {
  font-family: 'Inter', sans-serif;
  font-size: 1.2rem;
  color: rgba(255, 255, 255, 0.85);
  margin-bottom: 3rem;
  max-width: 500px;
  line-height: 1.8;
  opacity: 0;
  transform: translateY(30px);
  animation: fadeInUp 1s ease 0.2s forwards;
}

.cta-button {
  position: relative;
  background: linear-gradient(135deg, rgba(212, 175, 55, 0.2) 0%, rgba(212, 175, 55, 0.1) 100%);
  color: #d4af37;
  padding: 1.5rem 3rem;
  border: none;
  border-radius: 50px;
  font-size: 1.1rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.4s cubic-bezier(0.23, 1, 0.32, 1);
  backdrop-filter: blur(10px);
  border: 1px solid rgba(212, 175, 55, 0.3);
  overflow: hidden;
}

.cta-button i {
  margin-right: 1rem;
  transition: transform 0.3s ease;
}

.cta-button:hover {
  background: linear-gradient(135deg, rgba(212, 175, 55, 0.3) 0%, rgba(212, 175, 55, 0.2) 100%);
  transform: translateY(-3px);
  box-shadow: 0 15px 40px rgba(212, 175, 55, 0.2);
}

.cta-button:hover i {
  transform: rotate(15deg);
}

.button-overlay {
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.1), transparent);
  transition: left 0.6s ease;
}

.cta-button:hover .button-overlay {
  left: 100%;
}

.image-wrapper {
  position: relative;
  border-radius: 24px;
  overflow: hidden;
  transform: translateY(50px);
  opacity: 0;
  animation: fadeInUp 1s ease 0.4s forwards;
}

.parallax-image {
  width: 100%;
  height: 700px;
  object-fit: cover;
  transition: transform 0.4s ease;
}

.image-overlay {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: linear-gradient(45deg, rgba(16, 16, 16, 0.4) 0%, rgba(16, 16, 16, 0.1) 100%);
}

.gradient-overlay {
  position: absolute;
  bottom: 0;
  left: 0;
  width: 100%;
  height: 50%;
  background: linear-gradient(180deg, transparent 0%, #0a0a0a 90%);
}

.hero-background {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: radial-gradient(circle at 70% 20%, rgba(212, 175, 55, 0.05) 0%, transparent 40%);
  z-index: 1;
}

@keyframes fadeInUp {
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@media (max-width: 1024px) {
  .hero-container {
    grid-template-columns: 1fr;
    gap: 3rem;
  }
  
  .hero-content {
    text-align: center;
  }
  
  .title-decoration {
    margin: 1.5rem auto;
  }
  
  .hero-text {
    max-width: none;
    margin-left: auto;
    margin-right: auto;
  }
  
  .parallax-image {
    height: 500px;
  }
}
</style>