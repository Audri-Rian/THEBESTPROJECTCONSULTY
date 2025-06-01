<template>
  <section class="lookbook" id="lookbook">
    <div class="lookbook-container">
      <div class="lookbook-content">
        <div class="section-header">
          <h2 class="section-title">LOOK BOOK</h2>
          <div class="title-decoration"></div>
        </div>
        <p class="lookbook-text">
          Explore o universo da joalheria e veja como nossas peças ganham vida com estilo,
          elegância e personalidade. Inspire-se com nossas composições exclusivas e descubra como cada joia pode
          transformar seu look.
        </p>
      </div>
      <div class="lookbook-grid">
        <div class="image-wrapper" v-for="(image, index) in images" :key="index">
          <div class="image-container">
            <img
              :src="image.src"
              :alt="image.alt"
              class="hover-lift"
            />
            <div class="image-overlay"></div>
          </div>
        </div>
      </div>
    </div>
    <div class="gradient-background"></div>
  </section>
</template>

<script>
export default {
  name: 'LookbookSection',
  data() {
    return {
      images: [
        {
          src: 'https://images.unsplash.com/photo-1469334031218-e382a71b716b?ixlib=rb-4.0.3&auto=format&fit=crop&w=2940&q=80',
          alt: 'Modelo usando joias elegantes'
        },
        {
          src: 'https://images.unsplash.com/photo-1588449668365-d15e397f6787?ixlib=rb-4.0.3&auto=format&fit=crop&w=2832&q=80',
          alt: 'Detalhe de pulseira dourada'
        },
        {
          src: 'https://cdn.awsli.com.br/2500x2500/191/191427/produto/20813017/c9b83ddffc.jpg',
          alt: 'Coleção de anéis premium'
        }
      ]
    }
  },
  mounted() {
    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add('visible');
        }
      });
    }, { threshold: 0.1 });

    document.querySelectorAll('.image-container, .lookbook-content').forEach(el => observer.observe(el));
  }
}
</script>

<style scoped>
.lookbook {
  padding: 120px 2rem;
  position: relative;
  overflow: hidden;
  background: #0a0a0a;
}

.lookbook-container {
  max-width: 1400px;
  margin: 0 auto;
  position: relative;
  z-index: 2;
}

.section-header {
  margin-bottom: 4rem;
}

.section-title {
  font-family: 'Playfair Display', serif;
  font-size: 4rem;
  color: #d4af37;
  letter-spacing: 0.1em;
  margin-bottom: 1.5rem;
}

.title-decoration {
  width: 100px;
  height: 2px;
  background: linear-gradient(90deg, #d4af37 0%, transparent 100%);
  margin-top: 1rem;
}

.lookbook-text {
  font-family: 'Inter', sans-serif;
  font-size: 1.2rem;
  line-height: 1.8;
  color: rgba(255, 255, 255, 0.85);
  max-width: 600px;
  margin-bottom: 4rem;
}

.lookbook-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 2rem;
}

.image-wrapper {
  position: relative;
  overflow: hidden;
  border-radius: 24px;
}

.image-container {
  position: relative;
  transform: translateY(50px);
  opacity: 0;
  transition: all 0.6s cubic-bezier(0.23, 1, 0.32, 1);
}

.image-container.visible {
  transform: translateY(0);
  opacity: 1;
}

.image-container img {
  width: 100%;
  height: 600px;
  object-fit: cover;
  transition: transform 0.6s cubic-bezier(0.23, 1, 0.32, 1);
}

.image-overlay {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: linear-gradient(180deg, rgba(16, 16, 16, 0.2) 0%, rgba(16, 16, 16, 0.6) 100%);
  transition: opacity 0.4s ease;
}

.hover-lift:hover img {
  transform: scale(1.05);
}

.hover-lift:hover .image-overlay {
  opacity: 0.8;
}

.gradient-background {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: radial-gradient(circle at 50% 100%, rgba(212, 175, 55, 0.05) 0%, transparent 40%);
  z-index: 1;
}

@media (max-width: 1024px) {
  .lookbook-grid {
    grid-template-columns: 1fr;
  }
  
  .section-title {
    font-size: 3rem;
  }
  
  .image-container img {
    height: 500px;
  }
}

@media (max-width: 768px) {
  .lookbook {
    padding: 80px 1.5rem;
  }
  
  .section-title {
    font-size: 2.5rem;
  }
  
  .lookbook-text {
    font-size: 1.1rem;
  }
}
</style>